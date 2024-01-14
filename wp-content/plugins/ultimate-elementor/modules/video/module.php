<?php
/**
 * UAEL Video Module.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Video;

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
	 * @since 0.0.1
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Video Widgets.
	 *
	 * @since 1.33.1
	 * @var all_video_widgets
	 */
	private static $all_video_widgets = array();

	/**
	 * Get Module Name.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'uael-video';
	}

	/**
	 * Get Widgets.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'Video',
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct();
		if ( UAEL_Helper::is_widget_active( 'Video' ) ) {

			add_filter( 'elementor/frontend/builder_content_data', array( $this, 'get_widget_data' ), 10, 2 );
			add_action( 'wp_footer', array( $this, 'render_video_schema' ) );
		}
	}

	/**
	 * Returns the link of the videos.
	 *
	 * @param array $settings Control Settings array.
	 *
	 * @access public
	 * @return mixed|string
	 * @since 1.33.1
	 */
	public function get_video_link( $settings ) {
		$video_type = $settings['video_type'];
		$video_link = '';
		switch ( $video_type ) {
			case 'youtube':
				$video_link = $settings['youtube_link'];
				break;
			case 'vimeo':
				$video_link = $settings['vimeo_link'];
				break;
			case 'wistia':
				$video_link = ( preg_match( '/https?\:\/\/[^\",]+/i', $settings['wistia_link'], $url ) ) ? $url[0] : '';
				break;
			case 'hosted':
				if ( 'hosted' === $video_type && 'yes' !== $settings['insert_link'] ) {
					$video_link = $settings['hosted_link']['url'];
				} elseif ( 'hosted' === $video_type && 'yes' === $settings['insert_link'] ) {
					$video_link = $settings['external_link']['url'];
				}
				break;
			default:
		}
		return $video_link;
	}

	/**
	 * Render the Video schema.
	 *
	 * @since 1.33.1
	 *
	 * @access public
	 */
	public function render_video_schema() {
		if ( ! empty( self::$all_video_widgets ) ) {

			$elementor    = Plugin::$instance;
			$widgets_data = self::$all_video_widgets;
			$video_data   = array();

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
				$content_schema_warning = false;
				$enable_schema          = $settings['schema_support'];
				$video_link             = $this->get_video_link( $settings );
				$is_custom_thumbnail    = 'yes' === $settings['show_image_overlay'] ? true : false;
				$custom_thumbnail_url   = isset( $settings['image_overlay']['url'] ) ? $settings['image_overlay']['url'] : '';
				if ( 'yes' === $enable_schema && ( ( '' === $settings['schema_title'] || '' === $settings['schema_description'] || ( ! $is_custom_thumbnail && '' === $settings['schema_thumbnail']['url'] ) || '' === $settings['schema_upload_date'] ) || ( $is_custom_thumbnail && '' === $custom_thumbnail_url ) ) ) {
					$content_schema_warning = true;
				}

				if ( 'yes' === $enable_schema && false === $content_schema_warning ) {
					$upload_date = new \DateTime( $settings['schema_upload_date'] );
					$video_data  = array(
						'@context'     => 'https://schema.org',
						'@type'        => 'VideoObject',
						'name'         => $settings['schema_title'],
						'description'  => $settings['schema_description'],
						'thumbnailUrl' => ( $is_custom_thumbnail ) ? $custom_thumbnail_url : $settings['schema_thumbnail']['url'],
						'uploadDate'   => $upload_date->format( 'Y-m-d\TH:i:s\Z' ),
						'contentUrl'   => $video_link,
						'embedUrl'     => $video_link,
					);
				}
			}
			UAEL_Helper::print_json_schema( $video_data );
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
				if ( 'uael-video' === $type ) {
					self::$all_video_widgets[] = $element;
				}
				return $element;
			}
		);

		return $data;
	}
}
