<?php

/**
 * Class CT_Ultimate_GDPR_Service_Sell_Personal_Data
 */
class CT_Ultimate_GDPR_Service_Sell_Personal_Data extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
	}

	/**
	 * @return $this
	 */
	public function collect() {
		$this->set_collected( array() );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", esc_html__("Do not sell my information", 'ct-ultimate-gdpr'));
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
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
	}

	/**
	 * @return bool
	 */
	public function is_subscribeable() {
		return true;
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

	}


	/**
	 * @return mixed
	 */
	public function front_action() {
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Request not to sell personal information', 'ct-ultimate-gdpr' );
	}

    /**
     *
     */
    public function unsubscribe()
    {
        // nothing to do here, admin should take care of the request
    }


}