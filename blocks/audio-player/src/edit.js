import { useBlockProps } from '@wordpress/block-editor';
import { InspectorControls } from '@wordpress/editor';
import { PanelBody, SelectControl, Placeholder, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const { trackId } = attributes;

	// Fetch all Music/Track CPT posts
	const { tracks, isLoading } = useSelect((select) => {
		const { getEntityRecords, isResolving } = select('core');

		return {
			tracks: getEntityRecords('postType', 'track', { per_page: -1 }),
			isLoading: isResolving('core', 'getEntityRecords', ['postType', 'track', { per_page: -1 }]),
		};
	}, []);

	// Prepare options for SelectControl
	const trackOptions = [
		{ label: __('Select a track...', 'godmind'), value: 0 },
	];

	if (tracks && tracks.length > 0) {
		tracks.forEach((track) => {
			trackOptions.push({
				label: track.title.rendered,
				value: track.id,
			});
		});
	}

	// Get selected track details
	const selectedTrack = tracks?.find((track) => track.id === trackId);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Audio Player Settings', 'godmind')}>
					<SelectControl
						label={__('Select Music Track', 'godmind')}
						value={trackId}
						options={trackOptions}
						onChange={(value) => setAttributes({ trackId: parseInt(value) })}
						help={__('Choose a track from the Music CPT', 'godmind')}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...useBlockProps()}>
				{isLoading ? (
					<Placeholder icon="controls-volumeon" label={__('Scene Audio Player', 'godmind')}>
						<Spinner />
						<p>{__('Loading tracks...', 'godmind')}</p>
					</Placeholder>
				) : !trackId ? (
					<Placeholder
						icon="controls-volumeon"
						label={__('Scene Audio Player', 'godmind')}
						instructions={__('Select a music track from the block settings to display the audio player.', 'godmind')}
					>
						<SelectControl
							label={__('Select Track', 'godmind')}
							value={trackId}
							options={trackOptions}
							onChange={(value) => setAttributes({ trackId: parseInt(value) })}
						/>
					</Placeholder>
				) : (
					<div className="godmind-audio-player-editor-preview">
						<div style={{
							padding: '1rem',
							background: '#1a1a2e',
							border: '1px solid #16213e',
							borderRadius: '8px',
							color: '#eaeaea',
						}}>
							<div style={{ marginBottom: '0.75rem', paddingBottom: '0.75rem', borderBottom: '1px solid #16213e' }}>
								<strong>🎵 {selectedTrack?.title.rendered || 'Audio Player'}</strong>
							</div>
							<div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
								<div style={{
									width: '48px',
									height: '48px',
									borderRadius: '50%',
									background: '#0f3460',
									display: 'flex',
									alignItems: 'center',
									justifyContent: 'center',
									fontSize: '24px',
								}}>
									▶️
								</div>
								<div style={{ flex: 1 }}>
									<input
										type="range"
										min="0"
										max="100"
										value="70"
										disabled
										style={{ width: '100%', cursor: 'not-allowed' }}
									/>
								</div>
								<span>70%</span>
							</div>
							<p style={{
								marginTop: '0.75rem',
								fontSize: '0.875rem',
								color: '#a0a0a0',
								fontStyle: 'italic',
								marginBottom: 0,
							}}>
								Preview only • Full player will display on the front-end
							</p>
						</div>
					</div>
				)}
			</div>
		</>
	);
}
