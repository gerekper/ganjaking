const gulp = require( 'gulp' );
const header = require( 'gulp-header' );
const config = require( '../config' );

module.exports = {
	adminIconsStyle() {
		return gulp.src( `${ config.paths.css_src }/admin/icons/_icons.pcss` )
			.pipe( header( `/* stylelint-disable */
/* -----------------------------------------------------------------------------
 *
 * Admin Font Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

` ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/admin/icons/` ) );
	},
	adminIconsVariables() {
		return gulp.src( `${ config.paths.css_src }/admin/variables/_icons.pcss` )
			.pipe( header( `/* stylelint-disable */
/* -----------------------------------------------------------------------------
 *
 * Variables: Admin Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

:root {` ) )
			.pipe( gulp.dest( `${ pkg.gravityforms.paths.css_src }/admin/variables/` ) );
	},
	themeIconsStyle() {
		return gulp.src( `${ pkg.gravityforms.paths.css_src }/theme/base/_icons.pcss` )
			.pipe( header( `/* -----------------------------------------------------------------------------
 *
 * Theme Font Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

` ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/theme/base/` ) );
	},
	themeIconsVariables() {
		return gulp.src( `${ config.paths.css_src }/theme/variables/_icons.pcss` )
			.pipe( header( `/* -----------------------------------------------------------------------------
 *
 * Variables: Theme Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

:root {` ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/theme/variables/` ) );
	},
};
