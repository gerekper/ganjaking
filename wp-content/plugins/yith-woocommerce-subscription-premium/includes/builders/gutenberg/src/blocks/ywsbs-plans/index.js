import {registerBlockType} from '@wordpress/blocks';
import {__} from '@wordpress/i18n';
import {yith_icon, attributesPlans as attributes} from "../../common";
import edit from './edit';
import save from './save';

const blockConfig = {
	title: __( 'Subscription Plans', 'yith-woocommerce-subscription' ),
	description: __(
		'Add subscription table price',
		'yith-woocommerce-subscription'
	),
icon: yith_icon,
category: 'yith-blocks',
	attributes,
example: {
	attributes: {
		preview: true,
	},
	},
	edit,
	save
};

registerBlockType(
	'yith/ywsbs-plans',
	{
		...blockConfig,
	}
);
