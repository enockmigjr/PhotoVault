(function() {
	'use strict';

	const config = window.photovault_ajax || {};

	function getSubmitter(form, event) {
		return event.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
	}

	function setBusy(form, submitter, busy) {
		form.toggleAttribute('aria-busy', busy);
		if (!submitter) {
			return;
		}
		submitter.toggleAttribute('aria-busy', busy);
		submitter.disabled = busy;
	}

	function clearErrors(form) {
		form.querySelectorAll('[data-pv-field-error]').forEach(function(error) {
			error.remove();
		});
		form.querySelectorAll('[aria-invalid="true"]').forEach(function(field) {
			field.removeAttribute('aria-invalid');
		});
	}

	function showFieldErrors(form, errors) {
		Object.keys(errors || {}).forEach(function(name) {
			const field = form.elements.namedItem(name);
			if (!field || typeof errors[name] !== 'string') {
				return;
			}
			field.setAttribute('aria-invalid', 'true');
			const message = document.createElement('small');
			message.className = 'pv-async-field-error';
			message.dataset.pvFieldError = '';
			message.textContent = errors[name];
			field.insertAdjacentElement('afterend', message);
		});
	}

	function getFeedback(form) {
		let feedback = form.querySelector('[data-pv-async-feedback]');
		if (feedback) {
			return feedback;
		}
		feedback = document.createElement('div');
		feedback.dataset.pvAsyncFeedback = '';
		feedback.className = 'pv-async-feedback';
		feedback.tabIndex = -1;
		feedback.hidden = true;
		form.prepend(feedback);
		return feedback;
	}

	function showFeedback(form, message, success, action) {
		const feedback = getFeedback(form);
		feedback.replaceChildren();
		feedback.hidden = false;
		feedback.classList.toggle('is-success', success);
		feedback.classList.toggle('is-error', !success);
		feedback.setAttribute('role', success ? 'status' : 'alert');

		const text = document.createElement('span');
		text.textContent = message;
		feedback.appendChild(text);

		if (action && action.url && action.label) {
			const link = document.createElement('a');
			link.href = action.url;
			link.textContent = action.label;
			feedback.appendChild(link);
		}

		const close = document.createElement('button');
		close.type = 'button';
		close.className = 'pv-async-feedback-close';
		close.setAttribute('aria-label', 'Fermer la notification');
		close.textContent = '\u00d7';
		close.addEventListener('click', function() {
			feedback.hidden = true;
		});
		feedback.appendChild(close);
		feedback.focus({ preventScroll: true });
	}

	function formPayload(form) {
		const payload = {};
		new FormData(form).forEach(function(value, key) {
			if (Object.prototype.hasOwnProperty.call(payload, key)) {
				payload[key] = Array.isArray(payload[key]) ? payload[key].concat(value) : [payload[key], value];
			} else {
				payload[key] = value;
			}
		});
		return payload;
	}

	async function submitForm(form, event) {
		const endpoint = form.dataset.pvEndpoint;
		const submitter = getSubmitter(form, event);
		if (!endpoint || form.getAttribute('aria-busy') === 'true') {
			return;
		}

		event.preventDefault();
		clearErrors(form);
		setBusy(form, submitter, true);

		try {
			const response = await fetch(endpoint, {
				method: form.method || 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': config.nonce || ''
				},
				body: JSON.stringify(formPayload(form))
			});
			const result = await response.json().catch(function() {
				return {};
			});
			if (!response.ok || !result.success) {
				throw { result: result, status: response.status };
			}

			const action = result.data && result.data.dashboard_url ? {
				url: result.data.dashboard_url,
				label: 'Voir mes reservations'
			} : null;
			showFeedback(form, result.message || 'Modification enregistree.', true, action);

			if (form.dataset.pvSuccess === 'reset') {
				form.reset();
			}
			form.dataset.pvStepReady = '0';
			const modalId = form.dataset.pvModalStep;
			const modal = modalId ? document.getElementById(modalId) : null;
			if (modal && typeof modal.close === 'function') {
				modal.close();
			}
			if (form.dataset.pvSuccess === 'remove') {
				const item = form.closest('[data-pv-async-item]');
				if (item) {
					item.remove();
				}
			}
			form.dispatchEvent(new CustomEvent('photovault:success', { bubbles: true, detail: result }));
		} catch (failure) {
			const result = failure && failure.result ? failure.result : {};
			showFieldErrors(form, result.errors || {});
			showFeedback(form, result.message || 'Une erreur est survenue. Veuillez reessayer.', false);
			form.dispatchEvent(new CustomEvent('photovault:error', { bubbles: true, detail: result }));
		} finally {
			setBusy(form, submitter, false);
		}
	}

	document.addEventListener('submit', function(event) {
		const form = event.target.closest('form[data-pv-async-form]');
		if (form) {
			const modalId = form.dataset.pvModalStep;
			if (modalId && form.dataset.pvStepReady !== '1') {
				event.preventDefault();
				if (!form.reportValidity()) {
					return;
				}
				const modal = document.getElementById(modalId);
				if (modal && typeof modal.showModal === 'function') {
					modal.showModal();
				} else if (modal) {
					modal.hidden = false;
				}
				return;
			}
			submitForm(form, event);
		}
	});

	document.addEventListener('click', function(event) {
		const confirm = event.target.closest('[data-pv-step-confirm]');
		if (confirm) {
			const form = document.getElementById(confirm.dataset.pvStepConfirm);
			if (form) {
				form.dataset.pvStepReady = '1';
				form.requestSubmit();
			}
			return;
		}

		const cancel = event.target.closest('[data-pv-step-cancel]');
		if (!cancel) {
			return;
		}
		const modal = cancel.closest('dialog');
		if (modal && typeof modal.close === 'function') {
			modal.close();
		} else if (modal) {
			modal.hidden = true;
		}
	});
})();
