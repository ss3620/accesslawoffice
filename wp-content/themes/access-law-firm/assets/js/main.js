/**
 * Access Law Firm — front-end interactions.
 * Flip cards, practice modal, and Virtual Lobby multi-step form.
 */
(function () {
  'use strict';

  /* ------------------------------------------------------------------ */
  /* Flip credential cards                                                */
  /* ------------------------------------------------------------------ */
  document.querySelectorAll('.flip-card').forEach(function (card) {
    card.addEventListener('click', function () {
      var next = !card.classList.contains('is-flipped');
      document.querySelectorAll('.flip-card.is-flipped').forEach(function (other) {
        if (other !== card) {
          other.classList.remove('is-flipped');
          other.setAttribute('aria-pressed', 'false');
        }
      });
      card.classList.toggle('is-flipped', next);
      card.setAttribute('aria-pressed', String(next));
    });
  });

  /* ------------------------------------------------------------------ */
  /* Practice area modal                                                  */
  /* ------------------------------------------------------------------ */
  (function initPracticeModal() {
    var modal = document.getElementById('practiceModal');
    var title = document.getElementById('practiceModalTitle');
    var text = document.getElementById('practiceModalText');
    if (!modal || !title || !text) return;

    var lastTrigger = null;

    document.querySelectorAll('.practice-card').forEach(function (card) {
      card.addEventListener('click', function () {
        lastTrigger = card;
        title.textContent = card.dataset.title || '';
        text.textContent = card.dataset.details || '';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        var closeBtn = modal.querySelector('.practice-modal-close');
        if (closeBtn) closeBtn.focus();
      });
    });

    function closeModal() {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (lastTrigger) lastTrigger.focus();
    }

    modal.querySelectorAll('[data-close-practice-modal]').forEach(function (el) {
      el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && modal.classList.contains('is-open')) {
        closeModal();
      }
    });
  })();

  /* ------------------------------------------------------------------ */
  /* Virtual Lobby multi-step controller                                  */
  /* ------------------------------------------------------------------ */
  (function initLobbyModal() {
    var modal = document.getElementById('lobbyModal');
    if (!modal) return;

    var steps = Array.prototype.slice.call(modal.querySelectorAll('.lobby-step'));
    var currentStep = 0;
    var selectedMatter = '';
    var busy = false;
    var config = window.alfLobby || {};
    var state = {
      name: '',
      phone: '',
      rawPhone: '',
      country: '+1',
      otp: '',
      matter: ''
    };

    function showError(key, show, message) {
      var el = modal.querySelector('[data-error-for="' + key + '"]');
      if (!el) return;
      if (message) el.textContent = message;
      el.classList.toggle('show', !!show);
    }

    function setBusy(btn, isBusy, busyLabel) {
      busy = isBusy;
      if (!btn) return;
      if (isBusy) {
        btn.dataset.originalLabel = btn.dataset.originalLabel || btn.innerHTML;
        btn.innerHTML = busyLabel || 'Please wait…';
        btn.disabled = true;
      } else {
        if (btn.dataset.originalLabel) {
          btn.innerHTML = btn.dataset.originalLabel;
        }
        btn.disabled = false;
      }
    }

    function ajaxPost(action, data) {
      var body = new URLSearchParams();
      body.append('action', action);
      body.append('nonce', config.nonce || '');
      Object.keys(data || {}).forEach(function (key) {
        body.append(key, data[key]);
      });

      return fetch(config.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString()
      }).then(function (res) {
        return res.json().catch(function () {
          return { success: false, data: { message: 'Unexpected server response. Please try again.' } };
        });
      }).catch(function () {
        return { success: false, data: { message: 'Network error. Please check your connection and try again.' } };
      });
    }

    function clearErrors() {
      modal.querySelectorAll('.lobby-error').forEach(function (el) {
        el.classList.remove('show');
      });
    }

    function showStep(n) {
      currentStep = n;
      clearErrors();
      steps.forEach(function (step) {
        var id = parseInt(step.getAttribute('data-step'), 10);
        step.classList.toggle('active', id === n);
      });

      if (n === 3) {
        var display = document.getElementById('lobbyPhoneDisplay');
        if (display) {
          display.textContent = state.phone || 'your phone';
        }
        var firstOtp = modal.querySelector('[data-otp]');
        if (firstOtp) {
          setTimeout(function () { firstOtp.focus(); }, 50);
        }
      }

      if (n === 1) {
        var nameInput = document.getElementById('lobbyFullName');
        if (nameInput) {
          setTimeout(function () { nameInput.focus(); }, 50);
        }
      }

      if (n === 2) {
        var phoneInput = document.getElementById('lobbyPhone');
        if (phoneInput) {
          setTimeout(function () { phoneInput.focus(); }, 50);
        }
      }
    }

    function openModal() {
      modal.classList.add('open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('lobby-open');
      showStep(0);
    }

    function closeModal() {
      modal.classList.remove('open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('lobby-open');
      // Reset for next visit
      state = { name: '', phone: '', rawPhone: '', country: '+1', otp: '', matter: '' };
      selectedMatter = '';
      var nameInput = document.getElementById('lobbyFullName');
      var phoneInput = document.getElementById('lobbyPhone');
      if (nameInput) nameInput.value = '';
      if (phoneInput) phoneInput.value = '';
      modal.querySelectorAll('[data-otp]').forEach(function (input) {
        input.value = '';
      });
      modal.querySelectorAll('.matter-option').forEach(function (btn) {
        btn.classList.remove('selected');
        btn.setAttribute('aria-selected', 'false');
      });
      showStep(0);
    }

    function digitsOnly(value) {
      return String(value || '').replace(/\D/g, '');
    }

    function validateCurrent() {
      clearErrors();

      if (currentStep === 1) {
        var nameInput = document.getElementById('lobbyFullName');
        var name = nameInput ? nameInput.value.trim() : '';
        if (name.length < 2) {
          showError('name', true);
          if (nameInput) nameInput.focus();
          return false;
        }
        state.name = name;
        return true;
      }

      if (currentStep === 2) {
        var phoneInput = document.getElementById('lobbyPhone');
        var countrySelect = document.getElementById('lobbyCountry');
        var country = countrySelect ? countrySelect.value : '+1';
        var phone = digitsOnly(phoneInput ? phoneInput.value : '');
        if (phone.length !== 10) {
          showError('phone', true, 'Please enter a valid 10-digit mobile number.');
          if (phoneInput) phoneInput.focus();
          return false;
        }
        state.country = country;
        state.rawPhone = phone;
        if (country === '+91') {
          state.phone = '+91 ' + phone.slice(0, 5) + ' ' + phone.slice(5);
        } else {
          state.phone = '+1 (' + phone.slice(0, 3) + ') ' + phone.slice(3, 6) + '-' + phone.slice(6);
        }
        return true;
      }

      if (currentStep === 3) {
        var otpInputs = modal.querySelectorAll('[data-otp]');
        var code = '';
        otpInputs.forEach(function (input) {
          code += input.value.replace(/\D/g, '');
        });
        if (code.length !== 6) {
          showError('otp', true);
          if (otpInputs[0]) otpInputs[0].focus();
          return false;
        }
        state.otp = code;
        return true;
      }

      if (currentStep === 4) {
        if (!selectedMatter) {
          showError('matter', true);
          return false;
        }
        state.matter = selectedMatter;
        return true;
      }

      return true;
    }

    function sendOtp(triggerBtn) {
      if (busy) return;
      clearErrors();
      setBusy(triggerBtn, true, 'Sending…');
      ajaxPost('alf_send_otp', { phone: state.rawPhone, country: state.country }).then(function (res) {
        setBusy(triggerBtn, false);
        if (res && res.success) {
          showStep(3);
        } else {
          var msg = res && res.data && res.data.message ? res.data.message : 'Could not send the code. Please try again.';
          showError('phone', true, msg);
        }
      });
    }

    function verifyOtp(triggerBtn) {
      if (busy) return;
      var otpInputs = modal.querySelectorAll('[data-otp]');
      var code = '';
      otpInputs.forEach(function (input) {
        code += input.value.replace(/\D/g, '');
      });
      if (code.length !== 6) {
        showError('otp', true, 'Please enter the complete 6-digit code.');
        if (otpInputs[0]) otpInputs[0].focus();
        return;
      }
      clearErrors();
      setBusy(triggerBtn, true, 'Verifying…');
      ajaxPost('alf_verify_otp', { phone: state.rawPhone, country: state.country, code: code }).then(function (res) {
        setBusy(triggerBtn, false);
        if (res && res.success) {
          state.otp = code;
          showStep(4);
        } else {
          var msg = res && res.data && res.data.message ? res.data.message : 'That code is incorrect. Please try again.';
          showError('otp', true, msg);
        }
      });
    }

    function goNext(triggerBtn) {
      if (busy) return;

      // Step 2: validate phone then send SMS via Twilio.
      if (currentStep === 2) {
        if (!validateCurrent()) return;
        sendOtp(triggerBtn);
        return;
      }

      // Step 3: verify the SMS code via Twilio.
      if (currentStep === 3) {
        verifyOtp(triggerBtn);
        return;
      }

      if (!validateCurrent()) return;
      if (currentStep < steps.length - 1) {
        showStep(currentStep + 1);
      }
    }

    function goBack() {
      if (busy) return;
      if (currentStep > 0) {
        showStep(currentStep - 1);
      }
    }

    // Open triggers
    document.querySelectorAll('.open-lobby').forEach(function (btn) {
      btn.addEventListener('click', openModal);
    });

    // Close
    modal.querySelectorAll('[data-lobby-close]').forEach(function (btn) {
      btn.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', function (event) {
      if (event.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && modal.classList.contains('open')) {
        closeModal();
      }
    });

    // Next / Back
    modal.querySelectorAll('[data-lobby-next]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        goNext(btn);
      });
    });

    modal.querySelectorAll('[data-lobby-back]').forEach(function (btn) {
      btn.addEventListener('click', goBack);
    });

    // Resend code via Twilio
    modal.querySelectorAll('[data-lobby-resend]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (busy || !state.rawPhone) return;
        modal.querySelectorAll('[data-otp]').forEach(function (input) {
          input.value = '';
        });
        clearErrors();
        var originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Sending…';
        ajaxPost('alf_send_otp', { phone: state.rawPhone, country: state.country }).then(function (res) {
          btn.disabled = false;
          if (res && res.success) {
            btn.textContent = 'Code resent';
          } else {
            var msg = res && res.data && res.data.message ? res.data.message : 'Could not resend the code.';
            showError('otp', true, msg);
            btn.textContent = originalText;
          }
          setTimeout(function () {
            btn.textContent = 'Resend Code';
          }, 2500);
        });
        var first = modal.querySelector('[data-otp]');
        if (first) first.focus();
      });
    });

    // Join Reception (prototype)
    modal.querySelectorAll('[data-lobby-join]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        alert('Prototype only — a Microsoft Teams meeting would open here for: ' + (state.name || 'guest'));
        closeModal();
      });
    });

    // Matter selection
    modal.querySelectorAll('.matter-option').forEach(function (btn) {
      btn.addEventListener('click', function () {
        modal.querySelectorAll('.matter-option').forEach(function (other) {
          other.classList.remove('selected');
          other.setAttribute('aria-selected', 'false');
        });
        btn.classList.add('selected');
        btn.setAttribute('aria-selected', 'true');
        selectedMatter = btn.getAttribute('data-matter') || '';
        showError('matter', false);
      });
    });

    // OTP auto-advance / backspace
    var otpInputs = modal.querySelectorAll('[data-otp]');
    otpInputs.forEach(function (input, index) {
      input.addEventListener('input', function () {
        var val = input.value.replace(/\D/g, '');
        input.value = val.slice(0, 1);
        if (val && index < otpInputs.length - 1) {
          otpInputs[index + 1].focus();
        }
        showError('otp', false);
      });

      input.addEventListener('keydown', function (event) {
        if (event.key === 'Backspace' && !input.value && index > 0) {
          otpInputs[index - 1].focus();
        }
        if (event.key === 'Enter') {
          goNext(modal.querySelector('.lobby-step[data-step="3"] [data-lobby-next]'));
        }
      });

      input.addEventListener('paste', function (event) {
        event.preventDefault();
        var pasted = digitsOnly((event.clipboardData || window.clipboardData).getData('text')).slice(0, 6);
        pasted.split('').forEach(function (digit, i) {
          if (otpInputs[i]) otpInputs[i].value = digit;
        });
        var focusIndex = Math.min(pasted.length, otpInputs.length - 1);
        if (otpInputs[focusIndex]) otpInputs[focusIndex].focus();
      });
    });

    // Country dropdown: update placeholder
    var countrySelect = document.getElementById('lobbyCountry');
    var phoneInputEl = document.getElementById('lobbyPhone');
    if (countrySelect && phoneInputEl) {
      countrySelect.addEventListener('change', function () {
        var selected = countrySelect.options[countrySelect.selectedIndex];
        var placeholder = selected ? selected.getAttribute('data-placeholder') : '';
        if (placeholder) phoneInputEl.placeholder = placeholder;
        phoneInputEl.value = '';
        clearErrors();
      });
    }

    // Enter key on name/phone
    [
      { id: 'lobbyFullName', step: 1 },
      { id: 'lobbyPhone', step: 2 }
    ].forEach(function (item) {
      var el = document.getElementById(item.id);
      if (!el) return;
      el.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          goNext(modal.querySelector('.lobby-step[data-step="' + item.step + '"] [data-lobby-next]'));
        }
      });
    });
  })();
})();
