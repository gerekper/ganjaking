import { __ } from "@wordpress/i18n";

export const ORDER_BY = [
    { label: __("Name", "betterdocs"), value: "name" },
    { label: __("Slug", "betterdocs"), value: "slug" },
    { label: __("Term Group", "betterdocs"), value: "term_group" },
    { label: __("ID", "betterdocs"), value: "id" },
    { label: __("Description", "betterdocs"), value: "description" },
    { label: __("Count", "betterdocs"), value: "count" },
    {
        label: __("Knowledgebase Order", "betterdocs"),
        value: "kb_order",
    },
];

export const ORDER = [
    { label: __("Ascending", "betterdocs"), value: "asc" },
    { label: __("Descending", "betterdocs"), value: "desc" },
];

export const LAYOUT = [
    { label: __("Default", "betterdocs"), value: "default" },
    { label: __("Layout 2", "betterdocs"), value: "layout-2" },
];

export const HTML_TAGS = [
    { label: __("H1", "betterdocs"), value: "h1" },
    { label: __("H2", "betterdocs"), value: "h2" },
    { label: __("H3", "betterdocs"), value: "h3" },
    { label: __("H4", "betterdocs"), value: "h4" },
    { label: __("H5", "betterdocs"), value: "h5" },
    { label: __("H6", "betterdocs"), value: "h6" },
    { label: __("span", "betterdocs"), value: "span" },
    { label: __("p", "betterdocs"), value: "p" },
    { label: __("div", "betterdocs"), value: "div" },
];

// responsive range controller
export const COLUMNS = "col";
export const ICON_AREA = "iconArea";
export const ICON_SIZE = "iconSize";

// dimension control
export const BOX_MARGIN = "boxMargin";
export const BOX_PADDING = "boxPadding";
export const TITLE_MARGIN = "titleMargin";
export const COUNT_MARGIN = "countMargin";
export const ICON_MARGIN = "iconMargin";
export const ICON_PADDING = "iconPadding";
export const WRAPPER_MARGIN = "wrpPadding";

// background controls
export const BOX_BACKGROUND = "boxBack";
export const ICON_BACKGROUND = "iconBack";

// border shadow attriubtes
export const BOX_BORDER = "boxBrd";
export const ICON_BORDER = "iconBrd";
