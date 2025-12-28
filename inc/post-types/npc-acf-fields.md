# NPC ACF Field Structure

This document outlines the Advanced Custom Fields structure for the NPC post type.

## Field Group Settings
- **Field Group Name:** NPC Details
- **Location Rule:** Post Type is equal to NPC

## Recommended Fields

### Basic Stats Section
**Group Name:** `npc_stats` (Field Type: Group)

- `level` - Number field - Character/Threat Level
- `health_points` - Text field - HP/Health (e.g., "50" or "50/50")
- `armor_class` - Text field - AC/Defense rating
- `speed` - Text field - Movement speed (e.g., "30 ft" or "6 squares")

### Attributes Section
**Group Name:** `npc_attributes` (Field Type: Group)

- `strength` - Number field
- `dexterity` - Number field
- `constitution` - Number field
- `intelligence` - Number field
- `wisdom` - Number field
- `charisma` - Number field

### Abilities Section
**Field Name:** `npc_abilities` (Field Type: Repeater)

Each row contains:
- `ability_name` - Text field - Name of the ability
- `ability_description` - Textarea - What the ability does

### Equipment Section
**Field Name:** `npc_equipment` (Field Type: Wysiwyg Editor or Textarea)

Freeform text for weapons, armor, and other gear.

### Personality Section
**Group Name:** `npc_personality` (Field Type: Group)

- `motivation` - Textarea - What drives this NPC
- `personality_traits` - Textarea - Notable quirks, behaviors
- `secrets` - Textarea - Hidden information (GM-only notes)

## Field Name Reference

When calling in PHP templates:
```php
// Basic Stats
$level = get_field('npc_stats')['level'];
$hp = get_field('npc_stats')['health_points'];
$ac = get_field('npc_stats')['armor_class'];
$speed = get_field('npc_stats')['speed'];

// Attributes
$attrs = get_field('npc_attributes');
$str = $attrs['strength'];
$dex = $attrs['dexterity'];
// etc.

// Abilities (Repeater)
$abilities = get_field('npc_abilities');
if ($abilities) {
    foreach ($abilities as $ability) {
        echo $ability['ability_name'];
        echo $ability['ability_description'];
    }
}

// Equipment
$equipment = get_field('npc_equipment');

// Personality
$personality = get_field('npc_personality');
$motivation = $personality['motivation'];
$traits = $personality['personality_traits'];
$secrets = $personality['secrets'];
```

## Notes

- You can customize these fields based on your specific game system
- Consider using Field Groups for better organization in the WordPress admin
- The `secrets` field could be displayed differently or hidden from players if needed
