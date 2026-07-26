<?php
/**
 * Admin settings for Access Law Firm theme.
 *
 * - Virtual Lobby Open/Closed lives on the WordPress Dashboard (widget).
 * - Verification toggles (SMS / CAPTCHA) and Twilio credentials under Settings > Access Law Firm.
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option name used to store all theme settings.
 */
if ( ! defined( 'ALF_OPTION_KEY' ) ) {
	define( 'ALF_OPTION_KEY', 'alf_settings' );
}

/**
 * Get a single setting value.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function alf_get_setting( $key, $default = '' ) {
	$settings = get_option( ALF_OPTION_KEY, array() );
	if ( is_array( $settings ) && isset( $settings[ $key ] ) ) {
		return $settings[ $key ];
	}
	return $default;
}

/**
 * Update a single setting without wiping the rest.
 *
 * @param string $key   Setting key.
 * @param mixed  $value Value to store.
 */
function alf_update_setting( $key, $value ) {
	$settings = get_option( ALF_OPTION_KEY, array() );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}
	$settings[ $key ] = $value;
	update_option( ALF_OPTION_KEY, $settings, true );
	// Ensure front-end / object-cache see the new value immediately.
	wp_cache_delete( ALF_OPTION_KEY, 'options' );
	wp_cache_delete( 'alloptions', 'options' );
}

/**
 * Sanitize a Microsoft Teams meeting join URL.
 *
 * Teams links often include characters that strict HTML5 url inputs reject.
 *
 * @param string $url Raw URL.
 * @return string
 */
function alf_sanitize_teams_url( $url ) {
	$url = trim( (string) $url );
	$url = preg_replace( '/\s+/', '', $url );
	if ( '' === $url ) {
		return '';
	}
	if ( ! preg_match( '#^https?://#i', $url ) ) {
		$url = 'https://' . ltrim( $url, '/' );
	}
	// Prefer WordPress sanitizer, but keep a safe fallback if it blanks a valid https URL.
	$clean = esc_url_raw( $url, array( 'http', 'https' ) );
	if ( $clean ) {
		return $clean;
	}
	if ( preg_match( '#^https://[^\s<>"\']+#i', $url, $m ) ) {
		return $m[0];
	}
	return '';
}

/**
 * Get / set Teams meeting URL (dedicated option for reliable saves).
 *
 * @param string|null $new_url Optional new value to save.
 * @return string
 */
function alf_teams_meeting_url( $new_url = null ) {
	if ( null !== $new_url ) {
		$clean = alf_sanitize_teams_url( $new_url );
		update_option( 'alf_teams_meeting_url', $clean, true );
		// Keep legacy key in sync for older reads.
		alf_update_setting( 'teams_meeting_url', $clean );
		wp_cache_delete( 'alf_teams_meeting_url', 'options' );
		return $clean;
	}

	$stored = get_option( 'alf_teams_meeting_url', '__missing__' );
	if ( '__missing__' !== $stored ) {
		return (string) $stored;
	}

	$legacy = (string) alf_get_setting( 'teams_meeting_url', '' );
	if ( '' !== $legacy ) {
		update_option( 'alf_teams_meeting_url', $legacy, true );
	}
	return $legacy;
}

/**
 * Whether the Virtual Lobby is currently open.
 *
 * Uses dedicated option `alf_lobby_open` (1/0) so saves are reliable.
 *
 * @return bool
 */
function alf_is_lobby_open() {
	$stored = get_option( 'alf_lobby_open', '__missing__' );

	if ( '__missing__' !== $stored ) {
		return 1 === (int) $stored;
	}

	// One-time migrate from older alf_settings['lobby_open'] if present.
	$legacy = alf_get_setting( 'lobby_open', null );
	if ( null !== $legacy && '' !== $legacy ) {
		$open = 1 === (int) $legacy ? 1 : 0;
		update_option( 'alf_lobby_open', $open, true );
		return (bool) $open;
	}

	// Default: open.
	return true;
}

/**
 * Persist lobby open/closed status.
 *
 * @param bool|int $open Open state.
 * @return bool Whether the option was updated.
 */
function alf_set_lobby_open( $open ) {
	$value   = $open ? 1 : 0;
	$updated = update_option( 'alf_lobby_open', $value, true );
	// update_option returns false when value is unchanged — still a success for our purposes.
	wp_cache_delete( 'alf_lobby_open', 'options' );
	wp_cache_delete( 'alloptions', 'options' );
	return true;
}

/**
 * Public AJAX: current lobby open/closed status for the front-end.
 */
function alf_ajax_public_lobby_status() {
	nocache_headers();
	$open = alf_is_lobby_open();
	wp_send_json_success(
		array(
			'open'  => $open,
			'label' => $open
				? __( 'Virtual Lobby Open', 'access-law-firm' )
				: __( 'Virtual Lobby Closed', 'access-law-firm' ),
		)
	);
}
add_action( 'wp_ajax_alf_lobby_status', 'alf_ajax_public_lobby_status' );
add_action( 'wp_ajax_nopriv_alf_lobby_status', 'alf_ajax_public_lobby_status' );

/**
 * Admin-post handler: toggle lobby via normal form submit (works without JS).
 */
function alf_handle_lobby_toggle_form() {
	if ( ! isset( $_POST['alf_lobby_toggle_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alf_lobby_toggle_nonce'] ) ), 'alf_lobby_toggle' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'access-law-firm' ), 403 );
	}

	if ( ! function_exists( 'alf_user_can_manage_lobby' ) || ! alf_user_can_manage_lobby() ) {
		wp_die( esc_html__( 'You do not have permission to change this.', 'access-law-firm' ), 403 );
	}

	// Hidden field sends 0; checked checkbox sends 1 (last value wins in PHP).
	$raw  = isset( $_POST['lobby_open'] ) ? wp_unslash( $_POST['lobby_open'] ) : '0';
	if ( is_array( $raw ) ) {
		$raw = end( $raw );
	}
	$open = ( '1' === (string) $raw ) ? 1 : 0;
	alf_set_lobby_open( $open );

	$redirect = wp_get_referer();
	if ( ! $redirect ) {
		$redirect = admin_url( 'admin.php?page=alf-virtual-lobby' );
	}
	$redirect = add_query_arg( 'alf_lobby_toggled', $open ? '1' : '0', $redirect );
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_alf_lobby_toggle', 'alf_handle_lobby_toggle_form' );

/**
 * Markup for the lobby open/close control (form submit — no JS required).
 *
 * @param string $context 'dashboard' or 'console'.
 */
function alf_render_lobby_toggle_control( $context = 'console' ) {
	$is_open = alf_is_lobby_open();
	$dot_id  = ( 'console' === $context ) ? 'alf-console-dot' : '';
	$label_id = ( 'console' === $context ) ? 'alf-console-status-label' : 'alf-lobby-status-label';
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="alf-lobby-toggle-form" style="display:contents">
		<?php wp_nonce_field( 'alf_lobby_toggle', 'alf_lobby_toggle_nonce' ); ?>
		<input type="hidden" name="action" value="alf_lobby_toggle">
		<input type="hidden" name="lobby_open" value="0">
		<label class="alf-lobby-toggle">
			<input
				type="checkbox"
				id="alf-lobby-open-toggle"
				name="lobby_open"
				value="1"
				<?php checked( $is_open, true ); ?>
				onchange="this.form.submit()"
			>
			<?php esc_html_e( 'Virtual Lobby Open', 'access-law-firm' ); ?>
		</label>
	</form>
	<?php
	unset( $dot_id, $label_id );
}

/**
 * Whether Twilio is fully configured.
 *
 * @return bool
 */
function alf_twilio_is_configured() {
	return alf_get_setting( 'twilio_sid' ) && alf_get_setting( 'twilio_token' ) && alf_get_setting( 'twilio_from' );
}

/* =========================================================
 * Settings page — Twilio only
 * ========================================================= */

/**
 * Register the settings page.
 */
function alf_register_settings_page() {
	add_options_page(
		__( 'Access Law Firm', 'access-law-firm' ),
		__( 'Access Law Firm', 'access-law-firm' ),
		'manage_options',
		'access-law-firm',
		'alf_render_settings_page'
	);
}
add_action( 'admin_menu', 'alf_register_settings_page' );

/**
 * Remind admins to add reCAPTCHA keys while SMS is off.
 */
function alf_captcha_keys_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! function_exists( 'alf_captcha_setting_enabled' ) || ! alf_captcha_setting_enabled() ) {
		return;
	}
	if ( function_exists( 'alf_captcha_is_configured' ) && alf_captcha_is_configured() ) {
		return;
	}
	if ( function_exists( 'alf_sms_enabled' ) && alf_sms_enabled() ) {
		return;
	}

	$url = admin_url( 'options-general.php?page=access-law-firm' );
	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Virtual Lobby is using CAPTCHA (SMS is off until Twilio is ready). Add your Google reCAPTCHA v2 keys under', 'access-law-firm' );
	echo ' <a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings → Access Law Firm', 'access-law-firm' ) . '</a>';
	echo ' ' . esc_html__( 'or spam protection will not run.', 'access-law-firm' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'alf_captcha_keys_admin_notice' );

/**
 * Register Twilio settings fields.
 */
function alf_register_settings() {
	register_setting(
		'alf_settings_group',
		ALF_OPTION_KEY,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'alf_sanitize_settings',
			'default'           => array(),
		)
	);

	add_settings_section(
		'alf_verify_section',
		__( 'Lobby verification', 'access-law-firm' ),
		'alf_verify_section_intro',
		'access-law-firm'
	);

	add_settings_field(
		'sms_enabled',
		__( 'Enable SMS (Twilio)', 'access-law-firm' ),
		'alf_field_checkbox',
		'access-law-firm',
		'alf_verify_section',
		array(
			'key'         => 'sms_enabled',
			'label'       => __( 'Require SMS code verification (turn on when Twilio is live)', 'access-law-firm' ),
			'default'     => 0,
		)
	);

	add_settings_field(
		'captcha_enabled',
		__( 'Enable CAPTCHA', 'access-law-firm' ),
		'alf_field_checkbox',
		'access-law-firm',
		'alf_verify_section',
		array(
			'key'         => 'captcha_enabled',
			'label'       => __( 'Require Google reCAPTCHA v2 (“I’m not a robot”) before check-in', 'access-law-firm' ),
			'default'     => 1,
		)
	);

	add_settings_field(
		'recaptcha_site_key',
		__( 'reCAPTCHA Site Key', 'access-law-firm' ),
		'alf_field_text',
		'access-law-firm',
		'alf_verify_section',
		array( 'key' => 'recaptcha_site_key', 'placeholder' => '6Lc...' )
	);

	add_settings_field(
		'recaptcha_secret_key',
		__( 'reCAPTCHA Secret Key', 'access-law-firm' ),
		'alf_field_password',
		'access-law-firm',
		'alf_verify_section',
		array( 'key' => 'recaptcha_secret_key' )
	);

	add_settings_section(
		'alf_twilio_section',
		__( 'Twilio SMS credentials', 'access-law-firm' ),
		'alf_twilio_section_intro',
		'access-law-firm'
	);

	add_settings_field(
		'twilio_sid',
		__( 'Twilio Account SID', 'access-law-firm' ),
		'alf_field_text',
		'access-law-firm',
		'alf_twilio_section',
		array( 'key' => 'twilio_sid', 'placeholder' => 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' )
	);

	add_settings_field(
		'twilio_token',
		__( 'Twilio Auth Token', 'access-law-firm' ),
		'alf_field_password',
		'access-law-firm',
		'alf_twilio_section',
		array( 'key' => 'twilio_token' )
	);

	add_settings_field(
		'twilio_from',
		__( 'Twilio From Number', 'access-law-firm' ),
		'alf_field_text',
		'access-law-firm',
		'alf_twilio_section',
		array( 'key' => 'twilio_from', 'placeholder' => '+1XXXXXXXXXX' )
	);
}
add_action( 'admin_init', 'alf_register_settings' );

/**
 * Intro for verification toggles.
 */
function alf_verify_section_intro() {
	$mode = function_exists( 'alf_lobby_verify_mode' ) ? alf_lobby_verify_mode() : 'none';
	echo '<p>' . esc_html__( 'While Twilio is pending, keep SMS off and use CAPTCHA. When Twilio is live, turn SMS on — you can keep CAPTCHA on as well.', 'access-law-firm' ) . '</p>';
	echo '<p class="description"><strong>' . esc_html__( 'Current active mode:', 'access-law-firm' ) . '</strong> ';
	if ( 'sms_captcha' === $mode ) {
		esc_html_e( 'SMS + CAPTCHA', 'access-law-firm' );
	} elseif ( 'sms' === $mode ) {
		esc_html_e( 'SMS verification', 'access-law-firm' );
	} elseif ( 'captcha' === $mode ) {
		esc_html_e( 'CAPTCHA only (SMS off or not configured)', 'access-law-firm' );
	} else {
		esc_html_e( 'Phone collected only — enable CAPTCHA keys or SMS', 'access-law-firm' );
	}
	echo '</p>';
	echo '<p class="description">' . esc_html__( 'Create keys at Google reCAPTCHA → choose v2 “I’m not a robot” Checkbox (not v3). Domains: accesslawoffice.com and www.accesslawoffice.com.', 'access-law-firm' ) . '</p>';
}

/**
 * Intro copy for the Twilio section.
 */
function alf_twilio_section_intro() {
	echo '<p>' . esc_html__( 'Save Twilio credentials now even if SMS is disabled. They will be used when you enable SMS above.', 'access-law-firm' ) . '</p>';
}

/**
 * Sanitize settings before save.
 *
 * @param array $input Raw input.
 * @return array
 */
function alf_sanitize_settings( $input ) {
	$existing = get_option( ALF_OPTION_KEY, array() );
	if ( ! is_array( $existing ) ) {
		$existing = array();
	}

	$output = $existing;

	$output['sms_enabled']     = ! empty( $input['sms_enabled'] ) ? 1 : 0;
	$output['captcha_enabled'] = ! empty( $input['captcha_enabled'] ) ? 1 : 0;

	if ( isset( $input['recaptcha_site_key'] ) ) {
		$output['recaptcha_site_key'] = sanitize_text_field( $input['recaptcha_site_key'] );
	}
	if ( isset( $input['recaptcha_secret_key'] ) ) {
		$secret = trim( $input['recaptcha_secret_key'] );
		if ( '' !== $secret ) {
			$output['recaptcha_secret_key'] = sanitize_text_field( $secret );
		}
	}

	if ( isset( $input['twilio_sid'] ) ) {
		$output['twilio_sid'] = sanitize_text_field( $input['twilio_sid'] );
	}
	if ( isset( $input['twilio_from'] ) ) {
		$output['twilio_from'] = sanitize_text_field( $input['twilio_from'] );
	}
	if ( isset( $input['twilio_token'] ) ) {
		$token = trim( $input['twilio_token'] );
		if ( '' !== $token ) {
			$output['twilio_token'] = sanitize_text_field( $token );
		}
	}

	return $output;
}

/**
 * Render: checkbox field.
 *
 * @param array $args Field args.
 */
function alf_field_checkbox( $args ) {
	$key     = $args['key'];
	$label   = isset( $args['label'] ) ? $args['label'] : '';
	$default = isset( $args['default'] ) ? (int) $args['default'] : 0;
	$value   = (int) alf_get_setting( $key, $default );
	printf(
		'<label><input type="checkbox" name="%1$s[%2$s]" value="1" %3$s> %4$s</label>',
		esc_attr( ALF_OPTION_KEY ),
		esc_attr( $key ),
		checked( 1, $value, false ),
		esc_html( $label )
	);
}

/**
 * Render: generic text field.
 *
 * @param array $args Field args.
 */
function alf_field_text( $args ) {
	$key         = $args['key'];
	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	$value       = alf_get_setting( $key );
	printf(
		'<input type="text" class="regular-text" name="%1$s[%2$s]" value="%3$s" placeholder="%4$s" autocomplete="off">',
		esc_attr( ALF_OPTION_KEY ),
		esc_attr( $key ),
		esc_attr( $value ),
		esc_attr( $placeholder )
	);
}

/**
 * Render: password/secret field (value masked; blank keeps existing).
 *
 * @param array $args Field args.
 */
function alf_field_password( $args ) {
	$key       = $args['key'];
	$has_value = (bool) alf_get_setting( $key );
	printf(
		'<input type="password" class="regular-text" name="%1$s[%2$s]" value="" placeholder="%3$s" autocomplete="new-password">',
		esc_attr( ALF_OPTION_KEY ),
		esc_attr( $key ),
		$has_value ? esc_attr__( '•••••••• (leave blank to keep current)', 'access-law-firm' ) : ''
	);
	if ( $has_value ) {
		echo '<p class="description">' . esc_html__( 'A token is already saved. Leave blank to keep it.', 'access-law-firm' ) . '</p>';
	}
}

/**
 * Render the settings page wrapper.
 */
function alf_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Access Law Firm — Verification & Twilio', 'access-law-firm' ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'alf_settings_group' );
			do_settings_sections( 'access-law-firm' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/* =========================================================
 * Dashboard widget — Virtual Lobby Open / Closed (shortcut)
 * Full queue lives under Virtual Lobby admin menu.
 * ========================================================= */

/**
 * Register the dashboard widget.
 */
function alf_register_dashboard_widget() {
	if ( ! function_exists( 'alf_user_can_manage_lobby' ) || ! alf_user_can_manage_lobby() ) {
		return;
	}

	wp_add_dashboard_widget(
		'alf_lobby_widget',
		__( 'Virtual Lobby', 'access-law-firm' ),
		'alf_render_dashboard_widget'
	);
}
add_action( 'wp_dashboard_setup', 'alf_register_dashboard_widget' );

/**
 * Render the Virtual Lobby dashboard widget.
 */
function alf_render_dashboard_widget() {
	$is_open = alf_is_lobby_open();

	if ( isset( $_GET['alf_lobby_toggled'] ) ) {
		$toggled_open = '1' === (string) wp_unslash( $_GET['alf_lobby_toggled'] );
		echo '<div class="notice notice-success inline"><p>' . esc_html(
			$toggled_open
				? __( 'Virtual Lobby is now Open.', 'access-law-firm' )
				: __( 'Virtual Lobby is now Closed.', 'access-law-firm' )
		) . '</p></div>';
	}
	?>
	<div class="alf-lobby-widget">
		<p class="alf-lobby-widget-status">
			<span class="alf-lobby-dot <?php echo $is_open ? 'is-open' : 'is-closed'; ?>" aria-hidden="true"></span>
			<strong id="alf-lobby-status-label">
				<?php
				echo $is_open
					? esc_html__( 'Virtual Lobby Open', 'access-law-firm' )
					: esc_html__( 'Virtual Lobby Closed', 'access-law-firm' );
				?>
			</strong>
		</p>
		<p class="description">
			<?php
			printf(
				/* translators: %s: Virtual Lobby admin URL */
				esc_html__( 'Quick toggle. Manage the visitor queue under %s.', 'access-law-firm' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=alf-virtual-lobby' ) ) . '">' . esc_html__( 'Virtual Lobby → Queue', 'access-law-firm' ) . '</a>'
			);
			?>
		</p>
		<p>
			<?php alf_render_lobby_toggle_control( 'dashboard' ); ?>
		</p>
	</div>
	<style>
		.alf-lobby-widget-status { display: flex; align-items: center; gap: 10px; font-size: 15px; margin-bottom: 8px; }
		.alf-lobby-dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; flex: none; }
		.alf-lobby-dot.is-open { background: #27b05d; box-shadow: 0 0 0 4px #eaf8ef; }
		.alf-lobby-dot.is-closed { background: #d92d20; box-shadow: 0 0 0 4px #fdeceb; }
		.alf-lobby-toggle { font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
	</style>
	<?php
}
