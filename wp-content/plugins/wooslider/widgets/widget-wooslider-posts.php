<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooSlider "Posts" Widget Class
 *
 * Widget class for the "Posts" widget for WooSlider.
 *
 * @package WordPress
 * @subpackage WooSlider
 * @category Widgets
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - generate_slideshow()
 */
class WooSlider_Widget_Posts extends WooSlider_Widget_Base {
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct () {
		/* Widget variable settings. */
		$this->slider_type = 'posts';
		$this->woo_widget_cssclass = 'widget_wooslider_slideshow_posts';
		$this->woo_widget_description = __( 'A slideshow of posts on your site', 'wooslider' );
		$this->woo_widget_idbase = 'wooslider_slideshow_posts';
		$this->woo_widget_title = __('Posts Slideshow (WooSlider)', 'wooslider' );

		$this->init();

		$this->defaults = array(
						'title' => __( 'Posts', 'wooslider' )
					);
	} // End Constructor

	/**
	 * Generate the HTML for this slideshow.
	 * @since  1.0.0
	 * @return string The generated HTML.
	 */
	protected function generate_slideshow ( $instance ) {
		global $wooslider;
		$settings = $wooslider->settings->get_settings();
		$settings['slider_type'] = $this->slider_type;

		$extra_args = array();

		// Tags.
		if ( isset( $instance['tag'] ) && is_array( $instance['tag'] ) ) {
			$count = 0;
			foreach ( $instance['tag'] as $k => $v ) {
				$count++;
				if ( $count > 1 ) {
					$extra_args['tag'] .= '+';
				}
				$extra_args['tag'] .= esc_attr( $v );
			}
			unset( $instance['tag'] );
		}

		// Categories.
		if ( isset( $instance['category'] ) && is_array( $instance['category'] ) ) {
			$count = 0;
			foreach ( $instance['category'] as $k => $v ) {
				$count++;
				if ( $count > 1 ) {
					$extra_args['category'] .= ',';
				}
				$extra_args['category'] .= esc_attr( $v );
			}
			unset( $instance['category'] );
		}

		foreach ( $instance as $k => $v ) {
			if ( ! in_array( $k, array_keys( $settings ) ) ) {
				$extra_args[$k] = esc_attr( $v );
			}
		}

		// Make sure the various settings are applied.
		if ( isset( $instance['show_advanced_settings'] ) && ( $instance['show_advanced_settings'] == true ) ) {
			foreach ( $settings as $k => $v ) {
				if ( isset( $instance[$k] ) && ( $instance[$k] != $settings[$k] ) ) {
					$settings[$k] = esc_attr( $instance[$k] );
				}
			}
		}

		$html = wooslider( $settings, $extra_args, false );

		return $html;
	} // End generate_slideshow()
} // End Class
?>