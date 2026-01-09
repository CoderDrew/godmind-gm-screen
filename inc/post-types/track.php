<?php

/**
 * Music CPT
 */

add_action('init', function () {

    register_post_type('track', [
        'labels' => [
            'name'          => 'Tracks',
            'singular_name' => 'Track',
        ],
        'public'        => true,
        'has_archive'   => true,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-format-audio',
        'supports'      => [
            'title'
        ],
    ]);
});
