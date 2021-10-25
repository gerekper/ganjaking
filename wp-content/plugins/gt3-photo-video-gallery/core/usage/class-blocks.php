<?php

namespace GT3\PhotoVideoGallery\Usage;

defined('ABSPATH') OR exit;

class Blocks {
	private static $instance = null;
	const KEY = '_gt3_usage_blocks';
	private static $allow_usage = false;

	private $blocks = array();

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function enable_usage(){
		self::$allow_usage = true;
	}

	public function disable_usage(){
		self::$allow_usage = false;
	}

	protected function get_blocks($block){
		foreach($block['innerBlocks'] as $chunk) {
			$this->get_blocks($chunk);
		}
		$block = $block['blockName'];
		if(!empty($block)) {
			if(!key_exists($block, $this->blocks)) {
				$this->blocks[$block] = 0;
			}
			$this->blocks[$block]++;
		}
	}

	private function __construct(){
		add_action('save_post', array( $this, 'save_post' ), 20, 2);
	}

	public function save_post($post_ID, $post){
		/** @var  \WP_Post $post */
		if((defined('REST_REQUEST') || self::$allow_usage) && $post->post_status === 'publish') {
			$blocks = parse_blocks($post->post_content);
			if(is_array($blocks) && count($blocks)) {
				foreach($blocks as $block) {
					$this->get_blocks($block);
				}

				$usage     = get_post_meta($post_ID, self::KEY);
				$new_usage = json_encode($this->blocks);
				if($usage !== $new_usage) {
					update_post_meta($post_ID, self::KEY, $new_usage);
				}
			}
			$this->blocks = array();
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
							'compare' => 'EXISTS',
						),
						array(
							'key'     => self::KEY,
							'compare' => '!=',
							'value'   => '[]',
						),
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

				$pro_usage[$post] = $usage;
			}
		}

		return $pro_usage;
	}
}
