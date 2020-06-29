<?php

if ( ! function_exists( 'yith_wapo_get_addons_by_group_id' ) ) {
	/**
	 * Get add-ons by group id
	 * This function return an array with related add-ons.
	 *
	 * @since 1.5.0
	 * @author Your Inspiration Themes
	 */
	function yith_wapo_get_addons_by_group_id( $group_id ) {
		global $wpdb;
		$addons_table_name = YITH_WAPO_Type::$table_name;
		$results = $wpdb->get_results( "SELECT id FROM {$wpdb->prefix}$addons_table_name WHERE group_id='$group_id' AND del='0'" );
		$addons_array = array();
		foreach ( $results as $key => $value ) {
			$addons_array[$value->id] = new YITH_WAPO_Type( $value->id );
		}
		return $addons_array;
	}
}

if ( ! function_exists( 'yith_wapo_get_addons_number_by_group_id' ) ) {
	/**
	 * Get the add-ons number by group id
	 * This function return the number of all add-ons related the group id.
	 *
	 * @since 1.5.0
	 * @author Your Inspiration Themes
	 */
	function yith_wapo_get_addons_number_by_group_id( $group_id ) {
		global $wpdb;
		$addons_table_name = YITH_WAPO_Type::$table_name;
		return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}$addons_table_name WHERE group_id='$group_id' AND del='0'" );
	}
}
