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
 * WC_Shipping_Per_Product class.
 *
 * @extends WC_Shipping_Method
 */
class WC_Shipping_Per_Product extends WC_Shipping_Method {

	const METHOD_ID = 'per_product';

	/**
	 * Default product shipping cost.
	 *
	 * @var int
	 */
	private $cost;

	/**
	 * Handling fee applied to entire order.
	 *
	 * @var int
	 */
	private $order_fee;

	/**
	 * Constructor.
	 *
	 * @param int $instance_id Instance Id for method in zone config.
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );
		$this->id                 = self::METHOD_ID;
		$this->method_title       = __( 'Per-product', 'woocommerce-shipping-per-product' );
		$this->method_description = __( 'Per product shipping allows you to define different shipping costs for products, based on customer location. These costs will be displayed and charged separately from any other shipping methods.', 'woocommerce-shipping-per-product' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);
		// Load the form fields.
		$this->init_form_fields();

		// Define user set variables.
		$this->title      = $this->get_option( 'title' );
		$this->tax_status = $this->get_option( 'tax_status' );
		$this->cost       = $this->get_option( 'cost' );
		$this->fee        = $this->get_option( 'fee' );
		$this->order_fee  = $this->get_option( 'order_fee' );

		// Actions.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'title'      => array(
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
			'cost'       => array(
				'title'       => __( 'Default Product Cost', 'woocommerce-shipping-per-product' ),
				'type'        => 'text',
				'description' => __( 'Cost excluding tax (per product) for products without defined costs. Enter an amount, e.g. 2.50. Entering an amount here will apply a global shipping cost for all products, effectively disabling all other shipping methods', 'woocommerce-shipping-per-product' ),
				'default'     => '',
				'placeholder' => __( 'Disabled, Enter an amount, e.g. 2.50.', 'woocommerce-shipping-per-product' ),
				'desc_tip'    => true,
			),
			'fee'        => array(
				'title'       => __( 'Handling Fee (per product)', 'woocommerce-shipping-per-product' ),
				'type'        => 'text',
				'description' => __( 'Fee excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-shipping-per-product' ),
				'default'     => '',
				'placeholder' => __( 'Disabled, Enter an amount, e.g. 2.50, or a percentage, e.g. 5%.', 'woocommerce-shipping-per-product' ),
				'desc_tip'    => true,
			),
			'order_fee'  => array(
				'title'       => __( 'Handling Fee (per order)', 'woocommerce-shipping-per-product' ),
				'type'        => 'text',
				'description' => __( 'Fee excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce-shipping-per-product' ),
				'default'     => '',
				'placeholder' => __( 'Disabled, Enter an amount, e.g. 2.50, or a percentage, e.g. 5%.', 'woocommerce-shipping-per-product' ),
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Check is per product shipping is enabled for the product.
	 *
	 * @param array $product_data The product data form the package array.
	 * @param array $package Shipping package array.
	 *
	 * @return bool
	 */
	public function is_per_product_shipping_product( array $product_data, array $package ) {
		if ( $product_data['quantity'] > 0 ) {
			if ( $product_data['data']->needs_shipping() ) {

				if ( false !== $this->calculate_product_shipping_cost( $product_data, $package ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Calculate the per product shipping cost if enabled for the product.
	 *
	 * @param array $product_data The product data form the package array.
	 * @param array $package Shipping package array.
	 *
	 * @return float|bool
	 */
	private function calculate_product_shipping_cost( array $product_data, array $package ) {

		$rule               = false;
		$item_shipping_cost = 0;

		if ( $product_data['variation_id'] ) {
			$rule = woocommerce_per_product_shipping_get_matching_rule( $product_data['variation_id'], $package );
		}

		if ( false === $rule ) {
			$rule = woocommerce_per_product_shipping_get_matching_rule( $product_data['product_id'], $package );
		}

		if ( $rule ) {
			$item_shipping_cost += (float) $rule->rule_item_cost * (int) $product_data['quantity'];
			$item_shipping_cost += (float) $rule->rule_cost;
		} elseif ( '0' === $this->cost || $this->cost > 0 ) {
			// Use default shipping cost.
			$item_shipping_cost += (float) $this->cost * (int) $product_data['quantity'];
		} else {
			// NO default and nothing found - abort.
			return false;
		}

		// Fee.
		$item_shipping_cost += (float) $this->get_fee( $this->fee, $item_shipping_cost ) * (int) $product_data['quantity'];

		return $item_shipping_cost;
	}

	/**
	 * Calculate shipping when this method is used standalone.
	 *
	 * @param array $package information.
	 */
	public function calculate_shipping( $package = array() ) {
		$_tax          = new WC_Tax();
		$taxes         = array();
		$shipping_cost = 0;

		if ( empty( $package['ship_via'] ) || ! in_array( $this->id, $package['ship_via'], true ) ) {
			return;  // must be a package mark as per product shipping in split_shipping_packages_per_product.
		}

		// This shipping method loops through products, adding up the cost.
		if ( count( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				if ( $values['quantity'] > 0 ) {
					if ( $values['data']->needs_shipping() ) {

						$item_shipping_cost = $this->calculate_product_shipping_cost( $values, $package );
						$shipping_cost     += $item_shipping_cost;

						if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) && 'taxable' === $this->tax_status ) {
							$rates      = $_tax->get_shipping_tax_rates( $values['data']->get_tax_class() );
							$item_taxes = $_tax->calc_shipping_tax( $item_shipping_cost, $rates );

							// Sum the item taxes.
							foreach ( array_keys( $taxes + $item_taxes ) as $key ) {
								$taxes[ $key ] = ( isset( $item_taxes[ $key ] ) ? $item_taxes[ $key ] : 0 ) + ( isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0 );
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
					$taxes[ $key ] = ( isset( $item_taxes[ $key ] ) ? $item_taxes[ $key ] : 0 ) + ( isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0 );
				}
			}
		}

		// Add rate.
		$this->add_rate(
			array(
				'id'    => $this->id,
				'label' => $this->title,
				'cost'  => $shipping_cost,
				'taxes' => $taxes, // We calc tax in the method.
			)
		);
	}
}
