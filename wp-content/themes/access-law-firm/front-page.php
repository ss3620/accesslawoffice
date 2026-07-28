<?php
/**
 * Front page template — Access Law Firm landing.
 *
 * @package Access_Law_Firm
 */

get_header();
?>

<main id="home">
	<section class="hero">
		<div class="container">
			<div class="hero-grid">
				<div class="hero-copy">
					<div class="eyebrow">Former Immigration Judge</div>
					<h1>Experience From Every Side of the <span>Immigration System.</span></h1>
					<p>Strategic, compassionate representation informed by federal service as an Immigration Judge, Asylum Officer, and USCIS Immigration Officer.</p>
					<div class="actions">
						<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
					</div>
				</div>
				<?php $alf_lobby_open = alf_is_lobby_open(); ?>
				<div class="portrait">
					<img src="<?php echo alf_img( 'stock-attorney.png' ); ?>" alt="Attorney portrait">
					<div id="alfHeroAvailability" class="availability<?php echo $alf_lobby_open ? '' : ' availability-closed'; ?>" data-lobby-availability>
						<div data-lobby-avail-open <?php echo $alf_lobby_open ? '' : 'hidden'; ?>>
							<strong>Virtual Lobby Open</strong>
							<span>Live assistant available</span><br>
							<small>● Live receptionist available</small>
						</div>
						<div data-lobby-avail-closed <?php echo $alf_lobby_open ? 'hidden' : ''; ?>>
							<strong>Virtual Lobby Closed</strong>
							<span>We are currently offline</span><br>
							<small>Please check back during lobby hours.</small>
						</div>
					</div>
				</div>
			</div>

			<div class="credstrip flip-strip" aria-label="Professional experience">
				<button class="flip-card" type="button" aria-pressed="false">
					<span class="flip-card-inner">
						<span class="flip-card-face flip-card-front">
							<span class="icon icon-svg" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
									<path d="M12 3v17"/>
									<path d="M6 6h12"/>
									<path d="m6 6-3 6h6L6 6Z"/>
									<path d="m18 6-3 6h6l-3-6Z"/>
									<path d="M8 21h8"/>
								</svg>
							</span>
							<span class="credential-copy">
								<b>Former Immigration Judge</b>
								<span>Immigration Court</span>
							</span>
						</span>
						<span class="flip-card-face flip-card-back">
							<b>Immigration Judge Experience</b>
							<span>Decided immigration cases</span>
							<span>Conducted bond hearings</span>
							<span>Issued oral and written decisions</span>
						</span>
					</span>
				</button>

				<button class="flip-card" type="button" aria-pressed="false">
					<span class="flip-card-inner">
						<span class="flip-card-face flip-card-front">
							<span class="icon icon-svg" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="12" r="9"/>
									<path d="M3 12h18"/>
									<path d="M12 3c2.6 2.4 4 5.4 4 9s-1.4 6.6-4 9"/>
									<path d="M12 3c-2.6 2.4-4 5.4-4 9s1.4 6.6 4 9"/>
								</svg>
							</span>
							<span class="credential-copy">
								<b>Former Asylum Officer</b>
								<span>U.S. Department of Homeland Security</span>
							</span>
						</span>
						<span class="flip-card-face flip-card-back">
							<b>Asylum Officer Experience</b>
							<span>Interviewed asylum applicants</span>
							<span>Evaluated credibility and evidence</span>
							<span>Assessed eligibility for protection</span>
						</span>
					</span>
				</button>

				<button class="flip-card" type="button" aria-pressed="false">
					<span class="flip-card-inner">
						<span class="flip-card-face flip-card-front">
							<span class="icon icon-svg" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
									<rect x="5" y="3" width="14" height="18" rx="2"/>
									<path d="M9 3v18"/>
									<circle cx="14" cy="11" r="3"/>
									<path d="M11 11h6"/>
									<path d="M14 8c.8.9 1.2 1.9 1.2 3s-.4 2.1-1.2 3"/>
									<path d="M14 8c-.8.9-1.2 1.9-1.2 3s.4 2.1 1.2 3"/>
								</svg>
							</span>
							<span class="credential-copy">
								<b>Former Immigration Officer</b>
								<span>U.S. Citizenship and Immigration Services</span>
							</span>
						</span>
						<span class="flip-card-face flip-card-back">
							<b>Immigration Officer Experience</b>
							<span>Reviewed immigration applications and petitions</span>
							<span>Conducted applicant interviews</span>
							<span>Determined eligibility under immigration law</span>
						</span>
					</span>
				</button>

				<button class="flip-card" type="button" aria-pressed="false">
					<span class="flip-card-inner">
						<span class="flip-card-face flip-card-front">
							<span class="icon icon-svg" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="7" r="3"/>
									<path d="M6.5 20v-2.5A4.5 4.5 0 0 1 11 13h2a4.5 4.5 0 0 1 4.5 4.5V20"/>
									<path d="M4 11h3"/>
									<path d="M17 11h3"/>
								</svg>
							</span>
							<span class="credential-copy">
								<b>Former Supervisory Immigration Services Officer</b>
								<span>U.S. Citizenship and Immigration Services</span>
							</span>
						</span>
						<span class="flip-card-face flip-card-back">
							<b>Supervisory USCIS Experience</b>
							<span>Supervised immigration officers</span>
							<span>Reviewed complex immigration matters</span>
							<span>Oversaw USCIS adjudications</span>
						</span>
					</span>
				</button>
			</div>
		</div>
	</section>

	<section>
		<div class="container">
			<div class="section-title">
				<div class="eyebrow">Start Here</div>
				<h2>A simpler way to reach your lawyer.</h2>
				<p>Enter the Virtual Lobby, verify your phone number, speak with a live assistant, and connect with the attorney.</p>
			</div>
			<div class="steps">
				<div class="step">
					<div class="number">1</div>
					<h3>Join Lobby</h3>
					<p>Check in through the secure Virtual Lobby.</p>
				</div>
				<div class="step">
					<div class="number">2</div>
					<h3>Verify Phone</h3>
					<p>Confirm your U.S. phone number for security.</p>
				</div>
				<div class="step">
					<div class="number">3</div>
					<h3>Live Assistant</h3>
					<p>A team member greets you and gathers details.</p>
				</div>
				<div class="step">
					<div class="number">4</div>
					<h3>Talk to Attorney</h3>
					<p>Speak with the attorney or arrange the next step.</p>
				</div>
			</div>
		</div>
	</section>

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

	<section>
		<div class="container">
			<div class="stats">
				<div class="stat"><b>15+</b><span>Years of Immigration Experience</span></div>
				<div class="stat"><b>Thousands</b><span>of Cases Reviewed</span></div>
				<div class="stat"><b>Federal Service</b><span>Across USCIS, the Asylum Office, and Immigration Court</span></div>
			</div>
		</div>
	</section>

	<section id="about">
		<div class="container about-grid founder-section">
			<div class="about-photo founder-photo">
				<img src="<?php echo alf_img( 'stock-office.png' ); ?>" alt="Access Law Firm office">
				<div class="founder-photo-label">
					<strong>15+ Years</strong>
					<span>Immigration Experience</span>
				</div>
			</div>
			<div class="founder-copy">
				<div class="eyebrow">About the Founder</div>
				<h2>Federal immigration insight. Personal representation.</h2>
				<p class="founder-intro">The founder of Access Law Firm brings more than 15 years of immigration experience, including service as an Immigration Judge, Supervisory Immigration Services Officer, Asylum Officer, Immigration Officer, and Adjudications Officer handling EB-5 matters.</p>
				<p>That experience provides a practical understanding of how immigration applications, interviews, and court cases are reviewed and decided. Access Law Firm brings that perspective to clients through clear advice, careful preparation, and strategic representation.</p>
				<div class="founder-highlights">
					<div><strong>Immigration Court</strong><span>Former Immigration Judge</span></div>
					<div><strong>USCIS Leadership</strong><span>Former Supervisory Immigration Services Officer</span></div>
					<div><strong>Protection Claims</strong><span>Former Asylum Officer</span></div>
					<div><strong>Agency Adjudications</strong><span>Immigration and EB-5 experience</span></div>
				</div>
				<blockquote class="founder-quote">“Every immigration case deserves preparation, strategy, and personal attention.”</blockquote>
			</div>
		</div>
	</section>

	<section class="practice" id="faq">
		<div class="container faq-grid">
			<div>
				<div class="eyebrow">Questions</div>
				<h2>Frequently asked questions.</h2>
				<div style="margin-top:24px">
					<details>
						<summary>How does the Virtual Lobby work?</summary>
						<p>Click “Join Virtual Lobby,” verify your phone number, provide basic case information, and wait for a live assistant.</p>
					</details>
					<details>
						<summary>Do I need an appointment?</summary>
						<p>No traditional appointment scheduler is required for initial contact. Clients begin through the Virtual Lobby.</p>
					</details>
					<details>
						<summary>Can I upload documents?</summary>
						<p>Yes. Existing clients can be directed to a secure document upload system after identity verification.</p>
					</details>
				</div>
			</div>
			<aside class="cta">
				<div class="eyebrow">Ready to Get Started?</div>
				<h3>Join the Virtual Lobby.</h3>
				<p class="hours"><b>Monday–Friday:</b> 9:00 AM–5:00 PM<br><b>Saturday–Sunday:</b> 10:00 AM–3:30 PM</p>
				<button class="btn btn-gold open-lobby" type="button">Enter Virtual Lobby →</button>
			</aside>
		</div>
	</section>
</main>

<?php
get_footer();
