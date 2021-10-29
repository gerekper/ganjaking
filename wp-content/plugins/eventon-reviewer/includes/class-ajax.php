<?php
/**
 * Reviewer Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-RE/Functions/AJAX
 * @version     0.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_re_ajax{
	public function __construct(){
		$ajax_events = array(
			'the_ajax_evore'=>'evore_save_review',
			'the_ajax_evore2'=>'get_reviews',
			'the_ajax_evore3'=>'sync_ratings',
			'evorev_get_form'=>'evorev_get_form',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	// get reviwer form
		function evorev_get_form(){
			ob_start();
			$args = $_POST;
			
			include_once('html_form.php');

			echo json_encode(array(
				'status'=>'good',
				'content'=>ob_get_clean()
			));
			exit;
		}

	// save new review
		function evore_save_review(){
			$nonce = $_POST['postnonce'];
			$status = 0;
			$message = $save = '';

			if(! wp_verify_nonce( $nonce, 'evore_nonce' ) ){
				$status = 1;	$message ='Invalid Nonce';				
			}else{
				// sanitize each posted values
				foreach($_POST as $key=>$val){
					$post[$key]= sanitize_text_field(urldecode($val));
				}

				// check if already submit a rating
				if(EVORE()->frontend->functions->has_user_reviewed($post) ){					
					$message = EVORE()->frontend->get_form_message('err8', $post['lang']);
					$status = 8;
				}else{
					$save= EVORE()->frontend->_form_save_review($post);
					$status = 0;
				}							
			}

			$return_content = array(
				'message'=> $message,
				'status'=>$status
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// View All Reviews for the event
		function get_reviews(){
			$status = 0;
			ob_start();

				$event_id = (int)$_POST['e_id'];
				$ri = (!empty($_POST['ri']) || (!empty($_POST['ri']) && $_POST['ri']==0 ))? $_POST['ri']:'all'; // repeat interval

				$event_reviews = new EVORE_Reviews($event_id, $ri);
				
				$REVIEWS_LIST = $event_reviews->get_all_reviews();

				echo "<div class='evore_list'>";
				if(!empty($REVIEWS_LIST) && count($REVIEWS_LIST)>0){
					$count = 1;
					foreach($REVIEWS_LIST as $review){
						//print_r($review);
						?>
						<p class='review' style='padding-top:10px;'>
						<span class='rating'><?php echo EVORE()->frontend->functions->get_star_rating_html($review['rating']);?></span>
						<?php if(!empty($review['review'])):?><span class='description'><?php echo $review['review'];?></span><?php endif;?>
						<span class='reviewer'><?php echo !empty($review['reviewer'])? $review['reviewer']:'';?> @ <?php echo $review['date'];?></span>
						</p>
						<?php
					}
				}else{
					echo "<p style='padding-top:10px;'>".__('No Reviews found.','eventon')."</p>";
				}
				echo "</div>";

			$output = ob_get_clean();

			$return_content = array(
				'content'=> $output,
				'status'=>$status
			);
			
			echo json_encode($return_content);		
			exit;
		}	

	// Sync review ratings and reviews
		function sync_ratings(){
			
			$status = 0;
			$event_id = $_POST['e_id'];			

			EVORE()->frontend->functions->sync_ratings($event_id);

			// get new updated event post data
			$pmv = get_post_custom($event_id);

			ob_start();

			?>
				<p><?php _e('Overall average rating for this event','eventon');?><?php echo EVO()->throw_guide("The rating information is for all repeating instances of this event (if has repeating instances)", '',false)?></p>
				<?php
					$ALLaverage = EVORE()->frontend->functions->get_average_all_ratings($event_id, $pmv);
					$ALLrating_count = EVORE()->frontend->functions->get_rating_all_count($event_id, $pmv);
					
					echo EVORE()->frontend->functions->get_star_rating_html( $ALLaverage);
				?>
				<em class='rating_data'><?php echo $ALLaverage? $ALLaverage:'0.0';?>/5.0 (<?php echo EVORE()->frontend->functions->get_rating_percentage($ALLaverage);  ?>%)</em>
				<em class='rating_data'><?php echo $ALLrating_count?$ALLrating_count:'0';?> <?php _e('Ratings','eventon');?></em>
				<p id="evore_message" style='display:none' data-t1='<?php _e('Loading..','eventon');?>' data-t2='<?php _e('Count not sync ratings at this moment, please try later','eventon');?>'></p>

			<?php

			$output = ob_get_clean();

			$return_content = array(
				'content'=> $output,
				'status'=>$status
			);
			
			echo json_encode($return_content);		
			exit;

		}
}
new evo_re_ajax();
?>