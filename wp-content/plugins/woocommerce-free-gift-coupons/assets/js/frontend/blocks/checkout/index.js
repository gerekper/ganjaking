 /**
 * External dependencies
 * NB: registerCheckoutFilters was "graduated" in Woo 7.5.0 or Woo Blocks 9.6.0
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	__experimentalRegisterCheckoutFilters as experimentalFilters,
	registerCheckoutFilters as graduatedFilters,
} from '@woocommerce/blocks-checkout';

const registerCheckoutFilters =
	typeof graduatedFilters !== 'undefined'
		? graduatedFilters
		: experimentalFilters;

 registerCheckoutFilters( 'free-gift-coupons', {

    itemName: ( context, { free_gift_coupons }, { cartItem } ) => {

		if ( free_gift_coupons && free_gift_coupons.free_gift ) {
			context = `${ context } x ${ cartItem.quantity }`;
		}

		return context;
	},
 
     cartItemClass: ( classlist, { free_gift_coupons }, { context, cartItem } ) => {

        if ( free_gift_coupons ) {

            let classes = [];

            if ( free_gift_coupons.free_gift ) {

                classes.push( 'is-free-gift-product' );

                if ( free_gift_coupons.fgc_edit_in_cart ) {
                    classes.push( 'free-gift-coupon-edit-in-cart' );
                }

            }

            if ( classes.length ) {
                classlist += ' ' + classes.join( ' ' );
            }
        }

        return classlist;
     
    },

    subtotalPriceFormat: ( price, { free_gift_coupons }, { context, cartItem } ) => {

        // Cannot use this to get "Free" to display in the Total column. In both cases still need <price/> component.
        if ( free_gift_coupons ) {

            if ( free_gift_coupons.free_gift ) {

                if ( free_gift_coupons.free_gift ) {

                    price +=  __( 'Free!', 'wc_free_gift_coupons' );

                }

            }

        }

        return price;

    }


} );
