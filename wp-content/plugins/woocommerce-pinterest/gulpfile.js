var gulp = require('gulp');
var uglify = require('gulp-uglify');
var pipeline = require('readable-stream').pipeline;
var rename = require("gulp-rename");
var cleanCSS = require('gulp-clean-css');




gulp.task('js', function () {
    return pipeline(
        gulp.src(['assets/**/*.js','!assets/**/*min.js']),
        uglify(),
        rename({suffix: '.min'}),
        gulp.dest(function (file) {
            return file.base;
        })
    );
});

gulp.task('css', function () {
    return pipeline(
        gulp.src(['assets/**/*.css','!assets/**/*min.css']),
        cleanCSS(),
        rename({suffix: '.min'}),
        gulp.dest(function (file) {
            return file.base;
        })
    );
});

gulp.task('default', gulp.series('css', 'js'), function (done) {
    done();
});
