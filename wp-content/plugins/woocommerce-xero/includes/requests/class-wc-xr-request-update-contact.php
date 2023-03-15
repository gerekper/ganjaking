<?php
/**
 * Update contact request
 *
 * @package woocommerce-xero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Update contact request
 */
class WC_XR_Request_Update_Contact extends WC_XR_Request {

	/**
	 * Contact email address
	 *
	 * @var string
	 */
	private $email;

	/**
	 * Constructor
	 *
	 * @param WC_XR_Settings $settings Xero settings.
	 * @param int            $contact_id Contact ID.
	 * @param WC_XR_Contact  $contact Contact object.
	 */
	public function __construct( WC_XR_Settings $settings, $contact_id, $contact ) {
		parent::__construct( $settings );

		$contact->set_id( $contact_id );

		$this->email = $contact->get_email_address();

		$this->set_method( 'POST' );
		$this->set_endpoint( 'Contacts' );
		$this->set_body( $contact->to_xml() );
	}

	/**
	 * Flush Contact cache before updating
	 *
	 * @return void
	 */
	protected function before_cache_set() {
		$cache_key  = 'api_' . $this->get_endpoint();
		$query      = array(
			'where' => 'EmailAddress=="' . $this->email . '"',
		);
		$cache_key .= '_' . wp_hash( http_build_query( $query ) );
		wp_cache_delete( $cache_key );
	}
}
