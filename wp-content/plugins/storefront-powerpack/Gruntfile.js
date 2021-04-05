var sass = require('node-sass');

/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig({

		// JavaScript linting with JSHint.
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'assets/js/*.js',
				'!assets/js/*.min.js',
				'includes/customizer/designer/assets/js/*js',
				'!includes/customizer/designer/assets/js/*.min.js',
				'includes/customizer/header/assets/js/*js',
				'!includes/customizer/header/assets/js/*.min.js',
				'includes/customizer/checkout/assets/js/*js',
				'!includes/customizer/checkout/assets/js/*.min.js'
			]
		},

		// Minify .js files.
		uglify: {
			options: {
				preserveComments: 'some',
				quoteStyle: 1
			},
			main: {
				files: [{
					expand: true,
					cwd: 'assets/js/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: 'assets/js/',
					ext: '.min.js'
				}]
			},
			designer: {
				files: [{
					expand: true,
					cwd: 'includes/customizer/designer/assets/js/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: 'includes/customizer/designer/assets/js/',
					ext: '.min.js'
				}]
			},
			header: {
				files: [{
					expand: true,
					cwd: 'includes/customizer/header/assets/js/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: 'includes/customizer/header/assets/js/',
					ext: '.min.js'
				}]
			},
			checkout: {
				files: [{
					expand: true,
					cwd: 'includes/customizer/checkout/assets/js/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: 'includes/customizer/checkout/assets/js/',
					ext: '.min.js'
				}]
			}
		},

		// Compile all .scss files.
		sass: {
			dist: {
				options: {
					implementation: sass,
					require: 'susy',
					sourcemap: 'none',
					includePaths: ['node_modules', 'node_modules/susy/sass'].concat( require( 'node-bourbon' ).includePaths )
				},
				files: [{
					'assets/css/customizer.css': 'assets/css/customizer.scss',
					'assets/css/admin.css': 'assets/css/admin.scss',
					'assets/css/style.css': 'assets/css/style.scss',
					'assets/css/fontawesome-4.css': 'assets/css/fontawesome-4.scss',
					'includes/customizer/checkout/assets/css/layout.css': 'includes/customizer/checkout/assets/css/layout.scss',
					'includes/customizer/checkout/assets/css/distraction-free.css': 'includes/customizer/checkout/assets/css/distraction-free.scss',
					'includes/customizer/checkout/assets/css/two-step.css': 'includes/customizer/checkout/assets/css/two-step.scss',
					'includes/customizer/designer/assets/css/sp-designer.css': 'includes/customizer/designer/assets/css/sp-designer.scss',
					'includes/customizer/designer/assets/css/sp-designer-preview.css': 'includes/customizer/designer/assets/css/sp-designer-preview.scss',
					'includes/customizer/header/assets/css/sp-header.css': 'includes/customizer/header/assets/css/scss/sp-header.scss',
					'includes/customizer/header/assets/css/sp-header-frontend.css': 'includes/customizer/header/assets/css/scss/sp-header-frontend.scss',
					'includes/customizer/header/assets/css/sp-sticky-header.css': 'includes/customizer/header/assets/css/scss/sp-sticky-header.scss',
					'includes/customizer/layout/assets/css/layout.css': 'includes/customizer/layout/assets/css/layout.scss'
				}]
			}
		},

		// Watch changes for assets.
		watch: {
			css: {
				files: [
					'assets/css/customizer.scss',
					'assets/css/admin.scss',
					'assets/css/style.scss',
					'assets/css/fontawesome-4.scss',
					'includes/customizer/checkout/assets/css/distraction-free.scss',
					'includes/customizer/checkout/assets/css/layout.scss',
					'includes/customizer/checkout/assets/css/two-step.scss',
					'includes/customizer/designer/assets/css/sp-designer.scss',
					'includes/customizer/designer/assets/css/sp-designer-preview.scss',
					'includes/customizer/header/assets/css/scss/sp-header.scss',
					'includes/customizer/header/assets/css/scss/sp-header-frontend.scss',
					'includes/customizer/header/assets/css/scss/sp-sticky-header.scss',
					'includes/customizer/layout/assets/css/layout.scss'
				],
				tasks: [
					'sass'
				]
			},
			js: {
				files: [
					// main js
					'assets/js/*js',
					'!assets/js/*.min.js',
					'includes/customizer/designer/assets/js/*js',
					'!includes/customizer/designer/assets/js/*.min.js',
					'includes/customizer/header/assets/js/*js',
					'!includes/customizer/header/assets/js/*.min.js',
					'includes/customizer/checkout/assets/js/*js',
					'!includes/customizer/checkout/assets/js/*.min.js'
				],
				tasks: ['jshint', 'uglify']
			}
		},

		// RTLCSS
		rtlcss: {
			options: {
				config: {
					swapLeftRightInUrl: false,
					swapLtrRtlInUrl: false,
					autoRename: false,
					preserveDirectives: true
				}
			},
			main: {
				expand: true,
				ext: '-rtl.css',
				src: [
					'assets/css/style.css',
					'includes/customizer/checkout/assets/css/distraction-free.css',
					'includes/customizer/checkout/assets/css/layout.css',
					'includes/customizer/checkout/assets/css/two-step.css'
				]
			}
		},

		// Creates deploy-able theme
		copy: {
			deploy: {
				src: [
					'**',
					'!.*',
					'!*.md',
					'!.*/**',
					'.htaccess',
					'!Gruntfile.js',
					'!package*.json',
					'!node_modules/**',
					'!.DS_Store',
					'!npm-debug.log'
				],
				dest: 'storefront-powerpack',
				expand: true,
				dot: true
			}
		},

		// Check textdomain errors.
		checktextdomain: {
			options:{
				text_domain: 'storefront-powerpack',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'**/*.php', // Include all files
					'!node_modules/**' // Exclude node_modules/
				],
				expand: true
			}
		},

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-theme',
				domainPath: 'languages',
				potHeaders: {
					'report-msgid-bugs-to': 'https://github.com/woothemes/storefront-powerpack/issues',
					'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
				}
			},
			frontend: {
				options: {
					potFilename: 'storefront-powerpack.pot',
					exclude: [
						'storefront-powerpack/.*' // Exclude deploy directory
					]
				}
			}
		},
		compress: {
			zip: {
				options: {
					archive: './storefront-powerpack.zip',
					mode: 'zip'
				},
				files: [
					{ src: './storefront-powerpack/**' }
				]
			}
		}
	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-rtlcss' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );

	// Register tasks
	grunt.registerTask( 'default', [
		'css',
		'jshint',
		'uglify'
	]);

	grunt.registerTask( 'css', [
		'sass',
		'rtlcss'
	]);

	grunt.registerTask( 'dev', [
		'default',
		'makepot'
	]);

	grunt.registerTask( 'deploy', [
		'dev',
		'copy',
		'compress'
	]);
};