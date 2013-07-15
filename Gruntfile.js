module.exports = function(grunt) {
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
            files: {
              "css/daux-blue.css": "less/daux-blue.less",
              "css/daux-green.css": "less/daux-green.less",
              "css/daux-navy.css": "less/daux-navy.less",
              "css/daux-red.css": "less/daux-red.less"
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
}