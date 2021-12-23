<?php
/**
 * @package WPBakery
 * @noinspection PhpIncludeInspection
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 *
 */
define( 'VC_SHORTCODE_CUSTOMIZE_PREFIX', 'vc_theme_' );
/**
 *
 */
define( 'VC_SHORTCODE_BEFORE_CUSTOMIZE_PREFIX', 'vc_theme_before_' );
/**
 *
 */
define( 'VC_SHORTCODE_AFTER_CUSTOMIZE_PREFIX', 'vc_theme_after_' );
/**
 *
 */
define( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG', 'vc_shortcodes_css_class' );

require_once $this->path( 'SHORTCODES_DIR', 'core/class-wpbakery-visualcomposer-abstract.php' );
require_once $this->path( 'SHORTCODES_DIR', 'core/class-wpbakeryshortcode.php' );
require_once $this->path( 'SHORTCODES_DIR', 'core/class-wbpakeryshortcodefishbones.php' );
require_once $this->path( 'SHORTCODES_DIR', 'core/class-wpbakeryshortcodescontainer.php' );

/**
 * @since 4.9
 *
 * Class Vc_Shortcodes_Manager
 */
class Vc_Shortcodes_Manager {
	private $shortcode_classes = array(
		'default' => array(),
	);

	private $tag;
	/**
	 * Core singleton class
	 * @var self - pattern realization
	 */
	private static $instance;

	/**
	 * Get the instance of Vc_Shortcodes_Manager
	 *
	 * @return self
	 */
	public static function getInstance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function getTag() {
		return $this->tag;
	}

	/**
	 * @param $tag
	 * @return $this
	 */
	/**
	 * @param $tag
	 * @return $this
	 */
	public function setTag( $tag ) {
		$this->tag = $tag;

		return $this;
	}

	/**
	 * @param $tag
	 * @return \WPBakeryShortCodeFishBones
	 * @throws \Exception
	 */
	/**
	 * @param $tag
	 * @return \WPBakeryShortCodeFishBones
	 * @throws \Exception
	 */
	public function getElementClass( $tag ) {
		$currentScope = WPBMap::getScope();
		if ( isset( $this->shortcode_classes[ $currentScope ], $this->shortcode_classes[ $currentScope ][ $tag ] ) ) {
			return $this->shortcode_classes[ $currentScope ][ $tag ];
		}
		if ( ! isset( $this->shortcode_classes[ $currentScope ] ) ) {
			$this->shortcode_classes[ $currentScope ] = array();
		}
		$settings = WPBMap::getShortCode( $tag );
		if ( empty( $settings ) ) {
			throw new Exception( 'Element must be mapped in system' );
		}
		require_once vc_path_dir( 'SHORTCODES_DIR', 'wordpress-widgets.php' );

		$class_name = ! empty( $settings['php_class_name'] ) ? $settings['php_class_name'] : 'WPBakeryShortCode_' . $settings['base'];

		$autoloaded_dependencies = VcShortcodeAutoloader::includeClass( $class_name );

		if ( ! $autoloaded_dependencies ) {
			$file = vc_path_dir( 'SHORTCODES_DIR', str_replace( '_', '-', $settings['base'] ) . '.php' );
			if ( is_file( $file ) ) {
				require_once $file;
			}
		}

		if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'WPBakeryShortCode' ) ) {
			$shortcode_class = new $class_name( $settings );
		} else {
			$shortcode_class = new WPBakeryShortCodeFishBones( $settings );
		}
		$this->shortcode_classes[ $currentScope ][ $tag ] = $shortcode_class;

		return $shortcode_class;
	}

	/**
	 * @return \WPBakeryShortCodeFishBones
	 * @throws \Exception
	 */
	/**
	 * @return \WPBakeryShortCodeFishBones
	 * @throws \Exception
	 */
	public function shortcodeClass() {
		return $this->getElementClass( $this->tag );
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function template( $content = '' ) {
		return $this->getElementClass( $this->tag )->contentAdmin( array(), $content );
	}

	/**
	 * @param $name
	 *
	 * @return null
	 * @throws \Exception
	 */
	public function settings( $name ) {
		$settings = WPBMap::getShortCode( $this->tag );

		return isset( $settings[ $name ] ) ? $settings[ $name ] : null;
	}

	/**
	 * @param $atts
	 * @param null $content
	 * @param null $tag
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function render( $atts, $content = null, $tag = null ) {
		return $this->getElementClass( $this->tag )->output( $atts, $content );
	}

	public function buildShortcodesAssets() {
		$elements = WPBMap::getAllShortCodes();
		foreach ( $elements as $tag => $settings ) {
			$element_class = $this->getElementClass( $tag );
			$element_class->enqueueAssets();
			$element_class->printIconStyles();
		}
	}

	public function buildShortcodesAssetsForEditable() {
		$elements = WPBMap::getAllShortCodes(); // @todo create pull to use only where it is set inside function. BC problem
		foreach ( $elements as $tag => $settings ) {
			$element_class = $this->getElementClass( $tag );
			$element_class->printIconStyles();
		}
	}

	/**
	 * @param $tag
	 * @return bool
	 */
	/**
	 * @param $tag
	 * @return bool
	 */
	public function isShortcodeClassInitialized( $tag ) {
		$currentScope = WPBMap::getScope();

		return isset( $this->shortcode_classes[ $currentScope ], $this->shortcode_classes[ $currentScope ][ $tag ] );
	}

	/**
	 * @param $tag
	 * @return bool
	 */
	/**
	 * @param $tag
	 * @return bool
	 */
	public function unsetElementClass( $tag ) {
		$currentScope = WPBMap::getScope();
		unset( $this->shortcode_classes[ $currentScope ][ $tag ] );

		return true;
	}
}
