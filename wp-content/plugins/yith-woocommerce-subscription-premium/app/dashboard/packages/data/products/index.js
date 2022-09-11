import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';
import {getCurrentDates, appendTimestamp, getIntervalForQuery} from '@woocommerce/date';

export const getProductReportData = async (options = {}) => {
	const response = {
		isEmpty: false,
		isError: false,
		data: {},
		totalResults: 0,
		totalPages: 0
	};

	const requestQuery = getProductsRequestQuery(options);
	const report = await getProductsReport(requestQuery);

	if (report.error) {
		return {...response, isError: true};
	} else if (!report || !report.data) {
		return {...response, isEmpty: true};
	}
	const {totalResults, totalPages, data} = report;
	return {...response, totalResults, totalPages, data};
};
function getProductsRequestQuery(query) {
	const datesFromQuery = getCurrentDates(query);
	const interval = getIntervalForQuery(query);
	const end = datesFromQuery.primary.before;
	const per_page = query?.per_page ? query.per_page : 25;
	const paged = query?.paged ? query.paged : 1;
	const $requestArgs = {
		order: query.order,
		orderby: query.orderby,
		interval,
		per_page: per_page,
		page: paged,
		after: appendTimestamp(datesFromQuery.primary.after, 'start'),
		before: appendTimestamp(end, 'end'),
		products: query?.products ? query.products : ''
	};

	return $requestArgs;
}
export const getProductsReport = async (options = {}) => {

	try {
		const response = await apiFetch({
			path: addQueryArgs(`yith-ywsbs/reports/products`, options),
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
