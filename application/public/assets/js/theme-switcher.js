/**
 * Theme Switcher Logic
 */

'use strict';

(() => {
    const getStoredTheme = () => localStorage.getItem('theme');
    const setStoredTheme = theme => localStorage.setItem('theme', theme);

    const getPreferredTheme = () => {
        const storedTheme = getStoredTheme();
        if (storedTheme) {
            return storedTheme;
        }
        // Default to dark if no preference (as per previous instructions) or use system
        return 'system';
    };

    const setTheme = (theme) => {
        const html = document.documentElement;
        let actualTheme = theme;

        if (theme === 'system') {
            actualTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        html.setAttribute('data-bs-theme', actualTheme);

        if (actualTheme === 'dark') {
            html.classList.add('dark-style');
            html.classList.remove('light-style');
        } else {
            html.classList.add('light-style');
            html.classList.remove('dark-style');
        }

        updateUI(theme);
    };

    const updateUI = (theme) => {
        const themeIcons = {
            light: 'bx-sun',
            dark: 'bx-moon',
            system: 'bx-desktop'
        };

        const activeItemText = document.querySelector('.theme-switcher-text');
        const activeIcon = document.querySelector('.theme-switcher-icon');

        if (activeIcon) {
            // Remove all possible icons
            Object.values(themeIcons).forEach(iconClass => activeIcon.classList.remove(iconClass));
            // Add current icon
            activeIcon.classList.add(themeIcons[theme]);
        }

        // Highlight active dropdown item
        document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
            element.classList.remove('active');
            if (element.getAttribute('data-bs-theme-value') === theme) {
                element.classList.add('active');
            }
        });
    };

    // Initial apply
    setTheme(getPreferredTheme());

    // Listen for system changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const storedTheme = getStoredTheme();
        if (storedTheme === 'system') {
            setTheme('system');
        }
    });

    // Listen for UI interactions
    window.addEventListener('DOMContentLoaded', () => {
        updateUI(getPreferredTheme());

        document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const theme = toggle.getAttribute('data-bs-theme-value');
                setStoredTheme(theme);
                setTheme(theme);
            });
        });
    });
})();
