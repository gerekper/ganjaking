<?php
/**
 * Meta boxes for evo-review
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/evo-review
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Initiate
	function evoRE_meta_boxes(){
		add_meta_box('evore_mb1','Review Event', 'evoRE_metabox_content','ajde_events', 'normal', 'high');
		add_meta_box('evore_mb1','Review Event', 'evoRE_metabox_review','evo-review', 'normal', 'high');
		
		do_action('evoRE_add_meta_boxes');
	}
	add_action( 'add_meta_boxes', 'evoRE_meta_boxes' );
	add_action( 'eventon_save_meta', 'evoRE_save_meta_data', 10 , 2 );
	add_action( 'save_post', 'evoRE_save_review_meta_data', 1 , 2 );

// META box for Review page
	function evoRE_metabox_review(){
		global $post, $eventon_re, $ajde, $pagenow;
		$pmv = get_post_meta($post->ID);
		
		?>	
		<div class='eventon_mb' style='margin:-6px -12px -12px'>
		<div style='background-color:#ECECEC; padding:15px;'>
			<div style='background-color:#fff; border-radius:8px;'>
			<table id='evors_rsvp_tb' width='100%' class='evo_metatable'>				
				<tr><td><?php _e('Review #','eventon');?>: </td><td><?php echo $post->ID;?></td></tr>
				<tr><td><?php _e('Star Rating','eventon');?>: </td>
					<td><div class='evore_star_rating_new'><?php
					if(!empty($pmv['rating'])){
						echo $eventon_re->frontend->functions->get_star_rating_html($pmv['rating'][0]);
					}else{
						echo $eventon_re->frontend->functions->get_star_rating_html(1);
					}
					?>
					<input type="hidden" name='rating' value='<?php echo (!empty($pmv['rating'])? $pmv['rating'][0]:'');?>'/>
					</div>
					</td></tr>
				<tr><td><?php _e('Review','eventon');?>: </td>
				<td>
					<textarea style='width:100%' name='review'><?php echo (!empty($pmv['review']) )? $pmv['review'][0]:'';?></textarea>					
				</td></tr>
				<tr><td><?php _e('Reviewer Name','eventon');?>: </td>
					<td><input style='width:100%' type='text' name='name' value='<?php echo (!empty($pmv['name']) )? $pmv['name'][0]:'';?>'/>
					</td></tr>
				<tr><td><?php _e('Reviewer Email','eventon');?>: </td>
					<td><input style='width:100%' type='text' name='email' value='<?php echo (!empty($pmv['email']) )? $pmv['email'][0]:'';?>'/>
					</td>
				</tr>				
				<tr><td><?php _e('Event','eventon');?>: </td>
					<td><?php 
						// event for review
						if(empty($pmv['e_id'])){
							$events = get_posts(array('posts_per_page'=>-1, 'post_type'=>'ajde_events'));
							if($events && count($events)>0 ){
								echo "<select name='e_id'>";
								foreach($events as $event){
									echo "<option value='".$event->ID."'>".get_the_title($event->ID)."</option>";
								}
								echo "</select>";
							}
							wp_reset_postdata();
						}else{
							echo '<a href="'.get_edit_post_link($pmv['e_id'][0]).'">'.get_the_title($pmv['e_id'][0]).'</a></td></tr>';
						}
				// REPEATING interval
				if($pagenow!='post-new.php' && !empty($pmv['e_id'])){
					$saved_ri = (!empty($pmv['repeat_interval']) && $pmv['repeat_interval'][0]!='0')?
						$pmv['repeat_interval'][0]:'0';
					$event_pmv = get_post_custom($pmv['e_id'][0]);
					?>
					<tr><td><?php _e('Event Date','eventon');?>: </td>
					<td><?php 
					$repeatIntervals = !empty($event_pmv['repeat_intervals'])? 
						unserialize($event_pmv['repeat_intervals'][0]): false;
					$wp_date_format = get_option('date_format');

					if($repeatIntervals && count($repeatIntervals)>0){
						$datetime = new evo_datetime();		
						echo "<select name='repeat_interval'>";
						$x=0;
						
						foreach($repeatIntervals as $interval){
							$time = $datetime->get_int_correct_event_time($event_pmv,$x);
							echo "<option value='".$x."' ".( $saved_ri == $x?'selected="selected"':'').">".date($wp_date_format.' h:i:a',$time)."</option>"; $x++;
						}
						echo "</select>";
					}else{
						// event with no repeating intervals but still data is saved as RI =0
						echo date($wp_date_format.' h:i:a',$event_pmv['evcal_srow'][0]);

					}
					?></td></tr>
					<?php
				}				
				?>
			</table>
			</div>
		</div>
		</div>
		<?php
	}
	function evoRE_save_review_meta_data($post_id, $post){
		if($post->post_type!='evo-review')
			return;
			
		// Stop WP from clearing custom fields on autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)	return;

		// Prevent quick edit from clearing custom fields
		if (defined('DOING_AJAX') && DOING_AJAX)	return;
		
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if( isset($_POST['evorsvp_nonce']) && !wp_verify_nonce( $_POST['evorsvp_nonce'], plugin_basename( __FILE__ ) ) ){
			return;
		}

		// Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) )	return;	

		global $pagenow;
		$_allowed = array( 'post-new.php', 'post.php' );
		if(!in_array($pagenow, $_allowed)) return;

		$fields = array(
			'rating', 'review','reviewer_name','reviewer_email','e_id','repeat_interval',
		);

		foreach($fields as $field){
			if(!empty($_POST[$field])){
				update_post_meta( $post_id, $field, $_POST[$field] );
			}else{
				if($field!='e_id')
					delete_post_meta($post_id, $field);
			}
		}

		// sync rating count
		global $eventon_re;
		if(!empty($_POST['e_id'])){
			$eventon_re->frontend->functions->sync_ratings($_POST['e_id']);
		}

	}

// Review meta box for EVENT posts
	function evoRE_metabox_content(){

		global $post, $eventon_re, $eventon;
		$pmv = get_post_meta($post->ID);
		wp_nonce_field( plugin_basename( __FILE__ ), 'evors_nonce' );
		

		$event_review = (!empty($pmv['event_review']) && $pmv['event_review'][0]=='yes')? 'yes': 'no';
		$datetime = new evo_datetime();
		$repeatIntervals = !empty($pmv['repeat_intervals'])? unserialize($pmv['repeat_intervals'][0]): false;

		$wp_date_format = get_option('date_format');
	?>
	<div class='eventon_mb'>
	<div class="evore">
		<p class='yesno_leg_line ' style='padding:10px'>
			<?php echo eventon_html_yesnobtn(array(
				'var'=>$event_review, 
				'id'=>'event_review',
				'label'=>__('Activate review and ratings for this event','eventon'),
				'input'=> true,
				'attr'=>array('afterstatement'=>'evore_details')
			)); ?>
		</p>
		<div id='evore_details' class='evors_details evomb_body ' <?php echo ( $event_review=='yes')? null:'style="display:none"'; ?>>		
			<div class="evore_star_rating">
				<p><?php _e('Overall average rating for this event','eventon');?><?php echo $eventon->throw_guide("The rating information is for all repeating instances of this event (if has repeating instances)", '',false)?></p>

				<div class='evore_star_data'>
					<span class='evore_stars'>
					<?php
						//$average = $eventon_re->frontend->functions->get_average_rating($post->ID, $pmv);
						//$rating_count = $eventon_re->frontend->functions->get_rating_count($post->ID, $pmv);

						$ALLaverage = $eventon_re->frontend->functions->get_average_all_ratings($post->ID, $pmv);
						$ALLrating_count = $eventon_re->frontend->functions->get_rating_all_count($post->ID, $pmv);
						
						echo $eventon_re->frontend->functions->get_star_rating_html( $ALLaverage);

					?>
					</span>
					<em class='rating_data'><?php echo $ALLaverage? $ALLaverage:'0.0';?>/5.0 (<?php echo $eventon_re->frontend->functions->get_rating_percentage($ALLaverage);  ?>%)</em>
					<em class='rating_data'><?php echo $ALLrating_count?$ALLrating_count:'0';?> <?php _e('Ratings','eventon');?></em>
				</div>
				<p id="evore_message" style='display:none' data-t1='<?php _e('Loading..','eventon');?>' data-t2='<?php _e('Count not sync ratings at this moment, please try later','eventon');?>'></p>
			</div>
			
			<?php $rating_data = ( evo_check_yn($pmv, '_rating_data') ? 'yes': 'no');?>
			<p class='yesno_leg_line ' style='padding:10px 0 0'>
				<?php echo eventon_html_yesnobtn(array(
					'var'=>$rating_data, 
					'id'=>'_rating_data',
					'label'=>__('Hide Rating Data for this Event','eventon'),
					'input'=> true,
				)); ?>
			</p>

			<?php $rating_data = ( evo_check_yn($pmv, '_all_reviews') ? 'yes': 'no');?>
			<p class='yesno_leg_line ' style='padding:10px 0 0'>
				<?php echo eventon_html_yesnobtn(array(
					'var'=>$rating_data, 
					'id'=>'_all_reviews',
					'label'=>__('Show all reviews instead of scrolling view','eventon'),
					'input'=> true,
				)); ?>
			</p>	
		
			<div class='evcal_rep evore_info_actions'>
				<div class="evcalr_1">
					<p class="actions">
						<a id="evore_VR" class='button_evo reviews ajde_popup_trig' data-e_id='<?php echo $post->ID;?>' data-riactive='<?php echo ($repeatIntervals)?'yes':'no';?>' data-popc='evore_lightbox'><?php _e('View All Reviews','eventon');?></a>
						<a id="evore_SY" class='button_evo ' data-e_id='<?php echo $post->ID;?>' data-riactive='<?php echo ($repeatIntervals)?'yes':'no';?>' ><?php _e('Manually Sync Ratings','eventon');?></a>
						
					</p>
					<div id="evore_message"></div>
					<?php
						// reviews lightbox content
						
						if($repeatIntervals && count($repeatIntervals)>0):
						ob_start();?>
						<div id='evore_view_reviews'>
							<p style='text-align:center'><label><?php _e('Select Repeating Instance of Event','eventon');?></label> 
								<select name="" id="evore_event_repeatInstance">
									<option value="all"><?php _e('All Repeating Instances','eventon');?></option>
									<?php
									$x=0;								
									foreach($repeatIntervals as $interval){
										$time = $datetime->get_correct_formatted_event_repeat_time($pmv,$x, $wp_date_format);
										echo "<option value='".$x."'>".$time['start']."</option>"; $x++;
									}
									?>
								</select>
							</p>
							<p style='text-align:center'><a id='evore_VR_submit' data-e_id='<?php echo $post->ID;?>' class='evo_admin_btn btn_prime' ><?php _e('Submit','eventon');?></a> </p>
						</div>
						<div id='evore_view_reviews_list'></div>
						<?php 
							$viewreviews_content = ob_get_clean(); 
							else:	$viewreviews_content = "<div id='evore_view_reviews'>".__('LOADING','eventon')."...</div>";	endif;
						?>
					<?php echo $eventon->output_eventon_pop_window(array('class'=>'evore_lightbox', 'content'=>$viewreviews_content, 'title'=>__('View Reviews for this Event','eventon'), 'type'=>'padded', 'max_height'=>450 ));
					?>
				</div>
			</div>
		</div>
	</div>
	</div>
	<?php
	}

// Save the data from meta box
	function evoRE_save_meta_data($arr, $post_id){
		$fields = array(
			'event_review', 
			'_rating_data',
			'_all_reviews'
		);
		foreach($fields as $field){
			if(!empty($_POST[$field])){
				update_post_meta( $post_id, $field, $_POST[$field] );
			}else{
				delete_post_meta($post_id, $field);
			}
		}			
	}

// message for publish right side meta box for review post page
	add_action( 'post_submitbox_misc_actions', 'evo_review_settings_per_post' );
	function evo_review_settings_per_post(){
		global $post;

		if ( ! is_object( $post ) ) return;

		if ( $post->post_type != 'evo-review' ) return;

		$post_status = get_post_status($post->ID);

		if($post_status=='draft'){
			echo "<div class='evoreview_publishbox' style='padding:10px;background-color:#FFA48B; color:#fff;'>".__('This review is not published yet','eventon')."</div>";
		}
	}