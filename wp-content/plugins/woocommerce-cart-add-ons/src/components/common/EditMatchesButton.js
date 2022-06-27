/**
 * External dependencies
 */
import { Button } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { loadDropdown } from '../../utils';

/**
 * Component for editing product matches
 *
 * @param {Object} props
 * @param {Function} props.setIsisEditing Callback function when button is clicked
 * @param {string} props.buttonText Text string for button
 * @return {JSX} JSX Buttons
 */

const EditMatchesButton = ( props ) => {
	const {
		setIsisEditing,
		buttonText,
		allProducts,
		setAllProducts,
		allCategories,
		setAllCategories,
		isLargeCatalog,
	} = props;
	return (
		<div className="wc-cart-addons-block__edit-done-link-wrapper">
			<Button
				variant="isLink"
				className="is-link"
				onClick={ () => {
					setIsisEditing( true );

					// Return Early if has large Catalog
					if ( isLargeCatalog ) return;

					// Get all products api call
					if (
						setAllProducts &&
						allProducts &&
						allProducts.length === 0
					) {
						loadDropdown( { setItem: setAllProducts } );
					}

					// Get all categories api call
					if (
						setAllCategories &&
						allCategories &&
						allCategories.length === 0
					) {
						loadDropdown( {
							setItem: setAllCategories,
							isProduct: false,
						} );
					}
				} }
			>
				{ buttonText }
			</Button>
		</div>
	);
};

export default EditMatchesButton;
