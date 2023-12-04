import { addFilter } from "@wordpress/hooks";
import { PanelBody, ToggleControl, BaseControl, TextControl } from "@wordpress/components";
import { typoPrefix_categorySearch, typoPrefix_searchButton, typoPrefix_popularTitle, typoPrefix_keyword } from "./typographyPrefixConstants";
import { generateTypographyStyles, generateBorderShadowStyles } from "../../util/helpers";
import TypographyDropdown from './../../util/typography-control-v2';
import BorderShadowControl from "../../util/border-shadow-control";
import ColorControl from '../../util/color-control/index';
import ResponsiveDimensionsControl from "../../util/dimensions-control-v2";
import { generateDimensionsControlStyles } from "../../util/helpers";
import { SEARCH_BUTTON_BORDER, SEARCH_BUTTON_PADDING, POPULAR_SEARCH_MARGIN, POPULAR_SEARCH_KEYWORD_BORDER, POPULAR_SEARCH_KEYWORD_PADDING, POPULAR_SEARCH_KEYWORD_MARGIN } from "./constants";
import { __ } from "@wordpress/i18n";

addFilter(
    "searchCategoryFilter",
    "betterdocs_advanced_search",
    (defaultOutput) => {
        return <select class="betterdocs-search-category" onChange={() => null}>
                 <option value="all-categories">All Categories</option>
              </select>;
    },
    10
);

addFilter("searchButtonFilter", "betterdocs_advanced_search", (defaultOutput) => {
    return  <input
                class="search-submit"
                type="submit"
                value="Search"
                disabled
            ></input>;
});

addFilter(
    "advancedSearchPanel",
    "betterdocs_advanced_search",
    (defaultOutput,[setAttributes, attributes, resOption, objAttributes]) => {
        let { categorySearch, searchButton, popularSearch, categoryTextColor, searchButtonTextColor, searchButtonBackgroundColor, searchButtonBackgroundColorHover, popularSearchText, popularTitleColor, popularKeywordBackgroundColor, popularKeywordTextColor } = attributes;
        let resRequiredProps = {
            setAttributes,
            resOption,
            attributes,
            objAttributes
        }
        return (
            <PanelBody
                title={__("Advanced Search", "betterdocs-pro")}
                initialOpen={false}
            >
                <ToggleControl
                    label={__("Enable Category Search", "betterdocs-pro")}
                    checked={categorySearch}
                    onChange={() => {
                        setAttributes({
                            categorySearch: !categorySearch,
                        })
                    }}
                />
                <ToggleControl
                    label={__("Enable Search Button", "betterdocs-pro")}
                    checked={searchButton}
                    onChange={() => {
                        setAttributes({
                            searchButton: !searchButton,
                        })
                    }}
                />
                  <ToggleControl
                    label={__("Enable Popular Search", "betterdocs-pro")}
                    checked={popularSearch}
                    onChange={() => {
                        setAttributes({
                            popularSearch: !popularSearch,
                        })
                    }}
                />
                 <BaseControl>
                        <h3 className="eb-control-title">
                          {__("Category Search", "betterdocs-pro")}
                        </h3>
                </BaseControl>
                <TypographyDropdown
                    baseLabel={__("Typography", "betterdocs-pro")}
                    typographyPrefixConstant={typoPrefix_categorySearch}
                    resRequiredProps={resRequiredProps}
                    defaultFontSize={14}
                />
                 <ColorControl
                        label={__("Category Text Color", "betterdocs-pro")}
                        color={categoryTextColor}
                        onChange={(value) =>
                          setAttributes({
                            categoryTextColor:value
                          })
                        }
                />
                <BaseControl>
                    <h3 className="eb-control-title">
                        {__("Search Button", "betterdocs-pro")}
                    </h3>
                </BaseControl>
                <TypographyDropdown
                    baseLabel={__("Typography", "betterdocs-pro")}
                    typographyPrefixConstant={typoPrefix_searchButton}
                    resRequiredProps={resRequiredProps}
                    defaultFontSize={18}
                />
                <ColorControl
                    label={__("Text Color", "betterdocs-pro")}
                    color={searchButtonTextColor}
                    onChange={(value) =>
                        setAttributes({
                        searchButtonTextColor:value
                        })
                    }
                />
                 <ColorControl
                    label={__("Background Color", "betterdocs-pro")}
                    color={searchButtonBackgroundColor}
                    onChange={(value) =>
                        setAttributes({
                            searchButtonBackgroundColor:value
                        })
                    }
                />
                 <ColorControl
                    label={__("Background Color Hover", "betterdocs-pro")}
                    color={searchButtonBackgroundColorHover}
                    onChange={(value) =>
                        setAttributes({
                            searchButtonBackgroundColorHover:value
                        })
                    }
                />
                 <BorderShadowControl
                    controlName={SEARCH_BUTTON_BORDER}
                    resRequiredProps={resRequiredProps}
                    noShadow={true}
                    noBdrHover={true}
                />
                <ResponsiveDimensionsControl
                    baseLabel={__("Padding", "betterdocs")}
                    resRequiredProps={resRequiredProps}
                    controlName={SEARCH_BUTTON_PADDING}
                />
                <BaseControl>
                    <h3 className="eb-control-title">
                        {__("Popular Search", "betterdocs-pro")}
                    </h3>
                </BaseControl>
                <ResponsiveDimensionsControl
                    baseLabel={__("Margin", "betterdocs")}
                    resRequiredProps={resRequiredProps}
                    controlName={POPULAR_SEARCH_MARGIN}
                />
                 <TextControl
                        label={__("Placeholder", "betterdocs")}
                        value={popularSearchText}
                        onChange={(value) => {
                          setAttributes({
                            popularSearchText: value,
                          });
                        }}
                />
                  <ColorControl
                    label={__("Title Color", "betterdocs-pro")}
                    color={popularTitleColor}
                    onChange={(value) =>
                        setAttributes({
                            popularTitleColor:value
                        })
                    }
                />
                  <TypographyDropdown
                    baseLabel={__("Typography", "betterdocs-pro")}
                    typographyPrefixConstant={typoPrefix_popularTitle}
                    resRequiredProps={resRequiredProps}
                    defaultFontSize={14}
                />
                 <TypographyDropdown
                    baseLabel={__("Keyword Typography", "betterdocs-pro")}
                    typographyPrefixConstant={typoPrefix_keyword}
                    resRequiredProps={resRequiredProps}
                    defaultFontSize={14}
                />
                  <ColorControl
                    label={__("Keyword Background Color", "betterdocs-pro")}
                    color={popularKeywordBackgroundColor}
                    onChange={(value) =>
                        setAttributes({
                            popularKeywordBackgroundColor:value
                        })
                    }
                />
                <ColorControl
                    label={__("Keyword Text Color", "betterdocs-pro")}
                    color={popularKeywordTextColor}
                    onChange={(value) =>
                        setAttributes({
                            popularKeywordTextColor:value
                        })
                    }
                />
                <BorderShadowControl
                    controlName={POPULAR_SEARCH_KEYWORD_BORDER}
                    resRequiredProps={resRequiredProps}
                    noShadow={true}
                    noBdrHover={true}
                />
                <ResponsiveDimensionsControl
                    baseLabel={__("Padding", "betterdocs")}
                    resRequiredProps={resRequiredProps}
                    controlName={POPULAR_SEARCH_KEYWORD_PADDING}
                />
                <ResponsiveDimensionsControl
                    baseLabel={__("Margin", "betterdocs")}
                    resRequiredProps={resRequiredProps}
                    controlName={POPULAR_SEARCH_KEYWORD_MARGIN}
                />
            </PanelBody>
        );
    }
);

addFilter('popularSearchKeyword', 'betterdocs_advanced_search', (defaultOutput,[attributes]) => {
    return <div class="betterdocs-popular-search-keyword">
                <span class="popular-search-title">{attributes?.popularSearchText}</span>
                <span class="popular-keyword">keyword 1</span>
                <span class="popular-keyword">keyword 2</span>
                <span class="popular-keyword">keyword 3</span>
                <span class="popular-keyword">keyword 4</span>
            </div>
});

addFilter('filterStyles', 'betterdocs_advanced_search', (defaultOutput, [styleObject, attributes, blockId]) => {
    let { desktop, mobile, tab } = styleObject;
    let { categoryTextColor, searchButtonTextColor, searchButtonBackgroundColor, searchButtonBackgroundColorHover, popularTitleColor, popularKeywordBackgroundColor, popularKeywordTextColor } = attributes;

    let modStyleObject;

    let {
        typoStylesDesktop: categorySearchTypoStylesDesktop,
        typoStylesTab: categorySearchTypoStylesTab,
        typoStylesMobile: categorySearchTypoStylesMobile,
    } = generateTypographyStyles({
        attributes,
        prefixConstant: typoPrefix_categorySearch,
        defaultFontSize: 14,
    });

    let {
        typoStylesDesktop: searchButtonTypoStylesDesktop,
        typoStylesTab: searchButtonTypoStylesTab,
        typoStylesMobile: searchButtonTypoStylesMobile,
    } = generateTypographyStyles({
        attributes,
        prefixConstant: typoPrefix_searchButton,
        defaultFontSize: 18,
    });

    let {
        styesDesktop:searhButtonBorderRadiusDesk,
        styesTab:searhButtonBorderRadiusTab,
        styesMobile:searhButtonBorderRadiusMobile,
    } = generateBorderShadowStyles({
        controlName: SEARCH_BUTTON_BORDER,
        noShadow: true,
        noBdrHove:true,
        attributes,
    });

    const {
        dimensionStylesDesktop: searchButtonPaddingDesktop,
        dimensionStylesTab: searchButtonPaddingTab,
        dimensionStylesMobile: searchButtonPaddingMobile,
    } = generateDimensionsControlStyles({
        controlName: SEARCH_BUTTON_PADDING,
        styleFor: "padding",
        attributes,
    });

    const {
        dimensionStylesDesktop: popularSearchMarginDesktop,
        dimensionStylesTab: popularSearchMarginTab,
        dimensionStylesMobile: popularSearchMarginMobile,
    } = generateDimensionsControlStyles({
        controlName: POPULAR_SEARCH_MARGIN,
        styleFor: "margin",
        attributes,
    });

    let {
        typoStylesDesktop: popularTitleTypoDesktop,
        typoStylesTab: popularTitleTypoTab,
        typoStylesMobile: popularTitleTypoMob,
    } = generateTypographyStyles({
        attributes,
        prefixConstant: typoPrefix_popularTitle,
        defaultFontSize: 14,
    });

    let {
        typoStylesDesktop: keywordDesktop,
        typoStylesTab: keywordTab,
        typoStylesMobile: keywordMobile,
    } = generateTypographyStyles({
        attributes,
        prefixConstant:typoPrefix_keyword,
        defaultFontSize: 14,
    })

    let {
        styesDesktop:popularSearchKewordBorderDesktop,
        styesTab:popularSearchKewordBorderTab,
        styesMobile:popularSearchKewordBorderMobile,
    } = generateBorderShadowStyles({
        controlName: POPULAR_SEARCH_KEYWORD_BORDER,
        noShadow: true,
        noBdrHove:true,
        attributes,
    });

    const {
        dimensionStylesDesktop: popularSearchKeywordPaddingDesktop,
        dimensionStylesTab: popularSearchKeywordPaddingTab,
        dimensionStylesMobile: popularSearchKeywordPaddingMob,
    } = generateDimensionsControlStyles({
        controlName: POPULAR_SEARCH_KEYWORD_PADDING,
        styleFor: "padding",
        attributes,
    });

    const {
        dimensionStylesDesktop: popularSearchKeywordMarginDesktop,
        dimensionStylesTab: popularSearchKeywordMarginTab,
        dimensionStylesMobile: popularSearchKeywordMarginMob
    } = generateDimensionsControlStyles({
        controlName: POPULAR_SEARCH_KEYWORD_MARGIN,
        styleFor: "margin",
        attributes,
    });


    desktop += `.${blockId}.betterdocs-search-form-wrapper .betterdocs-live-search .betterdocs-searchform select{
                        ${categorySearchTypoStylesDesktop};
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-live-search .betterdocs-searchform .betterdocs-search-category{
                    color:${categoryTextColor};
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-searchform .search-submit {
                    ${searchButtonTypoStylesDesktop}
                    color:${searchButtonTextColor};
                    background-color:${searchButtonBackgroundColor};
                    ${searhButtonBorderRadiusDesk}
                    ${searchButtonPaddingDesktop}
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-searchform .search-submit:hover {
                    background-color:${searchButtonBackgroundColorHover};
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword{
                    ${popularSearchMarginDesktop}
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword .popular-keyword{
                    color:${popularKeywordTextColor};
                    ${popularSearchKewordBorderDesktop}
                    ${popularSearchKeywordPaddingDesktop}
                    ${popularSearchKeywordMarginDesktop}
                    ${keywordDesktop}
                    background-color:${popularKeywordBackgroundColor};
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword .popular-search-title {
                    color:${popularTitleColor};
                    ${popularTitleTypoDesktop}
                }
    `;

    mobile += `.${blockId}.betterdocs-search-form-wrapper .betterdocs-live-search .betterdocs-searchform select{
                    ${categorySearchTypoStylesMobile};
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-searchform .search-submit {
                    ${searchButtonTypoStylesMobile}
                    ${searchButtonTypoStylesMobile}
                    ${searchButtonPaddingMobile}
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword{
                    ${popularSearchMarginMobile}
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword .popular-keyword{
                    ${keywordMobile}
                    ${popularSearchKewordBorderMobile}
                    ${popularSearchKeywordPaddingMob}
                    ${popularSearchKeywordMarginMob}
                }
                .${blockId}.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword .popular-search-title {
                    ${popularTitleTypoMob}
                }
    `;

    tab += `.${blockId}.betterdocs-search-form-wrapper .betterdocs-live-search .betterdocs-searchform select{
                ${categorySearchTypoStylesTab};
            }
            .${blockId}.betterdocs-search-form-wrapper .betterdocs-searchform .search-submit {
                ${searchButtonTypoStylesTab}
                ${searhButtonBorderRadiusTab}
                ${searchButtonPaddingTab}
            }
            .${blockId}.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword{
                ${popularSearchMarginTab}
            }
            .${blockId}.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword .popular-keyword{
                ${keywordTab}
                ${popularSearchKewordBorderTab}
                ${popularSearchKeywordPaddingTab}
                ${popularSearchKeywordMarginTab}
            }
            .${blockId}.betterdocs-search-form-wrapper .betterdocs-popular-search-keyword .popular-search-title {
                ${popularTitleTypoTab}
            }
    `;

    modStyleObject = {
        desktop,
        tab,
        mobile
    };

    return modStyleObject;
});

