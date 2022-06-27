/**
 * External dependencies
 */
import classNames from 'classnames';

const Save = ( props ) => {
	const { className } = props.attributes;
	return (
		<div
			className={ classNames(
				'wc-blocks-cart-addons-wrapper',
				className
			) }
		></div>
	);
};

export default Save;
