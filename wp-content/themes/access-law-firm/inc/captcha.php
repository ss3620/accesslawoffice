<?php
/**
 * Google reCAPTCHA v2 helpers for the Virtual Lobby.
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether SMS verification is turned on in settings.
 *
 * @return bool
 */
function alf_sms_setting_enabled() {
	return 1 === (int) alf_get_setting( 'sms_enabled', 0 );
}

/**
 * Whether SMS can actually be used (setting on + Twilio credentials).
 *
 * @return bool
 */
function alf_sms_enabled() {
	return alf_sms_setting_enabled() && function_exists( 'alf_twilio_is_configured' ) && alf_twilio_is_configured();
}

/**
 * Whether CAPTCHA is turned on in settings.
 *
 * @return bool
 */
function alf_captcha_setting_enabled() {
	return 1 === (int) alf_get_setting( 'captcha_enabled', 1 );
}

/**
 * Whether CAPTCHA keys are present.
 *
 * @return bool
 */
function alf_captcha_is_configured() {
	return (bool) alf_get_setting( 'recaptcha_site_key' ) && (bool) alf_get_setting( 'recaptcha_secret_key' );
}

/**
 * Whether CAPTCHA should run on the front-end.
 *
 * @return bool
 */
function alf_captcha_enabled() {
	return alf_captcha_setting_enabled() && alf_captcha_is_configured();
}

/**
 * Active verification mode for the lobby flow.
 *
 * @return string 'sms_captcha' | 'sms' | 'captcha' | 'none'
 */
function alf_lobby_verify_mode() {
	$sms     = alf_sms_enabled();
	$captcha = alf_captcha_enabled();
	if ( $sms && $captcha ) {
		return 'sms_captcha';
	}
	if ( $sms ) {
		return 'sms';
	}
	if ( $captcha ) {
		return 'captcha';
	}
	return 'none';
}

/**
 * Mark a phone as verified for check-in (OTP, CAPTCHA, or skip mode).
 *
 * @param string $phone E.164 phone.
 */
function alf_mark_visitor_verified( $phone ) {
	set_transient( 'alf_phone_ok_' . md5( $phone . '|' . wp_salt() ), 1, 15 * MINUTE_IN_SECONDS );
}

/**
 * Verify a Google reCAPTCHA v2 response token.
 *
 * @param string $token Client response token.
 * @return true|WP_Error
 */
function alf_verify_recaptcha_token( $token ) {
	$token = trim( (string) $token );
	if ( '' === $token ) {
		return new WP_Error( 'alf_captcha_empty', __( 'Please complete the CAPTCHA.', 'access-law-firm' ) );
	}

	$secret = alf_get_setting( 'recaptcha_secret_key' );
	if ( ! $secret ) {
		return new WP_Error( 'alf_captcha_config', __( 'CAPTCHA is not configured.', 'access-law-firm' ) );
	}

	$response = wp_remote_post(
		'https://www.google.com/recaptcha/api/siteverify',
		array(
			'timeout' => 15,
			'body'    => array(
				'secret'   => $secret,
				'response' => $token,
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'alf_captcha_http', __( 'Could not verify CAPTCHA. Please try again.', 'access-law-firm' ) );
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $data['success'] ) ) {
		return new WP_Error( 'alf_captcha_fail', __( 'CAPTCHA verification failed. Please try again.', 'access-law-firm' ) );
	}

	return true;
}

/**
 * AJAX: verify CAPTCHA (and optionally mark phone verified when SMS is off).
 */
function alf_ajax_verify_captcha() {
	check_ajax_referer( 'alf_lobby', 'nonce' );

	if ( ! alf_is_lobby_open() ) {
		wp_send_json_error( array( 'message' => __( 'The Virtual Lobby is currently closed.', 'access-law-firm' ) ), 403 );
	}

	if ( ! alf_captcha_enabled() ) {
		wp_send_json_error( array( 'message' => __( 'CAPTCHA is not enabled.', 'access-law-firm' ) ), 400 );
	}

	$token   = isset( $_POST['captcha_token'] ) ? wp_unslash( $_POST['captcha_token'] ) : '';
	$country = isset( $_POST['country'] ) ? wp_unslash( $_POST['country'] ) : '';
	$phone   = alf_normalize_phone( isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '', $country );

	$verified = alf_verify_recaptcha_token( $token );
	if ( is_wp_error( $verified ) ) {
		wp_send_json_error( array( 'message' => $verified->get_error_message() ), 400 );
	}

	// When SMS is off, CAPTCHA is the gate before check-in.
	if ( $phone && ! alf_sms_enabled() ) {
		alf_mark_visitor_verified( $phone );
	}

	wp_send_json_success( array( 'message' => __( 'CAPTCHA verified.', 'access-law-firm' ) ) );
}
add_action( 'wp_ajax_alf_verify_captcha', 'alf_ajax_verify_captcha' );
add_action( 'wp_ajax_nopriv_alf_verify_captcha', 'alf_ajax_verify_captcha' );

/**
 * AJAX: skip verification when both SMS and CAPTCHA are off (phone still collected).
 */
function alf_ajax_skip_verify() {
	check_ajax_referer( 'alf_lobby', 'nonce' );

	if ( ! alf_is_lobby_open() ) {
		wp_send_json_error( array( 'message' => __( 'The Virtual Lobby is currently closed.', 'access-law-firm' ) ), 403 );
	}

	if ( 'none' !== alf_lobby_verify_mode() ) {
		wp_send_json_error( array( 'message' => __( 'Verification is required.', 'access-law-firm' ) ), 400 );
	}

	$country = isset( $_POST['country'] ) ? wp_unslash( $_POST['country'] ) : '';
	$phone   = alf_normalize_phone( isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '', $country );
	if ( '' === $phone ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid phone number.', 'access-law-firm' ) ), 400 );
	}

	alf_mark_visitor_verified( $phone );
	wp_send_json_success( array( 'message' => __( 'Continue.', 'access-law-firm' ) ) );
}
add_action( 'wp_ajax_alf_skip_verify', 'alf_ajax_skip_verify' );
add_action( 'wp_ajax_nopriv_alf_skip_verify', 'alf_ajax_skip_verify' );
