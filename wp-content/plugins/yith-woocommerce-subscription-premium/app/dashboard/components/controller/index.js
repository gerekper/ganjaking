import React             from 'react';
import { isEqual, omit } from 'lodash';
import { useLocation, useMatch, useParams } from 'react-router-dom';
import { parse }         from 'qs';
import { getNewPath, getHistory }   from '@woocommerce/navigation';
import { Component, createElement } from '@wordpress/element';

const RoutedController = ( props ) => {
	const location = useLocation();
	const match = useMatch( location.pathname );
	const params = useParams();
	const matchProp = { params, url: match.pathname };

	props = {
		...props,
		location,
		match: matchProp,
	};

	return <Controller { ...props } />;
};

class Controller extends Component {
	componentDidMount() {
		window.document.documentElement.scrollTop = 0;
	}

	componentDidUpdate( prevProps ) {

		const prevQuery     = this.getQuery( prevProps.location.search );
		const prevBaseQuery = omit( this.getQuery( prevProps.location.search ), 'paged' );
		const baseQuery     = omit( this.getQuery( this.props.location.search ), 'paged' );

		if ( prevQuery.paged > 1 && !isEqual( prevBaseQuery, baseQuery ) ) {
			getHistory().replace( getNewPath( { paged: 1 } ) );
		}

		if ( prevProps.match.url !== this.props.match.url ) {
			window.document.documentElement.scrollTop = 0;
		}

	}

	getQuery = ( searchString ) => {
		if ( !searchString ) {
			return {};
		}

		const search = searchString.substring( 1 );
		return parse( search );
	};

	getPath = ( pathname ) => {
		return pathname;
	};

	render() {
		const { container, location } = this.props;
		const query                   = this.getQuery( location.search );
		const path                    = this.getPath( location.pathname );
		return createElement( container, { location, path, query } );
	}
}

export default RoutedController;

