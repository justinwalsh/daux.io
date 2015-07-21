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
                    port: 8085,
                    router: "index.php"
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
                    "resources/themes/daux-blue/css/theme.min.css": "resources/themes/daux-blue/less/theme.less",
                    "resources/themes/daux-green/css/theme.min.css": "resources/themes/daux-green/less/theme.less",
                    "resources/themes/daux-navy/css/theme.min.css": "resources/themes/daux-navy/less/theme.less",
                    "resources/themes/daux-red/css/theme.min.css": "resources/themes/daux-red/less/theme.less"
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
