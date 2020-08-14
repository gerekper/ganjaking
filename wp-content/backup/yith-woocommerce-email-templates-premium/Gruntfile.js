/**
 * Required
 * - install grunt
 *      sudo npm install -g grunt-cli
 * - install node-wp-i18n
 *      sudo npm install -g node-wp-i18n
 */

const potInfo = {
    potFilename: 'yith-woocommerce-email-templates.pot',
    potHeaders : {
        poedit                 : true, // Includes common Poedit headers.
        'x-poedit-keywordslist': true, // Include a list of all possible gettext functions.
        'report-msgid-bugs-to' : 'Your Inspiration Themes <plugins@yithemes.com>',
        'language-team'        : 'Your Inspiration Themes <info@yithemes.com>'
    }
};

module.exports = function ( grunt ) {
    'use strict';

    grunt.initConfig( {
                          makepot: {
                              options: {
                                  type         : 'wp-plugin',
                                  domainPath   : 'languages',
                                  potHeaders   : potInfo.potHeaders,
                                  updatePoFiles: true,
                                  processPot: function( pot ) {
                                      // Exclude plugin meta
                                      var translation,
                                          excluded_meta = [
                                              'Plugin Name of the plugin/theme',
                                              'Plugin URI of the plugin/theme',
                                              'Author of the plugin/theme',
                                              'Author URI of the plugin/theme'
                                          ];

                                      for ( translation in pot.translations[''] ) {
                                          if ( 'undefined' !== typeof pot.translations[''][ translation ].comments.extracted ) {
                                              if ( excluded_meta.indexOf( pot.translations[''][ translation ].comments.extracted ) >= 0 ) {
                                                  console.log( 'Excluded meta: ' + pot.translations[''][ translation ].comments.extracted );
                                                  delete pot.translations[''][ translation ];
                                              }
                                          }
                                      }

                                      return pot;
                                  },
                              },
                              dist   : {
                                  options: {
                                      potFilename: potInfo.potFilename,
                                      exclude    : [
                                          'plugin-fw/.*',
                                          'plugin-upgrade/.*',
                                          'mode_modules/.*',
                                          'tmp/.*',
                                      ]
                                  }
                              }
                          }

                      } );

    // Load NPM tasks to be used here.
    grunt.loadNpmTasks( 'grunt-wp-i18n' );

    // Register tasks.
    grunt.registerTask( 'default', [
        'i18n'
    ] );

    grunt.registerTask( 'i18n', [ 'makepot' ] );
};
