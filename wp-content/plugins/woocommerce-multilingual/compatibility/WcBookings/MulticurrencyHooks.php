<?php

namespace WCML\Compatibility\WcBookings;

/**
 * @todo: Most of the code in this class was just moved from
 * the original \WCML_Bookings class with the minimal
 * adjustments. There's a lot of weak/obsolete formatting
 * and code duplication that we should fix in the future.
 */
class MulticurrencyHooks implements \IWPML_Action {


	/** @var \woocommerce_wpml $woocommerce_wpml */
	private $woocommerce_wpml;

	public function __construct( \woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		add_action( 'woocommerce_bookings_after_booking_base_cost', [ $this, 'wcml_price_field_after_booking_base_cost' ] );
		add_action( 'woocommerce_bookings_after_booking_block_cost', [ $this, 'wcml_price_field_after_booking_block_cost' ] );
		add_action( 'woocommerce_bookings_after_display_cost', [ $this, 'wcml_price_field_after_display_cost' ] );
		add_action( 'woocommerce_bookings_after_booking_pricing_base_cost', [ $this, 'wcml_price_field_after_booking_pricing_base_cost' ], 10, 2 );
		add_action( 'woocommerce_bookings_after_booking_pricing_cost', [ $this, 'wcml_price_field_after_booking_pricing_cost' ], 10, 2 );
		add_action( 'woocommerce_bookings_after_person_cost', [ $this, 'wcml_price_field_after_person_cost' ] );
		add_action( 'woocommerce_bookings_after_person_block_cost', [ $this, 'wcml_price_field_after_person_block_cost' ] );
		add_action( 'woocommerce_bookings_after_resource_cost', [ $this, 'wcml_price_field_after_resource_cost' ], 10, 2 );
		add_action( 'woocommerce_bookings_after_resource_block_cost', [ $this, 'wcml_price_field_after_resource_block_cost' ], 10, 2 );

		add_action( 'woocommerce_bookings_after_bookings_pricing', [ $this, 'after_bookings_pricing' ] );

		add_action( 'save_post', [ $this, 'save_custom_costs' ], \WCML_Bookings::PRIORITY_SAVE_POST_ACTION - 1 );

		add_filter( 'woocommerce_bookings_process_cost_rules_cost', [ $this, 'wc_bookings_process_cost_rules_cost' ], 10, 3 );
		add_filter(	'woocommerce_bookings_process_cost_rules_base_cost', [ $this, 'wc_bookings_process_cost_rules_base_cost' ], 10, 3 );
		add_filter( 'woocommerce_bookings_process_cost_rules_override_block', [ $this, 'wc_bookings_process_cost_rules_override_block_cost' ], 10, 3 );

		add_action( 'woocommerce_bookings_after_create_booking_page', [ $this, 'booking_currency_dropdown' ] );
		add_action( 'init', [ $this, 'set_booking_currency' ] );
		add_action( 'wp_ajax_wcml_booking_set_currency', [ $this, 'set_booking_currency_ajax' ] );

		add_action( 'woocommerce_bookings_create_booking_page_add_order_item', [ $this, 'set_order_currency_on_create_booking_page' ] );
		add_filter( 'woocommerce_currency_symbol', [ $this, 'filter_booking_currency_symbol' ] );

		add_filter( 'wcml_filter_currency_position', [ $this, 'create_booking_page_client_currency' ] );
		add_filter( 'wcml_client_currency', [ $this, 'create_booking_page_client_currency' ] );

		if ( ! is_admin() || isset( $_POST['action'] ) && 'wc_bookings_calculate_costs' === $_POST['action'] ) {
			add_filter( 'get_post_metadata', [ $this, 'filter_wc_booking_cost' ], 10, 4 );
		}
	}

	/**
	 * @param int $postId
	 *
	 * @return void
	 */
	public function wcml_price_field_after_booking_base_cost( $postId ) {
		$this->echo_wcml_price_field( $postId, 'wcml_wc_booking_cost' );
	}

	/**
	 * @param int $postId
	 *
	 * @return void
	 */
	public function wcml_price_field_after_booking_block_cost( $postId ) {
		if ( self::isWcBookingsBefore_1_10_9() ) {
			$this->echo_wcml_price_field( $postId, 'wcml_wc_booking_base_cost' );
		} else {
			$this->echo_wcml_price_field( $postId, 'wcml_wc_booking_block_cost' );
		}
	}

	/**
	 * @param int $postId
	 *
	 * @return void
	 */
	public function wcml_price_field_after_display_cost( $postId ) {
		$this->echo_wcml_price_field( $postId, 'wcml_wc_display_cost' );
	}

	/**
	 * @param array $pricing
	 * @param int   $postId
	 *
	 * @return void
	 */
	public function wcml_price_field_after_booking_pricing_base_cost( $pricing, $postId ) {
		$this->echo_wcml_price_field( $postId, 'wcml_wc_booking_pricing_base_cost', $pricing );
	}

	/**
	 * @param array $pricing
	 * @param int   $postId
	 *
	 * @return void
	 */
	public function wcml_price_field_after_booking_pricing_cost( $pricing, $postId ) {
		$this->echo_wcml_price_field( $postId, 'wcml_wc_booking_pricing_cost', $pricing );
	}

	/**
	 * @param int $personTypeId
	 *
	 * @return void
	 */
	public function wcml_price_field_after_person_cost( $personTypeId ) {
		$this->echo_wcml_price_field( $personTypeId, 'wcml_wc_booking_person_cost', false, false );
	}

	/**
	 * @param int $personTypeId
	 *
	 * @return void
	 */
	public function wcml_price_field_after_person_block_cost( $personTypeId ) {
		$this->echo_wcml_price_field( $personTypeId, 'wcml_wc_booking_person_block_cost', false, false );
	}

	/**
	 * @param int $resourceId
	 * @param int $postId
	 *
	 * @return void
	 */
	public function wcml_price_field_after_resource_cost( $resourceId, $postId ) {
		$this->echo_wcml_price_field( $postId, 'wcml_wc_booking_resource_cost', false, true, $resourceId );
	}

	/**
	 * @param int $resourceId
	 * @param int $postId
	 *
	 * @return void
	 */
	public function wcml_price_field_after_resource_block_cost( $resourceId, $postId ) {
		$this->echo_wcml_price_field( $postId, 'wcml_wc_booking_resource_block_cost', false, true, $resourceId );
	}

	/**
	 * @param int         $postId
	 * @param string      $field
	 * @param array|false $pricing
	 * @param bool        $check
	 * @param int|false   $resourceId
	 *
	 * @return void
	 */
	public function echo_wcml_price_field( $postId, $field, $pricing = false, $check = true, $resourceId = false ) {
		if ( ( ! $check || $this->woocommerce_wpml->products->is_original_product( $postId ) ) ) {

			$currencies = $this->woocommerce_wpml->multi_currency->get_currencies();

			$wc_currencies = get_woocommerce_currencies();

			if ( ! function_exists( 'woocommerce_wp_text_input' ) ) {
				include_once dirname( WC_PLUGIN_FILE ) . '/includes/admin/wc-meta-box-functions.php';
			}

			echo '<div class="wcml_custom_cost_field" >';

			foreach ( $currencies as $currency_code => $currency ) {

				switch ( $field ) {
					case 'wcml_wc_booking_cost':
						woocommerce_wp_text_input(
							[
								'id'                => 'wcml_wc_booking_cost',
								'class'             => 'wcml_bookings_custom_price',
								'name'              => 'wcml_wc_booking_cost[' . $currency_code . ']',
								'label'             => get_woocommerce_currency_symbol( $currency_code ),
								'description'       => __( 'One-off cost for the booking as a whole.', 'woocommerce-bookings' ),
								'value'             => get_post_meta( $postId, '_wc_booking_cost_' . $currency_code, true ),
								'type'              => 'number',
								'desc_tip'          => true,
								'custom_attributes' => [
									'min'  => '',
									'step' => '0.01',
								],
							]
						);
						break;
					case 'wcml_wc_booking_block_cost':
					case 'wcml_wc_booking_base_cost':
						$block_cost_key = '_wc_booking_base_cost_';
						if ( $field === 'wcml_wc_booking_block_cost' ) {
							$block_cost_key = '_wc_booking_block_cost_';
						}
						$block_cost_key .= $currency_code;
						woocommerce_wp_text_input(
							[
								'id'                => $field,
								'class'             => 'wcml_bookings_custom_price',
								'name'              => $field . '[' . $currency_code . ']',
								'label'             => get_woocommerce_currency_symbol( $currency_code ),
								'description'       => __( 'This is the cost per block booked. All other costs (for resources and persons) are added to this.', 'woocommerce-bookings' ),
								'value'             => get_post_meta( $postId, $block_cost_key, true ),
								'type'              => 'number',
								'desc_tip'          => true,
								'custom_attributes' => [
									'min'  => '',
									'step' => '0.01',
								],
							]
						);
						break;
					case 'wcml_wc_display_cost':
						woocommerce_wp_text_input(
							[
								'id'                => 'wcml_wc_display_cost',
								'class'             => 'wcml_bookings_custom_price',
								'name'              => 'wcml_wc_display_cost[' . $currency_code . ']',
								'label'             => get_woocommerce_currency_symbol( $currency_code ),
								'description'       => __( 'The cost is displayed to the user on the frontend. Leave blank to have it calculated for you. If a booking has varying costs, this will be prefixed with the word "from:".', 'woocommerce-bookings' ),
								'value'             => get_post_meta( $postId, '_wc_display_cost_' . $currency_code, true ),
								'type'              => 'number',
								'desc_tip'          => true,
								'custom_attributes' => [
									'min'  => '',
									'step' => '0.01',
								],
							]
						);
						break;

					case 'wcml_wc_booking_pricing_base_cost':
						if ( isset( $pricing[ 'base_cost_' . $currency_code ] ) ) {
							$value = $pricing[ 'base_cost_' . $currency_code ];
						} else {
							$value = '';
						}

						echo '<div class="wcml_bookings_range_block" >';
						echo '<label>' . wp_kses_post( get_woocommerce_currency_symbol( $currency_code ) ) . '</label>';
						echo '<input type="number" step="0.01" name="wcml_wc_booking_pricing_base_cost[' . esc_html( $currency_code ) . '][]" class="wcml_bookings_custom_price" value="' . esc_html( $value ) . '" placeholder="0" />';
						echo '</div>';
						break;

					case 'wcml_wc_booking_pricing_cost':
						if ( isset( $pricing[ 'cost_' . $currency_code ] ) ) {
							$value = $pricing[ 'cost_' . $currency_code ];
						} else {
							$value = '';
						}

						echo '<div class="wcml_bookings_range_block" >';
						echo '<label>' . wp_kses_post( get_woocommerce_currency_symbol( $currency_code ) ) . '</label>';
						echo '<input type="number" step="0.01" name="wcml_wc_booking_pricing_cost[' . esc_html( $currency_code ) . '][]" class="wcml_bookings_custom_price" value="' . esc_html( $value ) . '" placeholder="0" />';
						echo '</div>';
						break;

					case 'wcml_wc_booking_person_cost':
						$value = get_post_meta( $postId, 'cost_' . $currency_code, true );

						echo '<div class="wcml_bookings_person_block" >';
						echo '<label>' . wp_kses_post( get_woocommerce_currency_symbol( $currency_code ) ) . '</label>';
						echo '<input type="number" step="0.01" name="wcml_wc_booking_person_cost[' . intval( $postId ) . '][' . esc_html( $currency_code ) . ']" class="wcml_bookings_custom_price" value="' . esc_html( $value ) . '" placeholder="0" />';
						echo '</div>';
						break;

					case 'wcml_wc_booking_person_block_cost':
						$value = get_post_meta( $postId, 'block_cost_' . $currency_code, true );

						echo '<div class="wcml_bookings_person_block" >';
						echo '<label>' . wp_kses_post( get_woocommerce_currency_symbol( $currency_code ) ) . '</label>';
						echo '<input type="number" step="0.01" name="wcml_wc_booking_person_block_cost[' . intval( $postId ) . '][' . esc_html( $currency_code ) . ']" class="wcml_bookings_custom_price" value="' . esc_html( $value ) . '" placeholder="0" />';
						echo '</div>';
						break;

					case 'wcml_wc_booking_resource_cost':
						$resource_base_costs = maybe_unserialize( get_post_meta( $postId, '_resource_base_costs', true ) );

						if ( isset( $resource_base_costs['custom_costs'][ $currency_code ][ $resourceId ] ) ) {
							$value = $resource_base_costs['custom_costs'][ $currency_code ][ $resourceId ];
						} else {
							$value = '';
						}

						echo '<div class="wcml_bookings_resource_block" >';
						echo '<label>' . wp_kses_post( get_woocommerce_currency_symbol( $currency_code ) ) . '</label>';
						echo '<input type="number" step="0.01" name="wcml_wc_booking_resource_cost[' . esc_html( $resourceId ) . '][' . esc_html( $currency_code ) . ']" class="wcml_bookings_custom_price" value="' . esc_html( $value ) . '" placeholder="0" />';
						echo '</div>';
						break;

					case 'wcml_wc_booking_resource_block_cost':
						$resource_block_costs = maybe_unserialize( get_post_meta( $postId, '_resource_block_costs', true ) );

						if ( isset( $resource_block_costs['custom_costs'][ $currency_code ][ $resourceId ] ) ) {
							$value = $resource_block_costs['custom_costs'][ $currency_code ][ $resourceId ];
						} else {
							$value = '';
						}

						echo '<div class="wcml_bookings_resource_block" >';
						echo '<label>' . wp_kses_post( get_woocommerce_currency_symbol( $currency_code ) ) . '</label>';
						echo '<input type="number" step="0.01" name="wcml_wc_booking_resource_block_cost[' . esc_html( $resourceId ) . '][' . esc_html( $currency_code ) . ']" class="wcml_bookings_custom_price" value="' . esc_html( $value ) . '" placeholder="0" />';
						echo '</div>';
						break;

					default:
						break;

				}
			}

			echo '</div>';
		}
	}

	/**
	 * @param int $postId
	 *
	 * @return void
	 */
	public function after_bookings_pricing( $postId ) {

		if ( in_array( 'booking', wp_get_post_terms( $postId, 'product_type', [ 'fields' => 'names' ] ) ) && $this->woocommerce_wpml->products->is_original_product( $postId ) ) {

			$customCostsStatus = get_post_meta( $postId, '_wcml_custom_costs_status', true );

			$checked = ! $customCostsStatus ? 'checked="checked"' : ' ';

			echo '<div class="wcml_custom_costs">';

			echo '<input type="radio" name="_wcml_custom_costs" id="wcml_custom_costs_auto" value="0" class="wcml_custom_costs_input" ' . esc_html( $checked ) . ' />';
			echo '<label for="wcml_custom_costs_auto">' . esc_html__( 'Calculate costs in other currencies automatically', 'woocommerce-multilingual' ) . '</label>';

			$checked = 1 === (int) $customCostsStatus ? 'checked="checked"' : ' ';

			echo '<input type="radio" name="_wcml_custom_costs" value="1" id="wcml_custom_costs_manually" class="wcml_custom_costs_input" ' . esc_html( $checked ) . ' />';
			echo '<label for="wcml_custom_costs_manually">' . esc_html__( 'Set costs in other currencies manually', 'woocommerce-multilingual' ) . '</label>';

			wp_nonce_field( 'wcml_save_custom_costs', '_wcml_custom_costs_nonce' );

			echo '</div>';
		}
	}

	/**
	 * @param int $postId
	 *
	 * @return false|void
	 */
	public function save_custom_costs( $postId ) {
		$nonce = filter_var( isset( $_POST['_wcml_custom_costs_nonce'] ) ? $_POST['_wcml_custom_costs_nonce'] : '', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( isset( $_POST['_wcml_custom_costs'] ) && isset( $nonce ) && wp_verify_nonce( $nonce, 'wcml_save_custom_costs' ) ) {

			update_post_meta( $postId, '_wcml_custom_costs_status', $_POST['_wcml_custom_costs'] );

			if ( 1 === (int) $_POST['_wcml_custom_costs'] ) {

				$currencies = $this->woocommerce_wpml->multi_currency->get_currencies();
				if ( empty( $currencies ) || 0 === $postId ) {
					return false;
				}

				$this->update_booking_costs( $currencies, $postId );
				$this->update_booking_pricing( $currencies, $postId );

				if ( isset( $_POST['wcml_wc_booking_person_cost'] ) && is_array( $_POST['wcml_wc_booking_person_cost'] ) ) {
					$this->update_booking_person_cost( $currencies, $_POST['wcml_wc_booking_person_cost'] );
				}

				if ( isset( $_POST['wcml_wc_booking_person_block_cost'] ) && is_array( $_POST['wcml_wc_booking_person_block_cost'] ) ) {
					$this->update_booking_person_block_cost( $currencies, $_POST['wcml_wc_booking_person_block_cost'] );
				}

				if ( isset( $_POST['wcml_wc_booking_resource_cost'] ) && is_array( $_POST['wcml_wc_booking_resource_cost'] ) ) {
					$this->update_booking_resource_cost( $currencies, $postId, $_POST['wcml_wc_booking_resource_cost'] );
				}

				if ( isset( $_POST['wcml_wc_booking_resource_block_cost'] ) && is_array( $_POST['wcml_wc_booking_resource_block_cost'] ) ) {
					$this->update_booking_resource_block_cost( $currencies, $postId, $_POST['wcml_wc_booking_resource_block_cost'] );
				}

				update_post_meta( $postId, '_price', '' );
			} else {
				return false;
			}
		}
	}

	/**
	 * @param array $currencies
	 * @param int   $postId
	 *
	 * @return bool
	 */
	private function update_booking_costs( $currencies = [], $postId = 0 ) {
		$bookingOptions = [
			'wcml_wc_booking_cost'       => '_wc_booking_cost_',
			'wcml_wc_booking_block_cost' => '_wc_booking_block_cost_',
			'wcml_wc_display_cost'       => '_wc_display_cost_',
		];

		if ( self::isWcBookingsBefore_1_10_9() ) {
			unset( $bookingOptions['wcml_wc_booking_block_cost'] );
			$bookingOptions['wcml_wc_booking_base_cost'] = '_wc_booking_base_cost_';
		}

		foreach ( $currencies as $code => $currency ) {
			foreach ( $bookingOptions as $bookingOptionsPostKey => $bookingOptionsMetaKeyPrefix ) {
				if ( isset( $_POST[ $bookingOptionsPostKey ][ $code ] ) ) {
					update_post_meta( $postId, $bookingOptionsMetaKeyPrefix . $code, sanitize_text_field( $_POST[ $bookingOptionsPostKey ][ $code ] ) );
				}
			}
		}

		return true;
	}

	/**
	 * @param array $currencies
	 * @param int   $postId
	 *
	 * @return bool
	 */
	private function update_booking_pricing( $currencies = [], $postId = 0 ) {
		$updatedMeta    = [];
		$bookingPricing = get_post_meta( $postId, '_wc_booking_pricing', true );
		if ( empty( $bookingPricing ) ) {
			return false;
		}

		foreach ( $bookingPricing as $key => $prices ) {
			$updatedMeta[ $key ] = $prices;
			foreach ( $currencies as $code => $currency ) {
				if ( isset( $_POST['wcml_wc_booking_pricing_base_cost'][ $code ][ $key ] ) ) {
					$updatedMeta[ $key ][ 'base_cost_' . $code ] = sanitize_text_field( $_POST['wcml_wc_booking_pricing_base_cost'][ $code ][ $key ] );
				}
				if ( isset( $_POST['wcml_wc_booking_pricing_cost'][ $code ][ $key ] ) ) {
					$updatedMeta[ $key ][ 'cost_' . $code ] = sanitize_text_field( $_POST['wcml_wc_booking_pricing_cost'][ $code ][ $key ] );
				}
			}
		}

		update_post_meta( $postId, '_wc_booking_pricing', $updatedMeta );

		return true;
	}

	/**
	 * @param array $currencies
	 * @param array $personCosts
	 *
	 * @return bool
	 */
	private function update_booking_person_cost( $currencies = [], $personCosts = [] ) {
		if ( empty( $personCosts ) ) {
			return false;
		}

		foreach ( $personCosts as $personId => $costs ) {
			foreach ( $currencies as $code => $currency ) {
				if ( isset( $costs[ $code ] ) ) {
					update_post_meta( $personId, 'cost_' . $code, sanitize_text_field( $costs[ $code ] ) );
				}
			}
		}

		return true;
	}

	/**
	 * @param array $currencies
	 * @param array $blockCosts
	 *
	 * @return bool
	 */
	private function update_booking_person_block_cost( $currencies = [], $blockCosts = [] ) {
		if ( empty( $blockCosts ) ) {
			return false;
		}

		foreach ( $blockCosts as $personId => $costs ) {
			foreach ( $currencies as $code => $currency ) {
				if ( isset( $costs[ $code ] ) ) {
					update_post_meta( $personId, 'block_cost_' . $code, sanitize_text_field( $costs[ $code ] ) );
				}
			}
		}

		return true;
	}

	/**
	 * @param array $currencies
	 * @param int   $postId
	 * @param array $resourceCost
	 *
	 * @return bool
	 */
	private function update_booking_resource_cost( $currencies = [], $postId = 0, $resourceCost = [] ) {
		if ( empty( $resourceCost ) ) {
			return false;
		}

		$updatedMeta = get_post_meta( $postId, '_resource_base_costs', true );
		if ( ! is_array( $updatedMeta ) ) {
			$updatedMeta = [];
		}

		$wcBookingResourceCosts = [];

		foreach ( $resourceCost as $resourceId => $costs ) {

			foreach ( $currencies as $code => $currency ) {

				if ( isset( $costs[ $code ] ) ) {
					$wcBookingResourceCosts[ $code ][ $resourceId ] = sanitize_text_field( $costs[ $code ] );
				}
			}
		}

		$updatedMeta['custom_costs'] = $wcBookingResourceCosts;

		update_post_meta( $postId, '_resource_base_costs', $updatedMeta );

		self::triggerActionResourceCostsUpdated( $postId, '_resource_base_costs' );

		return true;
	}

	/**
	 * @param array $currencies
	 * @param int   $postId
	 * @param array $resourceBlockCost
	 *
	 * @return bool
	 */
	private function update_booking_resource_block_cost( $currencies = [], $postId = 0, $resourceBlockCost = [] ) {
		if ( empty( $resourceBlockCost ) ) {
			return false;
		}

		$updatedMeta = get_post_meta( $postId, '_resource_block_costs', true );
		if ( ! is_array( $updatedMeta ) ) {
			$updatedMeta = [];
		}

		$wc_booking_resource_block_costs = [];

		foreach ( $resourceBlockCost as $resource_id => $costs ) {

			foreach ( $currencies as $code => $currency ) {

				if ( isset( $costs[ $code ] ) ) {
					$wc_booking_resource_block_costs[ $code ][ $resource_id ] = sanitize_text_field( $costs[ $code ] );
				}
			}
		}

		$updatedMeta['custom_costs'] = $wc_booking_resource_block_costs;

		update_post_meta( $postId, '_resource_block_costs', $updatedMeta );

		self::triggerActionResourceCostsUpdated( $postId, '_resource_block_costs' );

		return true;
	}

	/**
	 * This is an internal action hook required after splitting
	 * the original WC Bookings compatibility code.
	 *
	 * @see \WCML_Bookings::sync_resource_costs_with_translations
	 *
	 * @param int|string $postId
	 * @param string     $key
	 *
	 * @return void
	 */
	private static function triggerActionResourceCostsUpdated( $postId, $key ) {
		do_action( 'wcml_bookings_resource_costs_updated', $postId, $key );
	}

	/**
	 * @param float|int $cost
	 * @param array     $fields
	 * @param string    $key
	 *
	 * @return float|int
	 */
	public function wc_bookings_process_cost_rules_cost( $cost, $fields, $key ) {
		return $this->filter_pricing_cost( $cost, $fields, 'cost_', $key );
	}

	/**
	 * @param float|int $base_cost
	 * @param array     $fields
	 * @param string    $key
	 *
	 * @return float|int
	 */
	public function wc_bookings_process_cost_rules_base_cost( $base_cost, $fields, $key ) {
		return $this->filter_pricing_cost( $base_cost, $fields, 'base_cost_', $key );
	}

	/**
	 * @param float|int $override_cost
	 * @param array     $fields
	 * @param string    $key
	 *
	 * @return float|int
	 */
	public function wc_bookings_process_cost_rules_override_block_cost( $override_cost, $fields, $key ) {
		return $this->filter_pricing_cost( $override_cost, $fields, 'override_block_', $key );
	}

	/**
	 * @param float|int $cost
	 * @param array     $fields
	 * @param string    $name
	 * @param string    $key
	 *
	 * @return float|int|mixed|string
	 */
	public function filter_pricing_cost( $cost, $fields, $name, $key ) {
		$currency = $this->woocommerce_wpml->multi_currency->get_client_currency();

		if ( $currency === wcml_get_woocommerce_currency_option() ) {
			return $cost;
		}

		if ( isset( $_POST['form'] ) ) {
			parse_str( $_POST['form'], $posted );

			$booking_id = $posted['add-to-cart'];

		} elseif ( isset( $_POST['add-to-cart'] ) ) {

			$booking_id = $_POST['add-to-cart'];

		}

		if ( isset( $booking_id ) ) {
			$original_id = $this->woocommerce_wpml->products->get_original_product_id( $booking_id );

			if ( $booking_id != $original_id ) {
				$fields = maybe_unserialize( get_post_meta( $original_id, '_wc_booking_pricing', true ) );
				$fields = $fields[ $key ];
			}
		}

		$needs_filter_pricing_cost = $this->needs_filter_pricing_cost( $name, $fields );

		if ( $needs_filter_pricing_cost ) {
			if ( isset( $fields[ $name . $currency ] ) ) {
				return $fields[ $name . $currency ];
			} else {
				return $this->woocommerce_wpml->multi_currency->prices->convert_price_amount( $cost, $currency );
			}
		}

		return $cost;
	}

	/**
	 * @param string $name
	 * @param array  $fields
	 *
	 * @return bool
	 */
	public function needs_filter_pricing_cost( $name, $fields ) {

		$modifier_skip_values = [ 'divide', 'times' ];

		if (
			'override_block_' === $name ||
			( 'cost_' === $name && ! in_array( $fields['modifier'], $modifier_skip_values ) ) ||
			( 'base_cost_' === $name && ! in_array( $fields['base_modifier'], $modifier_skip_values ) )
		) {
			return true;
		} else {
			return false;
		}
	}

	public function booking_currency_dropdown() {
		$current_booking_currency = $this->get_cookie_booking_currency();

		$wc_currencies = get_woocommerce_currencies();
		$currencies    = $this->woocommerce_wpml->multi_currency->get_currencies( true );
		?>
		<tr valign="top">
			<th scope="row"><?php _e( 'Booking currency', 'woocommerce-multilingual' ); ?></th>
			<td>
				<select id="dropdown_booking_currency">
					<?php foreach ( $currencies as $currency => $count ) : ?>
						<option
							value="<?php echo esc_html( $currency ); ?>" <?php echo $current_booking_currency == $currency ? 'selected="selected"' : ''; ?>><?php echo esc_html( $wc_currencies[ $currency ] ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<?php

		$wcml_booking_set_currency_nonce = wp_create_nonce( 'booking_set_currency' );

		wc_enqueue_js(
			"

		jQuery(document).on('change', '#dropdown_booking_currency', function(){
		   jQuery.ajax({
				url: ajaxurl,
				type: 'post',
				data: {
					action: 'wcml_booking_set_currency',
					currency: jQuery('#dropdown_booking_currency').val(),
					wcml_nonce: '" . $wcml_booking_set_currency_nonce . "'
				},
				success: function( response ){
					if(typeof response.error !== 'undefined'){
						alert(response.error);
					}else{
					   window.location = window.location.href;
					}
				}
			})
		});
	"
		);
	}

	public function set_booking_currency_ajax() {
		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'booking_set_currency' ) ) {
			echo json_encode( [ 'error' => __( 'Invalid nonce', 'woocommerce-multilingual' ) ] );
			die();
		}

		$this->set_booking_currency( filter_input( INPUT_POST, 'currency', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );

		die();
	}

	public function set_booking_currency( $currency_code = false ) {
		$cookie_name = '_wcml_booking_currency';

		if ( ! isset( $_COOKIE[ $cookie_name ] ) && ! headers_sent() ) {
			$currency_code = $this->woocommerce_wpml->multi_currency->get_currency_code();
		}

		if ( $currency_code ) {
			// @todo uncomment or delete when #wpmlcore-5796 is resolved
			// do_action( 'wpsc_add_cookie', $cookie_name );
			setcookie( $cookie_name, $currency_code, time() + 86400, COOKIEPATH, COOKIE_DOMAIN );
		}
	}

	public function get_cookie_booking_currency() {
		if ( isset( $_COOKIE ['_wcml_booking_currency'] ) ) {
			$currency = $_COOKIE['_wcml_booking_currency'];
		} else {
			$currency = wcml_get_woocommerce_currency_option();
		}

		return $currency;
	}

	public function set_order_currency_on_create_booking_page( $order_id ) {
		update_post_meta( $order_id, '_order_currency', $this->get_cookie_booking_currency() );
	}

	public function filter_booking_currency_symbol( $currency ) {
		global $pagenow;

		remove_filter( 'woocommerce_currency_symbol', [ $this, 'filter_booking_currency_symbol' ] );
		if ( isset( $_COOKIE ['_wcml_booking_currency'] ) && $pagenow == 'edit.php' && isset( $_GET['page'] ) && $_GET['page'] == 'create_booking' ) {
			$currency = get_woocommerce_currency_symbol( $_COOKIE ['_wcml_booking_currency'] );
		}
		add_filter( 'woocommerce_currency_symbol', [ $this, 'filter_booking_currency_symbol' ] );

		return $currency;
	}

	public function create_booking_page_client_currency( $currency ) {
		global $pagenow;

		if ( wpml_is_ajax() && isset( $_POST['form'] ) ) {
			parse_str( $_POST['form'], $posted );
		}

		if ( ( $pagenow == 'edit.php' && isset( $_GET['page'] ) && $_GET['page'] == 'create_booking' ) || ( isset( $posted['_wp_http_referer'] ) && strpos( $posted['_wp_http_referer'], 'page=create_booking' ) !== false ) ) {
			$currency = $this->get_cookie_booking_currency();
		}

		return $currency;
	}

	public function filter_wc_booking_cost( $check, $object_id, $meta_key, $single ) {

		if ( in_array(
			$meta_key,
			[
				'_wc_booking_cost',
				'_wc_booking_base_cost',
				'_wc_display_cost',
				'_wc_booking_pricing',
				'cost',
				'_wc_booking_block_cost',
				'block_cost',
				'_resource_base_costs',
				'_resource_block_costs',
			]
		) ) {

			$original_id = $this->woocommerce_wpml->products->get_original_product_id( $object_id );

			$cost_status = get_post_meta( $original_id, '_wcml_custom_costs_status', true );

			$currency = $this->woocommerce_wpml->multi_currency->get_client_currency();

			if ( $currency === wcml_get_woocommerce_currency_option() ) {
				return $check;
			}

			if ( in_array( $meta_key, [ 'cost', 'block_cost' ] ) ) {

				if ( get_post_type( $object_id ) == 'bookable_person' ) {

					$original_id = apply_filters( 'translate_object_id', wp_get_post_parent_id( $object_id ), 'product', true, $this->woocommerce_wpml->products->get_original_product_language( wp_get_post_parent_id( $object_id ) ) );
					$cost_status = get_post_meta( $original_id, '_wcml_custom_costs_status', true );

					$value = get_post_meta( $object_id, $meta_key . '_' . $currency, true );

					if ( $cost_status && $value ) {

						return $value;

					} else {

						remove_filter( 'get_post_metadata', [ $this, 'filter_wc_booking_cost' ], 10 );

						$cost = get_post_meta( $object_id, $meta_key, true );

						add_filter( 'get_post_metadata', [ $this, 'filter_wc_booking_cost' ], 10, 4 );

						return $this->woocommerce_wpml->multi_currency->prices->convert_price_amount( $cost, $currency );
					}
				} else {

					return $check;

				}
			}

			if ( in_array(
				$meta_key,
				[
					'_wc_booking_pricing',
					'_resource_base_costs',
					'_resource_block_costs',
				]
			) ) {

				remove_filter( 'get_post_metadata', [ $this, 'filter_wc_booking_cost' ], 10 );

				if ( $meta_key == '_wc_booking_pricing' ) {

					if ( $original_id != $object_id ) {
						$value = get_post_meta( $original_id, $meta_key );
					} else {
						$value = $check;
					}
				} else {

					$costs = maybe_unserialize( get_post_meta( $object_id, $meta_key, true ) );

					if ( ! $costs ) {
						$value = $check;
					} elseif ( $cost_status && isset( $costs['custom_costs'][ $currency ] ) ) {

						$res_costs = [];
						foreach ( $costs['custom_costs'][ $currency ] as $resource_id => $cost ) {
							$trns_resource_id               = apply_filters( 'translate_object_id', $resource_id, 'bookable_resource', true );
							$res_costs[ $trns_resource_id ] = $cost;
						}
						$value = [ 0 => $res_costs ];
					} elseif ( $cost_status && isset( $costs[0]['custom_costs'][ $currency ] ) ) {
						$value = [ 0 => $costs[0]['custom_costs'][ $currency ] ];
					} else {

						$converted_values = [];

						foreach ( $costs as $resource_id => $cost ) {
							$converted_values[0][ $resource_id ] = $this->woocommerce_wpml->multi_currency->prices->convert_price_amount( $cost, $currency );
						}

						$value = $converted_values;
					}
				}

				add_filter( 'get_post_metadata', [ $this, 'filter_wc_booking_cost' ], 10, 4 );

				return $value;

			}

			$value = get_post_meta( $original_id, $meta_key . '_' . $currency, true );

			if ( $cost_status && ( ! empty( $value ) || ( empty( $value ) && $meta_key == '_wc_display_cost' ) ) ) {

				return $value;

			} else {

				remove_filter( 'get_post_metadata', [ $this, 'filter_wc_booking_cost' ], 10 );

				$value = get_post_meta( $original_id, $meta_key, true );

				$value = $this->woocommerce_wpml->multi_currency->prices->convert_price_amount( $value, $currency );

				add_filter( 'get_post_metadata', [ $this, 'filter_wc_booking_cost' ], 10, 4 );

				return $value;

			}
		}

		return $check;
	}

	/**
	 * @return bool
	 */
	public static function isWcBookingsBefore_1_10_9() {
		return version_compare( WC_BOOKINGS_VERSION, '1.10.9', '<' );
	}
}
