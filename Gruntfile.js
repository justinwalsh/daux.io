module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-php');

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
        }
    });

    grunt.registerTask('default', ['php']);
};
