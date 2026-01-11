# Track Archive Page Template - Documentation

## Overview

The Track Archive is a custom WordPress page template that provides an interactive audio control console for managing tabletop RPG session tracks. It uses the Web Audio API for advanced playback controls, supports multiple track types (Music, Ambient, SFX), and offers GM-focused features like favorites, recently played, and smart filtering.

---

## Files Created

```
wp-content/themes/godmind/
├── track-archive.php                    # Page template
├── assets/
│   ├── track-archive.js                 # Audio engine + UI controller
│   └── track-archive.css                # Cyberpunk console UI styles
└── inc/
    └── track-archive-assets.php         # Conditional enqueue function
```

---

## Installation & Usage

### 1. Assign the Template to a Page

1. Go to **Pages → Add New** (or edit an existing page)
2. In the Page settings sidebar, find **Template**
3. Select **Track Archive** from the dropdown
4. Add optional intro content using Gutenberg blocks above the archive
5. Publish the page

The archive will automatically query and display all published `track` posts with valid MP3 files.

---

## Features

### Audio Playback Engine (Web Audio API)

- **Music Tracks**: Exclusive playback (only one music track at a time)
- **Ambient Tracks**: Auto-loop enabled, can play simultaneously
- **SFX**: One-shot playback, auto-stop when complete
- **Fade In/Out**: 2-second smooth transitions
- **Volume Ducking**: Music automatically reduces to 30% volume when SFX plays
- **Safe Interaction Handling**: Prevents errors from rapid clicks

### User Interface

- **Track Type Filters**: All / Music / Ambient / SFX
- **Live Search**: Filter by track title, purpose, or scene
- **Favorites System**: Mark tracks as favorites (stored in localStorage)
- **Recently Played**: Shows last 8 played tracks (clickable to scroll to track)
- **Real-time Progress**: Visual progress bars and time displays
- **Volume Control**: Per-track volume sliders initialized to default_volume
- **Loop Toggle**: Enable/disable looping (locked ON for ambient tracks)

### Track Card Components

Each track displays:
- Track title and type badge
- Track purpose (GM description)
- Suggested scene use
- Duration (auto-loaded via Web Audio API)
- Playback controls (Play/Pause, Restart, Loop, Favorite)
- Volume slider with percentage display
- Progress bar with current/total time

---

## ACF Field Requirements

The template expects these ACF fields on the `track` CPT:

| Field Name             | Type         | Description                          |
|------------------------|--------------|--------------------------------------|
| `audio_file`           | File         | MP3 file (return format: Array)      |
| `track_type`           | Select       | Options: `music`, `ambient`, `sfx`   |
| `track_purpose`        | Text         | GM-facing description                |
| `audio_volume`         | Number       | Default volume (0-100)               |
| `suggested_scene_use`  | Text         | Recommended scene context            |

**Note**: The existing `audio_loop` field is respected if set, otherwise defaults based on track_type.

---

## Browser Compatibility

- **Chrome/Edge**: Full support
- **Firefox**: Full support
- **Safari**: Full support (requires user interaction to start AudioContext)
- **Mobile**: Supported, but UX optimized for desktop GM usage

---

## Converting to a Gutenberg Block

This page template can be converted into a reusable Gutenberg block. Here's the recommended approach:

### Step 1: Create Block Directory Structure

```
blocks/
└── track-archive-block/
    ├── block.json
    ├── render.php
    ├── edit.js
    ├── view.js
    └── style.css
```

### Step 2: Move Server-Side Logic to `render.php`

Extract the PHP query and HTML output from `track-archive.php` into `render.php`:

```php
<?php
// blocks/track-archive-block/render.php

$tracks_query = new WP_Query([
    'post_type'      => 'track',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

ob_start();
?>
<div class="godmind-track-archive">
    <!-- Move entire HTML structure here -->
</div>
<?php
return ob_get_clean();
```

### Step 3: Create `block.json`

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "godmind/track-archive",
  "title": "Track Archive",
  "category": "godmind",
  "icon": "format-audio",
  "description": "Interactive audio archive for GM track management",
  "supports": {
    "html": false,
    "align": ["wide", "full"]
  },
  "textdomain": "godmind",
  "editorScript": "file:./edit.js",
  "viewScript": "file:./view.js",
  "style": "file:./style.css",
  "render": "file:./render.php"
}
```

### Step 4: Create Block Editor JavaScript (`edit.js`)

```javascript
import { useBlockProps } from '@wordpress/block-props';

export default function Edit() {
    const blockProps = useBlockProps();

    return (
        <div {...blockProps}>
            <div style={{ padding: '2rem', background: '#f0f0f0', textAlign: 'center' }}>
                <h3>Track Archive Block</h3>
                <p>The track archive will be rendered on the frontend.</p>
            </div>
        </div>
    );
}
```

### Step 5: Create Frontend Script (`view.js`)

Copy the contents of `assets/track-archive.js` into `view.js`. This ensures the audio engine loads when the block is rendered.

### Step 6: Move Styles

Copy `assets/track-archive.css` into `blocks/track-archive-block/style.css`.

### Step 7: Register the Block

In `functions.php`, add:

```php
register_block_type(get_theme_file_path('/blocks/track-archive-block'));
```

### Step 8: Build with webpack (if using)

If your theme uses webpack, update `webpack.config.js` to compile the block:

```javascript
entry: {
    // ... existing entries
    'blocks/track-archive-block/edit': './blocks/track-archive-block/edit.js',
    'blocks/track-archive-block/view': './blocks/track-archive-block/view.js',
},
```

### Step 9: Use the Block

Once registered, the "Track Archive" block will appear in the block inserter. Insert it into any page or post to render the interactive archive.

---

## Benefits of Block Conversion

1. **Reusability**: Insert the archive into any page, post, or FSE template part
2. **Flexibility**: Combine with other blocks (e.g., intro text, navigation)
3. **Editor Integration**: Preview and manage within Gutenberg
4. **Future Attributes**: Easily add block settings (e.g., filter defaults, layout options)

---

## Technical Architecture

### Audio Engine Flow

```
User clicks Play
    ↓
AudioEngine.ensureAudioContext() — Create/resume AudioContext
    ↓
AudioEngine.play(trackId, trackData)
    ↓
Fetch MP3 → Decode → Create BufferSource → Create GainNode
    ↓
Connect: BufferSource → GainNode → MasterGain → DuckingGain → Destination
    ↓
Fade in over 2 seconds
    ↓
Store in activeTracks Map
    ↓
If SFX: Duck music tracks
    ↓
Start progress tracking interval
```

### State Management

- **AudioEngine**: Manages Web Audio API, active sources, gain nodes
- **TrackArchiveState**: Handles favorites (localStorage), recently played (localStorage)
- **TrackArchiveUI**: DOM manipulation, event binding, filtering, progress updates

### Data Flow

```
PHP (track-archive.php)
    ↓
Query WP_Query('track') → Fetch ACF fields
    ↓
Render HTML with data-* attributes
    ↓
JavaScript (track-archive.js)
    ↓
Parse data-* attributes → Initialize trackCards Map
    ↓
Bind events → Play → AudioEngine → Update UI
```

---

## Customization

### Changing Fade Duration

In `track-archive.js`:

```javascript
this.fadeTime = 2.0; // Change to desired seconds
```

### Changing Duck Amount

```javascript
this.duckAmount = 0.3; // 0.3 = 30% volume
```

### Changing Recently Played Limit

```javascript
this.maxRecent = 8; // Change to desired number
```

### Adding New Filters

1. Add filter button in `track-archive.php`
2. Update `applyFilters()` method in `track-archive.js`

### Styling

All CSS variables are defined in `:root` in `track-archive.css`:

```css
:root {
    --cyber-primary: #00ff9f;    /* Change primary accent color */
    --bg-dark: #0a0e14;          /* Change background */
    --spacing-md: 1rem;          /* Adjust spacing */
}
```

---

## Performance Considerations

- **Audio Decoding**: Tracks are decoded on first play (cached in memory)
- **Progress Tracking**: Uses 100ms intervals (minimal CPU impact)
- **Filtering**: Client-side (instant, no server requests)
- **LocalStorage**: Favorites and recently played persist across sessions

---

## Accessibility

- Semantic HTML (`role="list"`, `role="listitem"`)
- ARIA labels on all interactive buttons
- Keyboard navigation support (`:focus-visible` styles)
- Respects `prefers-reduced-motion`
- Proper color contrast (WCAG AA compliant)

---

## Debugging

Enable debug mode by setting in browser console:

```javascript
window.godmindDebug = true;
```

Then reload the page. The audio engine, state, and UI controller will be exposed at:

```javascript
window.godmindTrackArchive.audioEngine
window.godmindTrackArchive.state
window.godmindTrackArchive.ui
```

---

## Troubleshooting

### Tracks Not Appearing

- Ensure tracks are **Published** (not Draft)
- Verify `audio_file` ACF field has a valid MP3 URL
- Check browser console for errors

### Audio Not Playing

- Web Audio API requires user interaction (click anywhere first)
- Check browser permissions for audio playback
- Verify MP3 file is accessible (not 404)

### Progress Bar Not Moving

- Ensure track is actively playing
- Check browser console for JavaScript errors
- Verify `audioContext` is running (not suspended)

### Volume Slider Not Working

- Make sure track is playing when adjusting volume
- Check that `gainNode` is connected properly
- Verify volume value is between 0-100

---

## Future Enhancements

### Potential Features

- **Playlists**: Create and save custom playlists
- **Crossfade**: Fade between music tracks instead of hard stop
- **EQ Controls**: Per-track equalizer settings
- **Hotkeys**: Keyboard shortcuts for play/pause/skip
- **Session Recorder**: Log all played tracks for post-session review
- **Remote Control**: WebSocket integration for multi-device sync
- **Waveform Visualization**: Canvas-based audio visualization

### Block Attributes (Post-Conversion)

```json
"attributes": {
  "defaultFilter": {
    "type": "string",
    "default": "all"
  },
  "showRecentlyPlayed": {
    "type": "boolean",
    "default": true
  },
  "maxTracks": {
    "type": "number",
    "default": -1
  }
}
```

---

## Support

For issues or feature requests, contact the development team or open an issue in the project repository.

---

## License

This code is part of the Godmind WordPress theme and follows the theme's licensing terms.
