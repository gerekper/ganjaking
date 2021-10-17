<?php
/**
 * The template for displaying event content within WP loop.
 *
 * Override this template by copying it to .../yourtheme/eventon/content-single-event.php
 *
 * @author 		AJDE
 * @package 	EventON/Templates
 * @version    	2.5.4
 */
 	

?>
<div class='eventon_main_section' >
	<?php do_action('eventon_oneevent_wrapper');?>
		
		<?php do_action('eventon_oneevent_evodata');?>
		
		<?php do_action('eventon_oneevent_head');?>		

		
		<div id='evcal_list' class='eventon_events_list evo_sin_event_list'>
		<?php
			
			/**
			 * Get event repeat information header informaion
			 */
			do_action('eventon_oneevent_repeat_header');

			/**
			 * Get event data
			 */
			do_action('eventon_oneevent_event_data');

		?>
		</div>
	</div>
</div>
