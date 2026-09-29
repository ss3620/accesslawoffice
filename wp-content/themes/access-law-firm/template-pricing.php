<?php
/**
 * Template Name: Pricing
 *
 * Attorney fee schedule. Layout follows the theme; amounts come from the published fee list.
 *
 * @package Access_Law_Firm
 */

get_header();

$alf_phone = alf_firm_phone_e164();

/**
 * Print one attorney-fee table.
 *
 * @param array<int, array{0: string, 1: string}> $rows Service label and fee.
 */
$alf_fee_table = static function ( array $rows ) {
	echo '<table class="fee-table">';
	echo '<thead><tr><th>' . esc_html__( 'Service', 'access-law-firm' ) . '</th><th>' . esc_html__( 'Attorney Fee', 'access-law-firm' ) . '</th></tr></thead>';
	echo '<tbody>';
	foreach ( $rows as $row ) {
		echo '<tr><td>' . esc_html( $row[0] ) . '</td><td>' . esc_html( $row[1] ) . '</td></tr>';
	}
	echo '</tbody></table>';
};
?>

<main id="page-content" class="alf-page pricing-page">

	<section class="pricing-hero">
		<div class="container pricing-hero-inner">
			<div class="eyebrow"><?php esc_html_e( 'Transparent · Fair · Experienced', 'access-law-firm' ); ?></div>
			<h1><?php esc_html_e( 'Immigration Attorney Fees', 'access-law-firm' ); ?></h1>
			<p class="pricing-lead"><?php esc_html_e( 'Clear pricing. No guesswork. At Access Law Firm PLLC, we believe clients should have a clear understanding of attorney fees before deciding to move forward.', 'access-law-firm' ); ?></p>
			<div class="actions">
				<button class="btn btn-primary open-lobby" type="button"><?php esc_html_e( 'Join Virtual Lobby', 'access-law-firm' ); ?></button>
				<?php alf_render_call_text_buttons(); ?>
			</div>
			<?php if ( $alf_phone ) : ?>
				<p class="note"><?php esc_html_e( 'Message and data rates may apply.', 'access-law-firm' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="pricing-grid-section">
		<div class="container">
			<div class="fee-board">
				<div class="fee-stack">
					<article class="fee-card">
						<header class="fee-card-head">
							<h2><?php esc_html_e( 'Family & Individual Immigration', 'access-law-firm' ); ?></h2>
							<p><?php esc_html_e( 'Prepare & File', 'access-law-firm' ); ?></p>
						</header>
						<?php
						$alf_fee_table(
							array(
								array( 'I-130 – Petition for Alien Relative', '$1,000' ),
								array( 'I-485 – Adjustment of Status', '$1,000' ),
								array( 'I-751 – Removal of Conditions', '$1,000' ),
								array( 'I-129F – Fiancé(e) Petition', '$1,000' ),
								array( 'Consular Processing / Immigrant Visa', '$1,000' ),
								array( 'I-90 – Green Card Renewal/Replacement', '$350' ),
								array( 'I-765 – Employment Authorization (EAD)', '$350' ),
								array( 'I-131 – Travel Document / Advance Parole', '$350' ),
								array( 'N-400 – Naturalization/Citizenship', '$500' ),
								array( 'I-589 – Affirmative Asylum (USCIS)', '$2,500' ),
							)
						);
						?>
					</article>

					<article class="fee-card">
						<header class="fee-card-head">
							<h2><?php esc_html_e( 'Immigration Court & Appeals', 'access-law-firm' ); ?></h2>
							<p><?php esc_html_e( 'Representation', 'access-law-firm' ); ?></p>
						</header>
						<?php
						$alf_fee_table(
							array(
								array( 'Removal Proceedings', '$2,500' ),
								array( 'Defensive Asylum', '$2,500' ),
								array( 'Cancellation of Removal', '$2,500' ),
								array( 'Motion to Reopen / Reconsider', '$2,500' ),
								array( 'BIA Appeal', '$2,500' ),
							)
						);
						?>
					</article>
				</div>

				<div class="fee-stack">
					<article class="fee-card">
						<header class="fee-card-head">
							<h2><?php esc_html_e( 'Immigration Waivers', 'access-law-firm' ); ?></h2>
							<p><?php esc_html_e( 'Prepare & File', 'access-law-firm' ); ?></p>
						</header>
						<?php
						$alf_fee_table(
							array(
								array( 'I-601 – Waiver of Inadmissibility', '$2,000' ),
								array( 'I-601A – Provisional Unlawful Presence Waiver', '$2,000' ),
								array( 'I-212 – Permission to Reapply for Admission', '$2,000' ),
								array( 'I-192 – Advance Permission to Enter', '$2,000' ),
							)
						);
						?>
					</article>

					<article class="fee-card">
						<header class="fee-card-head">
							<h2><?php esc_html_e( 'Employment-Based Immigration', 'access-law-firm' ); ?></h2>
							<p><?php esc_html_e( 'Prepare & File', 'access-law-firm' ); ?></p>
						</header>
						<?php
						$alf_fee_table(
							array(
								array( 'H-1B – Specialty Occupation', '$5,000' ),
								array( 'L-1 – Intracompany Transferee', '$5,000' ),
								array( 'E-2 – Treaty Investor', '$5,000' ),
								array( 'EB-1 Petition', '$3,500' ),
								array( 'EB-2 Petition', '$3,500' ),
								array( 'EB-2 NIW – National Interest Waiver', '$3,500' ),
								array( 'EB-3 Petition', '$3,500' ),
								array( 'O-1 – Extraordinary Ability', '$3,500' ),
								array( 'EB-5 – Immigrant Investor', '$15,000' ),
							)
						);
						?>
					</article>
				</div>
			</div>
		</div>
	</section>

	<section class="fee-appearance-section">
		<div class="container fee-appearance">
			<div class="fee-appearance-copy">
				<div class="eyebrow"><?php esc_html_e( 'Appearance only', 'access-law-firm' ); ?></div>
				<h2><?php esc_html_e( 'Need an attorney for an interview or hearing?', 'access-law-firm' ); ?></h2>
				<p><?php esc_html_e( 'Attorneys, paralegals, document preparers, notaries, and other immigration professionals: if you prepare and file immigration cases but need a licensed attorney to attend an immigration court hearing or USCIS interview, contact Access Law Firm PLLC.', 'access-law-firm' ); ?></p>
				<p class="fee-appearance-tag"><?php esc_html_e( 'You prepare the case. We handle the appearance.', 'access-law-firm' ); ?></p>
			</div>
			<div class="fee-appearance-side">
				<article class="fee-card">
					<?php
					$alf_fee_table(
						array(
							array( 'USCIS Interview – Any Type', '$500' ),
							array( 'EOIR Master Calendar Hearing', '$500' ),
							array( 'EOIR Individual/Merits Hearing', '$1,000' ),
						)
					);
					?>
				</article>
				<p class="fee-cocounsel"><?php esc_html_e( 'For attorneys: co-counsel or of-counsel arrangements may be available.', 'access-law-firm' ); ?></p>
			</div>
		</div>
	</section>

	<section class="fee-notes-section">
		<div class="container fee-notes">
			<article class="fee-notes-card">
				<h2><?php esc_html_e( 'Important fee information', 'access-law-firm' ); ?></h2>
				<ul>
					<li><?php esc_html_e( 'The prices above are attorney fees for standard cases.', 'access-law-firm' ); ?></li>
					<li><?php esc_html_e( 'Cases involving criminal history, arrests, fraud, misrepresentation, prior immigration violations, prior removal orders, or other complicating factors may require additional legal work and are subject to higher attorney fees. The final fee will be determined after attorney review.', 'access-law-firm' ); ?></li>
					<li><?php esc_html_e( 'Government filing fees, USCIS or EOIR fees, premium processing fees, consular fees, medical examinations, translations, expert fees, and other third-party costs are not included.', 'access-law-firm' ); ?></li>
					<li><?php esc_html_e( 'Appearance-only representation is subject to attorney review and acceptance of the case.', 'access-law-firm' ); ?></li>
				</ul>
			</article>
			<aside class="fee-cta">
				<h2><?php esc_html_e( 'Talk with our team', 'access-law-firm' ); ?></h2>
				<ul>
					<li><?php esc_html_e( 'Get a clear assessment of your case', 'access-law-firm' ); ?></li>
					<li><?php esc_html_e( 'Understand your options', 'access-law-firm' ); ?></li>
					<li><?php esc_html_e( 'Work with an experienced immigration attorney', 'access-law-firm' ); ?></li>
				</ul>
				<div class="actions">
					<button class="btn btn-primary open-lobby" type="button"><?php esc_html_e( 'Join Virtual Lobby', 'access-law-firm' ); ?></button>
					<?php alf_render_call_text_buttons(); ?>
				</div>
			</aside>
		</div>
	</section>

</main>

<?php
get_footer();
