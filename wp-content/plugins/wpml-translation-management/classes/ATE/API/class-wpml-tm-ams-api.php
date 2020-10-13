<?php

use WPML\TM\ATE\ClonedSites\FingerprintGenerator;
use WPML\TM\ATE\Log\Entry;
use WPML\TM\ATE\Log\ErrorEvents;
use WPML\TM\ATE\ClonedSites\ApiCommunication as ClonedSitesHandler;

/**
 * @author OnTheGo Systems
 */
class WPML_TM_AMS_API {

	const HTTP_ERROR_CODE_400 = 400;

	private $auth;
	private $endpoints;
	private $wp_http;

	/**
	 * @var ClonedSitesHandler
	 */
	private $clonedSitesHandler;

	/**
	 * @var FingerprintGenerator
	 */
	private $fingerprintGenerator;


	/**
	 * WPML_TM_ATE_API constructor.
	 *
	 * @param WP_Http                    $wp_http
	 * @param WPML_TM_ATE_Authentication $auth
	 * @param WPML_TM_ATE_AMS_Endpoints  $endpoints
	 * @param ClonedSitesHandler  $clonedSitesHandler
	 * @param FingerprintGenerator $fingerprintGenerator
	 */
	public function __construct(
		WP_Http $wp_http,
		WPML_TM_ATE_Authentication $auth,
		WPML_TM_ATE_AMS_Endpoints $endpoints,
		ClonedSitesHandler $clonedSitesHandler,
		FingerprintGenerator $fingerprintGenerator
	) {
		$this->wp_http   = $wp_http;
		$this->auth      = $auth;
		$this->endpoints = $endpoints;
		$this->clonedSitesHandler = $clonedSitesHandler;
		$this->fingerprintGenerator = $fingerprintGenerator;
	}

	/**
	 * @param string $translator_email
	 *
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function enable_subscription( $translator_email ) {
		$result = null;

		$verb = 'PUT';
		$url  = $this->endpoints->get_enable_subscription();
		$url  = str_replace( '{translator_email}', base64_encode( $translator_email ), $url );

		$response = $this->signed_request( $verb, $url );

		if ( $this->response_has_body( $response ) ) {

			$result = $this->get_errors( $response );

			if ( ! is_wp_error( $result ) ) {
				$result = json_decode( $response['body'], true );
			}
		}

		return $result;
	}

	/**
	 * @param string $translator_email
	 *
	 * @return bool|WP_Error
	 */
	public function is_subscription_activated( $translator_email ) {
		$result = null;

		$url = $this->endpoints->get_subscription_status();

		$url = str_replace( '{translator_email}', base64_encode( $translator_email ), $url );
		$url = str_replace( '{WEBSITE_UUID}', $this->auth->get_site_id(), $url );

		$response = $this->signed_request( 'GET', $url );

		if ( $this->response_has_body( $response ) ) {

			$result = $this->get_errors( $response );

			if ( ! is_wp_error( $result ) ) {
				$result = json_decode( $response['body'], true );
				$result = $result['subscription'];
			}
		}

		return $result;
	}

	/**
	 * @return array|mixed|null|object|WP_Error
	 *
	 * @throws \InvalidArgumentException Exception.
	 */
	public function get_status() {
		$result = null;

		$registration_data = $this->get_registration_data();
		$shared            = array_key_exists( 'shared', $registration_data ) ? $registration_data['shared'] : null;

		if ( $shared ) {
			$url = $this->endpoints->get_ams_status();
			$url = str_replace( '{SHARED_KEY}', $shared, $url );

			$response = $this->request( 'GET', $url );

			if ( $this->response_has_body( $response ) ) {
				$response_body = json_decode( $response['body'], true );

				$result = $this->get_errors( $response );

				if ( ! is_wp_error( $result ) ) {
					$registration_data = $this->get_registration_data();
					if ( isset( $response_body['activated'] ) && (bool) $response_body['activated'] ) {
						$registration_data['status'] = WPML_TM_ATE_Authentication::AMS_STATUS_ACTIVE;
						$this->set_registration_data( $registration_data );
					}
					$result = $response_body;
				}
			}
		}

		return $result;
	}

	/**
	 * Used to register a manager and, at the same time, create a website in AMS.
	 * This is called only when registering the site with AMS.
	 * To register new managers or translators `\WPML_TM_ATE_AMS_Endpoints::get_ams_synchronize_managers`
	 * and `\WPML_TM_ATE_AMS_Endpoints::get_ams_synchronize_translators` will be used.
	 *
	 * @param WP_User   $manager     The WP_User instance of the manager.
	 * @param WP_User[] $translators An array of WP_User instances representing the current translators.
	 * @param WP_User[] $managers    An array of WP_User instances representing the current managers.
	 *
	 * @return array|bool|null|WP_Error
	 */
	public function register_manager( WP_User $manager, array $translators, array $managers ) {
		static $recreate_site_id = false;

		$manager_data     = $this->get_user_data( $manager, true );
		$translators_data = $this->get_users_data( $translators );
		$managers_data    = $this->get_users_data( $managers, true );

		$result = null;

		if ( $manager_data ) {
			$url = $this->endpoints->get_ams_register_client();

			$params                 = $manager_data;
			$params['website_url']  = get_site_url();
			$params['website_uuid'] = wpml_get_site_id( WPML_TM_ATE::SITE_ID_SCOPE, $recreate_site_id );

			$params['translators']          = $translators_data;
			$params['translation_managers'] = $managers_data;

			$response = $this->request( 'POST', $url, $params );

			if ( $this->response_has_body( $response ) ) {
				$response_body = json_decode( $response['body'], true );

				$result = $this->get_errors( $response );

				if ( ! is_wp_error( $result ) && $this->response_has_keys( $response ) ) {

					$registration_data = $this->get_registration_data();

					$registration_data['user_id'] = $manager->ID;
					$registration_data['secret']  = $response_body['secret_key'];
					$registration_data['shared']  = $response_body['shared_key'];
					$registration_data['status']  = WPML_TM_ATE_Authentication::AMS_STATUS_ENABLED;

					$result = $this->set_registration_data( $registration_data );
				}

				if ( is_wp_error( $result ) && $result->get_error_code() === 409 && ! $recreate_site_id ) {
					$recreate_site_id = true;
					return $this->register_manager( $manager, $translators, $managers );
				}
			}
		}

		return $result;
	}

	/**
	 * Gets the data required by AMS to register a user.
	 *
	 * @param WP_User $wp_user           The user from which data should be extracted.
	 * @param bool    $with_name_details True if name details should be included.
	 *
	 * @return array
	 */
	private function get_user_data( WP_User $wp_user, $with_name_details = false ) {
		$data = array();

		$data['email'] = $wp_user->user_email;

		if ( $with_name_details ) {
			$data['display_name'] = $wp_user->display_name;
			$data['first_name']   = $wp_user->first_name;
			$data['last_name']    = $wp_user->last_name;
		} else {
			$data['name'] = $wp_user->display_name;
		}

		return $data;
	}

	private function prepareClonedSiteArguments( $method ) {
		$headers = [
			'Accept'                              => 'application/json',
			'Content-Type'                        => 'application/json',
			FingerprintGenerator::NEW_SITE_FINGERPRINT_HEADER => $this->fingerprintGenerator->getSiteFingerprint(),
		];

		return [
			'method'  => $method,
			'headers' => $headers,
		];
	}

	/**
	 * @return array|WP_Error
	 */
	public function reportCopiedSite() {
		return $this->processReport(
			$this->endpoints->get_ams_site_copy(),
			'POST'
		);
	}

	/**
	 * @return array|WP_Error
	 */
	public function reportMovedSite() {
		return $this->processReport(
			$this->endpoints->get_ams_site_move(),
			'PUT'
		);
	}

	/**
	 * @param array $response Response from reportMovedSite()
	 *
	 * @return bool|WP_Error
	 */
	public function processMoveReport( $response ) {
		if ( ! $this->response_has_body( $response ) ) {
			return new WP_Error( 'auth_error', 'Unable to report site moved.' );
		}

		$response_body = json_decode( $response['body'], true );
		if ( isset( $response_body['moved_successfully'] ) && (bool) $response_body['moved_successfully'] ) {
			return true;
		}

		return new WP_Error( 'auth_error', 'Unable to report site moved.' );
	}

	/**
	 * @param array $response_body body from reportMovedSite() response.
	 *
	 * @return bool
	 */
	private function storeAuthData( $response_body ) {
		$setRegistrationDataResult = $this->updateRegistrationData( $response_body );
		$setUuidResult             = $this->updateSiteUuId( $response_body );

		return $setRegistrationDataResult && $setUuidResult;
	}

	/**
	 * @param array $response_body body from reportMovedSite() response.
	 *
	 * @return bool
	 */
	private function updateRegistrationData( $response_body ) {
		$registration_data = $this->get_registration_data();

		$registration_data['secret'] = $response_body['new_secret_key'];
		$registration_data['shared'] = $response_body['new_shared_key'];

		return $this->set_registration_data( $registration_data );
	}

	/**
	 * @param array $response_body body from reportMovedSite() response.
	 *
	 * @return bool
	 */
	private function updateSiteUuId( $response_body ) {
		$this->override_site_id( $response_body['new_website_uuid'] );

		return update_option(
			WPML_Site_ID::SITE_ID_KEY . ':ate',
			$response_body['new_website_uuid'],
			false
		);
	}

	private function sendSiteReportConfirmation() {
		$url    = $this->endpoints->get_ams_site_confirm();
		$method = 'POST';

		$args = $this->prepareClonedSiteArguments( $method );

		$url_parts = wp_parse_url( $url );

		$registration_data         = $this->get_registration_data();
		$query['new_shared_key']   = $registration_data['shared'];
		$query['token']            = uuid_v5( wp_generate_uuid4(), $url );
		$query['new_website_uuid'] = $this->auth->get_site_id();
		$url_parts['query']        = http_build_query( $query );

		$url = http_build_url( $url_parts );


		$signed_url = $this->auth->signUrl( $method, $url );

		$response = $this->wp_http->request( $signed_url, $args );

		if ( $this->response_has_body( $response ) ) {
			$response_body = json_decode( $response['body'], true );

			return (bool) $response_body['confirmed'];
		}

		return new WP_Error( 'auth_error', 'Unable confirm site copied.' );
	}

	/**
	 * @param string $url
	 * @param string $method
	 *
	 * @return array|WP_Error
	 */
	private function processReport( $url, $method ) {
		$args = $this->prepareClonedSiteArguments( $method );

		$url_parts = wp_parse_url( $url );

		$registration_data     = $this->get_registration_data();
		$query['shared_key']   = $registration_data['shared'];
		$query['token']        = uuid_v5( wp_generate_uuid4(), $url );
		$query['website_uuid'] = $this->auth->get_site_id();
		$url_parts['query']    = http_build_query( $query );

		$url = http_build_url( $url_parts );

		$signed_url = $this->auth->signUrl( $method, $url );

		return $this->wp_http->request( $signed_url, $args );
	}

	/**
	 * @param array $response Response from reportCopiedSite()
	 *
	 * @return bool
	 */
	public function processCopyReportConfirmation( $response ) {
		if ( $this->response_has_body( $response ) ) {
			$response_body = json_decode( $response['body'], true );
			return $this->storeAuthData( $response_body ) && (bool) $this->sendSiteReportConfirmation();
		}

		return false;
	}

	/**
	 * Converts an array of WP_User instances into an array of data nedded by AMS to identify users.
	 *
	 * @param WP_User[] $users             An array of WP_User instances.
	 * @param bool      $with_name_details True if name details should be included.
	 *
	 * @return array
	 */
	private function get_users_data( array $users, $with_name_details = false ) {
		$user_data = array();

		foreach ( $users as $user ) {
			$wp_user     = get_user_by( 'id', $user->ID );
			$user_data[] = $this->get_user_data( $wp_user, $with_name_details );
		}

		return $user_data;
	}

	/**
	 * Checks if a reponse has a body.
	 *
	 * @param array|\WP_Error $response The response of the remote request.
	 *
	 * @return bool
	 */
	private function response_has_body( $response ) {
		return ! is_wp_error( $response ) && array_key_exists( 'body', $response );
	}

	private function get_errors( array $response ) {
		$response_errors = null;

		if ( is_wp_error( $response ) ) {
			$response_errors = $response;
		} elseif ( array_key_exists( 'body', $response ) && $response['response']['code'] >= self::HTTP_ERROR_CODE_400 ) {
			$main_error    = array();
			$errors        = array();
			$error_message = $response['response']['message'];

			$response_body = json_decode( $response['body'], true );
			if ( ! $response_body ) {
				$error_message = $response['body'];
				$main_error    = array( $response['body'] );
			} elseif ( array_key_exists( 'errors', $response_body ) ) {
				$errors     = $response_body['errors'];
				$main_error = array_shift( $errors );
				$error_message = $this->get_error_message( $main_error, $response['body'] );
			}

			$response_errors = new WP_Error( $main_error['status'], $error_message, $main_error );

			foreach ( $errors as $error ) {
				$error_message = $this->get_error_message( $error, $response['body'] );
				$error_status = isset( $error['status'] ) ? 'ams_error: ' . $error['status'] : '';
				$response_errors->add( $error_status, $error_message, $error );
			}
		}

		if ( $response_errors ) {
			$entry              = new Entry();
			$entry->event       = ErrorEvents::SERVER_AMS;
			$entry->description = $response_errors->get_error_message();
			$entry->extraData   = [
				'errorData' => $response_errors->get_error_data(),
			];

			wpml_tm_ate_ams_log( $entry );
		}

		return $response_errors;
	}

	/**
	 * @param array  $ams_error
	 * @param string $default
	 *
	 * @return string
	 */
	private function get_error_message( $ams_error, $default ) {
		$title   = isset( $ams_error['title'] ) ? $ams_error['title'] . ': ' : '';
		$details = isset( $ams_error['detail'] ) ? $ams_error['detail'] : $default;
		return $title . $details;
	}

	private function response_has_keys( $response ) {
		if ( $this->response_has_body( $response ) ) {
			$response_body = json_decode( $response['body'], true );

			return array_key_exists( 'secret_key', $response_body )
			       && array_key_exists( 'shared_key', $response_body );
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function get_registration_data() {
		return get_option( WPML_TM_ATE_Authentication::AMS_DATA_KEY, [] );
	}

	/**
	 * @param $registration_data
	 *
	 * @return bool
	 */
	private function set_registration_data( $registration_data ) {
		return update_option( WPML_TM_ATE_Authentication::AMS_DATA_KEY, $registration_data );
	}

	/**
	 * @param array $managers
	 *
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function synchronize_managers( array $managers ) {
		$result = null;

		$managers_data = $this->get_users_data( $managers, true );

		if ( $managers_data ) {
			$url = $this->endpoints->get_ams_synchronize_managers();
			$url = str_replace( '{WEBSITE_UUID}', wpml_get_site_id( WPML_TM_ATE::SITE_ID_SCOPE ), $url );

			$params = array( 'translation_managers' => $managers_data );

			$response = $this->signed_request( 'PUT', $url, $params );

			if ( $this->response_has_body( $response ) ) {
				$response_body = json_decode( $response['body'], true );

				$result = $this->get_errors( $response );

				if ( ! is_wp_error( $result ) ) {
					$result = $response_body;
				}
			}
		}

		return $result;
	}

	/**
	 * @param array $translators
	 *
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function synchronize_translators( array $translators ) {
		$result = null;

		$translators_data = $this->get_users_data( $translators );

		if ( $translators_data ) {
			$url = $this->endpoints->get_ams_synchronize_translators();

			$params = array( 'translators' => $translators_data );

			$response = $this->signed_request( 'PUT', $url, $params );

			if ( $this->response_has_body( $response ) ) {
				$response_body = json_decode( $response['body'], true );

				$result = $this->get_errors( $response );

				if ( ! is_wp_error( $result ) ) {
					$result = $response_body;
				}
			}
		}

		return $result;
	}

	/**
	 * @param string     $method
	 * @param string     $url
	 * @param array|null $params
	 *
	 * @return array|WP_Error
	 */
	private function request( $method, $url, array $params = null ) {
		$lock = $this->clonedSitesHandler->checkCloneSiteLock();
		if ($lock) {
			return $lock;
		}

		$method = strtoupper( $method );
		$headers = [
			'Accept'                                      => 'application/json',
			'Content-Type'                                => 'application/json',
			FingerprintGenerator::SITE_FINGERPRINT_HEADER => $this->fingerprintGenerator->getSiteFingerprint(),
		];

		$args = [
			'method'  => $method,
			'headers' => $headers,
		];

		if ( $params ) {
			$args['body'] = wp_json_encode( $params, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
		}

		$response = $this->wp_http->request( $this->add_versions_to_url( $url ), $args );

		if ( !is_wp_error( $response ) ) {
			$response = $this->clonedSitesHandler->handleClonedSiteError($response);
		}

		return $response;
	}

	/**
	 * @param string     $verb
	 * @param string     $url
	 * @param array|null $params
	 *
	 * @return array|WP_Error
	 */
	private function signed_request( $verb, $url, array $params = null ) {
		$verb       = strtoupper( $verb );
		$signed_url = $this->auth->get_signed_url_with_parameters( $verb, $url, $params );

		return $this->request( $verb, $signed_url, $params );
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	private function add_versions_to_url( $url ) {
		$url_parts = wp_parse_url( $url );
		$query     = array();
		if ( array_key_exists( 'query', $url_parts ) ) {
			parse_str( $url_parts['query'], $query );
		}
		$query['wpml_core_version'] = ICL_SITEPRESS_VERSION;
		$query['wpml_tm_version']   = WPML_TM_VERSION;

		$url_parts['query'] = http_build_query( $query );
		$url                = http_build_url( $url_parts );

		return $url;
	}

	public function override_site_id( $site_id ) {
		$this->auth->override_site_id( $site_id);
	}

}
