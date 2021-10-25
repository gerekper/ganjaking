<?php

namespace GT3\PhotoVideoGallery\Usage;

use Elementor\Plugin as Elementor_Plugin;

defined('ABSPATH') OR exit;

class Elementor_Widgets {
	private static $instance = null;
	const KEY = '_elementor_controls_usage';

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct(){
		if(!class_exists('\Elementor')) {
			return;
		}
	}

	public static function get_usage(){
		$query = new \WP_Query(
			array(
				'post_type'      => 'any',
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'meta_query'     => array_merge(
					array(
						'relation' => 'AND',
					),
					array(
						array(
							'key'     => self::KEY,
							'compare' => 'LIKE',
							'value'   => 'gt3pg',
						),
					)
				),
			)
		);

		$pro_usage = array();

		foreach($query->posts as $post) {
			$usage = get_post_meta($post, self::KEY, true);

			if(is_string($usage)) {
				$_usage = json_decode($usage, true);
				if(json_last_error()) {
					continue;
				}
				$usage = $_usage;
			}
			if(is_array($usage)) {
				$usage = array_filter(
					$usage, function($key){
					return (false !== strpos($key, 'gt3pg'));
				}, ARRAY_FILTER_USE_KEY
				);
				$usage = array_map(function($widget) {
					return $widget['count'];
				}, $usage);

				$pro_usage[$post] = $usage;
			}
		}

		return $pro_usage;
	}
}
