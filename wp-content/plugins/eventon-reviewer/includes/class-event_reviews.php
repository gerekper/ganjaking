<?php
/**
 * Event Reviews Class
 * @version 0.1
 */

class EVORE_Reviews extends EVO_Event{

	public function __construct($event_id, $RI = 0){
		parent::__construct($event_id);
		$this->ri = $RI;
	}

	function get_all_reviews(){
		$arg = array(
			'posts_per_page'=>-1,
			'post_type' => 'evo-review',
			'meta_query'=> array(
				array('key'=>'e_id','value'=>$this->ID)
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

		echo 'tt';


		return false;

	}
}