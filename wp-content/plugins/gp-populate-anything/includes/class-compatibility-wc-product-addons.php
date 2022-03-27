<?php

class GPPA_Compatibility_WC_Product_Addons {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_filter( 'woocommerce_get_item_data', array( $this, 'maybe_hydrate_form_for_cart_item' ), 5, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'clear_form_values' ), 15, 2 );
	}

	/**
	 * While an item is in the cart, the entry has not been saved yet. It's attached to the cart item.
	 *
	 * Due to this, we need to hydrate the form choices using the pseudo-entry inside the cart item otherwise
	 * field choices may not be populated.
	 *
	 * @param $item_data
	 * @param $cart_item
	 *
	 * @return mixed
	 */
	public function maybe_hydrate_form_for_cart_item( $item_data, $cart_item ) {
		// Grab the lead data from the un-saved entry and hydrate the form
		$wc_form = rgar( $cart_item, '_gravity_form_data' );
		$lead    = rgar( $cart_item, '_gravity_form_lead' );

		if ( ! rgblank( $wc_form ) && ! rgblank( $lead ) ) {
			$form = GFAPI::get_form( $wc_form['id'] );

			// Run hydration to prime GLOBALs such as $GLOBALS['gppa-field-values']
			if ( gp_populate_anything()->form_has_dynamic_population( $form ) ) {
				gp_populate_anything()->hydrate_initial_load( $form, false, $lead, $lead );
			}
		}

		return $item_data;
	}

	public function clear_form_values( $item_data, $cart_item ) {
		// Grab the lead data from the un-saved entry and hydrate the form
		$wc_form = rgar( $cart_item, '_gravity_form_data' );
		$lead    = rgar( $cart_item, '_gravity_form_lead' );

		if ( ! rgblank( $wc_form ) && ! rgblank( $lead ) ) {
			$form = GFAPI::get_form( $wc_form['id'] );

			$GLOBALS['gppa-field-values'][ $form['id'] ]                     = array();
			gp_populate_anything()->prepopulate_fields_values[ $form['id'] ] = array();
		}

		return $item_data;
	}

}

function gppa_compatibility_wc_product_addons() {
	return GPPA_Compatibility_WC_Product_Addons::get_instance();
}
