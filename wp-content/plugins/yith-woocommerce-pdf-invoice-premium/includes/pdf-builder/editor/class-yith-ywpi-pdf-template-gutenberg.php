<?php
/**
 * Class to manage the PDF template Gutenberg
 *
 * @class   YITH_YWPI_PDF_Template_Gutenberg
 * @since   4.0.0
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDF_Invoice\PDF_Builder
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_YWPI_PDF_Template_Gutenberg' ) ) {
	/**
	 * Class YITH_YWPI_PDF_Template_Gutenberg
	 */
	class YITH_YWPI_PDF_Template_Gutenberg {

		/**
		 * Custom blocks
		 *
		 * @var array
		 */
		public $blocks = array(
			'yith/ywpi-products-table',
			'yith/ywpi-products-totals',
			'yith/ywpi-customer-info',
			'yith/ywpi-shipping-info',
			'yith/ywpi-date',
			'yith/ywpi-order-number',
			'yith/ywpi-order-amount',
			'yith/ywpi-document-number',
		);

		/**
		 * Enable template cache
		 *
		 * @var bool
		 */
		protected $enable_cash = true;

		/**
		 * Single instance of the class.
		 *
		 * @var YITH_YWPI_PDF_Template_Gutenberg
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_YWPI_PDF_Template_Gutenberg
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'gutenberg_integration' ) );
			add_filter( 'allowed_block_types_all', array( $this, 'allowed_block_types' ), 10, 2 );
			add_filter( 'block_categories_all', array( $this, 'block_category' ), 100 );
			add_action( 'after_setup_theme', array( $this, 'disable_theme_palette' ), 100 );

			add_action( 'wp_ajax_ywpi_get_template_pdf_content', array( $this, 'get_template_pdf_content' ) );
			add_action( 'wp_ajax_ywpi_get_pdf_templates', array( $this, 'get_templates' ) );

			$this->enable_cash = apply_filters( 'ywpi_enable_pdf_template_cache', true );
		}

		/**
		 * Add block category
		 *
		 * @param array $categories Array block categories array.
		 *
		 * @return array block categories
		 */
		public function block_category( $categories ) {
			$found_key = array_search( 'yith-blocks', array_column( $categories, 'slug' ), true );

			if ( ! $found_key ) {
				$categories[] = array(
					'slug'  => 'yith-blocks',
					'title' => _x( 'YITH', '[gutenberg]: Category Name', 'yith-plugin-fw' ),
				);
			}

			return $categories;
		}

		/**
		 * Gutenberg Integration
		 */
		public function gutenberg_integration() {
			// Register blocks for PDF template.
			if ( YITH_YWPI_PDF_Template_Builder::check_valid_page() ) {
				$version = YITH_YWPI_VERSION . '4';

				wp_register_style( 'ywpi-pdf-template-builder', YITH_YWPI_ASSETS_URL . '/css/pdf-builder/ywpi-pdf-template-builder.css', array( 'wp-edit-blocks' ), $version, false );
				wp_register_script( 'ywpi-pdf-template-builder-script', YITH_YWPI_URL . 'dist/blocks/index.js', array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-plugins', 'wp-i18n', 'wc-components', 'wp-mediaelement' ), $version, false );

				wp_localize_script(
					'ywpi-pdf-template-builder-script',
					'ywpi_pdf_template',
					array(
						'ajaxurl'                       => admin_url( 'admin-ajax.php' ),
						'preview_image_url'             => YITH_YWPI_ASSETS_IMAGES_URL . 'pdf-builder',
						'customer_info_placeholders'    => yith_ywpi_template_editor()->get_customer_info_placeholders(),
						'shipping_info_placeholders'    => yith_ywpi_template_editor()->get_shipping_info_placeholders(),
						'licence_key'                   => yith_ywpi_get_license(),
						'licence_url'                   => yith_ywpi_get_license_activation_url(),
						'slug'                          => YITH_YWPI_SLUG,
						'get_template_content_security' => wp_create_nonce( 'get_template_content' ),
						'get_templates_security'        => wp_create_nonce( 'get_templates' ),
						'preview_products'              => yith_ywpi_template_editor()->get_preview_products(),
						'preview_fee'                   => yith_ywpi_template_editor()->get_fee_preview_content(),
						'preview_shipping'              => yith_ywpi_template_editor()->get_shipping_preview_content(),
						'today'                         => date_i18n( wc_date_format(), time() ),
						'preview_template_nonce'        => wp_create_nonce( 'preview_template' ),
						'credit_notes_positive_amounts' => get_option( 'ywpi_credit_note_positive_values_builder', 'no' ),
					)
				);

				$assets = array(
					'script'       => 'ywpi-pdf-template-builder-script',
					'editor_style' => 'ywpi-pdf-template-builder',
					'style'        => 'ywpi-pdf-template-builder',
				);

				foreach ( $this->blocks as $block ) {
					register_block_type( $block, $assets );
				}

				if ( function_exists( 'wp_set_script_translations' ) ) {
					wp_set_script_translations( 'ywpi-pdf-template-builder-script', 'yith-woocommerce-pdf-invoice', YITH_YWPI_DIR . 'languages' );
				}
			}
		}

		/**
		 * Select specific block from Gutenberg
		 *
		 * @param array                   $allowed_blocks       Current blocks.
		 * @param WP_Block_Editor_Context $block_editor_context The current block editor.
		 *
		 * @return array
		 */
		public function allowed_block_types( $allowed_blocks, $block_editor_context ) {
			$post = $block_editor_context->post;

			if ( $post && YITH_YWPI_PDF_Template_Builder::$pdf_template === $post->post_type ) {
				$allowed_blocks = array(
					'core/image',
					'core/paragraph',
					'core/heading',
					'core/list',
					'core/columns',
					'core/buttons',
					'core/separator',
					'core/spacer',
				);
				$allowed_blocks = array_merge( $allowed_blocks, $this->blocks );
			}

			return $allowed_blocks;
		}

		/**
		 * Removing the theme palette because the colors on pdf are not available
		 *
		 * @return void
		 */
		public function disable_theme_palette() {
			if ( YITH_YWPI_PDF_Template_Builder::check_valid_page() ) {
				add_theme_support( 'editor-color-palette', array() );
			}
		}

		/**
		 * Return the content of template
		 *
		 * @return false|void
		 */
		public function get_template_pdf_content() {
			check_ajax_referer( 'get_template_content', 'security' );

			$template = isset( $_POST['template_id'] ) ? sanitize_text_field( wp_unslash( $_POST['template_id'] ) ) : '';

			$transient        = 'ywpi_pdf_templates_content_' . YITH_YWPI_VERSION;
			$template_content = get_transient( $transient );

			if ( $this->enable_cash && false !== $template_content && isset( $template_content[ $template ] ) ) {
				wp_send_json(
					array( 'content' => $template_content )
				);
			} else {
				$response = $this->get_response( 'content/' . $template . '.txt' );

				if ( is_wp_error( $response ) ) {
					wp_send_json_error( array( 'error' => 'Call to remote template failed' ) );
				}

				if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
					$template_content               = wp_remote_retrieve_body( $response );
					$template_contents[ $template ] = $template_content;

					set_transient( $transient, $template_contents, DAY_IN_SECONDS );
					wp_send_json(
						array( 'content' => $template_content )
					);
				} elseif ( 403 === wp_remote_retrieve_response_code( $response ) ) {
					$response                       = $this->get_response( 'content/' . $template . '.txt', true );
					$template_content               = wp_remote_retrieve_body( $response );
					$template_contents[ $template ] = $template_content;

					set_transient( $transient, $template_contents, DAY_IN_SECONDS );

					wp_send_json(
						array( 'content' => $template_content )
					);
				}
			}
		}

		/**
		 * Get the templates
		 */
		public function get_templates() {
			check_ajax_referer( 'get_templates', 'security' );
			$transient = 'ywpi_pdf_templates_' . YITH_YWPI_VERSION;
			$templates = get_transient( $transient );

			if ( $this->enable_cash && false !== $templates ) {
				wp_send_json(
					array( 'templates' => $templates )
				);
			} else {
				$response = $this->get_response( 'list.json' );

				if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
					$template_list = json_decode( wp_remote_retrieve_body( $response ), true );

					set_transient( $transient, $template_list, DAY_IN_SECONDS );

					wp_send_json(
						array( 'templates' => $template_list )
					);
				} elseif ( 403 === wp_remote_retrieve_response_code( $response ) ) {
					$response      = $this->get_response( 'list.json', true );
					$template_list = json_decode( wp_remote_retrieve_body( $response ), true );

					set_transient( $transient, $template_list, DAY_IN_SECONDS );

					wp_send_json(
						array( 'templates' => $template_list )
					);
				} else {
					wp_send_json_error( array( 'error' => 'Call to remote template failed' ) );
				}
			}
		}

		/**
		 * Return the response to get the remote templates
		 *
		 * @param string $url    URL.
		 * @param bool   $casper Whether retrieve templates from an alternative server.
		 *
		 * @return array|WP_Error
		 */
		private function get_response( $url, $casper = false ) {
			if ( $casper ) {
				$api_url = 'https://casper.yithemes.com/resources/yith-woocommerce-pdf-invoice/pdf-templates/';
			} else {
				$api_url = 'https://plugins.yithemes.com/resources/yith-woocommerce-pdf-invoice/pdf-templates/';
			}

			$api_call_args = array(
				'timeout'    => apply_filters( 'ywpi_get_templates_timeout', 15 ),
				'user-agent' => 'YITH WooCommerce PDF Invoices & Packing Slips Premium/' . YITH_YWPI_VERSION . '; ' . get_site_url(),
			);

			return wp_remote_get( $api_url . $url, $api_call_args );
		}
	}
}

/**
 * Unique access to instance of YITH_YWPI_PDF_Template_Gutenberg class
 *
 * @return YITH_YWPI_PDF_Template_Gutenberg
 */
function yith_ywpi_gutenberg() { // phpcs:ignore Universal.Files.SeparateFunctionsFromOO
	return YITH_YWPI_PDF_Template_Gutenberg::get_instance();
}

yith_ywpi_gutenberg();
