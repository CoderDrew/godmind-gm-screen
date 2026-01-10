import { useBlockProps, InnerBlocks } from "@wordpress/block-editor";

export default function Save({ attributes }) {
  const { isOpen } = attributes;

  return (
    <div {...useBlockProps.save({ className: "gm-notes" })}>
      <div className="gm-notes__header">
        <span>GM NOTES</span>
      </div>

      {isOpen && (
        <div className="gm-notes__content">
          <InnerBlocks.Content />
        </div>
      )}
    </div>
  );
}