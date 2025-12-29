<?php
/**
 * Audio Player Template
 *
 * Displays a custom audio player for Scene pages.
 * This is a GM control interface, not a media player.
 *
 * @var array $args {
 *     @type string $url        Audio file URL
 *     @type string $title      Track title
 *     @type bool   $loop       Whether audio should loop
 *     @type int    $volume     Initial volume (0-100)
 *     @type string $track_type Track type (music|ambient|sfx)
 * }
 */

if (!isset($args) || !isset($args['url'])) {
    return;
}

$url = $args['url'];
$title = $args['title'];
$loop = $args['loop'];
$volume = $args['volume'];
$track_type = $args['track_type'];
$track_purpose = isset($args['track_purpose']) ? $args['track_purpose'] : '';
?>

<div class="godmind-audio-player" data-track-type="<?php echo $track_type; ?>">

    <!-- Track Info -->
    <div class="audio-player__info">
        <div class="audio-player__info-main">
            <h3 class="audio-player__title"><?php echo $title; ?></h3>
            <?php if ($track_purpose) : ?>
                <p class="audio-player__purpose"><?php echo $track_purpose; ?></p>
            <?php endif; ?>
        </div>
        <div class="audio-player__metadata">
            <span class="audio-player__type"><?php echo ucfirst($track_type); ?></span>
            <?php if ($loop) : ?>
                <span class="audio-player__badge audio-player__badge--loop" aria-label="Looping enabled" title="Looping enabled">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 1l4 4-4 4"/>
                        <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                        <path d="M7 23l-4-4 4-4"/>
                        <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                    </svg>
                    Loop
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Controls -->
    <div class="audio-player__controls">

        <!-- Play/Pause Button -->
        <button
            class="audio-player__play-pause"
            type="button"
            aria-label="Play"
            data-state="paused"
        >
            <!-- Play Icon -->
            <svg class="audio-player__icon audio-player__icon--play" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 5v14l11-7z"/>
            </svg>
            <!-- Pause Icon -->
            <svg class="audio-player__icon audio-player__icon--pause" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
            </svg>
        </button>

        <!-- Volume Control -->
        <div class="audio-player__volume">
            <label for="audio-volume-<?php echo esc_attr($track_type); ?>" class="audio-player__volume-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                    <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                </svg>
            </label>
            <input
                type="range"
                id="audio-volume-<?php echo esc_attr($track_type); ?>"
                class="audio-player__volume-slider"
                min="0"
                max="100"
                value="<?php echo $volume; ?>"
                aria-label="Volume"
            >
            <span class="audio-player__volume-value"><?php echo $volume; ?>%</span>
        </div>

    </div>

    <!-- Hidden Audio Element -->
    <audio
        class="audio-player__element"
        preload="metadata"
        <?php echo $loop ? 'loop' : ''; ?>
    >
        <source src="<?php echo $url; ?>" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

</div>
