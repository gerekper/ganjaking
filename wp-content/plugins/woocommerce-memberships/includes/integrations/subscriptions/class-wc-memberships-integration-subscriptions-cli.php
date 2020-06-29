<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

/**
 * WooCommerce Memberships CLI Subscriptions extension.
 *
 * Extends Memberships WP CLI support for user memberships and plans with Subscriptions-specific properties via WordPress hooks.
 *
 * @since 1.7.0
 */
class WC_Memberships_Integration_Subscriptions_CLI {


	/**
	 * Extends the Memberships WP CLI support with Subscriptions specific fields.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		// filter default fields in CLI columns/table
		add_filter( 'wc_memberships_cli_user_membership_default_fields', array( $this, 'get_user_membership_default_fields' ) );
		add_filter( 'wc_memberships_cli_membership_plan_default_fields', array( $this, 'get_membership_plan_default_fields' ) );

		// filter output data in CLI columns/table
		add_filter( 'wc_memberships_cli_membership_plan_data', array( $this, 'get_membership_plan_data' ), 10, 2 );
		add_filter( 'wc_memberships_cli_user_membership_data', array( $this, 'get_user_membership_data' ), 10, 2 );

		// create or update a user membership via CLI data
		add_filter( 'woocommerce_memberships_cli_create_user_membership_data', array( $this, 'user_membership_data_validate_subscription_args' ) ) ;
		add_filter( 'woocommerce_memberships_cli_update_user_membership_data', array( $this, 'user_membership_data_validate_subscription_args' ) ) ;
		add_action( 'wc_memberships_cli_create_user_membership',               array( $this, 'tie_subscription_to_membership' ), 10, 2 );
		add_action( 'wc_memberships_cli_update_user_membership',               array( $this, 'tie_subscription_to_membership' ), 10, 2 );

		// create or update a membership plan via CLI data
		add_filter( 'woocommerce_memberships_cli_create_membership_plan_data', array( $this, 'membership_plan_data_validate_subscription_args' ) );
		add_filter( 'woocommerce_memberships_cli_update_membership_plan_data', array( $this, 'membership_plan_data_validate_subscription_args' ) );
		add_action( 'wc_memberships_cli_create_membership_plan',               array( $this, 'set_subscription_tied_membership_length' ), 10, 2 );
		add_action( 'wc_memberships_cli_update_membership_plan',               array( $this, 'set_subscription_tied_membership_length' ), 10, 2 );
	}


	/**
	 * Checks if there is at least one subscription product that grants access.
	 *
	 * Executes the check on the products specified in CLI command to create or update a plan.
	 *
	 * @since 1.7.0
	 *
	 * @param array $data
	 * @return bool
	 */
	private function plan_has_subscription_product( $data ) {

		$has_subscription = false;

		if ( ! empty( $data['product'] ) ) {

			$product_ids = array_map( 'absint', explode( ',', $data['product'] ) );

			if ( ! empty( $product_ids ) ) {

				foreach ( $product_ids as $product_id ) {

					if ( \WC_Subscriptions_Product::is_subscription( $product_id ) ) {
						$has_subscription = true;
						break;
					}
				}
			}
		}

		return $has_subscription;
	}


	/**
	 * Validates Subscription data before creating a membership plan.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $data
	 * @return array
	 * @throws \WC_CLI_Exception
	 */
	public function membership_plan_data_validate_subscription_args( $data ) {

		if ( isset( $data['subscription_length'] ) ) {

			if ( ! $this->plan_has_subscription_product( $data ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_no_subscription_product_that_grants_access', 'If you want to use subscription-specific arguments for the plan data, you need to specify at least one subscription product that grants access.' );
			}

			$length = sanitize_text_field( $data['length'] );

			if ( 'unlimited' === $length ) {

				$data['subscription_length'] = 'unlimited';

			} elseif ( '' === $length ) {

				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_plan_subscription_length', sprintf( 'Membership Plan length "%s" is not valid. Must be "unlimited" or in "<amount> <period>" format.', $data['length'] ) );

			} else {

				$length_amount = wc_memberships_parse_period_length( $length, 'amount' );
				$length_period = wc_memberships_parse_period_length( $length, 'period' );

				if ( ! is_int( $length_amount ) || $length_amount < 1 || empty( $length_period ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_plan_subscription_length', sprintf( 'Membership Plan length "%s" is not valid. Must be "unlimited" or in "<amount> <period>" format.', $data['length'] ) );
				}

				$data['subscription_length'] = $length_amount . ' ' . $length_period;
			}
		}

		if ( isset( $data['subscription_start_date'] ) || isset( $data['subscription_end_date'] ) ) {

			if ( ! $this->plan_has_subscription_product( $data ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_no_subscription_product_that_grants_access', 'If you want to use subscription-specific arguments for the plan data, you need to specify at least one subscription product that grants access.' );
			}

			$start_date = isset( $data['subscription_start_date'] ) ? wc_memberships_parse_date( $data['subscription_start_date'], 'mysql' ) : date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
			$end_date   = isset( $data['subscription_end_date'] )   ? wc_memberships_parse_date( $data['subscription_end_date'],  'mysql' )  : null;

			if ( ! $start_date ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_subscription_start_date', sprintf( 'Membership Plan start date "%s" is not valid. Must be a non-empty YYYY-MM-DD value. Can be omitted and current date will be used.', $data['start_date'] ) );
			} elseif( ! $end_date ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_subscription_end_date', sprintf( 'Membership Plan end date "%s" is not valid. Must be a non-empty YYYY-MM-DD value.', $data['end_date'] ) );
			} elseif ( isset( $data['subscription_length'] ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_plan_subscription_length_conflict', 'You cannot define a plan subscription length and fixed subscription start or end dates at the same time.' );
			}

			$data['subscription_start_date'] = $start_date;
			$data['subscription_end_date']   = $end_date;
		}

		return $data;
	}


	/**
	 * Saves or updates subscription access information of a membership plan.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan membership plan being saved or updated
	 * @param array $data array of membership plan data
	 */
	public function set_subscription_tied_membership_length( $membership_plan, $data ) {

		$membership_plan = new \WC_Memberships_Integration_Subscriptions_Membership_Plan( $membership_plan->post );

		$membership_plan->delete_access_length();
		$membership_plan->delete_access_start_date();
		$membership_plan->delete_access_end_date();
		$membership_plan->delete_installment_plan();

		if ( $membership_plan->has_subscription() ) {

			if ( isset( $data['subscription_length'] ) ) {

				if ( 'unlimited' === $data['subscription_length'] ) {
					$membership_plan->set_installment_plan();
				} else {
					$membership_plan->set_access_length( $data['subscription_length'] );
					$membership_plan->set_installment_plan();
				}

			} elseif ( isset( $data['subscription_start_date'], $data['subscription_end_date'] ) ) {

				$membership_plan->set_access_start_date( $data['subscription_start_date'] );
				$membership_plan->set_access_end_date( $data['subscription_end_date'] );
				$membership_plan->set_installment_plan();
			}
		}
	}


	/**
	 * Validates Subscription data before creating a user membership.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $data
	 * @return array
	 * @throws \WC_CLI_Exception
	 */
	public function user_membership_data_validate_subscription_args( $data ) {

		if ( isset( $data['subscription'] ) ) {

			$subscription = wcs_get_subscription( $data['subscription'] );

			if ( ! $subscription instanceof \WC_Subscription ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_subscription_not_found', sprintf( 'Subscription %s not found.', $data['subscription'] ) );
			}

			$data['subscription'] = (int) $subscription->get_id();
		}

		return $data;
	}


	/**
	 * Saves or updates subscription data of a user membership.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership the user membership object being created or updated
	 * @param array $data array of membership data
	 */
	public function tie_subscription_to_membership( $user_membership, $data ) {

		$subscription_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership->post );

		if ( isset( $data['subscription'] ) && $data['subscription'] > 0 ) {

			$subscription_membership->set_subscription_id( $data['subscription'] );

			$the_subscription = wcs_get_subscription( $data['subscription'] );

			// maybe update the free trial end date information
			if ( $the_subscription && ( $trial_end = wc_memberships()->get_integrations_instance()->get_subscriptions_instance()->get_subscription_event_date( $the_subscription, 'trial_end' ) ) ) {
				$subscription_membership->set_free_trial_end_date( $trial_end );
			}
		}
	}


	/**
	 * Filters membership plan default fields in CLI to add subscription information.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $default_fields
	 * @return array
	 */
	public function get_membership_plan_default_fields( array $default_fields ) {

		$default_fields[] = 'has_subscription';

		return array_unique( $default_fields );
	}


	/**
	 * Filters user membership default fields in CLI to add subscription information.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $default_fields
	 * @return array
	 */
	public function get_user_membership_default_fields( array $default_fields ) {

		$default_fields[] = 'subscription';

		return array_unique( $default_fields );
	}


	/**
	 * Returns membership plan data adjusted with subscription data.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $membership_plan_data the plan data
	 * @param \WC_Memberships_Membership_Plan $membership_plan the plan object
	 * @return array
	 */
	public function get_membership_plan_data( $membership_plan_data, $membership_plan ) {

		if (    is_array( $membership_plan_data )
		     && $membership_plan instanceof \WC_Memberships_Membership_Plan ) {

			$integration      = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();
			$has_subscription = $integration->has_membership_plan_subscription( $membership_plan->get_id() );

			$membership_plan_data['has_subscription'] = true === $has_subscription ? 'yes' : 'no';
		}

		return $membership_plan_data;
	}


	/**
	 * Returns membership plan data adjusted with subscription data.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $user_membership_data the user membership data
	 * @param \WC_Memberships_User_Membership $user_membership the user membership object
	 * @return array
	 */
	public function get_user_membership_data( $user_membership_data, $user_membership ) {

		if (    is_array( $user_membership_data )
		     && $user_membership instanceof \WC_Memberships_User_Membership ) {

			$integration     = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();
			$subscription_id = $integration ? $integration->get_user_membership_subscription_id( $user_membership->get_id() ) : null;

			$user_membership_data['subscription_id'] = $subscription_id ? (int) $subscription_id : '';
			$user_membership_data['subscription']    = $subscription_id ? (int) $subscription_id : '';
		}

		return $user_membership_data;
	}


}
