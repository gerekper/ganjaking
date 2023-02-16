<?php
/**
 * The template for displaying event content within loops.
 *
 * Override this template by copying it to yourtheme/eventon/content-event.php
 *
 * @author 		AJDE
 * @package 	eventon-single-event/Templates
 * @version    	0.14
 */
 
global $eventon, $eventon_sin_event;
	$event_id = get_the_ID();
	$evopt1 = get_option('evcal_options_evcal_1');
	
	$lang = (isset($_GET['l']))? $_GET['l']: 'L1';	

	// redirect to correct repeat interval, when using hashtag based repeat intervals
	$eventon_sin_event->frontend->functions->redirect_script();

	$repeati = (isset($_GET['ri']))? $_GET['ri']: 0;
	
	$event_header = $eventon_sin_event->get_single_event_header($event_id, $repeati, $lang);
	$rtl = (!empty($evopt1['evo_rtl']) && $evopt1['evo_rtl']=='yes')? true:false;


	$sin_event_evodata = apply_filters('evosin_evodata_vals',array(
		'mapformat'=> (($evopt1['evcal_gmap_format']!='')?$evopt1['evcal_gmap_format']:'roadmap'),
		'mapzoom'=> ( ($evopt1['evcal_gmap_zoomlevel']!='')?$evopt1['evcal_gmap_zoomlevel']:'12' ),
		'mapscroll'=> ( (!empty($evopt1['evcal_gmap_scroll']) && $evopt1['evcal_gmap_scroll']=='yes')?'false':'true'),
		'evc_open'=>'1'
	));
	$_cd = '';
	foreach ($sin_event_evodata as $f=>$v){
		$_cd .='data-'.$f.'="'.$v.'" ';
	}

?>
<div class='eventon_main_section' >
	<div id='evcal_single_event_<?php echo $event_id;?>' class='ajde_evcal_calendar eventon_single_event evo_sin_page <?php echo $rtl?'evortl':'';?>' >
		
		<div class='evo-data' <?php echo $_cd;?>></div>


		<div id='evcal_head' class='calendar_header'><p id='evcal_cur'><?php echo $event_header;?></p></div>
		<div id='evcal_list' class='eventon_events_list evo_sin_event_list'>
		<?php
				
			// repeat event information header
			$eventon_sin_event->frontend->functions->repeat_event_header($repeati, $event_id);

			$content =  $eventon->evo_generator->get_single_event_data($event_id, $lang, $repeati);			
			echo $content[0]['content'];
		?>
		</div>
	</div>
</div>

<div id='primary'>
<?php
	comments_template( '', true );
?>
</div>