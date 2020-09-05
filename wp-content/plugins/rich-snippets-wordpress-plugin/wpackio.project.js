const pkg            = require( './package.json' );
const UglifyJsPlugin = require( 'uglifyjs-webpack-plugin' );

module.exports = {
	// Project Identity
	appName            : 'snip', // Unique name of your project
	type               : 'plugin', // Plugin or theme
	slug               : 'rich-snippets-wordpress-plugin', // Plugin or Theme slug, basically the directory name under `wp-content/<themes|plugins>`
	// Used to generate banners on top of compiled stuff
	bannerConfig       : {
		name         : 'snip',
		author       : 'floriansimeth',
		license      : 'GPLv3',
		link         : 'https://www.gnu.org/licenses/gpl-3.0.html',
		version      : pkg.version,
		copyrightText:
				'This software is released under the GPLv3 License\nhttps://www.gnu.org/licenses/gpl-3.0.html',
		credit       : true,
	},
	// Files we need to compile, and where to put
	files              : [
		// If this has length === 1, then single compiler
		{
			name         : 'snip-pro',
			entry        : {
				// mention each non-interdependent files as entry points
				// The keys of the object will be used to generate filenames
				// The values can be string or Array of strings (string|string[])
				// But unlike webpack itself, it can not be anything else
				// <https://webpack.js.org/concepts/#entry>
				// You do not need to worry about file-size, because we would do
				// code splitting automatically. When using ES6 modules, forget
				// global namespace pollutions 😉
				setupwizard: './pro/js/admin-setupwizard.js', // Could be a string
				//main  : [ './pro/js/setupwizard.js' ], // Or an array of string (string[])
			},
			// Extra webpack config to be passed directly
			webpackConfig: ( config, merge, appDir, isDev ) => {

				if ( isDev ) return config;

				// Create a new config
				const newConfig = { ...config };

				newConfig.optimization = {
					minimize : true,
					minimizer: [
						new UglifyJsPlugin( {
							uglifyOptions: {
								warnings   : false,
								parse      : {},
								compress   : {},
								mangle     : {
									reserved: [ '__', '_x', '_n', '_nx' ]
								},
								output     : null,
								toplevel   : false,
								nameCache  : null,
								ie8        : false,
								keep_fnames: false,
							},
						} ),
					],
				};

				// newConfig.module.rules = [
				// 	...newConfig.module.rules,
				// 	{
				// 		test   : /SetupWizard\.js$/,
				// 		enforce: 'post',
				// 		use    : {
				// 			loader : 'babel-loader',
				// 			options: {
				// 				plugins: [
				// 					[ '@wordpress/babel-plugin-makepot', {
				// 						output: 'languages/setupwizard.pot'
				// 					} ]
				// 				],
				// 				presets: [ '@babel/preset-env', '@babel/preset-react' ]
				// 			}
				// 		},
				// 	},
				// ];

				// Return it
				return newConfig;
			},
		},
		// If has more length, then multi-compiler
	],
	// Output path relative to the context directory
	// We need relative path here, else, we can not map to publicPath
	outputPath         : 'pro/js-dist',
	// Project specific config
	// Needs react(jsx)?
	hasReact           : true,
	// Needs sass?
	hasSass            : true,
	// Needs less?
	hasLess            : false,
	// Needs flowtype?
	hasFlow            : false,
	// Externals
	// <https://webpack.js.org/configuration/externals/>
	externals          : {
		'react'    : 'React',
		'react-dom': 'ReactDOM',
	},
	// Webpack Aliases
	// <https://webpack.js.org/configuration/resolve/#resolve-alias>
	alias              : undefined,
	// Show overlay on development
	errorOverlay       : true,
	// Auto optimization by webpack
	// Split all common chunks with default config
	// <https://webpack.js.org/plugins/split-chunks-plugin/#optimization-splitchunks>
	// Won't hurt because we use PHP to automate loading
	optimizeSplitChunks: true,
	// Usually PHP and other files to watch and reload when changed
	watch              : '*.php',
	// Files that you want to copy to your ultimate theme/plugin package
	// Supports glob matching from minimatch
	// @link <https://github.com/isaacs/minimatch#usage>
	packageFiles       : [
		'inc/**',
		'vendor/**',
		'dist/**',
		'*.php',
		'*.md',
		'readme.txt',
		'languages/**',
		'layouts/**',
		'LICENSE',
		'*.css',
	],
	// Path to package directory, relative to the root
	packageDirPath     : 'package',
	useBabelConfig     : false,

	// Hook into babeloverride so that we can add react-hot-loader plugin
	// @floriansimeth: This is not working!!
	// jsBabelOverride: defaults => {
	// 	return {
	// 		...defaults,
	// 		plugins: [
	// 			[ '@wordpress/babel-plugin-makepot', {
	// 				output: 'myplugin.pot'
	// 			} ],
	// 			'react-hot-loader/babel',
	// 		],
	// 	};
	// },
};
