<?php

namespace ACP\Export;

/**
 * Encryption and decryption class, which can be used to securely encrypt and decrypt strings.
 * Partially borrowed from https://github.com/ioncube/php-openssl-cryptor. Original license is included below
 * @since 1.0
 */

/**
 * --- ORIGINAL LICENSE ---
 * Available under the MIT License
 * The MIT License (MIT)
 * Copyright (c) 2016 ionCube Ltd.
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of
 * the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT
 * OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

use Exception;

/**
 * Encryption/decryption class
 * @since 1.0
 */
class Cryptor {

	/**
	 * Cipher method to be used. Must be supported by openssl_encrypt()
	 * @since 1.0
	 * @var string
	 */
	private $cipher_algorithm;

	/**
	 * Hashing method to be used for hashing the encryption key right before encryption/decryption
	 * @since 1.0
	 * @var string
	 */
	private $hash_algorithm;

	/**
	 * Constructor
	 *
	 * @param string $cipher_algorithm Cypher algorithm supported by openssl_encrypt()
	 * @param string $hash_algorithm   Hash algorithm supported by openssl_digest()
	 *
	 * @throws Exception
	 * @since 1.0
	 */
	public function __construct( $cipher_algorithm = 'aes-256-ctr', $hash_algorithm = 'sha256' ) {
		if ( ! in_array( $cipher_algorithm, openssl_get_cipher_methods( true ) ) ) {
			throw new Exception( 'Unknown cipher algorithm specified' );
		}

		if ( ! in_array( $hash_algorithm, openssl_get_md_methods( true ) ) ) {
			throw new Exception( 'Unknown hash algorithm specified' );
		}

		// Store cipher and hash algorithm preferences
		$this->cipher_algorithm = $cipher_algorithm;
		$this->hash_algorithm = $hash_algorithm;
	}

	/**
	 * Encrypt a string using a key
	 *
	 * @param string $data Input data to encrypt
	 * @param bool   $key
	 *
	 * @return array Array consisting of two values: the encrypted data prefixed by the
	 *   initialization vector (with array key "result"), and the encryption key used for
	 *   encryption (with array key "key")
	 * @throws Exception
	 * @since 1.0
	 */
	public function encrypt( $data, $key = false ) {
		// Generate initialization vector
		$iv_length = openssl_cipher_iv_length( $this->cipher_algorithm );
		$iv = openssl_random_pseudo_bytes( $iv_length );

		// Generate key if it isn't set
		$key_use = ( $key === false ) ? Utility\Users::get_user_encryption_key() : $key;

		// Hash encryption key
		$key_hashed = $this->prepare_key( $key_use );

		// Perform encryption
		$data_encrypted = openssl_encrypt( $data, $this->cipher_algorithm, $key_hashed, OPENSSL_RAW_DATA, $iv );

		if ( $data_encrypted === false ) {
			throw new Exception( 'Encryption failed: ' . openssl_error_string() );
		}

		// Return the resulting string and the key used for encryption
		return [
			'result' => $iv . $data_encrypted,
			'key'    => $key_use,
		];
	}

	/**
	 * Decrypt a piece of encrypted data
	 *
	 * @param string $input Encrypted data to decrypt (prefixed by the initialization vector)
	 * @param string $key   Key to use for decryption
	 *
	 * @return string Decrypted data
	 * @throws Exception
	 * @since 1.0
	 */
	public function decrypt( $input, $key ) {
		// Retrieve initialization vector
		$iv_length = openssl_cipher_iv_length( $this->cipher_algorithm );
		$iv = substr( $input, 0, $iv_length );

		// Retrieve encrypted data
		$data_encrypted = substr( $input, $iv_length );

		// Hash encryption key
		$key_hashed = $this->prepare_key( $key );

		// Perform encryption
		$data_decrypted = openssl_decrypt( $data_encrypted, $this->cipher_algorithm, $key_hashed, OPENSSL_RAW_DATA, $iv );

		if ( $data_decrypted === false ) {
			throw new Exception( 'Decryption failed: ' . openssl_error_string() );
		}

		return $data_decrypted;
	}

	/**
	 * Prepare an encryption key for use
	 *
	 * @param string $key Encryption key
	 *
	 * @return string Prepared (hashed) encryption key
	 * @since 1.0
	 */
	private function prepare_key( $key ) {
		return openssl_digest( $key, $this->hash_algorithm, true );
	}

}