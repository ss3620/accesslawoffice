<?php
/**
 * Admin settings for Access Law Firm theme.
 *
 * Adds a settings page under Settings > Access Law Firm with:
 *  - Virtual Lobby Open on/off toggle (controls header status).
 *  - Twilio credentials for SMS verification (SID, Auth Token, From number).
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
 * Whether the Virtual Lobby is currently open.
 *
 * @return bool
 */
function alf_is_lobby_open() {
	// Cast via int so string "0" from the options table is correctly treated as closed.
	return 1 === (int) alf_get_setting( 'lobby_open', 1 );
}

/**
 * Whether Twilio is fully configured.
 *
 * @return bool
 */
function alf_twilio_is_configured() {
	return alf_get_setting( 'twilio_sid' ) && alf_get_setting( 'twilio_token' ) && alf_get_setting( 'twilio_from' );
}

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
 * Register settings, sections and fields.
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
		'alf_lobby_section',
		__( 'Virtual Lobby', 'access-law-firm' ),
		'__return_false',
		'access-law-firm'
	);

	add_settings_field(
		'lobby_open',
		__( 'Virtual Lobby Open', 'access-law-firm' ),
		'alf_field_lobby_open',
		'access-law-firm',
		'alf_lobby_section'
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

	$output               = $existing;
	$output['lobby_open'] = ! empty( $input['lobby_open'] ) ? 1 : 0;

	if ( isset( $input['twilio_sid'] ) ) {
		$output['twilio_sid'] = sanitize_text_field( $input['twilio_sid'] );
	}
	if ( isset( $input['twilio_from'] ) ) {
		$output['twilio_from'] = sanitize_text_field( $input['twilio_from'] );
	}
	if ( isset( $input['twilio_token'] ) ) {
		// Keep existing token if the field was submitted empty (masked display).
		$token = trim( $input['twilio_token'] );
		if ( '' !== $token ) {
			$output['twilio_token'] = sanitize_text_field( $token );
		}
	}

	return $output;
}

/**
 * Render: lobby open toggle.
 */
function alf_field_lobby_open() {
	$value = alf_is_lobby_open();
	?>
	<label class="alf-switch">
		<input type="checkbox" name="<?php echo esc_attr( ALF_OPTION_KEY ); ?>[lobby_open]" value="1" <?php checked( $value, true ); ?>>
		<?php esc_html_e( 'Show "Virtual Lobby Open" in the site header', 'access-law-firm' ); ?>
	</label>
	<p class="description"><?php esc_html_e( 'When off, the header shows "Virtual Lobby Closed" and the check-in popup shows a closed notice.', 'access-law-firm' ); ?></p>
	<?php
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
	$key      = $args['key'];
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
		<h1><?php esc_html_e( 'Access Law Firm Settings', 'access-law-firm' ); ?></h1>
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
