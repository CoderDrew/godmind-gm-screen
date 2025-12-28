import { useBlockProps, RichText } from "@wordpress/block-editor";

export default function Save({ attributes }) {
  const { content, isOpen } = attributes;

  return (
    <div {...useBlockProps.save({ className: "gm-notes" })}>
      <div className="gm-notes__header">
        <span>GM NOTES</span>
      </div>

      {isOpen && (
        <RichText.Content
          tagName="div"
          className="gm-notes__content"
          value={content}
        />
      )}
    </div>
  );
}
