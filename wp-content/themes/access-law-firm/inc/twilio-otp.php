<?php
/**
 * Twilio SMS OTP handling for the Virtual Lobby.
 *
 * Provides two AJAX endpoints (available to logged-in and guest users):
 *  - alf_send_otp:   generates a 6-digit code, stores it in a transient, sends via Twilio.
 *  - alf_verify_otp: validates the submitted code against the stored transient.
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * How long a code stays valid (seconds).
 */
if ( ! defined( 'ALF_OTP_TTL' ) ) {
	define( 'ALF_OTP_TTL', 10 * MINUTE_IN_SECONDS );
}

/**
 * Build the transient key for a phone number.
 *
 * @param string $phone Digits-only phone number.
 * @return string
 */
function alf_otp_key( $phone ) {
	return 'alf_otp_' . md5( $phone . '|' . wp_salt() );
}

/**
 * Build the attempt-throttle key for a phone number.
 *
 * @param string $phone Digits-only phone number.
 * @return string
 */
function alf_otp_throttle_key( $phone ) {
	return 'alf_otp_send_' . md5( $phone . '|' . wp_salt() );
}

/**
 * Normalize a phone number to E.164.
 *
 * Accepts either:
 *  - Full number with country digits (e.g. 919876543210, 17135550123), or
 *  - Local digits + optional $country like "+1" / "+91".
 *
 * Supported: USA (+1) and India (+91) — both use 10-digit national numbers.
 *
 * @param string $raw     Raw phone input (local or full).
 * @param string $country Optional country code, e.g. "+1" or "+91".
 * @return string Empty string if invalid.
 */
function alf_normalize_phone( $raw, $country = '' ) {
	$digits  = preg_replace( '/\D/', '', (string) $raw );
	$country = preg_replace( '/\D/', '', (string) $country );

	if ( '' === $digits ) {
		return '';
	}

	// Already E.164 without plus: US (11 digits starting with 1).
	if ( 11 === strlen( $digits ) && '1' === $digits[0] ) {
		return '+' . $digits;
	}

	// Already E.164 without plus: India (12 digits starting with 91).
	if ( 12 === strlen( $digits ) && 0 === strpos( $digits, '91' ) ) {
		return '+' . $digits;
	}

	// Local 10-digit number + country.
	if ( 10 === strlen( $digits ) ) {
		if ( '91' === $country ) {
			return '+91' . $digits;
		}
		// Default to US when country is +1 or empty.
		if ( '' === $country || '1' === $country ) {
			return '+1' . $digits;
		}
	}

	// Fallback: accept 11-15 digit international numbers.
	if ( strlen( $digits ) >= 11 && strlen( $digits ) <= 15 ) {
		return '+' . $digits;
	}

	return '';
}

/**
 * AJAX: send an OTP via Twilio.
 */
function alf_ajax_send_otp() {
	check_ajax_referer( 'alf_lobby', 'nonce' );

	if ( ! function_exists( 'alf_is_lobby_open' ) || ! alf_is_lobby_open() ) {
		wp_send_json_error( array( 'message' => __( 'The Virtual Lobby is currently closed. Please try again during lobby hours.', 'access-law-firm' ) ), 403 );
	}

	$country = isset( $_POST['country'] ) ? wp_unslash( $_POST['country'] ) : '';
	$phone   = alf_normalize_phone( isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '', $country );
	if ( '' === $phone ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid phone number.', 'access-law-firm' ) ), 400 );
	}

	if ( ! function_exists( 'alf_twilio_is_configured' ) || ! alf_twilio_is_configured() ) {
		wp_send_json_error( array( 'message' => __( 'SMS verification is not configured yet. Please contact the office.', 'access-law-firm' ) ), 500 );
	}

	// Basic throttle: max 5 sends per phone per hour.
	$throttle_key = alf_otp_throttle_key( $phone );
	$sends        = (int) get_transient( $throttle_key );
	if ( $sends >= 5 ) {
		wp_send_json_error( array( 'message' => __( 'Too many attempts. Please wait a while before requesting another code.', 'access-law-firm' ) ), 429 );
	}

	$code = (string) wp_rand( 100000, 999999 );
	set_transient( alf_otp_key( $phone ), $code, ALF_OTP_TTL );
	set_transient( $throttle_key, $sends + 1, HOUR_IN_SECONDS );

	$body = sprintf(
		/* translators: %s: verification code. */
		__( 'Your Access Law Firm verification code is %s. It expires in 10 minutes.', 'access-law-firm' ),
		$code
	);

	$sent = alf_twilio_send_sms( $phone, $body );

	if ( is_wp_error( $sent ) ) {
		return wp_send_json_error( array( 'message' => $sent->get_error_message() ), 502 );
	}

	wp_send_json_success(
		array(
			'message' => __( 'A verification code has been sent to your phone.', 'access-law-firm' ),
			'phone'   => $phone,
		)
	);
}
add_action( 'wp_ajax_alf_send_otp', 'alf_ajax_send_otp' );
add_action( 'wp_ajax_nopriv_alf_send_otp', 'alf_ajax_send_otp' );

/**
 * AJAX: verify a submitted OTP.
 */
function alf_ajax_verify_otp() {
	check_ajax_referer( 'alf_lobby', 'nonce' );

	$country = isset( $_POST['country'] ) ? wp_unslash( $_POST['country'] ) : '';
	$phone   = alf_normalize_phone( isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '', $country );
	$code    = preg_replace( '/\D/', '', isset( $_POST['code'] ) ? wp_unslash( $_POST['code'] ) : '' );

	if ( '' === $phone || 6 !== strlen( $code ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter the complete 6-digit code.', 'access-law-firm' ) ), 400 );
	}

	$key      = alf_otp_key( $phone );
	$expected = get_transient( $key );

	if ( false === $expected ) {
		wp_send_json_error( array( 'message' => __( 'Your code has expired. Please request a new one.', 'access-law-firm' ) ), 410 );
	}

	if ( ! hash_equals( (string) $expected, (string) $code ) ) {
		wp_send_json_error( array( 'message' => __( 'That code is incorrect. Please try again.', 'access-law-firm' ) ), 401 );
	}

	// One-time use.
	delete_transient( $key );
	delete_transient( alf_otp_throttle_key( $phone ) );

	wp_send_json_success( array( 'message' => __( 'Phone number verified.', 'access-law-firm' ) ) );
}
add_action( 'wp_ajax_alf_verify_otp', 'alf_ajax_verify_otp' );
add_action( 'wp_ajax_nopriv_alf_verify_otp', 'alf_ajax_verify_otp' );

/**
 * Send an SMS through the Twilio REST API.
 *
 * @param string $to   Destination number in E.164.
 * @param string $body Message body.
 * @return true|WP_Error
 */
function alf_twilio_send_sms( $to, $body ) {
	$sid   = alf_get_setting( 'twilio_sid' );
	$token = alf_get_setting( 'twilio_token' );
	$from  = alf_get_setting( 'twilio_from' );

	if ( ! $sid || ! $token || ! $from ) {
		return new WP_Error( 'alf_twilio_config', __( 'SMS verification is not configured.', 'access-law-firm' ) );
	}

	$endpoint = 'https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode( $sid ) . '/Messages.json';

	$args = array(
		'timeout' => 15,
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( $sid . ':' . $token ),
			'Content-Type'  => 'application/x-www-form-urlencoded',
		),
		'body'    => array(
			'To'   => $to,
			'From' => $from,
			'Body' => $body,
		),
	);

	$response = wp_remote_post( $endpoint, $args );

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'alf_twilio_http', __( 'Could not reach the SMS service. Please try again.', 'access-law-firm' ) );
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	if ( $status >= 200 && $status < 300 ) {
		return true;
	}

	$data    = json_decode( wp_remote_retrieve_body( $response ), true );
	$message = isset( $data['message'] ) ? $data['message'] : __( 'The SMS could not be sent. Please check the number and try again.', 'access-law-firm' );

	// Log full detail for admins; return a friendly message to users.
	if ( function_exists( 'error_log' ) ) {
		error_log( '[Access Law Firm] Twilio error ' . $status . ': ' . wp_remote_retrieve_body( $response ) );
	}

	return new WP_Error( 'alf_twilio_send', $message );
}
