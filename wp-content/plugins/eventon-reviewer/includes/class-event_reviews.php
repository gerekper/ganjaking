<?php
/**
 * Event Reviews Class
 * @version 0.1
 */

class EVORE_Reviews {

	public $v = 3;

	public function __construct($EVENT, $RI = 0){
		
		if(!$EVENT) return;
		$this->event = is_numeric($EVENT) ? new EVO_Event( $EVENT, '', $RI) : $EVENT;
				
		$this->ri = $this->event->ri;
	}


	function get_all_reviews(){
		$arg = array(
			'posts_per_page'=>-1,
			'post_type' => 'evo-review',
			'meta_query'=> array(
				array('key'=>'e_id','value'=>$this->event->ID)
			)
		);

		if(!empty($wp_arg)) $arg = array_merge($arg,$wp_arg);

		$reviews = new WP_Query($arg);


		if($reviews->have_posts() && $reviews->found_posts > 0){
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

	function get_calculated_average_rating($decimal = 2){
		$_event_reviews = $this->event->get_prop('_event_reviews');

		if(!$_event_reviews) return false;
		if(!is_array($_event_reviews) ) return false;

		if( !isset( $_event_reviews[ $this->ri ]) ) return false;
		if( !isset( $_event_reviews[ $this->ri ]['average']) ) return false;

		return number_format( $_event_reviews[ $this->ri ]['average'] , $decimal,'.','');
	}
	function get_average_all_ratings(){
		$_event_reviews = $this->event->get_prop('_event_reviews');

		if(!$_event_reviews) return false;
		if(!is_array($_event_reviews) ) return false;

		$sum = $count = 0;
		foreach($_event_reviews as $ri){
			$_count = array_sum($ri['rates']);
			$sum += $ri['average']*$_count;
			$count += $_count;
		}

		return number_format( ($sum/$count), 2,'.','');
	}
	function get_specific_ratings_count(){
		$_event_reviews = $this->event->get_prop('_event_reviews');
		if( !isset( $_event_reviews[$this->ri])) return false;
		if( !isset( $_event_reviews[$this->ri]['rates'])) return 0;
		if( !is_array( $_event_reviews[$this->ri]['rates'])) return 0;
		return array_sum( $_event_reviews[$this->ri]['rates'] );
	}
	function get_rating_all_count(){
		$_event_reviews = $this->event->get_prop('_event_reviews');

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

// ADMIN
	function get_admin_reviews_stat_html(){

		ob_start();
		?>
		<p><?php _e('Overall average rating for this event','eventon');?><?php echo EVO()->throw_guide("The rating information is for all repeating instances of this event (if has repeating instances)", '',false)?></p>

		<div class='evore_star_data'>
			<span class='evore_stars'>
			<?php
				//$average = EVORE()->frontend->functions->get_average_rating($post->ID, $pmv);
				//$rating_count = EVORE()->frontend->functions->get_rating_count($post->ID, $pmv);

				$ALLaverage = $this->get_average_all_ratings(  );
				$ALLrating_count = $this->get_rating_all_count(  );
				
				echo EVORE()->frontend->functions->get_star_rating_html( $ALLaverage);

			?>
			</span>
			<em class='rating_data'><?php echo $ALLaverage? $ALLaverage:'0.0';?>/5.0 (<?php echo EVORE()->frontend->functions->get_rating_percentage($ALLaverage);  ?>%)</em>
			<em class='rating_data'><?php echo $ALLrating_count ? $ALLrating_count:'0';?> <?php _e('Ratings','eventon');?></em>
		</div>
		<p id="evore_message" style='display:none' data-t1='<?php _e('Loading..','eventon');?>' data-t2='<?php _e('Count not sync ratings at this moment, please try later','eventon');?>'></p>
		<?php 

		return ob_get_clean();
	}
}