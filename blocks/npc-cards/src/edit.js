import { useBlockProps } from "@wordpress/block-editor";
import { TextControl } from "@wordpress/components";

export default function Edit({ attributes, setAttributes }) {
  const { npcId } = attributes;

  return (
    <div {...useBlockProps({ className: "gm-npc-card gm-npc-card--editor" })}>
      <strong>NPC Card</strong>

      <div style={{ marginTop: "0.5rem", maxWidth: "220px" }}>
        <TextControl
          label="NPC ID"
          type="number"
          value={npcId || ""}
          onChange={(value) =>
            setAttributes({
              npcId: value ? parseInt(value, 10) : null,
            })
          }
          help="Enter the NPC post ID"
        />
      </div>

      <p style={{ opacity: 0.8 }}>
        {npcId ? `Selected NPC ID: ${npcId}` : "No NPC selected."}
      </p>
    </div>
  );
}
