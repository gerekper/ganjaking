<?php

namespace WBCR\Factory_Freemius_111\Entities;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, Alex Kovalev <alex.kovalevv@gmail.com>
 * @link          https://webcraftic.com
 * @copyright (c) 2018 Webraftic Ltd, Freemius, Inc.
 * @version       1.0
 */
class License extends Entity implements \WBCR\Factory_423\Premium\Interfaces\License {

	/**
	 * @var number
	 */
	public $plugin_id;

	/**
	 * @var number
	 */
	public $user_id;

	/**
	 * @var number
	 */
	public $plan_id;

	/**
	 * @var
	 */
	public $plan_title;

	/**
	 * @var
	 */
	public $billing_cycle;

	/**
	 * @var number
	 */
	public $pricing_id;

	/**
	 * @var int|null
	 */
	public $quota;

	/**
	 * @var int
	 */
	public $activated;

	/**
	 * @var int
	 */
	public $activated_local;

	/**
	 * @var string
	 */
	public $expiration;

	/**
	 * @var string
	 */
	public $secret_key;

	/**
	 * @var bool $is_free_localhost Defaults to true. If true, allow unlimited localhost installs with the same
	 *      license.
	 */
	public $is_free_localhost;

	/**
	 * @var bool $is_block_features Defaults to true. If false, don't block features after license expiry - only
	 *      block updates and support.
	 */
	public $is_block_features;

	/**
	 * @var bool
	 */
	public $is_cancelled;

	/**
	 * @param stdClass|bool $license
	 */
	public function __construct( $license = false ) {
		parent::__construct( $license );
	}

	/**
	 * Get entity type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'license';
	}

	/**
	 * Example: #sk_f=>}-5vuHp$3*wPQHxd(AD3<);1&i
	 *
	 * @return string|null
	 */
	public function get_key() {
		return $this->secret_key;
	}

	/**
	 * Example:  #sk_f=>}-5vuHp$3******d(AD3<);1&i
	 *
	 * @return mixed
	 */
	public function get_hidden_key() {
		return substr_replace( $this->get_key(), '******', 15, 6 );
	}

	/**
	 * @return string|null
	 */
	public function get_plan() {
		return $this->plan_title;
	}

	/**
	 * @return mixed
	 */
	public function get_billing_cycle() {
		return $this->billing_cycle;
	}

	/**
	 * @return int
	 */
	public function get_sites_quota() {
		return $this->quota;
	}

	/**
	 * @return int
	 */
	public function get_count_active_sites() {
		return $this->activated;
	}

	/**
	 * Check how many site activations left.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.5
	 *
	 * @return int
	 */
	public function get_site_activations_left() {
		if ( ! $this->is_active() || $this->is_expired() ) {
			return 0;
		}

		if ( $this->is_unlimited() ) {
			return 999;
		}

		return ( $this->quota - $this->activated - ( $this->is_free_localhost ? 0 : $this->activated_local ) );
	}

	/**
	 * @param string $format   Return time in formats (time|days|date)
	 *
	 * @return float|int|string
	 */
	public function get_expiration_time( $format = 'time' ) {
		if ( $format == 'days' ) {
			if ( $this->is_lifetime() ) {
				return 999;
			}

			$remaining      = strtotime( $this->expiration ) - date( 'U' );
			$days_remaining = floor( $remaining / 86400 );

			return $days_remaining;
		} else if ( $format == 'date' ) {
			return date( 'Y-m-d', strtotime( $this->expiration ) );
		}

		return $this->expiration;
	}

	/**
	 * @param $actual_license_data
	 */
	/*public function sync( $actual_license_data ) {
		$props = get_object_vars( $this );
		
		foreach ( $props as $key => $def_value ) {
			$this->{$key} = isset( $actual_license_data->{$key} ) ? $actual_license_data->{$key} : $def_value;
		}
		if ( isset( $actual_license_data->expiration ) and is_null( $actual_license_data->expiration ) ) {
			$this->expiration = null;
		}
	}*/

	/**
	 * Check if single site license.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.1.8.1
	 *
	 * @return bool
	 */
	public function is_single_site() {
		return ( is_numeric( $this->quota ) && 1 == $this->quota );
	}

	/**
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.5
	 *
	 * @return bool
	 */
	public function is_expired() {
		return ! $this->is_lifetime() && ( strtotime( $this->expiration ) < date( 'U' ) );
	}

	/**
	 * Check if license is not expired.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.2.1
	 *
	 * @return bool
	 */
	public function is_valid() {
		return ! $this->is_expired();
	}

	/**
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.6
	 *
	 * @return bool
	 */
	public function is_lifetime() {
		return is_null( $this->expiration );
	}

	/**
	 * @author Vova Feldman (@svovaf)
	 * @since  1.2.0
	 *
	 * @return bool
	 */
	public function is_unlimited() {
		return is_null( $this->quota );
	}

	/**
	 * Check if license is fully utilized.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.6
	 *
	 * @param bool|null $is_localhost
	 *
	 * @return bool
	 */
	public function is_utilized( $is_localhost = null ) {
		if ( is_null( $is_localhost ) ) {
			$is_localhost = false; // была WP_FS__IS_LOCALHOST_FOR_SERVER
		}

		if ( $this->is_unlimited() ) {
			return false;
		}

		return ! ( $this->is_free_localhost && $is_localhost ) && ( $this->quota <= $this->activated + ( $this->is_free_localhost ? 0 : $this->activated_local ) );
	}

	/**
	 * Check if license can be activated.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  2.0.0
	 *
	 * @param bool|null $is_localhost
	 *
	 * @return bool
	 */
	public function can_activate( $is_localhost = null ) {
		return ! $this->is_utilized( $is_localhost ) && $this->is_features_enabled();
	}

	/**
	 * Check if license can be activated on a given number of production and localhost sites.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  2.0.0
	 *
	 * @param int $production_count
	 * @param int $localhost_count
	 *
	 * @return bool
	 */
	//public function can_activate_bulk( $production_count, $localhost_count ) {
	//if ( $this->is_unlimited() ) {
	//return true;
	//}

	/**
	 * For simplicity, the logic will work as following: when given X sites to activate the license on, if it's
	 * possible to activate on ALL of them, do the activation. If it's not possible to activate on ALL of them,
	 * do NOT activate on any of them.
	 */
	//return ( $this->quota >= $this->activated + $production_count + ( $this->is_free_localhost ? 0 : $this->activated_local + $localhost_count ) );
	//}

	/**
	 * @author Vova Feldman (@svovaf)
	 * @since  1.2.1
	 *
	 * @return bool
	 */
	public function is_active() {
		return ( ! $this->is_cancelled );
	}

	/**
	 * Check if license's plan features are enabled.
	 *
	 *  - Either if plan not expired
	 *  - If expired, based on the configuration to block features or not.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.6
	 *
	 * @return bool
	 */
	public function is_features_enabled() {
		return $this->is_active() && ( ! $this->is_block_features || ! $this->is_expired() );
	}

	/**
	 * Subscription considered to be new without any payments
	 * if the license expires in less than 24 hours
	 * from the license creation.
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.9
	 *
	 * @return bool
	 */
	public function is_first_payment_pending() {
		return ( 86400 >= strtotime( $this->expiration ) - strtotime( $this->created ) );
	}

	/**
	 * @return int
	 */
	public function total_activations() {
		return ( $this->activated + $this->activated_local );
	}

}
