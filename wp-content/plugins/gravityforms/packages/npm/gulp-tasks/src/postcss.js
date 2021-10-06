const gulp = require( 'gulp' );
const postcss = require( 'gulp-postcss' );
const sourcemaps = require( 'gulp-sourcemaps' );
const rename = require( 'gulp-rename' );
const gulpif = require( 'gulp-if' );
const concat = require( 'gulp-concat' );
const browserSync = require( 'browser-sync' );
const config = require( '../config' );

const compilePlugins = [
	require( 'postcss-import' )( {
		path: [
			`./${ config.paths.root }`,
			`./${ config.paths.css_dist }`,
			`./${ config.paths.legacy_css }`,
		],
	} ),
	require( 'postcss-mixins' ),
	require( 'postcss-custom-media' ),
	require( 'postcss-custom-properties' )( { preserve: false } ),
	require( 'postcss-extend' ),
	require( 'postcss-nested' ),
	require( 'postcss-preset-env' )( { stage: 0, autoprefixer: { grid: true } } ),
];

const compileTheme = [
	require( 'postcss-import' )( {
		path: [
			`./${ config.paths.root }`,
			`./${ config.paths.css_dist }`,
			`./${ config.paths.legacy_css }`,
		],
	} ),
	require( 'postcss-mixins' ),
	require( 'postcss-custom-media' ),
	require( 'postcss-custom-properties' )( { preserve: false } ),
	require( 'postcss-extend' ),
	require( 'postcss-nested' ),
	require( 'postcss-preset-env' )( { stage: 0, autoprefixer: { grid: true } } ),
	require( 'postcss-rem-to-pixel' )( { propList: [ '*' ] } ),
];

const compileDevDirectory = [
	require( 'postcss-import' )( {
		path: [
			`./${ config.paths.root }`,
		],
	} ),
	require( 'postcss-custom-properties' )( { preserve: false } ),
	require('tailwindcss'),
	require('autoprefixer'),
];

/**
 *
 *
 * @param {Object} options {
 * 	src = [],
 * 	dest = config.paths.core_admin_css,
 * 	plugins = compilePlugins,
 * 	bundleName = 'empty.css',
 * }
 * @param {Array<string>} options.src
 * @param {string} options.dest
 * @param {Array<Function>} options.plugins
 * @param {string} options.bundleName
 * @returns
 */
function cssProcess( {
	src = [],
	dest = config.paths.css_dist,
	plugins = compilePlugins,
	bundleName = 'empty.css', // Needs to be a valid filename else concat errors
} ) {
	const server = browserSync.get( config.browserSync.serverName );
	return gulp.src( src )
		.pipe( sourcemaps.init() )
		.pipe( postcss( plugins ) )
		.pipe( rename( { extname: '.css' } ) )
		.pipe( gulpif(
			bundleName !== 'empty.css',
			concat( bundleName )
		) )
		.pipe( sourcemaps.write( '.' ) )
		.pipe( gulp.dest( dest ) )
		.pipe( gulpif( server.active, server.reload( { stream: true } ) ) );
}

module.exports = {
	adminCss() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/admin/admin.pcss`,
			],
			dest: config.paths.css_dist,
		} );
	},
	adminThemeCss() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/admin/admin-theme.pcss`,
			],
			dest: config.paths.css_dist,
		} );
	},
	adminIconCss() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/admin/admin-icons.pcss`,
			],
			dest: config.paths.css_dist,
		} );
	},
	adminFontAwesomeCss() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/admin/deprecated/font-awesome.pcss`,
			],
			dest: config.paths.css_dist,
		} );
	},
	adminIE11Css() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/admin/admin-ie11.pcss`,
			],
			dest: config.paths.css_dist,
		} );
	},
	editorCss() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/admin/editor.pcss`,
			],
			dest: config.paths.css_dist,
		} );
	},
	settingsCss() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/admin/settings.pcss`,
			],
			dest: config.paths.settings_css_dist,
		} );
	},
	baseCss() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/theme/basic.pcss`,
			],
			dest: config.paths.css_dist,
			plugins: compileTheme,
		} );
	},
	themeCss() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/theme/theme.pcss`,
			],
			dest: config.paths.css_dist,
			plugins: compileTheme,
		} );
	},
	themeIE11Css() {
		return cssProcess( {
			src: [
				`${ config.paths.css_src }/theme/theme-ie11.pcss`,
			],
			dest: config.paths.css_dist,
			plugins: compileTheme,
		} );
	},

	devComponentCss() {
		return cssProcess( {
			src: [
				`${ config.paths.dev }/components/layout.pcss`,
			],
			dest: `${ config.paths.dev }/components/`,
			plugins: compileDevDirectory,
		} );
	},
};
