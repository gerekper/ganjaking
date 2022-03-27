<?php

class GPBUA_Activate {

	protected $key; // user activation key
	protected $signup; // signup being processed
	protected $result; // result from activate attempt
	protected $view; // view type to show in template

	private static $instance = null;

	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {

		// include GF User Registration functionality
		require_once( gf_user_registration()->get_base_path() . '/includes/signups.php' );

		// this needs to be called because it inits $wpdb->signups otherwise activation query fails
		GFUserSignups::prep_signups_functionality();

		// process the key request
		$this->process_key();

	}

	public function activate() {

		// no key
		if ( ! $this->has_key() ) {
			$this->view = 'error_no_key';
		} else {

			// store signup data
			$this->signup = GFSignup::get( $this->get_key() );

			// try activation
			$this->result = GFUserSignups::activate_signup( $this->get_key() );

			// error state, process error
			if ( is_wp_error( $this->result ) ) {

				if ( 'already_active' == $this->result->get_error_code() || 'blog_taken' == $this->result->get_error_code() ) {
					$this->view = 'error_already_active';
				} else {
					$this->view = 'error';
				}
			} else {

						// success, run action that gives opportunity to redirect or other functionality
						$this->view = 'success';
						/**
						 * Activation was completed successfully.
						 *
						 * @since 1.0-beta-1
						 *
						 * @param GPBUA_Activate $gpbua_activate The current GPBUA_Activate activation object.
						 */
						do_action( 'gpbua_activation_success', $this );

			}
		}

	}

	public function get_key() {
		return $this->key;
	}

	public function get_signup() {
		return $this->signup;
	}

	public function get_view() {
		return $this->view;
	}

	public function get_result() {
		return $this->result;
	}

	public function has_key() {
		if ( $this->get_key() ) {
			return true;
		}
		return false;
	}

	public function process_key() {
		parse_str( $_SERVER['QUERY_STRING'], $query_args );

		// Before GFUR 4.6, the activation key was passed via the "key" parameter. Post GFUR 4.6, it is passed via the
		// "gfur_activation" parameter.
		$key = rgar( $query_args, 'key', rgar( $query_args, 'gfur_activation' ) );
		$key = empty( $key ) ? rgar( $_POST, 'key', rgar( $_POST, 'gfur_activation' ) ) : $key;

		if ( empty( $key ) ) {
			$this->key = false; // no key provided
		} else {
			$this->key = $key; // store key
		}
	}

}
