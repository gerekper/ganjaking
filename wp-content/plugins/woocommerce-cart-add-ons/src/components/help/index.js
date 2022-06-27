export const HelpLabel = ( props ) => {
	return <p className="wc-cart-addons-block__help-text">{ props.text }</p>;
};

export const HelpEmpty = ( props ) => {
	return (
		<p className="wc-cart-addons-block__help-text">
			<em>{ props.text }</em>
		</p>
	);
};
