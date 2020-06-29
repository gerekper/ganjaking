var gulp = require('gulp');

gulp.task('rjs', function () {
    var requirejsOptimize = require('gulp-requirejs-optimize');
    return gulp.src('src/assets/js/src/main.js')
        .pipe(requirejsOptimize(function (file) {
            return {
                preserveLicenseComments: false,
                optimize: 'uglify',
                wrap: true,
                baseUrl: './src/assets/js/src',
                name: "almond",
                include: "main",
                out: "mailoptin.min.js"
            };
        }))
        .pipe(gulp.dest('src/assets/js'));
});

gulp.task('default', ['rjs']);