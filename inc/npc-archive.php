<?php
/**
 * NPC Archive Functionality
 * Renders NPC cards on the archive page
 */

/**
 * Render NPC cards grid on archive page
 */
function godmind_render_npc_archive_grid() {
    // Check if we're on the NPC archive page
    if (!is_post_type_archive('npc')) {
        return;
    }

    // Query all NPCs
    $args = array(
        'post_type' => 'npc',
        'posts_per_page' => -1, // Get all NPCs
        'orderby' => 'title',
        'order' => 'ASC',
    );

    $npc_query = new WP_Query($args);

    if (!$npc_query->have_posts()) {
        echo '<p>No NPCs found.</p>';
        return;
    }

    echo '<div class="gm-npc-archive__container">';

    // Left side - NPC list
    echo '<div class="gm-npc-archive__list">';

    while ($npc_query->have_posts()) {
        $npc_query->the_post();
        $npc_id = get_the_ID();
        $npc_title = get_the_title();
        $npc_identity = get_field('npc_identity', $npc_id);
        $npc_class = !empty($npc_identity['npc_class']) ? $npc_identity['npc_class'] : '';

        echo '<div class="gm-npc-list-item" data-npc-id="' . esc_attr($npc_id) . '">';
        echo '<div class="gm-npc-list-item__info">';
        echo '<h3 class="gm-npc-list-item__name">' . esc_html($npc_title) . '</h3>';
        if ($npc_class) {
            echo '<span class="gm-npc-list-item__class">' . esc_html($npc_class) . '</span>';
        }
        echo '</div>';
        echo '<button class="gm-npc-list-item__button" aria-label="View ' . esc_attr($npc_title) . ' details">';
        echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
        echo '<polyline points="9 18 15 12 9 6"></polyline>';
        echo '</svg>';
        echo '</button>';
        echo '</div>';
    }

    echo '</div>';

    // Right side - NPC details panel
    echo '<div class="gm-npc-archive__details">';
    echo '<div class="gm-npc-archive__placeholder">';
    echo '<p>Select an NPC to view details</p>';
    echo '</div>';

    // Generate hidden detail panels for each NPC
    $npc_query->rewind_posts();
    while ($npc_query->have_posts()) {
        $npc_query->the_post();
        $npc_id = get_the_ID();

        // Get NPC data
        $npc_title = get_the_title();
        $npc_excerpt = get_the_excerpt();
        $npc_thumbnail = get_the_post_thumbnail($npc_id, 'medium', ['class' => 'gm-npc-detail__image']);

        // Get ACF data
        $npc_identity = get_field('npc_identity', $npc_id);
        $npc_attributes = get_field('npc_attributes', $npc_id);
        $npc_vitals = get_field('npc_vitals', $npc_id);
        $npc_skills = get_field('npc_skills', $npc_id);
        $npc_talents = get_field('npc_talents', $npc_id);
        $npc_gear = get_field('npc_gear', $npc_id);
        $npc_notes = get_field('npc_notes', $npc_id);

        // Include the detail panel template
        include get_theme_file_path('template-parts/npc-detail-panel.php');
    }

    echo '</div>';
    echo '</div>';

    wp_reset_postdata();
}

// Hook into template_include to inject the grid
add_action('wp_footer', 'godmind_inject_npc_archive_grid');

function godmind_inject_npc_archive_grid() {
    if (!is_post_type_archive('npc')) {
        return;
    }
    ?>
    <script>
    (function() {
        const container = document.querySelector('.gm-npc-archive__grid');
        if (container && !container.hasChildNodes()) {
            // Content will be rendered server-side
        }
    })();
    </script>
    <?php
}

// Render the grid in the HTML block placeholder
add_filter('render_block', 'godmind_render_npc_grid_block', 10, 2);

function godmind_render_npc_grid_block($block_content, $block) {
    // Only process on NPC archive
    if (!is_post_type_archive('npc')) {
        return $block_content;
    }

    // Check if this is our placeholder div
    if (isset($block['blockName']) && $block['blockName'] === 'core/html') {
        if (strpos($block_content, 'gm-npc-archive__container') !== false) {
            ob_start();
            godmind_render_npc_archive_grid();
            return ob_get_clean();
        }
    }

    return $block_content;
}
