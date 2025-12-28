<?php
/**
 * NPC Helper Functions
 * Centralized game logic for NPC character mechanics
 */

/**
 * Get canonical skill-to-attribute mappings
 *
 * These mappings are authoritative game canon and must not be modified.
 * Each skill is associated with exactly one attribute used for calculations.
 *
 * @return array Associative array mapping skill keys to attribute keys
 */
function godmind_get_skill_attribute_mapping() {
    return [
        'skill_force'         => 'attr_strength',
        'skill_melee_combat'  => 'attr_strength',
        'skill_stamina'       => 'attr_strength',
        'skill_firearms'      => 'attr_agility',
        'skill_mobility'      => 'attr_agility',
        'skill_stealth'       => 'attr_agility',
        'skill_observation'   => 'attr_wits',
        'skill_tech'          => 'attr_wits',
        'skill_medical_aid'   => 'attr_wits',
        'skill_manipulation'  => 'attr_empathy',
        'skill_insight'       => 'attr_empathy',
        'skill_driving'       => 'attr_agility',
    ];
}

/**
 * Get the attribute key for a given skill
 *
 * @param string $skill_key The skill field key (e.g., 'skill_force')
 * @return string|null The attribute field key or null if not found
 */
function godmind_get_skill_attribute( $skill_key ) {
    $mapping = godmind_get_skill_attribute_mapping();
    return $mapping[ $skill_key ] ?? null;
}

/**
 * Get attribute display name from attribute key
 *
 * @param string $attr_key The attribute field key (e.g., 'attr_strength')
 * @return string Human-readable attribute name
 */
function godmind_get_attribute_name( $attr_key ) {
    $names = [
        'attr_strength' => 'Strength',
        'attr_agility'  => 'Agility',
        'attr_wits'     => 'Wits',
        'attr_empathy'  => 'Empathy',
    ];

    return $names[ $attr_key ] ?? '';
}

/**
 * Calculate total dice pool for a skill check
 *
 * In this game system, the dice pool = attribute value + skill value
 *
 * @param int $npc_id The NPC post ID
 * @param string $skill_key The skill field key
 * @return int The total dice pool (0 if skill or attribute not found)
 */
function godmind_calculate_skill_pool( $npc_id, $skill_key ) {
    // Get the associated attribute key
    $attr_key = godmind_get_skill_attribute( $skill_key );

    if ( ! $attr_key ) {
        return 0;
    }

    // Get ACF field groups
    $skills = get_field( 'npc_skills', $npc_id );
    $attributes = get_field( 'npc_attributes', $npc_id );

    // Get individual values
    $skill_value = ! empty( $skills[ $skill_key ] ) ? (int) $skills[ $skill_key ] : 0;
    $attr_value = ! empty( $attributes[ $attr_key ] ) ? (int) $attributes[ $attr_key ] : 0;

    return $attr_value + $skill_value;
}

/**
 * Get all skills for an NPC with their calculated pools
 *
 * @param int $npc_id The NPC post ID
 * @return array Array of skills with keys: skill_key, label, skill_value, attr_key, attr_name, pool
 */
function godmind_get_npc_skill_pools( $npc_id ) {
    $skills = get_field( 'npc_skills', $npc_id );
    $attributes = get_field( 'npc_attributes', $npc_id );

    if ( ! $skills || ! $attributes ) {
        return [];
    }

    $skill_labels = [
        'skill_melee_combat' => 'Melee Combat',
        'skill_force'        => 'Force',
        'skill_stamina'      => 'Stamina',
        'skill_firearms'     => 'Firearms',
        'skill_mobility'     => 'Mobility',
        'skill_stealth'      => 'Stealth',
        'skill_medical_aid'  => 'Medical Aid',
        'skill_observation'  => 'Observation',
        'skill_tech'         => 'Tech',
        'skill_manipulation' => 'Manipulation',
        'skill_insight'      => 'Insight',
        'skill_driving'      => 'Driving',
    ];

    $result = [];
    $mapping = godmind_get_skill_attribute_mapping();

    foreach ( $skill_labels as $skill_key => $label ) {
        // Only include skills that have values
        if ( empty( $skills[ $skill_key ] ) ) {
            continue;
        }

        $skill_value = (int) $skills[ $skill_key ];
        $attr_key = $mapping[ $skill_key ] ?? null;
        $attr_value = ! empty( $attributes[ $attr_key ] ) ? (int) $attributes[ $attr_key ] : 0;

        $result[] = [
            'skill_key'   => $skill_key,
            'label'       => $label,
            'skill_value' => $skill_value,
            'attr_key'    => $attr_key,
            'attr_name'   => godmind_get_attribute_name( $attr_key ),
            'pool'        => $attr_value + $skill_value,
        ];
    }

    return $result;
}
