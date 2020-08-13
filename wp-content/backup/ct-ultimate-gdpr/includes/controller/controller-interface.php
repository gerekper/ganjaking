<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CT_Ultimate_GDPR_Controller_Interface
 *
 */
interface CT_Ultimate_GDPR_Controller_Interface {

	/** Run after options set */
	public function init();

	/**
	 * Get unique controller id (page name, option id)
	 */
	public function get_id();

	/**
	 * Set admin options
	 *
	 * @param array $options
	 *
	 * @return CT_Ultimate_GDPR_Controller_Abstract
	 */
	public function set_options( $options );

	/**
	 * Do actions on frontend
	 */
	public function front_action();

	/**
	 * Do actions in admin (general)
	 */
	public function admin_action();

	/**
	 * Return array of default controller admin options
	 *
	 * @return array
	 */
	public function get_default_options();

}
