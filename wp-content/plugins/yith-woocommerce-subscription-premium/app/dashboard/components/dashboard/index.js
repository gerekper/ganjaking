import React, { Component, Fragment } from 'react';
import { Route, Routes, unstable_HistoryRouter as HistoryRouter  } from 'react-router-dom';
import { getHistory }            from '@woocommerce/navigation';
import Controller                from '../controller';
import DashboardMain             from '../dashboard-main';
import DashboardProducts from "../dashboard-products";
import DashboardSubscribers from "../dashboard-subscribers";

class Dashboard extends Component {

	constructor() {
		super( ...arguments );

		this.state = {
			currentLocation: '',
		};
	}



	componentDidMount() {
		const history = getHistory();
		this.setState({currentLocation:history.location});
	}

	componentDidUpdate( prevProps ) {
		/**
		 const prevQuery     = this.getQuery( prevProps.location.search );
		 const prevBaseQuery = omit( this.getQuery( prevProps.location.search ), 'paged' );
		 const baseQuery     = omit( this.getQuery( this.props.location.search ), 'paged' );

		 if ( prevQuery.paged > 1 && !isEqual( prevBaseQuery, baseQuery ) ) {
			getHistory().replace( getNewPath( { paged: 1 } ) );
		}

		 if ( prevProps.match.url !== this.props.match.url ) {
			window.document.documentElement.scrollTop = 0;
		}
		 */
	}

	render() {
		const path = document.location.pathname;
		const basename = path.substring( 0, path.lastIndexOf( '/' ) );

		return <div className="yith-ywsbs-dashboard">
			<HistoryRouter history={getHistory()}>
				<Routes basename={ basename }>
					<Route
						key='/'
						path='/*'
						exact
						element={<Controller container={DashboardMain} />}
					/>
					<Route
						key='/products-report'
						path='/products-report'
						exact
						element={<Controller container={DashboardProducts}/>}
					/>
					<Route
						key='/subscribers-report'
						path='/subscribers-report'
						exact
						element={<Controller container={DashboardSubscribers} />}
					/>

				</Routes>

			</HistoryRouter>
		</div>
	}
}

export default Dashboard;