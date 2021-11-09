<?php
// @codingStandardsIgnoreLine
namespace Onedrive;

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

final class UpdraftPlus_OneDrive_Account {
	
	public static $types = array(
		'com' => array(
			'client_id' => '276d9423-7d0c-41be-a3e1-4cdad89dc36f',
			'override_client_id_const_name' => 'UPDRAFTPLUS_ONEDRIVE_CLIENT_ID',

			'graph_url' => 'https://graph.microsoft.com',
			'api_url' => 'https://graph.microsoft.com/v1.0/',
			'auth_url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
			'token_url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
		),
		'de' => array(
			'client_id' => '7cc3beb4-daab-4a59-b091-c4c2319d8d2d',
			'override_client_id_const_name' => 'UPDRAFTPLUS_ONEDRIVE_GERMANY_CLIENT_ID',

			'graph_url' => 'https://graph.microsoft.de',
			'api_url' => 'https://graph.microsoft.de/v1.0/',
			'auth_url' => 'https://login.microsoftonline.de/common/oauth2/v2.0/authorize',
			'token_url' => 'https://login.microsoftonline.de/common/oauth2/v2.0/token',
		),
	);

	/**
	 * Get client id
	 *
	 * @param string $endpoint_tld Account type endpoint tld
	 * @return string Client id
	 */
	public static function get_client_id($endpoint_tld = 'com') {
		$valid_endpoint_tlds = array_keys(self::$types);
		if (in_array($endpoint_tld, $valid_endpoint_tlds)) {
			if (defined(self::$types[$endpoint_tld]['override_client_id_const_name'])) {
				return constant(self::$types[$endpoint_tld]['override_client_id_const_name']);
			} else {
				return self::$types[$endpoint_tld]['client_id'];
			}
		}
		return '';
	}
}
