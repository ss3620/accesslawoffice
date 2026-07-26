<?php
/**
 * Admin settings for Access Law Firm theme.
 *
 * - Virtual Lobby Open/Closed lives on the WordPress Dashboard (widget).
 * - Twilio SMS credentials live under Settings > Access Law Firm.
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
 * Whether the Virtual Lobby is currently open.
 *
 * @return bool
 */
function alf_is_lobby_open() {
	// Cast via int so string "0" from the options table is correctly treated as closed.
	return 1 === (int) alf_get_setting( 'lobby_open', 1 );
}

/**
 * Public AJAX: current lobby open/closed status for the front-end.
 */
function alf_ajax_public_lobby_status() {
	nocache_headers();
	wp_send_json_success(
		array(
			'open'  => alf_is_lobby_open(),
			'label' => alf_is_lobby_open()
				? __( 'Virtual Lobby Open', 'access-law-firm' )
				: __( 'Virtual Lobby Closed', 'access-law-firm' ),
		)
	);
}
add_action( 'wp_ajax_alf_lobby_status', 'alf_ajax_public_lobby_status' );
add_action( 'wp_ajax_nopriv_alf_lobby_status', 'alf_ajax_public_lobby_status' );

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
		'alf_twilio_section',
		__( 'Twilio SMS Verification', 'access-law-firm' ),
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
 * Intro copy for the Twilio section.
 */
function alf_twilio_section_intro() {
	echo '<p>' . esc_html__( 'Enter your Twilio credentials to send the 6-digit verification code by SMS. Find these in your Twilio Console.', 'access-law-firm' ) . '</p>';
	echo '<p class="description">' . esc_html__( 'Receptionists manage the live queue under Virtual Lobby in the admin menu. Teams meeting URL is under Virtual Lobby → Settings.', 'access-law-firm' ) . '</p>';
}

/**
 * Sanitize Twilio settings before save.
 * Does not change lobby_open — that is controlled from the Dashboard widget.
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
		<h1><?php esc_html_e( 'Access Law Firm — Twilio SMS', 'access-law-firm' ); ?></h1>
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
			<label class="alf-lobby-toggle">
				<input type="checkbox" id="alf-lobby-open-toggle" value="1" <?php checked( $is_open, true ); ?>>
				<?php esc_html_e( 'Virtual Lobby Open', 'access-law-firm' ); ?>
			</label>
		</p>
		<p id="alf-lobby-widget-message" class="alf-lobby-widget-message" hidden></p>
	</div>
	<style>
		.alf-lobby-widget-status { display: flex; align-items: center; gap: 10px; font-size: 15px; margin-bottom: 8px; }
		.alf-lobby-dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; flex: none; }
		.alf-lobby-dot.is-open { background: #27b05d; box-shadow: 0 0 0 4px #eaf8ef; }
		.alf-lobby-dot.is-closed { background: #d92d20; box-shadow: 0 0 0 4px #fdeceb; }
		.alf-lobby-toggle { font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
		.alf-lobby-widget-message { margin-top: 8px; }
		.alf-lobby-widget-message.is-success { color: #1a7f37; }
		.alf-lobby-widget-message.is-error { color: #b42318; }
	</style>
	<?php
}
