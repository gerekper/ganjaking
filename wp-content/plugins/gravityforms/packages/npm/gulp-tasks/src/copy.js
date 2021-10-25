const gulp = require( 'gulp' );
const rename = require( 'gulp-rename' );
const config = require( '../config' );

module.exports = {
	adminIconsFonts() {
		return gulp
			.src( [
				`${ config.paths.dev }/icons/admin/fonts/*`,
			] )
			.pipe( gulp.dest( config.paths.fonts ) );
	},
	adminIconsStyles() {
		return gulp
			.src( [
				`${ config.paths.dev }/icons/admin/style.css`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/admin/icons/` ) );
	},
	adminIconsVariables() {
		return gulp
			.src( [
				`${ config.paths.dev }/icons/admin/variables.scss`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/admin/variables/` ) );
	},
	themeIconsFonts() {
		return gulp
			.src( [
				`${ config.paths.dev }/icons/theme/fonts/*`,
			] )
			.pipe( gulp.dest( config.paths.fonts ) );
	},
	themeIconsStyles() {
		return gulp
			.src( [
				`${ config.paths.dev }/icons/theme/style.css`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/theme/base/` ) );
	},
	themeIconsVariables() {
		return gulp
			.src( [
				`${ config.paths.dev }icons/theme/variables.scss`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ config.paths.css_src }theme/variables/` ) );
	},
};
