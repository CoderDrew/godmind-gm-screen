<?php

/**
 * Asset CPT
 */

add_action('init', function () {

    register_post_type('asset', [
        'labels' => [
            'name'          => 'Assets',
            'singular_name' => 'Asset',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-format-image',
        'supports'      => [
            'title',
            'thumbnail',
            'editor',   // Optional description / notes
        ],
    ]);
});
