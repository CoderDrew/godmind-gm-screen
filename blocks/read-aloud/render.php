<?php
$content = $attributes['content'] ?? '';

if (empty($content)) {
    return;
}
?>

<div class="read-aloud">
    <div class="read-aloud__header">
        <span class="read-aloud__label">READ ALOUD</span>
        <button
            class="read-aloud__copy"
            type="button"
            onclick="navigator.clipboard.writeText(this.closest('.read-aloud').querySelector('.read-aloud__content')?.innerText || '')">
            Copy
        </button>
    </div>

    <div class="read-aloud__content">
        <?php echo wp_kses_post(wpautop($content)); ?>
    </div>
</div>