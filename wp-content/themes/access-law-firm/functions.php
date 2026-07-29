<?php
/**
 * Access Law Firm theme functions.
 *
 * @package Access_Law_Firm
 * @author  shailendra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALF_THEME_VERSION', '1.4.0' );

require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/twilio-otp.php';
require_once get_template_directory() . '/inc/captcha.php';
require_once get_template_directory() . '/inc/lobby-admin.php';
require_once get_template_directory() . '/inc/lobby-visits.php';
require_once get_template_directory() . '/inc/elementor-home.php';

/**
 * Theme setup.
 */
function alf_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 80,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
}
add_action( 'after_setup_theme', 'alf_theme_setup' );

/**
 * Enqueue styles and scripts.
 */
function alf_enqueue_assets() {
	wp_enqueue_style(
		'access-law-firm-style',
		get_stylesheet_uri(),
		array(),
		ALF_THEME_VERSION
	);

	$deps = array();
	if ( function_exists( 'alf_captcha_enabled' ) && alf_captcha_enabled() ) {
		wp_enqueue_script(
			'google-recaptcha',
			'https://www.google.com/recaptcha/api.js?render=explicit',
			array(),
			null,
			true
		);
		$deps[] = 'google-recaptcha';
	}

	wp_enqueue_script(
		'access-law-firm-main',
		get_template_directory_uri() . '/assets/js/main.js',
		$deps,
		ALF_THEME_VERSION,
		true
	);

	wp_localize_script(
		'access-law-firm-main',
		'alfLobby',
		array(
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'alf_lobby' ),
			'lobbyOpen'        => alf_is_lobby_open(),
			'smsConfigured'    => alf_twilio_is_configured(),
			'smsEnabled'       => function_exists( 'alf_sms_enabled' ) && alf_sms_enabled(),
			'captchaEnabled'   => function_exists( 'alf_captcha_enabled' ) && alf_captcha_enabled(),
			'recaptchaSiteKey' => function_exists( 'alf_recaptcha_site_key' ) ? alf_recaptcha_site_key() : '',
			'verifyMode'       => function_exists( 'alf_lobby_verify_mode' ) ? alf_lobby_verify_mode() : 'none',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'alf_enqueue_assets' );

/**
 * Helper: theme image URL.
 *
 * @param string $filename Image filename inside assets/img.
 * @return string
 */
function alf_img( $filename ) {
	return esc_url( get_template_directory_uri() . '/assets/img/' . ltrim( $filename, '/' ) );
}
