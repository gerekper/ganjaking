/**
 * External dependencies
 */
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { __ } from '@wordpress/i18n';
import { getSetting, WC_ASSET_URL } from '@woocommerce/settings';
import { decodeEntities } from '@wordpress/html-entities';

const settings = getSetting( 'worldpay_data', {} );

/**
 * @typedef {import('@woocommerce/type-defs/registered-payment-method-props').RegisteredPaymentMethodProps} RegisteredPaymentMethodProps
 */

/**
 * Content component
 */
const Content = () => {
	return decodeEntities( settings.description || '' );
};

const worldpayPaymentMethod = {
	name: 'worldpay',
	label: (
		<img
			src={ `${ WC_ASSET_URL }/images/paypal.png` }
			alt={ decodeEntities(
				settings.title || __( 'Powered by Worldpay', 'woocommerce_worlday' )
			) }
		/>
	),
	placeOrderButtonLabel: __(
		'Proceed to Worldpay',
		'woocommerce_worlday'
	),
	content: <Content />,
	edit: <Content />,
	canMakePayment: () => true,
	ariaLabel: decodeEntities(
		settings.title ||
			__( 'Payment via Worldpay', 'woocommerce_worlday' )
	),
	supports: {
		features: settings.supports ?? [],
	},
};

registerPaymentMethod( worldpayPaymentMethod );