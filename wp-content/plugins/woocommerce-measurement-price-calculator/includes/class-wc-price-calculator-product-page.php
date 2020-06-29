<?php
/**
 * WooCommerce Measurement Price Calculator
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Measurement Price Calculator Product Page View Class
 *
 * @since 3.0
 */
class WC_Price_Calculator_Product_Page {


	/** @var float $default_step the default step based on the calculator precision */
	private $default_step;


	/**
	 * Construct and initialize the class
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// make all product variations visible for pricing calculator with pricing table products
		add_filter( 'woocommerce_product_is_visible',   array( $this, 'variable_product_is_visible' ), 1, 2 );

		// make all pricing calculator with pricing table products purchasable
		add_filter( 'woocommerce_is_purchasable',       array( $this, 'product_is_purchasable' ), 1, 2 );
		add_filter( 'woocommerce_variation_is_visible', array( $this, 'variation_is_visible' ), 10, 3 );

		// display the pricing calculator price per unit on the frontend (catalog and product page)
		$this->add_price_html_filters();

		// add the price and product measurements into the variation JSON object
		add_filter( 'woocommerce_available_variation', array( $this, 'available_variation' ), 10, 3 );

		// display the calculator styling, html and javascript on the frontend product detail page
		add_action( 'wp_print_styles',    array( $this, 'render_embedded_styles' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );

		// render pricing calculator on all product types except for variable type
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'render_price_calculator' ), 5 );

		// render pricing calculator on variable products
		add_action( 'woocommerce_single_variation',          array( $this, 'render_price_calculator' ), 15 );

		// fix sale flash on pricing rules products
		add_filter( 'woocommerce_product_is_on_sale',   array( $this, 'is_on_sale' ), 10, 2 );

		// add the weight per unit label to product attribues template
		add_action( 'woocommerce_before_template_part', array( $this, 'add_weight_per_unit_label_filter' ) );
		add_action( 'woocommerce_after_template_part',  array( $this, 'remove_weight_per_unit_label_filter' ) );

		// adjust the max value in quantity inputs when calculated inventory is enabled
		add_filter( 'woocommerce_quantity_input_max',   array( $this, 'remove_max_quantity_calculated_inventory' ), 100, 2 );

		// set the default step based on grabbing the filtered precision
		add_filter( 'wc_measurement_price_calculator_measurement_precision', array( $this, 'set_default_step' ), 9999 );
	}


	/**
	 * Add all price_html product filters
	 *
	 * @since 3.0
	 */
	private function add_price_html_filters() {

		add_filter( 'woocommerce_get_price_html', array( $this, 'price_per_unit_html' ), 10, 2 );

		add_filter( 'woocommerce_empty_price_html', array( $this, 'price_per_unit_html' ), 10, 2 );

		// variation price adjustments
		remove_filter( 'woocommerce_variation_prices_price',         array( $this, 'get_variation_price_per_unit' ), 10 );
		remove_filter( 'woocommerce_variation_prices_regular_price', array( $this, 'get_variation_price_per_unit' ), 10 );
		remove_filter( 'woocommerce_variation_prices_sale_price',    array( $this, 'get_variation_price_per_unit' ), 10 );
	}


	/**
	 * Remove all price_html product filters
	 *
	 * @since 3.0
	 */
	private function remove_price_html_filters() {

		remove_filter( 'woocommerce_get_price_html', array( $this, 'price_per_unit_html' ), 10 );

		remove_filter( 'woocommerce_empty_price_html', array( $this, 'price_per_unit_html' ), 10 );

		// variation price adjustments
		add_filter( 'woocommerce_variation_prices_price',         array( $this, 'get_variation_price_per_unit' ), 10, 3 );
		add_filter( 'woocommerce_variation_prices_regular_price', array( $this, 'get_variation_price_per_unit' ), 10, 3 );
		add_filter( 'woocommerce_variation_prices_sale_price',    array( $this, 'get_variation_price_per_unit' ), 10, 3 );
	}


	/**
	 * Convert a variation's price to a per unit price
	 * This is used to filter the actual price, regular price, and sale price
	 * in WC's get_variation_prices() array.
	 *
	 * @see \WC_Product_Variable::get_variation_prices()
	 *
	 * @since 3.9.0
	 * @param string|float|int $price Product price
	 * @param \WC_Product $variation The variation product
	 * @param \WC_Product $product The parent product
	 * @return float The converted price per unit
	 */
	public function get_variation_price_per_unit( $price, $variation, $product ) {

		if ( '' !== $price ) {

			$settings    = new \WC_Price_Calculator_Settings( $product );
			$measurement = \WC_Price_Calculator_Product::get_product_measurement( $variation, $settings );

			if ( $measurement ) {

				$measurement->set_unit( $settings->get_pricing_unit() );

				$measurement_value = $measurement->get_value();

				// convert to price per unit
				if ( $measurement_value > 0 ) {
					$price /= $measurement_value;
				}
			}
		}

		return $price;
	}


	/** Price methods *********************************************************/

	/**
	 * Set product on sale.
	 *
	 * Fixes sale flash on pricing rules products
	 *
	 * @since 3.5.2
	 * @param string $is_on_sale the price
	 * @param WC_Product $product the product
	 * @return string the price
	 */
	public function is_on_sale( $is_on_sale, $product ) {

		$settings = new \WC_Price_Calculator_Settings( $product );

		if ( $settings->pricing_rules_enabled() ) {
			$is_on_sale = $settings->pricing_rules_is_on_sale();
		}

		return $is_on_sale;
	}


	/** Frontend methods ******************************************************/


	/**
	 * Returns true if $product is purchasable.  We mark pricing table products
	 * as being purchasable, as they wouldn't be otherwise without a price set
	 *
	 * This is one of the few times where we are altering this filter in a
	 * positive manner, and so we try to hook into it first.
	 *
	 * @since 3.0
	 * @param boolean $is_purchasable true if the product is purchasable, false otherwise
	 * @param WC_Product $product the product
	 * @return boolean true if the product is purchasable, false otherwise
	 */
	public function product_is_purchasable( $is_purchasable, $product ) {

		// even if the product isn't purchasable, if it has pricing rules set, then we'll change that
		if ( ! $is_purchasable && \WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {

			$settings = new \WC_Price_Calculator_Settings( $product );

			if ( $settings->pricing_rules_enabled() ) {
 				$is_purchasable = true;
			}
		}

		return $is_purchasable;
	}


	/**
	 * Returns true if the identified variation is visible.  We mark pricing
	 * table products as being visible, as they wouldn't be otherwise without a
	 * price set
	 *
	 * This is one of the few times where we are altering this filter in a
	 * positive manner, and so we try to hook into it first.
	 *
	 * @since 3.3.2
	 * @param boolean $visible whether the variation is visible
	 * @param int $variation_id the variation identifier
	 * @param int $parent_id the parent product identifier
	 * @return boolean true if the variation is visible, false otherwise
	 */
	public function variation_is_visible( $visible, $variation_id, $parent_id ) {
		return $this->variable_product_is_visible( $visible, $parent_id );
	}


	/**
	 * Make product variations visible even if they don't have a price, as long
	 * as they are priced with a pricing table
	 *
	 * This is one of the few times where we are altering this filter in a
	 * positive manner, and so we try to hook into it first.
	 *
	 * @since 3.0
	 * @param boolean $visible whether the product is visible
	 * @param int $product_id the product id
	 * @return boolean true if the product is visible, false otherwise.
	 */
	public function variable_product_is_visible( $visible, $product_id ) {

		$product = wc_get_product( $product_id );

		if ( ! $visible && $product && $product->is_type( 'variable' ) && \WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {

			$settings = new \WC_Price_Calculator_Settings( $product );

			if ( $settings->pricing_rules_enabled() ) {
				$visible = true;
			}
		}

		return $visible;
	}


	/**
	 * Renders the price/sale price in terms of a unit of measurement for display
	 * on the catalog/product pages
	 *
	 * @since 3.0
	 * @param string $price_html the formatted sale price
	 * @param \WC_Product|\WC_Product_Variable $product the product
	 * @return string the formatted sale price, per unit
	 */
	public function price_per_unit_html( $price_html, $product ) {

		// if this is a product variation, get the parent product which holds the calculator settings
		$_product = $product;

		if ( $product->is_type( 'variation' ) ) {
			$_product = wc_get_product( $product->get_parent_id() );
		}

		if ( \WC_Price_Calculator_Product::pricing_per_unit_enabled( $_product ) ) {

			$settings = new \WC_Price_Calculator_Settings( $product );

			// if this is a quantity calculator, the displayed price per unit will have to be calculated from
			//  the product price and pricing measurement.  alternatively, for a pricing calculator product,
			//  the price set in the admin *is* the price per unit, so we just need to format it by adding the units
			if ( $settings->is_quantity_calculator_enabled() ) {

				$measurement = null;

				// for variable products we must go synchronize price levels to our per unit price
				if ( $product->is_type( 'variable' ) ) {

					// synchronize to the price per unit pricing
					\WC_Price_Calculator_Product::variable_product_sync( $product, $settings );

					// get price suffix
					$price_suffix = $product->get_price_suffix();

					// then remove it from the price html
					add_filter( 'woocommerce_get_price_suffix', '__return_empty_string' );

					// remove the price_html filters
					$this->remove_price_html_filters();

					// get the appropriate price html
					$price_html = $product->get_price_html();

					// then re-add the filters
					$this->add_price_html_filters();

					// re-add price suffix
					remove_filter( 'woocommerce_get_price_suffix', '__return_empty_string' );

					$pricing_label = __( $settings->get_pricing_label(), 'woocommerce-measurement-price-calculator' );

					// add units
					$price_html .= ' ' . $pricing_label;

					// add price suffix
					$price_html .= $price_suffix;

					/** this filter is documented in /includes/class-wc-price-calculator-product.php */
					$price_html = (string) apply_filters( 'wc_measurement_price_calculator_get_price_html', $price_html, $product, $pricing_label, true, false );

					// restore the original values
					\WC_Price_Calculator_Product::variable_product_unsync( $product );

				// other product types
				} elseif ( $measurement = WC_Price_Calculator_Product::get_product_measurement( $product, $settings ) ) {

					$measurement->set_unit( $settings->get_pricing_unit() );

					if ( $measurement && '' !== $price_html && $measurement->get_value() ) {

						// save the original price and remove the filter that we're currently within, to avoid an infinite loop
						$original_prices = array(
							'price'         => $product->get_price( 'edit' ),
							'regular_price' => $product->get_regular_price( 'edit' ),
							'sale_price'    => $product->get_sale_price( 'edit' ),
						);

						// calculate the price per unit, then format it
						$new_prices = array(
							'price'         => (float) $original_prices['price']         / $measurement->get_value(),
							'regular_price' => (float) $original_prices['regular_price'] / $measurement->get_value(),
						);

						// ensure there is a sale price before trying to set / use it
						// otherwise this will result in warnings with PHP 7.1+
						if ( ! empty( $original_prices['sale_price'] ) ) {
							$new_prices['sale_price'] = (float) $original_prices['sale_price'] / $measurement->get_value();
						}

						// save new prices with WC 3.x compatibility
						$product->set_props( $new_prices );

						$product = apply_filters( 'wc_measurement_price_calculator_quantity_price_per_unit', $product, $measurement );

						// get price suffix
						$price_suffix = $product->get_price_suffix();

						// remove it from the price html
						add_filter( 'woocommerce_get_price_suffix', '__return_empty_string' );

						// remove the price_html filters
						$this->remove_price_html_filters();

						// get the appropriate price html
						$price_html = $product->get_price_html();

						// then re-add the filters
						$this->add_price_html_filters();

						// re-add price suffix
						remove_filter( 'woocommerce_get_price_suffix', '__return_empty_string' );

						// restore the original product price and price_html filters (WC 3.x compatibility)
						$product->set_props( $original_prices );

						$pricing_label = __( $settings->get_pricing_label(), 'woocommerce-measurement-price-calculator' );

						// add units
						$price_html .= ' ' . $pricing_label;

						// add price suffix
						$price_html .= $price_suffix;

						/** this filter is documented in /includes/class-wc-price-calculator-product.php */
						$price_html = (string) apply_filters( 'wc_measurement_price_calculator_get_price_html', $price_html, $product, $pricing_label, true, false );
					}
				}

			// pricing calculator
			} else {

				if ( $settings->pricing_rules_enabled() ) {

					// pricing rules product
					$price_html = \WC_Price_Calculator_Product::get_pricing_rules_price_html( $product );

				} elseif ( '' !== $price_html ) {

					$pricing_label = __( $settings->get_pricing_label(), 'woocommerce-measurement-price-calculator' );

					// normal pricing calculator non-empty price: add units
					$price_html .= ' ' . $pricing_label;

					/** this filter is documented in /includes/class-wc-price-calculator-product.php */
					$price_html = (string) apply_filters( 'wc_measurement_price_calculator_get_price_html', $price_html, $product, $pricing_label, false, false );
				}
			}

			if ( '' !== $price_html ) {
				$price_html = '<span class="wc-measurement-price-calculator-price">' . $price_html . '</span>';
			}
		}

		return $price_html;
	}


	/**
	 * Add product 'price', minimum price, measurement value and measurement unit attributes to the variations JSON
	 *
	 * @since 3.0
	 * @param array $variation_data associative array of variation data
	 * @param \WC_Product $product parent product
	 * @param \WC_Product_Variation $variation product variation
	 * @return array $variation_data
	 */
	public function available_variation( $variation_data, $product, $variation ) {

		// is the calculator enabled for this product?
		if ( ! $product || ! \WC_Price_Calculator_Product::calculator_enabled( $product ) ) {
			return $variation_data;
		}

		$variation_data['price'] = wc_get_price_to_display( $variation );

		$variation_data['minimum_price'] = $variation->get_meta( '_wc_measurement_price_calculator_min_price' );

		// add the pricing unit to the weight display
		if ( ! empty( $variation_data['weight_html'] ) && $variation && $variation->get_weight() && \WC_Price_Calculator_Product::pricing_calculated_weight_enabled( $product ) ) {

			$settings = new \WC_Price_Calculator_Settings( $product );

			$variation_data['weight_html'] = $variation_data['weight_html'] . ' / ' . $settings->get_pricing_unit();
		}

		$settings = new \WC_Price_Calculator_Settings( $variation );

		// this is the measurement that represents one quantity of the product
		$product_measurement = \WC_Price_Calculator_Product::get_product_measurement( $variation, $settings );

		// if we have the required product physical attributes
		if ( $product_measurement && $product_measurement->get_value() ) {
			$variation_data['product_measurement_value'] = $product_measurement->get_value();
			$variation_data['product_measurement_unit']  = $product_measurement->get_unit();
		} else {
			$variation_data['product_measurement_value'] = '';
			$variation_data['product_measurement_unit']  = '';
		}

		return $variation_data;
	}


	/**
	 * Output the price calculator CSS styling inline within the page head.
	 *
	 * @since 3.0
	 */
	public function render_embedded_styles() {
		global $post;

		$product = null;

		if ( is_product() ) {
			$product = wc_get_product( $post->ID );
		}

		// is the calculator enabled for this product?
		if ( ! $product || ! \WC_Price_Calculator_Product::calculator_enabled( $product ) ) {
			return;
		}

		?>
		<style type="text/css">

			#price_calculator {
				border-style:none;
			}

			#price_calculator td {
				border-style: none;
				vertical-align: top;
			}

			#price_calculator input, #price_calculator span {
				float: right;
			}

			#price_calculator input {
				text-align: right;
				width: 64px;
			}

			.variable_price_calculator {
				display: none;
			}

			#price_calculator .calculate td {
				text-align: right;
			}

			#price_calculator .calculate button {
				margin-right: 0;
			}

			#price_calculator span.wc-measurement-price-calculator-input-help {
				display: inline-block;
				float: none;
				margin: 0 8px;
				vertical-align: middle;
			}

			@media screen and (max-width: 1081px) {
				#price_calculator span.wc-measurement-price-calculator-input-help {
					position: absolute;
				}
			}

			#tiptip_holder {
				display: none;
				position: absolute;
				top: 0;
				left: 0;
				z-index: 99999;
			}

			#tiptip_holder.tip_top {
				padding-bottom: 5px;
			}

			#tiptip_holder.tip_top #tiptip_arrow_inner {
				margin-top: -7px;
				margin-left: -6px;
				border-top-color: #464646;
			}

			#tiptip_holder.tip_bottom {
				padding-top: 5px;
			}

			#tiptip_holder.tip_bottom #tiptip_arrow_inner {
				margin-top: -5px;
				margin-left: -6px;
				border-bottom-color: #464646;
			}

			#tiptip_holder.tip_right {
				padding-left: 5px;
			}

			#tiptip_holder.tip_right #tiptip_arrow_inner {
				margin-top: -6px;
				margin-left: -5px;
				border-right-color: #464646;
			}

			#tiptip_holder.tip_left {
				padding-right: 5px;
			}

			#tiptip_holder.tip_left #tiptip_arrow_inner {
				margin-top: -6px;
				margin-left: -7px;
				border-left-color: #464646;
			}

			#tiptip_content, .chart-tooltip {
				font-size: 11px;
				color: #fff;
				padding: 0.5em 0.5em;
				background: #464646;
				-webkit-border-radius: 3px;
				-moz-border-radius: 3px;
				border-radius: 3px;
				-webkit-box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
				-moz-box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
				box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
				text-align: center;
				max-width: 150px;
			}

			#tiptip_content code, .chart-tooltip code {
				background: #888;
				padding: 1px;
			}

			#tiptip_arrow, #tiptip_arrow_inner {
				position: absolute;
				border-color: transparent;
				border-style: solid;
				border-width: 6px;
				height: 0;
				width: 0;
			}

		</style>
		<?php
	}


	/**
	 * Add filter to display the weight per unit label on the Additional
	 * Infromation product tab
	 *
	 * @since 3.7.0
	 * @param string $template_name The template name
	 */
	public function add_weight_per_unit_label_filter( $template_name ) {

		if ( 'single-product/product-attributes.php' === $template_name ) {
			add_filter( 'woocommerce_format_weight', array( $this, 'add_weight_per_unit_label' ) );
		}
	}


	/**
	 * Remove filter which displays the weight per unit label on the Additional
	 * Infromation product tab
	 *
	 * @since 3.7.0
	 * @param string $template_name The template name
	 */
	public function remove_weight_per_unit_label_filter( $template_name ) {

		if ( 'single-product/product-attributes.php' === $template_name ) {
			remove_filter( 'woocommerce_format_weight', array( $this, 'add_weight_per_unit_label' ) );
		}
	}


	/**
	 * Append the dimension unit to the weight unit option value
	 *
	 * @since 3.7.0
	 * @param string $weight_unit The value of woocommerce_weight_unit option
	 * @return string The weight per unit label
	 */
	public function add_weight_per_unit_label( $weight_unit ) {
		global $product;

		// bail if the calculator isn't enabled for this product
		if ( ! $product || ! \WC_Price_Calculator_Product::calculator_enabled( $product ) ) {
			return $weight_unit;
		}

		// bail if the calculator isn't enabled for this product
		// check that a weight is set first, to avoid displaying something like "N/A / cm"
		if ( ! $product->get_weight() || ! \WC_Price_Calculator_Product::pricing_calculated_weight_enabled( $product ) ) {
			return $weight_unit;
		}

		$settings = new \WC_Price_Calculator_Settings( $product );

		return $weight_unit . ' / ' . $settings->get_pricing_unit();
	}


	/**
	 * Remove the quantity input's default max value when calculated inventory is enabled
	 *
	 * The true maximum should be ( measurement needed x quantity ) and is difficult to
	 * predict without knowing at least one of these before
	 *
	 * TODO: We really should have some kind of JS validation to ensure the
	 * ( measurement needed x quantity ) does not exceed the measurement in
	 * stock. For now, WC's notice "You cannot add that amount of.. to the
	 * cart" is sufficient but is a bit ambiguous.
	 *
	 * @since 3.10.1
	 * @param string|int|float|null The max value
	 * @param \WC_Product $product The product object
	 * @return string|int|float|null
	 */
	public function remove_max_quantity_calculated_inventory( $max_value, $product ) {

		// do not modify max quantity as long as the product sold individually
		if ( $product->is_sold_individually() ) {
			return $max_value;
		}

		return \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) ? '' : $max_value;
	}


	/**
	 * Register/queue frontend scripts.
	 *
	 * @since 3.0
	 */
	public function enqueue_frontend_scripts() {
		global $post, $post_id;

		if ( $post && is_product() ) {

			$post_id = $post->ID;

		} else {

			/**
			 * Filters the product post ID used for the measurement calculator scripts if the current page is not a single product page.
			 *
			 * Overriding this with the product ID will allow the use of the calculator when using the [product_page] shortcode (as long as only one measurement product is displayed per page).
			 *
			 * @since 3.13.5
			 *
			 * @param int $post_id the product post ID
			 */
			$post_id = (int) apply_filters( 'wc_measurement_price_calculator_frontend_scripts_override_post_id', (int) $post_id );
		}

		$product = $post_id > 0 ? wc_get_product( $post_id ) : null;

		if ( $product && \WC_Price_Calculator_Product::calculator_enabled( $product ) ) {

			$settings = new \WC_Price_Calculator_Settings( $product );
			$version  = wc_measurement_price_calculator()->get_version();

			wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), defined( 'WC_VERSION' ) ? WC_VERSION : $version, true );

			wp_enqueue_script( 'wc-price-calculator', wc_measurement_price_calculator()->get_plugin_url() . '/assets/js/frontend/wc-measurement-price-calculator.min.js', array( 'jquery', 'jquery-cookie', 'jquery-tiptip' ), $version );

			/**
			 * Filters the measurement precision.
			 *
			 * @since 3.0
			 *
			 * @param int $measurement_precision the measurement precision
			 */
			$measurement_precision = apply_filters( 'wc_measurement_price_calculator_measurement_precision', 3 );

			// Variables for JS scripts
			$wc_price_calculator_params = array(
				'woocommerce_currency_symbol'     => get_woocommerce_currency_symbol(),
				'woocommerce_price_num_decimals'  => wc_get_price_decimals(),
				'woocommerce_currency_pos'        => get_option( 'woocommerce_currency_pos', 'left' ),
				'woocommerce_price_decimal_sep'   => stripslashes( wc_get_price_decimal_separator() ),
				'woocommerce_price_thousand_sep'  => stripslashes( wc_get_price_thousand_separator() ),
				'woocommerce_price_trim_zeros'    => get_option( 'woocommerce_price_trim_zeros' ),
				'unit_normalize_table'            => \WC_Price_Calculator_Measurement::get_normalize_table(),
				'unit_conversion_table'           => \WC_Price_Calculator_Measurement::get_conversion_table(),
				'measurement_precision'           => $measurement_precision,
				'measurement_type'                => $settings->get_calculator_type(),
				'cookie_name'                     => $settings->get_product_inputs_cookie_name(),
				'ajax_url'                        => admin_url( 'admin-ajax.php' ),
				'filter_calculated_price_nonce'   => wp_create_nonce( 'filter-calculated-price' ),
				'product_id'                      => $product->get_id(),
				'stock_warning'                   => esc_html__( "Unfortunately we don't have enough", 'woocommerce-measurement-price-calculator' ),
			);

			$min_price = $product->get_meta( '_wc_measurement_price_calculator_min_price' );

			$wc_price_calculator_params['minimum_price'] = is_numeric( $min_price ) ? wc_get_price_to_display( $product, [ 'price' => $min_price ] ) : '';

			// information required for either pricing or quantity calculator to function
			$wc_price_calculator_params['product_price'] = $product->is_type( 'variable' ) ? '' : wc_get_price_to_display( $product );

			// get the product total measurement (ie Area), get a measurement (ie length), and determine the product total measurement common unit based on the measurements common unit
			$product_measurement = \WC_Price_Calculator_Product::get_product_measurement( $product, $settings );

			if ( ! $product_measurement ) {
				return;
			}

			list( $measurement ) = $settings->get_calculator_measurements();

			$product_measurement->set_common_unit( $measurement->get_unit_common() );

			// this is the unit that the product total measurement will be in, ie it's how we know what unit we get for the Volume (AxH) calculator after multiplying A * H
			$wc_price_calculator_params['product_total_measurement_common_unit'] = $product_measurement->get_unit_common();

			if ( \WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {

				// product information required for the pricing calculator javascript to function
				$wc_price_calculator_params['calculator_type'] = 'pricing';
				$wc_price_calculator_params['product_price_unit'] = $settings->get_pricing_unit();
				$wc_price_calculator_params['pricing_overage'] = $settings->get_pricing_overage();

				// if there are pricing rules, include them on the page source
				if ( $settings->pricing_rules_enabled() ) {

					$wc_price_calculator_params['pricing_rules'] = $settings->get_pricing_rules();

					// generate the pricing html
					foreach ( $wc_price_calculator_params['pricing_rules'] as $index => $rule ) {
						$wc_price_calculator_params['pricing_rules'][ $index ]['price_html'] = $settings->get_pricing_rule_price_html( $rule );
					}
				}

			} else {

				// product information required for the quantity calculator javascript to function
				$wc_price_calculator_params['calculator_type'] = 'quantity';

				$quantity_range = \WC_Price_Calculator_Product::get_quantity_range( $product );

				$wc_price_calculator_params['quantity_range_min_value'] = $quantity_range['min_value'];
				$wc_price_calculator_params['quantity_range_max_value'] = $quantity_range['max_value'];

				if ( $product->is_type( 'simple' ) ) {

					// product_measurement represents one quantity of the product, bail if missing required product physical attributes
					if ( ! $product_measurement->get_value() ) {
						return;
					}

					$wc_price_calculator_params['product_measurement_value'] = $product_measurement->get_value();
					$wc_price_calculator_params['product_measurement_unit']  = $product_measurement->get_unit();
				} else {
					// provided by the available_variation() method
					$wc_price_calculator_params['product_measurement_value'] = '';
					$wc_price_calculator_params['product_measurement_unit']  = '';
				}
			}

			wp_localize_script( 'wc-price-calculator', 'wc_price_calculator_params', $wc_price_calculator_params );
		}
	}


	/**
	 * Render the price calculator on the product page
	 *
	 * @since 3.0
	 */
	public function render_price_calculator() {
		global $product;

		// is the calculator enabled for this product?
		if ( ! $product instanceof \WC_Product || ! \WC_Price_Calculator_Product::calculator_enabled( $product ) ) {
			return;
		}

		// ensure the calculator doesn't display twice on variable products (the `woocommerce_single_variation` action adds the calculator for variable product types)
		if ( doing_action( 'woocommerce_before_add_to_cart_button' ) && $product->is_type( array( 'variable', 'variable-subscription' ) ) ) {
			return;
		}

		$settings        = new \WC_Price_Calculator_Settings( $product );
		$calculator_mode = $settings->is_pricing_calculator_enabled() ? 'user-defined-mode' : 'quantity-based-mode';

		if ( \WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {
			// pricing calculator with custom dimensions and a price "per unit"

			// get the product total measurement (ie Area or Volume, etc)
			$product_measurement = \WC_Price_Calculator_Product::get_product_measurement( $product, $settings );

			if ( ! $product_measurement ) {
				return;
			}

			$product_measurement->set_unit( $settings->get_pricing_unit() );

			// get the product measurements, get a measurement, and set the product total measurement common unit based on the measurements common unit
			$measurements        = $settings->get_calculator_measurements();
			list( $measurement ) = $measurements;

			$product_measurement->set_common_unit( $measurement->get_unit_common() );

			// pricing calculator enabled, get the template
			wc_get_template(
				'single-product/price-calculator.php',
				array(
					'product_measurement' => $product_measurement,
					'settings'            => $settings,
					'calculator_mode'     => $calculator_mode,
					'measurements'        => $measurements,
					'default_step'        => $this->default_step,
				),
				'',
				wc_measurement_price_calculator()->get_plugin_path() . '/templates/' );

				// need an element to contain the price for simple pricing rule products
				if ( $product->is_type( 'simple' ) && $settings->pricing_rules_enabled() ) {
					echo '<div class="single_variation"></div>';
				}

		} else {
			// quantity calculator.  where the quantity of product needed is based on the configured product dimensions.  This is a actually bit more complex

			// get the starting quantity, max quantity, and total product measurement in product units
			$quantity_range = \WC_Price_Calculator_Product::get_quantity_range( $product );

			// set the product measurement based on the minimum quantity value, and set the unit to the frontend calculator unit
			$measurements = $settings->get_calculator_measurements();

			// The product measurement will be used to create the 'amount actual' field.
			$product_measurement = \WC_Price_Calculator_Product::get_product_measurement( $product, $settings );

			if ( ! $product_measurement ) {
				return;
			}

			// see whether all calculator measurements are defined in the same units (ie 'in', 'sq. in.' are considered the same)
			$measurements_unit = null;

			foreach ( $measurements as $measurement ) {

				if ( ! $measurements_unit ) {
					$measurements_unit = $measurement->get_unit();
				} else if ( ! \WC_Price_Calculator_Measurement::compare_units( $measurements_unit, $measurement->get_unit() ) ) {
					$measurements_unit = false;
					break;
				}
			}

			// all calculator measurements use the same base units, so lets use those for the 'amount actual' field
			//  area/volume product measurement can have a calculator measurement defined in units of length, so it
			//  will need to be converted to units of area or volume respectively
			if ( $measurements_unit ) {

				switch( $product_measurement->get_type() ) {

					case 'area':
						$measurements_unit = \WC_Price_Calculator_Measurement::to_area_unit( $measurements_unit );
					break;

					case 'volume':
						$measurements_unit = \WC_Price_Calculator_Measurement::to_volume_unit( $measurements_unit );
					break;
				}
			}

			// if the price per unit is displayed for this product, default to the pricing units for the 'amount actual' field
			if ( \WC_Price_Calculator_Product::pricing_per_unit_enabled( $product ) ) {
				$measurements_unit = $settings->get_pricing_unit();
			}

			// if a measurement unit other than the default was determined, set it
			if ( $measurements_unit ) {
				$product_measurement->set_unit( $measurements_unit );
			}

			$total_price = '';

			if ( $product->is_type( 'simple' ) ) {
				// if the product type is simple we can set an initial 'Amount Actual' and 'total price'
				//  we can't do this for variable products because we don't know which will be configured
				//  initially (actually I guess a default product can be configured, so maybe we can do something here)

				// not enough product physical attributes defined to get our measurement, so bail
				if ( ! $product_measurement->get_value() ) {
					return;
				}

				// figure out the starting measurement amount
				// multiply the starting quantity by the measurement value
				$product_measurement->set_value( round( $quantity_range['min_value'] * $product_measurement->get_value(), 2 ) );

				$total_price = wc_price( $quantity_range['min_value'] * wc_get_price_to_display( $product ), 2 );

			} elseif ( $product->is_type( 'variable' ) ) {
				// clear the product measurement value for variable products, since we can't really know what it is ahead of time (except for when a default is set)
				$product_measurement->set_value( '' );
			}

			// pricing calculator enabled, get the template
			wc_get_template(
				'single-product/quantity-calculator.php',
				array(
					'calculator_type'     => $settings->get_calculator_type(),
					'calculator_mode'     => $calculator_mode,
					'product_measurement' => $product_measurement,
					'measurements'        => $measurements,
					'total_price'         => $total_price,
				),
				'',
				wc_measurement_price_calculator()->get_plugin_path() . '/templates/'
			);
		}
	}


	/**
	 * Uses the enabled precision on the calculator to set a default step value.
	 *
	 * If a numeric input is being used, we need to set the step so that the browser default ("1" or the min value) isn't
	 *  automatically used (ie min value = .5, but I want to allow 1.1 as an input; without this 1.0 or 1.5 are forced).
	 * Set the default step to use the number of decimal places dictated by precision instead.
	 *
	 * @internal
	 *
	 * @since 3.12.0
	 *
	 * @param int $precision the precision number of decimal places
	 * @return int the unmodified precision
	 */
	public function set_default_step( $precision ) {

		if ( ! $this->default_step ) {
			$this->default_step = pow( 10, -$precision );
		}

		return $precision;
	}


}
