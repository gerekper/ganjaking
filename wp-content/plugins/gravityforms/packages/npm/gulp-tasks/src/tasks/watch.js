const gulp = require( 'gulp' );
const config = require( '../../config' );
const browserSync = require( 'browser-sync' );
const webpack = require( 'webpack' );
const webpackStream = require( 'webpack-stream' );
const merge = require( 'webpack-merge' );

const webpackAdminDevConfig = require( '../webpack/admindev' );
const webpackThemeDevConfig = require( '../webpack/themedev' );
const watchRules = require( '../webpack/rules/watch' );
const watchPlugins = require( '../webpack/plugins/watch' );

const watchConfig = {
	watch: true,
};

webpackAdminDevConfig.module.rules = watchRules;
webpackThemeDevConfig.module.rules = watchRules;
webpackAdminDevConfig.plugins = watchPlugins.admin;
webpackThemeDevConfig.plugins = watchPlugins.theme;

function maybeReloadBrowserSync() {
	const server = browserSync.get( config.browserSync.serverName );
	if ( server.active ) {
		server.reload();
	}
}

module.exports = {
	main() {
		// watch main plugin postcss

		gulp.watch( [
			`${ config.paths.css_src }/admin/admin.pcss`,
			`${ config.paths.css_src }/admin/admin-ie11.pcss`,
			`${ config.paths.css_src }/admin/base/**/*.pcss`,
			`${ config.paths.css_src }/admin/components/**/*.pcss`,
			`${ config.paths.css_src }/admin/deprecated/**/*.pcss`,
			`${ config.paths.css_src }/admin/global/**/*.pcss`,
			`${ config.paths.css_src }/admin/mixins/**/*.pcss`,
			`${ config.paths.css_src }/admin/pages/**/*.pcss`,
			`${ config.paths.css_src }/admin/variables/**/*.pcss`,
			`${ config.paths.css_src }/admin/vendor/**/*.pcss`,
		], gulp.parallel( 'postcss:adminCss', 'postcss:adminIE11Css' ) );

		gulp.watch( [
			`${ config.paths.css_src }/admin/admin-icons.pcss`,
			`${ config.paths.css_src }/admin/icons/**/*.pcss`,
		], gulp.parallel( 'postcss:adminIconCss' ) );

		gulp.watch( [
			`${ config.paths.css_src }/admin/base/**/*.pcss`,
			`${ config.paths.css_src }/admin/components/**/*.pcss`,
			`${ config.paths.css_src }/admin/editor.pcss`,
			`${ config.paths.css_src }/admin/editor/**/*.pcss`,
		], gulp.parallel( 'postcss:editorCss' ) );

		gulp.watch( [
			`${ config.paths.css_src }/admin/settings.pcss`,
			`${ config.paths.css_src }/admin/settings/**/*.pcss`,
		], gulp.parallel( 'postcss:settingsCss' ) );

		gulp.watch( [
			`${ config.paths.css_src }/admin/admin-theme.pcss`,
			`${ config.paths.css_src }/theme/**/*.pcss`,
		], gulp.parallel( ['postcss:baseCss', 'postcss:themeCss', 'postcss:adminThemeCss', 'postcss:themeIE11Css'] ) );

		gulp.watch( [
			`${ config.paths.dev }/components/index.html`,
			`${ config.paths.dev }/components/layout.pcss`,
		], gulp.parallel( 'postcss:devComponentCss' ) );

		gulp.watch( [
			`${ config.paths.dev }/components/index.html`,
		] ).on( 'change', function() {
			maybeReloadBrowserSync();
		} );
	},
	watchAdminJS() {
		gulp.src( `${ config.paths.js_src }/admin/**/*.js` )
			.pipe( webpackStream( merge( webpackAdminDevConfig, watchConfig ), webpack, function( err, stats ) {
				console.log( stats.toString( { colors: true } ) );
				maybeReloadBrowserSync();
			} ) )
			.pipe( gulp.dest( config.paths.js_dist ) );
	},
	watchThemeJS() {
		gulp.src( [
			`${ config.paths.js_src }/theme/**/*.js`,
		] )
			.pipe( webpackStream( merge( webpackThemeDevConfig, watchConfig ), webpack, function( err, stats ) {
				console.log( stats.toString( { colors: true } ) );
				maybeReloadBrowserSync();
			} ) )
			.pipe( gulp.dest( config.paths.js_dist ) );
	},
};
