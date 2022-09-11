import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';
import {getCurrentDates, appendTimestamp, getIntervalForQuery} from '@woocommerce/date';



export const getLostSubscribersReportData = async (options = {}) => {
	const response = {
		isEmpty: false,
		isError: false,
		data: {},
		totalResults: 0,
		totalPages: 0
	};

	const requestQuery = getLostSubscribersRequestQuery(options);
	const report = await getLostSubscribersReport(requestQuery);

	if (report.error) {
		return {...response, isError: true};
	} else if (!report || !report.data) {
		return {...response, isEmpty: true};
	}
	const {totalResults, totalPages, data} = report;
	return {...response, totalResults, totalPages, data};
};

function getLostSubscribersRequestQuery(query) {
	const datesFromQuery = getCurrentDates(query);
	const interval = getIntervalForQuery(query);
	const end = datesFromQuery.primary.before;

	let orderby = 'cancelled_date';

	const $requestArgs = {
		order: query.order,
		orderby: orderby,
		interval,
		per_page: 5,
		page: 1,
		after: appendTimestamp(datesFromQuery.primary.after, 'start'),
		before: appendTimestamp(end, 'end'),
	};

	return $requestArgs;
}

export const getLostSubscribersReport = async (options = {}) => {

	try {
		const response = await apiFetch({
			path: addQueryArgs(`yith-ywsbs/reports/lost-subscribers`, options),
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

