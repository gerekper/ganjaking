const path = require('path');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')
const plugins = [];
const isProduction = (process.env.NODE_ENV == 'production');
function resolve (dir) {
  return path.join(__dirname, './views/assets/src', dir)
}
if (isProduction) {
    plugins.push(
        new UglifyJsPlugin()
    )   
}
module.exports = {
	entry: path.resolve( __dirname, 'views/assets/src/start-frontend.js' ),

	output: {
		path: path.resolve( __dirname, 'views/assets/js'),
		filename: 'stripe-frontend.js',
		publicPath: '',
		chunkFilename: 'chunk/[chunkhash].chunk-bundle.js',
		jsonpFunction: 'wedevsPmProInvoiceFrontendWebpack',
	},

	resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
          '@components': resolve('components'),
          '@directives': resolve('directives'),
          '@helpers': resolve('helpers'),
          '@router': resolve('router'),
          '@store': resolve('store'),
          '@src': resolve('')
        }
    },

	module: {
		rules: [
			// doc url https://vue-loader.vuejs.org/en/options.html#loaders
			{
				test: /\.vue$/,
				loader: 'vue-loader',
				options: {
					loaders: {
			        	 js: 'babel-loader'
			        }
				}
			},
			{
				test: /\.js$/,
				loader: 'babel-loader',
				include: [
                	resolve('')
                ],
				exclude: /node_modules/,
				query: {
                    presets:[ "env", "stage-3" , "es2015" ]
                }
			},
			{
				test: /\.(png|jpg|gif|svg)$/,
				loader: 'file-loader',
				exclude: /node_modules/,
				options: {
					name: '[name].[ext]?[hash]'
				}
			}
		]
	},

	plugins: plugins

}



