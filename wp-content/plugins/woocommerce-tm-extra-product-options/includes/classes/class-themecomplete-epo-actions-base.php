<?php
/**
 * Extra Product Options Actions class
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Actions class
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_Actions_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Actions_Base|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @return THEMECOMPLETE_EPO_Actions_Base
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'woocommerce_checkout_update_order_review' ], 10 );
		add_action( 'woocommerce_load_shipping_methods', [ $this, 'woocommerce_load_shipping_methods' ], 10, 1 );
		add_filter( 'woocommerce_package_rates', [ $this, 'woocommerce_package_rates' ], 10, 1 );
	}

	/**
	 * Refresh shipping methods on AJAX update order review on checkout
	 *
	 * @return void
	 * @since 6.4
	 */
	public function woocommerce_checkout_update_order_review() {
		$packages = WC()->cart->get_shipping_packages();

		// Remove rates stored in the WC session data.
		foreach ( $packages as $package_key => $package ) {
			WC()->session->set( 'shipping_for_package_' . $package_key, false );
		}
	}

	/**
	 * Get rate and key
	 *
	 * @param string $rate Rate string.
	 * @return string|array<mixed>
	 * @since 6.4
	 */
	public function get_rate_and_key( $rate = '' ) {
		$match = preg_match( '/(?P<rate>[\p{L}_]+)(:|-)(?P<key>\d+)/u', $rate, $matches );
		if ( $match ) {
			return [
				'rate' => $matches['rate'],
				'key'  => $matches['key'],
			];
		}
		return $rate;
	}

	/**
	 * Alter shipping methods
	 *
	 * @param array<mixed> $package Package information.
	 * @return void
	 * @since 6.4
	 */
	public function woocommerce_load_shipping_methods( $package = [] ) {
		if ( ! empty( $package ) ) {
			$enable_ids  = [];
			$disable_ids = [];
			foreach ( $package['contents'] as $ckey => $citem ) {
				if ( isset( $citem['tmcartepo'] ) && is_array( $citem['tmcartepo'] ) ) {
					foreach ( $citem['tmcartepo'] as $epo ) {
						$shippingmethodsenable            = isset( $epo['shippingmethodsenable'] ) ? $epo['shippingmethodsenable'] : '';
						$shippingmethodsenablelogicrules  = isset( $epo['shippingmethodsenablelogicrules'] ) ? $epo['shippingmethodsenablelogicrules'] : '';
						$shippingmethodsdisable           = isset( $epo['shippingmethodsdisable'] ) ? $epo['shippingmethodsdisable'] : '';
						$shippingmethodsdisablelogicrules = isset( $epo['shippingmethodsdisablelogicrules'] ) ? $epo['shippingmethodsdisablelogicrules'] : '';
						$posted_value                     = $epo['value'];
						if ( $shippingmethodsenable && is_array( $shippingmethodsenable ) ) {
							$toggle_action = 'show';
							$group_visible = true;
							if ( $shippingmethodsenablelogicrules && is_array( $shippingmethodsenablelogicrules ) && isset( $shippingmethodsenablelogicrules['rules'] ) && is_array( $shippingmethodsenablelogicrules['rules'] ) && ! empty( $shippingmethodsenablelogicrules['rules'] ) ) {
								$group_visible    = false;
								$toggle_action    = $shippingmethodsenablelogicrules['toggle'];
								$condition_groups = $shippingmethodsenablelogicrules['rules'];
								foreach ( $condition_groups as $conditions ) {
									$conditions_met = false;
									foreach ( $conditions as $condition ) {
										$operator       = $condition['operator'];
										$value          = $condition['value'];
										$conditions_met = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->tm_check_match( $posted_value, $value, $operator );
									}
									if ( $conditions_met ) {
										$group_visible = true;
										break;
									}
								}
							}

							if ( ( 'show' === $toggle_action && $group_visible ) || ( 'hide' === $toggle_action && ! $group_visible ) ) {
								$enable_ids = array_merge( $enable_ids, $shippingmethodsenable );
								$enable_ids = array_unique( $enable_ids );
							}
						}
						if ( $shippingmethodsdisable && is_array( $shippingmethodsdisable ) ) {
							$toggle_action = 'show';
							$group_visible = true;
							if ( $shippingmethodsdisablelogicrules && is_array( $shippingmethodsdisablelogicrules ) && isset( $shippingmethodsdisablelogicrules['rules'] ) && is_array( $shippingmethodsdisablelogicrules['rules'] ) && ! empty( $shippingmethodsdisablelogicrules['rules'] ) ) {
								$group_visible    = false;
								$toggle_action    = $shippingmethodsdisablelogicrules['toggle'];
								$condition_groups = $shippingmethodsdisablelogicrules['rules'];
								foreach ( $condition_groups as $conditions ) {
									$conditions_met = false;
									foreach ( $conditions as $condition ) {
										$operator       = $condition['operator'];
										$value          = $condition['value'];
										$conditions_met = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->tm_check_match( $posted_value, $value, $operator );
									}
									if ( $conditions_met ) {
										$group_visible = true;
										break;
									}
								}
							}

							if ( ( 'show' === $toggle_action && $group_visible ) || ( 'hide' === $toggle_action && ! $group_visible ) ) {
								$disable_ids = array_merge( $disable_ids, $shippingmethodsdisable );
								$disable_ids = array_unique( $disable_ids );
							}
						}
					}
				}
				if ( isset( $citem['tmcartfee'] ) && is_array( $citem['tmcartfee'] ) ) {
					foreach ( $citem['tmcartfee'] as $epo ) {
						$shippingmethodsenable            = isset( $epo['shippingmethodsenable'] ) ? $epo['shippingmethodsenable'] : '';
						$shippingmethodsenablelogicrules  = isset( $epo['shippingmethodsenablelogicrules'] ) ? $epo['shippingmethodsenablelogicrules'] : '';
						$shippingmethodsdisable           = isset( $epo['shippingmethodsdisable'] ) ? $epo['shippingmethodsdisable'] : '';
						$shippingmethodsdisablelogicrules = isset( $epo['shippingmethodsdisablelogicrules'] ) ? $epo['shippingmethodsdisablelogicrules'] : '';
						$posted_value                     = $epo['value'];
						if ( $shippingmethodsenable && is_array( $shippingmethodsenable ) ) {
							$toggle_action = 'show';
							$group_visible = true;
							if ( $shippingmethodsenablelogicrules && is_array( $shippingmethodsenablelogicrules ) && isset( $shippingmethodsenablelogicrules['rules'] ) && is_array( $shippingmethodsenablelogicrules['rules'] ) && ! empty( $shippingmethodsenablelogicrules['rules'] ) ) {
								$group_visible    = false;
								$toggle_action    = $shippingmethodsenablelogicrules['toggle'];
								$condition_groups = $shippingmethodsenablelogicrules['rules'];
								foreach ( $condition_groups as $conditions ) {
									$conditions_met = false;
									foreach ( $conditions as $condition ) {
										$operator       = $condition['operator'];
										$value          = $condition['value'];
										$conditions_met = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->tm_check_match( $posted_value, $value, $operator );
									}
									if ( $conditions_met ) {
										$group_visible = true;
										break;
									}
								}
							}

							if ( ( 'show' === $toggle_action && $group_visible ) || ( 'hide' === $toggle_action && ! $group_visible ) ) {
								$enable_ids = array_merge( $enable_ids, $shippingmethodsenable );
								$enable_ids = array_unique( $enable_ids );
							}
						}
						if ( $shippingmethodsdisable && is_array( $shippingmethodsdisable ) ) {
							$toggle_action = 'show';
							$group_visible = true;
							if ( $shippingmethodsdisablelogicrules && is_array( $shippingmethodsdisablelogicrules ) && isset( $shippingmethodsdisablelogicrules['rules'] ) && is_array( $shippingmethodsdisablelogicrules['rules'] ) && ! empty( $shippingmethodsdisablelogicrules['rules'] ) ) {
								$group_visible    = false;
								$toggle_action    = $shippingmethodsdisablelogicrules['toggle'];
								$condition_groups = $shippingmethodsdisablelogicrules['rules'];
								foreach ( $condition_groups as $conditions ) {
									$conditions_met = false;
									foreach ( $conditions as $condition ) {
										$operator       = $condition['operator'];
										$value          = $condition['value'];
										$conditions_met = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->tm_check_match( $posted_value, $value, $operator );
									}
									if ( $conditions_met ) {
										$group_visible = true;
										break;
									}
								}
							}

							if ( ( 'show' === $toggle_action && $group_visible ) || ( 'hide' === $toggle_action && ! $group_visible ) ) {
								$disable_ids = array_merge( $disable_ids, $shippingmethodsdisable );
								$disable_ids = array_unique( $disable_ids );
							}
						}
					}
				}
				if ( isset( $citem['tmsubscriptionfee'] ) && is_array( $citem['tmsubscriptionfee'] ) ) {
					foreach ( $citem['tmsubscriptionfee'] as $epo ) {
						$shippingmethodsenable            = isset( $epo['shippingmethodsenable'] ) ? $epo['shippingmethodsenable'] : '';
						$shippingmethodsenablelogicrules  = isset( $epo['shippingmethodsenablelogicrules'] ) ? $epo['shippingmethodsenablelogicrules'] : '';
						$shippingmethodsdisable           = isset( $epo['shippingmethodsdisable'] ) ? $epo['shippingmethodsdisable'] : '';
						$shippingmethodsdisablelogicrules = isset( $epo['shippingmethodsdisablelogicrules'] ) ? $epo['shippingmethodsdisablelogicrules'] : '';
						$posted_value                     = $epo['value'];
						if ( $shippingmethodsenable && is_array( $shippingmethodsenable ) ) {
							$toggle_action = 'show';
							$group_visible = true;
							if ( $shippingmethodsenablelogicrules && is_array( $shippingmethodsenablelogicrules ) && isset( $shippingmethodsenablelogicrules['rules'] ) && is_array( $shippingmethodsenablelogicrules['rules'] ) && ! empty( $shippingmethodsenablelogicrules['rules'] ) ) {
								$group_visible    = false;
								$toggle_action    = $shippingmethodsenablelogicrules['toggle'];
								$condition_groups = $shippingmethodsenablelogicrules['rules'];
								foreach ( $condition_groups as $conditions ) {
									$conditions_met = false;
									foreach ( $conditions as $condition ) {
										$operator       = $condition['operator'];
										$value          = $condition['value'];
										$conditions_met = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->tm_check_match( $posted_value, $value, $operator );
									}
									if ( $conditions_met ) {
										$group_visible = true;
										break;
									}
								}
							}

							if ( ( 'show' === $toggle_action && $group_visible ) || ( 'hide' === $toggle_action && ! $group_visible ) ) {
								$enable_ids = array_merge( $enable_ids, $shippingmethodsenable );
								$enable_ids = array_unique( $enable_ids );
							}
						}
						if ( $shippingmethodsdisable && is_array( $shippingmethodsdisable ) ) {
							$toggle_action = 'show';
							$group_visible = true;
							if ( $shippingmethodsdisablelogicrules && is_array( $shippingmethodsdisablelogicrules ) && isset( $shippingmethodsdisablelogicrules['rules'] ) && is_array( $shippingmethodsdisablelogicrules['rules'] ) && ! empty( $shippingmethodsdisablelogicrules['rules'] ) ) {
								$group_visible    = false;
								$toggle_action    = $shippingmethodsdisablelogicrules['toggle'];
								$condition_groups = $shippingmethodsdisablelogicrules['rules'];
								foreach ( $condition_groups as $conditions ) {
									$conditions_met = false;
									foreach ( $conditions as $condition ) {
										$operator       = $condition['operator'];
										$value          = $condition['value'];
										$conditions_met = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->tm_check_match( $posted_value, $value, $operator );
									}
									if ( $conditions_met ) {
										$group_visible = true;
										break;
									}
								}
							}

							if ( ( 'show' === $toggle_action && $group_visible ) || ( 'hide' === $toggle_action && ! $group_visible ) ) {
								$disable_ids = array_merge( $disable_ids, $shippingmethodsdisable );
								$disable_ids = array_unique( $disable_ids );
							}
						}
					}
				}
			}

			$shipping             = WC_Shipping::instance();
			$shipping_zone        = WC_Shipping_Zones::get_zone_matching_package( $package );
			$free_shipping        = false;
			$free_shipping_key    = false;
			$all_shipping_methods = $shipping_zone->get_shipping_methods( false );

			foreach ( $all_shipping_methods as $shipping_key => $shipping_method ) {
				$id      = $shipping_method->id;
				$method  = $shipping_method;
				$enable  = false;
				$disable = false;

				foreach ( $disable_ids as $disable_id ) {
					$rk = $this->get_rate_and_key( $disable_id );
					if ( is_array( $rk ) ) {
						if ( (string) $rk['key'] === (string) $shipping_key && (string) $rk['rate'] === (string) $id ) {
							$disable = true;
							break;
						}
					} elseif ( (string) $id === (string) $rk ) {
						$disable = true;
						break;
					}
				}

				if ( $disable ) {
					$method->enabled                             = 'no';
					$shipping->shipping_methods[ $shipping_key ] = $method;
				} else {
					foreach ( $enable_ids as $enable_id ) {
						$rk = $this->get_rate_and_key( $enable_id );
						if ( is_array( $rk ) ) {
							if ( (string) $rk['key'] === (string) $shipping_key && (string) $rk['rate'] === (string) $id ) {
								$enable = true;
								break;
							}
						} elseif ( (string) $id === (string) $rk ) {
							$enable = true;
							break;
						}
					}

					if ( $enable ) {
						$method->requires                            = '';
						$method->enabled                             = 'yes';
						$shipping->shipping_methods[ $shipping_key ] = $method;
					}
				}
			}
		}
	}

	/**
	 * Get rate and key
	 *
	 * @param array<mixed> $rates Package rates.
	 * @return array<mixed>
	 * @since 6.4
	 */
	public function woocommerce_package_rates( $rates ) {
		return $rates;
	}
}
