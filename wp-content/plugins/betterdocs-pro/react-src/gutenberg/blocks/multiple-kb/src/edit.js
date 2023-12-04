import React from 'react'
import { useBlockProps } from '@wordpress/block-editor';
import { buildQueryString } from '@wordpress/url';
import { Spinner } from '@wordpress/components';
import { useMemo } from '@wordpress/element';
import { __ } from "@wordpress/i18n";

import Inspector from './inspector'
import { NotFound, SingleCategory } from '../../../components'
import { usePreviewDeviceType, useDuplicateBlockIdFix, useStyleSheets, useFetch } from '../../../hooks';
import cssProperties from './cssProperties';

const BLOCK_PREFIX = "betterdocs-multiple-kb";
const Edit = ({ attributes, setAttributes, isSelected, clientId }) => {
    const {
        blockId,
        includeCategories,
        excludeCategories,
        boxPerPage,
        orderBy,
        order,
        layout
    } = attributes;
    usePreviewDeviceType(setAttributes);
    useDuplicateBlockIdFix(BLOCK_PREFIX, blockId, clientId, setAttributes);
    const { properties, htmlAttributes, styleTag } = useStyleSheets({
        properties: cssProperties({ blockId }),
        setAttributes,
        attributes
    })

    const categoryApiURL = useMemo(() => {
        let query = {
            per_page: boxPerPage ? boxPerPage : 8,
            orderby: orderBy,
            order: order,
            hide_empty: true,
        };

        let include =
            includeCategories && includeCategories !== "[]"
                ? JSON.parse(includeCategories).map((category) => category.value)
                : [];

        let exclude =
            excludeCategories && excludeCategories !== "[]"
                ? JSON.parse(excludeCategories).map((category) => category.value)
                : [];

        include = include.filter((item) => !exclude.includes(item));

        if (include.length > 0) {
            query["include"] = include;
        }
        if (exclude.length > 0) {
            query["exclude"] = exclude;
        }

        return `/wp/v2/knowledge_base/?${buildQueryString(query)}`
    }, [
        includeCategories,
        excludeCategories,
        boxPerPage,
        orderBy,
        order
    ]);

    const { data: categories, isLoading: isCategoryLoading, error: categoryError } = useFetch(categoryApiURL);

    const blockProps = useBlockProps({
        className: `eb-guten-block-main-parent-wrapper`,
    });

    let selectedLayout = layout == 'default' ? 'layout-1' : layout;

    return (
        <>
            {isSelected && <Inspector attributes={attributes} setAttributes={setAttributes} />}
            <div {...blockProps}>
                {styleTag()}
                <div className={`betterdocs-category-box-wrapper betterdocs-blocks ${blockId} betterdocs-multiple-kb-wrapper betterdocs-pro`}>
                    <div className={`betterdocs-category-box-inner-wrapper ash-bg layout-flex ${selectedLayout} ${properties?.col?.classNames}`} {...htmlAttributes}>
                        {
                            /**
                             * If item is not loaded yet
                             */
                            isCategoryLoading && <Spinner />
                        }
                        {
                            /**
                             * If no item is found.
                             */
                            !isCategoryLoading && categories?.length == 0 && <NotFound>{__("No Knowledge Base Found.", "betterdocs")}</NotFound>
                        }
                        {
                            /**
                             * If items are loaded.
                             */
                            !isCategoryLoading &&
                            categories?.length > 0 && categories.map(category => <SingleCategory
                                key={category.id}
                                widgetType="category-box"
                                attributes={attributes}
                                categories={categories}
                                tagType={layout == 'default' ? 'div' : 'a'}
                                {...category}
                            />)
                        }
                    </div>
                </div>
            </div>
        </>
    )
}

export default Edit
