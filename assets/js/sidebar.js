/**
 * Sidebar Navigation Toggle
 * Handles opening/closing the sidebar navigation panel
 */

(function () {
	'use strict';

	// Wait for DOM to be ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	// Helper functions for arrow icons
	function getRightArrowIcon() {
		return `
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
				<path d="M5 3h14s2 0 2 2v14s0 2 -2 2H5s-2 0 -2 -2V5s0 -2 2 -2" stroke-width="2"></path>
				<path d="M9 3v18" stroke-width="2"></path>
				<path d="m14 9 3 3 -3 3" stroke-width="2"></path>
			</svg>
		`;
	}

	function getLeftArrowIcon() {
		return `
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
				<path d="M5 3h14s2 0 2 2v14s0 2 -2 2H5s-2 0 -2 -2V5s0 -2 2 -2" stroke-width="2"></path>
				<path d="M9 3v18" stroke-width="2"></path>
				<path d="m16 15 -3 -3 3 -3" stroke-width="2"></path>
			</svg>
		`;
	}

	function init() {
		const sidebar = document.querySelector('.gm-sidebar');
		const body = document.body;

		if (!sidebar) {
			return;
		}

		// Create and inject header toggle button if it doesn't exist
		let headerToggle = document.querySelector('.gm-header .gm-sidebar-toggle');
		if (!headerToggle) {
			const header = document.querySelector('.gm-header');
			if (header) {
				const firstGroup = header.querySelector('.wp-block-group');
				if (firstGroup) {
					headerToggle = document.createElement('button');
					headerToggle.className = 'gm-sidebar-toggle';
					headerToggle.setAttribute('aria-label', 'Open sidebar');
					headerToggle.innerHTML = getRightArrowIcon();
					firstGroup.insertBefore(headerToggle, firstGroup.firstChild);
				}
			}
		}

		// Get all toggle buttons (header + sidebar)
		const toggleButtons = document.querySelectorAll('.gm-sidebar-toggle, .gm-sidebar__toggle');

		if (toggleButtons.length === 0) {
			console.warn('No sidebar toggle buttons found');
		}

		// Create overlay for mobile
		const overlay = document.createElement('div');
		overlay.className = 'gm-sidebar-overlay';
		body.appendChild(overlay);

		// Initialize sidebar as closed by default
		// Check localStorage for saved state
		const savedState = localStorage.getItem('gm-sidebar-state');

		// Start closed to prevent flash
		sidebar.classList.add('is-collapsed');
		sidebar.setAttribute('aria-hidden', 'true');

		if (savedState === 'open') {
			// Small delay to allow CSS to initialize
			setTimeout(function() {
				openSidebar();
			}, 50);
		}

		// Toggle button click handlers
		toggleButtons.forEach(function (button) {
			button.addEventListener('click', function (e) {
				e.preventDefault();
				toggleSidebar();
			});
		});

		// Overlay click handler (mobile)
		overlay.addEventListener('click', function () {
			closeSidebar();
		});

		// Keyboard shortcut: Ctrl/Cmd + B to toggle sidebar
		document.addEventListener('keydown', function (e) {
			if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
				e.preventDefault();
				toggleSidebar();
			}
		});

		// Escape key to close sidebar
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !sidebar.classList.contains('is-collapsed')) {
				closeSidebar();
			}
		});

		function toggleSidebar() {
			if (sidebar.classList.contains('is-collapsed')) {
				openSidebar();
			} else {
				closeSidebar();
			}
		}

		function openSidebar() {
			sidebar.classList.remove('is-collapsed');
			body.classList.add('sidebar-open');
			overlay.classList.add('is-visible');

			// Update header toggle button icon and label
			const headerToggle = document.querySelector('.gm-header .gm-sidebar-toggle');
			if (headerToggle) {
				headerToggle.innerHTML = getLeftArrowIcon();
				headerToggle.setAttribute('aria-label', 'Close sidebar');
			}

			// Save state
			localStorage.setItem('gm-sidebar-state', 'open');

			// Update aria attributes
			sidebar.setAttribute('aria-hidden', 'false');
		}

		function closeSidebar() {
			sidebar.classList.add('is-collapsed');
			body.classList.remove('sidebar-open');
			overlay.classList.remove('is-visible');

			// Update header toggle button icon and label
			const headerToggle = document.querySelector('.gm-header .gm-sidebar-toggle');
			if (headerToggle) {
				headerToggle.innerHTML = getRightArrowIcon();
				headerToggle.setAttribute('aria-label', 'Open sidebar');
			}

			// Save state
			localStorage.setItem('gm-sidebar-state', 'closed');

			// Update aria attributes
			sidebar.setAttribute('aria-hidden', 'true');
		}

		// Handle window resize
		let resizeTimer;
		window.addEventListener('resize', function () {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function () {
				// On desktop, ensure content offset is correct
				if (window.innerWidth >= 1024) {
					if (!sidebar.classList.contains('is-collapsed')) {
						body.classList.add('sidebar-open');
					}
				} else {
					// On mobile, remove content offset
					if (sidebar.classList.contains('is-collapsed')) {
						body.classList.remove('sidebar-open');
					}
				}
			}, 250);
		});
	}
})();
