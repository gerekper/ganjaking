<?php

/**
 * Class CT_Ultimate_GDPR_Model_User
 */
class CT_Ultimate_GDPR_Model_User {

	/**
	 * @var
	 */
	private $data;

	/** @var WP_User|false */
	private $wp_user;

	/**
	 * CT_Ultimate_GDPR_Model_User constructor.
	 *
	 * @param array $data
	 */
	public function __construct( $data = array() ) {
		$this->set_data( $data );
		$this->set_wp_user();
	}

	/**
	 * @return bool|string
	 */
	public function get_email(  ) {

		if ( $this->wp_user instanceof WP_User) {
			return $this->wp_user->user_email;
		}

		return isset( $this->data[ 'user_email' ] ) ? $this->data[ 'user_email' ] : false;

	}

	/**
	 * @return int
	 */
	public function get_id(  ) {

		if ( $this->wp_user instanceof WP_User) {
			return $this->wp_user->ID;
		}

		return isset( $this->data[ 'ID' ] ) ? $this->data[ 'ID' ] : 0;

	}

	/**
	 * @param mixed $data
	 */
	public function set_data( $data ) {
		$this->data = (array) $data;
	}

	/**
	 *
	 */
	private function set_wp_user() {

		if ( ! empty( $this->data['ID'] ) ) {
			$this->wp_user = get_user_by( 'id', $this->data['ID'] );
		} elseif ( ! empty( $this->data['user_email'] ) ) {
			$this->wp_user = get_user_by( 'email', $this->data['user_email'] );
		} else {
			$this->wp_user = false;
		}


	}

	/**
	 * @return WP_User
	 */
	public function get_wp_user() {
		return $this->wp_user;
	}

	/**
	 * Get id of user to set posts authorship to
	 * @return int
	 */
	public function get_target_user_id(  ) {

		$user_id = (int) CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value(
			'forgotten_wp_posts_target_user',
			$this->get_current_user_id(),
			CT_Ultimate_GDPR_Controller_Forgotten::ID
		);

		if ( ! $user_id ) {
			$user    = get_user_by( 'email', get_bloginfo( 'admin_email' ) );
			$user_id = $user instanceof WP_User ? $user->ID : 1;
		}

		return $user_id;
	}

	/**
	 * @return int
	 */
	public function get_current_user_id(  ) {
		$user = wp_get_current_user();
		return $user->ID;
	}


}