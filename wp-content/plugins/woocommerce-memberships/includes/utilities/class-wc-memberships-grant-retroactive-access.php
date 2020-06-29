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

defined( 'ABSPATH' ) or exit;

/**
 * Job handler to grant access to membership plans retroactively.
 *
 * @since 1.10.0
 */
class WC_Memberships_Grant_Retroactive_Access extends \WC_Memberships_Job_Handler {


	/**
	 * Sets up the job handler.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		$this->action   = 'grant_retroactive_access';
		$this->data_key = 'user_ids';

		parent::__construct();

		add_action( "{$this->identifier}_job_complete", array( $this, 'cleanup_jobs' ), 1 );
		add_action( "{$this->identifier}_job_failed",   array( $this, 'cleanup_jobs' ), 1 );
	}


	/**
	 * Creates a new job.
	 *
	 * @since 1.10.4
	 *
	 * @param array $attrs associative array
	 * @return null|\stdClass
	 */
	public function create_job( $attrs ) {

		$attrs = wp_parse_args( $attrs, array(
			'membership_plan_id' => 0,
			'total'              => 0,
			'results'            => (object) array(
				'granted' => 0,
				'skipped' => 0,
				'html'    => '',
			),
		) );

		return parent::create_job( $attrs );
	}


	/**
	 * Returns a job object.
	 *
	 * @since 1.10.0
	 *
	 * @param null|int|string|\stdClass|\WC_Memberships_Membership_Plan $id a membership plan ID, or a job identifier (string or object)
	 * @return null|\stdClass
	 */
	public function get_job( $id = null ) {

		// for this task we may pass a plan ID (integer) or a plan object to retrieve the corresponding job,
		// since each job should be unique per plan and only one job per plan is allowed to run at one time
		if ( $id instanceof \WC_Memberships_Membership_Plan || is_numeric( $id ) ) {

			$plan_id  = $id instanceof \WC_Memberships_Membership_Plan ? $id->get_id() : (int) $id;
			$jobs     = $plan_id > 0 ? $this->get_jobs() : array();
			$plan_job = null;

			if ( ! empty( $jobs ) ) {

				foreach ( $jobs as $job ) {

					if ( isset( $job->membership_plan_id ) && $job->membership_plan_id > 0 && $plan_id === (int) $job->membership_plan_id ) {

						$plan_job = $job;
						break;
					}
				}
			}

		} else {

			// otherwise, retrieve the job normally by job ID or object
			$plan_job = parent::get_job( $id );
		}

		return $plan_job;
	}


	/**
	 * Checks whether there is an ongoing job.
	 *
	 * The assumption is that there should be only one job at the time, if it's processing it must be the current one.
	 *
	 * @since 1.10.0
	 *
	 * @param string|int $id job ID or membership plan ID
	 * @return bool
	 */
	public function has_ongoing_job( $id = null ) {

		$job = $this->get_job( $id );

		return $job && isset( $job->status ) && 'processing' !== $job->status;
	}


	/**
	 * Deletes a job.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass|string|int|\WC_Memberships_Membership_Plan|null $job a job or plan identifier
	 * @return bool
	 */
	public function delete_job( $job ) {

		if ( is_numeric( $job ) ) {
			$job = $this->get_job( $job );
		}

		return $job && parent::delete_job( $job );
	}


	/**
	 * Grants access to users in background.
	 *
	 * @since 1.10.0
	 *
	 * @param \stdClass $job job object
	 * @param int $items_per_batch items to process per batch
	 * @return false|\stdClass job object or false on error
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function process_job( $job, $items_per_batch = 5 ) {

		$items_per_batch = $this->get_items_per_batch( $items_per_batch, $job );

		if ( ! $this->start_time ) {
			$this->start_time = time();
		}

		// indicate that the job has started processing
		if ( 'processing' !== $job->status ) {

			$job->status                = 'processing';
			$job->started_processing_at = current_time( 'mysql' );

			$job = $this->update_job( $job );
		}

		$data_key = $this->data_key;

		// we need users to loop to check if they can be granted access
		if ( ! isset( $job->{$data_key} ) || ! is_array( $job->{$data_key} ) ) {

			$this->fail_job( $job );

			throw new Framework\SV_WC_Plugin_Exception( esc_html__( 'Users to look for granting access to a plan not defined or invalid.', 'woocommerce-memberships' ) );
		}

		// we need a membership plan ID to give access to
		if ( ! isset( $job->membership_plan_id ) || ! is_numeric( $job->membership_plan_id ) ) {

			$this->fail_job( $job );

			throw new Framework\SV_WC_Plugin_Exception( sprintf( esc_html__( 'Membership Plan to grant access to not defined or invalid.', 'woocommerce-memberships' ), 'membership_plan_id' ) );
		}

		/* @type int[] $user_ids array of user IDs */
		$user_ids = $job->{$data_key};

		$job->total = count( $user_ids );

		// skip already processed items
		if ( $job->progress && ! empty( $user_ids ) ) {
			$user_ids = array_slice( $user_ids, $job->progress, null, true );
		}

		// loop over unprocessed items and process them
		if ( ! empty( $user_ids ) ) {

			$membership_plan = wc_memberships_get_membership_plan( $job->membership_plan_id );
			$processed_users = 0;

			foreach ( $user_ids as $user_id ) {

				if ( is_numeric( $user_id ) && $user_id > 0 && $this->process_item( $user_id, $membership_plan ) ) {
					$job = $this->update_job_results( $job, 'granted' );
				} else {
					$job = $this->update_job_results( $job, 'skipped' );
				}

				$processed_users++;

				// job limits reached
				if ( $processed_users >= $items_per_batch || $this->time_exceeded() || $this->memory_exceeded() ) {
					break;
				}
			}

			$job->progress  += $processed_users;
			$job->percentage = $this->get_percentage( $job );

			// update job progress
			$job = $this->update_job( $job );

		} else {

			// if there are no more users to process, then we're done
			$job->progress   = $job->total;
			$job->percentage = $this->get_percentage( $job );
		}

		// complete current job
		if ( $job->progress >= $job->total ) {

			$job = $this->update_job_results( $job, 'html' );
			$job = $this->complete_job( $job );
		}

		return $job;
	}


	/**
	 * Process one user to grant membership access.
	 *
	 * @since 1.10.0
	 *
	 * @param int $user_id the ID of the user to grant a membership to
	 * @param \WC_Memberships_Membership_Plan $membership_plan the plan to grant access to
	 * @return bool whether a membership was granted
	 */
	public function process_item( $user_id, $membership_plan ) {

		$granted = false;

		if ( $membership_plan instanceof \WC_Memberships_Membership_Plan && 'publish' === $membership_plan->post->post_status ) {

			switch ( $membership_plan->get_access_method() ) {

				// grant access to users who have purchased in the past at least one product that should grant access
				case 'purchase' :
					$granted = $this->grant_access_to_existing_purchases( $user_id, $membership_plan );
				break;

				// free plan: grant access to users for the simple act of having signed up
				case 'signup' :
					$granted = $this->grant_free_access_to_existing_user( $user_id, $membership_plan );
				break;
			}
		}

		return $granted;
	}


	/**
	 * Retroactively grants a user membership to registered users for a signup-access membership plan.
	 *
	 * @since 1.10.0
	 *
	 * @param \WP_User|int $user user to grant access to
	 * @param \WC_Memberships_Membership_Plan $membership_plan Membership Plan the user would access to
	 * @return bool whether access was granted for the given user/plan combination
	 */
	private function grant_free_access_to_existing_user( $user, $membership_plan ) {

		$user_membership = null;

		/**
		 * Filters whether existing users can be retroactively granted access to free membership plans created after a user registration occurred.
		 *
		 * @since 1.7.0
		 *
		 * @param bool $grant_access whether to grant access (default true if the user is not a member or expired member already)
		 * @param array $args
		 */
		$grant_access = (bool) apply_filters( 'wc_memberships_grant_access_to_existing_user', ! wc_memberships_is_user_member( $user, $membership_plan ), array(
			'user_id'    => $user instanceof \WP_User ? $user->ID : $user,
			'plan_id'    => $membership_plan->get_id(),
		) );

		if ( $grant_access ) {
			$user_membership = wc_memberships()->get_plans_instance()->grant_access_to_free_membership( $user, false, $membership_plan );
		}

		$granted = $user_membership instanceof \WC_Memberships_User_Membership;

		/**
		 * Applies when a user is being processed for granting access to sign up plans retroactively.
		 *
		 * @since 1.10.1
		 *
		 * @param \WC_Memberships_User_Membership|null $user_membership a user membership, if access was granted, or null if not
		 * @param \WP_User $user an user to grant access to a plan (may be a member already)
		 * @param \WC_Memberships_Membership_Plan $membership_plan the sign up plan we are granting access to
		 */
		$user_membership = apply_filters( 'wc_memberships_granted_free_access_from_previous_signup', $user_membership, $user, $membership_plan );

		if ( ! $granted && $user_membership instanceof \WC_Memberships_User_Membership ) {
			$granted = true;
		}

		return $granted;
	}


	/**
	 * Grants access to a non-free membership plan to users which have previously purchased a product that grants access.
	 *
	 * @since 1.10.0
	 *
	 * @param \WP_User|int $user the user to grant access to, by object or ID
	 * @param \WC_Memberships_Membership_Plan $membership_plan Membership Plan to grant user access to
	 * @return bool whether access was granted for the given user/plan combination
	 */
	private function grant_access_to_existing_purchases( $user, $membership_plan ) {

		$granted          = false;
		$user_membership  = null;
		$user_id          = $user instanceof \WP_User ? $user->ID : $user;
		$plan_id          = $membership_plan->get_id();
		$plan_product_ids = $membership_plan->get_product_ids();

		if ( ! empty( $plan_product_ids ) ) {

			/**
			 * Filters the array of valid order statuses that grant access.
			 *
			 * Allows to include additional custom order statuses that should grant access when the admin uses the "grant previous purchases access" action.
			 *
			 * @since 1.0.0
			 *
			 * @param array $valid_order_statuses_for_grant array of order statuses
			 * @param \WC_Memberships_Membership_Plan $membership_plan the associated membership plan object
			 */
			$statuses = (array) apply_filters( 'wc_memberships_grant_access_from_existing_purchase_order_statuses', wc_get_is_paid_statuses(), $membership_plan );

			if ( ! empty( $statuses ) ) {

				// get all orders for the current user with a status suited for membership activation
				$order_ids = wc_get_orders( array(
					'customer_id' => $user_id,
					'type'        => 'shop_order',
					'status'      => $statuses,
					'limit'       => -1,
					'return'      => 'ids',
				) );

				if ( ! empty( $order_ids ) ) {

					// get all product ids from suitable orders that may grant access to a membership
					$cumulative_access_allowed = wc_memberships_cumulative_granting_access_orders_allowed();
					$products_per_order        = $this->get_products_from_orders( $order_ids, $plan_product_ids );

					foreach ( $products_per_order as $order_id => $order_product_ids ) {

						if ( ! empty( $order_product_ids ) && is_array( $order_product_ids ) ) {

							foreach ( $order_product_ids as $order_product_id ) {

								if ( in_array( $order_product_id, $plan_product_ids, false ) ) {

									// if membership extensions by cumulative purchases are enabled grant access if the order didn't grant access before
									if ( $cumulative_access_allowed ) {
										$existing_membership = wc_memberships_get_user_membership( $user_id, $membership_plan );
										$grant_access        = ! ( $existing_membership && wc_memberships_has_order_granted_access( $order_id, array( 'user_membership' => $existing_membership ) ) );
										// if, instead, cumulative granting access orders are disallowed, grant access if user is not already a member
									} else {
										$grant_access = ! wc_memberships_is_user_member( $user_id, $membership_plan, false );
									}

									/**
									 * Filters whether an existing purchase of the product should grant access to the membership plan or not.
									 *
									 * Allows third party code to override if a previously purchased product should retroactively grant access to a membership plan or not.
									 *
									 * @since 1.0.0
									 *
									 * @param bool $grant_access whether grant access from existing purchase
									 * @param array $args array of arguments connected with the access request
									 */
									$really_grant_access = (bool) apply_filters( 'wc_memberships_grant_access_from_existing_purchase', $grant_access, array(
										'user_id'    => $user_id,
										'product_id' => $order_product_id,
										'order_id'   => $order_id,
										'plan_id'    => $plan_id,
									) );

									if ( $really_grant_access ) {

										$user_membership_id = $membership_plan->grant_access_from_purchase( $user_id, $order_product_id, $order_id );

										if ( is_numeric( $user_membership_id ) && $user_membership_id > 0 ) {

											$user_membership = wc_memberships_get_user_membership( $user_membership_id );

											if ( ! $granted && $user_membership instanceof \WC_Memberships_User_Membership ) {
												$granted = true;
											}
										}
									}
								}
							}
						}
					}
				}
			}

			/**
			 * Applies when a user is being processed for granting access to their existing purchases.
			 *
			 * @since 1.10.1
			 *
			 * @param \WC_Memberships_User_Membership|null $user_membership a user membership, if access was granted, or null, if not
			 * @param \WP_User|int $user an user (by object or ID) we are evaluating whether to grant access to a plan (may be a member already)
			 * @param \WC_Memberships_Membership_Plan $membership_plan the plan we are granting access to
			 */
			$user_membership = apply_filters( 'wc_memberships_granted_access_from_existing_purchase', $user_membership, $user, $membership_plan );

			if ( ! $granted && $user_membership instanceof \WC_Memberships_User_Membership ) {
				$granted = true;
			}
		}

		return $granted;
	}


	/**
	 * Gets product IDs from an order ID (helper method).
	 *
	 * TODO When WooCommerce starts using alternate data stores for products (perhaps from WC 3.5+) this method may require an update as it performs a direct SQL query assuming a standard WPDB data organization {FN 2018-07-23}
	 *
	 * @since 1.10.6
	 *
	 * @param int|int[] $orders an order ID or array of order IDs
	 * @param int[] $access_products array of IDs of products that grant access to a plan
	 * @return array of product IDs for each given order
	 */
	private function get_products_from_orders( $orders, $access_products ) {
		global $wpdb;

		$products_per_order = array();

		if ( is_numeric( $orders ) ) {
			$orders = array( $orders );
		}

		if ( is_array( $orders ) && is_array( $access_products ) ) {

			$product_ids = implode( ',', array_unique( array_map( 'absint', $access_products ) ) );
			$order_ids   = implode( ',', array_unique( array_map( 'absint', $orders ) ) );

			if ( ! empty( $order_ids ) && ! empty( $product_ids ) ) {

				$items_table = $wpdb->prefix . 'woocommerce_order_items';
				$meta_table  = $wpdb->prefix . 'woocommerce_order_itemmeta';
				$results     = $wpdb->get_results( "
					SELECT items_table.order_id AS order_id,
					       items_meta_table.meta_value AS product_id
					FROM {$items_table} AS items_table
					JOIN {$meta_table}  AS items_meta_table
					ON items_table.order_item_id = items_meta_table.order_item_id
					WHERE items_table.order_id IN ({$order_ids})
					AND ( ( items_meta_table.meta_key = '_product_id'   AND items_meta_table.meta_value IN ({$product_ids}) )
					 OR   ( items_meta_table.meta_key = '_variation_id' AND items_meta_table.meta_value IN ({$product_ids}) ) )
				" );

				if ( ! empty ( $results ) ) {

					foreach ( $results as $result ) {

						if ( isset( $result->order_id, $result->product_id ) && is_numeric( $result->order_id ) && is_numeric( $result->product_id ) && (int) $result->product_id > 0 ) {

							if ( ! array_key_exists( (int) $result->order_id, $products_per_order ) ) {
								$products_per_order[ (int) $result->order_id ] = array();
							}

							$products_per_order[ (int) $result->order_id ][] = (int) $result->product_id;
						}
					}
				}
			}
		}

		return $products_per_order;
	}


	/**
	 * Returns an access granting job results information.
	 *
	 * @since 1.10.4
	 *
	 * @param \stdClass $job a job object
	 * @return string HTML
	 */
	protected function get_job_results_html( $job ) {

		$results     = (object) $job->results;
		$total       = max( 0, (int) $job->total );
		$granted     = max( 0, (int) $results->granted );
		$plan        = wc_memberships_get_membership_plan( $job->membership_plan_id );
		$plan_name   = $plan->get_name();
		$members_url = admin_url( 'edit.php?s&post_type=wc_user_membership&action=-1&post_parent=' . $plan->get_id() . '&filter_action=Filter&paged=1&action2=-1' );

		if ( $total > 0 ) {

			if ( $granted > 0 ) {
				/* translators: Placeholders: %1$s - number of users that were just granted access to a plan, %2$s - Membership Plan name */
				$message = '<p><span class="dashicons dashicons-yes"></span> ' . sprintf( _n( '%1$s user was granted access to "%2$s".', '%1$s users were granted access to "%2$s".', $granted, 'woocommerce-memberships' ), $granted, esc_html( $plan_name ) );
			} else {
				$message = '<p><span class="dashicons dashicons-no"></span> ' . esc_html__( 'No new users were given access to this plan.', 'woocommerce-memberships' ) . '</p>';
			}

		} else {

			$message = '<p><span class="dashicons dashicons-no"></span> ' . esc_html__( 'No users were found to process for granting previous access.', 'woocommerce-memberships' ) . '</p>';
		}

		/* translators: Placeholder: %s - Membership Plan name */
		$message .= '<p>' . sprintf( __( 'View all memberships for "%s".', 'woocommerce-memberships' ), '<a href="' . esc_url( $members_url ) . '">' . esc_html( $plan_name ) . '</a>' ) . '</p>';

		return $message;
	}


}
