/**
 * WordPress dependencies
 */
import { InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const wrapperClass = ' ywsbs-plans ' + ' plans-' + attributes.plans;
	return (
		<div className={wrapperClass}>
			<InnerBlocks.Content/>
		</div>
	);
}
