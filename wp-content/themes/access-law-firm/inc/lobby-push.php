<?php
/**
 * Staff lobby waiting push (FCM via Cloud Function).
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Notify signed-in staff that someone is waiting in the Virtual Lobby.
 *
 * Fires a generic push through the Firebase HTTPS function
 * `notifyLobbyWaiting`. Failures are logged and never block check-in.
 *
 * @param int $visit_id Lobby visit post ID (for logging only).
 */
function alf_notify_staff_lobby_waiting( $visit_id = 0 ) {
	$url    = trim( (string) alf_get_setting( 'lobby_push_url', '' ) );
	$secret = (string) alf_get_setting( 'lobby_push_secret', '' );

	if ( '' === $url || '' === $secret ) {
		return;
	}

	$response = wp_remote_post(
		$url,
		array(
			'timeout'  => 8,
			'blocking' => false, // Don't slow down the visitor check-in.
			'headers'  => array(
				'Content-Type' => 'application/json',
				'X-ALF-Secret' => $secret,
			),
			'body'     => wp_json_encode(
				array(
					'visitId' => (int) $visit_id,
					'type'    => 'lobby_waiting',
				)
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( 'ALF lobby push failed: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}
