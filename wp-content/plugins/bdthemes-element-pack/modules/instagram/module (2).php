<?php

namespace ElementPack\Modules\Instagram;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();

		$this->add_actions();
	}

	public function get_name() {
		return 'instagram';
	}

	public function get_widgets() {

		$widgets = [ 'Instagram' ];

		return $widgets;
	}

	/**
	 * @param int $item_count
	 * @param bool $cache
	 * @param float|int $cache_time
	 *
	 * @return bool|mixed
	 */
	public function element_pack_instagram_feed( $item_count = 100, $cache = true, $cache_time = HOUR_IN_SECONDS * 12 ) {

		$options      = get_option( 'element_pack_api_settings' );
		$access_token = ( ! empty( $options['instagram_access_token'] ) ) ? $options['instagram_access_token'] : '';


		if ( $access_token ) {

			$data = ( $cache ) ? get_transient( 'ep_instagram_feed_data' ) : false;

			if ( false === $data ) {

				$url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $access_token . '&count=' . $item_count;

				$feeds_json = wp_remote_fopen( $url );

				$feeds_obj = json_decode( $feeds_json, true );

				$feeds_images_array = [];
				$instagram_user     = [];
				$ins_counter        = 1;

				if ( 200 == $feeds_obj['meta']['code'] ) {

					if ( ! empty( $feeds_obj['data'] ) ) {

						foreach ( $feeds_obj['data'] as $data ) {

							array_push( $feeds_images_array,
								array(
									'image'     => [
										'small'  => $data['images']['thumbnail']['url'], // thumbnail image
										'medium' => $data['images']['low_resolution']['url'], // medium image
										'large'  => $data['images']['standard_resolution']['url'], // large image
									],
									'link'      => $data['link'],
									'like'      => $data['likes']['count'],
									'comment'   => [
										'count' => $data['comments']['count']
									],
									//'text'      => $data['text'],
									'post_type' => $data['type'],
									'user'      => $data['user'],
								)
							);

							if ( 1 == $ins_counter ) {
								$instagram_user = $data['user'];
								$ins_counter ++;
							}


						}

						set_transient( 'ep_instagram_feed_data', $feeds_images_array, $cache_time );
						set_transient( 'ep_instagram_user', $instagram_user, $cache_time );

						return get_transient( 'ep_instagram_feed_data' );
					}
				}
			}

			return $data;
		}
	}

	/**
	 * Instagram post layout maker with ajax load
	 * @return string instagram images with layout
	 */
	public function element_pack_instagram_ajax_load() {

		$limit               = isset($_REQUEST['item_per_page']) ? sanitize_text_field($_REQUEST['item_per_page']) : 12;
		$current_page        = isset($_REQUEST['current_page']) ? sanitize_text_field($_REQUEST['current_page']) : 1;
		$load_more_per_click = isset($_REQUEST['load_more_per_click']) ? sanitize_text_field($_REQUEST['load_more_per_click']) : '';
		$cache               = isset($_REQUEST['cache']) ? sanitize_text_field($_REQUEST['cache']) : false;
		$cache_time          = isset($_REQUEST['cache_time']) ? sanitize_text_field($_REQUEST['cache_time']) : 0;

		$skin = isset( $_REQUEST['skin'] ) ? sanitize_text_field($_REQUEST['skin']) : '';

		$insta_feeds = $this->element_pack_instagram_feed( 100, $cache, $cache_time);

		if ( $current_page == 1 ) {
			$start = 0;
			$end   = $limit - 1;
		} else {
			$start = $limit + ( ( $current_page - 2 ) * $load_more_per_click ) + 1;
			$end   = $limit + ( $load_more_per_click * ( $current_page - 1 ) );
		}

		ob_start();

		for ( $i = $start; $i <= $end; $i ++ ) {
			if ( isset($insta_feeds[ $i ])) {

				if ( 'bdt-classic-grid' == $skin ) {
					include 'widgets/template-classic.php';
				} else {
					include 'widgets/template.php';
				}

			}
		}

		$output = ob_get_clean();

		echo $output;

		die();
	}


	public function add_actions() {
		add_action( 'wp_ajax_nopriv_element_pack_instagram_ajax_load', [ $this, 'element_pack_instagram_ajax_load' ] );
		add_action( 'wp_ajax_element_pack_instagram_ajax_load', array( $this, 'element_pack_instagram_ajax_load' ) );
	}
}
