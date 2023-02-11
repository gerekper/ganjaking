/**
 * External dependencies
 */
import { decodeEntities } from '@wordpress/html-entities';
import { __ } from '@wordpress/i18n';
import { useState, useEffect, createElement } from '@wordpress/element';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import { PAYMENT_METHOD_NAME } from './constants';
import { getPurchaseOrderServerData } from './purchase-order-utils';

/**
 * Renders a Purchase Order Number input.
 *
 * @param {Object}   props          Incoming props for the component.
 * @param {string}   props.id       The id attribute for the input.
 * @param {string}   props.value    The value of the input.
 * @param {Function} props.onChange The callback to be invoked when the input value changes.
 */
const PONumber = ({ id, value = '', onChange }) => {
	return (
		<ValidatedTextInput
			id={id}
			type="text"
			required={true}
			label={__('Purchase Order', 'woocommerce-gateway-purchase-order')}
			value={value}
			onChange={onChange}
		/>
	);
};

const Content = ({ eventRegistration, emitResponse }) => {
	const { onPaymentProcessing } = eventRegistration;
	const [poNumber, setPONumber] = useState('');
	useEffect(() => {
		const unsubscribe = onPaymentProcessing(() => {
			const paymentMethodData = { po_number_field: poNumber };
			return {
				type: emitResponse.responseTypes.SUCCESS,
				meta: {
					paymentMethodData,
				},
			};
		});
		return unsubscribe;
	}, [emitResponse.responseTypes.SUCCESS, onPaymentProcessing, poNumber]);
	return (
		<>
			{decodeEntities(getPurchaseOrderServerData()?.description || '')}
			<PONumber
				id="po_number_field"
				value={poNumber}
				onChange={setPONumber}
			/>
		</>
	);
};

const Label = (props) => {
	const { PaymentMethodLabel } = props.components;

	const labelText = getPurchaseOrderServerData().title
		? getPurchaseOrderServerData().title
		: __('Purchase Order', 'woocommerce-gateway-purchase-order');

	return <PaymentMethodLabel text={labelText} />;
};

registerPaymentMethod({
	name: PAYMENT_METHOD_NAME,
	label: <Label />,
	ariaLabel: __(
		'Purchase Order Payment Gateway',
		'woocommerce-gateway-purchase-order'
	),
	canMakePayment: () => true,
	content: <Content />,
	edit: <Content />,
	supports: {
		features: getPurchaseOrderServerData()?.supports ?? [],
	},
});
