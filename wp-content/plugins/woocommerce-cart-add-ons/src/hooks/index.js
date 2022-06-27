/**
 * External dependencies
 */
import { useSelect } from '@wordpress/data';

export const useStoreCart = () => {
	const CART_STORE_KEY = 'wc/store/cart';

	const results = useSelect( ( select ) => {
		const store = select( CART_STORE_KEY );
		const cartData = store.getCartData();
		return {
			cartItems: cartData.items,
		};
	} );

	return results;
};
