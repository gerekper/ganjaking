<?php

/**
 * Class YWSBS_Helper_Points_Product.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class YWSBS_Helper_Points_table {
	/**
	 * Update date earning
	 *
	 * @param $user_id
	 * @param $day_off
	 * @param int $limit
	 *
	 * @return bool|int
	 */
	public static function update_date_to_the_last( $user_id, $day_off, $limit = 1) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'yith_ywpar_points_log';
		$date        = date( "Y-m-d H:i:s", mktime(0, 0, 0, date("m")  , date("d")- $day_off, date("Y")) );

		$q = "UPDATE $table_name SET `date_earning`='$date' WHERE `user_id`=$user_id ORDER BY id DESC LIMIT $limit";
		$result = $wpdb->query( $q );
		return $result;
	}


}
