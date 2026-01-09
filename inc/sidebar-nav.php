<?php
/**
 * Sidebar Navigation
 * Generates navigation links for custom post types
 */

/**
 * Render sidebar navigation
 */
function godmind_render_sidebar_nav() {
    $nav_items = array(
        array(
            'slug' => 'home',
            'label' => 'Home',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>'
        ),
        array(
            'slug' => 'scene',
            'label' => 'Scenes',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>'
        ),
        array(
            'slug' => 'npc',
            'label' => 'NPCs',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>'
        ),
        array(
            'slug' => 'asset',
            'label' => 'Assets',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
            </svg>'
        ),
        array(
            'slug' => 'track',
            'label' => 'Tracks',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
            </svg>'
        )
    );

    echo '<nav class="gm-sidebar__nav">';

    foreach ($nav_items as $item) {
        // Handle Home link differently
        if ($item['slug'] === 'home') {
            $link = home_url('/');
            $is_active = (is_front_page() || is_home()) ? 'is-active' : '';
        } else {
            $link = get_post_type_archive_link($item['slug']);
            if (!$link) {
                continue;
            }
            $is_active = is_post_type_archive($item['slug']) ? 'is-active' : '';
        }

        echo '<a href="' . esc_url($link) . '" class="gm-sidebar__nav-item ' . $is_active . '">';
        echo '<span class="gm-sidebar__nav-icon">' . $item['icon'] . '</span>';
        echo '<span class="gm-sidebar__nav-label">' . esc_html($item['label']) . '</span>';
        echo '</a>';
    }

    echo '</nav>';
}

// Inject navigation into sidebar template
add_filter('render_block', 'godmind_inject_sidebar_nav', 10, 2);

function godmind_inject_sidebar_nav($block_content, $block) {
    // Check if this is an HTML block with our navigation container
    if (isset($block['blockName']) && $block['blockName'] === 'core/html') {
        if (strpos($block_content, 'gm-sidebar__nav-container') !== false) {
            ob_start();
            godmind_render_sidebar_nav();
            return ob_get_clean();
        }
    }

    return $block_content;
}
