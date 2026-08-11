(function() {
	'use strict';

	const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	function setBusy(form, busy) {
		form.toggleAttribute('aria-busy', busy);
		form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function(control) {
			control.disabled = busy;
			control.toggleAttribute('aria-busy', busy);
			if (control instanceof HTMLInputElement) {
				if (busy) {
					control.dataset.pvIdleValue = control.value;
					control.value = 'Chargement...';
				} else if (control.dataset.pvIdleValue) {
					control.value = control.dataset.pvIdleValue;
					delete control.dataset.pvIdleValue;
				}
			}
		});
	}

	/**
	 * Validate the login form before any request is sent.
	 * Returns null for non-login forms and a field => message map otherwise.
	 */
	function validateLogin(form) {
		const loginField = form.elements.namedItem('log');
		const passwordField = form.elements.namedItem('pwd');
		if (!loginField || !passwordField) return null;

		const errors = {};
		const loginValue = loginField.value.trim();

		if (loginValue === '') {
			errors.log = 'Saisissez votre identifiant ou votre adresse e-mail.';
		} else if (loginValue.indexOf('@') !== -1 && !EMAIL_PATTERN.test(loginValue)) {
			errors.log = 'Saisissez une adresse e-mail valide.';
		}

		if (passwordField.value === '') {
			errors.pwd = 'Saisissez votre mot de passe.';
		}

		return errors;
	}

	function focusFirstInvalid(form) {
		const field = form.querySelector('[aria-invalid="true"]');
		if (field) field.focus({ preventScroll: false });
	}

	function clearErrors(form) {
		form.querySelectorAll('[data-pv-field-error]').forEach(function(error) {
			error.remove();
		});
		form.querySelectorAll('[aria-invalid="true"]').forEach(function(field) {
			field.removeAttribute('aria-invalid');
		});
	}

	function showErrors(form, errors) {
		Object.keys(errors || {}).forEach(function(name) {
			const field = form.elements.namedItem(name);
			if (!field || typeof errors[name] !== 'string') return;
			field.setAttribute('aria-invalid', 'true');
			const error = document.createElement('small');
			error.dataset.pvFieldError = '';
			error.className = 'pv-async-field-error';
			error.textContent = errors[name];
			field.insertAdjacentElement('afterend', error);
		});
	}

	function feedback(form, message, success) {
		let notice = form.querySelector('[data-pv-auth-feedback]');
		if (!notice) {
			notice = document.createElement('div');
			notice.dataset.pvAuthFeedback = '';
			notice.tabIndex = -1;
			form.prepend(notice);
		}
		notice.replaceChildren();
		notice.className = 'pv-auth-notice ' + (success ? 'is-success' : 'is-error');
		notice.setAttribute('role', success ? 'status' : 'alert');

		const text = document.createElement('span');
		text.textContent = message;
		notice.appendChild(text);

		const close = document.createElement('button');
		close.type = 'button';
		close.className = 'pv-auth-notice__close';
		close.setAttribute('aria-label', 'Fermer la notification');
		close.textContent = '\u00d7';
		close.addEventListener('click', function() {
			notice.remove();
		});
		notice.appendChild(close);
		notice.focus({ preventScroll: true });
	}

	async function submit(form) {
		if (form.getAttribute('aria-busy') === 'true') return;
		clearErrors(form);

		const validationErrors = validateLogin(form);
		if (validationErrors && Object.keys(validationErrors).length > 0) {
			showErrors(form, validationErrors);
			focusFirstInvalid(form);
			return;
		}

		const payload = new FormData(form);
		setBusy(form, true);

		try {
			const response = await fetch(form.getAttribute('action') || window.location.href, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Accept': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: payload
			});
			const result = await response.json().catch(function() {
				return {};
			});
			if (!response.ok || !result.success) {
				throw result;
			}

			feedback(form, result.message || 'Action terminee.', true);
			form.dispatchEvent(new CustomEvent('photovault:auth-success', { bubbles: true, detail: result }));
			if (result.data && result.data.redirect_url) {
				window.setTimeout(function() {
					// Authentication changes the session and every REST nonce.
					window.location.assign(result.data.redirect_url);
				}, 250);
				return;
			}
			if (form.dataset.pvAuthSuccess === 'reset') form.reset();
		} catch (result) {
			showErrors(form, result.errors || {});
			feedback(form, result.message || 'Une erreur est survenue. Veuillez reessayer.', false);
			setBusy(form, false);
		}
	}

	document.addEventListener('submit', function(event) {
		const form = event.target.closest('form[data-pv-auth-form]');
		if (!form) return;
		event.preventDefault();
		submit(form);
	});
})();
