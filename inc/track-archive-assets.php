<?php

/**
 * Track Archive Assets Enqueue
 *
 * Conditionally loads JS and CSS for the Track Archive.
 */

add_action('wp_enqueue_scripts', function () {
    // Only enqueue on track archive
    if (!is_post_type_archive('track')) {
        return;
    }

    // Enqueue CSS
    wp_enqueue_style(
        'godmind-track-archive',
        get_theme_file_uri('assets/track-archive.css'),
        [],
        filemtime(get_theme_file_path('assets/track-archive.css'))
    );

    // Enqueue JavaScript
    wp_enqueue_script(
        'godmind-track-archive',
        get_theme_file_uri('assets/track-archive.js'),
        [],
        filemtime(get_theme_file_path('assets/track-archive.js')),
        true // Load in footer
    );
});
