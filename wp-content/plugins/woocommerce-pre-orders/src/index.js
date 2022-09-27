/**
 * External dependencies
 */
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { getSetting } from '@woocommerce/settings';
import { __experimentalRegisterCheckoutFilters } from '@woocommerce/blocks-checkout';
import { __, _x, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import preorderPaymentMethod from './preorder-gateway';
import './index.scss';

/**
 * Inspects the cart payload and the cart item `extension` property for
 * additional information regarding if it is a preorder cart item type.
 *
 * @param {*} cart
 * @returns Boolean A boolean indicating the presence of a pre-order product in cart.
 */
const cartContainsPreorders = ( cart ) => {
	if ( cart.cartItemsCount > 0 ) {
		return (
			cart.cartItems[ 0 ].extensions.preorders.charged_upfront ||
			cart.cartItems[ 0 ].extensions.preorders.charged_upon_release
		);
	}
	return false;
};

/**
 * Looks for the availability information (date) at the the
 * cart item `extension` property.
 *
 * @param {*} cart
 * @returns String Returns the date in string format to be displayed.
 */
const getPreorderDate = ( cart ) => {
	if ( cart.cartItemsCount > 0 ) {
		return cart.cartItems[ 0 ].extensions.preorders.availability;
	}
};

/**
 * Prepares the information that is going to be added to the
 * total label on both cart and checkout blocks.
 *
 * @param {*} cart
 * @returns String Returns the sentence to be used with the total label.
 */
const getPreorderAdditionalInformation = ( cart ) => {
	const charged_upfront =
		cart.cartItems[ 0 ].extensions.preorders.charged_upfront;
	const charged_upon_release =
		cart.cartItems[ 0 ].extensions.preorders.charged_upon_release;
	if ( charged_upfront ) {
		return __( 'charged upfront.', 'wc-pre-orders' );
	} else if ( charged_upon_release ) {
		const preorderDate = getPreorderDate( cart );
		return sprintf( __( 'charged %1$s', 'wc-pre-orders' ), preorderDate );
	} else {
		return '';
	}
};

/**
 * We register the new payment gateway only if needed.
 */
const settings = getSetting( 'pre_orders_pay_later_data', {} );
if ( settings.is_enabled ) {
	registerPaymentMethod( preorderPaymentMethod );
}

/**
 * Deals with manipulating the total label to add information if needed.
 */
__experimentalRegisterCheckoutFilters( 'woocommerce-pre-order', {
	totalLabel: ( label, extensions, { cart } ) => {
		if ( cartContainsPreorders( cart ) ) {
			const additionalInformation = getPreorderAdditionalInformation(
				cart
			);
			return sprintf(
				_x( '%1$s %2$s', 'label and additional info', 'wc-pre-orders' ),
				label,
				additionalInformation
			);
		}
		return label;
	},
} );
