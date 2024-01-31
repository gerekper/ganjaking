<?php

/**
 * WC_Min_Max_Quantities_Blocks class.
 */
class WC_Min_Max_Quantities_Blocks {

  public function __construct() {
    add_action( 'woocommerce_block_template_area_product-form_after_add_block_inventory', array( $this, 'add_blocks_to_product_editor' ), 5 );
	add_action( 'init', array( $this, 'register_custom_blocks' ), 5 );
  }

  public function register_custom_blocks() {
		if ( isset( $_GET['page'] ) && 'wc-admin' === $_GET['page'] ) {
			register_block_type( WC_MMQ_ABSPATH . 'assets/dist/admin/product-editor/min-quantity/block.json' );
			register_block_type( WC_MMQ_ABSPATH . 'assets/dist/admin/product-editor/max-quantity/block.json' );
			register_block_type( WC_MMQ_ABSPATH . 'assets/dist/admin/product-editor/group-of-quantity/block.json' );
		}
	}

  public function add_blocks_to_product_editor( $inventory_tab ) {

		if ( ! method_exists( $inventory_tab, 'add_section' ) ) {
			return;
		}

		if ( $inventory_tab->get_root_template()->get_id() === 'simple-product' ) {
			$this->add_blocks_to_simple_product_template( $inventory_tab );
		}
	}

	private function add_blocks_to_simple_product_template( $inventory_tab ) {
		$section = $inventory_tab->add_section(
			array(
				'id'         => 'wc_min_max_section',
				'attributes' => array(
					'title' => __( 'Quantity rules', 'woocommerce-min-max-quantities' ),
				),
			)
		);

		$section->add_block(
			array(
				'id' => 'wc_min_max_group_of_quantity',
				'blockName' => 'woocommerce-min-max/group-of-quantity-field',
				'attributes' => array(
					'label' => __( 'Sell in groups of', 'woocommerce-min-max-quantities' ),
				)
			)
		);
	
		$section->add_block(
			array(
				'id' => 'wc_min_max_minimum_allowed_quantity',
				'blockName' => 'woocommerce-min-max/min-quantity-field',
				'attributes' => array(
					'label' => __( 'Min. Quantity', 'woocommerce-min-max-quantities' ),
				)
			)
		);
		$section->add_block(
			array(
				'id' => 'wc_min_max_maximum_allowed_quantity',
				'blockName' => 'woocommerce-min-max/max-quantity-field',
				'attributes' => array(
					'label' => __( 'Max. Quantity', 'woocommerce-min-max-quantities' ),
				)
			)
		);
		$section->add_block(
			array(
				'id' => 'wc_min_max_allow_combination',
				'blockName' => 'woocommerce/product-checkbox-field',
				'hideConditions' => array(
					array(
						'expression' => 'editedProduct.type !== "variable"',
					),
				),
				'attributes' => array(
					'label' => __( 'Combine variations', 'woocommerce-min-max-quantities' ),
					'property' => 'meta_data.allow_combination',
					'checkedValue' => 'yes',
					'uncheckedValue' => 'no',
					'tooltip' => __( 'Check to apply the settings above to the sum of quantities of variations. E.g., max quantity of 5 can be satisfied by adding 2 units of one variation and 3 units of another.', 'woocommerce-min-max-quantities' ),
				)
			)
		);
		$section->add_block(
			array(
				'id' => 'wc_min_max_cart_exclude_from_order',
				'blockName' => 'woocommerce/product-checkbox-field',
				'attributes' => array(
					'label' => __( 'Exclude from order rules', 'woocommerce-min-max-quantities' ),
					'property' => 'meta_data.minmax_cart_exclude',
					'checkedValue' => 'yes',
					'uncheckedValue' => 'no',
					'tooltip' => sprintf( __( 'Check to exclude this product from the total order quantity and value calculations set up in %1$sorder settings.%2$s', 'woocommerce-min-max-quantities' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products' ) . '" target="_blank">', '</a>' ),
				)
			)
		);
	}
}

// Add blocks to new product editor.
if (apply_filters( 'min_max_new_product_editor_enabled', '__return_false' )) {
  new WC_Min_Max_Quantities_Blocks();
}

