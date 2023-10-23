<?php
/**
 * Badge Rule Data Store CPT
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPremium\DataStores
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Rule_Data_Store_CPT' ) ) {
	/**
	 * Badge Rule Data Store CPT Class
	 */
	class YITH_WCBM_Badge_Rule_Data_Store_CPT extends YITH_WCBM_Simple_Data_Store_CPT {

		/**
		 * Map that relates meta keys to properties for YITH_WCBM_Badge_Rule object
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_enabled'             => 'enabled',
			'_type'                => 'type',
			'_schedule'            => 'schedule',
			'_schedule_dates_from' => 'schedule_dates_from',
			'_schedule_dates_to'   => 'schedule_dates_to',
			'_exclude_products'    => 'exclude_products',
			'_excluded_products'   => 'excluded_products',
			'_show_badge_to'       => 'show_badge_to',
			'_users'               => 'users',
			'_user_roles'          => 'user_roles',
		);

		/**
		 * Map that relates meta keys to properties for the Object
		 *
		 * @var array
		 */
		protected $object_type = 'badge_rule';

		/**
		 * Map that relates meta keys to properties for the Object
		 *
		 * @var array
		 */
		protected $object_post_type = 'ywcbm-badge-rule';

		/*
		|--------------------------------------------------------------------------
		| CRUD Methods
		|--------------------------------------------------------------------------
		*/

		/**
		 * Create a new Rule in the database.
		 *
		 * @param YITH_WCBM_Badge_Rule $rule The Rule.
		 */
		public function create( &$rule ) {
			$id = parent::create( $rule );

			if ( $id && ! is_wp_error( $id ) ) {
				$this->update_associations( $rule );
			}

			return $id;
		}

		/**
		 * Read a Badge Rule data in the database.
		 *
		 * @param YITH_WCBM_Badge_Rule $rule Badge Rule object.
		 */
		public function read( &$rule ) {
			$rule->set_defaults();
			$post_object = $rule->get_id() ? wp_cache_get( 'yith_wcbm_badge_rule_' . $rule->get_id(), 'yith_wcbm_badge_rules' ) : false;

			if ( ! $post_object ) {
				$post_object = get_post( $rule->get_id() );

				if ( ! $post_object || YITH_WCBM_Post_Types_Premium::$badge_rule !== $post_object->post_type ) {
					return;
				} else {
					$rule->set_props(
						array(
							'title'  => $post_object->post_title,
							'status' => $post_object->post_status,
						)
					);
				}
				wp_cache_set( 'yith_wcbm_badge_rule_' . $rule->get_id(), $post_object, 'yith_wcbm_badge_rules' );
			}

			$rule->set_id( $post_object->ID );

			$this->read_post_meta( $rule );
			$this->read_associations( $rule );
			$rule->set_object_read( true );

			do_action( 'yith_wcbm_badge_rule_read', $rule->get_id() );
		}

		/**
		 * Update Badge Rule in the database
		 *
		 * @param YITH_WCBM_Badge_Rule $rule Badge Rule.
		 */
		public function update( &$rule ) {
			parent::update( $rule );

			$this->update_associations( $rule );
		}

		/**
		 * Delete Badge Rule in the database
		 *
		 * @param YITH_WCBM_Badge_Rule $rule Badge Rule.
		 * @param array                $args Arguments.
		 *
		 * @return bool|void
		 */
		public function delete( &$rule, $args = array() ) {
			$this->delete_associations( $rule );
		}

		/**
		 * Read Badge Rule associations
		 *
		 * @param YITH_WCBM_Badge_Rule $rule Badge Rule.
		 */
		public function read_associations( &$rule ) {
			$associations = wp_cache_get( 'yith_wcbm_badge_rule_associations_' . $rule->get_id(), 'yith_wcbm_badge_rule_associations' );
			if ( false === $associations ) {
				global $wpdb;
				$table = $wpdb->prefix . YITH_WCBM_DB::BADGE_RULES_ASSOCIATIONS_TABLE;

				$associations = $wpdb->get_results( $wpdb->prepare( "SELECT value, badge_id FROM $table WHERE rule_id = %d", $rule->get_id() ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				wp_cache_set( 'yith_wcbm_badge_rule_associations_' . $rule->get_id(), $associations, 'yith_wcbm_badge_rule_associations' );
			}

			return $associations;
		}

		/**
		 * Delete Badge Rule Associations from DB
		 *
		 * @param YITH_WCBM_Badge_Rule $rule Badge Rule.
		 */
		public function delete_associations( $rule ) {
			global $wpdb;
			$wpdb->delete( YITH_WCBM_DB::get_badge_rules_table_name(), array( 'rule_id' => $rule->get_id() ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			wp_cache_delete( 'yith_wcbm_badge_rule_associations_' . $rule->get_id(), 'yith_wcbm_badge_rule_associations' );
		}

		/**
		 * Update Associations Table
		 *
		 * @param YITH_WCBM_Badge_Rule $rule Badge Rule.
		 */
		public function update_associations( $rule ) {
			$this->delete_associations( $rule );
			global $wpdb;
			$table  = YITH_WCBM_DB::get_badge_rules_table_name();
			$format = array(
				'rule_id'  => '%d',
				'type'     => '%s',
				'value'    => '%s',
				'badge_id' => '%d',
				'enabled'  => '%d',
			);
			foreach ( $rule->get_associations_rows() as $association_row ) {
				$wpdb->insert( $table, $association_row, array_replace( $association_row, $format ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			}

			return false;
		}
	}
}
