<?php

/**
 * Scene CPT
 */

add_action('init', function () {

    register_post_type('scene', [
        'labels' => [
            'name'          => 'Scenes',
            'singular_name' => 'Scene',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-controls-play',
        'supports'      => [
            'title',
            'editor',     // GM Notes (use blocks)
            'excerpt',    // Scene tagline
            'revisions',
        ],
        'rewrite' => [
            'slug' => 'scene',
        ],
    ]);
});
