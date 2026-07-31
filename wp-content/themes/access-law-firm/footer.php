<?php
/**
 * Theme footer and Virtual Lobby multi-step modal.
 *
 * @package Access_Law_Firm
 */
?>

<footer class="site-footer">
	<div class="container footer-grid">
		<div>
			<div class="brand">
				<div class="logo">A</div>
				<span>ACCESS<small style="color:#c6d5e8">LAW FIRM</small></span>
			</div>
			<p style="margin-top:15px">Former Immigration Judge. Experience, insight, and modern access.</p>
		</div>
		<div>
			<b>Contact</b>
			<p style="margin-top:10px">00000000<br>info@accesslawoffice.com<br>Houston, Texas</p>
		</div>
		<div>
			<b>Quick Links</b>
			<p style="margin-top:10px">
				<a href="#practice">Practice Areas</a><br>
				<a href="#about">About</a><br>
				<a href="#faq">FAQ</a>
			</p>
		</div>
	</div>
</footer>

<!-- Welcome intro gate (auto-closes; close or enter) -->
<div class="intro-modal" id="introModal" role="dialog" aria-modal="true" aria-labelledby="introModalTitle" aria-hidden="true" hidden>
	<div class="intro-modal-card">
		<button class="intro-modal-close" type="button" aria-label="Close welcome message" data-intro-dismiss>&times;</button>
		<h2 class="intro-modal-title" id="introModalTitle">
			<span>Experience</span>
			<span class="intro-modal-x" aria-hidden="true">&times;</span>
			<span>Affordability</span>
			<span class="intro-modal-eq" aria-hidden="true">=</span>
			<span class="intro-modal-accent">Access</span>
		</h2>
		<div class="intro-modal-divider" aria-hidden="true">
			<span class="intro-modal-line"></span>
			<svg class="intro-modal-scales" viewBox="0 0 48 48" width="36" height="36" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M24 8v28" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
				<path d="M16 36h16" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
				<path d="M10 16h28" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
				<path d="M14 16l-6 12h12l-6-12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
				<path d="M34 16l-6 12h12l-6-12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
				<circle cx="24" cy="8" r="2.5" fill="currentColor"/>
			</svg>
			<span class="intro-modal-line"></span>
		</div>
		<p class="intro-modal-copy">
			At Access Law Office, we believe everyone deserves experienced, high-quality immigration representation. Our mission is to make exceptional legal services accessible to individuals and families through practical, client-focused solutions.
		</p>
		<button class="intro-modal-enter" type="button" data-intro-dismiss>
			Enter Access Law Office <span aria-hidden="true">&gt;</span>
		</button>
		<div class="intro-modal-timer" aria-live="polite">
			<span class="intro-modal-timer-text">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
				This message will close automatically in <strong data-intro-seconds>5</strong> seconds.
			</span>
			<span class="intro-modal-badge" data-intro-seconds aria-hidden="true">5</span>
		</div>
	</div>
</div>

<!-- Virtual Lobby multi-step popup -->
<div class="lobby-modal" id="lobbyModal" role="dialog" aria-modal="true" aria-labelledby="lobbyModalTitle" aria-hidden="true">
	<div class="lobby-modal-card">
		<div class="lobby-modal-topbar">
			<button class="lobby-modal-close" type="button" aria-label="Close Virtual Lobby" data-lobby-close>&times;</button>
		</div>
		<div class="lobby-modal-scroll">

		<!-- Step 0: Welcome -->
		<?php $alf_lobby_open = alf_is_lobby_open(); ?>
		<div class="lobby-step active" data-step="0">
			<div class="lobby-welcome">
				<div class="lobby-brand-mark" aria-hidden="true">
					<img src="<?php echo alf_img( 'brand-mark.png' ); ?>" alt="" width="64" height="64" decoding="async">
				</div>
				<div class="eyebrow" style="color:#f0bd5d">Virtual Lobby Experience</div>

				<div data-lobby-welcome-open <?php echo $alf_lobby_open ? '' : 'hidden'; ?>>
					<h2 id="lobbyModalTitle">Welcome to our Virtual Reception</h2>
					<p>A live receptionist will greet you before you speak with your attorney.</p>
					<div class="lobby-status-box">
						<strong id="lobbyWelcomeWait">You’re next</strong>
						<div class="lobby-status-row">
							<span>Lobby Status</span>
							<span class="lobby-status-open"><span class="dot" style="box-shadow:none"></span> OPEN</span>
						</div>
					</div>
					<button class="btn btn-gold" type="button" data-lobby-next style="width:100%">Enter Lobby</button>
				</div>

				<div data-lobby-welcome-closed <?php echo $alf_lobby_open ? 'hidden' : ''; ?>>
					<h2>The Virtual Lobby Is Closed</h2>
					<p>Our live reception is currently offline. Please check back during lobby hours.</p>
					<div class="lobby-status-box">
						<strong>Lobby Hours</strong>
						<div class="lobby-status-row">
							<span>Mon–Fri</span><span>9:00 AM – 5:00 PM CST</span>
						</div>
						<div class="lobby-status-row">
							<span>Sat–Sun</span><span>10:00 AM – 3:30 PM CST</span>
						</div>
					</div>
				</div>
			</div>

			<div class="lobby-info-panels">
				<div class="lobby-info-card lobby-info-hours">
					<strong>Lobby Hours</strong>
					<p><span>Monday–Friday</span><span>9:00 AM – 5:00 PM CST</span></p>
					<p><span>Saturday–Sunday</span><span>10:00 AM – 3:30 PM CST</span></p>
					<small>Our virtual lobby stays open until 3:30&nbsp;PM CST on weekends.</small>
				</div>
				<div class="lobby-info-card lobby-info-virtual">
					<strong>100% Virtual</strong>
					<p>All meetings are conducted online. No in-person appointments.</p>
				</div>
			</div>

			<details class="lobby-next-details">
				<summary>What happens next (inside Zoom)</summary>
				<ol class="lobby-next-list">
					<li>
						<strong>Join Reception</strong>
						<span>You’ll join a secure Zoom meeting with our receptionist. On a phone or tablet, install Zoom first if needed.</span>
					</li>
					<li>
						<strong>Receptionist Greets You</strong>
						<span>They will verify some information and answer your initial questions.</span>
					</li>
					<li>
						<strong>Join Attorney</strong>
						<span>After intake, you’ll be transferred and join a separate Zoom meeting with the attorney (Waiting Room may apply).</span>
					</li>
					<li>
						<strong>Consultation</strong>
						<span>Speak with the attorney in the attorney meeting.</span>
					</li>
					<li>
						<strong>Meeting Ends</strong>
						<span>When your meeting is complete, the call will end.</span>
					</li>
				</ol>
			</details>
		</div>

		<!-- Step 1: Name (1 of 4) -->
		<div class="lobby-step" data-step="1">
			<div class="lobby-progress" data-progress="1">
				<div class="lobby-progress-label">Step 1 of 4</div>
				<div class="lobby-progress-bar" aria-hidden="true">
					<span class="filled"></span><span></span><span></span><span></span>
				</div>
			</div>
			<div class="lobby-step-icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="8" r="3.5"/>
					<path d="M5.5 19c1-3.5 3.2-5 6.5-5s5.5 1.5 6.5 5"/>
				</svg>
			</div>
			<h3 class="lobby-step-title">What is your full name?</h3>
			<p class="lobby-step-desc">Please enter the name you would like us to use.</p>
			<div class="field">
				<label for="lobbyFullName">Full name</label>
				<input type="text" id="lobbyFullName" name="full_name" placeholder="Enter your full name" autocomplete="name">
				<div class="lobby-error" data-error-for="name">Please enter your full name.</div>
			</div>
			<div class="lobby-actions">
				<button class="btn btn-primary" type="button" data-lobby-next>Continue →</button>
				<button class="lobby-back" type="button" data-lobby-back>← Back</button>
			</div>
		</div>

		<!-- Step 2: Phone (2 of 4) -->
		<div class="lobby-step" data-step="2">
			<div class="lobby-progress" data-progress="2">
				<div class="lobby-progress-label">Step 2 of 4</div>
				<div class="lobby-progress-bar" aria-hidden="true">
					<span class="filled"></span><span class="filled"></span><span></span><span></span>
				</div>
			</div>
			<div class="lobby-step-icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
					<rect x="6" y="2.5" width="12" height="19" rx="2.5"/>
					<path d="M10 18h4"/>
				</svg>
			</div>
			<h3 class="lobby-step-title">What is your mobile phone number?</h3>
			<p class="lobby-step-desc" id="lobbyPhoneDesc">We use this number to reach you about your visit.</p>
			<div class="field">
				<label for="lobbyPhone">Mobile phone number</label>
				<div class="phone-row">
					<select id="lobbyCountry" aria-label="Country code">
						<option value="+1" data-placeholder="(713) 555-0123">🇺🇸 +1</option>
						<option value="+91" data-placeholder="98765 43210">🇮🇳 +91</option>
					</select>
					<input type="tel" id="lobbyPhone" name="phone" placeholder="(713) 555-0123" autocomplete="tel">
				</div>
				<div class="lobby-error" data-error-for="phone">Please enter a valid 10-digit mobile number.</div>
				<div class="phone-info" id="lobbyPhoneInfo">Your number is kept private and used only for this visit.</div>
			</div>
			<div class="lobby-actions">
				<button class="btn btn-primary" type="button" data-lobby-next id="lobbyPhoneNext">Continue →</button>
				<button class="lobby-back" type="button" data-lobby-back>← Back</button>
			</div>
		</div>

		<!-- Step 3: CAPTCHA and/or SMS OTP -->
		<div class="lobby-step" data-step="3">
			<div class="lobby-progress" data-progress="2">
				<div class="lobby-progress-label">Step 2 of 4</div>
				<div class="lobby-progress-bar" aria-hidden="true">
					<span class="filled"></span><span class="filled"></span><span></span><span></span>
				</div>
			</div>
			<div class="lobby-step-icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
					<path d="M12 3 4.5 6v5.5c0 4.4 3 7.2 7.5 9 4.5-1.8 7.5-4.6 7.5-9V6L12 3Z"/>
					<path d="m9 12 2 2 4-4"/>
				</svg>
			</div>

			<div id="lobbyVerifyCaptcha" class="lobby-verify-panel" hidden>
				<h3 class="lobby-step-title">Quick security check</h3>
				<p class="lobby-step-desc">Please confirm you are human to continue to the Virtual Lobby.</p>
				<div class="lobby-captcha-wrap">
					<div id="lobbyRecaptcha"></div>
				</div>
				<div class="lobby-error" data-error-for="captcha">Please complete the CAPTCHA.</div>
			</div>

			<div id="lobbyVerifySms" class="lobby-verify-panel" hidden>
				<h3 class="lobby-step-title">Enter the 6-digit code</h3>
				<p class="lobby-step-desc">We sent a code to <strong id="lobbyPhoneDisplay">your phone</strong>.</p>
				<div class="otp-row" id="lobbyOtpRow">
					<input type="text" inputmode="numeric" maxlength="1" aria-label="Digit 1" data-otp>
					<input type="text" inputmode="numeric" maxlength="1" aria-label="Digit 2" data-otp>
					<input type="text" inputmode="numeric" maxlength="1" aria-label="Digit 3" data-otp>
					<input type="text" inputmode="numeric" maxlength="1" aria-label="Digit 4" data-otp>
					<input type="text" inputmode="numeric" maxlength="1" aria-label="Digit 5" data-otp>
					<input type="text" inputmode="numeric" maxlength="1" aria-label="Digit 6" data-otp>
				</div>
				<div class="lobby-error" data-error-for="otp">Please enter the complete 6-digit code.</div>
				<button class="resend-link" type="button" data-lobby-resend>Resend Code</button>
			</div>

			<div class="lobby-actions">
				<button class="btn btn-primary" type="button" data-lobby-next id="lobbyVerifyNext">Verify</button>
				<button class="lobby-back" type="button" data-lobby-back>← Back</button>
			</div>
		</div>

		<!-- Step 4: Matter type (3 of 4) -->
		<div class="lobby-step" data-step="4">
			<div class="lobby-progress" data-progress="3">
				<div class="lobby-progress-label">Step 3 of 4</div>
				<div class="lobby-progress-bar" aria-hidden="true">
					<span class="filled"></span><span class="filled"></span><span class="filled"></span><span></span>
				</div>
			</div>
			<h3 class="lobby-step-title">What can we help you with today?</h3>
			<p class="lobby-step-desc">Select the option that best matches your matter.</p>
			<div class="matter-grid" role="listbox" aria-label="Matter type">
				<button class="matter-option" type="button" data-matter="Removal Proceedings" role="option" aria-selected="false">
					<span class="matter-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 4.5 6v5.7c0 4.6 3.1 7.5 7.5 9.3 4.4-1.8 7.5-4.7 7.5-9.3V6L12 3Z"/></svg>
					</span>
					<span>Removal Proceedings</span>
				</button>
				<button class="matter-option" type="button" data-matter="Asylum" role="option" aria-selected="false">
					<span class="matter-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3c2.5 2.4 4 5.4 4 9s-1.5 6.6-4 9"/><path d="M12 3c-2.5 2.4-4 5.4-4 9s1.5 6.6 4 9"/></svg>
					</span>
					<span>Asylum</span>
				</button>
				<button class="matter-option" type="button" data-matter="Family Immigration" role="option" aria-selected="false">
					<span class="matter-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3.5 19c.5-3.4 2.5-5 5.5-5s5 1.6 5.5 5"/><path d="M14 15c.8-.7 1.8-1 3-1 2.3 0 3.8 1.4 4 4"/></svg>
					</span>
					<span>Family Immigration</span>
				</button>
				<button class="matter-option" type="button" data-matter="Waivers" role="option" aria-selected="false">
					<span class="matter-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10Z"/></svg>
					</span>
					<span>Waivers</span>
				</button>
				<button class="matter-option" type="button" data-matter="Employment" role="option" aria-selected="false">
					<span class="matter-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="7" width="18" height="12" rx="2"/><path d="M9 7V5h6v2"/><path d="M3 12h18"/></svg>
					</span>
					<span>Employment</span>
				</button>
				<button class="matter-option" type="button" data-matter="Citizenship" role="option" aria-selected="false">
					<span class="matter-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="4" width="14" height="16" rx="2"/><path d="M8 8h8"/><path d="M8 12h5"/><path d="m14 16 1.4 1.4L18 15"/></svg>
					</span>
					<span>Citizenship</span>
				</button>
				<button class="matter-option" type="button" data-matter="Existing Client" role="option" aria-selected="false">
					<span class="matter-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.5"/><path d="M5.5 19c1-3.5 3.2-5 6.5-5s5.5 1.5 6.5 5"/></svg>
					</span>
					<span>Existing Client</span>
				</button>
				<button class="matter-option" type="button" data-matter="Other" role="option" aria-selected="false">
					<span class="matter-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><path d="M12 16.5h.01"/></svg>
					</span>
					<span>Other</span>
				</button>
			</div>
			<div class="lobby-error" data-error-for="matter">Please select a matter type to continue.</div>
			<div class="lobby-actions">
				<button class="btn btn-primary" type="button" data-lobby-next>Continue →</button>
				<button class="lobby-back" type="button" data-lobby-back>← Back</button>
			</div>
		</div>

		<!-- Step 5: Checked in (4 of 4) — waits until receptionist marks Ready -->
		<div class="lobby-step" data-step="5">
			<div class="lobby-progress" data-progress="4">
				<div class="lobby-progress-label">Step 4 of 4</div>
				<div class="lobby-progress-bar" aria-hidden="true">
					<span class="filled"></span><span class="filled"></span><span class="filled"></span><span class="filled"></span>
				</div>
			</div>
			<div class="lobby-step-icon success" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M20 6 9 17l-5-5"/>
				</svg>
			</div>
			<h3 class="lobby-step-title">You're Checked In!</h3>
			<p class="lobby-step-desc">Thank you. A receptionist will be with you shortly.</p>
			<p class="lobby-step-desc lobby-wait-instruction">Do not close or refresh this page. A secure meeting link will appear here as soon as our receptionist is available to assist you.</p>
			<div class="lobby-confirm-stats">
				<div>
					<strong>Status</strong>
					<span id="lobbyWaitStatus">Waiting</span>
				</div>
				<div>
					<strong>Current Position</strong>
					<span id="lobbyWaitPosition">—</span>
				</div>
			</div>
			<p class="lobby-remain" id="lobbyWaitRemain">Please remain on this page · You’re next in line</p>
			<div class="lobby-pulse" aria-hidden="true"><span></span><span></span><span></span></div>
			<p class="note" id="lobbyWaitNote" style="text-align:center;margin-top:10px">Waiting for the receptionist…</p>
		</div>

		<!-- Step 6: Reception ready -->
		<div class="lobby-step" data-step="6">
			<div class="lobby-step-icon success" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
					<path d="M6 9a6 6 0 0 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9"/>
					<path d="M10 20a2 2 0 0 0 4 0"/>
				</svg>
			</div>
			<h3 class="lobby-step-title">Your Receptionist Is Ready</h3>
			<p class="lobby-step-desc">A member of our team is ready to assist you. Click below to join your secure video reception.</p>
			<p class="phone-info" style="margin:0 0 14px;text-align:center;">On a phone or tablet, install the <strong>Zoom</strong> app first if you don’t already have it, then tap Join Reception.</p>
			<div class="lobby-actions">
				<button class="btn btn-gold" type="button" data-lobby-join="reception">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
					Join Reception
				</button>
			</div>
			<div class="lobby-error" id="lobbyJoinError" data-error-for="join">Meeting link is not available. Ask the receptionist to check Virtual Lobby → Settings.</div>
			<p class="note" id="lobbyReceptionNote" style="text-align:center;margin-top:12px">Stay on this page after joining. When intake is done, you’ll be asked to join the attorney.</p>
			<p class="lobby-powered">Powered by Zoom</p>
		</div>

		<!-- Step 7: Attorney ready -->
		<div class="lobby-step" data-step="7">
			<div class="lobby-step-icon success" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
					<path d="M12 3 4.5 6v5.5c0 4.4 3 7.2 7.5 9 4.5-1.8 7.5-4.6 7.5-9V6L12 3Z"/>
					<path d="m9 12 2 2 4-4"/>
				</svg>
			</div>
			<h3 class="lobby-step-title">Your Attorney Is Ready</h3>
			<p class="lobby-step-desc">Reception has finished intake. Leave the reception Zoom meeting if you are still in it, then join your attorney below. You may wait briefly in the Zoom Waiting Room until the attorney admits you.</p>
			<p class="phone-info" style="margin:0 0 14px;text-align:center;">On a phone or tablet, use the Zoom app to join.</p>
			<div class="lobby-actions">
				<button class="btn btn-gold" type="button" data-lobby-join="attorney">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
					Join Attorney
				</button>
			</div>
			<div class="lobby-error" id="lobbyAttorneyJoinError" data-error-for="join-attorney">Attorney meeting link is not available. Ask the receptionist to check Virtual Lobby → Settings.</div>
			<p class="lobby-powered">Powered by Zoom</p>
		</div>
		</div><!-- .lobby-modal-scroll -->
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
