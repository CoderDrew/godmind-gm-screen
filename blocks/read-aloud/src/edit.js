import { useBlockProps, RichText } from "@wordpress/block-editor";
import { Button } from "@wordpress/components";

export default function Edit({ attributes, setAttributes }) {
  const { content } = attributes;

  return (
    <div {...useBlockProps({ className: "read-aloud" })}>
      <div className="read-aloud__header">
        <span className="read-aloud__label">READ ALOUD</span>
      </div>

      <RichText
        tagName="div"
        className="read-aloud__content"
        value={content}
        onChange={(value) => setAttributes({ content: value })}
        placeholder="Enter text to read aloud to the players…"
        multiline="p"
      />
    </div>
  );
}
