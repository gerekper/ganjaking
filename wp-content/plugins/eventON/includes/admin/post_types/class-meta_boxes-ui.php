<?php
/**
 * Event User Interaction
 * @4.5.9
 */

// initial values
	$exlink_option = ($EVENT->get_prop("_evcal_exlink_option"))? $EVENT->get_prop("_evcal_exlink_option") :1;
	$_show_extra_fields = ($exlink_option=='1' || $exlink_option=='3' || $exlink_option=='X')? false:true;
?>
<div class='evcal_data_block_style1'>
	<div class='evcal_db_data'>										
		
		<input id='evcal_exlink_option' type='hidden' name='_evcal_exlink_option' value='<?php echo $exlink_option; ?>'/>
		
		<div <?php echo !$_show_extra_fields?"style='display:none'":null;?> id='evo_new_window_io' class=''>
			<?php 
			// option to hide related event images
			echo EVO()->elements->get_element(array(
				'type'=>'yesno',
				'id'=>'_evcal_exlink_target',
				'value'=> $EVENT->get_prop('_evcal_exlink_target'),
				'name'=> __('Open in new window','eventon'),
				'tooltip'=> __('This will open this link in a new window, when event is clicked.','eventon')
			));
			?>
		</div>
		
		<!-- external link field-->
		<input id='evcal_exlink' placeholder='<?php _e('Type the URL address eg. https://','eventon');?>' type='text' name='evcal_exlink' value='<?php echo ($EVENT->get_prop("evcal_exlink") )? $EVENT->get_prop("evcal_exlink"):null?>' style='width:100%; <?php echo $_show_extra_fields? 'display:block':'display:none'?>'/>
		
		<div class='evcal_db_uis'>
			<a link='no'  class='evcal_db_ui evcal_db_ui_0 <?php echo ($exlink_option=='X')?'selected':null;?>' title='<?php _e('Do nothing','eventon');?>' value='X'></a>

			<a link='no'  class='evcal_db_ui evcal_db_ui_1 <?php echo ($exlink_option=='1')?'selected':null;?>' title='<?php _e('Slide Down Event Card','eventon');?>' value='1'></a>
			
			<!-- open as link-->
			<a link='yes' class='evcal_db_ui evcal_db_ui_2 <?php echo ($exlink_option=='2')?'selected':null;?>' title='<?php _e('External Link','eventon');?>' value='2'></a>	
			
			<!-- open as popup -->
			<a link='yes' class='evcal_db_ui evcal_db_ui_3 <?php echo ($exlink_option=='3')?' selected':null;?>' title='<?php _e('Popup Window','eventon');?>' value='3'></a>
			
			<!-- single event -->
			<a link='yes' linkval='<?php echo get_permalink($EVENT->ID);?>' class='evcal_db_ui evcal_db_ui_4 <?php echo (($exlink_option=='4')?'selected':null);?>' title='<?php _e('Open Event Page','eventon');?>' value='4'></a>
			
			<?php
				// (-- addon --)
				//if(has_action('evcal_ui_click_additions')){do_action('evcal_ui_click_additions');}
			?>							
			<div class='clear'></div>
		</div>
	</div>
</div>
<?php