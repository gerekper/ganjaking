import { Component, Fragment } from '@wordpress/element';
import {__} from '@wordpress/i18n';
import {ReportTable} from '../report-table';
import {getSubscriptionsReportData} from '../../packages/data/subscriptions';
import {formatCurrency} from "../../lib/numbers";
import {isEqual} from "lodash";
import { format as formatDate } from '@wordpress/date';
import {getStatusBlock, getEditorlLink, getProductReportLink} from "../../lib/general";
class SubscriptiptionsTable extends Component {

	constructor() {
		super( ...arguments );

		this.state = {
			subscriptionIsRequesting: false,
			subscriptionReport: [],
			totalResults: 0,
			totalPages: 0
		};


	}

	componentDidMount() {
		this.update();
	}

	componentDidUpdate( prevProps, prevState ) {

		if ( !isEqual( prevProps.query, this.props.query ) ) {
			const prevQuery    = this.getQueryWithoutChart( prevProps.query );
			const currentQuery = this.getQueryWithoutChart( this.props.query );

			if ( !isEqual( prevQuery, currentQuery ) ) {
				this.update();
			}
		}
	}

	getQueryWithoutChart = ( query ) => {
		const { chart, chartType, ...newQuery } = query;
		return { ...newQuery };
	};

	update = () => {
		const { query } = this.props;
		this.setState( { subscriptionIsRequesting: true } );

		getSubscriptionsReportData( query ).then( report => {
			const { data, totalResults, totalPages  } = report;
			this.setState( { subscriptionReport: data, subscriptionIsRequesting: false, totalResults, totalPages } );
		} );

	};

	getHeadersContent() {
		const {query} = this.props;
		let headerContent = [
			{
				label: __('Date', 'yith-woocommerce-subscription'),
				key: 'date_created',
				isSortable: true,
			},
			{label: __('Subscription #', 'yith-woocommerce-subscription'),
				key: 'subscription_id',
				isSortable: true
			},
			{label: __('Status', 'yith-woocommerce-subscription'),
				key: 'status',
				isSortable: true
			},
			{
				label: __('End Date', 'yith-woocommerce-subscription'),
				key: 'cancelled_date',
				isSortable: true,
			},
			{label: __('Customer', 'yith-woocommerce-subscription')},
			{label: __('Product', 'yith-woocommerce-subscription'),
				key: 'product_name',
				isSortable: true},
			{
				label: __('Net', 'yith-woocommerce-subscription'),
				key: 'net_total',
				isSortable: true,
				isNumeric: true,
			},

		];

		if( 'conversions' == query.chart ){

			headerContent =[...headerContent, {
				label: __('Conversion Date', 'yith-woocommerce-subscription'),
					key: 'conversion_date',
					isSortable: false,
			}];
		}


		return headerContent;
	}

	getRowsContent() {

		const { subscriptionReport } = this.state;

		return subscriptionReport.map( subscription => {

			const extendedInfo = subscription.extended_info || {};
			const { customer } = extendedInfo;
			const cancelled_date = ('0000-00-00 00:00:00' !== subscription.cancelled_date) ? formatDate( ywsbsSettings.wc.date_format, subscription.cancelled_date) : '-';

			return [
				{ display: formatDate( ywsbsSettings.wc.date_format, subscription.date_created), value: subscription.date_created },
				{ display: getEditorlLink( '#'+subscription.subscription_id, subscription.subscription_id ) , value: subscription.subscription_id },
				{ display: getStatusBlock( subscription.status), value: subscription.status },
				{ display: cancelled_date, value: subscription.cancelled_date },
				{ display: this.getCustomerName( customer ), value: this.getCustomerName( customer ) },
				{ display: getProductReportLink( subscription.product_name, subscription.product_id ), value: subscription.product_name },
				{ display: formatCurrency( subscription.net_total ), value: subscription.net_total },
				{ display: formatDate( ywsbsSettings.wc.date_format, subscription.conversion_date), value: subscription.conversion_date },
			]
		} );

	}
	getSummary(){

	}

	getCustomerName( customer ) {
		const { first_name: firstName, last_name: lastName } = customer || {};

		if ( ! firstName && ! lastName ) {
			return '';
		}

		return [ firstName, lastName ].join( ' ' );
	}
	
	render() {
		const { query, isRequesting } = this.props;
		const per_page = query?.per_page ? query.per_page : 25;
		const rows = this.getRowsContent();
		const {totalResults} = this.state;
		return (
			<Fragment>
				<ReportTable
					headers={ this.getHeadersContent() }
					rows={ rows }
					query={ query }
					totalRows = {totalResults}
					rowsPerPage={parseInt(per_page)}
					title={__( 'Subscriptions list', 'yith-woocommerce-subscription' )}
					isRequesting={isRequesting}

				/>
			</Fragment>

		);
	}
}

export default SubscriptiptionsTable;