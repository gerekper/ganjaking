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
	 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
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
		 * Blocks to register
		 *
		 * @var array
		 */
		private $to_register_blocks = array();

		/**
		 * Blocks args
		 *
		 * @var array
		 */
		private $blocks_args = array();

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
			add_action( 'init', array( $this, 'register_blocks' ), 30 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
			add_action( 'wp_ajax_yith_plugin_fw_gutenberg_do_shortcode', array( $this, 'do_shortcode' ) );
			add_action( 'wc_ajax_yith_plugin_fw_gutenberg_do_shortcode', array( $this, 'do_shortcode' ) );
		}

		/**
		 * Enqueue scripts for gutenberg
		 */
		public function enqueue_block_editor_assets() {
			$ajax_url = function_exists( 'WC' ) ? add_query_arg( 'wc-ajax', 'yith_plugin_fw_gutenberg_do_shortcode', trailingslashit( site_url() ) ) : admin_url( 'admin-ajax.php' );
			$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$deps     = apply_filters(
				'yith_plugin_fw_gutenberg_script_deps',
				array(
					'wp-blocks',
					'wp-element',
					'yith-js-md5',
				)
			);
			wp_register_script( 'yith-js-md5', YIT_CORE_PLUGIN_URL . '/assets/js/javascript-md5/md5.min.js', array(), '2.10.0', true );
			wp_enqueue_script( 'yith-gutenberg', YIT_CORE_PLUGIN_URL . '/assets/js/yith-gutenberg' . $suffix . '.js', $deps, yith_plugin_fw_get_version(), true );
			wp_localize_script( 'yith-gutenberg', 'yith_gutenberg', $this->blocks_args );
			wp_localize_script( 'yith-gutenberg', 'yith_gutenberg_ajax', array( 'ajaxurl' => $ajax_url ) );
		}

		/**
		 * Add blocks to gutenberg editor.
		 */
		public function register_blocks() {
			$block_args = array();
			foreach ( $this->to_register_blocks as $block => $args ) {
				if ( isset( $args['style'] ) ) {
					$block_args['style'] = $args['style'];
				}

				if ( isset( $args['script'] ) ) {
					$block_args['script'] = $args['script'];
				}

				if ( register_block_type( "yith/{$block}", $block_args ) ) {
					$this->registered_blocks[] = $block;
				}
			}

			if ( ! empty( $this->registered_blocks ) ) {
				add_filter( 'block_categories', array( $this, 'block_category' ), 10, 2 );
			}
		}

		/**
		 * Add block category
		 *
		 * @param array   $categories The block categories.
		 * @param WP_Post $post       The current post.
		 *
		 * @return array The block categories.
		 */
		public function block_category( $categories, $post ) {
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
		 * Add new blocks to Gutenberg
		 *
		 * @param string|array $blocks The blocks to be added.
		 *
		 * @return bool True if the blocks was successfully added, false otherwise.
		 */
		public function add_blocks( $blocks ) {
			$added = false;
			if ( ! empty( $blocks ) ) {
				$added = true;
				if ( is_array( $blocks ) ) {
					$this->to_register_blocks = array_merge( $this->to_register_blocks, $blocks );
				} else {
					$this->to_register_blocks[] = $blocks;
				}
			}

			return $added;
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
			return $this->to_register_blocks;
		}

		/**
		 * Return an array with the block(s) arguments
		 *
		 * @param string $block_key The block key.
		 *
		 * @return array|false
		 */
		public function get_block_args( $block_key = 'all' ) {
			if ( 'all' === $block_key ) {
				return $this->blocks_args;
			} elseif ( isset( $this->blocks_args[ $block_key ] ) ) {
				return $this->blocks_args[ $block_key ];
			}

			return false;
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
		 * Set the block arguments
		 *
		 * @param array $args The block arguments.
		 */
		public function set_block_args( $args ) {
			foreach ( $args as $block => $block_args ) {

				// Add Default Keywords.
				$default_keywords = array( 'yith' );
				if ( ! empty( $block_args['shortcode_name'] ) ) {
					$default_keywords[] = $block_args['shortcode_name'];
				}

				$args[ $block ]['keywords'] = ! empty( $args[ $block ]['keywords'] ) ? array_merge( $args[ $block ]['keywords'], $default_keywords ) : $default_keywords;

				if ( count( $args[ $block ]['keywords'] ) > 3 ) {
					$args[ $block ]['keywords'] = array_slice( $args[ $block ]['keywords'], 0, 3 );
				}

				if ( empty( $block_args['category'] ) ) {
					// Add the YITH block category.
					$args[ $block ]['category'] = $this->get_default_blocks_category_slug();
				}

				if ( isset( $block_args['attributes'] ) ) {
					foreach ( $block_args['attributes'] as $attr_name => $attributes ) {
						// Set the do_shortcode args.
						if ( ! empty( $attributes['do_shortcode'] ) ) {
							$args[ $block ]['attributes'][ $attr_name ] = true;
						}

						if ( ! empty( $attributes['options'] ) && is_array( $attributes['options'] ) ) {
							$options = array();
							foreach ( $attributes['options'] as $v => $l ) {
								// Prepare options array for react component.
								$options[] = array(
									'label' => $l,
									'value' => $v,
								);
							}
							$args[ $block ]['attributes'][ $attr_name ]['options'] = $options;
						}

						if ( empty( $attributes['remove_quotes'] ) ) {
							$args[ $block ]['attributes'][ $attr_name ]['remove_quotes'] = false;
						}

						// Special Requirements for Block Type.
						if ( ! empty( $attributes['type'] ) ) {
							$args[ $block ]['attributes'][ $attr_name ]['blocktype'] = $attributes['type'];
							$args[ $block ]['attributes'][ $attr_name ]['type']      = 'string';

							switch ( $attributes['type'] ) {
								case 'select':
									// Add default value for multiple.
									if ( ! isset( $attributes['multiple'] ) ) {
										$args[ $block ]['attributes'][ $attr_name ]['multiple'] = false;
									}

									if ( ! empty( $attributes['multiple'] ) ) {
										$args[ $block ]['attributes'][ $attr_name ]['type'] = 'array';
									}
									break;

								case 'color':
								case 'colorpicker':
									if ( ! isset( $attributes['disableAlpha'] ) ) {
										// Disable alpha gradient for color picker.
										$args[ $block ]['attributes'][ $attr_name ]['disableAlpha'] = true;
									}
									break;

								case 'number':
									$args[ $block ]['attributes'][ $attr_name ]['type'] = 'integer';
									break;

								case 'toggle':
								case 'checkbox':
									$args[ $block ]['attributes'][ $attr_name ]['type'] = 'boolean';
									break;
							}
						}
					}
				}
			}

			$this->blocks_args = array_merge( $this->blocks_args, $args );
		}

		/**
		 * Get a do_shortcode in ajax call to show block preview
		 **/
		public function do_shortcode() {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			$current_action = current_action();
			$shortcode      = ! empty( $_POST['shortcode'] ) ? wp_unslash( $_POST['shortcode'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( ! apply_filters( 'yith_plugin_fw_gutenberg_skip_shortcode_sanitize', false ) ) {
				$shortcode = sanitize_text_field( stripslashes( $shortcode ) );
			}

			do_action( 'yith_plugin_fw_gutenberg_before_do_shortcode', $shortcode, $current_action );
			echo do_shortcode( apply_filters( 'yith_plugin_fw_gutenberg_shortcode', $shortcode, $current_action ) );
			do_action( 'yith_plugin_fw_gutenberg_after_do_shortcode', $shortcode, $current_action );

			if ( is_ajax() ) {
				die();
			}

			// phpcs:enable
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
