import { useBlockProps, InnerBlocks } from "@wordpress/block-editor";

export default function Save() {
  return (
    <div {...useBlockProps.save({ className: "read-aloud" })}>
      <button className="read-aloud__header" type="button" aria-expanded="false">
        <span className="read-aloud__label">READ ALOUD</span>
        <svg className="read-aloud__icon" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" strokeWidth="2">
          <polyline points="4 6 8 10 12 6"></polyline>
        </svg>
      </button>

      <div className="read-aloud__content">
        <InnerBlocks.Content />
      </div>
    </div>
  );
}
