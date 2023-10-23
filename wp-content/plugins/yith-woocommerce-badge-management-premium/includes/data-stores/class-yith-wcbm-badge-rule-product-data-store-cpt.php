<?php
/**
 * Produtc Badge Rule Data Store CPT
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPremium\DataStores
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Rule_Product_Data_Store_CPT' ) ) {
	/**
	 * Badge Rule Data Store CPT Class
	 */
	class YITH_WCBM_Badge_Rule_Product_Data_Store_CPT extends YITH_WCBM_Badge_Rule_Data_Store_CPT {

		/**
		 * Map that relates meta keys to properties for YITH_WCBM_Badge_Rule_Product object
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_enabled'              => 'enabled',
			'_type'                 => 'type',
			'_schedule'             => 'schedule',
			'_schedule_dates_from'  => 'schedule_dates_from',
			'_schedule_dates_to'    => 'schedule_dates_to',
			'_exclude_products'     => 'exclude_products',
			'_excluded_products'    => 'excluded_products',
			'_show_badge_to'        => 'show_badge_to',
			'_users'                => 'users',
			'_user_roles'           => 'user_roles',
			'_low_stock_quantity'   => 'low_stock_quantity',
			'_newer_then'           => 'newer_then',
			'_bestsellers_quantity' => 'bestsellers_quantity',
		);

		/**
		 * Read Associations from DB and set them to Badge Rule Object
		 *
		 * @param YITH_WCBM_Badge_Rule_Product $rule Badge Rule.
		 */
		public function read_associations( &$rule ) {
			$association = current( parent::read_associations( $rule ) );

			if ( $association ) {
				$rule->set_assign_to( $association->value );
				$rule->set_badge( $association->badge_id );
			}
		}
	}
}
