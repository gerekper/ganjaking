<?php 
/**
 * EventCard Health html content
 * @4.5.9
 */



$EVENT->localize_edata('_edata');

?>
<div class='evo_metarow_health evorow evcal_evdata_row'>
	<span class='evcal_evdata_icons'><i class='fa <?php echo get_eventON_icon('evcal__fai_health', 'fa-heartbeat',$evOPT );?>'></i></span>
	<div class='evcal_evdata_cell'>
		<h3 class='evo_h3'><?php echo evo_lang('Health Guidelines for this Event');?></h3>
		<div class='evcal_cell'>
			<div class='evo_card_health_boxes'>
			<?php

			foreach(apply_filters('evo_healthcaredata_frontend', array(
				'_health_mask'=> array('svg','mask', evo_lang('Masks Required')),
				'_health_temp'=> array('i','thermometer-half', evo_lang('Temperature Checked At Entrance')),
				'_health_pdis'=> array('svg','distance', evo_lang('Physical Distance Maintained')),
				'_health_san'=> array('i','clinic-medical', evo_lang('Event Area Sanitized')),
				'_health_out'=> array('i','tree', evo_lang('Outdoor Event')),
				'_health_vac'=> array('i','syringe', evo_lang('Vaccination Required')),
			), $EVENT) as $k=>$v){

				if(!$EVENT->echeck_yn( $k )) continue;
				
				echo "<div class='evo_health_b_o'><div class='evo_health_b'>";
			 	if($v[0]=='svg')
					echo "<svg class='evo_svg_icon' enable-background='new 0 0 20 20' height='20' viewBox='0 0 512 512' width='20' xmlns='http://www.w3.org/2000/svg'>". EVO()->elements->svg->get_icon_path( $v[1]) ."</svg>";
				if($v[0]=='i') echo "<i class='fa fa-{$v[1]}'></i>";
				echo "<span>". $v[2] ."</span>
				</div></div>";
			}

			?>

			</div>

			<?php if($EVENT->get_eprop('_health_other')):?>
			<div class='evo_health_b ehb_other'>
				<span class='evo_health_bo_title'>
					<i class='fa fa-laptop-medical'></i><span><?php evo_lang_e('Other Health Guidelines');?></span>
				</span>
				<span class='evo_health_bo_data'><?php echo $EVENT->get_eprop('_health_other');?></span>
			</div>
			<?php endif;?>
				

		</div>
	</div>
</div>
<?php 