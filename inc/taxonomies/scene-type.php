<?php

/**
 * Scene Type Taxonomy
 */

add_action('init', function () {

    register_taxonomy('scene_type', 'scene', [
        'labels' => [
            'name'          => 'Scene Types',
            'singular_name' => 'Scene Type',
        ],
        'hierarchical' => false,
        'show_in_rest' => true,
    ]);
});
