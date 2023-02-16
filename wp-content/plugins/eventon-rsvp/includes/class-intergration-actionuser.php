<?php
/**
 * Intergration with ActionUser Addon
 * @version 2.8
 * @actionuser_version 2.0.10
 */
class evors_actionuser{
	public function __construct(){

		add_filter('evoau_form_fields', array($this, 'fields_to_form'), 10, 1);

		// only for frontend
		// actionUser intergration
		add_action('evoau_frontform_evors', array($this, 'fields'), 10, 6);	
		add_action('evoau_save_formfields', array($this, 'save_values'), 10, 3);
		add_action('evoau_frontend_scripts_enqueue', array($this, 'enqueue_scripts'), 10);

		// event manager
		add_action('evoau_manager_row_title', array($this, 'event_manager_row_title'), 10, 1);
		add_action('evoau_manager_row', array($this, 'event_manager_row'), 10, 1);
		add_filter('evoau_event_manager_backlink_vars', array($this, 'back_link'), 10, 1);

		// ajax filters
		add_action( 'wp_ajax_evors_ajax_get_auem_stats', array( $this, 'evors_ajax_get_auem_stats' ) );
		add_action( 'wp_ajax_nopriv_evors_ajax_get_auem_stats', array( $this, 'evors_ajax_get_auem_stats' ) );

		// only admin fields
		if(is_admin()){
			add_filter('eventonau_language_fields', array($this, 'language'), 10, 1);
		}

		$this->HELP = new evo_helper();
	}

	// include rsvp script
		function enqueue_scripts(){
			wp_enqueue_script('evo_RS_script');	
		}

	// include fields to submission form array
		function fields_to_form($array){
			$array['evors']=array('RSVP Fields', 'evors_rsvp', 'evors','custom','');
			return $array;
		}

	// Frontend showing fields and saving values  
		function fields($field, $event_id, $default_val, $EPMV, $opt2, $lang){
			$helper = $this->HELP;

			echo "<div class='row evors'><p>";
				$evors_rsvp = ($EPMV && !empty($EPMV['evors_rsvp']) && $EPMV['evors_rsvp'][0]=='yes')? true: false;
				echo $helper->html_yesnobtn(array(
					'id'=>'evors_rsvp',
					'input'=>true,
					'label'=>evo_lang_get('evoAUL_rsvp1', 'Allow user RSVP capabilities for this event', $lang, $opt2),
					'var'=> ($evors_rsvp?'yes':'no'),
					'lang'=>$lang,
					'afterstatement'=>'evors_rsvp_section'
				));

			echo "</p></div>";
			$style = 'style="padding-top:8px;"';

			$evors_capacity_count = ($EPMV && !empty($EPMV['evors_capacity_count']))? $EPMV['evors_capacity_count'][0]:'';

			echo "<div id='evors_rsvp_section' class='row evoau_sub_formfield' style='display:".($evors_rsvp?'':'none')."'>
					<p class='label evoau_total_capacity' style='padding:8px 0'><label>".evo_lang_get('evoAUL_rsvp2','Total Available RSVP Capacity',$lang, $opt2)."</label><input type='text' class='' style='margin-left:5px;' name='evors_capacity_count' value='".$evors_capacity_count."' placeholder=''/></p>";

				echo "<p class='label evoau_capacity_show' {$style}>";
				$evors_capacity_show = ($EPMV && !empty($EPMV['evors_capacity_show']) && $EPMV['evors_capacity_show'][0]=='yes')? true: false;
				echo $helper->html_yesnobtn(array(
					'id'=>'evors_capacity_show',
					'input'=>true,
					'label'=>evo_lang_get('evoAUL_rsvp3', 'Show available spaces count on front-end', $lang, $opt2),
					'var'=> ($evors_capacity_show?'yes':'no'),
					'lang'=>$lang,
				));
				echo "</p>";

				echo "<p class='label evoau_show_rsvp' {$style}>";
				$evors_show_rsvp = ($EPMV && !empty($EPMV['evors_show_rsvp']) && $EPMV['evors_show_rsvp'][0]=='yes')? true: false;
				echo $helper->html_yesnobtn(array(
					'id'=>'evors_show_rsvp',
					'input'=>true,
					'label'=>evo_lang_get('evoAUL_rsvp4', 'Show RSVP count for the event on EventCard', $lang, $opt2),
					'var'=> ($evors_show_rsvp?'yes':'no'),
					'lang'=>$lang,
				));
				echo "</p>";

				echo "<p class='label evoau_show_whos_coming' {$style}>";
				$evors_show_whos_coming = ($EPMV && !empty($EPMV['evors_show_whos_coming']) && $EPMV['evors_show_whos_coming'][0]=='yes')? true: false;
				echo $helper->html_yesnobtn(array(
					'id'=>'evors_show_whos_coming',
					'input'=>true,
					'label'=>evo_lang_get('evoAUL_rsvp5', 'Show who is coming to the event', $lang, $opt2),
					'var'=> ($evors_show_whos_coming?'yes':'no'),
					'lang'=>$lang,
				));
				echo "</p>";

				echo "<p class='label evoau_min_cap' {$style}>";
				$evors_min_cap = ($EPMV && evo_check_yn($EPMV, 'evors_min_cap') )? true: false;
				echo $helper->html_yesnobtn(array(
					'id'=>'evors_min_cap',
					'input'=>true,
					'label'=>evo_lang_get('evoAUL_rsvp6', 'Activate event happening minimum capacity', $lang, $opt2),
					'var'=> ($evors_min_cap?'yes':'no'),
					'lang'=>$lang,
					'afterstatement'=>'evors_min_count'
				));
				echo "</p>";

				$evors_min_count = ($EPMV && !empty($EPMV['evors_min_count']))? $EPMV['evors_min_count'][0]:'';

				echo "<div id='evors_min_count' style='display:".($evors_min_cap?'block':'none')."'>
					<p id='' class='label evoau_min_cap_happen' style='padding:8px 0'>
					<label>".evo_lang_get('evoAUL_rsvp7','Minimum Capacity for event to happen',$lang, $opt2)."</label> <input type='text' style='margin-left:5px' name='evors_min_count' value='".$evors_min_count."' placeholder=''/>
					</p>
				</div>";

				// limit max capacity
				echo "<p class='label evors_max_active' {$style}>";
				$evors_max_active = ($EPMV && evo_check_yn($EPMV, 'evors_max_active') )? true: false;
				echo $helper->html_yesnobtn(array(
					'id'=>'evors_max_active',
					'input'=>true,
					'label'=>evo_lang_get('evoAUL_rsvp9', 'Limit maximum capacity count per each RSVP', $lang, $opt2),
					'var'=> ($evors_max_active?'yes':'no'),
					'lang'=>$lang,
					'afterstatement'=>'evors_max_count'
				));
				echo "</p>";

				$evors_max_count = ($EPMV && !empty($EPMV['evors_max_count']))? $EPMV['evors_max_count'][0]:'';

				echo "<div id='evors_max_count' style='display:".($evors_max_active?'block':'none')."'>
					<p id='' class='label evoau_min_cap_happen' style='padding:8px 0'>
					<label>".evo_lang_get('evoAUL_rsvp10','Maximum count number',$lang, $opt2)."</label> <input type='text' style='margin-left:5px' name='evors_max_count' value='".$evors_max_count."' placeholder=''/>
					</p>
				</div>";

				echo "<p class='label evors_notify_event_author' {$style}>";
				$evors_notify_event_author = ($EPMV && !empty($EPMV['evors_notify_event_author']) && $EPMV['evors_notify_event_author'][0]=='yes')? true: false;
				echo $helper->html_yesnobtn(array(
					'id'=>'evors_notify_event_author',
					'input'=>true,
					'label'=>evo_lang_get('evoAUL_rsvp8', 'Receive email notification for new RSVPs', $lang, $opt2),
					'var'=> ($evors_notify_event_author?'yes':'no'),
					'lang'=>$lang,
				));
				echo "</p>";

			echo "</div>";
		}

		// save the RSVP field values
		function save_values($field, $fn, $created_event_id){
			if( $field =='evors'){				

				if(!empty($_POST['evors_rsvp']) && $_POST['evors_rsvp']=='yes'){
					update_post_meta($created_event_id, 'evors_rsvp', $_POST['evors_rsvp']);
					
					// for each above fields
					foreach(array(
						'evors_capacity_count',
						'evors_capacity_show',
						'evors_show_rsvp',
						'evors_show_whos_coming',
						'evors_min_cap',
						'evors_min_count',
						'evors_max_active',
						'evors_max_count',
						'evors_notify_event_author'
					) as $field){
						if(!empty($_POST[$field]))
							update_post_meta($created_event_id, $field, $_POST[$field]);
					}

					// set submitter email add additional emails to receive new rsvp notifications
						EVO()->cal->set_cur('evcal_rs');
						if( EVO()->cal->check_yn('evorsau_add_to_notification')){
						
						// if user is loggedin
							$current_user = wp_get_current_user();
							$cu_email = false;
							if(!empty($_POST['youremail']) && isset($_POST['youremail'])){
								$cu_email = stripslashes($_POST['youremail']);
							}elseif(!empty($current_user)){
								$cu_email = $current_user->user_email;		
							}

							update_post_meta($created_event_id, 'evors_add_emails', $cu_email);
						}

					// capacity set
					if(!empty($_POST['evors_capacity_count']))
						update_post_meta($created_event_id, 'evors_capacity', 'yes');
				}
			}			
		}
	
	// event manager additions
		function event_manager_row_title($EVENT){
			if( $EVENT->check_yn('evors_rsvp') ){
				echo "<tags style='background-color:#8ae06e'>".evo_lang('RSVP On')."</tags>";
			}
		}
		function event_manager_row($EVENT){
			if( $EVENT->check_yn('evors_rsvp') ){
				echo "<a class='evoauem_additional_buttons load_rsvp_stats' data-eid='{$EVENT->ID}' data-ri='{$EVENT->ri}'>".evo_lang('View RSVP Stats')."</a>";
			}
		}
		// add url parameters used in RSVP for parsing event manager url
		function back_link($vars){
			$vars[] = 'customaction';
			return $vars;
		}

		// return ajax based rsvp stats
		function evors_ajax_get_auem_stats(){

			$PP = $this->HELP->process_post( $_POST);

			// set global language
			if(isset($PP['data']) && isset($PP['data']['lang']) ){
				evo_set_global_lang( $PP['data']['lang'] );
			}

			$html = $this->event_manager_show_data($PP['eid'], (isset($PP['ri'])? $PP['ri']: '0' ) );
			
			echo json_encode(array(
				'status'=>'good',
				'html'=>$html
			));exit;
		}

		function event_manager_show_data($event_id, $ri){

			ob_start();

			$EVENT = new EVORS_Event($event_id, $ri);
			
			global $eventon_rs;

			$evors_opt = get_option('evcal_options_evcal_rs');
			$allowed_checkin = evo_settings_check_yn($evors_opt, 'evotx_checkin_guests');
			$selection = (!empty($evors_opt['evors_selection']))? $evors_opt['evors_selection']: true;

			wp_enqueue_script('evo_RS_script');
				
			?>
			<div id='evorsau_rsvp_section' class='evoau_manager_continer' style='padding:15px;' data-eid='<?php echo $event_id;?>' data-ri='<?php echo $ri;?>'>
				<h3 class="evoauem_section_subtitle"><?php evo_lang_e('Event');?>: <b><?php echo get_the_title($event_id);?></b> 
					<i class='evoau_information_bubble' style=''><?php evo_lang_e('RSVP Information & Stats');?></i> 
					<i class='fa fa-repeat evorsau_refresh_data evorsau_icon_button' title='<?php evo_lang_e('Refresh data');?>'></i>
				</h3>	
				<div class="evoaursvp_data" style='margin-top:10px;'>
					
					<div id="evorsau_stats" class='evoau_tile' style=''>
						<h4 style='margin:0'><?php evo_lang_e('Total Capacity');?></h4>
						<?php
							$total = $EVENT->event->get_prop('evors_capacity_count');
							
							if( !empty( $total )){
								echo "<p class='num'>{$total}</p>";
							}

							$synced = $EVENT->total_rsvp_counts();

							$total = !empty($total)? $total: ($synced['y'] + $synced['n'] + $synced['m']);

						?>

						<div class="evorsau_bar">
							<span class="yes_count" style='width:<?php echo ($total>0? ($synced['y']/$total *100):0);?>%'></span>
							<?php if(is_array($selection) && in_array('n', $selection)):?>
								<span class="no_count" style='width:<?php echo ($total>0? ($synced['n']/$total *100):0);?>%'></span>
							<?php endif;?>

							<?php if(is_array($selection) && in_array('m', $selection)):?>
							<span class="maybe_count" style='width:<?php echo ($total>0? ($synced['m']/$total *100):0);?>%'></span>
							<?php endif;?>
						</div>
						<div class="evorsau_legends">
							<span class="data_yes"><b></b> <?php evo_lang_e('Yes');?> <em><?php echo $synced['y'];?></em></span>

							<?php if(is_array($selection) && in_array('n', $selection)):?>
							<span class="data_no"><b></b> <?php evo_lang_e('No');?> <em><?php echo $synced['n'];?></em></span>
							<?php endif;?>

							<?php if(is_array($selection) && in_array('m', $selection)):?>
							<span class="data_maybe"><b></b> <?php evo_lang_e('Maybe');?> <em><?php echo $synced['m'];?></em></span>
							<?php endif;?>
						</div>
					</div>	

					<div class='evorsau_actions evoau_tile trig_evo_loading'>
						<?php
						$JSON_data = EVORS()->frontend->event_rsvp_data(
							$EVENT, 	array('rsvpid'=> '','rsvp'=> 'null'	,'incard'=>'no')
						);

						?>
						<div class='evors_jdata' data-j='<?php echo $JSON_data;?>'></div>
						<a class='evcal_btn evorsau_trig_rsvp_form' ><?php evo_lang_e('Register a new guest on the spot');?></a>
					</div>	

									
						
					<?php 
						$RSVP_LIST = $EVENT->GET_rsvp_list();

						if($RSVP_LIST):
							$__checking_status_text = EVORS()->frontend->get_trans_checkin_status();

							?>
							<?php
							// download attendees at CSV file
								if(evo_settings_check_yn($evors_opt, 'evorsau_csv_download')):
							?>
								<div class='evors_actions'>
								<?php
									$exportURL = add_query_arg(array(
									    'action' => 'the_ajax_evors_f3',
									    'e_id' => $event_id,     // cache buster
									    'nonce'=> wp_create_nonce(AJDE_EVCAL_BASENAME),
									    
									), admin_url('admin-ajax.php'));
								?>
									<h4><?php evo_lang_e('Event RSVP Actions');?></h4>
									<p><a href='<?php echo $exportURL;?>' class='evcal_btn'><?php evo_lang_e('Download Attendees as CSV');?></a></p>
								</div>
							<?php endif;?>

							<div id='evorsau_attendee_list' class='evors_list <?php echo evo_settings_check_yn($evors_opt,'evorsau_check_guest')?'checkable ':'';?>evorsau_attendee_list'>	
								
								<?php wp_nonce_field( AJDE_EVCAL_BASENAME, 'evors_nonce' );?>

								<h4 style='margin:0'><?php evo_lang_e('Registered Attendees');?> <i class='fa fa-search evorsau_trig_find_attendee evorsau_icon_button'></i></h4>

								<div class='evorsau_find_rsvp evoau_tile' style='display: none'>
									<p style='padding:0; margin:0'><input placeholder='<?php evo_lang_e('Type in guest email address or RSVP id');?>' type="text" class='evorsau_find_attendee' style='width:100%' name="rsvp_info"></p>
								</div>	

								<?php // RSVP yes attendees?>

								<div class='evoau_tile'>
									<p class='header rsvp_yes'><?php evo_lang_e('RSVP Status: YES');?></p>
									<?php
									if(!empty($RSVP_LIST['y']) && count($RSVP_LIST['y'])>0){
										echo "<ul>";
										foreach($RSVP_LIST['y'] as $_id=>$rsvp){
											echo $this->each_attendee_data_row($_id, $rsvp, $__checking_status_text);
										}
										echo "</ul>";
									}else{
										echo "<p class='noone'>".evo_lang('No Attendees found.')."</p>";
									}
									?>
								</div>

								<?php 

								// RSVP maybe attendees

								if(is_array($selection) && in_array('m', $selection)):?>
								
								<div class='evoau_tile'>
									<p class='header rsvp_maybe'><?php evo_lang_e('RSVP Status: MAYBE');?></p><?php
									if(!empty($RSVP_LIST['m']) && count($RSVP_LIST['m'])>0){
										echo "<ul>";
										foreach($RSVP_LIST['m'] as $_id=>$rsvp){
											echo $this->each_attendee_data_row($_id ,$rsvp, $__checking_status_text);
										}
										echo "</ul>";
									}else{	
										echo "<p class='noone'>".evo_lang('No Attendees found.')."</p>";	
									}	
								?>
								</div>	
								<?php endif;	?>
							</div>
								
						<?php endif;	?>
				</div>
				</div>
			<?php

			return ob_get_clean();
		}

		function each_attendee_data_row($_id, $rsvp, $text){
			ob_start();

			//print_r($rsvp);
			
			$phone = !empty($rsvp['phone'])? $rsvp['phone']:false;
			$_status = (!empty($rsvp['status']))? $rsvp['status']:'check-in';
			$translated_status = $text[$_status];
			
			?>
			<li data-rsvpid='<?php echo $_id;?>' data-e='<?php echo $rsvp['email'];?>'>
				<em class='evorsadmin_rsvp' title='<?php _e('Click for more information','eventon');?>'><?php echo '#'.$_id;?></em>
				<?php echo ' '. $rsvp['name'].' <i style="padding-left:10px">('.$rsvp['email'].( $phone? ' '. evo_lang('PHONE') .':'.$phone:'').')</i>';?>
				<span data-id='<?php echo $_id;?>' data-status='<?php echo $_status;?>' class='checkin <?php echo ($_status=='checked')? 'checked':null;?>'><?php echo $translated_status;?></span>
				<?php
				// signed in
				if( isset($rsvp['signin']) && $rsvp['signin'] == 'y'){
					echo "<i class='signin fa fa-check'></i>";
				}
				?>

				<span class='count'><?php echo $rsvp['count'];?></span>
				<?php 
				// if RSVP have other names show those as well
				if($rsvp['names']!= 'na'):?>
					<span class='other_names'><?php 
						echo implode(', ', $rsvp['names']);
					?></span>
				<?php endif;?>
			</li>
			<?php
			return ob_get_clean();
		}
	// language
		function language($array){
			$newarray = array(
				array('label'=>'RSVP Fields for ActionUser','type'=>'subheader'),
					array('label'=>'Allow user RSVP capabilities for this event','name'=>'evoAUL_rsvp1'),
					array('label'=>'Total Available RSVP Capacity','name'=>'evoAUL_rsvp2'),
					array('label'=>'Show available spaces count on front-end','name'=>'evoAUL_rsvp3'),
					array('label'=>'Show RSVP count for the event on EventCard','name'=>'evoAUL_rsvp4'),
					array('label'=>'Show who is coming to the event','name'=>'evoAUL_rsvp5'),
					array('label'=>'Activate event happening minimum capacity','name'=>'evoAUL_rsvp6'),
					array('label'=>'Minimum Capacity for event to happen','name'=>'evoAUL_rsvp7'),
					array('label'=>'Receive email notification for new RSVPs','name'=>'evoAUL_rsvp8'),
					array('label'=>'Limit maximum capacity count per each RSVP','name'=>'evoAUL_rsvp9'),
					array('label'=>'Maximum count number','name'=>'evoAUL_rsvp10'),
					array('label'=>'Event','var'=>1),
					array('label'=>'RSVP Information & Stats','var'=>1),
					array('label'=>'Event RSVP Actions','var'=>1),
					array('label'=>'Download Attendees as CSV','var'=>1),
					array('label'=>'Total Capacity','var'=>1),
					array('label'=>'Yes','var'=>1),
					array('label'=>'No','var'=>1),
					array('label'=>'Maybe','var'=>1),
					array('label'=>'Attendees','var'=>1),
					array('label'=>'RSVP Status: YES','var'=>1),
					array('label'=>'RSVP Status: MAYBE','var'=>1),
					array('label'=>'No Attendees found','var'=>1),
					array('label'=>'Phone','var'=>1),
					array('label'=>'RSVP On','var'=>1),
					array('label'=>'View RSVP Stats','var'=>1),
				array('type'=>'togend'),
			);
			return array_merge($array, $newarray);
		}
}
new evors_actionuser();