<?php

class GPNF_Session {

	const COOKIE_NAME = 'gpnf_form_session';

	private $_form_id;
	private $_cookie;

	public function __construct( $form_id ) {

		$this->_form_id = $form_id;
		$this->_cookie  = $this->get_cookie();

	}

	public function get( $prop ) {
		if ( ! isset( $this->$prop ) ) {
			if ( empty( $this->_cookie ) ) {
				return null;
			}
			// Clean up nested entries; only return non-trashed entries that exist.
			if ( $prop == 'nested_entries' ) {
				$this->_cookie[ $prop ] = $this->get_valid_entry_ids( $this->_cookie[ $prop ] );
			}
			return $this->_cookie[ $prop ];
		} else {
			return $this->$prop;
		}
	}

	public function add_child_entry( $child_entry_id ) {

		// @todo review
		$nested_form_field_key = gform_get_meta( $child_entry_id, GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY );

		if ( ! array_key_exists( $nested_form_field_key, (array) $this->_cookie['nested_entries'] ) ) {
			$this->_cookie['nested_entries'][ $nested_form_field_key ] = array();
		}

		$this->_cookie['nested_entries'][ $nested_form_field_key ][] = $child_entry_id;

		$this->set_cookie();

	}

	public function set_session_data() {

		$cookie = $this->get_cookie();

		// Existing cookie.
		if ( $cookie ) {

			$data = array(
				'form_id'        => $cookie['form_id'],
				'hash'           => $cookie['hash'],
				'user_id'        => $cookie['user_id'],
				'nested_entries' => $cookie['nested_entries'],
			);

			foreach ( $cookie as $key => $value ) {
				$data[ $key ] = $value;
			}
		}
		// New cookie.
		else {

			$data = array(
				'form_id'        => $this->_form_id,
				'hash'           => $this->make_hashcode(),
				'user_id'        => get_current_user_id(),
				'nested_entries' => array(),
			);

		}

		foreach ( $_POST as $key => $value ) {
			$data[ $key ] = $value ? $value : rgar( $data, $key );
		}

		$this->_cookie = $data;

		return $this;
	}

	public function make_hashcode() {
		return substr( md5( uniqid( rand(), true ) ), 0, 12 );
	}

	public function set_cookie() {
		setcookie( $this->get_cookie_name(), json_encode( $this->_cookie ), time() + 60 * 60 * 24 * 7, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
	}

	public function get_cookie() {

		$cookie_name = $this->get_cookie_name();
		if ( isset( $_COOKIE[ $cookie_name ] ) ) {
			$cookie = json_decode( stripslashes( $_COOKIE[ $cookie_name ] ), true );
			return $cookie;
		}

		return false;
	}

	public function get_cookie_name() {
		$name = implode( '_', array( self::COOKIE_NAME, $this->_form_id ) );
		/**
		 * Filter the name of the session cookie GPNF uses for a given form
		 *
		 * @since 1.0-beta-8.68
		 *
		 * @param string $name    Default session cookie name GPNF has generated.
		 * @param string $form_id Parent form ID that the nested form belongs to.
		 */
		return apply_filters( 'gpnf_cookie_name', $name, $this->_form_id );
	}

	public function delete_cookie() {
		/**
		 * GravityView initializes the Edit Form mid page load so we can't utilize the Set-Cookie header since headers
		 * have already been sent.
		 */
		if ( headers_sent() ) {
			$cookie_name = $this->get_cookie_name();

			add_action( 'gform_register_init_scripts', function () use ( $cookie_name ) {
				echo '<script type="text/javascript">document.cookie = "' . $cookie_name . '= ; expires = Thu, 01 Jan 1970 00:00:00 GMT; path = /;"</script>';
			}, 0 );

			return;
		}

		$cookie_name = $this->get_cookie_name();
		unset( $_COOKIE[ $cookie_name ] );
		setcookie( $cookie_name, '', time() - ( 15 * 60 ), COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
	}

	public function has_data() {
		return ! empty( $this->_cookie );
	}

	public function get_valid_entry_ids( $entries ) {
		global $wpdb;

		if ( empty( $entries ) ) {
			return array();
		}

		$all = array();
		foreach ( $entries as $field_id => $entry_ids ) {
			$all = array_merge( $all, $entry_ids );
		}

		$sql             = "SELECT id FROM {$wpdb->prefix}gf_entry WHERE id IN( " . implode( ', ', $all ) . " ) and status != 'trash'";
		$valid_entry_ids = wp_list_pluck( $wpdb->get_results( $sql ), 'id' );
		$return          = array();

		foreach ( $entries as $field_id => $entry_ids ) {
			$return[ $field_id ] = array_intersect( $valid_entry_ids, $entry_ids );
		}

		$return = array_filter( $return );

		return $return;
	}

	/**
	 * Get Save & Continue from URL if it exists.
	 *
	 * @return string|null
	 */
	public static function get_save_and_continue_token() {
		return isset( $_POST['gform_resume_token'] ) ? $_POST['gform_resume_token'] : rgget( 'gf_token' );
	}

	public static function get_default_session_data( $form_id, $field_values = array() ) {

		$data = array(
			'action'       => 'gpnf_session',
			'form_id'      => $form_id,
			'request'      => $_REQUEST ? $_REQUEST : array(),
			'post_id'      => get_queried_object_id(),
			'field_values' => $field_values,
		);

		if( self::get_save_and_continue_token() ) {
			$parent_hash = gp_nested_forms()->get_save_and_continue_parent_hash( $form_id );
			if ( $parent_hash ) {
				$data['hash'] = $parent_hash;
			}
		}

		/**
		 * Filter the data used to initialize the session script.
		 *
		 * @since 1.0-beta-6.13
		 *
		 * @param array $data An array of data used to initialize the session script.
		 */
		$data = gf_apply_filters( array( 'gpnf_session_script_data', $form_id ), $data );

		return $data;
	}

}
