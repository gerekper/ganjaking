<?php
/**
 * Theme sidebars.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

/**
 * Sidebar | Add
 */

if (! function_exists('mfn_register_sidebars')) {
	function mfn_register_sidebars()
	{

		// custom sidebars

		$sidebars = mfn_opts_get('sidebars');
		if (is_array($sidebars)) {
			foreach ($sidebars as $sidebar) {
				register_sidebar(array(
					'name' => $sidebar,
					'id' => 'sidebar-'. str_replace('+', '-', urlencode(strtolower(trim($sidebar)))),
					'description'	=> __('Custom sidebar created in Theme Options.', 'betheme'),
					'before_widget' => '<aside id="%1$s" class="widget %2$s">',
					'after_widget' => '</aside>',
					'before_title' => '<h3>',
					'after_title' => '</h3>',
				));
			}
		}

		// footer areas

		for ($i = 1; $i <= 5; $i++) {
			register_sidebar(array(
				'name' => __('Footer', 'mfn-opts') .' | #'.$i,
				'id' => 'footer-area-'.$i,
				'description'	=> __('Appears in the Footer section of the site.', 'betheme'),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h4>',
				'after_title' => '</h4>',
			));
		}

		// sliding top

		for ($i = 1; $i <= 4; $i++) {
			register_sidebar(array(
				'name' => __('Sliding Top', 'mfn-opts') .' | #'.$i,
				'id' => 'top-area-'.$i,
				'description'	=> __('Appears in the Sliding Top section of the site.', 'betheme'),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h4>',
				'after_title' => '</h4>',
			));
		}

		// Page | Search

		register_sidebar(array(
			'name' => __('Page | Search', 'mfn-opts'),
			'id' => 'mfn-search',
			'description'	=> __('Main sidebar for Search page that appears on the right. Leave it empty to use Full Width layout.', 'betheme'),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));

		// Forum | bbPress

		register_sidebar(array(
			'name' => __('Plugin | bbPress', 'mfn-opts'),
			'id' => 'forum',
			'description'	=> __('Main sidebar for bbPress pages that appears on the right. Leave it empty to use Full Width layout.', 'betheme'),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));

		// Forum | BuddyPress

		register_sidebar(array(
			'name' => __('Plugin | BuddyPress', 'mfn-opts'),
			'id' => 'buddy',
			'description'	=> __('Main sidebar for BuddyPress pages that appears on the right. Leave it empty to use Full Width layout.', 'betheme'),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));

		// Events | Events Callendar

		register_sidebar(array(
			'name' => __('Plugin | Events Calendar', 'mfn-opts'),
			'id' => 'events',
			'description'	=> __('Main sidebar for The Events Calendar pages that appears on the right. Leave it empty to use Full Width layout.', 'betheme'),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));

		// Download | Easy Digital Downloads

		register_sidebar(array(
			'name' => __('Plugin | Easy Digital Downloads', 'mfn-opts'),
			'id' => 'edd',
			'description'	=> __('Main sidebar for Easy Digital Downloads single pages that appears on the right. Leave it empty to use Full Width layout.', 'betheme'),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));

		// Shop | WooCommerce

		register_sidebar(array(
			'name' => __('Plugin | WooCommerce', 'mfn-opts'),
			'id' => 'shop',
			'description'	=> __('Main sidebar for WooCommerce pages that appears on the right. Leave it empty to use Full Width layout.', 'betheme'),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
	}
}
add_action('widgets_init', 'mfn_register_sidebars');

/**
 * Sidebar | Add: Categories
 */

if (! function_exists('mfn_register_sidebars_cat')) {
	function mfn_register_sidebars_cat()
	{

		// blog categories

		$categories = get_categories(array(
			'taxonomy' => 'category',
		));

		if (is_array($categories)) {
			foreach ($categories as $category) {
				register_sidebar(array(
					'name' => __('Blog', 'mfn-opts') .' | '. $category->cat_name,
					'id' => 'blog-cat-'. trim($category->slug),
					'description'	=> __('Sidebar for Blog Category. Appears only when you select sidebar for Blog.', 'betheme'),
					'before_widget' => '<aside id="%1$s" class="widget %2$s">',
					'after_widget' => '</aside>',
					'before_title' => '<h4>',
					'after_title' => '</h4>',
				));
			}
		}

		// portfolio categories

		$post_types_disable = mfn_opts_get('post-type-disable');

		if (! isset($post_types_disable['portfolio'])) {

			$categories = get_categories(array(
				'taxonomy' => 'portfolio-types',
			));

			if (is_array($categories)) {
				foreach ($categories as $category) {
					register_sidebar(array(
						'name' => __('Portfolio', 'mfn-opts') .' | '. $category->cat_name,
						'id' => 'portfolio-cat-'. trim($category->slug),
						'description'	=> __('Appears on Portfolio Category Page.', 'betheme'),
						'before_widget' => '<aside id="%1$s" class="widget %2$s">',
						'after_widget' => '</aside>',
						'before_title' => '<h4>',
						'after_title' => '</h4>',
					));
				}
			}

		}

	}
}

$theme_disable = mfn_opts_get('theme-disable');
if (! isset($theme_disable['categories-sidebars'])) {
	add_action('init', 'mfn_register_sidebars_cat');	// get_categories can be call only on init
}
