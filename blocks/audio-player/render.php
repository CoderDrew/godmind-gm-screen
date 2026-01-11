<?php
/**
 * Audio Player Block - Server-side Render
 *
 * @var array    $attributes Block attributes
 * @var string   $content    Block default content
 * @var WP_Block $block      Block instance
 */

// Get the track ID from block attributes
$track_id = isset($attributes['trackId']) ? intval($attributes['trackId']) : 0;

// If no track selected, show placeholder in editor
if (!$track_id) {
    if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) {
        echo '<div class="godmind-audio-player-placeholder" style="padding: 1rem; background: #f0f0f1; border: 1px dashed #8c8f94; border-radius: 4px; text-align: center;">';
        echo '<p style="margin: 0; color: #50575e;">🎵 Select a track in the block settings to display the audio player.</p>';
        echo '</div>';
        return;
    }
    return;
}

// Get ACF fields directly
$audio_file = get_field('audio_file', $track_id);
$track_type = get_field('track_type', $track_id);
$track_purpose = get_field('track_purpose', $track_id);
$default_volume = get_field('audio_volume', $track_id);
$suggested_scene = get_field('suggested_scene_use', $track_id);

// If no valid audio file, show error in editor
if (!$audio_file || !isset($audio_file['url'])) {
    if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) {
        echo '<div class="godmind-audio-player-placeholder" style="padding: 1rem; background: #fff3cd; border: 1px solid #856404; border-radius: 4px; text-align: center;">';
        echo '<p style="margin: 0; color: #856404;">⚠️ Selected track has no audio file. Please check the Track CPT settings.</p>';
        echo '</div>';
        return;
    }
    return;
}

// Resolve audio URL
$audio_url = esc_url($audio_file['url']);

// Set defaults
$track_type = $track_type ?: 'music';
$default_volume = is_numeric($default_volume) ? max(0, min(100, intval($default_volume))) : 70;
$track_purpose = $track_purpose ?: '';
$suggested_scene = $suggested_scene ?: '';

// Determine if should auto-loop
$should_loop = ($track_type === 'ambient');

$track_title = get_the_title($track_id);
?>

<article
    <?php echo get_block_wrapper_attributes(['class' => 'track-card godmind-audio-player-block']); ?>
    role="region"
    aria-label="Audio Player"
    data-track-id="<?php echo esc_attr($track_id); ?>"
    data-src="<?php echo $audio_url; ?>"
    data-track-type="<?php echo esc_attr($track_type); ?>"
    data-default-volume="<?php echo esc_attr($default_volume); ?>"
    data-purpose="<?php echo esc_attr($track_purpose); ?>"
    data-scene="<?php echo esc_attr($suggested_scene); ?>"
    data-loop="<?php echo $should_loop ? 'true' : 'false'; ?>"
>
    <header class="track-card-header">
        <h3 class="track-title"><?php echo esc_html($track_title); ?></h3>
        <span class="track-type-badge track-type-<?php echo esc_attr($track_type); ?>">
            <?php echo esc_html(ucfirst($track_type)); ?>
        </span>
    </header>

    <div class="track-meta">
        <?php if ($track_purpose): ?>
            <div class="track-purpose">
                <span class="meta-label">Purpose:</span>
                <span class="meta-value"><?php echo esc_html($track_purpose); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($suggested_scene): ?>
            <div class="track-scene">
                <span class="meta-label">Scene:</span>
                <span class="meta-value"><?php echo esc_html($suggested_scene); ?></span>
            </div>
        <?php endif; ?>

        <div class="track-duration">
            <span class="meta-label">Duration:</span>
            <span class="meta-value duration-value">--:--</span>
        </div>
    </div>

    <div class="track-controls">
        <div class="track-primary-controls">
            <button class="track-btn track-btn-play" aria-label="Play" title="Play">
                <span class="btn-icon icon-play">▶</span>
            </button>
            <button class="track-btn track-btn-pause hidden" aria-label="Pause" title="Pause">
                <span class="btn-icon icon-pause">⏸</span>
            </button>
            <button class="track-btn track-btn-restart" aria-label="Restart" title="Restart">
                <span class="btn-icon">↻</span>
            </button>
            <button class="track-btn track-btn-loop <?php echo $should_loop ? 'active' : ''; ?>"
                    aria-label="Loop"
                    title="Loop"
                    <?php echo ($track_type === 'ambient') ? 'disabled' : ''; ?>>
                <span class="btn-icon">🔁</span>
            </button>
            <button class="track-btn track-btn-favorite" aria-label="Favorite" title="Add to favorites">
                <span class="btn-icon icon-favorite">☆</span>
            </button>
        </div>

        <div class="track-volume-control">
            <label class="volume-label" for="volume-<?php echo esc_attr($track_id); ?>">
                <span class="volume-icon">🔊</span>
            </label>
            <input
                type="range"
                id="volume-<?php echo esc_attr($track_id); ?>"
                class="volume-slider"
                min="0"
                max="100"
                value="<?php echo esc_attr($default_volume); ?>"
                aria-label="Volume"
            >
            <span class="volume-value"><?php echo esc_html($default_volume); ?>%</span>
        </div>
    </div>

    <div class="track-progress">
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        <div class="progress-time">
            <span class="time-current">0:00</span>
            <span class="time-total">0:00</span>
        </div>
    </div>

    <noscript>
        <div class="track-fallback">
            <a href="<?php echo $audio_url; ?>" download>Download MP3</a>
        </div>
    </noscript>
</article>
