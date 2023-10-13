<?php
/**
 * YITH Gutenberg Class
 * handle Gutenberg blocks and shortcodes.
 *
 * @class   YITH_Gutenberg
 * @package YITH\PluginFramework\Classes
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_Gutenberg' ) ) {
	/**
	 * YITH_Gutenberg class.
	 *
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_Gutenberg {
		/**
		 * The single instance of the class.
		 *
		 * @var YITH_Gutenberg
		 */
		private static $instance;

		/**
		 * Registered blocks
		 *
		 * @var array
		 */
		private $registered_blocks = array();

		/**
		 * The registered blocks.
		 *
		 * @var array
		 */
		private $blocks = array();

		/**
		 * Block category slug
		 *
		 * @var string
		 */
		private $category_slug = 'yith-blocks';

		/**
		 * Singleton implementation.
		 *
		 * @return YITH_Gutenberg
		 */
		public static function instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_Gutenberg constructor.
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'init', array( $this, 'register_blocks' ), 30 );
			add_action( 'init', array( $this, 'handle_iframe_preview' ), 99 );
			add_action( 'wp_ajax_yith_plugin_fw_gutenberg_do_shortcode', array( $this, 'do_shortcode' ) );
			add_action( 'wc_ajax_yith_plugin_fw_gutenberg_do_shortcode', array( $this, 'do_shortcode' ) );
		}

		/**
		 * Initialization
		 */
		public function init() {

		}

		/**
		 * Enqueue scripts for gutenberg
		 */
		public function register_block_editor_assets() {
			$ajax_url   = function_exists( 'WC' ) ? add_query_arg( 'wc-ajax', 'yith_plugin_fw_gutenberg_do_shortcode', trailingslashit( site_url() ) ) : admin_url( 'admin-ajax.php' );
			$gutenberg  = array(
				'ajaxurl'      => $ajax_url,
				'ajaxNonce'    => wp_create_nonce( 'gutenberg-ajax-action' ),
				'siteURL'      => get_site_url(),
				'previewNonce' => wp_create_nonce( 'yith-plugin-fw-block-preview' ),
			);
			$asset_file = include YIT_CORE_PLUGIN_PATH . '/dist/gutenberg/index.asset.php';

			$gutenberg_assets_url = YIT_CORE_PLUGIN_URL . '/dist/gutenberg';

			wp_register_script(
				'yith-gutenberg',
				$gutenberg_assets_url . '/index.js',
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);

			wp_localize_script( 'yith-gutenberg', 'yith_gutenberg_ajax', $gutenberg ); // Deprecated! Kept for backward compatibility.
			wp_localize_script( 'yith-gutenberg', 'yith_gutenberg', $this->blocks ); // Deprecated! Kept for backward compatibility.

			wp_localize_script( 'yith-gutenberg', 'yithGutenberg', $gutenberg );
			wp_localize_script( 'yith-gutenberg', 'yithGutenbergBlocks', $this->blocks );

			wp_register_style( 'yith-gutenberg', $gutenberg_assets_url . '/style-index.css', array( 'yith-plugin-fw-icon-font' ), yith_plugin_fw_get_version() );
		}

		/**
		 * Add new blocks to Gutenberg
		 *
		 * @param array $blocks The blocks to be added.
		 *
		 * @return bool True if the blocks was successfully added, false otherwise.
		 */
		public function add_blocks( $blocks ) {
			$added = false;
			if ( ! empty( $blocks ) && is_array( $blocks ) ) {
				$added        = true;
				$this->blocks = array_merge( $this->blocks, array_map( array( $this, 'parse_block_args' ), $blocks ) );
			}

			return $added;
		}

		/**
		 * Add blocks to gutenberg editor.
		 */
		public function register_blocks() {
			$this->register_block_editor_assets();

			foreach ( $this->blocks as $block => $block_args ) {
				if ( register_block_type( "yith/{$block}", $block_args ) ) {
					$this->registered_blocks[] = $block;
				}
			}

			if ( ! empty( $this->registered_blocks ) ) {
				global $wp_version;

				$categories_hook = version_compare( $wp_version, '5.8-beta', '>=' ) ? 'block_categories_all' : 'block_categories';
				add_filter( $categories_hook, array( $this, 'block_category' ), 10, 1 );
			}
		}

		/**
		 * Add block category
		 *
		 * @param array $categories The block categories.
		 *
		 * @return array The block categories.
		 */
		public function block_category( $categories ) {
			return array_merge(
				$categories,
				array(
					array(
						'slug'  => 'yith-blocks',
						'title' => _x( 'YITH', '[gutenberg]: Category Name', 'yith-plugin-fw' ),
					),
				)
			);
		}

		/**
		 * Retrieve the default category slug
		 *
		 * @return string
		 */
		public function get_default_blocks_category_slug() {
			return $this->category_slug;
		}

		/**
		 * Return an array with the registered blocks
		 *
		 * @return array
		 */
		public function get_registered_blocks() {
			return $this->registered_blocks;
		}

		/**
		 * Return an array with the blocks to register
		 *
		 * @return array
		 */
		public function get_to_register_blocks() {
			return $this->blocks;
		}

		/**
		 * Parse block args.
		 *
		 * @param array $block_args The block args.
		 *
		 * @return array
		 * @since 4.3.0
		 */
		private function parse_block_args( $block_args ) {
			$keywords = array( 'yith' );
			if ( ! empty( $block_args['shortcode_name'] ) ) {
				$keywords[] = $block_args['shortcode_name'];
			}
			$keywords = array_merge( $keywords, $block_args['keywords'] ?? array() );
			if ( count( $keywords ) > 3 ) {
				$keywords = array_slice( $keywords, 0, 3 );
			}
			$block_args['keywords'] = $keywords;

			$block_args['category']     = $block_args['category'] ?? $this->get_default_blocks_category_slug();
			$block_args['do_shortcode'] = ! ! ( $block_args['do_shortcode'] ?? true );

			$block_args['editor_style_handles']  = array_merge( array( 'yith-gutenberg' ), $block_args['editor_style_handles'] ?? array() );
			$block_args['editor_script_handles'] = array_merge( array( 'yith-gutenberg' ), $block_args['editor_script_handles'] ?? array() );

			$block_args['supports'] = wp_parse_args( $block_args['supports'] ?? array(), array( 'customClassName' => false ) );

			if ( isset( $block_args['attributes'] ) ) {
				foreach ( $block_args['attributes'] as $attr_name => $attributes ) {

					if ( ! empty( $attributes['options'] ) && is_array( $attributes['options'] ) ) {
						$options = array();
						foreach ( $attributes['options'] as $v => $l ) {
							// Prepare options array for react component.
							$options[] = array(
								'label' => $l,
								'value' => $v,
							);
						}
						$block_args['attributes'][ $attr_name ]['options'] = $options;
					}

					if ( empty( $attributes['remove_quotes'] ) ) {
						$block_args['attributes'][ $attr_name ]['remove_quotes'] = false;
					}

					// Special Requirements for Block Type.
					if ( ! empty( $attributes['type'] ) ) {
						$block_args['attributes'][ $attr_name ]['controlType'] = $attributes['type'];
						$block_args['attributes'][ $attr_name ]['type']        = 'string';

						switch ( $attributes['type'] ) {
							case 'select':
								// Add default value for multiple.
								if ( ! isset( $attributes['multiple'] ) ) {
									$block_args['attributes'][ $attr_name ]['multiple'] = false;
								}

								if ( ! empty( $attributes['multiple'] ) ) {
									$block_args['attributes'][ $attr_name ]['type'] = 'array';
								}
								break;

							case 'color':
							case 'colorpicker':
								if ( ! isset( $attributes['disableAlpha'] ) ) {
									// Disable alpha gradient for color picker.
									$block_args['attributes'][ $attr_name ]['disableAlpha'] = true;
								}
								break;

							case 'number':
								$block_args['attributes'][ $attr_name ]['type'] = 'integer';
								break;

							case 'toggle':
							case 'checkbox':
								$block_args['attributes'][ $attr_name ]['type'] = 'boolean';
								break;
						}
					}
				}
			}

			return $block_args;
		}

		/**
		 * Get a do_shortcode in ajax call to show block preview
		 **/
		public function do_shortcode() {
			check_ajax_referer( 'gutenberg-ajax-action', 'security' );

			$post_id    = absint( $_REQUEST['context']['postId'] ?? 0 );
			$admin_page = sanitize_text_field( wp_unslash( $_REQUEST['context']['adminPage'] ?? '' ) );
			$page_now   = sanitize_text_field( wp_unslash( $_REQUEST['context']['pageNow'] ?? '' ) );
			$has_access = ( in_array( $admin_page, array( 'widgets-php', 'site-editor-php' ), true ) && current_user_can( 'edit_theme_options' ) );
			$has_access = $has_access || ( in_array( $page_now, array( 'customize', 'widgets', 'site-editor' ), true ) && current_user_can( 'edit_theme_options' ) );
			$has_access = $has_access || $post_id && current_user_can( 'edit_post', $post_id );

			if ( $has_access ) {
				$current_action = current_action();
				$shortcode      = ! empty( $_REQUEST['shortcode'] ) ? wp_unslash( $_REQUEST['shortcode'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				if ( ! apply_filters( 'yith_plugin_fw_gutenberg_skip_shortcode_sanitize', false ) ) {
					$shortcode = sanitize_text_field( stripslashes( $shortcode ) );
				}

				ob_start();

				do_action( 'yith_plugin_fw_gutenberg_before_do_shortcode', $shortcode, $current_action );
				echo do_shortcode( apply_filters( 'yith_plugin_fw_gutenberg_shortcode', $shortcode, $current_action ) );
				do_action( 'yith_plugin_fw_gutenberg_after_do_shortcode', $shortcode, $current_action );

				$html = ob_get_clean();

				wp_send_json(
					array(
						'html' => $html,
					)
				);
			}
		}

		/**
		 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
		 * Non-scalar values are ignored.
		 *
		 * @param string|array $var Data to sanitize.
		 *
		 * @return string|array
		 * @since 4.3.0
		 */
		private function clean( $var ) {
			if ( is_array( $var ) ) {
				return array_map( array( $this, 'clean' ), $var );
			} else {
				return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
			}
		}

		/**
		 * Handle preview through iFrame to load theme scripts and styles.
		 *
		 * @since 4.3.0
		 */
		public function handle_iframe_preview() {
			if ( empty( $_GET['yith-plugin-fw-block-preview'] ) ) {
				return;
			}
			$block = sanitize_text_field( wp_unslash( $_GET['block'] ?? '' ) );
			check_admin_referer( 'yith-plugin-fw-block-preview', 'yith-plugin-fw-block-preview-nonce' );

			$attributes = $this->clean( wp_unslash( $_GET['attributes'] ?? array() ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$context    = $this->clean( wp_unslash( $_GET['context'] ?? array() ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( ! current_user_can( 'edit_posts' ) ) {
				return;
			}
			$post_id = $context['postId'] ?? false;
			if ( $post_id ) {
				global $post;
				$post = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}

			$parsed_block = array(
				'blockName' => "yith/{$block}",
				'attrs'     => $attributes,
			);

			define( 'IFRAME_REQUEST', true );
			if ( ! defined( 'YITH_PLUGIN_FW_BLOCK_PREVIEW' ) ) {
				define( 'YITH_PLUGIN_FW_BLOCK_PREVIEW', true );
			}

			do_action( 'wp_loaded' ); // Trigger wp_loaded to allow loading font-families and styles from theme.json.

			// phpcs:disable Generic.WhiteSpace.DisallowSpaceIndent.SpacesUsed
			?>
			<!doctype html>
			<html <?php language_attributes(); ?>>
			<head>
				<meta charset="<?php bloginfo( 'charset' ); ?>"/>
				<meta name="viewport" content="width=device-width, initial-scale=1"/>
				<link rel="profile" href="https://gmpg.org/xfn/11"/>
				<?php wp_head(); ?>
				<style>
                    html, body, #page, #content {
                        padding    : 0 !important;
                        margin     : 0 !important;
                        min-height : 0 !important;
                    }

                    #hidden-footer {
                        display : none !important;
                    }
				</style>
			</head>
			<body <?php body_class(); ?>>
			<div id="page" class="site">
				<div id="content" class="site-content">
					<?php echo render_block( $parsed_block );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div><!-- #content -->
			</div><!-- #page -->
			<div id="hidden-footer">
				<?php
				// The footer is wrapped in a hidden element to prevent issues if any plugin prints something there.
				wp_footer();
				?>
			</div>
			</body>
			</html>
			<?php
			// phpcs:enable

			exit;
		}

		/** ---------------------------------------------
		 *  Deprecated!
		 * ----------------------------------------------
		 */

		/**
		 * Return an array with the block(s) arguments
		 *
		 * @param string $block_key The block key.
		 *
		 * @return array|false
		 * @deprecated  4.3.0
		 */
		public function get_block_args( $block_key = 'all' ) {
			if ( 'all' === $block_key ) {
				return $this->blocks;
			} elseif ( isset( $this->blocks[ $block_key ] ) ) {
				return $this->blocks[ $block_key ];
			}

			return false;
		}

		/**
		 * Set the block arguments
		 *
		 * @param array $args The block arguments.
		 *
		 * @deprecated  4.3.0
		 */
		public function set_block_args( $args ) {
			// Do nothing.
		}
	}
}

if ( ! function_exists( 'YITH_Gutenberg' ) ) {
	/**
	 * Single instance of YITH_Gutenberg
	 *
	 * @return YITH_Gutenberg
	 */
	function YITH_Gutenberg() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		return YITH_Gutenberg::instance();
	}
}

YITH_Gutenberg();
