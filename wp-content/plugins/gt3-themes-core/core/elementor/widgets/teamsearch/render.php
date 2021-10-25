<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_TeamSearch $widget */

$settings = $widget->get_settings();

$compile = '';

$widget->add_render_attribute('wrapper', 'class', 'gt3_team_search');

$team_slug = function_exists( 'gt3_option' ) ? gt3_option( 'team_slug' ) : '';

global $wp_query;

$query_args = array(
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'post_type' => 'team',
    'orderby' => 'date',
);

$tax_query = array();

$team_name = get_query_var(apply_filters( "gt3_team_category_search_slug_filter", 's'));
$category = get_query_var(apply_filters( "gt3_team_category_search_slug_filter", 'specialty'));
$location = get_query_var(apply_filters( "gt3_team_location_search_slug_filter", 'location'));

if (!empty($team_name)) {
    $query_args['name'] = $team_name;
}

if (!empty($category)) {
    $tax_query[] = array(
        'taxonomy' => 'team_category',
        'field'    => 'slug',
        'terms'    => $category
    );
}

if (!empty($location)) {
    $tax_query[] = array(
        'taxonomy' => 'team_location',
        'field'    => 'slug',
        'terms'    => $location
    );
}

if (!empty($tax_query)) {
    $query_args['tax_query'] = $tax_query;
}

$team_query = new \WP_Query( $query_args );
if($team_query->have_posts()) {
	$post_ids = wp_list_pluck( $team_query->posts, 'ID' );
	$post_titles = wp_list_pluck( $team_query->posts, 'post_title' );
	$post_slugs = wp_list_pluck( $team_query->posts, 'post_name' );
	wp_reset_postdata();
}

if (!empty($post_ids)) {
	$cats = get_terms( 'team_category', array(
		'hide_empty' => true,
		'object_ids' => $post_ids
	));

	$locations = get_terms( 'team_location', array(
		'hide_empty' => true,
		'object_ids' => $post_ids
	));
}else{
	$cats = '';
	$locations = '';
}


?>
<form action="<?php echo esc_url( home_url('/') ); ?>" method="GET" role="search" class="gt3_team_search"<?php
	if (!empty($team_name)) {
		echo " data_search_".apply_filters( "gt3_team_category_search_slug_filter", 's').'="'.esc_attr($team_name).'"';
	}
	if (!empty($category)) {
		echo " data_search_".apply_filters( "gt3_team_category_search_slug_filter", 'specialty').'="'.esc_attr($category).'"';
	}
	if (!empty($location)) {
		echo " data_search_".apply_filters( "gt3_team_location_search_slug_filter", 'location').'="'.esc_attr($location).'"';
	}
?>>
	<input type="hidden" name="type" value="<?php echo (!empty($team_slug) ? $team_slug : 'team'); ?>" />
	<?php
	if(!empty($settings['team_categories']) && (bool) $settings['team_categories']){
	?>
	<div class="search_box search_box-2"><select name="<?php echo apply_filters( "gt3_team_category_search_slug_filter", 'specialty'); ?>" <?php
		if (!empty($settings['team_category_placeholder'])) {
			echo 'data-placeholder="' . esc_html($settings['team_category_placeholder']) . '"';
		}
	?>><?php
	if (!empty($category) && !empty($cats[0])) {
		foreach ($cats as $cat) {
			if ($cat->slug === $category) {
				echo '<option value="'.esc_attr($category).'" selected="selected">'.esc_html($cat->name).'</option>';
			}
		}
	}
	?></select></div>
	<?php }

	if(!empty($settings['team_names']) && (bool) $settings['team_names']){  ?>
		<div class="search_box search_box-1"><select name="<?php echo apply_filters( "gt3_team_category_search_slug_filter", 's'); ?>" <?php
			if (!empty($settings['team_names_placeholder'])) {
				echo 'data-placeholder="' . esc_html($settings['team_names_placeholder']) . '"';
			}
			?>><?php
				if (!empty($team_name) && !empty($post_titles[0])) {
					echo '<option value="'.esc_attr($team_name).'" selected="selected">'.esc_html($post_titles[0]).'</option>';
				}else{
					echo '<option value="" selected="selected"></option>';
				}
				?></select></div>
	<?php }



	if(!empty($settings['team_locations']) && (bool) $settings['team_locations']){
	?>
	<div class="search_box search_box-3" style="position: relative;"><select style="width:100%" name="<?php echo apply_filters( "gt3_team_location_search_slug_filter", 'location'); ?>" <?php
		if (!empty($settings['team_location_placeholder'])) {
			echo 'data-placeholder="' . esc_html($settings['team_location_placeholder']) . '"';
		}
	?>><?php
	if (!empty($location) && !empty($locations[0])) {
		foreach ($locations as $location_item) {
			if ($location_item->slug === $location) {
				echo '<option value="'.esc_attr($location).'" selected="selected">'.esc_html($location_item->name).'</option>';
			}
		}
	}
	?></select></div>
	<?php } ?>
	<div class="submit_box"><button type="submit" value="Submit" class="button"><?php echo !empty($settings['team_search_button_text']) ? $settings['team_search_button_text'] : ""; ?></button></div>
</form>
