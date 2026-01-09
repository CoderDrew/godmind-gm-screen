/**
 * NPC Archive Interaction
 * Handles showing NPC details when clicking list items
 */

(function () {
	'use strict';

	// Wait for DOM to be ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	function init() {
		const listItems = document.querySelectorAll('.gm-npc-list-item');
		const placeholder = document.querySelector('.gm-npc-archive__placeholder');
		const listContainer = document.querySelector('.gm-npc-archive__list');
		const detailsContainer = document.querySelector('.gm-npc-archive__details');

		if (!listItems.length) {
			return;
		}

		// Add click handlers to list items
		listItems.forEach((item) => {
			item.addEventListener('click', () => {
				const npcId = item.dataset.npcId;
				showNpcDetail(npcId);

				// Update active state
				listItems.forEach((i) => i.classList.remove('is-active'));
				item.classList.add('is-active');
			});

			// Also handle button clicks
			const button = item.querySelector('.gm-npc-list-item__button');
			if (button) {
				button.addEventListener('click', (e) => {
					e.stopPropagation(); // Prevent double trigger
					item.click();
				});
			}

			// Keyboard navigation
			item.addEventListener('keydown', (e) => {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					item.click();
				}
			});
		});

		// Make list items focusable
		listItems.forEach((item) => {
			item.setAttribute('tabindex', '0');
			item.setAttribute('role', 'button');
		});

		// Setup scroll fade indicators
		if (listContainer) {
			setupScrollIndicators(listContainer);
		}
		if (detailsContainer) {
			setupScrollIndicators(detailsContainer);
		}
	}

	function setupScrollIndicators(container) {
		function updateScrollIndicators() {
			const scrollTop = container.scrollTop;
			const scrollHeight = container.scrollHeight;
			const clientHeight = container.clientHeight;
			const scrollBottom = scrollHeight - scrollTop - clientHeight;

			// Show top fade if scrolled down more than 10px
			if (scrollTop > 10) {
				container.classList.add('has-scroll-top');
			} else {
				container.classList.remove('has-scroll-top');
			}

			// Show bottom fade if can scroll down more than 10px
			if (scrollBottom > 10) {
				container.classList.add('has-scroll-bottom');
			} else {
				container.classList.remove('has-scroll-bottom');
			}
		}

		// Update on scroll
		container.addEventListener('scroll', updateScrollIndicators);

		// Update on load and resize
		updateScrollIndicators();
		window.addEventListener('resize', updateScrollIndicators);

		// Use ResizeObserver for content changes
		if ('ResizeObserver' in window) {
			const resizeObserver = new ResizeObserver(updateScrollIndicators);
			resizeObserver.observe(container);
		}
	}

	function showNpcDetail(npcId) {
		const detailsContainer = document.querySelector('.gm-npc-archive__details');
		const placeholder = detailsContainer.querySelector('.gm-npc-archive__placeholder');
		const allDetails = detailsContainer.querySelectorAll('.gm-npc-detail');
		const targetDetail = document.getElementById(`npc-detail-${npcId}`);

		if (!targetDetail) {
			return;
		}

		// Hide placeholder
		if (placeholder) {
			placeholder.style.display = 'none';
		}

		// Hide all details
		allDetails.forEach((detail) => {
			detail.style.display = 'none';
		});

		// Show target detail
		targetDetail.style.display = 'block';

		// Scroll details container to top
		detailsContainer.scrollTop = 0;
	}
})();
