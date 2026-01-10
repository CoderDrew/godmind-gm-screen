import { useBlockProps, InnerBlocks } from "@wordpress/block-editor";

export default function Edit() {
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
    ['core/paragraph', { placeholder: 'Enter text to read aloud to the players…' }]
  ];

  return (
    <div {...useBlockProps({ className: "read-aloud" })}>
      <div className="read-aloud__header">
        <span className="read-aloud__label">READ ALOUD</span>
      </div>

      <div className="read-aloud__content">
        <InnerBlocks
          allowedBlocks={ALLOWED_BLOCKS}
          template={TEMPLATE}
        />
      </div>
    </div>
  );
}
