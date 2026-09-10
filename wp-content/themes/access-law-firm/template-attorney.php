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
					<strong>Federal Career</strong>
					<span>Immigration Judge · USCIS · Asylum</span>
				</div>
			</div>
		</div>
	</section>

	<section class="attorney-bio-section">
		<div class="container attorney-bio-grid">
			<div class="attorney-bio-copy">
				<h2>About Nadeem R. Kasam</h2>
				<p>Nadeem R. Kasam is a Texas-licensed attorney whose immigration experience spans multiple levels of the federal immigration system—from interviewing asylum applicants and adjudicating immigration petitions to supervising immigration officers and ultimately serving as a United States Immigration Judge.</p>
				<p>Before entering private practice, Mr. Kasam spent more than a decade working within the federal immigration system. That experience gave him a firsthand understanding of how immigration cases are evaluated, how government officers and judges analyze evidence, and how decisions are made at different stages of the immigration process.</p>
			</div>

			<aside class="attorney-doj">
				<h3>U.S. Department of Justice</h3>
				<p class="attorney-doj-label">Immigration Judge Service</p>
				<p>Mr. Kasam served as an Immigration Judge with EOIR at the LaSalle Immigration Court in Louisiana. His appointment is reflected in official U.S. Department of Justice materials.</p>
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
				<div class="eyebrow">Federal Service</div>
				<h2>Experience from every side of the immigration system.</h2>
			</div>

			<article class="attorney-role">
				<header class="attorney-role-head">
					<span class="attorney-role-org">U.S. Department of Justice · EOIR</span>
					<h3>Former Immigration Judge</h3>
					<p class="attorney-role-place">LaSalle Immigration Court, Louisiana</p>
				</header>
				<div class="attorney-role-body">
					<p>Mr. Kasam served as an Immigration Judge with the U.S. Department of Justice, Executive Office for Immigration Review, at the LaSalle Immigration Court in Louisiana.</p>
					<p>As an Immigration Judge, he presided over detained removal proceedings and conducted master calendar hearings, bond hearings, and individual merits hearings. He heard testimony, evaluated documentary evidence, ruled on motions, and applied federal immigration law to determine whether individuals were removable from the United States and whether they qualified for relief.</p>
					<p>His docket included cases involving asylum, withholding of removal, protection under the Convention Against Torture, cancellation of removal, voluntary departure, custody and bond issues, and other forms of relief from removal.</p>
					<p>Serving as an Immigration Judge provided Mr. Kasam with a perspective few immigration attorneys have: experience sitting on the other side of the bench and personally evaluating the arguments, testimony, evidence, and legal issues presented by both the government and the respondent.</p>
				</div>
			</article>

			<article class="attorney-role">
				<header class="attorney-role-head">
					<span class="attorney-role-org">U.S. Citizenship and Immigration Services</span>
					<h3>Immigration Services Officer — Service Center Operations</h3>
				</header>
				<div class="attorney-role-body">
					<p>Before becoming an Immigration Judge, Mr. Kasam served as an Immigration Services Officer with U.S. Citizenship and Immigration Services Service Center Operations.</p>
					<p>In that position, he adjudicated a variety of immigration petitions and applications, including family-based petitions, employment-based immigrant petitions, Temporary Protected Status matters, and requests for extensions of immigration status.</p>
					<p>His work required reviewing immigration histories and supporting documentation, applying the Immigration and Nationality Act and federal regulations, and preparing written decisions. He also worked on matters involving inadmissibility, removability, eligibility for immigration benefits, and the issuance of Notices to Appear initiating removal proceedings.</p>
				</div>
			</article>

			<article class="attorney-role">
				<header class="attorney-role-head">
					<span class="attorney-role-org">USCIS Houston Asylum Office</span>
					<h3>Asylum Officer</h3>
					<p class="attorney-role-place">Approximately four years of service</p>
				</header>
				<div class="attorney-role-body">
					<p>Mr. Kasam spent approximately four years as an Asylum Officer with the USCIS Houston Asylum Office.</p>
					<p>There, he conducted affirmative asylum interviews as well as credible-fear and reasonable-fear interviews. His work required detailed questioning of applicants, evaluating testimony and supporting evidence, reviewing country conditions, assessing credibility, and determining whether applicants met the legal standards for protection under U.S. immigration law.</p>
					<p>He prepared legal analyses and decisions and handled cases in which an applicant could be referred to Immigration Court through the issuance of a Notice to Appear.</p>
					<p>This experience gives Mr. Kasam an especially detailed understanding of asylum and protection claims—from the initial government interview through litigation before the Immigration Court.</p>
				</div>
			</article>

			<article class="attorney-role">
				<header class="attorney-role-head">
					<span class="attorney-role-org">USCIS Investor Program Office</span>
					<h3>Adjudication Officer — EB-5</h3>
					<p class="attorney-role-place">Washington, D.C.</p>
				</header>
				<div class="attorney-role-body">
					<p>Mr. Kasam also served as an Adjudication Officer with USCIS’s Investor Program Office in Washington, D.C., where he handled EB-5 immigrant investor matters.</p>
					<p>His work included analyzing complex immigration petitions, business structures, financial records, investment documentation, and multimillion-dollar commercial projects to determine compliance with federal immigration law and the requirements of the EB-5 program.</p>
				</div>
			</article>

			<article class="attorney-role">
				<header class="attorney-role-head">
					<span class="attorney-role-org">USCIS Potomac Service Center</span>
					<h3>Supervisory Immigration Services Officer</h3>
					<p class="attorney-role-place">Virginia</p>
				</header>
				<div class="attorney-role-body">
					<p>At the USCIS Potomac Service Center in Virginia, Mr. Kasam served as a Supervisory Immigration Services Officer.</p>
					<p>In that role, he supervised immigration officers responsible for adjudicating immigration benefits, including family petitions, employment authorization applications, and permanent-resident-card matters. He reviewed officers’ work, including Requests for Evidence, Notices of Intent to Deny, and written decisions, while also training employees and assisting with agency procedures and adjudication standards.</p>
					<p>The position provided him not only with experience deciding immigration matters, but also with experience reviewing and supervising the decisions of other immigration officers.</p>
				</div>
			</article>

			<article class="attorney-role">
				<header class="attorney-role-head">
					<span class="attorney-role-org">USCIS San Fernando Valley Field Office</span>
					<h3>Immigration Services Officer</h3>
					<p class="attorney-role-place">California</p>
				</header>
				<div class="attorney-role-body">
					<p>Earlier in his federal career, Mr. Kasam served as an Immigration Services Officer at the USCIS San Fernando Valley Field Office in California.</p>
					<p>His work included adjudicating adjustment-of-status applications, naturalization applications, family-based petitions, inadmissibility waivers, and other immigration benefits. He interviewed applicants, reviewed immigration records and supporting evidence, identified legal and factual issues, and determined whether applicants satisfied the requirements of federal immigration law.</p>
				</div>
			</article>
		</div>
	</section>

	<section class="attorney-both-sides">
		<div class="container attorney-both-sides-inner">
			<div class="eyebrow">Perspective</div>
			<h2>Experience from both sides of the immigration system</h2>
			<p>Over the course of his federal career, Mr. Kasam worked at the field-office, service-center, asylum, supervisory, and judicial levels of the immigration system.</p>
			<p>He has interviewed applicants. He has adjudicated petitions and applications. He has reviewed the decisions of other immigration officers. He has evaluated asylum and fear-based claims. And as an Immigration Judge, he has presided over removal proceedings and decided whether respondents established eligibility for relief.</p>
			<p>Today, he brings that experience to the representation of individuals and families facing the immigration system.</p>
			<p class="attorney-both-sides-close">At Access Law Firm PLLC, clients receive representation informed not simply by knowledge of immigration law, but by years of firsthand experience applying that law from within the federal immigration system.</p>
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
