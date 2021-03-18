<?php

/**
 * Interface CT_Ultimate_GDPR_Service_Interface
 */
interface CT_Ultimate_GDPR_Service_Interface {

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

	/**
	 * Can data be forgotten by this service?
	 *
	 * @return bool
	 */
	public function is_forgettable();

	/**
	 * Can user be subscribe to a newsletter by this service?
	 *
	 * @return bool
	 */
	public function is_subscribeable();

	/**
	 * Set target user
	 *
	 * @param CT_Ultimate_GDPR_Model_User $user
	 *
	 * @return $this
	 */
	public function set_user( $user );

	/**
	 * Register add service to the collection
	 *
	 * @param $services
	 *
	 * @return array
	 */
	public function register( $services );

	/**
	 * Collect data of a specific user
	 *
	 * @return $this
	 */
	public function collect();

	/**
	 * Forget specific user data
	 *
	 * @throws Exception
	 * @return void
	 */
	public function forget();

	/**
	 * Do optional action on front
	 *
	 * @return mixed
	 */
	public function front_action();

	/**
	 * Get service description used in front form shortcode
	 *
	 * @return string
	 */
	public function get_description();

    /**
     * Get service name used in front form shortcode
     *
     * @return string
     */
    public function get_service_name();

}