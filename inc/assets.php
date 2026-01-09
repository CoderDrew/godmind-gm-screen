<?php
// inc/assets.php

function godmind_enqueue_assets()
{

    // Main global styles
    wp_enqueue_style(
        'godmind-main',
        get_theme_file_uri('assets/css/main.css'),
        [],
        wp_get_theme()->get('Version')
    );

    // Header component styles
    wp_enqueue_style(
        'godmind-header',
        get_theme_file_uri('assets/css/header.css'),
        [],
        wp_get_theme()->get('Version')
    );

    // Footer component styles
    wp_enqueue_style(
        'godmind-footer',
        get_theme_file_uri('assets/css/footer.css'),
        [],
        wp_get_theme()->get('Version')
    );

    // Sidebar navigation styles
    wp_enqueue_style(
        'godmind-sidebar',
        get_theme_file_uri('assets/css/sidebar.css'),
        [],
        wp_get_theme()->get('Version')
    );

    // Sidebar navigation JavaScript
    wp_enqueue_script(
        'godmind-sidebar',
        get_theme_file_uri('assets/js/sidebar.js'),
        [],
        wp_get_theme()->get('Version'),
        true // Load in footer
    );

    // NPC Modal styles
    wp_enqueue_style(
        'godmind-npc-modal',
        get_theme_file_uri('assets/css/npc-modal.css'),
        [],
        wp_get_theme()->get('Version')
    );

    // NPC Modal JavaScript
    wp_enqueue_script(
        'godmind-npc-modal',
        get_theme_file_uri('assets/js/npc-modal.js'),
        [],
        wp_get_theme()->get('Version'),
        true // Load in footer
    );

    // Audio Player styles (only load on Scene pages)
    if (is_singular('scene')) {
        wp_enqueue_style(
            'godmind-audio-player',
            get_theme_file_uri('assets/css/audio-player.css'),
            [],
            wp_get_theme()->get('Version')
        );

        // Audio Player JavaScript
        wp_enqueue_script(
            'godmind-audio-player',
            get_theme_file_uri('assets/js/audio-player.js'),
            [],
            wp_get_theme()->get('Version'),
            true // Load in footer
        );
    }
}
add_action('wp_enqueue_scripts', 'godmind_enqueue_assets');
