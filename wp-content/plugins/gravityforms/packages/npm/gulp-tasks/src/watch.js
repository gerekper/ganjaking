const gulp = require( 'gulp' );
const config = require( '../config' );
const browserSync = require( 'browser-sync' );

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
};
