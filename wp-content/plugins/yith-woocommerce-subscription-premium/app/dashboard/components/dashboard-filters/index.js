import React, {Component} from 'react';
import { ReportFilters }           from '@woocommerce/components';
class DashboardFilters extends Component {

	constructor() {
		super( ...arguments );
	}

	render() {
		const { query, path, onDataSelect, filters, onFilterSelect, report} = this.props;
		return <ReportFilters
			path={path}
			query={query}
			filters={filters}
			report={report}
			onDateSelect={onDataSelect}
			onFilterSelect = {onFilterSelect}
		/>
	}
}

export default DashboardFilters;