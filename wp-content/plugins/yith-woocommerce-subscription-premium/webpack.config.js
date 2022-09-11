// var webpack = require("webpack");

const path = require( 'path' );

const CustomTemplatedPathPlugin = require( '@wordpress/custom-templated-path-webpack-plugin' );

const externals = {
	'@wordpress/api-fetch'    	    : { this: ['wp', 'apiFetch'] },
	'@wordpress/element'      	    : { this: ['wp', 'element'] },
	'@wordpress/data'         	    : { this: ['wp', 'data'] },
	'@wordpress/hooks'        	    : { this: ['wp', 'hooks'] },
	'@wordpress/url'          	    : { this: ['wp', 'url'] },
	'@wordpress/html-entities'	    : { this: ['wp', 'htmlEntities'] },
	'@wordpress/i18n'         	    : { this: ['wp', 'i18n'] },
	'@wordpress/date'         	    : { this: ['wp', 'date'] },
	'@woocommerce/settings'   	    : { this: ['wc', 'wcSettings'] },
	'@woocommerce/components' 	    : { this: ['wc', 'components'] },
	'@woocommerce/navigation' 	    : { this: ['wc', 'navigation'] },
	'@woocommerce/date'       	    : { this: ['wc', 'date'] },
	'@woocommerce/number'     	    : { this: ['wc', 'number'] },
	'@wordpress/block-editor'       : { this: ['wp', 'blockEditor'] },
	'@wordpress/blocks'             : { this: ['wp', 'blocks'] },
	'@wordpress/components'         : { this: ['wp', 'components'] },
	'@wordpress/compose'            : { this: ['wp', 'compose'] },
	'@wordpress/editor'             : { this: ['wp', 'editor'] },
	'@wordpress/jest-preset-default': { this: ['wp', 'default'] },
	'@wordpress/scripts'            : { this: ['wp', 'scripts'] },
	react                           : 'React',
	lodash                          : 'lodash',
	'react-dom'                     : 'ReactDOM'
};

const webpackConfig = {
	entry      : {
		dashboard: "./app/dashboard/index.js",
		blocks: "./includes/builders/gutenberg/src/index.js",
	},
	output     : {
		filename     : "./[name]/index.js",
		libraryTarget: 'this'
	},
	externals,
	module     : {
		rules: [
			{
				parser: {
					amd: false
				}
			},
			{
				exclude: /node_modules/,
				loader : 'babel-loader',
				options: {
					presets: [
						'@babel/preset-env',
						'@babel/react', { 'plugins': ['@babel/plugin-proposal-class-properties'] }
					]
				}
			}
		]
	},
	resolve    : {
		extensions: ['.json', '.js', '.jsx'],
		modules   : [
			path.join( __dirname, 'src' ),
			'node_modules'
		],
		alias     : {
			'gutenberg-components': path.resolve( __dirname, 'node_modules/@wordpress/components/src' ),
			'react-spring'        : 'react-spring/web.cjs'
		}
	},
	plugins    : [
		new CustomTemplatedPathPlugin( {
			modulename( outputPath, data ) {
				const entryName = get( data, ['chunk', 'name'] );
				if ( entryName ) {
					return entryName.replace( /-([a-z])/g, ( match, letter ) => letter.toUpperCase() );
				}
				return outputPath;
			}
		} )
	],
	performance: {
		maxEntrypointSize: 2000000,
		maxAssetSize     : 2000000
	}
};

module.exports = webpackConfig;
