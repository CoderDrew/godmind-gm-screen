<?php
$content = $attributes['content'] ?? '';

if (empty($content)) {
    return;
}
?>

<div class="read-aloud">
    <button class="read-aloud__header" type="button" aria-expanded="false">
        <span class="read-aloud__label">READ ALOUD</span>
        <svg class="read-aloud__icon" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="4 6 8 10 12 6"></polyline>
        </svg>
    </button>

    <div class="read-aloud__content">
        <?php echo wp_kses_post(wpautop($content)); ?>
    </div>
</div>