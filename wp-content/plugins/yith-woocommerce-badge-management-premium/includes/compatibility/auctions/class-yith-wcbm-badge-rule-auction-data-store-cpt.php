<?php
/**
 * Auction Badge Rule Data Store CPT
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPremium\Compatibility
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Rule_Auction_Data_Store_CPT' ) ) {
	/**
	 * Badge Rule Data Store CPT Class
	 */
	class YITH_WCBM_Badge_Rule_Auction_Data_Store_CPT extends YITH_WCBM_Badge_Rule_Data_Store_CPT {

		/**
		 * Map that relates meta keys to properties for YITH_WCBM_Badge_Rule_Product object
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
		 * Read Associations from DB and set them to Badge Rule Object
		 *
		 * @param YITH_WCBM_Badge_Rule_Auction $rule Badge Rule.
		 */
		public function read_associations( &$rule ) {
			$associations = parent::read_associations( $rule );
			foreach ( $associations as $association ) {
				$db_value_to_prop = array(
					'badge-auction-not-started' => 'badge_auction_not_started',
					'badge-auction-started'     => 'badge_auction_started',
					'badge-auction-finished'    => 'badge_auction_finished',
				);
				if ( array_key_exists( $association->value, $db_value_to_prop ) ) {
					$setter = 'set_' . $db_value_to_prop[ $association->value ];
					if ( method_exists( $rule, $setter ) ) {
						$rule->{$setter}( $association->badge_id );
					}
				}
			}
		}
	}
}
