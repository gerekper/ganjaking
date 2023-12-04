/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, BaseControl, ToggleControl, SelectControl, TabPanel } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { select, withSelect } from '@wordpress/data';

/**
 * Internal depencencies
 */

import objAttributes from "./attributes";
import { ORDER_BY, ORDER, LAYOUT, BOX_BACKGROUND } from "./constants";
import Select from "react-select";

import {
    COLUMNS,
    HTML_TAGS,
    BOX_MARGIN,
    BOX_PADDING,
    BOX_BORDER,
    TITLE_MARGIN,
    COUNT_MARGIN,
    ICON_AREA,
    ICON_SIZE,
    ICON_BACKGROUND,
    ICON_BORDER,
    ICON_PADDING,
    ICON_MARGIN,
    WRAPPER_MARGIN,
} from "./constants";
import {
    typoPrefix_title,
    typoPrefix_count,
} from "./typographyPrefixConstants";

import ResponsiveRangeController from "../../../util/responsive-range-control";
import ResponsiveDimensionsControl from "../../../util/dimensions-control-v2";
import TypographyDropdown from "../../../util/typography-control-v2";
import BackgroundControl from "../../../util/background-control";
import BorderShadowControl from "../../../util/border-shadow-control";
import ColorControl from "../../../util/color-control";

const Inspector = ({ docCategories, attributes, setAttributes }) => {
    const {
        resOption,
        includeCategories,
        excludeCategories,
        boxPerPage,
        orderBy,
        order,
        layout,
        showIcon,
        showTitle,
        titleTag,
        showCount,
        prefix,
        suffix,
        suffixSingular,
        titleColor,
        titleHoverColor,
        countColor,
        countHoverColor,
    } = attributes;
    console.log()
    const editorStoreForGettingPreivew =
        betterdocs_style_handler.editor_type === "edit-site"
            ? "core/edit-site"
            : "core/edit-post";

    useEffect(() => {
        // this is for setting the resOption attribute to desktop/tab/mobile depending on the added 'eb-res-option-' class only the first time once
        setAttributes({
            resOption: select(
                editorStoreForGettingPreivew
            ).__experimentalGetPreviewDeviceType(),
        });
    }, []);

    const resRequiredProps = {
        setAttributes,
        resOption,
        attributes,
        objAttributes,
    };

    // handle include categories
    const handleIncludeCategories = (categories) => {
        setAttributes({
            includeCategories: categories ? JSON.stringify(categories) : null,
        });
    };
    // handle exclude categories
    const handleExcludeCategories = (categories) => {
        setAttributes({
            excludeCategories: categories ? JSON.stringify(categories) : null,
        });
    };

    return (
        <InspectorControls key="controls">
            <div className="eb-panel-control">
                <TabPanel
                    className="eb-parent-tab-panel"
                    activeClass="active-tab"
                    tabs={[
                        {
                            name: "general",
                            title: __("General", "betterdocs"),
                            className: "eb-tab general",
                        },
                        {
                            name: "styles",
                            title: __("Style", "betterdocs"),
                            className: "eb-tab styles",
                        },
                        {
                            name: "advance",
                            title: __("Advanced", "betterdocs"),
                            className: "eb-tab styles",
                        },
                    ]}
                >
                    {(tab) => (
                        <div className={"eb-tab-controls " + tab.name}>
                            {tab.name === "general" && (
                                <>
                                    <PanelBody title={__("Query", "better-docs")}>
                                        <>
                                            <BaseControl>
                                                <h3 className="eb-control-title">
                                                    {__("Include", "better-docs")}
                                                </h3>
                                            </BaseControl>
                                            <Select
                                                name="include_categories"
                                                value={
                                                    includeCategories && JSON.parse(includeCategories)
                                                }
                                                onChange={handleIncludeCategories}
                                                options={
                                                    docCategories &&
                                                    docCategories.map((category) => ({
                                                        value: category.id,
                                                        label: category.name,
                                                    }))
                                                }
                                                isMulti="true"
                                            />
                                            <BaseControl>
                                                <h3 className="eb-control-title">
                                                    {__("Exclude", "betterdocs")}
                                                </h3>
                                            </BaseControl>
                                            <Select
                                                name="exclude_categories"
                                                value={
                                                    excludeCategories && JSON.parse(excludeCategories)
                                                }
                                                onChange={handleExcludeCategories}
                                                options={
                                                    docCategories &&
                                                    docCategories.map((category) => ({
                                                        value: category.id,
                                                        label: category.name,
                                                    }))
                                                }
                                                isMulti="true"
                                            />
                                            <TextControl
                                                label={__("Box Per Page", "betterdocs")}
                                                type="number"
                                                value={boxPerPage}
                                                onChange={(value) => {
                                                    setAttributes({
                                                        boxPerPage: parseInt(value),
                                                    });
                                                }}
                                            />
                                            <SelectControl
                                                label={__("Order By", "betterdocs")}
                                                value={orderBy}
                                                options={ORDER_BY}
                                                onChange={(newOrderby) =>
                                                    setAttributes({
                                                        orderBy: newOrderby,
                                                    })
                                                }
                                            />
                                            <SelectControl
                                                label={__("Order", "betterdocs")}
                                                value={order}
                                                options={ORDER}
                                                onChange={(newOrder) =>
                                                    setAttributes({
                                                        order: newOrder,
                                                    })
                                                }
                                            />
                                        </>
                                    </PanelBody>
                                    <PanelBody title={__("Settings", "betterdocs")}>
                                        <>
                                            <SelectControl
                                                label={__("Select layout", "betterdocs")}
                                                value={layout}
                                                options={LAYOUT}
                                                onChange={(newLayout) =>
                                                    setAttributes({
                                                        layout: newLayout,
                                                    })
                                                }
                                            />
                                            <ResponsiveRangeController
                                                baseLabel={__("Box Column", "betterdocs")}
                                                controlName={COLUMNS}
                                                resRequiredProps={resRequiredProps}
                                                min={1}
                                                max={6}
                                                step={1}
                                                noUnits
                                            />
                                            <ToggleControl
                                                label={__("Show Icon", "betterdocs")}
                                                checked={showIcon}
                                                onChange={() => {
                                                    setAttributes({
                                                        showIcon: !showIcon,
                                                    });
                                                }}
                                            />
                                            <ToggleControl
                                                label={__("Show Title", "betterdocs")}
                                                checked={showTitle}
                                                onChange={() => {
                                                    setAttributes({
                                                        showTitle: !showTitle,
                                                    });
                                                }}
                                            />
                                            <SelectControl
                                                label={__("Select Tag", "betterdocs")}
                                                value={titleTag}
                                                options={HTML_TAGS}
                                                onChange={(newTitleTag) =>
                                                    setAttributes({
                                                        titleTag: newTitleTag,
                                                    })
                                                }
                                            />
                                            <ToggleControl
                                                label={__("Show Count", "betterdocs")}
                                                checked={showCount}
                                                onChange={() => {
                                                    setAttributes({
                                                        showCount: !showCount,
                                                    });
                                                }}
                                            />
                                            {showCount && (
                                                <>
                                                    <TextControl
                                                        label={__("Prefix", "betterdocs")}
                                                        value={prefix}
                                                        onChange={(prefix) =>
                                                            setAttributes({
                                                                prefix,
                                                            })
                                                        }
                                                    />
                                                    <TextControl
                                                        label={__("Suffix", "betterdocs")}
                                                        value={suffix}
                                                        onChange={(suffix) =>
                                                            setAttributes({
                                                                suffix,
                                                            })
                                                        }
                                                    />
                                                    <TextControl
                                                        label={__("Suffix Singular", "betterdocs")}
                                                        value={suffixSingular}
                                                        onChange={(suffixSingular) =>
                                                            setAttributes({
                                                                suffixSingular,
                                                            })
                                                        }
                                                    />
                                                </>
                                            )}
                                        </>
                                    </PanelBody>
                                </>
                            )}
                            {tab.name === "styles" && (
                                <>
                                    <PanelBody title={__("Box", "betterdocs")}>
                                        <ResponsiveDimensionsControl
                                            baseLabel={__("Margin", "betterdocs")}
                                            resRequiredProps={resRequiredProps}
                                            controlName={BOX_MARGIN}
                                        />
                                        <ResponsiveDimensionsControl
                                            baseLabel={__("Padding", "betterdocs")}
                                            resRequiredProps={resRequiredProps}
                                            controlName={BOX_PADDING}
                                        />
                                        <BaseControl>
                                            <h3 className="eb-control-title">
                                                {__("Background", "betterdocs")}
                                            </h3>
                                        </BaseControl>
                                        <BackgroundControl
                                            controlName={BOX_BACKGROUND}
                                            resRequiredProps={resRequiredProps}
                                            noOverlay={true}
                                        />
                                        <BaseControl>
                                            <h3 className="eb-control-title">
                                                {__("Border", "betterdocs")}
                                            </h3>
                                        </BaseControl>
                                        <BorderShadowControl
                                            controlName={BOX_BORDER}
                                            resRequiredProps={resRequiredProps}
                                        />
                                    </PanelBody>
                                    <PanelBody
                                        title={__("Icon", "betterdocs")}
                                        initialOpen={false}
                                    >
                                        <>
                                            <ResponsiveRangeController
                                                baseLabel={__("Icon Area", "betterdocs")}
                                                controlName={ICON_AREA}
                                                resRequiredProps={resRequiredProps}
                                                min={1}
                                                max={500}
                                                step={1}
                                            />
                                            <ResponsiveRangeController
                                                baseLabel={__("Icon Size", "betterdocs")}
                                                controlName={ICON_SIZE}
                                                resRequiredProps={resRequiredProps}
                                                min={1}
                                                max={500}
                                                step={1}
                                            />
                                            <BaseControl>
                                                <h3 className="eb-control-title">
                                                    {__("Background", "betterdocs")}
                                                </h3>
                                            </BaseControl>
                                            <BackgroundControl
                                                controlName={ICON_BACKGROUND}
                                                resRequiredProps={resRequiredProps}
                                                noOverlay={true}
                                                noMainBgi={true}
                                            />
                                            <BaseControl>
                                                <h3 className="eb-control-title">
                                                    {__("Border", "betterdocs")}
                                                </h3>
                                            </BaseControl>
                                            <BorderShadowControl
                                                controlName={ICON_BORDER}
                                                resRequiredProps={resRequiredProps}
                                                noShadow={true}
                                            />
                                            <ResponsiveDimensionsControl
                                                baseLabel={__("Padding", "betterdocs")}
                                                resRequiredProps={resRequiredProps}
                                                controlName={ICON_PADDING}
                                            />
                                            <ResponsiveDimensionsControl
                                                baseLabel={__("Margin", "betterdocs")}
                                                resRequiredProps={resRequiredProps}
                                                controlName={ICON_MARGIN}
                                            />
                                        </>
                                    </PanelBody>
                                    <PanelBody
                                        title={__("Title", "betterdocs")}
                                        initialOpen={false}
                                    >
                                        <TypographyDropdown
                                            baseLabel={__("Typography", "betterdocs")}
                                            typographyPrefixConstant={typoPrefix_title}
                                            resRequiredProps={resRequiredProps}
                                            defaultFontSize={20}
                                        />
                                        <ColorControl
                                            label={__("Color", "betterdocs")}
                                            color={titleColor}
                                            onChange={(titleColor) => setAttributes({ titleColor })}
                                        />
                                        <ColorControl
                                            label={__("Hover Color", "betterdocs")}
                                            color={titleHoverColor}
                                            onChange={(titleHoverColor) =>
                                                setAttributes({
                                                    titleHoverColor,
                                                })
                                            }
                                        />
                                        <ResponsiveDimensionsControl
                                            baseLabel={__("Spacing", "betterdocs")}
                                            resRequiredProps={resRequiredProps}
                                            controlName={TITLE_MARGIN}
                                        />
                                    </PanelBody>
                                    <PanelBody
                                        title={__("Count", "betterdocs")}
                                        initialOpen={false}
                                    >
                                        <TypographyDropdown
                                            baseLabel={__("Typography", "betterdocs")}
                                            typographyPrefixConstant={typoPrefix_count}
                                            resRequiredProps={resRequiredProps}
                                            defaultFontSize={15}
                                        />
                                        <ColorControl
                                            label={__("Color", "betterdocs")}
                                            color={countColor}
                                            onChange={(countColor) => setAttributes({ countColor })}
                                        />
                                        <ColorControl
                                            label={__("Hover Color", "betterdocs")}
                                            color={countHoverColor}
                                            onChange={(countHoverColor) =>
                                                setAttributes({
                                                    countHoverColor,
                                                })
                                            }
                                        />
                                        <ResponsiveDimensionsControl
                                            baseLabel={__("Spacing", "betterdocs")}
                                            resRequiredProps={resRequiredProps}
                                            controlName={COUNT_MARGIN}
                                        />
                                    </PanelBody>
                                </>
                            )}
                            {tab.name === "advance" && (
                                <PanelBody
                                    title={__("Wrapper", "betterdocs")}
                                    initialOpen={false}
                                >
                                    <ResponsiveDimensionsControl
                                        baseLabel={__("Margin", "betterdocs")}
                                        resRequiredProps={resRequiredProps}
                                        controlName={WRAPPER_MARGIN}
                                    />
                                </PanelBody>
                            )}
                        </div>
                    )}
                </TabPanel>
            </div>
        </InspectorControls>
    );
};

export default withSelect((select) => {
    return {
        docCategories: select("core").getEntityRecords("taxonomy", "knowledge_base", {
            per_page: -1,
        }),
    };
})(Inspector);
