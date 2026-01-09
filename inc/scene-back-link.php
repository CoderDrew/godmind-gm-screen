<?php
/**
 * Scene Back Link
 * Adds a back link to the scene archive on single scene pages
 */

/**
 * Render back to scenes link
 */
function godmind_render_scene_back_link() {
    if (!is_singular('scene')) {
        return;
    }

    $archive_link = get_post_type_archive_link('scene');
    if (!$archive_link) {
        return;
    }

    echo '<a href="' . esc_url($archive_link) . '" class="gm-scene-back-link__button">';
    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
    echo '<polyline points="15 18 9 12 15 6"></polyline>';
    echo '</svg>';
    echo '<span>All Scenes</span>';
    echo '</a>';
}

// Inject the back link into the placeholder
add_filter('render_block', 'godmind_render_scene_back_link_block', 10, 2);

function godmind_render_scene_back_link_block($block_content, $block) {
    if (!is_singular('scene')) {
        return $block_content;
    }

    // Check if this is our placeholder div
    if (isset($block['blockName']) && $block['blockName'] === 'core/html') {
        if (strpos($block_content, 'gm-scene-back-link') !== false) {
            ob_start();
            godmind_render_scene_back_link();
            return ob_get_clean();
        }
    }

    return $block_content;
}