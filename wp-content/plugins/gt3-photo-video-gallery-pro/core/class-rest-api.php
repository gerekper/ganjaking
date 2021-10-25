<?php

namespace GT3\PhotoVideoGalleryPro;

defined('ABSPATH') OR exit;

use WP_REST_Response;
use WP_REST_Server;
use WP_REST_Request;
use GT3_Post_Type_Gallery;

class Rest_Api {
	const REST_NAMESPACE = 'gt3/v1/photo-gallery-pro';

	private static $instance = null;

	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct(){
		if(\is_user_logged_in() && current_user_can('edit_posts')) {
			add_action('rest_api_init', array( $this, 'rest_api_init' ));
		}
	}

	function rest_api_init(){
		$namespace = 'gt3/v1';

		register_rest_route(
			$namespace,
			'photo-gallery/cpt-gallery_images',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
					'callback' => array( $this, 'get_gallery_images' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'photo-gallery/cpt-galleries',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
					'callback' => array( $this, 'get_galleries' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'photo-gallery/cpt-galleries_categories',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
					'callback' => array( $this, 'get_galleries_categories' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'taxonomy/get',
			array(
				array(
					'methods'  => WP_REST_Server::ALLMETHODS,
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
					'callback' => array( $this, 'get_taxonomy' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'users/get',
			array(
				array(
					'methods'  => WP_REST_Server::ALLMETHODS,
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
					'callback' => array( $this, 'get_users' ),
				)
			)
		);
		register_rest_route(
			$namespace,
			'posts/get',
			array(
				array(
					'methods'  => WP_REST_Server::ALLMETHODS,
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
					'callback' => array( $this, 'get_posts' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'photo-gallery/admin-save-settings',
			array(
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},
					'callback' => array( $this, 'save_settings' ),
				)
			)
		);
		register_rest_route(
			$namespace,
			'photo-gallery/admin-reset-settings',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},
					'callback' => array( $this, 'reset_settings' ),
				)
			)
		);
		register_rest_route(
			$namespace,
			'photo-gallery/admin-settings-get-page',
			array(
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},
					'callback' => array( $this, 'get_page' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'photo-gallery/flush-rewrite',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'permission_callback' => function() {
						return current_user_can('manage_options');
					},
					'callback' => array( $this, 'flush_rewrite' ),
				)
			)
		);

	}


	function get_gallery_images(WP_REST_Request $request){
		if(!class_exists('GT3_Post_Type_Gallery')) {
			rest_ensure_response(array());
		}
		$args   = $request->get_params();
		$ids    = GT3_Post_Type_Gallery::get_gallery_images($args['id']);
		$images = array();
		if(is_array($ids) && count($ids)) {
			foreach($ids as $id) {
				$image = wp_prepare_attachment_for_js($id);
				if(!$image) {
					continue;
				}
				//id, caption, title, description, width, height, url
				$images[] = array(
					'id'          => $id,
					'caption'     => $image['caption'],
					'title'       => $image['title'],
					'description' => $image['description'],
					'width'       => $image['width'],
					'height'      => $image['height'],
					'url'         => (isset($image['sizes']) && isset($image['sizes']['large'])) ? $image['sizes']['large']['url'] : $image['url'],
				);
			}
		}

		return rest_ensure_response(
			array(
				'ids'    => join(',', $ids),
				'images' => $images,
			)
		);
	}

	function get_galleries(WP_REST_Request $request){
		if(!class_exists('GT3_Post_Type_Gallery')) {
			rest_ensure_response(array());
		}

		$response_array = GT3_Post_Type_Gallery::get_galleries();
		$response       = array();

		if(is_array($response_array) && !empty($response_array)) {
			foreach($response_array as $key => $title) {
				$response[] = array(
					'value' => $key,
					'label' => $title,
				);
			}
		}

		return rest_ensure_response($response);
	}

	function get_galleries_categories(WP_REST_Request $request){
		if(!class_exists('GT3_Post_Type_Gallery')) {
			rest_ensure_response(array());
		}
		$response = array();

		$response_array = GT3_Post_Type_Gallery::get_galleries_categories();

		if(is_array($response_array) && !empty($response_array)) {
			foreach($response_array as $key => $title) {
				$response[] = array(
					'value' => $key,
					'label' => $title,
				);
			}
		}

		return rest_ensure_response($response);
	}

	function get_taxonomy(WP_REST_Request $request){
		$response_array = array();

		$params = array_merge(
			array(
				'post_type' => 'post',
				'taxonomy'  => 'category',
				's'         => '',
				'include'   => '',
				'exclude'   => '',
				'paged'     => 1,
			), $request->get_params()
		);

		$isSelect2      = ($request->get_param('typeQuery') === 'select2');
		$keys           = $isSelect2 ?
			[ 'label' => 'text', 'value' => 'id' ] :
			[ 'label' => 'label', 'value' => 'value' ];
		$ids_to_exclude = array();
		if(isset($params['exclude']) && !empty($params['exclude'])) {
			$get_terms_to_exclude = get_terms(
				array(
					'fields'   => 'ids',
					'slug'     => $params['exclude'],
					'taxonomy' => $params['taxonomy'],
				)
			);
			if(!is_wp_error($get_terms_to_exclude) && count($get_terms_to_exclude) > 0) {
				$ids_to_exclude = $get_terms_to_exclude;
			}
		}
		$args = array(
			'taxonomy'   => $params['taxonomy'],
			'hide_empty' => isset($params['hide_empty']),
			'search'     => isset($params['term']) ? $params['term'] : '',
			'name__like' => isset($params['term']) ? $params['term'] : '',
			'slug'       => isset($params['include']) ? ($params['include']) : '',
			'exclude'    => $ids_to_exclude,
		);

		$terms = get_terms($args);

		if(is_array($terms) && count($terms)) {
			foreach($terms as $term) {
				/* @var \WP_Term $term */
				$response_array[] = array(
					$keys['value'] => $term->slug,
					$keys['label'] => $term->name.' ('.$term->slug.')',
				);
			}
		}

		return rest_ensure_response(
			array(
				'results'    => $response_array,
				'args'       => $args,
				'pagination' => array(
					'more' => false,
				)
			)
		);
	}

	function get_users(WP_REST_Request $request){
		$response = array();

		$args = $request->get_params();

		if(is_array($args) && !empty($args) && isset($args['post_type']) && !empty($args['post_type']) && (isset($args['include']) || isset($args['term']))) {
			$users = get_users(
				array(
					'number'              => 20,
					'has_published_posts' => $args['post_type'],
					'search'              => isset($args['term']) ? sprintf('%1$s%2$s%1$s', '*', $args['term']) : '',
					'include'             => isset($args['include']) ? ($args['include']) : '',
					'exclude'             => isset($args['exclude']) ? ($args['exclude']) : '',
					'fields'              => array( 'ID', 'display_name' ),
				)
			);
			foreach($users as $user) {
				$response[] = array(
					'value' => $user->ID,
					'label' => $user->display_name,
				);
			}
		}

		return rest_ensure_response($response);
	}

	function get_posts(WP_REST_Request $request){
		$params    = array_merge(
			array(
				's'         => '',
				'include'   => '',
				'exclude'   => '',
				'paged'     => 1,
				'post_type' => 'post',
			), $request->get_params()
		);
		$isSelect2 = ($request->get_param('typeQuery') === 'select2');

		$paged = key_exists('page', $params) ? $params['page'] :
			(key_exists('paged', $params) ? $params['paged'] : 1);

		$args = array(
			'post_status'    => 'publish',
			'post_type'      => $params['post_type'],
			'paged'          => $paged,
			'posts_per_page' => 5,
		);

		if(!empty($params['s'])) {
			$args['s'] = $params['s'];
		}
		if(!empty($params['include'])) {
			$args['post__in'] = is_array($params['include']) ? $params['include'] : array( $params['include'] );
		}
		if(!empty($params['exclude'])) {
			$args['post__not_in'] = is_array($params['exclude']) ? $params['exclude'] : array( $params['exclude'] );
		}

		$response_array = array();
		$keys           = $isSelect2 ?
			[ 'label' => 'text', 'value' => 'id' ] :
			[ 'label' => 'label', 'value' => 'value' ];

		$posts = new \WP_Query($args);
		if($posts->post_count > 0) {
			foreach($posts->posts as /** \WP_Post */ $_post) {
				$response_array[] = array(
					$keys['label'] => !empty($_post->post_title) ? $_post->post_title : __('No Title', 'sb-builder'),
					$keys['value'] => $_post->ID,
				);
			}
		}
		wp_reset_postdata();

		$return = array(
			'results'    => $response_array,
			'pagination' => array(
				'more' => $posts->max_num_pages >= ++$params['paged'],
			)
		);

		return rest_ensure_response($return);
	}

	function save_settings(WP_REST_Request $request){
		if(current_user_can('manage_options')) {
			$new_options = $request->get_json_params();
			if(is_object($new_options)) {
				/** @var object $new_options */
				$new_options = get_object_vars($new_options);
			}
			if(!is_array($new_options)) {
				$new_options = array();
			}
			$settings = Settings::instance();

			$settings->setSettings($new_options);

			return rest_ensure_response(
				array(
					'saved' => true,
				)
			);
		} else {
			return rest_ensure_response(
				array(
					'saved' => false,
				)
			);
		}
	}

	function reset_settings(WP_REST_Request $request){
		if(current_user_can('manage_options')) {
			Settings::instance()->resetSettings();

			return rest_ensure_response(
				array(
					'saved'        => true,
					'changed_slug' => true,
				)
			);
		} else {
			return rest_ensure_response(
				array(
					'saved' => false,
				)
			);
		}
	}

	function get_page(WP_REST_Request $request){
		$respond = array();
		$pages   = $request->get_json_params();
		foreach($pages as $page) {
			if(in_array(
				$page, array(
					'started',
					'foundation',
					'components',
					'plugins',
					'premium_themes',
				)
			)) {
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

	public function flush_rewrite(){
		flush_rewrite_rules(true);

		return rest_ensure_response(
			array(
				'flushed' => true,
			)
		);
	}
}

