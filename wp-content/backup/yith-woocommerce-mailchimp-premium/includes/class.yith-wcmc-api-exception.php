<?php
/**
 * API Exception class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMC_API_Exception' ) ) {
	/**
	 * WooCommerce Mailchimp
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC_API_Exception extends Exception {
		protected $http_status = '';
		protected $error = '';

		public function __construct( $message, $error, $http_status ) {
			$this->http_status = $http_status;
			$this->error       = $error;
			$this->code        = $this->http_status;

			parent::__construct( $message );
		}

		public function getHttpStatus() {
			return $this->http_status;
		}

		public function getError() {
			return str_replace( ' ', '', $this->error );
		}

		public function getLocalizedMessage() {
			if ( apply_filters( 'yith_wcmc_localize_error_messages', false ) ) {
				$localizations = $this->get_available_errors();

				if ( isset( $localizations[ $this->getError() ] ) ) {
					return apply_filters( 'yith_wcmc_api_error_message', $localizations[ $this->getError() ], $this->getMessage(), $this->getError(), $this->getHttpStatus() );
				} else {
					return $this->getMessage();
				}
			} else {
				return $this->getMessage();
			}
		}

		public function get_available_errors() {
			$translations = array(
				'BadRequest'             => __( 'Your request could not be processed.', 'yith-woocommerce-mailchimp' ),
				'InvalidAction'          => __( 'The action requested was not valid for this resource.', 'yith-woocommerce-mailchimp' ),
				'InvalidResource'        => __( 'The resource submitted could not be validated.', 'yith-woocommerce-mailchimp' ),
				'JSONParseError'         => __( 'We encountered an unspecified JSON parsing error.', 'yith-woocommerce-mailchimp' ),
				'APIKeyMissing'          => __( 'Your request did not include an API key.', 'yith-woocommerce-mailchimp' ),
				'APIKeyInvalid'          => __( 'Your API key may be invalid, or you’ve attempted to access the wrong data center.', 'yith-woocommerce-mailchimp' ),
				'Forbidden'              => __( 'You are not permitted to access this resource.', 'yith-woocommerce-mailchimp' ),
				'UserDisabled'           => __( 'This account has been disabled.', 'yith-woocommerce-mailchimp' ),
				'WrongDatacenter'        => __( 'The API key provided is linked to a different data center.', 'yith-woocommerce-mailchimp' ),
				'ResourceNotFound'       => __( 'The requested resource could not be found.', 'yith-woocommerce-mailchimp' ),
				'MethodNotAllowed'       => __( 'The requested method and resource are not compatible. See the Allow header for this resource’s available methods.', 'yith-woocommerce-mailchimp' ),
				'ResourceNestingTooDeep' => __( 'The sub-resource requested is nested too deeply.', 'yith-woocommerce-mailchimp' ),
				'InvalidMethodOverride'  => __( 'You can only use the X-HTTP-Method-Override header with the POST method.', 'yith-woocommerce-mailchimp' ),
				'RequestedFieldsInvalid' => __( 'The fields requested from this resource are invalid.', 'yith-woocommerce-mailchimp' ),
				'TooManyRequests'        => __( 'You have exceeded the limit of 10 simultaneous connections.', 'yith-woocommerce-mailchimp' ),
				'InternalServerError'    => __( 'An unexpected internal error has occurred. Please contact Support for more information.', 'yith-woocommerce-mailchimp' ),
				'ComplianceRelated'      => __( 'This method has been disabled.', 'yith-woocommerce-mailchimp' ),
			);

			return apply_filters( 'yith_wcmc_available_translations', $translations );
		}
	}
}