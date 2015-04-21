module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-php');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.initConfig({
        php: {
            dist: {
                options: {
                    keepalive: true,
                    open: true,
                    port: 8085
                }
            }
        },
        less: {
            development: {
                options: {
                    cleancss: true,
                    report: 'min'
                },
                files: {
                    "templates/default/themes/daux-blue/css/theme.min.css": "templates/default/themes/daux-blue/less/theme.less",
                    "templates/default/themes/daux-green/css/theme.min.css": "templates/default/themes/daux-green/less/theme.less",
                    "templates/default/themes/daux-navy/css/theme.min.css": "templates/default/themes/daux-navy/less/theme.less",
                    "templates/default/themes/daux-red/css/theme.min.css": "templates/default/themes/daux-red/less/theme.less"
                }
            }
        },
        watch: {
            scripts: {
                files: ['templates/default/theme/**/*.less'],
                tasks: ['less'],
                options: {
                    nospawn: true
                },
            },
        },
    });

    //grunt.registerTask('default', ['less', 'watch']);
    grunt.registerTask('default', ['php']);
};
