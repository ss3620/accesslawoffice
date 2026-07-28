<?php
/**
 * Elementor duplicate home page (Option 1) — full sections + functionality.
 *
 * Keeps theme header / footer / Virtual Lobby.
 * Home (Elementor) uses shortcodes that render the real home sections
 * (lobby buttons, flip cards, practice modal, FAQ, etc.).
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Bump to force re-seed of the Elementor home page content. */
define( 'ALF_ELEMENTOR_HOME_SEED', 2 );

/**
 * Declare Elementor compatibility.
 */
function alf_elementor_theme_support() {
	add_theme_support( 'elementor' );
}
add_action( 'after_setup_theme', 'alf_elementor_theme_support' );

/**
 * Allowed home section slugs.
 *
 * @return string[]
 */
function alf_home_section_slugs() {
	return array( 'hero', 'process', 'practice', 'stats', 'about', 'faq' );
}

/**
 * Render a home section template part.
 *
 * @param string $slug Section slug.
 */
function alf_render_home_section( $slug ) {
	$slug = sanitize_key( $slug );
	if ( ! in_array( $slug, alf_home_section_slugs(), true ) ) {
		return;
	}
	get_template_part( 'template-parts/home/' . $slug );
}

/**
 * Shortcode: [alf_home_section name="hero"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function alf_shortcode_home_section( $atts ) {
	$atts = shortcode_atts(
		array(
			'name' => '',
		),
		$atts,
		'alf_home_section'
	);

	ob_start();
	alf_render_home_section( $atts['name'] );
	return (string) ob_get_clean();
}
add_shortcode( 'alf_home_section', 'alf_shortcode_home_section' );

/**
 * Shortcode: [alf_home] — full home body (all sections).
 *
 * @return string
 */
function alf_shortcode_home_all() {
	ob_start();
	foreach ( alf_home_section_slugs() as $slug ) {
		alf_render_home_section( $slug );
	}
	return (string) ob_get_clean();
}
add_shortcode( 'alf_home', 'alf_shortcode_home_all' );

/**
 * Whether a post is built with Elementor.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function alf_is_elementor_page( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return false;
	}
	return 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/**
 * Whether the site front should render the Elementor static page.
 *
 * @return bool
 */
function alf_use_elementor_front() {
	if ( 'page' !== get_option( 'show_on_front' ) ) {
		return false;
	}
	$front_id = (int) get_option( 'page_on_front' );
	return alf_is_elementor_page( $front_id );
}

/**
 * Get seeded Elementor home page ID.
 *
 * @return int
 */
function alf_get_elementor_home_page_id() {
	return (int) get_option( 'alf_elementor_home_page_id', 0 );
}

/**
 * Generate a short Elementor element id.
 *
 * @return string
 */
function alf_elementor_uid() {
	return substr( md5( uniqid( (string) wp_rand(), true ) ), 0, 7 );
}

/**
 * One Elementor section wrapping a shortcode widget.
 *
 * @param string $label    Editor label (via HTML heading for clarity).
 * @param string $shortcode Shortcode string.
 * @return array
 */
function alf_elementor_shortcode_section( $label, $shortcode ) {
	return array(
		'id'       => alf_elementor_uid(),
		'elType'   => 'section',
		'settings' => array(
			'layout'                => 'full_width',
			'gap'                   => 'no',
			'content_position'      => 'top',
			'structure'             => '10',
			'_title'                => $label,
		),
		'elements' => array(
			array(
				'id'       => alf_elementor_uid(),
				'elType'   => 'column',
				'settings' => array(
					'_column_size' => 100,
					'_inline_size' => null,
				),
				'elements' => array(
					array(
						'id'         => alf_elementor_uid(),
						'elType'     => 'widget',
						'widgetType' => 'shortcode',
						'settings'   => array(
							'shortcode' => $shortcode,
						),
						'elements'   => array(),
					),
				),
				'isInner'  => false,
			),
		),
		'isInner'  => false,
	);
}

/**
 * Full Elementor document: every home section as its own shortcode block
 * so the client can reorder/hide sections in Elementor while keeping
 * lobby / flip cards / practice modal / FAQ behavior.
 *
 * @return array
 */
function alf_elementor_home_document_data() {
	$sections = array(
		'Hero + credentials'   => '[alf_home_section name="hero"]',
		'How it works'         => '[alf_home_section name="process"]',
		'Practice areas'       => '[alf_home_section name="practice"]',
		'Stats'                => '[alf_home_section name="stats"]',
		'About the founder'    => '[alf_home_section name="about"]',
		'FAQ + Virtual Lobby'  => '[alf_home_section name="faq"]',
	);

	$data = array();
	foreach ( $sections as $label => $shortcode ) {
		$data[] = alf_elementor_shortcode_section( $label, $shortcode );
	}
	return $data;
}

/**
 * Apply Elementor meta + layout JSON to a page.
 *
 * @param int $page_id Page ID.
 */
function alf_apply_elementor_home_layout( $page_id ) {
	$page_id = (int) $page_id;
	if ( $page_id <= 0 ) {
		return;
	}

	$data = alf_elementor_home_document_data();
	$json = wp_json_encode( $data );
	if ( ! $json ) {
		return;
	}

	update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $page_id, '_elementor_data', wp_slash( $json ) );
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
	}
	update_post_meta( $page_id, '_wp_page_template', 'default' );
	// Clear Elementor CSS so shortcodes render fresh.
	delete_post_meta( $page_id, '_elementor_css' );

	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}

/**
 * Create or refresh the Elementor duplicate home page.
 */
function alf_maybe_create_elementor_home_page() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		return;
	}

	$page_id = alf_get_elementor_home_page_id();
	if ( ! $page_id || ! get_post( $page_id ) ) {
		$by_slug = get_page_by_path( 'home-elementor' );
		if ( $by_slug ) {
			$page_id = (int) $by_slug->ID;
			update_option( 'alf_elementor_home_page_id', $page_id, false );
		}
	}

	$seeded = (int) get_option( 'alf_elementor_home_seed', 0 );

	if ( ! $page_id ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => 'Home (Elementor)',
				'post_name'    => 'home-elementor',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( is_wp_error( $page_id ) || ! $page_id ) {
			return;
		}

		update_option( 'alf_elementor_home_page_id', (int) $page_id, false );
		$seeded = 0;
	}

	if ( $seeded < ALF_ELEMENTOR_HOME_SEED ) {
		alf_apply_elementor_home_layout( (int) $page_id );
		update_option( 'alf_elementor_home_seed', ALF_ELEMENTOR_HOME_SEED, false );
	}
}
add_action( 'admin_init', 'alf_maybe_create_elementor_home_page' );

/**
 * Admin tools: refresh layout button + notice.
 */
function alf_elementor_home_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! defined( 'ELEMENTOR_VERSION' ) ) {
		return;
	}

	$page_id = alf_get_elementor_home_page_id();
	if ( ! $page_id || ! get_post( $page_id ) ) {
		return;
	}

	// Handle manual refresh.
	if ( isset( $_GET['alf_refresh_elementor_home'] ) && check_admin_referer( 'alf_refresh_elementor_home' ) ) {
		alf_apply_elementor_home_layout( $page_id );
		update_option( 'alf_elementor_home_seed', ALF_ELEMENTOR_HOME_SEED, false );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Home (Elementor) was refreshed with all homepage sections and functionality.', 'access-law-firm' ) . '</p></div>';
	}

	$edit_url    = admin_url( 'post.php?post=' . $page_id . '&action=elementor' );
	$view_url    = get_permalink( $page_id );
	$reading     = admin_url( 'options-reading.php' );
	$refresh_url = wp_nonce_url( admin_url( 'index.php?alf_refresh_elementor_home=1' ), 'alf_refresh_elementor_home' );
	$is_front    = ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) === $page_id );
	?>
	<div class="notice notice-info is-dismissible">
		<p>
			<strong><?php esc_html_e( 'Home (Elementor) — full sections', 'access-law-firm' ); ?></strong>
			<?php esc_html_e( 'Includes hero, credentials, process, practice areas (with modal), stats, about, FAQ, and Virtual Lobby buttons. Theme header/footer/lobby stay.', 'access-law-firm' ); ?>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Edit with Elementor', 'access-law-firm' ); ?></a>
			<a class="button" href="<?php echo esc_url( $view_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View page', 'access-law-firm' ); ?></a>
			<a class="button" href="<?php echo esc_url( $refresh_url ); ?>"><?php esc_html_e( 'Refresh all sections', 'access-law-firm' ); ?></a>
			<?php if ( ! $is_front ) : ?>
				<a class="button" href="<?php echo esc_url( $reading ); ?>"><?php esc_html_e( 'Set as homepage (Settings → Reading)', 'access-law-firm' ); ?></a>
			<?php else : ?>
				<span style="margin-left:8px;color:#00a32a;"><?php esc_html_e( 'This page is the current homepage.', 'access-law-firm' ); ?></span>
			<?php endif; ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'In Elementor you can reorder or hide sections. Each block is a shortcode that keeps the real site functionality. To change section text permanently, edit the theme home template parts or replace a shortcode block with Elementor widgets.', 'access-law-firm' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'alf_elementor_home_admin_notice' );
