/**
 * PhotoVault Theme Global JavaScript
 * Handles mobile menu animations, layout events, and general interaction.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Menu mobile Hamburger
    const menuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const closeIcon = document.getElementById('close-icon');

    if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = !mobileMenu.classList.contains('hidden');

            if (isOpen) {
                // Fermer le menu
                closeMobileMenu();
            } else {
                // Ouvrir le menu
                openMobileMenu();
            }
        });

        // Fermer le menu mobile en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !menuButton.contains(e.target)) {
                if (!mobileMenu.classList.contains('hidden')) {
                    closeMobileMenu();
                }
            }
        });
    }

    function openMobileMenu() {
        mobileMenu.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
		menuButton.setAttribute('aria-expanded', 'true');
		menuButton.setAttribute('aria-label', 'Fermer le menu');
        // Ajouter un léger délai pour la transition d'opacité et de scale
        setTimeout(() => {
            mobileMenu.classList.remove('opacity-0', 'scale-y-95');
            mobileMenu.classList.add('opacity-100', 'scale-y-100');
        }, 10);

        if (hamburgerIcon && closeIcon) {
            hamburgerIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
        }
    }

    function closeMobileMenu() {
        mobileMenu.classList.remove('opacity-100', 'scale-y-100');
        mobileMenu.classList.add('opacity-0', 'scale-y-95');
        document.body.classList.remove('overflow-hidden');
		menuButton.setAttribute('aria-expanded', 'false');
		menuButton.setAttribute('aria-label', 'Ouvrir le menu');

        if (hamburgerIcon && closeIcon) {
            hamburgerIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }

        // Attendre la fin de la transition avant de cacher complètement
        setTimeout(() => {
            if (mobileMenu.classList.contains('opacity-0')) {
                mobileMenu.classList.add('hidden');
            }
        }, 300);
    }
});

/**
 * PhotoVault notice system: custom non-browser dialog for protected actions.
 */
document.addEventListener('DOMContentLoaded', function() {
    let notice = document.getElementById('photovault-protection-notice');

    if (!notice) {
        notice = document.createElement('div');
        notice.id = 'photovault-protection-notice';
        notice.className = 'pv-protection-notice';
        notice.setAttribute('role', 'dialog');
        notice.setAttribute('aria-live', 'polite');
        notice.innerHTML = '<div class="pv-protection-notice__panel"><button type="button" class="pv-protection-notice__close" aria-label="Fermer">&times;</button><span class="pv-protection-notice__eyebrow">PhotoVault</span><strong>Image protegee</strong><p>La sauvegarde directe est desactivee ici. Ouvrez la fiche de l\'oeuvre pour consulter les options disponibles.</p></div>';
        document.body.appendChild(notice);
    }

    const closeButton = notice.querySelector('.pv-protection-notice__close');
    let timeoutId = null;

    function showNotice(message) {
        const paragraph = notice.querySelector('p');
        if (message && paragraph) {
            paragraph.textContent = message;
        }
        notice.classList.add('is-visible');
        window.clearTimeout(timeoutId);
        timeoutId = window.setTimeout(function() {
            notice.classList.remove('is-visible');
        }, 4600);
    }

    window.PhotoVaultProtectionNotice = showNotice;

    if (closeButton) {
        closeButton.addEventListener('click', function() {
            notice.classList.remove('is-visible');
        });
    }

    ['contextmenu', 'dragstart'].forEach(function(eventName) {
        document.addEventListener(eventName, function(event) {
            const guard = event.target.closest('[data-pv-protection-guard]');
            if (!guard) {
                return;
            }

            event.preventDefault();
            showNotice(guard.getAttribute('data-pv-message') || 'La sauvegarde directe est desactivee sur les apercus PhotoVault.');
        });
    });
});

/** Dismissible status messages shared by public and private surfaces. */
document.addEventListener('click', function(event) {
	const closeButton = event.target.closest('[data-pv-toast-close]');
	if (!closeButton) {
		return;
	}

	const toast = closeButton.closest('[data-pv-toast]');
	if (toast) {
		toast.remove();
	}
});

/** Personal favorites backed by authenticated WordPress REST endpoints. */
document.addEventListener('DOMContentLoaded', function() {
    const config = window.photovault_ajax || {};
    if (!config.rest_url || !config.nonce) {
        return;
    }

    document.addEventListener('click', async function(event) {
        const button = event.target.closest('[data-pv-favorite]');
        if (!button || button.disabled) {
            return;
        }

        const mediaId = button.getAttribute('data-media-id');
        const isFavorite = button.getAttribute('aria-pressed') === 'true';
        const icon = button.querySelector('svg');
        button.disabled = true;

        try {
            const response = await fetch(config.rest_url.replace(/\/$/, '') + '/favorites/' + encodeURIComponent(mediaId), {
                method: isFavorite ? 'DELETE' : 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-WP-Nonce': config.nonce
                }
            });
            if (!response.ok) {
                throw new Error('favorite_request_failed');
            }

            const nextState = !isFavorite;
            const label = nextState ? 'Retirer des favoris' : 'Ajouter aux favoris';
            button.setAttribute('aria-pressed', nextState ? 'true' : 'false');
            button.setAttribute('aria-label', label);
            button.setAttribute('title', label);
            if (icon) {
                icon.setAttribute('fill', nextState ? 'currentColor' : 'none');
            }
            document.dispatchEvent(new CustomEvent('photovault:favorite-changed', { detail: { mediaId: Number(mediaId), favorite: nextState } }));
        } catch (error) {
            if (window.PhotoVaultProtectionNotice) {
                window.PhotoVaultProtectionNotice('Le favori n\'a pas pu etre mis a jour. Rechargez la page puis reessayez.');
            }
        } finally {
            button.disabled = false;
        }
    });
});

/** Full-screen gallery viewer. Preview images are loaded only when requested. */
document.addEventListener('DOMContentLoaded', function() {
    const dialog = document.getElementById('pv-gallery-lightbox');
    if (!dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    const image = dialog.querySelector('[data-pv-lightbox-image]');
    const title = dialog.querySelector('[data-pv-lightbox-title]');
    const meta = dialog.querySelector('[data-pv-lightbox-meta]');
    const count = dialog.querySelector('[data-pv-lightbox-count]');
    const detail = dialog.querySelector('[data-pv-lightbox-detail]');
    let currentIndex = 0;
    let pointerStart = null;

    function items() {
        return Array.from(document.querySelectorAll('#media-grid [data-pv-lightbox-item]'));
    }

    function render(index) {
        const collection = items();
        if (!collection.length) {
            return;
        }

        currentIndex = (index + collection.length) % collection.length;
        const item = collection[currentIndex];
        const nextTitle = item.getAttribute('data-title') || 'Oeuvre PhotoVault';
        image.removeAttribute('src');
        image.alt = nextTitle;
        image.src = item.getAttribute('data-preview-url');
        title.textContent = nextTitle;
        meta.textContent = item.getAttribute('data-meta') || '';
        detail.href = item.getAttribute('data-detail-url') || '#';
        count.textContent = String(currentIndex + 1).padStart(2, '0') + ' / ' + String(collection.length).padStart(2, '0');
    }

    function closeViewer() {
        dialog.close();
        image.removeAttribute('src');
    }

    document.addEventListener('click', function(event) {
        const opener = event.target.closest('[data-pv-lightbox-open]');
        if (opener) {
            const item = opener.closest('[data-pv-lightbox-item]');
            currentIndex = Math.max(0, items().indexOf(item));
            render(currentIndex);
            dialog.showModal();
            return;
        }

        if (event.target.closest('[data-pv-lightbox-prev]')) render(currentIndex - 1);
        if (event.target.closest('[data-pv-lightbox-next]')) render(currentIndex + 1);
        if (event.target.closest('[data-pv-lightbox-close]')) closeViewer();
        if (event.target.closest('[data-pv-lightbox-fullscreen]')) {
            if (document.fullscreenElement) document.exitFullscreen();
            else dialog.requestFullscreen().catch(function() {});
        }
    });

    dialog.addEventListener('click', function(event) {
        if (event.target === dialog) {
            closeViewer();
        }
    });

    dialog.addEventListener('keydown', function(event) {
        if (event.key === 'ArrowLeft') render(currentIndex - 1);
        if (event.key === 'ArrowRight') render(currentIndex + 1);
    });

    dialog.addEventListener('pointerdown', function(event) {
        pointerStart = { x: event.clientX, y: event.clientY };
    });
    dialog.addEventListener('pointerup', function(event) {
        if (!pointerStart) return;
        const horizontal = event.clientX - pointerStart.x;
        const vertical = event.clientY - pointerStart.y;
        pointerStart = null;
        if (Math.abs(horizontal) > 70 && Math.abs(horizontal) > Math.abs(vertical)) render(currentIndex + (horizontal < 0 ? 1 : -1));
    });
});
