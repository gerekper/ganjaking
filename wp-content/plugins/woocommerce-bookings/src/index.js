/**
 * External dependencies
 */
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { getSetting } from '@woocommerce/settings';
import { dispatch } from '@wordpress/data';
import { addAction } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import bookingsPaymentMethod from './check-availability';

const settings = getSetting( 'wc-bookings-gateway_data', {} );
const { PAYMENT_STORE_KEY } = wc.wcBlocksData;

registerPaymentMethod( bookingsPaymentMethod );

if ( settings.is_enabled ) {
	// Set the payment method as active when the checkout form is rendered.
	addAction(
		'experimental__woocommerce_blocks-checkout-render-checkout-form',
		'woocommerce-bookings-gateway',
		() => dispatch( PAYMENT_STORE_KEY ).__internalSetActivePaymentMethod( 'wc-bookings-gateway' )
	);
}
