<?php
/**
 * Variable Product Data Metabox Class
 *
 * @package  WooCommerce Mix and Match Products/Admin/Meta-Boxes/Product
 * @since    3.0.0
 * @version  3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Meta_Box_Variable_Product_Data Class.
 *
 * Adds and save product meta.
 */
class WC_MNM_Meta_Box_Variable_Product_Data {

	/**
	 * Bootstraps the class and hooks required.
	 */
	public static function init() {

		// Creates the MnM panel tab.
	//	add_action( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tab' ), 5 );

		// Creates the panel for selecting product options.
	//	add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data_panel' ) );

		// Adds the vmnm product options.
		add_action( 'wc_mnm_variable_admin_product_options', array( 'WC_MNM_Meta_Box_Product_Data', 'container_layout_options' ), 5, 2 );
		add_action( 'wc_mnm_variable_admin_product_options', array( __CLASS__, 'shared_contents_options' ), 10, 2 );
	//	add_action( 'wc_mnm_variable_admin_product_options', array( 'WC_MNM_Meta_Box_Product_Data', 'allowed_contents_options' ), 20, 2 );
	//	add_action( 'wc_mnm_variable_admin_product_options', array( __CLASS__, 'pricing_options' ), 30, 2 );
	//  add_action( 'wc_mnm_variable_admin_product_options', array( __CLASS__, 'discount_options' ), 35, 2 );

		// Creates the panel for selecting product options.
		add_action( 'woocommerce_variation_options_pricing', [ __CLASS__, 'variation_options' ] );

		// Processes and saves the necessary post metas from the selections made above.
		add_action( 'woocommerce_admin_woocommerce_admin_process_variation_object', [ __CLASS__, 'process_variation_object' ] );

	}

	/**
	 * Adds the MnM Product write panel tabs.
	 *
	 * @param  array $tabs
	 * @return array
	 */
	public static function product_data_tab( $tabs ) {

		global $post, $product_object, $vmnm_product_object;

		/*
		 * Create a global MnM-type object to use for populating fields.
		 */

		$post_id = $post->ID;

		if ( empty( $product_object ) || false === $product_object->is_type( 'mix-and-match' ) ) {
			$vmnm_product_object = $post_id ? new WC_Product_Variable_Mix_and_Match( $post_id ) : new WC_Product_Variable_Mix_and_Match();
		} else {
			$vmnm_product_object = $product_object;
		}

		$tabs['variable_mnm_options'] = array(
			'label'  => esc_html__( 'Variable Mix and Match', 'woocommerce-mix-and-match-products' ),
			'target' => 'vmnm_product_data',
			'class'  => array( 'show_if_variable-mix-and-match', 'mnm_product_tab', 'mnm_product_options' )
		);

		$tabs['inventory']['class'][] = 'show_if_variable-mix-and-match'; // Cannot add same to shipping tab as it hide shipping on simple products. Use JS instead.

		return $tabs;
	}


	/**
	 * Write panel.
	 */
	public static function product_data_panel() {
		global $post;

		?>
		<div id="vmnm_product_data" class="variable_mnm_panel panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
			<div class="options_group mix_and_match">

				<?php

				$post_id = $post->ID;

				$vmnm_product_object = $post_id ? new WC_Product_Variable_Mix_and_Match( $post_id ) : new WC_Product_Variable_Mix_and_Match();

				/**
				 * Add Variable Mix and Match Product Options.
				 *
				 * @param int $post_id
				 *
				 * @see $this->container_layout_options   - 5
				 * @see $this->shared_contents_options   - 10
				 * @see $this->allowed_contents_options - 20
				 * @see $this->pricing_options - 30
				 */
				do_action( 'wc_mnm_variable_admin_product_options', $post->ID, $vmnm_product_object );
				?>

			</div> <!-- options group -->
		</div>

		<?php
	}

	/**
	 * Render Shared contents options on 'wc_mnm_variable_admin_product_options'.
	 *
	 * @param  int $post_id
	 * @param  WC_Product_Variable_Mix_and_Match  $mnm_product_object
	 */
	public static function shared_contents_options( $post_id, $mnm_product_object ) {

		// Override option.
		woocommerce_wp_checkbox(
			array(
				'id'            => 'wc_mnm_shared_contents',
				'wrapper_class' => 'wc_mnm_toggle',
			//	'value'         => wc_bool_to_string( $mnm_product_object->get_layout_override( 'edit' ) ),
				'label'         => esc_html__( 'Share contents across variations', 'woocommerce-mix-and-match-products' ),
				'description'   => '<label for="wc_mnm_shared_contents"></label>',
			)
		);

	}

	/**
	 * Add container discount option.
	 *
	 * @param  int $post_id
	 * @param  WC_Product_Variable_Mix_and_Match  $mnm_product_object
	 */
	public static function discount_options( $post_id, $mnm_product_object ) {

		// Per-Item Discount.
		woocommerce_wp_text_input(
			array(
				'id'            => '_mnm_per_product_discount',
				'wrapper_class' => 'show_if_per_item_pricing',
				'label'         => __( 'Per-Item Discount (%)', 'woocommerce-mix-and-match-products' ),
				'value'         => $mnm_product_object->get_discount( 'edit' ),
				'description'   => __( 'Discount applied to each item when in per-item pricing mode. This discount applies whenever the quantity restrictions are satisfied.', 'woocommerce-mix-and-match-products' ),
				'desc_tip'      => true,
				'data_type'     => 'decimal',
			)
		);

	}

	/**
	 * Add custom inputs to each variation
	 *
	 * @param string  $loop
	 * @param array   $variation_data
	 * @param WP_Post $variation
	 */
	public static function variation_options( $loop, $variation_data, $variation ) {

		$variation_object = wc_get_product( $variation->ID );

		woocommerce_wp_text_input(
			array(
			'id'            => '_mnm_min_container_size[' . $loop . ']',
			'label'         => __( 'Minimum Container Size', 'woocommerce-mix-and-match-products' ),
			'wrapper_class' => 'mnm_container_size_options',
			'description'   => __( 'Minimum quantity for Mix and Match containers.', 'woocommerce-mix-and-match-products' ),
			'type'          => 'number',
			'value'         => $variation_object->get_min_container_size( 'edit' ),
			'desc_tip'      => true
			)
		);
		woocommerce_wp_text_input(
			array(
			'id'            => '_mnm_max_container_size[' . $loop . ']',
			'label'         => __( 'Maximum Container Size', 'woocommerce-mix-and-match-products' ),
			'wrapper_class' => 'mnm_container_size_options',
			'description'   => __( 'Maximum quantity for Mix and Match containers. Leave blank to not enforce an upper quantity limit.', 'woocommerce-mix-and-match-products' ),
			'type'          => 'number',
			'value'         => $variation_object->get_max_container_size( 'edit' ),
			'desc_tip'      => true
			)
		);

	}

	/**
	 * Save extra meta info for variations
	 *
	 * @param WC_Product_Variation $variation
	 * @param int $i
	 */
	public static function process_variation_object( $variation, $i ) {
		$props = [
			'min_container_size' => 0,
			'max_container_size' => '',
		];

		// Set the min container size.
		if ( ! empty( $_POST['_mnm_min_container_size'] ) && ! empty( $_POST['_mnm_min_container_size'][$i] ) ) {
			$props['min_container_size'] = absint( wc_clean( $_POST['_mnm_min_container_size'][$i] ) );
		}

		// Set the max container size.
		if ( ! empty( $_POST['_mnm_max_container_size'] ) && ! empty( $_POST['_mnm_max_container_size'][$i] ) ) {
			$props['max_container_size'] = absint( wc_clean( $_POST['_mnm_max_container_size'][$i] ) );
		}

		// Make sure the max container size is not smaller than the min size.
		if ( $props['max_container_size'] > 0 && $props['max_container_size'] < $props['min_container_size'] ) {
			$props['max_container_size'] = $props['min_container_size'];
		}

		$variation->set_props( $props );

	}

}

// Launch the admin class.
WC_MNM_Meta_Box_Variable_Product_Data::init();