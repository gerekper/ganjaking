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
					<table class="shippingrows widefat striped" cellspacing="0">
						<thead>
							<tr>
								<th class="check-column"><input type="checkbox"></th>
								<th class="head-col col-condition"><?php esc_html_e( 'Condition', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'On what condition must the rule be applied.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th class="head-col col-min"><?php esc_html_e( 'Min', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Minimum condition value, leave blank for no limit. Travel time based in minutes.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th class="head-col col-max"><?php esc_html_e( 'Max', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Maximum condition value, leave blank for no limit. Travel time based in minutes.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th class="head-col col-base-cost"><?php esc_html_e( 'Base Cost', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Base cost for rule, exluding tax. Other calculations will be added on top of this cost.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th class="head-col col-per-distance"><?php esc_html_e( 'Cost Per Distance / Minute', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Cost per distance unit, or cost per minute for total travel time, excluding tax. Will be added to Base cost.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th class="head-col col-unit"><?php esc_html_e( 'Unit', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Unit that is used for calculation', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th class="head-col col-multiply"><?php esc_html_e( 'Multiply by Qty', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Cost will be multiplied by quantity', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th class="head-col col-handling-fee"><?php esc_html_e( 'Handling Fee', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Fee excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Will be added to Base cost.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th class="head-col col-break"><?php esc_html_e( 'Break', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Check to not continue processing rules below the selected rule.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
								<th class="head-col col-abort"><?php esc_html_e( 'Abort', 'woocommerce-distance-rate-shipping' ); ?> <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Check to disable the shipping method if the rule matches.', 'woocommerce-distance-rate-shipping' ) ); ?>">[?]</a></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="11"><a href="#" class="add button"><?php _e( 'Add Distance Rule', 'woocommerce-distance-rate-shipping' ); ?></a> <a href="#" class="remove button"><?php _e( 'Delete selected rule', 'woocommerce-distance-rate-shipping' ); ?></a> <a href="#" class="advanced_mode turn_on button"><?php _e( 'Enable Advanced Mode', 'woocommerce-distance-rate-shipping' ); ?></a> <a href="#" class="advanced_mode turn_off button"><?php _e( 'Disable Advanced Mode', 'woocommerce-distance-rate-shipping' ); ?></a></th>
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

									$checked_per_qty = '';
									if ( isset( $rule['per_qty'] ) && 'yes' == $rule['per_qty'] ) {
										$checked_per_qty = 'checked="checked"';
									}

									$min_rule = isset( $rule['min'] ) ? $rule['min'] : '';
									$max_rule = isset( $rule['max'] ) ? $rule['max'] : '';

									if ( 'total' === $rule['condition'] ) {
										$min_rule = wc_format_localized_price( $min_rule );
										$max_rule = wc_format_localized_price( $max_rule );
									}

									// 'conditions' array is introduced on 1.0.31. This condition is for the compatibility prior to version 1.0.31.
									// To make sure the 'conditions' array has the correct value after the plugin is updated from 1.0.30 to 1.0.31.
									if ( ! isset( $rule['conditions'] ) && isset( $rule['condition'] ) ) {
										$rule['conditions'] = array(
											array(
												array(
													'condition' => $rule['condition'],
													'min'       => $min_rule,
													'max'       => $max_rule,
												)
											)
										);
									}

									// 'unit' array is introduced on 1.0.31. This condition is for the compatibility prior to version 1.0.31.
									// To make sure the 'unit' array has the correct value after the plugin is updated from 1.0.30 to 1.0.31.
									if ( ! isset( $rule['unit'] ) ) {
										$rule['unit'] = ( isset( $rule['condition'] ) && 'time' === $rule['condition'] ) ? 'time' : 'distance';
									}

									$table_conditions = $this->create_table_conditions( $i, $rule['conditions'] );

									echo '<tr class="distance_rule" data-row="' . $i . '">
										<th class="check-column"><input type="checkbox" name="select" /></th>
										<td colspan="3" class="table-condition-column">
											' . $table_conditions . '
										</td>
										<td><input type="text" name="' . $this->id . '_cost[' . $i . ']' . '" value="' . wc_format_localized_price( $rule['cost'] ) . '" placeholder="' . wc_format_localized_price( 0 ) . '" size="4" class="wc_input_price" /></td>
										<td><input type="text" name="' . $this->id . '_cost_unit[' . $i . ']' . '" value="' . wc_format_localized_price( $rule['cost_unit'] ) . '" placeholder="' . wc_format_localized_price( 0 ) . '" size="4" class="wc_input_price" /></td>
										<td><select name="' . $this->id . '_unit[' . $i . ']" class="wc-shipping-distance-rate-unit">
											<option value="distance" ' . selected( $rule['unit'], 'distance', false ) . '>' . __( 'Distance', 'woocommerce-distance-rate-shipping' ) . '</option>
											<option value="time" ' . selected( $rule['unit'], 'time', false ) . '>' . __( 'Travel Time', 'woocommerce-distance-rate-shipping' ) . '</option>
										</select></td>
										<td><input type="checkbox" name="' . $this->id . '_per_qty[' . $i . ']' . '" ' . $checked_per_qty . ' /></td>
										<td><input type="text" name="' . $this->id . '_fee[' . $i . ']' . '" value="' . wc_format_localized_price( $rule['fee'] ) . '" placeholder="' . wc_format_localized_price( 0 ) . '" size="4" class="wc_input_price" /></td>
										<td><input type="checkbox" name="' . $this->id . '_break[' . $i . ']' . '" ' . $checked_break . ' /></td>
										<td><input type="checkbox" name="' . $this->id . '_abort[' . $i . ']' . '" ' . $checked_abort . ' /></td>
									</tr>';
								}
							}
							?>
						</tbody>
					</table>
					<style>
						table.shippingrows thead th.head-col.col-condition {
							width: 167px;
							padding-left: 17px;
						}

						table.shippingrows thead th.head-col.col-min {
							width: 70px;
						}

						table.shippingrows thead th.head-col.col-max {
							width: 150px;
						}

						table.shippingrows thead th.head-col.col-per-distance {
							padding-left: 20px;
						}

						table.shippingrows tr.distance_rule td {
							vertical-align: top;
						}

						table.distance_rule_conditions tr,
						table.distance_rule_add_table_button tr {
							background-color: transparent !important;
						}

						table.distance_rule_add_table_button,
						table.distance_rule_conditions .add_condition_row,
						table.distance_rule_conditions .remove_condition_row,
						.button.advanced_mode.turn_off {
							display: none;
						}

						table.shippingrows.advanced_mode table.distance_rule_add_table_button{
							display: table;
						}
						
						table.shippingrows.advanced_mode table.distance_rule_conditions .add_condition_row,
						table.shippingrows.advanced_mode table.distance_rule_conditions .remove_condition_row {
							display: inline-block;
						}
						
						table.shippingrows table.distance_rule_conditions .add_condition_text {
							
						}

						table.shippingrows.advanced_mode table.distance_rule_conditions .add_condition_text,
						table.shippingrows table.distance_rule_conditions tr.distance_rule_group:last-child .add_condition_text {
							display: none;
						}

						table.shippingrows.advanced_mode .button.advanced_mode.turn_on {
							display: none;
						}

						table.shippingrows.advanced_mode .button.advanced_mode.turn_off {
							display: inline-block;
						}

						table.distance_rule_conditions {
							border-spacing: 0;
						}

						table.distance_rule_conditions tr.distance_rule_group td,
						table.distance_rule_conditions tr.distance_or_text td,
						table.distance_rule_add_table_button td {
							padding: 0px 5px 8px;
							vertical-align: middle;
						}

						table.distance_rule_conditions tr.distance_rule_group select.wc-shipping-distance-rate-condition {
							width: 155px;
						}

						table.distance_rule_conditions tr.distance_rule_group td input.wc-shipping-distance-rate-min {
							width: 55px !important;
						}

						table.distance_rule_conditions tr.distance_rule_group td input.wc-shipping-distance-rate-max {
							width: 55px !important;
						}

						table.distance_rule_conditions a.remove_condition_row {
							font-size: 16px;
						}

						table.distance_rule_conditions a.remove_condition_row:hover {
							color: #aa0000;
						}
					</style>
					<script type="text/javascript">
					function wc_generate_distance_rule_conditions_row( size, group, row, is_first_table = false ) {
						var remove_button = ( true !== is_first_table ) ? '<a href="#" class="remove_condition_row dashicons dashicons-no">&nbsp;</a>' : '';

						return '\
							<tr class="distance_rule_group distance_rule_group_' + group + ' distance_rule_group_'+ group +'_row_' + row + '" data-group="' + group + '">\
								<td><select name="<?php echo $this->id; ?>_conditions[' + size + '][' + group + '][' + row + '][condition]" class="wc-shipping-distance-rate-condition">\
									<option value="distance"><?php esc_html_e( 'Distance', 'woocommerce-distance-rate-shipping' ); ?></option>\
									<option value="time"><?php esc_html_e( 'Total Travel Time', 'woocommerce-distance-rate-shipping' ); ?></option>\
									<option value="weight"><?php esc_html_e( 'Weight', 'woocommerce-distance-rate-shipping' ); ?></option>\
									<option value="total"><?php esc_html_e( 'Order Total', 'woocommerce-distance-rate-shipping' ); ?></option>\
									<option value="quantity"><?php esc_html_e( 'Quantity', 'woocommerce-distance-rate-shipping' ); ?></option>\
								</select></td>\
								<td><input type="text" name="<?php echo $this->id; ?>_conditions[' + size + '][' + group + '][' + row + '][min]" size="4" class="wc-shipping-distance-rate-min wc_input_price" /></td>\
								<td><input type="text" name="<?php echo $this->id; ?>_conditions[' + size + '][' + group + '][' + row + '][max]" size="4" class="wc-shipping-distance-rate-max wc_input_price" /></td>\
								<td><button class="add_condition_row button"><?php _e( 'and', 'woocommerce-distance-rate-shipping'); ?></button><span class="add_condition_text"><?php _e( 'and', 'woocommerce-distance-rate-shipping'); ?></span></td>\
								<td>' + remove_button + '</td>\
							</tr>';
					}

					function wc_generate_distance_or_text(){
						return '\
							<tr class="distance_or_text">\
								<td colspan="3"><?php _e( 'or', 'woocommerce-distance-rate-shipping' ); ?></td>\
							</tr>\
						';
					}

					function wc_generate_distance_rule_conditions_table( size, group, row, is_first_row = true, is_first_table = false ) {
						var row = wc_generate_distance_rule_conditions_row( size, group, row, is_first_table );
						
						if ( true !== is_first_row ) {
							row = wc_generate_distance_or_text() + row;
						}
						return '\
							<table class="distance_rule_conditions">\
							<tbody>\
								' + row  + '\
							</tbody>\
							</table>';
					}

					function wc_generate_distance_rule_conditions_final_table( size, group, row ) {
						var table = wc_generate_distance_rule_conditions_table( size, group, row, true, true );
						
						return table  + '\
							<table class="distance_rule_add_table_button">\
								' + wc_generate_distance_or_text() + '\
								<tr>\
									<td colspan="3" class="distance_or_button"><button class="or_condition_row button"><?php _e( 'Add rule group', 'woocommerce-distance-rate-shipping'); ?></button></td>\
								</tr>\
							</table>';
					}

					function wc_event_add_rule_button_on_click( evt ) {
						evt.preventDefault();
						
						var parent_dr     = jQuery( this ).closest( '.distance_rule' );
						var parent_table  = jQuery( this ).closest( '.distance_rule_add_table_button' );
						var parent_column = parent_table.closest( '.table-condition-column' );
						var table_count   = parent_column.find('.distance_rule_conditions').length;
						var row_number    = parent_dr.data( 'row' );

						parent_table.before( wc_generate_distance_rule_conditions_table( row_number, table_count, 0, false, false ) );

						parent_column.find('.distance_rule_conditions').last().find( '.add_condition_row.button').on( 'click', wc_event_add_row_button_on_click );
						parent_column.find('.distance_rule_conditions').last().find( '.remove_condition_row').on( 'click', wc_event_remove_row_button_on_click );
					}

					function wc_event_add_row_button_on_click( evt ) {
						evt.preventDefault();

						var parent_table = jQuery( this ).closest( '.distance_rule_conditions' );
						var parent_dr    = parent_table.closest( '.distance_rule' );
						var parent_tr    = jQuery( this ).closest( 'tr' );
						var group        = parent_tr.data( 'group' );
						var group_class  = '.distance_rule_group_' + group;
						var row_length   = parent_table.find( group_class ).length;
						var row_number   = parent_dr.data( 'row' );

						parent_table.find('tbody').append( wc_generate_distance_rule_conditions_row( row_number, group, row_length, false ) );

						parent_table.find( 'tr' ).last().find( '.add_condition_row.button').on( 'click', wc_event_add_row_button_on_click );
						parent_table.find( 'tr' ).last().find( '.remove_condition_row').on( 'click', wc_event_remove_row_button_on_click );
					}

					function wc_event_remove_row_button_on_click( evt ) {
						evt.preventDefault();
						
						var parent_tr    = jQuery( this ).closest( 'tr' );
						var parent_table = parent_tr.closest( '.distance_rule_conditions' );
						var row_length   = parent_table.find( 'tr.distance_rule_group' ).length;

						if ( row_length == 1 ) {
							parent_table.remove();
						} else {
							parent_tr.remove();
						}
					}

					jQuery(function() {
						jQuery( '.or_condition_row.button' ).on( 'click', wc_event_add_rule_button_on_click );
						jQuery( '.add_condition_row.button').on( 'click', wc_event_add_row_button_on_click );
						jQuery( '.remove_condition_row').on( 'click', wc_event_remove_row_button_on_click );

						var is_adv_mode_activated = false;

						jQuery( '#<?php echo $this->id; ?>_rules tr.distance_rule' ).each( function( idx ){
							var rule_group_length = jQuery( this ).find( '.distance_rule_group' ).length;

							if ( rule_group_length > 1 ) {
								is_adv_mode_activated = true;
							}
						});

						if ( is_adv_mode_activated ) {
							jQuery('#<?php echo $this->id; ?>_rules .shippingrows').addClass( 'advanced_mode' );
						}

						jQuery('#<?php echo $this->id; ?>_rules').on( 'click', 'a.add', function(){

							var size             = jQuery('#<?php echo $this->id; ?>_rules tbody .distance_rule').length;
							var table_conditions = wc_generate_distance_rule_conditions_final_table( size, 0 , 0 );
							var distance_rates   = jQuery( '#<?php echo $this->id; ?>_rules table.shippingrows tbody.distance_rates' );

							var new_distance_row = jQuery('<tr class="distance_rule" data-row="' + size + '">\
								<th class="check-column"><input type="checkbox" name="select" /></th>\
								<td colspan="3" class="table-condition-column">' + table_conditions + '</td>\
								<td><input type="text" name="<?php echo $this->id; ?>_cost[' + size + ']" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" size="4" class="wc_input_price" /></td>\
								<td><input type="text" name="<?php echo $this->id; ?>_cost_unit[' + size + ']" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" size="4" class="wc_input_price" /></td>\
								<td><select name="<?php echo $this->id; ?>_unit[' + size + ']" class="wc-shipping-distance-rate-unit">\
									<option value="distance"><?php esc_html_e( 'Distance', 'woocommerce-distance-rate-shipping' ); ?></option>\
									<option value="time"><?php esc_html_e( 'Travel Time', 'woocommerce-distance-rate-shipping' ); ?></option>\
								</select></td>\
								<td><input type="checkbox" name="<?php echo $this->id; ?>_per_qty[' + size + ']" /></td>\
								<td><input type="text" name="<?php echo $this->id; ?>_fee[' + size + ']" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" size="4" class="wc_input_price" /></td>\
								<td><input type="checkbox" name="<?php echo $this->id; ?>_break[' + size + ']" /></td>\
								\<td><input type="checkbox" name="<?php echo $this->id; ?>_abort[' + size + ']" /></td>\
							</tr>');

							new_distance_row.appendTo( distance_rates );

							new_distance_row.find( '.or_condition_row.button' ).on( 'click', wc_event_add_rule_button_on_click );
							new_distance_row.find( '.add_condition_row.button').on( 'click', wc_event_add_row_button_on_click );
							new_distance_row.find( '.remove_condition_row').on( 'click', wc_event_remove_row_button_on_click );

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

						jQuery('#<?php echo $this->id; ?>_rules').on( 'click', 'a.advanced_mode', function(){
							jQuery('#<?php echo $this->id; ?>_rules .shippingrows').toggleClass( 'advanced_mode' );
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
						jQuery( '.shippingrows .distance_rule, .distance_rule_conditions .distance_rule_group, .distance_rule_add_table_button .distance_or_text' ).removeClass( 'alternate' );
					});
				</script>
				</td>
			</tr>
			<?php
			return ob_get_clean();
		}

		public function re_order_array( $array ) {
			if ( ! is_array( $array ) ) {
				 return $array;
			}

			$count  = 0;
			$result = array();
			
			foreach( $array as $k => $v ) {
				if ( $this->is_integer_value( $k ) ) {
				   $result[$count] = $this->re_order_array( $v );
				   
				   ++$count;
				} else {
				  $result[$k] = $this->re_order_array( $v );
				}
			}

			return $result;
		}
	
		public function is_integer_value($value) {
			if ( ! is_int( $value ) ) {
				if( is_string( $value ) && preg_match( "/^-?\d+$/i", $value ) ) {
					return true;
				}

				return false;
			}

			return true;
		}

		/**
		 * Save the rules.
		 *
		 * @param string $key Key.
		 *
		 * @return array
		 */
		public function validate_distance_rate_rule_table_field( $key ) {
			$rule_conditions = array();
			$rule_condition  = array();
			$rule_min        = array();
			$rule_max        = array();
			$rule_cost       = array();
			$rule_cost_unit  = array();
			$rule_unit       = array();
			$rule_per_qty    = array();
			$rule_fee        = array();
			$rule_break      = array();
			$rule_abort      = array();
			$rules           = array();

			if ( isset( $_POST[ $this->id . '_conditions' ] ) ) {
				$rule_conditions = $this->re_order_array( $_POST[ $this->id . '_conditions' ] );
			}

			// Not processing the rules if it's not an array.
			if ( ! is_array( $rule_conditions ) ) {
				return $rules;
			}

			if ( isset( $_POST[ $this->id . '_condition' ] ) ) {
				$rule_condition = array_map( 'stripslashes', $_POST[ $this->id . '_condition' ] );
			}

			if ( isset( $_POST[ $this->id . '_min' ] ) ) {
				$rule_min = array_map( 'stripslashes', $_POST[ $this->id . '_min' ] );
			}

			if ( isset( $_POST[ $this->id . '_max' ] ) ) {
				$rule_max = array_map( 'stripslashes', $_POST[ $this->id . '_max' ] );
			}

			if ( isset( $_POST[ $this->id . '_cost' ] ) ) {
				$rule_cost = array_map( 'stripslashes', $_POST[ $this->id . '_cost' ] );
			}

			if ( isset( $_POST[ $this->id . '_cost_unit' ] ) ) {
				$rule_cost_unit = array_map( 'stripslashes', $_POST[ $this->id . '_cost_unit' ] );
			}

			if ( isset( $_POST[ $this->id . '_unit' ] ) ) {
				$rule_unit = array_map( 'stripslashes', $_POST[ $this->id . '_unit' ] );
			}

			if ( isset( $_POST[ $this->id . '_per_qty' ] ) ) {
				$rule_per_qty = array_map( 'stripslashes', $_POST[ $this->id . '_per_qty' ] );
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

			for ( $i = 0; $i <= count( $rule_conditions ); $i++ ) {

				if ( isset( $rule_conditions[ $i ] ) ) {

					foreach ( $rule_conditions[ $i ] as $or_idx => $or_conditions ) {
						foreach( $or_conditions as $and_idx => $and_rule ) {
							$min       = stripslashes( $and_rule['min'] );
							$max       = stripslashes( $and_rule['max'] );
							$condition = stripslashes( $and_rule['condition'] );

							$rule_conditions[ $i ][ $or_idx ][ $and_idx ]['min']       = $min;
							$rule_conditions[ $i ][ $or_idx ][ $and_idx ]['max']       = $max;
							$rule_conditions[ $i ][ $or_idx ][ $and_idx ]['condition'] = $condition;

							if ( 'total' === $condition ) {
								$rule_conditions[ $i ][ $or_idx ][ $and_idx ]['min'] = wc_format_decimal( $min );
								$rule_conditions[ $i ][ $or_idx ][ $and_idx ]['max'] = wc_format_decimal( $max );
							}

							if ( ! empty( $min ) ) {
								// For quantity condition, we disallow floats.
								if ( 'quantity' === $condition ) {
									$rule_conditions[ $i ][ $or_idx ][ $and_idx ]['min'] = intval( $min );
								} else {
									$rule_conditions[ $i ][ $or_idx ][ $and_idx ]['min'] = wc_format_decimal( $min );
								}
							}

							if ( ! empty( $max ) ) {
								// For quantity condition, we disallow floats.
								if ( 'quantity' === $condition ) {
									$rule_conditions[ $i ][ $or_idx ][ $and_idx ]['max'] = intval( $max );
								} else {
									$rule_conditions[ $i ][ $or_idx ][ $and_idx ]['max'] = wc_format_decimal( $max );
								}
							}
						}
					}

					if ( isset( $rule_condition[ $i ] ) && 'total' === $rule_condition[ $i ] ) {
						$rule_min[ $i ] = wc_format_decimal( $rule_min[ $i ] );
						$rule_max[ $i ] = wc_format_decimal( $rule_max[ $i ] );
					}

					$rule_cost[ $i ] = wc_format_decimal( $rule_cost[ $i ] );
					$rule_cost_unit[ $i ] = wc_format_decimal( $rule_cost_unit[ $i ] );

					if ( empty( $rule_unit[ $i ] ) ) {
						$rule_unit[ $i ] = 'distance';
					}

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

					if ( ! empty( $rule_per_qty[ $i ] ) ) {
						$rule_per_qty[ $i ] = 'yes';
					} else {
						$rule_per_qty[ $i ] = 'no';
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

					if ( empty( $rule_condition[ $i ] ) ) {
						$rule_condition[ $i ] = '';
					}

					if ( empty( $rule_max[ $i ] ) ) {
						$rule_max[ $i ] = '';
					}

					if ( empty( $rule_min[ $i ] ) ) {
						$rule_min[ $i ] = '';
					}

					// Add to rules array.
					$rules[] = array(
						'conditions' => $rule_conditions[ $i ],
						'condition'  => $rule_condition[ $i ],
						'min'        => $rule_min[ $i ],
						'max'        => $rule_max[ $i ],
						'cost'       => $rule_cost[ $i ],
						'cost_unit'  => $rule_cost_unit[ $i ],
						'unit'       => $rule_unit[ $i ],
						'per_qty'    => $rule_per_qty[ $i ],
						'fee'        => $rule_fee[ $i ],
						'break'      => $rule_break[ $i ],
						'abort'      => $rule_abort[ $i ],
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

			/*
			 * Make sure the customer address is not only the country code.
			 * as this means the customer has not yet entered an address.
			 */ 
			if ( 2 === strlen( $this->get_customer_address_string( $package, false ) )  ) {
				return;
			}

			// Get region based on customer address (GB uses CCTLD).
			$region = empty( $package['destination']['country'] ) ? '' : strtolower( $package['destination']['country'] );
			if ( 'gb' === $region ) {
				$region = 'uk';
			}

			// Get language code to allow localization.
			$language = get_bloginfo("language");
			
			$distance = $this->get_api()->get_distance( $this->get_shipping_address_string(), $this->get_customer_address_string( $package ), false, $this->mode, $this->avoid, $this->unit, $region, $language );

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

			// Get product quantities from package contents.
			$package_quantities  = array_map( function( $package_items ) {

				return ! empty( $package_items['quantity'] ) ? $package_items['quantity'] : 0;

			}, $package['contents'] );
			
			// Calculate the product quantities.
			$quantity            = is_array( $package_quantities ) ? array_sum( $package_quantities ) : 0;
			$weight              = WC()->cart->cart_contents_weight;
			$order_total         = $package['cart_subtotal'];

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
				$final_result = null;

				if ( ! isset( $rule['conditions'] ) && isset( $rule['condition'] ) ) {
					$rule['conditions'] = array(
						array(
							array(
								'condition' => $rule['condition'],
								'min'       => $rule['min'],
								'max'       => $rule['max'],
							),
						),
					);
				}

				$first_condition     = '';
				$more_than_one_rules = false;
				
				if ( count( $rule['conditions'] ) > 0 ) {
					foreach ( $rule['conditions'] as $or_idx => $or_conditions ) {
						$and_result = null;
	
						foreach( $or_conditions as $and_idx => $and_rule ) {
							$result = false;

							if ( empty( $first_condition ) ) {
								$first_condition = $and_rule['condition'];
							} else {
								$more_than_one_rules = true;
							}
	
							switch( $and_rule['condition'] ) {
								case 'distance':
									$result = $this->distance_shipping_rule( $and_rule, $distance, $package, $distance_value );
								break;
	
								case 'time':
									$result = $this->time_shipping_rule( $and_rule, $travel_time_minutes, $package );
								break;
	
								case 'weight':
									$result = $this->weight_shipping_rule( $and_rule, $distance, $package, $weight );
								break;
	
								case 'total':
									$result = $this->order_total_shipping_rule( $and_rule, $distance, $package, $order_total );
								break;
	
								case 'quantity':
									$result = $this->quantity_shipping_rule( $and_rule, $distance, $package, $quantity );
								break;
							}
	
							$and_result = ( ! is_bool( $and_result) ) ? $result : ( $result && $and_result );
						}
	
						$final_result = ( ! is_bool( $final_result ) ) ? $and_result : ( $and_result || $final_result );
					}
				}
				
				$rule_found = false;

				if ( true === $final_result ) {
					$rule_found = true;

					// For backward compatibility. Use the condition to determine the total_unit.
					if ( ! isset( $rule['unit'] ) ) {
						$total_unit = ( isset( $rule['condition'] ) && 'time' === $rule['condition'] ) ? $travel_time_minutes : $distance;
					
					// Use distance value if the unit used is not 'time';
					} else {
						$total_unit = ( 'time' === $rule['unit'] ) ? $travel_time_minutes : $distance;
					}

					$rule_cost       = $this->rule_cost_calculation( $rule, $total_unit, $quantity, $package );
					$shipping_total += $rule_cost;
				
				} elseif ( false === $more_than_one_rules ) {
					switch ( $first_condition ) {
						case 'distance':
							$this->show_notice( __( 'Sorry, that shipping location is beyond our shipping radius.', 'woocommerce-distance-rate-shipping' ) );
						break;

						case 'time':
							$this->show_notice( __( 'Sorry, that shipping location is beyond our shipping travel time.', 'woocommerce-distance-rate-shipping' ) );
						break;

						case 'weight':
							$this->show_notice( __( 'Sorry, the total weight of your chosen items is beyond what we can ship.', 'woocommerce-distance-rate-shipping' ) );
						break;

						case 'total':
							$this->show_notice( __( 'Sorry, the total order cost is beyond what we can ship.', 'woocommerce-distance-rate-shipping' ) );
						break;

						case 'quantity':
							$this->show_notice( __( 'Sorry, the total quantity of items is beyond what we can ship.', 'woocommerce-distance-rate-shipping' ) );
						break;
					}
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

		public function create_table_conditions( $rule_row, $conditions = array() ) {
			$table_conditions = '';

			if ( count( $conditions ) > 0 ) {
				foreach( $conditions as $idx_group => $or_conditions ) {
					$row_conditions = '';

					foreach( $or_conditions as $idx_and => $and_rule ) {
						$condition = $conditions[ $idx_group ][ $idx_and ]['condition'];
						$min       = $conditions[ $idx_group ][ $idx_and ]['min'];
						$max       = $conditions[ $idx_group ][ $idx_and ]['max'];
						$array_str = 'conditions[' . $rule_row . '][' . $idx_group . '][' . $idx_and . ']';
						$row_class = sprintf( 'distance_rule_group distance_rule_group_%1$s distance_rule_group_%1$s_row_%2$s', $idx_group, $idx_and );

						if ( empty( $table_conditions ) && empty( $row_conditions ) ) {
							$remove_button = '';
						} else {
							$remove_button = '<a href="#" class="remove_condition_row dashicons dashicons-no">&nbsp;</a>';
						}

						$row_conditions .= '
						<tr class="'. esc_attr( $row_class ) .'" data-group="' . esc_attr( $idx_group ) . '">
							<td>
								<select name="' . $this->id . '_'. $array_str .'[condition]" class="wc-shipping-distance-rate-condition">
									<option value="distance" ' . selected( $condition, 'distance', false ) . '>' . __( 'Distance', 'woocommerce-distance-rate-shipping' ) . '</option>
									<option value="time" ' . selected( $condition, 'time', false ) . '>' . __( 'Total Travel Time', 'woocommerce-distance-rate-shipping' ) . '</option>
									<option value="weight" ' . selected( $condition, 'weight', false ) . '>' . __( 'Weight', 'woocommerce-distance-rate-shipping' ) . '</option>
									<option value="total" ' . selected( $condition, 'total', false ) . '>' . __( 'Order Total', 'woocommerce-distance-rate-shipping' ) . '</option>
									<option value="quantity" ' . selected( $condition, 'quantity', false ) . '>' . __( 'Quantity', 'woocommerce-distance-rate-shipping' ) . '</option>
								</select>
							</td>
							<td><input type="text" name="' . $this->id . '_'. $array_str .'[min]" size="4" class="wc-shipping-distance-rate-min wc_input_price" value="' . esc_attr( $min ) . '" /></td>
							<td><input type="text" name="' . $this->id . '_'. $array_str .'[max]" size="4" class="wc-shipping-distance-rate-max wc_input_price" value="' . esc_attr( $max ) . '"/></td>
							<td><button class="add_condition_row button">' . __( 'and', 'woocommerce-distance-rate-shipping') . '</button><span class="add_condition_text">'. __( 'and', 'woocommerce-distance-rate-shipping' ) .'</span></td>
							<td>' . $remove_button . '</td>
						</tr>';
					}

					if ( ! empty( $table_conditions ) ) {
						$row_conditions = '
						<tr class="distance_or_text">
							<td colspan="3">' . __( 'or', 'woocommerce-distance-rate-shipping' ) . '</td>
						</tr>' . $row_conditions;
					}

					$table_conditions .= '
					<table class="distance_rule_conditions">
					<tbody>
						' . $row_conditions . '
					</tbody>
					</table>';
				}
			} else {
				$array_str = '[' . $rule_row . '][0][0]';
				$row_class = sprintf( 'distance_rule_group distance_rule_group_%1$s distance_rule_group_%1$s_row_%2$s', '0', '0' );

				$table_conditions .= '
				<table class="distance_rule_conditions">
				<tbody>
				<tr class="'. esc_attr( $row_class ) .'" data-group="0">
					<td>
						<select name="' . $this->id . '_condition'. $array_str .'" class="wc-shipping-distance-rate-condition">
							<option value="distance">' . __( 'Distance', 'woocommerce-distance-rate-shipping' ) . '</option>
							<option value="time">' . __( 'Total Travel Time', 'woocommerce-distance-rate-shipping' ) . '</option>
							<option value="weight">' . __( 'Weight', 'woocommerce-distance-rate-shipping' ) . '</option>
							<option value="total">' . __( 'Order Total', 'woocommerce-distance-rate-shipping' ) . '</option>
							<option value="quantity">' . __( 'Quantity', 'woocommerce-distance-rate-shipping' ) . '</option>
						</select>
					</td>
					<td><input type="text" name="' . $this->id . '_min'. $array_str .'" size="4" class="wc-shipping-distance-rate-min wc_input_price" value="" /></td>
					<td><input type="text" name="' . $this->id . '_max'. $array_str .'" size="4" class="wc-shipping-distance-rate-max wc_input_price" value=""/></td>
					<td><button class="add_condition_row button">' . __( 'and', 'woocommerce-distance-rate-shipping') . '</button><span class="add_condition_text">'. __( 'and', 'woocommerce-distance-rate-shipping' ) .'</span></td>
					<td></td>
				</tr>
				</tbody>
				</table>';
			}

			return $table_conditions . '
				<table class="distance_rule_add_table_button">
					<tr class="distance_or_text">
						<td colspan="3">' . __( 'or', 'woocommerce-distance-rate-shipping' ) . '</td>
					</tr>
					<tr>
						<td colspan="3" class="distance_or_button"><button class="or_condition_row button">' . __( 'Add rule group', 'woocommerce-distance-rate-shipping') . '</button></td>
					</tr>
				</table>';
		}

		/**
		 * Calculate shipping based on the chosen unit.
		 *
		 * @since 1.0.29
		 * @version 1.0.29
		 *
		 * @param  array  $rule       Rule.
		 * @param  float  $total_unit Value of the chosen unit.
		 * @param  int    $quantity   Cart quantity.
		 * @param  object $package  Package to ship.
		 * 
		 * @return float  Cost of the shipping.
		 */
		public function rule_cost_calculation( $rule, $total_unit, $quantity, $package ) {
			$rule_cost       = 0;
			$multiply_by_qty = false;

			if ( isset( $rule['per_qty'] ) && 'yes' === $rule['per_qty'] ) {
				$multiply_by_qty = true;
			}

			if ( ! empty( $rule['cost_unit'] ) ) {
				$rule_cost = ( $multiply_by_qty ) ? $rule['cost_unit'] * $total_unit * $quantity : $rule['cost_unit'] * $total_unit;
			}

			if ( ! empty( $rule['cost'] ) ) {
				$rule_cost += ( $multiply_by_qty ) ? $rule['cost'] * $quantity : $rule['cost'];
			}

			if ( ! empty( $rule['fee'] ) ) {
				$rule_cost += $this->get_fee( $rule['fee'], $package['contents_cost'] );
			}

			/**
			 * Honor the legacy filter for backward compatibility.
			 *
			 * @since 1.0.29
			 * @version 1.0.29
			 *
			 * @param float $rule_cost     Calculated cost.
			 * @param array $rule          Rule in DRS' row.
			 * @param float $total_unit    Total unit value either distance or time.
			 * @param array $package       Package to ship.
			 */
			$condition = empty( $rule['condition'] ) ? $rule['conditions'][0][0]['condition'] : $rule['condition'];
			$condition = 'total' === $condition ? 'order_total' : $condition;// Rename `total` condition to match legacy filter name.
			$rule_cost = apply_filters( 
				'woocommerce_distance_rate_shipping_rule_cost_'. $condition .'_shipping', 
				$rule_cost,
				$rule,
				$total_unit,
				$package
			);

			/**
			 * Filter the rule cost calculation.
			 *
			 * @since 1.0.29
			 * @version 1.0.29
			 *
			 * @param float $rule_cost     Calculated cost.
			 * @param array $rule          Rule in DRS' row.
			 * @param float $total_unit    Total unit value either distance or time.
			 * @param int   $quantity      Cart quantity.
			 * @param array $package       Package to ship.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_calculation',
				$rule_cost,
				$rule,
				$total_unit,
				$quantity,
				$package
			);
		}

		/**
		 * Processing the shipping rule.
		 *
		 * @since 1.0.29
		 * @version 1.0.29
		 *
		 * @param  array  $rule       Rule.
		 * @param  float  $distance   Distance in KM.
		 * @param  object $package    Package to ship.
		 * @param  float  $distance_m Distance in M.
		 * 
		 * @return bool  Result of the rule.
		 */
		public function distance_shipping_rule( $rule, $distance, $package, $distance_m ) {
			$min_match = false;
			$max_match = false;

			if ( isset( $distance_m ) && $distance_m > 0 ) {

				if ( empty( $rule['min'] ) || $distance >= $rule['min'] ) {
					$min_match = true;
				}

				if ( empty( $rule['max'] ) || $distance <= $rule['max'] ) {
					$max_match = true;
				}
			}

			/**
			 * Filter the rule cost for distance shipping.
			 *
			 * @since 1.0.29
			 * @version 1.0.29
			 *
			 * @param float $rule_cost   Calculated cost.
			 * @param array $rule        Rule in DRS' row.
			 * @param float $distance    Distance.
			 * @param array $package     Package to ship.
			 * @param array $order_total Cart total.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_distance_shipping_match',
				( $min_match && $max_match ),
				$rule,
				$distance,
				$package
			);
		}

		public function time_shipping_rule( $rule, $travel_time_minutes, $package ) {
			$min_match = false;
			$max_match = false;

			if ( isset( $travel_time_minutes ) && $travel_time_minutes > 0 ) {

				if ( empty( $rule['min'] ) || $travel_time_minutes >= $rule['min'] ) {
					$min_match = true;
				}

				if ( empty( $rule['max'] ) || $travel_time_minutes <= $rule['max'] ) {
					$max_match = true;
				}
			}

			/**
			 * Filter the rule cost for time shipping.
			 *
			 * @since 1.0.29
			 * @version 1.0.29
			 *
			 * @param float $rule_cost              Calculated cost.
			 * @param array $rule                   Rule in DRS' row.
			 * @param float $travel_time_minutes    Travel time in minutes.
			 * @param array $package                Package to ship.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_time_shipping_match',
				( $min_match && $max_match ),
				$rule,
				$travel_time_minutes,
				$package
			);
		}

		public function weight_shipping_rule( $rule, $distance, $package, $weight ) {
			$min_match = false;
			$max_match = false;

			if ( isset( $weight ) && $weight > 0 ) {

				if ( empty( $rule['min'] ) || $weight >= $rule['min'] ) {
					$min_match = true;
				}

				if ( empty( $rule['max'] ) || $weight <= $rule['max'] ) {
					$max_match = true;
				}
			}

			/**
			 * Filter the rule cost for weight shipping.
			 *
			 * @since 1.0.29
			 * @version 1.0.29
			 *
			 * @param float $rule_cost   Calculated cost.
			 * @param array $rule        Rule in DRS' row.
			 * @param float $distance    Distance.
			 * @param array $package     Package to ship.
			 * @param array $weight      Cart weight.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_weight_shipping_match',
				( $min_match && $max_match ),
				$rule,
				$distance,
				$package,
				$weight
			);
		}

		public function order_total_shipping_rule( $rule, $distance, $package, $order_total ) {
			$min_match = false;
			$max_match = false;

			if ( isset( $order_total ) && $order_total > 0 ) {

				if ( empty( $rule['min'] ) || $order_total >= $rule['min'] ) {
					$min_match = true;
				}

				if ( empty( $rule['max'] ) || $order_total <= $rule['max'] ) {
					$max_match = true;
				}
			}

			/**
			 * Filter the rule cost for order total shipping.
			 *
			 * @since 1.0.29
			 * @version 1.0.29
			 *
			 * @param float $rule_cost   Calculated cost.
			 * @param array $rule        Rule in DRS' row.
			 * @param float $distance    Distance.
			 * @param array $package     Package to ship.
			 * @param array $order_total Cart total.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_order_total_shipping_match',
				( $min_match && $max_match ),
				$rule,
				$distance,
				$package,
				$order_total
			);
		}

		public function quantity_shipping_rule( $rule, $distance, $package, $quantity ) {
			$min_match = false;
			$max_match = false;

			if ( isset( $quantity ) && $quantity > 0 ) {

				if ( empty( $rule['min'] ) || $quantity >= $rule['min'] ) {
					$min_match = true;
				}

				if ( empty( $rule['max'] ) || $quantity <= $rule['max'] ) {
					$max_match = true;
				}
			}

			/**
			 * Filter the rule cost for quantity shipping.
			 *
			 * @since 1.0.29
			 * @version 1.0.29
			 *
			 * @param float $rule_cost Calculated cost.
			 * @param array $rule      Rule in DRS' row.
			 * @param float $distance  Distance.
			 * @param array $package   Package to ship.
			 * @param array $quantity  Cart quantity.
			 */
			return apply_filters(
				'woocommerce_distance_rate_shipping_rule_cost_quantity_shipping_match',
				( $min_match && $max_match ),
				$rule,
				$distance,
				$package,
				$quantity
			);
		}

		/**
		 * Build customer address string from package.
		 *
		 * @param  array $package Package to ship.
		 * @param  bool  $convert_country_code Use full country name or just the country code ( France vs. FR )
		 * @return string
		 */
		public function get_customer_address_string( $package, $convert_country_code = true ) {
			$address = array();

			if ( ! empty( $package['destination']['address'] ) ) {
				$address['address_1'] = $package['destination']['address'];
			} else if ( ! empty( WC()->customer ) && ! empty( WC()->customer->get_shipping_address() ) ) {
				$address['address_1'] = WC()->customer->get_shipping_address();
			}

			if ( ! empty( $package['destination']['address_2'] ) ) {
				$address['address_2'] = $package['destination']['address_2'];
			} else if ( ! empty( WC()->customer ) && ! empty( WC()->customer->get_shipping_address_2() ) ) {
				$address['address_2'] = WC()->customer->get_shipping_address_2();
			}

			if ( ! empty( $package['destination']['city'] ) ) {
				$address['city'] = $package['destination']['city'];
			} else if ( ! empty( WC()->customer ) && ! empty( WC()->customer->get_shipping_city() ) ) {
				$address['city'] = WC()->customer->get_shipping_city();
			}

			if ( ! empty( $package['destination']['state'] ) ) {
				$state = $package['destination']['state'];
				$country = $package['destination']['country'];

				// Convert state code to full name if available
				if ( isset( WC()->countries->states[ $country ], WC()->countries->states[ $country ][ $state ] ) ) {
					$state = WC()->countries->states[ $country ][ $state ];
					$country = WC()->countries->countries[ $country ];
				}
				$address['state'] = $state;
			}

			// Cart page only has country, state and zipcodes.
			if ( ! empty( $package['destination']['postcode'] ) ) {
				$address['postcode'] = $package['destination']['postcode'];
			}

			if ( ! empty( $package['destination']['country'] ) ) {
				$country = $package['destination']['country'];

				// Convert country code to full name if available
				if ( $convert_country_code && isset( WC()->countries->countries[ $country ] ) ) {
					$country = WC()->countries->countries[ $country ];
				}
				$address['country'] = $country;
			}

			return implode( ', ', apply_filters( 'woocommerce_shipping_' . $this->id . '_get_customer_address_string', $address ) );
		}

		/**
		 * Get the shipping from address as string.
		 *
		 * @return string
		 */
		public function get_shipping_address_string() {
			$address = array();
			if ( ! empty( $this->address_1 ) ) {
				$address['address_1'] = $this->address_1;
			}

			if ( ! empty( $this->address_2 ) ) {
				$address['address_2'] = $this->address_2;
			}

			if ( ! empty( $this->city ) ) {
				$address['city'] = $this->city;
			}

			if ( ! empty( $this->postal_code ) ) {
				$address['postcode'] = $this->postal_code;
			}

			if ( ! empty( $this->state_province ) ) {
				$address['state'] = $this->state_province;
			}

			if ( ! empty( $this->country ) ) {
				$address['country'] = $this->country;
			}

			return implode( ', ', apply_filters( 'woocommerce_shipping_' . $this->id . '_get_shipping_address_string', $address ) );
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
