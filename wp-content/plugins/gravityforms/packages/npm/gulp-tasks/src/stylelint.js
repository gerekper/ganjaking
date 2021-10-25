const gulp = require( 'gulp' );
const stylelint = require( 'gulp-stylelint' );
const config = require( '../config' );

module.exports = {
	admin() {
		return gulp.src( [
			`${ config.paths.css_src }/admin/**/*.pcss`,
		] )
			.pipe( stylelint( {
				fix: true,
				reporters: [
					{ formatter: 'string', console: true },
				],
			} ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/admin/` ) );
	},
	theme() {
		return gulp.src( [
			`${ config.paths.css_src }/theme/**/*.pcss`,
		] )
			.pipe( stylelint( {
				fix: true,
				reporters: [
					{ formatter: 'string', console: true },
				],
			} ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/theme/` ) );
	},
};
