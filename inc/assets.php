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
}
add_action('wp_enqueue_scripts', 'godmind_enqueue_assets');
