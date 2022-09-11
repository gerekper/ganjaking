import { Component, Fragment } from '@wordpress/element';
import {ReportTable} from "../report-table";
import {isEqual} from "lodash";
import {__} from '@wordpress/i18n';
import {getCustomersReportData} from "../../packages/data/customers";
import {formatCurrency} from "../../lib/numbers";
import {getCustomerName, getEmailLink} from "../../lib/general";

class SubscribersTable extends Component {

	constructor() {
		super( ...arguments );

		this.state = {
			isRequesting: false,
			subscribersReport: [],
			totalResults: 0,
			totalPages: 0
		};
	}

	componentDidMount() {
		this.update();
	}

	componentDidUpdate( prevProps, prevState ) {

		if ( !isEqual( prevProps.query, this.props.query ) ) {
			this.update();
		}
	}

	update = () => {
		const { query } = this.props;
		this.setState( { isRequesting: true } );

		getCustomersReportData( query ).then( report => {
			const { data, totalResults, totalPages  } = report;
			this.setState( { subscribersReport: data, isRequesting: false, totalResults, totalPages } );
		} );

	};

	getHeadersContent = () => {

		let headerContent = [
			{
				label: __('Name', 'yith-woocommerce-subscription'),
				key: 'customer',
				isSortable: true,
			},
			{
				label: __('Email', 'yith-woocommerce-subscription'),
				key: 'email',
				isSortable: true,
			},
			{
				label: __('Total paid', 'yith-woocommerce-subscription'),
				key: 'total_paid',
				isSortable: true
			},

			{
				label: __('Active subscriptions', 'yith-woocommerce-subscription'),
				key: 'total_paid',
				isSortable: false
			},
			{
				label: __('Cancelled subscriptions', 'yith-woocommerce-subscription'),
				key: 'total_paid',
				isSortable: false
			}
		];

		return headerContent;
	}


	getRowsContent() {

		const {subscribersReport} = this.state;

		return subscribersReport.map(customer => {
			const subscriber = { first_name: customer.first_name,last_name:customer.last_name };
			return [
				{display: getCustomerName(subscriber), value: getCustomerName(customer.customer)},
				{display: getEmailLink( customer.email), value: customer.email},
				{display: formatCurrency(customer.total_paid), value: customer.customer},
				{display: customer.subscription_active, value: customer.subscription_active},
				{display: customer.subscription_cancelled, value: customer.subscription_cancelled},
			]
		});

	}



	render() {
		const { query } = this.props;
		const per_page = query?.per_page ? query.per_page : 25;
		const rows = this.getRowsContent();
		const {totalResults, isRequesting} = this.state;
		return (
			<Fragment>
				<ReportTable
					headers={ this.getHeadersContent() }
					rows={ rows }
					query={ query }
					totalRows = {totalResults}
					rowsPerPage={parseInt(per_page)}
					title={ __( 'Subscribers', 'yith-woocommerce-subscription' ) }
					isRequesting={isRequesting}

				/>
			</Fragment>

		);
	}
}
export default SubscribersTable;