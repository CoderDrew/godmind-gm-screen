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
        'public'        => true,
        'has_archive'   => true,
        'show_ui'       => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-businessperson',
        'supports'      => [
            'title',
            'revisions',
        ],
    ]);
});
