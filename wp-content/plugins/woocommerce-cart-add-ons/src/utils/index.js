/**
 * External dependencies
 */
import { speak } from '@wordpress/a11y';
import { __, sprintf } from '@wordpress/i18n';
import { SVG } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * Speak name of product that has been added or removed.
 *
 * @param {Object} filterAdded Product Label and ID
 * @param {Object} filterRemoved Product Label and ID
 * @param {Object} changeCategory Category Label and ID
 */

export const announceFilterChange = ( {
	filterAdded,
	filterRemoved,
	changeCategory,
} ) => {
	const filterAddedName = filterAdded ? filterAdded.label : null;
	const filterRemovedName = filterRemoved ? filterRemoved.label : null;
	const changeCategoryName = changeCategory ? changeCategory.label : null;
	if ( filterAddedName ) {
		speak(
			sprintf(
				/* translators: %s product name (for example: 'blue t-shirt', 'red socks', 'green dress'...) */
				__( '%s product added.', 'sfn_cart_addons' ),
				filterAddedName
			)
		);
	} else if ( filterRemovedName ) {
		speak(
			sprintf(
				/* translators: %s product name (for example: 'blue t-shirt', 'red socks', 'green dress'...) */
				__( '%s product removed.', 'sfn_cart_addons' ),
				filterRemovedName
			)
		);
	} else if ( changeCategoryName ) {
		speak(
			sprintf(
				/* translators: %s category (for example: 't-shirts', 'socks', 'dresses'...) */
				__( 'Changed category to %s.', 'sfn_cart_addons' ),
				changeCategoryName
			)
		);
	}
};

/**
 * Check whether we should update attributes if both categories and products are set
 *
 * @param {Array} catArray Product or Category data from api call
 * @param {number} order Order of current Category
 * @param {string} objectKey Name of objectKey either category or product
 * @return {boolean} Whether to allow the user to update the attributes if their selections are valid
 */
export const canUpdateData = ( catArray, order, objectKey ) => {
	return (
		catArray[ order ][ objectKey ] !== null &&
		catArray[ order ].products.length > 0
	);
};

/**
 * Make sure no empty categories are saved to attributes
 *
 * @param {Array} catArray
 * @param {string} objectKey
 * @return {Array} Validated array of Categories
 */
export const cleanCategoryData = ( catArray, objectKey ) => {
	return [ ...catArray ].filter( ( value ) => {
		const shouldInclude =
			value[ objectKey ] !== null &&
			value.category !== undefined &&
			value.products.length !== 0;

		return shouldInclude;
	} );
};

/**
 * Make sure no empty products are saved to attributes
 *
 * @param {Array} catArray
 * @param {string} objectKey
 * @return {Array} Validated array of Categories
 */
export const cleanProductData = ( catArray, objectKey ) => {
	return [ ...catArray ].filter( ( value ) => {
		const shouldInclude =
			value[ objectKey ] !== null &&
			value.product !== undefined &&
			value.products.length !== 0;

		return shouldInclude;
	} );
};

/**
 * Decode HTML
 *
 * @param {string} str
 * @return {string} Modified string
 */
export const decodeHTML = ( str ) => {
	return str
		.replace( '&amp;', '&' )
		.replace( '&lt;', '<' )
		.replace( '&gt;', '>' )
		.replace( '&quot;', '"' )
		.replace( '&#039;', "'" );
};

/**
 * Remove all checked products from the Dropdown list
 *
 * @param {Array} allProducts All products loaded in Dropdown
 * @param {Array} checkedProducts Checked products
 * @param {Function} setNewDropdown Function to update allProducts
 */
export const filterProducts = (
	allProducts,
	checkedProducts,
	setNewDropdown
) => {
	const filtered = allProducts.filter( ( product ) => {
		return ! checkedProducts.some(
			( checkedProduct ) => checkedProduct.value === product.value
		);
	} );
	setNewDropdown( filtered );
};

/**
 * Get the name of a product from it's ID
 *
 * @param {number} filterValue Product ID
 * @param {Array} searchArray array of objects to search for the product name
 */
export const getFilterNameFromValue = ( filterValue, searchArray ) => {
	return searchArray.find( ( option ) => {
		return option.value === filterValue;
	} );
};

/**
 * Make new array of product or category objects with correct data structure
 *
 * @param {Array} data Product or Category data from api call
 * @return {Array} Modified array of objects with the data structure {value: number, label: string}
 */
export const mapData = ( data = [] ) => {
	return data.map( ( datum ) => {
		return { value: datum.id, label: decodeHTML( datum.name ) };
	} );
};

/**
 * Make new array of product with an array of product variaton IDs
 *
 * @param {Array} products Product data from api call
 * @return {Array} Modified array of objects with the data structure {value: number, label: string, variations: array[ number ], itIncludesVariations: boolean }
 */

export const addVariationsToProducts = ( products = [] ) => {
	const getVariationIds = ( variations = [] ) => {
		return variations.map( ( variation ) => {
			return variation.id;
		} );
	};

	return products.map( ( product ) => {
		return {
			value: product.id,
			label: decodeHTML( product.name ),
			variations: getVariationIds( product.variations ),
			itIncludesVariations: false,
		};
	} );
};

/**
 * Update the Category Data array in preperation for saving to attributes
 *
 * https://stackoverflow.com/questions/29537299/react-how-to-update-state-item1-in-state-using-setstate
 *
 * @param {Array} currentCategoryMatches Product Label and ID
 * @param {number} order key of object that should be updated
 * @param {Array | Object} newChecked New Category Data
 * @param {string} type whether to update category or products
 */
export const updateCategoryData = (
	currentCategoryMatches,
	order,
	newChecked,
	type
) => {
	const items = [ ...currentCategoryMatches ];

	items[ order ] = {
		...items[ order ],
		[ type ]: newChecked,
	};

	return items;
};

/// Load Dropdown Functions ///

/**
 * Internal function called by getItems. Builds api request url
 *
 * @param {boolean} isProduct If load for products (true) or categories (false)
 * @param {Array} exclude Array of items to exclude
 * @param {string} searchData String of search data
 * @param {boolean} isLargeCatalog If store has large catalog
 */

const getQueryPath = ( isProduct, exclude, searchData, isLargeCatalog ) => {
	const search = searchData ?? '';
	const endpoint = isProduct
		? '/wc/store/products'
		: '/wc/store/products/categories';

	const defaultArgs = {
		per_page: isLargeCatalog ? 10 : 0,
		search,
		exclude: exclude ?? '',
	};

	const requests = [
		addQueryArgs( endpoint, {
			...defaultArgs,
		} ),
	];

	return requests;
};

/**
 * Internal function called by loadDropdown. Does API call
 *
 * @param {boolean} isProduct If load for products (true) or categories (false)
 * @param {Array} exclude Array of items to exclude
 * @param {string} searchData String of search data
 * @param {boolean} isLargeCatalog If store has large catalog
 */

const getItems = async ( isProduct, exclude, searchData, isLargeCatalog ) => {
	const path = getQueryPath(
		isProduct,
		exclude,
		searchData,
		isLargeCatalog
	)[ 0 ];

	return apiFetch( {
		path,
	} );
};

/**
 * Async function that loads a list of products or categories
 *
 * @param {Function} setItem Load results to state
 * @param {boolean} isProduct If load for products (true) or categories (false)
 * @param {Array} exclude Array of items to exclude
 * @param {string} searchData String of search data
 * @param {boolean} isLargeCatalog If store has large catalog
 */

export async function loadDropdown( {
	setItem = () => {},
	isProduct = true,
	exclude = '',
	searchData = '',
	isLargeCatalog = false,
} ) {
	const items = await getItems(
		isProduct,
		exclude,
		searchData,
		isLargeCatalog
	).then( ( data ) => {
		return new Promise( ( resolve ) => {
			const mapped = isProduct
				? addVariationsToProducts( data )
				: mapData( data );
			resolve( mapped );
		} );
	} );
	setItem( items );
}

/**
 * Icon for the block, same as used by WooCommerce Blocks.
 */
const cartIcon = (
	<SVG xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
		<path fill="none" d="M0 0h24v24H0V0z" />
		<path d="M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
	</SVG>
);

export default cartIcon;
