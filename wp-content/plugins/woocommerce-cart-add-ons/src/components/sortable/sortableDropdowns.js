/**
 * External dependencies
 */

import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import SelectMultipleItems from '../select/multiple-items';
import SelectSingleItem from '../select/single-item';
import { canUpdateData } from '../../utils';

/**
 * Wrapper Component checks if a category or product has been selected, and displays the product select if true, otherwise displays help text.
 *
 * @param {Object} props Incoming props for the component.
 * @param {number} props.order array key of the item in loop.
 * @param {Array} props.sortableArray Multidimentional array of categories or products and their associated product matches
 * @param {Function} props.setSortableArray Function that sets state for the sortableArray
 * @param {Function} props.setShouldUpdate Function that sets state for the ShouldUpdate variable
 * @param {boolean} props.isProduct Whether this is a dropdown product or a category
 */

const SortableDropdowns = ( props ) => {
	const {
		order,
		sortableArray,
		setSortableArray,
		setShouldUpdate,
		isProduct,
		allProducts,
		allCategories,
		isLargeCatalog,
	} = props;
	const objectKey = isProduct ? 'product' : 'category';
	const currentSelection = sortableArray[ order ]
		? sortableArray[ order ][ objectKey ]
		: '';

	const setProductVariations = ( checked ) => {
		const newArray = [ ...sortableArray ];
		newArray[ order ].product.itIncludesVariations = checked;

		setSortableArray( newArray );

		const canUpdate = canUpdateData(
			[ ...sortableArray ],
			order,
			objectKey
		);

		if ( canUpdate ) {
			setShouldUpdate( true );
		}
	};

	return (
		<div>
			<SelectSingleItem
				currentSelection={ currentSelection }
				order={ order }
				isProduct={ isProduct }
				setSortableArray={ setSortableArray }
				sortableArray={ sortableArray }
				setShouldUpdate={ setShouldUpdate }
				objectKey={ objectKey }
				allProducts={ allProducts }
				allCategories={ allCategories }
				isLargeCatalog={ isLargeCatalog }
			/>
			{ currentSelection && currentSelection?.variations?.length > 0 && (
				<ToggleControl
					className="wc-cart-addons-blocks-toggle-variations"
					label={ __( 'Include Variations', 'sfn_cart_addons' ) }
					checked={ currentSelection.itIncludesVariations }
					onChange={ ( checked ) => {
						setProductVariations( checked );
					} }
				/>
			) }
			{ currentSelection && (
				<SelectMultipleItems
					productsArray={ sortableArray }
					setProductsArray={ setSortableArray }
					order={ order }
					isMultiArray={ true }
					setShouldUpdate={ setShouldUpdate }
					allProducts={ allProducts }
					allCategories={ allCategories }
					isLargeCatalog={ isLargeCatalog }
				/>
			) }
		</div>
	);
};

export default SortableDropdowns;
