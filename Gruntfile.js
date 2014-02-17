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
                    "css/daux-blue.min.css": "less/daux-blue.less",
                    "css/daux-green.min.css": "less/daux-green.less",
                    "css/daux-navy.min.css": "less/daux-navy.less",
                    "css/daux-red.min.css": "less/daux-red.less"
                }
            }
        },
        watch: {
            scripts: {
                files: ['less/**/*.less'],
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