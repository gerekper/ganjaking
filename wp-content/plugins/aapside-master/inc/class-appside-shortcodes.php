<?php
/*
 * @package Dizzcox
 * @since 1.0.0
 * */

if ( !defined('ABSPATH') ){
	exit(); // exit if access directly
}


if ( !class_exists('Appside_Master_shortcodes') ){

	class Appside_Master_shortcodes{
		/*
			* $instance
			* @since 1.0.0
			* */
		private static $instance;
		/*
		* construct()
		* @since 1.0.0
		* */
		public function __construct() {
			//social post share
			add_shortcode('appside_post_share',array($this,'post_share'));
		}
		/*
	   * getInstance()
	   * @since 1.0.0
	   * */
		public static function getInstance(){
			if ( null == self::$instance ){
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Shortcode :: dizzcox_post_share
		 * @since 1.0.0
		 * */
		public function post_share($atts, $content = null){

			extract(shortcode_atts(array(
				'custom_class' => '',
			),$atts));

			global $post;
			$output = '';

			if ( is_singular() || is_home() ){

				//get current page url
				$appside_url = urlencode_deep(get_permalink());
				//get current page title
				$appside_title = str_replace(' ','%20',get_the_title($post->ID));
				//get post thumbnail for pinterest
				$appside_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),'full');

				//all social share link generate
				$facebook_share_link = 'https://www.facebook.com/sharer/sharer.php?u='.$appside_url;
				$twitter_share_link = 'https://twitter.com/intent/tweet?text='.$appside_title.'&amp;url='.$appside_url.'&amp;via=Crunchify';
				$linkedin_share_link = 'https://www.linkedin.com/shareArticle?mini=true&url='.$appside_url.'&amp;title='.$appside_title;
				$pinterest_share_link = 'https://pinterest.com/pin/create/button/?url='.$appside_url.'&amp;media='.$appside_thumbnail[0].'&amp;description='.$appside_title;

                $output .='<ul class="social-share">';
                $output .='<li class="title">'.esc_html__('Share:','dizzcox').'</li>';
                $output .='<li><a class="facebook" href="'.esc_url($facebook_share_link).'"><i class="fa fa-facebook-f"></i></a></li>';
                $output .='<li><a class="twitter" href="'.esc_url($twitter_share_link).'"><i class="fa fa-twitter"></i></a></li>';
                $output .='<li><a class="linkedin" href="'.esc_url($linkedin_share_link).'"><i class="fa fa-linkedin"></i></a></li>';
                $output .='<li><a class="pinterest" href="'.esc_url($pinterest_share_link).'"><i class="fa fa-pinterest-p"></i></a></li>';
                $output .='</ul>';

				return $output;

			}
		}
	}//end class

	if ( class_exists('Appside_Master_shortcodes') ){
		Appside_Master_shortcodes::getInstance();
	}

}//end if
