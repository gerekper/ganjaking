const WatchExternalFilesPlugin = require( 'webpack-watch-files-plugin' ).default;
const config = require( '../../../config' );

module.exports = {
	theme: [
		new WatchExternalFilesPlugin( {
			files: [
				`${ config.paths.js_src }common/**/*.js`,
				`${ config.paths.js_src }utils/**/*.js`,
			],
		} ),

	],
	admin: [
		new WatchExternalFilesPlugin( {
			files: [
				`${ config.paths.js_src }common/**/*.js`,
				`${ config.paths.js_src }utils/**/*.js`,
			],
		} ),
	],
};
