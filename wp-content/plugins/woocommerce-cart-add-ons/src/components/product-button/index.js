/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Spinner } from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';

const ProductButton = ( props ) => {
	const { product, isEditing } = props;
	const [ addingToCart, setAddingToCart ] = useState( false );
	const { addItemToCart } = useDispatch( 'wc/store/cart' );

	const addProduct = () => {
		setAddingToCart( true );
		return addItemToCart( product.id, 1 )
			.then( () => {
				setAddingToCart( false );
			} )
			.catch( ( reason ) => {
				setAddingToCart( false );
				wp.data
					.dispatch( 'core/notices' )
					.createNotice( 'error', reason.message, {
						type: 'default',
						isDismissible: true,
						context: 'wc/cart',
					} );
			} );
	};

	const buttonHTML = () => {
		if ( ! product.is_purchasable && ! product.is_in_stock ) {
			return (
				<div className="wc-block-components-product-add-to-cart-unavailable">
					{ __(
						'Sorry, this product cannot be purchased.',
						'woo-gutenberg-products-block'
					) }
				</div>
			);
		}
		switch ( product.type ) {
			case 'simple':
				return (
					<button
						disabled={ isEditing ? true : false }
						aria-label={ `Add “${ product.name }“ to cart.` }
						className="
							wp-block-button__link
							add_to_cart_button"
						onClick={ () => ( isEditing ? null : addProduct() ) }
					>
						{ product.add_to_cart.text }
					</button>
				);
			default:
				if ( isEditing ) {
					return (
						<button
							disabled
							className="
								wp-block-button__link
								add_to_cart_button"
						>
							{ __(
								'Read more',
								'woo-gutenberg-products-block'
							) }
						</button>
					);
				}
				return (
					<a
						href={ product.add_to_cart.url }
						aria-label={ `Read more about “${ product.name }“.` }
						className="
							wp-block-button__link
							add_to_cart_button"
					>
						{ product.add_to_cart.text }
					</a>
				);
		}
	};

	return (
		<div
			className="
				wp-block-button
	        	wc-block-grid__product-add-to-cart
	        "
		>
			{ addingToCart ? <Spinner /> : buttonHTML() }
		</div>
	);
};

export default ProductButton;
