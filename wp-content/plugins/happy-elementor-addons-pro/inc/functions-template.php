<?php

/**
 * Template functions and defination
 *
 */
defined('ABSPATH') || exit;

/**
 * Print the first category from a post
 *
 * @param array $args {
 * 		int $post_id
 * 		string $class
 * 	}
 * @return void
 */
function hapro_the_first_category($post_id = null, $args = [])
{
	$args = wp_parse_args($args, [
		'class' => 'ha-tiles__tile-tag',
	]);

	if (empty($post_id)) {
		$post_id = get_the_ID();
	}

	$categories = wp_get_post_terms($post_id, 'category', [
		'fields' => 'id=>name'
	]);

	if (is_wp_error($categories) || empty($categories)) {
		return;
	}

	printf(
		'<a href="%s" rel="tag" class="%s">%s</a>',
		esc_url(get_term_link(key($categories))),
		esc_attr($args['class']),
		esc_html(current($categories))
	);
}

/**
 * Get post excerpt by length
 *
 * @param integer $length
 * @return string
 */
function ha_pro_get_excerpt($post_id = null, $length = 15)
{
	if (empty($post_id)) {
		$post_id = get_the_ID();
	}

	return wp_trim_words(get_the_excerpt($post_id), $length);
}

/**
 * Get post date link
 *
 * @param int $post_id
 * @return string
 */
function ha_pro_get_date_link($post_id = null)
{
	if (empty($post_id)) {
		$post_id = get_the_ID();
	}

	$year = get_the_date('Y', $post_id);
	$month = get_the_time('m', $post_id);
	$day = get_the_time('d', $post_id);
	$url = get_day_link($year, $month, $day);

	return $url;
}

/**
 * Get taxonomy
 *
 * @return array
 */
function ha_pro_get_taxonomies()
{
	$taxonomies = get_taxonomies(['show_in_nav_menus' => true], 'objects');

	$options = ['' => ''];

	foreach ($taxonomies as $taxonomy) {
		$options[$taxonomy->name] = $taxonomy->label;
	}

	return $options;
}

/**
 * Print the first Taxonomoy under specific post id
 *
 * @param number string
 * @param string
 * @return void
 */
function ha_pro_the_first_taxonomy($post_id = null, $taxonomy_id = null, $args = [])
{
	if (empty($taxonomy_id)) {
		return;
	}
	if (empty($post_id)) {
		$post_id = get_the_ID();
	}


	$args = wp_parse_args($args, [
		'class' => 'ha-pg-badge-taxonomy',
	]);

	$terms = get_the_terms($post_id, $taxonomy_id);
	if (empty($terms[0])) {
		return;
	}


	return sprintf(
		'<a href="%s" class="%s" rel="tag">%s</a>',
		esc_url(get_term_link($terms[0]->term_id)),
		esc_attr($args['class']),
		esc_html($terms[0]->name)
	);
}

function ha_pro_get_elementor_templates()
{
	// $args = [
	// 	'post_type' => 'elementor_library',
	// 	'post_status' => 'publish',
	// 	'posts_per_page' => -1,
	// 	'orderby' => 'ID',
	// 	'order' => 'DESC',
	// ];

	// $loop = new WP_Query($args);

	// wp_reset_postdata();

	// return $loop->posts;

	$elementor_templates = [];

	$posts = get_posts([
		'post_type' => 'elementor_library',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby' => 'ID',
		'order'    => 'DESC',
	]);

	foreach ($posts as $post) {
		$elementor_templates[$post->ID] = $post->post_title;
	}

	wp_reset_postdata();

	return $elementor_templates;
}
