let gulp = require('gulp');
let wpPot = require('gulp-wp-pot');

gulp.task('compile', (done) => {

	// default compile tasks
	let tasks = ['makepot'];

	gulp.parallel(tasks)(done)
});

gulp.task('makepot', function () {
	return gulp.src('src/**/*.php')
		.pipe(wpPot( {
			domain: 'sv-wordpress-plugin-admin',
			package: 'SkyVerge WordPress Admin'
		} ))
		.pipe(gulp.dest('src/i18n/languages/sv-wordpress-plugin-admin.pot'));
});
