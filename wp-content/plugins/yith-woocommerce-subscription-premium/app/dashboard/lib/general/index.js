import { getDateParamsFromQuery, getCurrentDates } from '@woocommerce/date';
import {__} from '@wordpress/i18n';
import {find, identity, uniq} from 'lodash';
import {formatCurrency} from '../numbers';
import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';


export const NAMESPACE = '/wc-analytics';

export function getDateQuery( query ) {
	const { period, compare, before, after }                 = getDateParamsFromQuery( query );
	const { primary: primaryDate, secondary: secondaryDate } = getCurrentDates( query );
	return {
		period,
		compare,
		before,
		after,
		primaryDate,
		secondaryDate
	};
}

export function getSelectedChart( chartName, charts = [] ) {
	const chart = find( charts, { key: chartName } );
	if ( chart ) {
		return chart;
	}
	return charts[ 0 ];
}


export function getTooltipValueFormat( type ) {
	switch ( type ) {
		case 'currency':
			return formatCurrency;
		case 'percent':
			return '.0%';
		case 'number':
			return ',';
		case 'average':
			return ',.2r';
		default:
			return ',';
	}
}

export function getStatusBlock(status) {
	const labels = ywsbsSettings.wc.status_labels;

	return "<span class='status column-status'><span class='status "+status+"'>"+labels[status].toLowerCase()+"</span></span>";
}

export function getEditorlLink(label, post_id) {
	return "<a href='post.php?post=" + post_id + "&action=edit'>" + label + "</a>";
}


export function getProductReportLink(label, post_id) {
	return "<a href='admin.php?page=yith_woocommerce_subscription&tab=dashboard&filter=single_product&path=/products-report&products=" + post_id + "'>" + label + "</a>";
}


export function getEmailLink(email) {
	return "<a href='mailto:" + email + "'>" + email + "</a>";
}


export function getCustomerName(customer) {
	const {first_name: firstName, last_name: lastName} = customer || {};

	if (!firstName && !lastName) {
		return __('Guest','yith-woocommerce-subscription');
	}

	return [firstName, lastName].join(' ');
}

/**
 * Create a variation name by concatenating each of the variation's
 * attribute option strings.
 *
 * @param {Object} variation - variation returned by the api
 * @param {Array} variation.attributes - attribute objects, with option property.
 * @param {string} variation.name - name of variation.
 * @return {string} - formatted variation name
 */
export function getVariationName( { attributes, name } ) {
	const separator = getSetting( 'variationTitleAttributesSeparator', ' - ' );

	if ( name.indexOf( separator ) > -1 ) {
		return name;
	}

	const attributeList = attributes
		.map( ( { option } ) => option )
		.join( ', ' );

	return attributeList ? name + separator + attributeList : name;
}


export const getVariationLabels = getRequestByIdString(
	( { products } ) => {
		// If a product was specified, get just its variations.
		if ( products ) {
			return NAMESPACE + `/products/${ products }/variations`;
		}

		return NAMESPACE + '/variations';
	},
	( variation ) => {
		return {
			key: variation.id,
			label: getVariationName( variation ),
		};
	}
);

/**
 * Get a function that accepts ids as they are found in url parameter and
 * returns a promise with an optional method applied to results
 *
 * @param {string|Function} path - api path string or a function of the query returning api path string
 * @param {Function} [handleData] - function applied to each iteration of data
 * @return {Function} - a function of ids returning a promise
 */
export function getRequestByIdString( path, handleData = identity ) {
	return function ( queryString = '', query ) {
		const pathString = typeof path === 'function' ? path( query ) : path;
		const idList = getIdsFromQuery( queryString );
		if ( idList.length < 1 ) {
			return Promise.resolve( [] );
		}
		const payload = {
			include: idList.join( ',' ),
			per_page: idList.length,
		};

		return apiFetch( {
			path: addQueryArgs( pathString, payload ),
		} ).then( ( data ) => data.map( handleData ) );
	};
}

/**
 * Get an array of IDs from a comma-separated query parameter.
 *
 * @param {string} queryString string value extracted from URL.
 * @return {Array} List of IDs converted to numbers.
 */
export function getIdsFromQuery( queryString = '' ) {
	return uniq(
		queryString
			.split( ',' )
			.map( ( id ) => parseInt( id, 10 ) )
			.filter( Boolean )
	);
}



export const getProductLabels = getRequestByIdString(
	NAMESPACE + '/products',
	( product ) => ( {
		key: product.id,
		label: product.name,
	} )
);

