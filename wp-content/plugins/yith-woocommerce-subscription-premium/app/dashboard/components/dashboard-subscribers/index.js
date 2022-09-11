import React, { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
import SubscribersTable       from '../subscriber-table';
import {Link} from "@woocommerce/components";

class DashboardSubscribers extends Component {


	render() {

		const { query} = this.props;

		return <Fragment>
			<Link href="admin.php?page=yith_woocommerce_subscription&tab=dashboard&path=/" type="wp-admin"
			>{__('< back to main report', 'yith-woocommerce-subscription')}</Link>
			<h2>{__('Subscribers Dashboard','yith-woocommerce-subscription')}</h2>

			<SubscribersTable
				query={ query }
			/>
		</Fragment>
	}
}

export default DashboardSubscribers;

