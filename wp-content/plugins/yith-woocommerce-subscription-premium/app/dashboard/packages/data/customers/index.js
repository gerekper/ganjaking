import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';
import {getCurrentDates, appendTimestamp, getIntervalForQuery} from '@woocommerce/date';

const MAX_PER_PAGE = 100;



/** CUSTOMERS **/
export const getCustomersReportData = async (options = {}) => {
	const response = {
		isEmpty: false,
		isError: false,
		data: {},
		totalResults: 0,
		totalPages: 0
	};

	const requestQuery = getCustomersRequestQuery(options);
	const report = await getCustomersReport(requestQuery);

	if (report.error) {
		return {...response, isError: true};
	} else if (!report || !report.data) {
		return {...response, isEmpty: true};
	}
	const {totalResults, totalPages, data} = report;
	return {...response, totalResults, totalPages, data};
};

function getCustomersRequestQuery(query) {
	const datesFromQuery = getCurrentDates(query);
	const interval = getIntervalForQuery(query);
	const end = datesFromQuery.primary.before;
	const type = 'subscribers-report' === query.tab ? 'dashboard' : 'leaderboard';
	const orderby = 'dashboard' === type ? query.orderby : 'total_paid';

	const $requestArgs = {
		order: query.order,
		orderby,
		interval,
		per_page: 'dashboard' === type ? 25 : 5,
		page: 1,
		after: appendTimestamp(datesFromQuery.primary.after, 'start'),
		before: appendTimestamp(end, 'end'),
		type

	};

	return $requestArgs;
}

export const getCustomersReport = async (options = {}) => {

	try {
		const response = await apiFetch({
			path: addQueryArgs(`yith-ywsbs/reports/customers`, options),
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

