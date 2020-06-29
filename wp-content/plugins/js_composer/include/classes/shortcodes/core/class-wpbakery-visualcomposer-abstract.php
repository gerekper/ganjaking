<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/* abstract VisualComposer class to create structural object of any type */

/**
 * Class WPBakeryVisualComposerAbstract
 */
abstract class WPBakeryVisualComposerAbstract {
	/**
	 * @var
	 */
	public static $config;
	/**
	 * @var string
	 */
	protected $controls_css_settings = 'cc';
	/**
	 * @var array
	 */
	protected $controls_list = array(
		'edit',
		'clone',
		'delete',
	);

	/**
	 * @var string
	 */
	protected $shortcode_content = '';

	/**
	 *
	 */
	public function __construct() {
	}

	/**
	 * @param $settings
	 * @deprecated not used
	 */
	public function init( $settings ) {
		self::$config = (array) $settings;
	}

	/**
	 * @param $action
	 * @param $method
	 * @param int $priority
	 * @return true|void
	 * @deprecated 6.0 use native WordPress actions
	 */
	public function addAction( $action, $method, $priority = 10 ) {
		return add_action( $action, array(
			$this,
			$method,
		), $priority );
	}

	/**
	 * @param $action
	 * @param $method
	 * @param int $priority
	 *
	 * @return bool
	 * @deprecated 6.0 use native WordPress actions
	 *
	 */
	public function removeAction( $action, $method, $priority = 10 ) {
		return remove_action( $action, array(
			$this,
			$method,
		), $priority );
	}

	/**
	 * @param $filter
	 * @param $method
	 * @param int $priority
	 *
	 * @return bool|void
	 * @deprecated 6.0 use native WordPress actions
	 *
	 */
	public function addFilter( $filter, $method, $priority = 10 ) {
		return add_filter( $filter, array(
			$this,
			$method,
		), $priority );
	}

	/**
	 * @param $filter
	 * @param $method
	 * @param int $priority
	 * @return bool
	 * @deprecated 6.0 use native WordPress
	 *
	 */
	public function removeFilter( $filter, $method, $priority = 10 ) {
		return remove_filter( $filter, array(
			$this,
			$method,
		), $priority );
	}

	/**
	 * @param $tag
	 * @param $func
	 * @deprecated 6.0 not used
	 *
	 */
	public function addShortCode( $tag, $func ) {
		// this function is deprecated since 6.0
	}

	/**
	 * @param $content
	 * @deprecated 6.0 not used
	 *
	 */
	public function doShortCode( $content ) {
		// this function is deprecated since 6.0
	}

	/**
	 * @param $tag
	 * @deprecated 6.0 not used
	 *
	 */
	public function removeShortCode( $tag ) {
		// this function is deprecated since 6.0
	}

	/**
	 * @param $param
	 *
	 * @return null
	 * @deprecated 6.0 not used, use vc_post_param
	 *
	 */
	public function post( $param ) {
		// this function is deprecated since 6.0

		return vc_post_param( $param );
	}

	/**
	 * @param $param
	 *
	 * @return null
	 * @deprecated 6.0 not used, use vc_get_param
	 *
	 */
	public function get( $param ) {
		// this function is deprecated since 6.0

		return vc_get_param( $param );
	}

	/**
	 * @param $asset
	 *
	 * @return string
	 * @deprecated 4.5 use vc_asset_url
	 *
	 */
	public function assetURL( $asset ) {
		// this function is deprecated since 4.5

		return vc_asset_url( $asset );
	}

	/**
	 * @param $asset
	 *
	 * @return string
	 * @deprecated 6.0 not used
	 */
	public function assetPath( $asset ) {
		// this function is deprecated since 6.0

		return self::$config['APP_ROOT'] . self::$config['ASSETS_DIR'] . $asset;
	}

	/**
	 * @param $name
	 *
	 * @return null
	 * @deprecated 6.0 not used
	 */
	public static function config( $name ) {
		return isset( self::$config[ $name ] ) ? self::$config[ $name ] : null;
	}
}
