/**
 * External dependencies
 */
import { useState, useEffect } from '@wordpress/element';
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
 * Component used get the api data for multiple products, as well as the functions to update the product list.
 * These are then passed down to the DropdownSelector Component as props.
 *
 * @param {Object} props Incoming props for the component.
 * @param {Array} props.productsArray Array of products from state
 * @param {Function} props.setProductsArray Function to set state for products array
 * @param {boolean} props.isMultiArray Whether passed products array is part of a multidimensional array.
 * @param {number} props.order If productsArray is multidimensional, this is the key for array we wish to display.
 * @param {Function} props.setShouldUpdate Function to set state for the ShouldUpdate variable.
 * @param {Array} props.allProducts Array of all loaded products.
 * @param {boolean} props.isLargeCatalog If store has large catalog
 */

const SelectMultipleItems = ( {
	productsArray,
	setProductsArray,
	isMultiArray,
	order,
	setShouldUpdate,
	allProducts,
	isLargeCatalog,
} ) => {
	const [ list, setList ] = useState( allProducts );
	const checked = isMultiArray
		? productsArray[ order ].products
		: productsArray;

	const debounced = useDebouncedCallback( ( value ) => {
		loadDropdown( {
			setItem: setList,
			isProduct: true,
			exclude: '',
			searchData: value,
			isLargeCatalog,
		} );
	}, 500 );

	useEffect( () => {
		filterProducts( allProducts, checked, setList );
	}, [ allProducts, checked, setList ] );

	/**
	 * Product API call to get product name from ID
	 *
	 * @param {number} checkedValue Product ID
	 */

	async function removedProductCall( checkedValue, filterName ) {
		const removedProduct = {
			id: checkedValue,
			label: filterName,
		};
		announceFilterChange( { filterRemoved: removedProduct } );
	}

	/**
	 * Sets Attributes with new checked items
	 *
	 * @param {Object} newChecked Object with ID and Label of product
	 */
	const setChecked = ( newChecked ) => {
		if ( isMultiArray ) {
			const newCategoryMatches = updateCategoryData(
				[ ...productsArray ],
				order,
				newChecked,
				'products'
			);
			setProductsArray( newCategoryMatches );
			const canUpdate = canUpdateData(
				[ ...newCategoryMatches ],
				order,
				'products'
			);

			if ( canUpdate ) {
				setShouldUpdate( true );
			}

			return;
		}
		setShouldUpdate( true );
		setProductsArray( newChecked );
	};

	/**
	 * Remove object that has been deleted with a backspace.
	 *
	 * @param {Object} checkedValue ID of changed product
	 */
	const removeFromArray = ( checkedValue ) => {
		const currentAttr = [ ...checked ];

		const newChecked = currentAttr.filter( function ( ele ) {
			return ele.value !== checkedValue.value;
		} );

		setChecked( newChecked );
	};

	/**
	 * When a checkbox in the list changes, update attributes.
	 *
	 * @param {number | Object} checkedValue ID of changed product or product object
	 */
	const onChange = ( checkedValue ) => {
		// this is only an object if user deletes using backspace
		const checkType = typeof checkedValue === 'object';

		if ( checkType ) {
			removeFromArray( checkedValue );
			announceFilterChange( { filterRemoved: checkedValue } );
			return;
		}

		const previouslyChecked = checked.some(
			( e ) => e.value === checkedValue
		);

		const newChecked = checked.filter( ( value ) => {
			return value.value !== checkedValue;
		} );

		if ( ! previouslyChecked ) {
			const newProduct = getFilterNameFromValue( checkedValue, list );
			newChecked.push( newProduct );
			newChecked.sort();
			announceFilterChange( { filterAdded: newProduct } );
		} else {
			const filterName = getFilterNameFromValue( checkedValue, checked );
			removedProductCall( checkedValue, filterName );
		}

		setChecked( newChecked );
		filterProducts( list, newChecked, setList );

		if ( isLargeCatalog ) {
			setList( [] );
		}
	};

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
			placeholder={ __( 'Search Products', 'sfn_cart_addons' ) }
			checked={ checked }
			inputLabel={ __( 'Search Products', 'sfn_cart_addons' ) }
			multiple={ true }
			onChange={ onChange }
			options={ list }
			handleChange={ handleChange }
		/>
	);
};
export default SelectMultipleItems;
