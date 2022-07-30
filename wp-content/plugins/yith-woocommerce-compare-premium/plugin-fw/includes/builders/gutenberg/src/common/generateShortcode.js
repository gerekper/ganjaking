/**
 * Internal dependencies
 */
import { checkForDeps } from './checkForDeps';

/**
 * Generate the shortcode
 *
 * @param {object} blockArgs The block arguments.
 * @param {object} attributes The attributes
 * @returns {string}
 */
export const generateShortcode = ( blockArgs, attributes ) => {
	let theShortcode = '';
	let callback     = false;

	if ( typeof blockArgs.callback !== 'undefined' ) {
		if ( jQuery && blockArgs.callback in jQuery.fn ) {
			callback = jQuery.fn[ blockArgs.callback ];
		} else if ( blockArgs.callback in window ) {
			callback = window[ blockArgs.callback ];
		}
	}

	if ( typeof callback === 'function' ) {
		theShortcode = callback( attributes, blockArgs );
	} else {
		const shortcodeAttrs = blockArgs.attributes ? Object.entries( blockArgs.attributes ).map( ( [attributeName, attributeArgs] ) => {
			const show  = checkForDeps( attributeArgs, attributes );
			const value = attributes[ attributeName ];

			if ( show && typeof value !== 'undefined' ) {
				const shortcodeValue = !!attributeArgs.remove_quotes ? value : `"${value}"`;
				return attributeName + '=' + shortcodeValue;
			}
		} ) : [];

		const shortcodeAttrsText = shortcodeAttrs.length ? ( ' ' + shortcodeAttrs.join( ' ' ) ) : '';
		theShortcode             = `[${blockArgs.shortcode_name}${shortcodeAttrsText}]`;
	}
	return theShortcode;
};