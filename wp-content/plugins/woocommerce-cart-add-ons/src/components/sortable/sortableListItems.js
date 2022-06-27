/**
 * External dependencies
 */

import {
	SortableContainer,
	SortableElement,
	SortableHandle,
} from 'react-sortable-hoc';
import { Button, Icon } from '@wordpress/components';
import { trash } from '@wordpress/icons';
import { arrayMoveImmutable } from 'array-move';

/**
 * Internal dependencies
 */
import SortableDropdowns from './sortableDropdowns';
import { canUpdateData } from '../../utils';

/**
 * Sortable List Component
 *
 * @param {Object} props Incoming props for the component.
 * @param {Array} props.sortableArray Multidimensional array of categories or products and their associated product matches
 * @param {Function} props.setSortableArray Function that sets state for the sortableArray
 * @param {Function} props.setShouldUpdate Function that sets state for the ShouldUpdate variable
 * @param {boolean} props.isProduct Whether this is a dropdown product or a category
 * @param {string} props.helpText The text to display if no items in the array
 * @param {Array} props.listItems Array of category or product matches that are saved in state
 */

const DragHandle = SortableHandle( () => (
	<Icon icon="menu" className="wc-cart-addons-block-panel__sort" />
) );

const SortableItem = SortableElement(
	( {
		idx,
		sortableArray,
		objectKey,
		setSortableArray,
		setShouldUpdate,
		isProduct,
		deleteMatch,
		helpText,
		allProducts,
		allCategories,
		isLargeCatalog,
	} ) => {
		const productEmptyClass = ! sortableArray[ idx ][ objectKey ]
			? 'no-product-selected'
			: 'products-selected';
		return (
			<li className="wc-cart-addons-block-panel__list-item">
				<div className={ `sortableElement ${ productEmptyClass }` }>
					<DragHandle />
					<SortableDropdowns
						order={ idx }
						sortableArray={ sortableArray }
						setSortableArray={ setSortableArray }
						setShouldUpdate={ setShouldUpdate }
						isProduct={ isProduct }
						allProducts={ allProducts }
						allCategories={ allCategories }
						isLargeCatalog={ isLargeCatalog }
					/>
					{ sortableArray[ idx ].products.length > 0 && (
						<Button
							icon={ trash }
							variant="primary"
							onClick={ () => deleteMatch( idx ) }
							className="wc-cart-addons-block-panel__trash"
						/>
					) }
				</div>
				{ ! sortableArray[ idx ][ objectKey ] && (
					<p className="wc-cart-addons-block__help-text wc-cart-addons-block__cat-match">
						{ helpText }
					</p>
				) }
			</li>
		);
	}
);

const SortableList = SortableContainer( ( props ) => {
	return (
		<ul>
			{ props.sortableArray?.map( ( value, index ) => {
				let key = index;
				if ( value.hasOwnProperty( 'category' ) && value.category ) {
					key = value.category.value;
				}
				if ( value.hasOwnProperty( 'product' ) && value.product ) {
					key = value.product.value;
				}
				return (
					<SortableItem
						key={ `item-${ key }` }
						index={ index }
						idx={ index }
						{ ...props }
					/>
				);
			} ) }
		</ul>
	);
} );

const SortableListItems = ( props ) => {
	const {
		sortableArray,
		setSortableArray,
		setShouldUpdate,
		isProduct,
	} = props;
	const objectKey = isProduct ? 'product' : 'category';

	const onSortEnd = ( { oldIndex, newIndex } ) => {
		const newCategoryArray = [ ...sortableArray ];

		const newArray = arrayMoveImmutable(
			newCategoryArray,
			oldIndex,
			newIndex
		);

		const validateData = canUpdateData(
			[ ...sortableArray ],
			oldIndex,
			objectKey
		);

		if ( validateData ) {
			setShouldUpdate( true );
		}

		setSortableArray( newArray );
	};

	const deleteMatch = ( index ) => {
		const newCategoryArray = [ ...sortableArray ];
		newCategoryArray.splice( index, 1 );

		const validateData = canUpdateData(
			[ ...sortableArray ],
			index,
			objectKey
		);

		if ( validateData ) {
			setShouldUpdate( true );
		}
		setSortableArray( newCategoryArray );
	};

	return (
		<SortableList
			onSortEnd={ onSortEnd }
			useDragHandle
			deleteMatch={ deleteMatch }
			{ ...props }
		/>
	);
};
export default SortableListItems;
