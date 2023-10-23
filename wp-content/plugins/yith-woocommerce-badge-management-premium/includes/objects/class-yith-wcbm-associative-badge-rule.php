<?php
/**
 * Associative Badge Rule class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Objects
 * @since   2.0
 */

if ( ! class_exists( 'YITH_WCBM_Associative_Badge_Rule' ) ) {
	/**
	 * Associative Badge Rule Class
	 */
	abstract class YITH_WCBM_Associative_Badge_Rule extends YITH_WCBM_Badge_Rule {

		/**
		 * Stores Badge Rule data.
		 *
		 * @var array
		 */
		protected $data = array(
			'title'               => '',
			'status'              => 'publish',
			'enabled'             => 'yes',
			'type'                => '',
			'schedule'            => 'no',
			'schedule_dates_from' => 0,
			'schedule_dates_to'   => 0,
			'exclude_products'    => 'no',
			'excluded_products'   => array(),
			'show_badge_to'       => 'all-users',
			'users'               => array(),
			'user_roles'          => array(),
			'associations'        => array(),
		);

		/**
		 * Meta to props.
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
			'_associations'        => 'associations',
		);

		/**
		 * Data Store object type.
		 *
		 * @var string
		 */
		protected $data_store_object_type = 'associative_badge_rule';

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from object.
		|
		*/

		/**
		 * Get association
		 *
		 * @param string $context Context.
		 *
		 * @return array
		 */
		public function get_associations( $context = 'view' ) {
			return $this->get_prop( 'associations', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		|
		| Methods for setting data from object.
		|
		*/

		/**
		 * Set the association property value
		 *
		 * @param array $value badge value.
		 */
		public function set_associations( $value ) {
			$this->set_prop( 'associations', $this->sanitize_associations( $value ) );
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		|
		| Checks if a condition is true or false.
		|
		*/

		/**
		 * Get the Associations IDS of the Rules
		 *
		 * @return array
		 */
		public function get_rules_associations_ids() {
			return array_map( 'absint', array_column( $this->get_associations(), 'association' ) );
		}

		/**
		 * Sanitize Associations
		 *
		 * @param array $associations Associations.
		 *
		 * @return array
		 */
		public function sanitize_associations( $associations ) {
			$sanitized_associations = array();
			foreach ( $associations as $association ) {
				$badge                    = absint( $association['badge'] );
				$association              = absint( $association['association'] );
				$sanitized_associations[] = compact( 'association', 'badge' );
			}

			return array_unique( $sanitized_associations, 0 );
		}


		/**
		 * Get Data to store in Associations table
		 *
		 * @return array
		 */
		public function get_associations_rows() {
			$rule_rows    = array();
			$associations = $this->get_associations();
			foreach ( $associations as $association ) {
				if ( ! empty( $association['association'] ) && ! empty( $association['badge'] ) ) {
					$rule_rows[] = array(
						'rule_id'  => $this->get_id(),
						'type'     => $this->get_type(),
						'value'    => $association['association'],
						'badge_id' => $association['badge'],
						'enabled'  => absint( $this->is_enabled() ),
					);
				}
			}

			return $rule_rows;
		}
	}
}
