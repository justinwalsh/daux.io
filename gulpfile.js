var cssnano = require('cssnano'),
    gulp = require('gulp'),
    less = require('gulp-less'),
    rename = require('gulp-rename'),
    plumber = require('gulp-plumber'),
    postcss = require('gulp-postcss'),
    sourcemaps = require('gulp-sourcemaps'),
    stylelint = require('gulp-stylelint');

var resources = {
    daux_blue:{source: "themes/daux/less/theme-blue.less", dest: "themes/daux/css/"},
    daux_green:{source: "themes/daux/less/theme-green.less", dest: "themes/daux/css/"},
    daux_navy:{source: "themes/daux/less/theme-navy.less", dest: "themes/daux/css/"},
    daux_red:{source: "themes/daux/less/theme-red.less", dest: "themes/daux/css/"},

    daux_singlepage:{source: "themes/daux_singlepage/less/main.less", dest: "themes/daux_singlepage/css/"}
};

var stylelintRules = {
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

var cssnanoOptions = {
    safe: true,           // Disable dangerous optimisations
    filterPlugins: false, // This does very weird stuff
    autoprefixer: {
        add: true,        // Add needed prefixes
        remove: true      // Remove unnecessary prefixes
    }
};

function createCSSTask(source, dest) {
    return function () {


        return gulp.src(source)
            //.pipe(sourcemaps.init())
            .pipe(plumber())
            .pipe(less())
            .pipe(postcss([cssnano(cssnanoOptions)]))
            .pipe(rename({suffix: '.min'}))
            //.pipe(sourcemaps.write())
            .pipe(gulp.dest(dest));
    }
}

gulp.task('lint-css', function () {
    return gulp
        .src(['themes/**/less/**/*.less', '!themes/**/vendor/**/*.less'])
        .pipe(stylelint({
            failAfterError: true,
            config: {
                extends: 'stylelint-config-standard',
                rules: stylelintRules
            },
            syntax: 'less',
            reporters: [{
                formatter: 'string',
                console: true
            }],
            debug: true
        }));
});

var style_tasks = [];
for (var style in resources) {
    if (resources.hasOwnProperty(style)) {
        gulp.task('style_' + style, createCSSTask(resources[style].source, resources[style].dest));
        style_tasks.push('style_' + style);
    }
}

style_tasks.push('lint-css');

gulp.task("styles", style_tasks);

gulp.task('watch', function () {
    // Watch .less files
    gulp.watch('themes/**/less/**/*.less', ['styles']);
});

gulp.task('default', ['styles']);
