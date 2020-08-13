<?php

/**
 * Interface CT_Ultimate_CCPA_Service_Interface
 */
interface CT_Ultimate_CCPA_Service_Interface {

	/**
	 * Get service id (based on class name)
	 *
	 * @return string
	 */
	public function get_id();

	/**
	 * Get service name
	 *
	 * @return mixed
	 */
	public function get_name();

	/**
	 * Is it active, eg. whether related plugin is enabled
	 *
	 * @return bool
	 */
	public function is_active();


}