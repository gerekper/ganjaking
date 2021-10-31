/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderCompare from './component'

const vcvAddElement = vcCake.getService( 'cook' ).add

vcvAddElement(
	require( './settings.json' ),
	// Component callback
	function ( component ) {
		component.add( PortoHeaderCompare )
	},
	// css settings // css for element
	{
		css: false,
		editorCss: false,
		mixins: {
			compare: {
				mixin: require( 'raw-loader!./cssMixins/compare.pcss' )
			}
		}
	}
)
