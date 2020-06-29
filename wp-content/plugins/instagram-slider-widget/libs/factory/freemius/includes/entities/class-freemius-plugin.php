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
class Plugin extends Scope {

	/**
	 * @since 1.0.6
	 * @var null|number
	 */
	public $parent_plugin_id;

	/**
	 * @var string
	 */
	public $title;
	/**
	 * @var string
	 */
	public $slug;

	/**
	 * @var string
	 */
	public $premium_slug;

	/**
	 * @var string 'plugin' or 'theme'
	 */
	public $type;

	/**
	 * @var string|false false if the module doesn't have an affiliate program or one of the following: 'selected',
	 *      'customers', or 'all'.
	 */
	public $affiliate_moderation;

	/**
	 * @var bool Set to true if the free version of the module is hosted on WordPress.org. Defaults to true.
	 */
	public $is_wp_org_compliant = true;

	/**
	 * @var string
	 */
	public $file;

	/**
	 * @var string
	 */
	public $version;

	/**
	 * @var bool
	 */
	public $auto_update;

	/**
	 * @var bool
	 */
	public $is_premium;

	/**
	 * @var string
	 */
	public $premium_suffix;

	/**
	 * @var bool
	 */
	public $is_live;

	const AFFILIATE_MODERATION_CUSTOMERS = 'customers';

	#endregion Install Specific Properties

	/**
	 * @param stdClass|bool $plugin
	 */
	function __construct( $plugin = false ) {
		parent::__construct( $plugin );

		$this->is_premium = false;
		$this->is_live    = true;
	}

	/**
	 * Check if plugin is an add-on (has parent).
	 *
	 * @author Vova Feldman (@svovaf)
	 * @since  1.0.6
	 *
	 * @return bool
	 */
	function is_addon() {
		return isset( $this->parent_plugin_id ) && is_numeric( $this->parent_plugin_id );
	}

	static function get_type() {
		return 'plugin';
	}
}