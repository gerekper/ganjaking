/**
 * External dependencies
 */
import { getSetting } from '@woocommerce/settings';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
const PAYMENT_METHOD_NAME = 'wc-bookings-gateway';
const settings = getSetting( 'wc-bookings-gateway_data', {} );
const label = decodeEntities( settings.title );
const orderButtonText = decodeEntities( settings.order_button_text );

/**
 * Content component
 */
const Content = () => {
	return decodeEntities( settings.description || '' );
};

/**
 * Label component
 *
 * @param {*} props Props from payment API.
 */
const Label = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	return <PaymentMethodLabel text={ label } />;
};

/**
 * Bookings payment method config object.
 */
const bookingsPaymentMethod = {
	name: PAYMENT_METHOD_NAME,
	content: <Content />,
	label: <Label />,
	edit: <Content />,
	canMakePayment: () => true,
	ariaLabel: label,
	supports: {
		features: settings.supports,
	},
	placeOrderButtonLabel: orderButtonText,
};

export default bookingsPaymentMethod;
