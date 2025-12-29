<?php

/**
 * Audio Player Helper Functions
 *
 * Retrieves and processes Music CPT data for the custom audio player.
 */

/**
 * Get audio player data from a Music CPT post
 *
 * @param int $track_id The Music CPT post ID
 * @return array|null Array of audio data or null if no valid audio found
 */
function godmind_get_audio_data($track_id) {
    if (!$track_id) {
        return null;
    }

    // Get the audio file field (ACF free version, return format = array)
    $audio_file = get_field('audio_file', $track_id);

    if (!$audio_file || !isset($audio_file['url'])) {
        return null;
    }

    // Get track type to determine default loop behavior
    $track_type = get_field('track_type', $track_id);

    // Determine if audio should loop
    // Priority: explicit audio_loop field > track_type default
    $should_loop = false;
    $audio_loop_field = get_field('audio_loop', $track_id);

    if ($audio_loop_field !== null && $audio_loop_field !== '') {
        // Explicit loop setting exists, use it
        $should_loop = (bool) $audio_loop_field;
    } else {
        // Use track type default: ambient loops, music/sfx do not
        $should_loop = ($track_type === 'ambient');
    }

    // Get volume (default 70 if not set)
    $volume = get_field('audio_volume', $track_id);
    if ($volume === null || $volume === '') {
        $volume = 70;
    }
    // Ensure volume is between 0-100
    $volume = max(0, min(100, intval($volume)));

    // Get track title (CPT title, not filename)
    $track_title = get_the_title($track_id);

    // Get track purpose (GM-facing description)
    $track_purpose = get_field('track_purpose', $track_id);

    return [
        'url'           => esc_url($audio_file['url']),
        'title'         => esc_html($track_title),
        'loop'          => $should_loop,
        'volume'        => $volume,
        'track_type'    => $track_type ? esc_attr($track_type) : 'music',
        'track_purpose' => $track_purpose ? esc_html($track_purpose) : '',
    ];
}

/**
 * Render the audio player HTML
 *
 * @param int $track_id The Music CPT post ID
 */
function godmind_render_audio_player($track_id) {
    $audio_data = godmind_get_audio_data($track_id);

    if (!$audio_data) {
        return;
    }

    // Load the template partial
    get_template_part('template-parts/audio-player', null, $audio_data);
}
