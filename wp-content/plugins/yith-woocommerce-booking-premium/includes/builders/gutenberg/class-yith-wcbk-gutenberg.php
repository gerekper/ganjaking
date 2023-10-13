<?php
/**
 * Handle Gutenberg blocks.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Gutenberg' ) ) {
	/**
	 * Gutenberg class
	 *
	 * @since 3.0.0
	 */
	class YITH_WCBK_Gutenberg {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Blocks
		 *
		 * @var array
		 */
		private $blocks = array();

		/**
		 * YITH_WCBK_Gutenberg constructor.
		 */
		private function __construct() {
			global $wp_version;

			$this->load();
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'init', array( $this, 'handle_iframe_preview' ), 99 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );

			$categories_hook = version_compare( $wp_version, '5.8', '>=' ) ? 'block_categories_all' : 'block_categories';
			add_filter( $categories_hook, array( $this, 'block_category' ), 100, 2 );

			add_filter( 'pre_load_script_translations', array( $this, 'script_translations' ), 10, 4 );
		}

		/**
		 * Load classes.
		 */
		private function load() {
			require_once YITH_WCBK_INCLUDES_PATH . '/builders/gutenberg/render/abstract-yith-wcbk-render-block.php';
			require_once YITH_WCBK_INCLUDES_PATH . '/builders/gutenberg/render/abstract-yith-wcbk-render-block-with-style.php';
			require_once YITH_WCBK_INCLUDES_PATH . '/builders/gutenberg/render/class-yith-wcbk-booking-form-block.php';
			require_once YITH_WCBK_INCLUDES_PATH . '/builders/gutenberg/render/class-yith-wcbk-booking-products-block.php';
			require_once YITH_WCBK_INCLUDES_PATH . '/builders/gutenberg/render/class-yith-wcbk-bookable-product-form-block.php';
			require_once YITH_WCBK_INCLUDES_PATH . '/builders/gutenberg/render/class-yith-wcbk-search-form-block.php';
		}

		/**
		 * Enqueue assets in block editor.
		 */
		public function enqueue_block_editor_assets() {
			wp_enqueue_style( 'yith-plugin-fw-icon-font' );
		}

		/**
		 * Init Gutenberg blocks
		 */
		public function init() {
			$asset_file = include YITH_WCBK_DIR . 'dist/gutenberg/index.asset.php';
			$is_premium = defined( 'YITH_WCBK_PREMIUM' ) && YITH_WCBK_PREMIUM;

			wp_register_script(
				'yith-wcbk-gutenberg-blocks',
				YITH_WCBK_URL . 'dist/gutenberg/index.js',
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);

			$products_per_row = absint( wc_get_default_products_per_row() );
			$style_attributes = array(
				'textColor'       => array(
					'type'    => 'string',
					'default' => '#000000',
				),
				'backgroundColor' => array(
					'type'    => 'string',
					'default' => '#ffffff',
				),
				'padding'         => array(
					'type'    => 'number',
					'default' => 20,
				),
				'border'          => array(
					'type'    => 'object',
					'default' => array(
						'color' => '#cccccc',
						'style' => 'solid',
						'width' => 0,
					),
				),
				'borderRadius'    => array(
					'type'    => 'number',
					'default' => 0,
				),
			);

			wp_register_style(
				'yith-wcbk-block-components',
				YITH_WCBK_URL . 'dist/gutenberg/style-index.css',
				array(),
				YITH_WCBK_VERSION
			);

			if ( $is_premium ) {
				register_block_type(
					'yith/wcbk-booking-products',
					array(
						'render_callback' => array( $this, 'render_booking_products' ),
						'editor_script'   => 'yith-wcbk-gutenberg-blocks',
						'editor_style'    => 'yith-wcbk-block-components',
						'attributes'      => array(
							'type'        => array(
								'type'    => 'string',
								'default' => 'newest',
							),
							'columns'     => array(
								'type'    => 'number',
								'default' => $products_per_row,
							),
							'rows'        => array(
								'type'    => 'number',
								'default' => 1,
							),
							'product_ids' => array(
								'type'    => 'array',
								'default' => array(),
							),
							'categories'  => array(
								'type'    => 'array',
								'default' => array(),
							),
						),
					)
				);
			}

			register_block_type(
				'yith/wcbk-booking-form',
				array(
					'render_callback' => array( $this, 'render_booking_form' ),
					'editor_script'   => 'yith-wcbk-gutenberg-blocks',
					'editor_style'    => 'yith-wcbk-block-components',
					'attributes'      => array_merge(
						$style_attributes,
						array(
							'productId'          => array(
								'type'    => 'number',
								'default' => 0,
							),
							'showTitle'          => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'titleTag'           => array(
								'type'    => 'string',
								'default' => 'h2',
							),
							'showReviews'        => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'showMeta'           => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'useFullWidthButton' => array(
								'type'    => 'boolean',
								'default' => true,
							),
						)
					),
				)
			);

			register_block_type(
				'yith/wcbk-bookable-product-form',
				array(
					'render_callback' => array( $this, 'render_bookable_product_form' ),
					'editor_script'   => 'yith-wcbk-gutenberg-blocks',
					'editor_style'    => 'yith-wcbk-block-components',
					'attributes'      => array_merge(
						$style_attributes,
						array(
							'show_reviews'          => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'use_full_width_button' => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'is_fixed_on_mobile'    => array(
								'type'    => 'boolean',
								'default' => true,
							),
						)
					),
				)
			);

			if ( yith_wcbk_is_search_forms_module_active() ) {
				register_block_type(
					'yith/wcbk-search-form',
					array(
						'render_callback' => array( $this, 'render_search_form' ),
						'editor_script'   => 'yith-wcbk-gutenberg-blocks',
						'editor_style'    => 'yith-wcbk-block-components',
						'attributes'      => array(
							'searchFormId' => array(
								'type'    => 'number',
								'default' => 0,
							),
						),
					)
				);
			}

			$category_count = wp_count_terms( array( 'taxonomy' => 'product_cat' ) );
			$category_count = ! is_wp_error( $category_count ) ? absint( $category_count ) : 0;

			wp_localize_script(
				'yith-wcbk-gutenberg-blocks',
				'bkBlocks',
				array(
					'siteURL'                  => get_site_url(),
					'defaultProductsPerRow'    => $products_per_row,
					'productCount'             => array_sum( (array) wp_count_posts( 'product' ) ),
					'categoryCount'            => $category_count,
					'previewNonce'             => wp_create_nonce( 'yith-wcbk-block-preview' ),
					'isPremium'                => $is_premium,
					'isSearchFormModuleActive' => yith_wcbk_is_search_forms_module_active(),
					'postTypes'                => array(
						'searchForm' => YITH_WCBK_Post_Types::SEARCH_FORM,
					),
					'postTypeCounts'           => array(
						'searchForm' => array_sum( (array) wp_count_posts( YITH_WCBK_Post_Types::SEARCH_FORM ) ),
					),
					'postTypeRestBases'        => array(
						'searchForm' => 'booking-search-forms',
					),
				)
			);

			wp_set_script_translations( 'yith-wcbk-gutenberg-blocks', 'yith-booking-for-woocommerce', YITH_WCBK_LANGUAGES_PATH );
		}

		/**
		 * Render the booking products block.
		 *
		 * @param array $attributes Attributes.
		 *
		 * @return string
		 */
		public function render_booking_products( $attributes ) {
			$block_renderer = new YITH_WCBK_Booking_Products_Block( $attributes );
			$block_renderer->set_allow_blank_state( false );
			ob_start();

			$block_renderer->render();

			return ob_get_clean();
		}

		/**
		 * Render the booking form.
		 *
		 * @param array $attributes Attributes.
		 *
		 * @return string
		 * @since 3.1.0
		 */
		public function render_booking_form( $attributes ) {
			$block_renderer = new YITH_WCBK_Booking_Form_Block( $attributes );
			$block_renderer->set_allow_blank_state( false );
			ob_start();

			$block_renderer->render();

			return ob_get_clean();
		}

		/**
		 * Render the booking form.
		 *
		 * @param array $attributes Attributes.
		 *
		 * @return string
		 * @since 3.1.0
		 */
		public function render_bookable_product_form( $attributes ) {
			$block_renderer = new YITH_WCBK_Bookable_Product_Form_Block( $attributes );
			$block_renderer->set_allow_blank_state( false );
			ob_start();

			$block_renderer->render();

			return ob_get_clean();
		}

		/**
		 * Render the booking products block.
		 *
		 * @param array $attributes Attributes.
		 *
		 * @return string
		 * @since 5.0.0
		 */
		public function render_search_form( $attributes ) {
			$block_renderer = new YITH_WCBK_Search_Form_Block( $attributes );
			$block_renderer->set_allow_blank_state( false );
			ob_start();

			$block_renderer->render();

			return ob_get_clean();
		}

		/**
		 * Get render-block object.
		 *
		 * @param string $block      The block.
		 * @param array  $attributes Attributes.
		 *
		 * @return false|YITH_WCBK_Render_Block
		 * @since 3.1.0
		 */
		private function get_render_block_object( $block, $attributes ) {
			$block = implode( '_', array_map( 'ucfirst', explode( '-', $block ) ) );
			$class = "YITH_WCBK_{$block}_Block";

			if ( class_exists( $class ) ) {
				return new $class( $attributes );
			}

			return false;
		}

		/**
		 * Handle preview through iFrame to load theme scripts and styles.
		 */
		public function handle_iframe_preview() {
			if ( empty( $_GET['yith-wcbk-block-preview'] ) ) {
				return;
			}
			$block = wc_clean( wp_unslash( $_GET['block'] ?? '' ) );
			check_admin_referer( 'yith-wcbk-block-preview', 'yith-wcbk-block-preview-nonce' );

			$attributes = wc_clean( wp_unslash( $_GET['attributes'] ?? array() ) );

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			define( 'IFRAME_REQUEST', true );
			if ( ! defined( 'YITH_WCBK_BLOCK_PREVIEW' ) ) {
				define( 'YITH_WCBK_BLOCK_PREVIEW', true );
			}

			$block_renderer = $this->get_render_block_object( $block, $attributes );

			if ( $block_renderer ) {
				$block_renderer->set_allow_blank_state( true );

				do_action( 'wp_loaded' ); // Trigger wp_loaded to allow loading font-families and styles from theme.json.
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
						<?php $block_renderer->render(); ?>
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
			}

			exit;
		}

		/**
		 * Add YITH Category
		 *
		 * @param array   $categories Block categories array.
		 * @param WP_Post $post       The current post.
		 *
		 * @return array block categories
		 */
		public function block_category( $categories, $post ) {
			$found_key = array_search( 'yith-blocks', array_column( $categories, 'slug' ), true );

			if ( ! $found_key ) {
				$categories[] = array(
					'slug'  => 'yith-blocks',
					'title' => 'YITH',
				);
			}

			return $categories;
		}

		/**
		 * Create the json translation through the PHP file.
		 * So, it's possible using normal translations (with PO files) also for JS translations
		 *
		 * @param string|null $json_translations Translations.
		 * @param string      $file              The file.
		 * @param string      $handle            The handle.
		 * @param string      $domain            The text-domain.
		 *
		 * @return string|null
		 */
		public function script_translations( $json_translations, $file, $handle, $domain ) {
			if ( 'yith-booking-for-woocommerce' === $domain && in_array( $handle, array( 'yith-wcbk-gutenberg-blocks' ), true ) ) {
				$path = trailingslashit( YITH_WCBK_LANGUAGES_PATH ) . 'gutenberg-i18n.php';
				if ( file_exists( $path ) ) {
					$translations = include $path;

					$json_translations = wp_json_encode(
						array(
							'domain'      => 'yith-booking-for-woocommerce',
							'locale_data' => array(
								'messages' =>
									array(
										'' => array(
											'domain'       => 'yith-booking-for-woocommerce',
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
