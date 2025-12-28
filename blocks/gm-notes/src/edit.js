import { useBlockProps, RichText } from "@wordpress/block-editor";
import { PanelBody, ToggleControl } from "@wordpress/components";
import { InspectorControls } from "@wordpress/editor";
import { useState } from "@wordpress/element";

export default function Edit({ attributes, setAttributes }) {
  const { content, isOpen = true } = attributes;

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
          <RichText
            tagName="div"
            className="gm-notes__content"
            value={content}
            onChange={(value) => setAttributes({ content: value })}
            placeholder="Private notes for the GM…"
            multiline="p"
          />
        )}
      </div>
    </>
  );
}
