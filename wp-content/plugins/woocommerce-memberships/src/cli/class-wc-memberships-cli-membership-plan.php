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

use SkyVerge\WooCommerce\Memberships\Helpers\Strings_Helper;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

/**
 * Manage Membership Plans from WP CLI.
 *
 * @since 1.7.0
 * @deprecated since 1.13.0
 */
class WC_Memberships_CLI_Membership_Plan extends \WC_Memberships_CLI_Command {


	/**
	 * Create a Membership Plan.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : Associative args for the new Membership Plan
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * Required fields:
	 *
	 * * name
	 *
	 * These fields are optionally available for create command:
	 *
	 * * product
	 * * status
	 * * slug
	 * * access
	 * * length
	 * * start_date
	 * * end_date
	 * * members_area_sections
	 * * rules
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan create --name="Golden Plan"
	 *
	 *     wp wc memberships plan create --name="Silver Plan" --slug="silver-membership" --length="1 month" --product=123,456,6780
	 *
	 *     wp wc memberships plan create --name="Bronze Plan" --start_date="2017-10-1" --end_date="2018-08-25"
	 *
	 *     wp wc memberships plan create --name="Free Plan" --access="free"
	 *
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function create( $args, $assoc_args ) {

		try {

			/**
			 * Filter arguments when creating a Membership Plan via CLI
			 *
			 * @since 1.7.0
			 * @param array $args
			 */
			$data = apply_filters( 'woocommerce_memberships_cli_create_membership_plan_data', $this->unflatten_array( $assoc_args ) );

			if ( ! isset( $data['name'] ) ) {

				throw new \WC_CLI_Exception( 'woocommerce_memberships_missing_plan_name', sprintf( 'Missing parameter "%s".', 'name' ) );

			} else {

				$name = sanitize_text_field(  $data['name'] );

				if ( '' === $name ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_name', sprintf( 'Invalid Membership Plan name "%s".', $data['name'] ) );
				}
			}

			$post_args = array(
				'post_type'   => 'wc_membership_plan',
				'post_title'  => $name,
			);

			if ( isset( $data['status'] ) ) {

				if ( ! in_array( trim( $data['status'] ), array( 'draft', 'publish' ), true ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_status', sprintf( 'Invalid Membership Plan status "%s".', $data['status'] ) );
				} else {
					$post_args['post_status'] = trim( $data['status'] );
				}

			} else {

				$post_args['post_status'] = 'publish';
			}

			$slug = '';

			if ( ! empty( $data['slug'] ) ) {

				$slug = sanitize_title( $data['slug'] );

				if ( '' === $slug ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_slug', sprintf( 'Slug "%s" is not valid.', $data['slug'] ) );
				}
			}

			$access_method  = 'manual-only';
			$access_methods = wc_memberships()->get_plans_instance()->get_membership_plans_access_methods();

			if ( isset( $data['access'] ) ) {

				if ( 'free' === $data['access'] ) {
					$access_method = 'signup';
				} elseif ( in_array( $data['access'], $access_methods, true ) ) {
					$access_method = $data['access'];
				} else {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_access_method', sprintf( 'Membership Plan type "%s" is not a recognized type.', $data['access'] ) );
				}

				// conflict between access method and defined products
				if ( 'purchase' !== $access_method && ! empty( $data['product'] ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_access_method_conflict', sprintf( 'If you define products that grant access, the Membership access method can only be "purchase" and cannot be "%s". You can still assign the membership manually.', $data['access'] ) );
				}
			}

			$start_date = '';
			$end_date   = '';

			if ( isset( $data['start_date'] ) || isset( $data['end_date'] ) ) {

				$start_date = isset( $data['start_date'] ) ? $this->parse_membership_date( $data['start_date'] ) : date( 'Y-m-d H:i:s', strtotime( 'today',    current_time( 'timestamp', true ) ) );
				$end_date   = isset( $data['end_date'] )   ? $this->parse_membership_date( $data['end_date']   ) : date( 'Y-m-d H:i:s', strtotime( 'tomorrow', strtotime( $start_date ) ) );

				if ( ! $start_date || strlen( $data['start_date'] ) !== 10 ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_start_date', sprintf( 'Membership Plan start date "%s" is not valid. Must be a non-empty YYYY-MM-DD value.', $data['start_date'] ) );
				} elseif( ! $end_date || strlen( $data['end_date'] ) !== 10 ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_end_date', sprintf( 'Membership Plan end date "%s" is not valid. Must be a non-empty YYYY-MM-DD value.', $data['end_date'] ) );
				} elseif ( strtotime( $start_date ) > strtotime( $end_date ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_fixed_dates_conflict', sprintf( 'Membership Plan start date %1$s cannot be set after end date in %2$s', $start_date, $end_date ) );
				} elseif ( isset( $data['length'] ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_plan_length_conflict', 'You cannot define a plan length and fixed start or end dates at the same time.' );
				}
			}

			$length_amount = 0;
			$length_period = false;

			if ( isset( $data['length'] ) && 'unlimited' !== $data['length'] ) {

				$length = sanitize_text_field( $data['length'] );

				if ( '' === $length ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_plan_length', sprintf( 'Membership Plan length "%s" is not valid. Must be "unlimited" or in "<amount> <period>" format.', $data['length'] ) );
				}

				// maybe add a final 's' if it's omitted
				$length = ! Framework\SV_WC_Helper::str_ends_with( $length, 's' ) ? $length . 's' : $length;

				$length_amount = wc_memberships_parse_period_length( $length, 'amount' );
				$length_period = wc_memberships_parse_period_length( $length, 'period' );

				if ( ! is_int( $length_amount ) || $length_amount < 1 || empty( $length_period ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_plan_length', sprintf( 'Membership Plan length "%s" is not valid. Must be "unlimited" or in "<amount> <period>" format.', $data['length'] ) );
				}
			}

			$product_ids = array();

			if ( ! empty( $data['product'] ) ) {

				$product_ids = array_map( 'absint', explode( ',', $data['product'] ) );

				if ( ! empty( $product_ids ) && is_array(  $product_ids ) ) {

					$error_ids = array();

					foreach ( $product_ids as $product_id ) {

						$product = wc_get_product( (int) $product_id );

						if ( ! $product instanceof \WC_Product ) {

							\WP_CLI::warning( "Product $product_id is not a valid product." );

							$error_ids[] = $product_id;
						}
					}

					$errors = count( $error_ids );

					if ( $errors > 0 ) {

						if ( 1 === $errors ) {
							$message = sprintf( 'Product %s is not a valid product.', $error_ids[0] );
						} else {
							$message = sprintf( 'Products %s are not valid products.', Strings_Helper::get_human_readable_items_list( $error_ids, 'and' ) );
						}

						throw new \WC_CLI_Exception( 'woocommerce_memberships_products_not_found', $message );
					}
				}

				$access_method = 'purchase';
			}

			$post_id = wp_insert_post( $post_args, true );

			if ( 0 === $post_id || is_wp_error( $post_id ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_cannot_create_membership_plan', $post_id->get_error_message() );
			}

			$membership_plan = wc_memberships_get_membership_plan( $post_id );

			if ( ! $membership_plan instanceof \WC_Memberships_Membership_Plan ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_cannot_create_membership_plan', "Could not create a valid Membership Plan with post ID $post_id." );
			}

			$membership_plan->set_access_method( $access_method );

			if ( ! empty( $product_ids ) ) {
				$membership_plan->set_product_ids( $product_ids );
			}

			if ( ! empty( $start_date ) && ! empty( $end_date ) ) {

				$time_start = strtotime( 'today', strtotime( $start_date ) );
				$time_end   = strtotime( 'today', strtotime( $end_date ) );
				$timezone   = wc_timezone_string();

				$membership_plan->set_access_start_date( date( 'Y-m-d H:i:s', wc_memberships_adjust_date_by_timezone( $time_start, 'timestamp', $timezone ) ) );
				$membership_plan->set_access_end_date(   date( 'Y-m-d H:i:s', wc_memberships_adjust_date_by_timezone( $time_end,   'timestamp', $timezone ) ) );
			}

			if ( $length_amount > 0 && false !== $length_period ) {
				$membership_plan->set_access_length( $length_amount . ' ' . $length_period );
			}

			if ( false !== $slug ) {

				$post    = get_post( $post_id );
				$updated = wp_update_post( array(
					'ID'        => $post_id,
					'post_type' => 'wc_membership_plan',
					'post_name' => wp_unique_post_slug( $slug, $post_id, $post->post_status, $post->post_type, $post->post_parent ),
				), true );

				if ( 0 === $updated || is_wp_error( $updated ) ) {
					\WP_CLI::warning( 'Could not set the slug "%1$s" for Membership Plan %2$s, auto-generated "%3$s" has been used instead.', $slug, $post_id, $post->post_name );
				}
			}

			if ( empty( $data['members_area_sections'] ) ) {
				$membership_plan->set_members_area_sections();
			} else {
				$membership_plan->set_members_area_sections( array_map( 'trim', explode( ',', $data['members_area_sections'] ) ) );
			}

			if ( ! empty( $data['rules'] ) ) {

				$rules = json_decode( $data['rules'], true );

				$this->set_membership_plan_rules( $membership_plan, $rules );
			}

			/**
			 * Upon creating a Membership Plan via CLI.
			 *
			 * @since 1.7.0
			 *
			 * @param \WC_Memberships_Membership_Plan $membership_plan
			 * @param array $data
			 */
			do_action( 'wc_memberships_cli_create_membership_plan', $membership_plan, $data );

			\WP_CLI::success( sprintf( 'Created Membership Plan %s.', $membership_plan->get_id() ) );

		} catch ( \WC_CLI_Exception $e ) {

			\WP_CLI::error( $e->getMessage() );
		}
	}


	/**
	 * Update one or more Membership Plans.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Membership Plan ID or name
	 *
	 * [--<field>=<value>]
	 * : One or more fields to update
	 *
	 * ## AVAILABLE_FIELDS
	 *
	 * For more fields, see: wp wc memberships plan create --help
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan update 123 --name="Another name"
	 *
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function update( $args, $assoc_args ) {

		try {

			// plan ID or name
			$id = is_numeric( $args[0] ) ? (int) $args[0] : $args[0];

			/**
			 * Filter arguments when updating a Membership Plan via CLI
			 *
			 * @since 1.7.0
			 * @param array $args
			 * @param int $id
			 */
			$data = apply_filters( 'woocommerce_memberships_cli_update_membership_plan_data', $this->unflatten_array( $assoc_args ), $id );

			$membership_plan = wc_memberships_get_membership_plan( $id );

			if ( ! $membership_plan instanceof \WC_Memberships_Membership_Plan ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_membership_plan_not_found', sprintf( 'Membership Plan %s not found.', $id ) );
			}

			$post_args = array(
				'ID'         => $membership_plan->get_id(),
				'post_type'  => 'wc_membership_plan',
			);

			if ( ! empty( $data['name'] ) ) {

				$name = sanitize_text_field( $data['name'] );

				if ( '' === $name ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_name', sprintf( 'Invalid Membership Plan name "%s".', $data['name'] ) );
				} else {
					$post_args['post_title'] = $name;
				}
			}

			if ( ! empty( $data['slug'] ) ) {

				$post = get_post( $membership_plan->get_id() );

				if ( $slug = sanitize_title( $data['slug'] ) ) {
					$slug = wp_unique_post_slug( $slug, $post->ID, $post->post_status, $post->post_type, $post->post_parent );
				}

				if ( '' === $slug ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_slug', sprintf( 'Membership Plan slug "%s" is not valid.', $data['slug'] ) );
				} else {
					$post_args['post_name'] = $slug;
				}
			}

			if ( ! empty( $data['status'] ) ) {

				if ( ! in_array( trim( $data['status'] ), array( 'draft', 'pending', 'publish' ), true ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_plan_status', sprintf( 'Invalid Membership Plan status "%s".', $data['status'] ) );
				} else {
					$post_args['post_status'] = trim( $data['status'] );
				}
			}

			$current_access_method = $membership_plan->get_access_method();
			$access_method         = empty( $current_access_method ) ? 'manual-only' : $current_access_method;
			$access_methods        = wc_memberships()->get_plans_instance()->get_membership_plans_access_methods();

			// update if there's a change in access method
			if ( isset( $data['access'] ) && $data['access'] !== $access_method ) {

				if ( 'free' === $data['access'] ) {
					$access_method = 'signup';
				} elseif ( in_array( $data['access'], $access_methods, true ) ) {
					$access_method = $data['access'];
				} else {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_access_method', sprintf( 'Membership Plan type "%s" is not a recognized type.', $data['access'] ) );
				}

				// conflict between access method and defined products
				if ( 'purchase' !== $access_method && ! empty( $data['product'] ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_access_method_conflict', sprintf( 'If you define products that grant access, the Membership type can only be "purchase" and cannot be "%s". You can still assign the membership manually.', $data['access'] ) );
				}
			}

			$access_start_date = '';
			$access_end_date   = '';

			if ( isset( $data['start_date'] ) || isset( $data['end_date'] ) ) {

				if ( isset( $data['start_date'] ) ) {
					$access_start_date = $this->parse_membership_date( $data['start_date'] );
				} else {
					$access_start_date = $membership_plan->get_access_start_date();
				}

				if ( isset( $data['end_date'] ) ) {
					$access_end_date = $this->parse_membership_date( $data['end_date'] );
				} else {
					$access_end_date = $membership_plan->get_access_end_date();
				}

				if ( ! $access_start_date || ( isset( $data['start_date'] ) && strlen( $data['start_date'] ) !== 10 ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_start_date', sprintf( 'Membership Plan start date "%s" is not valid. Must be a non-empty YYYY-MM-DD value.', $data['start_date'] ) );
				} elseif( ! $access_end_date || ( isset( $data['end_date'] ) && strlen( $data['end_date'] ) !== 10 ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_end_date', sprintf( 'Membership Plan end date "%s" is not valid. Must be a non-empty YYYY-MM-DD value.', $data['end_date'] ) );
				} elseif ( strtotime( $access_start_date ) > strtotime( $access_end_date ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_fixed_dates_conflict', sprintf( 'Membership Plan start date %1$s cannot be set after end date in %2$s', $access_start_date, $access_end_date ) );
				} elseif ( isset( $data['length'] ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_plan_length_conflict', 'You cannot define a plan length and fixed start or end dates at the same time' );
				}
			}

			$access_length = false;

			if ( ! empty( $data['length'] ) ) {

				if ( 'unlimited' === trim( $data['length'] ) ) {

					$access_length = 'unlimited';

				} else {

					$length = sanitize_text_field( $data['length'] );

					if ( '' === $length ) {
						throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_plan_length', sprintf( 'Membership Plan length "%s" is not valid. Must be "unlimited" or in "<amount> <period>" format.', $data['length'] ) );
					}

					// maybe add a final 's' if it's omitted
					$length = ! Framework\SV_WC_Helper::str_ends_with( $length, 's' ) ? $length . 's' : $length;

					$length_amount = wc_memberships_parse_period_length( $length, 'amount' );
					$length_period = wc_memberships_parse_period_length( $length, 'period' );

					if ( ! is_int( $length_amount ) || $length_amount < 1 || empty( $length_period ) ) {
						throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_plan_length', sprintf( 'Membership Plan length "%s" is not valid. Must be "unlimited" or in "<amount> <period>" format.', $data['length'] ) );
					} else {
						$access_length = $length_amount . ' ' . $length_period;
					}
				}
			}

			$product_ids = array();

			if ( ! empty( $data['product'] ) ) {

				if ( 'none' === $data['product'] ) {

					$product_ids = 'none';

				} else {

					$product_ids = array_map( 'absint', explode( ',', $data['product'] ) );

					if ( ! empty( $product_ids ) && is_array( $product_ids ) ) {

						$error_ids = array();

						foreach ( $product_ids as $product_id ) {

							$product = wc_get_product( (int) $product_id );

							if ( ! $product instanceof \WC_Product ) {

								\WP_CLI::warning( "Product $product_id is not a valid product." );

								$error_ids[] = $product_id;
							}
						}

						$errors = count( $error_ids );

						if ( $errors > 0 ) {

							if ( 1 === $errors ) {
								$message = sprintf( 'Product %s is not a valid product.', $error_ids[0] );
							} else {
								$message = sprintf( 'Products %s are not valid products.', Strings_Helper::get_human_readable_items_list( $error_ids, 'and' ) );
							}

							throw new \WC_CLI_Exception( 'woocommerce_memberships_products_not_found', $message );
						}
					}
				}

				$access_method = 'purchase';
			}

			if ( ! empty( $post_args ) ) {

				$updated = wp_update_post( $post_args, true );

				if ( 0 === $updated || is_wp_error( $updated ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_cannot_update_membership_plan', $updated->get_error_message() );
				}
			}

			$membership_plan->set_access_method( $access_method );

			if ( 'purchase' !== $access_method ) {
				$membership_plan->delete_product_ids();
			}

			if ( ! empty( $access_start_date ) && ! empty( $access_end_date ) ) {

				$time_start = strtotime( 'today', strtotime( $access_start_date ) );
				$time_end   = strtotime( 'today', strtotime( $access_end_date ) );
				$timezone   = wc_timezone_string();

				$membership_plan->set_access_start_date( date( 'Y-m-d H:i:s', wc_memberships_adjust_date_by_timezone( $time_start, 'timestamp', $timezone ) ) );
				$membership_plan->set_access_end_date(   date( 'Y-m-d H:i:s', wc_memberships_adjust_date_by_timezone( $time_end,   'timestamp', $timezone ) ) );
				$membership_plan->delete_access_length();
			}

			if ( false !== $access_length ) {

				if ( 'unlimited' === $access_length ) {
					$membership_plan->delete_access_length();
				} else {
					$membership_plan->set_access_length( $access_length );
				}

				$membership_plan->delete_access_start_date();
				$membership_plan->delete_access_end_date();
			}

			if ( ! empty( $product_ids ) ) {

				if ( 'none' === $product_ids ) {
					$membership_plan->delete_product_ids();
				} elseif( is_array( $product_ids ) ) {
					$membership_plan->set_product_ids( $product_ids );
				}
			}

			if ( isset( $data['members_area_sections'] ) ) {

				if ( '' === trim( $data['members_area_sections'] ) ) {
					$membership_plan->set_members_area_sections( array() );
				} else {
					$membership_plan->set_members_area_sections( array_map( 'trim', explode( ',', $data['members_area_sections'] ) ) );
				}
			}

			if ( ! empty( $data['rules'] ) ) {

				$rules = json_decode( $data['rules'], true );

				$this->set_membership_plan_rules( $membership_plan, $rules );
			}

			/**
			 * Upon updating a Membership Plan via CLI.
			 *
			 * @since 1.7.0
			 *
			 * @param \WC_Memberships_Membership_Plan $membership_plan
			 * @param array $data
			 */
			do_action( 'wc_memberships_cli_update_membership_plan', $membership_plan, $data );

			\WP_CLI::success( sprintf( 'Updated Membership Plan %s.', $membership_plan->get_id() ) );

		} catch ( \WC_CLI_Exception $e ) {

			\WP_CLI::error( $e->getMessage() );
		}
	}


	/**
	 * Sets rules for a given Membership Plan.
	 *
	 * @since 1.12.3
	 * @deprecated since 1.13.0
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan plan to set rules for
	 * @param array $rules associative array of rules data
	 * @return \WC_Memberships_Membership_Plan_Rule[] set rules
	 */
	protected function set_membership_plan_rules( \WC_Memberships_Membership_Plan $membership_plan, array $rules ) {

		$set_rules = array();

		foreach ( $rules as $rule_type => $rule_data ) {

			if ( ! is_array( $rule_data ) || ! in_array( $rule_type, array( 'content_restriction', 'product_restriction', 'purchasing_discount' ), true ) ) {
				continue;
			}

			$rule = new \WC_Memberships_Membership_Plan_Rule();

			$rule->set_id();
			$rule->set_membership_plan_id( $membership_plan->get_id() );
			$rule->set_rule_type( $rule_type );

			if ( 'purchasing_discount' === $rule_type ) {

				$rule->set_inactive();

				if ( isset( $rule_data['discount'] ) ) {

					$rule->set_discount( $rule_data['discount'] );

					if ( isset( $rule_data['active'] ) && in_array( $rule_data['active'], array( 'yes', true ), true ) ) {
						$rule->set_active();
					}
				}
			}

			if ( isset( $rule_data['target'] ) ) {

				$rule->set_target( $rule_data['target'] );

			} elseif ( isset( $rule_data['content_type'], $rule_data['content_type_name'] ) ) {

				$rule->set_content_type( $rule_data['content_type'] );
				$rule->set_content_type_name( $rule_data['content_type_name'] );

			} else {

				continue;
			}

			if ( ! empty( $rule_data['object_ids'] ) ) {

				$ids = is_string( $rule_data['object_ids'] ) ? explode( ',', $rule_data['object_ids'] ) : (array) $rule_data['object_ids'];

				$rule->set_object_ids( $ids );
			}

			if ( ! empty( $rule_data['access_type'] ) ) {
				$rule->set_access_type( $rule_data['access_type'] );
			}

			if ( ! empty( $rule_data['access_schedule'] ) ) {
				$rule->set_access_schedule( $rule_data['access_schedule'] );
			}

			$access_exclude_trial = ! empty( $rule_data['access_exclude_trial'] ) && in_array( $rule_data['access_exclude_trial'], array( 'yes', true ), true );
			$access_include_trial = ! empty( $rule_data['access_include_trial'] ) && in_array( $rule_data['access_include_trial'], array( 'yes', true ), true );

			if ( $access_exclude_trial && ! $access_include_trial ) {
				$rule->set_access_schedule_exclude_trial();
			} elseif ( $access_include_trial && ! $access_exclude_trial ) {
				$rule->set_access_schedule_include_trial();
			}

			$set_rules[] = $rule;
		}

		if ( ! empty( $set_rules ) ) {
			$membership_plan->set_rules( $set_rules );
		} else {
			\WP_CLI::warning( 'Could not parse rules to set for Membership Plan %1$s.', $membership_plan->get_id() );
		}

		return $set_rules;
	}


	/**
	 * Get a Membership Plan.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Membership Plan ID or plan name to look for
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole Membership Plan fields, returns the value of a single fields
	 *
	 * [--fields=<fields>]
	 * : Get a specific subset of the Membership Plan's fields
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * For more fields, see: wp wc memberships plan list --help
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan get 123
	 *
	 *     wp wc memberships plan get "Golden Membership"
	 *
	 *     wp wc memberships plan get 123 --fields=id
	 *
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param int[] $args only the first id will be used
	 * @param array $assoc_args formatting arguments
	 */
	public function get( $args, $assoc_args ) {

		\WP_CLI::warning( $this->get_deprecation_warning( 'wp wc membership_plan get' ) );

		try {

			$membership_plan = wc_memberships_get_membership_plan( is_numeric( $args[0] ) ? (int) $args[0] : $args[0] );

			if ( ! $membership_plan instanceof \WC_Memberships_Membership_Plan ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_membership_plan', sprintf( 'Membership Plan "%s" invalid or not found.', $args[0] ) );
			}

			$membership_plan_data = $this->get_membership_plan_data( $membership_plan );

			$formatter = $this->get_formatter( $assoc_args );
			$formatter->display_item( $membership_plan_data );

		} catch ( \WC_CLI_Exception $e ) {

			\WP_CLI::error( $e->getMessage() );
		}
	}


	/**
	 * Get default format fields that will be used in `list` and `get` subcommands.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @return string
	 */
	protected function get_default_format_fields() {

		$default_fields = array(
			'id',
			'name',
			'slug',
			'type',
			'length',
			'product_ids',
		);

		$default_fields[] = 'members_count';

		/**
		 * Memberships Plan default format fields used in WP CLI.
		 *
		 * @since 1.7.0
		 *
		 * @param array $default_fields
		 */
		$default_fields = apply_filters( 'wc_memberships_cli_membership_plan_default_fields', $default_fields );

		return implode( ',', $default_fields );
	}


	/**
	 * Get Membership Plan data.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan
	 * @return array
	 */
	protected function get_membership_plan_data( $membership_plan ) {

		$membership_plan_data = '';

		if ( $membership_plan instanceof \WC_Memberships_Membership_Plan ) {

			$product_ids   = $membership_plan->get_product_ids();
			$access_length = $membership_plan->get_human_access_length();
			$access_type   = $membership_plan->get_access_method();

			$membership_plan_data = array(
				'id'               => $membership_plan->get_id(),
				'name'             => $membership_plan->get_name(),
				'slug'             => $membership_plan->get_slug(),
				'type'             => ucwords( $access_type ),
				'start_date'       => $membership_plan->get_local_access_start_date(),
				'end_date'         => $membership_plan->get_local_access_end_date(),
				'length'           => empty( $access_length ) ? 'Unlimited' : $access_length,
				'product_ids'      => $product_ids,
				'members_count'    => $membership_plan->get_memberships_count(),
			);

			$post = get_post( $membership_plan->get_id() );
			$membership_plan_data['status'] = $post->post_status;
		}

		/**
		 * Filter the membership plan data for Memberships CLI.
		 *
		 * @since 1.7.0
		 *
		 * @param array $membership_plan_data the plan data passed to CLI
		 * @param \WC_Memberships_Membership_Plan $membership_plan the membership plan
		 */
		$membership_plan_data = apply_filters( 'wc_memberships_cli_membership_plan_data', $membership_plan_data, $membership_plan );

		return $this->flatten_array( (array) $membership_plan_data );
	}


	/**
	 * List Membership Plans.
	 *
	 * ## OPTIONS
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each Membership Plan
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific Membership Plan fields
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each Membership Plan:
	 *
	 * * id
	 * * name
	 * * slug
	 * * length
	 * * product_ids
	 * * members_count
	 *
	 * These fields are optionally available:
	 *
	 * * status
	 * * start_date
	 * * end_date
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan list
	 *
	 *     wp wc memberships plan list --field=id
	 *
	 *     wp wc memberships plan list --fields=id,name,slug --format=json
	 *
	 *
	 * @subcommand list
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function list_( $args, $assoc_args ) {

		\WP_CLI::warning( $this->get_deprecation_warning( 'wp wc membership_plan list' ) );

		$query_args = $this->get_list_query_args( $assoc_args );
		$formatter  = $this->get_formatter( $assoc_args );

		if ( 'ids' === $formatter->format ) {

			$query_args['fields'] = 'ids';
			$query = new WP_Query( $query_args );
			echo implode( ' ', $query->posts );

		} else {

			$query = new WP_Query( $query_args );
			$items = $this->format_posts_to_items( $query->posts );
			$formatter->display_items( $items );

		}
	}


	/**
	 * Get query args for list subcommand.
	 *
	 * @see WC_Memberships_CLI_Membership_Plan::list__()
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args arguments from command line
	 * @return array
	 */
	protected function get_list_query_args( $args ) {

		$query_args = array(
			'post_type'      => 'wc_membership_plan',
			'posts_per_page' => -1,
			'post_status'    => 'any',
		);

		return $query_args;
	}


	/**
	 * Format posts from WP_Query result to items.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param \WP_Post[] $posts array of post objects
	 * @return array items
	 */
	protected function format_posts_to_items( $posts ) {

		$items = array();

		foreach ( $posts as $post ) {

			$membership_plan = wc_memberships_get_membership_plan( $post->ID );

			if ( ! $membership_plan instanceof \WC_Memberships_Membership_Plan ) {
				continue;
			}

			$items[] = $this->get_membership_plan_data( $membership_plan );
		}

		return $items;
	}


	/**
	 * Delete Membership Plans.
	 *
	 * ## OPTIONS
	 *
	 * <id>...
	 * : The ID or the plan name of the Membership Plan to delete
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships plan delete 123
	 *
	 *     wp wc memberships plan delete "Golden Membership"
	 *
	 *     wp wc memberships plan delete $(wp wc memberships plan list --format=ids)
	 *
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param int|int[] $args
	 * @param array $assoc_args
	 */
	public function delete( $args, $assoc_args ) {

		$exit_code = 0;
		$args      = ! is_array( $args ) ? (array) $args : $args;

		foreach ( $args as $membership_plan_id ) {

			$membership_plan = wc_memberships_get_membership_plan( is_numeric( $membership_plan_id ) ? (int) $membership_plan_id : $membership_plan_id );

			if ( ! $membership_plan instanceof \WC_Memberships_Membership_Plan ) {
				\WP_CLI::warning( "Failed deleting Membership Plan $membership_plan_id: not a Membership Plan." );
				continue;
			} elseif ( $membership_plan->get_memberships_count() > 0 ) {
				\WP_CLI::warning( "Failed deleting Membership Plan $membership_plan_id: cannot delete plan with members - delete members first." );
				continue;
			}

			/**
			 * Upon deleting a Membership Plan via CLI.
			 *
			 * @since 1.7.0
			 *
			 * @param int $membership_plan_id
			 */
			do_action( 'wc_memberships_cli_delete_membership_plan', $membership_plan_id );

			$success = wp_delete_post( $membership_plan_id, true );

			if ( $success ) {
				\WP_CLI::success( "Deleted Membership Plan $membership_plan_id." );
			} else {
				$exit_code++;
				\WP_CLI::warning( "Failed deleting Membership Plan $membership_plan_id." );
			}
		}

		exit( $exit_code ? 1 : 0 );
	}


}
