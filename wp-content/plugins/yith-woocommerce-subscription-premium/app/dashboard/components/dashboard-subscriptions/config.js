import { __, _n, _x, _nx } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';

const DASHBOARD_SUBSCRIPTION_CHARTS_FILTER = 'yith_ywsbs_dashboard_subscription_charts';

export const charts = applyFilters( DASHBOARD_SUBSCRIPTION_CHARTS_FILTER, [
	{
		key  : 'total_revenue',
		label: __( 'Total Net Sale', 'yith-woocommerce-subscription' ),
		type: 'currency',
		order: 'desc',
		orderby: 'date_created',
	},
	{
		key  : 'subscriptions_count',
		label: __( 'New Subscriptions', 'yith-woocommerce-subscription' ),
		type : 'number',
		order: 'desc',
		orderby: 'date_created',
	},
	{
		key: 'net_revenue',
		label: __( 'Net Sales of new subscriptions', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'net_total',
		type: 'currency',
	},
	{
		key  : 'renews_count',
		label: __( 'Renewed subscriptions', 'yith-woocommerce-subscription' ),
		type : 'number',
		order: 'desc',
		orderby: 'date_created',
	},
	{
		key: 'renews_net_revenue',
		label: __( 'Net Sales of renewed subscriptions', 'yith-woocommerce-subscription' ),
		type: 'currency',
		order: 'desc',
		orderby: 'date_created',
	},
	{
		key: 'cancelled_subscriptions',
		label: __( 'Cancelled subscriptions', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'cancelled_date',
	},
	{
		key: 'trial',
		label: __( 'New Trials', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'status',
	},

	{
		key: 'conversions',
		label: __( 'Trial conversions', 'yith-woocommerce-subscription' ),
		order: 'desc',
		orderby: 'trial',
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
		orderby: 'arr',
		type: 'currency'
	},
] );