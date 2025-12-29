<?php
require_once get_theme_file_path('/inc/post-types/scene.php');
require_once get_theme_file_path('/inc/post-types/npc.php');
require_once get_theme_file_path('/inc/post-types/asset.php');
require_once get_theme_file_path('/inc/post-types/music.php');

require_once get_theme_file_path('/inc/taxonomies/act.php');
require_once get_theme_file_path('/inc/taxonomies/scene-type.php');

require_once get_theme_file_path('inc/assets.php');
require_once get_theme_file_path('inc/npc.php');


add_action('init', function () {

    register_block_type(get_theme_file_path('/blocks/read-aloud'));
    register_block_type(get_theme_file_path('/blocks/gm-notes'));
    register_block_type(get_theme_file_path('/blocks/npc-cards'));

    error_log('Registered blocks: read-aloud, gm-notes, npc-cards');
});

// Register block pattern categories and patterns
add_action('init', function () {
    // Register category
    register_block_pattern_category('godmind', array(
        'label' => __('Godmind', 'godmind'),
    ));

    // Manually register the scene-purpose pattern
    $pattern_content = file_get_contents(get_theme_file_path('patterns/scene-purpose.php'));
    // Remove PHP tags
    $pattern_content = preg_replace('/<\?php.*?\?>/s', '', $pattern_content);
    $pattern_content = trim($pattern_content);

    register_block_pattern(
        'godmind/scene-purpose',
        array(
            'title'       => __('Scene Purpose', 'godmind'),
            'description' => __('Scene purpose layout for Godmind.', 'godmind'),
            'content'     => $pattern_content,
            'categories'  => array('godmind'),
        )
    );
}, 9);
