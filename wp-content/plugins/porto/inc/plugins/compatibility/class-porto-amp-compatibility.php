<?php
/**
 * AMP Compatibility class
 *
 * @since 6.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_AMP_Compatibility {
	/**
	 * Constructor
	 *
	 * @since 6.3.0
	 */
	public function __construct() {
		add_action(
			'wp',
			function () {
				if ( porto_is_amp_endpoint() ) {
					add_filter( 'porto_skeleton_lazyload', array( $this, 'disable_skeleton_lazyload' ), 10, 1 );
					add_filter( 'the_content', array( $this, 'disable_animation' ), 10, 1 );

					add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
					$this->disable_lazyload();
				}
			}
		);
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 6.3.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'porto-theme-amp', PORTO_URI . '/css/theme_amp' . ( is_rtl() ? '_rtl' : '' ) . '.css', array(), PORTO_VERSION );
	}

	/**
	 * Disable skeleton lazyload
	 *
	 * @since 6.3.0
	 */
	public function disable_skeleton_lazyload( $flag ) {
		return false;
	}

	/**
	 * Disable Image and menu lazyload
	 *
	 * @since 6.3.0
	 */
	public function disable_lazyload() {
		global $porto_settings_optimize;
		if ( isset( $porto_settings_optimize ) ) {
			$porto_settings_optimize['lazyload']      = false;
			$porto_settings_optimize['lazyload_menu'] = false;
		}
	}

	/**
	 * Disable appear animation
	 *
	 * @since 6.3.0
	 */
	public function disable_animation( $content ) {
		$content = preg_replace( '/data-appear-animation="[a-zA-Z]+"/', '', $content );
		return $content;
	}
}

new Porto_AMP_Compatibility();
