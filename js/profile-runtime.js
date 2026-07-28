(function() {
	'use strict';
	const config = window.photovault_ajax || {};

	function prepareAsyncForms() {
		document.querySelectorAll('form').forEach(function(form) {
			const profileAction = form.elements.profile_action;
			const legacyAction = form.elements.action ? form.elements.action.value : '';
			if (profileAction) {
				form.dataset.pvAsyncForm = '';
				form.dataset.profileAsync = '';
				form.dataset.pvEndpoint = (config.identity_rest_url || '') + '/account';
			} else if (legacyAction === 'identity_security_kit_resend_email_verification') {
				form.dataset.pvAsyncForm = '';
				form.dataset.profileAsync = '';
				form.dataset.profileAction = 'resend_verification';
				form.dataset.pvEndpoint = (config.identity_rest_url || '') + '/account';
				const action = document.createElement('input');
				action.type = 'hidden';
				action.name = 'account_action';
				action.value = 'resend_verification';
				form.appendChild(action);
			} else if (legacyAction === 'identity_security_kit_cancel_email_change') {
				form.dataset.pvAsyncForm = '';
				form.dataset.profileAsync = '';
				form.dataset.profileAction = 'cancel_email_change';
				form.dataset.pvEndpoint = (config.identity_rest_url || '') + '/account';
				const action = document.createElement('input');
				action.type = 'hidden';
				action.name = 'account_action';
				action.value = 'cancel_email_change';
				form.appendChild(action);
			} else if (legacyAction === 'photovault_save_preferences') {
				form.dataset.pvAsyncForm = '';
				form.dataset.profileAsync = '';
				form.dataset.profileAction = 'preferences';
				form.dataset.pvEndpoint = (config.rest_url || '') + '/account/preferences';
			}
		});
	}

	function setText(selector, value) {
		const element = document.querySelector(selector);
		if (element && typeof value === 'string') {
			element.textContent = value || element.dataset.emptyLabel || '';
		}
	}

	function showToast(message, success) {
		document.querySelectorAll('[data-pv-runtime-toast]').forEach(function(toast) {
			toast.remove();
		});
		const toast = document.createElement('div');
		toast.className = 'pv-runtime-toast ' + (success ? 'is-success' : 'is-error');
		toast.dataset.pvRuntimeToast = '';
		toast.setAttribute('role', success ? 'status' : 'alert');
		const text = document.createElement('p');
		text.textContent = message;
		toast.appendChild(text);
		const close = document.createElement('button');
		close.type = 'button';
		close.setAttribute('aria-label', 'Fermer la notification');
		close.textContent = '\u00d7';
		close.addEventListener('click', function() {
			toast.remove();
		});
		toast.appendChild(close);
		document.body.appendChild(toast);
		window.setTimeout(function() {
			toast.remove();
		}, 7000);
	}

	document.addEventListener('photovault:success', function(event) {
		const form = event.target;
		if (!form.matches('[data-profile-async]')) {
			return;
		}
		const result = event.detail || {};
		const data = result.data || {};
		const action = form.elements.profile_action ? form.elements.profile_action.value : form.dataset.profileAction;

		if (action === 'avatar' && data.avatar_url) {
			const image = document.querySelector('[data-profile-avatar-image]');
			if (image) image.src = data.avatar_url;
		}
		if (action === 'identity') {
			setText('[data-profile-display-name]', data.display_name);
			setText('[data-profile-bio]', data.bio);
		}
		if (action === 'phone') {
			setText('[data-profile-phone]', data.phone);
			setText('[data-profile-phone-status]', 'A verifier');
		}
		if (action === 'preferences') {
			const density = form.elements.gallery_density.options[form.elements.gallery_density.selectedIndex].text;
			const landing = form.elements.dashboard_landing.options[form.elements.dashboard_landing.selectedIndex].text;
			const motion = form.elements.reduce_motion.checked ? 'reduites' : 'actives';
			setText('[data-profile-preferences-summary]', 'Galerie ' + density.toLowerCase() + ' - animations ' + motion + ' - ouverture sur ' + landing);
			document.body.classList.toggle('pv-gallery-compact', form.elements.gallery_density.value === 'compact');
			document.body.classList.toggle('pv-gallery-editorial', form.elements.gallery_density.value !== 'compact');
			document.body.classList.toggle('pv-reduce-motion', form.elements.reduce_motion.checked);
		}
		if (action === 'password' || action === 'email' || action === 'avatar') {
			form.reset();
		}
		const dialog = form.closest('dialog');
		if (dialog) dialog.close();
		showToast(result.message || 'Modification enregistree.', true);
	});

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', prepareAsyncForms);
	} else {
		prepareAsyncForms();
	}
})();
