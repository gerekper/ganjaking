/**
 * Ajax Fetch
 */

/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';

/**
 * Check status of ajax call
 * @param response
 * @returns {*}
 */
function ajaxCheckStatus( response ) {
	if ( response.status >= 200 && response.status < 300 ) {
		return response;
	}

	throw response;
}

/**
 * Parse the response of the ajax call
 * @param response
 * @returns {*}
 */
function parseResponse( response ) {
	return response.json ? response.json() : response.text();
}

/**
 * Fetch using WordPress Ajax
 *
 * @param {object} data The data to use in the ajax call.
 * @param {string} url The ajax URL.
 * @returns {Promise<Response>}
 */
export const ajaxFetch = ( data, url = yithGutenberg.ajaxurl ) => {
	url = addQueryArgs( url, data );
	return fetch( url ).then( ajaxCheckStatus ).then( parseResponse );
};