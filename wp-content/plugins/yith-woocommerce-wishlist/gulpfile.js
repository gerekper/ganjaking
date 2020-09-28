var gulp = require('gulp');
var cleanCSS = require('gulp-clean-css');
var uglify = require('gulp-uglify-es').default;
var wpPot = require('gulp-wp-pot');
var poSync = require('gulp-po-sync');
var po2mo = require('gulp-po2mo');
var jshint = require('gulp-jshint');

/* Task to compile less */

var minifyCss = function () {
    return gulp.src('assets/css/unminified/*.css')
        .pipe(cleanCSS({debug: true}, (details) => {
            console.log(`${details.name}: ${details.stats.originalSize}kb => ${details.stats.minifiedSize} kb`);
            }))
        .pipe(gulp.dest('./assets/css/'));
};

var minifyThemesCss = function () {
    return gulp.src('assets/css/unminified/themes/*.css')
        .pipe(cleanCSS({debug: true}, (details) => {
            console.log(`${details.name}: ${details.stats.originalSize}kb => ${details.stats.minifiedSize} kb`);
        }))
        .pipe(gulp.dest('./assets/css/themes/'));
};

var minifyMainJs = function () {
    return gulp.src('./assets/js/unminified/jquery.yith-wcwl.js')
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js/'))
};

var minifyAdminJs = function () {
    return gulp.src('./assets/js/unminified/admin/yith-wcwl.js')
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js/admin/'))
};

var updatePot = function () {
    return gulp.src(['./*.php', './**/*.php', '!./plugin-fw/**/*.php', '!./plugin-upgrade/**/*.php'])
        .pipe(wpPot( {
            domain: 'yith-woocommerce-wishlist',
            package: 'YITH WooCommerce Wishlist',
            destFile: './languages/yith-woocommerce-wishlist.pot',
            metadataFile: '../init.php',
            headers: {
                "Project-Id-Version": "YITH WooCommerce Wishlist Premium",
                "Content-Type": "text/plain; charset=UTF-8",
                "Language-Team": "YITH <plugins@yithemes.com>",
                "X-Poedit-KeywordsList": "__;_e;_n:1,2;__ngettext:1,2;__ngettext_noop:1,2;_n_noop:1,2;_c,_nc:4c,1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;esc_attr__;esc_attr_e;esc_html__;esc_html_e",
                "X-Poedit-Basepath": "..",
                "X-Poedit-SearchPath-0": ".",
                "X-Poedit-SearchPathExcluded-0": "plugin-fw",
                "X-Poedit-SearchPathExcluded-1": "plugin-upgrade",
                "X-Poedit-SearchPathExcluded-2": "node_modules"
            }
        } ))
        .pipe(gulp.dest('./languages/yith-woocommerce-wishlist.pot'));
};

var updatePo = function () {
    return gulp.src('./languages/**/*.po')
        .pipe(poSync('./languages/yith-woocommerce-wishlist.pot'))
        .pipe(gulp.dest('./languages'));
};

var updateMo = function () {
    return gulp.src('./languages/**/*.po')
        .pipe(po2mo())
        .pipe(gulp.dest('./languages'));
};

var validateJs = function () {
    return gulp.src('./assets/js/unminified/*yith*.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
};

exports.minify_js = gulp.series(minifyMainJs, minifyAdminJs);
exports.minify = gulp.series(minifyCss, minifyThemesCss);
exports.uppot = gulp.series(updatePot);
exports.localize = gulp.series(updatePot, updatePo, updateMo);
exports.deploy = gulp.series(minifyCss, minifyThemesCss, minifyMainJs, minifyAdminJs, updatePot, updatePo, updateMo);
exports.default = gulp.series(minifyCss, minifyThemesCss, validateJs, minifyMainJs, minifyAdminJs);
exports.jshint = gulp.series(validateJs);
