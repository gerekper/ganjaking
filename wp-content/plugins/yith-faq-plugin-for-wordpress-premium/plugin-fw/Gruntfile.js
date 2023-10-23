const potInfo = {
	languageFolderPath: './languages/',
	filename          : 'yith-plugin-fw.pot',
	headers           : {
		poedit                 : true, // Includes common Poedit headers.
		'x-poedit-keywordslist': true, // Include a list of all possible gettext functions.
		'report-msgid-bugs-to' : 'YITH <plugins@yithemes.com>',
		'language-team'        : 'YITH <info@yithemes.com>'
	}
};

module.exports = function ( grunt ) {
	'use strict';

	var sass = require( 'node-sass' );

	grunt.initConfig( {
						  dirs: {
							  css : 'assets/css',
							  scss: 'assets/css/scss',
							  js  : 'assets/js'
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

						  // Compile all .scss files.
						  sass: {
							  compile: {
								  options: {
									  implementation: sass,
									  outputStyle: 'expanded'
								  },
								  files  : [{
									  expand: true,
									  cwd   : '<%= dirs.scss %>/',
									  src   : ['*.scss'],
									  dest  : '<%= dirs.css %>/',
									  ext   : '.css'
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


						  // Watch changes for assets.
						  watch: {
							  css: {
								  files: ['<%= dirs.scss %>/**/*.scss'],
								  tasks: ['sass']
							  }
						  },

						  makepot  : {
							  options: {
								  type         : 'wp-plugin',
								  domainPath   : 'languages',
								  domain       : 'yith-plugin-fw',
								  potHeaders   : potInfo.headers,
								  updatePoFiles: false
							  },
							  dist   : {
								  options: {
									  potFilename: potInfo.filename,
									  exclude    : [
										  'bin/.*',
										  'dist/.*',
										  'node_modules/.*',
										  'tests/.*',
										  'tmp/.*',
										  'vendor/.*'
									  ]
								  }
							  }
						  },
						  update_po: {
							  options: {
								  template: potInfo.languageFolderPath + potInfo.filename
							  },
							  build  : {
								  src: potInfo.languageFolderPath + '*.po'
							  }
						  },

						  // PHP Code Sniffer.
						  phpcs: {
							  options: {
								  bin: 'vendor/bin/phpcs'
							  },
							  dist   : {
								  src: [
									  '**/*.php', // Include all php files.
									  '!bin/**',
									  '!dist/**',
									  '!node_modules/**',
									  '!tests/**',
									  '!tools/**',
									  '!tmp/**',
									  '!vendor/**'
								  ]
							  }
						  }

					  } );

	grunt.registerMultiTask( 'update_po', 'This task update .po strings by .pot', function () {
		grunt.log.writeln( 'Updating .po files.' );

		var done     = this.async(),
			options  = this.options(),
			template = options.template;
		this.files.forEach( function ( file ) {
			if ( file.src.length ) {
				var counter = file.src.length;

				grunt.log.writeln( 'Processing ' + file.src.length + ' files.' );

				file.src.forEach( function ( fileSrc ) {
					grunt.util.spawn( {
										  cmd : 'msgmerge',
										  args: ['-U', fileSrc, template]
									  }, function ( error, result, code ) {
						const output = fileSrc.replace( '.po', '.mo' );
						grunt.log.writeln( 'Updating: ' + fileSrc + ' ...' );

						if ( error ) {
							grunt.verbose.error();
						} else {
							grunt.verbose.ok();
						}

						// Updating also the .mo files
						grunt.util.spawn( {
											  cmd : 'msgfmt',
											  args: [fileSrc, '-o', output]
										  }, function ( moError, moResult, moCode ) {
							grunt.log.writeln( 'Updating MO for: ' + fileSrc + ' ...' );
							counter--;
							if ( moError || counter === 0 ) {
								done( moError );
							}
						} );
						if ( error ) {
							done( error );
						}
					} );
				} );
			} else {
				grunt.log.writeln( 'No file to process.' );
			}
		} );
	} );

	// Load NPM tasks to be used here.
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.loadNpmTasks( 'grunt-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );

	// Use uglify-es (instead of uglify) to uglify also JS for ES6.
	grunt.loadNpmTasks( 'grunt-contrib-uglify-es' );

	// Register tasks.
	grunt.registerTask( 'js', ['uglify'] );

	grunt.registerTask( 'css', ['sass'] );
	grunt.registerTask( 'assets', ['js', 'css'] );
};
