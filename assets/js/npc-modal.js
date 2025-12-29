/**
 * NPC Modal Functionality
 * Handles opening/closing NPC detail modals
 */

(function () {
  'use strict';

  // Initialize when DOM is ready
  document.addEventListener('DOMContentLoaded', function () {
    initNPCModals();
  });

  function initNPCModals() {
    // Get all NPC cards
    const npcCards = document.querySelectorAll('.gm-npc-card');

    npcCards.forEach((card) => {
      // Make card clickable
      card.addEventListener('click', function () {
        openNPCModal(card);
      });

      // Handle keyboard activation (Enter/Space)
      card.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          openNPCModal(card);
        }
      });
    });

    // Close modal when clicking outside or on close button
    document.addEventListener('click', function (e) {
      // Close if clicking on the overlay
      if (e.target.classList.contains('gm-npc-modal')) {
        closeNPCModal(e.target);
      }

      // Close if clicking the close button or anything inside it (SVG)
      const closeButton = e.target.closest('.gm-npc-modal__close');
      if (closeButton) {
        closeNPCModal(closeButton.closest('.gm-npc-modal'));
      }

      // Pop out if clicking the popout button
      const popoutButton = e.target.closest('.gm-npc-modal__popout');
      if (popoutButton) {
        popoutNPCModal(popoutButton.closest('.gm-npc-modal'));
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        const openModal = document.querySelector('.gm-npc-modal.is-open');
        if (openModal) {
          closeNPCModal(openModal);
        }
      }
    });
  }

  function openNPCModal(card) {
    const npcId = card.dataset.npcId;
    const modal = document.getElementById(`npc-modal-${npcId}`);

    if (!modal) return;

    modal.classList.add('is-open');
    document.body.classList.add('npc-modal-open');

    // Trap focus within modal
    const focusableElements = modal.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    if (focusableElements.length) {
      focusableElements[0].focus();
    }
  }

  function closeNPCModal(modal) {
    if (!modal) return;

    // Prevent multiple close attempts
    if (modal.classList.contains('is-closing')) return;

    // Add closing class to trigger close animation
    modal.classList.add('is-closing');

    // Remove body class immediately to allow scrolling
    document.body.classList.remove('npc-modal-open');

    // Wait for animation to complete before hiding modal
    setTimeout(() => {
      modal.classList.remove('is-open');
      modal.classList.remove('is-closing');
    }, 300); // Match the animation duration in CSS
  }

  function popoutNPCModal(modal) {
    if (!modal) return;

    // Get the modal content
    const modalBody = modal.querySelector('.gm-npc-modal__body');
    const modalTitle = modal.querySelector('.gm-npc-modal__title');

    if (!modalBody) return;

    // Clone the modal body to get all the HTML
    const contentClone = modalBody.cloneNode(true);

    // Build the HTML for the new window
    const popoutHTML = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${modalTitle ? modalTitle.textContent : 'NPC Details'}</title>
    <style>
        body {
            margin: 0;
            padding: 2rem;
            background: #0a0e1a;
            color: #e2e8f0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
            line-height: 1.6;
        }
        ${document.querySelector('link[href*="npc-modal.css"]') ? '@import url("' + document.querySelector('link[href*="npc-modal.css"]').href + '");' : ''}
        .gm-npc-modal__body {
            max-width: 1000px;
            margin: 0 auto;
        }
        @media print {
            body { background: white; color: black; }
        }
    </style>
</head>
<body>
    ${contentClone.outerHTML}
</body>
</html>
    `;

    // Open new window
    const popoutWindow = window.open('', '_blank', 'width=1000,height=800,scrollbars=yes,resizable=yes');

    if (popoutWindow) {
      popoutWindow.document.write(popoutHTML);
      popoutWindow.document.close();

      // Close the modal after opening the popout
      closeNPCModal(modal);
    }
  }
})();
