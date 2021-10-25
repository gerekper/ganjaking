<?php

namespace GT3\Elementor\Ajax;

if(!class_exists('\GT3\Elementor\Ajax\Ajax')) {

	class Ajax {
		function __construct(){
			add_action('wp_ajax_gt3_ajax_query', function(){
				$request = isset($_POST) ? $_POST : array();
				if(count($request) && key_exists('gt3_action', $request)) {
					header('Content-Type: application/json');
					switch($request['gt3_action']) {
						case 'get-taxonomy':
							$this->get_taxonomy($request);
							break;
						case 'get-user':
							$this->get_user($request);
							break;
						case 'get-post':
							$this->get_post($request);
							break;
					}
				}
				wp_die(0);
			});
		}

		function getSlugById($taxonomy, $ids){
			$slugs = array();

			$terms = get_terms(array(
				'taxonomy' => $taxonomy,
				'include'  => $ids,
			));
			if(!is_wp_error($terms)) {
				if(is_array($terms) && count($terms)) {
					foreach($terms as $term) {
						$slugs[] = $term->slug;
					}
				}
			}

			return $slugs;
		}

		function isIds($ids){
			if(is_array($ids) && count($ids)) {
				foreach($ids as $id) {
					if(!is_numeric($id)) {
						return false;
					}
				}

				return true;
			}

			return false;
		}

		function get_taxonomy($request){
			$response = array();

			if(is_array($request) && !empty($request) && isset($request['taxonomy']) && !empty($request['taxonomy']) && (isset($request['include']) || isset($request['term']))) {

				if(isset($request['include']) && $this->isIds($request['include'])) {
					$request['include'] = $this->getSlugById($request['taxonomy'], $request['include']);
				}
				$terms = get_terms(array(
					'number'     => 20,
					'taxonomy'   => $request['taxonomy'],
					'hide_empty' => isset($request['hide_empty']),
					'search'     => isset($request['term']) ? $request['term'] : '',
					'name__like' => isset($request['term']) ? $request['term'] : '',
					'slug'       => isset($request['include']) ? ($request['include']) : '',
					'exclude'    => isset($request['exclude']) ? ($request['exclude']) : '',
				));

				if(is_array($terms) && count($terms)) {
					foreach($terms as $term) {
						/* @var \WP_Term $term */
						$response[] = array(
							'value' => $term->slug,
							'label' => $term->name.' ('.$term->slug.')',
						);
					}
				}
//			print_r($_REQUEST);
			}

			wp_die(wp_json_encode($response));
		}

		function get_user($request){
			$response = array();

			if(is_array($request) && !empty($request) && isset($request['post_type']) && !empty($request['post_type']) && (isset($request['include']) || isset($request['term']))) {
				$users = get_users(array(
					'number'              => 20,
					'has_published_posts' => $request['post_type'],
					'search'              => isset($request['term']) ? sprintf('%1$s%2$s%1$s', '*', $request['term']) : '',
					'include'             => isset($request['include']) ? ($request['include']) : '',
					'exclude'             => isset($request['exclude']) ? ($request['exclude']) : '',
					'fields'              => array( 'ID', 'display_name' ),
				));
				foreach($users as $user) {
					$response[] = array(
						'value' => $user->ID,
						'label' => $user->display_name,
					);
				}
			}

			wp_die(wp_json_encode($response));
		}

		function get_post($request){
			$response = array();

			if(is_array($request) && !empty($request) && isset($request['post_type']) && !empty($request['post_type']) && (isset($request['include']) || isset($request['term']))) {
				$posts = get_posts(array(
					'numberposts' => 20,
					'post_type'   => $request['post_type'],
					's'           => isset($request['term']) ? $request['term'] : '',
					'include'     => isset($request['include']) ? ($request['include']) : '',
					'fields'      => array( 'ID', 'post_title' ),
				));
				foreach($posts as $_post) {
					$response[] = array(
						'value' => $_post->ID,
						'label' => $_post->post_title,
					);
				}
			}

			wp_die(wp_json_encode($response));
		}
	}

	new Ajax();
}
