<?php
/**
 * UAEL FAQ widget
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\FAQ;

use Elementor\Plugin;
use Elementor\Widget_Base;
use UltimateElementor\Base\Module_Base;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Module_Base {

	/**
	 * Module should load or not.
	 *
	 * @since 1.22.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * FAQ Widgets.
	 *
	 * @var all_faq_widgets
	 */
	private static $all_faq_widgets = array();

	/**
	 * Get Module Name.
	 *
	 * @since 1.22.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'uael-faq';
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.22.0
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'FAQ',
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct();

		if ( UAEL_Helper::is_widget_active( 'FAQ' ) ) {

			add_filter( 'elementor/frontend/builder_content_data', array( $this, 'get_widget_data' ), 10, 2 );
			add_action( 'wp_footer', array( $this, 'render_faq_schema' ) );
		}
	}

	/**
	 * Render the FAQ schema.
	 *
	 * @since 1.29.0
	 *
	 * @access public
	 */
	public function render_faq_schema() {

		if ( ! empty( self::$all_faq_widgets ) ) {

			$elementor    = \Elementor\Plugin::$instance;
			$widgets_data = self::$all_faq_widgets;
			$object_data  = array();

			foreach ( $widgets_data as $_widget ) {
				$widget = $elementor->elements_manager->create_element_instance( $_widget );
				if ( isset( $_widget['templateID'] ) ) {
					$type          = UAEL_Helper::get_global_widget_type( $_widget['templateID'], 1 );
					$element_class = $type->get_class_name();
					try {
						$widget = new $element_class( $_widget, array() );
					} catch ( \Exception $e ) {
						return null;
					}
				}
				$settings               = $widget->get_settings();
				$content_schema_warning = 0;
				$enable_schema          = $settings['schema_support'];

				foreach ( $settings['tabs'] as $key ) {
					if ( 'content' !== $key['faq_content_type'] ) {
						$content_schema_warning = 1;
					}
				}

				if ( 'yes' === $enable_schema && ( 0 === $content_schema_warning ) ) {
					foreach ( $settings['tabs'] as $faqs ) {
						if ( '' !== $faqs['question'] && '' !== $faqs['answer'] ) {
							$new_data = array(
								'@type'          => 'Question',
								'name'           => $faqs['question'],
								'acceptedAnswer' =>
								array(
									'@type' => 'Answer',
									'text'  => $faqs['answer'],
								),
							);
							array_push( $object_data, $new_data );
						}
					}
				}
			}

			if ( $object_data ) {

				$schema_data = array(
					'@context'   => 'https://schema.org',
					'@type'      => 'FAQPage',
					'mainEntity' => $object_data,
				);

				UAEL_Helper::print_json_schema( $schema_data );
			}
		}
	}

	/**
	 * Get widget name.
	 *
	 * @since 1.36.5
	 * @access public
	 * @param array $data The builder content.
	 * @param int   $post_id The post ID.
	 */
	public function get_widget_data( $data, $post_id ) {

		Plugin::$instance->db->iterate_data(
			$data,
			function ( $element ) use ( &$widgets ) {
				$type = UAEL_Helper::get_widget_type( $element );
				if ( 'uael-faq' === $type ) {
					self::$all_faq_widgets[] = $element;
				}
				return $element;
			}
		);

		return $data;
	}
}
