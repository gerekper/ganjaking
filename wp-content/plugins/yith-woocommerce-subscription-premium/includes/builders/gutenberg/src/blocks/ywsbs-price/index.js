import {registerBlockType} from '@wordpress/blocks';
import {__} from '@wordpress/i18n';
import {attributesPrice as attributes, yith_icon} from "../../common";
import edit from './edit';
import save from './save';

const blockConfig = {
	title: __( 'Subscription Price', 'yith-woocommerce-subscription' ),
	description: __( 'Add subscription price inside the Subscription plan', 'yith-woocommerce-subscription' ),
	icon: yith_icon,
	category: 'yith-blocks',
	parent:['yith/ywsbs-plan'],
    styles: [
                {
                    name: '',
                    label: __('Billing period inline', 'yith-woocommerce-subscription'),

                },
                {
                    name: 'on-top',
                    label: __('Billing period on top', 'yith-woocommerce-subscription'),

                },
                {
                    name: 'on-bottom',
                    label: __('Billing period on bottom', 'yith-woocommerce-subscription'),
                },

    ],
	attributes,
	edit,
	save,
	"supports": {

    		"__experimentalColor": true,
    		"__experimentalLineHeight": true,
    		"__experimentalFontSize": true,
    	}
};

registerBlockType(
	'yith/ywsbs-price',
	{
		...blockConfig,
	}
);
