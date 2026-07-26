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
			<p style="margin-top:12px;font-size:.76rem;color:#9fb3cf">Phase One design prototype — photographs are temporary placeholders.</p>
		</div>
		<div>
			<b>Contact</b>
			<p style="margin-top:10px">(713) 489-2089<br>info@accesslawfirm.com<br>Houston, Texas</p>
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

<!-- Virtual Lobby multi-step popup -->
<div class="lobby-modal" id="lobbyModal" role="dialog" aria-modal="true" aria-labelledby="lobbyModalTitle" aria-hidden="true">
	<div class="lobby-modal-card">
		<button class="lobby-modal-close" type="button" aria-label="Close Virtual Lobby" data-lobby-close>&times;</button>

		<!-- Step 0: Welcome -->
		<?php $alf_lobby_open = alf_is_lobby_open(); ?>
		<div class="lobby-step active" data-step="0">
			<div class="lobby-welcome">
				<div class="lobby-brand-mark" aria-hidden="true">A</div>
				<div class="eyebrow" style="color:#f0bd5d">Access Law Firm</div>
				<?php if ( $alf_lobby_open ) : ?>
					<h2 id="lobbyModalTitle">Welcome to our Virtual Reception</h2>
					<p>Check in securely to speak with our live receptionist and connect with the attorney.</p>
					<div class="lobby-status-box">
						<strong>Estimated Wait 12–18 Minutes</strong>
						<div class="lobby-status-row">
							<span>Lobby Status</span>
							<span class="lobby-status-open"><span class="dot" style="box-shadow:none"></span> OPEN</span>
						</div>
					</div>
					<button class="btn btn-gold" type="button" data-lobby-next style="width:100%">Enter Lobby</button>
				<?php else : ?>
					<h2 id="lobbyModalTitle">The Virtual Lobby Is Closed</h2>
					<p>Our live reception is currently offline. Please check back during lobby hours.</p>
					<div class="lobby-status-box">
						<strong>Lobby Hours</strong>
						<div class="lobby-status-row">
							<span>Mon–Fri</span><span>9:00 AM – 5:00 PM</span>
						</div>
						<div class="lobby-status-row">
							<span>Sat–Sun</span><span>10:00 AM – 3:30 PM</span>
						</div>
					</div>
					<a class="btn btn-secondary" href="tel:+17134892089" style="width:100%">Call the Office</a>
				<?php endif; ?>
			</div>
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
			<p class="lobby-step-desc">We will send a 6-digit verification code.</p>
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
				<div class="phone-info">A 6-digit code will be sent by SMS for verification. Standard message rates may apply.</div>
			</div>
			<div class="lobby-actions">
				<button class="btn btn-primary" type="button" data-lobby-next>Send Code →</button>
				<button class="lobby-back" type="button" data-lobby-back>← Back</button>
			</div>
		</div>

		<!-- Step 3: OTP Verify (still 2 of 4 visually) -->
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
			<div class="lobby-actions">
				<button class="btn btn-primary" type="button" data-lobby-next>Verify</button>
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

		<!-- Step 5: Checked in (4 of 4) -->
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
			<p class="lobby-step-desc">A receptionist will be with you shortly. Please keep this window open.</p>
			<div class="lobby-confirm-stats">
				<div>
					<strong>Estimated Wait</strong>
					<span>12 Minutes</span>
				</div>
				<div>
					<strong>Current Position</strong>
					<span>#3</span>
				</div>
			</div>
			<div class="lobby-actions">
				<button class="btn btn-primary" type="button" data-lobby-next>Continue</button>
			</div>
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
			<p class="lobby-step-desc">Join the secure virtual reception to speak with our live assistant.</p>
			<div class="lobby-actions">
				<button class="btn btn-gold" type="button" data-lobby-join>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
					Join Reception
				</button>
			</div>
			<p class="lobby-powered">Powered by Microsoft Teams · Prototype only — no meeting is started.</p>
		</div>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
