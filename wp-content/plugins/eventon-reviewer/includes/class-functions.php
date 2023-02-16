<?php
/**
 *  frontend supporting functions
 */
class evo_re_functions{
	private $UMV = 'eventon_review_user';

	// add a new rating to the calculations and update event information with new rating
		function add_new_rating($rating, $event_id, $ri=0){
			$event_ratings = get_post_meta($event_id, '_event_reviews', true);

			if(!empty($event_ratings) && isset( $event_ratings[$ri]) ){
				$rates = $this->get_updated_rates_array($rating, $event_ratings[$ri]['rates']);
				$average = $this->get_update_average_rating($rates, $rating);
				
				$event_ratings[$ri] = array(
					'average'=>$average,
					'rates'=>$rates
				);
			}else{	
				$event_ratings = array();		
				$rates = $this->get_updated_rates_array($rating);
				$average = $this->get_update_average_rating($rates, $rating);
				$event_ratings[$ri] = array(
					'average'=>$average,
					'rates'=>$rates
				);
			}
			update_post_meta($event_id,'_event_reviews', $event_ratings);
		}

	// sync ratings for an event for all repeat intervals
		function sync_ratings($event_id, $ri=0){
			$reviews = new WP_Query(array(
				'post_type'=>'evo-review',
				'post_status'=>'publish',
				'posts_per_page'=>-1,
				'meta_query' => array(array('key'  => 'e_id','value'=> $event_id)),
			));

			$event_ratings = array();

			if($reviews->have_posts()){				
				while($reviews->have_posts()): $reviews->the_post();
					$rpmv = get_post_custom($reviews->post->ID);
					$ri = !empty($rpmv['repeat_interval'])? $rpmv['repeat_interval'][0]:0;
					$rating = !empty($rpmv['rating'])? $rpmv['rating'][0]:1;

					//echo $rating;

					if(!empty($event_ratings[$ri])){
						$rates = $this->get_updated_rates_array($rating, $event_ratings[$ri]['rates']);					
						$event_ratings[$ri]= array(
							'rates'=>$rates,
							'average'=>$this->get_update_average_rating($rates, $rating)
						);
					}else{
						$rates = $this->get_updated_rates_array($rating);	
						$event_ratings[$ri]= array(
							'rates'=>$rates,
							'average'=>$this->get_update_average_rating($rates, $rating)
						);
					}	
				endwhile;
				//print_r($event_ratings);
				update_post_meta($event_id, '_event_reviews', $event_ratings);
			}
			wp_reset_postdata();
		}

	// QUERY REVIEWS
		private function get_reviews($wp_arg = ''){

			$arg = array(
				'posts_per_page'=>-1,
				'post_type' => 'evo-review',
			);

			if(!empty($wp_arg)) $arg = array_merge($arg,$wp_arg);

			$reviews = new WP_Query($arg);

			if($reviews->have_posts() && $reviews->found_posts > 0) return $reviews;

			return false;

		}

	// GET ALL REVIEWS
		function get_all_reviews(){
			$reviews =  $this->get_reviews( );

			if($reviews){
				$output = array();
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');

				while($reviews->have_posts()): $reviews->the_post();
					$rpmv = get_post_custom($reviews->post->ID);
					
					$output[] = array(
						'rating'=> (!empty($rpmv['rating'])? $rpmv['rating'][0]:1),
						'review'=> (!empty($rpmv['review'])? $rpmv['review'][0]:''),
						'reviewer'=> (!empty($rpmv['name'])? $rpmv['name'][0]:''),
						'date'=> get_the_date($date_format.' '.$time_format, $reviews->post->ID)
					);
				endwhile;
				wp_reset_postdata();
				return $output;
			}

			return false;
		}

	
	// CHEKS
		// CHECK if user reviewed already
			function has_user_reviewed($post){
				$reviews = $this->get_reviews(
					array(
						'meta_query' => array(
							array('key' => 'email','value' => $post['email']),
							array('key' => 'e_id','value' => $post['e_id']),
							array('key' => 'repeat_interval','value' => $post['repeat_interval']),
						)
					)
				);

				return ($reviews)? true: false;
			}
			function has_loggedin_user_reviewed(){
				$reviewed = $this->get_reviews(
					array(
						'meta_query' => array(
							array('key' => 'e_id','value' => $post['e_id']),
							array('key' => 'repeat_interval','value' => $post['repeat_interval']),
							array('key' => 'userid','value' => $post['uid']),
						),
					)
				);
				
				$review = false;
				if($reviewed){
					while($reviewed->have_posts()): $reviewed->the_post();
						$review = get_post_meta($reviewed->post->ID, 'rating',true);
					endwhile;
				}
				wp_reset_postdata();

				return $review;
			}

	// returns
		function get_update_average_rating($rates, $rating){
			$count = 0;
			$total = 0;

			foreach($rates as $rate=>$times){
								
				$total += $rate* (int)$times;
				$count += $times;
				// echo '<br/>'.$rate.'x'.$times.'cnt:'.$count.' '.($rate* (int)$times).' '.$total.' '.($total/$count).'<br/>';	
			}

			$average = $total/$count;
			return ($average>5)?5:$average;
		}
		function get_updated_rates_array($rate, $array=''){
			if(!empty($array)){
				$new_array = array();
				foreach($array as $key=>$val){
					if($key==$rate){
						$new_array[$key]= $val+1;
					}else{
						$new_array[$key]= $val;
					}
				}
			}else{
				$new_array = array(
					1=>0, 2=>0,3=>0,4=>0, 5=>0				
				);
				$new_array[$rate]= 1;
			}
			return $new_array;
		}
		// return average rating for the event
		function get_average_rating($event_id, $event_pmv='', $ri=0){
			$event_pmv = !empty($event_pmv)? $event_pmv: get_post_custom($event_id);

			$_event_reviews = !empty($event_pmv['_event_reviews'])? unserialize($event_pmv['_event_reviews'][0]):false;
			$ri = empty($ri)? 0: $ri;

			return ($_event_reviews && isset($_event_reviews[$ri]) && isset($_event_reviews[$ri]['average']))? 
				number_format($_event_reviews[$ri]['average'],2,'.',''): false;
		}

		// return average rating for all instances of an event
		function get_average_all_ratings($event_id, $event_pmv=''){
			$event_pmv = !empty($event_pmv)? $event_pmv: get_post_custom($event_id);
			$_event_reviews = !empty($event_pmv['_event_reviews'])? unserialize($event_pmv['_event_reviews'][0]):false;

			if(!$_event_reviews) return false;

			$sum = $count = 0;
			foreach($_event_reviews as $ri){
				$_count = array_sum($ri['rates']);
				$sum += $ri['average']*$_count;
				$count += $_count;
			}

			return number_format( ($sum/$count), 2,'.','');
		}

		function get_rating_percentage($average='',$event_id='', $event_pmv=''){
			$average = !empty($average)? $average: $this->get_average_rating($event_id, $event_pmv);
			
			return (($average/5)*100);
		}	
		// COUNT
		function get_rating_count($event_id, $event_pmv='', $ri=0){
			$event_pmv = !empty($event_pmv)? $event_pmv: get_post_custom($event_id);
			$_event_reviews = !empty($event_pmv['_event_reviews'])? unserialize($event_pmv['_event_reviews'][0]):false;

			if(!isset($_event_reviews[$ri])) return false;

			$count = (is_array($_event_reviews[$ri]['rates'])? array_sum($_event_reviews[$ri]['rates']):0);
			return $count;
		}
		function get_rating_all_count($event_id, $event_pmv=''){
			$event_pmv = !empty($event_pmv)? $event_pmv: get_post_custom($event_id);
			$_event_reviews = (!empty($event_pmv['_event_reviews']))? unserialize($event_pmv['_event_reviews'][0]):false;

			if(!$_event_reviews || !is_array($_event_reviews) || (is_array($_event_reviews) && count($_event_reviews)==0) ){ 
				return false;
			}else{
				$count = 0;		
				foreach($_event_reviews as $ri){
					$count += array_sum($ri['rates']);
				}
				return $count;
			}
		}

		function get_rating_ind_counts($event_id, $ri=0, $event_pmv){
			$event_pmv = !empty($event_pmv)? $event_pmv: get_post_custom($event_id);
			$_event_reviews = !empty($event_pmv['_event_reviews'])? unserialize($event_pmv['_event_reviews'][0]):false;

			if(!isset($_event_reviews[$ri])) return false;

			return (is_array($_event_reviews[$ri]['rates'])? $_event_reviews[$ri]['rates']:false);			
		}

		// return all reviews for an event
		function get_all_reviews_for_event($event_id, $ri=0, $reviews_text=false){
			$ri = ($ri=='all')?'':$ri; // repeat interval value for all events vs ind
			
			$meta_query[] = array('key'  => 'e_id','value'=> $event_id);
			if($ri!='all' && !empty($ri)){
				$meta_query[] = array('key'  => 'repeat_interval','value'=> $ri);
			}

			$reviews = $this->get_reviews(
				array(
					'meta_key'=>'rating','orderby'=>'meta_value_num','order'=>'DESC',
					'meta_query' => $meta_query,
				)
			);


			$event_reviews = array();

			if($reviews){
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				while($reviews->have_posts()): $reviews->the_post();
					$rpmv = get_post_custom($reviews->post->ID);

					if( (empty($rpmv['review']) && !$reviews_text) ) continue;
					
					$event_reviews[] = array(
						'rating'=> (!empty($rpmv['rating'])? $rpmv['rating'][0]:1),
						'review'=> (!empty($rpmv['review'])? $rpmv['review'][0]:''),
						'reviewer'=> (!empty($rpmv['name'])? $rpmv['name'][0]:''),
						'date'=>get_the_date($date_format.' '.$time_format, $reviews->post->ID)
					);
				endwhile;
			}
			wp_reset_postdata();

			return $event_reviews;
		}

	// get star rating html
		function get_star_rating_html($value){
			$lowVal = floor($value);
			$fraction = $value - $lowVal;

			$count = 1;
			$remain = ($value != 5 && $fraction==0)? 5- $value: 
				( $fraction!= 0? 4- $lowVal: 0); 

			$output = '';
			for($x=1; $x<=$lowVal; $x++){
				$output .= "<span class='fa fa-star' data-value='{$count}'></span>";
				$count++;
			}
			if($fraction != 0){
				$output .= "<span class='fa fa-star-half' data-value='{$count}'></span>";
				$count++;
			}

			for($y=1; $y<= $remain; $y++){
				$output .= "<span class='far fa-star' data-value='{$count}'></span>";
				$count++;
			}
			return $output;
		}

// Supporting
	// get IP address of user
		function get_client_ip() {
		    $ipaddress = '';
		    if ($_SERVER['HTTP_CLIENT_IP'])
		        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
		        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    else if($_SERVER['HTTP_X_FORWARDED'])
		        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		    else if($_SERVER['HTTP_FORWARDED_FOR'])
		        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		    else if($_SERVER['HTTP_FORWARDED'])
		        $ipaddress = $_SERVER['HTTP_FORWARDED'];
		    else if($_SERVER['REMOTE_ADDR'])
		        $ipaddress = $_SERVER['REMOTE_ADDR'];
		    else
		        $ipaddress = false;
		    return $ipaddress;
		}
		function get_current_userid(){
			if(is_user_logged_in()){
				global $current_user;
				wp_get_current_user();
				return $current_user->ID;
			}else{
				return false;
			}
		}
		
}