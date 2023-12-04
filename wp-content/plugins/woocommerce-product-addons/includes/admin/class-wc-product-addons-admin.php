<?php
/**
 * Product Add-ons admin
 *
 * @package WC_Product_Addons/Classes/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;
use Automattic\WooCommerce\Admin\Features\Features;

/**
 * Product_Addon_Admin class.
 */
/**
 * Product Add-Ons admin.
 *
 * @class    WC_Product_Addons_Admin
 * @version  6.5.1
 */
class WC_Product_Addons_Admin {

	/**
	 * Store of generated ids.
	 *
	 * @since 5.0.1
	 *
	 * @var array
	 */
	private $generated_ids = array();

	/**
	 * Initialize administrative actions.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'script_styles' ), 100 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id' ) );
		add_filter( 'woocommerce_navigation_screen_ids', array( $this, 'add_screen_id' ) );
		add_action( 'admin_menu', array( $this, 'wc_admin_connect_pao_pages' ) );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_meta_box' ), 1 );

		// Addon order display.
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'filter_hidden_order_itemmeta' ) );
		add_action( 'woocommerce_before_order_item_line_item_html', array( $this, 'filter_order_line_item_html' ), 10, 3 );
		add_action( 'woocommerce_order_item_line_item_html', array( $this, 'filter_order_line_item_after_html' ), 10, 3 );

		add_action( 'wp_ajax_wc_pao_get_addon_options', array( $this, 'ajax_get_addon_options' ) );
		add_action( 'wp_ajax_wc_pao_get_addon_field', array( $this, 'ajax_get_addon_field' ) );

		// Handles importing of new add-ons via AJAX.
		add_action( 'wp_ajax_wc_pao_import_addons', array( $this, 'ajax_import_addons' ) );

		// Include dependencies.
		add_action( 'admin_init', array( $this, 'includes' ) );

		// Add add-ons settings under WooCommerce > Settings > Products.
		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_addons_section' ), 15 );
		add_action( 'woocommerce_get_settings_products', array( $this, 'add_addons_settings' ), 10, 2 );

		// Add a notice if an add-on title exceeds the 255 characters threshold.
		add_action( 'admin_notices', array( $this, 'maybe_add_title_max_length_notice' ), 0 );
	}

	/**
	 * Add menus
	 */
	public function admin_menu() {
		$page = add_submenu_page(
			'edit.php?post_type=product',
			__( 'Add-ons', 'woocommerce-product-addons' ),
			__( 'Add-ons', 'woocommerce-product-addons' ),
			'manage_woocommerce',
			'addons',
			array( $this, 'global_addons_admin' ) );

		if (
			! class_exists( 'Features' ) ||
			! method_exists( Screen::class, 'register_post_type' ) ||
			! method_exists( Menu::class, 'add_plugin_item' ) ||
			! method_exists( Menu::class, 'add_plugin_category' ) ||
			! Features::is_enabled( 'navigation' )
		) {
			return;
		}

		Menu::add_plugin_item(
			array(
				'id'         => 'woocommerce-product-addons',
				'title'      => __( 'Product Add-ons', 'woocommerce-product-addons' ),
				'url'        => 'edit.php?post_type=product&page=addons',
				'capability' => 'manage_woocommerce',
			)
		);
	}

	/**
	 * Include dependencies.
	 */
	public function includes() {

		// Register compatibility with WooCommerce Importer/Exporter.
		include( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/admin/export/class-wc-product-addons-product-export.php' );
		include( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/admin/import/class-wc-product-addons-product-import.php' );

		include( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/admin/class-wc-product-addons-admin-notices.php' );
	}

	/**
	 * Injects new add-ons settings section.
	 *
	 * @param array $sections Other admin settings sections.
	 *
	 * @return array
	 */
	public function add_addons_section( array $sections ): array {
		$sections['add_ons'] = __( 'Add-ons', 'woocommerce-product-addons' );
		return $sections;
	}

	/**
	 * Renders the add-ons settings.
	 */
	public function add_addons_settings( $settings, $section_id ) {

		if ( 'add_ons' !== $section_id ) {
			return $settings;
		}

		$title_item = array(
			'title' => __( 'Add-ons', 'woocommerce-product-addons' ),
			'type'  => 'title',
		);

		$settings[] = $title_item;

		$options       = get_option( 'product_addons_options', array() );
		$show_subtotal = 'no';

		// Backwards compatibility.
		if ( isset( $options['show-incomplete-subtotal'] ) && ( 1 === absint( $options['show-incomplete-subtotal'] ) || 'yes' === $options['show-incomplete-subtotal'] ) ) {
			$show_subtotal = 'yes';
		}

		$settings[] = array(
			'title'         => __( 'Show Incomplete subtotal', 'woocommerce-product-addons' ),
			'desc'          => __( 'Show running subtotal, even if not all required add-on choices have been made.', 'woocommerce-product-addons' ),
			'id'            => 'product_addons_options[show-incomplete-subtotal]',
			'default'       => 'no',
			'value'         => $show_subtotal,
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
		);

		$settings[] = array( 'type' => 'sectionend' );

		return $settings;
	}

	/**
	 * Get add-on options.
	 *
	 * @since 3.0.0.
	 */
	public function ajax_get_addon_options() {
		check_ajax_referer( 'wc-pao-get-addon-options', 'security' );

		global $product_addons, $post, $options;

		$option = WC_Product_Addons_Admin::get_new_addon_option();
		$loop   = "{loop}";

		ob_start();
		include( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/admin/views/html-addon-option.php' );
		$html = ob_get_clean();

		$html = str_replace( array( "\n", "\r" ), '', str_replace( "'", '"', $html ) );

		wp_send_json( array( 'html' => $html ) );
	}

	/**
	 * Get add-on field.
	 *
	 * @since 3.0.0.
	 */
	public function ajax_get_addon_field() {
		check_ajax_referer( 'wc-pao-get-addon-field', 'security' );

		global $product_addons, $post, $options;

		ob_start();

		// If add-on data are requested via a POST request (e.g. import), return a pre-populated add-on edit form.
		if ( isset( $_POST[ 'addon' ] ) && ! empty( $_POST[ 'addon' ] ) ) {
			$addon                       = $_POST[ 'addon' ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$addon['name']               = isset( $addon[ 'name' ] ) ? sanitize_text_field( wp_unslash($addon[ 'name' ] ) ) : '';
			$addon['title_format']       = isset( $addon[ 'title_format' ] ) ? sanitize_text_field( wp_unslash($addon[ 'title_format' ] ) ) : '';
			$addon['description_enable'] = isset( $addon[ 'description_enable' ] ) && '1' === $addon[ 'description_enable' ] ? 1 : 0;
			$addon['description']        = isset( $addon[ 'description' ] ) ? wp_kses_post( wp_unslash( $addon[ 'description' ] ) ) : '';
			$addon['required']           = isset( $addon[ 'required' ] ) && '1' === $addon[ 'required' ] ? 1 : 0;
			$addon['type']               = isset( $addon[ 'field_type' ] ) ? sanitize_text_field( wp_unslash( $addon[ 'field_type' ] ) ) : 'multiple_choice';
			$addon['display']            = isset( $addon[ 'display' ] ) ? sanitize_text_field( wp_unslash( $addon[ 'display' ] ) ) : 'select';
			$addon['restrictions']       = isset( $addon[ 'restrictions' ] ) && '1' === $addon[ 'restrictions' ] ? 1 : 0;
			$addon['restrictions_type']  = isset( $addon[ 'restrictions_type' ] ) ? sanitize_text_field( wp_unslash( $addon[ 'restrictions_type' ] ) ) : 'any_text';
			$addon['min']                = isset( $addon[ 'min' ] ) ? (float) sanitize_text_field( wp_unslash( $addon[ 'min' ] ) ) : '';
			$addon['max']                = isset( $addon[ 'max' ] ) ? (float) sanitize_text_field( wp_unslash( $addon[ 'max' ] ) ) : '';
			$addon['adjust_price']       = isset( $addon[ 'adjust_price' ] ) && '1' === $addon[ 'adjust_price' ] ? 1 : 0;
			$addon['price_type']         = isset( $addon[ 'price_type' ] ) ? sanitize_text_field( wp_unslash( $addon[ 'price_type' ] ) ) : '';
			$addon['price']              = isset( $addon[ 'price' ] ) ? wc_format_decimal( sanitize_text_field( wp_unslash( $addon[ 'price' ] ) ) ) : '';

			if ( isset( $addon[ 'options' ] ) ) {
				foreach ( $addon[ 'options' ] as $option ) {
					$option[ 'label' ]      = sanitize_text_field( wp_unslash( $option[ 'label' ] ) );
					$option[ 'price' ]      = wc_format_decimal( sanitize_text_field( wp_unslash( $option[ 'price' ] ) ) );
					$option[ 'image' ]      = sanitize_text_field( wp_unslash( $option[ 'image' ] ) );
					$option[ 'price_type' ] = sanitize_text_field( wp_unslash( $option[ 'price_type' ] ) );
				}
			} else {
				$addon['options'] = array(
					WC_Product_Addons_Admin::get_new_addon_option(),
				);
			}

		// Otherwise, return the default add-ons edit form.
		} else {
			$addon                       = array();
			$addon['name']               = '';
			$addon['title_format']       = 'label';
			$addon['description_enable'] = '';
			$addon['description']        = '';
			$addon['required']           = '';
			$addon['type']               = isset( $_POST[ 'field_type' ] ) ? sanitize_text_field( wp_unslash($_POST[ 'field_type' ] ) ) : 'multiple_choice';
			$addon['display']            = 'select';
			$addon['restrictions']       = '';
			$addon['restrictions_type']  = 'any_text';
			$addon['min']                = '';
			$addon['max']                = '';
			$addon['adjust_price']       = '';
			$addon['price_type']         = '';
			$addon['price']              = '';

			$addon['options']            = array(
				WC_Product_Addons_Admin::get_new_addon_option(),
			);
		}

		$loop = "{loop}";

		include( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/admin/views/html-addon.php' );

		$html = ob_get_clean();

		$html = str_replace( array( "\n", "\r" ), '', str_replace( "'", '"', $html ) );

		wp_send_json( array( 'html' => $html ) );
	}

	/**
	 * AJAX callback for importing product add-ons.
	 *
	 * @since 6.5.0
	 */
	public function ajax_import_addons() {

		check_ajax_referer( 'wc-pao-import-addons', 'security' );

		$fail_message = __( 'Could not import add-ons. Please review the import string and try again.', 'woocommerce-product-addons' );

		if ( ! empty( $_POST[ 'import_product_addon' ] ) && ! empty( $_POST[ 'post_id' ] ) ) {

			$current_addon_data = array();
			$current_addon_ids  = array();
			$post_id            = absint( $_POST[ 'post_id' ] );

			// Get current product/global add-ons. Imported add-ons should be added after existing add-ons.
			if ( isset( $_POST[ 'is_global' ] ) && wc_clean( wp_unslash( $_POST[ 'is_global' ] ) ) ) {
				$global_addon       = get_post( $post_id );
				$current_addon_data = is_a( $global_addon, 'WP_Post' ) ? array_filter( (array) get_post_meta( $global_addon->ID, '_product_addons', true ) ) : array(); // nosemgrep: audit.php.lang.misc.array-filter-no-callback
			} else {
				$current_addon_data = WC_Product_Addons_Helper::get_product_addons( $post_id, false, true, false );
			}

			foreach ( $current_addon_data as $addon ) {
				if ( isset( $addon[ 'id' ] ) ) {
					$current_addon_ids[] = $addon[ 'id' ];
				}
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$import_addons = trim( wp_unslash( $_POST[ 'import_product_addon' ] ) );

			// maybe_unserialize does not support additional options, to set allowed_classes to false.
			if ( is_serialized( $import_addons ) ) { // Don't attempt to unserialize data that wasn't serialized going in.
				$import_addons =  @unserialize( $import_addons, array( 'allowed_classes' => false ) ); // nosemgrep: audit.php.lang.security.object-injection
			}

			if ( is_array( $import_addons ) && ! empty( $import_addons ) ) {
				$valid = true;

				foreach ( $import_addons as $key => $addon ) {
					if ( ! isset( $addon['name'] ) || ! $addon['name'] ) {
						$valid = false;
					}
					if ( ! isset( $addon['description'] ) ) {
						$valid = false;
					}
					if ( ! isset( $addon['type'] ) ) {
						$valid = false;
					}
					if ( ! isset( $addon['position'] ) ) {
						$valid = false;
					}
					if ( ! isset( $addon['required'] ) ) {
						$valid = false;
					}

					$addon['id'] = $this->generate_id( $current_addon_ids );

					// Sanitize the addon before importing.
					if ( $valid ) {
						$import_addons[ $key ] = apply_filters( 'woocommerce_product_addons_import_data', $this->sanitize_addon( $addon ), $addon, $key );
					} else {
						wp_send_json_error( array( 'message' => $fail_message ) );
					}
				}

				/* translators: %s number of imported add-ons */
				wp_send_json_success( array( 'addons' => $import_addons, 'message' => sprintf( __( '%d add-ons were successfully imported.', 'woocommerce-product-addons' ), count( $import_addons ) ) ) );

			} else {
				wp_send_json_error( array( 'message' => $fail_message ) );
			}
		}

		wp_send_json_error( array( 'message' => $fail_message ) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 3.0.0
	 */
	public function script_styles() {

		$valid_screen_ids = array(
			'product',
			'shop_order',
			'shop_subscription',
			WC_PAO()->get_formatted_screen_id( 'woocommerce_page_wc-orders' ),
			WC_PAO()->get_formatted_screen_id( 'woocommerce_page_wc-orders--shop_subscription' ),
			WC_PAO()->get_formatted_screen_id( 'woocommerce_page_wc-orders--shop_order' )
		);

		if ( ! WC_PAO()->is_current_screen( $valid_screen_ids ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'woocommerce_product_addons_css', WC_PRODUCT_ADDONS_PLUGIN_URL . '/assets/css/admin/admin.css', array(), WC_PRODUCT_ADDONS_VERSION );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'woocommerce_product_addons', plugins_url( 'assets/js/admin/admin' . $suffix . '.js', WC_PRODUCT_ADDONS_MAIN_FILE ), array( 'jquery' ), WC_PRODUCT_ADDONS_VERSION, true );

		$params = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => array(
				'get_addon_options' => wp_create_nonce( 'wc-pao-get-addon-options' ),
				'get_addon_field'   => wp_create_nonce( 'wc-pao-get-addon-field' ),
				'import_addons'     => wp_create_nonce( 'wc-pao-import-addons' ),
			),
			'i18n'     => array(
				'required_fields'           => __( 'Please complete all highlighted fields before saving.', 'woocommerce-product-addons' ),
				'max_title_length_exceeded' => __( 'Add-on titles must have maximum 255 characters. Please update the titles of the highlighted add-ons.', 'woocommerce-product-addons' ),
				'limit_price_range'         => __( 'Limit price', 'woocommerce-product-addons' ),
				'limit_quantity_range'      => __( 'Limit quantity', 'woocommerce-product-addons' ),
				'limit_character_length'    => __( 'Limit character length', 'woocommerce-product-addons' ),
				'restrictions'              => __( 'Restrictions', 'woocommerce-product-addons' ),
				'confirm_remove_addon'      => __( 'Remove this add-on field?', 'woocommerce-product-addons' ),
				'confirm_remove_option'     => __( 'Delete this option?', 'woocommerce-product-addons' ),
				'add_image_swatch'          => __( 'Add Image Swatch', 'woocommerce-product-addons' ),
				'add_image'                 => __( 'Add Image', 'woocommerce-product-addons' ),
				'import_addons_prompt'      => __( 'Use this field to paste the add-ons you previously exported.', 'woocommerce-product-addons' ),
				'addons_exported'           => __( 'Add-ons successfully copied to your clipboard.', 'woocommerce-product-addons' ),
				'delete_addons_warning'     => __( 'The selected add-on groups will be deleted permanently. Are you sure?', 'woocommerce-product-addons' ),
				'options_header_default'    => __( 'Option title', 'woocommerce-product-addons' ),
				'options_header_with_image' => __( 'Option title/image', 'woocommerce-product-addons' ),
			),
		);

		wp_localize_script( 'woocommerce_product_addons', 'wc_pao_params', apply_filters( 'wc_pao_params', $params ) );

		wp_enqueue_script( 'woocommerce_product_addons' );
	}

	/**
	 * Add screen id to WooCommerce.
	 *
	 * @param array $screen_ids List of screen IDs.
	 * @return array
	 */
	public function add_screen_id( $screen_ids ) {
		$screen_ids = array_merge( $screen_ids, WC_PAO()->get_screen_ids() );

		return $screen_ids;
	}

	/**
	 * Connect pages with navigation bar.
	 *
	 * @since 6.4.7
	 * @return void
	 */
	public static function wc_admin_connect_pao_pages() {

		if ( function_exists( 'wc_admin_connect_page' ) ) {
			wc_admin_connect_page(
				array(
					'id'        => 'woocommerce-product-addons',
					'screen_id' => 'product_page_addons',
					'title'     => __( 'Global Add-ons', 'woocommerce-product-addons' ),
				)
			);

		}
	}

	/**
	 * Controls the global addons admin page.
	 */
	public function global_addons_admin() {
		if ( ! empty( $_GET['add'] ) || ! empty( $_GET['edit'] ) ) {

			if ( $_POST ) {

				// Check if all form fields have been posted.
				if ( isset( $_POST[ 'pao_post_control_var' ] ) && ! isset( $_POST[ 'pao_post_test_var' ] ) ) {

					/* translators: %1$s max_input_vars size */
					echo wp_kses_post( '<div class="notice notice-warning"><p>' . sprintf( esc_html__( 'Product Add-Ons has detected that your server may have failed to process and save some of the data on this page. Please get in touch with your server\'s host or administrator and (kindly) ask them to increase the number of variables that PHP scripts can post and process%1$s.', 'woocommerce-product-addons' ), function_exists( 'ini_get' ) && ini_get( 'max_input_vars' ) ? sprintf( __( ' (currently %s)', 'woocommerce-product-addons' ), ini_get( 'max_input_vars' ) ) : '' ) ) . '</p></div>';

				} else {
					$posted_addons_data = $this->save_global_addons();
					$edit_id            = $posted_addons_data[ 'edit_id' ];
					$reference          = $posted_addons_data[ 'reference' ];
					$priority           = $posted_addons_data[ 'priority' ];
					$objects            = $posted_addons_data[ 'objects' ];
					$product_addons     = array_filter( (array) $posted_addons_data[ 'product_addons' ] );

					if ( $edit_id ) {
						echo '<div class="updated"><p>' . esc_html__( 'Add-on saved successfully', 'woocommerce-product-addons' ) . '</p></div>';
					}
				}
			}

			if ( ! empty( $_GET['edit'] ) ) {

				$edit_id      = absint( $_GET['edit'] );
				$global_addon = get_post( $edit_id );

				if ( ! $global_addon || 'global_product_addon' !== $global_addon->post_type ) {
					echo '<div class="error"><p>' . esc_html__( 'Error: Add-on not found', 'woocommerce-product-addons' ) . '</p></div>';
					return;
				}

				$reference      = $global_addon->post_title;
				$priority       = get_post_meta( $global_addon->ID, '_priority', true );
				$objects        = (array) wp_get_post_terms( $global_addon->ID, apply_filters( 'woocommerce_product_addons_global_post_terms', array( 'product_cat' ) ), array( 'fields' => 'ids' ) );
				$product_addons = array_filter( (array) get_post_meta( $global_addon->ID, '_product_addons', true ) ); // nosemgrep: audit.php.lang.misc.array-filter-no-callback

				if ( get_post_meta( $global_addon->ID, '_all_products', true ) == 1 ) {
					$objects[] = 0;
				}
			} elseif ( ! empty( $edit_id ) ) {

				$global_addon = get_post( $edit_id );

				if ( ! $global_addon || 'global_product_addon' !== $global_addon->post_type ) {
					echo '<div class="error"><p>' . esc_html__( 'Error: Add-on not found', 'woocommerce-product-addons' ) . '</p></div>';
					return;
				}

				$reference      = $global_addon->post_title;
				$priority       = get_post_meta( $global_addon->ID, '_priority', true );
				$objects        = (array) wp_get_post_terms( $global_addon->ID, apply_filters( 'woocommerce_product_addons_global_post_terms', array( 'product_cat' ) ), array( 'fields' => 'ids' ) );
				$product_addons = array_filter( (array) get_post_meta( $global_addon->ID, '_product_addons', true ) );

				if ( get_post_meta( $global_addon->ID, '_all_products', true ) == 1 ) {
					$objects[] = 0;
				}
			} else {

				$global_addons_count = wp_count_posts( 'global_product_addon' );
				$reference           = __( 'Add-ons Group', 'woocommerce-product-addons' ) . ' #' . ( $global_addons_count->publish + 1 );
				$priority            = 1;
				$objects             = array( 0 );
				$product_addons      = array();

			}

			include( dirname( __FILE__ ) . '/views/html-global-admin-add.php' );
		} else {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			if ( ! empty( $_GET['delete'] ) && isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( wc_clean( $_REQUEST['_wpnonce'] ), 'delete_addon' ) ) {
				wp_delete_post( absint( $_GET['delete'] ), true );
				echo '<div class="updated"><p>' . esc_html__( 'Add-on deleted successfully', 'woocommerce-product-addons' ) . '</p></div>';
			}

			$table  = new WC_PAO_List_Table();
			$table->prepare_items();

			include( dirname( __FILE__ ) . '/views/html-global-admin.php' );
		}
	}

	/**
	 * Converts the field type key to display name.
	 *
	 * @since 3.0.0
	 * @param string $type
	 * @return string $name
	 */
	public function convert_type_name( $type = '' ) {
		switch ( $type ) {
			case 'checkboxes':
				$name = __( 'Checkbox', 'woocommerce-product-addons' );
				break;
			case 'custom_price':
				$name = __( 'Price', 'woocommerce-product-addons' );
				break;
			case 'input_multiplier':
				$name = __( 'Quantity', 'woocommerce-product-addons' );
				break;
			case 'custom_text':
				$name = __( 'Short Text', 'woocommerce-product-addons' );
				break;
			case 'custom_textarea':
				$name = __( 'Long Text', 'woocommerce-product-addons' );
				break;
			case 'file_upload':
				$name = __( 'File Upload', 'woocommerce-product-addons' );
				break;
			case 'select':
				$name = __( 'Dropdown', 'woocommerce-product-addons' );
				break;
			case 'multiple_choice':
			default:
				$name = __( 'Multiple Choice', 'woocommerce-product-addons' );
				break;
		}

		return $name;
	}

	/**
	 * Save global addons
	 *
	 * @return array posted addons data
	 */
	public function save_global_addons() {

		check_admin_referer( 'wc_pao_global_addons_edit' );

		$edit_id        = ! empty( $_POST['edit_id'] ) ? absint( $_POST['edit_id'] ) : '';
		$reference      = ! empty( $_POST['addon-reference'] ) ? wc_clean( wp_unslash( $_POST['addon-reference'] ) ) : '';
		$priority       = ! empty( $_POST['addon-priority'] ) ? absint( $_POST['addon-priority'] ) : 0;
		$objects        = ! empty( $_POST['addon-objects'] ) ? array_map( 'absint', $_POST['addon-objects'] ) : array();
		$product_addons = $this->get_posted_product_addons();

		if ( ! $reference ) {
			$global_addons_count = wp_count_posts( 'global_product_addon' );
			$reference           = __( 'Add-ons Group', 'woocommerce-product-addons' ) . ' #' . ( $global_addons_count->publish + 1 );
		}

		if ( ! $priority && 0 !== $priority ) {
			$priority = 10;
		}

		$data = array(
			'edit_id'        => $edit_id,
			'reference'      => $reference,
			'priority'       => $priority,
			'objects'        => $objects,
			'product_addons' => $product_addons
		);

		$edit_post = array(
			'ID'         => $edit_id,
			'post_title' => $reference,
			'post_type'  => 'global_product_addon'
		);

		if ( $edit_id ) {

			$post_type = get_post_type( $edit_id );
			if ( 'global_product_addon' !== $post_type ) {
				return false;
			}

			wp_update_post( $edit_post );
			wp_set_post_terms( $edit_id, $objects, 'product_cat', false );

			do_action( 'woocommerce_product_addons_global_edit_addons', $edit_post, $objects, $data );

		} else {

			$edit_id = wp_insert_post( apply_filters( 'woocommerce_product_addons_global_insert_post_args', array(
				'post_title'    => $reference,
				'post_status'   => 'publish',
				'post_type'     => 'global_product_addon',
				'tax_input'     => array(
					'product_cat' => $objects,
				),
			), $reference, $objects ) );

			$edit_post['ID'] = $data['edit_id'] = $edit_id;

			do_action( 'woocommerce_product_addons_global_create_addons', $edit_post, $objects, $data );
		}

		if ( in_array( 0, $objects ) ) {
			update_post_meta( $edit_id, '_all_products', 1 );
		} else {
			update_post_meta( $edit_id, '_all_products', 0 );
		}

		update_post_meta( $edit_id, '_priority', $priority );
		update_post_meta( $edit_id, '_product_addons', $product_addons );
		update_option( 'woocommerce_global_product_addons_last_modified', current_time( 'U' ) );

		return $data;
	}

	/**
	 * Add product tab.
	 */
	public function tab() {
	?>
		<li class="addons_tab product_addons hide_if_grouped hide_if_external"><a href="#product_addons_data"><span><?php esc_html_e( 'Add-ons', 'woocommerce-product-addons' ); ?></span></a></li>
	<?php
	}

	/**
	 * Add product panel.
	 */
	public function panel() {
		global $post;

		$product        = wc_get_product( $post );
		$exists         = (bool) $product->get_id();
		$product_addons = array_filter( (array) $product->get_meta( '_product_addons' ) );
		$exclude_global = $product->get_meta( '_product_addons_exclude_global' );

		include( dirname( __FILE__ ) . '/views/html-addon-panel.php' );
	}

	/**
	 * Backwards compatibility: Add a notice if an add-on title exceeds the 255 characters threshold.
	 *
	 */
	public function maybe_add_title_max_length_notice() {
		global $post_id;

		// Get admin screen ID.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product' !== $screen_id ) {
			return;
		}

		$addons = get_post_meta( $post_id, '_product_addons', true );

		foreach ( $addons as $addon ) {
			if ( isset( $addon[ 'name' ] ) && strlen( $addon[ 'name' ] ) > 255 ) {
				/* translators: %1$s: Product name */
				$notice = sprintf( __( '<strong>%1$s</strong> has at least one add-on whose <strong>Title</strong> is longer than <strong>255</strong> characters. Please adjust its value and save your changes.', 'woocommerce-product-addons' ), get_the_title( $post_id ) );
				$this->output_notice( $notice, 'warning' );
			}
		}
	}

	/**
	 * Process meta box.
	 *
	 * @param int $post_id Post ID.
	 */
	public function process_meta_box( $post_id ) {

		// Check if all addons have been posted.
		if ( isset( $_POST[ 'pao_post_control_var' ] ) && ! isset( $_POST[ 'pao_post_test_var' ] ) ) {
			$notice = sprintf( __( 'Product Add-Ons has detected that your server may have failed to process and save some of the data on this page. Please get in touch with your server\'s host or administrator and (kindly) ask them to increase the number of variables that PHP scripts can post and process%1$s.', 'woocommerce-product-addons' ), function_exists( 'ini_get' ) && ini_get( 'max_input_vars' ) ? sprintf( __( ' (currently %s)', 'woocommerce-product-addons' ), ini_get( 'max_input_vars' ) ) : '' );
			WC_PAO_Admin_Notices::add_notice( $notice, 'warning', true );
			return;
		}

		// Save addons as serialised array.
		$product_addons                = $this->get_posted_product_addons();
		$product_addons_exclude_global = isset( $_POST['_product_addons_exclude_global'] ) ? 0 : 1;
		$product = wc_get_product( $post_id );
		$product->update_meta_data( '_product_addons', $product_addons );
		$product->update_meta_data( '_product_addons_exclude_global', $product_addons_exclude_global );
		$product->save();
	}

	/**
	 * Generate a filterable default new addon option.
	 *
	 * @return array
	 */
	public static function get_new_addon_option() {
		$new_addon_option = array(
			'label'      => '',
			'image'      => '',
			'price'      => '',
			'price_type' => 'flat_fee',
		);

		return apply_filters( 'woocommerce_product_addons_new_addon_option', $new_addon_option );
	}

	/**
	 * Put posted addon data into an array.
	 *
	 * @return array
	 */
	protected function get_posted_product_addons() {
		global $post;

		$product_addons     = array();
		$current_addon_data = array();
		$current_addon_ids  = array();
		$empty_title_error  = false;
		$long_title_error   = false;

		// Product addons.
		if ( ! empty( $post ) ) {
			$current_addon_data = WC_Product_Addons_Helper::get_product_addons( $post->ID, false, true, false );

		// Global addons.
		} elseif ( ! empty( $_GET[ 'edit' ] ) ) {
			$edit_id            = absint( $_GET[ 'edit' ] );
			$global_addon       = get_post( $edit_id );
			$current_addon_data = is_a( $global_addon, 'WP_Post' ) ? array_filter( (array) get_post_meta( $global_addon->ID, '_product_addons', true ) ) : array(); // nosemgrep: audit.php.lang.misc.array-filter-no-callback
		}

		foreach ( $current_addon_data as $addon ) {
			if ( isset( $addon[ 'id' ] ) ) {
				$current_addon_ids[] = $addon[ 'id' ];
			}
		}

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( isset( $_POST['product_addon_name'] ) ) {
			$addon_name               = $_POST['product_addon_name' ];
			$addon_title_format       = isset( $_POST['product_addon_title_format'] ) ? $_POST['product_addon_title_format'] : array();
			$addon_description_enable = isset( $_POST['product_addon_description_enable'] ) ? $_POST['product_addon_description_enable'] : array();
			$addon_description        = isset( $_POST['product_addon_description'] ) ? $_POST['product_addon_description'] : array();
			$addon_type               = isset( $_POST['product_addon_type'] ) ?  $_POST['product_addon_type'] : array();
			$addon_display            = isset( $_POST['product_addon_display'] ) ? $_POST['product_addon_display'] : array();
			$addon_position           = isset( $_POST['product_addon_position'] ) ? $_POST['product_addon_position'] : array();
			$addon_required           = isset( $_POST['product_addon_required'] ) ?  $_POST['product_addon_required'] : array();
			$addon_option_label       = isset( $_POST['product_addon_option_label'] ) ? $_POST['product_addon_option_label'] : array();
			$addon_option_price       = isset( $_POST['product_addon_option_price'] ) ? $_POST['product_addon_option_price'] : array();
			$addon_option_price_type  = isset( $_POST['product_addon_option_price_type'] ) ? $_POST['product_addon_option_price_type'] : array();
			$addon_option_image       = isset( $_POST['product_addon_option_image'] ) ? $_POST['product_addon_option_image'] : array();
			$addon_restrictions       = isset( $_POST['product_addon_restrictions'] ) ? $_POST['product_addon_restrictions'] : array();
			$addon_restrictions_type  = isset( $_POST['product_addon_restrictions_type'] ) ? $_POST['product_addon_restrictions_type'] : array();
			$addon_adjust_price       = isset( $_POST['product_addon_adjust_price'] ) ? $_POST['product_addon_adjust_price'] : array();
			$addon_price_type         = isset( $_POST['product_addon_price_type'] ) ? $_POST['product_addon_price_type'] : array();
			$addon_price              = isset( $_POST['product_addon_price'] ) ? $_POST['product_addon_price'] : array();
			$addon_min                = isset( $_POST['product_addon_min'] ) ? $_POST['product_addon_min'] : array();
			$addon_max                = isset( $_POST['product_addon_max'] ) ? $_POST['product_addon_max'] : array();
			$id                       = isset( $_POST['product_addon_id_'] ) ? $_POST['product_addon_id_'] : array();
			// phpcs:enable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized end

			for ( $i = 0; $i < count( $addon_name ); $i++ ) {
				if ( ! isset( $addon_name[ $i ] ) || ( '' == $addon_name[ $i ] ) ) {
					$empty_title_error = true;
					$addon_name[ $i ] = '-';
				}

				if ( strlen( $addon_name[ $i ] ) > 255 ) {
					$long_title_error = true;
					$addon_name[ $i ] = substr( $addon_name[ $i ], 0, 255 );
				}

				$addon_options = array();

				if ( isset( $addon_option_label[ $i ] ) ) {
					$option_label      = $addon_option_label[ $i ];
					$option_price      = $addon_option_price[ $i ];
					$option_price_type = $addon_option_price_type[ $i ];
					$option_image      = $addon_option_image[ $i ];

					for ( $ii = 0; $ii < count( $option_label ); $ii++ ) {
						$label      = sanitize_text_field( wp_unslash( $option_label[ $ii ] ) );
						$price      = wc_format_decimal( sanitize_text_field( wp_unslash( $option_price[ $ii ] ) ) );
						$image      = sanitize_text_field( wp_unslash( $option_image[ $ii ] ) );
						$price_type = sanitize_text_field( wp_unslash( $option_price_type[ $ii ] ) );

						$addon_options[] = array(
							'label'      => $label,
							'price'      => $price,
							'image'      => $image,
							'price_type' => $price_type,
						);
					}
				}

				$data                       = array();
				$data['name']               = sanitize_text_field( wp_unslash( $addon_name[ $i ] ) );
				$data['title_format']       = sanitize_text_field( wp_unslash( $addon_title_format[ $i ] ) );
				$data['description_enable'] = isset( $addon_description_enable[ $i ] ) ? 1 : 0;
				$data['description']        = wp_kses_post( wp_unslash( $addon_description[ $i ] ) );
				$data['type']               = sanitize_text_field( wp_unslash( $addon_type[ $i ] ) );
				$data['display']            = sanitize_text_field( wp_unslash( $addon_display[ $i ] ) );
				$data['position']           = absint( $addon_position[ $i ] );
				$data['required']           = isset( $addon_required[ $i ] ) ? 1 : 0;
				$data['restrictions']       = isset( $addon_restrictions[ $i ] ) ? 1 : 0;
				$data['restrictions_type']  = sanitize_text_field( wp_unslash( $addon_restrictions_type[ $i ] ) );
				$data['adjust_price']       = isset( $addon_adjust_price[ $i ] ) ? 1 : 0;
				$data['price_type']         = sanitize_text_field( wp_unslash( $addon_price_type[ $i ] ) );
				$data['price']              = wc_format_decimal( sanitize_text_field( wp_unslash( $addon_price[ $i ] ) ) );
				$data['min']                = (float) sanitize_text_field( wp_unslash( $addon_min[ $i ] ) );
				$data['max']                = (float) sanitize_text_field( wp_unslash( $addon_max[ $i ] ) );
				$data['id']                 = isset( $id[ $i ] ) && ! empty( $id[ $i ] ) ? $id[ $i ] : $this->generate_id( $current_addon_ids );

				if ( ! empty( $addon_options ) ) {
					$data['options'] = $addon_options;
				}

				// Always use quantity based price type for custom price.
				if ( 'custom_price' === $data['type'] ) {
					$data['price_type'] = 'quantity_based';
				}

				// Add to array.
				$product_addons[] = apply_filters( 'woocommerce_product_addons_save_data', $data, $i );
			}
		}

		if ( $empty_title_error ) {
			WC_PAO_Admin_Notices::add_notice( __( 'Some add-ons could not be saved correctly because their <strong>Title</strong> was not set. Please review your add-ons configuration and update this product.', 'woocommerce-product-addons' ), 'error', true );
		}

		if ( $long_title_error ) {
			WC_PAO_Admin_Notices::add_notice( __( 'Some add-ons could not be saved correctly because their <strong>Title</strong> was longer than 255 characters. Please review your add-ons configuration and update this product.', 'woocommerce-product-addons' ), 'error', true );
		}

		uasort( $product_addons, array( $this, 'addons_cmp' ) );

		return $product_addons;
	}

	/**
	 * Sanitize the addon.
	 *
	 * @since 3.0.36
	 * @param array $addon Array containing the addon data.
	 * @return array
	 */
	public function sanitize_addon( $addon ) {
		$sanitized = array(
			'name'               => sanitize_text_field( $addon['name'] ),
			'title_format'       => sanitize_text_field( $addon['title_format'] ),
			'description_enable' => ! empty( $addon['description_enable'] ) ? 1 : 0,
			'description'        => wp_kses_post( $addon['description'] ),
			'type'               => sanitize_text_field( $addon['type'] ),
			'display'            => sanitize_text_field( $addon['display'] ),
			'position'           => absint( $addon['position'] ),
			'required'           => ! empty( $addon['required'] ) ? 1 : 0,
			'restrictions'       => ! empty( $addon['restrictions'] ) ? 1 : 0,
			'restrictions_type'  => sanitize_text_field( $addon['restrictions_type'] ),
			'adjust_price'       => ! empty( $addon['adjust_price'] ) ? 1 : 0,
			'price_type'         => sanitize_text_field( $addon['price_type'] ),
			'price'              => wc_format_decimal( sanitize_text_field( $addon['price'] ) ),
			'min'                => (float) sanitize_text_field( $addon['min'] ),
			'max'                => (float) sanitize_text_field( $addon['max'] ),
			'id'                 => isset( $addon['id'] ) ? absint( $addon['id'] ) : 0,
		);

		if ( isset( $addon[ 'options' ] ) && is_array( $addon['options'] ) ) {
			$sanitized['options'] = array();

			foreach ( $addon['options'] as $key => $option ) {
				$sanitized['options'][ $key ] = array(
					'label'      => sanitize_text_field( $option['label'] ),
					'price'      => wc_format_decimal( sanitize_text_field( $option['price'] ) ),
					'image'      => sanitize_text_field( $option['image'] ),
					'price_type' => sanitize_text_field( $option['price_type'] ),
				);
			}
		}

		return $sanitized;
	}

	/**
	 * Filters the admin order hidden metas to hide addons.
	 *
	 * @since 3.0.0
	 * @param array $hidden_metas
	 */
	public function filter_hidden_order_itemmeta( $hidden_metas ) {
		$hidden_metas[] = '_wc_pao_addon_name';
		$hidden_metas[] = '_wc_pao_addon_value';
		$hidden_metas[] = '_wc_pao_addon_field_type';
		$hidden_metas[] = '_reduced_stock';

		return $hidden_metas;
	}

	/**
	 * Filters the admin order line item to show only addons.
	 *
	 * @since 3.0.0
	 * @param int $item_id
	 * @param object $item
	 * @param object $order
	 */
	public function filter_order_line_item_html( $item_id, $item, $order ) {
		$is_addon = ! empty( $item->get_meta( '_wc_pao_addon_value', true ) );

		if ( $is_addon ) {
			ob_start();
		}
	}

	/**
	 * Filters the admin order line item to show only addons.
	 *
	 * @since 3.0.0
	 * @param int $item_id
	 * @param object $item
	 * @param object $order
	 */
	public function filter_order_line_item_after_html( $item_id, $item, $order ) {
		$addon_name  = $item->get_meta( '_wc_pao_addon_name', true );
		$addon_value = $item->get_meta( '_wc_pao_addon_value', true );

		$is_addon = ! empty( $addon_value );

		if ( $is_addon ) {
			$product      = $item->get_product();
			$product_link = $product ? admin_url( 'post.php?post=' . $item->get_product_id() . '&action=edit' ) : '';
			$thumbnail    = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';

			$addon_html = ob_get_clean();

			$addon_html = str_replace( '<div class="wc-order-item-thumbnail">' . wp_kses_post( $thumbnail ) . '</div>', '', $addon_html );

			$addon_html = str_replace( $product_link ? '<a href="' . esc_url( $product_link ) . '" class="wc-order-item-name">' . esc_html( $item->get_name() ) . '</a>' : '<div class="wc-order-item-name">' . esc_html( $item->get_name() ) . '</div>', '<div class="wc-order-item-name"><div class="wc-pao-order-item-name"><strong>' . esc_html( $addon_name ) . '</strong></div><div class="wc-pao-order-item-value">' . esc_html( $addon_value ) . '</div></div>', $addon_html );

			$addon_html = str_replace( '"display_meta"', '"display_meta" style="display:none;"', $addon_html );

			if ( $product && $product->get_sku() ) {
				$addon_html = str_replace( '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'woocommerce-product-addons' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>', '', $addon_html );
			}

			// Variations.
			if ( $item->get_variation_id() ) {
				if ( 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
					$var_id = esc_html( $item->get_variation_id() );
				} else {
					/* translators: %s ID of variation */
					$var_id = sprintf( esc_html__( '%s (No longer exists)', 'woocommerce-product-addons' ), esc_html( $item->get_variation_id() ) );
				}

				$addon_html = str_replace( '<div class="wc-order-item-variation"><strong>' . esc_html__( 'Variation ID:', 'woocommerce-product-addons' ) . '</strong> ' . $var_id . '</div>', '', $addon_html );
			}

			echo $addon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Sort addons.
	 *
	 * @param  array $a First item to compare.
	 * @param  array $b Second item to compare.
	 * @return bool
	 */
	protected function addons_cmp( $a, $b ) {
		if ( $a['position'] == $b['position'] ) {
			return 0;
		}

		return ( $a['position'] < $b['position'] ) ? -1 : 1;
	}

	/**
	 * Generate a unique timestamp and use it as id.
	 *
	 * @since  5.0.1
	 *
	 * @param  array  $existing_ids
	 * @return int
	 */
	protected function generate_id( $existing_ids ) {

		$generated_id    = current_time( 'timestamp' );
		$blacklisted_ids = array_merge( $existing_ids, $this->generated_ids );
		$found_unique_id = false;

		while ( ! $found_unique_id ) {
			$generated_id++;
			if ( ! in_array( $generated_id, $blacklisted_ids ) ) {
				$found_unique_id = true;
			}
		}

		$this->generated_ids[] = $generated_id;

		return $generated_id;
	}

	/**
	 * Prints warning messages in the admin area.
	 *
	 * @param string $content
	 * @param string $type
	 * @return void
	 */
	public function output_notice( $content, $type ) {
		echo '<div class="notice notice-' . esc_attr( $type ) . '">';
		echo wpautop( wp_kses_post( $content ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}
}
