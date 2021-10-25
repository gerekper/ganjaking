<?php

namespace GT3\Gallery\PhotoVideo;

defined('ABSPATH') OR exit;

use GT3\PhotoVideoGallery\Watermark;
use Imagick;
use WP_REST_Server;
use WP_REST_Request;
use GT3\PhotoVideoGallery\Settings;

class Rest {
	private static $instance = null;

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct(){
		add_action('rest_api_init', array( $this, 'rest_api_init' ));
	}

	function rest_api_init(){
		$namespace = 'gt3/v1';

		register_rest_route($namespace,
			'admin-save-settings',
			array(
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},'callback' => array( $this, 'save_settings' ),
				)
			)
		);
		register_rest_route($namespace,
			'admin-reset-settings',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},	'callback' => array( $this, 'reset_settings' ),
				)
			)
		);
		register_rest_route($namespace,
			'admin-settings-get-page',
			array(
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},	'callback' => array( $this, 'get_page' ),
				)
			)
		);

		if (!function_exists('WP_Filesystem')) {
			require_once(ABSPATH.'wp-admin/includes/file.php');

			if (!WP_Filesystem()) {
				return false;
			};
		}



		register_rest_route($namespace,
			'images-to-watermark',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},	'callback' => array( $this, 'images_to_watermark_generate' ),
				)
			)
		);

		register_rest_route($namespace,
			'images-to-watermark',
			array(
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},	'callback' => array( $this, 'images_to_watermark_process' ),
				)
			)
		);

		register_rest_route($namespace,
			'images-to-watermark-restore',
			array(
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},	'callback' => array( $this, 'images_to_watermark_restore' ),
				)
			)
		);
	}

	function save_settings(WP_REST_Request $request){
		if(current_user_can('manage_options')) {
			$new_options = $request->get_json_params();
			if(is_object($new_options)) {
				$new_options = get_object_vars($new_options);
			}
			if(!is_array($new_options)) {
				$new_options = array();
			}
			array_walk_recursive($new_options,'sanitize_text_field');

			Settings::instance()->setSettings($new_options);

			return rest_ensure_response(array(
				'saved' => true,
			));
		} else {
			return rest_ensure_response(array(
				'saved' => false,
			));
		}
	}

	function reset_settings(WP_REST_Request $request){
		if(current_user_can('manage_options')) {
			Settings::instance()->resetSettings();

			return rest_ensure_response(array(
				'saved' => true,
			));
		} else {
			return rest_ensure_response(array(
				'saved' => false,
			));
		}
	}

	function get_page(WP_REST_Request $request){
		$respond = array();
		$pages   = $request->get_json_params();
		foreach($pages as $page) {
			if(in_array($page, array(
				'started',
				'foundation',
				'components',
				'plugins',
				'premium_themes',
			))) {
				$page_path = __DIR__.'/pages/'.$page.'.php';
				if(file_exists($page_path) && is_readable($page_path)) {
					ob_start();
					require_once $page_path;
					$respond[$page] = ob_get_clean();
				} else {
					$respond[$page] = 'File not found';
				}
			} else {
				$respond[$page] = 'Page not found';
			}
		}

		return rest_ensure_response($respond);
	}

	public function images_to_watermark_generate(WP_REST_Request $request){
		$settings = Settings::instance()->getSettings('basic');
		$settings = $settings['watermark'];

		$_attachments = get_posts(array(
			'post_type'      => 'attachment',
			'post_mime_type' => array(
				'image/jpeg',
				'image/png'
			),
			'posts_per_page' => '-1',
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_watermark_original',
					'compare' => 'NOT EXISTS'
				),
			),
			'post__not_in'   => array(
				$settings['image']['id']
			)
		));

		return rest_ensure_response(array(
			'images'       => $_attachments,
			'images_count' => count($_attachments),
			'_nonce'       => wp_create_nonce('process_watermarks'),
			'src_url'      => GT3PG_LITE_IMG_URL,
		));
	}

	public function images_to_watermark_process(WP_REST_Request $request){
		$params = array_merge(array(
			'image'  => 0,
			'_nonce' => '',
		), $request->get_params());
		if (get_transient('gt3_watermark_processing')) return rest_ensure_response(array(
			'error' => true,
			'msg'   => __('Adding watermark process is running. Try again later.', 'gt3pg_lite'),
		));

		if(!class_exists('Imagick')) {
			return rest_ensure_response(array(
				'error' => true,
				'msg'   => __('Image library "Imagick" is not found', 'gt3pg_lite'),
			));
		} else if(!wp_verify_nonce($params['_nonce'], 'process_watermarks')) {
			return rest_ensure_response(array(
				'error' => true,
				'msg'   => __('Nonce Failed. Please reload page.', 'gt3pg_lite'),
			));
		}

		$settings = Settings::instance()->getSettings('basic');
		$settings = $settings['watermark'];
		if(!$settings['enable']) {
			return rest_ensure_response(array(
				'error' => true,
				'msg'   => 'Watermark option is disabled in the plugin settings<br/>'.
				           'Please enable that option in the plugin general settings.'
			));
		}
		$image = wp_prepare_attachment_for_js($params['image']);

		if(false === $image) {
			return rest_ensure_response(array(
				'error' => true,
				'msg'   => __('Image not found.')
			));
		} /*else if('' !== get_post_meta($params['image'], '_watermark_original', true)) {

			return rest_ensure_response(array(
				'error' => false,
				'msg'   => __('Watermark has already been added.', 'gt3pg_pro'),
			));
		}*/ else if(!$settings['image']['id'] || false === get_attached_file($settings['image']['id'])) {
			return rest_ensure_response(array(
				'error' => true,
				'msg'   => 'Watermark image is missing<br/>'.
				           'Please add a watermark image in the plugin general settings.'
			));
		} else if($settings['image']['id'] == $params['image']) {
			return rest_ensure_response(array(
				'error' => false,
				'msg'   => __('Watermark.', 'gt3pg_lite')
			));
		}

		return rest_ensure_response(Watermark::process($params['image']));
	}

	public function images_to_watermark_restore(WP_REST_Request $request){
		$params = array_merge(array(
			'_nonce' => '',
			'ids'    => false,
		), $request->get_params());

		if(!wp_verify_nonce($params['_nonce'], 'process_watermarks')) {
			return rest_ensure_response(array(
				'error' => true,
				'msg'   => __('Nonce Failed','gt3pg_lite'),
			));
		}

		if(!is_array($params['ids'])) {
			$params['ids'] = get_posts(array(
				'post_type'      => 'attachment',
				'post_mime_type' => array(
					'image/jpeg',
					'image/png'
				),
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'     => '_watermark_original',
						'compare' => 'EXISTS'
					),
				)
			));
		}

		@set_time_limit(0);
		@ignore_user_abort(true);

		foreach($params['ids'] as $image_id) {
			Watermark::restore($image_id);
		}

		return rest_ensure_response(array(
			'error' => false,
			'msg'   => __('Done','gt3pg_lite'),
		));
	}
}

Rest::instance();
