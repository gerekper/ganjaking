/**
 * External dependencies
 */
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { getSetting } from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import bookingsPaymentMethod from './check-availability';

const settings = getSetting( 'wc-bookings-gateway_data', {} );

if ( settings.is_enabled ) {
	registerPaymentMethod( bookingsPaymentMethod );
}
