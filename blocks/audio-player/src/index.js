import { registerBlockType } from '@wordpress/blocks';
import edit from './edit';

registerBlockType('godmind/audio-player', {
	edit,
	save: () => null, // Server-side render via render.php
});
