const gulp = require( 'gulp' );
const config = require( '../../config' );
const shell = require( 'gulp-shell' );
const browserSync = require( 'browser-sync' );

module.exports = {
	eslint() {
		return gulp.src( './' )
			.pipe( shell( 'npm run lint' ) );
	},
	test() {
		return gulp.src( './' )
			.pipe( shell( 'npm run test' ) );
	},
	scriptsThemeDev() {
		const server = browserSync.get( config.browserSync.serverName );
		return gulp.src( './' )
			.pipe( shell( 'npm run js:theme:dev' ) )
			.on( 'finish', function() {
				if ( server.active ) {
					server.reload();
				}
			} );
	},
	scriptsThemeProd() {
		return gulp.src( './' )
			.pipe( shell( 'npm run js:theme:prod' ) );
	},
	scriptsAdminDev() {
		const server = browserSync.get( config.browserSync.serverName );
		return gulp.src( './' )
			.pipe( shell( 'npm run js:admin:dev' ) )
			.on( 'finish', function() {
				if ( server.active ) {
					server.reload();
				}
			} );
	},
	scriptsAdminProd() {
		return gulp.src( './' )
			.pipe( shell( 'npm run js:admin:prod' ) );
	},
};
