<?php
/**
 * Default page template — Elementor-friendly (theme header/footer/lobby stay).
 *
 * @package Access_Law_Firm
 */

get_header();
?>

<main id="page-content" class="alf-page">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>

<?php
get_footer();
