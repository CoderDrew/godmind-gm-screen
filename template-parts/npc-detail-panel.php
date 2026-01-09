<?php
/**
 * NPC Detail Panel Template Part
 * Used in the NPC archive page to display full NPC details
 *
 * Expected variables:
 * - $npc_id
 * - $npc_title
 * - $npc_excerpt
 * - $npc_thumbnail
 * - $npc_identity
 * - $npc_attributes
 * - $npc_vitals
 * - $npc_skills
 * - $npc_talents
 * - $npc_gear
 * - $npc_notes
 */
?>

<div id="npc-detail-<?php echo esc_attr($npc_id); ?>" class="gm-npc-detail" data-npc-id="<?php echo esc_attr($npc_id); ?>" style="display: none;">
    <div class="gm-npc-detail__header">
        <h2 class="gm-npc-detail__title"><?php echo esc_html($npc_title); ?></h2>
    </div>

    <div class="gm-npc-detail__body">
        <?php
        // Use ACF profile image if available, otherwise fallback to thumbnail
        $profile_image = !empty($npc_identity['npc__image']) ? wp_get_attachment_image($npc_identity['npc__image'], 'medium', false, ['class' => 'gm-npc-detail__image']) : $npc_thumbnail;
        ?>

        <?php if ($profile_image): ?>
            <div class="gm-npc-detail__portrait">
                <?php echo $profile_image; ?>
            </div>
        <?php endif; ?>

        <div class="gm-npc-detail__content">
            <?php // Identity Section ?>
            <?php if ($npc_identity && (!empty($npc_identity['npc_class']) || !empty($npc_identity['npc_personality']))): ?>
                <?php if (!empty($npc_identity['npc_class'])): ?>
                    <div class="npc-class-badge">
                        <span class="npc-class-badge__label">Class:</span>
                        <span class="npc-class-badge__value"><?php echo esc_html($npc_identity['npc_class']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($npc_identity['npc_personality'])): ?>
                    <div class="gm-npc-detail__excerpt">
                        <?php echo wp_kses_post(wpautop($npc_identity['npc_personality'])); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php // Vitals - Health and Resolve ?>
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

            <?php // Attributes ?>
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

            <?php // Skills with attribute associations and dice pools ?>
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

            <?php // Talents ?>
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

            <?php // Gear Section ?>
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

            <?php // GM Notes ?>
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
