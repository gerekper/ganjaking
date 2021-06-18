<?php
// Check if file is accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Mailchimp integration class
 * @since 4.9.29
 * @package userpro
 * @subpackage email
 */

final class UserProMailChimp {

	/**
	 * API Endpoint placeholder
	 * @access private
	 * @var string
	 */
	private $_apiEnd = 'https://<dc>.api.mailchimp.com/3.0/';

	/**
	 * Mailchimp api key
	 * @access private
	 * @var string
	 */
	private $_api = null;

	/**
	 * Mailchimp data centre
	 * @access private
	 * @var string
	 */
	private $_dc = null;

	/**
	 * Mailchimp class singleton
	 * @access private
	 * @var object
	 */
	private static $_instance = null;

	/**
	 * Mailchimp api status
	 * @access private
	 * @var boolean
	 */
	private $_status = false;

	/**
	 * Returns UserProMailChimp singleton
	 * @access public
	 * @return UserProMailChimp
	 */
	public static function get_instance( ) {
		if( !isset( self::$_instance ) && !( self::$_instance instanceof self ) )
			self::$_instance = new self( );

		return self::$_instance;
	}

	/**
	 * Mailchimp class constructor
	 */
	public function __construct( ) {
		// Get the api key from the database
		$this -> _api = userpro_get_option( 'mailchimp_api' );

		// Get the data centre from the api key
		$this -> _dc  = explode( '-', $this -> _api );
		$this -> _dc  = str_replace( '<dc>', end( $this -> _dc ), $this -> _apiEnd );

		// Check if api is valid
		$this -> _check_api( );
	}

	/**
	 * Calls mailchimp api outside the class
	 * @param  string $endpoint  endpoint to call
	 * @param  array  $arguments arguments to provide
	 * @return array             response array
	 */
	public function callApi( $endpoint, $arguments = [ ] ) {
		if( $this->_status === false )
			return [ 'error' => __( 'Invalid mailchimp key', 'userpro' ) ];

		return $this -> _callApi( $endpoint, $arguments );
	}

	/**
	 * Calls mailchimp api to provided endpoint with provided arguments
	 * @access private
	 * @param  string $endpoint  endpoint to call
	 * @param  array  $arguments arguments to provide
	 * @return array             response array
	 */
	private function _callApi( $endpoint, $request, $arguments = [ ] ) {
		// Initiate curl
		$curl = curl_init( );

		// Init headers
		$headers = [
			'Content-Type: application/json',
			'Authorization: apikey ' . $this -> _api
		];

		// Generate url
		$url = $this -> _dc . $endpoint;

		// Set curl options
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $curl, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0' );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		curl_setopt( $curl, CURLOPT_ENCODING, '' );
		curl_setopt( $curl, CURLINFO_HEADER_OUT, true );

		switch( $request ) :
			case 'POST' :
				// Json encode data
				$data = json_encode( $arguments );

				curl_setopt( $curl, CURLOPT_POST, true );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
				break;
			case 'GET' :
				$data = http_build_query( $arguments, '', '&' );
				curl_setopt( $curl, CURLOPT_URL, $url . '?' . $data );
				break;
			case 'PUT' :
				// Json encode data
				$data = json_encode( $arguments );

				curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PUT' );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
				break;
		endswitch;

		// Execute curl
		$response = curl_exec( $curl );

		// Close curl
		curl_close( $curl );

		// Return the response
		return json_decode( $response, true );
	}

	/**
	 * Checks if api is available
	 * @return boolean true if available, false otherwise
	 */
	private function _check_api( ) {
		// Get lists to check response
		$response = $this -> _callApi( 'ping', 'GET' );

		// Check if status for error is set
		$this -> _status = !isset( $response[ 'status' ] );
	}

	/**
	 * Check whether an email is subscribed
	 * @param  string  $list_id mailchimp list id
	 * @param  string  $email   an email to check
	 * @return boolean          true if subscribed, false otherwise
	 */
	public function is_subscribed( $list_id, $email ) {
		// Check status
		if( !$this -> _status )
			return [ 'error' => __( 'Invalid mailchimp key', 'userpro' ) ];

		// Hash the email
		$emailHash = md5( $email );

		$endpoint = 'lists/' . $list_id . '/members/' . $emailHash;

		$request = $this -> _callApi( $endpoint, 'GET' );

		if( isset( $request[ 'id' ] ) )
			return $request[ 'status' ] === 'subscribed';

		return false;
	}

	/**
	 * Subscribe to a list
	 * @param  array $arguments data needed for subscribtion
	 * @return array            response array
	 */
	public function subscribe( $list_id, $data = [ ] ) {
		// Check status
		if( !$this -> _status )
			return [ 'error' => __( 'Invalid mailchimp key', 'userpro' ) ];

		// Default data for subscribtion
		$defaults = [
			'email_address' => '',
			'status'        => 'subscribed',
			'merge_fields'  => [
				'FNAME' => '',
				'LNAME' => ''
			]
		];

		// Parse data
		$data = wp_parse_args( $data, $defaults );

		// TODO: Check if data is valid

		// Generate the endpoint for subscribtion
		$endpoint = 'lists/' . $list_id . '/members/' . md5( $data[ 'email_address' ] );

		return $this -> _callApi( $endpoint, 'PUT', $data );
	}

	public function unsubscribe( $list_id, $data = [ ] ) {
		// Check status
		if( !$this -> _status )
			return [ 'error' => __( 'Invalid mailchimp key', 'userpro' ) ];

		// Default data for subscribtion
		$defaults = [
			'email_address' => '',
			'status'        => 'unsubscribed'
		];

		// Parse data
		$data = wp_parse_args( $data, $defaults );

		// TODO: Check if data is valid

		// Generate the endpoint for subscribtion
		$endpoint = 'lists/' . $list_id . '/members/' . md5( $data[ 'email_address' ] );

		return $this -> _callApi( $endpoint, 'PUT', $data );
	}

}

?>