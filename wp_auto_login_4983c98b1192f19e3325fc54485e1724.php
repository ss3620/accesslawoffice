<?php
//
// Automatically login a single WordPress user upon arrival to main page.
//
function append_sg_auto_param($url) {
	// Prefer WordPress helper if available
	if (function_exists('add_query_arg')) {
		return add_query_arg('sg_auto', '1', $url);
	}

	// Fallback if WP helpers aren't loaded
	if (strpos($url, '?') !== false) {
		$url .= '&';
	} else {
		$url .= '?';
	}
	$url .= 'sg_auto=1';
	return $url;
}

function auto_login() {
	if (!is_user_logged_in()) {
		// Admin user_id
		$admins = get_users(array('role'=>'administrator'));
		$user_id = $admins[0]->ID;
		$user = get_user_by( 'ID', $user_id );
		if ( !$user ) {
			error_log("wp_auto_login: user_id==1 not found redirecting to admin_url");
			$redirect_page = append_sg_auto_param(admin_url());
			wp_redirect( $redirect_page );
			exit();
		}
		$loginusername = $user->user_login;
		wp_set_current_user($user_id, $loginusername);
		wp_set_auth_cookie($user_id);
		// Go to admin area
		$redirect_page = append_sg_auto_param(admin_url());
		error_log("wp_auto_login: trying to login user_id==1 loginusername=$loginusername to admin_url=$redirect_page");
		wp_redirect( $redirect_page );
		exit();
	}
}

// Initialize wordpress
define( 'WP_USE_THEMES', true );

$time_since_script_creation = time() - stat(__FILE__)['mtime'];

// Delete itself to make sure it is executed only once
unlink(__FILE__);

if ( ! isset( $wp_did_header ) ) {
	$wp_did_header = true;

	// Load the WordPress library.
	require_once( dirname( __FILE__ ) . '/wp-load.php' );

	// If the user is already logged in just redirect it to admin area
	if ( is_user_logged_in() ) {
		$redirect_page = append_sg_auto_param(admin_url());
		wp_redirect( $redirect_page );
		exit();
	}

	// Avalon auto-login
	// If script is older than 30 minutes, doesn't log in as admin
	if ($time_since_script_creation < 30 + 60) {
		auto_login();
	}

	// Set up the WordPress query
	wp();

	// Load the theme template
	require_once( ABSPATH . WPINC . '/template-loader.php' );
}
