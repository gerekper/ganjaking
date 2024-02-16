<?php
/**
 * A WC_Bookings_Templates class file.
 *
 * @package WooCommerce Bookings
 */

/**
 * WC_Bookings_Templates.
 */
class WC_Bookings_Templates {

	const SOURCE_TEMPLATE_META_KEY = '_wc_bookings_source_template';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'wc_bookings_templates_page_redirect' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 48 );

		// Ajax handler to create a bookable products from template.
		add_action( 'wp_ajax_wc_bookings_get_product_template', array( $this, 'wc_bookings_get_product_template' ) );

		/**
		 * Option key name for triggering upgrade notices.
		 *
		 * @since 2.0.0
		 */
		if ( ! defined( 'WC_BOOKINGS_UPGRADE_NOTICE_KEY' ) ) {
			define( 'WC_BOOKINGS_UPGRADE_NOTICE_KEY', 'woocommerce_bookings_show_upgrade_notice' );
		}

		/*
		* Show upgrade notice.
		*
		* Large priority ensures this occurs after WooCommerce Admin has loaded.
		*/
		add_action( 'upgrader_process_complete', array( $this, 'woocommerce_bookings_upgrade' ) );
		add_action( 'plugins_loaded', array( $this, 'show_upgrade_notice' ), 100 );
	}

	/**
	 * Shows admin notice after the plugin upgrade.
	 *
	 * Notices are triggered by a flag in options so they can be triggered once after upgrade
	 * and then actually shown once all necessary resources have been loaded.
	 *
	 * @since 2.0.0
	 *
	 * @return false|void false when option key not set.
	 */
	public function show_upgrade_notice() {
		// Return if option key not set.
		if ( false === get_option( WC_BOOKINGS_UPGRADE_NOTICE_KEY ) ) {
			return false;
		}

		delete_option( WC_BOOKINGS_UPGRADE_NOTICE_KEY );

		$url         = admin_url( 'edit.php?post_type=wc_booking&page=wc_bookings_product_templates' );
		$notice_html = sprintf(
			/* translators: 1: Anchor tag open tag html 2: Anchor tag close tag html */
			esc_html__(
				'Bookings now includes bookable product templates that can be imported to more easily set up new bookable products. %1$sClick here%2$s to learn more about them.',
				'woocommerce-bookings'
			),
			'<a href="' . esc_url( $url ) . '" target="_blank">',
			'</a>'
		);

		WC_Admin_Notices::add_custom_notice( 'woocommerce_bookings_upgrade', $notice_html );
	}

	/**
	 * Upgrade hook.
	 *
	 * @since 2.0.0
	 */
	public function woocommerce_bookings_upgrade() {
		// Flag to trigger upgrade notice.
		update_option( WC_BOOKINGS_UPGRADE_NOTICE_KEY, true );
	}

	/**
	 * Redirect users to the templates screen on plugin activation.
	 *
	 * @since 2.0.0
	 */
	public function wc_bookings_templates_page_redirect() {
		if ( ! get_option( 'wc_bookings_show_templates_on_activation' ) ) {
			add_option( 'wc_bookings_show_templates_on_activation', true );
			wp_safe_redirect( admin_url( 'edit.php?post_type=wc_booking&page=wc_bookings_product_templates' ) );
			exit;
		}
	}

	/**
	 * Add a submenu for managing bookings pages.
	 *
	 * @since 2.0.0
	 */
	public function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=wc_booking',
			__( 'Add Product', 'woocommerce-bookings' ),
			__( 'Add Product', 'woocommerce-bookings' ),
			'edit_wc_bookings',
			'wc_bookings_product_templates',
			array( $this, 'product_templates' )
		);
	}

	/**
	 * A Templates page content.
	 *
	 * @since 2.0.0
	 */
	public function product_templates() {
		// Enqueue JS.
		wp_enqueue_script( 'wc_bookings_admin_js' );

		// Get the templates data.
		$product_data = $this->wc_bookings_get_template_data();

		include 'views/html-product-templates-page.php';
	}

	/**
	 * Get the templates data.
	 *
	 * @since 2.0.0
	 *
	 * @return array of the templates data.
	 */
	public function wc_bookings_get_template_data() {
		// Read the JSON file contents.
		$json_data = file_get_contents( WC_BOOKINGS_ABSPATH . 'includes/admin/product-templates.json' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		// Decode the JSON data into an array.
		return json_decode( $json_data, true );
	}

	/**
	 * Create a product and return its edit page URL.
	 *
	 * @since 2.0.0
	 *
	 * Take posted booking form values and then use these to quote a price for what has been chosen.
	 * Returns a string which is appended to the booking form.
	 */
	public function wc_bookings_get_product_template() {
		$nonce = isset( $_POST['security'] ) ? wc_clean( wp_unslash( $_POST['security'] ) ) : false;

		if ( $nonce && ! wp_verify_nonce( $nonce, 'wc_bookings_get_product_template' ) ) {
			// This nonce is not valid.
			wp_send_json_error( esc_html__( 'Please refresh the page and try again.', 'woocommerce-bookings' ) );
		}

		$index = wc_clean( wp_unslash( $_POST['index'] ?? 0 ) );
		$slug  = wc_clean( wp_unslash( $_POST['slug'] ?? '' ) );

		// Get the templates data.
		$product_id   = 0;
		$product_data = $this->wc_bookings_get_template_data();
		if ( isset( $product_data['products'] ) ) {
			if ( $product_data['products'][ $index ] ) {
				$product_data = $product_data['products'][ $index ];

				if ( $product_data['slug'] === $slug ) {
					$product_id = $this->wc_bookings_create_product( $product_data );
				}
			}
		}

		// Send the output.
		if ( $product_id > 0 ) {
			$product_url = get_edit_post_link( $product_id );
			$product_url = html_entity_decode( $product_url );
			wp_send_json_success( $product_url );
		} else {
			wp_send_json_error( esc_html__( 'Something went wrong, please try again.', 'woocommerce-bookings' ), null );
		}
	}

	/**
	 * Creates a bookable product.
	 *
	 * @since 2.0.0
	 *
	 * @param array $product_data the product data from the JSON.
	 *
	 * @return int newly created product's ID.
	 */
	public function wc_bookings_create_product( $product_data ) {
		// Create a new product.
		$product = new WC_Product_Booking();

		$product->add_meta_data( self::SOURCE_TEMPLATE_META_KEY, $product_data['slug'] );
		unset( $product_data['slug'] );

		// Set a draft status.
		$product->set_status( 'draft' );

		// Set the product type to bookable.
		$product->set_virtual( isset( $product_data['is_virtual'] ) ? $product_data['is_virtual'] : false );

		// Update the dates to the current month and year.
		$product_data = $this->wc_bookings_update_dates( $product_data );

		$product->set_props( $product_data );

		// Convert the person types.
		$product_persons = $this->convert_person_types( $product );

		$product = isset( $product_persons[0] ) ? $product_persons[0] : $product;

		// Create resources.
		if ( isset( $product_data['resources'] ) ) {
			$resource_ids         = array();
			$resource_base_costs  = array();
			$resource_block_costs = array();
			foreach ( $product_data['resources'] as $res_data ) {
				$resource = new WC_Product_Booking_Resource();
				$resource->set_props( $res_data );
				$new_resource_id = $resource->save();
				$resource_ids[]  = $new_resource_id;

				$resource_base_costs[ $new_resource_id ]  = $res_data['base_cost'];
				$resource_block_costs[ $new_resource_id ] = $res_data['block_cost'];
			}
			if ( count( $resource_ids ) > 0 ) {
				$product->set_resource_ids( $resource_ids );
				$product->set_resource_base_costs( $resource_base_costs );
				$product->set_resource_block_costs( $resource_block_costs );
			}
		}

		// Add details in the product description.
		$image_url    = $product_data['product_thumbnail'] ? WC_BOOKINGS_PLUGIN_URL . '/dist/images/product-templates/' . $product_data['product_thumbnail'] : '';
		$description  = isset( $image_url ) ? '<img src="' . esc_url( $image_url ) . '" width="400"><br><br>' : '';
		$description .= isset( $product_data['scenario'] ) ? $product_data['scenario'] . '<br>' : '';

		if ( isset( $product_data['features_utilized'] ) && is_array( $product_data['features_utilized'] ) && count( $product_data['features_utilized'] ) > 0 ) {
			$features_utilized = $product_data['features_utilized'];
			$description      .= '<h4>Features Utilized</h4>';
			$description      .= '<ul>';
			foreach ( $features_utilized as $feature ) {
				$description .= '<li>' . $feature . '</li>';
			}
			$description .= '</ul>';
		}

		$product->set_description( wp_kses_post( $description ) );

		// Save the product.
		$product_id = $product->save();

		// Attach the person types with the product.
		$person_types = isset( $product_persons[1] ) ? $product_persons[1] : array();
		if ( count( $person_types ) > 0 ) {
			foreach ( $person_types as $person_type ) {
				$person_type->set_parent_id( $product_id );
				$person_type->save();
			}
		}

		return $product_id;
	}

	/**
	 * This function changes the from and to dates to the current month and year.
	 * It keeps the day (i.e. 'd') same as received.
	 *
	 * @since 2.0.0
	 *
	 * @param array $product_data a bookable product data.
	 *
	 * @return array $product_data updated bookable product data.
	 */
	public function wc_bookings_update_dates( $product_data ) {
		// Get the current year and month.
		$current_year  = date( 'Y' );
		$current_month = date( 'm' );

		foreach ( array( 'availability', 'pricing' ) as $type ) {
			if ( isset( $product_data[ $type ] ) ) {
				foreach ( $product_data[ $type ] as $key => $data ) {
					if ( 'custom' === $data['type'] ) {
						foreach ( array( 'from', 'to' ) as $from_to ) {
							// Split the 'from' date into year, month, and day components.
							list( $year, $month, $day ) = explode( '-', $data[ $from_to ] );

							// Combine the updated year, and month components into a new date string.
							$new_date = implode( '-', array( $current_year, $current_month, $day ) );

							// Update the date with the new date string.
							$product_data[ $type ][ $key ][ $from_to ] = $new_date;
						}
					}
				}
			}
		}

		return $product_data;
	}

	/**
	 * This function converts the person type info from a serialized array to the required
	 * objects before we save the product.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Product_Booking $product a bookable product.
	 *
	 * @return array WC_Product_Booking updated object & Created Person Types.
	 */
	public function convert_person_types( $product ) {
		if ( ! is_callable( array( $product, 'has_person_types' ) )
			|| ! $product->has_person_types() ) {
			return array( $product, array() );
		}

		$import_types = $product->get_person_types();

		$person_types = array();
		foreach ( $import_types as $person_type_data ) {
			$person_type = new WC_Product_Booking_Person_Type();
			$person_type->set_block_cost( $person_type_data['block_cost'] );
			$person_type->set_cost( $person_type_data['cost'] );
			$person_type->set_description( $person_type_data['description'] );
			$person_type->set_max( $person_type_data['max'] );
			$person_type->set_min( $person_type_data['min'] );
			$person_type->set_name( $person_type_data['name'] );
			$person_type->set_parent_id( $product->get_id() );
			$person_type->set_sort_order( $person_type_data['sort_order'] );
			$person_type->save();

			$person_types[] = $person_type;
		}

		$product->set_person_types( $person_types );

		return array( $product, $person_types );
	}
}
