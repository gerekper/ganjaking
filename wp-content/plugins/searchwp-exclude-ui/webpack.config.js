const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const externals = {
	jquery: 'jQuery',
	lodash: 'lodash',
	react: 'React',
	'react-dom': 'ReactDOM',
};

// Define WordPress dependencies
const wpDependencies = [
	'components',
	'compose',
	'data',
	'edit-post',
	'editor',
	'element',
	'i18n',
	'plugins',
];

/**
 * Given a string, returns a new string with dash separators converted to
 * camel-case equivalent. This is not as aggressive as `_.camelCase` in
 * converting to uppercase, where Lodash will convert letters following
 * numbers.
 *
 * @param {string} string Input dash-delimited string.
 *
 * @return {string} Camel-cased string.
 */
function camelCaseDash( string ) {
	return string.replace(
		/-([a-z])/,
		( match, letter ) => letter.toUpperCase()
	);
}

wpDependencies.forEach( ( name ) => {
	externals[ `@wordpress/${ name }` ] = {
		this: [ 'wp', camelCaseDash( name ) ],
	};
} );
const postCssPlugins = process.env.NODE_ENV === 'production' ?
    [
        require( 'postcss-nested' ),
        require( 'autoprefixer' ),
        require( 'cssnano' )( {
            safe: true,
        } )
    ] :
    [
        require( 'postcss-nested' ),
        require( 'autoprefixer' ),
    ];

module.exports = {
	mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',

	// https://webpack.js.org/configuration/entry-context/
	entry: {
		'editor': './assets/js/src/editor.js',
	},

	// https://webpack.js.org/configuration/output/
	output: {
		path: __dirname + '/assets/js/',
		filename: '[name].js',
		library: 'SearchwpExclude',
		libraryTarget: 'this',
	},

	// https://webpack.js.org/configuration/externals/
	externals,

	// https://github.com/babel/babel-loader#usage
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: 'babel-loader',
			},
			{
				test: /\.css$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					'css-loader',
					{
						loader: 'postcss-loader',
						options: {
							plugins: postCssPlugins,
						}
					},
				]
			},
		],
	},

	// https://webpack.js.org/configuration/plugins/
	plugins: [
		new MiniCssExtractPlugin({
			// Options similar to the same options in webpackOptions.output
			// both options are optional
			filename: '../css/editor.css',
			chunkFilename: '[id].css'
		}),
	],
};
