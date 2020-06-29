<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCodeFishBones
 */
class WPBakeryShortCodeFishBones extends WPBakeryShortCode {
	/**
	 * @var bool
	 */
	protected $shortcode_class = false;

	/**
	 * @param $settings
	 * @throws \Exception
	 */
	public function __construct( $settings ) {
		if ( ! $settings ) {
			throw new Exception( 'Element must have settings to register' );
		}
		$this->settings = $settings;
		$this->shortcode = $this->settings['base'];
		add_action( 'admin_init', array(
			$this,
			'hookAdmin',
		) );
		if ( ! shortcode_exists( $this->shortcode ) ) {
			add_shortcode( $this->shortcode, array(
				$this,
				'render',
			) );
		}
	}

	public function hookAdmin() {
		$this->enqueueAssets();
		add_action( 'admin_init', array(
			$this,
			'enqueueAssets',
		) );
		if ( vc_is_page_editable() ) {
			// fix for page editable
			add_action( 'wp_head', array(
				$this,
				'printIconStyles',
			) );
		}

		add_action( 'admin_head', array(
			$this,
			'printIconStyles',
		) ); // fe+be
		add_action( 'admin_print_scripts-post.php', array(
			$this,
			'enqueueAssets',
		) );
		add_action( 'admin_print_scripts-post-new.php', array(
			$this,
			'enqueueAssets',
		) );
	}

	/**
	 * @return WPBakeryShortCodeFishBones
	 * @throws \Exception
	 */
	public function shortcodeClass() {
		if ( false !== $this->shortcode_class ) {
			return $this->shortcode_class;
		}

		require_once vc_path_dir( 'SHORTCODES_DIR', 'wordpress-widgets.php' );

		$class_name = $this->settings( 'php_class_name' ) ? $this->settings( 'php_class_name' ) : 'WPBakeryShortCode_' . $this->settings( 'base' );

		$autoloaded_dependencies = VcShortcodeAutoloader::includeClass( $class_name );

		if ( ! $autoloaded_dependencies ) {
			$file = vc_path_dir( 'SHORTCODES_DIR', str_replace( '_', '-', $this->settings( 'base' ) ) . '.php' );
			if ( is_file( $file ) ) {
				require_once $file;
			}
		}

		if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'WPBakeryShortCode' ) ) {
			$this->shortcode_class = new $class_name( $this->settings );
		} else {
			$this->shortcode_class = new WPBakeryShortCodeFishBones( $this->settings );
		}

		return $this->shortcode_class;
	}

	/**
	 *
	 *
	 * @param $tag
	 *
	 * @return \WPBakeryShortCodeFishBones
	 * @throws \Exception
	 * @since 4.9
	 *
	 */
	public static function getElementClass( $tag ) {
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

		return $shortcode_class;
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
		return self::getElementClass( $tag )->output( $atts, $content );
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function template( $content = '' ) {
		return $this->shortcodeClass()->contentAdmin( array(), $content );
	}
}
