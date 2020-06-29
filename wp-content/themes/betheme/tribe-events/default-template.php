<?php
/**
 * Default Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Template -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
 *
 * @package TribeEventsCalendar
 * @since  3.0
 * @author Modern Tribe Inc.
 */

if (!defined('ABSPATH')) {
	die('-1');
}

get_header();
?>

<!-- #Content -->
<div id="Content">
	<div class="content_wrapper clearfix">

		<!-- .sections_group -->
		<div class="sections_group">
			<div class="section the_content">
				<div class="section_wrapper">
					<div class="the_content_wrapper">
						<?php tribe_events_before_html(); ?>
							<?php tribe_get_view(); ?>
						<?php tribe_events_after_html(); ?>
					</div>
				</div>
			</div>
		</div>

		<!-- .four-columns - sidebar -->
		<?php if (is_active_sidebar('events')):  ?>
			<div class="sidebar four columns">
				<div class="widget-area clearfix <?php echo esc_attr(mfn_opts_get('sidebar-lines')); ?>">
					<?php dynamic_sidebar('events'); ?>
				</div>
			</div>
		<?php endif; ?>

	</div>
</div>

<?php get_footer();
