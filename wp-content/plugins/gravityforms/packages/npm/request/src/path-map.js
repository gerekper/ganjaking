import config from './config';
import { template } from 'lodash';

/**
 * Get your endpoints safely here first with optional chaining
 */

export const endpoints = config?.endpoints;

/**
 * Now map them to action names and add templates as needed
 */

const getPathMap = () => {
	const map = {};
	for ( const endpoint in endpoints ) {
		const data = endpoints[ endpoint ];
		map[ endpoint ] = {
			endpoint: template( `${ data.path }${ data.rest_params }` ),
			nonce: data.nonce,
		};
	}

	return map;
};

export const PATH_MAP = getPathMap();
