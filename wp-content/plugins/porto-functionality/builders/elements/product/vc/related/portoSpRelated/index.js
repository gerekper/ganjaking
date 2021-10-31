/* eslint-disable import/no-webpack-loader-syntax */
import { getService } from 'vc-cake'
import PortoSpRelated from './component'

const vcvAddElement = getService( 'cook' ).add

vcvAddElement(
	require( './settings.json' ),
	// Component callback
	function ( component ) {
		component.add( PortoSpRelated )
	}
)