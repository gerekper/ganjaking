<?php

namespace WCML\MultiCurrency\ExchangeRateServices;

use WPML\FP\Obj;

abstract class ApiLayerService extends Service {

	/**
	 * @return string
	 */
	abstract protected function getApiLayerUrl();

	/**
	 * @return string
	 */
	abstract protected function getApiLegacyUrl();

	/**
	 * @return string
	 */
	public function getApiUrl() {
		return $this->getSelectedApiEndpoint();
	}

	/**
	 * @return void
	 */
	public function resetConnectionCache() {
		$this->setSelectedApiEndpoint( null );
	}

	/**
	 * @param string|null $endpoint
	 *
	 * @return void
	 */
	private function setSelectedApiEndpoint( $endpoint ) {
		$this->saveSetting( 'selected-endpoint', $endpoint );
	}

	/**
	 * @return string|null
	 */
	private function getSelectedApiEndpoint() {
		return $this->getSetting( 'selected-endpoint' );
	}

	/**
	 * @param string $from The base currency code.
	 * @param array  $tos  The target currency codes.
	 *
	 * @return array|\WP_Error
	 */
	protected function makeRequest( $from, $tos ) {
		if ( $this->getSelectedApiEndpoint() ) {
			$response = parent::makeRequest( $from, $tos );
		} else {
			$this->setSelectedApiEndpoint( $this->getApiLayerUrl() );
			$response = parent::makeRequest( $from, $tos );

			if ( $this->isWrongAuthenticationWithApiLayer( $response ) ) {
				$this->setSelectedApiEndpoint( $this->getApiLegacyUrl() );
				$response = parent::makeRequest( $from, $tos );
			}
		}

		return $response;
	}

	/**
	 * @param object $data
	 *
	 * @return bool
	 */
	private function isWrongAuthenticationWithApiLayer( $data ) {
		return Obj::path( [ 'response', 'code' ], $data ) === 401;
	}

	/**
	 * @return array
	 */
	protected function getRequestHeaders() {
		return [ 'apikey' => $this->getApiKey() ];
	}

	/**
	 * @return bool
	 */
	public function isKeyRequired() {
		return true;
	}
}
