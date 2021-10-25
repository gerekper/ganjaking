<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\GT3_Core_Elementor_Control_Query;
use Tribe__Date_Utils as Dates;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Events $widget */

$settings = array(
	'show_type'     => 'type1',
	'post_to_show'  => '3',
	'order'  => 'ASC',
	'show_date'  => 'yes',
	'show_venue'  => 'yes',
	'show_post_button'  => 'yes',
	'show_more_button'  => 'yes',
	'featured_events_only'  => '',
	'items_per_line'  => '3',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

//$events_label_plural = tribe_get_event_label_plural();
$events_label_plural_lowercase = tribe_get_event_label_plural_lowercase();

$post_status = [ 'publish' ];
if ( is_user_logged_in() ) {
	$post_status[] = 'private';
}

$query_args = array(
	'posts_per_page' => $settings['post_to_show'],
	'post_status' => $post_status,
	'offset' => 0,
	'featured' => $settings['featured_events_only'],
	'ends_after' => Dates::build_date_object( 'now' ),
	'orderby' => 'event_date',
	'order' => $settings['order'],
);

$events = Tribe__Events__Query::getEvents( $query_args);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3-tribe-events-list',
	$settings['show_type'],
	($settings['show_type'] !== 'type1') ? 'items'.$settings['items_per_line'] : '',
));

$event_view_btn_text = esc_html__( 'View  more', 'gt3_themes_core' );

if ($settings['show_type'] !== 'type1') {
	$event_view_btn_text = esc_html__( 'Know more', 'gt3_themes_core' );
}

?>

<div <?php $widget->print_render_attribute_string('wrapper') ?>>
	<?php if ($events) { ?>
		<div class="gt3-tribe-events-wrap">
		<?php foreach ( $events as $post ) {
			$post_id = $post->ID;
			$week_day = '<span class="gt3-tribe-event-day">'.tribe_get_start_date( $post_id, false, 'l' ).'</span>';
			/*
			Full date
		    tribe_events_event_schedule_details($post_id);
			*/
			$event_post_thumbnail = get_the_post_thumbnail_url( $post_id, 'large' );
		?>
		<div class="gt3-tribe-item">
			<?php
			if ($settings['show_type'] !== 'type1') {
				echo '<div class="item_wrapper"><div class="item_inner_wrap">';
			}
			// Post thumbnail wrap
			if ($settings['show_type'] == 'type3' && !empty($event_post_thumbnail)) {
				echo '<div class="gt3-tribe-event-image" style="background-image: url(' . $event_post_thumbnail . ');"><a href="' . esc_url( tribe_get_event_link($post_id) ) . '"></a>';
			}
			if ($settings['show_date'] == 'yes') {
				echo '<div class="gt3-tribe-date"><div class="gt3-tribe-day">' . tribe_get_start_date( $post_id, false, 'd' ) . '</div><div class="gt3-tribe-date-info"><div>' . (($settings['show_type'] == 'type3') ? tribe_get_start_date( $post_id, false, 'M' ) : tribe_get_start_date( $post_id, false, 'F' )) .'</div>'.(($settings['show_type'] !== 'type3') ? $week_day : "").'</div></div>';
			}
			// End Post thumbnail wrap
			if ($settings['show_type'] == 'type3' && !empty($event_post_thumbnail)) {
				echo '</div>';
			}
			echo '<div class="gt3-tribe-title"><h4><a href="'. esc_url( tribe_get_event_link($post_id) ) .'">'. get_the_title($post_id) . '</a></h4>'.(($settings['show_type'] == 'type1') ? $week_day : "").'</div>';
			if (($settings['show_venue'] == 'yes') and function_exists( 'tribe_has_venue' ) and tribe_has_venue($post_id)) {
				echo '<div class="gt3-tribe-venue"><div class="gt3-tribe-venue-label">'.tribe_get_venue($post_id).'</div><span><i>' . tribe_get_start_date( $post_id, false, 'G:i' ) . ' - ' .tribe_get_end_date($post_id, false, 'G:i' ) . (($settings['show_type'] !== 'type1') ? ", ".tribe_get_start_date( $post_id, false, 'l' ) : "") . '</i></span></div>';
			}
			if ($settings['show_post_button'] == 'yes') {
				echo '<div class="gt3-tribe-view"><a class="gt3-tribe-button" href="' . esc_url( tribe_get_event_link($post_id) ) . '">' . $event_view_btn_text . '</a></div>';
			}
			if ($settings['show_type'] !== 'type1') {
				echo '</div></div>';
			}
			?>
		</div>
	<?php } ?>
	</div>

	<?php if ($settings['show_more_button'] == 'yes') { ?>
	<div class="gt3-tribe-all-events-link">
		<a href="<?php echo esc_url( tribe_get_events_link() ); ?>" target="_blank"><?php echo esc_html__( 'View More', 'gt3_themes_core' ); ?></a>
	</div>
	<?php } ?>

<?php } else { ?>
	<p><?php printf( esc_html__( 'There are no upcoming %s at this time.', 'gt3_themes_core' ), $events_label_plural_lowercase ); ?></p>
<?php } ?>

</div>
<?php
