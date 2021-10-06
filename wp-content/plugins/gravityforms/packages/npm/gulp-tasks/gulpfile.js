const fs = require( 'fs' );
const gulp = require( 'gulp' );
const requireDir = require( 'require-dir' );
const findConfig = require( 'find-config' );
const config = require( './config' );
const tasks = requireDir( './src/tasks' );
const browserSync = require( 'browser-sync' ).create( config.browserSync.serverName );
const localConfig = findConfig.require( 'local-config.json' );
const bsConfig = localConfig || {
	proxy: config.browserSync.defaultUrl || '',
	certs_path: '',
};

/**
 * List out your tasks as defined in the gulp_tasks directory
 * require-dir module will bring those in here as an object
 * Each task type object will be named by its filename
 * So: 'postcss:admin' means a file named 'postcss' in the gulp-tasks dir,
 * and the method admin inside that modules export.
 * You must follow this approach, or modify the registerTasks function below.
 *
 * @type {string[]}
 */

const gulpTasks = [
	/* Copy tasks */

	'copy:adminIconsFonts', // copy fonts for admin icons from dev folder to fonts directory
	'copy:adminIconsStyles', // copy styles for admin icons to pcss shared dir
	'copy:adminIconsVariables', // copy variables for admin icons to theme pcss variables dir
	'copy:themeIconsFonts', // copy fonts for theme icons from dev folder to fonts directory
	'copy:themeIconsStyles', // copy styles for theme icons to pcss shared dir
	'copy:themeIconsVariables', // copy variables for theme icons to theme pcss variables dir

	/* Clean tasks */

	'clean:adminIconsStart', // delete all files related to admin icons in pcss, in prep for reinjection
	'clean:adminIconsEnd', // delete admin icon zip
	'clean:themeIconsStart', // delete all files related to theme icons in pcss, in prep for reinjection
	'clean:themeIconsEnd', // delete theme icon zip
	'clean:js', // clean chunks javascript

	/* Decompress tasks */

	'decompress:adminIcons', // extract icomoon admin kit to dev directory
	'decompress:themeIcons', // extract icomoon theme kit to dev directory

	/* Footer tasks */

	'footer:adminIconsVariables', // just adds a closing } to the admin icons variables file during the icons import transform tasks
	'footer:themeIconsVariables', // just adds a closing } to the theme icons variables file during the icons import transform tasks

	/* Header tasks */

	'header:adminIconsStyle', // sets the header for the admin icons style file in base during the icons import transform tasks
	'header:adminIconsVariables', // sets the header for the admin icons style file in vars during the icons import transform tasks
	'header:themeIconsStyle', // sets the header for the theme icons style file in base during the icons import transform tasks
	'header:themeIconsVariables', // sets the header for the theme icons style file in vars during the icons import transform tasks

	/* Postcss tasks */

	'postcss:adminCss', // the postcss task that transforms admin css
	'postcss:editorCss', // the postcss task that transforms editor css
	'postcss:settingsCss', // the postcss task that transforms settings css
	'postcss:adminThemeCss', // the postcss task that transforms the front end theme components used in the admin
	'postcss:adminIconCss', // the postcss task that outputs the admin kit to its own file
	'postcss:adminFontAwesomeCss', // the postcss task that transforms the deprecated font awesome kit
	'postcss:adminIE11Css', // the postcss task that transforms the admin ie11 css
	'postcss:baseCss', // the postcss task that transforms base css
	'postcss:themeCss', // the postcss task that transforms theme css
	'postcss:themeIE11Css', // the postcss task that transforms the theme ie11 css
	'postcss:devComponentCss', // the postcss task that transforms dev component css

	/* Replace tasks */

	'replace:adminIconsStyle', // runs regex to replace and convert scss to pcss compatible with our systems in the icons task
	'replace:adminIconsVariables', // runs regex to replace and convert scss to pcss compatible with our systems in the icons task
	'replace:themeIconsStyle', // runs regex to replace and convert scss to pcss compatible with our systems in the icons task
	'replace:themeIconsVariables', // runs regex to replace and convert scss to pcss compatible with our systems in the icons task

	/* Shell tasks */

	'shell:eslint', // runs eslint
	'shell:test', // runs jests tests
	'shell:scriptsThemeDev', // runs webpack for the theme dev build
	'shell:scriptsThemeProd', // runs webpack for the theme prod build
	'shell:scriptsAdminDev', // runs webpack for the admin dev build
	'shell:scriptsAdminProd', // runs webpack for the admin prod build

	/* Stylelint tasks */

	'stylelint:admin', // lints and fixes the admin pcss
	'stylelint:theme', // lints and fixes the theme pcss

	/* Watch Tasks (THESE MUST BE LAST) */

	'watch:main', // watch all fe assets and run appropriate routines
	'watch:watchAdminJS', // watch admin js and run appropriate webpack tasks
	'watch:watchThemeJS', // watch theme js and run appropriate webpack tasks
];

/**
 * Iterate over the above array. Split on the colon to access the imported tasks array's
 * corresponding function for the current task in the loop
 */

function registerTasks( tasksArray, taskModules ) {
	tasksArray.forEach( ( task ) => {
		const parts = task.split( ':' );
		gulp.task( task, taskModules[ parts[ 0 ] ][ parts[ 1 ] ] );
	} );
}

/**
 * Register all tasks in the src directory
 */

registerTasks( gulpTasks, tasks );

/**
 * Register external tasks
 */

if ( config.tasks && config.tasksDir && fs.existsSync( config.tasksDir ) ) {
	const externalTaskModules = requireDir( config.tasksDir );
	registerTasks( config.tasks, externalTaskModules );
}

const watchTasks = [ 'watch:main', 'watch:watchAdminJS', 'watch:watchThemeJS' ];

gulp.task( 'watch', gulp.parallel( watchTasks ) );

/**
 * Lints css, fixes common issues automatically.
 */

gulp.task(
	'lint',
	gulp.series(
		gulp.parallel(
			'shell:eslint',
			'stylelint:admin',
		)
	)
);

/**
 * Takes a zip file from icomoon and injects it into the postcss, modifying the scss to pcss and handling all conversions/cleanup.
 */

gulp.task( 'icons:admin', gulp.series(
	'clean:adminIconsStart',
	'decompress:adminIcons',
	'copy:adminIconsFonts',
	'copy:adminIconsStyles',
	'copy:adminIconsVariables',
	'replace:adminIconsStyle',
	'replace:adminIconsVariables',
	'header:adminIconsStyle',
	'header:adminIconsVariables',
	'footer:adminIconsVariables',
	'clean:adminIconsEnd',
	'postcss:adminCss',
	'postcss:editorCss',
	'postcss:settingsCss',
	'postcss:adminThemeCss',
	'postcss:adminIconCss',
	'postcss:adminIE11Css',
	'postcss:adminFontAwesomeCss',
	'postcss:baseCss',
	'postcss:themeCss',
	'postcss:themeIE11Css',
) );

gulp.task( 'icons:theme', gulp.series(
	'clean:themeIconsStart',
	'decompress:themeIcons',
	'copy:themeIconsFonts',
	'copy:themeIconsStyles',
	'copy:themeIconsVariables',
	'replace:themeIconsStyle',
	'replace:themeIconsVariables',
	'header:themeIconsStyle',
	'header:themeIconsVariables',
	'footer:themeIconsVariables',
	'clean:themeIconsEnd',
	'postcss:adminCss',
	'postcss:editorCss',
	'postcss:settingsCss',
	'postcss:adminIconCss',
	'postcss:adminThemeCss',
	'postcss:adminIE11Css',
	'postcss:adminFontAwesomeCss',
	'postcss:baseCss',
	'postcss:themeCss',
	'postcss:themeIE11Css',
) );

/**
 * Watches all css and php for bundle, runs tasks and reloads browser using browsersync.
 */

gulp.task( 'dev', gulp.parallel( watchTasks, async function() {
	browserSync.init( {
		watchTask: true,
		debugInfo: true,
		logConnections: true,
		notify: true,
		open: 'external',
		host: bsConfig.proxy,
		proxy: `https://${ bsConfig.proxy }`,
		https: {
			key: `${ bsConfig.certs_path }/${ bsConfig.proxy }.key`,
			cert: `${ bsConfig.certs_path }/${ bsConfig.proxy }.crt`,
		},
		ghostMode: {
			scroll: true,
			links: true,
			forms: true,
		},
	} );
} ) );

/**
 * Builds the entire package for production locally
 */

gulp.task( 'dist',
	gulp.series(
		gulp.parallel( 'lint' ),
		gulp.parallel( 'clean:js', 'postcss:adminCss', 'postcss:editorCss', 'postcss:settingsCss' ),
		gulp.parallel( 'postcss:adminThemeCss', 'postcss:adminIconCss', 'postcss:adminFontAwesomeCss', 'postcss:adminIE11Css' ),
		gulp.parallel( 'postcss:baseCss', 'postcss:themeCss', 'postcss:themeIE11Css' ),
		gulp.parallel( 'shell:scriptsThemeDev', 'shell:scriptsAdminDev' )
	)
);

gulp.task( 'default', gulp.series( 'dist' ) );
