<?php	
/*
 *	The template for displaying single event
 *
 *	Override this tempalte by coping it to ....yourtheme/eventon/single-ajde_events.php
 *	This template is built based on wordpress twentythirteen theme standards and may not fit your custom
 *	theme correctly, in which case you may have to add custom styles to fix style issues
 *
 *	@Author: AJDE
 *	@EventON
 *	@version: 4.5.5
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {	exit; }	

do_action('eventon_before_header');	


// you can also pass a lang value in below function to create fixed lang single event page.
// this value will be overriden by language corresponding events
do_action('eventon_before_main_content');

?>	
<div id='main'>
	<div class='evo_page_body'>

		<?php do_action('eventon_single_content_wrapper');?>

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php evo_get_template_part('content','single-event');?>
				
			<?php endwhile; ?>	

		<?php	do_action('eventon_single_sidebar');	// DEP ?>

		<?php	do_action('eventon_single_after_loop');	?>

	</div><!-- #primary -->	

</div>	

<?php 	do_action('eventon_after_main_content'); 
