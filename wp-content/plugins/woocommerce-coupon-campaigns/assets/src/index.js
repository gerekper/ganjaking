/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * Add a coupon campaign filter to WC admin reports.
 *
 * @param {Array} filters Array of filters to process.
 * @returns {Array} Resulting array of filters to use on the page.
 */
const addCouponCampaignFilters = ( filters ) => {
	return [
		...filters,
		{
			label: __( 'Coupon Campaign', 'wc-coupon-campaigns' ),
			staticParams: [],
			param: 'coupon_campaign',
			showFilters: () => true,
			defaultValue: 'all',
			filters: [ ...( wcSettings.couponCampaigns || [] ) ].map(
				filter => {
					return {
						...filter,
						settings: {},
					};
				},
			),
		},
	];
};

addFilter(
	'woocommerce_admin_coupons_report_filters',
	'wc-coupon-campaigns',
	addCouponCampaignFilters,
	10,
);
