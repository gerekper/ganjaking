<?php

/**
 * WooCommerce API Manager Grace Period Data Store Interface
 *
 * @since       2.6
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Grace Period Data Store
 */
interface WCAM_Grace_Period_Data_Store_Interface {

	/**
	 * Adds a Grace Period expiration time for an API Resource.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 * @param int $expires
	 *
	 * @return bool
	 */
	public function insert( $api_resource_id, $expires );

	/**
	 * Updates a Grace Period expiration time for an API Resource if it exists,
	 * or adds the expiration if it does not exist.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 * @param int $expires
	 *
	 * @return bool
	 */
	public function update( $api_resource_id, $expires );

	/**
	 * Deletes a Grace Period expiration time for an API Resource.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function delete( $api_resource_id );

	/**
	 * Returns a Grace Period expiration time for an API Resource.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function get_expiration( $api_resource_id );

	/**
	 * Returns true if the $api_resource_id exists in the table.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function exists( $api_resource_id );

	/**
	 * Returns true if the Grace Period is not greater than current time (now).
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function is_expired( $api_resource_id );

}