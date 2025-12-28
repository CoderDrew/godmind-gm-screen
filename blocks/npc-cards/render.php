<?php
if (empty($attributes['npcId'])) {
    echo '<div class="gm-npc-card gm-npc-card--empty">No NPC selected.</div>';
    return;
}

$npc_id = absint($attributes['npcId']);
$npc = get_post($npc_id);

if (! $npc || $npc->post_type !== 'npc') {
    echo '<div class="gm-npc-card gm-npc-card--missing">NPC not found (ID: ' . esc_html($npc_id) . ').</div>';
    return;
}
?>

<div class="gm-npc-card" data-npc-id="<?php echo esc_attr($npc_id); ?>">
    <h3 class="gm-npc-card__name"><?php echo esc_html(get_the_title($npc)); ?></h3>

    <div class="gm-npc-card__excerpt">
        <?php echo wp_kses_post(wpautop($npc->post_excerpt)); ?>
    </div>

    <button class="gm-npc-card__open" type="button">
        View Full NPC
    </button>
</div>