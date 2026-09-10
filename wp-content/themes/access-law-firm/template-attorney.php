<?php
/**
 * Template Name: Attorney Profile
 *
 * Standalone attorney information page (Nadeem R. Kasam).
 * Theme header / footer / Virtual Lobby modal still apply.
 *
 * @package Access_Law_Firm
 */

get_header();

$alf_attorney_phone = alf_firm_phone_e164();
$alf_doj_pdf        = alf_doc( 'eoir-immigration-judge-announcement.pdf' );
?>

<main id="page-content" class="alf-page attorney-page">

	<section class="attorney-hero">
		<div class="container about-grid attorney-hero-grid">
			<div class="attorney-intro">
				<div class="eyebrow">Our Attorney</div>
				<h1 class="attorney-name">Nadeem R. Kasam</h1>
				<p class="attorney-subtitle">Attorney at Law<br>Former Immigration Judge</p>
				<p class="attorney-tagline">Real experience. Practical solutions. A stronger tomorrow.</p>
				<div class="actions">
					<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
					<?php alf_render_call_text_buttons(); ?>
				</div>
				<?php if ( $alf_attorney_phone ) : ?>
					<p class="note"><?php esc_html_e( 'Message and data rates may apply.', 'access-law-firm' ); ?></p>
				<?php endif; ?>
			</div>
			<div class="about-photo founder-photo attorney-photo">
				<img src="<?php echo alf_img( 'stock-attorney.png' ); ?>" alt="Nadeem R. Kasam, Attorney at Law">
				<div class="founder-photo-label">
					<strong>15+ Years</strong>
					<span>Immigration Experience</span>
				</div>
			</div>
		</div>
	</section>

	<section class="attorney-bio-section">
		<div class="container attorney-bio-grid">
			<div class="attorney-bio-copy">
				<h2>About Nadeem R. Kasam</h2>
				<p>Nadeem R. Kasam is a licensed attorney in the State of Texas and the founder of Access Law Firm. With more than 15 years of immigration experience, he helps individuals and families navigate complex immigration matters with clarity, strategy, and personal attention.</p>
				<p>Mr. Kasam previously served as an Immigration Judge with the U.S. Department of Justice, Executive Office for Immigration Review (EOIR). Prior to his judicial appointment, he held several positions within the federal immigration system, including Asylum Officer, Immigration Officer, and Supervisory Immigration Services Officer.</p>
			</div>

			<aside class="attorney-doj">
				<h3>U.S. Department of Justice</h3>
				<p class="attorney-doj-label">Immigration Judge Service</p>
				<p>Mr. Kasam’s appointment as an Immigration Judge is reflected in official U.S. Department of Justice materials.</p>
				<a class="attorney-doj-link" href="<?php echo $alf_doj_pdf; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in alf_doc(). ?>" target="_blank" rel="noopener">
					<span class="attorney-doj-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M14 3v5h5"/><path d="M9 14h6"/><path d="M9 17h4"/></svg>
					</span>
					<?php esc_html_e( 'View Official DOJ List (PDF)', 'access-law-firm' ); ?>
					<span aria-hidden="true">↗</span>
				</a>
			</aside>
		</div>
	</section>

	<section class="attorney-roles-section">
		<div class="container">
			<div class="section-title">
				<h2>Experience From Every Side of the Immigration System</h2>
			</div>

			<div class="cards attorney-agency-cards">
				<div class="card attorney-agency-card">
					<h3>Former Immigration Judge</h3>
					<p>U.S. Department of Justice<br>Executive Office for Immigration Review</p>
				</div>
				<div class="card attorney-agency-card">
					<h3>Former Asylum Officer</h3>
					<p>U.S. Citizenship and Immigration Services</p>
				</div>
				<div class="card attorney-agency-card">
					<h3>Former Immigration Officer</h3>
					<p>U.S. Department of Homeland Security</p>
				</div>
				<div class="card attorney-agency-card">
					<h3>Former Supervisory Immigration Services Officer</h3>
					<p>U.S. Citizenship and Immigration Services</p>
				</div>
			</div>
		</div>
	</section>

	<section class="attorney-practice-section">
		<div class="container">
			<div class="section-title">
				<div class="eyebrow">Practice Areas</div>
				<h2>Immigration matters handled by the firm.</h2>
			</div>

			<ul class="attorney-practice-list">
				<li><strong>Removal Defense</strong><span>Detained and non-detained Immigration Court proceedings, including bond hearings.</span></li>
				<li><strong>Asylum</strong><span>Asylum, withholding of removal, and protection under the Convention Against Torture.</span></li>
				<li><strong>Family Immigration</strong><span>Petitions and green cards for spouses, parents, children, and other relatives.</span></li>
				<li><strong>Hardship Waivers</strong><span>I-601, I-601A, and other waivers available under U.S. immigration law.</span></li>
				<li><strong>Naturalization &amp; Citizenship</strong><span>Naturalization, citizenship interviews, and complex eligibility questions.</span></li>
				<li><strong>Employment Visas</strong><span>Options for professionals, employees, entrepreneurs, and sponsoring businesses.</span></li>
			</ul>
		</div>
	</section>

	<?php
	// Anything typed into the WordPress editor for this page renders here.
	$alf_attorney_content = trim( (string) get_post_field( 'post_content', get_queried_object_id() ) );
	if ( '' !== $alf_attorney_content ) :
		?>
		<section class="attorney-editor-content">
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

	<section class="attorney-cta">
		<div class="container attorney-cta-bar">
			<div class="attorney-cta-copy">
				<h2>Let’s Talk About Your Case</h2>
				<p>Join our virtual lobby or contact us today.</p>
			</div>
			<div class="attorney-cta-side">
				<div class="actions attorney-cta-actions">
					<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
					<?php alf_render_call_text_buttons(); ?>
				</div>
				<?php if ( $alf_attorney_phone ) : ?>
					<p class="note attorney-cta-note"><?php esc_html_e( 'Message and data rates may apply.', 'access-law-firm' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
