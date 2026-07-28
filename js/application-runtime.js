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

	function requestOptions(form) {
		const options = {
			method: form.method || 'POST',
			credentials: 'same-origin',
			headers: { 'X-WP-Nonce': config.nonce || '' }
		};
		const hasFile = Array.from(form.querySelectorAll('input[type="file"]')).some(function(input) {
			return input.files && input.files.length;
		});
		if (hasFile) {
			options.body = new FormData(form);
			return options;
		}
		options.headers['Content-Type'] = 'application/json';
		options.body = JSON.stringify(formPayload(form));
		return options;
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
			const response = await fetch(endpoint, requestOptions(form));
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
				const newsletterTopics = form.querySelector('[data-pv-newsletter-topics]');
				if (newsletterTopics) newsletterTopics.hidden = true;
				const newsletterSubmit = form.querySelector('[data-pv-newsletter-submit]');
				if (newsletterSubmit && newsletterSubmit.dataset.pvInitialLabel) {
					newsletterSubmit.textContent = newsletterSubmit.dataset.pvInitialLabel;
				}
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
			const newsletterTopics = form.querySelector('[data-pv-newsletter-topics]');
			if (form.hasAttribute('data-pv-newsletter-steps') && newsletterTopics && newsletterTopics.hidden) {
				event.preventDefault();
				if (!form.reportValidity()) return;
				newsletterTopics.hidden = false;
				const submit = form.querySelector('[data-pv-newsletter-submit]');
				if (submit) {
					if (!submit.dataset.pvInitialLabel) submit.dataset.pvInitialLabel = submit.textContent;
					submit.textContent = 'Confirmer mon inscription';
				}
				const legend = newsletterTopics.querySelector('legend');
				if (legend) {
					legend.tabIndex = -1;
					legend.focus();
				}
				return;
			}
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

	document.addEventListener('photovault:favorite-changed', function(event) {
		if (!event.detail || event.detail.favorite) return;
		const card = document.querySelector('[data-pv-favorite-card="' + event.detail.mediaId + '"]');
		if (card) card.remove();
	});

	let dashboardController = null;

	function isDashboardUrl(url) {
		const current = document.querySelector('[data-pv-dashboard-content]');
		if (!current || url.origin !== window.location.origin) {
			return false;
		}
		return url.pathname.replace(/\/+$/, '') === window.location.pathname.replace(/\/+$/, '');
	}

	function syncDashboardNavigation(documentFragment, url) {
		document.querySelectorAll('#main-sidebar nav a[href]').forEach(function(link) {
			const source = Array.from(documentFragment.querySelectorAll('#main-sidebar nav a[href]')).find(function(candidate) {
				return candidate.href === link.href;
			});
			if (!source) return;
			link.className = source.className;
			if (source.hasAttribute('aria-current')) {
				link.setAttribute('aria-current', source.getAttribute('aria-current'));
			} else {
				link.removeAttribute('aria-current');
			}
		});
		window.history.replaceState(Object.assign({}, window.history.state, { photovaultDashboard: true }), '', url.href);
	}

	function closeDashboardSidebar() {
		const sidebar = document.getElementById('main-sidebar');
		const overlay = document.getElementById('sidebar-overlay');
		const toggle = document.getElementById('toggle-sidebar');
		if (!sidebar || !overlay || !toggle) return;
		sidebar.setAttribute('data-sidebar-open', 'false');
		sidebar.classList.add('-translate-x-full');
		overlay.hidden = true;
		overlay.classList.add('hidden');
		overlay.classList.remove('opacity-100');
		toggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('overflow-hidden');
	}

	async function navigateDashboard(url, pushState) {
		const current = document.querySelector('[data-pv-dashboard-content]');
		if (!current || current.getAttribute('aria-busy') === 'true') return;
		if (dashboardController) dashboardController.abort();
		dashboardController = new AbortController();
		current.setAttribute('aria-busy', 'true');

		try {
			const response = await fetch(url.href, {
				credentials: 'same-origin',
				headers: { 'X-Requested-With': 'XMLHttpRequest' },
				signal: dashboardController.signal
			});
			if (!response.ok) throw new Error('dashboard_navigation_failed');
			const html = await response.text();
			const nextDocument = new DOMParser().parseFromString(html, 'text/html');
			const next = nextDocument.querySelector('[data-pv-dashboard-content]');
			if (!next) throw new Error('dashboard_surface_missing');

			current.replaceWith(next);
			document.title = nextDocument.title || document.title;
			if (pushState) {
				window.history.pushState({ photovaultDashboard: true }, '', url.href);
			}
			syncDashboardNavigation(nextDocument, url);
			closeDashboardSidebar();
			const heading = next.querySelector('h1');
			if (heading) {
				heading.tabIndex = -1;
				heading.focus({ preventScroll: true });
			}
			next.dispatchEvent(new CustomEvent('photovault:navigation', { bubbles: true, detail: { url: url.href } }));
		} catch (error) {
			if (error.name !== 'AbortError') {
				window.location.assign(url.href);
			}
		} finally {
			const active = document.querySelector('[data-pv-dashboard-content]');
			if (active) active.removeAttribute('aria-busy');
		}
	}

	document.addEventListener('click', function(event) {
		if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
			return;
		}
		const link = event.target.closest('a[href]');
		if (!link || link.target || link.hasAttribute('download')) return;
		const url = new URL(link.href, window.location.href);
		if (!isDashboardUrl(url)) return;
		event.preventDefault();
		navigateDashboard(url, true);
	});

	window.addEventListener('popstate', function() {
		const url = new URL(window.location.href);
		if (isDashboardUrl(url)) {
			navigateDashboard(url, false);
		} else if (document.querySelector('[data-pv-dashboard-content]')) {
			window.location.assign(url.href);
		}
	});
})();
