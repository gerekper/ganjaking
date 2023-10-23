<?php
/**
 * Auction Badge Rule class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Compatibility
 * @since   2.0
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Rule_Auction' ) ) {
	/**
	 * Badge Rule Class
	 */
	class YITH_WCBM_Badge_Rule_Auction extends YITH_WCBM_Badge_Rule {

		/**
		 * Badge rule object type
		 *
		 * @var string
		 */
		protected $badge_rule_type = 'auction';

		/**
		 * Stores Badge Rule data.
		 *
		 * @var array
		 */
		protected $data = array(
			'title'                     => '',
			'status'                    => 'publish',
			'enabled'                   => 'yes',
			'type'                      => '',
			'schedule'                  => 'no',
			'schedule_dates_from'       => 0,
			'schedule_dates_to'         => 0,
			'exclude_products'          => 'no',
			'excluded_products'         => array(),
			'show_badge_to'             => 'all-users',
			'users'                     => array(),
			'user_roles'                => array(),
			'badge_auction_not_started' => 0,
			'badge_auction_started'     => 0,
			'badge_auction_finished'    => 0,
		);

		/**
		 * Meta to props.
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_enabled'                   => 'enabled',
			'_type'                      => 'type',
			'_schedule'                  => 'schedule',
			'_schedule_dates_from'       => 'schedule_dates_from',
			'_schedule_dates_to'         => 'schedule_dates_to',
			'_exclude_products'          => 'exclude_products',
			'_excluded_products'         => 'excluded_products',
			'_show_badge_to'             => 'show_badge_to',
			'_users'                     => 'users',
			'_user_roles'                => 'user_roles',
			'_badge'                     => 'badge',
			'_assign_to'                 => 'assign_to',
			'_newer_then'                => 'newer_then',
			'_low_stock_quantity'        => 'low_stock_quantity',
			'_badge_auction_not_started' => 'badge_auction_not_started',
			'_badge_auction_started'     => 'badge_auction_started',
			'_badge_auction_finished'    => 'badge_auction_finished',
		);

		/**
		 * Data Store object type.
		 *
		 * @var string
		 */
		protected $data_store_object_type = 'badge_rule_auction';

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from object.
		|
		*/

		/**
		 * Get badge_auction_not_started
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_badge_auction_not_started( $context = 'view' ) {
			return $this->get_prop( 'badge_auction_not_started', $context );
		}

		/**
		 * Get badge_auction_started
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_badge_auction_started( $context = 'view' ) {
			return $this->get_prop( 'badge_auction_started', $context );
		}

		/**
		 * Get badge_auction_finished
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_badge_auction_finished( $context = 'view' ) {
			return $this->get_prop( 'badge_auction_finished', $context );
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
		 * Set the badge_auction_not_started property value
		 *
		 * @param int $value badge value.
		 */
		public function set_badge_auction_not_started( $value ) {
			$this->set_prop( 'badge_auction_not_started', absint( $value ) );
		}

		/**
		 * Set the badge_auction_started property value
		 *
		 * @param int $value badge value.
		 */
		public function set_badge_auction_started( $value ) {
			$this->set_prop( 'badge_auction_started', absint( $value ) );
		}

		/**
		 * Set the badge_auction_finished property value
		 *
		 * @param int $value badge value.
		 */
		public function set_badge_auction_finished( $value ) {
			$this->set_prop( 'badge_auction_finished', absint( $value ) );
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
		 * Check if the badge rule is valid for the Product
		 *
		 * @param int $product_id User ID.
		 *
		 * @return bool
		 */
		public function is_valid_for_product( $product_id = 0 ) {
			$valid   = false;
			$product = $product_id ? wc_get_product( $product_id ) : wc_get_product();

			if ( $product && $product->is_type( 'auction' ) && ! $this->is_product_excluded( $product->get_id() ) ) {
				$valid = true;
			}

			return $valid;
		}

		/**
		 * Get Data to store in Associations table
		 *
		 * @return array
		 */
		public function get_associations_rows() {
			$default_data = array(
				'rule_id' => $this->get_id(),
				'type'    => $this->get_type(),
				'enabled' => absint( $this->is_enabled() ),
			);
			$associations = array(
				array(
					'value'    => 'badge-auction-not-started',
					'badge_id' => $this->get_badge_auction_not_started(),
				),
				array(
					'value'    => 'badge-auction-started',
					'badge_id' => $this->get_badge_auction_started(),
				),
				array(
					'value'    => 'badge-auction-finished',
					'badge_id' => $this->get_badge_auction_finished(),
				),
			);

			foreach ( $associations as &$association ) {
				$association = array_merge( $association, $default_data );
			}

			return $associations;
		}

		/**
		 * Get badge for product
		 *
		 * @param int $product_id The product ID.
		 */
		public function get_badge_for_product( $product_id ) {
			$badge   = false;
			$product = wc_get_product( $product_id );
			if ( $product && $product->is_type( 'auction' ) ) {
				switch ( $product->get_auction_status() ) {
					case 'non-started':
						$badge = $this->get_badge_auction_not_started();
						break;
					case 'started':
					case 'started-reached-reserve':
						$badge = $this->get_badge_auction_started();
						break;
					case 'finished':
					case 'finished-reached-reserve':
						$badge = $this->get_badge_auction_finished();
						break;
				}
			}

			return $badge;
		}
	}
}
