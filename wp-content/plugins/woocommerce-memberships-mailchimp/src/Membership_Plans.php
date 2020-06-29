<?php
/**
 * MailChimp for WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade MailChimp for WooCommerce Memberships to newer
 * versions in the future. If you wish to customize MailChimp for WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\MailChimp;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership Plans events handler.
 *
 * @since 1.0.0
 */
class Membership_Plans {


	/** @var array associative array of memoized plan IDs and their merge field IDs */
	private $membership_plans_merge_ids = array();

	/** @var array associative array of memoized plan IDs and their merge field tags */
	private $membership_plans_merge_tags = array();

	/** @var string the meta key of the plan post meta holding a copy of the current MailChimp merge field tag */
	private $membership_plan_merge_tag_meta_key = '_mailchimp_sync_merge_tag';

	/** @var string the meta key of the plan post meta holding a copy of the default MailChimp merge field tag */
	private $membership_plan_default_merge_tag_meta_key = '_mailchimp_sync_default_merge_tag';


	/**
	 * Hooks in membership plans events.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'save_post',          array( $this, 'membership_plan_saved' ), 10, 2 );
		add_action( 'before_delete_post', array( $this, 'membership_plan_deleted' ) );
	}


	/**
	 * Handles events upon membership plan creation (in admin or by other means).
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $plan_id the matching post (plan) ID
	 * @param bool $is_update whether the plan is being updated
	 */
	public function membership_plan_saved( $plan_id, $is_update ) {

		$plugin = wc_memberships_mailchimp();

		if (    'wc_membership_plan' === get_post_type( $plan_id )
		     && 'publish'            === get_post_status( $plan_id )
		     && $plugin->is_connected() ) {

			$plan = wc_memberships_get_membership_plan( $plan_id );
			$list = MailChimp_Lists::get_list();

			if ( $plan && $list && ! $this->plan_has_merge_field_id( $plan_id ) ) {

				$api = $plugin->get_api_instance();

				if ( $is_update && isset( $_POST['_mailchimp_sync_merge_tag'] ) ) {
					$merge_tag = MailChimp_Lists::parse_merge_tag( $_POST['_mailchimp_sync_merge_tag'] );
				} else {
					$merge_tag = MailChimp_Lists::parse_merge_tag( $plan->get_slug() );
				}

				if ( ! empty( $merge_tag ) ) {
					// create a new tag or assign an existing one if matches
					$existing_merge_id = $api->merge_field_tag_exists( $list->get_id(), $merge_tag );
					$merge_field_id    = false !== $existing_merge_id ? $existing_merge_id : $api->create_plan_merge_field( $list->get_id(), $plan, $merge_tag );
				}

				if ( ! empty( $merge_field_id ) ) {

					$list->set_plan_merge_field( $plan_id, $merge_field_id );

					if ( $merge_tag = $this->get_plan_merge_field_tag( $plan ) ) {
						$this->set_plan_merge_field_tag_cache( $plan_id, $merge_tag );
					} else {
						$this->clear_plan_merge_field_tag_cache( $plan_id );
					}

				} else {

					$this->clear_plan_merge_field_tag_cache( $plan_id );
				}
			}
		}
	}


	/**
	 * Handles events upon membership plan deletion.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $plan_id The matching post ID
	 */
	public function membership_plan_deleted( $plan_id ) {

		if (    'wc_membership_plan' === get_post_type( $plan_id )
		     && wc_memberships_mailchimp()->is_connected() ) {

			$list = MailChimp_Lists::get_list();

			if ( $list ) {

				$merge_field_id = $this->get_plan_merge_field_id( $plan_id );

				if ( $merge_field_id && $list->is_deleted_plan_merge_field_handling( 'delete' ) ) {
					// if enabled from settings, then also delete this merge field from MailChimp
					wc_memberships_mailchimp()->get_api_instance()->delete_list_merge_field( MailChimp_Lists::get_current_list_id(), $merge_field_id );
				}

				// always remove from setting
				$list->set_plan_merge_field( $plan_id, '' );
				// clean object cache, in case
				$this->clear_plan_merge_field_tag_cache( $plan_id );
			}
		}
	}


	/**
	 * Returns a Membership Plan's ID (helper method).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|\WC_Memberships_Membership_Plan $plan plan ID, slug or object
	 * @return int|null
	 */
	private function get_membership_plan_id( $plan ) {

		$plan_id = null;

		if ( is_numeric( $plan ) ) {
			$plan_id     = (int) $plan;
		} elseif ( is_string( $plan ) ) {
			$plan_object = wc_memberships_get_membership_plan( $plan );
			$plan_id     = $plan_object ? $plan_object->get_id() : null;
		} elseif ( $plan instanceof \WC_Memberships_Membership_Plan ) {
			$plan_id     = $plan->get_id();
		}

		return $plan_id;
	}


	/**
	 * Gets a plan's merge field ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|\WC_Memberships_Membership_Plan $plan Memberships plan ID, slug or object
	 * @return false|string|int a string or a number
	 */
	public function get_plan_merge_field_id( $plan ) {

		$list     = MailChimp_Lists::get_list();
		$merge_id = false;

		if ( $list && ( $plan_id = $this->get_membership_plan_id( $plan ) ) ) {

			if ( array_key_exists( $plan_id, $this->membership_plans_merge_ids ) ) {

				$merge_id = $this->membership_plans_merge_ids[ $plan_id ];

			} else {

				$merge_id = $list->get_plan_merge_field_id( $plan_id );

				if ( is_numeric( $merge_id ) || is_string( $merge_id ) ) {
					$this->membership_plans_merge_ids[ $plan_id ] = $merge_id;
				}
			}
		}

		return $merge_id;
	}


	/**
	 * Checks whether a plan has a merge ID set.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|\WC_Memberships_Membership_Plan $plan_id plan ID, slug or object
	 * @return bool
	 */
	public function plan_has_merge_field_id( $plan_id ) {

		$merge_id = $this->get_plan_merge_field_id( $plan_id );

		return ! empty( $merge_id ) && ( is_numeric( $merge_id ) || is_string( $merge_id ) );
	}


	/**
	 * Returns the plan merge field ID corresponding tag.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|\WC_Memberships_Membership_Plan $plan the plan ID, slug or object
	 * @return null|string merge tag or null upon error (ID is set but no corresponding tag is found: perhaps it has been deleted?)
	 */
	public function get_plan_merge_field_tag( $plan ) {

		$merge_tag = '';

		if ( $plan_id = $this->get_membership_plan_id( $plan ) ) {

			$list     = MailChimp_Lists::get_list();
			$settings = $list ? $list->get_settings() : null;
			$merge_id = null;

			if ( ! empty( $settings ) ) {
				$merge_id = isset( $settings['merge_fields']['plans'][ $plan_id ] ) ? $settings['merge_fields']['plans'][ $plan_id ] : '';
			}

			if ( is_string( $merge_id ) || is_numeric( $merge_id ) ) {

				if ( isset( $this->membership_plans_merge_tags[ $plan_id ] ) ) {

					$merge_tag = $this->membership_plans_merge_tags[ $plan_id ];

				} else {

					$transient         = get_transient( MailChimp_Lists::$plans_merge_tags_transient );
					$cached_merge_tags = is_array( $transient ) ? $transient : array();

					if ( isset( $cached_merge_tags[ $plan_id ] ) ) {
						$merge_tag   = $cached_merge_tags[ $plan_id ];
					} else {
						$merge_field = wc_memberships_mailchimp()->get_api_instance()->get_list_merge_field_by_id( $list->get_id(), $merge_id );
						$merge_tag   = $merge_field && isset( $merge_field->tag ) ? $merge_field->tag : null;
					}

					if ( ! empty( $merge_tag ) ) {
						$this->set_plan_merge_field_tag_cache( $plan_id, $merge_tag );
					} else {
						$this->clear_plan_merge_field_tag_cache( $plan_id );
					}
				}
			}
		}

		return $merge_tag;
	}


	/**
	 * Checks whether a plan has a merge field tag set.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|\WC_Memberships_Membership_Plan $plan plan object, ID or slug
	 * @param string $merge_tag whether to check if the plan has a specific merge tag associated to
	 * @return bool
	 */
	public function plan_has_merge_field_tag( $plan, $merge_tag = null ) {

		$plan_merge_tag = null;

		if ( $plan_id = $this->get_membership_plan_id( $plan ) ) {

			if ( isset( $this->membership_plans_merge_tags[ $plan_id ] ) ) {

				$plan_merge_tag   = $this->membership_plans_merge_tags[ $plan_id ];

			} else {

				// check if there is a cached value first
				$cached_merge_tag = get_post_meta( $plan_id, $this->membership_plan_merge_tag_meta_key, true );
				// merge tag set on audience has priority, which means if they differ the meta will be reset
				$list_merge_tag   = $cached_merge_tag ? $this->get_plan_merge_field_tag( $plan_id ) : null;
				$plan_merge_tag   = $list_merge_tag && $cached_merge_tag && $cached_merge_tag === $list_merge_tag ? $cached_merge_tag : null;

				// update caches
				if ( ! $plan_merge_tag ) {
					$this->set_plan_merge_field_tag_cache( $plan_id, $plan_merge_tag );
				} else {
					$this->clear_plan_merge_field_tag_cache( $plan_id );
				}
			}
		}

		if ( is_string( $merge_tag ) ) {
			$has_merge_tag = $merge_tag === $plan_merge_tag;
		} else {
			$has_merge_tag = ! empty( $plan_merge_tag );
		}

		return $has_merge_tag;
	}


	/**
	 * Generates a merge field tag for a Membership Plan.
	 *
	 * If the plan has already a merge field tag set, it won't be generated again.
	 *
	 * @since 1.0.0
	 *
	 * @param MailChimp_List $list the associated MailChimp audience
	 * @param int|string|\WC_Memberships_Membership_Plan $plan_id membership plan object, ID or slug
	 * @param string optional $merge_tag to create (defaults to plan slug if none passed)
	 */
	public function create_plan_merge_field_tag( MailChimp_List $list, $plan_id, $merge_tag = '' ) {

		$plan = $plan_id instanceof \WC_Memberships_Membership_Plan ? $plan_id : wc_memberships_get_membership_plan( $plan_id );

		if ( $plan && ! $this->plan_has_merge_field_tag( $plan ) ) {

			$plan_id = $plan->get_id();

			if ( $merge_field_id = wc_memberships_mailchimp()->get_api_instance()->create_plan_merge_field( $list->get_id(), $plan, $merge_tag ) ) {

				$list->set_plan_merge_field( $plan_id, $merge_field_id );

				if ( $found_merge_tag = $this->get_plan_merge_field_tag( $plan_id ) ) {

					$this->set_plan_merge_field_tag_cache( $plan_id, $found_merge_tag );
				}

			} else {

				$this->clear_plan_merge_field_tag_cache( $plan_id );
			}
		}
	}


	/**
	 * Generates merge tags for Membership Plans who do not have a merge field associated yet.
	 *
	 * @since 1.0.0
	 *
	 * @param MailChimp_List $list the associated Mailchimp audience
	 */
	public function create_plans_merge_field_tags( MailChimp_List $list ) {

		$plans = wc_memberships_get_membership_plans( array( 'post_status' => 'publish' ) );

		if ( ! empty( $plans ) ) {

			foreach ( $plans as $plan ) {

				// checks if a default tag has already been generated and perhaps set on MailChimp before
				$default_tag = get_post_meta( $plan->get_id(), $this->membership_plan_default_merge_tag_meta_key, true );
				$slug_tag    = MailChimp_Lists::parse_merge_tag( $plan->get_slug() );

				// if the tag no longer exists in MailChimp we can assume this has been deleted by the admin, therefore let's not push it again
				if ( $slug_tag === $default_tag && ! wc_memberships_mailchimp()->get_api_instance()->merge_field_tag_exists( MailChimp_Lists::get_current_list_id(), $default_tag ) ) {
					continue;
				}

				$this->create_plan_merge_field_tag( $list, $plan );

				// mark a default merge field created for this plan so in the future it will not bulk-created and pushed to MailChimp again
				update_post_meta( $plan->get_id(), $this->membership_plan_default_merge_tag_meta_key, $slug_tag );
			}
		}
	}


	/**
	 * Sets caches for a plan merge tag.
	 *
	 * @since 1.0.0
	 *
	 * @param int $plan_id
	 * @param string $merge_field_tag the merge field tag
	 */
	private function set_plan_merge_field_tag_cache( $plan_id, $merge_field_tag ) {

		if ( (bool) update_post_meta( $plan_id, $this->membership_plan_merge_tag_meta_key, $merge_field_tag ) ) {

			$cached_merge_tags = get_transient( MailChimp_Lists::$plans_merge_tags_transient );

			if ( is_array( $cached_merge_tags ) ) {
				$cached_merge_tags[ $plan_id ] = $merge_field_tag;
			} else {
				$cached_merge_tags = array( $plan_id => $merge_field_tag );
			}

			set_transient( MailChimp_Lists::$plans_merge_tags_transient, $cached_merge_tags, WEEK_IN_SECONDS );

			$this->membership_plans_merge_tags[ $plan_id ] = $cached_merge_tags;

		} else {

			$this->clear_plan_merge_field_tag_cache( $plan_id );
		}
	}


	/**
	 * Clears cached values of a plan merge field tag.
	 *
	 * @since 1.0.0
	 *
	 * @param int $plan_id the Membership Plan ID
	 */
	private function clear_plan_merge_field_tag_cache( $plan_id ) {

		$cached_merge_tags = get_transient( MailChimp_Lists::$plans_merge_tags_transient );

		if ( is_array( $cached_merge_tags ) && isset( $cached_merge_tags[ $plan_id ] ) ) {

			unset( $cached_merge_tags[ $plan_id ] );

			set_transient( MailChimp_Lists::$plans_merge_tags_transient, $cached_merge_tags, WEEK_IN_SECONDS );
		}

		delete_post_meta( $plan_id, $this->membership_plan_merge_tag_meta_key );

		unset( $this->membership_plans_merge_tags[ $plan_id ] );
	}


}
