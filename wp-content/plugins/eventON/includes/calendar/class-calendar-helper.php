<?php
/**
 * helper fnctions for calendar
 *
 * @class 		evo_cal_help
 * @version		3.0.6
 * @package		EventON/Classes
 * @category	Class
 * @author 		AJDE
 */

class evo_cal_help {
	public $opt1 = array();
	public $ordered_eventcard_fields = array();
	public function __construct(){
		$this->opt1 = EVO()->calendar->evopt1;
		// /$this->options = get_option('evcal_options_evcal_1');
	}
	
	// return classes array as a string
		function get_eventinclasses($atts){
			 
			$classnames[] = (!empty($atts['img_thumb_src']) && !empty($atts['show_et_ft_img']) && $atts['show_et_ft_img']=='yes')? 'hasFtIMG':'';

			$classnames[] = ($atts['event_type']!='nr')? 'event_repeat':null;	
			$classnames[] = $atts['event_description_trigger'];

			$classnames[] = (!empty($atts['existing_classes']['__featured']) && $atts['existing_classes']['__featured'])? 'featured_event':null;
			$classnames[] = (!empty($atts['existing_classes']['_cancel']) && $atts['existing_classes']['_cancel'])? 'cancel_event':null;
			$classnames[] = (!empty($atts['existing_classes']['_completed']) && $atts['existing_classes']['_completed'])? 'completed-event':null;

			$classnames[] = ($atts['monthlong'])? 'month_long':null;
			$classnames[] = ($atts['yearlong'])? 'year_long':null;

			
			// filter through existing class and remove true false values
				$existingClasses = array();
				if(is_array($atts)){
					foreach($atts['existing_classes'] as $field=>$value){
						//if($field==0 || $field ==1) continue;
						$existingClasses[$field]= $value;
					}
				}

			$classnames = array_merge($classnames, $existingClasses);
			$classnames = array_filter($classnames);

			return implode(' ',  $classnames);
		}

	function implode($array=''){
		if(empty($array))
			return '';

		return implode(' ', $array);
	}

	function get_attrs($array){
		if(empty($array)) return;

		$output = '';
		$array = array_filter($array);

		foreach($array as $key=>$value){
			if($key=='style' && !empty($value)){
				$output .= 'style="'. implode("", $value).'" ';
			}elseif($key=='rest'){
				$output .= implode(" ", $value);
			}else{
				if(is_array($value)) $value = json_encode($value);
				if( $key == 'data-j'){
					$output .= $key."='".$value."'";
				}else{
					$output .= $key.'="'.$value.'" ';
				}				
			}
		}

		return $output;
	}

	function evo_meta($field, $array, $type=''){
		switch($type){
			case 'tf':
				return (!empty($array[$field]) && $array[$field][0]=='yes')? true: false;
			break;
			case 'yn':
				return (!empty($array[$field]) && $array[$field][0]=='yes')? 'yes': 'no';
			break;
			case 'null':
				return (!empty($array[$field]) )? $array[$field][0]: null;
			break;
			default;
				return (!empty($array[$field]))? true: false;
			break;
		}		
	}

	// social share generator
		public function get_social_share_htmls($data){

			extract($data);

			// social media array

			$fb_js = "javascript:window.open(this.href, '', 'left=50,top=50,width=600,height=350,toolbar=0');return false;";
			$tw_js = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;";
			$gp_js = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";

			//http://www.facebook.com/sharer.php?s=100&p[url]=PERMALINK&p[title]=TITLE&display=popup" data-url="PERMALINK
			$social_sites = apply_filters('evo_se_social_media', array(
									
				'FacebookShare'    => array(
					'key'=>'eventonsm_fbs',
					'counter' =>1,
					'favicon' => 'likecounter.png',
					'url' => '<a class=" evo_ss" target="_blank" onclick="'.$fb_js.'"
						href="//www.facebook.com/sharer.php?u=PERMALINK" title="'.evo_lang('Share on facebook').'"><i class="fa fab fa-facebook"></i></a>',
				),
				'Twitter'    => array(
					'key'=>'eventonsm_tw',
					'counter' =>1,
					'favicon' => 'twitter.png',
					'url' => '<a class="tw evo_ss" onclick="'.$tw_js.'" href="//twitter.com/intent/tweet?text=TITLECOUNT&#32;-&#32;&url=PERMALINK" title="'.evo_lang('Share on Twitter').'" rel="nofollow" target="_blank" data-url="PERMALINK"><i class="fa fab fa-twitter"></i></a>',
				),
				'LinkedIn'=> array(
					'key'=>'eventonsm_ln',
					'counter'=>1,'favicon' => 'linkedin.png',
					'url' => '<a class="li evo_ss" href="//www.linkedin.com/shareArticle?mini=true&url=PERMALINK&title=TITLE&summary=SUMMARY" target="_blank" title="'.evo_lang('Share on Linkedin').'"><i class="fa fab fa-linkedin"></i></a>',
				),						
				'Pinterest' => Array (
					'key'=>'eventonsm_pn',
					'counter' =>1,
					'favicon' => 'pinterest.png',
					'url' => '<a class="pn evo_ss" href="//www.pinterest.com/pin/create/button/?url=PERMALINK&media=IMAGEURL&description=SUMMARY"
				        data-pin-do="buttonPin" data-pin-config="above" target="_blank" title="'.evo_lang('Share on Pinterest').'"><i class="fa fab fa-pinterest"></i></a>'
				),
				'Whatsapp' => Array (
					'key'=>'eventonsm_wa',
					'counter' =>1,
					'favicon' => 'whatsapp.png',
					'url' => '<a class="wa evo_ss" href="whatsapp://send? text=PERMALINK"
				        data-action="share/whatsapp/share" target="_blank" title="'.evo_lang('Share on Whatsapp').'"><i class="fa fab fa-whatsapp"></i></a>'
				),
				'SMS' => Array (
					'key'=>'eventonsm_sms',
					'counter' =>1,
					'favicon' => 'sms.png',
					'url' => '<a class="sms evo_ss" href="sms:?body=PERMALINK"
				         target="_blank" title="'.evo_lang('Share via SMS').'"><i class="fa fa-comment"></i></a>'
				),
				'EmailShare' => Array (
					'key'=>'eventonsm_email',						
					'url' => '<a class="em evo_ss" href="HREF" target="_blank"><i class="fa fa-envelope"></i></a>'
				)						
			));
			
			$sm_count = 0;
			$output_sm='';

			$title 		= str_replace('+','%20',urlencode($post_title));
			$titleCOUNT = $post_title;
			
			// foreach sharing option
			foreach($social_sites as $sm_site=>$sm_site_val){
				if(!empty($this->opt1[$sm_site_val['key']]) && $this->opt1[$sm_site_val['key']]=='yes'){
					// for emailing
					if($sm_site=='EmailShare'){
						$url = $sm_site_val['url'];

						// removed urlencode on title
						$title 		= str_replace('+','%20',($post_title));
						$title 		= str_replace('&','%26',($title));
						$title 		= str_replace('#038;','',($title));

						//echo $title;
						$mailtocontent = '';
						foreach( apply_filters('evo_emailshare_data', array(
							'event_name'=> array('label'=> evo_lang('Event Name'), 'value'=>$title),
							'event_date'=> array('label'=> evo_lang('Event Date'), 'value'=> ucwords($datetime_string)) ,
							'link'=> array('label'=> evo_lang('Link'), 'value'=>$encodeURL),

						)) as $key=>$data){
							$mailtocontent .= $data['label'] .': '. str_replace('+','%20',$data['value']) . '%0A';
						}
													

						$href_ = 'mailto:?subject='.$title.'&body='.$mailtocontent;
						$url = str_replace('HREF', $href_, $url);

						$link= "<div class='evo_sm ".$sm_site."'>".$url."</div>";
					}else{

						// check interest
						if( $sm_site=='Pinterest' && empty($imgurl)) continue;

						$site = $sm_site;
						$url = $sm_site_val['url'];
						
						$url = str_replace('TITLECOUNT', $titleCOUNT, $url);
						$url = str_replace('TITLE', $title, $url);			
						$url = str_replace('PERMALINK', $encodeURL, $url);
						$url = str_replace('SUMMARY', $summary, $url);
						$url = str_replace('IMAGEURL', $imgurl, $url);
						
						$linkitem = '';
						
						$style='';
						$target='';
						$href = $url;
						
						$link= "<div class='evo_sm ".$sm_site."'>".$href."</div>";
					}

					// Output
					$link = apply_filters('evo_single_process_sharable',$link);
					$output_sm.=$link;
					$sm_count++;
				}
			}

			return $output_sm;
		}

	// all available event card fields
		function get_eventcard_fields($legacy = false){
			$rearrange_items = apply_filters('eventon_eventcard_boxes',array(
				'ftimage'=>array('ftimage',__('Featured Image','eventon')),
				'eventdetails'=>array('eventdetails',__('Event Details','eventon')),
				'virtual'=>array('virtual',__('Virtual Event Details','eventon')),
				'health'=>array('health',__('Health Guidelines Details','eventon')),

				'timelocation'=>array('timelocation',__('Time & Location','eventon')),
				'learnmoreICS'=>array('learnmoreICS',__('Learn more and Add to calendar','eventon')),

				'time'=>array('time',__('Time','eventon')),
				'location'=>array('location',__('Location','eventon')),
				'repeats'=>array('repeats',__('Event Repeats Info','eventon')),
				'organizer'=>array('organizer',__('Event Organizer','eventon')),
				'locImg'=>array('locImg',__('Location Image','eventon')),
				'gmap'=>array('gmap',__('Google Maps','eventon')),
				'learnmore'=>array('learnmore',__('Learn More','eventon')),
				'addtocal'=>array('addtocal',__('Add to your calendar','eventon')),
				'relatedEvents'=>array('relatedEvents',__('Related Events','eventon')),
				'evosocial'=>array('evosocial',__('Social Share Icons','eventon')),
			));

			// removed -- learnmoreICS, timelocation

			// other values
				//get directions
				$rearrange_items['getdirection']=array('getdirection',__('Get Directions','eventon'));
				
					
				//paypal
				if( isset($this->opt1['evcal_paypal_pay']) && $this->opt1['evcal_paypal_pay']=='yes')
					$rearrange_items['paypal']= array('paypal',__('Paypal','eventon'));
				
				// custom fields
				$_cmd_num = evo_calculate_cmd_count( $this->opt1 );

				for($x=1; $x<=$_cmd_num; $x++){
					$val1 = $this->opt1['evcal_ec_f'.$x.'a1'];
					$val2 = $this->opt1['evcal_af_'.$x];
					$val3 = $this->opt1['evcal_af_'.$x];
					if( $val1  && $val2 && $val3 =='yes'){
						$rearrange_items['customfield'.$x] = 
							array('customfield'.$x, $this->opt1['evcal_ec_f'.$x.'a1'] );
					}
				}

			if(!$legacy){
				unset($rearrange_items['learnmoreICS']);	
				unset($rearrange_items['timelocation']);

				$R = array();
				foreach($rearrange_items as $F=>$V){
					$R[ $F ] = $V[1];
				}	

				return $R;
			}
			
			return $rearrange_items;
		}

	// Get EventCard Fields Array - 4.0
		public function get_eventcard_fields_array(){
			$opt = $this->opt1;
			if( isset($opt['evo_ecl'])){
				$evo_ecl = json_decode( html_entity_decode($opt['evo_ecl'] ), true);
			}else{

				$fields = $hidden_items = array();

				// load legacy fields
				$event_card_fields = $this->get_eventcard_fields(true);

				$all_fields = array();
				foreach($event_card_fields as $FK=>$FF){
					if( empty($FF)) continue;
					$all_fields[] = $FK;
				}

				// previous saved event order
				if( !empty($opt['evoCard_order'])){
					$old_order = $opt['evoCard_order'];
					
					$fields = explode(',', $old_order);
					$hidden_items = !empty($opt['evoCard_hide']) ? $opt['evoCard_hide']: '';
					$hidden_items = explode(',', $hidden_items);
				}else{
					$fields = $all_fields;
				}

				$count = 1;
				foreach($fields as $ii){

					if( empty($ii)) continue;
					if( in_array($ii, array('time','location','learnmore','addtocal'))) continue;

					if( $ii == 'timelocation'){
						$evo_ecl[ $count][1] = array(
							'n' =>'time','h'=> (in_array($ii, $hidden_items) ? 'y': ''),
						);
						$evo_ecl[ $count][2] = array(
							'n' =>'location','h'=> (in_array($ii, $hidden_items) ? 'y': ''),
						);
						$count++; continue;
					}

					if( $ii == 'learnmoreICS'){
						$evo_ecl[ $count][1] = array(
							'n' =>'learnmore','h'=> (in_array($ii, $hidden_items) ? 'y': ''),
						);
						$evo_ecl[ $count][2] = array(
							'n' =>'addtocal','h'=> (in_array($ii, $hidden_items) ? 'y': ''),
						);
						$count++; continue;
					}

					$evo_ecl[ $count][1] = array(
						'n' =>$ii,'h'=> (in_array($ii, $hidden_items) ? 'y': ''),
					);
					$count++;
				}
			}

			
			return $evo_ecl;
		}

	// ORDERED EventCard Fields
		function _is_card_field($field_var){
			if(count($this->ordered_eventcard_fields)==0) return true;
			if(in_array($field_var, $this->ordered_eventcard_fields)) return true;
			return false;
		}

	// event top fields - @version 4.1
		function get_eventtop_all_fields(){

			$base = array(
				'ft_img'=>__('Event Image'),
				'day_block' =>__('Event date blcok'),
				'tags'=>__('Tags'),
				'title'=>__('Title'),
				'subtitle'=>__('Subtitle'),
				'time'=>__('Event Time'),
				'location'=>__('Location'),
				'organizer'=>__('Organizer'),
				'eventtags'=>__('Event Tag Types'),
				'progress_bar'=>__('Event Progress Bar')
			);
			for($x =1; $x< evo_retrieve_cmd_count() +1; $x++){
				$base['cmd'.$x] = __('Custom Field'). " ".$x;
			}

			// add event types	
				for($x =1; $x< evo_get_ett_count() +1; $x++){
					$base['eventtype'.($x==1? '': $x)] = __('Event Type'). ($x==1? '': $x);
				}

			// add addon fields
			$additions = apply_filters('evo_eventtop_adds' , array());
			if( count($additions)>0){
				foreach($additions as $key=>$ad_field){
					if( $key == '0')  $key = $ad_field;
					$base[$key] = $ad_field;
				}
			}

			return $base;
		}

		function get_eventtop_fields_array(){

			$all_fields = $this->get_eventtop_all_fields();
			$all_fields_array = array();
			$used_fields = array();
			
			foreach($all_fields as $f=>$v){
				$all_fields_array[] = $f;
			}

			$this->opt1 = get_option('evcal_options_evcal_1');

			$evo_etl = isset($this->opt1['evo_etl']) ? 
				 json_decode( html_entity_decode($this->opt1['evo_etl'] ), true): 
				 false;

			$saved_eventtop_fields = isset($this->opt1['evcal_top_fields']) ? 
				$this->opt1['evcal_top_fields']: array();

			// if fields are not set, using for first times
			// f - field v - visibility
			if(!$evo_etl){

				// build the ETL for first time
				$evo_etl = array(
					'c0'=> array(),
					'c1'=> array(1 =>array('f'=>'ft_img', 'v'=>'y') ),
					'c2'=> array(1=>array('f'=>'day_block') ),
					'c3'=> array(
						1=>array('f'=>'tags', 'v'=>'y'),
						array('f'=>'title', 'v'=>'y'),
						array('f'=>'subtitle', 'v'=>'y'),
						array('f'=>'time', 'v'=>'y'),
						array('f'=>'location', 'v'=>'y'),
						array('f'=>'organizer', 'v'=>'y'),
						array('f'=>'eventtags', 'v'=>'y'),
						array('f'=>'progress_bar', 'v'=>'y')
					),
					'c4'=> array()
				);

				// add custom fields
				for($x =1; $x< evo_retrieve_cmd_count() +1; $x++){
					$evo_etl['c3'][] = array('f' => 'cmd'.$x, 'v'=> 'y' );
				}	

				// add event types	
				for($x =1; $x< evo_get_ett_count() +1; $x++){
					$evo_etl['c3'][] = array('f' => 'eventtype'.($x==1? '': $x), 'v'=> 'y' );
				}		

				// add addon fields
				$additions = apply_filters('evo_eventtop_adds' , array());
				if( count($additions)>0){
					foreach($additions as $key=>$ad_field){
						if( $key == '0')  $key = $ad_field;
						$evo_etl['c3'][] = array('f' => $key, 'v'=> 'y' );
						$saved_eventtop_fields[] = $key;
					}
				}

				// add default fields of image and day block
				$saved_eventtop_fields[] = 'day_block';
				$saved_eventtop_fields[] = 'ft_img';
				$saved_eventtop_fields[] = 'title';
				$saved_eventtop_fields[] = 'subtitle';

				// go through each field in design and check if they are in use
				foreach($evo_etl as $col=>$coldata){
					foreach($coldata as $ind=>$fields){
						if(!isset( $fields['f'])) continue;
						$field_key = $fields['f'];
						
						if(  in_array($field_key, $saved_eventtop_fields)  ){
							$used_fields[] = $fields['f'];
						}else{
							unset($evo_etl[$col][$ind]);
						}
					}
				}

			}else{
				// go through each field in design and check if they are in use				
				foreach($evo_etl as $col=>$coldata){
					foreach($coldata as $ind=>$fields){
						if(!isset( $fields['f'])) continue;						
						$used_fields[] = $fields['f'];						
					}
				}
			}			

			

			// create day block values from legacy - what to show
			$evotop_dayblock = isset($this->opt1['evotop_dayblock']) ? $this->opt1['evotop_dayblock'] :  false;
			if( !$evotop_dayblock){
				foreach( array(
					'dayname','eventyear','eventendyear'
				) as $ff){
					if( in_array($ff, $saved_eventtop_fields)) $evotop_dayblock[] = $ff;
				}
			}
			// create location data from legacy to show
			$evotop_location = isset($this->opt1['evotop_location']) ? $this->opt1['evotop_location'] :  false;
			if( !$evotop_location){
				$location_on = false;
				if( in_array('locationame', $saved_eventtop_fields)){
					$location_on = true;
					$evotop_location = 'locationame';
				}
				if( in_array('location', $saved_eventtop_fields)){
					$evotop_location = ( !$evotop_location ? 'location':'both');
					$location_on = true;
				}
				if($location_on){
					$used_fields[] = 'location';
					$evotop_location = 'location';
				}
			}

			
			


			return array(
				'all'=> $all_fields,
				'alla'=> $all_fields_array,
				'used'=> $used_fields,
				'layout'=>$evo_etl,
				'day_block'=> $evotop_dayblock,
				'location'=> $evotop_location
			);
		}

	// get repeating intervals for the event
		function get_ri_for_event($event_){
			return (!empty($event_['event_repeat_interval'])? 
				$event_['event_repeat_interval']: 
				( !empty($_GET['ri'])? (int)$_GET['ri']: 0) );
		}

	// get event type #1 font awesome icon
		function get_tax_icon($tax, $term_id, $opt){

			if(!empty($opt['evcal_hide_filter_icons']) && $opt['evcal_hide_filter_icons']=='yes') return false;

			$icon_str = false;
			if($tax == 'event_type'){
				$term_meta = get_option( "evo_et_taxonomy_$term_id" ); 
				if( !empty($term_meta['et_icon']) )
					$icon_str = '<i class="fa '. $term_meta['et_icon']  .'"></i>';
			}
			return $icon_str;
		}

	// get all event default values
	// updated 2.8
		function get_calendar_defaults(){
			$options = EVO()->calendar->evopt1;
			$SC = EVO()->calendar->shortcode_args;

			$defaults = array();

			$defaults['ux_val'] = !empty($SC['ux_val'])? $SC['ux_val']: false;
			$defaults['hide_end_time'] = (!empty($SC['hide_end_time']) && $SC['hide_end_time']=='yes' )? true: false;
			$defaults['ft_event_priority'] = (!empty($SC['ft_event_priority']) && $SC['ft_event_priority']=='yes' )? true: false;
			$defaults['eventcard_open'] = evo_settings_check_yn($SC,'evc_open');

			// SCHEMA
				$show_schema = EVO()->cal->check_yn('evo_schema')? false: true;
				if(EVO()->calendar->__calendar_type =='single' && EVO()->cal->get_prop('evcal_schema_disable_section') =='single' && !$show_schema)
					$show_schema = true;

				$defaults['show_schema'] = $show_schema;

				$show_jsonld = EVO()->cal->check_yn('evo_remove_jsonld')? false:true;						
				if(EVO()->calendar->__calendar_type =='single' && EVO()->cal->get_prop('evo_remove_jsonld_section') =='single' && !$show_jsonld)
					$show_jsonld = true;

				$defaults['show_jsonld'] = $show_jsonld;

			// default event image
				if(EVO()->cal->check_yn('evcal_default_event_image_set') && !empty($options['evcal_default_event_image']) ){
					$defaults['image'] = $options['evcal_default_event_image'];
				}

			// default event color
				$defaults['color'] = (!empty($options['evcal_hexcode']))? '#'.$options['evcal_hexcode']:'#4bb5d8';
			
			// check if single events addon active
				$defaults['single_addon']  = true;		
				$defaults['user_loggedin'] = is_user_logged_in();


			$defaults['start_of_week'] = get_option('start_of_week');
			$defaults['hide_arrows'] = EVO()->cal->check_yn('evcal_arrow_hide');
			$defaults['wp_date_format'] = evo_convert_php_moment(EVO()->calendar->date_format);
			$defaults['wp_time_format'] = evo_convert_php_moment( EVO()->calendar->time_format );
			$defaults['utc_offset'] = get_option('gmt_offset');

			// google maps
			$defaults['google_maps_load'] = EVO()->calendar->google_maps_load;
				
			return apply_filters('evo_calendar_defaults',$defaults, $options, $SC);
		}


	// return the login message with button for fields that require login
		function get_field_login_message(){
			global $wp;
			$options_1 = $this->opt1 ;
			$current_url = home_url(add_query_arg(array(),$wp->request));

			$link = wp_login_url($current_url);

			if(!empty($options_1['evo_login_link']))
				$link = $options_1['evo_login_link'];

			return sprintf("%s <a href='%s' class='evcal_btn'>%s</a>", evo_lang('Login required to see the information') , $link, evo_lang('Login'));
		}

	// run special character encoding
		function htmlspecialchars_decode($data){
			return ( evo_settings_check_yn($this->opt1, 'evo_dis_icshtmldecode'))? 
				$data:
				htmlspecialchars_decode($data);
		}	


	// time functions
		function time_since($old_time, $new_time){
	        $since = $new_time - $old_time;
	        // array of time period chunks
	        $chunks = array(
	            /* translators: 1: The number of years in an interval of time. */
	            array( 60 * 60 * 24 * 365, _n_noop( '%s year', '%s years', 'wp-crontrol' ) ),
	            /* translators: 1: The number of months in an interval of time. */
	            array( 60 * 60 * 24 * 30, _n_noop( '%s month', '%s months', 'wp-crontrol' ) ),
	            /* translators: 1: The number of weeks in an interval of time. */
	            array( 60 * 60 * 24 * 7, _n_noop( '%s week', '%s weeks', 'wp-crontrol' ) ),
	            /* translators: 1: The number of days in an interval of time. */
	            array( 60 * 60 * 24, _n_noop( '%s day', '%s days', 'wp-crontrol' ) ),
	            /* translators: 1: The number of hours in an interval of time. */
	            array( 60 * 60, _n_noop( '%s hour', '%s hours', 'wp-crontrol' ) ),
	            /* translators: 1: The number of minutes in an interval of time. */
	            array( 60, _n_noop( '%s minute', '%s minutes', 'wp-crontrol' ) ),
	            /* translators: 1: The number of seconds in an interval of time. */
	            array( 1, _n_noop( '%s second', '%s seconds', 'wp-crontrol' ) ),
	        );

	        if ( $since <= 0 ) {
	            return __( 'now', 'wp-crontrol' );
	        }

	        // we only want to output two chunks of time here, eg:
	        // x years, xx months
	        // x days, xx hours
	        // so there's only two bits of calculation below:

	        // step one: the first chunk
	        for ( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) {
	            $seconds = $chunks[ $i ][0];
	            $name = $chunks[ $i ][1];

	            // finding the biggest chunk (if the chunk fits, break)
	            if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
	                break;
	            }
	        }

	        // set output var
	        $output = sprintf( translate_nooped_plural( $name, $count, 'wp-crontrol' ), $count );

	        // step two: the second chunk
	        if ( $i + 1 < $j ) {
	            $seconds2 = $chunks[ $i + 1 ][0];
	            $name2 = $chunks[ $i + 1 ][1];

	            if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
	                // add to output var
	                $output .= ' ' . sprintf( translate_nooped_plural( $name2, $count2, 'wp-crontrol' ), $count2 );
	            }
	        }

	        return $output;
	    }


	// wpdb based event post meta retrieval
	// @since 2.5.5
		function event_meta($event_id, $fields){
			global $wpdb;

			$fields_str = '';
			$select = '';

			asort($fields);

			$len = count($fields); $i=1;
			foreach($fields as $field){
				$fields_str .= "'{$field}". ($i==$len? "'":"',");
				$select .= "MT.meta_value AS {$field}" . ($i==$len? "":",");
				$i++;
			}

			//print_r($fields_str);
	        $sql = "SELECT MT.meta_value
	            FROM $wpdb->postmeta AS MT
	            WHERE MT.meta_key IN ({$fields_str}) 
	            AND MT.post_id='{$event_id}' ORDER BY MT.meta_key DESC";
			$results = $wpdb->get_results( $sql);

			if(!$results && count($results) ==0) return false;

			//print_r($sql);
			//print_r($fields);

			$output = array();
			foreach($results as $index=>$result){
				$output[ $fields[$index]] = maybe_unserialize($result->meta_value);
			}
	        //print_r($output);
			return $output;

		}

	// use this to save multiple event post meta values with one data base query 
	// @since 2.5.6
	    function update_event_meta($event_id, $fields){
	        // check required values
	        $event_id = absint($event_id); if(!$event_id) return false;
	        $table = _get_meta_table('post');   if(!$table) return false;


	        $values = array();
	        foreach($fields as $meta_key=>$meta_value){
	            $meta_key = wp_unslash($meta_key);
	            $meta_value = maybe_serialize(wp_unslash($meta_value));

	            $values[] = "('{$meta_key}','{$meta_value}','{$event_id}')";
	        }

	        $values = implode(',', $values);

	        global $wpdb;

	        $res = $wpdb->update(
	            $table,
	            array(
	                'meta_value'=>'yes'
	            ),
	            array(
	                'meta_key'=>'_evoto_block_assoc'
	            )
	        );

	        /*$results = $wpdb->query(  
	            "INSERT INTO $wpdb->postmeta (meta_key, meta_value, post_id)
	            VALUES ('_evoto_block_assoc','yes','1840') 
	            ON DUPLICATE KEY UPDATE meta_key=VALUES(meta_key), meta_value=VALUES(meta_value)");

	        echo $wpdb->show_errors(); 
	        echo $wpdb->print_error();
	        */

	    }





}