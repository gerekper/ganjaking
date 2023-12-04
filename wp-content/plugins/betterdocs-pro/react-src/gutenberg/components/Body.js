import React from 'react'
import { useMemo } from '@wordpress/element';
import { buildQueryString } from '@wordpress/url';

import { useFetch } from '../hooks';
import { DocList } from './partials';

const Body = (props) => {
    return (
        <div className='betterdocs-body'>
            <DocList {...props} />
        </div>
    )
}

export default Body;
