<?php

namespace ACP\Entity;

use ACP\Type\License\ExpiryDate;
use ACP\Type\License\Key;
use ACP\Type\License\RenewalDiscount;
use ACP\Type\License\RenewalMethod;
use ACP\Type\License\Status;

final class License {

	/**
	 * @var Key
	 */
	private $key;

	/**
	 * @var Status
	 */
	private $status;

	/**
	 * @var RenewalDiscount
	 */
	private $renewal_discount;

	/**
	 * @var RenewalMethod
	 */
	private $renewal_method;

	/**
	 * @var ExpiryDate
	 */
	private $expiry_date;

	public function __construct(
		Key $key,
		Status $status,
		RenewalDiscount $renewal_discount,
		RenewalMethod $renewal_method,
		ExpiryDate $expiry_date
	) {
		$this->key = $key;
		$this->status = $status;
		$this->renewal_discount = $renewal_discount;
		$this->renewal_method = $renewal_method;
		$this->expiry_date = $expiry_date;
	}

	/**
	 * @return Key
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * @return ExpiryDate
	 */
	public function get_expiry_date() {
		return $this->expiry_date;
	}

	/**
	 * @return bool
	 */
	public function is_lifetime() {
		return $this->expiry_date->is_lifetime();
	}

	/**
	 * @return bool
	 */
	public function is_expired() {
		return $this->expiry_date->is_expired();
	}

	/**
	 * @return RenewalDiscount
	 */
	public function get_renewal_discount() {
		return $this->renewal_discount;
	}

	/**
	 * @return RenewalMethod
	 */
	public function get_renewal_method() {
		return $this->renewal_method;
	}

	/**
	 * @return bool
	 */
	public function is_auto_renewal() {
		return $this->renewal_method->is_auto_renewal();
	}

	/**
	 * @return bool
	 */
	public function is_manual_renewal() {
		return $this->renewal_method->is_manual_renewal();
	}

	/**
	 * @return Status
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return $this->status->is_active();
	}

	/**
	 * @return bool
	 */
	public function is_cancelled() {
		return $this->status->is_cancelled();
	}

}