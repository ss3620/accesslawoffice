<?php
/**
 * Home section: Hero + credential flip cards.
 * Structure matches thms/correct access law firm/index.html.
 *
 * @package Access_Law_Firm
 */

$alf_lobby_open = alf_is_lobby_open();
?>
<section class="hero">
	<div class="container">
		<div class="hero-grid">
			<div class="hero-copy">
				<div class="eyebrow">Former Immigration Judge</div>
				<h1>Experience From Every Side of the <span>Immigration System.</span></h1>
				<p>Strategic, compassionate representation informed by federal service as an Immigration Judge, Asylum Officer, and USCIS Immigration Officer.</p>
				<div class="actions">
					<button class="btn btn-primary open-lobby" type="button">Join Virtual Lobby</button>
					<?php alf_render_call_text_buttons(); ?>
				</div>
				<?php if ( alf_firm_phone_e164() ) : ?>
					<p class="note"><?php esc_html_e( 'Message and data rates may apply.', 'access-law-firm' ); ?></p>
				<?php endif; ?>
			</div>

			<div class="portrait">
				<img src="<?php echo alf_img( 'stock-attorney.png' ); ?>" alt="Attorney portrait">
				<div id="alfHeroAvailability" class="availability lobby-card<?php echo $alf_lobby_open ? '' : ' lobby-card-closed availability-closed'; ?>" data-lobby-availability>
					<div data-lobby-avail-open <?php echo $alf_lobby_open ? '' : 'hidden'; ?>>
						<strong class="lobby-card-title">Virtual Lobby Open</strong>
						<div class="lobby-card-features">
							<div class="lobby-card-feature lobby-card-feature--live">
								<span class="lobby-card-icon" aria-hidden="true">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.5"/><path d="M5.5 19c1-3.5 3.2-5 6.5-5s5.5 1.5 6.5 5"/></svg>
								</span>
								<span class="lobby-card-feature-text">
									<b>Live Human Assistant</b>
									<small>Not AI</small>
								</span>
							</div>
							<div class="lobby-card-feature lobby-card-feature--free">
								<span class="lobby-card-icon" aria-hidden="true">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="12" rx="2"/><path d="M9 7V5h6v2"/><path d="M3 12h18"/></svg>
								</span>
								<span class="lobby-card-feature-text">
									<b>Free to Join</b>
									<small>No Payment Info Needed</small>
								</span>
							</div>
						</div>
						<p class="lobby-card-foot"><span class="dot" aria-hidden="true"></span> Live receptionist available</p>
						<p class="lobby-card-hours">
							<span><b>Mon–Fri:</b> 9:00 AM–5:00 PM CST</span>
							<span><b>Sat–Sun:</b> 10:00 AM–3:30 PM CST</span>
						</p>
					</div>
					<div data-lobby-avail-closed <?php echo $alf_lobby_open ? 'hidden' : ''; ?>>
						<strong class="lobby-card-title">Virtual Lobby Closed</strong>
						<p class="lobby-card-offline">We are currently offline. Please check back during lobby hours:</p>
						<p class="lobby-card-hours lobby-card-hours--closed">
							<span><b>Monday–Friday:</b> 9:00 AM–5:00 PM CST</span>
							<span><b>Saturday–Sunday:</b> 10:00 AM–3:30 PM CST</span>
						</p>
						<p class="lobby-card-foot lobby-card-foot--closed"><span class="dot" aria-hidden="true"></span> Offline</p>
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
