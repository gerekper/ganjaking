<?php
/**
 * EventON singlEvent shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	singlEvent/Functions/shortcode
 * @version     1.1.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class evo_se_shortcode{

	function __construct(){

		add_shortcode('add_single_eventon', array($this,'eventon_SE_get_event'));
		add_filter('eventon_shortcode_popup',array($this,'evoSE_add_shortcode_options'), 10, 1);
	}

	// add new default shortcode arguments
	function evoSE_add_shortcode_defaults($arr){
		
		return array_merge($arr, array(
			'id'=>0,
			'show_excerpt'=>'no',
			'show_exp_evc'=>'no',
			'open_as_popup'=>'no',
			'ev_uxval'=>4,
			'repeat_interval'=>0,
			'ext_url'=>''
		));
		
	}

	//	Single event box shortcode
		function eventon_SE_get_event($atts){
			global $eventon_sin_event, $eventon;	
						
			add_filter('eventon_shortcode_defaults', array($this,'evoSE_add_shortcode_defaults'), 10, 1);
			$supported_defaults = $eventon->evo_generator->shell->get_supported_shortcode_atts();	

			$args = shortcode_atts( $supported_defaults, $atts ) ;
			//print_r($args);

			if(!empty($args['id'])){

				// user interaction for this event box
					$ev_uxval = 4; // default open as event page
					$external_url = '';
					if( $args['open_as_popup']=='yes' || $args['ev_uxval']==3){
						$ev_uxval = 3;
						$args['show_exp_evc'] = 'no';// override expended event card
					}elseif(  $args['ev_uxval']=='X'){
						$ev_uxval = 'X';
					}elseif(  $args['ev_uxval']=='2' && !empty($args['ext_url'])){// external link
						$ev_uxval = '2';
						$external_url = $args['ext_url'];
					}elseif(  $args['ev_uxval']=='1' ){// slidedown
						$ev_uxval = 1;
					}

					// update calendar ux_val to 4 so eventcard HTML content will not load on eventbox
					if( ($ev_uxval==3 && $args['show_exp_evc']!='no') || $ev_uxval==1){}else{
						$eventon->evo_generator->process_arguments(array('ux_val'=>4));	
					}
						

				$eventon->evo_generator->is_eventcard_hide_forcer= true;
				//$eventon->evo_generator->is_eventcard_hide_forcer= false;
				$opt = $eventon->evo_generator->evopt1;

					// google map variables
					$evcal_gmap_format = ($opt['evcal_gmap_format']!='')?$opt['evcal_gmap_format']:'roadmap';	
					$evcal_gmap_zooml = ($opt['evcal_gmap_zoomlevel']!='')?$opt['evcal_gmap_zoomlevel']:'12';	
						
					$evcal_gmap_scrollw = (!empty($opt['evcal_gmap_scroll']) && $opt['evcal_gmap_scroll']=='yes')?'false':'true';	

				wp_enqueue_style( 'evcal_single_event_one_style');				
				wp_enqueue_script( 'evcal_single_event_one');

									
				// get individual event content from calendar generator function
					$modified_event_ux = ($args['show_exp_evc']=='yes'  )? null: 4;
					$event = $eventon->evo_generator->get_single_event_data(
						$args['id'], 
						$args['lang'],
						$args['repeat_interval'],
						$args
					);
				
				// other event box variables
				$ev_excerpt = ($args['show_excerpt']=='yes')? "data-excerpt='1'":null;
				$ev_expand = ($args['show_exp_evc']=='yes')? "data-expanded='1'":null;
				
				ob_start();
					
				echo "<div class='ajde_evcal_calendar eventon_single_event eventon_event ' >";
				echo "<div class='evo-data' ".$ev_excerpt." ".$ev_expand." data-ux_val='{$ev_uxval}' data-exturl='{$external_url}' data-mapscroll='".$evcal_gmap_scrollw."' data-mapformat='".$evcal_gmap_format."' data-mapzoom='".$evcal_gmap_zooml."' ></div> ";
				echo "<div id='evcal_list' class='eventon_events_list ".($ev_uxval=='X'?'noaction':null)."'>";
				echo $event[0]['content'];
				echo "</div></div>";
					
				
				return ob_get_clean();
			}
		}

	//	ADD shortcode buttons to eventON shortcode popup	
		function evoSE_add_shortcode_options($shortcode_array){
			global $evo_shortcode_box, $evo_shortcode_box;

			global $post;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_SE',
					'name'=>'Single Event',
					'code'=>'add_single_eventon',
					'variables'=>array(
						array(
							'name'=>'Event ID',
							'type'=>'select','var'=>'id',
							'placeholder'=>'eg. 234',	
							'options'=>	$this->get_event_ids()		
						),array(
							'name'=>'Show Event Excerpt',
							'type'=>'YN',
							'guide'=>'Show event excerpt under the single event box',
							'var'=>'show_excerpt',
							'default'=>'no'
						),array(
							'name'=>'Show expanded eventCard',
							'type'=>'YN',
							'guide'=>'Show single event eventCard expanded on load',
							'var'=>'show_exp_evc',
							'default'=>'no'
						),array(
							'name'=>'User click on Event Box',
							'type'=>'select',
							'guide'=>'What to do when user click on event box. NOTE: Show expended eventCard will be overridden if opening lightbox',
							'var'=>'ev_uxval',
							'options'=>array(
								'4'=>'Go to Event Page',
								'3'=>'Open event as Lightbox',
								'2'=>'External Link',
								'1'=>'SlideDown EventCard',
								'X'=>'Do nothing'
							),
							'default'=>'4'
						),
						array(
							'name'=>'External Link URL',
							'type'=>'text',
							'guide'=>'If user click on event box is set to external link this field is required with a complete url',
							'var'=>'ext_url',
							'placeholder'=>'http://'
						),
						$evo_shortcode_box->shortcode_default_field('lang')
						,array(
							'name'=>'Repeat Interval ID',
							'type'=>'text','var'=>'repeat_interval',
							'guide'=>'Enter the repeat interval instance ID such as 1, 2,3. This is only for repeating events',
							'placeholder'=>'eg. 4',							
						)
					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}

		function get_event_ids(){
			global $post;
			$backup_post = $post;

			$events = new WP_Query(array(
				'orderby'=>'title','order'=>'ASC',
				'post_type'=> 'ajde_events',
				'posts_per_page'=>-1
			));
			$ids = array();
			if($events->have_posts()){
				while($events->have_posts()): $events->the_post();
					$id = $events->post->ID;
					$ids[$id] = get_the_title($id).' ('.$id.')';

				endwhile;	
				//$events->reset_postdata();
				wp_reset_postdata();			
			}
			
			$post = $backup_post;

			return $ids;

		}

}
?>