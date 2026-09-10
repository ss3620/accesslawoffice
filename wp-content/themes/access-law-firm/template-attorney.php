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
?>

<main id="page-content" class="alf-page attorney-page">

	<section class="attorney-hero">
		<div class="container about-grid attorney-hero-grid">
			<div class="about-photo founder-photo attorney-photo">
				<img src="<?php echo alf_img( 'stock-attorney.png' ); ?>" alt="Nadeem R. Kasam, Attorney at Law">
				<div class="founder-photo-label">
					<strong>15+ Years</strong>
					<span>Immigration Experience</span>
				</div>
			</div>
			<div class="attorney-intro">
				<div class="eyebrow">Attorney Profile</div>
				<h1 class="attorney-name">Nadeem R. Kasam</h1>
				<p class="founder-title">Founder &amp; Managing Attorney <span class="founder-title-sep" aria-hidden="true">|</span> Former Immigration Judge</p>
				<p class="attorney-lead">Nadeem R. Kasam brings more than 15 years of immigration experience, including service as an Immigration Judge, Supervisory Immigration Services Officer, Asylum Officer, Immigration Officer, and Adjudications Officer handling EB-5 matters.</p>
				<p class="attorney-body">That experience provides a practical understanding of how immigration applications, interviews, and court cases are reviewed and decided. Access Law Firm brings that perspective to clients through clear advice, careful preparation, and strategic representation.</p>
				<div class="actions">
					<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
					<?php alf_render_call_text_buttons(); ?>
				</div>
				<?php if ( $alf_attorney_phone ) : ?>
					<p class="note"><?php esc_html_e( 'Message and data rates may apply.', 'access-law-firm' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="attorney-roles-section">
		<div class="container">
			<div class="section-title">
				<div class="eyebrow">Federal Service</div>
				<h2>Experience from every side of the immigration system.</h2>
			</div>

			<div class="cards attorney-roles">
				<div class="card">
					<div class="icon practice-svg" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="M12 3v17"/>
							<path d="M6 6h12"/>
							<path d="m6 6-3 6h6L6 6Z"/>
							<path d="m18 6-3 6h6l-3-6Z"/>
							<path d="M8 21h8"/>
						</svg>
					</div>
					<h3>Immigration Judge</h3>
					<p>Decided removal cases in Immigration Court, including asylum, cancellation of removal, and bond matters.</p>
				</div>

				<div class="card">
					<div class="icon practice-svg" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="8" r="3.2"/>
							<path d="M5 19c1-3.5 3.4-5.2 7-5.2s6 1.7 7 5.2"/>
							<path d="m17.5 4.5 1.4 1.4 2.6-2.6"/>
						</svg>
					</div>
					<h3>Supervisory Immigration Services Officer</h3>
					<p>Supervised USCIS adjudications and reviewed complex benefit decisions at the agency level.</p>
				</div>

				<div class="card">
					<div class="icon practice-svg" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10Z"/>
							<path d="M8.5 12.5h7"/>
						</svg>
					</div>
					<h3>Asylum Officer</h3>
					<p>Conducted asylum interviews and evaluated persecution and credible fear claims.</p>
				</div>

				<div class="card">
					<div class="icon practice-svg" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<rect x="5" y="4" width="14" height="16" rx="2"/>
							<path d="M8 8h8"/>
							<path d="M8 12h5"/>
							<path d="m14 16 1.4 1.4L18 15"/>
						</svg>
					</div>
					<h3>Immigration Officer</h3>
					<p>Reviewed applications and petitions across a broad range of immigration benefit categories.</p>
				</div>

				<div class="card">
					<div class="icon practice-svg" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="M12 3v18"/>
							<path d="M8.5 7.5h5a2.5 2.5 0 0 1 0 5h-3a2.5 2.5 0 0 0 0 5h5"/>
						</svg>
					</div>
					<h3>Adjudications Officer — EB-5</h3>
					<p>Adjudicated EB-5 investor matters, including source-of-funds and job-creation requirements.</p>
				</div>

				<div class="card attorney-role-highlight">
					<div class="icon practice-svg" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="M12 3 4.5 6v5.7c0 4.6 3.1 7.5 7.5 9.3 4.4-1.8 7.5-4.7 7.5-9.3V6L12 3Z"/>
							<path d="m9 12 2 2 4-4"/>
						</svg>
					</div>
					<h3>Now representing clients</h3>
					<p>That government experience now works for you — anticipating how your case will be reviewed before it is filed.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="attorney-value">
		<div class="container">
			<div class="section-title">
				<div class="eyebrow">What This Means for You</div>
				<h2>Preparation shaped by how decisions are actually made.</h2>
			</div>

			<div class="attorney-value-grid">
				<div>
					<strong>Cases prepared for review</strong>
					<span>Applications and filings are organized the way adjudicators and judges read them, with the evidence they look for first.</span>
				</div>
				<div>
					<strong>Realistic case assessment</strong>
					<span>Honest guidance on strengths, risks, and the likely path of your case — before you commit time and money.</span>
				</div>
				<div>
					<strong>Interview and hearing readiness</strong>
					<span>Clients are prepared for the questions, standards, and procedures they will actually face.</span>
				</div>
				<div>
					<strong>Direct attorney attention</strong>
					<span>Your case is handled with personal attention, not passed off after the first consultation.</span>
				</div>
			</div>

			<blockquote class="founder-quote">“Every immigration case deserves preparation, strategy, and personal attention.”</blockquote>
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
		<div class="container attorney-cta-inner">
			<div class="eyebrow">Speak With Our Office</div>
			<h2>Talk with a live person today.</h2>
			<p>Join the Virtual Lobby to speak with our receptionist, or reach us directly by phone or text.</p>
			<div class="actions attorney-cta-actions">
				<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
				<?php alf_render_call_text_buttons(); ?>
			</div>
			<?php if ( $alf_attorney_phone ) : ?>
				<p class="note attorney-cta-note"><?php esc_html_e( 'Message and data rates may apply.', 'access-law-firm' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

</main>

<?php
get_footer();
