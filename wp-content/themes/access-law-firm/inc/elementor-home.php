<?php
/**
 * Elementor duplicate home page (Option 1).
 *
 * - Keeps theme header / footer / Virtual Lobby.
 * - Creates an Elementor-editable page the client can customize.
 * - When that page is set as the static front page, front-page.php yields to it.
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare Elementor compatibility.
 */
function alf_elementor_theme_support() {
	add_theme_support( 'elementor' );
}
add_action( 'after_setup_theme', 'alf_elementor_theme_support' );

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
 * Whether the site front should render the Elementor static page (not PHP front-page).
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
 * Build starter Elementor sections mirroring the current home copy.
 *
 * @return array
 */
function alf_elementor_home_document_data() {
	$lobby_btn = array(
		'id'         => alf_elementor_uid(),
		'elType'     => 'widget',
		'widgetType' => 'button',
		'settings'   => array(
			'text'            => 'Join Virtual Lobby',
			'link'            => array(
				'url'         => '#',
				'is_external' => '',
				'nofollow'    => '',
			),
			'align'           => 'left',
			'background_color'=> '#082c62',
			'button_text_color'=> '#ffffff',
							'css_classes'     => 'open-lobby',
							'_css_classes'    => 'open-lobby',
		),
		'elements'   => array(),
	);

	$hero_image = alf_img( 'stock-attorney.png' );
	$about_image = alf_img( 'stock-office.png' );

	return array(
		// Hero
		array(
			'id'       => alf_elementor_uid(),
			'elType'   => 'section',
			'settings' => array(
				'layout'     => 'boxed',
				'gap'        => 'extended',
				'content_width' => array( 'unit' => 'px', 'size' => 1180 ),
			),
			'elements' => array(
				array(
					'id'       => alf_elementor_uid(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 55 ),
					'elements' => array(
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title'     => 'Former Immigration Judge',
								'header_size'=> 'div',
								'title_color'=> '#c9922f',
								'typography_typography' => 'custom',
								'typography_font_size' => array( 'unit' => 'px', 'size' => 14 ),
							),
							'elements'   => array(),
						),
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title' => 'Experience From Every Side of the Immigration System.',
								'header_size' => 'h1',
								'title_color' => '#082c62',
							),
							'elements'   => array(),
						),
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p>Strategic, compassionate representation informed by federal service as an Immigration Judge, Asylum Officer, and USCIS Immigration Officer.</p>',
							),
							'elements'   => array(),
						),
						$lobby_btn,
					),
					'isInner'  => false,
				),
				array(
					'id'       => alf_elementor_uid(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 45 ),
					'elements' => array(
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'image',
							'settings'   => array(
								'image' => array(
									'url' => $hero_image,
									'id'  => '',
								),
								'image_size' => 'large',
							),
							'elements'   => array(),
						),
					),
					'isInner'  => false,
				),
			),
			'isInner'  => false,
		),
		// Process
		array(
			'id'       => alf_elementor_uid(),
			'elType'   => 'section',
			'settings' => array(
				'layout' => 'boxed',
			),
			'elements' => array(
				array(
					'id'       => alf_elementor_uid(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title' => 'A simpler way to reach your lawyer.',
								'header_size' => 'h2',
								'align' => 'center',
								'title_color' => '#082c62',
							),
							'elements'   => array(),
						),
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center">Enter the Virtual Lobby, speak with a live assistant, and connect with the attorney.</p>',
								'align'  => 'center',
							),
							'elements'   => array(),
						),
					),
					'isInner'  => false,
				),
			),
			'isInner'  => false,
		),
		// Steps row
		array(
			'id'       => alf_elementor_uid(),
			'elType'   => 'section',
			'settings' => array( 'layout' => 'boxed' ),
			'elements' => array(
				alf_elementor_step_column( '1', 'Join Lobby', 'Check in through the secure Virtual Lobby.' ),
				alf_elementor_step_column( '2', 'Security Check', 'Complete a quick verification before joining.' ),
				alf_elementor_step_column( '3', 'Live Assistant', 'A team member greets you and gathers details.' ),
				alf_elementor_step_column( '4', 'Talk to Attorney', 'Speak with the attorney or arrange the next step.' ),
			),
			'isInner'  => false,
		),
		// Practice areas
		array(
			'id'       => alf_elementor_uid(),
			'elType'   => 'section',
			'settings' => array( 'layout' => 'boxed' ),
			'elements' => array(
				array(
					'id'       => alf_elementor_uid(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title' => 'Immigration representation built around your case.',
								'header_size' => 'h2',
								'align' => 'center',
								'title_color' => '#082c62',
							),
							'elements'   => array(),
						),
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center">Removal Defense · Asylum · Family Immigration · Hardship Waivers · Naturalization &amp; Citizenship · Employment Visas</p><p style="text-align:center">Edit this section in Elementor to add cards, icons, or detail pages.</p>',
							),
							'elements'   => array(),
						),
					),
					'isInner'  => false,
				),
			),
			'isInner'  => false,
		),
		// About
		array(
			'id'       => alf_elementor_uid(),
			'elType'   => 'section',
			'settings' => array( 'layout' => 'boxed' ),
			'elements' => array(
				array(
					'id'       => alf_elementor_uid(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 45 ),
					'elements' => array(
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'image',
							'settings'   => array(
								'image' => array(
									'url' => $about_image,
									'id'  => '',
								),
							),
							'elements'   => array(),
						),
					),
					'isInner'  => false,
				),
				array(
					'id'       => alf_elementor_uid(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 55 ),
					'elements' => array(
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title' => 'About the Founder',
								'header_size' => 'div',
								'title_color' => '#c9922f',
							),
							'elements'   => array(),
						),
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title' => 'Federal immigration insight. Personal representation.',
								'header_size' => 'h2',
								'title_color' => '#082c62',
							),
							'elements'   => array(),
						),
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p>The founder of Access Law Firm brings more than 15 years of immigration experience, including service as an Immigration Judge, Supervisory Immigration Services Officer, Asylum Officer, Immigration Officer, and Adjudications Officer handling EB-5 matters.</p><p>That experience provides a practical understanding of how immigration applications, interviews, and court cases are reviewed and decided.</p>',
							),
							'elements'   => array(),
						),
					),
					'isInner'  => false,
				),
			),
			'isInner'  => false,
		),
		// CTA
		array(
			'id'       => alf_elementor_uid(),
			'elType'   => 'section',
			'settings' => array(
				'layout' => 'boxed',
				'background_background' => 'classic',
				'background_color' => '#fff4e0',
			),
			'elements' => array(
				array(
					'id'       => alf_elementor_uid(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100 ),
					'elements' => array(
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => array(
								'title' => 'Join the Virtual Lobby.',
								'header_size' => 'h2',
								'align' => 'center',
								'title_color' => '#082c62',
							),
							'elements'   => array(),
						),
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => array(
								'editor' => '<p style="text-align:center"><strong>Monday–Friday:</strong> 9:00 AM–5:00 PM<br><strong>Saturday–Sunday:</strong> 10:00 AM–3:30 PM</p>',
							),
							'elements'   => array(),
						),
						array(
							'id'         => alf_elementor_uid(),
							'elType'     => 'widget',
							'widgetType' => 'button',
							'settings'   => array(
								'text'             => 'Enter Virtual Lobby →',
								'align'            => 'center',
								'link'             => array( 'url' => '#' ),
								'background_color' => '#c9922f',
								'button_text_color'=> '#082c62',
								'css_classes'      => 'open-lobby',
							),
							'elements'   => array(),
						),
					),
					'isInner'  => false,
				),
			),
			'isInner'  => false,
		),
	);
}

/**
 * One process-step column for Elementor.
 *
 * @param string $number Step number.
 * @param string $title  Title.
 * @param string $text   Description.
 * @return array
 */
function alf_elementor_step_column( $number, $title, $text ) {
	return array(
		'id'       => alf_elementor_uid(),
		'elType'   => 'column',
		'settings' => array( '_column_size' => 25 ),
		'elements' => array(
			array(
				'id'         => alf_elementor_uid(),
				'elType'     => 'widget',
				'widgetType' => 'heading',
				'settings'   => array(
					'title'       => $number . '. ' . $title,
					'header_size' => 'h3',
					'title_color' => '#082c62',
					'align'       => 'center',
				),
				'elements'   => array(),
			),
			array(
				'id'         => alf_elementor_uid(),
				'elType'     => 'widget',
				'widgetType' => 'text-editor',
				'settings'   => array(
					'editor' => '<p style="text-align:center">' . esc_html( $text ) . '</p>',
				),
				'elements'   => array(),
			),
		),
		'isInner'  => false,
	);
}

/**
 * Create the Elementor duplicate home page once.
 */
function alf_maybe_create_elementor_home_page() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Require Elementor.
	if ( ! did_action( 'elementor/loaded' ) && ! defined( 'ELEMENTOR_VERSION' ) ) {
		return;
	}

	$existing = alf_get_elementor_home_page_id();
	if ( $existing && get_post( $existing ) ) {
		return;
	}

	// Recover by slug if option was lost.
	$by_slug = get_page_by_path( 'home-elementor' );
	if ( $by_slug ) {
		update_option( 'alf_elementor_home_page_id', (int) $by_slug->ID, false );
		return;
	}

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

	update_option( 'alf_elementor_home_page_id', (int) $page_id, false );

	// Clear Elementor CSS cache for this page when available.
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}
add_action( 'admin_init', 'alf_maybe_create_elementor_home_page' );

/**
 * Admin notice with edit / Reading settings links.
 */
function alf_elementor_home_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		return;
	}

	$page_id = alf_get_elementor_home_page_id();
	if ( ! $page_id || ! get_post( $page_id ) ) {
		return;
	}

	if ( get_user_meta( get_current_user_id(), 'alf_dismiss_elementor_home_notice', true ) ) {
		return;
	}

	$edit_url = admin_url( 'post.php?post=' . $page_id . '&action=elementor' );
	$view_url = get_permalink( $page_id );
	$reading  = admin_url( 'options-reading.php' );
	$is_front = ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) === $page_id );
	?>
	<div class="notice notice-info is-dismissible" id="alf-elementor-home-notice">
		<p>
			<strong><?php esc_html_e( 'Home (Elementor) is ready.', 'access-law-firm' ); ?></strong>
			<?php esc_html_e( 'Theme header, footer, and Virtual Lobby stay as they are. The client can edit page content in Elementor.', 'access-law-firm' ); ?>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Edit with Elementor', 'access-law-firm' ); ?></a>
			<a class="button" href="<?php echo esc_url( $view_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View page', 'access-law-firm' ); ?></a>
			<?php if ( ! $is_front ) : ?>
				<a class="button" href="<?php echo esc_url( $reading ); ?>"><?php esc_html_e( 'Set as homepage (Settings → Reading)', 'access-law-firm' ); ?></a>
			<?php else : ?>
				<span class="dashicons dashicons-yes" style="color:#00a32a;vertical-align:middle"></span>
				<?php esc_html_e( 'This page is the current homepage.', 'access-law-firm' ); ?>
			<?php endif; ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'alf_elementor_home_admin_notice' );
