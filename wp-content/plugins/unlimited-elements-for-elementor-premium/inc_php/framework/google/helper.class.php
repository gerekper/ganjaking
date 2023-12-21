<?php

class UEGoogleAPIHelper{

	const AUTH_URL = "https://accounts.google.com/o/oauth2/v2/auth";
	const AUTH_CLIENT_ID = "852030113875-d858hv695ki288ha3qk2s3qifng6ra9e.apps.googleusercontent.com"; // TODO: Replace with a real one
	const AUTH_REDIRECT_URL = "https://dev.unlimited-elements.com/google-connect/connect.php"; // TODO: Replace with a real one

	const SCOPE_CALENDAR_EVENTS = "https://www.googleapis.com/auth/calendar.events.readonly";
	const SCOPE_SHEETS_ALL = "https://www.googleapis.com/auth/spreadsheets";
	const SCOPE_USER_EMAIL = "https://www.googleapis.com/auth/userinfo.email";
	const SCOPE_YOUTUBE = "https://www.googleapis.com/auth/youtube.readonly";

	private static $credentials = array();

	/**
	 * Get the API key.
	 *
	 * @return string
	 */
	public static function getApiKey(){

		$apiKey = HelperProviderCoreUC_EL::getGeneralSetting("google_api_key");

		return $apiKey;
	}

	/**
	 * Get the access token.
	 *
	 * @return string
	 */
	public static function getAccessToken(){

		$credentials = self::getCredentials();
		$accessToken = UniteFunctionsUC::getVal($credentials, "access_token");

		return $accessToken;
	}

	/**
	 * Get the fresh access token.
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function getFreshAccessToken(){

		if(self::isAccessTokenExpired() === true)
			self::refreshAccessToken();

		$accessToken = self::getAccessToken();

		return $accessToken;
	}

	/**
	 * Get the user's email address.
	 *
	 * @return string
	 */
	public static function getUserEmail(){

		$credentials = self::getCredentials();
		$user = UniteFunctionsUC::getVal($credentials, "user", array());
		$email = UniteFunctionsUC::getVal($user, "email");

		return $email;
	}

	/**
	 * Get the authorization URL.
	 *
	 * @return string
	 */
	public static function getAuthUrl(){

		$returnUrl = HelperUC::getUrlAjax("save_google_connect_data");
		$returnUrl = UniteFunctionsUC::encodeContent($returnUrl);

		$params = array(
			"client_id" => self::AUTH_CLIENT_ID,
			"redirect_uri" => self::AUTH_REDIRECT_URL,
			"scope" => implode(" ", self::getScopes()),
			"access_type" => "offline",
			"prompt" => "consent select_account",
			"response_type" => "code",
			"include_granted_scopes" => "true",
			"state" => $returnUrl,
		);

		$url = self::AUTH_URL . "?" . http_build_query($params);

		return $url;
	}

	/**
	 * Get the revoke URL.
	 *
	 * @return string
	 */
	public static function getRevokeUrl(){

		$returnUrl = HelperUC::getUrlAjax("remove_google_connect_data");
		$returnUrl = UniteFunctionsUC::encodeContent($returnUrl);

		$params = array(
			"revoke_token" => self::getAccessToken(),
			"state" => $returnUrl,
		);

		$url = self::AUTH_REDIRECT_URL . "?" . http_build_query($params);

		return $url;
	}

	/**
	 * Save the credentials.
	 *
	 * @param array $data
	 *
	 * @return void
	 * @throws Exception
	 */
	public static function saveCredentials($data){

		$accessToken = UniteFunctionsUC::getVal($data, "access_token");
		$refreshToken = UniteFunctionsUC::getVal($data, "refresh_token");
		$expiresAt = UniteFunctionsUC::getVal($data, "expires_at", 0);
		$scopes = UniteFunctionsUC::getVal($data, "scopes", array());
		$user = UniteFunctionsUC::getVal($data, "user", array());

		UniteFunctionsUC::validateNotEmpty($accessToken, "access_token");
		UniteFunctionsUC::validateNotEmpty($refreshToken, "refresh_token");
		UniteFunctionsUC::validateNotEmpty($expiresAt, "expires_at");
		UniteFunctionsUC::validateNotEmpty($scopes, "scopes");
		UniteFunctionsUC::validateNotEmpty($user, "user");

		self::validateScopes($scopes);

		$credentials = array(
			"access_token" => $accessToken,
			"refresh_token" => $refreshToken,
			"expires_at" => $expiresAt,
			"scopes" => $scopes,
			"user" => $user,
		);

		self::setCredentials($credentials);
	}

	/**
	 * Remove the credentials.
	 *
	 * @return void
	 */
	public static function removeCredentials(){

		self::setCredentials(array());
	}

	/**
	 * Redirect to settings.
	 *
	 * @param array $params
	 *
	 * @return void
	 */
	public static function redirectToSettings($params = array()){

		$params = http_build_query($params) . "#tab=integrations";
		$url = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_SETTINGS_ELEMENTOR, $params);

		UniteFunctionsUC::redirectToUrl($url);
	}

	/**
	 * Get the scopes.
	 *
	 * @return array
	 */
	private static function getScopes(){

		$scopes = array(
			self::SCOPE_SHEETS_ALL,
			self::SCOPE_USER_EMAIL,
		);

		if(GlobalsUnlimitedElements::$enableGoogleCalendarScopes === true)
			$scopes[] = self::SCOPE_CALENDAR_EVENTS;

		if(GlobalsUnlimitedElements::$enableGoogleYoutubeScopes === true)
			$scopes[] = self::SCOPE_YOUTUBE;

		return $scopes;
	}

	/**
	 * Validate the scopes.
	 *
	 * @param array $scopes
	 *
	 * @return void
	 * @throws Exception
	 */
	private static function validateScopes($scopes){

		$requestedScopes = self::getScopes();
		$grantedScopes = array_intersect($requestedScopes, $scopes);

		if(count($grantedScopes) !== count($requestedScopes))
			UniteFunctionsUC::throwError("Required permissions are missing. Please grant all requested permissions.");
	}

	/**
	 * Get the credentials.
	 *
	 * @return array
	 */
	private static function getCredentials(){

		if(empty(self::$credentials) === true)
			self::$credentials = HelperProviderUC::getGoogleConnectCredentials();

		return self::$credentials;
	}

	/**
	 * Set the credentials.
	 *
	 * @param array $credentials
	 *
	 * @return void
	 */
	private static function setCredentials($credentials){

		self::$credentials = $credentials;

		HelperProviderUC::saveGoogleConnectCredentials(self::$credentials);
	}

	/**
	 * Merge the credentials.
	 *
	 * @param array $credentials
	 *
	 * @return void
	 */
	private static function mergeCredentials($credentials){

		$credentials = array_merge(self::getCredentials(), $credentials);

		self::setCredentials($credentials);
	}

	/**
	 * Get the refresh token.
	 *
	 * @return string
	 */
	private static function getRefreshToken(){

		$credentials = self::getCredentials();
		$refreshToken = UniteFunctionsUC::getVal($credentials, "refresh_token");

		return $refreshToken;
	}

	/**
	 * Get the expiration time.
	 *
	 * @return int
	 */
	private static function getExpirationTime(){

		$credentials = self::getCredentials();
		$expirationTime = UniteFunctionsUC::getVal($credentials, "expires_at", 0);

		return $expirationTime;
	}

	/**
	 * Determine if the access token is expired.
	 *
	 * @return bool
	 */
	private static function isAccessTokenExpired(){

		$accessToken = self::getAccessToken();
		$expirationTime = self::getExpirationTime();

		if(empty($accessToken) === true)
			return true;

		// Check if the token expires in the next 30 seconds
		if($expirationTime - 30 < time())
			return true;

		return false;
	}

	/**
	 * Refresh the access token.
	 *
	 * @return void
	 * @throws Exception
	 */
	private static function refreshAccessToken(){

		$refreshToken = self::getRefreshToken();

		if(empty($refreshToken) === true)
			UniteFunctionsUC::throwError("Refresh token is missing.");

		$params = array("refresh_token" => $refreshToken);
		$url = self::AUTH_REDIRECT_URL . "?" . http_build_query($params);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($curl);
		$response = json_decode($response, true);

		$error = curl_error($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if($error)
			UniteFunctionsUC::throwError("Unable to execute the request: $error");

		if($response === null)
			UniteFunctionsUC::throwError("Unable to parse the response (status code $code).");

		if(empty($response["error"]) === false)
			UniteFunctionsUC::throwError("Unable to refresh the access token: {$response["error"]}");

		$accessToken = UniteFunctionsUC::getVal($response, "access_token");
		$expiresAt = UniteFunctionsUC::getVal($response, "expires_at", 0);
		$scopes = UniteFunctionsUC::getVal($response, "scopes", array());

		UniteFunctionsUC::validateNotEmpty($accessToken, "access_token");
		UniteFunctionsUC::validateNotEmpty($expiresAt, "expires_at");
		UniteFunctionsUC::validateNotEmpty($scopes, "scopes");

		self::validateScopes($scopes);

		$credentials = array(
			"access_token" => $accessToken,
			"expires_at" => $expiresAt,
			"scopes" => $scopes,
		);

		self::mergeCredentials($credentials);
	}

}
