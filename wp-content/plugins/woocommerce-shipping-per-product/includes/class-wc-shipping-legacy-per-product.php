<?php
/**
 * Shipping method class.
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Shipping_Legacy_Per_Product class.
 * This is a copy of the class from v 2.2.15.
 * It supports stores which have not migrated to the new shipping zone and instance based shipping method.
 *
 * @deprecated 2.3.0
 */
class WC_Shipping_Legacy_Per_Product extends WC_Shipping_Method {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id           = 'legacy_per_product';
		$this->method_title = __( 'Per-product (legacy)', 'woocommerce-shipping-per-product' );
		/* translators: %s: Admin shipping settings URL */
		$this->method_description = '<strong>' . sprintf( __( 'This method is deprecated in Per Product Shipping 2.3.0 and will be removed in future versions - we recommend disabling it and instead setting up a new rate within your <a href="%s">Shipping zones</a>.', 'woocommerce-shipping-per-product' ), admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ) . '</strong>';
		$this->method_description = __( 'Per product shipping allows you to define different shipping costs for products, based on customer location. These costs can be added to other shipping methods, or used as a standalone shipping method.', 'woocommerce-shipping-per-product' );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->enabled      = $this->settings['enabled'];
		$this->title        = $this->settings['title'];
		$this->availability = $this->settings['availability'];
		$this->countries    = $this->settings['countries'];
		$this->tax_status   = $this->settings['tax_status'];
		$this->cost         = $this->settings['cost'];
		$this->fee          = $this->settings['fee'];
		$this->order_fee    = $this->settings['order_fee'];

		// Actions.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Return the name of the option in the WP DB.
	 * Needs to override base class so we get the old option key without legacy in the name.
	 *
	 * @return string
	 */
	public function get_option_key() {
		return $this->plugin_id . 'per_product_settings';
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Standalone Method', 'woocommerce-shipping-per-product' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable per-product shipping as a standalone shipping method', 'woocommerce-shipping-per-product' ),
				'default' => 'yes',
			),
			'title' => array(
				'title'       => __( 'Method Title', 'woocommerce-shipping-per-product' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-shipping-per-product' ),
				'default'     => __( 'Product Shipping', 'woocommerce-shipping-per-product' ),
				'desc_tip'    => true,
			),
			'tax_status' => array(
				'title'       => __( 'Tax Status', 'woocommerce-shipping-per-product' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'taxable',
				'options'     => array(
					'taxable' => __( 'Taxable', 'woocommerce-shipping-per-product' ),
					'none'    => __( 'None', 'woocommerce-shipping-per-product' ),
				),
			),
			'cost' => array(
				'title'       => __( 'Default Product Cost', 'woocommerce-shipping-per-product' ),
				'type'        => 'text',
				'description' => __( 'Cost excluding tax (per product) for products without defined costs. Enter an amount, e.g. 2.50.', 'woocommerce-shipping-per-product' ),
				'default'     => '',
				'placeholder' => '0',
				'desc_tip'    => true,
			),
			'fee' => array(
				'title'       => __( 'Handling Fee (per product)', 'woocommerce-shipping-per-product' ),
				'type'        => 'text',
				'description' => __( 'Fee excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-shipping-per-product' ),
				'default'     => '',
				'placeholder' => '0',
				'desc_tip'    => true,
			),
			'order_fee' => array(
				'title'       => __( 'Handling Fee (per order)', 'woocommerce-shipping-per-product' ),
				'type'        => 'text',
				'description' => __( 'Fee excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-shipping-per-product' ),
				'default'     => '',
				'placeholder' => '0',
				'desc_tip'    => true,
			),
			'availability' => array(
				'title'        => __( 'Method availability', 'woocommerce-shipping-per-product' ),
				'type'         => 'select',
				'default'      => 'all',
				'class'        => 'availability',
				'options'      => array(
					'all'      => __( 'All allowed countries', 'woocommerce-shipping-per-product' ),
					'specific' => __( 'Specific Countries', 'woocommerce-shipping-per-product' ),
				),
			),
			'countries' => array(
				'title'   => __( 'Specific Countries', 'woocommerce-shipping-per-product' ),
				'type'    => 'multiselect',
				'class'   => 'chosen_select',
				'css'     => 'width: 450px;',
				'default' => '',
				'options' => WC()->countries->get_allowed_countries(),
			),
		);
	}

	/**
	 * Calculate shipping when this method is used standalone.
	 *
	 * @param array $package information.
	 */
	public function calculate_shipping( $package = array() ) {
		$_tax           = new WC_Tax();
		$taxes          = array();
		$shipping_cost  = 0;

		if ( 'yes' !== $this->enabled ) {
			return;
		}

		// This shipping method loops through products, adding up the cost.
		if ( count( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				if ( $values['quantity'] > 0 ) {
					if ( $values['data']->needs_shipping() ) {

						$rule = false;
						$item_shipping_cost = 0;

						if ( $values['variation_id'] ) {
							$rule = woocommerce_per_product_shipping_get_matching_rule( $values['variation_id'], $package );
						}

						if ( false === $rule ) {
							$rule = woocommerce_per_product_shipping_get_matching_rule( $values['product_id'], $package );
						}

						if ( $rule ) {
							$item_shipping_cost += (float) $rule->rule_item_cost * (int) $values['quantity'];
							$item_shipping_cost += (float) $rule->rule_cost;
						} elseif ( '0' === $this->cost || $this->cost > 0 ) {
							// Use default.
							$item_shipping_cost += (float) $this->cost * (int) $values['quantity'];
						} else {
							// NO default and nothing found - abort.
							return;
						}

						// Fee.
						$item_shipping_cost += (float) $this->get_fee( $this->fee, $item_shipping_cost ) * (int) $values['quantity'];

						$shipping_cost += $item_shipping_cost;

						if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) && 'taxable' === $this->tax_status ) {
							$rates      = $_tax->get_shipping_tax_rates( $values['data']->get_tax_class() );
							$item_taxes = $_tax->calc_shipping_tax( $item_shipping_cost, $rates );

							// Sum the item taxes.
							foreach ( array_keys( $taxes + $item_taxes ) as $key ) {
								$taxes[ $key ] = ( isset( $item_taxes[ $key ] ) ? $item_taxes[ $key ] : 0 ) + ( isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0);
							}
						}
					}
				}
			}
		}

		// Add order shipping cost + tax.
		if ( $this->order_fee ) {
			$order_fee = (float) $this->get_fee( $this->order_fee, $shipping_cost );

			$shipping_cost += $order_fee;
			if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) && 'taxable' === $this->tax_status ) {
				$rates      = $_tax->get_shipping_tax_rates();
				$item_taxes = $_tax->calc_shipping_tax( $order_fee, $rates );

				// Sum the item taxes.
				foreach ( array_keys( $taxes + $item_taxes ) as $key ) {
					$taxes[ $key ] = ( isset( $item_taxes[ $key ] ) ? $item_taxes[ $key ] : 0 ) + ( isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0);
				}
			}
		}

		// Add rate.
		$this->add_rate( array(
			'id'    => $this->id,
			'label' => $this->title,
			'cost'  => $shipping_cost,
			'taxes' => $taxes, // We calc tax in the method.
		) );
	}
}
