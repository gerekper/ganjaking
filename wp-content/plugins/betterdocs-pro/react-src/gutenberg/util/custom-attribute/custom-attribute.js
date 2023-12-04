/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { Fragment } = wp.element;
const { compose, createHigherOrderComponent } = wp.compose;

function addAttributes(settings) {
	// Add custom selector/id
	if (typeof settings.attributes !== "undefined") {
		settings.attributes = {
			...settings.attributes,
			eb: { type: "object" }
		};
	}

	return settings;
}

const withAttributes = createHigherOrderComponent(BlockEdit => {
	return props => {
		const { attributes, setAttributes } = props;

		if (typeof attributes.eb === "undefined") {
			attributes.eb = [];
		}

		//add unique selector
		if (typeof attributes.eb.id === "undefined") {
			let d = new Date();

			if (
				typeof attributes.eb !== "undefined" &&
				typeof attributes.eb.id !== "undefined"
			) {
				delete attributes.eb.id;
			}

			const eb = Object.assign(
				{
					id:
						"" +
						d.getMonth() +
						d.getDate() +
						d.getHours() +
						d.getMinutes() +
						d.getSeconds() +
						d.getMilliseconds()
				},
				attributes.eb
			);
			setAttributes({ eb });
		}

		return (
			<Fragment>
				<BlockEdit {...props} />
			</Fragment>
		);
	};
}, "withAttributes");

addFilter("blocks.registerBlockType", "eb/button", addAttributes);

addFilter("editor.BlockEdit", "eb/button", withAttributes);
