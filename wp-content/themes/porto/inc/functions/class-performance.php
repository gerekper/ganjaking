<?php
/**
 * Porto Performance
 *
 * @author     Porto Themes
 * @since      6.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Porto_Performance' ) ) :
	class Porto_Performance {
		/**
		 * Belongs to plugin we bundle.
		 *
		 * @access public
		 * @since 6.3.0
		 */
		public $including_plugins = array(
			'woocommerce',
			'wc',
			'prettyPhoto',
			'jquery-blockui', // woocommerce
			'js-cookie', // woocommerce
			'jquery-cookie', // woocommerce
			'zoom', // woocommerce
			'contact-form-7',
			'revslider',
			'tp-tools', // revolution slider
			'revmin',
			'rs-plugin-settings', // revolution slider
			'porto',
			'jquery-magnific-popup',
			'jquery-selectric',
			'elementor',
			'e-animations',
			'jquery-selectBox',
			'wpforms',
			'wp-block-library',
			'bootstrap', // porto bootstrap plugin
			'lazyload', // porto lazyload plugin
			'js_composer', // wpb
			'wpb_composer',
			'vc',
			'gglcptch', // google captcha
			'dokan',
			'mec-',  // modern events calendar
			'featherlight',
			'wpgdprc',
			'jquery-flipshow', // porto plugin
		);
		/**
		 * Exclude Javascript.
		 *
		 * @access public
		 * @since 6.3.0
		 */
		public $exclude_javascript = array(
			'elementor-common-modules', // if login elementor
			'elementor-dialog',
			'elementor-common',
			'elementor-app-loader',
			'elementor-admin-bar',
			'elementor-web-cli',
		);
		/**
		 * Exclude Style.
		 *
		 * @access public
		 * @since 6.3.0
		 */
		public $exclude_style = array(
			'porto-google-fonts',
			'porto_admin_bar',
			'elementor-icons',
			'elementor-common', // if login elementor
			'mec-font-icons',  // modern events calendar
			'mec-google-fonts',
		);

		/**
		 * The google fonts and elementor-icons for elementor
		 *
		 * @since 6.3.0
		 */
		public $defer_elementor_style = array();
		/**
		 * Removed resources because of merge
		 *
		 * @access public
		 * @since 6.3.0
		 */
		public $removed_resources = array();

		/**
		 * Css var
		 *
		 * @access private
		 * @since 6.3.0
		 */
		private $css_vars = array();

		/**
		 * The exclude css var about responsive var.
		 * 
		 * @access private
		 * @since 6.3.0
		 */
		private $exclude_vars = array(
			'--porto-container-width',
			'--porto-logo-mw',
			'--porto-res-spacing',
			'--porto-fluid-spacing',
			'--porto-container-spacing',
			'--porto-mobile-fs-scale',
			'--porto-body-fs',
			'--porto-body-lh',
			'--porto-body-ls',
			'--porto-h1-fs',
			'--porto-h1-lh',
			'--porto-h1-ls',
			'--porto-h2-fs',
			'--porto-h2-lh',
			'--porto-h2-ls',
			'--porto-h3-fs',
			'--porto-h3-lh',
			'--porto-h3-ls',
			'--porto-h4-fs',
			'--porto-h4-lh',
			'--porto-h4-ls',
			'--porto-h5-fs',
			'--porto-h5-lh',
			'--porto-h5-ls',
			'--porto-h6-fs',
			'--porto-h6-lh',
			'--porto-h6-ls',
		);

		/**
		 * defer style
		 *
		 * @since 6.3.0
		 */
		public static $defer_style;

		/**
		 * The existing of merged css.
		 *
		 * @var bool
		 * @since 6.3.0
		 */
		public static $is_merged_style;

		public function __construct() {
			// image quality
			add_filter( 'jpeg_quality', array( $this, 'modify_jpg_quality' ) );
			add_filter( 'wp_editor_set_quality', array( $this, 'modify_jpg_quality' ) );
			add_filter( 'big_image_size_threshold', array( $this, 'modify_image_size_threshold' ) );

			// remove emojis script
			add_action( 'init', array( $this, 'remove_emojis' ) );

			// disable jQuery migrate
			add_action( 'wp_default_scripts', array( $this, 'disable_jquery_migrate' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'jquery_in_footer' ), PHP_INT_MAX );

			global $porto_settings_optimize;

			if ( ! empty( $porto_settings_optimize['mobile_disable_slider'] ) && wp_is_mobile() ) {
				add_filter( 'body_class', array( $this, 'add_body_class' ) );
			}

			if ( ! empty( $_REQUEST['mobile_url'] ) || ! empty( $_REQUEST['desktop_url'] ) ) {
				return;
			}
			if ( isset( $_REQUEST['action'] ) && ( 'yith-woocompare-view-table' == $_REQUEST['action'] || 'porto_lazyload_menu' == $_REQUEST['action'] ) ) {
				return;
			}
			// Merge css and js => Only Frontend and except elementor preview.
			if ( ( function_exists( 'porto_vc_is_inline' ) && ! porto_vc_is_inline() ) && ( function_exists( 'porto_is_elementor_preview' ) && ! porto_is_elementor_preview() ) && ! is_customize_preview() && ! is_admin() && ! empty( $porto_settings_optimize['merge_stylesheets'] ) ) {

				if ( defined( 'ELEMENTOR_VERSION' ) ) {
					add_action(
						'template_redirect',
						function() {
							add_action( 'wp_head', array( $this, 'elementor_google_fonts' ), 7 );
						},
						11
					);
				}

				add_action( 'wp', array( $this, 'defer' ), 20 );

				global $porto_body_merged_css;
				$porto_body_merged_css = '';
				/**
				 * Filters the included plugins.
				 *
				 * @since 6.3.0
				 */
				$this->including_plugins = apply_filters( 'porto_include_plugins', $this->including_plugins );
				/**
				 * Filters the excluded style.
				 *
				 * @since 6.3.0
				 */
				$this->exclude_style = apply_filters( 'porto_exclude_style', $this->exclude_style );
				/**
				 * Filters the excluded js.
				 *
				 * @since 6.3.0
				 */
				$this->exclude_javascript = apply_filters( 'porto_exclude_javascript', $this->exclude_javascript );

				add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_resources' ), PHP_INT_MAX );
				add_action( 'wp_print_footer_scripts', array( $this, 'dequeue_resources' ), 9 );
				add_action( 'wp_print_footer_scripts', array( $this, 'merge_js_css' ), 9 );
			}
		}

		/**
		 * Add body class for disable mobile slider.
		 *
		 * @since 6.3.0
		 */
		public function add_body_class( $classes ) {
			$classes[] = 'porto-dm-slider'; //disable mobile slider
			return $classes;
		}

		/**
		 * Returns the existing of merged css.
		 *
		 * @return bool
		 * @since 6.3.0
		 */
		public static function has_merged_css() {
			if ( empty( self::$is_merged_style ) ) {
				$merged_css = self::get_uri( 'css', 'path' );
				if ( file_exists( $merged_css ) ) {
					self::$is_merged_style = 'yes';
				} else {
					self::$is_merged_style = 'no';
				}
			}
			return self::$is_merged_style;
		}

		/**
		 * Has critical css or critical preview page?
		 *
		 * @since 6.3.0
		 */
		public function defer() {
			if ( class_exists( 'Porto_Critical' ) && Porto_Critical::get_instance()->is_critical() ) {
				self::$defer_style = true;
				return true;
			}
			self::$defer_style = false;
			return false;
		}

		public function get_css_vars() {
			global $reduxPortoSettings;

			if ( empty( $reduxPortoSettings ) ) {
				require_once( PORTO_ADMIN . '/functions.php' );
				// include redux framework core functions
				require_once( PORTO_ADMIN . '/ReduxCore/framework.php' );
				// porto theme settings options
				require_once( PORTO_ADMIN . '/theme_options/settings.php' );
			}
			ob_start();
			require PORTO_ADMIN . '/theme_options/config_css_vars.php';
			$css      = ob_get_clean();
			// Regular expression
			preg_match_all( '/(--[^:]*):([^;}]*)/', $css, $matches );
			if ( ! empty( $matches ) ) {
				for ( $i = 0; $i < count( $matches[1] ); $i++ ) {
					if ( ! in_array( $matches[1][$i], $this->exclude_vars ) ) {
						$this->css_vars[ $matches[1][$i] ] = $matches[2][$i];
					}
				}
			}
		}

		/**
		 * Combine all javascript files and stylesheets.
		 * This function combines all resources including javascripts and stylesheets.
		 *
		 * @since 6.3.0
		 */
		public function merge_js_css() {
			$merged_css = self::get_uri( 'css', 'path' );
			$merged_js  = self::get_uri( 'js', 'path' );

			if ( self::$defer_style ) {
				if ( ! file_exists( $merged_css ) ) {
					$this->merge_rc( 'css', $this->exclude_style );
				}
				wp_enqueue_style( 'porto-merged' );
			}

			if ( ! file_exists( $merged_js ) ) {
				$this->merge_rc( 'js', $this->exclude_javascript );
			}
			wp_enqueue_script( 'porto-merged' );
		}

		/**
		 * Merge Resources: javascript and stylesheets.
		 *
		 * @param string $rc_type The resource type which you are going to merge.
		 * @since 6.3.0
		 */
		public function merge_rc( $rc_type = 'css', $exclude_rc = array() ) {
			global $wp_styles, $wp_scripts;
			$wp_resources = ( 'css' == $rc_type ? $wp_styles : $wp_scripts );

			// Combine all stylesheets.
			$resources = '';
			foreach ( $this->removed_resources as $index => $file ) {
				if ( $rc_type == $file['type'] ) {
					$contents = '';
					if ( ! empty( $file['before'] ) ) {
						$contents .= $file['before'];
					}
					$contents .= $this->get_file_uri_contents( $file['src'] );
					if ( ! empty( $file['after'] ) ) {
						$contents .= $file['after'];
					}
					if ( 'css' == $rc_type ) {
						if ( 'porto-theme-css' == $index || 'porto-plugins-css' == $index ) {
							$contents = str_replace( 'url(..', 'url(' . get_parent_theme_file_uri(), $contents );
							$contents = str_replace( 'url("..', 'url("' . get_parent_theme_file_uri(), $contents );
						}
						if ( false !== strpos( $file['src'], 'woocommerce' ) && false !== strpos( $file['src'], 'default-skin' ) && defined( 'WC_PLUGIN_FILE' ) ) {
							$contents = str_replace( 'url(', 'url(' . plugins_url( '/', WC_PLUGIN_FILE ) . 'assets/css/photoswipe/default-skin/', $contents );
						}

						if ( false !== strpos( $file['src'], 'contact-form-7' ) && function_exists( 'wpcf7_plugin_url' ) ) {
							$contents = str_replace( '../../assets/ajax-loader.gif', wpcf7_plugin_url( 'assets/ajax-loader.gif' ), $contents );
						}

						if ( false !== strpos( $file['src'], 'revslider' ) && function_exists( 'get_rs_plugin_url' ) ) {
							$contents = str_replace( "url('..", "url('" . get_rs_plugin_url() . 'public/assets', $contents );
							$contents = str_replace( 'url(..', 'url(' . get_rs_plugin_url() . 'public/assets', $contents );
							$contents = str_replace( array( 'url(openhand.cur)' ), 'url(' . get_rs_plugin_url() . 'public/assets/css/openhand.cur)', $contents );
							$contents = str_replace( array( 'url(closedhand.cur)' ), 'url(' . get_rs_plugin_url() . 'public/assets/css/closedhand.cur)', $contents );
						}

						// modern events calendar
						if ( false !== strpos( $file['src'], 'modern-events-calendar' ) && defined( 'MEC_ABSPATH' ) ) {
							$contents = str_replace( 'url(..', 'url(' . plugins_url( '/', MEC_ABSPATH .  MEC_FILENAME ) . 'assets', $contents );
						}
					}
					$resources .= $contents . PHP_EOL;
				}
			}
			if ( 'css' == $rc_type ) {
				if ( empty( $this->css_vars ) ) {
					$this->get_css_vars();
				}
				// Because of --porto-skin-color-inverse and --porto-skin-color
				$var_names = array_map( 'strlen', array_keys( $this->css_vars ) );
				array_multisort( $var_names, SORT_DESC, $this->css_vars );
				foreach ( $this->css_vars as $var => $value ) {
					if ( is_string( $value ) ) {
						$resources = $this->css_var_to_static( $var, $value, $resources );
					}
				}
			}
			if ( 'css' == $rc_type ) {
				global $porto_body_merged_css;
				if ( ! empty( $porto_body_merged_css ) ) {
					$resources .= $porto_body_merged_css . PHP_EOL;
				}
			}

			global $wp_filesystem;
			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			try {
				ob_start();
				print( porto_filter_output( $resources ) );
				$upload_rc_file = self::get_uri( $rc_type, 'path' );
				$upload_path    = dirname( $upload_rc_file );
				if ( ! file_exists( $upload_path ) ) {
					wp_mkdir_p( $upload_path );
				}
				// check file mode and make it writable.
				if ( is_writable( $upload_path ) == false ) {
					@chmod( get_theme_file_path( $upload_rc_file ), 0755 );
				}
				if ( file_exists( $upload_rc_file ) ) {
					if ( is_writable( $upload_rc_file ) == false ) {
						@chmod( $upload_rc_file, 0755 );
					}
					@unlink( $upload_rc_file );
				}
				$wp_filesystem->put_contents( $upload_rc_file, ob_get_clean(), FS_CHMOD_FILE );

			} catch ( Exception $e ) {
				var_dump( $e );
			}
		}

		/**
		 * Dequeue resources which are merged in a file.
		 *
		 * @since 6.3.0
		 */
		public function dequeue_resources() {
			if ( doing_action( 'wp_enqueue_scripts' ) ) {
				wp_register_style( 'porto-merged', self::get_uri( 'css', 'uri' ), array(), PORTO_VERSION );
				wp_register_script( 'porto-merged', self::get_uri( 'js', 'uri' ), array(), PORTO_VERSION );
				if ( empty( self::$defer_style ) ) {
					$merged_css = self::get_uri( 'css', 'path' );
					$this->remove_resources( 'css' );
					if ( ! file_exists( $merged_css ) ) {
						$this->merge_rc( 'css', $this->exclude_style );
					}
					wp_enqueue_style( 'porto-merged' );
				}
			}
			if ( self::$defer_style ) {
				$this->remove_resources( 'css' );
			}
			$this->remove_resources( 'js' );
		}

		/**
		 * Dequeue and deregister scripts
		 *
		 * @param string $rc_type The resource type: css, js
		 * @since 6.3.0
		 */
		public function remove_resources( $rc_type = 'css' ) {
			global $wp_styles, $wp_scripts;
			$wp_resources = ( 'css' == $rc_type ? $wp_styles : $wp_scripts );
			$wp_resources->all_deps( $wp_resources->queue );
			foreach ( $wp_resources->to_do as $enqueued_index => $file ) {
				// Don't use print stylesheets
				if ( 'print' == $wp_resources->registered[ $file ]->args || empty( $wp_resources->registered[ $file ]->src ) ) {
					continue;
				}
				if ( str_replace( $this->including_plugins, '', $file ) != $file && ! in_array( $file, 'css' == $rc_type ? $this->exclude_style : $this->exclude_javascript ) ) {
					$this->removed_resources[ $file . '-' . $rc_type ] = array(
						'src'  => $wp_resources->registered[ $file ]->src,
						'type' => $rc_type,
					);
					$add_inline = array( 'before', 'after' );
					foreach ( $add_inline as $pos ) {
						if ( ! empty( $wp_resources->registered[ $file ]->extra[ $pos ] ) ) {
							$res = &$wp_resources->registered[ $file ]->extra[ $pos ];
							if ( is_array( $res ) ) {
								$res = implode( PHP_EOL, $res );
							}
							$this->removed_resources[ $file . '-' . $rc_type ][ 'data' == $pos ? 'before' : $pos ] = $res;
							$res = '';
						}
					}

					$wp_resources->registered[ $file ]->src            = '';
				}
			}
			$wp_resources->to_do = array();
		}

		/**
		 * Get the url of resources
		 *
		 * @since 6.3.0
		 */
		public static function get_uri( $file_type = 'css', $path = 'uri' ) {

			$blog_id = '';
			if ( is_multisite() ) {
				$current_site = get_blog_details();
				if ( $current_site->blog_id > 1 ) {
					$blog_id = "porto_site-{$current_site->blog_id}";
				}
			}

			$id        = md5( porto_current_page_id() );
			$file_name = "{$id}";
			if ( $blog_id ) {
				$file_name = "{$blog_id}-{$id}";
			}

			$upload_dir = wp_upload_dir();
			if ( is_ssl() ) {
				$upload_dir['baseurl'] = str_replace( 'http://', 'https://', $upload_dir['baseurl'] );
			}
			if ( 'uri' == $path ) {
				return $upload_dir['baseurl'] . '/porto_merged_resources/' . $file_name . '.' . $file_type;
			} else {
				return $upload_dir['basedir'] . '/porto_merged_resources/' . $file_name . '.' . $file_type;
			}
		}

		/**
		 * Replace all css vars to static.
		 *
		 * @param string $var   The name of css var.
		 * @param string $value The Value of css var.
		 * @param string $css   The Page Style.
		 * @return string       The Static Style
		 * @since 6.3.0
		 */
		public function css_var_to_static( $var, $value, $css ) {
			$css = str_replace( "var($var)", $value, $css ); // color: var(--porto-primary-color);

			// Check if we have var(--porto-primary-color,#08c) and replace them accordingly.
			/**
			 * if $css => html {
			 *              color: var(--porto-primary-color,#08c);
			 *            }
			 *            body {
			 *              color: var(--porto-primary-color,#08c);
			 *            }
			 *  The Result is $matches
			 *          array(1) {
			 *              [0]=>
			 *              array(2) {
			 *                  [0]=>
			 *                  string(31) "var(--porto-primary-color,#08c)"
			 *                  [1]=>
			 *                  string(31) "var(--porto-primary-color,#08c)"
			 *              }
			 *          }
			 */
			if ( preg_match_all( "/var\($var.*\)/U", $css, $matches ) ) {
				$matches = array_unique( $matches[0] );
				// foreach var variables.
				foreach ( $matches as $match ) {
					// $match is var(--porto-primary-color,#08c)
					$replacement = $value;
					// like var(--porto-primary-color-op-80, rgba(0,136,20)) because of the regex.
					$match = str_pad( $match, strlen( $match ) + substr_count( $match, '(' ) - substr_count( $match, ')' ), ')' );
					if ( '' === $value ) {
						$default = explode( "var($var,", $match );
						// Remove the last trailing ) that is there because of the regex.
						$default     = substr( $default[1], 0, -1 );
						$replacement = $default;
					}
					$css = str_replace( $match, $replacement, $css );
				}
			}
			return $css;
		}

		/**
		 * Get file data.
		 *
		 * @param string $uri Import demo file path.
		 * @since 6.3.0
		 */
		public function get_file_uri_contents( $uri ) {
			if ( false === strstr( $uri, 'http' ) ) { // no http or https
				$uri = dirname( dirname( get_theme_root_uri() ) ) . $uri;
			}
			// $response = wp_remote_get( str_replace( 'https', 'http', $uri ) );
			$response = wp_remote_get( $uri );
			$data     = '';
			if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
				$data = wp_remote_retrieve_body( $response );
			}
			return $data;
		}
		/**
		 * Remove elementor-icons in <head> tag
		 *
		 * @since 6.3.0
		 */
		public function elementor_google_fonts() {
			global $wp_styles;
			foreach ( $wp_styles->queue as $style ) {
				if ( false !== strpos( $style, 'google-fonts' ) ) {
					$this->defer_elementor_style[ $style ] = $wp_styles->registered[ $style ];
					wp_dequeue_style( $style );
				}
			}
			if ( ! empty( $wp_styles->registered['elementor-icons'] ) ) {
				$this->defer_elementor_style['elementor-icons'] = $wp_styles->registered['elementor-icons'];
				unset( $wp_styles->registered['elementor-icons'] );
				wp_dequeue_style( 'elementor-icons' );
			}

			// modern events calendar
			if ( defined( 'MEC_ABSPATH' ) ) {
				if ( ! empty( $wp_styles->registered['mec-font-icons'] ) ) {
					$this->defer_elementor_style['mec-font-icons'] = $wp_styles->registered['mec-font-icons'];
					unset( $wp_styles->registered['mec-font-icons'] );
					wp_dequeue_style( 'mec-font-icons' );
				}
				if ( ! empty( $wp_styles->registered['mec-google-fonts'] ) ) {
					$this->defer_elementor_style['mec-google-fonts'] = $wp_styles->registered['mec-google-fonts'];
					unset( $wp_styles->registered['mec-google-fonts'] );
					wp_dequeue_style( 'mec-google-fonts' );
				}
			}

			if ( ! empty( count( $this->defer_elementor_style ) ) ) {
				add_action( 'wp_footer', array( $this, 'defer_load_elementor_icons_font' ), 9 );
			}
		}
		/**
		 * Defer load the elementor-icons and google font.
		 *
		 * @since 6.3.0
		 */
		public function defer_load_elementor_icons_font() {
			if ( ! empty( count( $this->defer_elementor_style ) ) ) {
				foreach ( $this->defer_elementor_style as $font => $value ) {
					wp_enqueue_style( $font, $value->src, $value->deps, $value->ver );
				}
			}
		}

		/**
		 * Modify the image quality
		 *
		 * @since 6.2.0
		 */
		public function modify_jpg_quality( $quality ) {
			global $porto_settings_optimize;

			if ( ! empty( $porto_settings_optimize['jpg_quality'] ) ) {
				return (int) $porto_settings_optimize['jpg_quality'];
			}

			return $quality;
		}

		/**
		 * Modify WordPress Max image size
		 *
		 * @since 6.2.0
		 */
		public function modify_image_size_threshold( $threshold ) {
			global $porto_settings_optimize;
			if ( isset( $porto_settings_optimize['max_image_size'] ) && '' !== (string) $porto_settings_optimize['max_image_size'] ) {
				if ( 0 === (int) $porto_settings_optimize['max_image_size'] ) {
					return false;
				}
				return (int) $porto_settings_optimize['max_image_size'];
			}

			return $threshold;
		}

		/**
		 * Removes emojis.
		 *
		 * @since 6.2.0
		 */
		public function remove_emojis() {

			global $porto_settings_optimize;
			if ( empty( $porto_settings_optimize['optimize_emojis'] ) ) {
				return;
			}

			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'tiny_mce_plugins', array( $this, 'remove_emojis_tinymce' ) );
			add_filter( 'wp_resource_hints', array( $this, 'remove_emojis_dns_prefetch' ), 10, 2 );

			if ( '1' === get_option( 'use_smilies' ) ) {
				update_option( 'use_smilies', '0' );
			}
		}

		/**
		 * Disable jQuery Migrate.
		 *
		 * @since 6.2.0
		 */
		public function disable_jquery_migrate( $scripts ) {

			global $porto_settings_optimize;
			if ( empty( $porto_settings_optimize['optimize_migrate'] ) ) {
				return;
			}

			if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
				$script = $scripts->registered['jquery'];

				if ( $script->deps ) {
					$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
				}
			}
		}

		/**
		 * Load jquery in footer
		 *
		 * @since 6.3.0
		 */
		public function jquery_in_footer() {
			global $porto_settings_optimize;
			if ( empty( $porto_settings_optimize['optimize_jquery_footer'] ) ) {
				return;
			}
			// load jquery-core and migrate in footer.
			wp_scripts()->add_data( 'jquery', 'group', 1 );
			wp_scripts()->add_data( 'jquery-core', 'group', 1 );
			wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );
			wp_scripts()->add_data( 'dokan-util-helper', 'group', 1 );
			wp_scripts()->add_data( 'vc_woocommerce-add-to-cart-js', 'group', 1 );
		}

		/**
		 * Remove tinymce emoji plugin
		 *
		 * @since 6.2.0
		 */
		public function remove_emojis_tinymce( $plugins ) {
			if ( is_array( $plugins ) ) {
				return array_diff( $plugins, array( 'wpemoji' ) );
			}

			return array();
		}

		/**
		 * Remove emoji CDN hostname from DNS prefetching hints
		 *
		 * @since 6.2.0
		 */
		public function remove_emojis_dns_prefetch( $urls, $relation_type ) {

			if ( 'dns-prefetch' === $relation_type ) {
				$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/11/svg/' );
				$urls          = array_diff( $urls, array( $emoji_svg_url ) );
			}

			return $urls;
		}
	}

	new Porto_Performance;
endif;
