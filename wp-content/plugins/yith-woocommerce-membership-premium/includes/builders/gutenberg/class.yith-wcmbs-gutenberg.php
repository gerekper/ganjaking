<?php
defined( 'YITH_WCMBS' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMBS_Gutenberg' ) ) {
	/**
	 * Gutenberg class
	 * handle Gutenberg blocks
	 *
	 * @since 1.4.0
	 */
	class YITH_WCMBS_Gutenberg {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS_Gutenberg
		 */
		private static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCMBS_Gutenberg
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCMBS_Gutenberg constructor.
		 */
		private function __construct() {
			global $wp_version;
			add_action( 'init', array( $this, 'init' ) );

			$categories_hook = version_compare( $wp_version, '5.8-beta', '>=' ) ? 'block_categories_all' : 'block_categories';
			add_filter( $categories_hook, array( $this, 'block_category' ), 100, 1 );

			add_filter( 'pre_load_script_translations', array( $this, 'script_translations' ), 10, 4 );
		}

		/**
		 * Init Gutenberg blocks
		 */
		public function init() {
			$asset_file = include( YITH_WCMBS_DIR . 'dist/gutenberg/index.asset.php' );

			wp_register_script(
				'yith-wcmbs-gutenberg-blocks',
				YITH_WCMBS_URL . 'dist/gutenberg/index.js',
				$asset_file['dependencies'],
				$asset_file['version']
			);

			wp_register_style(
				'yith-wcmbs-gutenberg-members-only-content-start-editor',
				YITH_WCMBS_ASSETS_URL . '/css/gutenberg/members-only-content-start-editor.css',
				array(),
				YITH_WCMBS_VERSION
			);

			register_block_type( 'yith/wcmbs-members-only-content-start', array(
				'render_callback' => array( $this, 'render_members_only_content_start' ),
				'editor_script'   => 'yith-wcmbs-gutenberg-blocks',
				'editor_style'    => 'yith-wcmbs-gutenberg-members-only-content-start-editor',
			) );

			wp_set_script_translations( 'yith-wcmbs-gutenberg-blocks', 'yith-woocommerce-membership', YITH_WCMBS_LANGUAGES_PATH );
		}

		/**
		 * Render the "Members-only content start" block
		 * @param $attributes
		 * @param $content
		 *
		 * @return string
		 */
		public function render_members_only_content_start( $attributes, $content ) {
			$defaults   = array(
				'hideAlternativeContent' => false,
			);
			$attributes = wp_parse_args( $attributes, $defaults );
			$tags       = array();

			if ( ! $attributes['hideAlternativeContent'] ) {
				$tags[] = '<!--yith_wcmbs_alternative_content-->';
			}

			$tags[] = '<!--yith_wcmbs_members_only_content_start-->';

			return implode( "\n", $tags );
		}

		/**
		 * Add YITH Category
		 *
		 * @param $categories array Block categories
		 *
		 * @return array Block categories
		 */
		public function block_category( $categories ) {

			$found_key = array_search( 'yith-blocks', array_column( $categories, 'slug' ) );

			if ( ! $found_key ) {
				$categories[] = array(
					'slug'  => 'yith-blocks',
					'title' => _x( 'YITH', '[gutenberg]: Category Name', 'yith-plugin-fw' ),
				);
			}

			return $categories;
		}

		/**
		 * Create the json translation through the PHP file
		 * so it's possible using normal translations (with PO files) also for JS translations
		 *
		 * @param string|null $json_translations
		 * @param string      $file
		 * @param string      $handle
		 * @param string      $domain
		 *
		 * @return string|null
		 */
		public function script_translations( $json_translations, $file, $handle, $domain ) {
			if ( 'yith-woocommerce-membership' === $domain && in_array( $handle, array( 'yith-wcmbs-gutenberg-blocks' ) ) ) {
				$path = YITH_WCMBS_LANGUAGES_PATH . 'yith-woocommerce-membership.php';
				if ( file_exists( $path ) ) {
					$translations = include $path;

					$json_translations = json_encode(
						array(
							'domain'      => 'yith-woocommerce-membership',
							'locale_data' => array(
								'messages' =>
									array(
										'' => array(
											'domain'       => 'yith-woocommerce-membership',
											'lang'         => get_locale(),
											'plural-forms' => 'nplurals=2; plural=(n != 1);',
										),
									)
									+
									$translations,
							),
						)
					);

				}
			}

			return $json_translations;
		}


	}
}