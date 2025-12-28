<?php

/**
 * Music CPT
 */

add_action('init', function () {

    register_post_type('music', [
        'labels' => [
            'name'          => 'Music',
            'singular_name' => 'Track',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-format-audio',
        'supports'      => [
            'title',
            'editor', // Embed + GM notes
        ],
    ]);
});
