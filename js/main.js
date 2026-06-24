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