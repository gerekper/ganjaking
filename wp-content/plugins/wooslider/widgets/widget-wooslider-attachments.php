<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooSlider "Attachments" Widget Class
 *
 * Widget class for the "Attachments" widget for WooSlider.
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
class WooSlider_Widget_Attachments extends WooSlider_Widget_Base {
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct () {
		/* Widget variable settings. */
		$this->slider_type = 'attachments';
		$this->woo_widget_cssclass = 'widget_wooslider_slideshow_attachments';
		$this->woo_widget_description = __( 'A slideshow of the images attached to the current page/post', 'wooslider' );
		$this->woo_widget_idbase = 'wooslider_slideshow_attachments';
		$this->woo_widget_title = __('Attached Images Slideshow (WooSlider)', 'wooslider' );

		$this->init();

		$this->defaults = array(
						'title' => __( 'Images', 'wooslider' )
					);
	} // End Constructor

	/**
	 * Generate the HTML for this slideshow.
	 * @since  1.0.0
	 * @return string The generated HTML.
	 */
	protected function generate_slideshow ( $instance ) {
		if ( ! is_singular() ) { return ''; }

		global $wooslider;
		$settings = $wooslider->settings->get_settings();
		$settings['slider_type'] = $this->slider_type;

		$extra_args = array();

		foreach ( $instance as $k => $v ) {
			if ( ! in_array( $k, array_keys( $settings ) ) ) {
				$extra_args[$k] = esc_attr( $v );
				unset( $instance[$k] );
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