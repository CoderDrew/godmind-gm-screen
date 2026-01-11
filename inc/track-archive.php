<?php
/**
 * Track Archive Functionality
 * Renders interactive audio track list on the archive page
 */

/**
 * Render track archive list
 */
function godmind_render_track_archive_list() {
    // Check if we're on the Track archive page
    if (!is_post_type_archive('track')) {
        return;
    }

    // Query all Tracks
    $args = array(
        'post_type' => 'track',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );

    $track_query = new WP_Query($args);

    if (!$track_query->have_posts()) {
        echo '<div class="no-tracks-message">';
        echo '<p>No tracks found. Please add tracks from the WordPress admin.</p>';
        echo '</div>';
        return;
    }

    ?>
    <div class="track-archive-controls">
        <div class="track-archive-filters">
            <div class="filter-group">
                <label for="track-search" class="filter-label">Search</label>
                <input
                    type="text"
                    id="track-search"
                    class="track-search-input"
                    placeholder="Search tracks..."
                    autocomplete="off"
                >
            </div>

            <div class="filter-group">
                <label class="filter-label">Track Type</label>
                <div class="filter-buttons" role="group" aria-label="Track type filters">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="music">Music</button>
                    <button class="filter-btn" data-filter="ambient">Ambient</button>
                    <button class="filter-btn" data-filter="sfx">SFX</button>
                </div>
            </div>

            <div class="filter-group">
                <label class="toggle-label">
                    <input type="checkbox" id="favorites-toggle" class="toggle-input">
                    <span class="toggle-text">Favorites Only</span>
                </label>
            </div>
        </div>

        <div class="recently-played-section" data-empty-message="No tracks played yet">
            <h3 class="recently-played-title">Recently Played</h3>
            <div class="recently-played-list"></div>
        </div>
    </div>

    <div class="track-grid" role="list">
    <?php
    while ($track_query->have_posts()) {
        $track_query->the_post();

        // Get ACF fields
        $audio_file = get_field('audio_file');
        $track_type = get_field('track_type');
        $track_purpose = get_field('track_purpose');
        $default_volume = get_field('audio_volume');
        $suggested_scene = get_field('suggested_scene_use');

        // Skip if no valid audio file
        if (!$audio_file || !isset($audio_file['url'])) {
            continue;
        }

        // Resolve audio URL
        $audio_url = esc_url($audio_file['url']);

        // Set defaults
        $track_type = $track_type ?: 'music';
        $default_volume = is_numeric($default_volume) ? max(0, min(100, intval($default_volume))) : 70;
        $track_purpose = $track_purpose ?: '';
        $suggested_scene = $suggested_scene ?: '';

        // Determine if should auto-loop
        $should_loop = ($track_type === 'ambient');

        $track_id = get_the_ID();
        $track_title = get_the_title();
        ?>

        <article
            class="track-card"
            role="listitem"
            data-track-id="<?php echo esc_attr($track_id); ?>"
            data-src="<?php echo $audio_url; ?>"
            data-track-type="<?php echo esc_attr($track_type); ?>"
            data-default-volume="<?php echo esc_attr($default_volume); ?>"
            data-purpose="<?php echo esc_attr($track_purpose); ?>"
            data-scene="<?php echo esc_attr($suggested_scene); ?>"
            data-loop="<?php echo $should_loop ? 'true' : 'false'; ?>"
        >
            <header class="track-card-header">
                <h3 class="track-title"><?php echo esc_html($track_title); ?></h3>
                <span class="track-type-badge track-type-<?php echo esc_attr($track_type); ?>">
                    <?php echo esc_html(ucfirst($track_type)); ?>
                </span>
            </header>

            <div class="track-meta">
                <?php if ($track_purpose): ?>
                    <div class="track-purpose">
                        <span class="meta-label">Purpose:</span>
                        <span class="meta-value"><?php echo esc_html($track_purpose); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($suggested_scene): ?>
                    <div class="track-scene">
                        <span class="meta-label">Scene:</span>
                        <span class="meta-value"><?php echo esc_html($suggested_scene); ?></span>
                    </div>
                <?php endif; ?>

                <div class="track-duration">
                    <span class="meta-label">Duration:</span>
                    <span class="meta-value duration-value">--:--</span>
                </div>
            </div>

            <div class="track-controls">
                <div class="track-primary-controls">
                    <button class="track-btn track-btn-play" aria-label="Play" title="Play">
                        <span class="btn-icon icon-play">▶</span>
                    </button>
                    <button class="track-btn track-btn-pause hidden" aria-label="Pause" title="Pause">
                        <span class="btn-icon icon-pause">⏸</span>
                    </button>
                    <button class="track-btn track-btn-restart" aria-label="Restart" title="Restart">
                        <span class="btn-icon">↻</span>
                    </button>
                    <button class="track-btn track-btn-loop <?php echo $should_loop ? 'active' : ''; ?>"
                            aria-label="Loop"
                            title="Loop"
                            <?php echo ($track_type === 'ambient') ? 'disabled' : ''; ?>>
                        <span class="btn-icon">🔁</span>
                    </button>
                    <button class="track-btn track-btn-favorite" aria-label="Favorite" title="Add to favorites">
                        <span class="btn-icon icon-favorite">☆</span>
                    </button>
                </div>

                <div class="track-volume-control">
                    <label class="volume-label" for="volume-<?php echo esc_attr($track_id); ?>">
                        <span class="volume-icon">🔊</span>
                    </label>
                    <input
                        type="range"
                        id="volume-<?php echo esc_attr($track_id); ?>"
                        class="volume-slider"
                        min="0"
                        max="100"
                        value="<?php echo esc_attr($default_volume); ?>"
                        aria-label="Volume"
                    >
                    <span class="volume-value"><?php echo esc_html($default_volume); ?>%</span>
                </div>
            </div>

            <div class="track-progress">
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
                <div class="progress-time">
                    <span class="time-current">0:00</span>
                    <span class="time-total">0:00</span>
                </div>
            </div>

            <noscript>
                <div class="track-fallback">
                    <a href="<?php echo $audio_url; ?>" download>Download MP3</a>
                </div>
            </noscript>
        </article>

        <?php
    }
    ?>
    </div>
    <?php

    wp_reset_postdata();
}

// Render the list in the HTML block placeholder
add_filter('render_block', 'godmind_render_track_list_block', 10, 2);

function godmind_render_track_list_block($block_content, $block) {
    // Only process on Track archive
    if (!is_post_type_archive('track')) {
        return $block_content;
    }

    // Check if this is our placeholder div
    if (isset($block['blockName']) && $block['blockName'] === 'core/html') {
        if (strpos($block_content, 'gm-track-archive__container') !== false) {
            ob_start();
            godmind_render_track_archive_list();
            return ob_get_clean();
        }
    }

    return $block_content;
}
