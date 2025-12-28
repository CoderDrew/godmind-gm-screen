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
