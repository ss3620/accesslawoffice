<?php
/**
 * Main template fallback.
 *
 * @package Access_Law_Firm
 */

get_header();
?>

<main class="container" style="padding:60px 0">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<div><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p>No content found.</p>
	<?php endif; ?>
</main>

<?php
get_footer();
