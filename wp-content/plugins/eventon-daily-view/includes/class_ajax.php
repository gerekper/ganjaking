<?php
/**
 * AJAX for EventON DV
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	dailyview/Functions/AJAX
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVODV_ajax{
	public function __construct(){	
		add_filter('eventon_ajax_arguments',array($this,'main_ajax_arg_adds'), 10, 2);
	}

	// alter calendar SC
	// Before generating events via ajax
		function main_ajax_arg_adds($SC){
			
			extract($SC);

			if($SC['calendar_type'] == 'daily' && !empty($fixed_day) ){

				// switching month in oneday style
				$direction = $_POST['direction'];
				$switch_type = 'month';


				// if derection present = chaning months
				if($direction != 'none' && !empty($fixed_month) && !empty($fixed_year) ){
					// month and year already adjusted
					$number_days_in_month = EVODV()->frontend->days_in_month( $fixed_month, $fixed_year);
					
					if( $dv_scroll_style =='continuous'){

						if( $fixed_day > $number_days_in_month){
							$fixed_day = $number_days_in_month;
						}

						// if switching month to previous -> go to end of prev month
						if( $switch_type == 'month' ){
							if($direction == 'prev')	$fixed_day = $number_days_in_month;
							if($direction == 'next')	$fixed_day = 1;
						}

						// last day of prev month, when clicked from ay strip arrows
						if( $direction == 'prev' && $fixed_day == 1){
							$fixed_day = $number_days_in_month;
						}

					}elseif( $dv_scroll_style == 'firstday'){
						$fixed_day = 1;

					}elseif( $dv_scroll_style == 'lastday'){
						$fixed_day = $number_days_in_month;
						
					// legacy
					}else{
						if($mo1st == 'yes'){
							$fixed_day = 1;
						}else{
							if( $fixed_day > $number_days_in_month){
								$fixed_day = $number_days_in_month;
							}

							if( !empty($dv_scroll_type) && $dv_scroll_type == 'arrow') $switch_type =  'arrow';

							// if switching month to previous -> go to end of prev month
							if( $switch_type == 'month' && $direction == 'prev') $fixed_day = $number_days_in_month;

							// last day of prev month, when clicked from ay strip arrows
							if( $direction == 'prev' && $fixed_day == 1){
								$fixed_day = $number_days_in_month;
							} 
						}	
					}


					

				}

				

				$DD = new DateTime();
				$DD->setTimezone( EVO()->calendar->timezone0 );
				$DD->setDate($fixed_year,$fixed_month, $fixed_day );
				$DD->setTime(0,0,0);

				$SC['fixed_day'] = $fixed_day;

				// ONE DAY
				if($dv_view_style == 'oneday'){
					$SC['focus_start_date_range'] = $DD->format('U');
					$DD->modify('+1 day');
					$SC['focus_end_date_range'] = $DD->format('U');	
				}
				
			}
			return $SC;
		}
}
new EVODV_ajax();
?>