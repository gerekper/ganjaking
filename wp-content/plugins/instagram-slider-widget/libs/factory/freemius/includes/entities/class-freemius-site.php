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
class Site extends Scope {

	/**
	 * @var number
	 */
	public $site_id;

	/**
	 * @var number
	 */
	public $plugin_id;

	/**
	 * @var number
	 */
	public $user_id;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $version;

	/**
	 * @var string E.g. en-GB
	 */
	public $language;

	/**
	 * @var string E.g. UTF-8
	 */
	public $charset;

	/**
	 * @var string Platform version (e.g WordPress version).
	 */
	public $platform_version;

	/**
	 * Freemius SDK version
	 *
	 * @var string SDK version
	 */
	public $sdk_version;

	/**
	 * @var string Programming language version (e.g PHP version).
	 */
	public $programming_language_version;

	/**
	 * @var number|null
	 */
	public $plan_id;

	/**
	 * @var number|null
	 */
	public $license_id;

	/**
	 * @var number|null
	 */
	public $trial_plan_id;

	/**
	 * @var string|null
	 */
	public $trial_ends;

	/**
	 * @var bool
	 */
	public $is_premium = false;

	/**
	 * @var bool
	 */
	public $is_disconnected = false;

	/**
	 * @var bool
	 */
	public $is_active = true;

	/**
	 * @var bool
	 */
	public $is_uninstalled = false;

	/**
	 *
	 * @param stdClass|bool $site
	 */
	public function __construct( $site = false ) {
		parent::__construct( $site );

		if ( is_object( $site ) and isset( $site->plan_id ) ) {
			$this->plan_id = $site->plan_id;
		}

		if ( ! is_bool( $this->is_disconnected ) ) {
			$this->is_disconnected = false;
		}

		$props = get_object_vars( $this );

		foreach ( $props as $key => $def_value ) {
			$this->{$key} = isset( $site->{'install_' . $key} ) ? $site->{'install_' . $key} : $def_value;
		}
		if ( isset ( $site->install_id ) ) {
			$this->site_id = $site->install_id;
		}
	}

	/**
	 * @return string
	 */
	static function get_type() {
		return 'install';
	}
}
