const gulp = require( 'gulp' );
const decompress = require( 'gulp-decompress' );
const config = require( '../config' );

module.exports = {
	adminIcons() {
		return gulp.src( [
			`${ config.paths.root }/gform-icons-admin*.zip`,
		] )
			.pipe( decompress() )
			.pipe( gulp.dest( `${ config.paths.dev }/icons/admin` ) );
	},
	themeIcons() {
		return gulp.src( [
			`${ config.paths.root }/gform-icons-theme*.zip`,
		] )
			.pipe( decompress() )
			.pipe( gulp.dest( `${ config.paths.dev }/icons/theme` ) );
	},
};
