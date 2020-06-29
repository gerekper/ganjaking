<?php
/**
 * Shipping method with distance rate.
 *
 * @package WC_Distance_Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Shipping_Distance_Rate class.
 *
 * @extends WC_Shipping_Method
 */
if ( ! class_exists( 'WC_Shipping_Distance_Rate' ) ) {

	/**
	 * Shipping method with distance rate.
	 */
	class WC_Shipping_Distance_Rate extends WC_Shipping_Method {

		/**
		 * Google Distance Matric API object.
		 *
		 * @var Object
		 */
		protected $api;

		/**
		 * Available distance rates and costs
		 *
		 * @var Object
		 */
		protected $available_rates;

		/**
		 * Notice to show users.
		 *
		 * @var string
		 */
		public $notice = '';

		/**
		 * Constructor.
		 *
		 * @param int $instance_id Instance ID.
		 *
		 * @return void
		 */
		public function __construct( $instance_id = 0 ) {
			$this->id           = 'distance_rate';
			$this->instance_id  = absint( $instance_id );
			$this->method_title = __( 'Distance Rate', 'woocommerce-distance-rate-shipping' );
			$this->supports     = array(
				'shipping-zones',
				'instance-settings',
				'settings',
			);
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

			$this->init();
		}

		/**
		 * Check if this shipping method is available or not.
		 *
		 * @param array $package Package to ship.
		 * @return bool
		 */
		public function is_available( $package ) {
			if ( empty( $package['destination']['country'] ) ) {
				return false;
			}

			if ( ! $this->get_rates( $package ) ) {
				return false;
			}

			return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
		}

		/**
		 * Initialize settings
		 *
		 * @version 1.0.5
		 * @since 1.0.5
		 * @return bool
		 */
		private function set_settings() {
			// Define user set variables.
			$this->title         = $this->get_option( 'title', $this->method_title );
			$this->origin        = $this->get_option( 'origin', '' );
			$this->debug         = ( 'yes' === $this->get_option( 'debug', 'no' ) ) ? true : false;
			$this->tax_status    = $this->get_option( 'tax_status', 'taxable' );
			$this->api_key       = $this->get_option( 'api_key', '' );
			$this->mode          = $this->get_option( 'mode', 'driving' );
			$this->avoid         = $this->get_option( 'avoid', 'none' );
			$this->unit          = $this->get_option( 'unit', 'metric' );
			$this->show_distance = ( 'yes' === $this->get_option( 'show_distance' ) );
			$this->show_duration = ( 'yes' === $this->get_option( 'show_duration' ) );
			$this->address_1     = $this->get_option( 'address_1', '' );
			$this->address_2     = $this->get_option( 'address_2', '' );
			$this->city          = $this->get_option( 'city', '' );
			$this->postal_code   = $this->get_option( 'postal_code', '' );
			$this->state_country = $this->get_option( 'state_country', '' );
			$this->country       = $this->get_option( 'country', '' );
			$this->rules         = $this->get_option( 'rules', array() );

			return true;
		}

		/**
		 * Init function.
		 *
		 * @access private
		 * @return void
		 */
		private function init() {
			// Load the settings.
			$this->init_form_fields();
			$this->set_settings();

			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'clear_transients' ) );
		}

		/**
		 * Process settings on save
		 *
		 * @access public
		 * @return void
		 */
		public function process_admin_options() {
			parent::process_admin_options();

			$this->set_settings();
		}

		/**
		 * Define form fields.
		 */
		public function init_form_fields() {
			$this->instance_form_fields = array(
				'title'            => array(
					'title'           => __( 'Method Title', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'text',
					'description'     => __( 'This controls the title which the user sees during checkout.', 'woocommerce-distance-rate-shipping' ),
					'default'         => __( 'Distance Rate', 'woocommerce-distance-rate-shipping' ),
				),
				'tax_status' => array(
					'title' 		=> __( 'Tax Status', 'woocommerce-distance-rate-shipping' ),
					'type' 			=> 'select',
					'default' 		=> 'taxable',
					'options'		=> array(
						'taxable' 	=> __( 'Taxable', 'woocommerce-distance-rate-shipping' ),
						'none' 		=> _x( 'None', 'Tax status', 'woocommerce-distance-rate-shipping' ),
					),
				),
				'mode'  => array(
					'title'           => __( 'Transportation Mode', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'select',
					'default'         => 'driving',
					'options'         => array(
						'driving'     => __( 'Driving', 'woocommerce-distance-rate-shipping' ),
						'walking'     => __( 'Walking', 'woocommerce-distance-rate-shipping' ),
						'bicycling'   => __( 'Bicycling', 'woocommerce-distance-rate-shipping' ),
					),
				),
				'avoid'  => array(
					'title'           => __( 'Avoid', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'select',
					'default'         => 'none',
					'options'         => array(
						'none'      => __( 'None', 'woocommerce-distance-rate-shipping' ),
						'tolls'     => __( 'Tolls', 'woocommerce-distance-rate-shipping' ),
						'highways'  => __( 'Highways', 'woocommerce-distance-rate-shipping' ),
						'ferries'   => __( 'Ferries', 'woocommerce-distance-rate-shipping' ),
					),
				),
				'unit'  => array(
					'title'           => __( 'Distance Unit', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'select',
					'default'         => 'metric',
					'options'         => array(
						'metric'      => __( 'Metric', 'woocommerce-distance-rate-shipping' ),
						'imperial'    => __( 'Imperial', 'woocommerce-distance-rate-shipping' ),
					),
				),
				'show_distance' => array(
					'title'           => __( 'Show distance', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'checkbox',
					'label'           => __( 'Show the distance next to the shipping cost for the customer.', 'woocommerce-distance-rate-shipping' ),
					'default'         => 'yes',
				),
				'show_duration' => array(
					'title'   => __( 'Show duration', 'woocommerce-distance-rate-shipping' ),
					'type'    => 'checkbox',
					'label'   => __( 'Show the duration next to the shipping cost for the customer.', 'woocommerce-distance-rate-shipping' ),
					'default' => 'no',
				),
				'distance_rate_address' => array(
					'title' 		=> __( 'Shipping Address', 'woocommerce-distance-rate-shipping' ),
					'type' 			=> 'title',
					'description' 	=> __( 'Please enter the address that you are shipping from below to work out the distance of the customer from the shipping location.', 'woocommerce-distance-rate-shipping' ),
				),
				'address_1' => array(
					'title'           => __( 'Address 1', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'text',
					'description'     => __( 'First address line of where you are shipping from.', 'woocommerce-distance-rate-shipping' ),
				),
				'address_2' => array(
					'title'           => __( 'Address 2', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'text',
					'description'     => __( 'Second address line of where you are shipping from.', 'woocommerce-distance-rate-shipping' ),
				),
				'city' => array(
					'title'           => __( 'City', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'text',
					'description'     => __( 'City of where you are shipping from.', 'woocommerce-distance-rate-shipping' ),
				),
				'postal_code'             => array(
					'title'           => __( 'Zip/Postal Code', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'text',
					'description'     => __( 'Zip or Postal Code of where you are shipping from.', 'woocommerce-distance-rate-shipping' ),
				),
				'state_province' => array(
					'title'           => __( 'State/Province', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'text',
					'description'     => __( 'State/Province of where you are shipping from.', 'woocommerce-distance-rate-shipping' ),
				),
				'country' => array(
					'title'           => __( 'Country', 'woocommerce-distance-rate-shipping' ),
					'type'            => 'text',
					'description'     => __( 'Country of where you are shipping from.', 'woocommerce-distance-rate-shipping' ),
				),
				'distance_rates_map' => array(
					'type' => 'distance_rates_map',
				),
				'rules' => array(
					'type' => 'distance_rate_rule_table',
				),
			);

			$this->form_fields = array(
				'api_key'		=> array(
					'title' => __( 'API Key', 'woocommerce-distance-rate-shipping' ),
					'type'	=> 'text',
					'description'	=> __( 'Your <a href="https://docs.woocommerce.com/document/woocommerce-distance-rate-shipping/#section-3">Google API Key</a>', 'woocommerce-distance-rate-shipping' ),
				),
				'debug' => array(
					'title'			=> __( 'Debug Mode', 'woocommerce-distance-rate-shipping' ),
					'type'			=> 'checkbox',
					'label'			=> __( 'Enable/Disable Debug Mode, display API calls on frontend', 'woocommerce-distance-rate-shipping' ),
					'default'		=> 'no',

				),
			);
		}

		/**
		 * Ouput a google map of the shipping location if set.
		 *
		 * @return string HTML of map.
		 */
		public function generate_distance_rates_map_html() {
			ob_start();
			$address_string = $this->get_shipping_address_string();
			if ( ! empty( $address_string ) ) {
				?>
				<tr valign="top">
					<td colspan="2">
						<iframe width="600" height="450" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=<?php echo urlencode( $this->get_shipping_address_string() ); ?>&key=<?php echo $this->api_key; ?>"></iframe>
					</td>
				</tr>
				<?php
			}
			return ob_get_clean();
		}

		/**
		 * Generate the rules table html.
		 *
		 * @return string HTML of rules table.
		 */
		public function generate_distance_rate_rule_table_html() {
			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc"><?php _e( 'Distance Rate Rules', 'woocommerce-distance-rate-shipping' ); ?>:</th>
				<td class="forminp" id="<?php echo $this->id; ?>_rules">
					<table class="shippingrows widefat" cellspacing="0">
						<thead>
							<tr>
								<th class="check-column"><input type="checkbox"></th>
								<th><?php esc_html_e( 'Condition', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'On what condition must the rule be applied.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th><?php esc_html_e( 'Min', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Minimum condition value, leave blank for no limit. Travel time based in minutes.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th><?php esc_html_e( 'Max', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Maximum condition value, leave blank for no limit. Travel time based in minutes.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th><?php esc_html_e( 'Fixed Cost', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Fixed cost for rule, exluding tax.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th style="padding-right: 20px;"><?php esc_html_e( 'Cost Per Distance / Minute', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Cost per distance unit, or cost per minute for total travel time, excluding tax.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th><?php esc_html_e( 'Handling Fee', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Fee excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th><?php esc_html_e( 'Break', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Check to not continue processing rules below the selected rule.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
                                <th><?php esc_html_e( 'Abort', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Check to disable the shipping method if the rule matches.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="8"><a href="#" class="add button"><?php _e( 'Add Distance Rule', 'woocommerce-distance-rate-shipping' ); ?></a> <a href="#" class="remove button"><?php _e( 'Delete selected rule', 'woocommerce-distance-rate-shipping' ); ?></a></th>
							</tr>
						</tfoot>
						<tbody class="distance_rates">
							<?php
							$i = -1;
							if ( $this->rules ) {
								foreach ( $this->rules as $rule ) {
									$i++;

									$checked_break = '';
									if ( 'yes' == $rule['break'] ) {
										$checked_break = 'checked="checked"';
									}

									$checked_abort = '';
									if ( isset( $rule['abort'] ) && 'yes' == $rule['abort'] ) {
										$checked_abort = 'checked="checked"';
									}

									$min_rule = $rule['min'];
									$max_rule = $rule['max'];

									if ( 'total' === $rule['condition'] ) {
										$min_rule = wc_format_localized_price( $rule['min'] );
										$max_rule = wc_format_localized_price( $rule['max'] );
									}

									echo '<tr class="distance_rule">
										<th class="check-column"><input type="checkbox" name="select" /></th>
										<td><select name="' . $this->id . '_condition[' . $i . ']" class="wc-shipping-distance-rate-condition">
											<option value="distance" ' . selected( $rule['condition'], 'distance', false ) . '>' . __( 'Distance', 'woocommerce-distance-rate-shipping' ) . '</option>
											<option value="time" ' . selected( $rule['condition'], 'time', false ) . '>' . __( 'Total Travel Time', 'woocommerce-distance-rate-shipping' ) . '</option>
											<option value="weight" ' . selected( $rule['condition'], 'weight', false ) . '>' . __( 'Weight', 'woocommerce-distance-rate-shipping' ) . '</option>
											<option value="total" ' . selected( $rule['condition'], 'total', false ) . '>' . __( 'Order Total', 'woocommerce-distance-rate-shipping' ) . '</option>
											<option value="quantity" ' . selected( $rule['condition'], 'quantity', false ) . '>' . __( 'Quantity', 'woocommerce-distance-rate-shipping' ) . '</option>
										</select></td>
								   		<td><input type="text" name="' . $this->id . '_min[' . $i . ']' . '" size="4" class="wc-shipping-distance-rate-min wc_input_price" value="' . esc_attr( $min_rule ) . '" /></td>
						   				<td><input type="text" name="' . $this->id . '_max[' . $i . ']' . '" size="4" class="wc-shipping-distance-rate-max wc_input_price" value="' . esc_attr( $max_rule ) . '"/></td>
										<td><input type="text" name="' . $this->id . '_cost[' . $i . ']' . '" value="' . wc_format_localized_price( $rule['cost'] ) . '" placeholder="' . wc_format_localized_price( 0 ) . '" size="4" class="wc_input_price" /></td>
										<td><input type="text" name="' . $this->id . '_cost_unit[' . $i . ']' . '" value="' . wc_format_localized_price( $rule['cost_unit'] ) . '" placeholder="' . wc_format_localized_price( 0 ) . '" size="4" class="wc_input_price" /></td>
										<td><input type="text" name="' . $this->id . '_fee[' . $i . ']' . '" value="' . wc_format_localized_price( $rule['fee'] ) . '" placeholder="' . wc_format_localized_price( 0 ) . '" size="4" class="wc_input_price" /></td>
										<td><input type="checkbox" name="' . $this->id . '_break[' . $i . ']' . '" ' . $checked_break . ' /></td>
										<td><input type="checkbox" name="' . $this->id . '_abort[' . $i . ']' . '" ' . $checked_abort . ' /></td>
									</tr>';
								}
							}
							?>
						</tbody>
					</table>
					<script type="text/javascript">
					jQuery(function() {

						jQuery('#<?php echo $this->id; ?>_rules').on( 'click', 'a.add', function(){

							var size = jQuery('#<?php echo $this->id; ?>_rules tbody .distance_rule').size();

							jQuery('<tr class="distance_rule">\
								<th class="check-column"><input type="checkbox" name="select" /></th>\
								<td><select name="<?php echo $this->id; ?>_condition[' + size + ']" class="wc-shipping-distance-rate-condition">\
									<option value="distance"><?php esc_html_e( 'Distance', 'woocommerce-distance-rate-shipping' ); ?></option>\
									<option value="time"><?php esc_html_e( 'Total Travel Time', 'woocommerce-distance-rate-shipping' ); ?></option>\
									<option value="weight"><?php esc_html_e( 'Weight', 'woocommerce-distance-rate-shipping' ); ?></option>\
									<option value="total"><?php esc_html_e( 'Order Total', 'woocommerce-distance-rate-shipping' ); ?></option>\
									<option value="quantity"><?php esc_html_e( 'Quantity', 'woocommerce-distance-rate-shipping' ); ?></option>\
								</select></td>\
								<td><input type="text" name="<?php echo $this->id; ?>_min[' + size + ']" size="4" class="wc-shipping-distance-rate-min wc_input_price" /></td>\
								<td><input type="text" name="<?php echo $this->id; ?>_max[' + size + ']" size="4" class="wc-shipping-distance-rate-max wc_input_price" /></td>\
								<td><input type="text" name="<?php echo $this->id; ?>_cost[' + size + ']" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" size="4" class="wc_input_price" /></td>\
								<td><input type="text" name="<?php echo $this->id; ?>_cost_unit[' + size + ']" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" size="4" class="wc_input_price" /></td>\
								<td><input type="text" name="<?php echo $this->id; ?>_fee[' + size + ']" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" size="4" class="wc_input_price" /></td>\
								<td><input type="checkbox" name="<?php echo $this->id; ?>_break[' + size + ']" /></td>\
								\<td><input type="checkbox" name="<?php echo $this->id; ?>_abort[' + size + ']" /></td>\
							</tr>').appendTo('#<?php echo $this->id; ?>_rules table tbody');

							jQuery( '.wc-shipping-distance-rate-condition' ).trigger( 'change' );
							return false;
						});

						// Remove row
						jQuery('#<?php echo $this->id; ?>_rules').on( 'click', 'a.remove', function(){
							var answer = confirm("<?php _e( 'Delete the selected rule?', 'woocommerce-distance-rate-shipping' ); ?>");
							if (answer) {
								jQuery('#<?php echo $this->id; ?>_rules table tbody tr th.check-column input:checked').each(function(i, el){
									jQuery(el).closest('tr').remove();
								});
							}
							return false;
						});

						const priceInputClass = 'wc_input_price';
						jQuery( '#<?php echo $this->id; ?>_rules' ).on( 'change', '.wc-shipping-distance-rate-condition', function() {
							switch ( jQuery( this ).val() ) {
								case 'distance':
								case 'time':
								case 'weight':
								case 'quantity':
									jQuery( this ).parents( 'tr.distance_rule' ).find( '.wc-shipping-distance-rate-min' ).removeClass( priceInputClass );
									jQuery( this ).parents( 'tr.distance_rule' ).find( '.wc-shipping-distance-rate-max' ).removeClass( priceInputClass );
									break;
								case 'total':
									jQuery( this ).parents( 'tr.distance_rule' ).find( '.wc-shipping-distance-rate-min' ).addClass( priceInputClass );
									jQuery( this ).parents( 'tr.distance_rule' ).find( '.wc-shipping-distance-rate-max' ).addClass( priceInputClass );
									break;
							}
						} );

						jQuery( '.wc-shipping-distance-rate-condition' ).trigger( 'change' );
					});
				</script>
				</td>
			</tr>
			<?php
			return ob_get_clean();
		}

		/**
		 * Save the rules.
		 *
		 * @param string $key Key.
		 *
		 * @return array
		 */
		public function validate_distance_rate_rule_table_field( $key ) {
			$rule_condition = array();
			$rule_min       = array();
			$rule_max       = array();
			$rule_cost      = array();
			$rule_cost_unit = array();
			$rule_fee       = array();
			$rule_break     = array();
			$rule_abort     = array();
			$rules          = array();

			if ( isset( $_POST[ $this->id . '_condition' ] ) ) {
				$rule_condition  = array_map( 'stripslashes', $_POST[ $this->id . '_condition' ] );
			}

			if ( isset( $_POST[ $this->id . '_min' ] ) ) {
				$rule_min  = array_map( 'stripslashes', $_POST[ $this->id . '_min' ] );
			}

			if ( isset( $_POST[ $this->id . '_max' ] ) ) {
				$rule_max  = array_map( 'stripslashes', $_POST[ $this->id . '_max' ] );
			}

			if ( isset( $_POST[ $this->id . '_cost' ] ) ) {
				$rule_cost  = array_map( 'stripslashes', $_POST[ $this->id . '_cost' ] );
			}

			if ( isset( $_POST[ $this->id . '_cost_unit' ] ) ) {
				$rule_cost_unit  = array_map( 'stripslashes', $_POST[ $this->id . '_cost_unit' ] );
			}

			if ( isset( $_POST[ $this->id . '_fee' ] ) ) {
				$rule_fee = array_map( 'stripslashes', $_POST[ $this->id . '_fee' ] );
			}

			if ( isset( $_POST[ $this->id . '_break' ] ) ) {
				$rule_break = array_map( 'stripslashes', $_POST[ $this->id . '_break' ] );
			}

			if ( isset( $_POST[ $this->id . '_abort' ] ) ) {
				$rule_abort = array_map( 'stripslashes', $_POST[ $this->id . '_abort' ] );
			}

			for ( $i = 0; $i <= count( $rule_condition ); $i++ ) {
				if ( isset( $rule_condition[ $i ] ) ) {

					if ( 'total' === $rule_condition[ $i ] ) {
						$rule_min[ $i ] = wc_format_decimal( $rule_min[ $i ] );
						$rule_max[ $i ] = wc_format_decimal( $rule_max[ $i ] );
					}

					$rule_cost[ $i ] = wc_format_decimal( $rule_cost[ $i ] );
					$rule_cost_unit[ $i ] = wc_format_decimal( $rule_cost_unit[ $i ] );

					if ( ! strstr( $rule_fee[ $i ], '%' ) ) {
						$rule_fee[ $i ] = wc_format_decimal( $rule_fee[ $i ] );
					} else {
						$rule_fee[ $i ] = wc_clean( $rule_fee[ $i ] );
					}

					if ( ! empty( $rule_min[ $i ] ) ) {
						// For quantity condition, we disallow floats.
						if ( 'quantity' === $rule_condition[ $i ] ) {
							$rule_min[ $i ] = intval( $rule_min[ $i ] );
						} else {
							$rule_min[ $i ] = wc_format_decimal( $rule_min[ $i ] );
						}
					}

					if ( ! empty( $rule_break[ $i ] ) ) {
						$rule_break[ $i ] = 'yes';
					} else {
						$rule_break[ $i ] = 'no';
					}

					if ( ! empty( $rule_abort[ $i ] ) ) {
						$rule_abort[ $i ] = 'yes';
					} else {
						$rule_abort[ $i ] = 'no';
					}

					if ( ! empty( $rule_max[ $i ] ) ) {
						// For quantity condition, we disallow floats.
						if ( 'quantity' === $rule_condition[ $i ] ) {
							$rule_max[ $i ] = intval( $rule_max[ $i ] );
						} else {
							$rule_max[ $i ] = wc_format_decimal( $rule_max[ $i ] );
						}
					}

					// Add to rules array.
					$rules[] = array(
						'condition' => $rule_condition[ $i ],
						'min'       => $rule_min[ $i ],
						'max'       => $rule_max[ $i ],
						'cost'      => $rule_cost[ $i ],
						'cost_unit' => $rule_cost_unit[ $i ],
						'fee'       => $rule_fee[ $i ],
						'break'     => $rule_break[ $i ],
						'abort'     => $rule_abort[ $i ],
					);
				}
			}

			return $rules;
		}

		/**
		 * Shows notices when shipping is not available.
		 *
		 * @access public
		 * @since 3.1.3
		 * @version 3.1.3
		 *
		 * @param string $notice        Notice message.
		 * @param bool   $cart_checkout Determine if we need to show in both cart and checkout pages.
		 */
		public function show_notice( $notice = '', $cart_checkout = true ) {
			$this->notice = $notice;

			add_filter( 'woocommerce_no_shipping_available_html', array( $this, 'get_notice' ) );

			if ( $cart_checkout ) {
				add_filter( 'woocommerce_cart_no_shipping_available_html', array( $this, 'get_notice' ) );
			}
		}

		/**
		 * Gets the currently set notice.
		 *
		 * @access public
		 * @since 3.1.3
		 * @version 3.1.3
		 * @return string Notice.
		 */
		public function get_notice() {
			return $this->notice;
		}

		/**
		 * Calculate shipping based on rules.
		 *
		 * @since 1.0.0
		 * @version 1.0.7
		 *
		 * @param  array $package Package to ship.
		 * @return array
		 */
		public function calculate_shipping( $package = array() ) {
			if ( $this->available_rates ) {
				foreach ( $this->available_rates as $rate ) {
					$this->add_rate( $rate );
				}
			}
		}

		/**
		 * Get rates based on rules.
		 *
		 * @since 1.0.20
		 * @version 1.0.20
		 *
		 * @param  array $package For specific package.
		 * @return array
		 */
		public function get_rates( $package = array() ) {
			$rates = array();

			// If they update information on the checkout and WC calls ajax,
			// but they still didn't enter a shipping address, skip calculation.
			if ( isset( $_GET['wc-ajax'] ) && 'update_order_review' === $_GET['wc-ajax'] && empty( $package['destination']['address'] ) ) {
				return;
			}

			// Make sure the customer address is not only the country code
			// as this means the customer has not yet entered an address.
			if ( 2 === strlen( $this->get_customer_address_string( $package ) )  ) {
				return;
			}

			// Get region based on customer address (GB uses CCTLD).
			$region = empty( $package['destination']['country'] ) ? '' : strtolower( $package['destination']['country'] );
			if ( 'gb' === $region ) {
				$region = 'uk';
			}

			$distance = $this->get_api()->get_distance( $this->get_shipping_address_string(), $this->get_customer_address_string( $package ), false, $this->mode, $this->avoid, $this->unit, $region );

			// Check if a valid response was received.
			if ( ! isset( $distance->rows[0] ) || 'OK' !== $distance->rows[0]->elements[0]->status ) {

				return;

			}

			$label_suffix = array();

			// Maybe display distance next to the cost.
			if ( $this->show_distance && is_object( $distance ) ) {
				$label_suffix[] = $distance->rows[0]->elements[0]->distance->text;
			}

			// Maybe display duration next to the cost.
			if ( $this->show_duration && is_object( $distance ) ) {
				$label_suffix[] = $distance->rows[0]->elements[0]->duration->text;
			}

			$label_suffix = ! empty( $label_suffix )
				? sprintf( ' (%s)', implode( '; ', $label_suffix ) )
				: '';

			$label_suffix = apply_filters( 'woocommerce_distance_rate_shipping_label_suffix', $label_suffix, $distance, $package );

			$travel_time_minutes = round( $distance->rows[0]->elements[0]->duration->value / 60 );
			$rounding_precision  = apply_filters( 'woocommerce_distance_rate_shipping_distance_rounding_precision', 1 );
			$distance_value      = $distance->rows[0]->elements[0]->distance->value;

			if ( 'imperial' === $this->unit ) {
				$distance = round( $distance_value * 0.000621371192, $rounding_precision );
			} else {
				$distance = round( $distance_value / 1000, $rounding_precision );
			}

			/**
			 * Filter the distance received by the api before the shipping rules are checked
			 *
			 * @param stdClass $distance
			 * @param integer $distance_value
			 * @param string $unit
			 */
			$distance = apply_filters( 'woocommerce_distance_rate_shipping_calculated_distance', $distance, $distance_value, $this->unit );

			$shipping_total = 0;

			$at_least_one_rule_found = false;

			foreach ( $this->rules as $rule ) {

				$rule_found = false;

				switch ( $rule['condition'] ) {
					case 'distance':
						$rule_cost = $this->distance_shipping( $rule, $distance, $package );

						if ( ! is_null( $rule_cost ) ) {
							$rule_found = true;
							$shipping_total += $rule_cost;
						} else {
							$this->show_notice( __( 'Sorry, that shipping location is beyond our shipping radius.', 'woocommerce-distance-rate-shipping' ) );
						}
					break;

					case 'time':
						$rule_cost = $this->time_shipping( $rule, $travel_time_minutes, $package );

						if ( ! is_null( $rule_cost ) ) {
							$rule_found = true;
							$shipping_total += $rule_cost;
						} else {
							$this->show_notice( __( 'Sorry, that shipping location is beyond our shipping travel time.', 'woocommerce-distance-rate-shipping' ) );
						}
					break;

					case 'weight':
						$rule_cost = $this->weight_shipping( $rule, $distance, $package );

						if ( ! is_null( $rule_cost ) ) {
							$rule_found = true;
							$shipping_total += $rule_cost;
						} else {
							$this->show_notice( __( 'Sorry, the total weight of your chosen items is beyond what we can ship.', 'woocommerce-distance-rate-shipping' ) );
						}
					break;

					case 'total':
						$rule_cost = $this->order_total_shipping( $rule, $distance, $package );
						if ( ! is_null( $rule_cost ) ) {
							$rule_found = true;
							$shipping_total += $rule_cost;
						} else {
							$this->show_notice( __( 'Sorry, the total order cost is beyond what we can ship.', 'woocommerce-distance-rate-shipping' ) );
						}
					break;

					case 'quantity':
						$rule_cost = $this->quantity_shipping( $rule, $distance, $package );
						if ( ! is_null( $rule_cost ) ) {
							$rule_found = true;
							$shipping_total += $rule_cost;
						} else {
							$this->show_notice( __( 'Sorry, the total quantity of items is beyond what we can ship.', 'woocommerce-distance-rate-shipping' ) );
						}
					break;
				}
				$at_least_one_rule_found = $at_least_one_rule_found || $rule_found;

				// Skip all rules if abort.
				if ( isset( $rule['abort'] ) && 'yes' === $rule['abort'] && $rule_found ) {
					return false;
				}

				// Skip other rules if break.
				if ( 'yes' === $rule['break'] && $rule_found ) {
					break;
				}
			}

			if ( $at_least_one_rule_found ) {
				$rates[] = array(
					'id'    => $this->get_rate_id(),
					'label' => $this->title . $label_suffix,
					'cost'  => $shipping_total,
				);
			}

			// None found?
			if ( count( $rates ) == 0 ) {
				return false;
			}

			// Set available
			$this->available_rates = $rates;

			return true;
		}

		/**
		 * Calculate shipping based on distance.
		 *
		 * @since 1.0.0
		 * @version 1.0.8
		 *
		 * @param  array  $rule     Rule.
		 * @param  int    $distance Distance.
		 * @param  object $package  Package to ship.
		 * @return int
		 */
		public function distance_shipping( $rule, $distance, $package ) {
			$min_match = false;
			$max_match = false;
			$rule_cost = null;

			if ( empty( $rule['min'] ) || $distance >= $rule['min'] ) {
				$min_match = true;
			}

			if ( empty( $rule['max'] ) || $distance <= $rule['max'] ) {
				$max_match = true;
			}

			if ( $min_match && $max_match ) {
				$rule_cost = 0;
				if ( ! empty( $rule['cost_unit'] ) ) {
					$rule_cost = $rule['cost_unit'] * $distance;
				}

				if ( ! empty( $rule['cost'] ) ) {
					$rule_cost += $rule['cost'];
				}

				if ( ! empty( $rule['fee'] ) ) {
					$rule_cost += $this->get_fee( $rule['fee'], $package['contents_cost'] );
				}
			}

			/**
			 * Filter the rule cost for distance shipping.
			 *
			 * @since 1.0.8
			 * @version 1.0.8
			 *
			 * @param float $rule_cost Calculated cost.
			 * @param array $rule      Rule in DRS' row.
			 * @param float $distance  Distance.
			 * @param array $package   Package to ship.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_distance_shipping',
				$rule_cost,
				$rule,
				$distance,
				$package
			);
		}

		/**
		 * Calculate shipping based on total travel time.
		 *
		 * @since 1.0.0
		 * @version 1.0.8
		 *
		 * @param  array  $rule                Rule.
		 * @param  int    $travel_time_minutes Travel time in minutes.
		 * @param  object $package             Package to ship.
		 * @return int
		 */
		public function time_shipping( $rule, $travel_time_minutes, $package ) {
			$min_match = false;
			$max_match = false;
			$rule_cost = null;

			if ( empty( $rule['min'] ) || $travel_time_minutes >= $rule['min'] ) {
				$min_match = true;
			}

			if ( empty( $rule['max'] ) || $travel_time_minutes <= $rule['max'] ) {
				$max_match = true;
			}

			if ( $min_match && $max_match ) {
				$rule_cost = 0;
				if ( ! empty( $rule['cost_unit'] ) ) {
					$rule_cost = $rule['cost_unit'] * $travel_time_minutes;
				}

				if ( ! empty( $rule['cost'] ) ) {
					$rule_cost += $rule['cost'];
				}

				if ( ! empty( $rule['fee'] ) ) {
					$rule_cost += $this->get_fee( $rule['fee'], $package['contents_cost'] );
				}
			}

			/**
			 * Filter the rule cost for time shipping.
			 *
			 * @since 1.0.8
			 * @version 1.0.8
			 *
			 * @param float $rule_cost         Calculated cost.
			 * @param array $rule              Rule in DRS' row.
			 * @param int $travel_time_minutes Travel time in minutes.
			 * @param array $package           Package to ship.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_time_shipping',
				$rule_cost,
				$rule,
				$travel_time_minutes,
				$package
			);
		}

		/**
		 * Calculate shipping based on weight.
		 *
		 * @since 1.0.0
		 * @version 1.0.8
		 *
		 * @param  array  $rule     Rule.
		 * @param  int    $distance Distance.
		 * @param  object $package  Package to ship.
		 * @return int
		 */
		public function weight_shipping( $rule, $distance, $package ) {
			$min_match = false;
			$max_match = false;
			$rule_cost = null;

			$total_weight = WC()->cart->cart_contents_weight;

			if ( isset( $total_weight ) && $total_weight > 0 ) {

				if ( empty( $rule['min'] ) || $total_weight >= $rule['min'] ) {
					$min_match = true;
				}

				if ( empty( $rule['max'] ) || $total_weight <= $rule['max'] ) {
					$max_match = true;
				}

				if ( $min_match && $max_match ) {
					$rule_cost = 0;

					if ( ! empty( $rule['cost_unit'] ) ) {
						$rule_cost = $rule['cost_unit'] * $distance;
					}

					if ( ! empty( $rule['cost'] ) ) {
						$rule_cost += $rule['cost'];
					}

					if ( ! empty( $rule['fee'] ) ) {
						$rule_cost += $this->get_fee( $rule['fee'], $package['contents_cost'] );
					}
				}
			}

			/**
			 * Filter the rule cost for distance shipping.
			 *
			 * @since 1.0.8
			 * @version 1.0.8
			 *
			 * @param float $rule_cost Calculated cost.
			 * @param array $rule      Rule in DRS' row.
			 * @param float $distance  Distance.
			 * @param array $package   Package to ship.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_weight_shipping',
				$rule_cost,
				$rule,
				$distance,
				$package
			);
		}

		/**
		 * Calculate shipping based on order total.
		 *
		 * @since 1.0.0
		 * @version 1.0.8
		 *
		 * @param  array  $rule     Rule.
		 * @param  int    $distance Distance.
		 * @param  object $package  Package to ship.
		 * @return int
		 */
		public function order_total_shipping( $rule, $distance, $package ) {
			$min_match = false;
			$max_match = false;
			$rule_cost = null;

			$order_total = $package['contents_cost'];

			if ( isset( $order_total ) && $order_total > 0 ) {

				if ( empty( $rule['min'] ) || $order_total >= $rule['min'] ) {
					$min_match = true;
				}

				if ( empty( $rule['max'] ) || $order_total <= $rule['max'] ) {
					$max_match = true;
				}

				if ( $min_match && $max_match ) {
					$rule_cost = 0;

					if ( ! empty( $rule['cost_unit'] ) ) {
						$rule_cost = $rule['cost_unit'] * $distance;
					}

					if ( ! empty( $rule['cost'] ) ) {
						$rule_cost += $rule['cost'];
					}

					if ( ! empty( $rule['fee'] ) ) {
						$rule_cost += $this->get_fee( $rule['fee'], $package['contents_cost'] );
					}
				}
			}

			/**
			 * Filter the rule cost for distance shipping.
			 *
			 * @since 1.0.8
			 * @version 1.0.8
			 *
			 * @param float $rule_cost Calculated cost.
			 * @param array $rule      Rule in DRS' row.
			 * @param float $distance  Distance.
			 * @param array $package   Package to ship.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_order_total_shipping',
				$rule_cost,
				$rule,
				$distance,
				$package
			);
		}

		/**
		 * Calculate shipping based on quantity.
		 *
		 * @since 1.0.0
		 * @version 1.0.8
		 *
		 * @param  array  $rule     Rule.
		 * @param  int    $distance Distance.
		 * @param  object $package  Package to ship.
		 * @return int
		 */
		public function quantity_shipping( $rule, $distance, $package ) {
			$min_match = false;
			$max_match = false;
			$rule_cost = null;

			$content_count = WC()->cart->get_cart_contents_count();

			if ( isset( $content_count ) && $content_count > 0 ) {

				if ( empty( $rule['min'] ) || $content_count >= $rule['min'] ) {
					$min_match = true;
				}

				if ( empty( $rule['max'] ) || $content_count <= $rule['max'] ) {
					$max_match = true;
				}

				if ( $min_match && $max_match ) {
					$rule_cost = 0;

					if ( ! empty( $rule['cost_unit'] ) ) {
						$rule_cost = $rule['cost_unit'] * $distance;
					}

					if ( ! empty( $rule['cost'] ) ) {
						$rule_cost += $rule['cost'];
					}

					if ( ! empty( $rule['fee'] ) ) {
						$rule_cost += $this->get_fee( $rule['fee'], $package['contents_cost'] );
					}
				}
			}

			/**
			 * Filter the rule cost for distance shipping.
			 *
			 * @since 1.0.8
			 * @version 1.0.8
			 *
			 * @param float $rule_cost Calculated cost.
			 * @param array $rule      Rule in DRS' row.
			 * @param float $distance  Distance.
			 * @param array $package   Package to ship.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_quantity_shipping',
				$rule_cost,
				$rule,
				$distance,
				$package
			);
		}

		/**
		 * Build customer address string from package.
		 *
		 * @param  array $package Package to ship.
		 * @return string
		 */
		public function get_customer_address_string( $package ) {
			$address = array();

			if ( ! empty( $package['destination']['address'] ) ) {
				$address[] = $package['destination']['address'];
			} else if ( ! empty( WC()->customer ) && ! empty( WC()->customer->get_shipping_address() ) ) {
				$address[] = WC()->customer->get_shipping_address();
			}

			if ( ! empty( $package['destination']['address_2'] ) ) {
				$address[] = $package['destination']['address_2'];
			} else if ( ! empty( WC()->customer ) && ! empty( WC()->customer->get_shipping_address_2() ) ) {
				$address[] = WC()->customer->get_shipping_address_2();
			}

			if ( ! empty( $package['destination']['city'] ) ) {
				$address[] = $package['destination']['city'];
			} else if ( ! empty( WC()->customer ) && ! empty( WC()->customer->get_shipping_city() ) ) {
				$address[] = WC()->customer->get_shipping_city();
			}

			if ( ! empty( $package['destination']['state'] ) ) {
				$state = $package['destination']['state'];
				$country = $package['destination']['country'];

				// Convert state code to full name if available
				if ( isset( WC()->countries->states[ $country ], WC()->countries->states[ $country ][ $state ] ) ) {
					$state = WC()->countries->states[ $country ][ $state ];
					$country = WC()->countries->countries[ $country ];
				}
				$address[] = $state;
			}

			// Cart page only has country, state and zipcodes.
			if ( ! empty( $package['destination']['postcode'] ) ) {
				$address[] = $package['destination']['postcode'];
			}

			if ( ! empty( $package['destination']['country'] ) ) {
				$country = $package['destination']['country'];

				// Convert country code to full name if available
				if ( isset( WC()->countries->countries[ $country ] ) ) {
					$country = WC()->countries->countries[ $country ];
				}
				$address[] = $country;
			}

			return implode( ', ', $address );
		}

		/**
		 * Get the shipping from address as string.
		 *
		 * @return string
		 */
		public function get_shipping_address_string() {
			$address = array();
			if ( ! empty( $this->address_1 ) ) {
				$address[] = $this->address_1;
			}

			if ( ! empty( $this->address_2 ) ) {
				$address[] = $this->address_2;
			}

			if ( ! empty( $this->city ) ) {
				$address[] = $this->city;
			}

			if ( ! empty( $this->postal_code ) ) {
				$address[] = $this->postal_code;
			}

			if ( ! empty( $this->state_province ) ) {
				$address[] = $this->state_province;
			}

			if ( ! empty( $this->country ) ) {
				$address[] = $this->country;
			}

			return implode( ', ', $address );
		}

		/**
		 * Return the API object.
		 *
		 * @return object WC_Google_Distance_Matrix_API
		 */
		public function get_api() {
			if ( is_object( $this->api ) ) {
				return $this->api;
			}

			require 'class-wc-google-distance-matrix-api.php';
			$api_key = $this->api_key;
			$debug = $this->debug;

			return $this->api = new WC_Google_Distance_Matrix_API( $api_key, $debug );
		}

		/**
		 * Clear transients.
		 *
		 * @access public
		 * @return void
		 */
		public function clear_transients() {
			global $wpdb;

			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_distance_rate_%') OR `option_name` LIKE ('_transient_timeout_distance_rate_%')" );
		}
	}
}
