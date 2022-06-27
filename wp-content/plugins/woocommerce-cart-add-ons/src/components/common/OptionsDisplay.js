/**
 * Component for outputting the default product matches.
 *
 * @param {Object} props
 * @param {Array} props.list Array of chosen products or categories
 * @return {JSX} JSX Buttons
 */

const OptionsDisplay = ( props ) => {
	if ( props.list.length === 0 ) return <></>;

	return (
		<ul className="wc-cart-addons-block-options-display">
			{ props.list.map( ( x ) => {
				return (
					<li key={ x.value } className="label">
						{ x.label }
					</li>
				);
			} ) }
		</ul>
	);
};

export default OptionsDisplay;
