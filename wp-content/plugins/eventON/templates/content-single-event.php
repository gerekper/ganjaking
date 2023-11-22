<?php
/**
 * The template for displaying event content within WP loop.
 *
 * Override this template by copying it to .../yourtheme/eventon/content-single-event.php
 *
 * @author 		AJDE
 * @package 	EventON/Templates
 * @version    	4.1.2
 */
 	
defined( 'ABSPATH' ) || exit;

global $event;

do_action('eventon_before_single_event');

?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<?php 
	/*
	 *	before event summary content
	 */
	do_action('eventon_before_single_event_summary');?>	

	<div class='eventon_main_section summary entry-summary' >

		<?php do_action('eventon_oneevent_wrapper');?>			
		<?php do_action('eventon_oneevent_evodata');?>		
		<?php do_action('eventon_oneevent_head');?>	

			
		<?php do_action('eventon_before_event_content');?>
		
		<div id='evcal_list' class='eventon_events_list evo_sin_event_list'>
		<?php
			
			/**
			 * Get event repeat information header informaion - DEP
			 */
			do_action('eventon_oneevent_repeat_header');

			/**
			 * Get event data - DEP
			 */
			do_action('eventon_oneevent_event_data');

			/**
			* Main Event summary content
			* event page repeat header
			* event summary
			*/
			do_action('eventon_single_event_summary');

		?>
		</div>

		<?php do_action('eventon_after_event_content');?>


	</div>

	<?php 
	/*
	 *	after event summary content
	 */
	do_action('eventon_after_single_event_summary');?>	

</div>


<?php do_action('eventon_after_single_event');