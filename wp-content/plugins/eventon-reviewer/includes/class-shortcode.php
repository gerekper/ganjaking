<?php
/**
 * Event Reviewer shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-RE/Functions/shortcode
 * @version     0.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_re_shortcode{

	static $add_script;

	function __construct(){		
		add_filter('eventon_shortcode_popup',array($this, 'add_shortcode_options'), 17, 1);
		add_shortcode('evo_reviews',array($this, 'all_reviews_content'));
		// add_shortcode('evo_review_manager',array($this, 'evo_review_manager'));
	}
	
	function add_shortcode_options($shortcode_array){
		global $evo_shortcode_box;
		
		$new_shortcode_array = array(
			array(
				'id'=>'s_re',
				'name'=>'User Review Manager',
				'code'=>'evo_review_manager',
				'variables'=>''
			)
		);

		$shortcode_array[] = array(
			'id'=>'evore',
			'name'=>'Reviews from all Events',
			'code'=>'evo_reviews',
			'variables'=>array(
				array(
					'name'=>'All ratings title',
					'placeholder'=>'eg. All reviews',
					'type'=>'text',
					'var'=>'header','default'=>'0',
				),
				array(
					'name'=>'How many reviews to show',
					'placeholder'=>'eg. 5',
					'type'=>'text',
					'guide'=>'Leave blank or type all to show all reviews',
					'var'=>'count','default'=>'0',
				),
				array('var'=>'ratingtype','type'=>'select',
					'name'=>'Show rating types', 
					'guide'=>'Select which rating types you want to show in all reivews',
					'options'=>array(
						'all'=>'All reviews',
						'5'=>'Only 5 star reviews',
						'4'=>'Only 4+ star reviews',
						'3'=>'Only 3+ star reviews',
						'2'=>'Only 2+ star reviews',
					)
				),
			)
		);
		return $shortcode_array;
	}

	function all_reviews_content($atts){
		return EVORE()->frontend->show_all_reviews_html($atts);
	}

	// Frontend event review manager
		public function evo_review_manager($atts){
			global $eventon_re;
			ob_start();				
			echo $eventon_re->frontend->user_review_manager($atts);			
			return ob_get_clean();
		}
}
?>