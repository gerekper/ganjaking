/**
 * External dependencies
 */
import { RangeControl, Panel, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const PanelLayoutOptions = ( props ) => {
	const { attributes, setAttributes } = props;
	const { columns } = attributes;

	return (
		<Panel>
			<PanelBody title="Layout Options" initialOpen={ true }>
				<RangeControl
					label={ __( 'Columns', 'sfn_cart_addons' ) }
					value={ columns }
					onChange={ ( cols ) => setAttributes( { columns: cols } ) }
					min={ 1 }
					max={ 6 }
					help={ __(
						'This defines the number of columns that the cart add-ons view will display.',
						'sfn_cart_addons'
					) }
				/>
			</PanelBody>
		</Panel>
	);
};

export default PanelLayoutOptions;
