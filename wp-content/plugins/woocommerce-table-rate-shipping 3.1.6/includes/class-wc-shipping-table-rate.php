<?php
/**
 * WC_Shipping_Table_Rate class file.
 *
 * @package WooCommerce_Table_Rat_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipping method class.
 */
class WC_Shipping_Table_Rate extends WC_Shipping_Method {

	/**
	 * Available table rates titles and costs.
	 *
	 * @var array
	 */
	private $available_rates;

	/**
	 * Method ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Instance ID.
	 *
	 * @var int
	 */
	public $instance_id;

	/**
	 * Decimal optipns.
	 *
	 * @var array
	 */
	private $decimal_options = array(
		'order_handling_fee',
		'max_shipping_cost',
		'handling_fee',
		'min_cost',
		'max_cost',
	);

	/**
	 * Order handling fee.
	 *
	 * @var float
	 */
	protected $order_handling_fee;

	/**
	 * Calculation type.
	 *
	 * @var string
	 */
	protected $calculation_type;

	/**
	 * Calculate the Min-Max after discount.
	 *
	 * @var boolean
	 */
	protected $minmax_after_discount;

	/**
	 * Calculate the Min-Max with tax.
	 *
	 * @var boolean
	 */
	protected $minmax_with_tax;

	/**
	 * Minimum cost value.
	 *
	 * @var float
	 */
	protected $min_cost;

	/**
	 * Maximum cost value.
	 *
	 * @var float
	 */
	protected $max_cost;

	/**
	 * Maximum cost that the customer will pay after all the shipping rules have been applied. If the shipping cost calculated is bigger than this value, this cost will be the one shown.
	 *
	 * @var float
	 */
	protected $max_shipping_cost;

	/**
	 * Name of the table rates in database.
	 *
	 * @var string
	 */
	protected $rates_table;

	/**
	 * Constructor.
	 *
	 * @param int $instance_id Instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		global $wpdb;

		// Default properties.
		$this->id                 = 'table_rate';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Table rates', 'woocommerce-table-rate-shipping' );
		$this->method_description = __( 'Table rates are dynamic rates based on a number of cart conditions.', 'woocommerce-table-rate-shipping' );
		$this->supports           = array( 'zones', 'shipping-zones', 'instance-settings' );
		$this->enabled            = 'yes';
		// Load the form fields.
		$this->init_form_fields();

		// Get settings.
		$this->title                 = $this->get_option( 'title', $this->method_title );
		$this->fee                   = $this->get_option( 'handling_fee' );
		$this->tax_status            = $this->get_option( 'tax_status' );
		$this->order_handling_fee    = $this->get_option( 'order_handling_fee' );
		$this->calculation_type      = $this->get_option( 'calculation_type' );
		$this->minmax_after_discount = $this->get_option( 'minmax_after_discount' );
		$this->minmax_with_tax       = $this->get_option( 'minmax_with_tax' );
		$this->min_cost              = $this->get_option( 'min_cost' );
		$this->max_cost              = $this->get_option( 'max_cost' );
		$this->max_shipping_cost     = $this->get_option( 'max_shipping_cost' );

		// Table rate specific variables.
		$this->rates_table     = $wpdb->prefix . 'woocommerce_shipping_table_rates';
		$this->available_rates = array();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_package_rates', array( $this, 'maybe_display_notice' ), 10, 2 );
	}

	/**
	 * Gets and option from the settings API, using defaults if necessary to prevent undefined notices.
	 *
	 * @param  string $key Option key.
	 * @param  mixed  $empty_value Value to return if option is empty.
	 * @return mixed  The value specified for the option or a default value for the option.
	 */
	public function get_option( $key, $empty_value = null ) {
		// Instance options take priority over global options.
		if ( in_array( $key, array_keys( $this->get_instance_form_fields() ), true ) ) {
			return $this->get_instance_option( $key, $empty_value );
		}

		// Return global option.
		return parent::get_option( $key, $empty_value );
	}

	/**
	 * Gets an option from the settings API, using defaults if necessary to prevent undefined notices.
	 *
	 * @param  string $key Option key.
	 * @param  mixed  $empty_value Value to return if option is empty.
	 * @return mixed  The value specified for the option or a default value for the option.
	 */
	public function get_instance_option( $key, $empty_value = null ) {
		if ( empty( $this->instance_settings ) ) {
			$this->init_instance_settings();
		}

		// Get option default if unset.
		if ( ! isset( $this->instance_settings[ $key ] ) ) {
			$form_fields = $this->get_instance_form_fields();

			if ( is_callable( array( $this, 'get_field_default' ) ) ) {
				$this->instance_settings[ $key ] = $this->get_field_default( $form_fields[ $key ] );
			} else {
				$this->instance_settings[ $key ] = empty( $form_fields[ $key ]['default'] ) ? '' : $form_fields[ $key ]['default'];
			}
		}

		if ( ! is_null( $empty_value ) && '' === $this->instance_settings[ $key ] ) {
			$this->instance_settings[ $key ] = $empty_value;
		}

		return $this->instance_settings[ $key ];
	}

	/**
	 * Get settings fields for instances of this shipping method (within zones).
	 * Should be overridden by shipping methods to add options.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_instance_form_fields() {
		return apply_filters( 'woocommerce_shipping_instance_form_fields_' . $this->id, $this->instance_form_fields );
	}

	/**
	 * Return the name of the option in the WP DB.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_instance_option_key() {
		return $this->instance_id ? $this->plugin_id . $this->id . '_' . $this->instance_id . '_settings' : '';
	}

	/**
	 * Initialise Settings for instances.
	 *
	 * @since 3.0.0
	 */
	public function init_instance_settings() {
		// 2nd option is for BW compat.
		$this->instance_settings = get_option( $this->get_instance_option_key(), get_option( $this->plugin_id . $this->id . '-' . $this->instance_id . '_settings', null ) );

		if ( empty( $this->instance_settings ) ) {
			$this->instance_settings = array();
		}

		/*
		 * Order handling fee does not handle percentages. So
		 * we need to remove previously saved % before initializing.
		 *
		 * @since 3.0.14 To fix https://github.com/woocommerce/woocommerce-table-rate-shipping/issues/91
		 */
		$this->instance_settings['order_handling_fee'] = str_replace(
			'%',
			'',
			empty( $this->instance_settings['order_handling_fee'] ) ? '' : $this->instance_settings['order_handling_fee']
		);

		// If there are no settings defined, use defaults.
		if ( ! is_array( $this->instance_settings ) ) {
			$form_fields             = $this->get_instance_form_fields();
			$this->instance_settings = array_merge( array_fill_keys( array_keys( $form_fields ), '' ), wp_list_pluck( $form_fields, 'default' ) );
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields          = array(); // No global options for table rates.
		$this->instance_form_fields = array(
			'title'                 => array(
				'title'       => __( 'Method Title', 'woocommerce-table-rate-shipping' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-table-rate-shipping' ),
				'default'     => __( 'Table Rate', 'woocommerce-table-rate-shipping' ),
			),
			'tax_status'            => array(
				'title'       => __( 'Tax Status', 'woocommerce-table-rate-shipping' ),
				'type'        => 'select',
				'description' => '',
				'desc_tip'    => true,
				'default'     => 'taxable',
				'options'     => array(
					'taxable' => __( 'Taxable', 'woocommerce-table-rate-shipping' ),
					'none'    => __( 'None', 'woocommerce-table-rate-shipping' ),
				),
			),
			'prices_include_tax'    => array(
				'title'       => __( 'Tax included in shipping costs', 'woocommerce-table-rate-shipping' ),
				'type'        => 'select',
				'description' => '',
				'desc_tip'    => true,
				'default'     => get_option( $this->get_instance_option_key() ) ? 'no' // Shipping method has previously been configured so we default to 'no' to maintain backwards compatibility.
					: ( 'yes' === get_option( 'woocommerce_prices_include_tax' ) ? 'yes' : 'no' ), // Otherwise default to the store setting.
				'options'     => array(
					'yes' => __( 'Yes, I will enter costs below inclusive of tax', 'woocommerce-table-rate-shipping' ),
					'no'  => __( 'No, I will enter costs below exclusive of tax', 'woocommerce-table-rate-shipping' ),
				),
			),
			'order_handling_fee'    => array(
				'title'       => __( 'Handling Fee', 'woocommerce-table-rate-shipping' ),
				'type'        => 'price',
				// translators: %s is amount example.
				'desc_tip'    => sprintf( __( 'Enter an amount, e.g. %s. Leave blank to disable. This cost is applied once for the order as a whole.', 'woocommerce-table-rate-shipping' ), '2' . wc_get_price_decimal_separator() . '50' ),
				'default'     => '',
				'placeholder' => __( 'n/a', 'woocommerce-table-rate-shipping' ),
			),
			'max_shipping_cost'     => array(
				'title'       => __( 'Maximum Shipping Cost', 'woocommerce-table-rate-shipping' ),
				'type'        => 'price',
				'desc_tip'    => __( 'Maximum cost that the customer will pay after all the shipping rules have been applied. If the shipping cost calculated is bigger than this value, this cost will be the one shown.', 'woocommerce-table-rate-shipping' ),
				'default'     => '',
				'placeholder' => __( 'n/a', 'woocommerce-table-rate-shipping' ),
			),
			'rates'                 => array(
				'title'       => __( 'Rates', 'woocommerce-table-rate-shipping' ),
				'type'        => 'title',
				'description' => __( 'This is where you define your table rates which are applied to an order.', 'woocommerce-table-rate-shipping' ),
				'default'     => '',
			),
			'calculation_type'      => array(
				'title'       => __( 'Calculation Type', 'woocommerce-table-rate-shipping' ),
				'type'        => 'select',
				'description' => __( 'Per order rates will offer the customer all matching rates. Calculated rates will sum all matching rates and provide a single total.', 'woocommerce-table-rate-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
				'options'     => array(
					''      => __( 'Per order', 'woocommerce-table-rate-shipping' ),
					'item'  => __( 'Calculated rates per item', 'woocommerce-table-rate-shipping' ),
					'line'  => __( 'Calculated rates per line item', 'woocommerce-table-rate-shipping' ),
					'class' => __( 'Calculated rates per shipping class', 'woocommerce-table-rate-shipping' ),
				),
			),
			'handling_fee'          => array(
				'title'       => __( 'Handling Fee Per [item]', 'woocommerce-table-rate-shipping' ),
				'type'        => 'price',
				'desc_tip'    => true,
				// translators: %1$s is amount example.
				'description' => sprintf( __( 'Handling fee. Enter an amount, e.g. %1$s, or a percentage, e.g. 5%%. Leave blank to disable. Applied based on the "Calculation Type" chosen below.', 'woocommerce-table-rate-shipping' ), '2' . wc_get_price_decimal_separator() . '50' ),
				'default'     => '',
				'placeholder' => __( 'n/a', 'woocommerce-table-rate-shipping' ),
			),
			'min_cost'              => array(
				'title'       => __( 'Minimum Cost Per [item]', 'woocommerce-table-rate-shipping' ),
				'type'        => 'price',
				'desc_tip'    => true,
				'description' => __( 'Minimum cost for this shipping method (optional). If the cost is lower, this minimum cost will be enforced.', 'woocommerce-table-rate-shipping' ),
				'default'     => '',
				'placeholder' => __( 'n/a', 'woocommerce-table-rate-shipping' ),
			),
			'max_cost'              => array(
				'title'       => __( 'Maximum Cost Per [item]', 'woocommerce-table-rate-shipping' ),
				'type'        => 'price',
				'desc_tip'    => true,
				'description' => __( 'Maximum cost for this shipping method (optional). If the cost is higher, this maximum cost will be enforced.', 'woocommerce-table-rate-shipping' ),
				'default'     => '',
				'placeholder' => __( 'n/a', 'woocommerce-table-rate-shipping' ),
			),
			'minmax_after_discount' => array(
				'title'       => __( 'Discounts in Min-Max', 'woocommerce-table-rate-shipping' ),
				'label'       => __( 'Use discounted price when comparing Min-Max Price Conditions.', 'woocommerce-table-rate-shipping' ),
				'type'        => 'checkbox',
				'description' => __( 'When comparing Min-Max Price Condition for rate in Table Rates, set if discounted or non-discounted price should be used.', 'woocommerce-table-rate-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'minmax_with_tax'       => array(
				'title'       => __( 'Taxes in Min-Max', 'woocommerce-table-rate-shipping' ),
				'label'       => __( 'Use price with tax when comparing Min-Max Price Conditions.', 'woocommerce-table-rate-shipping' ),
				'type'        => 'checkbox',
				'description' => __( 'When comparing Min-Max Price Condition for rate in Table Rates, set if price with taxes or without taxes should be used.', 'woocommerce-table-rate-shipping' ),
				'default'     => '',
				'desc_tip'    => true,
			),
		);

	}

	/**
	 * Admin options
	 */
	public function admin_options() {
		$this->instance_options();
	}

	/**
	 * Return admin options as a html string.
	 *
	 * @return string
	 */
	public function get_admin_options_html() {
		ob_start();
		$this->instance_options();
		return ob_get_clean();
	}

	/**
	 * Admin options.
	 */
	public function instance_options() {
		?>
		<table class="form-table">
			<?php
			$this->generate_settings_html( $this->get_instance_form_fields() );
			?>
			<tr>
				<th><?php esc_html_e( 'Table Rates', 'woocommerce-table-rate-shipping' ); ?></th>
				<td>
					<?php wc_table_rate_admin_shipping_rows( $this ); ?>
				</td>
			</tr>
			<?php if ( count( WC()->shipping->get_shipping_classes() ) ) : ?>
				<tr valign="top" id="shipping_class_priorities">
					<th scope="row" class="titledesc"><?php esc_html_e( 'Class Priorities', 'woocommerce-table-rate-shipping' ); ?></th>
					<td class="forminp" id="shipping_rates">
						<?php wc_table_rate_admin_shipping_class_priorities( $this->instance_id ); ?>
					</td>
				</tr>
			<?php endif; ?>
		</table>
		<?php
	}

	/**
	 * Process admin options.
	 */
	public function process_admin_options() {
		$decimal_separator = wc_get_price_decimal_separator();
		// Make sure decimals are with dot so that they add properly in PHP.
		foreach ( $this->decimal_options as $option ) {
			$option = 'woocommerce_table_rate_' . $option;

			// phpcs:disable WordPress.Security.NonceVerification.Missing --- security is handled by WooCommerce
			if ( ! isset( $_POST[ $option ] ) ) {
				continue;
			}

			$_POST[ $option ] = str_replace( $decimal_separator, '.', sanitize_text_field( wp_unslash( $_POST[ $option ] ) ) );
			// phpcs:enable WordPress.Security.NonceVerification.Missing
		}

		parent::process_admin_options();
		wc_table_rate_admin_shipping_rows_process( $this->instance_id );
	}

	/**
	 * Check if the shipping method is available based on the package and cart.
	 *
	 * @param array $package Shipping package.
	 * @return bool
	 */
	public function is_available( $package ) {
		$available = true;

		if ( ! $this->get_rates( $package ) ) {
			$available = false;
		}

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $available, $package, $this );
	}

	/**
	 * Get the items in class count.
	 *
	 * @param array $package Shipping package.
	 * @param int   $class_id Class ID.
	 * @return int
	 */
	public function count_items_in_class( $package, $class_id ) {
		$count = 0;

		// Find shipping classes for products in the package.
		foreach ( $package['contents'] as $item_id => $values ) {
			if ( $values['data']->needs_shipping() && $class_id === $values['data']->get_shipping_class_id() ) {
				$count += $values['quantity'];
			}
		}

		return $count;
	}

	/**
	 * Get cart shipping class id.
	 *
	 * @param array $package Shipping package.
	 * @return int
	 */
	public function get_cart_shipping_class_id( $package ) {
		// Find shipping class for cart.
		$found_shipping_classes = array();
		$shipping_class_id      = 0;
		$shipping_class_slug    = '';

		// Find shipping classes for products in the package.
		if ( count( $package['contents'] ) > 0 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				if ( $values['data']->needs_shipping() ) {
					$found_shipping_classes[ $values['data']->get_shipping_class_id() ] = $values['data']->get_shipping_class();
				}
			}
		}

		$found_shipping_classes = array_unique( $found_shipping_classes );

		if ( 1 === count( $found_shipping_classes ) ) {
			$shipping_class_slug = current( $found_shipping_classes );
		} elseif ( $found_shipping_classes > 1 ) {

			// Get class with highest priority.
			$priority   = get_option( 'woocommerce_table_rate_default_priority_' . $this->instance_id );
			$priorities = get_option( 'woocommerce_table_rate_priorities_' . $this->instance_id );

			$rates_has_no_class = false;

			// search the shipping rates array if it has empty rate_class or zero rate_class.
			foreach ( $this->get_shipping_rates( ARRAY_A ) as $idx => $shipping_rate ) {
				if ( empty( $shipping_rate['rate_class'] ) ) {
					$rates_has_no_class = true;
					break;
				}
			}

			foreach ( $found_shipping_classes as $id => $class ) {
				if ( isset( $priorities[ $class ] ) && ( $priorities[ $class ] < $priority || ( empty( $shipping_class_slug ) && false === $rates_has_no_class ) ) ) {
					$priority            = $priorities[ $class ];
					$shipping_class_slug = $class;
				}
			}
		}

		$found_shipping_classes = array_flip( $found_shipping_classes );

		if ( isset( $found_shipping_classes[ $shipping_class_slug ] ) ) {
			$shipping_class_id = $found_shipping_classes[ $shipping_class_slug ];
		}

		return $shipping_class_id;
	}

	/**
	 * Rates query.
	 *
	 * @param array $args Rates args.
	 * @return array
	 */
	public function query_rates( $args ) {
		global $wpdb;

		$defaults = array(
			'price'             => '',
			'weight'            => '',
			'count'             => 1,
			'count_in_class'    => 1,
			'shipping_class_id' => '',
		);

		$args = apply_filters( 'woocommerce_table_rate_query_rates_args', wp_parse_args( $args, $defaults ) );

		$shipping_class_id = '' === $args['shipping_class_id'] ? 0 : absint( $args['shipping_class_id'] );
		$rates             = $wpdb->get_results(
		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $rates_table is hardcoded & Repeated arguments.
			$wpdb->prepare(
				"
				SELECT rate_id, rate_cost, rate_cost_per_item, rate_cost_per_weight_unit, rate_cost_percent, rate_label, rate_priority, rate_abort, rate_abort_reason
				FROM {$this->rates_table}
				WHERE shipping_method_id IN ( %s )
				AND rate_class IN ( '', %d )
				AND
				(
					rate_condition = ''
					OR
					(
						rate_condition = 'price'
						AND
						(
							( ( rate_min ) = '' AND ( rate_max ) = '' )
							OR
							( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) >=0 AND %f >= ( rate_min + 0 ) AND %f <= ( rate_max + 0 ) )
							OR
							( ( rate_min ) >= 0 AND ( rate_max ) = '' AND %f >= ( rate_min + 0 ) )
							OR
							( ( rate_min ) = '' AND ( rate_max ) >= 0 AND %f <= ( rate_max + 0 ) )
						)
					)
					OR
					(
						rate_condition = 'weight'
						AND
						(
							( ( rate_min ) = '' AND ( rate_max ) = '' )
							OR
							( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) >=0 AND %f >= ( rate_min + 0 ) AND %f <= ( rate_max + 0 ) )
							OR
							( ( rate_min ) >= 0 AND ( rate_max ) = '' AND %f >= ( rate_min + 0 ) )
							OR
							( ( rate_min ) = '' AND ( rate_max ) >= 0 AND %f <= ( rate_max + 0 ) )
						)
					)
					OR
					(
						rate_condition = 'items'
						AND
						(
							( ( rate_min ) = '' AND ( rate_max ) = '' )
							OR
							( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) >=0 AND %d >= ( rate_min + 0 ) AND %d <= ( rate_max + 0 ) )
							OR
							( ( rate_min ) >= 0 AND ( rate_max ) = '' AND %d >= ( rate_min + 0 ) )
							OR
							( ( rate_min ) = '' AND ( rate_max ) >= 0 AND %d <= ( rate_max + 0 ) )
						)
					)
					OR
					(
						rate_condition = 'items_in_class'
						AND
						(
							( ( rate_min ) = '' AND ( rate_max ) = '' )
							OR
							( ( rate_min + 0 ) >= 0 AND ( rate_max + 0 ) >= 0 AND %d >= ( rate_min + 0 ) AND %d <= ( rate_max + 0 ) )
							OR
							( ( rate_min ) >= 0 AND ( rate_max ) = '' AND %d >= ( rate_min + 0 ) )
							OR
							( ( rate_min ) = '' AND ( rate_max ) >= 0 AND %d <= ( rate_max + 0 ) )
						)
					)
				)
				ORDER BY rate_order ASC
			",
				$this->instance_id,
				$shipping_class_id,
				...array_fill( 0, 4, $args['price'] ),
				...array_fill( 0, 4, $args['weight'] ),
				...array_fill( 0, 4, $args['count'] ),
				...array_fill( 0, 4, $args['count_in_class'] )
			)
		// phpcs:enable
		);

		return apply_filters( 'woocommerce_table_rate_query_rates', $rates );
	}

	/**
	 * Get rates function.
	 *
	 * @param array $package Package to check.
	 * @return bool|void
	 */
	public function get_rates( $package ) {
		global $wpdb;

		if ( ! $this->instance_id ) {
			return false;
		}

		$rates = array();
		$this->unset_abort_message( $package );

		// Get rates, depending on type.
		if ( 'item' === $this->calculation_type ) {

			// For each ITEM get matching rates.
			$costs = array();

			$matched = false;

			foreach ( $package['contents'] as $item_id => $values ) {

				$_product = $values['data'];

				if ( $values['quantity'] > 0 && $_product->needs_shipping() ) {

					$product_price = $this->get_product_price( $_product, 1, $values );

					$matching_rates = $this->query_rates(
						array(
							'price'             => $product_price,
							'weight'            => (float) $_product->get_weight(),
							'count'             => 1,
							'count_in_class'    => $this->count_items_in_class( $package, $_product->get_shipping_class_id() ),
							'shipping_class_id' => $_product->get_shipping_class_id(),
						)
					);

					$item_weight = round( (float) $_product->get_weight(), 2 );
					$item_fee    = (float) $this->get_fee( $this->fee, $product_price );
					$item_cost   = 0;

					foreach ( $matching_rates as $rate ) {
						$item_cost += (float) $rate->rate_cost;
						$item_cost += (float) $rate->rate_cost_per_weight_unit * $item_weight;
						$item_cost += ( (float) $rate->rate_cost_percent / 100 ) * $product_price;
						$matched    = true;
						if ( $rate->rate_abort ) {
							if ( ! empty( $rate->rate_abort_reason ) && ! wc_has_notice( $rate->rate_abort_reason, 'notice' ) ) {
								$this->add_notice( $rate->rate_abort_reason, $package );
							}
							return;
						}
						if ( $rate->rate_priority ) {
							break;
						}
					}

					$cost = ( $item_cost + $item_fee ) * $values['quantity'];

					if ( $this->min_cost && $cost < $this->min_cost ) {
						$cost = $this->min_cost;
					}
					if ( $this->max_cost && $cost > $this->max_cost ) {
						$cost = $this->max_cost;
					}

					$costs[ $item_id ] = $cost;

				}
			}

			if ( $matched ) {
				if ( $this->order_handling_fee ) {
					$costs['order'] = $this->order_handling_fee;
				} else {
					$costs['order'] = 0;
				}

				if ( $this->max_shipping_cost && ( $costs['order'] + array_sum( $costs ) ) > $this->max_shipping_cost ) {
					$rates[] = array(
						'id'    => is_callable( array( $this, 'get_rate_id' ) ) ? $this->get_rate_id() : $this->instance_id,
						'label' => $this->title,
						'cost'  => $this->max_shipping_cost,
					);
				} else {
					$rates[] = array(
						'id'       => is_callable( array( $this, 'get_rate_id' ) ) ? $this->get_rate_id() : $this->instance_id,
						'label'    => $this->title,
						'cost'     => $costs,
						'calc_tax' => 'per_item',
						'package'  => $package,
					);
				}
			}
		} elseif ( 'line' === $this->calculation_type ) {

			// For each LINE get matching rates.
			$costs = array();

			$matched = false;

			foreach ( $package['contents'] as $item_id => $values ) {

				$_product = $values['data'];

				if ( $values['quantity'] > 0 && $_product->needs_shipping() ) {

					$product_price = $this->get_product_price( $_product, $values['quantity'], $values );

					$matching_rates = $this->query_rates(
						array(
							'price'             => $product_price,
							'weight'            => (float) $_product->get_weight() * $values['quantity'],
							'count'             => $values['quantity'],
							'count_in_class'    => $this->count_items_in_class( $package, $_product->get_shipping_class_id() ),
							'shipping_class_id' => $_product->get_shipping_class_id(),
						)
					);

					$item_weight = round( (float) $_product->get_weight() * $values['quantity'], 2 );
					$item_fee    = (float) $this->get_fee( $this->fee, $product_price );
					$item_cost   = 0;

					foreach ( $matching_rates as $rate ) {
						$item_cost += (float) $rate->rate_cost;
						$item_cost += (float) $rate->rate_cost_per_item * $values['quantity'];
						$item_cost += (float) $rate->rate_cost_per_weight_unit * $item_weight;
						$item_cost += ( (float) $rate->rate_cost_percent / 100 ) * $product_price;
						$matched    = true;

						if ( $rate->rate_abort ) {
							if ( ! empty( $rate->rate_abort_reason ) && ! wc_has_notice( $rate->rate_abort_reason, 'notice' ) ) {
								$this->add_notice( $rate->rate_abort_reason, $package );
							}
							return;
						}
						if ( $rate->rate_priority ) {
							break;
						}
					}

					$item_cost = $item_cost + $item_fee;

					if ( $this->min_cost && $item_cost < $this->min_cost ) {
						$item_cost = $this->min_cost;
					}
					if ( $this->max_cost && $item_cost > $this->max_cost ) {
						$item_cost = $this->max_cost;
					}

					$costs[ $item_id ] = $item_cost;
				}
			}

			if ( $matched ) {
				if ( $this->order_handling_fee ) {
					$costs['order'] = $this->order_handling_fee;
				} else {
					$costs['order'] = 0;
				}

				if ( $this->max_shipping_cost && ( $costs['order'] + array_sum( $costs ) ) > $this->max_shipping_cost ) {
					$rates[] = array(
						'id'      => is_callable( array( $this, 'get_rate_id' ) ) ? $this->get_rate_id() : $this->instance_id,
						'label'   => $this->title,
						'cost'    => $this->max_shipping_cost,
						'package' => $package,
					);
				} else {
					$rates[] = array(
						'id'       => is_callable( array( $this, 'get_rate_id' ) ) ? $this->get_rate_id() : $this->instance_id,
						'label'    => $this->title,
						'cost'     => $costs,
						'calc_tax' => 'per_item',
						'package'  => $package,
					);
				}
			}
		} elseif ( 'class' === $this->calculation_type ) {

			// For each CLASS get matching rates.
			$total_cost = 0;

			// First get all the rates in the table.
			$all_rates = $this->get_shipping_rates();

			// Now go through cart items and group items by class.
			$classes = array();

			foreach ( $package['contents'] as $item_id => $values ) {

				$_product = $values['data'];

				if ( $values['quantity'] > 0 && $_product->needs_shipping() ) {

					$shipping_class = $_product->get_shipping_class_id();

					if ( ! isset( $classes[ $shipping_class ] ) ) {
						$classes[ $shipping_class ]                 = new stdClass();
						$classes[ $shipping_class ]->price          = 0;
						$classes[ $shipping_class ]->weight         = 0;
						$classes[ $shipping_class ]->items          = 0;
						$classes[ $shipping_class ]->items_in_class = 0;
					}

					$classes[ $shipping_class ]->price          += $this->get_product_price( $_product, $values['quantity'], $values );
					$classes[ $shipping_class ]->weight         += (float) $_product->get_weight() * $values['quantity'];
					$classes[ $shipping_class ]->items          += $values['quantity'];
					$classes[ $shipping_class ]->items_in_class += $values['quantity'];
				}
			}

			$matched    = false;
			$total_cost = 0;
			$stop       = false;

			// Now we have groups, loop the rates and find matches in order.
			foreach ( $all_rates as $rate ) {

				foreach ( $classes as $class_id => $class ) {

					if ( '' === $class_id ) {
						if ( 0 !== (int) $rate->rate_class && '' !== $rate->rate_class ) {
							continue;
						}
					} else {
						if ( $class_id !== (int) $rate->rate_class && '' !== $rate->rate_class ) {
							continue;
						}
					}

					$rate_match = false;

					switch ( $rate->rate_condition ) {
						case '':
							$rate_match = true;
							break;
						case 'price':
						case 'weight':
						case 'items_in_class':
						case 'items':
							$condition = $rate->rate_condition;
							$value     = $class->$condition;

							if ( '' === $rate->rate_min && '' === $rate->rate_max ) {
								$rate_match = true;
							}

							if ( $value >= $rate->rate_min && $value <= $rate->rate_max ) {
								$rate_match = true;
							}

							if ( $value >= $rate->rate_min && ! $rate->rate_max ) {
								$rate_match = true;
							}

							if ( $value <= $rate->rate_max && ! $rate->rate_min ) {
								$rate_match = true;
							}

							break;
					}

					// Rate matched class.
					if ( $rate_match ) {
						$rate_label  = $this->title;
						$class_cost  = 0;
						$class_cost += (float) $rate->rate_cost;
						$class_cost += (float) $rate->rate_cost_per_item * $class->items_in_class;
						$class_cost += (float) $rate->rate_cost_per_weight_unit * $class->weight;
						$class_cost += ( (float) $rate->rate_cost_percent / 100 ) * $class->price;

						if ( $rate->rate_abort ) {
							if ( ! empty( $rate->rate_abort_reason ) && ! wc_has_notice( $rate->rate_abort_reason, 'notice' ) ) {
								$this->add_notice( $rate->rate_abort_reason, $package );
							}
							return;
						}

						if ( $rate->rate_priority ) {
							$stop = true;
						}

						$matched = true;

						$class_fee   = (float) $this->get_fee( $this->fee, $class->price );
						$class_cost += $class_fee;

						if ( $this->min_cost && $class_cost < $this->min_cost ) {
							$class_cost = $this->min_cost;
						}
						if ( $this->max_cost && $class_cost > $this->max_cost ) {
							$class_cost = $this->max_cost;
						}

						$total_cost += $class_cost;
					}
				}

				// Breakpoint.
				if ( $stop ) {
					break;
				}
			}

			if ( $this->order_handling_fee ) {
				$total_cost += $this->get_fee( $this->order_handling_fee, $total_cost );
			}

			if ( $this->max_shipping_cost && $total_cost > $this->max_shipping_cost ) {
				$total_cost = $this->max_shipping_cost;
			}

			if ( $matched ) {
				$rates[] = array(
					'id'      => is_callable( array( $this, 'get_rate_id' ) ) ? $this->get_rate_id() : $this->instance_id,
					'label'   => $rate_label,
					'cost'    => $total_cost,
					'package' => $package,
				);
			}
		} else {

			// For the ORDER get matching rates.
			$shipping_class = $this->get_cart_shipping_class_id( $package );
			$price          = 0;
			$weight         = 0;
			$count          = 0;
			$count_in_class = 0;

			foreach ( $package['contents'] as $item_id => $values ) {

				$_product = $values['data'];

				if ( $values['quantity'] > 0 && $_product->needs_shipping() ) {
					$price  += $this->get_product_price( $_product, $values['quantity'], $values );
					$weight += (float) $_product->get_weight() * (float) $values['quantity'];
					$count  += $values['quantity'];

					if ( $_product->get_shipping_class_id() === $shipping_class ) {
						$count_in_class += $values['quantity'];
					}
				}
			}

			$matching_rates = $this->query_rates(
				array(
					'price'             => $price,
					'weight'            => $weight,
					'count'             => $count,
					'count_in_class'    => $count_in_class,
					'shipping_class_id' => $shipping_class,
				)
			);

			foreach ( $matching_rates as $rate ) {
				$label = $rate->rate_label;
				if ( ! $label ) {
					$label = $this->title;
				}

				if ( $rate->rate_abort ) {
					if ( ! empty( $rate->rate_abort_reason ) && ! wc_has_notice( $rate->rate_abort_reason, 'notice' ) ) {
						$this->add_notice( $rate->rate_abort_reason, $package );
					}
					$rates = array(); // Clear rates.
					break;
				}

				if ( $rate->rate_priority ) {
					$rates = array();
				}

				$cost  = (float) $rate->rate_cost;
				$cost += (float) $rate->rate_cost_per_item * $count;
				$cost += (float) $this->get_fee( $this->fee, $price );
				$cost += (float) $rate->rate_cost_per_weight_unit * $weight;
				$cost += ( (float) $rate->rate_cost_percent / 100 ) * $price;

				if ( $this->order_handling_fee ) {
					$cost += $this->order_handling_fee;
				}

				if ( $this->min_cost && $cost < $this->min_cost ) {
					$cost = $this->min_cost;
				}

				if ( $this->max_cost && $cost > $this->max_cost ) {
					$cost = $this->max_cost;
				}

				if ( $this->max_shipping_cost && $cost > $this->max_shipping_cost ) {
					$cost = $this->max_shipping_cost;
				}

				$rates[] = array(
					'id'      => is_callable( array( $this, 'get_rate_id' ) ) ? $this->get_rate_id( $rate->rate_id ) : $this->instance_id . ' : ' . $rate->rate_id,
					'label'   => $label,
					'cost'    => $cost,
					'package' => $package,
				);

				if ( $rate->rate_priority ) {
					break;
				}
			}
		}

		$is_customer_vat_exempt = WC()->cart->get_customer()->get_is_vat_exempt();

		if ( 'yes' === $this->get_instance_option( 'prices_include_tax' ) && ( $this->is_taxable() || $is_customer_vat_exempt ) ) {
			// We allow the table rate to be entered inclusive of taxes just like product prices.
			foreach ( $rates as $key => $rate ) {

				$tax_rates = WC_Tax::get_shipping_tax_rates();

				// Temporarily override setting since our shipping rate will always include taxes here.
				add_filter( 'woocommerce_prices_include_tax', array( $this, 'override_prices_include_tax_setting' ) );
				$base_tax_rates = WC_Tax::get_shipping_tax_rates( null, false );
				remove_filter( 'woocommerce_prices_include_tax', array( $this, 'override_prices_include_tax_setting' ) );

				$total_cost = is_array( $rate['cost'] ) ? array_sum( $rate['cost'] ) : $rate['cost'];

				if ( apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
					$taxes = WC_Tax::calc_tax( $total_cost, $base_tax_rates, true );
				} else {
					$taxes = WC_Tax::calc_tax( $total_cost, $tax_rates, true );
				}

				$rates[ $key ]['cost'] = $total_cost - array_sum( $taxes );

				$rates[ $key ]['taxes'] = $is_customer_vat_exempt ? array() : WC_Tax::calc_shipping_tax( $rates[ $key ]['cost'], $tax_rates );

				$rates[ $key ]['price_decimals'] = '4'; // Prevent the cost from being rounded before the tax is added.
			}
		}

		// None found?
		if ( 0 === count( $rates ) ) {
			return false;
		}

		// Set available.
		$this->available_rates = $rates;

		return true;
	}

	/**
	 * Unique function for overriding the prices including tax setting in WooCommerce.
	 *
	 * @since 3.0.27
	 *
	 * @return bool
	 */
	public function override_prices_include_tax_setting() {
		return true;
	}

	/**
	 * Calculate shipping.
	 *
	 * @param array $package Shipping package.
	 */
	public function calculate_shipping( $package = array() ) {
		if ( $this->available_rates ) {
			foreach ( $this->available_rates as $rate ) {
				$this->add_rate( $rate );
			}
		}
	}

	/**
	 * Get raw shipping rates from the DB.
	 *
	 * Optional filter helper for integration with other plugins.
	 *
	 * @param string $output Output format.
	 * @return mixed
	 */
	public function get_shipping_rates( $output = OBJECT ) {
		global $wpdb;

		$rates = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT * FROM {$this->rates_table} WHERE shipping_method_id = %s ORDER BY rate_order ASC;",
				$this->instance_id
			),
			$output
		);

		return apply_filters( 'woocommerce_table_rate_get_shipping_rates', $rates );
	}

	/**
	 * Get shipping rates with normalized values (respect decimal separator
	 * settings), for display.
	 *
	 * @return array
	 */
	public function get_normalized_shipping_rates() {
		$shipping_rates    = $this->get_shipping_rates( ARRAY_A );
		$decimal_separator = wc_get_price_decimal_separator();
		$normalize_keys    = array(
			'rate_cost',
			'rate_cost_per_item',
			'rate_cost_per_weight_unit',
			'rate_cost_percent',
			'rate_max',
			'rate_min',
		);

		foreach ( $shipping_rates as $index => $shipping_rate ) {
			foreach ( $normalize_keys as $key ) {
				if ( ! isset( $shipping_rate[ $key ] ) ) {
					continue;
				}

				$shipping_rates[ $index ][ $key ] = str_replace( '.', $decimal_separator, $shipping_rates[ $index ][ $key ] );
			}
		}

		return $shipping_rates;
	}

	/**
	 * Retrieve the product price from a line item.
	 *
	 * @param object $_product Product object.
	 * @param int    $qty      Line item quantity.
	 * @param array  $item     Array of line item data.
	 * @return float
	 */
	public function get_product_price( $_product, $qty = 1, $item = array() ) {

		// Use the product price  with discounts or without discounts to calculate Min-Max conditions.
		if ( apply_filters( 'woocommerce_table_rate_compare_price_limits_after_discounts', wc_string_to_bool( $this->minmax_after_discount ), $item ) && isset( $item['line_total'] ) ) {
			$row_base_price = $item['line_total'] + ( wc_string_to_bool( $this->minmax_with_tax ) && isset( $item['line_tax'] ) ? $item['line_tax'] : 0 );
			return apply_filters( 'woocommerce_table_rate_package_row_base_price', $row_base_price, $_product, $qty );
		} elseif ( isset( $item['line_subtotal'] ) ) {
			$row_base_price = $item['line_subtotal'] + ( wc_string_to_bool( $this->minmax_with_tax ) && isset( $item['line_subtotal_tax'] ) ? $item['line_subtotal_tax'] : 0 );
			return apply_filters( 'woocommerce_table_rate_package_row_base_price', $row_base_price, $_product, $qty );
		}

		$row_base_price = $_product->get_price() * $qty;

		// From Issue #134 : Adding a compatibility product price for Measurement Price Calculator plugin by SkyVerge.
		if ( class_exists( 'WC_Measurement_Price_Calculator_Loader' ) && isset( $item['pricing_item_meta_data']['_price'] ) ) {
			$row_base_price = $item['pricing_item_meta_data']['_price'] * $qty;
		}

		$row_base_price = apply_filters( 'woocommerce_table_rate_package_row_base_price', $row_base_price, $_product, $qty );

		if ( $_product->is_taxable() && wc_prices_include_tax() ) {

			$base_tax_rates = WC_Tax::get_base_tax_rates( $_product->get_tax_class() );

			$tax_rates = WC_Tax::get_rates( $_product->get_tax_class() );

			if ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
				$base_taxes     = WC_Tax::calc_tax( $row_base_price, $base_tax_rates, true, true );
				$modded_taxes   = WC_Tax::calc_tax( $row_base_price - array_sum( $base_taxes ), $tax_rates, false );
				$row_base_price = ( $row_base_price - array_sum( $base_taxes ) ) + array_sum( $modded_taxes );
			}
		}

		return $row_base_price;
	}

	/**
	 * Admin Panel Options Processing
	 * - Saves the options to the DB
	 *
	 * @since 1.0.0
	 * @deprecated 3.0.0
	 */
	public function process_instance_options() {
		$this->validate_settings_fields( $this->get_instance_form_fields() );

		if ( count( $this->errors ) > 0 ) {
			$this->display_errors();
			return false;
		} else {
			wc_table_rate_admin_shipping_rows_process( $this->instance_id );
			update_option( $this->get_instance_option_key(), $this->sanitized_fields );
			return true;
		}
	}

	/**
	 * Save the abort notice in the session (to display when shipping methods are loaded from cache).
	 *
	 * @since 3.0.19
	 * @param string $message Message to show.
	 * @param array  $package Shipping package.
	 * @return void
	 */
	private function add_notice( $message, $package = null ) {
		$abort = WC()->session->get( WC_Table_Rate_Shipping::$abort_key );

		if ( ! is_array( $abort ) ) {
			$abort = array();
		}

		$package_hash           = WC_Table_Rate_Shipping::create_package_hash( $package );
		$abort[ $package_hash ] = $message;

		WC()->session->set( WC_Table_Rate_Shipping::$abort_key, $abort );
	}

	/**
	 * Print the abort text if no other available rates.
	 *
	 * @param array $rates   Shipping rates.
	 * @param array $package Shipping package.
	 *
	 * @return array.
	 */
	public function maybe_display_notice( $rates, $package ) {
		// Only display shipping notices in cart/checkout.
		if ( ! is_cart() && ! is_checkout() ) {
			return $rates;
		}

		if ( ! empty( $rates ) ) {
			$this->unset_abort_message( $package );
			return $rates;
		}

		$abort = WC()->session->get( WC_Table_Rate_Shipping::$abort_key );

		if ( ! is_array( $abort ) ) {
			return $rates;
		}

		$package_hash = WC_Table_Rate_Shipping::create_package_hash( $package );

		if ( isset( $abort[ $package_hash ] ) && ! wc_has_notice( $abort[ $package_hash ], 'notice' ) ) {
			wc_add_notice( $abort[ $package_hash ], 'notice', array( 'wc_trs' => 'yes' ) );
		}

		return $rates;
	}

	/**
	 * Unset the abort notice in the session.
	 *
	 * @param array $package Shipping package.
	 * @since 3.0.25
	 */
	private function unset_abort_message( $package = null ) {
		$abort        = WC()->session->get( WC_Table_Rate_Shipping::$abort_key );
		$package_hash = WC_Table_Rate_Shipping::create_package_hash( $package );

		unset( $abort[ $package_hash ] );
		WC()->session->set( WC_Table_Rate_Shipping::$abort_key, $abort );
	}

}
