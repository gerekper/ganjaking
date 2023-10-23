<?php
/**
 * Class to manage the PDF template builder
 *
 * @class   YITH_YWPI_PDF_Template_Builder
 * @since   4.0.0
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDF_Invoice\PDF_Builder
 */

defined( 'ABSPATH' ) || exit;

use Mpdf\Mpdf;

if ( ! class_exists( 'YITH_YWPI_PDF_Template_Builder' ) ) {
	/**
	 * Class YITH_YWPI_PDF_Template_Builder
	 */
	class YITH_YWPI_PDF_Template_Builder {

		/**
		 * Pdf Template Post Type
		 *
		 * @var string
		 * @static
		 */
		public static $pdf_template = 'ywpi-pdf-template';

		/**
		 * Hook in methods.
		 */
		public static function init() {
			$panel_page = YITH_YWPI_Plugin_FW_Loader::get_instance()->get_panel_page();

			add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
			add_action( 'init', array( __CLASS__, 'load' ), 5 );
			add_action( 'init', array( __CLASS__, 'maybe_create_templates' ), 30 );

			add_action( 'admin_init', array( __CLASS__, 'add_capabilities' ) );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_admin_scripts' ), 10 );
			add_filter( 'use_block_editor_for_post_type', array( __CLASS__, 'filter_use_block_editor_for_post_type' ), 10, 2 );
			add_filter( 'rest_prepare_' . self::$pdf_template, array( __CLASS__, 'filter_post_json' ), 10, 2 );
			add_action( 'rest_after_insert_' . self::$pdf_template, array( __CLASS__, 'save_meta' ), 10, 2 );

			add_filter( 'admin_body_class', array( __CLASS__, 'add_class_on_body' ) );
			add_filter( "yith_plugin_fw_panel_{$panel_page}_nav_item_classes", array( __CLASS__, 'add_class_on_templates_tab' ), 10, 2 );
			add_action( 'wp_ajax_ywpi_template_pdf_preview', array( __CLASS__, 'ajax_get_template_pdf_preview' ), 10 );

			// Script Translations.
			add_filter( 'pre_load_script_translations', array( __CLASS__, 'script_translations' ), 10, 4 );
		}

		/**
		 * Create templates on installation.
		 */
		public static function maybe_create_templates() {
			if ( empty( get_option( 'yith_ywpi_pdf_template_version' ) ) ) {
				self::create_default_pdf_templates();
				update_option( 'yith_ywpi_pdf_template_version', YITH_YWPI_VERSION );
			}
		}

		/**
		 * Create a pdf template for each type of document
		 */
		public static function create_default_pdf_templates() {
			$templates = apply_filters(
				'yith_ywpi_default_pdf_templates',
				array(
					'invoice'     => array(
						'name'    => 'simple-invoice',
						'title'   => _x( 'Simple Invoice', 'Page title', 'yith-woocommerce-pdf-invoice' ),
						'content' => self::get_default_pdf_content( 'invoice' ),
					),
					'credit-note' => array(
						'name'    => 'simple-credit-note',
						'title'   => _x( 'Simple Credit note', 'Page title', 'yith-woocommerce-pdf-invoice' ),
						'content' => self::get_default_pdf_content( 'credit-note' ),
					),
					'proforma'    => array(
						'name'    => 'simple-proforma',
						'title'   => _x( 'Simple Pro-forma', 'Page title', 'yith-woocommerce-pdf-invoice' ),
						'content' => self::get_default_pdf_content( 'proforma' ),
					),
					'shipping'    => array(
						'name'    => 'simple-shipping',
						'title'   => _x( 'Simple Packing slip', 'Page title', 'yith-woocommerce-pdf-invoice' ),
						'content' => self::get_default_pdf_content( 'shipping' ),
					),
				)
			);

			foreach ( $templates as $key => $template ) {
				self::create_pdf_template( $template['name'], $key, $template['title'], $template['content'] );
			}
		}

		/**
		 * Create a default template
		 *
		 * @param string $slug Slug of the post.
		 * @param string $option Key to save the option.
		 * @param string $page_title Page title.
		 * @param string $page_content Page content.
		 *
		 * @return void
		 */
		public static function create_pdf_template( $slug, $option = '', $page_title = '', $page_content = '' ) {
			$template_data = array(
				'post_status'    => 'publish',
				'post_type'      => self::$pdf_template,
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => wp_slash( $page_content ),
				'post_parent'    => 0,
				'comment_status' => 'closed',
			);

			$post_id = wp_insert_post( $template_data );

			if ( $post_id ) {
				// sync the main option.
				update_option( 'ywpi_pdf_custom_templates_' . $option, $post_id );
				update_post_meta( $post_id, '_template_parent', 'default' );
				update_post_meta( $post_id, '_name', $page_title );
			}
		}

		/**
		 * Check if the block editor can be used
		 *
		 * @param   bool   $use_block_editor Current value.
		 * @param   string $post_type        Post type.
		 */
		public static function filter_use_block_editor_for_post_type( $use_block_editor, $post_type ) {
			if ( self::$pdf_template === $post_type ) {
				$use_block_editor = false;
			}

			return $use_block_editor;
		}

		/**
		 * Register admin scripts
		 */
		public static function register_admin_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'ywpi-pdf-builder', YITH_YWPI_ASSETS_URL . '/js/ywpi-pdf-builder' . $suffix . '.js', array( 'jquery', 'jquery-ui-dialog', 'yith-plugin-fw-fields' ), YITH_YWPI_VERSION, true );
			wp_register_style( 'ywpi-pdf-builder', YITH_YWPI_ASSETS_URL . '/css/pdf-builder/ywpi-pdf-template-builder.css', false, YITH_YWPI_VERSION );
			wp_register_script( 'ywpi-pdf-template', YITH_YWPI_URL . 'dist/templates/index.js', false, YITH_YWPI_VERSION, true );

			wp_localize_script(
				'ywpi-pdf-builder',
				'ywpi_pdf_builder',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				)
			);

			if ( self::check_valid_page() ) {
				wp_enqueue_script( 'ywpi-pdf-builder' );
				wp_enqueue_style( 'ywpi-pdf-builder' );

				global $pagenow;

				if ( 'edit.php' !== $pagenow ) {
					wp_enqueue_script( 'ywpi-pdf-template' );
				}
			}
		}

		/**
		 * Load the other classes of the builder
		 *
		 * @return void
		 */
		public static function load() {
			require_once YITH_YWPI_INC_DIR . 'pdf-builder/class-yith-ywpi-pdf-template.php';
			include_once YITH_YWPI_INC_DIR . 'pdf-builder/editor/class-yith-ywpi-pdf-template-editor.php';
			include_once YITH_YWPI_INC_DIR . 'pdf-builder/editor/class-yith-ywpi-pdf-template-gutenberg.php';
		}

		/**
		 * Register core post types.
		 */
		public static function register_post_types() {
			if ( post_type_exists( self::$pdf_template ) ) {
				return;
			}

			/* PDF TEMPLATES  */
			$labels = array(
				'name'               => esc_html_x( 'PDF Templates', 'Post Type General Name', 'yith-woocommerce-pdf-invoice' ),
				'singular_name'      => esc_html_x( 'PDF Template', 'Post Type Singular Name', 'yith-woocommerce-pdf-invoice' ),
				'add_new_item'       => esc_html__( 'PDF Template', 'yith-woocommerce-pdf-invoice' ),
				'add_new'            => esc_html__( '+ Add new template', 'yith-woocommerce-pdf-invoice' ),
				'new_item'           => esc_html__( 'New template', 'yith-woocommerce-pdf-invoice' ),
				'edit_item'          => esc_html__( 'Edit template', 'yith-woocommerce-pdf-invoice' ),
				'view_item'          => esc_html__( 'View template', 'yith-woocommerce-pdf-invoice' ),
				'search_items'       => esc_html__( 'Search template', 'yith-woocommerce-pdf-invoice' ),
				'not_found'          => esc_html__( 'Not found', 'yith-woocommerce-pdf-invoice' ),
				'not_found_in_trash' => esc_html__( 'Not found in Trash', 'yith-woocommerce-pdf-invoice' ),
			);

			$post_type_args = array(
				'labels'                => $labels,
				'supports'              => array( 'editor', 'title' ),
				'hierarchical'          => false,
				'public'                => false,
				'show_ui'               => true,
				'show_in_menu'          => false,
				'menu_position'         => 10,
				'capability_type'       => self::$pdf_template,
				'capabilities'          => self::get_capabilities( self::$pdf_template ),
				'show_in_nav_menus'     => false,
				'has_archive'           => true,
				'exclude_from_search'   => true,
				'rewrite'               => false,
				'publicly_queryable'    => false,
				'query_var'             => false,
				'show_in_rest'          => true,
				'rest_base'             => 'ywpi_pdf_template',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			);

			register_post_type( self::$pdf_template, apply_filters( 'yith_ywpi_register_post_type_' . self::$pdf_template, $post_type_args ) );

			$meta = array(
				'_footer_content'    => 'string',
				'_template_parent'   => 'string',
				'_custom_background' => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'string',
						),
					),
				),
			);

			foreach ( $meta as $name => $type ) {
				register_post_meta(
					self::$pdf_template,
					$name,
					array(
						'show_in_rest' => true,
						'single'       => true,
						'type'         => $type,
					)
				);
			}
		}

		/**
		 * Add the capability
		 */
		public static function add_capabilities() {
			self::add_admin_capabilities( self::$pdf_template );
		}

		/**
		 * Add management capabilities to Admin and Shop Manager
		 *
		 * @param   string $ctp  Custom post type.
		 *
		 * @return  void
		 * @since   4.0.0
		 */
		public static function add_admin_capabilities( $ctp ) {
			$caps = self::get_capabilities( $ctp );

			$roles = array(
				'administrator',
				'shop_manager',
			);

			foreach ( $roles as $role_slug ) {
				$role = get_role( $role_slug );

				if ( ! $role ) {
					continue;
				}

				foreach ( $caps as $key => $cap ) {
					$role->add_cap( $cap );
				}
			}
		}

		/**
		 * Get capabilities for custom post type
		 *
		 * @param   string $capability_type  Capability name.
		 *
		 * @return  array
		 *
		 * @since 4.0.0
		 */
		public static function get_capabilities( $capability_type ) {
			return array(
				'edit_post'              => "edit_{$capability_type}",
				'read_post'              => "read_{$capability_type}",
				'delete_post'            => "delete_{$capability_type}",
				'edit_posts'             => "edit_{$capability_type}s",
				'edit_others_posts'      => "edit_others_{$capability_type}s",
				'publish_posts'          => "publish_{$capability_type}s",
				'read_private_posts'     => "read_private_{$capability_type}s",
				'delete_posts'           => "delete_{$capability_type}s",
				'delete_private_posts'   => "delete_private_{$capability_type}s",
				'delete_published_posts' => "delete_published_{$capability_type}s",
				'delete_others_posts'    => "delete_others_{$capability_type}s",
				'edit_private_posts'     => "edit_private_{$capability_type}s",
				'edit_published_posts'   => "edit_published_{$capability_type}s",
				'create_posts'           => "edit_{$capability_type}s",
				'manage_posts'           => "manage_{$capability_type}s",
			);
		}

		/**
		 * Return if the current pagenow is valid for a post_type, useful if you want add metabox, scripts inside the editor of a particular post type.
		 *
		 * @return bool
		 */
		public static function check_valid_page() {
			global $pagenow;

			$post_type_name = self::$pdf_template;
			$screen         = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id      = $screen ? $screen->id : '';

			$post = isset( $_REQUEST['post'] ) ? intval( $_REQUEST['post'] ) : ( isset( $_REQUEST['post_ID'] ) ? intval( $_REQUEST['post_ID'] ) : 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post = get_post( $post );

			return 'edit-' . $post_type_name === $screen_id || ( $post && $post->post_type === $post_type_name ) || ( 'post-new.php' === $pagenow && isset( $_REQUEST['post_type'] ) && $post_type_name === $_REQUEST['post_type'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * Save the template parent meta inside the pdf template.
		 *
		 * @param   WP_Post $post     Post.
		 * @param   array   $request  Request.
		 *
		 * @return void
		 */
		public static function save_meta( $post, $request ) {
			if ( isset( $request['meta']['_template_parent'] ) ) {
				update_post_meta( $post->ID, '_template_parent', $request['meta']['_template_parent'] );
			}

			if ( isset( $request['meta']['_custom_background'] ) ) {
				update_post_meta( $post->ID, '_custom_background', $request['meta']['_custom_background'] );
			}

			if ( isset( $request['meta']['_footer_content'] ) ) {
				update_post_meta( $post->ID, '_footer_content', $request['meta']['_footer_content'] );
			}

			update_post_meta( $post->ID, '_name', $post->post_title );
		}

		/**
		 * Add meta to the post via REST
		 *
		 * @param   WP_REST_Response $data  The response object.
		 * @param   WP_Post          $post  Post requested.
		 *
		 * @return WP_REST_Response
		 */
		public static function filter_post_json( $data, $post ) {
			if ( $post->post_type !== self::$pdf_template ) {
				return $data;
			}

			$data->data['template_parent']            = get_post_meta( $post->ID, '_template_parent', true );
			$data->data['template_parent']            = empty( $data->data['template_parent'] ) ? 'default' : $data->data['template_parent'];
			$data->data['meta']['_footer_content']    = get_post_meta( $post->ID, '_footer_content', true );
			$data->data['meta']['_custom_background'] = get_post_meta( $post->ID, '_custom_background', true );
			$data->data['meta']['_template_parent']   = get_post_meta( $post->ID, '_template_parent', true );

			return $data;
		}

		/**
		 * Duplicate post type
		 *
		 * @param   WP_Post $original_post  Original post.
		 * @param   string  $post_type      Post type.
		 *
		 * @return int
		 */
		public static function duplicate_post( $original_post, $post_type ) {
			$new_title = yith_ywpi_get_unique_post_title( $original_post->post_title, $original_post->ID, self::$pdf_template );
			$new_post  = array(
				'post_status'  => 'publish',
				'post_type'    => $post_type,
				'post_title'   => $new_title,
				'post_content' => wp_slash( $original_post->post_content ),
			);

			$new_post_id = wp_insert_post( $new_post );
			$metas       = get_post_meta( $original_post->ID );

			if ( ! empty( $metas ) ) {
				foreach ( $metas as $meta_key => $meta_value ) {
					if ( in_array( $meta_key, array( '_default', '_edit_lock', '_edit_last' ), true ) ) {
						continue;
					}

					update_post_meta( $new_post_id, $meta_key, maybe_unserialize( $meta_value[0] ) );
				}
			}

			update_post_meta( $new_post_id, '_name', $new_title );

			return $new_post_id;
		}

		/**
		 * Return the list of pdf templates
		 *
		 * @return array
		 */
		public static function get_pdf_template_list() {
			$template_list = array();

			$posts = get_posts(
				array(
					'post_type'   => self::$pdf_template,
					'numberposts' => - 1,
					'order_by'    => 'post_title',
					'order'       => 'ASC',
				)
			);

			if ( $posts ) {
				foreach ( $posts as $post ) {
					$template_list[ $post->ID ] = $post->post_title;
				}
			}

			return $template_list;
		}

		/**
		 * Add a class inside the templates tab
		 *
		 * @param array  $classes Active classes.
		 * @param string $tab_key Current tab key.
		 *
		 * @return array
		 * @since 4.0
		 */
		public static function add_class_on_templates_tab( $classes, $tab_key ) {
			if ( 'template' === $tab_key ) {
				$template_to_use = get_option( 'ywpi_pdf_template_to_use', yith_ywpi_is_gutenberg_active() ? 'builder' : 'default' );
				$class_to_add    = 'default' === $template_to_use ? 'hide-tab-templates' : 'show-tab-templates';

				$classes[] = $class_to_add;
			}

			return $classes;
		}

		/**
		 * Add a class inside the quote tab
		 *
		 * @param   string $classes  Active class.
		 *
		 * @return string
		 * @since 4.0
		 */
		public static function add_class_on_body( $classes ) {
			if ( isset( $_GET['page'] ) && 'yith_woocommerce_pdf_invoice_panel' === $_GET['page'] && isset( $_GET['tab'] ) && 'template' === $_GET['tab'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$classes .= ' tab-templates';
			}

			return $classes;
		}

		/**
		 * Create the pdf preview from template builder
		 */
		public static function ajax_get_template_pdf_preview() {
			check_ajax_referer( 'preview_template', 'security' );

			if ( isset( $_REQUEST['action'], $_REQUEST['pdf_template_preview'] ) && 'ywpi_template_pdf_preview' === sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) ) {
				$template_id      = sanitize_text_field( wp_unslash( $_REQUEST['pdf_template_preview'] ) );
				$template         = yith_ywpi_get_pdf_template( $template_id );
				$preview_products = isset( $_REQUEST['preview_product'] ) ? $_REQUEST['preview_product'] : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$pdf_url          = $template->get_preview( $preview_products );

				wp_send_json(
					array( 'pdf' => $pdf_url )
				);
			}
		}

		/**
		 * Return the mpdf object
		 *
		 * @param YITH_Document|null $document Document object.
		 *
		 * @return Mpdf
		 * @throws \Mpdf\MpdfException Exception.
		 */
		public static function get_mpdf( $document = null ) {
			require_once YITH_YWPI_DIR . 'lib/vendor/autoload.php';

			$mpdf_args = apply_filters(
				'yith_ywpdi_mpdf_args',
				array(
					'autoScriptToLang'  => true,
					'autoLangToFont'    => true,
					'default_font'      => 'dejavusans',
					'default_font_size' => 12,
				),
				$document
			);

			if ( is_array( $mpdf_args ) ) {
				$mpdf = new Mpdf( $mpdf_args );
			} else {
				$mpdf = new Mpdf();
			}

			$direction                  = is_rtl() ? 'rtl' : 'ltr';
			$mpdf->directionality       = apply_filters( 'yith_ywpdi_mpdf_directionality', $direction );
			$mpdf->shrink_tables_to_fit = 1;

			return $mpdf;
		}

		/**
		 * Return the content of the default pdf template.
		 *
		 * @param string $document_type Document type.
		 *
		 * @return string
		 */
		public static function get_default_pdf_content( $document_type ) {
			$content  = '';
			$filename = YITH_YWPI_INC_DIR . 'pdf-builder/pdf-default-content/' . $document_type . '.txt';

			if ( file_exists( $filename ) ) {
				$content = file_get_contents( $filename ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			}

			return $content;
		}

		/**
		 * Create the json translation through the PHP file
		 * so it's possible using normal translations (with PO files) also for JS translations
		 *
		 * @param   string|null $json_translations  Json translation.
		 * @param   string      $file               File.
		 * @param   string      $handle             Handle.
		 * @param   string      $domain             Domain.
		 *
		 * @return string|null
		 * @since 4.0
		 */
		public static function script_translations( $json_translations, $file, $handle, $domain ) {
			$plugin_domain = 'yith-woocommerce-pdf-invoice';
			$handles       = array( 'ywpi-pdf-template-builder-script', 'ywpi-pdf-builder' );

			if ( $plugin_domain === $domain && in_array( $handle, $handles, true ) ) {
				$path = YITH_YWPI_DIR . 'languages/' . $domain . '.php';

				if ( file_exists( $path ) ) {
					$translations = include $path;

					$json_translations = wp_json_encode(
						array(
							'domain'      => $handles,
							'locale_data' => array(
								'messages' => array(
									'' => array(
										'domain'       => $handles,
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

YITH_YWPI_PDF_Template_Builder::init();
