<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Love')) {
	class Mfn_Love
	{
		public function __construct()
		{
			add_action('wp_ajax_mfn_love', array( $this, 'ajax' ));
			add_action('wp_ajax_nopriv_mfn_love', array( $this, 'ajax' ));
			add_action('wp_ajax_mfn_love_randomize', array( $this, 'randomize' ));
			add_action('wp_ajax_nopriv_mfn_love_randomize', array( $this, 'randomize' ));
		}

		public function ajax($post_id)
		{
			echo esc_attr($this->love($_POST['post_id']));
			exit;
		}

		public function randomize()
		{
			$post_type = htmlspecialchars(stripslashes($_POST['post_type']));

			$aPosts = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => $post_type ? $post_type : false,
				'fields' => 'ids',
			));

			if (is_array($aPosts)) {
				foreach ($aPosts as $post) {
					$love_count = rand(10, 100);	// Random number of loves [min:10, max:100]
					update_post_meta($post, 'mfn-post-love', $love_count);
				}

				esc_html_e('Love randomized', 'mfn-opts');
			}

			exit;
		}

		public function love($post_id)
		{
			if (! is_numeric($post_id)) {
				return;
			}

			$love_count = get_post_meta($post_id, 'mfn-post-love', true);

			if (isset($_COOKIE['mfn-post-love-'. $post_id])) {
				return $love_count;
			}

			$love_count++;
			update_post_meta($post_id, 'mfn-post-love', $love_count);
			setcookie('mfn-post-love-'. $post_id, $post_id, time()*20, '/');

			return $love_count;

		}

		public static function get()
		{
			global $post;

			if (! mfn_opts_get('love')) {
				return false;
			}

			$love_count = get_post_meta($post->ID, 'mfn-post-love', true);
			if (!$love_count) {
				$love_count = 0;
				add_post_meta($post->ID, 'mfn-post-love', $love_count, true);
			}

			$class = '';
			if (isset($_COOKIE['mfn-post-love-'. $post->ID])) {
				$class = 'loved';
			}

			return '<a href="#" class="mfn-love '. esc_attr($class) .'" data-id="'. esc_attr($post->ID) .'"><span class="icons-wrapper"><i class="icon-heart-empty-fa"></i><i class="icon-heart-fa"></i></span><span class="label">'. esc_html($love_count) .'</span></a>';
		}
	}
}
new Mfn_Love();

if (! function_exists('mfn_love')) {
	function mfn_love($return = '')
	{
		return Mfn_Love::get();
	}
}
