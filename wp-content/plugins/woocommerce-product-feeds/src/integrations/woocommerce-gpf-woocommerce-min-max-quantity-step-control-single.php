<?php

class WoocommerceGpfWoocommerceMinMaxQuantityStepControlSingle {

	/**
	 * Run the integration.
	 */
	public function run() {
		// Allow integration to be disabled.
		if ( ! apply_filters( 'woocommerce_gpf_integration_minmaxquantitystepcontrolsingle', true ) ) {
			return;
		}
		// Add the filters we use.
		add_filter(
			'woocommerce_gpf_feed_item',
			[ $this, 'multiply_out_by_minimum_quantities' ],
			10,
			2
		);
		add_filter(
			'woocommerce_gpf_title',
			[ $this, 'include_minimum_qty_in_title' ],
			10,
			3
		);
	}

	/**
	 * @param $title
	 * @param $specific_product_id
	 * @param $general_product_id
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function include_minimum_qty_in_title( $title, $specific_product_id, $general_product_id ) {
		if ( ! apply_filters( 'woocommerce_gpf_minmaxquantitystepcontrolsingle_add_qty_to_title', true ) ) {
			return $title;
		}

		// Get the relevant minimum quantity & group by settings.
		$minimum_quantity = absint( get_post_meta( $general_product_id, '_wcmmq_s_min_quantity', true ) );

		// We're done if there is no minimum specified.
		if ( empty( $minimum_quantity ) ) {
			return $title;
		}

		return sprintf(
			/*
			 * Translators: This is the pattern for modifying a title to include a minimum purchase quantity.
			 * %1$d is the minimum purchase quantity, and %2$d is the product title.
			 */
			__( '%1$d x %2$s', 'woocommerce_gpf' ),
			$minimum_quantity,
			$title
		);
	}

	/**
	 * Multiplies prices out based on the minimum product quantities
	 *
	 * @param WoocommerceGpfFeedItem $feed_item
	 * @param \WC_Product $wc_product
	 *
	 * @return mixed
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function multiply_out_by_minimum_quantities( $feed_item, $wc_product ) {
		// Get the relevant minimum quantity & group by settings.
		$minimum_quantity = absint( get_post_meta( $feed_item->general_id, '_wcmmq_s_min_quantity', true ) );

		// We're done if there is no minimum specified.
		if ( empty( $minimum_quantity ) ) {
			return $feed_item;
		}
		// Adjust prices to represent the minimum quantity.
		$feed_item->sale_price_ex_tax     *= $minimum_quantity;
		$feed_item->sale_price_inc_tax    *= $minimum_quantity;
		$feed_item->regular_price_ex_tax  *= $minimum_quantity;
		$feed_item->regular_price_inc_tax *= $minimum_quantity;
		$feed_item->price_ex_tax          *= $minimum_quantity;
		$feed_item->price_inc_tax         *= $minimum_quantity;

		if ( empty( $feed_item->additional_elements['unit_pricing_measure'] ) &&
			 empty( $feed_item->additional_elements['unit_pricing_base_measure'] ) &&
			 apply_filters( 'woocommerce_gpf_minmaxquantitystepcontrolsingle_send_unit_pricing', true ) ) {
			$feed_item->additional_elements['unit_pricing_measure']      = array( $minimum_quantity . ' ct' );
			$feed_item->additional_elements['unit_pricing_base_measure'] = array( '1 ct' );
		}

		return $feed_item;
	}
}
