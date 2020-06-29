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
 * Manage User Memberships from WP CLI.
 *
 * @since 1.7.0
 * @deprecated since 1.13.0
 */
class WC_Memberships_CLI_User_Membership extends \WC_Memberships_CLI_Command {


	/**
	 * Create a User Membership.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : Associative args for the new User Membership
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * Required fields:
	 *
	 * * customer
	 * * plan
	 *
	 * These fields are optionally available for create command:
	 *
	 * * order
	 * * product
	 * * subscription
	 * * status
	 * * start_date
	 * * end_date
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships membership create --customer=123 --plan=456
	 *
	 *     wp wc memberships membership create --customer=user@example.com --plan=123 --status=paused --start_date="2016-05-12 21:00:00 UTC"
	 *
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function create( $args, $assoc_args ) {

		\WP_CLI::warning( $this->get_deprecation_warning( 'wp wc user_membership create' ) );

		try {

			/**
			 * Filter WP CLI data used to create a new user membership.
			 *
			 * @since 1.7.0
			 *
			 * @param array $data associative array
			 */
			$data = apply_filters( 'woocommerce_memberships_cli_create_user_membership_data', $this->unflatten_array( $assoc_args ) );

			if ( ! isset( $data['customer'] ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_missing_user', sprintf( 'Missing parameter "%s".', 'customer' ) );
			}

			$member = $this->get_member( $data['customer'] );

			if ( ! $member instanceof \WP_User ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_user_not_found', sprintf( 'User "%s" not found.', $data['user'] ) );
			} elseif ( user_can( $member, 'manage_woocommerce' ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_user_is_admin', sprintf( 'User "%s" is a Memberships administrator. Administrators and Shop Managers cannot have user memberships assigned to them.', $member->ID ) );
			}

			if ( ! isset( $data['plan'] ) ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_missing_plan', sprintf( 'Missing parameter "%s".', 'plan' ) );
			}

			$membership_plan = wc_memberships_get_membership_plan( $data['plan'] );

			if ( ! $membership_plan instanceof \WC_Memberships_Membership_Plan ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_plan_not_found', sprintf( 'Membership Plan %s not found.', $data['plan'] ) );
			}

			$product_id = 0;

			if ( isset( $data['product'] ) ) {

				$product = wc_get_product( (int) $data['product'] );

				if ( ! $product instanceof \WC_Product ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_product_not_found', sprintf( 'Product that grants access "%s" not found.', $data['product'] ) );
				}

				$product_id = (int) $product->get_id();
			}

			$order_id = 0;

			if ( isset( $data['order'] ) ) {

				$order = wc_get_order( $data['order'] );

				if ( ! $order instanceof \WC_Order ) {
					throw new Framework\WC_CLI_Exception( 'woocommerce_memberships_order_not_found', sprintf( 'Order "%s" not found.', $data['order'] ) );
				}

				$order_id = (int) $order->get_id();
			}

			$start_date = false;

			if ( isset( $data['start_date'] ) ) {

				$start_date = $this->parse_membership_date( $data['start_date'] );

				if ( false === $start_date || ( strlen( $data['start_date'] ) < 10 ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_date', 'The start date is not a valid date. Must be a non-empty YYYY-MM-DD value.' );
				}
			}

			$end_date = false;

			if ( isset( $data['end_date'] ) ) {

				$end_date = $this->parse_membership_date( $data['end_date'] );

				if ( false === $start_date || ( strlen( $data['end_date'] ) < 10 ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_date', 'The end date is not a valid date. Must be a non-empty YYYY-MM-DD value.' );
				}
			}

			$status = false;

			if ( isset( $data['status'] ) ) {

				if ( $this->is_valid_membership_status( $data['status'] ) ) {
					$status = $data['status'];
				} else {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_status', sprintf( 'The status "%1$s" is not a valid User Membership status. Please use one of the following: %2$s', $data['status'], wc_memberships_list_items( $this->get_membership_status_keys(), 'or' ) ) );
				}
			}

			try {
				$user_membership = wc_memberships_create_user_membership( array(
					'plan_id'    => (int) $membership_plan->get_id(),
					'user_id'    => (int) $member->ID,
					'product_id' => $product_id,
					'order_id'   => $order_id,
				), 'create' );
			} catch ( Framework\SV_WC_Plugin_Exception $e ) {
				throw new Framework\WC_CLI_Exception( 'woocommerce_memberships_cli_cannot_create_user_membership', $e->getMessage() );
			}

			if ( false !== $start_date ) {
				$user_membership->set_start_date( $start_date );
			}

			if ( false !== $status ) {
				$user_membership->update_status( $status );
			}

			if ( false !== $end_date ) {
				$user_membership->set_end_date( $end_date );
			}

			/**
			 * Upon creating a User Membership via CLI.
			 *
			 * @since 1.7.0
			 *
			 * @param \WC_Memberships_User_Membership $user_membership
			 * @param array $data
			 */
			do_action( 'wc_memberships_cli_create_user_membership', $user_membership, $data );

			\WP_CLI::success( sprintf( 'Created User Membership %s.', $user_membership->get_id() ) );

		} catch ( \WC_CLI_Exception $e ) {

			\WP_CLI::error( $e->getMessage() );
		}
	}


	/**
	 * Update one or more User Memberships.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : User Membership ID
	 *
	 * [--<field>=<value>]
	 * : One or more fields to update
	 *
	 * ## AVAILABLE_FIELDS
	 *
	 * For more fields, see: wp wc memberships membership create --help
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships membership update 123 --status=cancelled
	 *
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function update( $args, $assoc_args ) {

		\WP_CLI::warning( $this->get_deprecation_warning( 'wp wc user_membership update' ) );

		try {

			$id   = $args[0];

			/**
			 * Filter WP CLI data used to update a new user membership.
			 *
			 * @since 1.7.0
			 *
			 * @param array $data associative array
			 */
			$data = apply_filters( 'woocommerce_memberships_cli_update_user_membership_data', $this->unflatten_array( $assoc_args ) );

			$user_membership = wc_memberships_get_user_membership( $id );

			if ( ! $user_membership instanceof \WC_Memberships_User_Membership ) {
				throw new \WC_CLI_Exception( 'woocommerce_memberships_user_membership_not_found', sprintf( 'User Membership %s not found.', $id ) );
			}

			if ( isset( $data['customer'] ) ) {

				$user = $this->get_member( $data['customer'] );

				if ( ! $user instanceof \WP_User ) {

					throw new \WC_CLI_Exception( 'woocommerce_memberships_user_not_found', sprintf( 'User "%s" not found.', $data['user'] ) );

				} elseif ( (int) $user->ID !== $user_membership->get_id() ) {

					if ( user_can( $user, 'manage_woocommerce' ) ) {
						throw new \WC_CLI_Exception( 'woocommerce_memberships_user_is_admin', sprintf( 'User "%s" is a Memberships administrator. Administrators and Shop Managers cannot have user memberships assigned to them.', $user->ID ) );
					} elseif ( wc_memberships_is_user_member( $user->ID, $user_membership->get_plan() ) ) {
						throw new \WC_CLI_Exception( 'woocommerce_memberships_user_already_member', sprintf( 'User "%1$s" is already a member of the plan "%2$s (%3%s)".', $user->ID, $user_membership->get_plan()->get_name(), $user_membership->get_plan_id() ) );
					} else {
						try {
							$user_membership->transfer_ownership( $user->ID );
						} catch ( Framework\SV_WC_Plugin_Exception $e ) {
							\WP_CLI::warning( sprintf( 'Membership transfer from user %1$s to user %2$s failed: %3$s.', $e->getMessage() ) );
						}
					}
				}
			}

			$start_date = false;

			if ( isset( $data['start_date'] ) ) {

				$start_date = $this->parse_membership_date( $data['start_date'] );

				if ( false === $start_date || ( strlen( $data['start_date'] ) < 10 ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_date', 'The start date is not a valid date. Must be a non-empty YYYY-MM-DD value.' );
				}
			}

			$end_date = false;

			if ( isset( $data['end_date'] ) ) {

				$end_date = $this->parse_membership_date( $data['end_date'] );

				if ( false === $end_date || ( strlen( $data['end_date'] ) < 10 ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_date', 'The end date is not a valid date. Must be a non-empty YYYY-MM-DD value.' );
				}
			}

			$status = false;

			if ( isset( $data['status'] ) ) {

				if ( $this->is_valid_membership_status( $data['status'] ) ) {
					$status = $data['status'];
				} else {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_invalid_status', sprintf( 'The status "%1$s" is not a valid User Membership status. Please use one of the following: %2$s', $data['status'], wc_memberships_list_items( $this->get_membership_status_keys(), 'or' ) ) );
				}
			}

			$product_id = 0;

			if ( isset( $data['product'] ) ) {

				$product = wc_get_product( (int) $data['product'] );

				if ( ! $product instanceof \WC_Product ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_product_not_found', sprintf( 'Product that grants access "%s" not found.', $data['product'] ) );
				}

				$product_id = (int) $product->get_id();
			}

			$order_id = 0;

			if ( isset( $data['order'] ) ) {

				$order = wc_get_order( $data['order'] );

				if ( ! $order instanceof \WC_Order ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_order_not_found', sprintf( 'Order %s not found.', $data['order'] ) );
				}

				$order_id = (int) $order->get_id();
			}

			$plan_id = 0;

			if ( isset( $data['plan_id'] ) ) {

				$plan = wc_memberships_get_membership_plan( (int) $data['plan_id'] );

				if ( ! $plan instanceof \WC_Memberships_Membership_Plan ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_membership_plan_not_found', sprintf( 'Membership Plan %s not found.', $id ) );
				}

				$plan_id = (int) $plan->get_id();
			}

			if ( $plan_id > 0 ) {

				$updated = wp_update_post( array(
					'ID'          => (int) $user_membership->get_id(),
					'post_type'   => 'wc_user_membership',
					'post_parent' => $plan_id
				), true );

				if ( 0 === $updated || is_wp_error( $updated ) ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_cannot_update_user_membership', $updated->get_error_message() );
				}
			}

			if ( false !== $start_date ) {
				$user_membership->set_start_date( $start_date );
			}

			if ( false !== $status ) {
				$user_membership->update_status( $status );
			}

			if ( false !== $end_date ) {
				$user_membership->set_end_date( $end_date );
			}

			if ( $product_id > 0 ) {
				$user_membership->set_product_id( $product_id );
			}

			if ( $order_id > 0 ) {
				$user_membership->set_order_id( $order_id );
			}

			/**
			 * Upon updating a User Membership via CLI.
			 *
			 * @since 1.7.0
			 *
			 * @param \WC_Memberships_User_Membership $user_membership
			 * @param array $data
			 */
			do_action( 'wc_memberships_cli_update_user_membership', $user_membership, $data );

			\WP_CLI::success( sprintf( 'Updated User Membership %s', $user_membership->get_id() ) );

		} catch ( \WC_CLI_Exception $e ) {

			\WP_CLI::error( $e->getMessage() );
		}
	}


	/**
	 * Get a User Membership.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : User Membership ID to look for, can be also a combination of user ID and plan ID (colon separated)
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole User Membership fields, returns the value of a single fields
	 *
	 * [--fields=<fields>]
	 * : Get a specific subset of the User Membership's fields
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * For more fields, see: wp wc memberships membership list --help
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships membership get 123 --fields=id,status
	 *
	 *     wp wc memberships membership get 19:80
	 *
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param int[] $args either only the user membership ID or a combination of user ID and plan ID, colon separated
	 * @param array $assoc_args formatting arguments
	 */
	public function get( $args, $assoc_args ) {

		\WP_CLI::warning( $this->get_deprecation_warning( 'wp wc user_membership get' ) );

		try {

			$args = array_filter( array_map( 'trim', explode( ':', $args[0] ) ) );

			if ( isset( $args[0], $args[1] ) ) {

				$user_membership = wc_memberships_get_user_membership( (int) $args[0], (int) $args[1] );

				if ( ! $user_membership instanceof \WC_Memberships_User_Membership ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_user_membership', sprintf( 'Invalid User Membership for user "%1$s" with plan "%2$s".', $args[0], $args[1] ) );
				}

			} else {

				$user_membership = wc_memberships_get_user_membership( (int) $args[0] );

				if ( ! $user_membership instanceof \WC_Memberships_User_Membership ) {
					throw new \WC_CLI_Exception( 'woocommerce_memberships_cli_invalid_user_membership', sprintf( 'Invalid User Membership "%s".', $args[0] ) );
				}
			}

			$user_membership_data = $this->get_user_membership_data( $user_membership );

			$formatter = $this->get_formatter( $assoc_args );
			$formatter->display_item( $user_membership_data );

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
			'user_id',
			'plan_id',
			'plan',
			'status',
		);

		$default_fields[] = 'start_date';
		$default_fields[] = 'end_date';

		/**
		 * User Memberships default format fields used in WP CLI.
		 *
		 * @since 1.7.0
		 *
		 * @param array $default_fields
		 */
		$default_fields = apply_filters( 'wc_memberships_cli_user_membership_default_fields', $default_fields );

		return implode( ',', $default_fields );
	}


	/**
	 * Get User Membership data.
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership
	 * @return array
	 */
	protected function get_user_membership_data( $user_membership ) {

		$user_membership_data = '';

		if ( $user_membership instanceof \WC_Memberships_User_Membership ) {

			$user_id = $user_membership->get_user_id();
			$member  = $this->get_member( (int) $user_id );

			$user_membership_data = array(
				'id'                => $user_membership->get_id(),
				'user_id'           => $user_membership->get_user_id(),
				'user_name'         => $member->user_login,
				'member_first_name' => $member->first_name,
				'member_last_name'  => $member->last_name,
				'member_email'      => $member->user_email,
				'plan_id'           => $user_membership->get_plan_id(),
				'plan'              => $user_membership->get_plan()->get_name(),
				'status'            => $user_membership->get_status(),
				'has_access'        => $user_membership->is_active() ? 'yes' : 'no',
				'paused_date'       => $user_membership->get_paused_date(),
				'order_id'          => $user_membership->get_order_id(),
				'product_id'        => $user_membership->get_product_id(),
				'start_date'        => $user_membership->get_start_date(),
				'end_date'          => $user_membership->get_end_date(),
				'previous_owner'    => '',
			);

			if ( $previous_owners = $user_membership->get_previous_owners() ) {
				$user_membership_data['previous_owner'] = $previous_owners;
			}
		}

		/**
		 * Filter the user membership data for Memberships CLI.
		 *
		 * @since 1.7.0
		 *
		 * @param array $membership_plan_data the plan data passed to CLI
		 * @param \WC_Memberships_User_Membership $membership_plan the user membership
		 */
		$user_membership_data = apply_filters( 'wc_memberships_cli_user_membership_data', $user_membership_data, $user_membership );

		return $this->flatten_array( (array) $user_membership_data );
	}


	/**
	 * List User Memberships.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : Filter User Memberships based on property
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each User Membership
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific User Membership fields
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each User Membership:
	 *
	 * * id
	 * * user_id
	 * * plan_id
	 * * plan
	 * * status
	 * * start_date
	 * * end_date
	 *
	 * These fields are optionally available:
	 *
	 * * plan_id
	 * * paused_date
	 * * order_id
	 * * product_id
	 * * subscription_id
	 * * user_name
	 * * member_first_name
	 * * member_last_name
	 * * member_email
	 * * previous_owner
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships membership list
	 *
	 *     wp wc memberships membership list --field=id
	 *
	 *     wp wc memberships membership list --fields=id,user_id,status --format=csv
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

		\WP_CLI::warning( $this->get_deprecation_warning( 'wp wc user_membership list' ) );

		$query_args = $this->get_list_query_args( $assoc_args );
		$formatter  = $this->get_formatter( $assoc_args );

		if ( 'ids' === $formatter->format ) {

			$query_args['fields'] = 'ids';
			$query = new \WP_Query( $query_args );
			echo implode( ' ', $query->posts );

		} else {

			$query = new \WP_Query( $query_args );
			$items = $this->format_posts_to_items( $query->posts );
			$formatter->display_items( $items );
		}
	}


	/**
	 * Get query args for list subcommand.
	 *
	 * @see WC_Memberships_CLI_User_Membership::list__()
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param array $args arguments from command line
	 * @return array
	 */
	protected function get_list_query_args( $args ) {

		$query_args = array(
			'post_type'      => 'wc_user_membership',
			'posts_per_page' => -1,
			'meta_query'     => array(),
		);

		if ( ! empty( $args['status'] ) ) {

			$statuses = explode( ',', $args['status'] );

			$query_args['post_status'] = array();

			foreach ( $statuses as $status ) {
				$query_args['post_status'][] = $this->add_membership_status_prefix( $status );
			}

		} else {

			$query_args['post_status'] = 'any';
		}

		if ( isset( $args['id'] ) ) {
			$query_args['post__in'] = explode( ',', $args['id'] );
		}

		if ( isset( $args['plan_id'] ) ) {
			$query_args['post_parent__in'] = explode( ',', $args['plan_id'] );
		}

		$users = array();

		if ( isset( $args['user_id'] ) ) {
			$users[] = array_map( 'absint', explode( ',', $args['user_id'] ) );
		}

		if ( isset( $args['user_name'] ) ) {

			$user_names = explode( ',', $args['user_name'] );

			foreach ( $user_names as $user_name ) {

				if ( $user = $this->get_member( $user_name ) ) {

					$users[] = (int) $user->ID;
				}
			}
		}

		if ( isset( $args['member_email'] ) ) {

			$user_names = explode( ',', $args['member_email'] );

			foreach ( $user_names as $user_name ) {

				if ( $user = $this->get_member( $user_name ) ) {

					$users[] = (int) $user->ID;
				}
			}
		}

		if ( ! empty( $users ) ) {
			$query_args['author__in'] = array_unique( $users );
		}

		if ( isset( $args['order_id'] ) ) {

			$order_ids = array_map( 'absint', explode( ',', $args['order_id'] ) );
			$count     = count( $order_ids );

			$query_args['meta_query'][] = array(
				'key'     => '_order_id',
				'value'   => 1 === $count ? $order_ids[0] : $order_ids,
				'compare' => 1 === $count ? '=' : 'IN',
				'type'    => 'NUMERIC',
			);
		}

		if ( isset( $args['product_id'] ) ) {

			$product_ids = array_map( 'absint', explode( ',', $args['product_id'] ) );
			$count       = count( $product_ids );


			$query_args['meta_query'][] = array(
				'key'     => '_product_id',
				'value'   => 1 === $count ? $product_ids[0] : $product_ids,
				'compare' => 1 === $count ? '=' : 'IN',
				'type'    => 'NUMERIC',
			);
		}

		if ( isset( $args['previous_owner'] ) ) {

			$previous_owners = array_map( 'absint', explode( ',', $args['previous_owner'] ) );

			$query_args['meta_query'][] = array(
				'key'     => '_previous_owners',
				'value'   => serialize( $previous_owners ),
				'compare' => 'LIKE',
			);
		}

		if ( isset( $args['start_date'] ) ) {
			$query_args['meta_query'][] = $this->get_date_range_meta_query_args(  '_start_date', $args['start_date'] );
		}

		if ( isset( $args['end_date'] ) ) {
			$query_args['meta_query'][] = $this->get_date_range_meta_query_args(  '_end_date', $args['end_date'] );
		}

		if ( isset( $args['paused_date'] ) ) {
			$query_args['meta_query'][] = $this->get_date_range_meta_query_args(  '_paused_date', $args['paused_date'] );
		}

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

			$user_membership = wc_memberships_get_user_membership( $post->ID );

			if ( ! $user_membership instanceof \WC_Memberships_User_Membership ) {
				continue;
			}

			$items[] = $this->get_user_membership_data( $user_membership );
		}

		return $items;
	}


	/**
	 * Delete User Memberships.
	 *
	 * ## OPTIONS
	 *
	 * <id>...
	 * : The User Membership ID to delete
	 *
	 * ## EXAMPLES
	 *
	 *     wp wc memberships membership delete 123
	 *
	 *     wp wc memberships membership delete $(wp wc memberships membership list --format=ids)
	 *
	 *
	 * @since 1.7.0
	 * @deprecated since 1.13.0
	 *
	 * @param int|int[] $args
	 * @param array $assoc_args
	 */
	public function delete( $args, $assoc_args ) {

		\WP_CLI::warning( $this->get_deprecation_warning( 'wp wc user_membership delete' ) );

		$exit_code = 0;
		$args      = is_array( $args ) ? $args : (array) $args;

		foreach ( $args as $user_membership_id ) {

			$user_membership = wc_memberships_get_user_membership( $user_membership_id );

			if ( ! $user_membership instanceof \WC_Memberships_User_Membership ) {
				\WP_CLI::warning( "Failed deleting User Membership $user_membership_id: not an User Membership." );
				continue;
			}

			/**
			 * Upon deleting a User Membership via CLI.
			 *
			 * @since 1.7.0
			 *
			 * @param int $user_membership_id
			 */
			do_action( 'wc_memberships_cli_delete_user_membership', $user_membership_id );

			$success = wp_delete_post( $user_membership_id, true );

			if ( $success ) {
				\WP_CLI::success( "Deleted User Membership $user_membership_id." );
			} else {
				$exit_code++;
				\WP_CLI::warning( "Failed deleting User Membership $user_membership_id." );
			}
		}

		exit( $exit_code ? 1 : 0 );
	}


}
