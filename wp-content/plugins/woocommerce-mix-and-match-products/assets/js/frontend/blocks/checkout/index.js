/**
 * Extend checkout block
 *
 * @package  WooCommerce Mix and Match Products/Blocks
 */

/**
 * External dependencies
 * NB: registerCheckoutFilters was "graduated" in Woo 7.5.0 or Woo Blocks 9.6.0
 */

import {
	__experimentalRegisterCheckoutFilters as experimentalFilters,
	registerCheckoutFilters as graduatedFilters,
} from '@woocommerce/blocks-checkout';

import { dispatch } from '@wordpress/data';
import { addAction } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import { actionPrefix, namespace } from '../constants';

/**
 * Remove item from the cart
 */
addAction(
	'experimental__woocommerce_blocks-cart-remove-item',
	'mix-and-match',
	( {
		product,
		quantity = 1,
	} ) => {
		if ( 'undefined' !== typeof product.extensions.mix_and_match.child_items && product.extensions.mix_and_match.child_items.length ) {
			const { itemIsPendingDelete } = dispatch( 'wc/store/cart' );
			for ( const childCartItemKey of product.extensions.mix_and_match.child_items ) {
				itemIsPendingDelete( childCartItemKey, true );
				// @Todo: If there's ever an event for after the cart is updated, we should technically set the items back to false.
			}
		}
	}
);


/**
 * Set cart item classes for styling
 */
const registerCheckoutFilters =
	typeof graduatedFilters !== 'undefined'
		? graduatedFilters
		: experimentalFilters;

registerCheckoutFilters(
	namespace,
	{

		itemName: ( context, { mix_and_match }, { cartItem } ) => {

			if ( mix_and_match && mix_and_match.child_item_data ) {
				context = `${ context } x ${ mix_and_match.child_item_data.child_qty }`;
			}

			return context;
		},

		cartItemClass: ( classlist, { mix_and_match }, { context, cartItem } ) => {

			if ( mix_and_match ) {

				let classes = [];

				if ( mix_and_match.container ) {

					classes.push( 'is-mnm-child' );

					classes.push( 'is-mnm-child__cid_' + mix_and_match.child_item_data.container_id );
					classes.push( 'is-mnm-child__iid_' + mix_and_match.child_item_data.child_item_id );

					if ( mix_and_match.child_item_data.is_last ) {
						classes.push( 'is-mnm-child__last' );
					}

					if ( mix_and_match.child_item_data.is_priced_per_product ) {
						classes.push( 'is-mnm-child__priced_per_product' );
					}

				} else if ( mix_and_match.child_items ) {

					classes.push( 'is-mnm-container' );

					classes.push( 'is-mnm-container__cid_' + cartItem.id );

					if ( mix_and_match.container_data.is_editable ) {
						classes.push( 'is-mnm-container__editable' );
					}

					if ( mix_and_match.container_data.is_priced_per_product ) {
						classes.push( 'is-mnm-container__priced_per_product' );
					}

				}

				if ( classes.length ) {
					classlist += ' ' + classes.join( ' ' );
				}
			}

			return classlist;
		}

	}
);
