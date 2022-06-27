/**
 * External dependencies
 */
import { useState, useEffect, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useDebouncedCallback } from 'use-debounce';

/**
 * Internal dependencies
 */
import DropdownSelector from '../../dropdown-selector';
import {
	updateCategoryData,
	getFilterNameFromValue,
	announceFilterChange,
	canUpdateData,
	filterProducts,
	loadDropdown,
} from '../../../utils';

/**
 * Component used get the api data for single categories or products, as well as the functions to update the selected categories or products list.
 * These are then passed down to the DropdownSelector Component as props.
 *
 * @param {Object} props Incoming props for the component.
 * @param {string} props.currentSelection Array of products from state.
 * @param {number} props.order Key of the current item.
 * @param {Function} props.setSortableArray Updates the state for the sortable array
 * @param {Array} props.sortableArray Multidimentional array of all categories and products and their associated product matches.
 * @param {Function} props.setShouldUpdate Function to set state for the ShouldUpdate variable.
 * @param {boolean} props.isProduct Whether this should load products instead of Categories
 * @param {string} props.objectKey Object key either product | category
 */

const SelectSingleItem = ( {
	currentSelection,
	order,
	setSortableArray,
	sortableArray,
	setShouldUpdate,
	isProduct,
	objectKey,
	allCategories,
	allProducts,
	isLargeCatalog,
} ) => {
	const [ item, setItem ] = useState( [] );

	const placeholder = isProduct
		? __( 'Find a Product', 'sfn_cart_addons' )
		: __( 'Find a Category', 'sfn_cart_addons' );
	const inputLabel = isProduct
		? __( 'Search Products', 'sfn_cart_addons' )
		: __( 'Search Categories', 'sfn_cart_addons' );
	const list = isProduct ? allProducts : allCategories;

	const debounced = useDebouncedCallback( ( value ) => {
		const exclude = getAllMatchesIDs( sortableArray, true );

		loadDropdown( {
			setItem,
			isProduct,
			exclude,
			searchData: value,
			isLargeCatalog,
		} );
	}, 500 );

	useEffect( () => {
		const allSelected = getAllMatchesIDs( sortableArray );

		filterProducts( list, allSelected, setItem );
	}, [ list, getAllMatchesIDs, setItem, sortableArray ] );

	/**
	 * Sets Attributes with new checked items
	 *
	 * @param {Object} newChecked Object with ID and Label of product
	 */
	const setChecked = ( newChecked ) => {
		const newCategoryMatches = updateCategoryData(
			[ ...sortableArray ],
			order,
			newChecked,
			objectKey
		);
		setSortableArray( newCategoryMatches );
		const canUpdate = canUpdateData(
			[ ...sortableArray ],
			order,
			objectKey
		);

		// Remove already selected catories from the list
		const allSelected = getAllMatchesIDs( newCategoryMatches );
		filterProducts( list, allSelected, setItem );

		if ( canUpdate ) {
			setShouldUpdate( true );
		}
	};

	/**
	 * Remove object that has been deleted with a backspace.
	 */
	const removeFromArray = () => {
		const currentAttr = [ ...sortableArray ];

		currentAttr[ order ] = { category: null, products: [] };

		setSortableArray( currentAttr );
	};

	const onChange = ( checkedValue ) => {
		// this is only an object if user deletes using backspace
		const checkType = typeof checkedValue === 'object';

		if ( checkType ) {
			removeFromArray();
			announceFilterChange( { filterRemoved: checkedValue } );
			return;
		}

		const newChecked = getFilterNameFromValue( checkedValue, item );

		announceFilterChange( {
			changeCategoryName: newChecked,
		} );
		setChecked( newChecked );

		if ( isLargeCatalog ) {
			setItem( [] );
		}
	};

	/**
	 * Get List of IDs from all currently used categories or products
	 *
	 * @return {Array} Array of ID's
	 */

	const getAllMatchesIDs = useCallback(
		( checkItems, IDArray = false ) => {
			const filteredList = checkItems.filter( ( selected ) => {
				return selected[ objectKey ] !== null;
			} );

			// return array of ID's
			if ( IDArray ) {
				return filteredList.map( ( selected ) => {
					return selected[ objectKey ]
						? selected[ objectKey ].value
						: '';
				} );
			}

			// retrun array of objects
			return filteredList.map( ( selected ) => {
				return selected[ objectKey ] ? selected[ objectKey ] : '';
			} );
		},
		[ objectKey ]
	);

	const handleChange = ( event ) => {
		// return early if has a small catalog
		if ( ! isLargeCatalog ) return;
		// return early if using up and down arrows
		if ( event.key === 'ArrowDown' || event.key === 'ArrowUp' ) return;

		const { value: nextValue } = event.target;
		debounced( nextValue );
	};

	return (
		<DropdownSelector
			placeholder={ placeholder }
			checked={ currentSelection ? [ currentSelection ] : [] }
			inputLabel={ inputLabel }
			multiple={ false }
			onChange={ onChange }
			options={ item }
			handleChange={ handleChange }
		/>
	);
};
export default SelectSingleItem;
