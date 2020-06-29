<?php

namespace ACP;

/**
 * @since 4.2.4
 */
final class License {

	const OPTION_KEY = 'cpupdate_cac-pro';

	/**
	 * @var string License key
	 */
	private $key;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var int Timestamp
	 */
	private $expiry_date;

	/**
	 * @var int Percentage
	 */
	private $renewal_discount;

	/** @var bool */
	private $is_network_active;

	/**
	 * @var string
	 */
	private $renewal_method;

	public function __construct( $is_network_active = false ) {
		$this->is_network_active = (bool) $is_network_active;

		$this->populate();
	}

	private function populate() {
		$this->set_key( defined( 'ACP_LICENCE' ) && ACP_LICENCE ? ACP_LICENCE : $this->get_option( self::OPTION_KEY ) )
		     ->set_status( $this->get_option( self::OPTION_KEY . '_sts' ) )
		     ->set_expiry_date( $this->get_option( self::OPTION_KEY . '_expiry_date' ) )
		     ->set_renewal_method( $this->get_option( self::OPTION_KEY . '_renewal_method' ) )
		     ->set_renewal_discount( $this->get_option( self::OPTION_KEY . '_renewal_discount' ) );
	}

	/**
	 * Store object vars into DB
	 */
	public function save() {
		foreach ( $this->mapping() as $var => $db_key ) {
			$this->update_option( self::OPTION_KEY . $db_key, $this->{$var} );
		}

		$this->populate();
	}

	/**
	 * Delete license setting from DB
	 */
	public function delete() {
		foreach ( $this->mapping() as $db_key ) {
			$this->delete_option( self::OPTION_KEY . $db_key );
		}

		$this->populate();
	}

	/**
	 * Maps object vars to their DB key
	 * @return array [ $var => $db_key ]
	 */
	private function mapping() {
		return array(
			'key'              => '',
			'status'           => '_sts',
			'expiry_date'      => '_expiry_date',
			'renewal_discount' => '_renewal_discount',
			'renewal_method'   => '_renewal_method',
		);
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return 'active' === $this->get_status();
	}

	/**
	 * @param string $key
	 *
	 * @return $this
	 */
	public function set_key( $key ) {
		$this->key = (string) $key;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * @return int timestamp
	 */
	public function get_expiry_date() {
		return $this->expiry_date;
	}

	protected function get_renewal_method() {
		return $this->renewal_method;
	}

	/**
	 * @param string $format days|seconds
	 *
	 * @return int|false Time ins seconds is returned or false when the expiry date hasn't been fetched yet.
	 */
	public function get_time_remaining( $format = 'seconds' ) {
		if ( ! $this->get_expiry_date() ) {
			return false;
		}

		$remaining = $this->get_expiry_date() - strtotime( 'midnight' );

		switch ( $format ) {
			case 'days':
				$remaining = floor( $remaining / DAY_IN_SECONDS );
		}

		return intval( $remaining );
	}

	/**
	 * @param int $date
	 *
	 * @return $this
	 */
	public function set_expiry_date( $date ) {
		if ( ! is_numeric( $date ) ) {
			$date = strtotime( $date );
		}

		$this->expiry_date = $date;

		return $this;
	}

	/**
	 * @param $renewal_method
	 *
	 * @return $this
	 */
	public function set_renewal_method( $renewal_method ) {
		$this->renewal_method = $renewal_method;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_auto_renew() {
		return 'auto' === $this->get_renewal_method();
	}

	/**
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * @param string $status
	 *
	 * @return $this
	 */
	public function set_status( $status ) {
		if ( '1' === $status || true === $status ) {
			$status = 'active';
		}

		if ( $status !== 'active' ) {
			$status = false;
		}

		$this->status = $status;

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_renewal_discount() {
		return $this->renewal_discount;
	}

	/**
	 * @param $discount
	 *
	 * @return $this
	 */
	public function set_renewal_discount( $discount ) {
		$this->renewal_discount = absint( $discount );

		return $this;
	}

	/**
	 * License needs to fetch remote data when the expiry date is empty.
	 * @return bool
	 */
	public function needs_update() {
		return ! $this->get_expiry_date();
	}

	/**
	 * @return bool
	 */
	public function is_expired() {
		return 0 >= $this->get_time_remaining();
	}

	/**
	 * @return bool
	 */
	public function has_expiry_date() {
		$time_remaining = $this->get_time_remaining();

		if ( ! $time_remaining ) {
			return false;
		}

		return $time_remaining > ( YEAR_IN_SECONDS * 10 );
	}

	/**
	 * @return bool
	 */
	private function is_network_managed_license() {
		return is_multisite() && $this->is_network_active;
	}

	/**
	 * @param string $option
	 * @param string $value
	 * @param bool   $autoload
	 *
	 * @return bool
	 */
	private function update_option( $option, $value, $autoload = false ) {
		return $this->is_network_managed_license()
			? update_site_option( $option, $value )
			: update_option( $option, $value, $autoload );
	}

	/**
	 * @param string $option
	 * @param bool   $default
	 *
	 * @return array|string
	 */
	private function get_option( $option, $default = false ) {
		return $this->is_network_managed_license()
			? get_site_option( $option, $default )
			: get_option( $option, $default );
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	private function delete_option( $option ) {
		return $this->is_network_managed_license()
			? delete_site_option( $option )
			: delete_option( $option );
	}

}