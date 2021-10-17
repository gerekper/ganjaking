<?php
/** 
 * Frontend Class for RSS plugin
 *
 * @author 		AJDE
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evorss_front{
	
	function __construct(){
		global $eventon_rss;
		add_action( 'init', array( $this, 'register_frontend_scripts' ) ,15);
		add_action('evo_cal_after_footer', array($this, 'rss_to_footer'), 10, 1);
		add_action( 'wp_footer', array( $this, 'print_scripts' ) ,15);

		// get rss feed slug from settings
		$rss_slug = get_evoOPT('1', 'evorss_page');
		$rss_slug = $eventon_rss->rss_slug = !empty($rss_slug)? $rss_slug: 'evofeed';
		add_feed($rss_slug, array($this, 'evo_rss_feed'));

		global $wp_rewrite;
		//$wp_rewrite->flush_rules();
	}

	// rss feed content
		function evo_rss_feed(){
			global $eventon_rss;
			include_once($eventon_rss->plugin_path.'/includes/rss.php');
		}
		
	// front end styles and scripts
		function register_frontend_scripts(){
			global $eventon_rss;
			wp_register_style( 'evo_rss_styles',$eventon_rss->plugin_url.'/assets/evorss_styles.css');
		}
		function print_scripts(){
			global $eventon_rss;
			if(!$eventon_rss->print_scripts_on) return;

			$this->print_front_end_scripts();
		}
		function print_front_end_scripts(){
			wp_enqueue_style('evo_rss_styles');
		}
	// RSS on front-end 
		function rss_to_footer($args){
			global $eventon_rss;
			//print_r($args);
			// shortcode variable rss passed as yes
			if(!empty($args['rss']) && $args['rss']=='yes'){
				$eventon_rss->print_scripts_on=true;
				$link = site_url().'/'.$eventon_rss->rss_slug;
				echo '<a class="evorss_rss_btn evcal_btn" href="'.$link.'"><em class="fa fa-rss"></em> '. eventon_get_custom_language('','evoRSS_001','RSS Feed for our events').'</a>';
			}
		}
	

}
new evorss_front();