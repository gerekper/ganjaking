import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';
import {getCurrentDates, appendTimestamp, getIntervalForQuery} from '@woocommerce/date';

export const getSubscriptionsReportData = async (options = {}) => {
	const response = {
		isEmpty: false,
		isError: false,
		data: {},
		totalResults: 0,
		totalPages: 0
	};

	const requestQuery = getSubscriptionsRequestQuery(options);
	const report = await getSubscriptionsReport(requestQuery);

	if (report.error) {
		return {...response, isError: true};
	} else if (!report || !report.data) {
		return {...response, isEmpty: true};
	}
	const {totalResults, totalPages, data} = report;
	return {...response, totalResults, totalPages, data};
};

function getSubscriptionsRequestQuery(query) {
	const chart = query?.chart ? query.chart : '';
	const datesFromQuery = getCurrentDates(query);
	const interval = getIntervalForQuery(query);
	const end = datesFromQuery.primary.before;

	const valid_order_by = [
		'date_created',
		'subscription_id',
		'status',
		'product_name',
		'net_total'];

	let orderby = 'date_created';

	const paged = query?.paged ? query.paged : 1;
	const per_page = query?.per_page ? query.per_page : 25;

	if (valid_order_by.includes(query.orderby)) {
		orderby = query.orderby;
	}

	const $requestArgs = {
		order: query.order,
		orderby: orderby,
		interval,
		per_page: per_page,
		page: paged,
		after: appendTimestamp(datesFromQuery.primary.after, 'start'),
		before: appendTimestamp(end, 'end'),
		extended_info: true,
		conversions: 'conversions' === chart,
		renews: ('renews_count' === chart || 'renews_net_revenue' == chart),
		status_is: []
	};

	if ('trial' === chart) {
		$requestArgs.status_is = 'trial';
	}

	if ('cancelled_subscriptions' === chart) {
		$requestArgs.status_is = 'cancelled,expired';
	}

	return $requestArgs;
}
export const getSubscriptionsReport = async (options = {}) => {
	try {
		const response = await apiFetch({
			path: addQueryArgs(`yith-ywsbs/reports/subscriptions`, options),
			parse: false
		});
		const totalResults = parseInt(response.headers.get('x-wp-total'));
		const totalPages = parseInt(response.headers.get('x-wp-totalpages'));
		const report = await response.json();

		return {
			data: report,
			totalResults,
			totalPages
		};
	} catch (error) {
		return {error};
	}
};
