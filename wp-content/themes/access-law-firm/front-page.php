<?php
/**
 * Front page template — Access Law Firm landing.
 *
 * If Settings → Reading uses an Elementor-built static page, render that
 * page (theme header/footer/lobby still apply). Otherwise use the PHP home.
 *
 * @package Access_Law_Firm
 */

if ( function_exists( 'alf_use_elementor_front' ) && alf_use_elementor_front() ) {
	$front_id = (int) get_option( 'page_on_front' );
	$front    = get_post( $front_id );
	if ( $front instanceof WP_Post ) {
		$GLOBALS['post'] = $front;
		setup_postdata( $front );
		get_header();
		?>
		<main id="home" class="alf-page alf-elementor-front">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor renders via the_content filters.
			echo apply_filters( 'the_content', $front->post_content );
			?>
		</main>
		<?php
		get_footer();
		wp_reset_postdata();
		return;
	}
}

get_header();
?>

<main id="home">
	<?php get_template_part( 'template-parts/home/hero' ); ?>
	<?php get_template_part( 'template-parts/home/process' ); ?>
	<?php get_template_part( 'template-parts/home/practice' ); ?>
	<?php get_template_part( 'template-parts/home/stats' ); ?>
	<?php get_template_part( 'template-parts/home/about' ); ?>
	<?php get_template_part( 'template-parts/home/faq' ); ?>
</main>

<?php
get_footer();
