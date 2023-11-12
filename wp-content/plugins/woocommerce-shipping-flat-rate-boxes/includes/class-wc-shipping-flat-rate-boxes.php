<?php
/**
 * Shipping method class file.
 *
 * @package woocommerce-shipping-flat-rate-boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shipping method class
 */
class WC_Shipping_Flat_Rate_Boxes extends WC_Shipping_Method {

	/**
	 * Available flat rate boxs titles and costs
	 *
	 * @var     array
	 */
	protected $available_rates;

	/**
	 * Method ID - should be unique to the shipping method.
	 *
	 * @var     string
	 */
	public $id;

	/**
	 * Instance ID number.
	 *
	 * @var     int
	 */
	public $instance_id;

	/**
	 * Tax class.
	 *
	 * @var     WC_Tax
	 */
	protected $tax;

	/**
	 * Cost for unpackable item.
	 *
	 * @var     float
	 */
	protected $unpackable_item_cost;

	/**
	 * Flat rate box table name in database.
	 *
	 * @var     string
	 */
	protected $flat_rate_boxes_table;

	/**
	 * Constructor.
	 *
	 * @param int $instance_id Instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		global $wpdb;

		$this->id                 = 'flat_rate_boxes';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Flat Rate Boxes', 'woocommerce-shipping-flat-rate-boxes' );
		$this->method_description = __( 'Define boxes which have varying dimensions and prices. Items are packed into boxes based on item size and volume.', 'woocommerce-table-rate-shipping' );
		$this->title              = $this->method_title;
		$this->supports           = array( 'zones', 'shipping-zones', 'instance-settings' );
		$this->tax                = new WC_Tax();

		// Load the form fields.
		$this->init_form_fields();

		// Get settings.
		$this->enabled              = 'yes';
		$this->title                = $this->get_option( 'title', __( 'Flat Rate Box', 'woocommerce-shipping-flat-rate-boxes' ) );
		$this->fee                  = $this->get_option( 'handling_fee' );
		$this->unpackable_item_cost = $this->get_option( 'unpackable_item_cost' );
		$this->tax_status           = $this->get_option( 'tax_status' );

		// flat rate box specific variables.
		$this->flat_rate_boxes_table = $wpdb->prefix . 'woocommerce_shipping_flat_rate_boxes';
		$this->available_rates       = array();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_translations_updates_for_woocommerce_shipping_flat_rate_boxes', '__return_true' );
	}

	/**
	 * Output a debug message.
	 *
	 * @param string $message   Message to display.
	 * @param string $type      Notice type.
	 *
	 * @return void
	 */
	public function debug( $message, $type = 'notice' ) {
		if ( BOX_SHIPPING_DEBUG ) {
			wc_add_notice( $message, $type );
		}
	}

	/**
	 * Gets an option from the settings API, using defaults if necessary to prevent undefined notices.
	 *
	 * @param  string $key          Option key.
	 * @param  mixed  $empty_value  default value.
	 *
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
	 * @param  string $key          Option key.
	 * @param  mixed  $empty_value  default value.
	 *
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
	 * @since 2.0.0
	 * @return array
	 */
	public function get_instance_form_fields() {
		/**
		 * Allow 3rd parties to filter shipping instance fields.
		 *
		 * @since 2.0.0
		 * @param array $instance_form_fields Shipping method instance fields.
		 * @param array
		 */
		return apply_filters( 'woocommerce_shipping_instance_form_fields_' . $this->id, $this->instance_form_fields );
	}

	/**
	 * Return the name of the option in the WP DB.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_instance_option_key() {
		return $this->instance_id ? $this->plugin_id . $this->id . '_' . $this->instance_id . '_settings' : '';
	}

	/**
	 * Initialise Settings for instances.
	 *
	 * @since 2.0.0
	 */
	public function init_instance_settings() {
		// 2nd option is for BW compat.
		$this->instance_settings = get_option( $this->get_instance_option_key(), get_option( $this->plugin_id . $this->id . '-' . $this->instance_id . '_settings', null ) );

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
		$this->form_fields          = array(); // No global options for flat rate boxes.
		$this->instance_form_fields = array(
			'title'                => array(
				'title'       => __( 'Method Title', 'woocommerce-shipping-flat-rate-boxes' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-shipping-flat-rate-boxes' ),
				'default'     => __( 'Flat Rate Boxes', 'woocommerce-shipping-flat-rate-boxes' ),
				'desc_tip'    => true,
			),
			'tax_status'           => array(
				'title'       => __( 'Tax Status', 'woocommerce-shipping-flat-rate-boxes' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'taxable',
				'options'     => array(
					'taxable' => __( 'Taxable', 'woocommerce-shipping-flat-rate-boxes' ),
					'none'    => __( 'None', 'woocommerce-shipping-flat-rate-boxes' ),
				),
			),
			'handling_fee'         => array(
				'title'       => __( 'Handling Fee', 'woocommerce-shipping-flat-rate-boxes' ),
				'type'        => 'text',
				'description' => __( 'Fee excluding tax. Enter an amount, e.g. 2.50, or a percentage of the cart value, e.g. 5%. Leave blank to disable.', 'woocommerce-shipping-flat-rate-boxes' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => __( 'n/a', 'woocommerce-shipping-flat-rate-boxes' ),
			),
			'unpackable_item_cost' => array(
				'title'       => __( 'Un-packable Item Cost', 'woocommerce-shipping-flat-rate-boxes' ),
				'type'        => 'text',
				'description' => __( 'Cost excluding tax. Leave blank to disable the rate if un-packable items are found.', 'woocommerce-shipping-flat-rate-boxes' ),
				'default'     => '',
				'desc_tip'    => true,
				'placeholder' => __( 'n/a', 'woocommerce-shipping-flat-rate-boxes' ),
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
	 * Process admin options.
	 */
	public function process_admin_options() {
		parent::process_admin_options();
		wc_box_shipping_admin_rows_process( $this->instance_id );
	}

	/**
	 * Check if this shipping method is available or not.
	 *
	 * @param array $package Package to check.
	 */
	public function is_available( $package ) {
		$available = true;

		if ( ! $this->get_rates( $package ) ) {
			$available = false;
		}

		/**
		 * Allow 3rd parties to override the shipping method availability.
		 *
		 * @since 1.0.0
		 * @param bool $availability True if the shipping method is available for the package.
		 * @param array $package     Package to check.
		 */
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $available, $package, $this );
	}

	/**
	 * Get boxes.
	 *
	 * @return array
	 */
	public function get_boxes() {
		global $wpdb;
		return $wpdb->get_results(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name.
			$wpdb->prepare( "SELECT * FROM {$this->flat_rate_boxes_table} WHERE shipping_method_id IN ( %s )", $this->instance_id )
		);
	}

	/**
	 * Get rates function.
	 *
	 * @param array $package Package to check.
	 *
	 * @return bool|void True if the rates is set.
	 */
	public function get_rates( $package ) {
		if ( ! $this->instance_id ) {
			return false;
		}

		if ( ! class_exists( 'WC_Boxpack' ) ) {
			include_once 'box-packer/class-wc-boxpack.php';
		}

		$boxpack = new WC_Boxpack();
		$rates   = array();
		$cost    = 0;
		$boxes   = $this->get_boxes();

		// Define boxes.
		foreach ( $boxes as $key => $box ) {
			$newbox = $boxpack->add_box( $box->box_length, $box->box_width, $box->box_height );

			$newbox->set_id( $key );

			if ( $box->box_max_weight ) {
				$newbox->set_max_weight( $box->box_max_weight );
			}
		}

		// Add items.
		foreach ( $package['contents'] as $item_id => $values ) {
			if ( ! $values['data']->needs_shipping() ) {
				continue;
			}

			if ( $values['data']->get_length() && $values['data']->get_height() && $values['data']->get_width() && $values['data']->get_weight() ) {

				$dimensions = array( $values['data']->get_length(), $values['data']->get_height(), $values['data']->get_width() );

				for ( $i = 0; $i < $values['quantity']; $i++ ) {
					$boxpack->add_item(
						$dimensions[2],
						$dimensions[1],
						$dimensions[0],
						$values['data']->get_weight(),
						$values['data']->get_price()
					);
				}
			} else {
				wc_add_notice( sprintf( __( 'Product # is missing dimensions. Aborting.', 'woocommerce-shipping-flat-rate-boxes' ), $item_id ), 'error' );
				return;
			}
		}

		// Pack it.
		$boxpack->pack();

		// Get packages.
		$packages = $boxpack->get_packages();

		foreach ( $packages as $package ) {

			if ( empty( $boxes[ $package->id ] ) ) {
				// Unpacked item!
				if ( '' === $this->unpackable_item_cost ) {
					$this->debug( 'Encountered un-packable item(s) - aborting' );
					return;
				}

				$this->debug( 'Encountered un-packable item(s) - using ' . floatval( $this->unpackable_item_cost ) );

				$cost += $this->unpackable_item_cost;
				continue;
			}

			$this->debug( 'Packed a box: <pre style="height: 200px; overflow: auto;">' . print_r( $package, true ) . '</pre>' ); // phpcs:ignore -- its a debug.

			$dimensions = array( $package->length, $package->width, $package->height );

			sort( $dimensions );

			$cost_box     = $boxes[ $package->id ]->box_cost;
			$cost_weight  = $boxes[ $package->id ]->box_cost_per_weight_unit;
			$cost_percent = $boxes[ $package->id ]->box_cost_percent;

			$cost += $cost_box;

			if ( $cost_weight ) {
				$cost += round( $package->weight ) * $cost_weight;
			}

			if ( $cost_percent ) {
				$cost += ( $package->value / 100 ) * $cost_percent;
			}

			// add fee.
			$cost += (float) $this->get_fee( $this->fee, $package->value );
		}

		$rates[] = array(
			'id'    => is_callable( array( $this, 'get_rate_id' ) ) ? $this->get_rate_id() : $this->instance_id,
			'label' => $this->title,
			'cost'  => $cost,
		);

		// Set available.
		$this->available_rates = $rates;

		return true;
	}

	/**
	 * Calculate shipping rates.
	 *
	 *  @param array $package Package array.
	 */
	public function calculate_shipping( $package = array() ) {
		if ( $this->available_rates ) {
			foreach ( $this->available_rates as $rate ) {
				$this->add_rate( $rate );
			}
		}
	}

	/**
	 * Instance options.
	 */
	public function instance_options() {
		?>
		<table class="form-table">
			<?php
			// Generate the HTML For the settings form.
			$this->generate_settings_html( $this->get_instance_form_fields() );
			?>
			<tr>
				<th><?php esc_html_e( 'Boxes', 'woocommerce-shipping-flat-rate-boxes' ); ?> <span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Define your flat rate boxes here. Items will be packed into them by order of volume.', 'woocommerce-shipping-flat-rate-boxes' ); ?>"></span></th>
				<td>
					<?php wc_box_shipping_admin_rows( $this ); ?>
				</td>
			</tr>
		</table>
		<?php
	}
}
