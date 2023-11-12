<?php
/**
 * Amazon custom shipping class. This adds standard shipping method to WC and provides
 * realtime shipment and delivery estimates.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 */

defined( 'ABSPATH' ) || exit;

// TODO: 4.1.0 Figure out if these are actually required.
// They should already be required and these lines can maybe totally get deleted.
// They are currently needed due to how file loading is done.
require_once WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-settings-api.php';
require_once WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-shipping-method.php';
require_once dirname( __FILE__ ) . '/class-ns-mcf-fulfillment.php';

/**
 * Amazon shipping method.
 */
class WC_Shipping_Amazon extends WC_Shipping_Method {

	const FBA_PER_UNIT_FULFILLMENT_FEE = 'FBAPerUnitFulfillmentFee';

	/**
	 * Custom rates.
	 *
	 * @var array
	 */
	public $custom_rates = array(
		'Standard'  => array(
			'type'        => 'Standard',
			'enabled'     => 'yes',
			'fee_percent' => 0,
			'fee_amount'  => 0,
		),
		'Expedited' => array(
			'type'        => 'Expedited',
			'enabled'     => 'yes',
			'fee_percent' => 0,
			'fee_amount'  => 0,
		),
		'Priority'  => array(
			'type'        => 'Priority',
			'enabled'     => 'yes',
			'fee_percent' => 0,
			'fee_amount'  => 0,
		),
	);

	/**
	 * Constructor.
	 *
	 * @param int $instance_id Shipping method instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'WC_Shipping_Amazon';
		$this->title              = __( 'Amazon', 'ns-fba-amazon' );
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Amazon', 'ns-fba-amazon' );
		$this->method_description = __( 'Custom Shipping Method for Amazon', 'ns-fba-amazon' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);

		$this->init();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		add_filter( 'woocommerce_shipping_' . $this->id . '_instance_settings_values', array( $this, 'shipping_amazon_instance_settings_values' ), 10, 2 );

	} // End __construct

	/**
	 * Load the settings
	 */
	public function init() {
		$this->init_form_fields();
		$this->init_settings();

	} // End function init

	/**
	 * Initialize shipping rates form controls
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'rates' => array(
				'title'       => __( 'Rates', 'ns-fba-amazon' ),
				'type'        => 'rate',
				'description' => 'Choose which Amazon shipping rates you\'d like to make available to customers at checkout',
			),
		);

		$encoded_rates = $this->get_option( 'rates' );

		if ( ! empty( $encoded_rates ) ) {

			$rates = json_decode( $encoded_rates, true );

			$rate_keys = array_keys( $this->custom_rates );

			foreach ( $rates as $key => $rate ) {
				if ( in_array( $key, $rate_keys, true ) ) {
					$this->custom_rates[ $key ] = $rate;
				}
			}
		}

	} // End function init_form_fields.

	/**
	 * Calculate shipping cost.
	 *
	 * @param array $package The package.
	 */
	public function calculate_shipping( $package = array() ) {
		$ns_fba          = NS_FBA::get_instance();
		$mcf_fulfillment = new NS_MCF_Fulfillment( $ns_fba );

		$shipping_speed_categories = array();

		foreach ( $this->custom_rates as $key => $rate ) {
			if ( 'yes' === $rate['enabled'] ) {
				$shipping_speed_categories[] = $key;
			}
		}

		$response = $mcf_fulfillment->get_fulfillment_orders_preview( $package, $shipping_speed_categories );

		if ( ! SP_API::is_error_in( $response ) ) {

			$payload = json_decode( $response['body'], true )['payload'];

			$index = 0;

			// phpcs:ignore
			// error_log( "\n\n\n\n calculate_shipping payload: \n\n" . print_r( $response, true ) );

			foreach ( $payload['fulfillmentPreviews'] as $fulfillment_previews ) {

				$rate_key = $fulfillment_previews['shippingSpeedCategory'];

				if ( isset( $this->custom_rates[ $rate_key ] ) &&
					'yes' === $this->custom_rates[ $rate_key ]['enabled'] ) {

					if ( ! isset( $fulfillment_previews['estimatedFees'] ) ) {
						// If the area is not supported, we should not proceed.
						continue;
					}

					$latest_arrival_date = $fulfillment_previews['fulfillmentPreviewShipments'][0]['latestArrivalDate'];

					$latest_arrival_date = ! empty( $latest_arrival_date ) ? sprintf( '(%s %s)', __( 'est. arrive by' ), ( new DateTime( $latest_arrival_date ) )->format( 'D, M jS' ) ) : '';

					$formatted_estimated_fee = $this->get_formatted_estimated_feeds( $fulfillment_previews['estimatedFees'] );
					$estimated_fee           = isset( $formatted_estimated_fee[ self::FBA_PER_UNIT_FULFILLMENT_FEE ] ) ? $formatted_estimated_fee[ self::FBA_PER_UNIT_FULFILLMENT_FEE ]['value'] : 0;

					if ( $this->custom_rates[ $rate_key ]['fee_percent'] > 0 ) {
						$estimated_fee += round( $this->custom_rates[ $rate_key ]['fee_percent'] * $estimated_fee / 100, 2 );
					}

					if ( $this->custom_rates[ $rate_key ]['fee_amount'] > 0 ) {
						$estimated_fee += $this->custom_rates[ $rate_key ]['fee_amount'];
					}

					$rate = array(
						'id'    => $this->id . '_' . $this->instance_id . '_' . $index,
						'label' => $this->title . ' ' . $rate_key . ' ' . $latest_arrival_date,
						'cost'  => $estimated_fee,
					);

					$this->add_rate( $rate );

					$index++;

				}
			}
		} else {
			$ns_fba->logger->add_entry( $response, 'wc', '_shipping_details' );
		}
	} // End function calculate_shipping.

	/**
	 * Get a formatted array with estimated feeds
	 *
	 * @param array $estimated_feeds The estimated feeds.
	 *
	 * @return array
	 */
	public function get_formatted_estimated_feeds( $estimated_feeds ): array {
		$result = array();

		foreach ( $estimated_feeds as $feed ) {
			$result[ $feed['name'] ] = $feed['amount'];
		}

		return $result;

	} // End function get_formatted_estimated_feeds.

	/**
	 * Render the rate control html.
	 *
	 * @param string $key The field key.
	 * @param array  $data The field data.
	 *
	 * @return string
	 */
	public function generate_rate_html( $key, $data ) {

		$field_key = $this->get_field_key( $key );

		$defaults = array(
			'title'             => '',
			'label'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		if ( ! $data['label'] ) {
			$data['label'] = $data['title'];
		}

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
			</th>
			<td class="forminp">
				<div><?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?></div>
				<table>
					<thead>
					<tr>
						<th><?php esc_html_e( 'Speed', 'ns-fba-amazon' ); ?></th>
						<th><?php esc_html_e( 'Enable', 'ns-fba-amazon' ); ?></th>
						<th>
							<?php esc_html_e( 'Price Adjustment ($)', 'ns-fba-amazon' ); ?>
							<span class="woocommerce-help-tip shipping-setting-help-tip" data-tip="<?php esc_html_e( 'Add an optional fixed markup to the cost returned by Amazon (e.g. if Amazon charges you $10 and you add a price adjustment of $1, the customer will be charged $11 for shipping).', 'ns-fba-amazon' ); ?>"></span>
						</th>
						<th>
							<?php esc_html_e( 'Price Adjustment (%)', 'ns-fba-amazon' ); ?>
							<span class="woocommerce-help-tip shipping-setting-help-tip" data-tip="<?php esc_html_e( 'Add an optional percentage-based markup to the cost returned by Amazon (e.g. if Amazon charges you $10 and you add a price adjustment of 20%, the customer will be charged $12 for shipping).', 'ns-fba-amazon' ); ?>"></span>
						</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $this->custom_rates as $rate_ley => $rate ) : ?>
						<?php $control_key = $field_key . '_' . $rate_ley; ?>
						<tr>
							<td><?php echo esc_attr( $rate_ley ); ?></td>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
									<label for="<?php echo esc_attr( $control_key ); ?>">
										<input <?php disabled( $data['disabled'], true ); ?> class="<?php echo esc_attr( $data['class'] ); ?>" type="checkbox" name="<?php echo esc_attr( 'enabled_' . $control_key ); ?>" id="<?php echo esc_attr( 'enabled_' . $control_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="1" <?php checked( $rate['enabled'], 'yes' ); ?> <?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?> />
									</label>
								</fieldset>
							</td>
							<td>
								<input type="number" name="<?php echo esc_attr( 'fee_amount_' . $control_key ); ?>" id="<?php echo esc_attr( 'fee_amount_' . $control_key ); ?>" class="<?php echo esc_attr( $data['class'] ); ?> txt-rate-data" value="<?php echo esc_attr( $rate['fee_amount'] > 0 ? $rate['fee_amount'] : '' ); ?>" />
							</td>
							<td>
								<input type="number" name="<?php echo esc_attr( 'fee_percent_' . $control_key ); ?>" id="<?php echo esc_attr( 'fee_percent_' . $control_key ); ?>" class="<?php echo esc_attr( $data['class'] ); ?> txt-rate-data" value="<?php echo esc_attr( $rate['fee_percent'] > 0 ? $rate['fee_percent'] : '' ); ?>" />
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php

		return ob_get_clean();

	} // End function generate_rate_html.

	/**
	 * Hook implementation for assigning of instance settings values.
	 *
	 * @param array $setting_values The setting values.
	 */
	public function shipping_amazon_instance_settings_values( $setting_values ) {

		$post_data = $this->get_post_data();

		if ( empty( $post_data ) ) {
			$setting_values['rates'] = wp_json_encode( $this->custom_rates );
			return $setting_values;
		}

		foreach ( $this->custom_rates as &$rate ) {

			$control_key = $this->plugin_id . $this->id . '_rates_' . $rate['type'];

			$enabled     = isset( $post_data[ 'enabled_' . $control_key ] );
			$fee_amount  = (float) $post_data[ 'fee_amount_' . $control_key ];
			$fee_percent = (float) $post_data[ 'fee_percent_' . $control_key ];

			$rate['enabled']     = $enabled ? 'yes' : 'no';
			$rate['fee_amount']  = $enabled && $fee_amount > 0 ? $fee_amount : '';
			$rate['fee_percent'] = $enabled && $fee_percent > 0 ? $fee_percent : '';

		}

		$setting_values['rates'] = wp_json_encode( $this->custom_rates );

		return $setting_values;

	} // End function shipping_amazon_instance_settings_values.
}
