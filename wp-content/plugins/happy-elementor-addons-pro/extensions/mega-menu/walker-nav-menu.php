<?php

namespace Happy_Addons_Pro;

use Happy_Addons_Pro\Extension\Mega_Menu;

class Happy_Menu_Walker extends \Walker_Nav_Menu {
	/**
	 * @var mixed
	 */
	public $menu_Settings;


	/**
	 * @param $menu_item_id
	 */
	public function get_item_meta($menu_item_id) {
		$meta_key = Mega_Menu::$menuitem_settings_key;
		$data     = get_post_meta($menu_item_id, $meta_key, true);
		$data     = (array) json_decode($data);

		$default = [
			"menu_id"                         => null,
			"menu_has_child"                  => '',
			"menu_enable"                     => 0,
			"menu_icon"                       => '',
			"menu_icon_color"                 => '',
			"menu_badge_text"                 => '',
			"menu_badge_color"                => '',
			"menu_badge_background"           => '',
			"mobile_submenu_content_type"     => 'builder_content',
			"vertical_megamenu_position_type" => 'relative_position',
			"vertical_menu_width"             => '',
			"megamenu_width_type"             => 'default_width',
		];
		return array_merge($default, $data);
	}

	/**
	 * @param $menu_slug
	 * @return mixed
	 */
	public function is_megamenu($menu_slug) {
		$menu_slug = (((gettype($menu_slug) == 'object') && (isset($menu_slug->slug))) ? $menu_slug->slug : $menu_slug);

		$cache_key = 'ha_megamenu_data_' . $menu_slug;
		$cached    = wp_cache_get($cache_key);
		if (false !== $cached) {
			return $cached;
		}

		$return = 0;

		$settings = ha_get_option(Mega_Menu::$megamenu_settings_key, []);
		$term     = get_term_by('slug', $menu_slug, 'nav_menu');

		if (
			isset($term->term_id)
			&& isset($settings['menu_location_' . $term->term_id])
			&& $settings['menu_location_' . $term->term_id]['is_enabled'] == '1'
		) {

			$return = 1;
		}

		wp_cache_set($cache_key, $return);
		return $return;
	}

	/**
	 * @param $item_meta
	 * @param $menu
	 */
	public function is_megamenu_item($item_meta, $menu) {
		if ($this->is_megamenu($menu) == 1 && $item_meta['menu_enable'] == 1 && class_exists('Elementor\Plugin')) {
			return true;
		}
		return false;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 *
	 *
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function start_lvl(&$output, $depth = 0, $args = array()) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"ha-dropdown ha-submenu-panel\">\n";
	}
	/**
	 * Ends the list of after the elements are added.
	 *
	 *
	 *
	 * @see Walker::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function end_lvl(&$output, $depth = 0, $args = array()) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}
	/**
	 * Start the element output.
	 *
	 *
	 *
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		$indent    = ($depth) ? str_repeat("\t", $depth) : '';
		$classes   = empty($item->classes) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$subIndicatorFileName = esc_attr($args->sub_indicator);

		// if(!$subIndicatorFileName){
		// 	$subIndicatorFileName = 'caret-1';
		// }

		$subIndicatorSVG = file_get_contents(HAPPY_ADDONS_PRO_DIR_PATH . 'assets/imgs/indicators/' . $subIndicatorFileName . '.svg');

		/**
		 * Filter the CSS class(es) applied to a menu item's list item element.
		 *
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
		// New
		$class_names .= ' nav-item';
		$item_meta        = $this->get_item_meta($item->ID);
		$is_megamenu_item = $this->is_megamenu_item($item_meta, $args->menu);

		if (in_array('menu-item-has-children', $classes) || $is_megamenu_item == true) {
			$class_names .= ' ha-dropdown-has ' . $item_meta['vertical_megamenu_position_type'] . ' ha-dropdown-menu-' . $item_meta['megamenu_width_type'] . '';
		}

		if ($is_megamenu_item == true) {
			$class_names .= ' ha-megamenu-has';
		}

		if ($item_meta['mobile_submenu_content_type'] == 'builder_content') {
			$class_names .= ' ha-mobile-builder-content';
		}

		if (in_array('current-menu-item', $classes)) {
			$class_names .= ' active';
		}

		// if ( $is_megamenu_item == true && $item_meta['mobile_submenu_content_type'] != 'builder_content' ){
		// 	$class_names .= ' ha-megamenu-hide';
		// }

		$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's list item element.
		 *
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
		$id = $id ? ' id="' . esc_attr($id) . '"' : '';
		// New
		$data_attr = '';
		switch ($item_meta['megamenu_width_type']) {
			case 'default_width':
				$data_attr = esc_attr(' data-vertical-menu=750px');
				break;

			case 'full_width':
				$data_attr = ' data-vertical-menu=""';
				break;

			case 'custom_width':
				$data_attr = $item_meta['vertical_menu_width'] === '' ? esc_attr(' data-vertical-menu=750px') : esc_attr(' data-vertical-menu=' . $item_meta['vertical_menu_width'] . '');
				break;

			default:
				$data_attr = esc_attr(' data-vertical-menu=750px');
				break;
		}
		//
		$output .= $indent . '<li' . $id . $class_names . $data_attr . '>';
		$atts           = array();
		$atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
		$atts['target'] = !empty($item->target) ? $item->target : '';
		$atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
		$atts['href']   = !empty($item->url) ? $item->url : '';

		$submenu_indicator = '';

		// New
		if ($depth === 0) {
			$atts['class'] = 'ha-menu-nav-link';
		}
		if ($depth === 0 && (in_array('menu-item-has-children', $classes) || ($is_megamenu_item == true))) {
			$atts['class'] .= ' ha-menu-dropdown-toggle';
		}
		if (in_array('menu-item-has-children', $classes) || ($is_megamenu_item == true) || ($is_megamenu_item == true && $item_meta['mobile_submenu_content_type'] == 'builder_content')) {
			$submenu_indicator .= '<span class="ha-submenu-indicator-wrap"> ' . $subIndicatorSVG . '</span>';
		}
		if ($depth > 0) {
			$manual_class  = array_values($classes)[0] . ' ' . 'dropdown-item';
			$atts['class'] = $manual_class;
		}
		if (in_array('current-menu-item', $item->classes)) {
			$atts['class'] .= ' active';
		}


		/**
		 * Filter the HTML attributes applied to a menu item's anchor element.
		 *
		 *
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 * @type  string $title  Title attribute.
		 * @type  string $target Target attribute.
		 * @type  string $rel    The rel attribute.
		 * @type  string $href   The href attribute.
		 * }
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array  $atts   {
		 * @param object $item   The current menu item.
		 * @param array  $args   An array of {@see wp_nav_menu()} arguments.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 */
		$atts       = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
		$attributes = '';
		foreach ($atts as $attr => $value) {
			if (!empty($value)) {
				$value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$item_output = $args->before;
		// New

		//
		$item_output .= '<a' . $attributes . '>';

		if ($this->is_megamenu($args->menu) == 1) {
			// add badge text
			if ($item_meta['menu_badge_text'] != '') {
				$badge_style        = 'background:' . $item_meta['menu_badge_background'] . '; color:' . $item_meta['menu_badge_color'];

				if ($item_meta['menu_badge_radius'] != '') {
					$rad = explode(',', $item_meta['menu_badge_radius']);
					if ($rad[0]) {
						$badge_style .= ';border-top-left-radius:' . $rad[0] . 'px';
					}
					if ($rad[1]) {
						$badge_style .= ';border-top-right-radius:' . $rad[1] . 'px';
					}
					if ($rad[2]) {
						$badge_style .= ';border-bottom-left-radius:' . $rad[2] . 'px';
					}
					if ($rad[3]) {
						$badge_style .= ';border-bottom-right-radius:' . $rad[3] . 'px';
					}
				}

				$badge_carret_style = 'border-top-color:' . $item_meta['menu_badge_background'];
				$item_output .= '<span style="' . $badge_style . '" class="ha-menu-badge">' . $item_meta['menu_badge_text'] . '<i style="' . $badge_carret_style . '" class="ha-menu-badge-arrow"></i></span>';
			}

			// add menu icon & style
			if ($item_meta['menu_icon'] != '') {
				$icon_style = 'color:' . $item_meta['menu_icon_color'];
				$item_output .= '<i class="ha-menu-icon ' . $item_meta['menu_icon'] . '" style="' . $icon_style . '" ></i>';
			}
		}

		/**
		 * This filter is documented in wp-includes/post-template.php
		 */
		$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
		$item_output .= $submenu_indicator . '</a>';
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 *
		 * @since 3.0.0
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
		 */
		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}
	/**
	 * Ends the element output, if needed.
	 *
	 *
	 *
	 * @see Walker::end_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Page data object. Not used.
	 * @param int    $depth  Depth of page. Not Used.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function end_el(&$output, $item, $depth = 0, $args = array()) {
		if ($depth === 0) {
			if ($this->is_megamenu($args->menu) == 1) {
				$item_meta = $this->get_item_meta($item->ID);

				if ($item_meta['menu_enable'] == 1 && class_exists('Elementor\Plugin')) {
					$builder_post_title = 'ha-megamenu-content-' . $item->ID;

					$query = new \WP_Query(
						array(
							'post_type'              => 'ha_nav_content',
							'title'                  => $builder_post_title,
							'post_status'            => 'all',
							'posts_per_page'         => 1,
							'no_found_rows'          => true,
							'ignore_sticky_posts'    => true,
							'update_post_term_cache' => false,
							'update_post_meta_cache' => false,
							'orderby'                => 'post_date ID',
							'order'                  => 'ASC',
						)
					);
					 
					if ( ! empty( $query->post ) ) {
						$builder_post = $query->post;
					} else {
						$builder_post = null;
						// $builder_post       = get_page_by_title($builder_post_title, OBJECT, 'ha_nav_content');
					}

					$output .= '<ul class="ha-megamenu-panel">';

					if ($builder_post != null) {
						// Elementor Instance
						$elementor = \Elementor\Plugin::instance();

						// Check if using elementor
						$data = $this->query_elementor($elementor, $builder_post->ID);
						
						// List all Used Widgets
						$widgetUsed = [];
						$templates = [];
						if( !empty( $data ) && is_array( $data ) ) {
							array_walk_recursive($data, function ($v, $k) use (&$widgetUsed, &$templates) {
								if ($k == 'template_id') {
									$templates[] = $v;
								}
								if ($k == 'widgetType') {
									$widgetUsed[] = $v;
								}
							});
						}

						if ($templates) {
							foreach ($templates as $template) {
								$tplData = $this->query_elementor($elementor, $template);
								if( !empty( $tplData ) && is_array( $tplData ) ) {
									array_walk_recursive($tplData, function ($v, $k) use (&$widgetUsed, &$templates) {
										if ($k == 'template_id') {
											$templates[] = $v;
										}
										if ($k == 'widgetType') {
											$widgetUsed[] = $v;
										}
									});
								}
							}
						}

						// Check For MegaMenu & Avoid Recursion
						if (in_array('ha-nav-menu', $widgetUsed)) {
							$output .= '<div class="elementor-alert elementor-alert-danger">' . esc_html__('Invalid Data: You can\'t use Happy Mega Menu inside a Happy Mega Menu.', 'happy-addons-pro') . '</div>';
						} else {
							$output .= $elementor->frontend->get_builder_content_for_display($builder_post->ID, true);
						}
					} else {
						$output .= esc_html__('No content found', 'happy-addons-pro');
					}

					$output .= '</ul>';
				}
			}
			$output .= "</li>\n";
		}
	}

	private function query_elementor($elementor, $post_id) {
		$document = $elementor->documents->get_doc_for_frontend($post_id);
		if (!$document || !$document->is_built_with_elementor()) {
			return '';
		}

		// Change the current post, so widgets can use `documents->get_current`.
		$elementor->documents->switch_to_document($document);
		$data = $document->get_elements_data();

		return $data;
	}
}
