import React, {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {SectionHeader,Link} from '@woocommerce/components';
import {ReportTable} from "../report-table";
import {isEqual} from "lodash";
import {getLostSubscribersReportData} from "../../packages/data/lost-subscribers";
import {getCustomersReportData} from "../../packages/data/customers";
import {getProductReportData} from "../../packages/data/products";
import {formatCurrency} from "../../lib/numbers";
import {getStatusBlock, getProductReportLink, getCustomerName} from "../../lib/general";

class DashboardLeaderboards extends Component {
	constructor() {
		super(...arguments);

		this.state = {
			productIsRequesting: false,
			productReport: [],
			productTotalResults: 0,
			productTotalPages: 0,
			customerIsRequesting: false,
			customerReport: [],
			customerTotalResults: 0,
			customerTotalPages: 0,
			lostSubscriberIsRequesting: false,
			lostSubscriberReport: [],
			lostSubscriberTotalResults: 0,
			lostSubscriberTotalPages: 0,
		};

	}

	componentDidMount() {
		this.update();
	}

	getQueryWithoutChart = (query) => {
		const {chart, chartType, ...newQuery} = query;
		return {...newQuery};
	};

	componentDidUpdate(prevProps, prevState) {
		if (!isEqual(prevProps.query, this.props.query)) {
			const prevQuery = this.getQueryWithoutChart(prevProps.query);
			const currentQuery = this.getQueryWithoutChart(this.props.query);

			if (!isEqual(prevQuery, currentQuery)) {
				this.update();
			} else {
				this.setState({});
			}
		}
	}

	update = () => {
		const {query} = this.props;
		this.setState({productIsRequesting: true, customerIsRequesting: true, lostSubscriberIsRequesting:true });

		getProductReportData({...query, orderby:'subscribers', order:'desc', paged:1, per_page:5}).then(report => {
			const {data, totalResults, totalPages} = report;
			this.setState({
				productReport: data,
				productIsRequesting: false,
				productTotalResults: totalResults,
				productTotalPages: totalPages
			});
		});

		getCustomersReportData({...query, orderby:'total_paid', order:'desc', paged:1, per_page:5}).then(report => {
			const {data, totalResults, totalPages} = report;
			this.setState({
				customerReport: data,
				customerIsRequesting: false,
				customerTotalResults: totalResults,
				customerTotalPages: totalPages
			});
		});

		getLostSubscribersReportData({...query, orderby:'cancelled_date', order:'desc', paged:1, per_page:5}).then(report => {
			const {data, totalResults, totalPages} = report;
			this.setState({
				lostSubscriberReport: data,
				lostSubscriberIsRequesting: false,
				lostSubscriberTotalResults: totalResults,
				lostSubscriberTotalPages: totalPages
			});
		});

	};
	getProductsHeadersContent = () => {
		let headerContent = [
			{
				label: __('Product name', 'yith-woocommerce-subscription'),
				key: 'product_name',
				isSortable: false,
			},
			{
				label: __('Subscribers', 'yith-woocommerce-subscription'),
				key: 'subscribers',
				isSortable: false
			},
			{
				label: __('MRR', 'yith-woocommerce-subscription'),
				key: 'mrr',
				isSortable: false
			},

			{
				label: __('Total', 'yith-woocommerce-subscription'),
				key: 'total',
				isSortable: false
			},

		];


		return headerContent;

	}

	getCustomersHeadersContent = () => {

		let headerContent = [
			{
				label: __('Customer', 'yith-woocommerce-subscription'),
				key: 'customer',
				isSortable: false,
			},
			{
				label: __('Total paid', 'yith-woocommerce-subscription'),
				key: 'total_paid',
				isSortable: false
			}
		];

		return headerContent;
	}

	getLostSubscribersHeadersContent = () => {

		let headerContent = [
			{
				label: __('Customer', 'yith-woocommerce-subscription'),
				key: 'customer',
				isSortable: false,
			},
			{
				label: __('Product', 'yith-woocommerce-subscription'),
				key: 'product_name',
				isSortable: false
			},
			{
				label: __('Reason', 'yith-woocommerce-subscription'),
				key: 'reason',
				isSortable: false
			}
		];

		return headerContent;
	}

	getProductsRows() {

		const {productReport} = this.state;
		return productReport.map(subscription => {

			return [
				{
					display: getProductReportLink(subscription.product_name, subscription.product_id),
					value: subscription.product_name
				},
				{display: subscription.subscribers, value: subscription.subscribers},
				{display: formatCurrency(subscription.mrr), value: subscription.mrr},
				{display: formatCurrency(subscription.total), value: subscription.total},
			]
		});

	}


	getCustomersRows() {

		const {customerReport} = this.state;
		return customerReport.map(customer => {
			const subscriber = { first_name: customer.first_name,last_name:customer.last_name };
			return [
				{display: getCustomerName(subscriber), value: getCustomerName(subscriber)},
				{display: formatCurrency(customer.total_paid), value: customer.customer},
			]
		});

	}

	getLostSubscribersRows() {
		const {lostSubscriberReport} = this.state;
		return lostSubscriberReport.map(subscriber => {
			return [
				{display: getCustomerName(subscriber.customer), value: getCustomerName(subscriber.customer)},
				{
					display: getProductReportLink(subscriber.product_name, subscriber.product_id),
					value: subscriber.product_name
				},
				{ display: getStatusBlock( subscriber.status), value: subscriber.status },
			]
		});

	}


	render() {
		const {query} = this.props;
		const productRows = this.getProductsRows();
		const customerRows = this.getCustomersRows();
		const lostSubscriberRows = this.getLostSubscribersRows();
		let {productTotalResults, productIsRequesting} = this.state;
		let {customerTotalResults, customerIsRequesting} = this.state;
		let {lostSubscriberTotalResults, lostSubscriberIsRequesting} = this.state;
		productTotalResults = productTotalResults > 5 ? 5 : productTotalResults;
		customerTotalResults = customerTotalResults > 5 ? 5 : customerTotalResults;
		lostSubscriberTotalResults = lostSubscriberTotalResults > 5 ? 5 : lostSubscriberTotalResults;
		return (
			<Fragment>
				<SectionHeader className="ywsbs-leaderboard-section-header"
					title={__('Products', 'yith-woocommerce-subscription')}
				>
					<Link className="button-primary" href="admin.php?page=yith_woocommerce_subscription&tab=dashboard&path=/products-report" type="wp-admin"
					>{__('View all', 'yith-woocommerce-subscription')}</Link>
				</SectionHeader>
				<div className="leaderboard-group">
					<ReportTable
						headers={this.getProductsHeadersContent()}
						rows={productRows}
						query={query}
						totalRows={productTotalResults}
						rowsPerPage={5}
						title={__('Top Subscription Products', 'yith-woocommerce-subscription')}
						isRequesting={productIsRequesting}
					/>

				</div>
				<SectionHeader
					className="ywsbs-leaderboard-section-header"
					title={__('Subscribers', 'yith-woocommerce-subscription')}>
					<Link className="button-primary" href="admin.php?page=yith_woocommerce_subscription&tab=dashboard&path=/subscribers-report" type="wp-admin"
					>{__('View all', 'yith-woocommerce-subscription')}</Link>
				</SectionHeader>
				<div className="leaderboard-group">
					<ReportTable
						headers={this.getCustomersHeadersContent()}
						rows={customerRows}
						query={query}
						totalRows={customerTotalResults}
						rowsPerPage={5}
						title={__('Top Subscribers', 'yith-woocommerce-subscription')}
						isRequesting={customerIsRequesting}
					/>
					<ReportTable
						headers={this.getLostSubscribersHeadersContent()}
						rows={lostSubscriberRows}
						query={query}
						totalRows={lostSubscriberTotalResults}
						rowsPerPage={5}
						title={__('Latest lost subscribers', 'yith-woocommerce-subscription')}
						isRequesting={lostSubscriberIsRequesting}
					/>
				</div>
			</Fragment>
		);
	}
}

export default DashboardLeaderboards;
