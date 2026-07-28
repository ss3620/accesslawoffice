<?php
/**
 * Home section: Practice areas + detail modal.
 *
 * @package Access_Law_Firm
 */
?>
<section class="practice" id="practice">
	<div class="container">
		<div class="section-title">
			<div class="eyebrow">Practice Areas</div>
			<h2>Immigration representation built around your case.</h2>
		</div>

		<div class="cards practice-cards">
			<button class="card practice-card" type="button"
				data-title="Removal Defense"
				data-details="Defense in detained and non-detained Immigration Court proceedings, including bond hearings, asylum, cancellation of removal, adjustment of status, voluntary departure, and other available relief.">
				<div class="icon practice-svg" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
						<path d="M12 3 4.5 6v5.7c0 4.6 3.1 7.5 7.5 9.3 4.4-1.8 7.5-4.7 7.5-9.3V6L12 3Z"/>
						<path d="m9 12 2 2 4-4"/>
					</svg>
				</div>
				<h3>Removal Defense</h3>
				<p>Defense in Immigration Court, including detained and non-detained proceedings.</p>
				<span class="practice-link">Learn More <span aria-hidden="true">→</span></span>
			</button>

			<button class="card practice-card" type="button"
				data-title="Asylum"
				data-details="Representation for asylum, withholding of removal, and protection under the Convention Against Torture, including preparation for interviews and Immigration Court hearings.">
				<div class="icon practice-svg" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="12" cy="12" r="9"/>
						<path d="M3 12h18"/>
						<path d="M12 3c2.5 2.4 4 5.4 4 9s-1.5 6.6-4 9"/>
						<path d="M12 3c-2.5 2.4-4 5.4-4 9s1.5 6.6 4 9"/>
					</svg>
				</div>
				<h3>Asylum</h3>
				<p>Protection for people facing persecution or serious harm in their home country.</p>
				<span class="practice-link">Learn More <span aria-hidden="true">→</span></span>
			</button>

			<button class="card practice-card" type="button"
				data-title="Family Immigration"
				data-details="Family-based petitions and green card matters for spouses, parents, children, fiancé(e)s, and other qualifying relatives, including consular processing and adjustment of status.">
				<div class="icon practice-svg" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="9" cy="8" r="3"/>
						<circle cx="17" cy="9" r="2.5"/>
						<path d="M3.5 19c.5-3.4 2.5-5 5.5-5s5 1.6 5.5 5"/>
						<path d="M14 15c.8-.7 1.8-1 3-1 2.3 0 3.8 1.4 4 4"/>
					</svg>
				</div>
				<h3>Family Immigration</h3>
				<p>Petitions and green cards for spouses, parents, children, and other relatives.</p>
				<span class="practice-link">Learn More <span aria-hidden="true">→</span></span>
			</button>

			<button class="card practice-card" type="button"
				data-title="Hardship Waivers"
				data-details="Representation for I-601, I-601A, and other hardship waivers available under U.S. immigration law.">
				<div class="icon practice-svg" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
						<path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10Z"/>
						<path d="M8.5 12.5h7"/>
					</svg>
				</div>
				<h3>Hardship Waivers</h3>
				<p>Hardship waivers and other waiver options for eligible immigration cases.</p>
				<span class="practice-link">Learn More <span aria-hidden="true">→</span></span>
			</button>

			<button class="card practice-card" type="button"
				data-title="Naturalization &amp; Citizenship"
				data-details="Assistance with naturalization applications, citizenship interviews and testing, derivative or acquired citizenship questions, and complex eligibility concerns.">
				<div class="icon practice-svg" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
						<rect x="5" y="4" width="14" height="16" rx="2"/>
						<path d="M8 8h8"/>
						<path d="M8 12h5"/>
						<path d="m14 16 1.4 1.4L18 15"/>
					</svg>
				</div>
				<h3>Naturalization &amp; Citizenship</h3>
				<p>Naturalization, citizenship interviews, and complex eligibility questions.</p>
				<span class="practice-link">Learn More <span aria-hidden="true">→</span></span>
			</button>

			<button class="card practice-card" type="button"
				data-title="Employment Visas"
				data-details="Selected employment-based immigration matters for professionals, employees, entrepreneurs, and sponsoring businesses, including temporary and permanent immigration options.">
				<div class="icon practice-svg" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
						<rect x="3" y="7" width="18" height="12" rx="2"/>
						<path d="M9 7V5h6v2"/>
						<path d="M3 12h18"/>
						<path d="M10 12v2h4v-2"/>
					</svg>
				</div>
				<h3>Employment Visas</h3>
				<p>Immigration options for professionals, employees, entrepreneurs, and businesses.</p>
				<span class="practice-link">Learn More <span aria-hidden="true">→</span></span>
			</button>
		</div>
	</div>
</section>

<div class="practice-modal" id="practiceModal" aria-hidden="true">
	<div class="practice-modal-backdrop" data-close-practice-modal></div>
	<div class="practice-modal-panel" role="dialog" aria-modal="true" aria-labelledby="practiceModalTitle">
		<button class="practice-modal-close" type="button" aria-label="Close" data-close-practice-modal>&times;</button>
		<div class="eyebrow">Practice Area</div>
		<h2 id="practiceModalTitle"></h2>
		<p id="practiceModalText"></p>
		<button class="btn btn-primary open-lobby" type="button" data-close-practice-modal>Join Virtual Lobby</button>
	</div>
</div>
