const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
  ...defaultConfig,
  entry: {
    'read-aloud/build/index': './blocks/read-aloud/src/index.js',
    'gm-notes/build/index': './blocks/gm-notes/src/index.js',
    'npc-cards/build/index': './blocks/npc-cards/src/index.js',
    'audio-player/build/index': './blocks/audio-player/src/index.js',
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'blocks'),
  },
};
