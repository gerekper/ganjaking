<?php
/**
 * View.
 *
 * @package Smush\Core
 */

namespace Smush\Core\Modules\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Class View
 */
class View {
	/**
	 * Templdate directory.
	 *
	 * @var string
	 */
	private $template_dir;

	/**
	 * Get template content.
	 *
	 * @param string $fname template name = file name.
	 * @param array  $args  Arguments.
	 * @param string $dir   Directory for the views. Default: views.
	 */
	public function get_template_content( $fname, $args = array(), $dir = 'views' ) {
		$file = $fname;
		if ( ! empty( $dir ) ) {
			$file = "{$dir}/{$file}";
		}
		$file    = trailingslashit( $this->get_template_dir() ) . $file . '.php';
		$content = '';

		if ( is_file( $file ) ) {
			add_filter( 'safe_style_css', array( $this, 'wp_kses_custom_safe_style_css' ) );
			extract( $args, EXTR_PREFIX_SAME, 'wpmudev' );
			ob_start();
			include $file;
			$content = ob_get_clean();
			remove_filter( 'safe_style_css', array( $this, 'wp_kses_custom_safe_style_css' ) );
		}

		// Everything escaped in all template files.
		return $content;
	}

	/**
	 * Allow display/float CSS property.
	 *
	 * @param array $styles Current allowed style CSS properties.
	 * @return array
	 */
	public function wp_kses_custom_safe_style_css( $styles ) {
		$styles[] = 'display';
		return $styles;
	}

	/**
	 * Get template directory.
	 *
	 * @return string
	 */
	private function get_template_dir() {
		return $this->template_dir;
	}

	/**
	 * Template directory.
	 *
	 * @param string $template_dir Template directory.
	 */
	public function set_template_dir( $template_dir ) {
		$this->template_dir = $template_dir;
		return $this;
	}

}
