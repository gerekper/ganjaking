<?php
/**
 * Product Add-ons admin
 *
 * @package WC_Product_Addons/Classes/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product_Addon_Admin class.
 */
class WC_Product_Addons_Admin {

	/**
	 * Initialize administrative actions.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'script_styles' ), 100 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id' ) );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_meta_box' ), 1 );

		// Addon order display.
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'filter_hidden_order_itemmeta' ) );
		add_action( 'woocommerce_before_order_item_line_item_html', array( $this, 'filter_order_line_item_html' ), 10, 3 );
		add_action( 'woocommerce_order_item_line_item_html', array( $this, 'filter_order_line_item_after_html' ), 10, 3 );

		add_action( 'wp_ajax_wc_pao_get_addon_options', array( $this, 'ajax_get_addon_options' ) );
		add_action( 'wp_ajax_wc_pao_get_addon_field', array( $this, 'ajax_get_addon_field' ) );
	}

	/**
	 * Add menus
	 */
	public function admin_menu() {
		$page = add_submenu_page( 'edit.php?post_type=product', __( 'Add-ons', 'woocommerce-product-addons' ), __( 'Add-ons', 'woocommerce-product-addons' ), 'manage_woocommerce', 'addons', array( $this, 'global_addons_admin' ) );
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
		$addon                       = array();
		$addon['name']               = '';
		$addon['title_format']       = 'label';
		$addon['description_enable'] = '';
		$addon['description']        = '';
		$addon['required']           = '';
		$addon['type']               = 'multiple_choice';
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

		$loop = "{loop}";

		include( WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/admin/views/html-addon.php' );

		$html = ob_get_clean();

		$html = str_replace( array( "\n", "\r" ), '', str_replace( "'", '"', $html ) );

		wp_send_json( array( 'html' => $html ) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 3.0.0
	 */
	public function script_styles() {
		if (
			'product_page_addons' !== get_current_screen()->id &&
			'product'             !== get_current_screen()->id &&
			'shop_order'          !== get_current_screen()->id &&
			'shop_subscription'   !== get_current_screen()->id
		) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'woocommerce_product_addons_css', WC_PRODUCT_ADDONS_PLUGIN_URL . '/assets/css/admin.css', array(), WC_PRODUCT_ADDONS_VERSION );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'woocommerce_product_addons', plugins_url( 'assets/js/admin' . $suffix . '.js', WC_PRODUCT_ADDONS_MAIN_FILE ), array( 'jquery' ), WC_PRODUCT_ADDONS_VERSION, true );

		$params = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => array(
				'get_addon_options' => wp_create_nonce( 'wc-pao-get-addon-options' ),
				'get_addon_field'   => wp_create_nonce( 'wc-pao-get-addon-field' ),
			),
			'i18n'     => array(
				'required_fields'       => __( 'All fields must have a title and/or option name. Please review the settings highlighted in red border.', 'woocommerce-product-addons' ),
				'limit_price_range'         => __( 'Limit price range', 'woocommerce-product-addons' ),
				'limit_quantity_range'      => __( 'Limit quantity range', 'woocommerce-product-addons' ),
				'limit_character_length'    => __( 'Limit character length', 'woocommerce-product-addons' ),
				'restrictions'              => __( 'Restrictions', 'woocommerce-product-addons' ),
				'confirm_remove_addon'      => __( 'Are you sure you want remove this add-on field?', 'woocommerce-product-addons' ),
				'confirm_remove_option'     => __( 'Are you sure you want delete this option?', 'woocommerce-product-addons' ),
				'add_image_swatch'          => __( 'Add Image Swatch', 'woocommerce-product-addons' ),
				'add_image'                 => __( 'Add Image', 'woocommerce-product-addons' ),
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
		$screen_ids[] = 'product_page_addons';

		return $screen_ids;
	}

	/**
	 * Controls the global addons admin page.
	 */
	public function global_addons_admin() {
		if ( ! empty( $_GET['add'] ) || ! empty( $_GET['edit'] ) ) {

			if ( $_POST ) {
				$edit_id = $this->save_global_addons();

				if ( $edit_id ) {
					echo '<div class="updated"><p>' . esc_html__( 'Add-on saved successfully', 'woocommerce-product-addons' ) . '</p></div>';
				}

				$reference      = wc_clean( $_POST['addon-reference'] );
				$priority       = absint( $_POST['addon-priority'] );
				$objects        = ! empty( $_POST['addon-objects'] ) ? array_map( 'absint', $_POST['addon-objects'] ) : array();
				$product_addons = array_filter( (array) $this->get_posted_product_addons() );
			}

			if ( ! empty( $_GET['edit'] ) ) {

				$edit_id      = absint( $_GET['edit'] );
				$global_addon = get_post( $edit_id );

				if ( ! $global_addon ) {
					echo '<div class="error">' . esc_html__( 'Error: Add-on not found', 'woocommerce-product-addons' ) . '</div>';
					return;
				}

				$reference      = $global_addon->post_title;
				$priority       = get_post_meta( $global_addon->ID, '_priority', true );
				$objects        = (array) wp_get_post_terms( $global_addon->ID, apply_filters( 'woocommerce_product_addons_global_post_terms', array( 'product_cat' ) ), array( 'fields' => 'ids' ) );
				$product_addons = array_filter( (array) get_post_meta( $global_addon->ID, '_product_addons', true ) );

				if ( get_post_meta( $global_addon->ID, '_all_products', true ) == 1 ) {
					$objects[] = 0;
				}
			} elseif ( ! empty( $edit_id ) ) {

				$global_addon   = get_post( $edit_id );
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
				$priority            = 10;
				$objects             = array( 0 );
				$product_addons      = array();

			}

			include( dirname( __FILE__ ) . '/views/html-global-admin-add.php' );
		} else {

			if ( ! empty( $_GET['delete'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'delete_addon' ) ) {
				wp_delete_post( absint( $_GET['delete'] ), true );
				echo '<div class="updated"><p>' . esc_html__( 'Add-on deleted successfully', 'woocommerce-product-addons' ) . '</p></div>';
			}

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
	 * @return bool success or failure
	 */
	public function save_global_addons() {
		$edit_id        = ! empty( $_POST['edit_id'] ) ? absint( $_POST['edit_id'] ) : '';
		$reference      = wc_clean( $_POST['addon-reference'] );
		$priority       = absint( $_POST['addon-priority'] );
		$objects        = ! empty( $_POST['addon-objects'] ) ? array_map( 'absint', $_POST['addon-objects'] ) : array();
		$product_addons = $this->get_posted_product_addons();

		if ( ! $reference ) {
			$global_addons_count = wp_count_posts( 'global_product_addon' );
			$reference           = __( 'Add-ons Group', 'woocommerce-product-addons' ) . ' #' . ( $global_addons_count->publish + 1 );
		}

		if ( ! $priority && 0 !== $priority ) {
			$priority = 10;
		}

		if ( $edit_id ) {

			$edit_post               = array();
			$edit_post['ID']         = $edit_id;
			$edit_post['post_title'] = $reference;

			wp_update_post( $edit_post );
			wp_set_post_terms( $edit_id, $objects, 'product_cat', false );
			do_action( 'woocommerce_product_addons_global_edit_addons', $edit_post, $objects );

		} else {

			$edit_id = wp_insert_post( apply_filters( 'woocommerce_product_addons_global_insert_post_args', array(
				'post_title'    => $reference,
				'post_status'   => 'publish',
				'post_type'     => 'global_product_addon',
				'tax_input'     => array(
					'product_cat' => $objects,
				),
			), $reference, $objects ) );
		}

		if ( in_array( 0, $objects ) ) {
			update_post_meta( $edit_id, '_all_products', 1 );
		} else {
			update_post_meta( $edit_id, '_all_products', 0 );
		}

		update_post_meta( $edit_id, '_priority', $priority );
		update_post_meta( $edit_id, '_product_addons', $product_addons );

		return $edit_id;
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
	 * Process meta box.
	 *
	 * @param int $post_id Post ID.
	 */
	public function process_meta_box( $post_id ) {
		// Save addons as serialised array.
		$product_addons                = $this->get_posted_product_addons();
		$product_addons_exclude_global = isset( $_POST['_product_addons_exclude_global'] ) ? 1 : 0;
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
		$product_addons = array();

		if ( isset( $_POST['product_addon_name'] ) ) {
			$addon_name               = $_POST['product_addon_name'];
			$addon_title_format       = $_POST['product_addon_title_format'];
			$addon_description_enable = isset( $_POST['product_addon_description_enable'] ) ? $_POST['product_addon_description_enable'] : array();
			$addon_description        = $_POST['product_addon_description'];
			$addon_type               = $_POST['product_addon_type'];
			$addon_display            = $_POST['product_addon_display'];
			$addon_position           = $_POST['product_addon_position'];
			$addon_required           = isset( $_POST['product_addon_required'] ) ? $_POST['product_addon_required'] : array();
			$addon_option_label       = $_POST['product_addon_option_label'];
			$addon_option_price       = $_POST['product_addon_option_price'];
			$addon_option_price_type  = $_POST['product_addon_option_price_type'];
			$addon_option_image       = $_POST['product_addon_option_image'];
			$addon_restrictions       = isset( $_POST['product_addon_restrictions'] ) ? $_POST['product_addon_restrictions'] : array();
			$addon_restrictions_type  = $_POST['product_addon_restrictions_type'];
			$addon_adjust_price       = isset( $_POST['product_addon_adjust_price'] ) ? $_POST['product_addon_adjust_price'] : array();
			$addon_price_type         = $_POST['product_addon_price_type'];
			$addon_price              = $_POST['product_addon_price'];
			$addon_min                = $_POST['product_addon_min'];
			$addon_max                = $_POST['product_addon_max'];

			for ( $i = 0; $i < count( $addon_name ); $i++ ) {
				if ( ! isset( $addon_name[ $i ] ) || ( '' == $addon_name[ $i ] ) ) {
					continue;
				}

				$addon_options = array();

				if ( isset( $addon_option_label[ $i ] ) ) {
					$option_label      = $addon_option_label[ $i ];
					$option_price      = $addon_option_price[ $i ];
					$option_price_type = $addon_option_price_type[ $i ];
					$option_image      = $addon_option_image[ $i ];

					for ( $ii = 0; $ii < count( $option_label ); $ii++ ) {
						$label      = sanitize_text_field( stripslashes( $option_label[ $ii ] ) );
						$price      = wc_format_decimal( sanitize_text_field( stripslashes( $option_price[ $ii ] ) ) );
						$image      = sanitize_text_field( stripslashes( $option_image[ $ii ] ) );
						$price_type = sanitize_text_field( stripslashes( $option_price_type[ $ii ] ) );

						$addon_options[] = array(
							'label'      => $label,
							'price'      => $price,
							'image'      => $image,
							'price_type' => $price_type,
						);
					}
				}

				$data                       = array();
				$data['name']               = sanitize_text_field( stripslashes( $addon_name[ $i ] ) );
				$data['title_format']       = sanitize_text_field( stripslashes( $addon_title_format[ $i ] ) );
				$data['description_enable'] = isset( $addon_description_enable[ $i ] ) ? 1 : 0;
				$data['description']        = wp_kses_post( stripslashes( $addon_description[ $i ] ) );
				$data['type']               = sanitize_text_field( stripslashes( $addon_type[ $i ] ) );
				$data['display']            = sanitize_text_field( stripslashes( $addon_display[ $i ] ) );
				$data['position']           = absint( $addon_position[ $i ] );
				$data['required']           = isset( $addon_required[ $i ] ) ? 1 : 0;
				$data['restrictions']       = isset( $addon_restrictions[ $i ] ) ? 1 : 0;
				$data['restrictions_type']  = sanitize_text_field( stripslashes( $addon_restrictions_type[ $i ] ) );
				$data['adjust_price']       = isset( $addon_adjust_price[ $i ] ) ? 1 : 0;
				$data['price_type']         = sanitize_text_field( stripslashes( $addon_price_type[ $i ] ) );
				$data['price']              = wc_format_decimal( sanitize_text_field( stripslashes( $addon_price[ $i ] ) ) );
				$data['min']                = (float) sanitize_text_field( stripslashes( $addon_min[ $i ] ) );
				$data['max']                = (float) sanitize_text_field( stripslashes( $addon_max[ $i ] ) );

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

		if ( ! empty( $_POST['import_product_addon'] ) ) {
			$import_addons = maybe_unserialize( maybe_unserialize( stripslashes( trim( $_POST['import_product_addon'] ) ) ) );

			if ( is_array( $import_addons ) && sizeof( $import_addons ) > 0 ) {
				$valid = true;

				foreach ( $import_addons as $addon ) {
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
				}

				if ( $valid ) {
					$product_addons = array_merge( $product_addons, $import_addons );
				}
			}
		}

		uasort( $product_addons, array( $this, 'addons_cmp' ) );

		return $product_addons;
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
					$var_id = sprintf( esc_html__( '%s (No longer exists)', 'woocommerce-product-addons' ), $item->get_variation_id() );
				}

				$addon_html = str_replace( '<div class="wc-order-item-variation"><strong>' . esc_html__( 'Variation ID:', 'woocommerce-product-addons' ) . '</strong> ' . $var_id . '</div>', '', $addon_html );
			}

			echo $addon_html;
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
}
