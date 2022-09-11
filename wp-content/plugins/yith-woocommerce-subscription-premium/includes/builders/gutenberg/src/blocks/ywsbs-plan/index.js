import {registerBlockType} from '@wordpress/blocks';
import {__} from '@wordpress/i18n';
import {attributesPlan as attributes, yith_icon} from "../../common";
import edit from './edit';
import save from './save';

const blockConfig = {
	title: __( 'Subscription Plan', 'yith-woocommerce-subscription' ),
	description: __( 'Add subscription table column', 'yith-woocommerce-subscription' ),
	icon: yith_icon,
	category: 'yith-blocks',
	parent:['yith/ywsbs-plans'],
	attributes,
	edit,
	save,
};

registerBlockType(
	'yith/ywsbs-plan',
	{
		...blockConfig,
	}
);
