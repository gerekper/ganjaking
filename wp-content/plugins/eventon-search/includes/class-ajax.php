<?php
/**
 * Search AJAX
 */
class EVOSR_ajax{
	public function __construct(){
		$ajax_events = array(
			'search_evo_events'=>'search_evo_events',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}
	function search_evo_events(){
		$searchfor = $_POST['search'];
		$shortcode = $_POST['shortcode'];

		global $eventon;

		$current_timestamp = current_time('timestamp');

		// restrained time unix
			$number_of_months = !empty($shortcode['number_of_months'])? $shortcode['number_of_months']:12;
			$month_dif = '+';
			$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

			$restrain_monthN = ($number_of_months>0)?				
				date('n',  $unix_dif):
				date('n',$current_timestamp);

			$restrain_year = ($number_of_months>0)?				
				date('Y', $unix_dif):
				date('Y',$current_timestamp);			

		// upcoming events list 
			$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));
			$__focus_start_date_range = $current_timestamp;
			$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));

		// Add extra arguments to shortcode arguments
			$new_arguments = array(
				'focus_start_date_range'=>$__focus_start_date_range,
				'focus_end_date_range'=>$__focus_end_date_range,
				's'=>$searchfor
			);

			$args = (!empty($args) && is_array($args))? 
				wp_parse_args($new_arguments, $args): $new_arguments;

			// merge passed shortcode values
				if(!empty($shortcode))
					$args= wp_parse_args($shortcode, $args);

			$args__ = $eventon->evo_generator->process_arguments($args);
			$this->shortcode_args=$args__;

			$content =$eventon->evo_generator->calendar_shell_header(
				array(
					'month'=>$restrain_monthN,
					'year'=>$restrain_year, 
					'date_header'=>false,
					'sort_bar'=>false,
					'date_range_start'=>$__focus_start_date_range,
					'date_range_end'=>$__focus_end_date_range,
					'title'=>'',
					'send_unix'=>true
				)
			);

			$content .=$eventon->evo_generator->eventon_generate_events($args__);
			
			$content .=$eventon->evo_generator->calendar_shell_footer();
			
			echo json_encode(array('content'=>$content));
			exit;

	}
}
new EVOSR_ajax();