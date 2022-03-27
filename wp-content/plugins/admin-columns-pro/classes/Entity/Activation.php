<?php

namespace ACP\Entity;

use ACP\Type\Activation\ExpiryDate;
use ACP\Type\Activation\Products;
use ACP\Type\Activation\RenewalMethod;
use ACP\Type\Activation\Status;

final class Activation {

	/**
	 * @var Status
	 */
	private $status;

	/**
	 * @var RenewalMethod
	 */
	private $renewal_method;

	/**
	 * @var ExpiryDate
	 */
	private $expiry_date;

	/**
	 * @var Products
	 */
	private $products;

	public function __construct( Status $status, RenewalMethod $renewal_method, ExpiryDate $expiry_date, Products $products ) {
		$this->status = $status;
		$this->renewal_method = $renewal_method;
		$this->expiry_date = $expiry_date;
		$this->products = $products;
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

	/**
	 * @return Products
	 */
	public function get_products() {
		return $this->products;
	}

}