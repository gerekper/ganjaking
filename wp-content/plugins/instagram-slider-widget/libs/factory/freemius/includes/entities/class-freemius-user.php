<?php

namespace WBCR\Factory_Freemius_111\Entities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, Alex Kovalev <alex.kovalevv@gmail.com>
 * @link          https://webcraftic.com
 * @copyright (c) 2018 Webraftic Ltd, Freemius, Inc.
 * @version       1.0
 */
class User extends Scope {

	/**
	 * @var string
	 */
	public $email;

	/**
	 * @var string
	 */
	public $first;

	/**
	 * @var string
	 */
	public $last;

	/**
	 * @var bool
	 */
	public $is_verified;

	/**
	 * @var string|null
	 */
	public $customer_id;

	/**
	 * @var float
	 */
	public $gross;

	/**
	 * @param object|bool $user
	 */
	public function __construct( $user = false ) {
		parent::__construct( $user );
		$props = get_object_vars( $this );

		foreach ( $props as $key => $def_value ) {
			$this->{$key} = isset( $user->{'user_' . $key} ) ? $user->{'user_' . $key} : $def_value;
		}
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return trim( ucfirst( trim( is_string( $this->first ) ? $this->first : '' ) ) . ' ' . ucfirst( trim( is_string( $this->last ) ? $this->last : '' ) ) );
	}

	/**
	 * @return bool
	 */
	public function is_verified() {
		return ( isset( $this->is_verified ) && true === $this->is_verified );
	}

	/**
	 * @return string
	 */
	static function get_type() {
		return 'user';
	}

}
