<?php
/**
 * UAEL VideoGallery Module.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\VideoGallery;

use Elementor\Plugin;
use Elementor\Widget_Base;
use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Base\Module_Base;

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
	 * @since 1.6.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * VideoGalleryWidget.
	 *
	 * @since 1.35.1
	 * @var all_video_gallery_widgets
	 */
	private static $all_video_gallery_widgets = array();


	/**
	 * Get Module Name.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'uael-video-gallery';
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'Video_Gallery',
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct();
		if ( UAEL_Helper::is_widget_active( 'Video_Gallery' ) ) {

			add_filter( 'elementor/frontend/builder_content_data', array( $this, 'get_widget_data' ), 10, 2 );
			add_action( 'wp_footer', array( $this, 'render_video_gallery_schema' ) );
		}
	}


	/**
	 * Render Video Gallery Schema.
	 *
	 * @since 1.35.1
	 *
	 * @access public
	 */
	public function render_video_gallery_schema() {

		if ( ! empty( self::$all_video_gallery_widgets ) ) {

			$elementor     = \Elementor\Plugin::$instance;
			$widgets_data  = self::$all_video_gallery_widgets;
			$object_data   = array();
			$positioncount = 1;
			$videocount    = 1;

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
				if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
					$actual_link = ( 'on' === isset( $_SERVER['HTTPS'] ) && sanitize_text_field( $_SERVER['HTTPS'] ) ) ? 'https' : 'http://' . sanitize_text_field( $_SERVER['HTTP_HOST'] ) . esc_url_raw( $_SERVER['REQUEST_URI'] );
				}
				$settings             = $widget->get_settings();
				$enable_schema        = $settings['schema_support'];
				$video_link           = array();
				$video_type           = '';
				$is_custom            = '';
				$custom_thumbnail_url = '';
				$schema_thumbnail_url = '';

				foreach ( $settings['gallery_items'] as $key => $val ) {
					$content_schema_warning = false;
					if ( is_array( $val ) ) {

						$video_type = $val['type'];

						switch ( $video_type ) {
							case 'youtube':
							case 'vimeo':
								$video_link = $val['video_url'];
								break;

							case 'wistia':
								$video_link = ( preg_match( '/https?\:\/\/[^\",]+/i', $val['wistia_url'], $url ) ) ? $url[0] : '';
								break;

							case 'hosted':
								if ( 'hosted' === $video_type && 'yes' !== $val['insert_url'] ) {
									$video_link = $val['hosted_url']['url'];
								} elseif ( 'hosted' === $video_type && 'yes' === $val['insert_url'] ) {
									$video_link = $val['external_url']['url'];
								}
								break;
							default:
						}

						$is_custom = ( 'yes' === $val['custom_placeholder'] ? true : false );
						foreach ( $val as $image_url => $url_value ) {
							if ( is_array( $url_value ) ) {

								if ( 'placeholder_image' === $image_url ) {
									$custom_image         = $url_value['url'];
									$custom_thumbnail_url = isset( $custom_image ) ? $custom_image : '';

								}
								if ( 'schema_thumbnail' === $image_url ) {
									$schema_image         = $url_value['url'];
									$schema_thumbnail_url = isset( $schema_image ) ? $schema_image : '';
								}
							}
						}
					}

					if ( 'yes' === $enable_schema && ( ( '' === $val['schema_title'] || '' === $val['schema_description'] || '' === $val['schema_upload_date'] || ( ! $is_custom && '' === $schema_thumbnail_url ) || ( $is_custom && '' === $custom_thumbnail_url ) ) ) ) {
						$content_schema_warning = true;
					}

					if ( 'yes' === $enable_schema && false === $content_schema_warning ) {
						$new_data = array(
							'@type'        => 'VideoObject',
							'url'          => $actual_link . '#uael-video__gallery-item' . ( $videocount++ ),
							'position'     => $positioncount++,
							'name'         => $val['schema_title'],
							'description'  => $val['schema_description'],
							'thumbnailUrl' => $is_custom ? $custom_thumbnail_url : $schema_thumbnail_url,
							'uploadDate'   => $val['schema_upload_date'],
							'contentUrl'   => $video_link,
							'embedUrl'     => $video_link,
						);
						array_push( $object_data, $new_data );
					}
				}
			}

			if ( $object_data ) {
				$schema_data = array(
					'@context'        => 'https://schema.org',
					'@type'           => 'ItemList',
					'itemListElement' => array( $object_data ),

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
				if ( 'uael-video-gallery' === $type ) {
					self::$all_video_gallery_widgets[] = $element;
				}
				return $element;
			}
		);

		return $data;
	}
}
