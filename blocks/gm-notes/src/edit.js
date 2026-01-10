import { useBlockProps, InnerBlocks } from "@wordpress/block-editor";
import { PanelBody, ToggleControl } from "@wordpress/components";
import { InspectorControls } from "@wordpress/editor";

export default function Edit({ attributes, setAttributes }) {
  const { isOpen = true } = attributes;

  const ALLOWED_BLOCKS = [
    'core/paragraph',
    'core/heading',
    'core/list',
    'core/image',
    'core/quote',
    'core/table',
    'core/separator',
    'core/spacer'
  ];

  const TEMPLATE = [
    ['core/paragraph', { placeholder: 'Private notes for the GM…' }]
  ];

  return (
    <>
      <InspectorControls>
        <PanelBody title="GM Notes Settings">
          <ToggleControl
            label="Expanded by default"
            checked={isOpen}
            onChange={(value) => setAttributes({ isOpen: value })}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps({ className: "gm-notes" })}>
        <div className="gm-notes__header">
          <span>GM NOTES</span>
        </div>

        {isOpen && (
          <div className="gm-notes__content">
            <InnerBlocks
              allowedBlocks={ALLOWED_BLOCKS}
              template={TEMPLATE}
            />
          </div>
        )}
      </div>
    </>
  );
}