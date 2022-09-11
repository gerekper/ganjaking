<?php
/**
* Calendar Filtering
* @version 4.1.2
*/

class EVO_Cal_Filering{
	public function __construct(){
		$this->cal = EVO()->evo_generator;
		add_filter('evo_cal_above_header_btn', array($this, 'cal_header_btn'),10,2);
		add_action('evo_cal_above_header_btns_end', array($this,'sort_icon'), 10,1);
	}

	function cal_header_btn( $A, $arg){
		$sortfilter = true;

		if( $this->cal->evcal_hide_sort == 'yes') return $A;
		if(isset($arg['hide_so']) && $arg['hide_so'] == 'yes') return $A;
		if(isset($arg['filters']) && $arg['filters'] == 'no') return $A;

		if(empty($this->cal->evopt1['evcal_filter_options'])) return $A;
		$A['evo-filter-btn'] = '';
		return $A;
	}

	// SORT EVENTS button
	function sort_icon($args){
		if( $this->cal->evcal_hide_sort == 'yes') return false;
		if(isset($args['hide_so']) && $args['hide_so'] == 'yes') return false;

		$sorting_options = (!empty($this->cal->evopt1['evcal_sort_options']))?$this->cal->evopt1['evcal_sort_options']:array();

		if( count ($sorting_options) <1) return false;
		
		echo "<span class='evo-sort-btn'>";
		$this->get_sort_content($args);
		echo "</span>";
	}

	// for header buttons
	function get_sort_content($args){	

		if( $this->cal->evcal_hide_sort != 'yes'){ // if sort bar is set to show

			$sorting_options = (!empty($this->cal->evopt1['evcal_sort_options']))?$this->cal->evopt1['evcal_sort_options']:array();

			// sorting section
			$evsa1 = array(
				'date'=>'Date',
				'title'=>'Title',
				'color'=>'Color',
				'posted'=>'Post Date'
			);
			$sort_options = array(	1=>'sort_date', 'sort_title','sort_color','sort_posted');
				$__sort_key = substr($args['sort_by'], 5);

			if(count($sorting_options)>0){
				echo "<div class='eventon_sort_line' style='display:none'>";

					$cnt =1;
					foreach($evsa1 as $so=>$sov){
						if(in_array($so, $sorting_options) || $so=='date' ){
						echo "<p data-val='sort_".$so."' data-type='".$so."' class='evs_btn ".( ($args['sort_by'] == $sort_options[$cnt])? 'evs_hide':null)."' >"
								.$this->cal->lang('evcal_lang_s'.$so,$sov)
								."</p>";
						}
						$cnt++;
					}
				echo "</div>";
			}
		}

	}


	// get post tags by ajde_events post type
		function get_terms_id_by_post_type( $taxonomy, $post_type ) {
		    global $wpdb;
		    $query = $wpdb->get_results( $wpdb->prepare( "SELECT t.*
		    	FROM $wpdb->terms AS t 
		    	INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id 
		    	INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id 
		    	INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id 
		    	WHERE p.post_type = %s  
		    	AND tt.taxonomy = %s
		    	GROUP BY t.term_id"
		    , $post_type, $taxonomy));

		   	//print_r($query);
		    return $query;
		}

	// HTML Calendar header filter and sort content
		public function get_content($args, $sortbar=true){

			//if(!$sortbar) return false;

			//if( $this->cal->evcal_hide_sort == 'yes') return false;

			// define variable values	
			$filtering_options = (!empty($this->cal->evopt1['evcal_filter_options']))?$this->cal->evopt1['evcal_filter_options']:array();
			$content='';

			if(count($filtering_options) == 0) return;

			$this->cal->reused(); // update reusable variables real quikc

			ob_start();

			// argument values
				$SO_display = (!empty($args['exp_so']) && $args['exp_so'] =='yes')? 'block': 'none';
				$filter_show_set_only = isset($args['filter_show_set_only']) && $args['filter_show_set_only'] == 'yes'? true:false;

			echo "<div class='eventon_sorting_section' style='display:{$SO_display}'>";

			$__text_all_ = $this->cal->lang('evcal_lang_all', 'All');
			
			// EACH EVENT TYPE
				$_filter_array = $this->cal->shell->get_all_event_tax();
				$_filter_array = apply_filters('eventon_so_filters', $_filter_array);


			// Filtering TYPE  /select / default
			$selectfilterType = (!empty($args['filter_type']) && $args['filter_type']=='select')? true: false;		


			echo "<div class='eventon_filter_line ".($selectfilterType?'selecttype':'')."'>";

				// For each taxonomy
				foreach($_filter_array as $ff=>$vv){ // vv = vv etc.

					if(!in_array($vv, $filtering_options)) continue;

					// past and future filtering
						if($ff == 'evpf'){
							$__filter_val = (!empty($args[$vv])? $args[$vv]: 'all');

							$filter_type_name = evo_lang('Past and Future Events');
							echo "<div class='eventon_filter evo_hideshow_pastfuture' data-filter_field='{$vv}' 
								data-filter_val='{$__filter_val}' data-filter_type='custom' >								
								<div class='eventon_filter_selection'>
									<p class='filtering_set_val' data-opts='evs4_in'>{$filter_type_name}</p>
									<div class='eventon_filter_dropdown evo_hideshow select_one' style='display:none'>";

									echo "<p class='". ($__filter_val =='all'? 'select ':'')."all' data-filter_val='all'>{$__text_all_}</p>";
									echo "<p class='past ". ( $__filter_val=='all' || $__filter_val=='past'? 'select':'')."' data-filter_val='past'>". evo_lang('Only Past Events') ."</p>";
									echo "<p class='future ". ( $__filter_val=='all'|| $__filter_val=='future'? 'select':'')."' data-filter_val='future'>". evo_lang('Only Future Events') ."</p>";
								echo "</div>
								</div><div class='clear'></div>
							</div>";
							continue;
						}

					// virtual event filtering
						if($ff == 'evvir'){
							$__filter_val = (!empty($args[$vv])? $args[$vv]: 'all');

							$filter_type_name = evo_lang('Virtual Events');
							echo "<div class='eventon_filter evo_hideshow_vir' data-filter_field='{$vv}' 
								data-filter_val='{$__filter_val}' data-filter_type='custom' >								
								<div class='eventon_filter_selection'>
									<p class='filtering_set_val' data-opts='evs4_in'>{$filter_type_name}</p>
									<div class='eventon_filter_dropdown evo_hideshow select_one' style='display:none'>";

									echo "<p class='". ($__filter_val =='all'? 'select ':'')."all' data-filter_val='all'>{$__text_all_}</p>";
									
									foreach(array(
										'vir'=>evo_lang('Virtual Events'),
										'nvir'=>evo_lang('Non Virtual Events'),
									) as $f=>$v){
										echo "<p class='{$f} ". ( $__filter_val=='all' || $__filter_val== $f ? 'select':'')."' data-filter_val='{$f}'>". evo_lang( $v ) ."</p>";
									}	
									
								echo "</div>
								</div><div class='clear'></div>
							</div>";
							continue;
						}

					// event status filtering
						if($ff == 'evst'){
							$__filter_val = (!empty($args[$vv])? $args[$vv]: 'all');

							$filter_type_name = evo_lang('Events Status');
							echo "<div class='eventon_filter evo_hideshow_st' data-filter_field='{$vv}' 
								data-filter_val='{$__filter_val}' data-filter_type='custom' >								
								<div class='eventon_filter_selection'>
									<p class='filtering_set_val' data-opts='evs4_in'>{$filter_type_name}</p>
									<div class='eventon_filter_dropdown evo_hideshow select_one' style='display:none'>";

									echo "<p class='". ($__filter_val =='all'? 'select ':'')."all' data-filter_val='all'>{$__text_all_}</p>";

									foreach( EVO()->cal->get_status_array('front') as $f=>$v){
										echo "<p class='{$f} ". ( $__filter_val=='all' || $__filter_val== $f ? 'select':'')."' data-filter_val='{$f}'>". evo_lang( $v ) ."</p>";
									}									
									
								echo "</div>
								</div><div class='clear'></div>
							</div>";
							continue;
						}

					// Event Tags filtering
						if($ff == 'evotag' ){
							/*
							$tags = get_terms(apply_filters('evo_get_frontend_filter_tags',
								array( 
									'taxonomy'=>'post_tag',
									'hide_empty'=> true,
									'parent'=>0
								)
							));
							*/

							$tags = $this->get_terms_id_by_post_type( 'post_tag','ajde_events');

							if(count($tags)>0):


							$__filter_val = (!empty($args[$vv])? $args[$vv]: 'all');
							$filtering_values = $__filter_val == 'all'? array(): explode(',', $__filter_val);

							// INSIDE
								$inside = '';
								// all event tags
									foreach($tags as $tag){

										// show only set filter values if set
										if($filter_show_set_only && !in_array($tag->term_id, $filtering_values) ) continue;
										$select = '';
										if($__filter_val == 'all' || in_array($tag->term_id, $filtering_values)) $select = 'select';

										$inside .= "<p class='". $select."' data-filter_val='{$tag->term_id}'>". $tag->name ."</p>";
									}

							// Empty inside
							if(empty($inside)) continue;

							echo "<div class='eventon_filter evo_hideshow_evotag' data-filter_field='event_tag' data-filter_val='{$__filter_val}' data-filter_type='tax' >								
								
								<div class='eventon_filter_selection'>
									<p class='filtering_set_val' data-opts='evs4_in'>". evo_lang('Event Tag'). "</p>
									<div class='eventon_filter_dropdown evo_hideshow' style='display:none'>";

									if(!$filter_show_set_only){
										echo "<p class='". ($__filter_val == 'all'? 'select':'')." all' data-filter_val='all'>{$__text_all_}</p>";
									}

									echo $inside;
									
								echo "</div>
								</div><div class='clear'></div>
							</div>";

							endif;

							continue;
						}

					// hook for other arguments
					$cats = get_terms( apply_filters('evo_get_frontend_filter_tax',
						array( 
							'taxonomy'=> $vv,
							'hide_empty'=> false,
						)
					));
			
					// filtering value filter is set to show
					if($cats ){

						$inside ='';
						$raw_filter_val = (!empty($args[$vv])? $args[$vv]: 'all');

						$FVALS = $this->process_filter_terms( $raw_filter_val );
						extract($FVALS);

						// If filter value is none skip it
							if( $_V == 'none') continue;

						// INSIDE drop down
						if(!$filter_show_set_only && $_FO != 'NOT'){
							$inside .=  "<p class='". ($_V == 'all'? 'select':'')." all' data-filter_val='all'>{$__text_all_}</p>";
						}

						// each taxonomy term
						foreach($cats as $ct){
							// show only set filter values if set
								if($filter_show_set_only && !in_array($ct->term_id, $_VA ) && $_FO != 'NOT') continue;

							// for NOT filters show everything else
								if($_FO == 'NOT' && in_array( $ct->term_id, $_VA)) continue;
							
							$select = ( in_array($ct->term_id, $_VA)  || $_V == 'all' || $_FO == 'NOT') ? 'select':'';

							// if term is parent level
							$par = $ct->parent == 0? true:false;
							
							$term_name = $this->cal->lang('evolang_'.$vv.'_'.$ct->term_id,$ct->name );
							
							if(!$selectfilterType){
								// event type 1 tax icon
								$icon_str = $this->cal->helper->get_tax_icon($vv,$ct->term_id, $this->cal->evopt1 );

								$inside .=  "<p class='{$select} ".$vv.'_'.$ct->term_id.' '.$ct->slug.' '. ($icon_str?'has_icon':''). ($par?'':' np'). "' data-filter_val='".$ct->term_id."' data-filter_slug='".$ct->slug."'>". $icon_str . $term_name."</p>";
							}else{// checkbox select option								
								$inside .=  "<p class='{$vv}_{$ct->term_id} {$select}' data-filter_val='".$ct->term_id."'>". $term_name."</p>";
							}
						}

						// if no values in the filter
						if(empty($inside)) continue;

						// only for event type taxonomies
						$_isthis_ett = (in_array($vv, $_filter_array))? true:false;
						$ett_count = ($ff==1)? '':$ff;

						// Language for the taxonomy name text
							$lang__ = ($_isthis_ett && isset($this->cal->lang_array['et'.$ett_count]))? 
								$this->cal->lang_array['et'.$ett_count]:
								(!empty($this->cal->lang_array[$ff])? $this->cal->lang_array[$ff]: 
									evo_lang(str_replace('_', ' ', $vv)) );

						echo "<div class='eventon_filter evo_sortOpt evo_sortList_{$vv}' data-filter_field='{$vv}' data-filter_val='{$raw_filter_val}' data-filter_type='tax' data-fl_o='{$_FO}'>
								<p class='filtering_set_val'>".$lang__."</p>
								<div class='eventon_filter_dropdown' style='display:none'>".$inside."</div>	
							<div class='clear'></div>
						</div>";					
						
					}
				}

				// for select filter type
				if($selectfilterType){
					echo "<p class='evo_filter_submit'>". $this->cal->lang('evcal_lang_apply_filters','Apply Filters')."</p>";
				}

				// (---) Hook for addon
				echo  do_action('eventon_sorting_filters', $content);

			echo "</div>"; // #eventon_filter_line	
			echo "<div class='clear'></div>"; // clear
			echo "</div>"; // #eventon_sorting_section

			return ob_get_clean();
		}

	// extract filters from shortcode values
		function get_filter_data(){
			$SC = $this->cal->shortcode_args;
			if(count($SC)==0) return false;
		}

	// process filter values
		function process_filter_terms($value){
			// value have NOT in it
			if(strpos($value, 'NOT-')!== false){
				if($value == 'NOT-all' || $value == 'NOT-ALL'){
					$filter_op='IN';
					$vals='none';
					$vals_array = array('none');
				}else{
					$op = explode('-', $value);
					$filter_op='NOT';
					$vals = str_replace('NOT-', '', $value);
					$vals_array = explode(',', $vals);
				}
			}else{
				$vals= $value;
				$filter_op = 'IN';
				$vals_array = explode(',', $vals);
			}
			return array('_V'=>$vals, '_VA'=>$vals_array, '_FO'=> $filter_op);
		}

	// Apply filters to calendar WP Query arguments
		public function apply_evo_filters_to_wp_argument($wp_arguments){
						
			$SC = $this->cal->shortcode_args;

			$wp_tax_query = $wp_meta_query = array();
			$meta_query_keys = array();
			$skip_query_keys = array('event_past_future', 'event_virtual','event_status');

			// get all available filters from shortcode
				$all_filters = $this->cal->shell->get_all_event_tax();

				foreach($all_filters as $slug=>$name){					
					if(empty($name)) continue;		
					if(in_array($name, $skip_query_keys)) continue;			
					if(!isset($SC[$name])) continue;

					$SC_val = $SC[$name];
					$SC_filter_val = apply_filters('eventon_event_type_value', $SC_val, $name, $SC);					

					if($SC_filter_val == 'all') continue;

					if(in_array($name, $meta_query_keys)){
						$wp_meta_query[] = array(
							'key'=> $name,
							'value'=>$SC_filter_val,
						);
					}else{

						$operator = 'IN';
						$terms = '';

						// NOT filter process
						if(strpos($SC_filter_val, 'NOT-')!== false){
							if($SC_filter_val == 'NOT-all' || $SC_filter_val == 'NOT-ALL'){
								$operator='NOT EXISTS';
								$terms = 'all';
							}else{
								$operator='NOT IN';
								$V = str_replace('NOT-', '', $SC_filter_val);
								$terms = explode(',', $V);
							}
						}else{
							$terms = explode(',', $SC_filter_val);
						}

						// add to tax query
						$wp_tax_query[] = array(
							'taxonomy'=> ($name == 'event_tag'? 'post_tag':$name),
							'field'=> 	apply_filters('eventon_filter_field_type', 'id',$name),
							'terms'=>	$terms,
							'operator'=>$operator,
						);
					}
				}	

			// Append to wp_query
				if(!empty($wp_tax_query)){
					
					$filter_relationship = isset($SC['filter_relationship'])? $SC['filter_relationship']: 'AND';
					$wp_tax_query['relation']= $filter_relationship;

					$filters_tax_wp_argument = array('tax_query'=>$wp_tax_query);					
					$wp_arguments = array_merge($wp_arguments, $filters_tax_wp_argument);
				}
				if(!empty($wp_meta_query)){
					$filters_meta_wp_argument = array(	'meta_query'=>$wp_meta_query	);
					$wp_arguments = array_merge($wp_arguments, $filters_meta_wp_argument);
				}

			//print_r($wp_arguments);

			/*
			// values from filtering events
			if($filters!=false && is_array($filters) ){

				// build out the proper format for filtering with WP_Query
				$cnt =0;

				if( sizeof($filters) > 1){
					$filter_logic = isset($this->cal->shortcode_args['filter_logic'])? $this->cal->shortcode_args['filter_logic']: 'AND';
					$filter_tax['relation']= $filter_logic;
				} 

				foreach($filters as $filter){
					if(empty($filter['filter_type'])) continue;
					if(empty($filter['filter_val'])) continue;
					if($filter['filter_type'] == 'custom') continue;

					if($filter['filter_type']=='tax'){

						$filter_val = explode(',', $filter['filter_val']);

						if(empty($filter_val)) continue;

						$filter_field_type = apply_filters('eventon_filter_field_type', 'id', $filter['filter_name']);
						$filter_tax[] = array(
							'taxonomy'=>$filter['filter_name'],
							'field'=> $filter_field_type,
							'terms'=>$filter_val,
							'operator'=>(!empty($filter['filter_op'])? $filter['filter_op']: 'IN'),
						);
						$cnt++;
					}else{
						$filter_meta[] = array(
							'key'=>$filter['filter_name'],
							'value'=>$filter['filter_val'],
						);
					}
				}

				//print_r($filter_tax);


				if(!empty($filter_tax)){
					$filters_tax_wp_argument = array('tax_query'=>$filter_tax);					
					$wp_arguments = array_merge($wp_arguments, $filters_tax_wp_argument);
				}
				if(!empty($filter_meta)){
					$filters_meta_wp_argument = array(	'meta_query'=>$filter_meta	);
					$wp_arguments = array_merge($wp_arguments, $filters_meta_wp_argument);
				}
			}else{

				// For each event type category + location and organizer tax
				foreach($this->cal->shell->get_all_event_tax() as $ety=>$event_type){
					// if the ett is  not empty and not equal to all
					if(!empty($ecv[$event_type]) && $ecv[$event_type] !='all'){
						$ev_type = explode(',', $ecv[$event_type]);
						$ev_type_ar = array(
								'tax_query'=>array(
								array('taxonomy'=>$event_type,
									'field'=>'id',
									'terms'=>$ev_type,
								) )
							);
						$wp_arguments = array_merge($wp_arguments, $ev_type_ar);
					}
				}
			}
			*/

			return $wp_arguments;
		}

	// APPLY filters to event List
		function apply_filters_to_event_list($event_list, $filter_type='all'){
			$SC = $this->cal->shortcode_args;

			if(!is_array($event_list)) return $event_list;

			// past future event filter			
			if( ($filter_type =='all' || $filter_type=='past_future') && isset($SC['event_past_future']) && $SC['event_past_future'] != 'all'){
				$new_event_list = array();
				if($SC['event_past_future'] == 'past'){							
					foreach($event_list as $event){
						if(isset($event['event_past']) && $event['event_past'] == 'yes') $new_event_list[] = $event;
					}
				}
				if($SC['event_past_future'] == 'future'){
					foreach($event_list as $event){
						if(isset($event['event_past']) && $event['event_past'] == 'no') $new_event_list[] = $event;
					}
				}
				$event_list = $new_event_list;
			}

			// pagination filter
			if( $filter_type =='all' || $filter_type=='pagination'){
				if($SC['show_limit_paged']>0 && 
					$SC['show_limit_ajax']=='yes' && 
					$SC['event_count']>0
				){
					$increment = $SC['event_count'];
					$paged = (int)$SC['show_limit_paged'];
					$bottom = (($paged-1)*$increment);
					$top = ($paged * $increment) ;
					$event_count = count($event_list);

					$index =1;
					foreach($event_list as $id=>$event){
						//echo "$index > $top && < $bottom -{$event['event_id']}<br/>";
						if($index <= $top && $index > $bottom){
						}else{
							unset($event_list[$id]);
						}
						$index++;
					}
				}
			}

			// event count filter
			if( $filter_type=='event_count' || $filter_type =='all' ){
				
				// make sure event count is only run for one month
				if(isset($SC['number_of_months']) && $SC['number_of_months'] >1) return $event_list;
				
				if(isset($SC['event_count']) && $SC['event_count'] >0){
					// if show limit then show all events but css hide
					if(!empty($SC['show_limit']) && $SC['show_limit']=='yes'){
						$lesser_of_count = count($event_list);
					}else{
						// make sure we take lesser value of count
						$lesser_of_count = (count($event_list)<$SC['event_count'])?
							count($event_list): $SC['event_count'];
					}

					// for each event until count
					$index =1;
					foreach($event_list as $id=>$event){
						if($index > $lesser_of_count){						
							unset($event_list[$id]);
						}
						$index++;
					}					
				}
			}

			// event 

			//print_r($event_list);

			return $event_list;
		}

	// pre filter featured events top and month/year long events top
		function move_important_events_up( $EL){
			$EL = $this->move_ft_to_top( $EL);
			$EL = $this->move_ml_yl_to_top( $EL);
			return $EL;
		}

	// process events list for no events or load more
		function no_more_events_add( $EL){
			$SC = $this->cal->shortcode_args;
			$content_li='';


			// if there are events in the list array
			if( is_array($EL) && count($EL)>0){

				// print all the events
				foreach($EL as $event)	$content_li.= $event['content'];

				// load more events button
				if( isset($SC['show_limit']) && $SC['show_limit']=='yes' && 
					((count($EL)> $SC['event_count'] && $SC['show_limit_ajax']=='no' ) || ($SC['show_limit_ajax'] =='yes')
					) ){
					$content_li.= '<div class="evoShow_more_events" style="'.( $SC['tile_height']!=0? 'height:'.$SC['tile_height'].'px':'' ).'"><span>'.$this->cal->lang_array['evsme'].'</span></div>';
				}
			}else{
				if( ($SC['sep_month'] == 'yes' && $SC['number_of_months']>1 )|| $SC['number_of_months'] ==1 )
					$content_li = "<div class='eventon_list_event no_events'><p class='no_events' >".$this->cal->lang_array['no_event']."</p></div>";
			}

			return $content_li;
		}

	// Other secondary filtering
		function move_ft_to_top($eventlist){
			$args = $this->cal->shortcode_args;
			if($args['ft_event_priority']=='yes' ){

				$ft_events = $events = array();
				foreach($eventlist as $event){

					$featured = (isset($event['event_pmv']['_featured']) && $event['event_pmv']['_featured'][0]=='yes')? true:false;

					if($featured){
						$ft_events[]=$event;
					}else{
						$events[]=$event;
					}
				}

				// move featured events to top
				return array_merge($ft_events,$events);
			}
			return $eventlist;
		}
		function move_ml_yl_to_top($eventlist){
			$args = $this->cal->shortcode_args;
			if(isset($args['ml_priority']) && $args['ml_priority']=='yes' ){

				$ml_events = $events = array();

				foreach($eventlist as $event){
					if(isset($event['event_pmv']['_evo_month_long']) && isset($event['event_pmv']['_evo_month_long'][0]) && $event['event_pmv']['_evo_month_long'][0]=='yes' ){
						$ml_events[]=$event;
					}else{
						$events[]=$event;
					}
				}
				// move featured events to top
				return array_merge($ml_events,$events);
			}

			if(isset($args['yl_priority']) && $args['yl_priority']=='yes' ){

				$yl_events = $events = array();
				foreach($eventlist as $event){

					if(isset($event['event_pmv']['evo_year_long']) && isset($event['event_pmv']['evo_year_long'][0]) && $event['event_pmv']['evo_year_long'][0]=='yes' ){
						$yl_events[]=$event;
					}else{
						$events[]=$event;
					}
				}

				// move featured events to top
				return array_merge($yl_events,$events);
			}
			return $eventlist;
		}
}