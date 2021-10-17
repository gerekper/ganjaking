<?php
/**
 * Event Lists Items front end class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON-LI/classes
 * @version     0.3
 */
class evoli_front{
	
	function __construct(){
		global $eventon_li;

		$this->evopt1 = get_option('evcal_options_evcal_1');
		$this->evopt2 = get_option('evcal_options_evcal_2');

		include_once('class-functions.php');
		$this->functions = new evoli_functions();

		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	
		add_action( 'wp_footer', array( $this, 'print_scripts' ) ,15);

		$this->opt2 = $eventon_li->opt2;
	}

	// Generate Lists HTML content
		function get_list_items($args){
			
			$args = EVO()->calendar->process_arguments( $args);	
			$this->load_required_evo_scripts();
			extract($args);

			$content = '';

			// Lang 
				$this->lang = isset($args['lang'])?  $args['lang']:'L1';

			// ux val filtering
				$args['ux_val'] = ($args['ux_val']=='1')? 3: $args['ux_val'];

			$category_type = $args['cat_type'];
			$list_type = $args['li_type'];
			$tax_name = $this->get_translated_tax_name($category_type);

			
			// list 
			if($list_type== 'li'){
				$terms = get_categories(array('taxonomy'=>$category_type));

				if($terms){	
					$content .= "<div class='EVOLI list_items ". ( $args['li_layout']) ."'>";
					if(!empty($args['li_title'])) $content .= "<h1 class='evoli_title'>".$args['li_title'].'</h1>';

					$content .= "<div class='EVOLI_container ". ($args['sep_month']=='yes'?'sepm':'')."'>";
					$content .= "<ul class='EVOLI_list {$category_type} '>";

					// each term
					$C = 1;
					foreach($terms as $term){
						$content .= $this->get_single_item_content($term,$category_type, $args, $tax_name );
						$C++;
					}
					$content .= "</ul>";
					$content .= $this->return_event_list_html($tax_name);
					$content .= "</div>";
					$content .= "<div id='evcal_footer' class='evo_bottom' style='display:none'><div class='evo_cal_data' data-sc='".
						json_encode( EVO()->calendar->shortcode_args ) ."'></div></div>";
					$content .= "</div>";
				}else{
					$content = "<div class='EVOLI'>".__('There are no terms in this taxonomy!')."</div>";
				}
			}else{
			// single list item
				if(empty($args['it_id'])) return __('WARNING: Item ID missing!','eventon');

				$term = get_term($args['it_id'] , $category_type, $args);

				if(empty($term)) return __('WARNING: Category does not exists!','eventon');
				
				$content .= "<div class='EVOLI list_single_item'>";	
				$content .= "<div class='EVOLI_container'>";
				$content .= "<ul class='EVOLI_list {$category_type} '>";
			
				$content .= $this->get_single_item_content($term,$category_type, $args, $tax_name );

				$content .= "</ul>";
				$content .= $this->return_event_list_html($tax_name);
				$content .= "</div>";
				$content .= "<div id='evcal_footer' class='evo_bottom' style='display:none'><div class='evo_cal_data' data-sc='".
						json_encode( EVO()->calendar->shortcode_args ) ."'></div></div>";
				$content .= "</div>";
			}

			return $content;
		}

		function get_single_item_content($term, $category_type, $args, $tax_name ){
			
			$data = $this->get_inner_item_data($term, $category_type, $args);
			$SC = EVO()->calendar->shortcode_args;

			$term_color = 'e5e5e5';
			$term_color = !empty($data['color'])? $data['color']: $term_color;
			$term_color = stripos($term_color, '#')=== false? '#'.$term_color:$term_color;

			$term_name = $this->lang('evolang_'.$category_type.'_'.$term->term_id,$term->name );				
											
			$additional = $data['add'] ."<p class='type_name'>".$tax_name."</p>";
				
			// class names for item
			$li_class[] = $args['it_stop']=='yes'? 'noevents':'';
			$li_class[] = !empty($data['left'])? 'lefter':'';
			$li_class = implode(' ', $li_class);

			$_li_styles = ($SC['li_layout'] == 'boxes')? 'background-color:'.$term_color: 'border-left-color:'. $term_color;

			return '<li class="EVOLI_list_item '.$li_class.'" data-id="'.$term->term_id.'" data-section="'.$term_name.'" style="'.$_li_styles.'" ><div class="inner">'.$data['left'].'<h2>'.$term_name. '</h2>'. $additional.'</div></li>';
		}
		function get_inner_item_data($term, $category_type, $args){
			$additional= $ITleft = $term_color = '';	

			// category type
				$CATTY = ( strpos($category_type, 'event_type')!== false)? 'event_type':$category_type;

			$term_meta = evo_get_term_meta( $CATTY, $term->term_id );

			switch($CATTY){
				case 'event_location':
					//$term_meta = get_option('taxonomy_'.$term->term_id);
					$term_meta = evo_get_term_meta( 'event_location', $term->term_id );
					if(!empty($term->description) && $args['it_hide_desc']!='yes')
						$additional .= "<p class='description'>".$term->description."</p>";
					if(!empty($term_meta['location_address']))
						$additional .= '<p class="address">'.$term_meta['location_address'].'</p>';

					if(!empty($term_meta['evo_loc_img'])){
						$img = wp_get_attachment_image_src($term_meta['evo_loc_img'], 'thumbnail');
						$ITleft = '<span class="it_image"><img src="'.$img[0].'"/></span>';
					}
				break;
				case 'event_organizer':
					//$term_meta = get_option('taxonomy_'.$term->term_id);
					$term_meta = evo_get_term_meta( 'event_organizer', $term->term_id );
					if(!empty($term->description) && $args['it_hide_desc']!='yes')
						$additional .= "<p class='description'>".$term->description."</p>";
					
					if(!empty($term_meta['evcal_org_contact']))
						$additional .= '<p class="contact">'.$term_meta['evcal_org_contact'].'</p>';

					if(!empty($term_meta['evcal_org_address']))
						$additional .= '<p class="address">'.$term_meta['evcal_org_address'].'</p>';

					if(!empty($term_meta['evcal_org_exlink']))
						$additional .= '<p class="link"><a href="'.$term_meta['evcal_org_exlink'].'">'.$term_meta['evcal_org_exlink'].'</a></p>';

					if(!empty($term_meta['evo_org_img'])){
						$img = wp_get_attachment_image_src($term_meta['evo_org_img'], 'thumbnail');
						$ITleft = '<span class="it_image"><img src="'.$img[0].'"/></span>';
					}
				break;
				case 'event_type':	
					$term_meta = get_option('evo_et_taxonomy_'.$term->term_id);

					if(!empty($term_meta['et_icon'])){
						$ITleft .= "<span class='it_icon'><i class='fa ".$term_meta['et_icon']."'></i></span>";
					}
					if(!empty($term_meta['et_color']))
						$term_color = $term_meta['et_color'];
					if(!empty($term->description) && $args['it_hide_desc']!='yes')
						$additional .= "<p class='description'>".$term->description."</p>";
					
				break;
			}

			// description
			if(!empty($term->description) && $args['it_hide_desc']!='yes')
				$additional .= "<p class='description'>".$term->description."</p>";

			// speakers compatibility
			if(!empty($term_meta['evo_spk_img'])){
				$img = wp_get_attachment_image_src($term_meta['evo_spk_img'], 'thumbnail');
				$ITleft = '<span class="it_image"><img src="'.$img[0].'"/></span>';
			}
		
			return array('add'=>$additional, 'left'=>$ITleft, 'color'=>$term_color);
		}

		function get_translated_tax_name($tax){

			$_tax_names_array = evo_get_localized_ettNames('',$this->evopt1,$this->evopt2);
			$slugs = apply_filters('evoli_translated_tax_names',array(
				'event_type'=>'et1',
				'event_type_2'=>'et2',
				'event_type_3'=>'et3',
				'event_type_4'=>'et4',
				'event_type_5'=>'et5',
				'event_location'=>'evloc',
				'event_organizer'=>'evorg',
			));

			if( !isset($slugs[$tax])) return $tax;

			// default name
				$default_name = $tax;
				if( strpos($tax, 'event_type')!== false){
					$cnt = $tax=='event_type'? 1: (str_replace('event_type_', '', $tax));
					$default_name = $_tax_names_array[$cnt];
				}

			return $this->lang('evcal_lang_'.$slugs[$tax], str_replace('_', ' ', $default_name) );
		}

		// return HTML for events list
		function return_event_list_html($tax_name){
			return "<div class='EVOLI_event_list'>
			<p class='EVOLI_back_btn'><i class='fa fa-chevron-left'></i>".$this->lang('EVOLIL_001','Back to List')."</p>
			<p class='EVOLI_section'><em>".$tax_name."</em><span></span></p>
			<div class='EVOLI_event_list_in'></div>
			</div>";
		}
		function get_events_list($args){
			$this->only__actions();
			$content = '';
				
			// CUT OFF time calculation
				/*//fixed time list
					if(!empty($args['pec']) && $args['pec']=='ft'){
						$__D = (!empty($args['fixed_date']))? $args['fixed_date']:date("j", current_time('timestamp'));
						$__M = (!empty($args['fixed_month']))? $args['fixed_month']:date("m", current_time('timestamp'));
						$__Y = (!empty($args['fixed_year']))? $args['fixed_year']:date("Y", current_time('timestamp'));

						$current_timestamp = mktime(0,0,0,$__M,$__D,$__Y);

					// current date cd
					}else if(!empty($args['pec']) && $args['pec']=='cd'){
						$current_timestamp = strtotime( date("m/j/Y", current_time('timestamp')) );
					}else{// current time ct
						$current_timestamp = current_time('timestamp');
					}
				*/
		

				$current_timestamp = current_time('timestamp');
				// reset arguments
				$args['fixed_date']= $args['fixed_month']= $args['fixed_year']='';
			
			// restrained time unix
				$number_of_months = (!empty($args['number_of_months']))? (int)($args['number_of_months']): 12;
				$month_dif = ($args['el_type']=='ue')? '+':'-';
				$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

				$restrain_monthN = ($number_of_months>0)?				
					date('n',  $unix_dif):
					date('n',$current_timestamp);

				$restrain_year = ($number_of_months>0)?				
					date('Y', $unix_dif):
					date('Y',$current_timestamp);			

			// upcoming events list 
				if($args['el_type']=='ue'){
					$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));
					$__focus_start_date_range = $current_timestamp;
					$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));
								
				}else{// past events list
					if(!empty($args['event_order']))	$args['event_order']='DESC';
					$args['hide_past']='no';					
					$__focus_start_date_range =  mktime(0,0,0,($restrain_monthN),1, ($restrain_year));
					$__focus_end_date_range = $current_timestamp;
				}
						
			// Add extra arguments to shortcode arguments
			$new_arguments = array(
				'focus_start_date_range'=>$__focus_start_date_range,
				'focus_end_date_range'=>$__focus_end_date_range,				
			);
			$args = (!empty($args) && is_array($args))? 
				wp_parse_args($new_arguments, $args): $new_arguments;

			// PROCESS variables
			$args__ = EVO()->calendar->process_arguments($args);
			EVO()->calendar->shortcode_args = $args__;

			//print_r($args__);

			//echo date('Y-m-d', $__focus_start_date_range);
			//echo date('Y-m-d', $__focus_end_date_range);
			
			$content =EVO()->calendar->calendar_shell_header(
				array(
					'month'=>$restrain_monthN,
					'year'=>$restrain_year, 
					'date_header'=>false,
					'sort_bar'=>false,
					'date_range_start'=>$__focus_start_date_range,
					'date_range_end'=> $__focus_end_date_range,
					'title'=>'',
					'send_unix'=>true
				)
			);
			$content .= EVO()->calendar->_generate_events('html');
			$content .= EVO()->calendar->calendar_shell_footer();

			$this->remove_only__actions();
			
			return  $content;	
		}

	// STYLES:  
		public function register_styles_scripts(){
			if(is_admin()) return false;
			wp_register_style( 'evoli_styles',EVOLI()->assets_path.'LI_styles.css');			
			wp_register_script('LI_script',EVOLI()->assets_path.'LI_script.js', array('jquery'), EVOLI()->version, true );
			wp_localize_script( 
				'LI_script', 
				'evoli_ajax_script', 
				array( 
					'evoli_ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evoli_nonce' )
				)
			);
			
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));				
		}
		public function print_scripts(){	if(EVOLI()->load_scripts) wp_enqueue_script('LI_script');		}
		function print_styles(){	wp_enqueue_style( 'evoli_styles');	}

		// Load required eventon scripts
		function load_required_evo_scripts(){
			
			wp_enqueue_script('evcal_gmaps');
			wp_enqueue_script('eventon_gmaps');
			wp_enqueue_script('eventon_init_gmaps');
			EVO()->frontend->load_default_evo_scripts();
		}

	// SUPPORT functions
		// ONLY for el calendar actions 
		public function only__actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);	
		}
		public function remove_only__actions(){
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));				
		}
		// add class name to calendar header for DV
		function eventon_cal_class($name){
			$name[]='evoLI';
			return $name;
		}
		// RETURN: language
			function lang($variable, $default_text){
				return EVOLI()->lang($variable, $default_text);
			}
		
}
