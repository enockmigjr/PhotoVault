(function() {
	'use strict';

	let enabled = false;
	let currentIndex = 0;
	let activeScope = null;
	let pointerStart = null;
	let filterController = null;
	let filterDebounce = null;

	function dialog() {
		return document.getElementById('pv-gallery-lightbox');
	}

	function items() {
		if (!activeScope || !activeScope.isConnected) activeScope = document.getElementById('media-grid');
		return activeScope ? Array.from(activeScope.querySelectorAll('[data-pv-lightbox-item]')) : [];
	}

	function updateFullscreenState(active) {
		const current = dialog();
		const button = current ? current.querySelector('[data-pv-lightbox-fullscreen]') : null;
		if (!button) return;
		button.setAttribute('aria-pressed', active ? 'true' : 'false');
		button.setAttribute('aria-label', active ? 'Quitter le plein ecran' : 'Afficher en plein ecran');
		button.setAttribute('title', active ? 'Quitter le plein ecran' : 'Plein ecran');
	}

	function render(index) {
		const current = dialog();
		const collection = items();
		if (!current || !collection.length) return;
		currentIndex = (index + collection.length) % collection.length;
		const item = collection[currentIndex];
		const image = current.querySelector('[data-pv-lightbox-image]');
		const title = current.querySelector('[data-pv-lightbox-title]');
		const meta = current.querySelector('[data-pv-lightbox-meta]');
		const count = current.querySelector('[data-pv-lightbox-count]');
		const detail = current.querySelector('[data-pv-lightbox-detail]');
		const nextTitle = item.dataset.title || 'Oeuvre PhotoVault';
		image.removeAttribute('src');
		image.alt = nextTitle;
		image.src = item.dataset.previewUrl;
		title.textContent = nextTitle;
		meta.textContent = item.dataset.meta || '';
		detail.href = item.dataset.detailUrl || '#';
		count.textContent = String(currentIndex + 1).padStart(2, '0') + ' / ' + String(collection.length).padStart(2, '0');
		current.querySelectorAll('[data-pv-lightbox-prev], [data-pv-lightbox-next]').forEach(function(button) {
			button.hidden = collection.length < 2;
		});
	}

	async function toggleFullscreen() {
		const current = dialog();
		if (!current) return;
		if (document.fullscreenElement && document.exitFullscreen) {
			await document.exitFullscreen();
			return;
		}
		if (current.classList.contains('is-immersive')) {
			current.classList.remove('is-immersive');
			updateFullscreenState(false);
			return;
		}
		if (current.requestFullscreen) {
			try {
				await current.requestFullscreen({ navigationUI: 'hide' });
				current.classList.add('is-browser-fullscreen');
				updateFullscreenState(true);
				return;
			} catch (error) {
				// The immersive dialog remains the accessible fallback.
			}
		}
		current.classList.add('is-immersive');
		updateFullscreenState(true);
	}

	function reset() {
		const current = dialog();
		if (!current) return;
		if (document.fullscreenElement && document.exitFullscreen) document.exitFullscreen().catch(function() {});
		current.classList.remove('is-immersive', 'is-browser-fullscreen');
		updateFullscreenState(false);
		const image = current.querySelector('[data-pv-lightbox-image]');
		if (image) image.removeAttribute('src');
	}

	function bindDialog() {
		const current = dialog();
		if (!current || current.dataset.pvGalleryRuntime === '1') return;
		current.dataset.pvGalleryRuntime = '1';
		current.addEventListener('close', reset);
		current.addEventListener('keydown', function(event) {
			if (event.key === 'ArrowLeft') render(currentIndex - 1);
			if (event.key === 'ArrowRight') render(currentIndex + 1);
		});
		current.addEventListener('pointerdown', function(event) {
			pointerStart = { x: event.clientX, y: event.clientY };
		});
		current.addEventListener('pointerup', function(event) {
			if (!pointerStart) return;
			const horizontal = event.clientX - pointerStart.x;
			const vertical = event.clientY - pointerStart.y;
			pointerStart = null;
			if (Math.abs(horizontal) > 70 && Math.abs(horizontal) > Math.abs(vertical)) {
				render(currentIndex + (horizontal < 0 ? 1 : -1));
			}
		});
	}

	function galleryEmptyState() {
		return '<div class="pv-gallery-empty"><strong>Aucune oeuvre ne correspond a cette recherche.</strong><span>Essayez une autre collection ou reinitialisez les filtres.</span></div>';
	}

	function initializeFilters(root) {
		const form = (root || document).querySelector('[data-pv-gallery-filters]');
		if (!form || form.dataset.pvGalleryFiltersBound === '1') return;
		const grid = document.getElementById('media-grid');
		const results = document.getElementById('gallery-results');
		const resetButton = form.querySelector('#reset-filters');
		const moreButton = document.getElementById('load-more-media');
		if (!grid || !results || !moreButton || !form.dataset.endpoint) return;

		form.dataset.pvGalleryFiltersBound = '1';
		let page = 1;
		let pages = Number(form.dataset.pages || 0);

		async function updateGallery(append) {
			if (filterController) filterController.abort();
			filterController = new AbortController();
			const controller = filterController;
			const requestedPage = append ? page + 1 : 1;
			const params = new URLSearchParams(new FormData(form));
			const settings = window.photovault_ajax || {};
			params.set('page', String(requestedPage));
			results.setAttribute('aria-busy', 'true');
			moreButton.disabled = true;
			moreButton.setAttribute('aria-busy', 'true');

			try {
				const response = await fetch(form.dataset.endpoint + '?' + params.toString(), {
					credentials: 'same-origin',
					headers: settings.nonce ? { 'X-WP-Nonce': settings.nonce } : {},
					signal: controller.signal
				});
				if (!response.ok) throw new Error('gallery_request_failed');
				const payload = await response.json();
				const entries = payload.success && Array.isArray(payload.data) ? payload.data : [];
				pages = Number(payload.pages || 0);
				page = requestedPage;
				const html = entries.length ? entries.map(function(item) { return item.html; }).join('') : galleryEmptyState();
				if (append && entries.length) {
					grid.insertAdjacentHTML('beforeend', html);
				} else {
					grid.innerHTML = html;
				}
				grid.classList.toggle('is-empty', !entries.length && !append);
				moreButton.classList.toggle('hidden', !pages || page >= pages);
				activeScope = null;
				document.dispatchEvent(new CustomEvent('photovault:gallery-updated'));
			} catch (error) {
				if (error.name === 'AbortError') return;
				if (window.PhotoVaultProtectionNotice) {
					window.PhotoVaultProtectionNotice('La galerie ne peut pas etre actualisee pour le moment.');
				}
			} finally {
				if (filterController !== controller) return;
				results.setAttribute('aria-busy', 'false');
				moreButton.disabled = false;
				moreButton.removeAttribute('aria-busy');
			}
		}

		form.addEventListener('submit', function(event) {
			event.preventDefault();
			updateGallery(false);
		});
		form.querySelectorAll('select').forEach(function(field) {
			field.addEventListener('change', function() {
				updateGallery(false);
			});
		});
		const search = form.querySelector('input[type="search"]');
		if (search) {
			search.addEventListener('input', function() {
				window.clearTimeout(filterDebounce);
				filterDebounce = window.setTimeout(function() {
					updateGallery(false);
				}, 320);
			});
		}
		if (resetButton) {
			resetButton.addEventListener('click', function() {
				window.setTimeout(function() {
					updateGallery(false);
				}, 0);
			});
		}
		moreButton.addEventListener('click', function() {
			updateGallery(true);
		});
	}

	document.addEventListener('DOMContentLoaded', function() {
		initializeFilters(document);
	});

	document.addEventListener('photovault:page-ready', function() {
		enabled = true;
		activeScope = null;
		bindDialog();
		initializeFilters(document);
	});

	document.addEventListener('click', function(event) {
		if (!enabled) return;
		const current = dialog();
		const opener = event.target.closest('[data-pv-lightbox-open]');
		if (opener) {
			activeScope = opener.closest('[data-pv-lightbox-scope]') || document.getElementById('media-grid');
			const item = opener.closest('[data-pv-lightbox-item]');
			currentIndex = Math.max(0, items().indexOf(item));
			render(currentIndex);
			if (current && current.showModal) current.showModal();
			return;
		}
		if (!current) return;
		if (event.target === current || event.target.closest('[data-pv-lightbox-close]')) current.close();
		if (event.target.closest('[data-pv-lightbox-prev]')) render(currentIndex - 1);
		if (event.target.closest('[data-pv-lightbox-next]')) render(currentIndex + 1);
		if (event.target.closest('[data-pv-lightbox-fullscreen]')) {
			event.preventDefault();
			toggleFullscreen().catch(function() {
				current.classList.add('is-immersive');
				updateFullscreenState(true);
			});
		}
	});

	document.addEventListener('fullscreenchange', function() {
		if (!enabled) return;
		const current = dialog();
		if (!current) return;
		const active = document.fullscreenElement === current;
		current.classList.toggle('is-browser-fullscreen', active);
		updateFullscreenState(active || current.classList.contains('is-immersive'));
	});
})();
