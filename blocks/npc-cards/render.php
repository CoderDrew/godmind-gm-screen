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

// Get NPC data
$npc_title = get_the_title($npc);
$npc_excerpt = $npc->post_excerpt;
$npc_content = apply_filters('the_content', $npc->post_content);
$npc_thumbnail = get_the_post_thumbnail($npc_id, 'medium', ['class' => 'gm-npc-modal__image']);

// Get ACF data
$npc_identity = get_field('npc_identity', $npc_id);
$npc_attributes = get_field('npc_attributes', $npc_id);
$npc_vitals = get_field('npc_vitals', $npc_id);
$npc_skills = get_field('npc_skills', $npc_id);
$npc_talents = get_field('npc_talents', $npc_id);
$npc_gear = get_field('npc_gear', $npc_id);
$npc_notes = get_field('npc_notes', $npc_id);
?>

<!-- NPC Card (Preview) -->
<div class="gm-npc-card" data-npc-id="<?php echo esc_attr($npc_id); ?>" role="button" tabindex="0">
    <h3 class="gm-npc-card__name"><?php echo esc_html($npc_title); ?></h3>

    <?php if (!empty($npc_identity['npc_class'])): ?>
        <div class="gm-npc-card__class"><?php echo esc_html($npc_identity['npc_class']); ?></div>
    <?php endif; ?>

    <div class="gm-npc-card__excerpt">
        <?php echo wp_kses_post(wpautop($npc_excerpt)); ?>
    </div>
</div>

<!-- NPC Modal (Full Details) -->
<div id="npc-modal-<?php echo esc_attr($npc_id); ?>" class="gm-npc-modal" role="dialog" aria-modal="true" aria-labelledby="npc-modal-title-<?php echo esc_attr($npc_id); ?>">
    <div class="gm-npc-modal__overlay"></div>
    <div class="gm-npc-modal__content">
        <div class="gm-npc-modal__header">
            <h2 id="npc-modal-title-<?php echo esc_attr($npc_id); ?>" class="gm-npc-modal__title">
                <?php echo esc_html($npc_title); ?>
            </h2>
            <div class="gm-npc-modal__actions">
                <button class="gm-npc-modal__popout" type="button" aria-label="Open in new window" title="Open in new window">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                </button>
                <button class="gm-npc-modal__close" type="button" aria-label="Close NPC details">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div class="gm-npc-modal__body">
            <?php
            // Use ACF profile image if available, otherwise fallback to thumbnail
            $profile_image = !empty($npc_identity['npc__image']) ? wp_get_attachment_image($npc_identity['npc__image'], 'medium', false, ['class' => 'gm-npc-modal__image']) : $npc_thumbnail;
            ?>

            <?php if ($profile_image): ?>
                <div class="gm-npc-modal__portrait">
                    <?php echo $profile_image; ?>
                </div>
            <?php endif; ?>

            <div class="gm-npc-modal__details">
                <?php // Identity Section 
                ?>
                <?php if ($npc_identity && (!empty($npc_identity['npc_class']) || !empty($npc_identity['npc_personality']))): ?>
                    <?php if (!empty($npc_identity['npc_class'])): ?>
                        <div class="npc-class-badge">
                            <span class="npc-class-badge__label">Class:</span>
                            <span class="npc-class-badge__value"><?php echo esc_html($npc_identity['npc_class']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($npc_identity['npc_personality'])): ?>
                        <div class="gm-npc-modal__excerpt">
                            <?php echo wp_kses_post(wpautop($npc_identity['npc_personality'])); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php // Vitals - Health and Resolve 
                ?>
                <?php if ($npc_vitals): ?>
                    <div class="npc-stats-grid">
                        <?php if (!empty($npc_vitals['health_max'])): ?>
                            <div class="npc-stat">
                                <span class="npc-stat__label">Health</span>
                                <span class="npc-stat__value"><?php echo esc_html($npc_vitals['health_max']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($npc_vitals['resolve_max'])): ?>
                            <div class="npc-stat">
                                <span class="npc-stat__label">Resolve</span>
                                <span class="npc-stat__value"><?php echo esc_html($npc_vitals['resolve_max']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php // Attributes 
                ?>
                <?php if ($npc_attributes): ?>
                    <h3>Attributes</h3>
                    <div class="npc-attributes-grid">
                        <?php if (!empty($npc_attributes['attr_strength'])): ?>
                            <div class="npc-attribute">
                                <span class="npc-attribute__label">STRENGTH</span>
                                <span class="npc-attribute__value"><?php echo esc_html($npc_attributes['attr_strength']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($npc_attributes['attr_agility'])): ?>
                            <div class="npc-attribute">
                                <span class="npc-attribute__label">AGILITY</span>
                                <span class="npc-attribute__value"><?php echo esc_html($npc_attributes['attr_agility']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($npc_attributes['attr_wits'])): ?>
                            <div class="npc-attribute">
                                <span class="npc-attribute__label">WITS</span>
                                <span class="npc-attribute__value"><?php echo esc_html($npc_attributes['attr_wits']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($npc_attributes['attr_empathy'])): ?>
                            <div class="npc-attribute">
                                <span class="npc-attribute__label">EMPATHY</span>
                                <span class="npc-attribute__value"><?php echo esc_html($npc_attributes['attr_empathy']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php // Skills with attribute associations and dice pools
                ?>
                <?php
                $skill_pools = godmind_get_npc_skill_pools($npc_id);
                ?>
                <?php if (!empty($skill_pools)): ?>
                    <h3>Skills</h3>
                    <div class="npc-skills-grid">
                        <?php foreach ($skill_pools as $skill): ?>
                            <div class="npc-skill">
                                <div class="npc-skill__info">
                                    <span class="npc-skill__name">
                                        <?php echo esc_html($skill['label']); ?>
                                        <span class="npc-skill__value"><?php echo esc_html($skill['skill_value']); ?></span>
                                    </span>
                                    <span class="npc-skill__attribute"><?php echo esc_html($skill['attr_name']); ?></span>
                                </div>
                                <span class="npc-skill__pool"><?php echo esc_html($skill['pool']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php // Talents 
                ?>
                <?php if ($npc_talents): ?>
                    <h3>Talents</h3>
                    <div class="npc-talents">
                        <?php
                        $talents = array_filter(array_map('trim', explode("\n", $npc_talents)));
                        if (!empty($talents)):
                        ?>
                            <ul class="npc-talents__list">
                                <?php foreach ($talents as $talent): ?>
                                    <li><?php echo esc_html($talent); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php // Gear Section 
                ?>
                <?php if ($npc_gear): ?>
                    <?php if (!empty($npc_gear['npc_weapons'])): ?>
                        <h3>Weapons</h3>
                        <div class="npc-gear-section">
                            <?php
                            $weapons = array_filter(array_map('trim', explode("\n", $npc_gear['npc_weapons'])));
                            if (!empty($weapons)):
                            ?>
                                <ul class="npc-gear__list">
                                    <?php foreach ($weapons as $weapon): ?>
                                        <li><?php echo esc_html($weapon); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($npc_gear['npc_armor'])): ?>
                        <h3>Armor</h3>
                        <div class="npc-gear-section">
                            <?php echo wp_kses_post(wpautop($npc_gear['npc_armor'])); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($npc_gear['npc_equipment'])): ?>
                        <h3>Equipment</h3>
                        <div class="npc-gear-section">
                            <?php
                            $equipment = array_filter(array_map('trim', explode("\n", $npc_gear['npc_equipment'])));
                            if (!empty($equipment)):
                            ?>
                                <ul class="npc-gear__list">
                                    <?php foreach ($equipment as $item): ?>
                                        <li><?php echo esc_html($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($npc_gear['npc_cyberware'])): ?>
                        <h3>Cyberware</h3>
                        <div class="npc-gear-section npc-cyberware">
                            <?php
                            $cyberware = array_filter(array_map('trim', explode("\n", $npc_gear['npc_cyberware'])));
                            if (!empty($cyberware)):
                            ?>
                                <ul class="npc-gear__list">
                                    <?php foreach ($cyberware as $cyber): ?>
                                        <li><?php echo esc_html($cyber); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php // GM Notes 
                ?>
                <?php if ($npc_notes): ?>
                    <h3>GM Notes</h3>
                    <div class="gm-notes">
                        <div class="gm-notes__header">GM ONLY</div>
                        <div class="gm-notes__content">
                            <?php echo wp_kses_post(wpautop($npc_notes)); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>