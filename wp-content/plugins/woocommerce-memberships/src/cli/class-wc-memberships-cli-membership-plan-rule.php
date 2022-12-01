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
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

/**
 * Manage Membership Plan Rules from WP CLI.
 *
 * @since 1.9.0
 * @deprecated since 1.13.0
 */
class WC_Memberships_CLI_Membership_Plan_Rule extends \WC_Memberships_CLI_Command {


	/**
	 * Create a Membership Plan Rule.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : Associative args for the new Membership Plan Rule
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * Required fields:
	 *
	 * * plan
	 * * type
	 * * target
	 *
	 * These fields are optionally available for create command:
	 *
	 * * object_ids
	 * * discount (for purchasing_discount rules only)
	 * * active (for purchasing_discount rules only, defaults to active)
	 * * access_type (for Products only)
	 * * access_schedule (for content_restriction and product_restriction rules only)
	 * * exclude_trial (for Subscriptions only)
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan rule create --plan=123 --type="content_restriction" --target="post_type:post"
	 *
	 *     wp wc memberships plan rule create --name="Bronze Plan" --type="purchasing_discount" --target="taxonomy:product_cat" --ids=11,12,13 --discount="10%"
	 *
	 *     wp wc memberships plan rule create --plan="Silver Plan" --type="product_restriction" --target="post_type:product" --ids=168,169 --access_type="purchase"
	 *
	 *     wp wc memberships plan rule create --name="Gold Plan" --type="purchasing_discount" --target="post_type:product" --discount=20 --exclude_trial="yes"
	 *
	 *
	 * @since 1.9.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function create( $args, $assoc_args ) {

		try {

			$rule_args = array();

			/**
			 * Filter arguments when creating a Membership Plan via CLI.
			 *
			 * @since 1.9.0
			 *
			 * @param array $args
			 */
			$data = apply_filters( 'woocommerce_memberships_cli_create_membership_plan_rule_data', $this->unflatten_array( $assoc_args ) );

			if ( ! isset( $data['plan'] ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_missing_plan_rule_plan', sprintf( 'Missing parameter "%s".', 'plan' ) );
			} elseif ( $plan = wc_memberships_get_membership_plan( is_numeric( $data['plan'] ) ?  (int) $data['plan'] : (string) $data['plan'] ) ) {
				$membership_plan_id = $plan->get_id();
			} else {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_invalid_plan', sprintf( 'Cannot find plan "%s".', $data['plan'] ) );
			}

			$rule_args['membership_plan_id'] = $membership_plan_id;

			$rule_type = isset( $data['type'] ) ? trim( $data['type'] ) : null;

			if ( empty( $rule_type ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_missing_plan_rule_type', sprintf( 'Missing parameter "%s".', 'type' ) );
			} elseif ( ! in_array( $rule_type, wc_memberships()->get_rules_instance()->get_valid_rule_types(), true ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_type', sprintf( 'Invalid rule type "%1$s". Must be one of: %2$s.', $data['type'], implode( ', ', wc_memberships()->get_rules_instance()->get_valid_rule_types() ) ) );
			}

			$rule_args['rule_type'] = $rule_type;

			if ( ! isset( $data['target'] ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_missing_plan_rule_content', sprintf( 'Missing parameter "%s".', 'target' ) );
			}

			// e.g. post_type:post, or taxonomy:product_cat
			$target            = explode( ':', $data['target'] );
			$content_type      = isset( $target[0] ) ? $target[0] : null;
			$content_type_name = isset( $target[1] ) ? $target[1] : null;

			// ensure that the content type is coherent with the rule type
			if ( ! $content_type || ! $content_type_name ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content', sprintf( 'The specified target combination for this rule does not seem valid: "%s".', $data['target'] ) );
			} elseif ( ! in_array( $content_type, array( 'post_type', 'taxonomy' ), true ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content_type', sprintf( 'Invalid content type "%s". Must be one of "post_type" or "taxonomy".', $content_type ) );
			} elseif ( 'taxonomy' === $content_type && ! taxonomy_exists( $content_type_name ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content_type_name_taxonomy', sprintf( 'The specified taxonomy for this rule does not seem to exist: "%s".', $content_type_name ) );
			} elseif ( 'post_type' === $content_type && ! post_type_exists( $content_type_name ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content_type_name_post_type', sprintf( 'The specified post type for this rule does not seem to exist: "%s".', $content_type_name ) );
			} elseif ( 'content_restriction' === $rule_type && in_array( $content_type_name, array( 'product', 'product_cat' ), true ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content_restriction_target', sprintf( 'If you are creating a content restriction rule, it cannot target "%s". Use another post type or taxonomy instead or create a product restriction rule.', $content_type_name ) );
			} elseif ( in_array( $rule_type, array( 'product_restriction', 'purchasing_discount' ), true )  && ! in_array( $content_type_name, array( 'product', 'product_cat' ), true ) ) {
				if ( 'product_restriction' === $rule_type ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_product_restriction', sprintf( 'If you are creating a product restriction rule, it must target a product or a product category instead of "%s".', $content_type_name ) );
				} elseif ( 'purchasing_discount' === $rule_type ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_purchasing_discount_target', sprintf( 'If you are creating a purchasing discount rule, it must target a product or a product category instead of "%s".', $content_type_name ) );
				}
			}

			$rule_args['content_type']      = $content_type;
			$rule_args['content_type_name'] = $content_type_name;

			// target specific content by IDs
			if ( isset( $data['object_ids'] ) ) {

				$ids     = array();
				$set_ids = is_string( $data['object_ids'] ) ? explode( ',', $data['object_ids'] ) : (array) $data['object_ids'];

				foreach ( $set_ids as $id ) {
					if ( is_numeric( $id ) ) {
						$ids[] = (int) $id;
					}
				}

				if ( ! empty( $ids ) ) {
					$rule_args['object_ids'] = $ids;
				} else {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_object_ids', sprintf( 'Could not parse specified object IDs: "%s".', is_array( $data['object_ids'] ) ) ? implode( ',', (array) $data['object_ids'] ) : $data['object_ids'] );
				}
			}

			// discount rules
			if ( ! empty( $data['discount'] ) ) {

				if ( $rule_type === 'purchasing_discount' ) {

					$discount_amount = (float) preg_replace( '/[^0-9,.]/', '', trim( str_replace( ',', '.', $data['discount'] ) ) );

					if ( $discount_amount > 0 ) {

						if ( Framework\SV_WC_Helper::str_ends_with( '%', $data['discount'] ) ) {
							$discount_type = 'percentage';
						} else {
							$discount_type = 'amount';
						}

						$rule_args['discount_type']   = $discount_type;
						$rule_args['discount_amount'] = $discount_amount;

					} else {

						throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_discount', sprintf( 'Could not parse discount type and amount: "%s".', $data['discount'] ) );
					}

				} else {

					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_purchasing_discount_mismatch_rule_type', 'You cannot assign discount properties to a rule that is not a purchasing discount rule. Please create a new rule or ensure you are editing the right rule.' );
				}
			}

			// for now the 'active' flag is for purchasing discounts only
			if ( ! empty( $data['active'] ) ) {
				if ( $rule_type === 'purchasing_discount' ) {
					if ( in_array( trim( $data['active'] ), array( 'yes', 'no' ), true ) ) {
						$rule_args['active'] = trim( $data['active'] );
					} else {
						throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_invalid_active_status', sprintf( 'Purchasing discount rule active status must be either "yes" or "no", cannot parse "%s".', $data['active'] ) );
					}
				} else {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_active_status_rule_type_mismatch', 'For now you can only set an active status to purchasing discount rules.' );
				}
			}

			// more optional properties for content or product restriction rules
			$access_type     = isset( $data['access_type'] )     ? $data['access_type']     : null;
			$access_schedule = isset( $data['access_schedule'] ) ? $data['access_schedule'] : null;
			$exclude_trial   = isset( $data['exclude_trial'] )   ? $data['exclude_trial']   : null;

			if ( ! empty( $access_type ) ) {
				if ( $rule_type !== 'product_restriction' ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_access_type_rule_type_mismatch', 'You cannot set access type for rules that are not of product restriction.' );
				} elseif ( ! wc_memberships()->get_rules_instance()->is_valid_rule_access_type( $access_type ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_access_type', sprintf( 'The specified access type for this rule does not seem valid: "%1$s". It should be one of: %2$s', $content_type_name, implode( ', ', wc_memberships()->get_rules_instance()->get_rules_valid_access_types() ) ) );
				} else {
					$rule_args['access_type'] = $access_type;
				}
			}

			if ( ! empty( $access_schedule ) ) {

				if ( $rule_type !== 'purchasing_discount' ) {

					if ( 'immediate' === $access_schedule ) {

						$rule_args['access_schedule'] = $access_schedule;

					} else {

						$amount = wc_memberships_parse_period_length( $access_schedule, 'amount' );
						$period = wc_memberships_parse_period_length( $access_schedule, 'period' );

						if ( $amount && $period && $amount > 0 ) {
							$rule_args['access_schedule'] = wc_memberships_parse_period_length( $access_schedule );
						} else {
							throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_access_schedule', sprintf( 'The specified access schedule for this rule does not seem valid: "%s".', $access_schedule ) );
						}
					}

				} else {

					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_access_schedule_rule_type_mismatch', 'You cannot set an access schedule for purchasing discount rules, you must use content or product restriction rules.' );
				}
			}

			if ( ! empty( $exclude_trial ) ) {
				if ( ! in_array( $exclude_trial, array( 'yes', 'no' ), true ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_exclude_trial_invalid_option', sprintf( 'Invalid option for exclude_trial: "%s". It must be either "yes" or "no".', $exclude_trial ) );
				} elseif ( ! wc_memberships()->get_integrations_instance()->is_subscriptions_active() ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_exclude_trial_subscriptions_inactive', sprintf( 'You want to %s the free trial period for this rule, but it looks like Subscriptions is not active. Please activate WooCommerce Subscriptions to set rules specific to Subscription Products.', $exclude_trial === 'yes' ? 'exclude' : 'include' ) );
				} else {
					$rule_args['access_schedule_exclude_trial'] = $exclude_trial;
				}
			}

			// now that we have all the rule arguments, create a rule object
			$membership_plan_rule = new \WC_Memberships_Membership_Plan_Rule( $rule_args );

			// set an ID before saving
			$membership_plan_rule->set_id();

			// add the rule
			wc_memberships()->get_rules_instance()->set_rules( array( 'add' => array( $membership_plan_rule ), ) );

			/**
			 * Upon creating a Membership Plan Rule via CLI.
			 *
			 * @since 1.9.0
			 *
			 * @param \WC_Memberships_Membership_Plan_Rule $membership_plan_rule
			 * @param array $data
			 */
			do_action( 'wc_memberships_cli_create_membership_plan_rule', $membership_plan_rule, $data );

			\WP_CLI::success( sprintf( 'Created Membership Plan Rule %s.', $membership_plan_rule->get_id() ) );

		} catch ( \WC_CLI_Exception $e ) {

			\WP_CLI::error( $e->getMessage() );
		}
	}


	/**
	 * Update a Membership Plan Rule.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Membership Plan Rule ID
	 *
	 * [--<field>=<value>]
	 * : One or more fields to update
	 *
	 * ## AVAILABLE_FIELDS
	 *
	 * For more fields, see: wp wc memberships plan rule create --help
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan rule update rule_123 --ids=2,11
	 *
	 *
	 * @since 1.9.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function update( $args, $assoc_args ) {

		try {

			// plan ID or name
			$rule_id = is_numeric( $args[0] ) ? (int) $args[0] : $args[0];

			/**
			 * Filter arguments when updating a Membership Plan Rule via CLI.
			 *
			 * @since 1.9.0
			 *
			 * @param array $args
			 * @param int $id
			 */
			$data = apply_filters( 'woocommerce_memberships_cli_update_membership_plan_rule_data', $this->unflatten_array( $assoc_args ), $rule_id );

			// begin by checking if the rule to update exists in the first place
			$rule = wc_memberships()->get_rules_instance()->get_rule( $rule_id );

			if ( ! $rule instanceof \WC_Memberships_Membership_Plan_Rule ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_membership_plan_rule_not_found', sprintf( 'Membership Plan Rule %s not found.', $rule_id ) );
			}

			// we cannot change an existing rule type
			if ( ! empty( $data['type'] ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_update_plan_rule_type_forbidden', 'Cannot change a rule type. Please delete the rule and create a new one.' );
			}

			// changing the content type should be compatible with the current rule type
			if ( ! empty( $data['target'] ) ) {

				// e.g. post_type:post, or taxonomy:product_cat
				$target            = explode( ':', trim( $data['target'] ) );
				$content_type      = isset( $target[0] ) ? $target[0] : null;
				$content_type_name = isset( $target[1] ) ? $target[1] : null;
				$rule_type         = $rule->get_rule_type();

				// ensure that the content type is coherent with the rule type
				if ( ! $content_type || ! $content_type_name ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content', sprintf( 'The specified target combination for this rule does not seem valid: "%s".', $data['target'] ) );
				} elseif ( ! in_array( $content_type, array( 'post_type', 'taxonomy' ), true ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content_type', sprintf( 'Invalid content type "%s". Must be one of "post_type" or "taxonomy".', $content_type ) );
				} elseif ( 'taxonomy' === $content_type && ! taxonomy_exists( $content_type_name ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content_type_name_taxonomy', sprintf( 'The specified taxonomy for this rule does not seem to exist: "%s".', $content_type_name ) );
				} elseif ( 'post_type' === $content_type && ! post_type_exists( $content_type_name ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content_type_name_post_type', sprintf( 'The specified post type for this rule does not seem to exist: "%s".', $content_type_name ) );
				} elseif ( 'content_restriction' === $rule_type && in_array( $content_type_name, array( 'product', 'product_cat' ), true ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_content_restriction_target', sprintf( 'If you are creating a content restriction rule, it cannot target "%s". Use another post type or taxonomy instead or create a product restriction rule.', $content_type_name ) );
				} elseif ( in_array( $rule_type, array( 'product_restriction', 'purchasing_discount' ), true )  && ! in_array( $content_type_name, array( 'product', 'product_cat' ), true ) ) {
					if ( 'product_restriction' === $rule_type ) {
						throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_product_restriction', sprintf( 'If you are creating a product restriction rule, it must target a product or a product category instead of "%s".', $content_type_name ) );
					} elseif ( 'purchasing_discount' === $rule_type ) {
						throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_purchasing_discount_target', sprintf( 'If you are creating a purchasing discount rule, it must target a product or a product category instead of "%s".', $content_type_name ) );
					}
				}

				$rule->set_content_type( $content_type );
				$rule->set_content_type_name( $content_type_name );
			}

			// change or add specific IDs
			if ( ! empty( $data['object_ids'] ) ) {

				$ids     = array();
				$set_ids = is_string( $data['object_ids'] ) ? explode( ',', $data['object_ids'] ) : (array) $data['object_ids'];

				foreach ( $set_ids as $id ) {
					if ( is_numeric( $id ) ) {
						$ids[] = (int) $id;
					}
				}

				if ( ! empty( $ids ) ) {
					$rule->set_object_ids( $ids );
				} else {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_object_ids', sprintf( 'Could not parse specified object IDs: "%s".', is_array( $data['object_ids'] ) ) ? implode( ',', (array) $data['object_ids'] ) : $data['object_ids'] );
				}
			}

			// set or update a discount
			if ( ! empty( $data['discount'] ) ) {

				if ( $rule->is_type( 'purchasing_discount' ) ) {

					$discount_amount = preg_replace( '/[^0-9,.]/', '', trim( str_replace( ',', '.', $data['discount'] ) ) );

					if ( '' === $discount_amount || (float) $discount_amount > 0 ) {
						$rule->set_discount( $data['discount'] );
					} else {
						throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_discount', sprintf( 'Could not parse discount type and amount: "%s".', $data['discount'] ) );
					}

				} else {

					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_purchasing_discount_mismatch_rule_type', 'You cannot assign discount properties to a rule that is not a purchasing discount rule. Please create a new rule or ensure you are editing the right rule.' );
				}
			}

			// for now the 'active' flag is for purchasing discounts only
			if ( ! empty( $data['active'] ) ) {
				if ( $rule->is_type( 'purchasing_discount' ) ) {
					$active = trim( $data['active'] );
					if ( 'yes' === $active ) {
						$rule->set_active();
					} elseif ( 'no' === $active ) {
						$rule->set_inactive();
					} else {
						throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_invalid_active_status', sprintf( 'Purchasing discount rule active status must be either "yes" or "no", cannot parse "%s".', $data['active'] ) );
					}
				} else {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_active_status_rule_type_mismatch', 'For now you can only set an active status to purchasing discount rules.' );
				}
			}

			// update access type for product restriction rules
			if ( ! empty( $data['access_type'] ) ) {
				if ( ! $rule->is_type( 'product_restriction' ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_access_type_rule_type_mismatch', 'You cannot set access type for rules that are not of product restriction.' );
				} elseif ( ! wc_memberships()->get_rules_instance()->is_valid_rule_access_type( $data['access_type'] ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_access_type', sprintf( 'The specified access type for this rule does not seem valid: "%1$s". It should be one of: %2$s', $content_type_name, implode( ', ', wc_memberships()->get_rules_instance()->get_rules_valid_access_types() ) ) );
				} else {
					$rule->set_access_type( $data['access_type'] );
				}
			}

			// update access schedule for content and product restriction rules
			if ( ! empty( $data['access_schedule'] ) ) {

				if ( ! $rule->is_type( 'purchasing_discount' ) ) {

					if ( 'immediate' === $data['access_schedule'] ) {

						$rule->set_access_schedule( 'immediate' );

					} else {

						$amount = wc_memberships_parse_period_length( $data['access_schedule'], 'amount' );
						$period = wc_memberships_parse_period_length( $data['access_schedule'], 'period' );

						if ( $amount && $period && $amount > 0 ) {
							$rule->set_access_schedule( wc_memberships_parse_period_length( $data['access_schedule'] ) );
						} else {
							throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_rule_access_schedule', sprintf( 'The specified access schedule for this rule does not seem valid: "%s".', $data['access_schedule'] ) );
						}
					}

				} else {

					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_access_schedule_rule_type_mismatch', 'You cannot set an access schedule for purchasing discount rules, you must use content or product restriction rules.' );
				}
			}

			// optionally mark rule to exclude trial period with Subscriptions
			if ( ! empty( $data['exclude_trial'] ) ) {
				if ( ! in_array( $data['exclude_trial'], array( 'yes', 'no' ), true ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_exclude_trial_invalid_option', sprintf( 'Invalid option for exclude_trial: "%s". It must be either "yes" or "no".', $data['exclude_trial'] ) );
				} elseif ( ! wc_memberships()->get_integrations_instance()->is_subscriptions_active() ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_rule_exclude_trial_subscriptions_inactive', sprintf( 'You want to %s the free trial period for this rule, but it looks like Subscriptions is not active. Please activate WooCommerce Subscriptions to set rules specific to Subscription Products.', $data['exclude_trial'] === 'yes' ? 'exclude' : 'include' ) );
				} else {
					$rule_args['access_schedule_exclude_trial'] = $data['exclude_trial'];
				}
			}

			// finally, update the rule
			wc_memberships()->get_rules_instance()->update_rules( array( $rule ) );

			/**
			 * Upon updating a Membership Plan Rule via CLI.
			 *
			 * @since 1.9.0
			 *
			 * @param \WC_Memberships_Membership_Plan_Rule $membership_plan_rule
			 * @param array $data
			 */
			do_action( 'wc_memberships_cli_update_membership_plan_rule', $rule, $data );

			\WP_CLI::success( sprintf( 'Updated Membership Plan Rule %s.', $rule->get_id() ) );

		} catch ( \WC_CLI_Exception $e ) {

			\WP_CLI::error( $e->getMessage() );
		}
	}


	/**
	 * Get a Membership Plan Rule.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Membership Plan Rule ID to look for
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole Membership Plan Rule fields, returns the value of a single field
	 *
	 * [--fields=<fields>]
	 * : Get a specific subset of the Membership Plan Rule fields
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * For more fields, see: wp wc memberships plan rule list --help
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan rule get rule_123
	 *
	 *
	 * @since 1.9.0
	 * @deprecated since 1.13.0
	 *
	 * @param string[] $args only the first ID will be used
	 * @param array $assoc_args Formatting arguments
	 */
	public function get( $args, $assoc_args ) {

		try {

			$rule = wc_memberships()->get_rules_instance()->get_rule( isset( $args[0] ) ? $args[0] : null );

			if ( ! $rule instanceof \WC_Memberships_Membership_Plan_Rule ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_membership_plan_rule', sprintf( 'Membership Plan Rule "%s" invalid or not found.', $args[0] ) );
			}

			$rule_data = $this->get_membership_plan_rule_data( $rule );
			$formatter = $this->get_formatter( $assoc_args );
			$formatter->display_item( $rule_data );

		} catch ( \WC_CLI_Exception $e ) {

			\WP_CLI::error( $e->getMessage() );
		}
	}


	/**
	 * Get default format fields that will be used in `list` and `get` subcommands
	 *
	 * @since 1.9.0
	 * @deprecated since 1.13.0
	 *
	 * @return string
	 */
	protected function get_default_format_fields() {

		$default_fields = array(
			'id',
			'type',
			'plan_id',
			'target',
			'object_ids',
		);

		/**
		 * Memberships Plan Rule default format fields used in WP CLI.
		 *
		 * @since 1.9.0
		 *
		 * @param array $default_fields
		 */
		$default_fields = apply_filters( 'wc_memberships_cli_membership_plan_rule_default_fields', $default_fields );

		return implode( ',', $default_fields );
	}


	/**
	 * Get Membership Plan Rule data.
	 *
	 * @since 1.9.0
	 * @deprecated since 1.13.0
	 *
	 * @param \WC_Memberships_Membership_Plan_Rule $rule
	 *
	 * @return array
	 */
	protected function get_membership_plan_rule_data( $rule ) {

		$membership_plan_rule_data = array();

		if ( $rule instanceof \WC_Memberships_Membership_Plan_Rule ) {

			$membership_plan_rule_data = array(
				'id'              => $rule->get_id(),
				'type'            => $rule->get_rule_type(),
				'plan_id'         => $rule->get_membership_plan_id(),
				'target'          => $rule->get_target() ? str_replace( '|', ':', $rule->get_target() ) : '',
				'object_ids'      => $rule->has_object_ids() ? $rule->get_object_ids() : '',
				'access_type'     => $rule->is_type( 'product_restriction' ) ? $rule->get_access_type() : '',
				'access_schedule' => ! $rule->is_type( 'purchasing_discount' ) ? $rule->get_access_schedule() : '',
				'discount'        => $rule->is_type( 'purchasing_discount' ) ? ( $rule->get_discount_amount() . ( $rule->is_discount_type( 'percentage' ) ? '%' : '' ) ) : '',
				'active'          => $rule->is_type( 'purchasing_discount' ) ? ( $rule->is_active() ? 'yes' : 'no' ) : '',
				'exclude_trial'   => $rule->is_access_schedule_excluding_trial() ? 'yes' : 'no',
			);
		}

		/**
		 * Filter the membership plan rule data for Memberships CLI.
		 *
		 * @since 1.9.0
		 *
		 * @param array $membership_plan_rule_data the plan data passed to CLI.
		 * @param \WC_Memberships_Membership_Plan_Rule $rule the membership plan rule
		 */
		$membership_plan_rule_data = apply_filters( 'wc_memberships_cli_membership_plan_rule_data', $membership_plan_rule_data, $rule );

		return $this->flatten_array( (array) $membership_plan_rule_data );
	}


	/**
	 * List Membership Plan Rules.
	 *
	 * ## OPTIONS
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each Membership Plan
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific Membership Plan Rule fields
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each Membership Plan Rule:
	 *
	 * * id
	 * * type
	 * * plan_id
	 * * target
	 * * object_ids
	 *
	 * These fields are optionally available:
	 *
	 * * access_type
	 * * access_schedule
	 * * exclude_trial
	 * * discount
	 * * active
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan rule list
	 *
	 *     wp wc memberships plan rule list --field=id
	 *
	 *     wp wc memberships plan rule list --fields=id,type,target --format=json
	 *
	 *
	 * @subcommand list
	 *
	 * @since 1.9.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function list_( $args, $assoc_args ) {

		$query_args = $assoc_args;
		$formatter  = $this->get_formatter( $assoc_args );

		if ( 'ids' === $formatter->format ) {

			$rules = wc_memberships()->get_rules_instance()->get_rules( array_merge( $query_args, array( 'fields' => 'ids' ) ) );

			echo implode( ' ', $rules );

		} else {

			$rules = wc_memberships()->get_rules_instance()->get_rules( $query_args );
			$items = $this->format_rules_to_items( $rules );

			$formatter->display_items( $items );
		}
	}


	/**
	 * Format posts from WP_Query result to items.
	 *
	 * @since 1.9.0
	 * @deprecated since 1.13.0
	 *
	 * @param \WC_Memberships_Membership_Plan_Rule[] $rules array of rule objects
	 * @return array formatted rule items for screen display
	 */
	protected function format_rules_to_items( $rules ) {

		$items = array();

		foreach ( $rules as $rule ) {

			// sanity check, although it shouldn't happen
			if ( ! $rule instanceof \WC_Memberships_Membership_Plan_Rule ) {
				continue;
			}

			$items[] = $this->get_membership_plan_rule_data( $rule );
		}

		return $items;
	}


	/**
	 * Delete Membership Plan Rule(s).
	 *
	 * ## OPTIONS
	 *
	 * <id>...
	 * : The ID of the rule to delete
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan rule delete rule_123
	 *
	 *     wp wc memberships plan rule delete $(wp wc memberships plan rule list --format=ids)
	 *
	 *
	 * @since 1.9.0
	 * @deprecated since 1.13.0
	 *
	 * @param int|int[] $args
	 * @param array $assoc_args
	 */
	public function delete( $args, $assoc_args ) {

		$exit_code = 0;
		$args      = ! is_array( $args ) ? (array) $args : $args;

		foreach ( $args as $membership_plan_rule_id ) {

			$membership_plan_rule = wc_memberships()->get_rules_instance()->get_rule( $membership_plan_rule_id );

			if ( ! $membership_plan_rule instanceof \WC_Memberships_Membership_Plan_Rule ) {
				\WP_CLI::warning( "Failed deleting Membership Plan Rule $membership_plan_rule_id: not a Membership Plan Rule." );
				continue;
			}

			/**
			 * Upon deleting a Membership Plan Rule via CLI.
			 *
			 * @since 1.9.0
			 *
			 * @param int $membership_plan_rule_id
			 */
			do_action( 'wc_memberships_cli_delete_membership_plan_rule', $membership_plan_rule_id );

			wc_memberships()->get_rules_instance()->delete_rules( array( $membership_plan_rule ) );

			if ( null === wc_memberships()->get_rules_instance()->get_rule( $membership_plan_rule_id ) ) {
				\WP_CLI::success( "Deleted Membership Plan Rule $membership_plan_rule_id." );
			} else {
				$exit_code++;
				\WP_CLI::warning( "Failed deleting Membership Plan Rule $membership_plan_rule_id." );
			}
		}

		exit( $exit_code ? 1 : 0 );
	}


}
