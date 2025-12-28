<?php

/**
 * NPC CPT
 */

add_action('init', function () {

    register_post_type('npc', [
        'labels' => [
            'name'          => 'NPCs',
            'singular_name' => 'NPC',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-businessperson',
        'supports'      => [
            'title',
            'editor',      // Stats / abilities / tactics
            'thumbnail',   // Portrait
            'revisions',
        ],
    ]);
});
