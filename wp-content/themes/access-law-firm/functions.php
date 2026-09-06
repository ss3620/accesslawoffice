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

define( 'ALF_THEME_VERSION', '1.5.1' );

require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/twilio-otp.php';
require_once get_template_directory() . '/inc/captcha.php';
require_once get_template_directory() . '/inc/lobby-admin.php';
require_once get_template_directory() . '/inc/lobby-appointments-admin.php';
require_once get_template_directory() . '/inc/lobby-clients-admin.php';
require_once get_template_directory() . '/inc/lobby-visits.php';
// Mobile API: prefer the "Access Law Firm — Mobile API" plugin when active;
// otherwise load from the theme so local/dev still works.
if ( ! defined( 'ALF_MOBILE_API_LOADED' ) ) {
	require_once get_template_directory() . '/inc/app-data.php';
	require_once get_template_directory() . '/inc/rest-api.php';
}
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

/**
 * Helper: Call Now / Text Now buttons.
 *
 * @param string $class Button classes shared by both links.
 */
function alf_render_call_text_buttons( $class = 'btn btn-secondary' ) {
	$phone = alf_firm_phone_e164();
	if ( '' === $phone ) {
		return;
	}

	$label = alf_firm_phone_display();
	?>
	<a class="<?php echo esc_attr( $class ); ?> btn-call" href="tel:<?php echo esc_attr( $phone ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: phone number */ __( 'Call us at %s', 'access-law-firm' ), $label ) ); ?>">
		<span class="btn-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6.6 3.5h2.2l1.4 3.6-1.8 1.3a11 11 0 0 0 5.2 5.2l1.3-1.8 3.6 1.4v2.2a2 2 0 0 1-2.2 2A15.8 15.8 0 0 1 4.6 5.7a2 2 0 0 1 2-2.2Z"/></svg>
		</span>
		<?php esc_html_e( 'Call Now', 'access-law-firm' ); ?>
	</a>
	<a class="<?php echo esc_attr( $class ); ?> btn-text" href="sms:<?php echo esc_attr( $phone ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: phone number */ __( 'Text us at %s', 'access-law-firm' ), $label ) ); ?>">
		<span class="btn-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12.5a7.5 7.5 0 0 1-10.9 6.7L4 20.5l1.4-4.6A7.5 7.5 0 1 1 20 12.5Z"/></svg>
		</span>
		<?php esc_html_e( 'Text Now', 'access-law-firm' ); ?>
	</a>
	<?php
}
