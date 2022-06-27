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
import EditMatchesButton from '../../common/EditMatchesButton';
import DoneCloseMatchesButtons from '../../common/DoneCloseMatchesButtons';
import SortableListItems from '../../sortable/sortableListItems';
import { HelpLabel, HelpEmpty } from '../../help';
import { cleanProductData } from '../../../utils';

const PanelProductMatches = ( props ) => {
	const newItem = {
		product: null,
		products: [],
	};

	const { setAttributes } = props;
	const [ isEditing, setIsisEditing ] = useState( false );
	const [ sortableArray, setSortableArray ] = useState( [
		...props.attributes.productMatches,
	] );

	const [ shouldUpdate, setShouldUpdate ] = useState( false );

	const setDoneButton = () => {
		const cleanArray = cleanProductData( sortableArray, 'product' );
		setSortableArray( cleanArray );
		setAttributes( { productMatches: cleanArray } );
		setShouldUpdate( false );
		setIsisEditing( false );
	};

	const setCancelButton = () => {
		setSortableArray( [ ...props.attributes.productMatches ] );
		setIsisEditing( false );
	};

	const addMatch = () => {
		const items = sortableArray;
		setSortableArray( [ ...( items ?? [] ), newItem ] );
	};

	return (
		<Panel className="wc-cart-addons-block-panel">
			<PanelBody title="Product Matches" initialOpen={ true }>
				{ isEditing ? (
					<>
						<HelpLabel
							text={ __(
								'Press the Add Product Match button to add some product matches. If a product in the shopping cart matches one of the products defined below, the cart upsells will display the matching products below to show. Hit done to return to the review screen. ',
								'sfn_cart_addons'
							) }
						/>
						<SortableListItems
							{ ...props }
							listItems={ sortableArray }
							useDragHandle
							sortableArray={ sortableArray }
							setSortableArray={ setSortableArray }
							setShouldUpdate={ setShouldUpdate }
							helpText={ __(
								'Select a product for product matches',
								'sfn_cart_addons'
							) }
							isProduct={ true }
						/>
						<Button
							icon={ plus }
							variant="primary"
							onClick={ addMatch }
							className="wc-cart-addons-block__add-match"
						>
							{ __( 'Add Product Match', 'sfn_cart_addons' ) }
						</Button>

						<DoneCloseMatchesButtons
							setCancelButton={ setCancelButton }
							setDoneButton={ setDoneButton }
							shouldUpdate={ shouldUpdate }
						/>
					</>
				) : (
					<>
						{ props.attributes.productMatches.length > 0 ? (
							<AdvancedOptionsDisplay
								list={ props.attributes.productMatches }
								objectKey="product"
							/>
						) : (
							<HelpEmpty
								text={ __(
									'You haven’t added any product matches yet.',
									'sfn_cart_addons'
								) }
							/>
						) }

						<EditMatchesButton
							{ ...props }
							setIsisEditing={ setIsisEditing }
							buttonText={ __(
								'Edit Product Matches',
								'sfn_cart_addons'
							) }
						/>
					</>
				) }
			</PanelBody>
		</Panel>
	);
};

export default PanelProductMatches;
