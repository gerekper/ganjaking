/**
 * External dependencies
 */
import { Panel, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import SelectMultipleItems from '../../select/multiple-items';
import DoneCloseMatchesButtons from '../../common/DoneCloseMatchesButtons';
import OptionsDisplay from '../../common/OptionsDisplay';
import { HelpLabel, HelpEmpty } from '../../help';
import EditMatchesButton from '../../common/EditMatchesButton';

const PanelDefaultUpsells = ( props ) => {
	const { attributes, allProducts, isLargeCatalog } = props;
	const { defaultAddons } = attributes;
	const [ isEditing, setIsisEditing ] = useState( false );
	const [ shouldUpdate, setShouldUpdate ] = useState( false );
	const [ defaultProducts, setDefaultProducts ] = useState( defaultAddons );
	const setDoneButton = () => {
		props.setAttributes( { defaultAddons: defaultProducts } );
		setShouldUpdate( false );
		setIsisEditing( false );
	};
	const setCancelButton = () => {
		setDefaultProducts( defaultAddons );
		setIsisEditing( false );
	};

	return (
		<Panel className="wc-cart-addons-block-panel">
			<PanelBody
				title={ __( 'Default Upsells', 'sfn_cart_addons' ) }
				initialOpen={ true }
			>
				<HelpLabel
					text={ __(
						'These products will be displayed on the cart page if there are no matching products and/or categories in the shopping cart from the settings below.',
						'sfn_cart_addons'
					) }
				/>
				{ isEditing ? (
					<>
						<SelectMultipleItems
							allProducts={ allProducts }
							setShouldUpdate={ setShouldUpdate }
							productsArray={ defaultProducts }
							setProductsArray={ setDefaultProducts }
							isLargeCatalog={ isLargeCatalog }
						/>
						<DoneCloseMatchesButtons
							setCancelButton={ setCancelButton }
							setDoneButton={ setDoneButton }
							shouldUpdate={ shouldUpdate }
						/>
					</>
				) : (
					<>
						{ defaultAddons.length > 0 ? (
							<OptionsDisplay list={ defaultAddons } />
						) : (
							<>
								<HelpEmpty
									text={ __(
										'You haven’t added any default upsells yet.',
										'sfn_cart_addons'
									) }
								/>
							</>
						) }
						<EditMatchesButton
							{ ...props }
							setIsisEditing={ setIsisEditing }
							buttonText={ __(
								'Edit default upsells',
								'sfn_cart_addons'
							) }
						/>
					</>
				) }
			</PanelBody>
		</Panel>
	);
};

export default PanelDefaultUpsells;
