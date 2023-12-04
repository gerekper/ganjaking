import React from 'react'
import { useMemo } from '@wordpress/element';
import { buildQueryString } from '@wordpress/url';
import { decodeEntities } from '@wordpress/html-entities';

import { useFetch } from '../../hooks';
import ListIcon from './ListIcon';
import NestedCategories from '../NestedCategories';


const DocList = ({ className = 'betterdocs-articles-list', attributes, categories, ...props }) => {
    const { postsPerPage, postsOrderBy, postsOrder, listIcon, enableNestedSubcategory } = attributes;

    const docsByCatIDURL = useMemo(() => {
        let query = {
            per_page: postsPerPage ? postsPerPage : 0,
            orderby: postsOrderBy,
            order: postsOrder,
            doc_category: props.id
        };

        const baseURL = query.orderby === "betterdocs_order" ? '/betterdocs/order_docs' : '/wp/v2/docs';

        return `${baseURL}?${buildQueryString(query)}`;
    }, [categories, postsPerPage, postsOrderBy, postsOrder, props.id]);

    const { data: docs, isLoading: isDocsLoading, error: docsError } = useFetch(docsByCatIDURL);

    return (
        <ul className={className}>
            {
                !isDocsLoading &&
                docs.length > 0 && docs.map(doc => (
                    <li key={doc.id}>
                        {listIcon?.length > 0 && <ListIcon icon={listIcon} />}
                        <a href={void 0}>
                            {decodeEntities(doc.title.rendered)}
                        </a>
                    </li>
                ))
            }

            {
                Boolean(enableNestedSubcategory) &&
                <NestedCategories
                    {...props}
                    attributes={attributes}
                />
            }
        </ul>
    )
}

export default DocList
