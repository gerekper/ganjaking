<?php
/**
 * Product Data Metabox Class
 *
 * @package  WooCommerce Mix and Match Products/Admin/Meta-Boxes/Product
 * @since    1.2.0
 * @version  2.2.0
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

		// Add Shipping type image select.
		if ( WC_MNM_Core_Compatibility::is_wc_version_gte( '6.0' ) ) {
			add_action( 'woocommerce_product_options_shipping_product_data', array( __CLASS__, 'container_shipping_options_admin_html' ), 10000 );
		} else {
			add_action( 'woocommerce_product_options_shipping', array( __CLASS__, 'container_shipping_options_admin_html' ), 10000 );
		}

		// Creates the MnM panel tab.
		add_action( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tab' ) );

		// Adds the mnm admin options.
		add_action( 'wc_mnm_admin_product_options', array( __CLASS__, 'container_layout_options' ), 5, 2 );
		add_action( 'wc_mnm_admin_product_options', array( __CLASS__, 'container_size_options' ), 10, 2 );
		add_action( 'wc_mnm_admin_product_options', array( __CLASS__, 'allowed_contents_options' ), 20, 2 );
		add_action( 'wc_mnm_admin_product_options', array( __CLASS__, 'pricing_options' ), 30, 2 );
		add_action( 'wc_mnm_admin_product_options', array( __CLASS__, 'discount_options' ), 35, 2 );

		// Creates the panel for selecting product options.
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data_panel' ) );

		// Processes and saves the necessary post metas from the selections made above.
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'process_mnm_data' ) );
	}


	/**
	 * Adds the 'mix and match product' type to the product types dropdown.
	 *
	 * @param  array    $options
	 * @return array
	 * @deprecated 1.8.0 moved to WC_MNM_Install class
	 */
	public static function product_selector_filter( $options ) {
		wc_deprecated_function( 'WC_MNM_Meta_Box_Product_Data::product_selector_filter', '1.8.0', 'WC_MNM_Install::product_selector_filter' );
		$options['mix-and-match'] = __( 'Mix and Match product', 'woocommerce-mix-and-match-products' );
		return $options;
	}


	/**
	 * Mix-and-match type options.
	 *
	 * @param  array    $options
	 * @return array
	 */
	public static function type_options( $options ) {

		$options['virtual']['wrapper_class']      .= ' hide_if_mix-and-match';
		$options['downloadable']['wrapper_class'] .= ' show_if_mix-and-match';

		return $options;
	}


	/**
	 * Shipping type image select html.
	 *
	 * @since 2.0.0
	 */
	public static function container_shipping_options_admin_html() {

		global $post, $product_object, $mnm_product_object;

		$needs_html_fix = 'woocommerce_product_options_shipping' === current_filter();

		$packing_mode_options = array(
			'together'   => array(
				'label'       => __( 'Packed together', 'woocommerce-mix-and-match-products' ),
				'description' => __( 'Selected products are packed together as a unit with specified dimensions, weight and shipping class.', 'woocommerce-mix-and-match-products' ),
			),
			'separate' => array(
				'label'       => __( 'Packed separately', 'woocommerce-mix-and-match-products' ),
				'description' => __( 'Selected products are packed separately, without any change to their shipping weight, dimensions, or shipping classes', 'woocommerce-mix-and-match-products' ),
			),
			'virtual' => array(
				'label'       => __( 'Virtual', 'woocommerce-mix-and-match-products' ),
				'description' => __( 'Nothing is packed or shipped. Everything is virtual.', 'woocommerce-mix-and-match-products' ),
			),
		);
		?>

		<?php // Old hook is nested and requires janky html markup to be compat with other plugins, ie: Subscriptions, Bundles, etc.
		if ( $needs_html_fix ) { ?>
			</div>
		<?php } ?>

		<div class="options_group mnm_packing_options show_if_mix-and-match">

			<?php

			// Packing mode.

			/**
			 * Back compatibility filter for folks who haven't update to 2.0x DB yet.
			 *
			 * Not recommended for regular use.
			 *
			 * @param  bool $packing_mode
			 * @param  obj WC_Product_Mix_and_Match $this
			 */
			$current_mode = apply_filters( '_wc_mnm_backcompat_product_get_packing_mode', $mnm_product_object->get_packing_mode( 'edit' ), $mnm_product_object );

			// Has physical container?
			$has_physical_container = 'separate_plus' === $current_mode;

			// The seperate plus additional physical box, uses the same icon/setting as separate.
			if ( 'separate_plus' === $current_mode ) {
				$current_mode = 'separate';
			}

			wc_mnm_wp_radio_images(
				array(
					'id'      => 'wc_mnm_packing_mode',
					'wrapper_class' => 'mnm_packing_mode wc_mnm_radio_images wc_mnm_display_toggle',
					'label'   => esc_html__( 'Packing mode', 'woocommerce-mix-and-match-products' ),
					'value'	  => $current_mode,
					'options' => $packing_mode_options,
				)
			);

			// Needs physical container
			wc_mnm_wp_toggle(
				array(
					'id'            => 'wc_mnm_has_physical_container',
					'wrapper_class' => 'wc_mnm_display_toggle show_if_wc_mnm_packing_mode_separate hide_if_wc_mnm_packing_mode_together hide_if_wc_mnm_packing_mode_virtual',
					'value'         => wc_bool_to_string( $has_physical_container ),
					'label'         => esc_html__( 'Has physical container?', 'woocommerce-mix-and-match-products' ),
				)
			);

			if ( wc_product_weight_enabled() ) {

				// Aggregated weight option.
				woocommerce_wp_radio(
                    array(
					'id'            => 'wc_mnm_weight_cumulative',
					'wrapper_class' => 'wc_mnm_weight_cumulative_field_field show_if_wc_mnm_packing_mode_together hide_if_wc_mnm_packing_mode_separate hide_if_wc_mnm_packing_mode_virtual',
					'value'         => $mnm_product_object->is_weight_cumulative() ? 'cumulative' : '',
					'label'         => __( 'Weight calculation', 'woocommerce-mix-and-match-products' ),
					'description'   => __( 'Controls whether to add the weight of the child items to the weight of the container.', 'woocommerce-mix-and-match-products' ),
					'desc_tip'      => true,
					'options'       => array(
						''            => __( 'None &mdash; Packed weight is always the same', 'woocommerce-mix-and-match-products' ),
						'cumulative' => __( 'Cumulative &mdash; Packed weight depends on the selected child items', 'woocommerce-mix-and-match-products' ),
					)
                    ) 
                );

			}

			?>

		<?php // Old hook is nested and requires janky html markup to be compat with other plugins, ie: Subscriptions, Bundles, etc.
		if ( ! $needs_html_fix ) { ?>
			</div>
		<?php } ?>

	<?php
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

		$tabs['mnm_options'] = array(
			'label'  => __( 'Mix and Match', 'woocommerce-mix-and-match-products' ),
			'target' => 'mnm_product_data',
			'class'  => array( 'show_if_mix-and-match', 'mnm_product_tab', 'mnm_product_options' ),
			'priority' => 45,
		);

		$tabs['inventory']['class'][] = 'show_if_mix-and-match'; // Cannot add same to shipping tab as it hide shipping on simple products. Use JS instead.

		return $tabs;
	}


	/**
	 * Write panel.
	 */
	public static function product_data_panel() {
		global $post;

		?>
		<div id="mnm_product_data" class="mnm_panel panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group mix_and_match">

				<?php

				$post_id = $post->ID;

				$mnm_product_object = $post_id ? new WC_Product_Mix_and_Match( $post_id ) : new WC_Product_Mix_and_Match();

				/**
				 * Add Mix and Match Product Options.
				 *
				 * @param int $post_id
				 * @param WC_Product_Mix_and_Match $mnm_product_object
				 *
				 * @see $this->container_layout_options()   - 5
				 * @see $this->container_size_options()   - 10
				 * @see $this->allowed_contents_options() - 20
				 * @see $this->pricing_options() - 30
				 * @see $this->discount_options() - 35
				 */
				do_action( 'wc_mnm_admin_product_options', $post->ID, $mnm_product_object );
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
		woocommerce_wp_text_input(
			array(
				'id'            => 'wc_mnm_per_product_discount',
				'wrapper_class' => 'show_if_wc_mnm_per_product_pricing_yes hide_if_wc_mnm_per_product_pricing_no',
				'label'         => __( 'Per-Item Discount (%)', 'woocommerce-mix-and-match-products' ),
				'value'         => $mnm_product_object->get_discount( 'edit' ),
				'description'   => __( 'Discount applied to each item when in per-item pricing mode. This discount applies whenever the quantity restrictions are satisfied.', 'woocommerce-mix-and-match-products' ),
				'desc_tip'      => true,
				'data_type'     => 'decimal',
			)
		);

	}


	/**
	 * Render Layout options on 'wc_mnm_admin_product_options'.
	 *
	 * @param  int $post_id
	 * @param  WC_Mix_and_Match  $mnm_product_object
	 */
	public static function container_layout_options( $post_id, $mnm_product_object ) {

		// Override option.
		wc_mnm_wp_toggle(
			array(
				'id'            => 'wc_mnm_layout_override',
				'wrapper_class' => 'wc_mnm_display_toggle',
				'value'         => wc_bool_to_string( $mnm_product_object->get_layout_override( 'edit' ) ),
				'label'         => esc_html__( 'Override global layout', 'woocommerce-mix-and-match-products' ),
			)
		);

		// Layout option.
		wc_mnm_wp_radio_images(
			array(
				'id'      => 'wc_mnm_layout',
				'wrapper_class' => 'mnm_container_layout_options show_if_wc_mnm_layout_override_yes hide_if_wc_mnm_layout_override_no hidden',
				'label'   => esc_html__( 'Layout', 'woocommerce-mix-and-match-products' ),
				'value'	  => $mnm_product_object->get_layout( 'edit' ),
				'options' => WC_Product_Mix_and_Match::get_layout_options(),
			)
		);
		?>

		<?php

		// Add to cart form location option.
		wc_mnm_wp_radio_images(
			array(
				'id'      => 'wc_mnm_form_location',
				'wrapper_class' => 'mnm_container_layout_options show_if_wc_mnm_layout_override_yes hide_if_wc_mnm_layout_override_no hidden',
				'label'   => esc_html__( 'Add to cart form location', 'woocommerce-mix-and-match-products' ),
				'value'	  => $mnm_product_object->get_add_to_cart_form_location( 'edit' ),
				'options' => WC_Product_Mix_and_Match::get_add_to_cart_form_location_options(),
			)
		);

		?>

		<?php
	}



	/**
	 * Adds the container size option writepanel options.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 * @since  1.0.7
	 */
	public static function container_size_options( $post_id, $mnm_product_object ) {
		woocommerce_wp_text_input(
			array(
			'id'            => 'wc_mnm_min_container_size',
			'label'         => __( 'Minimum Container Size', 'woocommerce-mix-and-match-products' ),
			'wrapper_class' => 'mnm_container_size_options',
			'description'   => __( 'Minimum quantity for Mix and Match containers.', 'woocommerce-mix-and-match-products' ),
			'type'          => 'number',
			'value'         => $mnm_product_object->get_min_container_size( 'edit' ),
			'desc_tip'      => true
			)
		);
		woocommerce_wp_text_input(
			array(
			'id'            => 'wc_mnm_max_container_size',
			'label'         => __( 'Maximum Container Size', 'woocommerce-mix-and-match-products' ),
			'wrapper_class' => 'mnm_container_size_options',
			'description'   => __( 'Maximum quantity for Mix and Match containers. Leave blank to not enforce an upper quantity limit.', 'woocommerce-mix-and-match-products' ),
			'type'          => 'number',
			'value'         => $mnm_product_object->get_max_container_size( 'edit' ),
			'desc_tip'      => true
			)
		);
	}


	/**
	 * Adds allowed contents select2 writepanel options.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 * @since  1.0.7
	 */
	public static function allowed_contents_options( $post_id, $mnm_product_object ) {

		woocommerce_wp_radio(
			array(
				'id'      => 'wc_mnm_content_source',
				'class'   => 'select short wc_mnm_content_source',
				'wrapper_class' => 'wc_mnm_display_toggle',
				'label'   => __( 'Allowed content', 'woocommerce-mix-and-match-products' ),
				'value'	  => 'categories' === $mnm_product_object->get_content_source( 'edit' ) ? 'categories' : 'products',
				'options' => array(
					'products'   => __( 'Select individual products', 'woocommerce-mix-and-match-products' ),
					'categories' => __( 'Select product categories', 'woocommerce-mix-and-match-products' ),
				)
			)
		);

		// Show warning if still need to update DB.
		$needs_update = ! WC_MNM_Compatibility::is_db_version_gte( '2.0' );
		if ( $needs_update ) {

			$update_url = wp_nonce_url(
				add_query_arg( 'wc_mnm_update_action', 'do_update_db' ),
				'wc_mnm_update_action',
				'wc_mnm_update_action_nonce'
			);
			?>

			<div id="message" class="inline notice woocommerce-message wc-mnm-notice-needs-update">
				<p><?php 
					// translators: %1$s is a link <a href> opening tag. %2$s is the closing </a> tag.
					printf( esc_html__( 'Before you can edit your container contents you need to %1$srun the database upgrade%2$s. Please take appropriate backups.', 'woocommerce-mix-and-match-products' ), '<a href="' . esc_url ( $update_url ) .'">', '</a>' );
				?></p>
			</div>

			<?php

		}

		// Generate some data for the select2 input.
		$child_items = 'products' === $mnm_product_object->get_content_source( 'edit' )  ? $mnm_product_object->get_child_items( 'edit' ) : array();

		// Exclude all but supported types.	
		$product_types   = array_keys( wc_get_product_types() );
		$supported_types = WC_MNM_Helpers::get_supported_product_types();
		$exclude_types   = array_diff( $product_types, $supported_types );

		$values = array();
		foreach ( $child_items as $child_item ) {
			if ( $child_item->get_product() ) {
				$values[ $child_item->get_product()->get_id() ] = $child_item->get_product()->get_formatted_name();
			}
		}
		
		// Search args.
		$args = array(
			'id'                 => 'wc_mnm_allowed_products',
			'name'               => 'wc_mnm_allowed_products[]',
			'class'              => 'wc-product-search wc-mnm-enhanced-select',
			'wrapper_class'      => 'form-field wc_mnm_allowed_products_field show_if_wc_mnm_content_source_products hide_if_wc_mnm_content_source_categories',
			'label'              => __( 'Select products', 'woocommerce-mix-and-match-products' ),
			'value'              => $values,
			'style'              => 'width: 400px',
			'custom_attributes'  => array(
				'multiple'          => 'multiple',
				'data-sortable'     => 'sortable',
				'data-placeholder'  => __( 'Search for a product&hellip;', 'woocommerce-mix-and-match-products' ),
				'data-action'       => 'woocommerce_json_search_products_and_variations',
				'data-exclude_type' => join( ",", $exclude_types ),
			),
		);

		if ( $needs_update ) {
			$args['custom_attributes']['disabled'] = 'disabled';
		}

		// Products search.
		wc_mnm_wp_enhanced_select( $args );

		// Generate some data for the select2 input.
		$selected_cats = $mnm_product_object->get_child_category_ids( 'edit' );

		$values = array();

		foreach ( $selected_cats as $cat_id ) {
			$current_cat = get_term_by( 'term_id', $cat_id, 'product_cat' );

			if ( $current_cat instanceof WP_Term ) {
				$values[$current_cat->term_id] = $current_cat->name;
			}
		}

		// Search args.
		$args = array(
			'id'                => 'wc_mnm_allowed_categories',
			'name'              => 'wc_mnm_allowed_categories[]',
			'class'             => 'wc-mnm-enhanced-select wc-mnm-category-search',
			'wrapper_class'     => 'form-field wc_mnm_allowed_categories_field show_if_wc_mnm_content_source_categories hide_if_wc_mnm_content_source_products',
			'label'             => __( 'Select categories', 'woocommerce-mix-and-match-products' ),
			'value'             => $values,
			'style'             => 'width: 400px',
			'custom_attributes' => array(
				'multiple'         => 'multiple',
				'data-sortable'    => 'sortable',
				'data-placeholder' => __( 'Search for a category&hellip;', 'woocommerce-mix-and-match-products' ),
				'data-action'      => 'woocommerce_json_search_categories',
				'data-allow_clear' => true,
				'data-return_id'   => true,
			),
		);

		if ( $needs_update ) {
			$args['custom_attributes']['disabled'] = 'disabled';
		}

		// Categories search.
		wc_mnm_wp_enhanced_select( $args );

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
		woocommerce_wp_radio(
			array(
				'id'      => 'wc_mnm_per_product_pricing',
				'class'   => 'wc_mnm_per_product_pricing',
				'wrapper_class' => 'wc_mnm_display_toggle',
				'label'   => esc_html__( 'Pricing mode', 'woocommerce-mix-and-match-products' ),
				'value'	  => $mnm_product_object->get_priced_per_product( 'edit' ) ? 'yes' : 'no',
				'options' => array(
					'no'  => esc_html__( 'Fixed &mdash; the price never changes', 'woocommerce-mix-and-match-products' ),
					'yes' => esc_html__( 'Per-item &mdash; the price depends on the selections', 'woocommerce-mix-and-match-products' )
				)
			)
		);
	}


	/**
	 * Adds the MnM per-item shipping option.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 * @since  1.2.0
	 * @deprecated 2.0.0 - Moved to Shipping Panel
	 */
	public static function shipping_options( $post_id, $mnm_product_object ) {

		wc_deprecated_function( __METHOD__ . '()', '2.0.0', __CLASS__ . '::container_shipping_options_admin_html() - Shipping options are moved to the shipping panel.' );

		global $mnm_product_object;

		// Per-Item Shipping.
		woocommerce_wp_checkbox(
			array(
			'id'          => '_mnm_per_product_shipping',
			'label'       => __( 'Per-Item Shipping', 'woocommerce-mix-and-match-products' ),
			'value'       => $mnm_product_object->get_shipped_per_product( 'edit' ) ? 'yes' : 'no',
			'description' => __( 'If your Mix-and-Match product consists of items that are assembled or packaged together, leave this option un-ticked and go to the Shipping tab to define the shipping properties of the entire container. Tick this option if the chosen items are shipped individually, without any change to their original shipping weight and dimensions.', 'woocommerce-mix-and-match-products' ),
			'desc_tip'    => true
			)
		);
	}


	/**
	 * Process, verify and save product data
	 *
	 * @param  WC_Product  $product
	 */
	public static function process_mnm_data( $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {

			$props = array(
				'layout_override'           => isset( $_POST['wc_mnm_layout_override'] ),
				'layout'                    => isset( $_POST['wc_mnm_layout'] ) ? wc_clean( $_POST['wc_mnm_layout'] ) : 'tabular',
				'add_to_cart_form_location' => isset( $_POST['wc_mnm_form_location'] ) ? wc_clean( $_POST['wc_mnm_form_location'] ) : 'default',
				'min_container_size'        => 0,
				'max_container_size'        => '',
				'child_items'               => array(),
				'priced_per_product'        => isset( $_POST['wc_mnm_per_product_pricing'] ) && 'yes' === wc_clean( $_POST[ 'wc_mnm_per_product_pricing' ] ),
				'discount'                  => '',
				'packing_mode'              => 'together',
				'weight_cumulative'         => isset( $_POST['wc_mnm_weight_cumulative'] ) && 'cumulative' === wc_clean( $_POST[ 'wc_mnm_weight_cumulative' ] ),
				'content_source'            => isset( $_POST['wc_mnm_content_source'] ) ? wc_clean( $_POST[ 'wc_mnm_content_source' ] ) : 'products',
				'child_category_ids'        => isset( $_POST['wc_mnm_allowed_categories'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['wc_mnm_allowed_categories'] ) ) : array(),
			);

			// Packing mode.
			if ( ! empty( $_POST['wc_mnm_packing_mode'] ) ) {
				$mode = wc_clean( $_POST['wc_mnm_packing_mode'] );
				$mode = 'separate' === $mode && isset( $_POST['wc_mnm_has_physical_container'] ) ? 'separate_plus' : $mode;
				$props['packing_mode'] = $mode;
			}

			// Set the min container size.
			if ( ! empty( $_POST['wc_mnm_min_container_size'] ) ) {
				$props['min_container_size'] = absint( wc_clean( $_POST['wc_mnm_min_container_size'] ) );
			}

			// Set the max container size.
			if ( ! empty( $_POST['wc_mnm_max_container_size'] ) ) {
				$props['max_container_size'] = absint( wc_clean( $_POST['wc_mnm_max_container_size'] ) );
			}

			// Make sure the max container size is not smaller than the min size.
			if ( $props['max_container_size'] > 0 && $props['max_container_size'] < $props['min_container_size'] ) {
				$props['max_container_size'] = $props['min_container_size'];
			}

			// Set the per-item discount.
			if ( $props['priced_per_product'] && ! empty( $_POST['wc_mnm_per_product_discount'] ) ) {
				$props['discount'] = wc_clean( wp_unslash( $_POST['wc_mnm_per_product_discount'] ) );
			}
			
			if ( ! defined( 'WC_MNM_UPDATING' ) && ! defined( 'WC_MNM_NEEDS_DB_UPDATE' ) ) {

				// Set child items.
				$props['child_items'] = self::process_child_items_data( $product, ! empty( $_POST['wc_mnm_allowed_products'] ) ? $_POST['wc_mnm_allowed_products'] : array() );

				// Show a notice if the user hasn't selected any items for the container.
				if ( apply_filters( 'wc_mnm_display_empty_container_error', true, $product ) ) {

					if ( 'categories' === $props['content_source'] && empty( $props['child_category_ids' ] ) ) {
						WC_Admin_Meta_Boxes::add_error( __( 'Please select at least one category to use for this Mix and Match product.', 'woocommerce-mix-and-match-products' ) );
					} elseif ( 'products' === $props['content_source'] && empty( $props['child_items'] ) ) {
						WC_Admin_Meta_Boxes::add_error( __( 'Please select at least one product to use for this Mix and Match product.', 'woocommerce-mix-and-match-products' ) );
					}

				}

				// Finally, set the properties for saving.
				$product->set_props( $props );



			} else {
				WC_Admin_Meta_Boxes::add_error( __( 'Your changes have not been saved &ndash; please wait for the <strong>WooCommerce Mix and Match Data Update</strong> routine to complete before creating new Mix and Match products or making changes to existing ones.', 'woocommerce-mix-and-match-products' ) );
			}

		}
	}

	/**
	 * Process allowed child items into array.
	 *
	 * @param  WC_Product $product
	 * @param  array $data [ int The Product|Variation ID ]
	 * @return array [ int => [
	 * 						'product_id' => int,
	 * 						'variation_id' => int 
	 * 				 ] ]
	 */
	public static function process_child_items_data( $product, $data ) {
		
		// Initialize the child content.
		$child_items_data = array();

		// Populate with product data.
		if ( ! empty( $data ) ) {

			$mnm_allowed_contents = array_filter( array_map( 'intval', (array) $data ) );

			$unsupported_error = false;

			// Check product types of selected items.
			foreach ( $mnm_allowed_contents as $mnm_id ) {

				// Do not allow the container to be saved in the contents, which results in an infinite loop.
				if ( $mnm_id === $product->get_id() ) {
					WC_Admin_Meta_Boxes::add_error( __( 'You cannot add the Mix and Match container product as part of the Mix and Match container\'s contents.', 'woocommerce-mix-and-match-products' ) );
					continue;
				}

				$mnm_product = wc_get_product( $mnm_id );

				if ( ! WC_MNM_Helpers::is_child_supported_product_type( $mnm_product ) ) {
					$unsupported_error = true;
				} else {

					// Product-specific data, such as discounts, or min/max quantities in container may be included later on.
					$child_items_data[ $mnm_id ]['product_id']   = $mnm_product->get_parent_id() > 0 ? $mnm_product->get_parent_id() : $mnm_product->get_id();
					$child_items_data[ $mnm_id ]['variation_id'] = $mnm_product->get_parent_id() ? $mnm_product->get_id() : 0;

				}
			}

			if ( $unsupported_error ) {
				WC_Admin_Meta_Boxes::add_error( sprintf( __( 'You have added an unsupported product type to your Mix and Match allowed contents. Please see the <a target="_blank" href="%s">documentation</a> for more details."', 'woocommerce-mix-and-match-products' ), esc_url( WC_Mix_and_Match()->get_resource_url( 'unsupported-types' ) ) ) );
			}
		}

		return $child_items_data;
	}
}

// Launch the admin class.
WC_MNM_Meta_Box_Product_Data::init();
