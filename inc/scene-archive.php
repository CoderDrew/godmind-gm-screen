<?php
/**
 * Scene Archive Functionality
 * Renders scene list on the archive page
 */

/**
 * Render scene list on archive page
 */
function godmind_render_scene_archive_list() {
    // Check if we're on the Scene archive page
    if (!is_post_type_archive('scene')) {
        return;
    }

    // Query all Scenes
    $args = array(
        'post_type' => 'scene',
        'posts_per_page' => -1, // Get all scenes
        'orderby' => 'title',
        'order' => 'ASC',
    );

    $scene_query = new WP_Query($args);

    if (!$scene_query->have_posts()) {
        echo '<p>No scenes found.</p>';
        return;
    }

    echo '<div class="gm-scene-archive__list">';

    while ($scene_query->have_posts()) {
        $scene_query->the_post();
        $scene_id = get_the_ID();
        $scene_title = get_the_title();
        $scene_excerpt = get_the_excerpt();
        $scene_link = get_permalink($scene_id);

        // Get taxonomies
        $acts = get_the_terms($scene_id, 'act');
        $scene_types = get_the_terms($scene_id, 'scene_type');

        echo '<a href="' . esc_url($scene_link) . '" class="gm-scene-card">';

        echo '<div class="gm-scene-card__content">';
        echo '<h2 class="gm-scene-card__title">' . esc_html($scene_title) . '</h2>';

        // Display categories
        if ($acts || $scene_types) {
            echo '<div class="gm-scene-card__categories">';

            if ($acts && !is_wp_error($acts)) {
                foreach ($acts as $act) {
                    echo '<span class="gm-scene-card__category gm-scene-card__category--act">' . esc_html($act->name) . '</span>';
                }
            }

            if ($scene_types && !is_wp_error($scene_types)) {
                foreach ($scene_types as $scene_type) {
                    echo '<span class="gm-scene-card__category gm-scene-card__category--type">' . esc_html($scene_type->name) . '</span>';
                }
            }

            echo '</div>';
        }

        if ($scene_excerpt) {
            echo '<p class="gm-scene-card__excerpt">' . esc_html($scene_excerpt) . '</p>';
        }
        echo '</div>';

        echo '<div class="gm-scene-card__arrow">';
        echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
        echo '<polyline points="9 18 15 12 9 6"></polyline>';
        echo '</svg>';
        echo '</div>';

        echo '</a>';
    }

    echo '</div>';

    wp_reset_postdata();
}

// Render the list in the HTML block placeholder
add_filter('render_block', 'godmind_render_scene_list_block', 10, 2);

function godmind_render_scene_list_block($block_content, $block) {
    // Only process on Scene archive
    if (!is_post_type_archive('scene')) {
        return $block_content;
    }

    // Check if this is our placeholder div
    if (isset($block['blockName']) && $block['blockName'] === 'core/html') {
        if (strpos($block_content, 'gm-scene-archive__container') !== false) {
            ob_start();
            godmind_render_scene_archive_list();
            return ob_get_clean();
        }
    }

    return $block_content;
}
