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
})();
