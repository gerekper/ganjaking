const defaultConfig                                = require( '@wordpress/scripts/config/webpack.config' );
const WooCommerceDependencyExtractionWebpackPlugin = require( '@woocommerce/dependency-extraction-webpack-plugin' );
const path                                         = require( 'path' );

module.exports = {
	...defaultConfig,
    entry: {
		'frontend/checkout-blocks': '/assets/js/frontend/blocks/checkout/index.js',
	},
	output: {
		path: path.resolve( __dirname, 'assets/dist' ),
		filename: '[name].js',
	},
	plugins: [
		...defaultConfig.plugins.filter(
			( plugin ) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WooCommerceDependencyExtractionWebpackPlugin(),
	],

};
