/**
 * External dependencies
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import { getSetting } from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import ProductsWrapper from '../components/products-wrapper';
import PanelDefaultOptions from '../components/panels/default-options';
import PanelDefaultUpsells from '../components/panels/default-upsells';
import PanelCategoryMatches from '../components/panels/category-matches';
import PanelProductMatches from '../components/panels/product-matches';
import PanelLayoutOptions from '../components/panels/layout-options';

const Edit = ( props ) => {
	const [ allProducts, setAllProducts ] = useState( [] );
	const [ allCategories, setAllCategories ] = useState( [] );
	const { productCount } = getSetting( 'wcBlocksConfig' );
	const isLargeCatalog = productCount > 100;

	return (
		<>
			<InspectorControls>
				<PanelDefaultOptions { ...props } />
				<PanelDefaultUpsells
					allProducts={ allProducts }
					setAllProducts={ setAllProducts }
					isLargeCatalog={ isLargeCatalog }
					{ ...props }
				/>
				<PanelCategoryMatches
					allProducts={ allProducts }
					allCategories={ allCategories }
					setAllProducts={ setAllProducts }
					setAllCategories={ setAllCategories }
					isLargeCatalog={ isLargeCatalog }
					{ ...props }
				/>
				<PanelProductMatches
					allProducts={ allProducts }
					setAllProducts={ setAllProducts }
					isLargeCatalog={ isLargeCatalog }
					{ ...props }
				/>
				<PanelLayoutOptions { ...props } />
			</InspectorControls>
			<div { ...useBlockProps() }>
				<ProductsWrapper isEditing={ true } { ...props } />
			</div>
		</>
	);
};

export default Edit;
