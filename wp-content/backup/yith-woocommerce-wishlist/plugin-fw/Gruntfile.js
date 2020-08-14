/**
 * in vagrant ssh, launch:
 * - npm install
 * - grunt (or use npm scripts in package.json)
 */

const potInfo = {
	potFilename: 'yith-plugin-fw.pot',
	potHeaders : {
		poedit                 : true, // Includes common Poedit headers.
		'x-poedit-keywordslist': true, // Include a list of all possible gettext functions.
		'report-msgid-bugs-to' : 'YITH <plugins@yithemes.com>',
		'language-team'        : 'YITH <info@yithemes.com>'
	}
};

module.exports = function ( grunt ) {
	'use strict';

	grunt.initConfig( {
						  dirs: {
							  css: 'assets/css',
							  js : 'assets/js'
						  },

						  uglify: {
							  options: {
								  ie8   : true,
								  parse : {
									  strict: false
								  },
								  output: {
									  comments: /@license|@preserve|^!/
								  }
							  },
							  common : {
								  files: [{
									  expand: true,
									  cwd   : '<%= dirs.js %>/',
									  src   : ['*.js', '!*.min.js'],
									  dest  : '<%= dirs.js %>/',
									  rename: function ( dst, src ) {
										  // To keep the source js files and make new files as `*.min.js`:
										  return dst + '/' + src.replace( '.js', '.min.js' );
									  }
								  }]
							  }
						  },

						  jshint: {
							  options: {
								  jshintrc: '.jshintrc'
							  },
							  all    : [
								  '<%= dirs.js %>/*.js',
								  '!<%= dirs.js %>/*.min.js'
							  ]
						  },


						  makepot: {
							  options: {
								  type         : 'wp-plugin',
								  domainPath   : 'languages',
								  domain       : 'yith-plugin-fw',
								  potHeaders   : potInfo.potHeaders,
								  updatePoFiles: true
							  },
							  dist   : {
								  options: {
									  potFilename: potInfo.potFilename,
									  exclude    : [
										  'node_modules/.*',
										  'tests/.*',
										  'tmp/.*'
									  ]
								  }
							  }
						  }

					  } );

	// Load NPM tasks to be used here.
	grunt.loadNpmTasks( 'grunt-wp-i18n' );

	// Use uglify-es (instead of uglify) to uglify also JS for ES6.
	grunt.loadNpmTasks( 'grunt-contrib-uglify-es' );

	// Register tasks.
	grunt.registerTask( 'js', ['uglify'] );
	grunt.registerTask( 'i18n', ['makepot'] );
	grunt.registerTask( 'default', [
		'js',
		'i18n'
	] );
};
