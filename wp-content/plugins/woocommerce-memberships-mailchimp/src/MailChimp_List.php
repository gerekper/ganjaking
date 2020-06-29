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
 * MailChimp audience list object normalized for MailChimp Sync context.
 *
 * @since 1.0.0
 */
class MailChimp_List {


	/** @var string the audience ID */
	private $id;

	/** @var array saved settings for the audience (associative array) */
	private $settings;

	/** @var string the audience name */
	private $name = '';

	/** @var array associative array of interests */
	private $interest_categories = array();

	/** @var array associative array of merge tags */
	private $merge_fields = array();


	/**
	 * MailChimp audience list constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the audience ID
	 */
	public function __construct( $list_id ) {

		$this->id       = $list_id;
		$this->settings = $this->get_settings();
	}


	/**
	 * Returns the audience ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string an alphanumeric string
	 */
	public function get_id() {

		return $this->id;
	}


	/**
	 * Returns the saved settings for the audience.
	 *
	 * @since 1.0.0
	 *
	 * @return array associative array of settings
	 */
	public function get_settings() {

		if ( empty( $this->settings ) || ! is_array( $this->settings ) ) {

			$lists_settings = MailChimp_Lists::get_lists_settings();

			$this->settings = isset( $lists_settings[ $this->id ] ) ? $lists_settings[ $this->id ] : array();
		}

		return $this->settings;
	}


	/**
	 * Updates saved preferences for the audience.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data
	 */
	public function update_settings( array $data ) {

		MailChimp_Lists::update_list_settings( $this->id, $data );

		$this->settings = $this->get_settings();
	}


	/**
	 * Deletes saved preferences for the audience.
	 *
	 * @since 1.0.0
	 */
	public function delete_settings() {

		$this->update_settings( array() );
	}


	/**
	 * Returns the audience name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {

		if ( empty( $this->name ) ) {

			$transient       = get_transient( MailChimp_Lists::$names_transient );
			$list_name_cache = is_array( $transient ) ? $transient : array();
			$update_cache    = false;

			if ( isset( $list_name_cache[ $this->id ] ) ) {
				$list_name    = $list_name_cache[ $this->id ];
			} else {
				$list         = wc_memberships_mailchimp()->get_api_instance()->get_list( $this->id );
				$list_name    = isset( $list, $list->name ) ? $list->name : '';
				$update_cache = true;
			}

			$this->name = $list_name;

			if ( $update_cache ) {

				$list_name_cache[ $this->id ] = $this->name;

				set_transient( MailChimp_Lists::$names_transient, $list_name_cache, WEEK_IN_SECONDS );
			}
		}

		return $this->name;
	}


	/**
	 * Returns the audience interests.
	 *
	 * @since 1.0.0
	 *
	 * @return array associative array
	 */
	public function get_interests() {

		$interest_categories = array();

		if ( ! empty( $this->interest_categories ) ) {

			$interest_categories = $this->interest_categories;

		} else {

			$categories_cache  = get_transient( MailChimp_Lists::$interest_categories_transient );
			$categories_cache  = is_array( $categories_cache ) ? $categories_cache : array();
			$interests_cache   = get_transient( MailChimp_Lists::$interests_transient );
			$interests_cache   = is_array( $interests_cache )  ? $interests_cache  : array();
			$update_transients = ! empty( $categories_cache ) && ! empty( $interests_cache );

			if ( isset( $categories_cache[ $this->id ] ) ) {
				$categories                    = $categories_cache[ $this->id ];
			} else {
				$categories                    = wc_memberships_mailchimp()->get_api_instance()->get_list_interest_categories( $this->id );
				$categories_cache[ $this->id ] = $categories;
				$update_transients             = true;
			}

			foreach ( $categories as $category ) {

				if ( isset( $category->id, $category->title ) ) {

					$interest_categories[ $category->id ] = array(
						'id'        => $category->id,
						'name'      => $category->title,
						'interests' => array()
					);

					if ( isset( $interests_cache[ $this->id ][ $category->id ] ) ) {
						$interests                                     = $interests_cache[ $this->id ][ $category->id ];
					} else {
						$interests                                     = wc_memberships_mailchimp()->get_api_instance()->get_list_interests_by_category( $this->id, $category->id );
						$interests_cache[ $this->id ][ $category->id ] = $interests;
						$update_transients                             = true;
					}

					if ( ! empty( $interests ) ) {

						foreach ( $interests as $interest ) {

							if ( isset( $interest->id, $interest->name ) ) {

								$interest_categories[ $category->id ]['interests'][] = array(
									'id'     => $interest->id,
									'name'   => $interest->name,
									// important: keep this check loose
									'chosen' => in_array( $interest->id, $this->get_chosen_interests( $category->id ), false )
								);
							}
						}

					} else {

						unset( $interest_categories[ $category->id ] );
					}
				}
			}

			$this->interest_categories = $interest_categories;

			if ( $update_transients ) {
				set_transient( MailChimp_Lists::$interest_categories_transient, $categories_cache, WEEK_IN_SECONDS );
				set_transient( MailChimp_Lists::$interests_transient,           $interests_cache,  WEEK_IN_SECONDS );
			}
		}

		return $interest_categories;
	}


	/**
	 * Returns the chosen interests.
	 *
	 * @since 1.0.0
	 *
	 * @param null|string $interest_category interest category ID, or default null to return all interests by category
	 * @return array
	 */
	public function get_chosen_interests( $interest_category = null ) {

		$interests = array();
		$settings  = $this->get_settings();

		if ( ! empty( $settings ) && ! empty( $settings['interests'] ) && is_array( $settings['interests'] ) ) {
			$interests = $settings['interests'];
		}

		if ( null !== $interest_category ) {
			$interests = isset( $interests[ $interest_category ] ) ? $interests[ $interest_category ] : array();
		}

		return $interests;
	}


	/**
	 * Returns the audience merge tags preferences.
	 *
	 * @since 1.0.0
	 *
	 * @return array associative array
	 */
	public function get_merge_fields() {

		$merge_fields = array();

		if ( ! empty( $this->merge_fields ) ) {

			$merge_fields = $this->merge_fields;

		} else {

			$fields_cache = get_transient( MailChimp_Lists::$merge_fields_transient );
			$fields_cache = is_array( $fields_cache ) ? $fields_cache : array();
			$update_cache = ! empty( $fields_cache );

			if ( isset( $fields_cache[ $this->id ] ) ) {
				$fields       = $fields_cache[ $this->id ];
			} else {
				// limits the fields to text types so we don't have to deal with other field inputs (e.g. dates)
				$fields       = wc_memberships_mailchimp()->get_api_instance()->get_list_merge_fields( $this->id, array( 'type' => 'text' ) );
				$update_cache = true;
			}

			if ( ! empty( $fields ) ) {

				$merge_fields['status']['active'] = array(
					'id'           => 'active',
					'name'         =>  __( 'Active Status', 'woocommerce-memberships-mailchimp' ),
					'merge_fields' => $this->parse_merge_fields( 'status', 'active', $fields ),
				);

				$plans = wc_memberships_get_membership_plans();

				if ( ! empty( $plans ) ) {

					foreach ( $plans as $plan ) {

						$plan_id = $plan->get_id();

						$merge_fields['plans'][ $plan_id ] = array(
							'id'           => $plan_id,
							'name'         => $plan->get_name(),
							'merge_fields' => $this->parse_merge_fields( 'plans', $plan_id, $fields ),
						);
					}
				}
			}

			$this->merge_fields = $merge_fields;

			if ( $update_cache ) {
				set_transient( MailChimp_Lists::$merge_fields_transient, $fields, WEEK_IN_SECONDS );
			}
		}

		return $merge_fields;
	}


	/**
	 * Parses merge fields and gathers chosen status (helper method).
	 *
	 * @since 1.0.0
	 *
	 * @param string $group group of merge fields
	 * @param string $which specific field ID
	 * @param array $fields raw merge fields
	 * @return array associative arrays
	 */
	private function parse_merge_fields( $group, $which, array $fields ) {

		$parsed_fields = array();

		foreach ( $fields as $field ) {

			if ( isset( $field->merge_id, $field->tag ) ) {

				$parsed_fields[ $field->merge_id ] = array(
					'id'     => $field->merge_id,
					'tag'    => $field->tag,
					// important: keep this check loose
					'chosen' => $field->merge_id == $this->get_chosen_merge_fields( $group, $which ),
				);
			}
		}

		return $parsed_fields;
	}


	/**
	 * Returns the chosen merge tags.
	 *
	 * @since 1.0.0
	 *
	 * @param null|string $group group to retrieve merge fields for, default null to return all
	 * @param null|int $id optional ID of the plan or 0 for active status merge tag, null to return all
	 * @return string|array will return a single int|string when querying a single item, or array for a list of items
	 */
	public function get_chosen_merge_fields( $group = null, $id = null ) {

		$merge_fields = array();
		$settings     = $this->get_settings();

		if ( ! empty( $settings ) && isset( $settings['merge_fields'] ) && is_array( $settings['merge_fields'] ) ) {
			$merge_fields = $settings['merge_fields'];
		}

		if ( null !== $group ) {

			$merge_fields = isset( $merge_fields[ $group ] ) ? $merge_fields[ $group ] : array();

			if ( null !== $id ) {
				$merge_fields = isset( $merge_fields[ $id ] ) ? $merge_fields[ $id ] : '';
			}
		}

		return $merge_fields;
	}


	/**
	 * Sets or removes a merge field ID for a status or a plan.
	 *
	 * @since 1.0.0
	 *
	 * @param string $which which group: either 'status' or 'plans'
	 * @param int|string $object_id the object ID to set a merge field for (e.g. plan ID or status)
	 * @param int|string $merge_field_id the chosen merge field ID, normally an integer
	 */
	private function set_merge_field( $which, $object_id, $merge_field_id ) {

		$settings = $this->get_settings();

		if ( ! empty( $merge_field_id ) && ( is_string( $merge_field_id ) || is_numeric( $merge_field_id ) ) ) {

			$settings['merge_fields'][ $which ][ $object_id ] = $merge_field_id;

		} elseif ( isset( $settings['merge_fields'][ $which ][ $object_id ] ) ) {

			unset( $settings['merge_fields'][ $which ][ $object_id ] );
		}

		$this->update_settings( $settings );
	}


	/**
	 * Returns a plan's chosen merge field ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $plan_id the Membership Plan ID
	 * @return string|int this is expected to be a number from the MailChimp API
	 */
	public function get_plan_merge_field_id( $plan_id ) {

		return $this->get_chosen_merge_fields( 'plans', $plan_id );
	}


	/**
	 * Returns a plan's chosen merge field.
	 *
	 * @since 1.0.0
	 *
	 * @param int $plan_id
	 * @return null|array associative array with merge field id and tag name or null if not set
	 */
	public function get_plan_merge_field( $plan_id ) {

		$plan_merge_field = null;
		$merge_field_id   = $this->get_plan_merge_field_id( $plan_id );

		if ( ! empty( $merge_field_id ) ) {

			$merge_fields = $this->get_merge_fields();

			if ( isset( $merge_fields['plans'][ $plan_id ]['merge_fields'] ) && is_array( $merge_fields['plans'][ $plan_id ]['merge_fields'] ) ) {

				foreach ( $merge_fields['plans'][ $plan_id ]['merge_fields'] as $merge_field_data ) {

					if ( ! empty( $merge_field_data['chosen'] ) ) {

						$plan_merge_field = array( $merge_field_data['id'] => $merge_field_data['tag'] );
						break;
					}
				}
			}
		}

		return $plan_merge_field;
	}


	/**
	 * Matches a merge field to a plan.
	 *
	 * @since 1.0.0
	 *
	 * @param int $plan_id the membership plan ID
	 * @param int|string $merge_field_id the merge field ID (usually an integer), empty to remove
	 */
	public function set_plan_merge_field( $plan_id, $merge_field_id ) {

		$this->set_merge_field( 'plans', $plan_id, $merge_field_id );
	}


	/**
	 * Returns the active status chosen merge field ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string|int this is expected to be a number from the MailChimp API
	 */
	public function get_active_status_merge_field_id() {

		return $this->get_chosen_merge_fields( 'status', 'active' );
	}


	/**
	 * Returns the active status chosen merge field.
	 *
	 * @since 1.0.0
	 *
	 * @return null|array associative array with merge field id and tag name or null if not set
	 */
	public function get_active_status_merge_field() {

		$active_status_merge_field = null;
		$merge_field_id            = $this->get_active_status_merge_field_id();

		if ( ! empty( $merge_field_id ) ) {

			$merge_fields = $this->get_merge_fields();

			if ( isset( $merge_fields['status']['active']['merge_fields'] ) && is_array( $merge_fields['status']['active']['merge_fields'] ) ) {

				foreach ( $merge_fields['status']['active']['merge_fields'] as $merge_field ) {

					if ( ! empty( $merge_field['chosen'] ) ) {

						$active_status_merge_field = array( $merge_field['id'] => $merge_field['tag'] );
						break;
					}
				}
			}
		}

		return $active_status_merge_field;
	}


	/**
	 * Checks whether an active status merge field has been assigned.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_active_status_merge_field() {

		$active_merge_field = $this->get_active_status_merge_field_id();

		return ! empty( $active_merge_field );
	}


	/**
	 * Matches a merge field to a plan.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $merge_field_id the merge field ID (usually an integer), empty to remove
	 */
	public function set_active_status_merge_field( $merge_field_id ) {

		$this->set_merge_field( 'status', 'active', $merge_field_id );
	}


	/**
	 * Sets a default active status merge field.

	 * Will only perform a single attempt per audience.
	 * If the default audience is changed, it won't work again, since the tag already exists in MailChimp.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force if the default active status merge field is found but is not assigned to the active status, force this setting
	 */
	public function set_default_active_status_merge_field( $force = false ) {

		$settings       = $this->get_settings();
		$default_tag    = MailChimp_Lists::get_default_is_active_tag();
		$tag_exists     = ! empty( $settings['default_active_status_merge_field_set'] );
		$merge_field_id = false;

		if ( ! $tag_exists ) {

			$merge_data = $this->get_merge_fields();

			if ( ! empty( $merge_data ) ) {

				foreach ( $merge_data as $group ) {

					foreach ( (array) $group as $item ) {

						if ( isset( $item['merge_fields'] ) && is_array( $item['merge_fields'] ) ) {

							foreach ( $item['merge_fields'] as $merge_field ) {

								if ( isset( $merge_field['tag'] ) && $default_tag === $merge_field['tag'] ) {

									$merge_field_id = isset( $merge_field['id'] ) ? $merge_field['id'] : null;
									$tag_exists     = true;
									break;
								}
							}
						}
					}
				}
			}
		}

		if ( ! $tag_exists ) {

			$merge_field_id = wc_memberships_mailchimp()->get_api_instance()->create_status_merge_field( $this->get_id(), 'active', MailChimp_Lists::get_default_is_active_tag() );

			if ( false !== $merge_field_id ) {

				$this->set_active_status_merge_field( $merge_field_id );
			}

		} elseif ( $merge_field_id && $force ) {

			$this->set_active_status_merge_field( $merge_field_id );
		}

		$settings['default_active_status_merge_field_set'] = true;

		$this->update_settings( $settings );
	}


	/**
	 * Returns member data to be used for audience syncing.
	 *
	 * @see API::sync_list_member()
	 * @see API::sync_list_members()
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_User $user the user member to generate audience data for
	 * @return array associative array of member data
	 */
	public function get_member_data( \WP_User $user ) {

		$member_data = MailChimp_Lists::get_list_default_member_data( $user );
		$subscribed  = false;
		$is_active   = false;

		// loop through all of the configured plan merge fields and set their status
		foreach ( (array) $this->get_chosen_merge_fields( 'plans' ) as $plan_id => $merge_field_id ) {

			$plan_merge_field = $this->get_plan_merge_field( $plan_id );

			if ( ! empty( $plan_merge_field ) && is_array( $plan_merge_field ) ) {

				$plan_merge_field = current( $plan_merge_field );

				if ( $user_membership = wc_memberships_get_user_membership( $user->ID, $plan_id ) ) {

					$status           = $user_membership->get_status();
					$plan_merge_value = empty( $status ) ? 'active' : $status;

					$subscribed = true;

					// if at least one plan has an active status, this will be kept to true
					if ( in_array( $status, wc_memberships()->get_user_memberships_instance()->get_active_access_membership_statuses(), true ) ) {
						$is_active = true;
					}

				} else {

					$plan_merge_value = '';
				}

				$member_data['merge_fields'][ $plan_merge_field ] = $plan_merge_value;
			}
		}

		// set the Is Active field value
		if ( $active_field = $this->get_active_status_merge_field() ) {

			$active_field = current( $active_field );

			$member_data['merge_fields'][ $active_field ] = $is_active ? 'yes' : 'no';
		}

		// set the subscription status
		$member_data['status'] = $subscribed ? 'subscribed' : 'unsubscribed';

		$interests              = array();
		$interest_by_categories = $this->get_interests();

		// add or remove optional interests:
		if ( ! empty( $interest_by_categories ) && is_array( $interest_by_categories ) ) {

			foreach ( $interest_by_categories as $interest_category ) {

				foreach ( (array) $interest_category['interests'] as $interest ) {

					if ( ! empty( $interest['chosen'] ) ) {
						$interests[ $interest['id'] ] = true;
					}
				}
			}

			// only sent interests if we have interests to send
			if ( ! empty( $interests ) ) {
				$member_data['interests'] = $interests;
			}
		}

		/**
		 * Filters the member data used for syncing a user memberships with MailChimp.
		 *
		 * @since 1.0.0
		 *
		 * @param array $member_data associative array
		 * @param \WP_User $user a member user object
		 * @param \SkyVerge\WooCommerce\Memberships\MailChimp\MailChimp_List $list a MailChimp Sync normalized audience object
		 */
		return (array) apply_filters( 'wc_memberships_mailchimp_list_member_data', $member_data, $user, $this );
	}


	/**
	 * Returns the handling setting for deleted user memberships.
	 *
	 * @since 1.0.0
	 *
	 * @return string either 'unsubscribe', 'keep' (clears merge fields) or 'remove' (delete) from audience
	 */
	public function get_deleted_memberships_handling() {

		$default_handling = 'unsubscribe';
		$settings         = $this->get_settings();

		return isset( $settings['deleted_memberships'] ) && in_array( $settings['deleted_memberships'], array( 'unsubscribe', 'remove', 'keep' ), true ) ? $settings['deleted_memberships'] : $default_handling;
	}


	/**
	 * Checks which is the current handling for deleted memberships.
	 *
	 * @since 1.0.0
	 *
	 * @param string|string[] $handling 'unsubscribe', 'keep' or 'remove' or any of these
	 * @return bool
	 */
	public function is_deleted_memberships_handling( $handling ) {

		return is_array( $handling ) ? in_array( $this->get_deleted_memberships_handling(), $handling, true ) : $handling === $this->get_deleted_memberships_handling();
	}


	/**
	 * Returns the handling setting for deleted membership plans merge fields.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_deleted_plan_merge_field_handling() {

		$default_handling = 'keep';
		$settings         = $this->get_settings();

		return isset( $settings['deleted_plan_merge_field'] ) && in_array( $settings['deleted_plan_merge_field'], array( 'keep', 'delete' ), true ) ? $settings['deleted_plan_merge_field'] : $default_handling;
	}


	/**
	 * Checks which is the current handling for deleted membership plans merge fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string $handling
	 * @return bool
	 */
	public function is_deleted_plan_merge_field_handling( $handling ) {

		return $handling === $this->get_deleted_plan_merge_field_handling();
	}


}
