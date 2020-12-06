<?php

/**
 * Class CT_Ultimate_GDPR_Service_Post_SMTP
 */
class CT_Ultimate_GDPR_Service_Post_SMTP extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_post-smtp/postman-smtp.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_post-smtp/postman-smtp.php', '__return_false' );
	}

	/**
	 * @return $this
	 */
	public function collect() {
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Post SMTP' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return false;
	}

	/**
	 * @return bool
	 */
	public function is_subscribeable() {
		return false;
	}



	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

	}

	/**
	 *
	 */
	public function render_field_breach_services() {

	}

	/**
	 * @return mixed
	 */
	public function front_action() {

	}
}