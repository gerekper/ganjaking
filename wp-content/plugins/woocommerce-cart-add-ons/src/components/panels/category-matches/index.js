/**
 * External dependencies
 */
import { Panel, PanelBody, Button } from '@wordpress/components';
import { plus } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import AdvancedOptionsDisplay from '../../common/AdvancedOptionsDisplay';
import DoneCloseMatchesButtons from '../../common/DoneCloseMatchesButtons';
import { HelpLabel, HelpEmpty } from '../../help';
import EditMatchesButton from '../../common/EditMatchesButton';
import SortableListItems from '../../sortable/sortableListItems';

import { cleanCategoryData } from '../../../utils';

const PanelCategoryMatches = ( props ) => {
	const newItem = {
		category: null,
		products: [],
	};

	const { setAttributes, allProducts, allCategories, isLargeCatalog } = props;
	const [ isEditing, setIsisEditing ] = useState( false );
	const [ sortableArray, setSortableArray ] = useState( [
		...props.attributes.categoryMatches,
	] );
	const [ shouldUpdate, setShouldUpdate ] = useState( false );

	const setDoneButton = () => {
		const cleanArray = cleanCategoryData( sortableArray, 'category' );
		setSortableArray( cleanArray );
		setAttributes( { categoryMatches: cleanArray } );
		setShouldUpdate( false );
		setIsisEditing( false );
	};

	const setCancelButton = () => {
		setSortableArray( [ ...props.attributes.categoryMatches ] );
		setIsisEditing( false );
	};

	const addMatch = () => {
		setSortableArray( [ ...( sortableArray ?? [] ), newItem ] );
	};

	return (
		<Panel className="wc-cart-addons-block-panel">
			<PanelBody title="Category Matches" initialOpen={ true }>
				{ isEditing ? (
					<>
						<HelpLabel
							text={ __(
								'Press the Add Category Match button to add some category matches. If a product in the shopping cart matches a category defined below, the cart upsells will display the matching products to show. Hit done to return to the review screen. ',
								'sfn_cart_addons'
							) }
						/>
						<SortableListItems
							useDragHandle
							sortableArray={ sortableArray }
							setSortableArray={ setSortableArray }
							setShouldUpdate={ setShouldUpdate }
							allProducts={ allProducts }
							allCategories={ allCategories }
							isLargeCatalog={ isLargeCatalog }
							helpText={ __(
								'Select a category for product matches',
								'sfn_cart_addons'
							) }
							isProduct={ false }
						/>

						<Button
							icon={ plus }
							variant="primary"
							onClick={ addMatch }
							className="wc-cart-addons-block__add-match"
						>
							<span>
								{ __(
									'Add category matches',
									'sfn_cart_addons'
								) }
							</span>
						</Button>
						<DoneCloseMatchesButtons
							setCancelButton={ setCancelButton }
							setDoneButton={ setDoneButton }
							shouldUpdate={ shouldUpdate }
						/>
					</>
				) : (
					<>
						{ props.attributes.categoryMatches.length > 0 ? (
							<AdvancedOptionsDisplay
								list={ props.attributes.categoryMatches }
								objectKey="category"
							/>
						) : (
							<HelpEmpty
								text={ __(
									'You haven’t added any category matches yet.',
									'sfn_cart_addons'
								) }
							/>
						) }

						<EditMatchesButton
							{ ...props }
							setIsisEditing={ setIsisEditing }
							buttonText={ __(
								'Edit category matches',
								'sfn_cart_addons'
							) }
						/>
					</>
				) }
			</PanelBody>
		</Panel>
	);
};

export default PanelCategoryMatches;
