<?php
/**
 * REST API for Flutter app — namespace alf/v1.
 *
 * Base: /wp-json/alf/v1/
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CORS for mobile / local tooling.
 */
function alf_rest_send_cors_headers() {
	if ( ! headers_sent() ) {
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS' );
		header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
	}
}
add_action( 'rest_api_init', 'alf_rest_send_cors_headers', 5 );

/**
 * Handle OPTIONS preflight.
 *
 * @param mixed           $response Response.
 * @param WP_REST_Server  $server   Server.
 * @param WP_REST_Request $request  Request.
 * @return mixed
 */
function alf_rest_preflight( $response, $server, $request ) {
	if ( 'OPTIONS' === $request->get_method() ) {
		alf_rest_send_cors_headers();
		return new WP_REST_Response( null, 204 );
	}
	return $response;
}
add_filter( 'rest_pre_dispatch', 'alf_rest_preflight', 10, 3 );

/**
 * Register all alf/v1 routes.
 */
function alf_register_rest_routes() {
	$ns = 'alf/v1';

	register_rest_route(
		$ns,
		'/config',
		array(
			'methods'             => 'GET',
			'callback'            => 'alf_rest_config',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/auth/login',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_auth_login',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/auth/me',
		array(
			'methods'             => 'GET',
			'callback'            => 'alf_rest_auth_me',
			'permission_callback' => 'alf_rest_require_staff',
		)
	);

	register_rest_route(
		$ns,
		'/auth/logout',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_auth_logout',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/clients/activate',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_client_activate',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/clients/(?P<id>\d+)',
		array(
			'methods'             => 'GET',
			'callback'            => 'alf_rest_client_get',
			'permission_callback' => 'alf_rest_require_client_or_staff',
		)
	);

	register_rest_route(
		$ns,
		'/clients',
		array(
			'methods'             => 'GET',
			'callback'            => 'alf_rest_clients_list',
			'permission_callback' => 'alf_rest_require_staff',
		)
	);

	register_rest_route(
		$ns,
		'/activation-codes',
		array(
			array(
				'methods'             => 'GET',
				'callback'            => 'alf_rest_activation_list',
				'permission_callback' => 'alf_rest_require_admin',
			),
			array(
				'methods'             => 'POST',
				'callback'            => 'alf_rest_activation_create',
				'permission_callback' => 'alf_rest_require_admin',
			),
		)
	);

	register_rest_route(
		$ns,
		'/threads/(?P<thread_id>[a-zA-Z0-9_-]+)/messages',
		array(
			array(
				'methods'             => 'GET',
				'callback'            => 'alf_rest_messages_list',
				'permission_callback' => 'alf_rest_require_client_or_staff',
			),
			array(
				'methods'             => 'POST',
				'callback'            => 'alf_rest_messages_send',
				'permission_callback' => 'alf_rest_require_client_or_staff',
			),
		)
	);

	register_rest_route(
		$ns,
		'/lobby/status',
		array(
			'methods'             => 'GET',
			'callback'            => 'alf_rest_lobby_status',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/lobby/clients/(?P<client_id>\d+)',
		array(
			array(
				'methods'             => 'GET',
				'callback'            => 'alf_rest_client_lobby_get',
				'permission_callback' => 'alf_rest_require_client_or_staff',
			),
			array(
				'methods'             => 'POST',
				'callback'            => 'alf_rest_client_lobby_enter',
				'permission_callback' => 'alf_rest_require_client_or_staff',
			),
		)
	);

	register_rest_route(
		$ns,
		'/lobby/clients/(?P<client_id>\d+)/leave',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_client_lobby_leave',
			'permission_callback' => 'alf_rest_require_client_or_staff',
		)
	);

	register_rest_route(
		$ns,
		'/lobby/check-in',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_lobby_check_in',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/lobby/visits/(?P<id>\d+)',
		array(
			'methods'             => 'GET',
			'callback'            => 'alf_rest_visit_get',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/lobby/visits/(?P<id>\d+)/joined',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_visit_joined',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/lobby/otp/send',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_otp_send',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/lobby/otp/verify',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_otp_verify',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$ns,
		'/queue',
		array(
			'methods'             => 'GET',
			'callback'            => 'alf_rest_queue_list',
			'permission_callback' => 'alf_rest_require_staff',
		)
	);

	register_rest_route(
		$ns,
		'/queue/(?P<id>\d+)/actions',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_queue_action',
			'permission_callback' => 'alf_rest_require_staff',
		)
	);

	register_rest_route(
		$ns,
		'/lobby/toggle',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_lobby_toggle',
			'permission_callback' => 'alf_rest_require_staff',
		)
	);

	register_rest_route(
		$ns,
		'/admin/settings',
		array(
			array(
				'methods'             => 'GET',
				'callback'            => 'alf_rest_admin_settings_get',
				'permission_callback' => 'alf_rest_require_admin',
			),
			array(
				'methods'             => 'PUT',
				'callback'            => 'alf_rest_admin_settings_put',
				'permission_callback' => 'alf_rest_require_admin',
			),
		)
	);

	register_rest_route(
		$ns,
		'/appointments',
		array(
			array(
				'methods'             => 'GET',
				'callback'            => 'alf_rest_appointments_list',
				'permission_callback' => 'alf_rest_require_client_or_staff',
			),
			array(
				'methods'             => 'POST',
				'callback'            => 'alf_rest_appointments_create',
				'permission_callback' => 'alf_rest_require_client_or_staff',
			),
		)
	);

	register_rest_route(
		$ns,
		'/appointments/(?P<id>\d+)',
		array(
			'methods'             => 'PATCH',
			'callback'            => 'alf_rest_appointments_patch',
			'permission_callback' => 'alf_rest_require_staff',
		)
	);

	register_rest_route(
		$ns,
		'/device/register',
		array(
			'methods'             => 'POST',
			'callback'            => 'alf_rest_device_register',
			'permission_callback' => 'alf_rest_require_client_or_staff',
		)
	);
}
add_action( 'rest_api_init', 'alf_register_rest_routes' );

/* ─── Auth helpers ─────────────────────────────────────────────────────── */

/**
 * Authenticate staff from Bearer token and set current user.
 *
 * @return WP_User|WP_Error
 */
function alf_rest_current_staff() {
	$tok = alf_resolve_app_token();
	if ( ! $tok || 'staff' !== $tok['type'] ) {
		return new WP_Error( 'alf_unauthorized', __( 'Staff authentication required.', 'access-law-firm' ), array( 'status' => 401 ) );
	}
	$user = get_user_by( 'id', (int) $tok['owner_id'] );
	if ( ! $user ) {
		return new WP_Error( 'alf_unauthorized', __( 'Invalid staff session.', 'access-law-firm' ), array( 'status' => 401 ) );
	}
	if ( ! user_can( $user, 'alf_manage_lobby' ) && ! user_can( $user, 'manage_options' ) ) {
		return new WP_Error( 'alf_forbidden', __( 'You do not have lobby access.', 'access-law-firm' ), array( 'status' => 403 ) );
	}
	wp_set_current_user( $user->ID );
	return $user;
}

/**
 * @param WP_REST_Request $request Request.
 * @return bool|WP_Error
 */
function alf_rest_require_staff( $request ) {
	$user = alf_rest_current_staff();
	return is_wp_error( $user ) ? $user : true;
}

/**
 * @param WP_REST_Request $request Request.
 * @return bool|WP_Error
 */
function alf_rest_require_admin( $request ) {
	$user = alf_rest_current_staff();
	if ( is_wp_error( $user ) ) {
		return $user;
	}
	if ( ! user_can( $user, 'manage_options' ) ) {
		return new WP_Error( 'alf_forbidden', __( 'Admin only.', 'access-law-firm' ), array( 'status' => 403 ) );
	}
	return true;
}

/**
 * Client Bearer or staff Bearer.
 *
 * @param WP_REST_Request $request Request.
 * @return bool|WP_Error
 */
function alf_rest_require_client_or_staff( $request ) {
	$tok = alf_resolve_app_token();
	if ( ! $tok ) {
		return new WP_Error( 'alf_unauthorized', __( 'Authentication required.', 'access-law-firm' ), array( 'status' => 401 ) );
	}
	if ( 'staff' === $tok['type'] ) {
		$user = alf_rest_current_staff();
		return is_wp_error( $user ) ? $user : true;
	}
	if ( 'client' === $tok['type'] ) {
		$client_id = (int) $tok['owner_id'];
		$post      = get_post( $client_id );
		if ( ! $post || 'alf_app_client' !== $post->post_type ) {
			return new WP_Error( 'alf_unauthorized', __( 'Invalid client session.', 'access-law-firm' ), array( 'status' => 401 ) );
		}
		$request->set_param( '_alf_client_id', $client_id );
		return true;
	}
	return new WP_Error( 'alf_unauthorized', __( 'Authentication required.', 'access-law-firm' ), array( 'status' => 401 ) );
}

/**
 * Ensure client can only access own resources unless staff.
 *
 * @param WP_REST_Request $request Request.
 * @param int             $client_id Target client.
 * @return true|WP_Error
 */
function alf_rest_assert_client_access( $request, $client_id ) {
	$tok = alf_resolve_app_token();
	if ( ! $tok ) {
		return new WP_Error( 'alf_unauthorized', __( 'Authentication required.', 'access-law-firm' ), array( 'status' => 401 ) );
	}
	if ( 'staff' === $tok['type'] ) {
		return true;
	}
	if ( 'client' === $tok['type'] && (int) $tok['owner_id'] === (int) $client_id ) {
		return true;
	}
	return new WP_Error( 'alf_forbidden', __( 'Access denied.', 'access-law-firm' ), array( 'status' => 403 ) );
}

/* ─── Route handlers ───────────────────────────────────────────────────── */

/**
 * Public config (dynamic hours, verify mode, flags).
 *
 * @return WP_REST_Response
 */
function alf_rest_config() {
	return rest_ensure_response(
		array(
			'lobby_open'       => function_exists( 'alf_is_lobby_open' ) && alf_is_lobby_open(),
			'verify_mode'      => function_exists( 'alf_lobby_verify_mode' ) ? alf_lobby_verify_mode() : 'none',
			'sms_enabled'      => function_exists( 'alf_sms_enabled' ) && alf_sms_enabled(),
			'captcha_enabled'  => function_exists( 'alf_captcha_enabled' ) && alf_captcha_enabled(),
			'recaptcha_site_key' => function_exists( 'alf_recaptcha_site_key' ) ? alf_recaptcha_site_key() : '',
			'waiting_count'    => function_exists( 'alf_waiting_count' ) ? (int) alf_waiting_count( 0 ) : 0,
			'hours'            => array(
				'weekday' => 'Mon–Fri: 9:00 AM – 5:00 PM CST',
				'weekend' => 'Sat–Sun: 10:00 AM – 3:30 PM CST',
			),
			'features'         => array(
				'messaging'   => true,
				'voip'        => false,
				'meeting_sdk' => false,
			),
			'brand'            => array(
				'name' => 'Access Law Office',
			),
		)
	);
}

/**
 * Staff login.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_auth_login( $request ) {
	$username = sanitize_text_field( (string) $request->get_param( 'username' ) );
	if ( '' === $username ) {
		$username = sanitize_text_field( (string) $request->get_param( 'email' ) );
	}
	$password = (string) $request->get_param( 'password' );

	if ( '' === $username || '' === $password ) {
		return new WP_Error( 'alf_invalid', __( 'Username and password are required.', 'access-law-firm' ), array( 'status' => 400 ) );
	}

	// Resolve email → login so wp_authenticate is reliable across hosts.
	if ( is_email( $username ) ) {
		$by_email = get_user_by( 'email', $username );
		if ( $by_email ) {
			$username = $by_email->user_login;
		}
	}

	$user = wp_authenticate( $username, $password );
	if ( is_wp_error( $user ) ) {
		return new WP_Error( 'alf_invalid_credentials', __( 'Invalid email or password.', 'access-law-firm' ), array( 'status' => 401 ) );
	}

	if ( ! user_can( $user, 'alf_manage_lobby' ) && ! user_can( $user, 'manage_options' ) ) {
		return new WP_Error( 'alf_forbidden', __( 'This account cannot access staff features.', 'access-law-firm' ), array( 'status' => 403 ) );
	}

	$token = alf_issue_app_token( 'staff', $user->ID );
	$profile = alf_serialize_staff_user( $user );

	return rest_ensure_response(
		array(
			'token'   => $token,
			'staff'   => $profile,
			'role'    => $profile['role'],
			'message' => __( 'Signed in.', 'access-law-firm' ),
		)
	);
}

/**
 * Current staff profile.
 *
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_auth_me() {
	$user = alf_rest_current_staff();
	if ( is_wp_error( $user ) ) {
		return $user;
	}
	return rest_ensure_response( alf_serialize_staff_user( $user ) );
}

/**
 * Logout — drop bearer token.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function alf_rest_auth_logout( $request ) {
	$header = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ) ) : '';
	if ( preg_match( '/Bearer\s+(\S+)/i', $header, $m ) ) {
		delete_transient( 'alf_app_tok_' . hash( 'sha256', $m[1] ) );
	}
	return rest_ensure_response( array( 'ok' => true ) );
}

/**
 * Redeem activation code → create client.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_client_activate( $request ) {
	$name  = sanitize_text_field( (string) $request->get_param( 'name' ) );
	$email = sanitize_email( (string) $request->get_param( 'email' ) );
	$code  = strtoupper( sanitize_text_field( (string) $request->get_param( 'code' ) ) );

	if ( strlen( $name ) < 2 || ! is_email( $email ) || '' === $code ) {
		return new WP_Error( 'alf_invalid', __( 'Name, email, and activation code are required.', 'access-law-firm' ), array( 'status' => 400 ) );
	}

	$activation = alf_find_activation_by_code( $code );
	if ( ! $activation ) {
		return new WP_Error( 'alf_invalid_code', __( 'That activation code was not recognized.', 'access-law-firm' ), array( 'status' => 404 ) );
	}

	$used       = (int) get_post_meta( $activation->ID, 'used', true );
	$bound_email = (string) get_post_meta( $activation->ID, 'email', true );
	$client_id  = (int) get_post_meta( $activation->ID, 'client_id', true );

	if ( $used ) {
		if ( $client_id ) {
			$existing = alf_serialize_app_client( $client_id );
			if ( $existing && strtolower( $existing['email'] ) === strtolower( $email ) ) {
				$token = alf_issue_app_token( 'client', $client_id );
				return rest_ensure_response(
					array(
						'token'  => $token,
						'client' => $existing,
					)
				);
			}
		}
		// Demo code can be reused for testing with a new email.
		if ( 'ALF-DEMO' !== $code ) {
			return new WP_Error( 'alf_code_used', __( 'That activation code has already been used.', 'access-law-firm' ), array( 'status' => 409 ) );
		}
	}

	if ( $bound_email && strtolower( $bound_email ) !== strtolower( $email ) ) {
		return new WP_Error( 'alf_email_mismatch', __( 'This code was issued to a different email.', 'access-law-firm' ), array( 'status' => 403 ) );
	}

	$client_id = wp_insert_post(
		array(
			'post_type'   => 'alf_app_client',
			'post_status' => 'publish',
			'post_title'  => $name,
		),
		true
	);

	if ( is_wp_error( $client_id ) ) {
		return new WP_Error( 'alf_create_failed', __( 'Could not create client.', 'access-law-firm' ), array( 'status' => 500 ) );
	}

	update_post_meta( $client_id, 'name', $name );
	update_post_meta( $client_id, 'email', strtolower( $email ) );
	update_post_meta( $client_id, 'thread_id', (string) $client_id );
	update_post_meta( $client_id, 'active', 1 );
	update_post_meta( $client_id, 'activation_code', $code );

	update_post_meta( $activation->ID, 'used', 1 );
	update_post_meta( $activation->ID, 'client_id', $client_id );
	update_post_meta( $activation->ID, 'email', strtolower( $email ) );

	// Welcome system message.
	$msg_id = wp_insert_post(
		array(
			'post_type'   => 'alf_chat_msg',
			'post_status' => 'publish',
			'post_title'  => 'Welcome',
			'post_content'=> sprintf(
				/* translators: %s: client name */
				__( 'Welcome %s. Your attorney and receptionist can see this chat. Send a message any time and we will respond during office hours.', 'access-law-firm' ),
				$name
			),
		)
	);
	if ( $msg_id && ! is_wp_error( $msg_id ) ) {
		update_post_meta( $msg_id, 'thread_id', (string) $client_id );
		update_post_meta( $msg_id, 'sender_id', 'system' );
		update_post_meta( $msg_id, 'sender_role', 'system' );
		update_post_meta( $msg_id, 'sender_name', 'Access Law Firm' );
		update_post_meta( $msg_id, 'urgent', 0 );
	}

	$token  = alf_issue_app_token( 'client', $client_id );
	$client = alf_serialize_app_client( $client_id );

	return rest_ensure_response(
		array(
			'token'  => $token,
			'client' => $client,
		)
	);
}

/**
 * Get one client.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_client_get( $request ) {
	$id = (int) $request['id'];
	$ok = alf_rest_assert_client_access( $request, $id );
	if ( is_wp_error( $ok ) ) {
		return $ok;
	}
	$data = alf_serialize_app_client( $id );
	if ( ! $data ) {
		return new WP_Error( 'alf_not_found', __( 'Client not found.', 'access-law-firm' ), array( 'status' => 404 ) );
	}
	return rest_ensure_response( $data );
}

/**
 * List all app clients (staff).
 *
 * @return WP_REST_Response
 */
function alf_rest_clients_list() {
	$posts = get_posts(
		array(
			'post_type'      => 'alf_app_client',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$items = array();
	foreach ( $posts as $post ) {
		$items[] = alf_serialize_app_client( $post->ID );
	}
	return rest_ensure_response( array( 'items' => $items ) );
}

/**
 * List activation codes (admin).
 *
 * @return WP_REST_Response
 */
function alf_rest_activation_list() {
	$posts = get_posts(
		array(
			'post_type'      => 'alf_activation',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$items = array();
	foreach ( $posts as $post ) {
		$items[] = array(
			'code'      => (string) get_post_meta( $post->ID, 'code', true ),
			'email'     => (string) get_post_meta( $post->ID, 'email', true ),
			'used'      => (bool) get_post_meta( $post->ID, 'used', true ),
			'clientId'  => get_post_meta( $post->ID, 'client_id', true ) ? (string) get_post_meta( $post->ID, 'client_id', true ) : null,
			'createdAt' => get_post_time( 'c', true, $post ),
		);
	}
	return rest_ensure_response( array( 'items' => $items ) );
}

/**
 * Create activation code (admin).
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_activation_create( $request ) {
	$email = sanitize_email( (string) $request->get_param( 'email' ) );
	$code  = strtoupper( sanitize_text_field( (string) $request->get_param( 'code' ) ) );
	if ( '' === $code ) {
		$code = 'ALF-' . strtoupper( wp_generate_password( 6, false, false ) );
	}

	if ( alf_find_activation_by_code( $code ) ) {
		return new WP_Error( 'alf_exists', __( 'That code already exists.', 'access-law-firm' ), array( 'status' => 409 ) );
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'alf_activation',
			'post_status' => 'publish',
			'post_title'  => $code,
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}

	update_post_meta( $id, 'code', $code );
	update_post_meta( $id, 'email', $email );
	update_post_meta( $id, 'used', 0 );

	return rest_ensure_response(
		array(
			'code'      => $code,
			'email'     => $email,
			'used'      => false,
			'createdAt' => get_post_time( 'c', true, $id ),
		)
	);
}

/**
 * List messages in a thread.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_messages_list( $request ) {
	$thread_id = sanitize_text_field( (string) $request['thread_id'] );
	$ok        = alf_rest_assert_client_access( $request, (int) $thread_id );
	if ( is_wp_error( $ok ) ) {
		// Staff may open any thread; clients only own.
		$tok = alf_resolve_app_token();
		if ( ! $tok || 'staff' !== $tok['type'] ) {
			return $ok;
		}
	}

	$posts = get_posts(
		array(
			'post_type'      => 'alf_chat_msg',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'meta_key'       => 'thread_id',
			'meta_value'     => $thread_id,
		)
	);

	$items = array();
	foreach ( $posts as $post ) {
		$items[] = array(
			'id'         => (string) $post->ID,
			'senderId'   => (string) get_post_meta( $post->ID, 'sender_id', true ),
			'senderRole' => (string) get_post_meta( $post->ID, 'sender_role', true ),
			'senderName' => (string) get_post_meta( $post->ID, 'sender_name', true ),
			'body'       => $post->post_content,
			'urgent'     => (bool) get_post_meta( $post->ID, 'urgent', true ),
			'createdAt'  => get_post_time( 'c', true, $post ),
		);
	}
	return rest_ensure_response( array( 'items' => $items ) );
}

/**
 * Send a chat message.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_messages_send( $request ) {
	$thread_id = sanitize_text_field( (string) $request['thread_id'] );
	$body      = sanitize_textarea_field( (string) $request->get_param( 'body' ) );
	$urgent    = (bool) $request->get_param( 'urgent' );

	if ( '' === trim( $body ) ) {
		return new WP_Error( 'alf_invalid', __( 'Message body is required.', 'access-law-firm' ), array( 'status' => 400 ) );
	}

	$tok = alf_resolve_app_token();
	if ( ! $tok ) {
		return new WP_Error( 'alf_unauthorized', __( 'Authentication required.', 'access-law-firm' ), array( 'status' => 401 ) );
	}

	if ( 'client' === $tok['type'] ) {
		if ( (int) $tok['owner_id'] !== (int) $thread_id ) {
			return new WP_Error( 'alf_forbidden', __( 'Access denied.', 'access-law-firm' ), array( 'status' => 403 ) );
		}
		$client = alf_serialize_app_client( (int) $tok['owner_id'] );
		$sender_id   = (string) $tok['owner_id'];
		$sender_role = 'client';
		$sender_name = $client ? $client['name'] : 'Client';
	} else {
		$user = alf_rest_current_staff();
		if ( is_wp_error( $user ) ) {
			return $user;
		}
		$profile     = alf_serialize_staff_user( $user );
		$sender_id   = (string) $user->ID;
		$sender_role = 'admin' === $profile['role'] ? 'receptionist' : $profile['role'];
		if ( 'attorney' === $profile['role'] ) {
			$sender_role = 'attorney';
		}
		$sender_name = $profile['name'];
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'alf_chat_msg',
			'post_status'  => 'publish',
			'post_title'   => wp_trim_words( $body, 8, '…' ),
			'post_content' => $body,
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}

	update_post_meta( $id, 'thread_id', $thread_id );
	update_post_meta( $id, 'sender_id', $sender_id );
	update_post_meta( $id, 'sender_role', $sender_role );
	update_post_meta( $id, 'sender_name', $sender_name );
	update_post_meta( $id, 'urgent', $urgent ? 1 : 0 );

	return rest_ensure_response(
		array(
			'id'         => (string) $id,
			'senderId'   => $sender_id,
			'senderRole' => $sender_role,
			'senderName' => $sender_name,
			'body'       => $body,
			'urgent'     => $urgent,
			'createdAt'  => get_post_time( 'c', true, $id ),
		)
	);
}

/**
 * Public lobby open/closed.
 *
 * @return WP_REST_Response
 */
function alf_rest_lobby_status() {
	$open = function_exists( 'alf_is_lobby_open' ) && alf_is_lobby_open();
	return rest_ensure_response(
		array(
			'open'          => $open,
			'lobby_open'    => $open,
			'waiting_count' => function_exists( 'alf_waiting_count' ) ? (int) alf_waiting_count( 0 ) : 0,
		)
	);
}

/**
 * Get lobby state for an app client.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_client_lobby_get( $request ) {
	$client_id = (int) $request['client_id'];
	$ok        = alf_rest_assert_client_access( $request, $client_id );
	if ( is_wp_error( $ok ) ) {
		return $ok;
	}
	return rest_ensure_response( alf_get_client_lobby_state( $client_id ) );
}

/**
 * Client enters video lobby — creates alf_lobby_visit linked to client.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_client_lobby_enter( $request ) {
	$client_id = (int) $request['client_id'];
	$ok        = alf_rest_assert_client_access( $request, $client_id );
	if ( is_wp_error( $ok ) ) {
		return $ok;
	}

	if ( ! function_exists( 'alf_is_lobby_open' ) || ! alf_is_lobby_open() ) {
		return new WP_Error( 'alf_closed', __( 'The Virtual Lobby is currently closed.', 'access-law-firm' ), array( 'status' => 403 ) );
	}

	$client = alf_serialize_app_client( $client_id );
	if ( ! $client ) {
		return new WP_Error( 'alf_not_found', __( 'Client not found.', 'access-law-firm' ), array( 'status' => 404 ) );
	}

	$existing_visit = (int) get_post_meta( $client_id, 'lobby_visit_id', true );
	if ( $existing_visit ) {
		$status = get_post_meta( $existing_visit, 'queue_status', true );
		if ( in_array( $status, array( 'waiting', 'ready', 'in_meeting', 'with_attorney' ), true ) ) {
			return rest_ensure_response( alf_get_client_lobby_state( $client_id ) );
		}
	}

	$now     = current_time( 'mysql' );
	$visit_id = wp_insert_post(
		array(
			'post_type'   => 'alf_lobby_visit',
			'post_status' => 'publish',
			'post_title'  => $client['name'],
			'post_author' => 0,
		),
		true
	);

	if ( is_wp_error( $visit_id ) || ! $visit_id ) {
		return new WP_Error( 'alf_checkin_failed', __( 'Could not enter lobby.', 'access-law-firm' ), array( 'status' => 500 ) );
	}

	update_post_meta( $visit_id, 'visitor_name', $client['name'] );
	update_post_meta( $visit_id, 'phone_e164', '' );
	update_post_meta( $visit_id, 'matter_type', 'App client' );
	update_post_meta( $visit_id, 'queue_status', 'waiting' );
	update_post_meta( $visit_id, 'checked_in_at', $now );
	update_post_meta( $visit_id, 'app_client_id', $client_id );

	$position = function_exists( 'alf_waiting_count' ) ? alf_waiting_count( $visit_id ) : 1;
	update_post_meta( $visit_id, 'position', $position );

	$token = wp_hash( $visit_id . '|' . $client_id . '|' . wp_salt( 'nonce' ) );
	set_transient( 'alf_visit_tok_' . $visit_id, $token, 4 * HOUR_IN_SECONDS );

	update_post_meta( $client_id, 'lobby_visit_id', $visit_id );
	update_post_meta( $client_id, 'lobby_visit_token', $token );

	return rest_ensure_response( alf_get_client_lobby_state( $client_id ) );
}

/**
 * Leave / complete lobby for client.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_client_lobby_leave( $request ) {
	$client_id = (int) $request['client_id'];
	$ok        = alf_rest_assert_client_access( $request, $client_id );
	if ( is_wp_error( $ok ) ) {
		return $ok;
	}

	$visit_id = (int) get_post_meta( $client_id, 'lobby_visit_id', true );
	if ( $visit_id ) {
		$status = get_post_meta( $visit_id, 'queue_status', true );
		if ( in_array( $status, array( 'waiting', 'ready', 'in_meeting', 'with_attorney' ), true ) ) {
			update_post_meta( $visit_id, 'queue_status', 'completed' );
		}
	}
	delete_post_meta( $client_id, 'lobby_visit_id' );
	delete_post_meta( $client_id, 'lobby_visit_token' );

	return rest_ensure_response( alf_get_client_lobby_state( $client_id ) );
}

/**
 * Public visitor check-in (parity with web Virtual Lobby).
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_lobby_check_in( $request ) {
	if ( ! function_exists( 'alf_is_lobby_open' ) || ! alf_is_lobby_open() ) {
		return new WP_Error( 'alf_closed', __( 'The Virtual Lobby is currently closed.', 'access-law-firm' ), array( 'status' => 403 ) );
	}

	$name    = sanitize_text_field( (string) $request->get_param( 'name' ) );
	$matter  = sanitize_text_field( (string) $request->get_param( 'matter' ) );
	$country = (string) $request->get_param( 'country' );
	$phone   = function_exists( 'alf_normalize_phone' )
		? alf_normalize_phone( (string) $request->get_param( 'phone' ), $country )
		: sanitize_text_field( (string) $request->get_param( 'phone' ) );

	$sms_on      = function_exists( 'alf_sms_enabled' ) && alf_sms_enabled();
	$verify_mode = function_exists( 'alf_lobby_verify_mode' ) ? alf_lobby_verify_mode() : 'none';

	if ( strlen( $name ) < 2 || '' === $matter ) {
		return new WP_Error( 'alf_invalid', __( 'Please complete your name and matter type.', 'access-law-firm' ), array( 'status' => 400 ) );
	}
	if ( $sms_on && '' === $phone ) {
		return new WP_Error( 'alf_invalid', __( 'Please enter a valid phone number.', 'access-law-firm' ), array( 'status' => 400 ) );
	}

	$verified_key = '';
	if ( $sms_on ) {
		$verified_key = 'alf_phone_ok_' . md5( $phone . '|' . wp_salt() );
		if ( ! get_transient( $verified_key ) ) {
			return new WP_Error( 'alf_verify', __( 'Please complete verification before checking in.', 'access-law-firm' ), array( 'status' => 403 ) );
		}
	} elseif ( 'captcha' === $verify_mode ) {
		$pass         = sanitize_text_field( (string) $request->get_param( 'verify_token' ) );
		$verified_key = $pass ? 'alf_verify_tok_' . $pass : '';
		if ( '' === $verified_key || ! get_transient( $verified_key ) ) {
			return new WP_Error( 'alf_verify', __( 'Please complete the CAPTCHA before checking in.', 'access-law-firm' ), array( 'status' => 403 ) );
		}
	}

	$now      = current_time( 'mysql' );
	$post_id  = wp_insert_post(
		array(
			'post_type'   => 'alf_lobby_visit',
			'post_status' => 'publish',
			'post_title'  => $name,
			'post_author' => 0,
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return new WP_Error( 'alf_checkin_failed', __( 'Could not complete check-in.', 'access-law-firm' ), array( 'status' => 500 ) );
	}

	update_post_meta( $post_id, 'visitor_name', $name );
	update_post_meta( $post_id, 'phone_e164', $phone );
	update_post_meta( $post_id, 'matter_type', $matter );
	update_post_meta( $post_id, 'queue_status', 'waiting' );
	update_post_meta( $post_id, 'checked_in_at', $now );

	$position = function_exists( 'alf_waiting_count' ) ? alf_waiting_count( $post_id ) : 1;
	update_post_meta( $post_id, 'position', $position );

	if ( '' !== $verified_key ) {
		delete_transient( $verified_key );
	}

	$token = wp_hash( $post_id . '|' . $phone . '|' . wp_salt( 'nonce' ) );
	set_transient( 'alf_visit_tok_' . $post_id, $token, 4 * HOUR_IN_SECONDS );

	return rest_ensure_response(
		array(
			'visit_id' => (int) $post_id,
			'token'    => $token,
			'position' => (int) $position,
		)
	);
}

/**
 * Poll public visit status (visit token).
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_visit_get( $request ) {
	$visit_id = (int) $request['id'];
	$token    = sanitize_text_field( (string) $request->get_param( 'token' ) );
	$stored   = get_transient( 'alf_visit_tok_' . $visit_id );

	if ( ! $visit_id || ! $token || ! $stored || ! hash_equals( (string) $stored, (string) $token ) ) {
		return new WP_Error( 'alf_forbidden', __( 'Visit session expired or invalid.', 'access-law-firm' ), array( 'status' => 403 ) );
	}

	$post = get_post( $visit_id );
	if ( ! $post || 'alf_lobby_visit' !== $post->post_type ) {
		return new WP_Error( 'alf_not_found', __( 'Visit not found.', 'access-law-firm' ), array( 'status' => 404 ) );
	}

	$status   = get_post_meta( $visit_id, 'queue_status', true );
	$position = ( 'waiting' === $status && function_exists( 'alf_waiting_count' ) )
		? alf_waiting_count( $visit_id )
		: (int) get_post_meta( $visit_id, 'position', true );

	$payload = array(
		'status'       => $status ? $status : 'waiting',
		'status_label' => function_exists( 'alf_queue_status_label' ) ? alf_queue_status_label( $status ? $status : 'waiting' ) : $status,
		'position'     => (int) $position,
		'zoom_url'     => '',
		'phase'        => 'waiting',
	);

	if ( in_array( $status, array( 'ready', 'in_meeting' ), true ) ) {
		$payload['zoom_url'] = function_exists( 'alf_zoom_meeting_url' ) ? alf_zoom_meeting_url() : '';
		$payload['phase']    = 'reception';
	} elseif ( 'with_attorney' === $status ) {
		$payload['zoom_url'] = function_exists( 'alf_zoom_attorney_url' ) ? alf_zoom_attorney_url() : '';
		$payload['phase']    = 'attorney';
	}

	return rest_ensure_response( $payload );
}

/**
 * Mark visit joined.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_visit_joined( $request ) {
	$visit_id = (int) $request['id'];
	$token    = sanitize_text_field( (string) $request->get_param( 'token' ) );
	$stored   = get_transient( 'alf_visit_tok_' . $visit_id );

	if ( ! $visit_id || ! $stored || ! hash_equals( (string) $stored, (string) $token ) ) {
		return new WP_Error( 'alf_forbidden', __( 'Invalid visit.', 'access-law-firm' ), array( 'status' => 403 ) );
	}

	$status = get_post_meta( $visit_id, 'queue_status', true );
	if ( 'ready' === $status ) {
		update_post_meta( $visit_id, 'queue_status', 'in_meeting' );
		return rest_ensure_response( array( 'status' => 'in_meeting' ) );
	}
	return rest_ensure_response( array( 'status' => $status ? $status : 'waiting' ) );
}

/**
 * Send OTP (reuse Twilio helpers).
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_otp_send( $request ) {
	if ( ! function_exists( 'alf_is_lobby_open' ) || ! alf_is_lobby_open() ) {
		return new WP_Error( 'alf_closed', __( 'The Virtual Lobby is currently closed.', 'access-law-firm' ), array( 'status' => 403 ) );
	}
	if ( function_exists( 'alf_sms_enabled' ) && ! alf_sms_enabled() ) {
		return new WP_Error( 'alf_sms_off', __( 'SMS verification is currently disabled.', 'access-law-firm' ), array( 'status' => 403 ) );
	}

	$country = (string) $request->get_param( 'country' );
	$phone   = alf_normalize_phone( (string) $request->get_param( 'phone' ), $country );
	if ( '' === $phone ) {
		return new WP_Error( 'alf_invalid', __( 'Please enter a valid phone number.', 'access-law-firm' ), array( 'status' => 400 ) );
	}
	if ( ! alf_twilio_is_configured() ) {
		return new WP_Error( 'alf_config', __( 'SMS verification is not configured yet.', 'access-law-firm' ), array( 'status' => 500 ) );
	}

	$throttle_key = alf_otp_throttle_key( $phone );
	$sends        = (int) get_transient( $throttle_key );
	if ( $sends >= 5 ) {
		return new WP_Error( 'alf_throttle', __( 'Too many attempts. Please wait.', 'access-law-firm' ), array( 'status' => 429 ) );
	}

	$code = (string) wp_rand( 100000, 999999 );
	set_transient( alf_otp_key( $phone ), $code, defined( 'ALF_OTP_TTL' ) ? ALF_OTP_TTL : 10 * MINUTE_IN_SECONDS );
	set_transient( $throttle_key, $sends + 1, HOUR_IN_SECONDS );

	$body = sprintf(
		__( 'Your Access Law Firm verification code is %s. It expires in 10 minutes.', 'access-law-firm' ),
		$code
	);
	$sent = alf_twilio_send_sms( $phone, $body );
	if ( is_wp_error( $sent ) ) {
		return $sent;
	}

	return rest_ensure_response(
		array(
			'message' => __( 'A verification code has been sent to your phone.', 'access-law-firm' ),
			'phone'   => $phone,
		)
	);
}

/**
 * Verify OTP.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_otp_verify( $request ) {
	$country = (string) $request->get_param( 'country' );
	$phone   = alf_normalize_phone( (string) $request->get_param( 'phone' ), $country );
	$code    = preg_replace( '/\D/', '', (string) $request->get_param( 'code' ) );

	if ( '' === $phone || 6 !== strlen( $code ) ) {
		return new WP_Error( 'alf_invalid', __( 'Please enter the complete 6-digit code.', 'access-law-firm' ), array( 'status' => 400 ) );
	}

	$key      = alf_otp_key( $phone );
	$expected = get_transient( $key );
	if ( false === $expected ) {
		return new WP_Error( 'alf_expired', __( 'Your code has expired. Please request a new one.', 'access-law-firm' ), array( 'status' => 410 ) );
	}
	if ( ! hash_equals( (string) $expected, (string) $code ) ) {
		return new WP_Error( 'alf_bad_code', __( 'That code is incorrect.', 'access-law-firm' ), array( 'status' => 401 ) );
	}

	delete_transient( $key );
	delete_transient( alf_otp_throttle_key( $phone ) );
	if ( function_exists( 'alf_mark_visitor_verified' ) ) {
		alf_mark_visitor_verified( $phone );
	}

	return rest_ensure_response( array( 'message' => __( 'Phone number verified.', 'access-law-firm' ) ) );
}

/**
 * Staff queue list (same data as web admin).
 *
 * @return WP_REST_Response
 */
function alf_rest_queue_list() {
	$query = new WP_Query(
		array(
			'post_type'      => 'alf_lobby_visit',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'date_query'     => array(
				array(
					'after'     => '7 days ago',
					'inclusive' => true,
				),
			),
			'meta_query'     => array(
				array(
					'key'     => 'queue_status',
					'value'   => array( 'waiting', 'ready', 'in_meeting', 'with_attorney' ),
					'compare' => 'IN',
				),
			),
		)
	);

	$waiting_ids = array();
	foreach ( $query->posts as $post ) {
		if ( 'waiting' === get_post_meta( $post->ID, 'queue_status', true ) ) {
			$waiting_ids[] = $post->ID;
		}
	}
	$waiting_pos = array_flip( $waiting_ids );
	$items       = array();

	foreach ( $query->posts as $post ) {
		$status     = get_post_meta( $post->ID, 'queue_status', true );
		$checked_in = get_post_meta( $post->ID, 'checked_in_at', true );
		$position   = isset( $waiting_pos[ $post->ID ] ) ? ( $waiting_pos[ $post->ID ] + 1 ) : (int) get_post_meta( $post->ID, 'position', true );
		$app_client = get_post_meta( $post->ID, 'app_client_id', true );

		$items[] = array(
			'id'           => (int) $post->ID,
			'name'         => get_post_meta( $post->ID, 'visitor_name', true ) ?: $post->post_title,
			'phone'        => get_post_meta( $post->ID, 'phone_e164', true ) ?: '—',
			'matter'       => get_post_meta( $post->ID, 'matter_type', true ),
			'status'       => $status,
			'status_label' => function_exists( 'alf_queue_status_label' ) ? alf_queue_status_label( $status ) : $status,
			'position'     => $position ? $position : '—',
			'wait'         => function_exists( 'alf_format_wait_time' ) ? alf_format_wait_time( $checked_in ) : '',
			'app_client_id'=> $app_client ? (string) $app_client : null,
		);
	}

	return rest_ensure_response(
		array(
			'items'      => $items,
			'lobby_open' => function_exists( 'alf_is_lobby_open' ) && alf_is_lobby_open(),
		)
	);
}

/**
 * Queue action: ready | transfer | complete | dismiss.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_queue_action( $request ) {
	$visit_id = (int) $request['id'];
	$action   = sanitize_key( (string) $request->get_param( 'action' ) );
	if ( '' === $action ) {
		$action = sanitize_key( (string) $request->get_param( 'queue_action' ) );
	}

	$post = get_post( $visit_id );
	if ( ! $post || 'alf_lobby_visit' !== $post->post_type ) {
		return new WP_Error( 'alf_not_found', __( 'Visit not found.', 'access-law-firm' ), array( 'status' => 404 ) );
	}

	$map = array(
		'ready'    => 'ready',
		'transfer' => 'with_attorney',
		'complete' => 'completed',
		'dismiss'  => 'dismissed',
	);
	if ( ! isset( $map[ $action ] ) ) {
		return new WP_Error( 'alf_invalid', __( 'Unknown action.', 'access-law-firm' ), array( 'status' => 400 ) );
	}

	$current = get_post_meta( $visit_id, 'queue_status', true );

	if ( 'ready' === $action && ! alf_zoom_meeting_url() ) {
		return new WP_Error( 'alf_zoom', __( 'Set the Reception Zoom URL before marking Ready.', 'access-law-firm' ), array( 'status' => 400 ) );
	}
	if ( 'transfer' === $action ) {
		if ( ! in_array( $current, array( 'ready', 'in_meeting' ), true ) ) {
			return new WP_Error( 'alf_invalid', __( 'Transfer is only available after Ready / reception.', 'access-law-firm' ), array( 'status' => 400 ) );
		}
		if ( ! alf_zoom_attorney_url() ) {
			return new WP_Error( 'alf_zoom', __( 'Set the Attorney Zoom URL before transferring.', 'access-law-firm' ), array( 'status' => 400 ) );
		}
	}

	$new_status = $map[ $action ];
	update_post_meta( $visit_id, 'queue_status', $new_status );
	if ( 'ready' === $new_status ) {
		update_post_meta( $visit_id, 'ready_at', current_time( 'mysql' ) );
	}
	if ( 'with_attorney' === $new_status ) {
		update_post_meta( $visit_id, 'transferred_at', current_time( 'mysql' ) );
	}

	// Sync app client lobby status when linked.
	$app_client = (int) get_post_meta( $visit_id, 'app_client_id', true );
	if ( $app_client && in_array( $new_status, array( 'completed', 'dismissed' ), true ) ) {
		delete_post_meta( $app_client, 'lobby_visit_id' );
		delete_post_meta( $app_client, 'lobby_visit_token' );
	}

	return rest_ensure_response(
		array(
			'status'  => $new_status,
			'message' => __( 'Updated.', 'access-law-firm' ),
		)
	);
}

/**
 * Toggle lobby open/closed.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function alf_rest_lobby_toggle( $request ) {
	$open = $request->get_param( 'open' );
	if ( null === $open ) {
		$open = ! alf_is_lobby_open();
	} else {
		$open = (bool) $open;
	}
	alf_set_lobby_open( $open );
	return rest_ensure_response(
		array(
			'lobby_open' => alf_is_lobby_open(),
			'open'       => alf_is_lobby_open(),
		)
	);
}

/**
 * Admin settings get.
 *
 * @return WP_REST_Response
 */
function alf_rest_admin_settings_get() {
	return rest_ensure_response(
		array(
			'receptionZoomUrl' => alf_zoom_meeting_url(),
			'attorneyZoomUrl'  => alf_zoom_attorney_url(),
			'verifyMode'       => function_exists( 'alf_lobby_verify_mode' ) ? alf_lobby_verify_mode() : 'none',
			'lobbyOpen'        => alf_is_lobby_open(),
		)
	);
}

/**
 * Admin settings update.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function alf_rest_admin_settings_put( $request ) {
	$reception = $request->get_param( 'receptionZoomUrl' );
	$attorney  = $request->get_param( 'attorneyZoomUrl' );

	if ( null !== $reception ) {
		alf_zoom_meeting_url( (string) $reception );
	}
	if ( null !== $attorney ) {
		alf_zoom_attorney_url( (string) $attorney );
	}

	return alf_rest_admin_settings_get();
}

/**
 * List appointments.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function alf_rest_appointments_list( $request ) {
	$client_id = $request->get_param( 'clientId' );
	$args      = array(
		'post_type'      => 'alf_appointment',
		'post_status'    => 'publish',
		'posts_per_page' => 100,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$tok = alf_resolve_app_token();
	if ( $tok && 'client' === $tok['type'] ) {
		$args['meta_key']   = 'client_id';
		$args['meta_value'] = (string) $tok['owner_id'];
	} elseif ( $client_id ) {
		$args['meta_key']   = 'client_id';
		$args['meta_value'] = (string) $client_id;
	}

	$posts = get_posts( $args );
	$items = array();
	foreach ( $posts as $post ) {
		$items[] = array(
			'id'              => (string) $post->ID,
			'clientId'        => (string) get_post_meta( $post->ID, 'client_id', true ),
			'clientName'      => (string) get_post_meta( $post->ID, 'client_name', true ),
			'preferredWindow' => (string) get_post_meta( $post->ID, 'preferred_window', true ),
			'note'            => (string) get_post_meta( $post->ID, 'note', true ),
			'status'          => (string) ( get_post_meta( $post->ID, 'status', true ) ?: 'requested' ),
			'createdAt'       => get_post_time( 'c', true, $post ),
		);
	}
	return rest_ensure_response( array( 'items' => $items ) );
}

/**
 * Create appointment.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_appointments_create( $request ) {
	$tok = alf_resolve_app_token();
	if ( ! $tok ) {
		return new WP_Error( 'alf_unauthorized', __( 'Authentication required.', 'access-law-firm' ), array( 'status' => 401 ) );
	}

	$client_id = (int) $request->get_param( 'clientId' );
	if ( 'client' === $tok['type'] ) {
		$client_id = (int) $tok['owner_id'];
	}
	$client = alf_serialize_app_client( $client_id );
	if ( ! $client ) {
		return new WP_Error( 'alf_not_found', __( 'Client not found.', 'access-law-firm' ), array( 'status' => 404 ) );
	}

	$window = sanitize_text_field( (string) $request->get_param( 'preferredWindow' ) );
	$note   = sanitize_textarea_field( (string) $request->get_param( 'note' ) );

	$id = wp_insert_post(
		array(
			'post_type'   => 'alf_appointment',
			'post_status' => 'publish',
			'post_title'  => $client['name'] . ' — ' . $window,
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}

	update_post_meta( $id, 'client_id', (string) $client_id );
	update_post_meta( $id, 'client_name', $client['name'] );
	update_post_meta( $id, 'preferred_window', $window );
	update_post_meta( $id, 'note', $note );
	update_post_meta( $id, 'status', 'requested' );

	return rest_ensure_response(
		array(
			'id'              => (string) $id,
			'clientId'        => (string) $client_id,
			'clientName'      => $client['name'],
			'preferredWindow' => $window,
			'note'            => $note,
			'status'          => 'requested',
			'createdAt'       => get_post_time( 'c', true, $id ),
		)
	);
}

/**
 * Update appointment status.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function alf_rest_appointments_patch( $request ) {
	$id     = (int) $request['id'];
	$status = sanitize_key( (string) $request->get_param( 'status' ) );
	$post   = get_post( $id );

	if ( ! $post || 'alf_appointment' !== $post->post_type ) {
		return new WP_Error( 'alf_not_found', __( 'Appointment not found.', 'access-law-firm' ), array( 'status' => 404 ) );
	}
	if ( ! in_array( $status, array( 'requested', 'confirmed', 'declined' ), true ) ) {
		return new WP_Error( 'alf_invalid', __( 'Invalid status.', 'access-law-firm' ), array( 'status' => 400 ) );
	}

	update_post_meta( $id, 'status', $status );
	return rest_ensure_response(
		array(
			'id'     => (string) $id,
			'status' => $status,
		)
	);
}

/**
 * Register push device token.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function alf_rest_device_register( $request ) {
	$owner_id = sanitize_text_field( (string) $request->get_param( 'ownerId' ) );
	$token    = sanitize_text_field( (string) $request->get_param( 'token' ) );
	$platform = sanitize_key( (string) $request->get_param( 'platform' ) );

	$tok = alf_resolve_app_token();
	if ( $tok ) {
		$owner_id = (string) $tok['owner_id'];
	}

	if ( $owner_id && $token ) {
		$key = 'alf_push_' . $owner_id;
		$existing = get_option( $key, array() );
		if ( ! is_array( $existing ) ) {
			$existing = array();
		}
		$existing[ $token ] = array(
			'platform' => $platform,
			'updated'  => time(),
		);
		update_option( $key, $existing, false );
	}

	return rest_ensure_response( array( 'ok' => true ) );
}
