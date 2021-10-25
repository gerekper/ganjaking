const gulp = require( 'gulp' );
const clean = require('gulp-clean');
const config = require( '../../config' );

const getIconPaths = ( target = 'admin' ) => ([
	`${ config.paths.root }/dev/icons/${ target }`,
	`${ config.paths.fonts }gform-icons-${ target }.*`,
	`${ config.paths.css_src }${ target }/icons/_icons.pcss`,
	`${ config.paths.css_src }${ target }/variables/_icons.pcss`,
]);

module.exports = {
	adminIconsStart() {
		return gulp.src( getIconPaths() )
			.pipe( clean() );
	},
	adminIconsEnd() {
		return gulp.src( `${ config.paths.root }gform-icons-admin*.zip` )
			.pipe( clean() );
	},
	themeIconsStart() {
		return gulp.src( getIconPaths( 'theme' ) )
			.pipe( clean() );
	},
	themeIconsEnd() {
		return gulp.src( `${ config.paths.root }gform-icons-theme*.zip` )
			.pipe( clean() );
	},
	js() {
		return gulp.src( `${ config.paths.js_dist }**/*.js` )
			.pipe( clean( { force: true } ) );
	},
};
