/**
 * External dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Returns the default products stored as default options for showing when no matches are show.
 *
 * @param {Array} defaultAddons Array of chossen default prodcuts (props.defaultAddons).
 * @return {Promise<Array>} Mapped list of products to render.
 */
export const getDefaultProducts = async ( defaultAddons ) => {
	if ( undefined === defaultAddons || defaultAddons.length === 0 ) {
		return [];
	}
	const ids = defaultAddons.map( ( product ) => product.value );
	return await getProductsByIds( ids );
};

/**
 * Returns a list of products that matches the category match rules.
 *
 * @param {Array} categoryMatches Array of chossen pairs of category and products (props.categoryMatches).
 * @return {Promise<Array>} Mapped list of products to render.
 */
export const getProductsFromCategoryMatches = async ( categoryMatches ) => {
	if ( undefined === categoryMatches || categoryMatches.length === 0 ) {
		return [];
	}
	const reducedCategoryMatches = reduceCategoryMatches( categoryMatches );
	const productCategoriesInCart = await getProductsCategoriesFromCart();

	const ids = [];
	for ( const possibleMatch of reducedCategoryMatches ) {
		if ( productCategoriesInCart.includes( possibleMatch.category ) ) {
			if ( undefined !== possibleMatch.products ) {
				ids.push( ...possibleMatch.products );
			}
		}
	}
	return await getProductsByIds( ids );
};

/**
 * Returns a list of products that matches the products match rules.
 *
 * @param {Array} productMatches Array of chossen pairs of product and prodcuts (props.productMatches).
 * @return {Promise<Array>} Mapped list of products to render.
 */
export const getProductsFromProductMatches = async ( productMatches ) => {
	if ( undefined === productMatches || productMatches.length === 0 ) {
		return [];
	}
	const reducedProductMatches = reduceProductMatches( productMatches );
	const productsFromCart = getProductsFromCart();
	const ids = [];
	for ( const possibleMatch of reducedProductMatches ) {
		if (
			productsFromCart.includes( possibleMatch.product ) ||
			productsFromCart.some(
				( product ) =>
					possibleMatch.itIncludesVariations &&
					possibleMatch.variations.includes( product )
			)
		) {
			if ( undefined !== possibleMatch.products ) {
				ids.push( ...possibleMatch.products );
			}
		}
	}
	return await getProductsByIds( ids );
};

/**
 * Returns a simplified version of the stored array for categoryMatches (props.categoryMatches).
 *
 * @param {Array} categoryMatches Array of chossen pairs of category and products (props.categoryMatches).
 * @return {Array} List of category ids and their associated product ids.
 */
export const reduceCategoryMatches = ( categoryMatches ) => {
	if ( undefined === categoryMatches ) {
		return [];
	}
	return categoryMatches.map( ( categoryMatch ) => {
		return {
			category: categoryMatch.category.value,
			products: categoryMatch.products.map( ( product ) => {
				return product.value;
			} ),
		};
	} );
};

/**
 * Returns a simplified version of the stored array for productMatches (props.productMatches).
 *
 * @param {Array} productMatches Array of chossen pairs of product and products (props.productMatches).
 * @return {Array} List of product ids and their associated product ids.
 */
export const reduceProductMatches = ( productMatches ) => {
	if ( undefined === productMatches ) {
		return [];
	}
	return productMatches.map( ( productMatch ) => {
		return {
			product: productMatch.product.value,
			itIncludesVariations: productMatch.product.itIncludesVariations,
			variations:
				productMatch.product.variations === undefined
					? []
					: productMatch.product.variations,
			products: productMatch.products.map( ( product ) => {
				return product.value;
			} ),
		};
	} );
};

/**
 * Returns a list of categories for the products that are in the cart.
 *
 * @return {Promise<Array>} List of category ids.
 */
export const getProductsCategoriesFromCart = async () => {
	const productIDsFromCart = getProductsFromCart();
	if ( productIDsFromCart.length === 0 ) {
		return [];
	}

	const products = await getProductsByIds( productIDsFromCart, false );
	const ids = [];

	products.forEach( ( product ) => {
		product.categories?.forEach( ( category ) => {
			ids.push( category.id );
		} );
	} );

	/**
	 * Check if there is any additional information about variable
	 * products and their categories in the extensions.
	 */
	const categoriesFromVariableProducts = getCategoriesFromVariableProductsInCart();
	return [ ...ids, ...categoriesFromVariableProducts ];
};

/**
 * Returns a list of mapped properties from a product data object.
 *
 * @param {Array} list List of objects of type product data.
 * @return {Array} Mapped products.
 */
export const mapProducts = ( list ) => {
	return list.map( ( product ) => {
		return {
			id: product.id,
			name: product.name,
			price_html: product.price_html,
			thumbnail:
				product.images !== undefined
					? product.images[ 0 ]?.thumbnail
					: '',
			thumbnail_alt:
				product.images !== undefined ? product.images[ 0 ]?.alt : '',
			type: product.type,
			permalink: product.permalink,
			is_purchasable: product.is_purchasable,
			is_in_stock: product.is_in_stock,
			add_to_cart: product.add_to_cart,
		};
	} );
};

/**
 * Gets all the categories of variable products in the cart.
 *
 * @return {Array} IDs of products in cart.
 */
export const getCategoriesFromVariableProductsInCart = () => {
	const wcCartStore = wp.data.select( 'wc/store/cart' );
	const cartAddons = wcCartStore.getCartData().extensions
		.wc_cart_addons_block;
	return cartAddons.categories ?? [];
};

/**
 * Gets all the products in cart and returns their ids.
 *
 * @return {Array} IDs of products in cart.
 */
export const getProductsFromCart = () => {
	const wcCartStore = wp.data.select( 'wc/store/cart' );
	const cartData = wcCartStore.getCartData();
	return cartData.items.map( ( product ) => {
		return product.id;
	} );
};

/**
 * Gets the latest 10 products added.
 *
 * @return {Promise<Array>} IDs of products.
 */
export const getLatestProducts = async () => {
	return apiFetch( {
		path: '/wc/store/products',
	} )
		.then( ( productsToMap ) => {
			return productsToMap ? mapProducts( productsToMap ) : [];
		} )
		.catch( () => {
			return [];
		} );
};

/**
 * Returns products by they id.
 *
 * @param {Array} ids List of products IDs to return.
 * @param {boolean} mapped Defines if the return should be mapped to our custom model.
 * @return {Promise<Array>} A list of product objects.
 */
export const getProductsByIds = async ( ids, mapped = true ) => {
	if ( ids.length === 0 ) {
		return [];
	}

	return apiFetch( {
		path: `/wc/store/products?include=${ ids.join( ',' ) }`,
	} )
		.then( ( products ) => {
			return mapped ? mapProducts( products ) : products;
		} )
		.catch( () => {
			return [];
		} );
};
