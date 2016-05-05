
var gulp = require('gulp'),
    php = require('gulp-connect-php'),
    less = require('gulp-less'),
    rename = require('gulp-rename'),
    plumber = require('gulp-plumber'),
    postcss = require('gulp-postcss'),
    sourcemaps = require('gulp-sourcemaps');

var resources = {
    daux:{source: "themes/daux/less/theme.less", dest: "themes/daux/css/"},
    daux_blue:{source: "themes/daux/less/theme-blue.less", dest: "themes/daux/css/"},
    daux_green:{source: "themes/daux/less/theme-green.less", dest: "themes/daux/css/"},
    daux_navy:{source: "themes/daux/less/theme-navy.less", dest: "themes/daux/css/"},
    daux_red:{source: "themes/daux/less/theme-red.less", dest: "themes/daux/css/"},
    daux_singlepage:{source: "themes/daux_singlepage/less/main.less", dest: "themes/daux_singlepage/css/"}
};

var unusedRules = [
    //We only use one glyphicon ...
    ".glyphicon-",
    "!.glyphicon-chevron-right",
    "!.glyphicon-search",

    //we dont need all buttons
    ".btn-",
    "!.btn-group",
    "!.btn-default",
    "!.btn-sm",
    "!.btn-primary",
    "!.btn-secondary",
    "!.btn-hero",
    "!.btn-sidebar",
    ".caret",

    //Typography
    ".h1",
    ".h2",
    ".h3",
    ".h4",
    ".h5",
    ".h6",
    ".small",

    // We need only small columns
    ".col-",
    "!.col-sm",

    // We don't use a lot of navs and navbars
    ".navbar-fixed",
    ".navbar-inverse",
    ".navbar-default",
    ".nav-pills",
    ".nav-tabs",
    ".nav-stacked",
    ".nav-justified",

    // And a few others we don't use
    ".bg-",
    ".table"
];

function prepare_rules(rules) {
    var regexes = {inclusion: [], exclusion: []}, rule, pattern, regex, exclusion;

    for (rule in rules) {
        if (!rules.hasOwnProperty(rule)) continue;

        pattern = rules[rule];

        exclusion = pattern.indexOf('!') === 0;
        if (exclusion) { pattern = pattern.slice(1); }

        regex = pattern.replace('.', '\\.').replace('*', '(.*)');

        if (exclusion) {
            regexes.exclusion.push(new RegExp(regex));
        } else {
            regexes.inclusion.push(new RegExp(regex));
        }
    }

    return regexes;
}

function processPatterns(patterns, string) {
    var i;

    for (i in patterns.exclusion) {
        if (!patterns.exclusion.hasOwnProperty(i)) continue;
        if (string.match(patterns.exclusion[i])) return false;
    }

    for (i in patterns.inclusion) {
        if (!patterns.inclusion.hasOwnProperty(i)) continue;
        if (string.match(patterns.inclusion[i])) return true;
    }
}

function removeUnusedRules(rules) {
    var regexes = prepare_rules(rules);

    return function(css) {
        css.walkRules(function (rule) {
            var removedSome = false,
                selectors = rule.selectors,
                i;

            for (i = 0; i < selectors.length; i++) {
                if (processPatterns(regexes, selectors[i])) {
                    selectors.splice(i, 1);
                    i--;
                    removedSome = true;
                }
            }

            if(removedSome) {
                if (selectors.length == 0) {
                    rule.remove();
                } else {
                    rule.selectors = selectors;
                }
            }
        });

        return css;
    }
}



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
                removeUnusedRules(unusedRules),
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
