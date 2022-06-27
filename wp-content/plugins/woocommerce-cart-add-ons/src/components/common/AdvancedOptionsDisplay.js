/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Component for outputting category or product and the products that should be displayed.
 *
 * @param {Object} props
 * @param {Array} props.list Array of chosen products or categories
 * @param {number} props.objectKey The key of wanted sub array
 * @return {JSX.Element} JSX Buttons
 */
const AdvancedOptionsDisplay = ( { list, objectKey } ) => {
	if ( typeof list === 'undefined' ) return <></>;

	if ( list.length === 0 ) return <></>;

	return (
		<>
			{ list.map( ( listElement ) => {
				if ( listElement[ objectKey ] !== null ) {
					return (
						<ul key={ listElement[ objectKey ].value }>
							<li>
								<p>
									<strong>
										{ listElement[ objectKey ].label }
									</strong>
									{ listElement[ objectKey ]
										?.itIncludesVariations
										? __(
												' (Including variations)',
												'sfn_cart_addons'
										  )
										: '' }
								</p>
								<ul className="wc-cart-addons-block-options-display">
									{ listElement.products.map( ( product ) => {
										return (
											<li key={ product.value }>
												{ product.label }
											</li>
										);
									} ) }
								</ul>
							</li>
						</ul>
					);
				}
				return null;
			} ) }
		</>
	);
};

export default AdvancedOptionsDisplay;
