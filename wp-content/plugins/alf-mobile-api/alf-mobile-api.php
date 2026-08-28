<?php
/**
 * Plugin Name: Access Law Firm — Mobile API
 * Description: REST API (/wp-json/alf/v1/) that connects the Flutter app to WordPress lobby, clients, chat, and staff auth.
 * Version: 1.0.2
 * Author: Access Law Office
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package ALF_Mobile_API
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALF_MOBILE_API_VERSION', '1.0.2' );
define( 'ALF_MOBILE_API_LOADED', true );
define( 'ALF_MOBILE_API_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Load API after the theme so lobby helpers (alf_is_lobby_open, etc.) exist.
 */
function alf_mobile_api_bootstrap() {
	// Avoid fatal redeclare if the theme already bundled the same files.
	if ( ! function_exists( 'alf_register_app_cpts' ) ) {
		require_once ALF_MOBILE_API_DIR . 'app-data.php';
	}
	if ( ! function_exists( 'alf_register_rest_routes' ) ) {
		require_once ALF_MOBILE_API_DIR . 'rest-api.php';
	}
}
add_action( 'after_setup_theme', 'alf_mobile_api_bootstrap', 20 );

/**
 * JWT Authentication for WP-API is not used by this app and often breaks
 * /alf/v1 when JWT_AUTH_SECRET_KEY is missing. Clear those errors for our routes.
 *
 * @param mixed $result Auth result.
 * @return mixed
 */
function alf_mobile_api_ignore_jwt_errors( $result ) {
	if ( ! alf_mobile_api_is_alf_request() ) {
		return $result;
	}
	if ( is_wp_error( $result ) ) {
		$code = $result->get_error_code();
		if ( is_string( $code ) && 0 === strpos( $code, 'jwt_' ) ) {
			return true;
		}
	}
	return $result;
}
add_filter( 'rest_authentication_errors', 'alf_mobile_api_ignore_jwt_errors', 99 );

/**
 * Whether the current request targets /wp-json/alf/.
 *
 * @return bool
 */
function alf_mobile_api_is_alf_request() {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	if ( false !== strpos( $uri, '/wp-json/alf/' ) || false !== strpos( $uri, 'rest_route=/alf/' ) ) {
		return true;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		$route = isset( $GLOBALS['wp']->query_vars['rest_route'] ) ? $GLOBALS['wp']->query_vars['rest_route'] : '';
		if ( is_string( $route ) && 0 === strpos( $route, '/alf/' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Admin notice if the Access Law Firm theme helpers are missing.
 */
function alf_mobile_api_theme_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	if ( function_exists( 'alf_is_lobby_open' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Access Law Firm — Mobile API works best with the Access Law Firm theme active (lobby / Zoom helpers).', 'alf-mobile-api' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'alf_mobile_api_theme_notice' );

/**
 * Warn admins that the JWT plugin is unnecessary / misconfigured for the app.
 */
function alf_mobile_api_jwt_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$jwt_active = is_plugin_active( 'jwt-authentication-for-wp-rest-api/jwt-auth.php' )
		|| is_plugin_active( 'jwt-auth/jwt-auth.php' );
	if ( ! $jwt_active ) {
		return;
	}
	if ( defined( 'JWT_AUTH_SECRET_KEY' ) && JWT_AUTH_SECRET_KEY ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'JWT Authentication for WP-API is active but not configured. The mobile app does not need it — deactivate that plugin, or add JWT_AUTH_SECRET_KEY to wp-config.php.', 'alf-mobile-api' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'alf_mobile_api_jwt_notice' );
