const path = require('path');

const nodeExternals = require('webpack-node-externals');

const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

module.exports = {

    plugins: [
        new BrowserSyncPlugin({
            host: 'localhost',
            port: 3000,
            open: true,
            browser: 'chrome',
            proxy: 'http://userpro.com/' //localhost project url
        })
    ],

    target: 'node',
    externals: [nodeExternals()],
    entry: ['./assets/index.js','./assets/sass/main.scss','./assets/admin/assets/sass/admin.scss', './profile-layouts/layout4/sass/layout4.scss'],
    mode: 'development', //production for minify css files.
    output: {
        path: path.resolve(__dirname, 'assets'),
        filename: 'bundle.js',
        publicPath: "/assets"
    },
    watch:true,
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader"
                }
            },
            {
                test: /\.scss$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].css',
                            context: './',
                            outputPath: '/css',
                            publicPath: '/assets'
                        }
                    },
                    {
                        loader: 'extract-loader'
                    },
                    {
                        loader: 'css-loader',
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true
                        }
                    }
                ]
            }
        ]
    }
}