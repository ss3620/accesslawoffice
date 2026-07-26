<?php
/**
 * Lobby visit CPT and public/admin AJAX for check-in and queue.
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom post type for lobby visits.
 */
function alf_register_lobby_visit_cpt() {
	register_post_type(
		'alf_lobby_visit',
		array(
			'labels'              => array(
				'name'          => __( 'Lobby Visits', 'access-law-firm' ),
				'singular_name' => __( 'Lobby Visit', 'access-law-firm' ),
			),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'alf_register_lobby_visit_cpt' );

/**
 * Count waiting visits ahead of (or including) a visit for position.
 *
 * @param int $visit_id Optional visit ID.
 * @return int
 */
function alf_waiting_count( $visit_id = 0 ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'alf_lobby_visit',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'   => 'queue_status',
					'value' => 'waiting',
				),
			),
		)
	);

	if ( ! $visit_id ) {
		return (int) $query->found_posts;
	}

	$position = 1;
	foreach ( $query->posts as $id ) {
		if ( (int) $id === (int) $visit_id ) {
			return $position;
		}
		$position++;
	}

	return $position;
}

/**
 * Format wait time from checked-in timestamp.
 *
 * @param string $checked_in_at MySQL datetime or timestamp string.
 * @return string
 */
function alf_format_wait_time( $checked_in_at ) {
	$ts = $checked_in_at ? strtotime( $checked_in_at ) : false;
	if ( ! $ts ) {
		return '—';
	}
	$mins = max( 0, (int) floor( ( time() - $ts ) / 60 ) );
	if ( $mins < 1 ) {
		return __( 'Just now', 'access-law-firm' );
	}
	/* translators: %d: minutes waiting */
	return sprintf( _n( '%d min', '%d mins', $mins, 'access-law-firm' ), $mins );
}

/**
 * Human label for queue status.
 *
 * @param string $status Status slug.
 * @return string
 */
function alf_queue_status_label( $status ) {
	$labels = array(
		'waiting'    => __( 'Waiting', 'access-law-firm' ),
		'ready'      => __( 'Ready', 'access-law-firm' ),
		'in_meeting' => __( 'In meeting', 'access-law-firm' ),
		'completed'  => __( 'Completed', 'access-law-firm' ),
		'dismissed'  => __( 'Dismissed', 'access-law-firm' ),
	);
	return isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
}

/**
 * Public AJAX: create a lobby visit after OTP + matter selection.
 */
function alf_ajax_check_in() {
	check_ajax_referer( 'alf_lobby', 'nonce' );

	if ( ! alf_is_lobby_open() ) {
		wp_send_json_error( array( 'message' => __( 'The Virtual Lobby is currently closed.', 'access-law-firm' ) ), 403 );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$matter  = isset( $_POST['matter'] ) ? sanitize_text_field( wp_unslash( $_POST['matter'] ) ) : '';
	$country = isset( $_POST['country'] ) ? wp_unslash( $_POST['country'] ) : '';
	$phone   = alf_normalize_phone( isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '', $country );

	$sms_on      = function_exists( 'alf_sms_enabled' ) && alf_sms_enabled();
	$verify_mode = function_exists( 'alf_lobby_verify_mode' ) ? alf_lobby_verify_mode() : 'none';

	if ( strlen( $name ) < 2 || '' === $matter ) {
		wp_send_json_error( array( 'message' => __( 'Please complete your name and matter type.', 'access-law-firm' ) ), 400 );
	}

	// Phone is only required while SMS verification is on (phone step is skipped otherwise).
	if ( $sms_on && '' === $phone ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid phone number.', 'access-law-firm' ) ), 400 );
	}

	$verified_key = '';
	if ( $sms_on ) {
		// Gate: recent successful OTP for this phone.
		$verified_key = 'alf_phone_ok_' . md5( $phone . '|' . wp_salt() );
		if ( ! get_transient( $verified_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Please complete verification before checking in.', 'access-law-firm' ) ), 403 );
		}
	} elseif ( 'captcha' === $verify_mode ) {
		// Gate: one-time CAPTCHA pass issued by alf_verify_captcha.
		$pass = isset( $_POST['verify_token'] ) ? sanitize_text_field( wp_unslash( $_POST['verify_token'] ) ) : '';
		$verified_key = $pass ? 'alf_verify_tok_' . $pass : '';
		if ( '' === $verified_key || ! get_transient( $verified_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Please complete the CAPTCHA before checking in.', 'access-law-firm' ) ), 403 );
		}
	}

	$now = current_time( 'mysql' );
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'alf_lobby_visit',
			'post_status' => 'publish',
			'post_title'  => $name,
			'post_author' => 0,
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		wp_send_json_error( array( 'message' => __( 'Could not complete check-in. Please try again.', 'access-law-firm' ) ), 500 );
	}

	update_post_meta( $post_id, 'visitor_name', $name );
	update_post_meta( $post_id, 'phone_e164', $phone );
	update_post_meta( $post_id, 'matter_type', $matter );
	update_post_meta( $post_id, 'queue_status', 'waiting' );
	update_post_meta( $post_id, 'checked_in_at', $now );

	$position = alf_waiting_count( $post_id );
	update_post_meta( $post_id, 'position', $position );

	// One-time use of verification flag for this check-in.
	if ( '' !== $verified_key ) {
		delete_transient( $verified_key );
	}

	$token = wp_hash( $post_id . '|' . $phone . '|' . wp_salt( 'nonce' ) );
	set_transient( 'alf_visit_tok_' . $post_id, $token, 4 * HOUR_IN_SECONDS );

	wp_send_json_success(
		array(
			'visit_id' => (int) $post_id,
			'token'    => $token,
			'position' => (int) $position,
			'message'  => __( 'You are checked in.', 'access-law-firm' ),
		)
	);
}
add_action( 'wp_ajax_alf_check_in', 'alf_ajax_check_in' );
add_action( 'wp_ajax_nopriv_alf_check_in', 'alf_ajax_check_in' );

/**
 * Public AJAX: poll visit status (client waiting room).
 */
function alf_ajax_visit_status() {
	check_ajax_referer( 'alf_lobby', 'nonce' );

	$visit_id = isset( $_POST['visit_id'] ) ? absint( $_POST['visit_id'] ) : 0;
	$token    = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';

	if ( ! $visit_id || ! $token ) {
		wp_send_json_error( array( 'message' => __( 'Invalid visit.', 'access-law-firm' ) ), 400 );
	}

	$stored = get_transient( 'alf_visit_tok_' . $visit_id );
	if ( ! $stored || ! hash_equals( (string) $stored, (string) $token ) ) {
		wp_send_json_error( array( 'message' => __( 'Visit session expired. Please check in again.', 'access-law-firm' ) ), 403 );
	}

	$post = get_post( $visit_id );
	if ( ! $post || 'alf_lobby_visit' !== $post->post_type ) {
		wp_send_json_error( array( 'message' => __( 'Visit not found.', 'access-law-firm' ) ), 404 );
	}

	$status   = get_post_meta( $visit_id, 'queue_status', true );
	$position = alf_waiting_count( $visit_id );
	if ( 'waiting' !== $status ) {
		$position = (int) get_post_meta( $visit_id, 'position', true );
	}

	$payload = array(
		'status'       => $status ? $status : 'waiting',
		'status_label' => alf_queue_status_label( $status ? $status : 'waiting' ),
		'position'     => (int) $position,
		'teams_url'    => '',
	);

	if ( in_array( $status, array( 'ready', 'in_meeting' ), true ) ) {
		$payload['teams_url'] = esc_url_raw( alf_teams_meeting_url() );
	}

	wp_send_json_success( $payload );
}
add_action( 'wp_ajax_alf_visit_status', 'alf_ajax_visit_status' );
add_action( 'wp_ajax_nopriv_alf_visit_status', 'alf_ajax_visit_status' );

/**
 * Public AJAX: client opened Teams link — mark in_meeting.
 */
function alf_ajax_visit_joined() {
	check_ajax_referer( 'alf_lobby', 'nonce' );

	$visit_id = isset( $_POST['visit_id'] ) ? absint( $_POST['visit_id'] ) : 0;
	$token    = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';
	$stored   = get_transient( 'alf_visit_tok_' . $visit_id );

	if ( ! $visit_id || ! $stored || ! hash_equals( (string) $stored, (string) $token ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid visit.', 'access-law-firm' ) ), 403 );
	}

	$status = get_post_meta( $visit_id, 'queue_status', true );
	if ( 'ready' === $status ) {
		update_post_meta( $visit_id, 'queue_status', 'in_meeting' );
	}

	wp_send_json_success( array( 'status' => 'in_meeting' ) );
}
add_action( 'wp_ajax_alf_visit_joined', 'alf_ajax_visit_joined' );
add_action( 'wp_ajax_nopriv_alf_visit_joined', 'alf_ajax_visit_joined' );

/**
 * Admin AJAX: list active queue items.
 */
function alf_ajax_queue_list() {
	check_ajax_referer( 'alf_lobby_admin', 'nonce' );

	if ( ! function_exists( 'alf_user_can_manage_lobby' ) || ! alf_user_can_manage_lobby() ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'access-law-firm' ) ), 403 );
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'alf_lobby_visit',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'date',
			'order'          => 'ASC',
			// Active visits from the last 7 days (avoid “today-only” timezone misses).
			'date_query'     => array(
				array(
					'after'     => '7 days ago',
					'inclusive' => true,
				),
			),
			'meta_query'     => array(
				array(
					'key'     => 'queue_status',
					'value'   => array( 'waiting', 'ready', 'in_meeting' ),
					'compare' => 'IN',
				),
			),
		)
	);

	$waiting_ids = array();
	$items       = array();

	foreach ( $query->posts as $post ) {
		$status = get_post_meta( $post->ID, 'queue_status', true );
		if ( 'waiting' === $status ) {
			$waiting_ids[] = $post->ID;
		}
	}

	$waiting_pos = array_flip( $waiting_ids );
	foreach ( $query->posts as $post ) {
		$status      = get_post_meta( $post->ID, 'queue_status', true );
		$checked_in  = get_post_meta( $post->ID, 'checked_in_at', true );
		$position    = isset( $waiting_pos[ $post->ID ] ) ? ( $waiting_pos[ $post->ID ] + 1 ) : (int) get_post_meta( $post->ID, 'position', true );

		$items[] = array(
			'id'           => (int) $post->ID,
			'name'         => get_post_meta( $post->ID, 'visitor_name', true ) ?: $post->post_title,
			'phone'        => get_post_meta( $post->ID, 'phone_e164', true ) ?: '—',
			'matter'       => get_post_meta( $post->ID, 'matter_type', true ),
			'status'       => $status,
			'status_label' => alf_queue_status_label( $status ),
			'position'     => $position ? $position : '—',
			'wait'         => alf_format_wait_time( $checked_in ),
		);
	}

	wp_send_json_success( array( 'items' => $items ) );
}
add_action( 'wp_ajax_alf_queue_list', 'alf_ajax_queue_list' );

/**
 * Admin AJAX: update visit status (ready / complete / dismiss).
 */
function alf_ajax_queue_update() {
	check_ajax_referer( 'alf_lobby_admin', 'nonce' );

	if ( ! function_exists( 'alf_user_can_manage_lobby' ) || ! alf_user_can_manage_lobby() ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'access-law-firm' ) ), 403 );
	}

	$visit_id = isset( $_POST['visit_id'] ) ? absint( $_POST['visit_id'] ) : 0;
	$action   = isset( $_POST['queue_action'] ) ? sanitize_key( wp_unslash( $_POST['queue_action'] ) ) : '';

	$post = get_post( $visit_id );
	if ( ! $post || 'alf_lobby_visit' !== $post->post_type ) {
		wp_send_json_error( array( 'message' => __( 'Visit not found.', 'access-law-firm' ) ), 404 );
	}

	$map = array(
		'ready'    => 'ready',
		'complete' => 'completed',
		'dismiss'  => 'dismissed',
	);

	if ( ! isset( $map[ $action ] ) ) {
		wp_send_json_error( array( 'message' => __( 'Unknown action.', 'access-law-firm' ) ), 400 );
	}

	if ( 'ready' === $action ) {
		if ( ! alf_teams_meeting_url() ) {
			wp_send_json_error(
				array(
					'message' => __( 'Set a Teams meeting URL under Virtual Lobby → Settings before marking Ready.', 'access-law-firm' ),
				),
				400
			);
		}
	}

	$new_status = $map[ $action ];
	update_post_meta( $visit_id, 'queue_status', $new_status );

	if ( 'ready' === $new_status ) {
		update_post_meta( $visit_id, 'ready_at', current_time( 'mysql' ) );
	}

	wp_send_json_success(
		array(
			'status'  => $new_status,
			'message' => __( 'Updated.', 'access-law-firm' ),
		)
	);
}
add_action( 'wp_ajax_alf_queue_update', 'alf_ajax_queue_update' );
