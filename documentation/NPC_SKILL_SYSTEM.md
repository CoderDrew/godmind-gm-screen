# NPC Skill-Attribute System

## Overview

This system associates each NPC skill with its governing attribute using pure PHP, keeping all game logic centralized and out of ACF field definitions.

## Architecture

### Location
All game logic is centralized in: `inc/npc.php`

### Core Functions

#### 1. `godmind_get_skill_attribute_mapping()`
Returns the canonical skill → attribute mappings as an associative array.

**Example:**
```php
$mapping = godmind_get_skill_attribute_mapping();
// Returns:
// [
//     'skill_force' => 'attr_strength',
//     'skill_melee_combat' => 'attr_strength',
//     ...
// ]
```

#### 2. `godmind_get_skill_attribute( $skill_key )`
Get the attribute key for a specific skill.

**Example:**
```php
$attr = godmind_get_skill_attribute('skill_firearms');
// Returns: 'attr_agility'
```

#### 3. `godmind_get_attribute_name( $attr_key )`
Convert attribute key to human-readable name.

**Example:**
```php
$name = godmind_get_attribute_name('attr_strength');
// Returns: 'Strength'
```

#### 4. `godmind_calculate_skill_pool( $npc_id, $skill_key )`
Calculate total dice pool for a skill check (attribute + skill).

**Example:**
```php
$pool = godmind_calculate_skill_pool(123, 'skill_firearms');
// If NPC has Agility 3 and Firearms 2, returns: 5
```

#### 5. `godmind_get_npc_skill_pools( $npc_id )`
Get all skills for an NPC with complete metadata.

**Returns:**
```php
[
    [
        'skill_key'   => 'skill_firearms',
        'label'       => 'Firearms',
        'skill_value' => 2,
        'attr_key'    => 'attr_agility',
        'attr_name'   => 'Agility',
        'pool'        => 5  // attr_value + skill_value
    ],
    // ... more skills
]
```

## Usage in Render Templates

### Basic Skill Display with Attributes
```php
<?php
// In blocks/npc-cards/render.php
$skill_pools = godmind_get_npc_skill_pools($npc_id);
?>

<?php if (!empty($skill_pools)): ?>
    <div class="npc-skills-grid">
        <?php foreach ($skill_pools as $skill): ?>
            <div class="npc-skill">
                <div class="npc-skill__info">
                    <span class="npc-skill__name">
                        <?php echo esc_html($skill['label']); ?>
                    </span>
                    <span class="npc-skill__attribute">
                        <?php echo esc_html($skill['attr_name']); ?>
                    </span>
                </div>
                <span class="npc-skill__value">
                    <?php echo esc_html($skill['skill_value']); ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
```

### Display Dice Pools
```php
<?php foreach ($skill_pools as $skill): ?>
    <div class="skill-check">
        <span><?php echo esc_html($skill['label']); ?></span>
        <span class="dice-pool">
            <?php echo esc_html($skill['pool']); ?> dice
        </span>
    </div>
<?php endforeach; ?>
```

### Single Skill Calculation
```php
<?php
// Calculate just one skill
$firearms_pool = godmind_calculate_skill_pool($npc_id, 'skill_firearms');
?>
<p>Firearms dice pool: <?php echo esc_html($firearms_pool); ?></p>
```

## Canonical Mappings

The following mappings are **authoritative game canon** and must not be modified:

| Skill | Attribute |
|-------|-----------|
| Force | Strength |
| Melee Combat | Strength |
| Stamina | Strength |
| Firearms | Agility |
| Mobility | Agility |
| Stealth | Agility |
| Driving | Agility |
| Observation | Wits |
| Tech | Wits |
| Medical Aid | Wits |
| Manipulation | Empathy |
| Insight | Empathy |

## Constraints Met

✅ **PHP only** - No JavaScript calculations
✅ **ACF Free only** - No Pro features used
✅ **No repeaters** - Uses groups and sub-fields
✅ **No editor-side calculations** - All server-side
✅ **No ACF changes** - Works with existing field structure
✅ **Centralized logic** - Single source of truth in `inc/npc.php`

## ACF Field Structure Expected

### npc_skills (Group)
- `skill_force` (Number)
- `skill_melee_combat` (Number)
- `skill_stamina` (Number)
- `skill_firearms` (Number)
- `skill_mobility` (Number)
- `skill_stealth` (Number)
- `skill_medical_aid` (Number)
- `skill_observation` (Number)
- `skill_tech` (Number)
- `skill_manipulation` (Number)
- `skill_insight` (Number)
- `skill_driving` (Number)

### npc_attributes (Group)
- `attr_strength` (Number)
- `attr_agility` (Number)
- `attr_wits` (Number)
- `attr_empathy` (Number)

## Future Extensions

This system can be extended for:
- Specialty modifiers
- Equipment bonuses
- Temporary conditions
- Damage tracking
- Custom difficulty calculations

All extensions should be added to `inc/npc.php` to maintain centralization.
