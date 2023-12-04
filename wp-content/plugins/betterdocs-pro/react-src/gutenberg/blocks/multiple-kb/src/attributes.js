import { __ } from '@wordpress/i18n';
import {
    generateTypographyAttributes,
    generateResponsiveRangeAttributes,
    generateDimensionsAttributes,
    generateBackgroundAttributes,
    generateBorderShadowAttributes,
} from '../../../util/helpers'

import {
    BOX_BACKGROUND,
    BOX_BORDER,
    BOX_MARGIN,
    BOX_PADDING,
    TITLE_MARGIN,
    COUNT_MARGIN,
    COLUMNS,
    ICON_AREA,
    ICON_SIZE,
    ICON_BACKGROUND,
    ICON_BORDER,
    ICON_MARGIN,
    ICON_PADDING,
    WRAPPER_MARGIN,
} from './constants'
import * as typoPrefixs from './typographyPrefixConstants'

const attributes = {
    // the following 4 attributes is must required for responsive options and asset generation for frontend
    // responsive control attributes ⬇
    resOption: {
        type: 'string',
        default: 'Desktop',
    },
    // blockId attribute for making unique className and other uniqueness ⬇
    blockId: {
        type: 'string',
    },
    blockRoot: {
        type: 'string',
        default: 'better_docs',
    },
    // blockMeta is for keeping all the styles ⬇
    blockMeta: {
        type: 'object',
    },
    includeCategories: {
        type: 'string',
        default: null,
    },
    excludeCategories: {
        type: 'string',
    },
    boxPerPage: {
        type: 'number',
        default: 9,
    },
    orderBy: {
        type: 'string',
        default: 'name',
    },
    order: {
        type: 'string',
        default: 'asc',
    },
    layout: {
        type: 'string',
        default: 'default',
    },
    showIcon: {
        type: 'boolean',
        default: true,
    },
    showTitle: {
        type: 'boolean',
        default: true,
    },
    titleTag: {
        type: 'string',
        default: 'h2',
    },
    showCount: {
        type: 'boolean',
        default: true,
    },
    prefix: {
        type: 'string',
    },
    suffix: {
        type: 'string',
        default: __('articles', 'betterdocs'),
    },
    suffixSingular: {
        type: 'string',
        default: __('article', 'betterdocs'),
    },
    titleColor: {
        type: 'string',
        default: '#333333',
    },
    titleHoverColor: {
        type: 'string',
    },
    countColor: {
        type: 'string',
        default: '#707070',
    },
    countHoverColor: {
        type: 'string',
    },
    // typography attributes
    ...generateTypographyAttributes(Object.values(typoPrefixs)),
    // responsive range
    ...generateResponsiveRangeAttributes(COLUMNS, {
        defaultRange: 3,
        noUnits: true,
    }),
    ...generateResponsiveRangeAttributes(ICON_AREA, {
        defaultRange: 80,
    }),
    ...generateResponsiveRangeAttributes(ICON_SIZE),
    // dimension
    ...generateDimensionsAttributes(BOX_MARGIN),
    ...generateDimensionsAttributes(BOX_PADDING),
    ...generateDimensionsAttributes(TITLE_MARGIN),
    ...generateDimensionsAttributes(COUNT_MARGIN),
    ...generateDimensionsAttributes(ICON_MARGIN),
    ...generateDimensionsAttributes(ICON_PADDING),
    ...generateDimensionsAttributes(WRAPPER_MARGIN, {
        top: 28,
        right: 0,
        bottom: 28,
        left: 0,
        isLinked: false,
    }),
    // background
    ...generateBackgroundAttributes(BOX_BACKGROUND, {
        defaultFillColor: '#f8f8fc',
        noOverlay: true,
    }),
    ...generateBackgroundAttributes(ICON_BACKGROUND, {
        noOverlay: true,
        noMainBgi: true,
    }),
    // border shadow attriubtes
    ...generateBorderShadowAttributes(BOX_BORDER),
    ...generateBorderShadowAttributes(ICON_BORDER, { noShadow: true }),
}

export default attributes
