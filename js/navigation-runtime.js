(function() {
	'use strict';

	const appSelector = '[data-pv-app-shell]';
	const nativeFetch = window.fetch.bind(window);
	let pendingRequests = 0;
	let navigationController = null;
	let completionTimer = null;

	function progressElement() {
		let bar = document.querySelector('[data-pv-page-progress]');
		if (bar) return bar;
		bar = document.createElement('div');
		bar.className = 'pv-page-progress';
		bar.dataset.pvPageProgress = '';
		bar.setAttribute('role', 'progressbar');
		bar.setAttribute('aria-label', 'Chargement en cours');
		document.body.appendChild(bar);
		return bar;
	}

	function startProgress() {
		pendingRequests++;
		window.clearTimeout(completionTimer);
		const bar = progressElement();
		bar.classList.remove('is-complete');
		window.requestAnimationFrame(function() {
			bar.classList.add('is-active');
		});
	}

	function finishProgress() {
		pendingRequests = Math.max(0, pendingRequests - 1);
		if (pendingRequests > 0) return;
		const bar = progressElement();
		bar.classList.add('is-complete');
		completionTimer = window.setTimeout(function() {
			bar.classList.remove('is-active', 'is-complete');
		}, 240);
	}

	window.PhotoVaultProgress = {
		start: startProgress,
		finish: finishProgress
	};

	window.fetch = function() {
		startProgress();
		return nativeFetch.apply(null, arguments).finally(finishProgress);
	};

	function normalizedAssetUrl(value) {
		try {
			return new URL(value, window.location.href).href;
		} catch (error) {
			return '';
		}
	}

	function loadMissingStyles(nextDocument) {
		const current = new Set(Array.from(document.querySelectorAll('link[rel="stylesheet"][href]')).map(function(link) {
			return normalizedAssetUrl(link.href);
		}));
		const tasks = [];
		nextDocument.querySelectorAll('link[rel="stylesheet"][href]').forEach(function(link) {
			const href = normalizedAssetUrl(link.href);
			if (!href || current.has(href)) return;
			current.add(href);
			tasks.push(new Promise(function(resolve) {
				const clone = document.createElement('link');
				clone.rel = 'stylesheet';
				clone.href = href;
				clone.addEventListener('load', resolve, { once: true });
				clone.addEventListener('error', resolve, { once: true });
				document.head.appendChild(clone);
			}));
		});
		return Promise.all(tasks);
	}

	async function loadMissingScripts(nextDocument) {
		const current = new Set(Array.from(document.querySelectorAll('script[src]')).map(function(script) {
			return normalizedAssetUrl(script.src);
		}));
		for (const source of nextDocument.querySelectorAll('script[src]')) {
			const src = normalizedAssetUrl(source.src);
			if (!src || current.has(src)) continue;
			current.add(src);
			await new Promise(function(resolve) {
				const script = document.createElement('script');
				script.src = src;
				script.async = false;
				script.addEventListener('load', resolve, { once: true });
				script.addEventListener('error', resolve, { once: true });
				document.body.appendChild(script);
			});
		}
	}

	function refreshLocalizedScripts(nextDocument) {
		nextDocument.querySelectorAll('script[id$="-js-extra"], script[id$="-js-before"]').forEach(function(source) {
			const current = document.getElementById(source.id);
			const script = document.createElement('script');
			Array.from(source.attributes).forEach(function(attribute) {
				script.setAttribute(attribute.name, attribute.value);
			});
			script.textContent = source.textContent;
			if (current) current.remove();
			document.head.appendChild(script);
		});
	}

	function activateInlineScripts(root) {
		root.querySelectorAll('script:not([src])').forEach(function(source) {
			const script = document.createElement('script');
			Array.from(source.attributes).forEach(function(attribute) {
				script.setAttribute(attribute.name, attribute.value);
			});
			script.textContent = source.textContent;
			source.replaceWith(script);
		});
	}

	function initializePublicMenu(root) {
		const menuButton = root.querySelector('#mobile-menu-button');
		const mobileMenu = root.querySelector('#mobile-menu');
		if (!menuButton || !mobileMenu || menuButton.dataset.pvNavigationBound === '1') return;
		menuButton.dataset.pvNavigationBound = '1';
		const hamburgerIcon = root.querySelector('#hamburger-icon');
		const closeIcon = root.querySelector('#close-icon');

		function setOpen(open) {
			mobileMenu.classList.toggle('hidden', !open);
			mobileMenu.classList.toggle('opacity-0', !open);
			mobileMenu.classList.toggle('scale-y-95', !open);
			mobileMenu.classList.toggle('opacity-100', open);
			mobileMenu.classList.toggle('scale-y-100', open);
			document.body.classList.toggle('overflow-hidden', open);
			menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
			menuButton.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
			if (hamburgerIcon && closeIcon) {
				hamburgerIcon.classList.toggle('hidden', open);
				closeIcon.classList.toggle('hidden', !open);
			}
		}

		menuButton.addEventListener('click', function(event) {
			event.stopPropagation();
			setOpen(mobileMenu.classList.contains('hidden'));
		});
		document.addEventListener('click', function(event) {
			if (!mobileMenu.contains(event.target) && !menuButton.contains(event.target)) setOpen(false);
		}, { signal: navigationController ? navigationController.signal : undefined });
	}

	function isProgressiveUrl(url, link) {
		if (url.origin !== window.location.origin || url.protocol.indexOf('http') !== 0) return false;
		if (link && (link.target || link.hasAttribute('download') || link.dataset.pvNative !== undefined)) return false;
		if (url.pathname.indexOf('/wp-admin/') === 0 || url.pathname.indexOf('/wp-login.php') === 0) return false;
		if (url.pathname.indexOf('/wp-json/') === 0 || url.pathname.indexOf('/secure-image') === 0) return false;
		if (url.searchParams.has('download') || url.searchParams.get('action') === 'logout') return false;
		if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return false;
		return true;
	}

	function updateDocumentMetadata(nextDocument) {
		document.title = nextDocument.title || document.title;
		document.documentElement.lang = nextDocument.documentElement.lang || document.documentElement.lang;
		document.body.className = nextDocument.body.className;
		document.body.classList.remove('overflow-hidden');
		const nextDescription = nextDocument.querySelector('meta[name="description"]');
		const description = document.querySelector('meta[name="description"]');
		if (description && nextDescription) description.content = nextDescription.content;
	}

	async function navigate(url, options) {
		const current = document.querySelector(appSelector);
		if (!current) {
			window.location.assign(url.href);
			return;
		}
		if (navigationController) navigationController.abort();
		navigationController = new AbortController();
		current.setAttribute('aria-busy', 'true');

		try {
			const response = await window.fetch(url.href, {
				credentials: 'same-origin',
				headers: {
					'Accept': 'text/html',
					'X-Requested-With': 'XMLHttpRequest'
				},
				signal: navigationController.signal
			});
			const contentType = response.headers.get('content-type') || '';
			if (!response.ok || contentType.indexOf('text/html') === -1) throw new Error('progressive_navigation_unavailable');
			const html = await response.text();
			const nextDocument = new DOMParser().parseFromString(html, 'text/html');
			const next = nextDocument.querySelector(appSelector);
			if (!next) throw new Error('progressive_surface_missing');

			await loadMissingStyles(nextDocument);
			current.replaceWith(next);
			updateDocumentMetadata(nextDocument);
			refreshLocalizedScripts(nextDocument);
			await loadMissingScripts(nextDocument);
			activateInlineScripts(next);
			initializePublicMenu(next);

			if (options && options.push) {
				window.history.pushState({ photovaultPage: true }, '', response.url);
			}
			const heading = next.querySelector('main h1, main h2, [role="main"] h1');
			if (heading) {
				heading.tabIndex = -1;
				heading.focus({ preventScroll: true });
			}
			if (!options || options.scroll !== false) window.scrollTo({ top: 0, behavior: 'auto' });
			document.dispatchEvent(new CustomEvent('photovault:page-ready', {
				detail: { url: response.url }
			}));
		} catch (error) {
			if (error.name !== 'AbortError') window.location.assign(url.href);
		} finally {
			const active = document.querySelector(appSelector);
			if (active) active.removeAttribute('aria-busy');
		}
	}

	window.PhotoVaultNavigation = {
		navigate: function(value) {
			const url = new URL(value, window.location.href);
			if (!isProgressiveUrl(url)) {
				window.location.assign(url.href);
				return;
			}
			navigate(url, { push: true });
		}
	};

	document.addEventListener('click', function(event) {
		if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
		const link = event.target.closest('a[href]');
		if (!link) return;
		const url = new URL(link.href, window.location.href);
		if (!isProgressiveUrl(url, link)) return;
		event.preventDefault();
		navigate(url, { push: true });
	});

	document.addEventListener('submit', function(event) {
		if (event.defaultPrevented) return;
		const form = event.target;
		if (!(form instanceof HTMLFormElement) || (form.method || 'get').toLowerCase() !== 'get') return;
		if (form.target || form.hasAttribute('data-pv-native') || form.hasAttribute('data-pv-async-form') || form.enctype === 'multipart/form-data') return;
		const url = new URL(form.getAttribute('action') || window.location.href, window.location.href);
		new FormData(form).forEach(function(value, key) {
			if (typeof value === 'string') url.searchParams.append(key, value);
		});
		if (!isProgressiveUrl(url)) return;
		event.preventDefault();
		navigate(url, { push: true });
	});

	window.addEventListener('popstate', function() {
		const url = new URL(window.location.href);
		if (isProgressiveUrl(url)) {
			navigate(url, { push: false, scroll: false });
		}
	});
})();
