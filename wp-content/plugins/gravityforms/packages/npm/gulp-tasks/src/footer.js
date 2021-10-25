const gulp = require( 'gulp' );
const footer = require( 'gulp-footer' );
const config = require( '../config' );

module.exports = {
	adminIconsVariables() {
		return gulp.src( `${ config.paths.css_src }/admin/variables/_icons.pcss` )
			.pipe( footer( '}\n' ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/admin/variables/` ) );
	},
	themeIconsVariables() {
		return gulp.src( `${ config.paths.css_src }/theme/variables/_icons.pcss` )
			.pipe( footer( '}\n' ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/theme/variables/` ) );
	},
};
