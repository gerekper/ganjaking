/**
 * WordPress dependeincies
 */
import { __ } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";

/**
 * Internal dependencies
 */
import Edit from "./edit";
import attributes from "./attributes";
import icon from "./icon";
/**
 * Import styles
 */
import "./style.scss";

registerBlockType("betterdocs/multiple-kb", {
    title: __("BetterDocs Multiple KB", "betterdocs-pro"),
    description: __(
        "Display your Knowledgebase in an amazing Box layout and style it any way you want.",
        "betterdocs"
    ),
    category: "betterdocs",
    attributes,
    icon,
    keywords: [
        __("better docs", "betterdocs"),
        __("multiple kb", "betterdocs-pro"),
    ],
    edit: Edit,
    save: () => null,
    example: {
        viewportWidth: 1200,
    },
});
