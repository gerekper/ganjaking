/**
 * External dependencies
 */
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import ProductsWrapper from './components/products-wrapper';

/**
 * This allows the block to render React components on the frontend.
 */
// eslint-disable-next-line @wordpress/no-global-event-listener
window.addEventListener( 'load', function () {
	const wrapper = document.querySelectorAll(
		'.wc-blocks-cart-addons-wrapper'
	);
	if ( wrapper ) {
		Array.from( wrapper ).forEach( ( element ) => {
			const attributes = { ...element.dataset };
			Object.keys( attributes ).forEach( ( key ) => {
				try {
					attributes[ key ] = JSON.parse( attributes[ key ] );
				} catch ( e ) {
					// We just ignore if it doesn't need to be parsed.
				}
			} );
			render(
				<ProductsWrapper
					isEditing={ false }
					attributes={ attributes }
				/>,
				element
			);
		} );
	}
} );
