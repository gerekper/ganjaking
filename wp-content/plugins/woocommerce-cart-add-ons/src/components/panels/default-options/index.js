/**
 * External dependencies
 */
import { RangeControl, Panel, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const PanelDefaultOptions = ( props ) => {
	const { attributes, setAttributes } = props;
	const { numberOfProducts } = attributes;

	return (
		<Panel className="wc-cart-addons-block-panel">
			<PanelBody
				title={ __( 'Default Options', 'sfn_cart_addons' ) }
				initialOpen={ true }
			>
				<RangeControl
					label={ __(
						'Maximum number of upsells to show',
						'sfn_cart_addons'
					) }
					value={ numberOfProducts }
					onChange={ ( products ) =>
						setAttributes( { numberOfProducts: products } )
					}
					min={ 1 }
					max={ 10 }
					help={ __(
						'This defines the number of products that the default view will display.',
						'sfn_cart_addons'
					) }
				/>
			</PanelBody>
		</Panel>
	);
};

export default PanelDefaultOptions;
