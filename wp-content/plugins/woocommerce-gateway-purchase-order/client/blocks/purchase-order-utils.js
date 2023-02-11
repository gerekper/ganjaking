/**
 * External dependencies
 */
import { getSetting } from '@woocommerce/settings';

/**
 * Purchase order data comes from the server passed on a global object.
 */
export const getPurchaseOrderServerData = () => {
	const purchaseOrderServerData = getSetting(
		'woocommerce_gateway_purchase_order_data',
		null
	);
	if (!purchaseOrderServerData) {
		throw new Error(
			'Purchase order gateway initialization data is not available'
		);
	}
	return purchaseOrderServerData;
};
