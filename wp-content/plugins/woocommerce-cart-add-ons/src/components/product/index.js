/* eslint-disable jsx-a11y/alt-text */
/**
 * External dependencies
 */
import { decodeEntities } from '@wordpress/html-entities';
import { PLACEHOLDER_IMG_SRC } from '@woocommerce/settings';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import ProductButton from '../product-button';

const Product = ( props ) => {
	const { product, isEditing } = props;

	const imageProps = product.thumbnail
		? {
				src: product.thumbnail,
				alt:
					decodeEntities( product.thumbnail_alt ) ||
					__( 'Product Image', 'sfn_cart_addons' ),
		  }
		: {
				src: PLACEHOLDER_IMG_SRC,
				alt: '',
		  };
	return (
		<>
			{ isEditing ? (
				<div className="wc-block-grid__product-image">
					<img
						className="wc-block-components-product-image"
						{ ...imageProps }
					/>
					<div className="wc-block-grid__product-title">
						{ product.name }
					</div>
				</div>
			) : (
				<a
					className="wc-block-grid__product-link"
					href={ product.permalink }
					rel="nofollow"
					tabIndex="-1"
				>
					<div className="wc-block-grid__product-image">
						<img
							className="wc-block-components-product-image"
							{ ...imageProps }
						/>
					</div>
					<div className="wc-block-grid__product-title">
						{ product.name }
					</div>
				</a>
			) }
			<div
				className="wc-block-grid__product-price price"
				dangerouslySetInnerHTML={ { __html: product.price_html } }
			></div>
			<ProductButton product={ product } isEditing={ isEditing } />
		</>
	);
};

export default Product;
