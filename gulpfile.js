
var gulp = require('gulp'),
    php = require('gulp-connect-php'),
    less = require('gulp-less'),
    rename = require('gulp-rename'),
    plumber = require('gulp-plumber'),
    postcss = require('gulp-postcss'),
    sourcemaps = require('gulp-sourcemaps');

var resources = {
    daux_blue:{source: "themes/daux/less/theme-blue.less", dest: "themes/daux/css/"},
    daux_green:{source: "themes/daux/less/theme-green.less", dest: "themes/daux/css/"},
    daux_navy:{source: "themes/daux/less/theme-navy.less", dest: "themes/daux/css/"},
    daux_red:{source: "themes/daux/less/theme-red.less", dest: "themes/daux/css/"},

    daux_singlepage:{source: "themes/daux_singlepage/less/main.less", dest: "themes/daux_singlepage/css/"}
};

function createTask(source, dest) {
    return function() {
        var nano_options = {
            safe: true,           // Disable dangerous optimisations
            filterPlugins: false, // This does very weird stuff
            autoprefixer: {
                add: true,                // Add needed prefixes
                remove: true              // Remove unnecessary prefixes
            }
        };

        return gulp.src(source)
            //.pipe(sourcemaps.init())
            .pipe(plumber())
            .pipe(less())
            .pipe(postcss([
                require('cssnano')(nano_options)
            ]))
            .pipe(rename({suffix: '.min'}))
            //.pipe(sourcemaps.write())
            .pipe(gulp.dest(dest));
    }
}


function createLinter() {
    var gulpStylelint = require('gulp-stylelint');

    var rules = {
        "indentation": 4,
        "selector-list-comma-newline-after": "always-multi-line",
        "selector-no-id": true,

        // Autoprefixer
        "at-rule-no-vendor-prefix": true,
        "media-feature-name-no-vendor-prefix": true,
        "property-no-vendor-prefix": true,
        "selector-no-vendor-prefix": true,
        "value-no-vendor-prefix": true
    };

    return gulp
        .src(['themes/**/less/**/*.less', '!themes/**/vendor/**/*.less'])
        .pipe(gulpStylelint({
            failAfterError: true,
            config: {
                extends: "stylelint-config-standard",
                rules: rules
            },
            syntax: "less",
            reporters: [
                {formatter: 'string', console: true}
            ],
            debug: true
        }));
}



var style_tasks = [];
for (var style in resources) {
    gulp.task('style_' + style, createTask(resources[style].source, resources[style].dest));
    style_tasks.push('style_' + style);
}

gulp.task('lint-css', createLinter);
style_tasks.push('lint-css');

gulp.task("styles", style_tasks);


gulp.task('watch', function() {

    // Watch .less files
    gulp.watch('themes/**/less/**/*.less', ['styles']);

});

gulp.task('php', function() {
    php.server({
        keepalive: true,
        open: true,
        port: 8085,
        router: "index.php"
    });
});

gulp.task('default', ['php']);
