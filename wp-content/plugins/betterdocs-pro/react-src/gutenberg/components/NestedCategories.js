import React from 'react'
import { buildQueryString } from '@wordpress/url';
import { useMemo } from '@wordpress/element';

import { useFetch } from '../hooks';
import { DocList } from './partials';
import { Spinner } from '@wordpress/components';
import { ArrowIcon } from './icons';

const NestedCategories = ({ id: parentCatID, attributes }) => {
    const nestedCatAPIURL = useMemo(() => {
        let query = {
            parent: parentCatID,
            hide_empty: true,
        };

        return `/wp/v2/doc_category/?${buildQueryString(query)}`;
    }, [parentCatID]);

    const { data: nestedCategories, isLoading: isCategoryLoading, error: categoryError } = useFetch(nestedCatAPIURL);

    return (
        <>
            { isCategoryLoading && <Spinner /> }
            {
                !isCategoryLoading &&
                nestedCategories.length > 0 &&
                nestedCategories.map(category => (
                    <li key={category.id} className='betterdocs-nested-category-wrapper'>
                        <span className='betterdocs-nested-category-title'>
                            {<ArrowIcon/>}
                            <a href={void 0}>
                                {category?.name}
                            </a>
                        </span>

                        <DocList
                            {...category}
                            className='betterdocs-nested-category-list'
                            attributes={attributes}
                            categories={nestedCategories}
                        />
                    </li>
                ))
            }
        </>
    )
}

export default NestedCategories
