<?php

class wfWafApiException extends Exception {
}

class wfWafApi {

	private $waf;

	public function __construct($waf) {
		$this->waf = $waf;
	}

	private function getConfig($key) {
		return $this->waf->getStorageEngine()->getConfig($key, null, 'synced');
	}

	private function guessSiteUrl() {
		return sprintf('%s://%s/', $this->waf->getRequest()->getProtocol(), $this->waf->getRequest()->getHost());
	}

	private function guessSiteUrlIfNecessary($configKey) {
		$url = $this->getConfig($configKey);
		if (!$url)
			$url = $this->guessSiteUrl();
		return $url;
	}

	private function getSiteUrl() {
		return $this->guessSiteUrlIfNecessary('siteURL');
	}

	private function getHomeUrl() {
		return $this->guessSiteUrlIfNecessary('homeURL');
	}

	private function buildQueryString($additionalParameters = array()) {
		$parameters = array(
			'k' => $this->getConfig('apiKey'),
			's' => $this->getSiteUrl(),
			'h' => $this->getHomeUrl(),
			't' => microtime(true),
			'lang' => $this->getConfig('WPLANG')
		);
		$parameters = array_merge($parameters, $additionalParameters);
		return http_build_query($parameters, '', '&');
	}

	private function buildUrl($queryParameters, $path = '') {
		return WFWAF_API_URL_SEC . $path . '?' . $this->buildQueryString($queryParameters);
	}

	public function actionGet($action, $parameters = array()) {
		$parameters['action'] = $action;
		$url = $this->buildUrl($parameters);
		$response = wfWAFHTTP::get($url);
		if ($response === false)
			throw new wfWafApiException('Request failed');
		return $response;
	}

}