/**
 * Read Aloud Block - Collapse/Expand Functionality
 */

document.addEventListener('DOMContentLoaded', function () {
	const readAloudBlocks = document.querySelectorAll('.read-aloud');

	readAloudBlocks.forEach((block) => {
		const header = block.querySelector('.read-aloud__header');
		if (!header) return;

		header.addEventListener('click', function () {
			const isOpen = block.classList.contains('is-open');

			// Toggle state
			block.classList.toggle('is-open');

			// Update aria-expanded
			header.setAttribute('aria-expanded', !isOpen);
		});
	});
});
