/**
 * Godmind Track Archive - Audio Engine
 * Web Audio API implementation with aggressive preloading and caching
 */

(function() {
    'use strict';

    // ────────────────────────────────────────────────────────────
    // AUDIO BUFFER CACHE
    // ────────────────────────────────────────────────────────────

    class AudioBufferCache {
        constructor() {
            this.cache = new Map(); // trackId -> { buffer, state, error }
            this.loadingPromises = new Map(); // trackId -> Promise
        }

        /**
         * Get cached buffer or start loading
         * @returns {Object} { buffer, state: 'ready'|'loading'|'error', promise }
         */
        get(trackId) {
            return this.cache.get(trackId) || { state: 'none', buffer: null, error: null };
        }

        /**
         * Start loading a track (non-blocking)
         * @returns {Promise<AudioBuffer>}
         */
        async load(audioContext, trackId, url) {
            // If already loading, return existing promise
            if (this.loadingPromises.has(trackId)) {
                return this.loadingPromises.get(trackId);
            }

            // If already cached, return immediately
            const cached = this.cache.get(trackId);
            if (cached && cached.state === 'ready') {
                return Promise.resolve(cached.buffer);
            }

            // Mark as loading
            this.cache.set(trackId, { state: 'loading', buffer: null, error: null });

            const startTime = performance.now();
            console.log(`[AudioCache] Loading track ${trackId}...`);

            // Start fetch + decode
            const loadPromise = (async () => {
                try {
                    const fetchStart = performance.now();
                    const response = await fetch(url);
                    const fetchTime = performance.now() - fetchStart;

                    const arrayBuffer = await response.arrayBuffer();
                    const fetchTotalTime = performance.now() - fetchStart;

                    const decodeStart = performance.now();
                    const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
                    const decodeTime = performance.now() - decodeStart;

                    const totalTime = performance.now() - startTime;

                    console.log(`[AudioCache] ✓ Track ${trackId} ready - Fetch: ${fetchTime.toFixed(0)}ms, Decode: ${decodeTime.toFixed(0)}ms, Total: ${totalTime.toFixed(0)}ms`);

                    // Cache the buffer
                    this.cache.set(trackId, { state: 'ready', buffer: audioBuffer, error: null });
                    this.loadingPromises.delete(trackId);

                    return audioBuffer;
                } catch (error) {
                    console.error(`[AudioCache] ✗ Track ${trackId} failed:`, error);
                    this.cache.set(trackId, { state: 'error', buffer: null, error });
                    this.loadingPromises.delete(trackId);
                    throw error;
                }
            })();

            this.loadingPromises.set(trackId, loadPromise);
            return loadPromise;
        }

        /**
         * Check if buffer is ready for immediate playback
         */
        isReady(trackId) {
            const cached = this.cache.get(trackId);
            return cached && cached.state === 'ready';
        }

        /**
         * Preload multiple tracks in background
         */
        async preloadBatch(audioContext, tracks) {
            const promises = tracks.map(({ id, url }) => {
                if (!this.isReady(id)) {
                    return this.load(audioContext, id, url).catch(() => {
                        // Silently fail preloads
                    });
                }
            });

            return Promise.all(promises);
        }
    }

    // ────────────────────────────────────────────────────────────
    // AUDIO ENGINE
    // ────────────────────────────────────────────────────────────

    class AudioEngine {
        constructor() {
            this.audioContext = null;
            this.activeTracks = new Map(); // trackId -> trackState
            this.masterGainNode = null;
            this.duckingGainNode = null;
            this.duckAmount = 0.3; // Duck music to 30% when SFX plays
            this.bufferCache = new AudioBufferCache();
            this.allTracks = []; // Will be populated by UI
            this.init();
        }

        init() {
            // Create audio context on FIRST user interaction (anywhere on page)
            const initAudio = () => {
                this.ensureAudioContext();
                console.log('[AudioEngine] AudioContext initialized on first interaction');
            };

            document.addEventListener('click', initAudio, { once: true });
            document.addEventListener('keydown', initAudio, { once: true });
        }

        ensureAudioContext() {
            if (!this.audioContext) {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                this.masterGainNode = this.audioContext.createGain();
                this.duckingGainNode = this.audioContext.createGain();

                this.masterGainNode.connect(this.duckingGainNode);
                this.duckingGainNode.connect(this.audioContext.destination);

                console.log('[AudioEngine] AudioContext created and ready');
            }

            if (this.audioContext.state === 'suspended') {
                this.audioContext.resume();
            }

            return this.audioContext;
        }

        /**
         * Register all tracks for smart preloading
         */
        registerTracks(tracks) {
            this.allTracks = tracks;
        }

        /**
         * Preload tracks by type or IDs
         */
        async preloadByType(type) {
            this.ensureAudioContext();

            const tracksToPreload = this.allTracks
                .filter(t => t.type === type)
                .map(t => ({ id: t.id, url: t.url }));

            console.log(`[AudioEngine] Preloading ${tracksToPreload.length} ${type} tracks...`);

            return this.bufferCache.preloadBatch(this.audioContext, tracksToPreload);
        }

        /**
         * Preload specific track
         */
        async preload(trackId, url) {
            this.ensureAudioContext();
            return this.bufferCache.load(this.audioContext, trackId, url);
        }

        /**
         * Play track with smart caching
         */
        async play(trackId, trackData) {
            const playStart = performance.now();

            this.ensureAudioContext();

            const { url, type, volume, loop } = trackData;

            // Music exclusivity: stop other music tracks
            if (type === 'music') {
                await this.stopAllTracksOfType('music');
            }

            // If already playing, restart it
            if (this.activeTracks.has(trackId)) {
                await this.stop(trackId);
            }

            try {
                // Check if buffer is cached
                const cached = this.bufferCache.get(trackId);

                let audioBuffer;

                if (cached.state === 'ready') {
                    // Instant playback - buffer already loaded
                    audioBuffer = cached.buffer;
                    console.log(`[AudioEngine] ⚡ Instant play from cache - Track ${trackId}`);
                } else {
                    // Need to load - this will block playback
                    console.log(`[AudioEngine] ⏳ Loading before playback - Track ${trackId}`);
                    audioBuffer = await this.bufferCache.load(this.audioContext, trackId, url);
                }

                // Create source and gain
                const source = this.audioContext.createBufferSource();
                const gainNode = this.audioContext.createGain();

                source.buffer = audioBuffer;
                source.loop = loop;

                // Set volume immediately (no fade)
                const targetVolume = volume / 100;
                gainNode.gain.setValueAtTime(targetVolume, this.audioContext.currentTime);

                source.connect(gainNode);
                gainNode.connect(this.masterGainNode);

                source.start(0);

                const timeToAudio = performance.now() - playStart;
                console.log(`[AudioEngine] 🔊 Audio started - Time to playback: ${timeToAudio.toFixed(0)}ms`);

                // Store track state
                this.activeTracks.set(trackId, {
                    source,
                    gainNode,
                    audioBuffer,
                    type,
                    loop,
                    volume,
                    startTime: this.audioContext.currentTime,
                    pauseTime: 0,
                });

                // Handle SFX auto-stop
                if (type === 'sfx') {
                    source.onended = () => {
                        this.activeTracks.delete(trackId);
                        this.unduckMusic();
                    };

                    // Duck music when SFX plays
                    this.duckMusic();
                }

                // Trigger smart preloading of similar tracks
                this.smartPreload(trackData);

                return true;
            } catch (error) {
                console.error('[AudioEngine] Play error:', error);
                return false;
            }
        }

        /**
         * Smart preload: load likely next tracks based on what just played
         */
        async smartPreload(justPlayed) {
            // Preload other tracks of the same type (in background, non-blocking)
            const sametype = this.allTracks
                .filter(t => t.type === justPlayed.type && t.id !== justPlayed.id)
                .slice(0, 3) // Preload up to 3 similar tracks
                .map(t => ({ id: t.id, url: t.url }));

            if (sametype.length > 0) {
                this.bufferCache.preloadBatch(this.audioContext, sametype).catch(() => {
                    // Silent fail for background preloads
                });
            }
        }

        async stop(trackId) {
            const trackState = this.activeTracks.get(trackId);
            if (!trackState) return;

            const { source, type } = trackState;

            try {
                source.stop();
            } catch (e) {
                // Already stopped
            }
            this.activeTracks.delete(trackId);

            if (type === 'sfx') {
                this.unduckMusic();
            }
        }

        async stopAllTracksOfType(type) {
            const promises = [];
            for (const [trackId, trackState] of this.activeTracks.entries()) {
                if (trackState.type === type) {
                    promises.push(this.stop(trackId));
                }
            }
            await Promise.all(promises);
        }

        setVolume(trackId, volume) {
            const trackState = this.activeTracks.get(trackId);
            if (!trackState) return;

            const targetVolume = volume / 100;
            trackState.gainNode.gain.linearRampToValueAtTime(
                targetVolume,
                this.audioContext.currentTime + 0.1
            );
            trackState.volume = volume;
        }

        setLoop(trackId, loop) {
            const trackState = this.activeTracks.get(trackId);
            if (trackState && trackState.source) {
                trackState.source.loop = loop;
                trackState.loop = loop;
            }
        }

        isPlaying(trackId) {
            return this.activeTracks.has(trackId);
        }

        getProgress(trackId) {
            const trackState = this.activeTracks.get(trackId);
            if (!trackState) return { current: 0, duration: 0 };

            const elapsed = this.audioContext.currentTime - trackState.startTime;
            const duration = trackState.audioBuffer.duration;

            return {
                current: Math.min(elapsed, duration),
                duration,
            };
        }

        duckMusic() {
            if (!this.duckingGainNode) return;

            this.duckingGainNode.gain.linearRampToValueAtTime(
                this.duckAmount,
                this.audioContext.currentTime + 0.2
            );
        }

        unduckMusic() {
            if (!this.duckingGainNode) return;

            // Only unduck if no SFX are playing
            let hasSFX = false;
            for (const trackState of this.activeTracks.values()) {
                if (trackState.type === 'sfx') {
                    hasSFX = true;
                    break;
                }
            }

            if (!hasSFX) {
                this.duckingGainNode.gain.linearRampToValueAtTime(
                    1.0,
                    this.audioContext.currentTime + 0.2
                );
            }
        }

        getDuration(audioBuffer) {
            return audioBuffer ? audioBuffer.duration : 0;
        }

        /**
         * Get cached buffer (for duration display without loading)
         */
        getCachedBuffer(trackId) {
            const cached = this.bufferCache.get(trackId);
            return cached.state === 'ready' ? cached.buffer : null;
        }

        /**
         * Check if track is ready for instant playback
         */
        isBufferReady(trackId) {
            return this.bufferCache.isReady(trackId);
        }
    }

    // ────────────────────────────────────────────────────────────
    // STATE MANAGEMENT
    // ────────────────────────────────────────────────────────────

    class TrackArchiveState {
        constructor() {
            this.favorites = this.loadFavorites();
            this.recentlyPlayed = this.loadRecentlyPlayed();
            this.maxRecent = 8;
        }

        loadFavorites() {
            try {
                const stored = localStorage.getItem('godmind_track_favorites');
                return stored ? JSON.parse(stored) : [];
            } catch (e) {
                return [];
            }
        }

        saveFavorites() {
            try {
                localStorage.setItem('godmind_track_favorites', JSON.stringify(this.favorites));
            } catch (e) {
                console.error('Failed to save favorites:', e);
            }
        }

        toggleFavorite(trackId) {
            const index = this.favorites.indexOf(trackId);
            if (index > -1) {
                this.favorites.splice(index, 1);
            } else {
                this.favorites.push(trackId);
            }
            this.saveFavorites();
            return this.isFavorite(trackId);
        }

        isFavorite(trackId) {
            return this.favorites.includes(trackId);
        }

        loadRecentlyPlayed() {
            try {
                const stored = localStorage.getItem('godmind_recently_played');
                return stored ? JSON.parse(stored) : [];
            } catch (e) {
                return [];
            }
        }

        saveRecentlyPlayed() {
            try {
                localStorage.setItem('godmind_recently_played', JSON.stringify(this.recentlyPlayed));
            } catch (e) {
                console.error('Failed to save recently played:', e);
            }
        }

        addToRecentlyPlayed(trackId, trackData) {
            // Remove if already in list
            this.recentlyPlayed = this.recentlyPlayed.filter(item => item.id !== trackId);

            // Add to front
            this.recentlyPlayed.unshift({
                id: trackId,
                title: trackData.title,
                type: trackData.type,
                timestamp: Date.now(),
            });

            // Limit to max
            if (this.recentlyPlayed.length > this.maxRecent) {
                this.recentlyPlayed = this.recentlyPlayed.slice(0, this.maxRecent);
            }

            this.saveRecentlyPlayed();
        }

        getRecentlyPlayed() {
            return this.recentlyPlayed;
        }
    }

    // ────────────────────────────────────────────────────────────
    // UI CONTROLLER
    // ────────────────────────────────────────────────────────────

    class TrackArchiveUI {
        constructor(audioEngine, state) {
            this.audioEngine = audioEngine;
            this.state = state;
            this.trackCards = new Map();
            this.currentFilter = 'all';
            this.searchQuery = '';
            this.favoritesOnly = false;
            this.progressIntervals = new Map();

            this.init();
        }

        init() {
            this.cacheElements();
            this.bindEvents();
            this.initializeCards();
            this.updateRecentlyPlayedUI();
            this.startSmartPreloading();
        }

        cacheElements() {
            this.trackGrid = document.querySelector('.track-grid');
            this.searchInput = document.getElementById('track-search');
            this.filterButtons = document.querySelectorAll('.filter-btn');
            this.favoritesToggle = document.getElementById('favorites-toggle');
            this.recentlyPlayedList = document.querySelector('.recently-played-list');
        }

        bindEvents() {
            // Search
            if (this.searchInput) {
                this.searchInput.addEventListener('input', (e) => {
                    this.searchQuery = e.target.value.toLowerCase();
                    this.applyFilters();
                });
            }

            // Type filters
            this.filterButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    this.filterButtons.forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');
                    this.currentFilter = e.target.dataset.filter;
                    this.applyFilters();

                    // Preload tracks of selected type
                    if (this.currentFilter !== 'all') {
                        this.audioEngine.preloadByType(this.currentFilter);
                    }
                });
            });

            // Favorites toggle
            if (this.favoritesToggle) {
                this.favoritesToggle.addEventListener('change', (e) => {
                    this.favoritesOnly = e.target.checked;
                    this.applyFilters();
                });
            }
        }

        initializeCards() {
            const cards = document.querySelectorAll('.track-card');
            const allTracks = [];

            cards.forEach(card => {
                const trackId = card.dataset.trackId;
                const trackData = {
                    id: trackId,
                    title: card.querySelector('.track-title').textContent,
                    url: card.dataset.src,
                    type: card.dataset.trackType,
                    volume: parseInt(card.dataset.defaultVolume, 10),
                    loop: card.dataset.loop === 'true',
                    purpose: card.dataset.purpose,
                    scene: card.dataset.scene,
                };

                allTracks.push(trackData);

                this.trackCards.set(trackId, { card, trackData });
                this.bindCardEvents(card, trackId, trackData);
                this.updateFavoriteUI(card, trackId);
                this.loadDuration(card, trackData);
            });

            // Register all tracks with audio engine for smart preloading
            this.audioEngine.registerTracks(allTracks);
        }

        /**
         * Start smart preloading strategy
         */
        async startSmartPreloading() {
            // Wait for first interaction to create AudioContext
            const waitForContext = () => {
                return new Promise(resolve => {
                    const check = setInterval(() => {
                        if (this.audioEngine.audioContext) {
                            clearInterval(check);
                            resolve();
                        }
                    }, 100);
                });
            };

            await waitForContext();

            console.log('[Preloader] Starting smart preload strategy...');

            // Strategy 1: Preload recently played tracks
            const recentIds = this.state.getRecentlyPlayed().map(r => r.id).slice(0, 3);
            for (const trackId of recentIds) {
                const trackInfo = this.trackCards.get(trackId);
                if (trackInfo) {
                    this.audioEngine.preload(trackInfo.trackData.id, trackInfo.trackData.url);
                }
            }

            // Strategy 2: Preload favorites
            const favoriteIds = this.state.favorites.slice(0, 3);
            for (const trackId of favoriteIds) {
                const trackInfo = this.trackCards.get(trackId);
                if (trackInfo) {
                    this.audioEngine.preload(trackInfo.trackData.id, trackInfo.trackData.url);
                }
            }

            // Strategy 3: Preload ambient tracks (likely to be looped)
            setTimeout(() => {
                this.audioEngine.preloadByType('ambient');
            }, 2000);
        }

        bindCardEvents(card, trackId, trackData) {
            const playBtn = card.querySelector('.track-btn-play');
            const pauseBtn = card.querySelector('.track-btn-pause');
            const restartBtn = card.querySelector('.track-btn-restart');
            const loopBtn = card.querySelector('.track-btn-loop');
            const favoriteBtn = card.querySelector('.track-btn-favorite');
            const volumeSlider = card.querySelector('.volume-slider');
            const volumeValue = card.querySelector('.volume-value');

            // Preload on hover (aggressive strategy)
            card.addEventListener('mouseenter', () => {
                if (!this.audioEngine.isBufferReady(trackId)) {
                    this.audioEngine.preload(trackId, trackData.url);
                }
            }, { once: true });

            // Play
            playBtn?.addEventListener('click', async () => {
                const success = await this.audioEngine.play(trackId, trackData);
                if (success) {
                    this.updatePlayingUI(card, true);
                    this.state.addToRecentlyPlayed(trackId, trackData);
                    this.updateRecentlyPlayedUI();
                    this.startProgressTracking(card, trackId);
                }
            });

            // Pause
            pauseBtn?.addEventListener('click', async () => {
                await this.audioEngine.stop(trackId);
                this.updatePlayingUI(card, false);
                this.stopProgressTracking(trackId);
            });

            // Restart
            restartBtn?.addEventListener('click', async () => {
                const wasPlaying = this.audioEngine.isPlaying(trackId);
                await this.audioEngine.stop(trackId);

                if (wasPlaying) {
                    const success = await this.audioEngine.play(trackId, trackData);
                    if (success) {
                        this.updatePlayingUI(card, true);
                        this.startProgressTracking(card, trackId);
                    }
                }
            });

            // Loop
            loopBtn?.addEventListener('click', () => {
                if (trackData.type === 'ambient') return; // Locked for ambient

                const newLoop = !loopBtn.classList.contains('active');
                loopBtn.classList.toggle('active');
                trackData.loop = newLoop;
                this.audioEngine.setLoop(trackId, newLoop);
            });

            // Favorite
            favoriteBtn?.addEventListener('click', () => {
                this.state.toggleFavorite(trackId);
                this.updateFavoriteUI(card, trackId);

                // Preload favorited tracks
                if (this.state.isFavorite(trackId)) {
                    this.audioEngine.preload(trackId, trackData.url);
                }
            });

            // Volume
            volumeSlider?.addEventListener('input', (e) => {
                const volume = parseInt(e.target.value, 10);
                trackData.volume = volume;
                volumeValue.textContent = `${volume}%`;
                this.audioEngine.setVolume(trackId, volume);
            });
        }

        updatePlayingUI(card, isPlaying) {
            const playBtn = card.querySelector('.track-btn-play');
            const pauseBtn = card.querySelector('.track-btn-pause');

            if (isPlaying) {
                playBtn?.classList.add('hidden');
                pauseBtn?.classList.remove('hidden');
                card.classList.add('is-playing');
            } else {
                playBtn?.classList.remove('hidden');
                pauseBtn?.classList.add('hidden');
                card.classList.remove('is-playing');
            }
        }

        updateFavoriteUI(card, trackId) {
            const favoriteBtn = card.querySelector('.track-btn-favorite');
            const icon = favoriteBtn?.querySelector('.icon-favorite');

            if (this.state.isFavorite(trackId)) {
                favoriteBtn?.classList.add('active');
                if (icon) icon.textContent = '★';
            } else {
                favoriteBtn?.classList.remove('active');
                if (icon) icon.textContent = '☆';
            }
        }

        async loadDuration(card, trackData) {
            // Try to get from cache first (non-blocking)
            const cached = this.audioEngine.getCachedBuffer(trackData.id);
            if (cached) {
                this.displayDuration(card, cached.duration);
                return;
            }

            // Load in background and update when ready
            try {
                await this.audioEngine.ensureAudioContext();
                const audioBuffer = await this.audioEngine.preload(trackData.id, trackData.url);
                this.displayDuration(card, audioBuffer.duration);
            } catch (e) {
                console.error('Failed to load duration:', e);
            }
        }

        displayDuration(card, duration) {
            const durationEl = card.querySelector('.duration-value');
            if (durationEl) {
                durationEl.textContent = this.formatTime(duration);
            }
            const totalTimeEl = card.querySelector('.time-total');
            if (totalTimeEl) {
                totalTimeEl.textContent = this.formatTime(duration);
            }
        }

        startProgressTracking(card, trackId) {
            this.stopProgressTracking(trackId);

            const interval = setInterval(() => {
                const progress = this.audioEngine.getProgress(trackId);
                this.updateProgressUI(card, progress);

                // Stop tracking if ended
                if (!this.audioEngine.isPlaying(trackId)) {
                    this.stopProgressTracking(trackId);
                    this.updatePlayingUI(card, false);
                }
            }, 100);

            this.progressIntervals.set(trackId, interval);
        }

        stopProgressTracking(trackId) {
            const interval = this.progressIntervals.get(trackId);
            if (interval) {
                clearInterval(interval);
                this.progressIntervals.delete(trackId);
            }
        }

        updateProgressUI(card, progress) {
            const progressFill = card.querySelector('.progress-fill');
            const timeCurrent = card.querySelector('.time-current');

            if (progressFill && progress.duration > 0) {
                const percent = (progress.current / progress.duration) * 100;
                progressFill.style.width = `${percent}%`;
            }

            if (timeCurrent) {
                timeCurrent.textContent = this.formatTime(progress.current);
            }
        }

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }

        applyFilters() {
            this.trackCards.forEach(({ card, trackData }, trackId) => {
                let show = true;

                // Type filter
                if (this.currentFilter !== 'all' && trackData.type !== this.currentFilter) {
                    show = false;
                }

                // Search filter
                if (this.searchQuery) {
                    const searchableText = [
                        trackData.title,
                        trackData.purpose,
                        trackData.scene,
                    ].join(' ').toLowerCase();

                    if (!searchableText.includes(this.searchQuery)) {
                        show = false;
                    }
                }

                // Favorites filter
                if (this.favoritesOnly && !this.state.isFavorite(trackId)) {
                    show = false;
                }

                card.style.display = show ? '' : 'none';
            });
        }

        updateRecentlyPlayedUI() {
            if (!this.recentlyPlayedList) return;

            const recent = this.state.getRecentlyPlayed();

            if (recent.length === 0) {
                this.recentlyPlayedList.innerHTML = '<p class="recently-played-empty">No tracks played yet</p>';
                return;
            }

            this.recentlyPlayedList.innerHTML = recent.map(item => `
                <div class="recent-track-item" data-track-id="${item.id}">
                    <span class="recent-track-title">${this.escapeHtml(item.title)}</span>
                    <span class="recent-track-type track-type-${item.type}">${item.type}</span>
                </div>
            `).join('');

            // Make recent items clickable
            this.recentlyPlayedList.querySelectorAll('.recent-track-item').forEach(item => {
                item.addEventListener('click', () => {
                    const trackId = item.dataset.trackId;
                    const trackCard = this.trackCards.get(trackId);
                    if (trackCard) {
                        trackCard.card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        trackCard.card.classList.add('highlight');
                        setTimeout(() => trackCard.card.classList.remove('highlight'), 2000);
                    }
                });
            });
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    // ────────────────────────────────────────────────────────────
    // INITIALIZATION
    // ────────────────────────────────────────────────────────────

    function init() {
        const audioEngine = new AudioEngine();
        const state = new TrackArchiveState();
        const ui = new TrackArchiveUI(audioEngine, state);

        // Expose to window for debugging
        if (window.godmindDebug) {
            window.godmindTrackArchive = { audioEngine, state, ui };
        }
    }

    // Wait for DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
