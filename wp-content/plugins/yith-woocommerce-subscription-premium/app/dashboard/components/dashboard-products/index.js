import React, { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
import {Link} from "@woocommerce/components";
import ProductsTable from "../product-table";
import DashboardFilters from "../dashboard-filters";
import {filters, charts} from "./config";
import SubscriptionChart from "../subscription-chart";
import SubscriptionSummary from "../subscription-summary";
import {getSelectedChart} from "../../lib/general";
import{getProductsChartData} from "../../packages/data/products-stats";
import {isEqual} from "lodash";


class DashboardProducts extends Component {

	constructor() {
		super( ...arguments );

		this.state = {
			stats       : {},
			isRequesting: true,
		};
	}

	componentDidMount() {
		const { query } = this.props;
		this.update( query );
	}

	getQueryWithoutChart = ( query ) => {
		const { chart, chartType, ...newQuery } = query;
		return { ...newQuery };
	};

	componentDidUpdate( prevProps, prevState ) {

		if ( !isEqual( prevProps.query, this.props.query ) ) {
			const prevQuery    = this.getQueryWithoutChart( prevProps.query );
			const currentQuery = this.getQueryWithoutChart( this.props.query );

			if ( !isEqual( prevQuery, currentQuery ) ) {
				this.update(this.props.query);
			} else {
				this.setState( {} );
			}
		}
	}

	update = async ( query ) => {

		this.setState( { isRequesting: true } );

		const stats = {
			primary  : await getProductsChartData( { query, dataType: 'primary' } ),
			secondary: await getProductsChartData( { query, dataType: 'secondary' } )
		};

		this.setState( { stats: stats, isRequesting: false, currentQuery: query } );
	};

	updatePath = ( path ) =>{
		let newQuery = this.props.query;
		newQuery['chart'] = path.chart;
		if( typeof path.orderby !== 'undefined' ){
			newQuery['orderby'] = path.orderby;
		}
		if( typeof path.order !== 'undefined' ) {
			newQuery['order'] = path.order;
		}
		this.update(newQuery);
	}

	render() {
		const { path, query} = this.props;
		const { isRequesting, stats } = this.state;
		const selectedChart = getSelectedChart(query.chart, charts);

		return <Fragment>

			<Link href="admin.php?page=yith_woocommerce_subscription&tab=dashboard&path=/" type="wp-admin"
			>{__('< back to main report', 'yith-woocommerce-subscription')}</Link>
			<h2>{__('Products Dashboard','yith-woocommerce-subscription')}</h2>

			<DashboardFilters path={path} query={query} report="products" filters={filters} onDataSelect={(date)=>this.update( date )}/>

			<SubscriptionSummary
				path={path}
				query={query}
				charts={charts}
				selectedChart={selectedChart}
				stats={stats}
				isRequesting={isRequesting}
				onChartSelect={(path)=>this.updatePath( path )}
			/>

			<SubscriptionChart path={path}
				query={query}
				selectedChart={selectedChart}
				stats={stats}
				filters={filters}
				isRequesting={isRequesting}/>

			<ProductsTable
				query={ query }
				isRequesting={isRequesting}
			/>

		</Fragment>
	}
}

export default DashboardProducts;

