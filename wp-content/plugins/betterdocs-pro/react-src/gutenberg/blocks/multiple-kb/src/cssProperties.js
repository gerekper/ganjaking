import * as BOX from "./constants";
import * as BOXTypo from "./typographyPrefixConstants";
import { addProperty } from "../../../helpers/helper";

export default ({ blockId }) => {
    const innerWrapperSelector = `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper .betterdocs-category-box-inner-wrapper`;
    const singleBoxSelector = `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper`;
    const iconSelector1 = `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-icon`;
    const iconSelector2 = `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-icon .betterdocs-category-icon-img`;
    const titleSelector = `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-title`;
    const titleHoverSelector = `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper:hover .betterdocs-category-title`;
    const countSelector1 = `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-items-counts`;
    const countSelector2 = `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper .betterdocs-category-items-counts span`;
    const countSelector3 = `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper .betterdocs-category-box-inner-wrapper .betterdocs-single-category-wrapper:hover .betterdocs-category-items-counts span`;
    return [
        addProperty("dimension", BOX.WRAPPER_MARGIN, {
            args: { styleFor: "margin" },
            cssSelector: `.${blockId}.betterdocs-category-box-wrapper.betterdocs-multiple-kb-wrapper`,
        }),

        addProperty("responsive", BOX.COLUMNS, {
            defaultData: {
                desktop: 3,
                tab: 2,
                mobile: 1,
            },
            args: { property: "" },
            class: true,
            attr: true,
            attrPrefix: "column",
            classPrefix: "betterdocs-column-",
            filter: "number",
            cssSelector: innerWrapperSelector,
        }),

        addProperty("dimension", BOX.BOX_PADDING, {
            args: { styleFor: "padding" },
            cssSelector: singleBoxSelector,
        }),

        addProperty("dimension", BOX.BOX_MARGIN, {
            args: { styleFor: "margin" },
            cssSelector: singleBoxSelector,
        }),

        addProperty("background", BOX.BOX_BACKGROUND, {
            args: { noOverlay: true },
            cssSelector: singleBoxSelector,
        }),

        addProperty("border", BOX.BOX_BORDER, {
            cssSelector: singleBoxSelector,
        }),

        addProperty("responsive", "iconAreaHeight", {
            args: { property: "height", controlName: BOX.ICON_AREA },
            cssSelector: iconSelector1,
        }),
        addProperty("responsive", "iconAreaWidth", {
            args: { property: "width", controlName: BOX.ICON_AREA },
            cssSelector: iconSelector1,
        }),
        addProperty("responsive", "iconAreaFlexBasis", {
            args: { property: "width", controlName: BOX.ICON_AREA },
            cssSelector: iconSelector1, //FIXME: selector needs to be changed. based on layout
        }),

        //should have custom unit like % for this controller
        addProperty("responsive", BOX.ICON_SIZE, {
            args: { property: "width" },
            cssSelector: iconSelector2,
        }),

        addProperty("background", BOX.ICON_BACKGROUND, {
            args: { noOverlay: true, noMainBgi: true },
            cssSelector: iconSelector1,
        }),

        addProperty("border", BOX.ICON_BORDER, {
            args: { noShadow: true },
            cssSelector: iconSelector1,
        }),

        addProperty("dimension", BOX.ICON_PADDING, {
            args: { styleFor: "padding" },
            cssSelector: iconSelector1,
        }),

        addProperty("dimension", BOX.ICON_MARGIN, {
            args: { styleFor: "margin" },
            cssSelector: iconSelector1,
        }),

        addProperty("typography", BOXTypo.typoPrefix_title, {
            args: {
                defaultFontSize: 20,
                prefixConstant: BOXTypo.typoPrefix_title,
            },
            cssSelector: titleSelector,
        }),

        addProperty("color", "titleColor", {
            args: { hover: "titleHoverColor" },
            cssSelector: titleSelector,
            hoverSelector: titleHoverSelector,
        }),

        addProperty("dimension", BOX.TITLE_MARGIN, {
            args: { styleFor: "margin" },
            cssSelector: titleSelector,
        }),

        addProperty("typography", BOXTypo.typoPrefix_count, {
            args: {
                defaultFontSize: 20,
                prefixConstant: BOXTypo.typoPrefix_count,
            },
            cssSelector: countSelector2,
        }),

        addProperty("color", "countColor", {
            args: { hover: "countHoverColor" },
            cssSelector: countSelector2,
            hoverSelector: countSelector3,
        }),

        addProperty("dimension", BOX.COUNT_MARGIN, {
            args: { styleFor: "margin" },
            cssSelector: countSelector1,
        }),
    ];
};
