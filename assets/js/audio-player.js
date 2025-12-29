/**
 * Custom Audio Player Controller
 *
 * Vanilla JavaScript for controlling the custom audio player.
 * No jQuery, no React, no auto-play, no state persistence.
 *
 * This is a GM scene control interface, not a media player.
 */

(function () {
    'use strict';

    /**
     * Initialize all audio players on the page
     */
    function initAudioPlayers() {
        const players = document.querySelectorAll('.godmind-audio-player');

        players.forEach((player) => {
            initSinglePlayer(player);
        });
    }

    /**
     * Initialize a single audio player
     *
     * @param {HTMLElement} playerEl - The player container element
     */
    function initSinglePlayer(playerEl) {
        // Get DOM elements
        const audioEl = playerEl.querySelector('.audio-player__element');
        const playPauseBtn = playerEl.querySelector('.audio-player__play-pause');
        const volumeSlider = playerEl.querySelector('.audio-player__volume-slider');
        const volumeValue = playerEl.querySelector('.audio-player__volume-value');

        if (!audioEl || !playPauseBtn || !volumeSlider || !volumeValue) {
            console.warn('Audio player missing required elements', playerEl);
            return;
        }

        // Set initial volume from slider value
        const initialVolume = parseInt(volumeSlider.value, 10);
        audioEl.volume = initialVolume / 100;

        // Play/Pause handler
        playPauseBtn.addEventListener('click', function () {
            if (audioEl.paused) {
                // Play audio
                const playPromise = audioEl.play();

                // Handle play promise (required for some browsers)
                if (playPromise !== undefined) {
                    playPromise
                        .then(() => {
                            // Playback started successfully
                            updatePlayPauseButton(playPauseBtn, 'playing');
                        })
                        .catch((error) => {
                            // Auto-play was prevented or other error
                            console.error('Audio playback failed:', error);
                            updatePlayPauseButton(playPauseBtn, 'paused');
                        });
                }
            } else {
                // Pause audio
                audioEl.pause();
                updatePlayPauseButton(playPauseBtn, 'paused');
            }
        });

        // Volume slider handler
        volumeSlider.addEventListener('input', function () {
            const volume = parseInt(this.value, 10);

            // Update audio element volume (0-1 range)
            audioEl.volume = volume / 100;

            // Update volume display
            volumeValue.textContent = volume + '%';
        });

        // Audio ended handler (reset button state if not looping)
        audioEl.addEventListener('ended', function () {
            // Button will auto-reset since audio is paused
            updatePlayPauseButton(playPauseBtn, 'paused');
        });

        // Audio error handler
        audioEl.addEventListener('error', function (e) {
            console.error('Audio error:', e);
            updatePlayPauseButton(playPauseBtn, 'paused');

            // Optionally show error to user
            const errorMsg = getAudioErrorMessage(audioEl.error);
            console.error('Audio player error:', errorMsg);
        });
    }

    /**
     * Update play/pause button state
     *
     * @param {HTMLElement} btn - The play/pause button
     * @param {string} state - 'playing' or 'paused'
     */
    function updatePlayPauseButton(btn, state) {
        btn.setAttribute('data-state', state);
        btn.setAttribute('aria-label', state === 'playing' ? 'Pause' : 'Play');
    }

    /**
     * Get human-readable error message from MediaError
     *
     * @param {MediaError|null} error - The MediaError object
     * @return {string} Error message
     */
    function getAudioErrorMessage(error) {
        if (!error) {
            return 'Unknown error';
        }

        switch (error.code) {
            case error.MEDIA_ERR_ABORTED:
                return 'Audio loading aborted';
            case error.MEDIA_ERR_NETWORK:
                return 'Network error while loading audio';
            case error.MEDIA_ERR_DECODE:
                return 'Audio decoding failed';
            case error.MEDIA_ERR_SRC_NOT_SUPPORTED:
                return 'Audio format not supported';
            default:
                return 'Unknown audio error';
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAudioPlayers);
    } else {
        // DOM already loaded
        initAudioPlayers();
    }
})();
