<?php
/**
 * Functions for single events 
 * @version  1.1.3
 */
class evose_functions{

	// single event page 
		function repeat_event_header($ri, $eventid){
			
			$ev_vals = get_post_meta($eventid);

			if( empty($ev_vals['evcal_repeat']) || (!empty($ev_vals['evcal_repeat']) && $ev_vals['evcal_repeat'][0]=='no') ) return false;

			$repeat_intervals = (!empty($ev_vals['repeat_intervals']))? 
							(is_serialized($ev_vals['repeat_intervals'][0])? unserialize($ev_vals['repeat_intervals'][0]): $ev_vals['repeat_intervals'][0] ) :false;		

			// if there are no repeat intervals or only one interval
			if($repeat_intervals && !is_array($repeat_intervals) && (is_array($repeat_intervals) && count($repeat_intervals)==1)) return false;

			$repeat_count = (count($repeat_intervals)-1)   ;
			$date = new evo_datetime();

			$event_permalink = get_permalink($eventid);
			
			echo "<div class='evose_repeat_header'><p><span class='title'>".evo_lang('This is a repeating event'). "</span>";
			echo "<span class='ri_nav'>";

			// previous link
			if($ri>0){ 
				$prev = $date->get_correct_formatted_event_repeat_time($ev_vals, ($ri-1));
				// /print_r($prev);
				$prev_link = $this->get_repeat_event_url($event_permalink, ($ri-1) );
				echo "<a href='{$prev_link}' class='prev' title='{$prev['start_']}'><b class='fa fa-angle-left'></b><em>{$prev['start_']}</em></a>";
			}

			// next link 
			if($ri<$repeat_count){
				$next = $date->get_correct_formatted_event_repeat_time($ev_vals, ($ri+1));
				//print_r($next); 
				$next_link = $this->get_repeat_event_url($event_permalink, ($ri+1) );
				echo "<a href='{$next_link}' class='next' title='{$next['start_']}'><em>{$next['start_']}</em><b class='fa fa-angle-right'></b></a>";
			}
			
			echo "</span><span class='clear'></span></p></div>";
		}

		function get_repeat_event_url($permalink, $ri){
			if(strpos($permalink, '?')!== false){ // ? exists
				return $permalink. '&ri='.$ri;
			}else{
				return $permalink. '?ri='.$ri;
			}
		}

	// print the redirect script for events with hashtag based repeat interval pages
		function redirect_script(){
			ob_start();
			?>
			<script> 
				href = window.location.href;
				var cleanurl = href.split('#');
				hash =  window.location.hash.substr(1);
				hash_ri = hash.split('=');

				if(hash_ri[1]){
					if(href.indexOf('?') >0){
						redirect = cleanurl[0]+'&ri='+hash_ri[1];
					}else{
						redirect = cleanurl[0]+'?ri='+hash_ri[1];
					}
					window.location.replace( redirect );
				}
			</script>
			<?php

			echo ob_get_clean();
		}
}