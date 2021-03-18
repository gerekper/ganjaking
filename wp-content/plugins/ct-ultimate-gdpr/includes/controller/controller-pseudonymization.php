<?php

/**
 * Class CT_Ultimate_GDPR_Controller_Pseudonymization
 */
class CT_Ultimate_GDPR_Controller_Pseudonymization extends CT_Ultimate_GDPR_Controller_Abstract {
	
	/**
	 *
	 */
	const ID = 'ct-ultimate-gdpr-pseudonymization';
	
	/**
	 * @var bool
	 */
	private static $doing_meta_filter = false;
	
	/**
	 * @var
	 */
	private $encryption_key;
	
	/**
	 * Get unique controller id (page name, option id)
	 */
	public function get_id() {
		return self::ID;
	}
	
	/**
	 * Init after construct
	 */
	public function init() {
		
		add_filter( "get_user_metadata", array( $this, 'get_metadata_filter' ), 0, 4 );
		add_filter( "get_post_metadata", array( $this, 'get_metadata_filter' ), 0, 4 );
		add_action( "added_user_meta", array( $this, 'updated_user_meta_action' ), 100, 4 );
		add_action( "updated_user_meta", array( $this, 'updated_user_meta_action' ), 100, 4 );
		add_action( "added_post_meta", array( $this, 'updated_user_meta_action' ), 100, 4 );
		add_action( "updated_post_meta", array( $this, 'updated_user_meta_action' ), 100, 4 );
		add_filter( "get_post_metadata", array( $this, 'fix_metadata' ), PHP_INT_MAX, 4 );

	}

	/**
	 * Do actions on frontend
	 */
	public function front_action() {
	}
	
	/**
	 * Do actions in admin (general)
	 */
	public function admin_action() {
	}
	
	/**
	 * Do actions on current admin page
	 */
	protected function admin_page_action() {
		
		if ( $this->is_admin_request_for_encrypt_all() ) {
			$this->encrypt_all();
		}
		
		if ( $this->is_admin_request_for_decrypt_all() ) {
			$this->decrypt_all();
		}
		
	}
	
	/**
	 * @return bool
	 */
	private function is_admin_request_for_encrypt_all() {
		
		if ( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-pseudo-encrypt-all', $this->get_request_array() ) ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	private function is_admin_request_for_decrypt_all() {
		
		if ( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-pseudo-decrypt-all', $this->get_request_array() ) ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get view template string
	 * @return string
	 */
	public function get_view_template() {
		return 'admin/admin-pseudonymization';
	}
	
	/**
	 * Add menu page (if not added in admin controller)
	 */
	public function add_menu_page() {
		
		add_submenu_page(
			'ct-ultimate-gdpr',
			esc_html__( 'Pseudonymization', 'ct-ultimate-gdpr' ),
			esc_html__( 'Pseudonymization', 'ct-ultimate-gdpr' ),
			'manage_options',
			$this->get_id(),
			array( $this, 'render_menu_page' )
		);
		
	}
	
	/**
	 * @return mixed
	 */
	public function add_option_fields() {
		
		if ( ! $this->check_encryption_possible() ) {
			$this->add_view_option( 'notices', array( esc_html__( 'Encryption is not possible. Please check your openssl library.', 'ct-ultimate-gdpr' ) ) );
			
			return;
		}
		
		/* Section */
		
		add_settings_section(
			$this->get_id(), // ID
			esc_html__( 'Pseudonymization', 'ct-ultimate-gdpr' ), // Title
			null, // callback
			$this->get_id() // Page
		);
		
		/* Section fields */
		
		add_settings_field(
			'pseudonymization_warning', // ID
			esc_html__( 'Warning', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_pseudonymization_warning' ), // Callback
			$this->get_id(), // Page
			$this->get_id(), // Section
			array(
				'class' => 'ct-ultimate-gdpr-message ct-ultimate-gdpr-msg-clone warning',
			)
		);
		
		add_settings_field(
			'pseudonymization_header', // ID
			esc_html__( 'Instructions', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_pseudonymization_header' ), // Callback
			$this->get_id(), // Page
			$this->get_id(), // Section
			array(
				'class' => 'ct-ultimate-gdpr-message ct-ultimate-gdpr-msg-clone',
			)
		);
		
		add_settings_field(
			'pseudonymization_encrypt_new_data', // ID
			esc_html__( 'Automatically encrypt new data', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_pseudonymization_encrypt_new_data' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);
		
		add_settings_field(
			'pseudonymization_decrypt_all_data', // ID
			esc_html__( 'Automatically decrypt all data on the fly (if you have anything encrypted, this is recommended)', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_pseudonymization_decrypt_all_data' ), // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);
		
		add_settings_field(
			'pseudonymization_encrypt_services_header', // ID
			esc_html__( 'Select data to encrypt', 'ct-ultimate-gdpr' ), // Title
			'__return_empty_string', // Callback
			$this->get_id(), // Page
			$this->get_id() // Section
		);
		
	}
	
	/**
	 *
	 */
	public function render_field_pseudonymization_encrypt_new_data() {
		
		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();
		
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);
		
	}
	
	/**
	 *
	 */
	public function render_field_pseudonymization_decrypt_all_data() {
		
		$admin = CT_Ultimate_GDPR::instance()->get_admin_controller();
		
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);
		
	}
	
	/**
	 *
	 */
	public function render_field_pseudonymization_warning() {
		echo sprintf( "%s %s %s",
			'<h4>',
			esc_html__( 'This feature is experimental and may lead to irreversible data lost! After encryption, it may be impossible for anyone to decrypt your data. ', 'ct-ultimate-gdpr' ),
			'</h4>'
		);
		
		
	}
	
	/**
	 *
	 */
	public function render_field_pseudonymization_header() {
		echo sprintf( "%s %s %s %s %s %s %s %s %s %s %s %s %s %s",
			esc_html__( 'Pseudonymization feature allows you to encrypt some of the data stored in the database. That way, if anyone got access to your database, the user\'s data would be pseudonymized. Database contents are permanently encrypted and can be decrypted on the fly when accessed by users. You can also permanently decrypt all your contents.', 'ct-ultimate-gdpr' ),
			'<br>',
			'<br>',
			esc_html__( 'How to use:', 'ct-ultimate-gdpr' ),
			'<br>',
			esc_html__( '1. Make a backup', 'ct-ultimate-gdpr' ),
			'<br>',
			esc_html__( "2. Select which data to encrypt", 'ct-ultimate-gdpr' ),
			'<br>',
			esc_html__( "3. (Optionally) Select 'automatically encrypt new data' for continuous encryption of all incoming data", 'ct-ultimate-gdpr' ),
			'<br>',
			esc_html__( "4. Click 'Save changes'", 'ct-ultimate-gdpr' ),
			'<br>',
			esc_html__( "5. (Optionally) Click 'Encrypt selected' to encrypt all current data which has not been yet encrypted", 'ct-ultimate-gdpr' )
		);
	}
	
	/**
	 *
	 */
	private function encrypt_all() {
		
		if ( ! $this->check_encryption_possible() ) {
			$this->add_view_option( 'notices', array( esc_html__( 'Encryption is not possible. Please check your openssl library.', 'ct-ultimate-gdpr' ) ) );
			
			return;
		}
		
		// get data
		$data = $this->get_data_to_encrypt();
		
		// encrypt
		$data = $this->encrypt_recursive( $data );
		
		// save data
		$this->save_encrypted_data( $data );
		
		$this->add_view_option( 'notices', array( esc_html__( 'Data encrypted successfully', 'ct-ultimate-gdpr' ) ) );
		
	}
	
	/**
	 *
	 */
	private function decrypt_all() {
		
		if ( ! $this->check_encryption_possible() ) {
			$this->add_view_option( 'notices', array( esc_html__( 'Decryption is not possible. Please check your openssl library.', 'ct-ultimate-gdpr' ) ) );
			
			return;
		}
		
		// get data
		$data = $this->get_data_to_encrypt();
		
		// encrypt
		$data = $this->encrypt_recursive( $data, 'decrypt' );
		
		// save data
		$this->save_encrypted_data( $data );
		
		$this->add_view_option( 'notices', array( esc_html__( 'Data decrypted successfully', 'ct-ultimate-gdpr' ) ) );
		
	}
	
	/**
	 * @param mixed $data
	 * @param string $method
	 *
	 * @return mixed
	 */
	private function encrypt_recursive( $data, $method = 'encrypt' ) {
		
		if ( ! in_array( $method, array( 'encrypt', 'decrypt' ) ) ) {
			return $data;
		}
		
		if ( is_array( $data ) || is_object( $data ) ) {
			
			if (
				is_array( $data ) &&
				! empty( $data['ct_ultimate_gdpr_key_to_encrypt'] ) &&
				isset( $data[ $data['ct_ultimate_gdpr_key_to_encrypt'] ] )
			) {
				
				$data[ $data['ct_ultimate_gdpr_key_to_encrypt'] ] = $this->encrypt_recursive( $data[ $data['ct_ultimate_gdpr_key_to_encrypt'] ], $method );
				unset( $data['ct_ultimate_gdpr_key_to_encrypt'] );
				
			} elseif (
				is_object( $data ) &&
				! empty( $data->ct_ultimate_gdpr_key_to_encrypt ) &&
				isset( $data->{$data->ct_ultimate_gdpr_key_to_encrypt} )
			) {
				
				$data->{$data->ct_ultimate_gdpr_key_to_encrypt} = $this->encrypt_recursive( $data->{$data->ct_ultimate_gdpr_key_to_encrypt}, $method );
				unset( $data->ct_ultimate_gdpr_key_to_encrypt );
				
			} else {
				
				foreach ( $data as $key => $datum ) {
					
					if ( is_array( $data ) ) {
						
						$data[ $key ] = $this->encrypt_recursive( $datum, $method );
						
					}
					
					if ( is_object( $data ) ) {
						
						$data->$key = $this->encrypt_recursive( $datum, $method );
						
					}
					
				}
				
			}
			
			return $data;
		}
		
		if ( ! $data ) {
			
			return $data;
			
		}
		
		return $this->$method( $data );
		
	}
	
	/**
	 * @return bool
	 */
	private function check_encryption_possible() {
		return in_array( $this->get_hash_method(), openssl_get_cipher_methods() );
	}
	
	/**
	 * @param $content
	 * @param bool $encode
	 *
	 * @return string
	 */
	private function encrypt( $content, $encode = true ) {
		
		if ( $this->is_encrypted( $content ) ) {
			return $content;
		}
		
		$nonce_size = openssl_cipher_iv_length( $this->get_hash_method() );
		$nonce      = openssl_random_pseudo_bytes( $nonce_size );
		
		$encrypted = openssl_encrypt(
			$content,
			$this->get_hash_method(),
			$this->get_encryption_key(),
			OPENSSL_RAW_DATA,
			$nonce );
		
		$return = $nonce . $encrypted;
		
		if ( $encode ) {
			return $this->get_encrypted_prefix() . base64_encode( $return );
		}
		
		return $return;
	}
	
	/**
	 * @param $content
	 * @param bool $encoded
	 *
	 * @return bool|string
	 */
	private function decrypt( $content, $encoded = true ) {
		
		if ( ! $this->is_encrypted( $content ) ) {
			return $content;
		}
		
		if ( $encoded ) {
			
			$content = mb_substr( $content, mb_strlen( $this->get_encrypted_prefix() ), null, '8bit' );
			$content = base64_decode( $content, true );
			if ( $content === false ) {
				return $content;
			}
			
		}
		
		$nonce_size = openssl_cipher_iv_length( $this->get_hash_method() );
		$nonce      = mb_substr( $content, 0, $nonce_size, '8bit' );
		$ciphertext = mb_substr( $content, $nonce_size, null, '8bit' );
		
		$plaintext = openssl_decrypt(
			$ciphertext,
			$this->get_hash_method(),
			$this->get_encryption_key(),
			OPENSSL_RAW_DATA,
			$nonce
		);
		
		if ( is_string( $plaintext ) && ! ctype_print( $plaintext ) ) {
			$plaintext = '';
		}
		
		return $plaintext;
		
	}
	
	/**
	 * @param $content
	 *
	 * @return bool
	 */
	private function is_encrypted( $content ) {
		$stripos = function_exists( 'mb_stripos' ) ? 'mb_stripos' : 'stripos';
		
		return is_string( $content ) && 0 === $stripos( $content, $this->get_encrypted_prefix() );
	}
	
	/**
	 *
	 */
	private function set_encryption_key() {
		
		$static_key = 'fasdfsad9f06sdf925y2+_)F)(&F&%F^$^&TDSGADASDFLAA:FFAK';
		$db_key_bin = hex2bin( $this->get_db_key() );
		
		$encrypted = openssl_encrypt(
			$static_key,
			$this->get_hash_method(),
			$db_key_bin,
			OPENSSL_RAW_DATA,
			'8473629874637485'
		);
		
		$this->encryption_key = (string) $encrypted;
	}
	
	/**
	 * @return mixed
	 */
	private function get_encryption_key() {
		
		if ( ! $this->encryption_key ) {
			$this->set_encryption_key();
		}
		
		return $this->encryption_key;
		
	}
	
	/**
	 * @return mixed|string
	 */
	public function get_db_key() {
		
		$key = $this->get_option( 'pseudonymization_db_key' );
		
		if ( ! $key ) {
			$key = $this->set_db_key();
		}
		
		return $key;
	}
	
	/**
	 * @return string
	 */
	private function set_db_key() {
		
		$key = bin2hex( openssl_random_pseudo_bytes( 32 ) );
		
		$this->options['pseudonymization_db_key'] = $key;
		update_option( $this->get_id(), $this->options );
		
		return $key;
	}
	
	/**
	 * @return string
	 */
	private function get_hash_method() {
		return 'aes-256-ctr';
	}
	
	/**
	 * @return string
	 */
	private function get_encrypted_prefix() {
		return 'ctenc=';
	}
	
	/**
	 * @return array
	 */
	private function get_data_to_encrypt() {
		
		$data = array();
		
		/* user meta */
		$meta_keys = apply_filters( 'ct_ultimate_gdpr_controller_pseudonymization_get_data_to_encrypt_meta_keys', array() );
		
		if ( $meta_keys ) {
			
			global $wpdb;
			
			$sql     = "
		SELECT user_id, umeta_id, meta_key, meta_value
		FROM {$wpdb->usermeta}
		WHERE meta_key in (" . implode( ', ', array_fill( 0, count( $meta_keys ), '%s' ) ) . ")
		";
			$query   = $wpdb->prepare( $sql, $meta_keys );
			$results = $wpdb->get_results( $query, ARRAY_A );
			
			if ( is_array( $results ) && $results ) {
				
				foreach ( $results as $result ) {
					
					$user_data = get_userdata( $result['user_id'] );
					$roles     = (array) $user_data->roles;
					
					//do not encrypt administrator data
					if ( in_array( 'administrator', $roles ) ) {
						continue;
					}
					
					$result['ct_ultimate_gdpr_key_to_encrypt'] = 'meta_value';
					$data[]                                    = $result;
					
				}
				
				
			}
			
		}
		
		/* post meta */
		$postmeta_keys = apply_filters( 'ct_ultimate_gdpr_controller_pseudonymization_get_data_to_encrypt_postmeta_keys', array() );
		
		if ( $postmeta_keys ) {
			
			global $wpdb;
			
			$sql     = "
		SELECT *
		FROM {$wpdb->postmeta}
		WHERE meta_key in (" . implode( ', ', array_fill( 0, count( $postmeta_keys ), '%s' ) ) . ")
		";
			$query   = $wpdb->prepare( $sql, $postmeta_keys );
			$results = $wpdb->get_results( $query, ARRAY_A );
			
			if ( is_array( $results ) && $results ) {
				
				foreach ( $results as $result ) {
					
					$result['ct_ultimate_gdpr_key_to_encrypt'] = 'meta_value';
					$data[]                                    = $result;
					
				}
				
				
			}
			
		}
		
		return apply_filters( 'ct_ultimate_gdpr_controller_pseudonymization_get_data_to_encrypt', $data, $meta_keys );
		
	}
	
	/**
	 * @param $data
	 */
	private function save_encrypted_data( $data ) {
		
		$data = apply_filters( 'ct_ultimate_gdpr_controller_pseudonymization_save_encrypted_data', $data );
		global $wpdb;
		
		foreach ( $data as $row ) {
			
			$meta_key   = $row['meta_key'];
			$meta_value = $row['meta_value'];
			
			$usermeta_id = ct_ultimate_gdpr_get_value( 'umeta_id', $row );
			$postmeta_id = ct_ultimate_gdpr_get_value( 'meta_id', $row );
			
			if ( $usermeta_id ) {
				
				$query = $wpdb->prepare( "
		UPDATE {$wpdb->usermeta}
		SET meta_value = %s
		WHERE meta_key = %s AND umeta_id = %d
		",
					array( $meta_value, $meta_key, $usermeta_id )
				);
				
			}
			
			if ( $postmeta_id ) {
				
				$query = $wpdb->prepare( "
		UPDATE {$wpdb->postmeta}
		SET meta_value = %s
		WHERE meta_key = %s AND meta_id = %d
		",
					array( $meta_value, $meta_key, $postmeta_id )
				);
				
			}
			
			$wpdb->query( $query );
			
		}
		
	}
	
	/**
	 * Fix returning data structure in case of filters from other plugins changing structure
	 *
	 * @param $value
	 * @param $object_id
	 * @param $meta_key
	 * @param $single
	 *
	 * @return array|bool|mixed|null|string
	 */
	public function fix_metadata( $value, $object_id, $meta_key, $single ) {

		// WP_Job_Manager_Visibility_Output doubled array fix
		if (
			$meta_key == '_thumbnail_id'
			&& class_exists( 'WP_Job_Manager_Visibility_Output' )
			&& $single
			&& is_array( $value )
			&& count( $value ) == 1
			&& isset( $value[0] )
			&& is_array( $value[0] )
		) {
			return $value[0];
		}

		return $value;
	}

	/**
	 * @param $value
	 * @param $object_id
	 * @param $meta_key
	 * @param $single
	 *
	 * @return array|bool|mixed|null|string
	 */
	public function get_metadata_filter( $value, $object_id, $meta_key, $single ) {

		// check if enabled
		if ( ! $this->get_option( 'pseudonymization_decrypt_all_data' ) ) {
			return $value;
		}
		
		// return default if only checking if metadata exists
		if ( in_array( 'metadata_exists', wp_debug_backtrace_summary( null, 0, false ) ) ) {
			return $value;
		}
		
		$meta_type = $this->get_filter_meta_type();
		
		$return = null;
		
		/** get_metadata original function below */
		
		$meta_cache = wp_cache_get( $object_id, $meta_type . '_meta' );
		
		/**do not encrypt admin data */
		
		if ( is_array( $meta_cache ) && isset( $meta_cache['wp_user_level'][0] ) && $meta_cache['wp_user_level'][0] == 10 ) {
			return $value;
		}
		
		if ( ! $meta_cache ) {
			$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
			$meta_cache = $meta_cache[ $object_id ];
		}
		
		/** get_metadata modified function below */
		
		if ( ! $meta_key ) {
			$return = $meta_cache;
		}
		
		if ( $return === null && isset( $meta_cache[ $meta_key ] ) ) {
			
			if ( $single ) {
				
				$return = maybe_unserialize( $meta_cache[ $meta_key ][0] );
				
			} else {
				
				$return = array_map( 'maybe_unserialize', $meta_cache[ $meta_key ] );
				
			}
			
		}
		
		if ( $return === null ) {
			
			if ( $single ) {
				
				$return = '';
				
			} else {
				
				$return = array();
				
			}
			
		}
		
		/** Decrypt all encrypted user meta values */
		if ( $return ) {
			
			$return = $this->encrypt_recursive( $return, 'decrypt' );
			
		}
		
		/** Set data format to fit get_metadata function hook expectations */
		if ( $single ) {
			$return = array( $return );
		}
		
		return $return;
		
	}
	
	/**
	 * @param $meta_id
	 * @param $object_id
	 * @param $meta_key
	 * @param $_meta_value
	 */
	public function updated_user_meta_action( $meta_id, $object_id, $meta_key, $_meta_value ) {
		
		// check if enabled
		if ( ! $this->get_option( 'pseudonymization_encrypt_new_data' ) ) {
			return;
		}
		
		// prevent action loop
		if ( self::is_doing_meta_filter() ) {
			return;
		}
		
		self::set_doing_meta_filter( true );
		
		$meta_keys_to_encrypt = apply_filters( 'ct_ultimate_gdpr_controller_pseudonymization_updated_user_meta_to_encrypt', array() );
		
		if ( in_array( $meta_key, $meta_keys_to_encrypt ) ) {
			
			// update selected meta
			update_metadata_by_mid( $this->get_filter_meta_type(), $meta_id, $this->encrypt_recursive( $_meta_value ) );
			
		}
		
		self::set_doing_meta_filter( false );
		
	}
	
	/**
	 * @return string
	 */
	private function get_filter_meta_type() {
		return stripos( current_filter(), 'post' ) ? 'post' : 'user';
	}
	
	/**
	 * @param bool $value
	 */
	private static function set_doing_meta_filter( $value = true ) {
		self::$doing_meta_filter = $value;
	}
	
	/**
	 * @return bool
	 */
	public static function is_doing_meta_filter() {
		return self::$doing_meta_filter;
	}
	
	/**
	 * @return array
	 */
	private function get_excluded_keys_options_to_export() {
		return array( 'pseudonymization_db_key' );
	}
}