<?php
/**
 * Admin class for countdown addon
 *
 * @version 	0.1
 * @author  	AJDE
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evocd_admin{
	function __construct(){

		// appearance
		add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 1);
		// styles
		add_filter('evo_styles_primary_font',array($this,'primary_font'), 10,1);
		add_filter('evo_styles_secondary_font',array($this,'secondary_font'), 10,1);

		// language
		add_filter('eventon_settings_lang_tab_content', array($this,'language_additions'), 10, 1);

		// settings
		add_filter('eventon_settings_tabs',array($this,'tab_array') ,10, 1);
		add_action('eventon_settings_tabs_evcal_cd',array($this,'tab_content') );	

		add_action( 'admin_menu', array( $this, 'menu' ),9);
		
		add_filter('eventon_event_metafields',array($this,'save_meta'), 10, 1);
		
		// event meta box
		add_action('eventon_add_meta_boxes',array($this, 'meta_box'));

		
	}
	

	// event meta box
		function meta_box(){
			add_meta_box('ajdeevcal_evocd',__('Countdown Timer','eventon_cd'), array($this,'meta_box_content'),'ajde_events', 'side', 'default');
		}
		function meta_box_content(){
			global $post, $eventon_cd, $ajde;

			$event_pmv = (!empty($post))? get_post_custom($post->ID):null;
			$_evocd_countdown = (!empty($event_pmv['_evocd_countdown']))?
				$event_pmv['_evocd_countdown'][0]:null;
			$_evocd_countdown_ux = (!empty($event_pmv['_evocd_countdown_ux']))?
				$event_pmv['_evocd_countdown_ux'][0]:null;
			
			ob_start();				
			?>
			<p class='yesno_leg_line' style='padding:5px 0px;margin:0'>
				<?php 	echo $ajde->wp_admin->html_yesnobtn(
					array(
						'id'=>'_evocd_countdown', 
						'var'=>$_evocd_countdown,
						'input'=>true,
						'label'=>__('Show countdown timer','eventon_cd'),
						'guide'=>__('This will show the countdown timer for this event on eventtop on the calendar.','eventon_cd'),
						'guide_position'=>'L',
						'attr'=> array('afterstatement'=>'evocd_settings')
					));
				?>	
			</p>
			<div id='evocd_settings' class='evo_edit_field_box' style='display:<?php echo (!empty($_evocd_countdown) && $_evocd_countdown=='yes')?'block':'none';?> '>
				
				<p><label for=""><?php _e('Text next to timer','eventon_cd');?></label><input style='width:100%' type="text" placeholder='This event ends in..' value='<?php echo !empty($event_pmv['_evocd_tx1'])? $event_pmv['_evocd_tx1'][0]:'';?>' name='_evocd_tx1'></p>
				
				<p><label for=""><?php _e('Text to show when timer expire','eventon');?></label><input style='width:100%' type="text" placeholder='Time has run out! Better luck next time!' value='<?php echo !empty($event_pmv['_evocd_tx2'])? $event_pmv['_evocd_tx2'][0]:'';?>' name='_evocd_tx2'></p>
				<p><label for=""><?php _e('What to do when timer expire?','eventon_cd');?></label>
				
				<?php $timer_expire_options = apply_filters('evocd_timer_expire_options', array(
						'0'=>__('Do nothing','eventon'),
						'1'=>__('Hide event','eventon'),
						'2'=>__('Make the event unclickable','eventon'),
						'3'=>__('Blackout event','eventon'),
					), $event_pmv);
				?>
				<select name="_evocd_countdown_ux" >
					<?php 
					foreach($timer_expire_options as $field=>$value){
						echo '<option value="'.$field.'" '. (($_evocd_countdown_ux==$field)?'selected="selected"':'' ).'>'.$value.'</option>';
					}
					?>					
				</select></p>
	
				<?php
					$_evocd_countdown_end  = (!empty($event_pmv['_evocd_countdown_end']))?
				$event_pmv['_evocd_countdown_end'][0]:null;
				?>
				<p><label for=""><?php _e('Countdown expiration time','eventon_cd');?></label>
				<select name="_evocd_countdown_end" >
					<?php 
					foreach(array(
						'end'=>__('At Event End','eventon'),
						'start'=>__('At Event Start','eventon'),
					) as $field=>$value){
						echo '<option value="'.$field.'" '. (($_evocd_countdown_end==$field)?'selected="selected"':'' ).'>'.$value.'</option>';
					}
					?>
				</select>
				</p>

				<p>
					<label for=""><?php _e('Custom time offset before expire','eventon');?> <?php $ajde->wp_admin->tooltips( __('Set a custom time offset to above selected countdown expiration time. Custom time set here will be subsctrated from above set expiration time.'),'L',true);?></label>
					<input style='width:100%' type="text" placeholder='60 in minutes' value='<?php echo !empty($event_pmv['_evocd_custom_time'])? $event_pmv['_evocd_custom_time'][0]:'';?>' name='_evocd_custom_time'></p>

			</div>
			<?php

			echo ob_get_clean();
		}
		function save_meta($fields){
			$fields[] = '_evocd_countdown';
			$fields[] = '_evocd_countdown_ux';
			$fields[] = '_evocd_tx1';
			$fields[] = '_evocd_tx2';
			$fields[] = '_evocd_countdown_end';
			$fields[] = '_evocd_custom_time';
			return $fields;
		}

	// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'Countdown', __('Countdown','eventon'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_cd', '' );
		}

	// TABS SETTINGS
		function tab_array($evcal_tabs){
			$evcal_tabs['evcal_cd']='Countdown';		
			return $evcal_tabs;
		}

		function tab_content(){
			global $eventon;

			$eventon->load_ajde_backender();

			ob_start();?>

				<p>Event countdown can be turned on for each event from event edit page.
				<br/>
				You can hide the event countdown timers from a calendar by including below shortcode variable:
				<br/><br/>
				<code>hide_countdown="yes"</code> example within a shortcode <code>[add_eventon hide_countdown="yes"]</code>
				<br/>
				<br/>Text captions that appear on countdown can be edited from  <strong>myeventon > language</strong>.
				</p><br/>

				<h4>Compatibility</h4>
				<p>
					Real-time event countdown timer (at this time) can only be run on an event in the calendar OR on a single event page. It will only show a static version of the timer when an event is set to open as lightbox.
				</p>


			<?php $content = ob_get_clean();
			?>
			<form method="post" action=""><?php settings_fields('evocd_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
			<div id="evcal_cd" class="evcal_admin_meta">	
				<div class="evo_inside">
				<?php
				$site_name = get_bloginfo('name');
				$site_email = get_bloginfo('admin_email');

				$customization_pg_array = array(					
					array(							
							'id'=>'eventon_countdown',
							'name'=>'Settings & Instructions for Event Countdown',
							'display'=>'show',
							'tab_name'=>'General',
							'fields'=> apply_filters('evo_cd_setting_fields', array(
								array('id'=>'evo_cd_001','type'=>'customcode',
										'code'=>$content),

							)
					))
				);
						
				$eventon->load_ajde_backender();						
				$evcal_opt = get_option('evcal_options_evcal_cd');
				print_ajde_customization_form($customization_pg_array, $evcal_opt);	
				?>
			</div>
			</div>
			<div class='evo_diag'>
				<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
			</div>
			
			</form>	
			<?php
		}
	
	// Appearnace section
		function appearance_settings($array){			
			$new[] = array('id'=>'evocd','type'=>'hiddensection_open','name'=>'CountDown Styles','display'=>'none');
			$new[] = array('id'=>'evocd','type'=>'fontation','name'=>'Time boxes',
				'variations'=>array(
					array('id'=>'evocd_1', 'name'=>'Box font color','type'=>'color', 'default'=>'6b6b6b'),
					array('id'=>'evocd_3', 'name'=>'Seconds Color','type'=>'color', 'default'=>'cccccc'),					
					array('id'=>'evocd_4', 'name'=>'Time amount text color','type'=>'color', 'default'=>'ABABAB'),					
				)
			);
			$new[] = array('id'=>'evocd_0','type'=>'fontation','name'=>'Countdown title text color',
				'type'=>'color', 'default'=>'ABABAB');
			$new[] = array('id'=>'evocd','type'=>'fontation','name'=>'Expired Timer',
				'variations'=>array(
					array('id'=>'evocd_5', 'name'=>'Font Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evocd_6', 'name'=>'Background color','type'=>'color', 'default'=>'F49483'),
					array('id'=>'evocd_7', 'name'=>'EventTop background color','type'=>'color', 'default'=>'EAEAEA'),				
					array('id'=>'evocd_8', 'name'=>'EventTop border color','type'=>'color', 'default'=>'3F3F3F'),					
				)
			);

			$new[] = array('id'=>'evotx','type'=>'hiddensection_close',);
			return array_merge($array, $new);
		}
		function dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'body .eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_time .countdown-section .countdown-amount, body .evo_pop_body .evcal_desc span.evocd_timer span.evocd_time .countdown-section .countdown-amount',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evocd_1',	'default'=>'6b6b6b'),
					)
				),array(
					'item'=>'body .eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_time .countdown-show3 span:nth-child(3) .countdown-amount, body .eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_time .countdown-show4 span:nth-child(4) .countdown-amount',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evocd_3',	'default'=>'cccccc'),
					)
				),array(
					'item'=>'.eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_time .countdown-section .countdown-period, .evo_pop_body .evcal_desc span.evocd_timer span.evocd_time .countdown-section .countdown-period',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evocd_4',	'default'=>'ABABAB'),
					)
				),array(
					'item'=>'body .eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_text, .evo_pop_body .evcal_desc span.evocd_timer span.evocd_text',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evocd_0',	'default'=>'ABABAB'),
					)
				),array(
					'item'=>'.eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_text.timeexpired, .evo_pop_body .evcal_desc span.evocd_timer span.evocd_text.timeexpired',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evocd_5',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evocd_6',	'default'=>'F49483'),
					)
				),array(
					'item'=>'#evcal_list .eventon_list_event.blackout .desc_trig',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evocd_7',	'default'=>'EAEAEA'),
						array('css'=>'border-color:#$!important', 'var'=>'evocd_8',	'default'=>'3F3F3F'),
					)
				)		
			);

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}

		function primary_font($classes){
			return $classes.',.eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_time, 
			.evo_pop_body .evcal_desc span.evocd_timer span.evocd_time,
			.eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_time .countdown-section .countdown-period, 
			.evo_pop_body .evcal_desc span.evocd_timer span.evocd_time .countdown-section .countdown-period,
			.eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_time .countdown-section .countdown-amount, 
			.evo_pop_body .evcal_desc span.evocd_timer span.evocd_time .countdown-section .countdown-amount';
		}
		function secondary_font($classes){
			return $classes.',.eventon_events_list .eventon_list_event .evcal_desc span.evocd_timer span.evocd_text, 
			.evo_pop_body .evcal_desc span.evocd_timer span.evocd_text';
		}
	// language settings additinos
		function language_additions($_existen){

			$opt2 = get_option('evcal_options_evcal_2');

			$lang_var = (!empty($_GET['lang']))? $_GET['lang']: 'L1';
			$opt2 = isset($opt2[$lang_var])? $opt2[$lang_var]: array();

			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: COUNTDOWN'),
					array('type'=>'subheader','label'=>'Countdown time piece names'),
						array('label'=>'Yr','type'=>'multibox_open', 'items'=>array(
								'evocd_001'=> (!empty($opt2['evocd_001'])?$opt2['evocd_001']:'Yr'),
								'evocd_002'=> (!empty($opt2['evocd_002'])?$opt2['evocd_002']:'Mo'),
								'evocd_003'=> (!empty($opt2['evocd_003'])?$opt2['evocd_003']:'Wk'),
								'evocd_004'=> (!empty($opt2['evocd_004'])?$opt2['evocd_004']:'Dy'),
								'evocd_005'=> (!empty($opt2['evocd_005'])?$opt2['evocd_005']:'Hr'),
								'evocd_006'=> (!empty($opt2['evocd_006'])?$opt2['evocd_006']:'Mn'),
								'evocd_007'=> (!empty($opt2['evocd_007'])?$opt2['evocd_007']:'Sc'),
							)),
					array('type'=>'togend'),
					array('label'=>'Event is going live in','var'=>1),
					array('label'=>'This event ends in..','var'=>1),
					array('label'=>'Time has ran out! Better luck next time!','var'=>1),
					
				array('type'=>'togend'),				
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

	
}
