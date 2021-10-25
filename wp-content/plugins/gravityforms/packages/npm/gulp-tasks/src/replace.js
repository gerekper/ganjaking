const gulp = require( 'gulp' );
const replace = require( 'gulp-replace' );
const config = require( '../config' );

module.exports = {
	adminIconsStyle() {
		return gulp.src( [
			`${ config.paths.css_src }/admin/icons/_icons.pcss`,
		] )
			.pipe( replace( /url\('fonts\/(.+)'\) /g, 'url(\'../fonts/$1\') ' ) )
			.pipe( replace( / {2}/g, '\t' ) )
			.pipe( replace( /}$\n^\./gm, '}\n\n\.' ) )
			.pipe( replace( config.icons.admin.replaceName, config.icons.admin.varName ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/admin/icons/` ) );
	},
	adminIconsVariables() {
		return gulp.src( [
			`${ config.paths.css_src }/admin/variables/_icons.pcss`,
		] )
			.pipe( replace( /(\\[a-f0-9]+);/g, '"$1";' ) )
			.pipe( replace( /\$icomoon-font-path: "fonts" !default;\n/g, '' ) )
			.pipe( replace( config.icons.admin.replaceScss, '' ) )
			.pipe( replace( /\$/g, '\t--' ) )
			.pipe( replace( /;\n\n$/m, ';\n' ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/admin/variables/` ) );
	},
	themeIconsStyle() {
		return gulp.src( [
			`${ config.paths.css_src }/theme/base/_icons.pcss`,
		] )
			.pipe( replace( /url\('fonts\/(.+)'\) /g, 'url(\'../fonts/$1\') ' ) )
			.pipe( replace( / {2}/g, '\t' ) )
			.pipe( replace( /}$\n^\./gm, '}\n\n\.' ) )
			.pipe( replace( config.icons.theme.replaceName, config.icons.theme.varName ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/theme/base/` ) );
	},
	themeIconsVariables() {
		return gulp.src( [
			`${ config.paths.css_src }/theme/variables/_icons.pcss`,
		] )
			.pipe( replace( /(\\[a-f0-9]+);/g, '"$1";' ) )
			.pipe( replace( /\$icomoon-font-path: "fonts" !default;\n/g, '' ) )
			.pipe( replace( config.icons.theme.replaceScss, '' ) )
			.pipe( replace( /\$/g, '\t--' ) )
			.pipe( replace( /;\n\n$/m, ';\n' ) )
			.pipe( gulp.dest( `${ config.paths.css_src }/theme/variables/` ) );
	},
};
