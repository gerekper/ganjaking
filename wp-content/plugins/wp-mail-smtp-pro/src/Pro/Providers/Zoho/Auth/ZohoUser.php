<?php


namespace WPMailSMTP\Pro\Providers\Zoho\Auth;

use WPMailSMTP\Vendor\League\OAuth2\Client\Provider\ResourceOwnerInterface;
use WPMailSMTP\Vendor\League\OAuth2\Client\Tool\ArrayAccessorTrait;

/**
 * Class ZohoUser - containing the Zoho user details.
 *
 * @since 2.3.0
 *
 * @link https://github.com/shahariaazam/zoho-oauth2/blob/master/src/Provider/ZohoUser.php
 */
class ZohoUser implements ResourceOwnerInterface {

	use ArrayAccessorTrait;

	/**
	 * The response from the Zoho API request.
	 *
	 * @var array
	 */
	protected $response = [];

	/**
	 * The list of available email addresses, their display names and account IDs for this Zoho user.
	 * If the user has multiple email addresses he can use as "from email" they will be listed here.
	 *
	 * Key:   email address
	 * Value: [ email address, display name, account ID ]
	 *
	 * @since 2.3.0
	 *
	 * @var array
	 */
	protected $availableSendEmailDetails = []; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase

	/**
	 * ZohoUser constructor.
	 *
	 * @since 2.3.0
	 *
	 * @param array $response The response from the Zoho API request.
	 */
	public function __construct( array $response ) {

		$this->response = $response;

		$this->processAvailableSendEmailDetails();
	}

	/**
	 * Returns the identifier of the authorized resource owner.
	 *
	 * The API returns array of account objects, so we are taking the first one.
	 *
	 * @since 2.3.0
	 *
	 * @return mixed
	 */
	public function getId() {

		return $this->getResponseData( 'data.0.ZUID' );
	}

	/**
	 * Get the data from the Zoho API response.
	 *
	 * @since 2.3.0
	 *
	 * @param string $path    The attribute/path to get.
	 * @param null   $default The default value, if the attribute/path can't be found.
	 *
	 * @return mixed
	 */
	public function getResponseData( $path, $default = null ) {

		return $this->getValueByKey( $this->response, $path, $default );
	}

	/**
	 * Return all of the owner details available as an array.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	public function toArray() {

		return [
			'id'                           => $this->getId(),
			'available_send_email_details' => $this->getAvailableSendEmailDetails(),
		];
	}

	/**
	 * Get the available email addresses data for the current Zoho user.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	public function getAvailableSendEmailDetails() {

		return $this->availableSendEmailDetails; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}

	/**
	 * Get available send email details of the provided email.
	 *
	 * @since 2.3.0
	 *
	 * @param string $email The email to search for.
	 *
	 * @return array
	 */
	public function getAvailableSendEmailDetailsByEmail( $email ) {

		if ( ! empty( $this->availableSendEmailDetails[ $email ] ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return $this->availableSendEmailDetails[ $email ]; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		return [];
	}

	/**
	 * Prepare the available email addresses, their display names and account IDs for the current Zoho user.
	 *
	 * @since 2.3.0
	 */
	protected function processAvailableSendEmailDetails() {

		foreach ( $this->response['data'] as $data ) {
			foreach ( $data['sendMailDetails'] as $details ) {
				$this->availableSendEmailDetails[ $details['fromAddress'] ] = [ // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					'email'        => $details['fromAddress'],
					'display_name' => $details['displayName'],
					'account_id'   => $data['accountId'],
				];
			}
		}
	}
}
