import React from 'react'
import { sprintf, _n } from '@wordpress/i18n';


const Counts = ({ count, attributes }) => {
    let { prefix = '', suffix = '', suffixSingular = '', layout } = attributes;

    return (
        <div data-count={count} className="betterdocs-category-items-counts">
            <span>{sprintf(_n(
                `${prefix} %d ${suffixSingular}`,
                `${prefix} %d ${suffix}`,
                count,
                'betterdocs'
            ), count)?.trim()} </span>
        </div>
    )
}

export default Counts
