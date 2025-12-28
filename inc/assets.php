<?php
// inc/assets.php

function godmind_enqueue_assets()
{

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
}
add_action('wp_enqueue_scripts', 'godmind_enqueue_assets');
