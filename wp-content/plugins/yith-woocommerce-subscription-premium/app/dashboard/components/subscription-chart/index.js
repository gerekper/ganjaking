import React, {Component, Fragment} from 'react';
import {Chart} from '@woocommerce/components';
import {format as formatDate} from '@wordpress/date';
import {__} from '@wordpress/i18n';
import {getTooltipValueFormat} from '../../lib/general';
import {
	getAllowedIntervalsForQuery,
	getIntervalForQuery,
	getCurrentDates,
	getPreviousDate,
	getChartTypeForQuery,
	getDateFormatsForInterval
} from '@woocommerce/date';
import {get} from "lodash";

class SubscriptionChart extends Component {


	getTimeChartData() {
		const { query, stats, selectedChart, isRequesting } = this.props;
		const currentInterval                               = getIntervalForQuery( query );
		const { primary, secondary }                        = getCurrentDates( query );

		if ( isRequesting ) {
			return [];
		}

		const formats = getDateFormatsForInterval(
			currentInterval,
			stats.primary.data.intervals.length
		);

		const chartData = stats.primary.data.intervals.map( function ( interval, index ) {
			const secondaryDate = getPreviousDate(
				interval.date_start,
				primary.after,
				secondary.after,
				query.compare,
				currentInterval
			);

			const secondaryInterval = stats.secondary.data.intervals[ index ];

			return {
				date     : formatDate( 'Y-m-d\\TH:i:s', interval.date_start ),
				primary  : {
					label    : `${primary.label} (${primary.range})`,
					labelDate: interval.date_start,
					value    : interval.subtotals[ selectedChart.key ] || 0
				},
				secondary: {
					label    : `${secondary.label} (${secondary.range})`,
					labelDate: secondaryDate.format( 'YYYY-MM-DD HH:mm:ss' ),
					value    : ( secondaryInterval && secondaryInterval.subtotals[ selectedChart.key ] ) || 0
				},

			};
		} );


		return chartData;
	}

	getTimeChartTotals() {
		const { stats, selectedChart, isRequesting } = this.props;

		if ( isRequesting ) {
			return [];
		}

		return {
			primary: get(
				stats.primary,
				[ 'data', 'totals', selectedChart.key ],
				null
			),
			secondary: get(
				stats.secondary,
				[ 'data', 'totals', selectedChart.key ],
				null
			),
		};
	}

	getFormatChart (){
		const { query, stats, selectedChart, isRequesting } = this.props;
		const currentInterval                               = getIntervalForQuery( query );
		const { primary, secondary }                        = getCurrentDates( query );

		if ( isRequesting ) {
			return [];
		}

		const formats = getDateFormatsForInterval(
			currentInterval,
			stats.primary.data.intervals.length
		);

		return formats;
	}

	render() {
		const { path, query, charts, selectedChart, isRequesting, stats } = this.props;
		const allowedIntervals                               = getAllowedIntervalsForQuery( query );

		const formats = this.getFormatChart();

		return <Fragment>
			<Chart
				allowedIntervals={allowedIntervals}
				data={this.getTimeChartData()}
				dateParser={ '%Y-%m-%dT%H:%M:%S' }
				emptyMessage={__( 'No data for the selected date range', 'yith-woocommerce-subscription' )}
				isRequesting={isRequesting}
				title={selectedChart.label}
				valueType={ selectedChart.type }
				interval={query.interval}
				legendTotals={ this.getTimeChartTotals() }
				mode="time-comparison"
				query={ query }
				path={ path }
				tooltipLabelFormat={ formats.tooltipLabelFormat }
				tooltipValueFormat={getTooltipValueFormat( selectedChart.type )}
				chartType={getChartTypeForQuery( query )}
				xFormat={ formats.xFormat }
				x2Format={ formats.x2Format }
				screenReaderFormat={ formats.screenReaderFormat }
			/>
		</Fragment>
	}
}


export default SubscriptionChart;