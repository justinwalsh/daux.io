module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-php');

    grunt.initConfig({
        php: {
            dist: {
                options: {
                	keepalive: true,
                	open: true,
                    port: 8085
                }
            }
        }
    });

    grunt.registerTask('default', ['php']);
}