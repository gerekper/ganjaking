import React, { Component, Fragment } from 'react';
import DashboardSubscriptions       from '../dashboard-subscriptions';
import DashboardLeaderboards from '../dashboard-leaderboards';

class DashboardMain extends Component {
	render() {
		return <Fragment>
			<DashboardSubscriptions {...this.props} />
			<DashboardLeaderboards {...this.props} />
		</Fragment>
	}
}

export default DashboardMain;

