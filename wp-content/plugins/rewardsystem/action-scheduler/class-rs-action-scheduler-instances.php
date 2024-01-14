<?php

/**
 * Action Scheduler Instances Class.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Action_Scheduler_Instances' ) ) {

	/**
	 * Class RS_Action_Scheduler_Instances
	 */
	class RS_Action_Scheduler_Instances {

		/**
		 * Action Schedulers.
		 * 
		 * @var array
		 * */
		private static $action_schedulers = array();

		/**
		 * Get action schedulers.
		 * 
		 * @var array
		 */
		public static function instance() {

			if ( ! self::$action_schedulers ) {
				self::load_action_schedulers();
			}

			return self::$action_schedulers;
		}

		/**
		 * Load action schedulers.
		 */
		public static function load_action_schedulers() {

			if ( ! class_exists( 'SRP_Action_Scheduler' ) ) {
				include_once SRP_PLUGIN_PATH . '/includes/abstract/abstract-srp-action-scheduler.php';
			}

			$action_scheduler_classes = array(
				'rs-apply-previous-order-points'          => 'RS_Apply_Previous_Order_Points',
				'rs-manually-add-points'                  => 'RS_Manually_Add_Points',
				'rs-manually-remove-points'               => 'RS_Manually_Remove_Points',
				'rs-imp-exp-module-export-points'         => 'RS_Imp_Exp_Module_Export_Points',
				'rs-import-points-data'                   => 'RS_Import_Points_Data',
				'rs-generate-gift-voucher'                => 'RS_Generate_Gift_Voucher',
				'rs-buying-points-bulk-update-action'     => 'RS_Buying_Points_Bulk_Update_Action',
				'rs-master-log-export-points'             => 'RS_Master_Log_Export_Points',
				'rs-reports-module-export-points'         => 'RS_Reports_Module_Export_Points',
				'rs-product-purchase-bulk-update-action'  => 'RS_Product_Purchase_Bulk_Update_Action',
				'rs-point-price-bulk-update-action'       => 'RS_Point_Price_Bulk_Update_Action',
				'rs-referral-purchase-bulk-update-action' => 'RS_Referral_Purchase_Bulk_Update_Action',
				'rs-social-points-bulk-update-action'     => 'RS_Social_Points_Bulk_Update_Action',
				'rs-add-user-old-available-points'        => 'RS_Add_User_Old_Available_Points',
				'rs-update-expired-points-action'         => 'RS_Update_Expired_Points_Action',
				'rs-update-earned-points-action'          => 'RS_Update_Earned_Points_Action',
				'rs-redeeming-points-bulk-update-action'  => 'RS_Redeeming_Points_Bulk_Update_Action',
			);

			foreach ( $action_scheduler_classes as $file_name => $class_name ) {

				// Include file.
				include 'class-' . $file_name . '.php';

				// Add action scheduler class.
				self::add_action_scheduler_class( new $class_name() );
			}
		}

		/**
		 * Add action scheduler class.
		 */
		public static function add_action_scheduler_class( $object ) {
			self::$action_schedulers[ $object->get_id() ] = $object;
			return new self();
		}

		/**
		 * Get action scheduler by id.
		 * 
		 * @var Object
		 */
		public static function get_action_scheduler_by_id( $action_scheduler_id ) {
			$action_schedulers = self::instance();
			return isset( $action_schedulers[ $action_scheduler_id ] ) ? $action_schedulers[ $action_scheduler_id ] : false;
		}
	}

}
