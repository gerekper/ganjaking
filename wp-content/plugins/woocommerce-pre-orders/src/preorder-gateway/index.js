/**
 * External dependencies
 */
import { getSetting } from '@woocommerce/settings';
import { decodeEntities } from '@wordpress/html-entities';

const PAYMENT_METHOD_NAME = 'pre_orders_pay_later';
const settings = getSetting( 'pre_orders_pay_later_data', {} );
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
 * Pre-Orders payment method config object.
 */
const preOrdersPaymentMethod = {
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

export default preOrdersPaymentMethod;
