import { useBlockProps } from "@wordpress/block-editor";
import { SelectControl, Spinner } from "@wordpress/components";
import { useSelect } from "@wordpress/data";

export default function Edit({ attributes, setAttributes }) {
  const { npcId } = attributes;

  // Fetch all NPC posts
  const { npcs, isLoading } = useSelect((select) => {
    const { getEntityRecords, isResolving } = select("core");

    return {
      npcs: getEntityRecords("postType", "npc", { per_page: -1, orderby: "title", order: "asc" }),
      isLoading: isResolving("core", "getEntityRecords", ["postType", "npc", { per_page: -1, orderby: "title", order: "asc" }]),
    };
  }, []);

  // Build options for the select control
  const npcOptions = [
    { label: "— Select an NPC —", value: "" },
    ...(npcs || []).map((npc) => ({
      label: npc.title.rendered,
      value: npc.id.toString(),
    })),
  ];

  return (
    <div {...useBlockProps({ className: "gm-npc-card gm-npc-card--editor" })}>
      <strong>NPC Card</strong>

      {isLoading ? (
        <div style={{ padding: "1rem", textAlign: "center" }}>
          <Spinner />
        </div>
      ) : (
        <div style={{ marginTop: "0.5rem" }}>
          <SelectControl
            label="Select NPC"
            value={npcId ? npcId.toString() : ""}
            options={npcOptions}
            onChange={(value) =>
              setAttributes({
                npcId: value ? parseInt(value, 10) : null,
              })
            }
            help={npcs && npcs.length === 0 ? "No NPCs found. Create an NPC post first." : ""}
          />
        </div>
      )}

      <p style={{ opacity: 0.8, marginTop: "0.5rem" }}>
        {npcId ? `Selected NPC ID: ${npcId}` : "No NPC selected."}
      </p>
    </div>
  );
}
