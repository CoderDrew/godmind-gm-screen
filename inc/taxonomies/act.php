<?php

/**
 * Act Taxonomy
 */

add_action('init', function () {

    register_taxonomy('act', 'scene', [
        'labels' => [
            'name'          => 'Acts',
            'singular_name' => 'Act',
        ],
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => [
            'slug' => 'act',
        ],
    ]);
});
