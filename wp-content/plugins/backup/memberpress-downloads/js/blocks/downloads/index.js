const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { SelectControl } = wp.components;
const { downloads, categories, tags } = mpd;
const { ToggleControl, RadioControl, TextControl } = wp.components;
import './style.scss';
import MPPlaceholder from "../../../../memberpress/js/blocks/_global/components/mp-placeholder";

registerBlockType("memberpress/memberpress-download", {
  title: __("Download", "memberpress"),
  icon: "download", // https://developer.wordpress.org/resource/dashicons/
  category: "memberpress",
  description: __("Display a MemberPress download.", "memberpress"),
  keywords: [__("membership download", "memberpress")],
  attributes: {
    download: { type: "string" },
    isList: { type: "boolean" },
    listBy: { type: "string" },
    limit: { type: "string" },
    category: { type: "string" },
    tag: { type: "string" }
  },
  supports: {
    customClassName: false, // Removes "Custom CSS Class" from "Advanced" tab of block
    html: false // User cannot edit block as HTML
  },
  edit: function({ attributes, setAttributes, className }) {

    const { download, isList, listBy, limit, category, tag } = attributes;

    return [
      <div className={className}>
        <MPPlaceholder
          icon="download"
          label={ __("MemberPress Download", "memberpress") }
          instructions={__(
            "Display a MemberPress download or list of downloads.",
            "memberpress"
          )}
        >
          <ToggleControl
            label={ __( 'Display a Downloads list?', 'memberpress' ) }
            checked={ isList }
            onChange={ isList => setAttributes( { isList } ) }
          />
          {( ! isList &&
            <SelectControl
              label={__(
                "Select a Download",
                "memberpress"
              )}
              value={ download }
              options={[
                {
                  label: __("-- Select a Download", "memberpress"),
                  value: ""
                },
                ...downloads
              ]}
              onChange={ download => setAttributes( { download } ) }
            />
          )}
          {( isList &&
            <div>
              <TextControl
                label={ __( 'Limit:', 'memberpress' ) }
                help={ __( 'Leave empty to show all downloads.', 'memberpress' ) }
                className="mp-downloads-list-limit-input"
                value={ limit }
                type="number"
                onChange={ limit => setAttributes( { limit } ) }
              />
              <RadioControl
                label={__(
                  "List by:",
                  "memberpress"
                )}
                selected={ listBy }
                options={ [
                    { label: __( 'Category', 'memberpress' ), value: 'category' },
                    { label: __( 'Tag', 'memberpress' ), value: 'tag' },
                ] }
                onChange={ listBy => setAttributes( { listBy } ) }
              />
              {( 'category' == listBy &&
                <SelectControl
                  label={__(
                    "Download Category:",
                    "memberpress"
                  )}
                  value={ category }
                  options={[
                    {
                      label: __("-- Select a Category", "memberpress"),
                      value: ""
                    },
                    ...categories
                  ]}
                  onChange={ category => setAttributes( { category } ) }
                />
              )}
              {( 'tag' == listBy &&
                <SelectControl
                  label={__(
                    "Download Tag:",
                    "memberpress"
                  )}
                  value={ tag }
                  options={[
                    {
                      label: __("-- Select a Tag", "memberpress"),
                      value: ""
                    },
                    ...tags
                  ]}
                  onChange={ tag => setAttributes( { tag } ) }
                />
              )}
            </div>
          )}
        </MPPlaceholder>
      </div>
    ];
  },
  save: function() {
    return null; // Null because we're rendering the output serverside
  }
});
