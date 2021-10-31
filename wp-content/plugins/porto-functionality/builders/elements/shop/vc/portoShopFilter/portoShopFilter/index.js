/* eslint-disable import/no-webpack-loader-syntax */
import { getService } from 'vc-cake'
import PortoShopFilter from './component'

const vcvAddElement = getService( 'cook' ).add

vcvAddElement(
	require( './settings.json' ),
	// Component callback
	function ( component ) {
		component.add( PortoShopFilter )
	},
	{
		css: false,
		editorCss: false,
		mixins: {
			vcStyle: {
				mixin: require( 'raw-loader!./cssMixins/vcStyle.pcss' )
			}
		}
	}
)