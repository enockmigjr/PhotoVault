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

    document.querySelectorAll('[data-pv-protection-guard]').forEach(function(guard) {
        const message = guard.getAttribute('data-pv-message') || 'La sauvegarde directe est desactivee sur les apercus PhotoVault.';

        ['contextmenu', 'dragstart'].forEach(function(eventName) {
            guard.addEventListener(eventName, function(event) {
                event.preventDefault();
                showNotice(message);
            });
        });

        guard.addEventListener('click', function(event) {
            if (event.detail > 1) {
                event.preventDefault();
                showNotice(message);
            }
        });
    });
});

/** Personal favorites backed by authenticated WordPress REST endpoints. */
document.addEventListener('DOMContentLoaded', function() {
    const config = window.photovault_ajax || {};
    const buttons = document.querySelectorAll('[data-pv-favorite]');

    if (!buttons.length || !config.rest_url || !config.nonce) {
        return;
    }

    buttons.forEach(function(button) {
        button.addEventListener('click', async function() {
            if (button.disabled) {
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
});
