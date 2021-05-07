import { megaphone } from '@wordpress/icons';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

addFilter(
	'woocommerce_admin_homescreen_quicklinks',
	'woocommerce-gpf',
	( quickLinks ) => {
		return [
			...quickLinks,
			{
				title: __('WooCommerce Product Feeds', 'woocommerce_gpf'),
				href: woocommerce_gpf_store_management_links_data.settings_link,
				icon: megaphone,
			},
		];
	}
);
