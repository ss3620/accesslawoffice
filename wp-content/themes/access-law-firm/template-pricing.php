<?php
/**
 * Template Name: Pricing
 *
 * Placeholder pricing layout — replace package details when ready.
 *
 * @package Access_Law_Firm
 */

get_header();

$alf_phone = alf_firm_phone_e164();
?>

<main id="page-content" class="alf-page pricing-page">

	<section class="pricing-hero">
		<div class="container pricing-hero-inner">
			<div class="eyebrow">Pricing</div>
			<h1>Clear options for your immigration matter.</h1>
			<p class="pricing-lead">Package details coming soon. Join the Virtual Lobby to speak with our team about fees for your case.</p>
			<div class="actions">
				<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
				<?php alf_render_call_text_buttons(); ?>
			</div>
			<?php if ( $alf_phone ) : ?>
				<p class="note"><?php esc_html_e( 'Message and data rates may apply.', 'access-law-firm' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="pricing-grid-section">
		<div class="container">
			<div class="section-title">
				<div class="eyebrow">Packages</div>
				<h2>Example fee structure (placeholder).</h2>
				<p>These cards are placeholders. Final pricing will be published here once confirmed.</p>
			</div>

			<div class="pricing-cards">
				<article class="pricing-card">
					<div class="eyebrow">Consult</div>
					<h3>Initial Consultation</h3>
					<p class="pricing-amount">TBD</p>
					<p class="pricing-desc">Case review and next-step guidance with our office.</p>
					<ul class="pricing-features">
						<li>Virtual Lobby intake</li>
						<li>Case overview</li>
						<li>Recommended path</li>
					</ul>
					<button class="btn btn-secondary open-lobby" type="button">Ask about this</button>
				</article>

				<article class="pricing-card pricing-card-featured">
					<div class="eyebrow">Representation</div>
					<h3>Flat-Fee Matter</h3>
					<p class="pricing-amount">TBD</p>
					<p class="pricing-desc">Scoped representation for a defined immigration filing or hearing.</p>
					<ul class="pricing-features">
						<li>Document preparation</li>
						<li>Filing strategy</li>
						<li>Attorney attention</li>
					</ul>
					<button class="btn btn-primary open-lobby" type="button">Ask about this</button>
				</article>

				<article class="pricing-card">
					<div class="eyebrow">Court</div>
					<h3>Hearing / Defense</h3>
					<p class="pricing-amount">TBD</p>
					<p class="pricing-desc">Representation for Immigration Court appearances and related relief.</p>
					<ul class="pricing-features">
						<li>Hearing preparation</li>
						<li>Court appearance</li>
						<li>Post-hearing guidance</li>
					</ul>
					<button class="btn btn-secondary open-lobby" type="button">Ask about this</button>
				</article>
			</div>
		</div>
	</section>

	<section class="pricing-note-section">
		<div class="container pricing-note-inner">
			<h2>Every case is different.</h2>
			<p>Fees depend on the type of matter, complexity, and whether court appearances are required. Speak with our receptionist for current rates and what is included.</p>
			<div class="actions">
				<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
			</div>
		</div>
	</section>

	<?php
	$alf_pricing_content = trim( (string) get_post_field( 'post_content', get_queried_object_id() ) );
	if ( '' !== $alf_pricing_content ) :
		?>
		<section class="pricing-editor-content">
			<div class="container">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</section>
		<?php
	endif;
	?>

</main>

<?php
get_footer();
