/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import Save from './save';
import './index.scss';
import block from '../block.json';
import cartIcon from './utils';

registerBlockType( 'woocommerce/cart-add-ons', {
	...block,
	icon: {
		foreground: '#7f54b3',
		src: cartIcon,
	},
	edit: Edit,
	save: Save,
} );
