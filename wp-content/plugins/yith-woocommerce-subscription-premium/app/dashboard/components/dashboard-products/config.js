import {getProductLabels, getVariationLabels} from "../../lib/general";
import { __ } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';


const DASHBOARD_PRODUCTS_CHARTS_FILTER = 'yith_ywsbs_dashboard_products_charts';

export const charts = applyFilters( DASHBOARD_PRODUCTS_CHARTS_FILTER, [
	{
		key  : 'total_revenue',
		label: __( 'Total Net Sale', 'yith-woocommerce-subscription' ),
		type: 'currency',
		order: 'desc',
		orderby: 'subscribers',
	},
	{
		key  : 'subscriptions_count',
		label: __( 'New Subscriptions', 'yith-woocommerce-subscription' ),
		type : 'number',
		order: 'desc',
		orderby: 'subscribers',
	},
	{
		key: 'net_revenue',
		label: __( 'Net Sales of new subscriptions', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'subscribers',
		type: 'currency',
	},
	{
		key  : 'renews_count',
		label: __( 'Renewal orders', 'yith-woocommerce-subscription' ),
		type : 'number',
		order: 'desc',
		orderby: 'subscribers',
	},
	{
		key: 'renews_net_revenue',
		label: __( 'Net Sales of renewed subscriptions', 'yith-woocommerce-subscription' ),
		type: 'currency',
		order: 'desc',
		orderby: 'subscribers',
	},
	{
		key: 'cancelled_subscriptions',
		label: __( 'Cancelled subscriptions', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'subscribers',
	},
	{
		key: 'trial',
		label: __( 'New Trials', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'subscribers',
	},
	{
		key: 'conversions',
		label: __( 'Trial conversions', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'subscribers',
	},
	{
		key: 'mrr',
		label: __( 'MRR (Monthly Recurring Revenue)', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'mrr',
		type: 'currency'
	},
	{
		key: 'arr',
		label: __( 'ARR (Annual Recurring Revenue)', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'subscribers',
		type: 'currency'
	},
] );

const PRODUCTS_REPORT_FILTERS_FILTER =
	'ywsbs_admin_products_report_filters';
const filterConfig = {
	label: __( 'Show', 'yith-woocommerce-subscription' ),
	staticParams: [ 'chartType', 'paged', 'per_page' ],
	param: 'filter',
	showFilters: () => true,
	filters: [
		{ label: __( 'All Products', 'yith-woocommerce-subscription' ), value: 'all' },
		{
			label: __( 'Single Product', 'yith-woocommerce-subscription' ),
			value: 'select_product',
			chartMode: 'item-comparison',
			subFilters: [
				{
					component: 'Search',
					value: 'single_product',
					chartMode: 'item-comparison',
					path: [ 'select_product' ],
					settings: {
						type: 'products',
						param: 'products',
						getLabels: getProductLabels,
						labels: {
							placeholder: __(
								'Type to search for a product',
								'yith-woocommerce-subscription'
							),
							button: __( 'Single Product', 'yith-woocommerce-subscription' ),
						},
					},
				},
			],
		
		},
	],
};

const variationsConfig = {
	showFilters: ( query ) =>
		query.filter === 'single_product' &&
		!! query.products &&
		query[ 'is-variable' ],
	staticParams: [ 'filter', 'products', 'chartType', 'paged', 'per_page' ],
	param: 'filter-variations',
	filters: [
		{
			label: __( 'All Variations', 'yith-woocommerce-subscription' ),
			chartMode: 'item-comparison',
			value: 'all',
		},
		{
			label: __( 'Single Variation', 'yith-woocommerce-subscription' ),
			value: 'select_variation',
			subFilters: [
				{
					component: 'Search',
					value: 'single_variation',
					path: [ 'select_variation' ],
					settings: {
						type: 'variations',
						param: 'variations',
						getLabels: getVariationLabels,
						labels: {
							placeholder: __(
								'Type to search for a variation',
								'yith-woocommerce-subscription'
							),
							button: __(
								'Single Variation',
								'yith-woocommerce-subscription'
							),
						},
					},
				},
			],
		}
	],
};

export const filters = applyFilters( PRODUCTS_REPORT_FILTERS_FILTER, [
	filterConfig,
	variationsConfig,
] );