<?php

namespace ACP;

use ACP\Access\ActivationKeyStorage;
use ACP\Type\ActivationToken;

class ActivationTokenFactory {

	/**
	 * @var ActivationKeyStorage
	 */
	private $activation_key_storage;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_storage;

	public function __construct( ActivationKeyStorage $activation_key_storage, LicenseKeyRepository $license_key_storage ) {
		$this->activation_key_storage = $activation_key_storage;
		$this->license_key_storage = $license_key_storage;
	}

	/**
	 * @return ActivationToken|null
	 */
	public function create() {
		$token = $this->activation_key_storage->find();

		if ( ! $token ) {
			$token = $this->license_key_storage->find();
		}

		return $token;
	}
}