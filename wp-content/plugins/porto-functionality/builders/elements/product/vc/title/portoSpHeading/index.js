/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoSpHeading from './component'

const vcvAddElement = vcCake.getService( 'cook' ).add

vcvAddElement(
	require( './settings.json' ),
	// Component callback
	function ( component ) {
		component.add( PortoSpHeading )
	},
	{
		'css': require( 'raw-loader!./styles.css' ),
	}
)
