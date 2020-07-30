<?php
/**
 * Product Data Metabox Class
 *
 * @author   Kathy Darling
 * @category Admin
 * @package  WooCommerce Mix and Match Products/Admin/Meta-Boxes/Product
 * @since    1.2.0
 * @version  1.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formerly the WC_Mix_and_Match_Admin_Meta_Boxes class.
 * Renamed in 1.7.0.
 */
class_alias( 'WC_MNM_Meta_Box_Product_Data', 'WC_Mix_and_Match_Admin_Meta_Boxes' );

/**
 * WC_MNM_Meta_Box_Product_Data Class.
 *
 * Adds and save product meta.
 */
class WC_MNM_Meta_Box_Product_Data {

	/**
	 * Bootstraps the class and hooks required.
	 */
	public static function init() {

		// Per-item pricing and shipping options.
		add_filter( 'product_type_options', array( __CLASS__, 'type_options' ) );

		// Creates the MnM panel tab.
		add_action( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tab' ) );

		// Adds the mnm admin options.
		add_action( 'woocommerce_mnm_product_options', array( __CLASS__, 'container_layout_options' ), 5, 2 );
		add_action( 'woocommerce_mnm_product_options', array( __CLASS__, 'container_size_options' ), 10, 2 );
		add_action( 'woocommerce_mnm_product_options', array( __CLASS__, 'allowed_contents_options' ), 20, 2 );
		add_action( 'woocommerce_mnm_product_options', array( __CLASS__, 'pricing_options' ), 30, 2 );
		add_action( 'woocommerce_mnm_product_options', array( __CLASS__, 'discount_options' ), 35, 2 );
		add_action( 'woocommerce_mnm_product_options', array( __CLASS__, 'shipping_options' ), 40, 2 );

		// Creates the panel for selecting product options.
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data_panel' ) );

		// Processes and saves the necessary post metas from the selections made above.
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'process_mnm_data' ) );
	}


	/**
	 * Adds the 'mix and match product' type to the product types dropdown.
	 *
	 * @param  array 	$options
	 * @return array
	 * @deprecated 1.8.0 moved to WC_MNM_Install class
	 */
	public static function product_selector_filter( $options ) {
		wc_deprecated_function( 'WC_MNM_Meta_Box_Product_Data::product_selector_filter', '1.8.0', 'WC_MNM_Install::product_selector_filter' );
		$options[ 'mix-and-match' ] = __( 'Mix and Match product', 'woocommerce-mix-and-match-products' );
		return $options;
	}


	/**
	 * Mix-and-match type options.
	 *
	 * @param  array    $options
	 * @return array
	 */
	public static function type_options( $options ) {

		$options[ 'virtual' ][ 'wrapper_class' ]      .= ' show_if_mix-and-match';
		$options[ 'downloadable' ][ 'wrapper_class' ] .= ' show_if_mix-and-match';

		return $options;
	}


	/**
	 * Adds the MnM Product write panel tabs.
	 *
	 * @param  array $tabs
	 * @return array
	 */
	public static function product_data_tab( $tabs ) {

		global $post, $product_object, $mnm_product_object;

		/*
		 * Create a global MnM-type object to use for populating fields.
		 */

		$post_id = $post->ID;

		if ( empty( $product_object ) || false === $product_object->is_type( 'mix-and-match' ) ) {
			$mnm_product_object = $post_id ? new WC_Product_Mix_and_Match( $post_id ) : new WC_Product_Mix_and_Match();
		} else {
			$mnm_product_object = $product_object;
		}

		$tabs[ 'mnm_options' ] = array(
			'label'  => __( 'Mix and Match', 'woocommerce-mix-and-match-products' ),
			'target' => 'mnm_product_data',
			'class'  => array( 'show_if_mix-and-match', 'mnm_product_tab', 'mnm_product_options' )
		);

		$tabs[ 'inventory' ][ 'class' ][] = 'show_if_mix-and-match';

		return $tabs;
	}


	/**
	 * Write panel.
	 */
	public static function product_data_panel() {
		global $post;

		?>
		<div id="mnm_product_data" class="mnm_panel panel woocommerce_options_panel wc-metaboxes-wrapper">
			<div class="options_group mix_and_match">

				<?php

				$post_id = $post->ID;

				$mnm_product_object = $post_id ? new WC_Product_Mix_and_Match( $post_id ) : new WC_Product_Mix_and_Match();

				/**
				 * Add Mix and Match Product Options.
				 *
				 * @param int $post_id
				 *
				 * @see $this->container_layout_options   - 5
				 * @see $this->container_size_options   - 10
				 * @see $this->allowed_contents_options - 20
				 * @see $this->pricing_options - 30
				 * @see $this->shipping_options - 40
				 */
				do_action( 'woocommerce_mnm_product_options', $post->ID, $mnm_product_object );
				?>

			</div> <!-- options group -->
		</div>

		<?php
	}


	/**
	 * Add container discount option.
	 *
	 * @param  int $post_id
	 * @param  WC_Mix_and_Match  $mnm_product_object
	 */
	public static function discount_options( $post_id, $mnm_product_object ) {

		// Per-Item Discount.
		woocommerce_wp_text_input( array(
			'id'          => '_mnm_per_product_discount',
			'wrapper_class' => 'show_if_per_item_pricing',
			'label'       => __( 'Per-Item Discount (%)', 'woocommerce-mix-and-match-products' ),
			'value'       => $mnm_product_object->get_discount( 'edit' ),
			'description' => __( 'Discount applied to each item when in per-item pricing mode. This discount applies whenever the quantity restrictions are satisfied.', 'woocommerce-mix-and-match-products' ),
			'desc_tip'    => true,
			'data_type'   => 'decimal',
		) );

	}


	/**
	 * Render Layout options on 'woocommerce_mnm_product_options'.
	 *
	 * @param  int $post_id
	 * @param  WC_Mix_and_Match  $mnm_product_object
	 */
	public static function container_layout_options( $post_id, $mnm_product_object ) {

		/*
		 * Layout option.
		 */
		woocommerce_wp_select( array(
			'id'            => '_mnm_layout_style',
			'wrapper_class' => 'mnm_container_layout_options',
			'value'         => $mnm_product_object->get_layout( 'edit' ),
			'label'         => __( 'Layout', 'woocommerce-mix-and-match-products' ),
			'description'   => __( 'Select the <strong>Grid</strong> option to have the thumbnails, descriptions and quantities of child products arranged in a grid. Recommended for displaying lots of product options.', 'woocommerce-mix-and-match-products' ),
			'desc_tip'      => true,
			'options'       => WC_Product_Mix_and_Match::get_layout_options()
		) );

		/*
		 * Add to cart form location option.
		 */
		$options  = WC_Product_Mix_and_Match::get_add_to_cart_form_location_options();

		$help_tip = '';
		$loop     = 0;

		foreach ( $options as $option_key => $option ) {

			$help_tip .= '<strong>' . $option[ 'title' ] . '</strong> &ndash; ' . $option[ 'description' ];

			if ( $loop < sizeof( $options ) - 1 ) {
				$help_tip .= '</br></br>';
			}

			$loop++;
		}

		woocommerce_wp_select( array(
			'id'            => '_mnm_add_to_cart_form_location',
			'wrapper_class' => 'mnm_container_layout_options',
			'value'         => $mnm_product_object->get_add_to_cart_form_location( 'edit' ),
			'label'         => __( 'Form Location', 'woocommerce-mix-and-match-products' ),
			'description'   => $help_tip,
			'desc_tip'      => true,
			'options'       => array_combine( array_keys( $options ), wp_list_pluck( $options, 'title' ) )
		) );

	}

	/**
	 * Adds the container size option writepanel options.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 * @since  1.0.7
	 */
	public static function container_size_options( $post_id, $mnm_product_object ) {
		woocommerce_wp_text_input( array(
			'id'            => '_mnm_min_container_size',
			'label'         => __( 'Minimum Container Size', 'woocommerce-mix-and-match-products' ),
			'wrapper_class' => 'mnm_container_size_options',
			'description'   => __( 'Minimum quantity for Mix and Match containers.', 'woocommerce-mix-and-match-products' ),
			'type'          => 'number',
			'value'			=> $mnm_product_object->get_min_container_size( 'edit' ),
			'desc_tip'      => true
		) );
		woocommerce_wp_text_input( array(
			'id'            => '_mnm_max_container_size',
			'label'         => __( 'Maximum Container Size', 'woocommerce-mix-and-match-products' ),
			'wrapper_class' => 'mnm_container_size_options',
			'description'   => __( 'Maximum quantity for Mix and Match containers. Leave blank to not enforce an upper quantity limit.', 'woocommerce-mix-and-match-products' ),
			'type'          => 'number',
			'value'			=> $mnm_product_object->get_max_container_size( 'edit' ),
			'desc_tip'      => true
		) );
	}


	/**
	 * Adds allowed contents select2 writepanel options.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 * @since  1.0.7
	 */
	public static function allowed_contents_options( $post_id, $mnm_product_object ) { ?>

		<p id="mnm_allowed_contents_options" class="form-field">
			<label for="mnm_allowed_contents"><?php _e( 'Allowed Contents', 'woocommerce-mix-and-match-products' ); ?></label>

			<?php

			// Generate some data for the select2 input.
			$mnm_children = $mnm_product_object->get_children( 'edit' );
			?>

			<select id="mnm_allowed_contents" class="wc-product-search" name="mnm_allowed_contents[]" multiple="multiple" style="width: 400px;" data-sortable="sortable" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-mix-and-match-products' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo esc_attr( $mnm_product_object->get_id() ); ?>">
			<?php
			foreach ( $mnm_children as $child ) {
				if ( is_object( $child ) ) {
					echo '<option value="' . esc_attr( $child->get_id() ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $child->get_formatted_name() ) . '</option>';
				}
			}
			?>
			</select>
		</p>
		<?php
	}


	/**
	 * Adds the MnM per-item pricing option.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 * @since  1.2.0
	 */
	public static function pricing_options( $post_id, $mnm_product_object ) {

		// Per-Item Pricing.
		woocommerce_wp_checkbox( array(
			'id'          => '_mnm_per_product_pricing',
			'label'       => __( 'Per-Item Pricing', 'woocommerce-mix-and-match-products' ),
			'value'       => $mnm_product_object->get_priced_per_product( 'edit' ) ? 'yes' : 'no',
			'description' => __( 'When enabled, your Mix-and-Match product will be priced individually, based on standalone item prices and tax rates.', 'woocommerce-mix-and-match-products' ),
			'desc_tip'    => true
		) );
	}


	/**
	 * Adds the MnM per-item shipping option.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 * @since  1.2.0
	 */
	public static function shipping_options( $post_id, $mnm_product_object ) {

		global $mnm_product_object;

		// Per-Item Shipping.
		woocommerce_wp_checkbox( array(
			'id'          => '_mnm_per_product_shipping',
			'label'       => __( 'Per-Item Shipping', 'woocommerce-mix-and-match-products' ),
			'value'       => $mnm_product_object->get_shipped_per_product( 'edit' ) ? 'yes' : 'no',
			'description' => __( 'If your Mix-and-Match product consists of items that are assembled or packaged together, leave this option un-ticked and go to the Shipping tab to define the shipping properties of the entire container. Tick this option if the chosen items are shipped individually, without any change to their original shipping weight and dimensions.', 'woocommerce-mix-and-match-products' ),
			'desc_tip'    => true
		) );
	}


	/**
	 * Process, verify and save product data
	 *
	 * @param  WC_Product  $product
	 */
	public static function process_mnm_data( $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {

			$props = array(
				'layout'                    => 'tabular',
				'add_to_cart_form_location' => 'default',
				'min_container_size'  => 0,
				'max_container_size'  => '',
				'contents'            => array(),
				'priced_per_product'  => isset( $_POST[ '_mnm_per_product_pricing' ] ),
				'discount'			  => '',
				'shipped_per_product' => isset( $_POST[ '_mnm_per_product_shipping' ] )
			);

			// Layout.
			if ( ! empty( $_POST[ '_mnm_layout_style' ] ) ) {

				$layout = wc_clean( $_POST[ '_mnm_layout_style' ] );

				if ( in_array( $layout, array_keys( WC_Product_Mix_and_Match::get_layout_options() ) ) ) {
					$props[ 'layout' ] = $layout;
				}

			}

			// Add to cart form location.
			if ( ! empty( $_POST[ '_mnm_add_to_cart_form_location' ] ) ) {

				$form_location = wc_clean( $_POST[ '_mnm_add_to_cart_form_location' ] );

				if ( in_array( $form_location, array_keys( WC_Product_Mix_and_Match::get_add_to_cart_form_location_options() ) ) ) {
					$props[ 'add_to_cart_form_location' ] = $form_location;
				}
			}

			// Set the min container size.
			if( ! empty( $_POST[ '_mnm_min_container_size' ] ) ) {
				$props[ 'min_container_size' ] = absint( wc_clean( $_POST[ '_mnm_min_container_size' ] ) );
			}

			// Set the max container size.
			if( ! empty( $_POST[ '_mnm_max_container_size' ] ) ) {
				$props[ 'max_container_size' ] = absint( wc_clean( $_POST[ '_mnm_max_container_size' ] ) );
			}

			// Make sure the max container size is not smaller than the min size.
			if( $props[ 'max_container_size' ] > 0 && $props[ 'max_container_size' ] < $props[ 'min_container_size' ] ) {
				$props[ 'max_container_size' ] = $props[ 'min_container_size' ];
			}

			// Set the per-item discount.
			if ( $props['priced_per_product'] && ! empty( $_POST[ '_mnm_per_product_discount' ] ) ) {
				$props['discount'] = wc_clean( wp_unslash( $_POST[ '_mnm_per_product_discount' ] ) );
			}

			if ( ! defined( 'WC_MNM_UPDATING' ) ) {

				// Initialize the child content.
				$mnm_contents_data = array();

				// Populate with product data.
				if ( isset( $_POST[ 'mnm_allowed_contents' ] ) && ! empty( $_POST[ 'mnm_allowed_contents' ] ) ) {

					$mnm_allowed_contents = array_filter( array_map( 'intval', (array) $_POST[ 'mnm_allowed_contents' ] ) );

					$unsupported_error = false;

					// Check product types of selected items.
					foreach ( $mnm_allowed_contents as $mnm_id ) {

						// Do not allow the container to be saved in the contents, which results in an infinite loop.
						if( $mnm_id == $product->get_id() ) {
							WC_Admin_Meta_Boxes::add_error( __( 'You cannot add the Mix and Match container product as part of the Mix and Match container\'s contents.', 'woocommerce-mix-and-match-products' ) );
							continue;
						}

						$mnm_product = wc_get_product( $mnm_id );

						if( ! WC_Mix_and_Match_Helpers::is_child_supported_product_type( $mnm_product ) ) {
							$unsupported_error = true;
						} else {

							// Product-specific data, such as discounts, or min/max quantities in container may be included later on.
							$mnm_contents_data[ $mnm_id ][ 'child_id' ]     = $mnm_product->get_id();
							$mnm_contents_data[ $mnm_id ][ 'product_id' ]   = $mnm_product->get_parent_id() > 0 ? $mnm_product->get_parent_id() : $mnm_product->get_id();
							$mnm_contents_data[ $mnm_id ][ 'variation_id' ] = $mnm_product->get_parent_id() ? $mnm_product->get_id() : 0;

						}
					}

					if ( $unsupported_error ) {
						WC_Admin_Meta_Boxes::add_error( __( 'Mix and Match supports simple products and individual product variations (but not variable products) with all attributes defined, ex: Shirt, Color: Blue but not Shirt, Color: Any. Other product types and partially-defined variations cannot be added to the Mix and Match container.', 'woocommerce-mix-and-match-products' ) );
					}
				}

				// Show a notice if the user hasn't selected any items for the container.
				if ( empty( $mnm_contents_data ) && apply_filters( 'wc_mnm_display_empty_container_error', true, $product ) ) {
					WC_Admin_Meta_Boxes::add_error( __( 'Please select at least one product to use for this Mix and Match product.', 'woocommerce-mix-and-match-products' ) );
				} else {
					$props['contents'] = $mnm_contents_data;
				}

				// Finally, set the properties for saving.
				$product->set_props( $props );

			} else {
				WC_Admin_Meta_Boxes::add_error( __( 'Your changes have not been saved &ndash; please wait for the <strong>WooCommerce Mix and Match Data Update</strong> routine to complete before creating new Mix and Match products or making changes to existing ones.', 'woocommerce-mix-and-match-products' ) );
			}

		}
	}
}

// Launch the admin class.
WC_MNM_Meta_Box_Product_Data::init();
