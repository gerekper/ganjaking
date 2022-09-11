import React, {Component, Fragment} from 'react';
import DashboardFilters from '../dashboard-filters';
import SubscriptionSummary from '../subscription-summary';
import SubscriptionChart from '../subscription-chart';
import SubscriptiptionsTable from '../subscription-table';
import {charts} from './config';
import {getSelectedChart} from '../../lib/general';
import {getSubscriptionChartData} from '../../packages/data/subscriptions-stats';
import {isEqual} from "lodash";
class DashboardSubscriptions extends Component {

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
			primary  : await getSubscriptionChartData( { query, dataType: 'primary' } ),
			secondary: await getSubscriptionChartData( { query, dataType: 'secondary' } )
		};

		this.setState( { stats: stats, isRequesting: false } );
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
		if( typeof path.chartType !== 'undefined' ) {
			newQuery['chartType'] = path.chartType;
		}
		this.update(newQuery);
	}

	render() {
		const {path, query} = this.props;
		const { stats, isRequesting } = this.state;
		const selectedChart = getSelectedChart(query.chart, charts);
		return <Fragment>
			<DashboardFilters path={path} query={query} onDataSelect={(date)=>this.update( date )} report="orders"/>
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
				isRequesting={isRequesting}
			/>
			<SubscriptiptionsTable
				query={ query }
				isRequesting={isRequesting}
			/>
		</Fragment>
	}
}


export default DashboardSubscriptions;