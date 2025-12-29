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

// Get audio data using helper function
$audio_data = godmind_get_audio_data($track_id);

// If no valid audio data, don't render anything
if (!$audio_data) {
    if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) {
        echo '<div class="godmind-audio-player-placeholder" style="padding: 1rem; background: #fff3cd; border: 1px solid #856404; border-radius: 4px; text-align: center;">';
        echo '<p style="margin: 0; color: #856404;">⚠️ Selected track has no audio file. Please check the Music CPT settings.</p>';
        echo '</div>';
        return;
    }
    return;
}

// Render the audio player using the template
get_template_part('template-parts/audio-player', null, $audio_data);
