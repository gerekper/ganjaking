<?php
/**
 * front-end
 * @version 	0.1
 */

class EVOIA_Frontend{

	public $SC = array();

	function __construct(){
		//add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	

		add_filter('eventon_wp_queried_events_list', array($this, 'evo_wp_query'),10,2);
		add_filter('evo_wp_query_post_type_if', array($this, 'wp_query_if'),10,2);

		add_filter('evo_event_etop_class_names', array($this, 'event_class_names'),10,3);
		add_filter('evo_event_etop_felds_array', array($this, 'event_etop_data'),10,3);
		add_filter('evodata_title', array($this, 'etop_title'),10,2);
		add_filter('evodata_subtitle', array($this, 'etop_subtitle'),10,2);
		add_filter('evo_event_data_array', array($this, 'event_data'),10,3);
		add_filter('evo_event_data_permalink', array($this, 'event_permalink'),10,3);

		// shortcode
		add_filter('eventon_calhead_shortcode_args', array($this, 'calhead_args'), 10, 2);
		add_filter('eventon_shortcode_defaults',array($this,  'add_shortcode_defaults'), 10, 1);
		add_filter('eventon_basiccal_shortcodebox',array($this, 'shortcode_fields'), 10, 1);
	}

// shortcode
	function calhead_args($array, $arg=''){
		if(!empty($arg['include_any']))	$array['include_any'] = $arg['include_any'];
		return $array;
	}
	function add_shortcode_defaults($arr){
		return array_merge($arr, array(
			'include_any'=>'no',
		));	
	}
	function shortcode_fields($array){

		$array[] = array(
			'name'=> __('Include any posts in this calendar','evoia'),
			'type'=>'YN',
			'guide'=> __('This will include all selected other post types inside this calendar in the appropriate date range','evoia'),
			'var'=>'include_any',
			'default'=>'no'
		);
		return $array; 			
	}

// integration
	public function event_permalink($link, $EVENT, $cal){

		$SC = $this->SC = $cal->shortcode_args;

		if(!isset($SC['include_any'])) return $link;
		if(isset($SC['include_any']) && $SC['include_any'] != 'yes') return $link;

		if(!$EVENT->check_yn('_evo_inc')) return $link;

		return $link;

		
	}
	public function event_data($arr, $EVENT, $cal){

		$SC = $this->SC = $cal->shortcode_args;

		if(!isset($SC['include_any'])) return $arr;
		if(isset($SC['include_any']) && $SC['include_any'] != 'yes') return $arr;

		if(!$EVENT->check_yn('_evo_inc')) return $arr;

		unset($arr['schema']);
		unset($arr['schema_jsonld']);
		
		return $arr;
		
	}
	public function etop_title($text, $EVENT){
		$SC = $this->SC;

		if(!isset($SC['include_any'])) return $text;
		if(isset($SC['include_any']) && $SC['include_any'] != 'yes') return $text;

		if( $EVENT->post_type != 'ajde_events'){
			return $EVENT->get_meta('_evoia_title');
		}

		return $text;

	}
	public function etop_subtitle($text, $EVENT){
		$SC = $this->SC;

		if(!isset($SC['include_any'])) return $text;
		if(isset($SC['include_any']) && $SC['include_any'] != 'yes') return $text;

		if( $EVENT->post_type != 'ajde_events'){
			return $EVENT->get_prop('_evoia_stitle');
		}

		return $text;

	}
	public function event_etop_data($arr, $EVENT, $cal){

		$SC = $this->SC = $cal->shortcode_args;

		if(!isset($SC['include_any'])) return $arr;
		if(isset($SC['include_any']) && $SC['include_any'] != 'yes') return $arr;

		if( $EVENT->post_type != 'ajde_events'){
			$arr['belowtitle'] = false;
			$arr['day_block'] = false;
		} 

		return $arr;
		
	}
	public function event_class_names($arr, $EVENT, $calendar){
		$SC = $calendar->shortcode_args;

		if(!isset($SC['include_any'])) return $arr;
		if(isset($SC['include_any']) && $SC['include_any'] != 'yes') return $arr;

		if( $EVENT->post_type != 'ajde_events') $arr[] = 'anypost';
		return $arr;
	}
	public function wp_query_if($bool, $SC){
		if(!isset($SC['include_any'])) return $bool;
		if(isset($SC['include_any']) && $SC['include_any'] != 'yes') return $bool;

		return false;
	}
	public function evo_wp_query($list,  $SC){		

		if(!isset($SC['include_any'])) return $list;
		if(isset($SC['include_any']) && $SC['include_any'] != 'yes') return $list;

		
		$posts = new WP_Query( array(
			'post_type'=>'any',
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'order_by'=>'menu_order',
			'meta_query'=>array(
				array(
					'key'=>'_evo_inc',
					'value'=>'yes'
				)
			)
		));

		if($posts->have_posts()){

			date_default_timezone_set('UTC');

			
			$add_items = EVO()->calendar->wp_query_event_cycle_filter( $posts );

			//print_r($posts);
			//print_r(EVO()->calendar->cal_range_data);

			$list = array_merge($list, $add_items);
		}

		

		return $list;
	}

	


// STYLES:  
	public function register_styles_scripts(){
		if(is_admin()) return;
		
		wp_register_style( 'evost_styles',EVORC()->assets_path.'styles.css');			
		wp_register_script('evorc_script',EVORC()->assets_path.'script.js', array('jquery'), EVORC()->version, true );
		wp_localize_script( 
			'evorc_script', 
			'evorc_ajax_script', 
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
				'postnonce' => wp_create_nonce( 'evorc_nonce' )
			)
		);
		
		$this->print_scripts();
		add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));				
	}
	public function print_scripts(){				
		wp_enqueue_script('evorc_script');		
	}
	function print_styles(){	wp_enqueue_style( 'evost_styles');	}

	
}