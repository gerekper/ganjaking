import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';
import {forEach} from "lodash";
import {getCurrentDates, appendTimestamp, getIntervalForQuery} from '@woocommerce/date';

const MAX_PER_PAGE = 100;

export const getProductsChartData = async (options = {}) => {
	const response = {
		isEmpty: false,
		isError: false,
		data: {
			totals: {},
			intervals: []
		}
	};

	const requestQuery = getRequestProductQuery(options);
	const stats = await getProductsStats(requestQuery);

	if (stats.error) {
		return {...response, isError: true};
	} else if (isReportDataEmpty(stats)) {
		return {...response, isEmpty: true};
	}

	const totals = (stats && stats.data && stats.data.totals) || null;
	let intervals = (stats && stats.data && stats.data.intervals) || [];

	if (stats.totalResults > MAX_PER_PAGE) {
		const pagedData = [];
		const totalPages = Math.ceil(stats.totalResults / MAX_PER_PAGE);
		let isError = false;

		let promises = [];

		for (let i = 2; i <= totalPages; i++) {
			const nextQuery = {...requestQuery, page: i};
			promises.push(getProductsStats(nextQuery));
		}

		const responses = await Promise.all(promises);

		for (let i = 0; i < responses.length; i++) {
			const _data = responses[i];
			if (_data.error) {
				isError = true;
				break;
			}

			pagedData.push(_data);
		}

		if (isError) {
			return {...response, isError: true};
		}

		forEach(pagedData, function (_data) {
			intervals = intervals.concat(_data.data.intervals);
		});
	}

	return {...response, data: {totals, intervals}};
};

export const getProductsStats = async (options = {}) => {

	try {
		const response = await apiFetch({
			path: addQueryArgs(`yith-ywsbs/reports/products/stats`, options),
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
		console.log('"********* ERROR ON REQUEST ************"');
		console.log(error);
		return {error};
	}
};

function getRequestProductQuery(options) {
	const {dataType, query} = options;
	const datesFromQuery = getCurrentDates(query);
	const interval = getIntervalForQuery(query);
	const end = datesFromQuery[dataType].before;

	const productQuery = {
		order: 'asc',
		interval,
		per_page: MAX_PER_PAGE,
		page: 1,
		after: appendTimestamp(datesFromQuery[dataType].after, 'start'),
		before: appendTimestamp(end, 'end'),
		segmentby: query.segmentby,
		products: query?.products ? query.products : ''
	}

	return productQuery;
}

function isReportDataEmpty(report) {

	if (!report) {
		return true;
	}

	if (!report.data) {
		return true;
	}

	return false;
}
