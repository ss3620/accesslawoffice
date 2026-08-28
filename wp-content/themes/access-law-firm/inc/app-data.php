<?php
/**
 * App data layer: activation codes, clients, messages, appointments.
 * Powers the Flutter app via /wp-json/alf/v1/.
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register CPTs used by the mobile app.
 */
function alf_register_app_cpts() {
	$common = array(
		'public'              => false,
		'show_ui'             => false,
		'show_in_menu'        => false,
		'show_in_rest'        => false,
		'supports'            => array( 'title' ),
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'exclude_from_search' => true,
	);

	register_post_type(
		'alf_app_client',
		array_merge(
			$common,
			array(
				'labels' => array(
					'name'          => __( 'App Clients', 'access-law-firm' ),
					'singular_name' => __( 'App Client', 'access-law-firm' ),
				),
			)
		)
	);

	register_post_type(
		'alf_activation',
		array_merge(
			$common,
			array(
				'labels' => array(
					'name'          => __( 'Activation Codes', 'access-law-firm' ),
					'singular_name' => __( 'Activation Code', 'access-law-firm' ),
				),
			)
		)
	);

	register_post_type(
		'alf_chat_msg',
		array_merge(
			$common,
			array(
				'labels' => array(
					'name'          => __( 'Chat Messages', 'access-law-firm' ),
					'singular_name' => __( 'Chat Message', 'access-law-firm' ),
				),
			)
		)
	);

	register_post_type(
		'alf_appointment',
		array_merge(
			$common,
			array(
				'labels' => array(
					'name'          => __( 'Appointments', 'access-law-firm' ),
					'singular_name' => __( 'Appointment', 'access-law-firm' ),
				),
			)
		)
	);
}
add_action( 'init', 'alf_register_app_cpts' );

/**
 * Seed a demo activation code once (ALF-DEMO).
 */
function alf_maybe_seed_demo_activation() {
	if ( get_option( 'alf_demo_code_seeded' ) ) {
		return;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'alf_activation',
			'posts_per_page' => 1,
			'meta_key'       => 'code',
			'meta_value'     => 'ALF-DEMO',
			'fields'         => 'ids',
		)
	);

	if ( empty( $existing ) ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'alf_activation',
				'post_status' => 'publish',
				'post_title'  => 'ALF-DEMO',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, 'code', 'ALF-DEMO' );
			update_post_meta( $id, 'email', '' );
			update_post_meta( $id, 'used', 0 );
		}
	}

	update_option( 'alf_demo_code_seeded', 1, true );
}
add_action( 'init', 'alf_maybe_seed_demo_activation', 20 );

/**
 * Find activation code post by code string.
 *
 * @param string $code Code.
 * @return WP_Post|null
 */
function alf_find_activation_by_code( $code ) {
	$code = strtoupper( trim( (string) $code ) );
	if ( '' === $code ) {
		return null;
	}
	$posts = get_posts(
		array(
			'post_type'      => 'alf_activation',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => 'code',
			'meta_value'     => $code,
		)
	);
	return ! empty( $posts[0] ) ? $posts[0] : null;
}

/**
 * Serialize an app client for the API.
 *
 * @param int $client_id Post ID.
 * @return array|null
 */
function alf_serialize_app_client( $client_id ) {
	$post = get_post( $client_id );
	if ( ! $post || 'alf_app_client' !== $post->post_type ) {
		return null;
	}

	return array(
		'id'             => (string) $post->ID,
		'name'           => (string) get_post_meta( $post->ID, 'name', true ),
		'email'          => (string) get_post_meta( $post->ID, 'email', true ),
		'threadId'       => (string) ( get_post_meta( $post->ID, 'thread_id', true ) ?: $post->ID ),
		'active'         => (bool) get_post_meta( $post->ID, 'active', true ),
		'activationCode' => (string) get_post_meta( $post->ID, 'activation_code', true ),
		'createdAt'      => get_post_time( 'c', true, $post ),
	);
}

/**
 * Serialize a lobby visit as lobby state for an app client.
 *
 * @param int $client_id App client ID.
 * @return array
 */
function alf_get_client_lobby_state( $client_id ) {
	$visit_id = (int) get_post_meta( $client_id, 'lobby_visit_id', true );
	$status   = 'idle';
	$position = 0;
	$reception = function_exists( 'alf_zoom_meeting_url' ) ? (string) alf_zoom_meeting_url() : '';
	$attorney  = function_exists( 'alf_zoom_attorney_url' ) ? (string) alf_zoom_attorney_url() : '';

	if ( $visit_id ) {
		$post = get_post( $visit_id );
		if ( $post && 'alf_lobby_visit' === $post->post_type ) {
			$raw = (string) get_post_meta( $visit_id, 'queue_status', true );
			$map = array(
				'waiting'       => 'waiting',
				'ready'         => 'ready',
				'in_meeting'    => 'ready',
				'with_attorney' => 'withAttorney',
				'completed'     => 'completed',
				'dismissed'     => 'completed',
			);
			$status = isset( $map[ $raw ] ) ? $map[ $raw ] : 'idle';
			if ( 'waiting' === $raw && function_exists( 'alf_waiting_count' ) ) {
				$position = (int) alf_waiting_count( $visit_id );
			} else {
				$position = (int) get_post_meta( $visit_id, 'position', true );
			}
		}
	}

	return array(
		'clientId'          => (string) $client_id,
		'status'            => $status,
		'receptionZoomUrl'  => $reception,
		'attorneyZoomUrl'   => $attorney,
		'position'          => $position,
		'updatedAt'         => gmdate( 'c' ),
		'visitId'           => $visit_id ? (string) $visit_id : null,
	);
}

/**
 * Issue / validate app tokens (staff or client).
 *
 * @param string $type  'staff' | 'client'.
 * @param int    $owner_id User ID or client post ID.
 * @return string Token.
 */
function alf_issue_app_token( $type, $owner_id ) {
	$token = wp_generate_password( 48, false, false );
	$key   = 'alf_app_tok_' . hash( 'sha256', $token );
	set_transient(
		$key,
		array(
			'type'     => $type,
			'owner_id' => (int) $owner_id,
			'issued'   => time(),
		),
		30 * DAY_IN_SECONDS
	);
	return $token;
}

/**
 * Resolve Bearer token from request.
 *
 * @return array|null { type, owner_id } or null.
 */
function alf_resolve_app_token() {
	$header = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ) ) : '';
	if ( '' === $header && isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
		$header = sanitize_text_field( wp_unslash( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) );
	}
	if ( ! preg_match( '/Bearer\s+(\S+)/i', $header, $m ) ) {
		return null;
	}
	$key  = 'alf_app_tok_' . hash( 'sha256', $m[1] );
	$data = get_transient( $key );
	return is_array( $data ) ? $data : null;
}

/**
 * Map WP user to staff profile payload.
 *
 * @param WP_User $user User.
 * @return array
 */
function alf_serialize_staff_user( $user ) {
	$role = 'receptionist';
	if ( user_can( $user, 'manage_options' ) || in_array( 'administrator', (array) $user->roles, true ) ) {
		$role = 'admin';
	} elseif ( in_array( 'alf_receptionist', (array) $user->roles, true ) ) {
		$role = 'receptionist';
	}

	$caps = array();
	if ( user_can( $user, 'alf_manage_lobby' ) || user_can( $user, 'manage_options' ) ) {
		$caps[] = 'alf_manage_lobby';
	}
	if ( user_can( $user, 'manage_options' ) ) {
		$caps[] = 'manage_options';
	}

	return array(
		'id'           => (string) $user->ID,
		'name'         => $user->display_name,
		'email'        => $user->user_email,
		'role'         => $role,
		'capabilities' => $caps,
	);
}

/**
 * Create or return an existing unified appointment record.
 *
 * @param array $args Appointment fields.
 * @return int Appointment post ID, or 0 on failure.
 */
function alf_create_appointment_record( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'client_id'        => '',
			'client_name'      => '',
			'email'            => '',
			'phone'            => '',
			'preferred_window' => '',
			'note'             => '',
			'status'           => 'requested',
			'source'           => 'app',
			'visit_id'         => 0,
		)
	);

	$visit_id = (int) $args['visit_id'];
	if ( $visit_id > 0 ) {
		$existing = get_posts(
			array(
				'post_type'      => 'alf_appointment',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => 'visit_id',
				'meta_value'     => (string) $visit_id,
			)
		);
		if ( ! empty( $existing ) ) {
			return (int) $existing[0];
		}
	}

	$name   = sanitize_text_field( (string) $args['client_name'] );
	$window = sanitize_text_field( (string) $args['preferred_window'] );
	if ( '' === $name || '' === $window ) {
		return 0;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'alf_appointment',
			'post_status' => 'publish',
			'post_title'  => $name . ' — ' . $window,
		),
		true
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}

	update_post_meta( $id, 'client_id', sanitize_text_field( (string) $args['client_id'] ) );
	update_post_meta( $id, 'client_name', $name );
	update_post_meta( $id, 'email', sanitize_email( (string) $args['email'] ) );
	update_post_meta( $id, 'phone', sanitize_text_field( (string) $args['phone'] ) );
	update_post_meta( $id, 'preferred_window', $window );
	update_post_meta( $id, 'note', sanitize_textarea_field( (string) $args['note'] ) );
	update_post_meta( $id, 'status', sanitize_key( (string) $args['status'] ) ?: 'requested' );
	update_post_meta( $id, 'source', sanitize_key( (string) $args['source'] ) ?: 'app' );
	if ( $visit_id > 0 ) {
		update_post_meta( $id, 'visit_id', (string) $visit_id );
	}

	return (int) $id;
}

/**
 * Serialize an appointment post for the REST API / mobile app.
 *
 * @param int $post_id Appointment post ID.
 * @return array|null
 */
function alf_serialize_appointment( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post || 'alf_appointment' !== $post->post_type ) {
		return null;
	}

	$visit_id = (int) get_post_meta( $post_id, 'visit_id', true );

	return array(
		'id'              => (string) $post_id,
		'clientId'        => (string) get_post_meta( $post_id, 'client_id', true ),
		'clientName'      => (string) get_post_meta( $post_id, 'client_name', true ),
		'email'           => (string) get_post_meta( $post_id, 'email', true ),
		'phone'           => (string) get_post_meta( $post_id, 'phone', true ),
		'preferredWindow' => (string) get_post_meta( $post_id, 'preferred_window', true ),
		'note'            => (string) get_post_meta( $post_id, 'note', true ),
		'status'          => (string) ( get_post_meta( $post_id, 'status', true ) ?: 'requested' ),
		'source'          => (string) ( get_post_meta( $post_id, 'source', true ) ?: 'app' ),
		'visitId'         => $visit_id ? (string) $visit_id : null,
		'createdAt'       => get_post_time( 'c', true, $post_id ),
	);
}

/**
 * Mirror a lobby visit into the unified appointments list.
 *
 * @param int    $visit_id  Lobby visit post ID.
 * @param string $source    `website` or `app`.
 * @param string $client_id Optional app client ID.
 * @return int Appointment post ID.
 */
function alf_sync_appointment_from_lobby_visit( $visit_id, $source = 'website', $client_id = '' ) {
	$visit_id = (int) $visit_id;
	if ( $visit_id <= 0 ) {
		return 0;
	}

	$name   = (string) get_post_meta( $visit_id, 'visitor_name', true );
	$phone  = (string) get_post_meta( $visit_id, 'phone_e164', true );
	$matter = (string) get_post_meta( $visit_id, 'matter_type', true );
	if ( '' === $client_id ) {
		$client_id = (string) get_post_meta( $visit_id, 'app_client_id', true );
	}

	$email = '';
	if ( '' !== $client_id && function_exists( 'alf_serialize_app_client' ) ) {
		$client = alf_serialize_app_client( (int) $client_id );
		if ( $client ) {
			$email = (string) $client['email'];
			if ( '' === $name ) {
				$name = (string) $client['name'];
			}
		}
	}
	if ( '' === $name ) {
		$post = get_post( $visit_id );
		$name = $post ? $post->post_title : __( 'Visitor', 'access-law-firm' );
	}

	return alf_create_appointment_record(
		array(
			'client_id'        => $client_id,
			'client_name'      => $name,
			'email'            => $email,
			'phone'            => $phone,
			'preferred_window' => __( 'Virtual Lobby — immediate', 'access-law-firm' ),
			'note'             => $matter,
			'status'           => 'requested',
			'source'           => $source,
			'visit_id'         => $visit_id,
		)
	);
}
