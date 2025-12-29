# Scene Audio Player Block

Custom Gutenberg block for playing background music, ambient audio, and SFX on Scene pages in the Godmind FSE theme.

---

## 🚀 Quick Start

### 1. Add the Block

1. Edit any **Scene** in WordPress
2. Click **+** to add a block
3. Search for **"Scene Audio Player"**
4. Insert the block

### 2. Select a Track

- Use the dropdown in the block
- **OR** use the Settings sidebar → **Audio Player Settings**

### 3. Publish

The audio player will render on the front-end with play/pause, volume control, and loop behavior.

---

## 📦 Features

✅ Gutenberg block for FSE
✅ Track selector dropdown (Music CPT)
✅ Editor preview
✅ Server-side rendering
✅ Play/pause control
✅ Volume slider (0-100%)
✅ Auto loop detection (ambient tracks)
✅ Cyberpunk styling
✅ No jQuery, no dependencies

---

## 🎵 Music CPT Requirements

The block requires these ACF fields on the Music CPT (`track`):

| Field Name | Type | Return Format | Purpose |
|------------|------|---------------|---------|
| `audio_file` | File | **Array** | Audio file URL |
| `track_type` | Radio | - | music \| ambient \| sfx |
| `audio_loop` | True/False | - | Override loop behavior (optional) |
| `audio_volume` | Number | - | Initial volume 0-100 (default 70) |

### Loop Behavior Logic

```
IF audio_loop is explicitly set
  → Use that value
ELSE
  IF track_type = "ambient"
    → Loop = true
  ELSE (music/sfx)
    → Loop = false
```

---

## 📁 File Structure

```
blocks/audio-player/
├── block.json              — Block metadata
├── render.php              — Server-side rendering
├── src/
│   ├── index.js           — Block registration
│   ├── edit.js            — Editor UI
│   └── editor.css         — Editor styles
└── build/
    ├── index.js           — Compiled JS
    ├── index.css          — Compiled CSS
    └── index.asset.php    — Dependencies

inc/audio-player.php        — Helper functions
template-parts/audio-player.php — HTML template
assets/css/audio-player.css — Front-end styles
assets/js/audio-player.js   — Front-end JavaScript
```

---

## 🔧 Development

### Making Changes

**Editor UI (requires rebuild):**
```bash
# Edit these files:
blocks/audio-player/src/edit.js
blocks/audio-player/src/editor.css

# Then rebuild:
npm run build
```

**Front-end Player (no rebuild needed):**
```bash
# Just edit and refresh:
template-parts/audio-player.php    # HTML
assets/css/audio-player.css        # Styles
assets/js/audio-player.js          # Behavior
```

### Building

```bash
cd wp-content/themes/godmind
npm run build
```

---

## 🎨 Customization

### Change Block Title/Icon

Edit `blocks/audio-player/block.json`:

```json
{
  "title": "Background Music",
  "icon": "format-audio"
}
```

### Customize Player Styles

Edit `assets/css/audio-player.css`:

```css
.godmind-audio-player {
  --player-bg: #1a1a2e;
  --player-highlight: #e94560;
  /* ... */
}
```

### Modify Editor Preview

Edit `blocks/audio-player/src/edit.js` — find the `godmind-audio-player-editor-preview` div.

---

## 🐛 Troubleshooting

### Block doesn't appear in editor

```bash
npm run build
# Then clear browser cache
```

### No tracks in dropdown

- Create Music CPT posts
- Verify `show_in_rest: true` in [inc/post-types/track.php:16](inc/post-types/track.php#L16)

### Player doesn't show on front-end

- Track must have `audio_file` field set
- `audio_file` return format must be "Array" (not URL)
- Check browser console for errors

### Audio doesn't play

- Verify audio file URL is accessible
- Use MP3 format for best browser support
- Check browser console for MediaError

---

## ⚡ Performance

| Asset | Size |
|-------|------|
| Block JS (editor) | ~2.6KB |
| Block CSS (editor) | ~0.2KB |
| Front-end CSS | ~5KB |
| Front-end JS | ~5KB |
| **Total** | **~13KB** |

Front-end assets only load on Scene pages (`is_singular('scene')`).

---

## 📝 Block Details

| Property | Value |
|----------|-------|
| **Name** | `godmind/audio-player` |
| **Category** | godmind |
| **Icon** | controls-volumeon |
| **Multiple** | No (one per page) |

### Attributes

```json
{
  "trackId": {
    "type": "number",
    "default": 0
  }
}
```

---

## 🎯 Next Steps

1. **Create Music Tracks:**
   - Go to **Tracks** → **Add New**
   - Set title (e.g., "Cyberpunk Street Ambience")
   - Upload audio file to `audio_file` ACF field
   - Set `track_type` (ambient/music/sfx)
   - Publish

2. **Add to Scenes:**
   - Edit a Scene
   - Insert **Scene Audio Player** block
   - Select a track
   - Publish

3. **Test:**
   - View the Scene page
   - Click play
   - Adjust volume
   - Verify loop behavior

---

## 🔐 Security

All output is properly escaped:
- `esc_url()` for audio URLs
- `esc_html()` for track titles
- `esc_attr()` for HTML attributes

---

**Your audio player is ready! 🎵**
