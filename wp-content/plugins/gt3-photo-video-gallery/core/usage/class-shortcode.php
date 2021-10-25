<?php

namespace GT3\PhotoVideoGallery\Usage;

use WP_Post;

defined('ABSPATH') OR exit;

class Shortcode {
	const KEY = '_gt3_usage_shortcodes';
	private static $instance = null;

	private $posts = array();

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct(){
		add_filter('do_shortcode_tag', array( $this, 'do_shortcode_tag' ), 20, 4);
		add_action('wp_footer', array( $this, 'wp_footer' ), 100);
	}

	public function do_shortcode_tag($output, $tag, $attr, $m){
		global $post;
		if($post instanceof WP_Post) {
			$this->storage_shortcode($post->ID, $tag);
		}

		return $output;
	}

	public function storage_shortcode($post_id, $shortcode){
		if(!key_exists($post_id, $this->posts)) {
			$this->posts[$post_id] = array();
		}
		if(!key_exists($shortcode, $this->posts[$post_id])) {
			$this->posts[$post_id][$shortcode] = 0;
		}
		$this->posts[$post_id][$shortcode]++;
	}

	public function wp_footer(){
		foreach($this->posts as $post_id => $shortcodes) {
			$usage     = get_post_meta($post_id, self::KEY);
			$new_usage = json_encode($shortcodes);
			if($usage !== $new_usage) {
				update_post_meta($post_id, self::KEY, $new_usage);
			}
		}
		$this->posts = array();
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
							'compare' => 'EXISTS',
						),
						array(
							'key'     => self::KEY,
							'compare' => 'LIKE',
							'value'   => 'gallery',
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
					return (false !== strpos($key, 'gallery'));
				}, ARRAY_FILTER_USE_KEY
				);

				$pro_usage[$post] = $usage;
			}
		}

		return $pro_usage;
	}

	/** @param \WP_Post */
	public function get_post_shortcodes($_post){
		$pattern = get_shortcode_regex();

		$module = $this;
		preg_replace_callback(
			"/$pattern/s",
			function($tag) use ($_post, $module){
				$module->storage_shortcode($_post->ID, $tag[2]);
			}, $_post->post_content
		);

		$this->wp_footer();
	}
}
