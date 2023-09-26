<?php
/**
 * WC_CSP_Conditions class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Conditions class.
 *
 * @class    WC_CSP_Conditions
 * @version  1.15.0
 */
class WC_CSP_Conditions {

	/**
	 * Array of registered condition classes.
	 *
	 * @var array
	 */
	public $conditions;

	/**
	 * Conditions evaluated so far in the current request.
	 * @var array
	 */
	private $active_conditions = array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		$load_conditions = apply_filters( 'woocommerce_csp_conditions', array(
			'WC_CSP_Condition_Cart_Total',
			'WC_CSP_Condition_Order_Total',
			'WC_CSP_Condition_Cart_Item_Quantity',
			'WC_CSP_Condition_Billing_Country_State',
			'WC_CSP_Condition_Billing_Postcode',
			'WC_CSP_Condition_Shipping_Country_State',
			'WC_CSP_Condition_Shipping_Postcode',
			'WC_CSP_Condition_Shipping_Method',
			'WC_CSP_Condition_Cart_Category',
			'WC_CSP_Condition_Package_Category',
			'WC_CSP_Condition_Cart_Shipping_Class',
			'WC_CSP_Condition_Package_Shipping_Class',
			'WC_CSP_Condition_Package_Weight',
			'WC_CSP_Condition_Package_Item_Quantity',
			'WC_CSP_Condition_Package_Total',
			'WC_CSP_Condition_Customer',
			'WC_CSP_Condition_Customer_Role',
			'WC_CSP_Condition_Coupon_Code',
			'WC_CSP_Condition_Cart_Backorder',
			'WC_CSP_Condition_Package_Backorder',
			'WC_CSP_Condition_Cart_Item_On_Sale',
			'WC_CSP_Condition_Cart_Subtotal',
			'WC_CSP_Condition_Order_Status',
			'WC_CSP_Condition_Date_Time',
			'WC_CSP_Condition_Stock_Quantity',
			'WC_CSP_Condition_Cart_Item_Count'
		) );

		// Load conditions.
		foreach ( $load_conditions as $condition ) {

			$condition = new $condition();

			$this->conditions[ $condition->id ] = $condition;
		}

		if ( is_admin() ) {

			/*---------------------------------------------------*/
			/*  Show Condition fields.                           */
			/*---------------------------------------------------*/

			add_action( 'woocommerce_csp_admin_product_fields', array( $this, 'get_admin_condition_product_fields' ), 10, 3 );
			add_action( 'woocommerce_csp_admin_global_fields', array( $this, 'get_admin_condition_global_fields' ), 10, 3 );

			/*---------------------------------------------------*/
			/*  Process Condition fields.                        */
			/*---------------------------------------------------*/

			add_filter( 'woocommerce_csp_process_admin_product_fields', array( $this, 'process_admin_condition_product_fields' ), 10, 3 );
			add_filter( 'woocommerce_csp_process_admin_global_fields', array( $this, 'process_admin_condition_global_fields' ), 10, 3 );

			/*---------------------------------------------------*/
			/*  Print condition JS templates in footer.          */
			/*---------------------------------------------------*/

			add_action( 'admin_footer', array( $this, 'print_condition_field_scripts' ) );
		}
	}

	/**
	 * Called when a condition is evaluated during the course of a request.
	 *
	 * @since  1.9.0
	 *
	 * @param  string  $id
	 */
	public function set_active( $id ) {
		$this->active_conditions[] = $id;
		$this->active_conditions = array_unique( $this->active_conditions );
	}

	/**
	 * Indicates whether a condition has been evaluated in the course of this request.
	 *
	 * @since  1.9.0
	 *
	 * @param  string  $id
	 */
	public function is_active( $id ) {
		return in_array( $id, $this->active_conditions );
	}

	/**
	 * Print condition JS templates in footer.
	 */
	public function print_condition_field_scripts() {

		if ( wp_script_is( 'wc-restrictions-writepanel', 'done' ) ) {
			$this->print_js_templates( 'product' );
		} elseif ( wp_script_is( 'wc-global-restrictions-writepanel', 'done'  ) ) {
			$this->print_js_templates( 'global' );
		}
	}

	/**
	 * Prints JS condition templates in footer.
	 *
	 * @param  string $scope
	 * @return void
	 */
	private function print_js_templates( $scope ) {

		if ( ! in_array( $scope, array( 'product', 'global' ) ) ) {
			return;
		}

		$fn_name      = 'get_admin_' . $scope . '_field_restrictions';
		$restrictions = WC_CSP()->restrictions->$fn_name();

		if ( ! empty( $restrictions ) ) {
			foreach ( $restrictions as $restriction_id => $restriction ) {

				$conditions = $this->get_supported_conditions( $restriction_id, $scope );

				if ( empty( $conditions ) ) {
					continue;
				}

				?><script type="text/template" id="tmpl-wc_csp_restriction_<?php echo esc_attr( $restriction_id ); ?>_condition_row">
					<div class="condition_row" data-condition_index="{{{ data.condition_index }}}">
						<div class="condition_select">
							<div class="sw-enhanced-select"><?php
								$this->get_conditions_dropdown( $conditions, '' );
							?></div>
						</div>
						<div class="condition_content">
							{{{ data.condition_content }}}
						</div>
						<div class="condition_remove column-wc_actions">
							<a href="#" class="button wc-action-button trash help_tip" data-tip="<?php esc_attr_e( 'Remove', 'woocommerce-conditional-shipping-and-payments' ); ?>"></a>
						</div>
					</div>
				</script>

				<script type="text/template" id="tmpl-wc_csp_restriction_<?php echo esc_attr( $restriction_id ); ?>_condition_default_content"><?php
					$default_condition = reset( $conditions );
					$default_condition->get_admin_fields_html( '{{{ data.restriction_index }}}', '{{{ data.condition_index }}}', array() );
				?></script><?php

				foreach ( $conditions as $condition_id => $condition ) {

					?><script type="text/template" id="tmpl-wc_csp_restriction_<?php echo esc_attr( $restriction_id ); ?>_condition_<?php echo esc_attr( $condition_id ); ?>_content"><?php

					$condition->get_admin_fields_html( '{{{ data.restriction_index }}}', '{{{ data.condition_index }}}', array() );

					?></script><?php
				}
			}
		}
	}

	/**
	 * Get condition class by condition_id.
	 *
	 * @param  str    $condition_id
	 * @return WC_CSP_Condition
	 */
	public function get_condition( $condition_id ) {

		if ( ! empty( $this->conditions[ $condition_id ] ) ) {
			return $this->conditions[ $condition_id ];
		}

		return false;
	}

	/**
	 * Get conditions by supported restriction id and scope.
	 *
	 * @param  string $restriction_id
	 * @return array
	 */
	public function get_supported_conditions( $restriction_id = '', $scope = 'global' ) {

		$conditions = array();

		foreach ( $this->conditions as $id => $condition ) {
			if ( $restriction_id === '' || $condition->has_fields( $restriction_id, $scope ) ) {
				$conditions[ $id ] = $condition;
			}
		}

		// Sort Conditions by name.
		uasort( $conditions, array( $this, 'cmp_condition_titles' ) );

		return apply_filters( 'woocommerce_csp_get_supported_conditions', $conditions, $restriction_id, $scope );
	}

	/**
	 * Compare Condition titles
	 *
	 * @param  WC_CSP_Condition $a
	 * @param  WC_CSP_Condition $b
	 * @return int
	 */
	private function cmp_condition_titles( $a, $b ) {
		return strcmp( $a->get_title(), $b->get_title() );
	}

	/**
	 * Get condition fields for admin product restriction metaboxes.
	 *
	 * @param  string $restriction_id
	 * @param  int    $index
	 * @param  array  $options
	 * @return str
	 */
	public function get_admin_condition_product_fields( $restriction_id, $index, $options = array() ) {
		$this->get_admin_condition_fields( 'product', $restriction_id, $index, $options );
	}

	/**
	 * Get condition fields for admin global restriction metaboxes.
	 *
	 * @param  string $restriction_id
	 * @param  int    $index
	 * @param  array  $options
	 * @return str
	 */
	public function get_admin_condition_global_fields( $restriction_id, $index, $options = array() ) {
		$this->get_admin_condition_fields( 'global', $restriction_id, $index, $options );
	}

	/**
	 * Get condition fields for admin restriction metaboxes.
	 *
	 * @param  string $scope
	 * @param  string $restriction_id
	 * @param  int    $index
	 * @param  array  $options
	 * @return str
	 */
	private function get_admin_condition_fields( $scope, $restriction_id, $index, $options = array() ) {

		$conditions = $this->get_supported_conditions( $restriction_id, $scope );

		if ( empty( $conditions ) ) {
			return false;
		}

		?>
		<div class="hr-section hr-section--conditions">
			<?php
			esc_html_e( 'Conditions', 'woocommerce-conditional-shipping-and-payments' );
			?>
		</div>
		<?php

		?><div class="restriction_conditions">
			<div class="restriction_conditions_list widefat"><?php

				if ( ! empty( $options[ 'conditions' ] ) ) {

					$conditions_data = $options[ 'conditions' ];

					foreach ( $conditions_data as $condition_index => $condition_data ) {

						if ( isset( $condition_data[ 'condition_id' ] ) ) {

							$condition_id = $condition_data[ 'condition_id' ];

							if ( array_key_exists( $condition_id, $conditions ) ) {

								?><div class="condition_row">

									<div class="condition_select">
										<div class="sw-enhanced-select"><?php
											$this->get_conditions_dropdown( $conditions, $condition_id );
										?></div>
									</div>
									<div class="condition_content"><?php
										$conditions[ $condition_id ]->get_admin_fields_html( $index, $condition_index, $condition_data );
									?></div>
									<div class="condition_remove column-wc_actions">
										<a href="#" data-tip="<?php esc_attr_e( 'Remove', 'woocommerce-conditional-shipping-and-payments' ); ?>" class="button wc-action-button trash help_tip"></a>
									</div>
								</div>
								<div class="hr-section hr-section--conditions-and">
									<?php esc_html_e( 'And', 'woocommerce-conditional-shipping-and-payments' ); ?>
								</div><?php
							}
						}
					}
				}
			?>
			</div>
			<div class="condition_add condition_row">
				<div class="condition_select">
					<div class="sw-enhanced-select">
						<?php $this->get_conditions_dropdown( $conditions, null, [ 'add_condition' => __( 'Add Condition', 'woocommerce-conditional-shipping-and-payments' ) ] ); ?>
					</div>
				</div>
				<div class="condition_content">
					<div class="condition_row_inner">
						<div class="condition_modifier condition--disabled">
						</div>
						<div class="condition_value condition--disabled">
						</div>
					</div>
				</div>
				<div class="condition_remove">
				</div>
			</div>
		</div><?php
	}

	/**
	 * Admin condition select dropdown.
	 *
	 * @param  int    $index
	 * @param  string $selected_id
	 * @param  array  $additional_options
	 * @return void
	 */
	private function get_conditions_dropdown( $conditions, $selected_id, $additional_options = array() ) {

		?><select name="condition_dropdown" class="condition_type"><?php

			if ( ! empty( $additional_options ) ) {

				foreach ( $additional_options as $key => $value ) {
					?><option value="<?php echo esc_attr( $key ); ?>" selected="selected"><?php echo esc_html( $value ); ?></option><?php
				}
			}

			foreach ( $conditions as $condition_id => $condition ) {
				?><option value="<?php echo esc_attr($condition_id); ?>" <?php echo $condition_id === $selected_id ? 'selected="selected"' : ''; ?>><?php
					echo esc_html( $condition->get_title() );
				?></option><?php
			}
		?></select><?php
	}

	/**
	 * Process condition fields for admin product restriction metaboxes.
	 *
	 * @param  array  $processed_data
	 * @param  array  $posted_data
	 * @param  string $restriction_id
	 * @return array
	 */
	public function process_admin_condition_product_fields( $processed_data, $posted_data, $restriction_id ) {
		return $this->process_admin_condition_fields( 'product', $restriction_id, $processed_data, $posted_data );
	}

	/**
	 * Process condition fields for admin global restriction metaboxes.
	 *
	 * @param  array  $processed_data
	 * @param  array  $posted_data
	 * @param  string $restriction_id
	 * @return array
	 */
	public function process_admin_condition_global_fields( $processed_data, $posted_data, $restriction_id ) {
		return $this->process_admin_condition_fields( 'global', $restriction_id, $processed_data, $posted_data );
	}

	/**
	 * Process condition fields.
	 *
	 * @param  string $scope
	 * @param  string $restriction_id
	 * @param  array  $processed_data
	 * @param  array  $posted_data
	 * @return array
	 */
	private function process_admin_condition_fields( $scope, $restriction_id, $processed_data, $posted_data ) {

		$conditions = $this->get_supported_conditions( $restriction_id, $scope );

		if ( ! empty( $posted_data[ 'conditions' ] ) && ! empty( $conditions ) ) {

			$conditions_data = $posted_data[ 'conditions' ];

			$processed_data[ 'conditions' ] = array();

			foreach ( $conditions_data as $condition_key => $condition_data ) {

				if ( isset( $condition_data[ 'condition_id' ] ) ) {

					$condition_id                             = $condition_data[ 'condition_id' ];
					$condition_data[ 'restriction_position' ] = $posted_data[ 'position' ];

					if ( array_key_exists( $condition_id, $conditions ) ) {

						if ( $processed_condition_data = $conditions[ $condition_id ]->process_admin_fields( $condition_data ) ) {
							$processed_data[ 'conditions' ][] = $processed_condition_data;
						}
					}
				}

			}
		}

		return $processed_data;
	}

	/**
	 * Evaluate if a condition is in effect or not.
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restrictions
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		if ( isset( $data[ 'condition_id' ] ) ) {

			$condition_id = $data[ 'condition_id' ];

			if ( $condition = $this->get_condition( $condition_id ) ) {
				return $condition->check_condition( $data, $args );
			}
		}

		return true;
	}

	/**
	 * Return condition-field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data   condition field data
	 * @param  array  $args   optional arguments passed by restriction
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		if ( isset( $data[ 'condition_id' ] ) ) {

			$condition_id = $data[ 'condition_id' ];

			if ( $condition = $this->get_condition( $condition_id ) ) {
				return $condition->get_condition_resolution( $data, $args );
			}
		}

		return false;
	}

	/**
	 * Sorts conditions data by execution priority.
	 * Conditions with lower priority are evaluated first to preserve CPU resources.
	 *
	 * @since  1.13.1
	 *
	 * @param  array  $data
	 * @return array
	 */
	public function get_optimized_conditions_data( $data ) {

		$sorted_data = array();
		$sort_order  = array();

		foreach ( $this->conditions as $id => $condition ) {
			$sort_order[ $id ] = $condition->get_priority();
		}

		asort( $sort_order );

		foreach ( $data as $condition_index => $condition_data ) {
			$data[ $condition_index ][ 'priority' ] = $sort_order[ $condition_data[ 'condition_id' ] ];
		}

		$sorted_data = wp_list_sort( $data, 'priority' );

		return $sorted_data;
	}
}
