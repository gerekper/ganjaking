<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access.');

/**
 * This script must be run on PHP 5.3, the version check must be done before calling this file. When running on PHP 5.2, the reason to skip this script is because we hope something else would load/include the v1 Crypt/Random.php file so that our existing code is still able to call the crypt_random_string function, there's a small chance that this could happen. However, if that doesn't happen then this will end up with PHP's "Call to undefined function crypt_random_string()" fatal error
 */
if (!function_exists('crypt_random_string')) {
	/**
	 * Generate a phpseclib random string. This function is a wrapper of the phpseclib v1 crypt_random_string function.
	 *
	 * @param Integer $length How many chars to be outputed
	 * @return String The random string
	 */
	function crypt_random_string($length) {
		return call_user_func(array('phpseclib\Crypt\Random', 'string'), $length);
	}
}

if (!defined('CRYPT_RSA_SIGNATURE_PKCS1')) define('CRYPT_RSA_SIGNATURE_PKCS1', phpseclib\Crypt\RSA::SIGNATURE_PKCS1); // class-udrpc.php
if (!defined('CRYPT_RSA_SIGNATURE_PSS')) define('CRYPT_RSA_SIGNATURE_PSS', phpseclib\Crypt\RSA::SIGNATURE_PSS); // class-udrpc.php
if (!defined('CRYPT_AES_MODE_CBC')) define('CRYPT_AES_MODE_CBC', phpseclib\Crypt\Base::MODE_CBC); // class-udrpc.php
if (!defined('NET_SFTP_LOCAL_FILE')) define('NET_SFTP_LOCAL_FILE', phpseclib\Net\SFTP::SOURCE_LOCAL_FILE); // addons/sftp.php
if (!defined('NET_SCP_LOCAL_FILE')) define('NET_SCP_LOCAL_FILE', phpseclib\Net\SCP::SOURCE_LOCAL_FILE); // addons/sftp.php
if (!defined('NET_SSH2_LOG_COMPLEX')) define('NET_SSH2_LOG_COMPLEX', phpseclib\Net\SSH2::LOG_COMPLEX); // addons/sftp.php
if (!defined('CRYPT_ENGINE_INTERNAL')) define('CRYPT_ENGINE_INTERNAL', phpseclib\Crypt\Base::ENGINE_INTERNAL); // includes/class-updraftplus-encryption.php
if (!defined('CRYPT_ENGINE_MCRYPT')) define('CRYPT_ENGINE_MCRYPT', phpseclib\Crypt\Base::ENGINE_MCRYPT);  // includes/class-updraftplus-encryption.php
if (!defined('CRYPT_ENGINE_OPENSSL')) define('CRYPT_ENGINE_OPENSSL', phpseclib\Crypt\Base::ENGINE_OPENSSL); // includes/class-updraftplus-encryption.php
