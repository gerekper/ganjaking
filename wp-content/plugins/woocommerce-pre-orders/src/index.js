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
 * @return {boolean} A boolean indicating the presence of a pre-order product in cart.
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
 * @return {string} Returns the date in string format to be displayed.
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
 * @return {string} Returns the sentence to be used with the total label.
 */
const getPreorderAdditionalInformation = ( cart ) => {
	const chargedUpfront =
		cart.cartItems[ 0 ].extensions.preorders.charged_upfront;
	const chargedUponRelease =
		cart.cartItems[ 0 ].extensions.preorders.charged_upon_release;
	if ( chargedUpfront ) {
		return __( 'charged upfront.', 'woocommerce-pre-orders' );
	} else if ( chargedUponRelease ) {
		const preorderDate = getPreorderDate( cart );
		return sprintf(
			/* translators: 1:Pre-order product date */
			__( 'charged %1$s', 'woocommerce-pre-orders' ),
			preorderDate
		);
	}
	return '';
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
			const additionalInformation =
				getPreorderAdditionalInformation( cart );
			return sprintf(
				/* translators: 1: Label, 2: Additional information */
				_x(
					'%1$s %2$s',
					'label and additional info',
					'woocommerce-pre-orders'
				),
				label,
				additionalInformation
			);
		}
		return label;
	},
} );
