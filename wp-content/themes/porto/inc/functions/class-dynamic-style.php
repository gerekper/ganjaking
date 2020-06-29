<?php
/**
 * Porto Dynamic Style
 *
 * @author     Porto Themes
 * @category   Style Functions
 * @since      4.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Porto_Dynamic_Style' ) ) :
	class Porto_Dynamic_Style {

		protected $mode = null;

		protected $js_composer_internal_styles = '';

		public function __construct() {
			add_action( 'wp', array( $this, 'init' ) );

			add_action( 'porto_admin_save_theme_settings', array( $this, 'compile_dynamic_css' ) );
			if ( is_admin() ) {
				add_action( 'customize_save_after', array( $this, 'compile_dynamic_css' ), 99 );
			}
		}

		public function init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'init_vc_custom_styles' ), 8 );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ), 990 );
			if ( 'internal' == $this->get_mode() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'output_dynamic_styles' ), 1002 );
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'output_internal_styles' ), 1005 );
			add_action( 'wp_head', array( $this, 'output_internal_js' ), 153 );
			add_action( 'wp_footer', array( $this, 'output_custom_js_body' ) );
		}

		public function get_mode() {
			if ( null != $this->mode ) {
				return $this->mode;
			}
			$upload_dir = wp_upload_dir();
			$css_file   = $upload_dir['basedir'] . '/porto_styles/dynamic_style.css';
			if ( ! get_option( 'porto_dynamic_style', false ) || is_customize_preview() || ! file_exists( $css_file ) ) {
				$this->mode = 'internal';
			} else {
				$this->mode = 'file';
			}
			return $this->mode;
		}

		/**
		 * compile dynamic css when saving theme options
		 */
		public function compile_dynamic_css() {
			// filesystem
			global $wp_filesystem;
			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$upload_dir = wp_upload_dir();
			$style_path = $upload_dir['basedir'] . '/porto_styles';
			// Compile dynamic styles
			$rtl_arr                               = array( '', '_rtl' );
			$GLOBALS['porto_save_settings_is_rtl'] = false;
			try {
				if ( ! file_exists( $style_path ) ) {
					wp_mkdir_p( $style_path );
				}
				$result = true;

				foreach ( $rtl_arr as $rtl_arr_value ) {
					ob_start();
					include PORTO_DIR . '/style.php';
					$css = ob_get_clean();

					$filename = $style_path . '/dynamic_style' . $rtl_arr_value . '.css';
					porto_check_file_write_permission( $filename );
					$result = $wp_filesystem->put_contents( $filename, $this->minify_css( $css ), FS_CHMOD_FILE );
					if ( $result ) {
						$result = true;
					} else {
						$result = false;
					}
					$GLOBALS['porto_save_settings_is_rtl'] = true;
				}

				// compile gutenberg editor style
				ob_start();
				include PORTO_DIR . '/style-editor.php';
				$css      = ob_get_clean();
				$filename = $style_path . '/style-editor.css';
				porto_check_file_write_permission( $filename );
				$result1 = $wp_filesystem->put_contents( $filename, $this->minify_css( $css ), FS_CHMOD_FILE );
				if ( $result1 && $result ) {
					$result = true;
				} else {
					$result = false;
				}

				update_option( 'porto_dynamic_style', $result );
			} catch ( Exception $e ) {
				update_option( 'porto_dynamic_style', false );
				// try to recompile dynamic style in every 4 days if compilation is failed
				set_transient( 'porto_dynamic_style_time', time(), DAY_IN_SECONDS * 4 );
			}
			unset( $GLOBALS['porto_save_settings_is_rtl'] );
		}

		public function output_dynamic_styles( $output = false ) {

			ob_start();
			require_once( PORTO_DIR . '/style.php' );
			if ( is_customize_preview() ) {
				if ( $output ) {
					$this->init_vc_custom_styles();
					if ( $this->js_composer_internal_styles ) {
						echo porto_filter_output( $this->js_composer_internal_styles );
					}
				}
				require_once( PORTO_DIR . '/style-internal.php' );
			}
			$css = ob_get_clean();
			if ( $output ) {
				return $this->minify_css( $css );
			} else {
				wp_add_inline_style( 'porto-style', apply_filters( 'porto_dynamic_style_internal_output', $this->minify_css( $css ) ) );
			}
		}

		public function output_internal_styles() {
			if ( $this->js_composer_internal_styles ) {
				wp_add_inline_style( 'porto-style', $this->js_composer_internal_styles );
			}
			if ( ! is_customize_preview() ) {
				ob_start();
				require_once( PORTO_DIR . '/style-internal.php' );
				do_action( 'porto_head_css' );
				$css = ob_get_clean();
				if ( $css ) {
					wp_add_inline_style( 'porto-style', $this->minify_css( $css ) );
				}
			}
		}

		public function output_internal_js() {
			global $porto_settings;
			if ( isset( $porto_settings['js-code-head'] ) && trim( $porto_settings['js-code-head'] ) ) { ?>
				<script>
					<?php echo trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $porto_settings['js-code-head'] ) ); ?>
				</script>
				<?php
			}
			$custom_js_head = porto_get_meta_value( 'custom_js_head' );
			if ( isset( $custom_js_head ) && trim( $custom_js_head ) ) {
				?>
				<script>
					<?php echo trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $custom_js_head ) ); ?>
				</script>
				<?php
			}
		}

		public function output_custom_js_body() {
			$custom_js_body = porto_get_meta_value( 'custom_js_body' );
			if ( ! empty( $custom_js_body ) ) {
				?>
				<script>
					<?php echo trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $custom_js_body ) ); ?>
				</script>
				<?php
			}
		}

		public function init_vc_custom_styles() {
			if ( ! defined( 'WPB_VC_VERSION' ) ) {
				return;
			}

			remove_action( 'wp_head', array( vc_manager()->vc(), 'addFrontCss' ), 1000 );
			remove_action( 'wp_enqueue_scripts', array( vc_manager()->vc(), 'addFrontCss' ) );
			ob_start();
			vc_manager()->vc()->addFrontCss();
			$css = ob_get_clean();
			$css = porto_strip_tags( $css );
			if ( $css ) {
				global $porto_settings_optimize;
				if ( is_singular() && isset( $porto_settings_optimize['lazyload'] ) && $porto_settings_optimize['lazyload'] && ! vc_is_inline() ) {
					global $post;
					preg_match_all( '/\.vc_custom_([^{]*)[^}]*((background-image):[^}]*|(background):[^}]*url\([^}]*)}/', $css, $matches );
					if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {
						foreach ( $matches[0] as $key => $value ) {
							if ( ! isset( $matches[1][ $key ] ) || empty( $matches[1][ $key ] ) ) {
								continue;
							}
							if ( preg_match( '/\[(porto_interactive_banner|vc_row|vc_column|vc_row_inner|vc_column_inner)\s[^]]*.vc_custom_' . trim( $matches[1][ $key ] ) . '[^]]*\]/', $post->post_content ) ) {
								if ( ! empty( $matches[3][ $key ] ) ) {
									$css = preg_replace( '/\.vc_custom_' . $matches[1][ $key ] . '([^}]*)(background-image:[^;]*;)/', '.vc_custom_' . $matches[1][ $key ] . '$1', $css );
								} else {
									$css = preg_replace( '/\.vc_custom_' . $matches[1][ $key ] . '([^}]*)(background)(:\s#[A-Fa-f0-9]{3,6}\s)(url\([^)]*\))\s(!important;)/', '.vc_custom_' . $matches[1][ $key ] . '$1background-color$3$5', $css );
								}
							}
						}
					}
				}
				$this->js_composer_internal_styles = $css;
			}
		}

		public function enqueue_style() {

			if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
				wp_add_inline_style( 'porto_admin_bar', '.vc_vc_column, .vc_vc_column_inner { width: 100%; }' );
			}

			global $porto_settings_optimize;

			// load visual composer styles
			if ( defined( 'WPB_VC_VERSION' ) && isset( $porto_settings_optimize['shortcodes_to_remove'] ) && ! empty( $porto_settings_optimize['shortcodes_to_remove'] ) ) {
				$upload_dir = wp_upload_dir();
				$css_file   = $upload_dir['basedir'] . '/porto_styles/js_composer.css';
				if ( file_exists( $css_file ) ) {
					$inline_styles = wp_styles()->get_data( 'js_composer_front', 'after' );
					wp_deregister_style( 'js_composer_front' );
					wp_dequeue_style( 'js_composer_front' );
					porto_register_style( 'js_composer_front', 'js_composer', false, false );
					if ( ! empty( $inline_styles ) ) {
						$inline_styles                     = implode( "\n", $inline_styles );
						$this->js_composer_internal_styles = $inline_styles;
						//wp_add_inline_style( 'js_composer_front', $inline_styles );
					}
				}
			}

			// bootstrap css
			$bootstrap_included = false;
			if ( is_customize_preview() ) {
				if ( isset( $_POST['wp_customize'] ) && 'on' == $_POST['wp_customize'] && ! empty( $_POST['customized'] ) ) {
					$bootstrap_options = array( 'css-type', 'container-width', 'grid-gutter-width', 'skin-color', 'secondary-color', 'color-dark', 'border-radius', 'thumb-padding' );
					$need_compile      = false;
					foreach ( $bootstrap_options as $o ) {
						if ( false !== strpos( $_POST['customized'], 'porto_settings[' . $o . ']' ) ) {
							$need_compile = true;
							break;
						}
					}
					if ( $need_compile ) {
						// config file
						ob_start();
						require PORTO_ADMIN . '/theme_options/config_scss_bootstrap.php';
						$_config_css = ob_get_clean();

						if ( ! class_exists( 'scssc' ) ) {
							require_once( PORTO_ADMIN . '/scssphp/scss.inc.php' );
						}
						$scss = new scssc();
						$scss->setImportPaths( PORTO_DIR . '/scss' );
						$scss->setFormatter( 'scss_formatter_crunched' );

						try {
							// bootstrap styles
							$optimize_suffix = '';
							if ( isset( $porto_settings_optimize['optimize_bootstrap'] ) && $porto_settings_optimize['optimize_bootstrap'] ) {
								$optimize_suffix = '.optimized';
							}
							if ( is_rtl() ) {
								$rtl_prefix = '$rtl: 1; $dir: rtl !default;';
							} else {
								$rtl_prefix = '$rtl: 0; $dir: ltr !default;';
							}
							$css = $scss->compile( $rtl_prefix . '@import "plugins/directional"; ' . $_config_css . ' @import "plugins/bootstrap/bootstrap' . $optimize_suffix . '";' );

							set_transient( 'porto_bootstrap_css_temp', $css, HOUR_IN_SECONDS * 24 );
							if ( wp_style_is( 'js_composer_front', 'registered' ) ) {
								wp_add_inline_style( 'js_composer_front', $css );
							} else {
								wp_add_inline_style( 'wp-block-library', $css );
							}
							$bootstrap_included = true;
						} catch ( Exception $e ) {
						}
					}
				} elseif ( $css = get_transient( 'porto_bootstrap_css_temp' ) ) {
					if ( wp_style_is( 'js_composer_front', 'registered' ) ) {
						wp_add_inline_style( 'js_composer_front', $css );
					} else {
						wp_add_inline_style( 'wp-block-library', $css );
					}
					$bootstrap_included = true;
				}
			}

			if ( ! $bootstrap_included ) {
				wp_deregister_style( 'bootstrap' );
				if ( is_rtl() ) {
					porto_register_style( 'bootstrap', 'bootstrap_rtl', false, true );
				} else {
					porto_register_style( 'bootstrap', 'bootstrap', false, true );
				}
			}

			// dynamic styles
			if ( 'file' == $this->get_mode() ) {
				wp_deregister_style( 'porto-dynamic-style' );
				if ( is_rtl() ) {
					porto_register_style( 'porto-dynamic-style', 'dynamic_style_rtl', false, false );
				} else {
					porto_register_style( 'porto-dynamic-style', 'dynamic_style', false, false );
				}
			}
		}

		protected function minify_css( $css ) {
			if ( ! $css ) {
				return '';
			}
			$output = preg_replace( '#/\*.*?\*/#s', '', $css );
			$output = preg_replace( '/\s*([{}|:;,])\s+/', '$1', $output );
			$output = preg_replace( '/\s\s+(.*)/', '$1', $output );
			$output = preg_replace( '/;(?=\s*})/', '', $output );
			$output = preg_replace( '/ (,|;|\{|})/', '$1', $output );
			$output = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $output );
			$output = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $output );
			return trim( $output );
		}

	}
	if ( is_customize_preview() ) {
		$GLOBALS['porto_dynamic_style'] = new Porto_Dynamic_Style();
	} else {
		new Porto_Dynamic_Style();
	}

endif;
