<?php

namespace MasterAddons\Inc\Helper;

use \Elementor\Icons_Manager;

class Master_Addons_Helper
{

	public static function jltma_elementor()
	{
		return \Elementor\Plugin::$instance;
	}

	/**
	 * Check if Woocommerce is installed and active
	 *
	 * @since 1.5.7
	 */
	public static function is_woocommerce_active()
	{
		return in_array(
			'woocommerce/woocommerce.php',
			apply_filters('active_plugins', get_option('active_plugins'))
		);
	}

	public static function jltma_is_edit_mode()
	{
		if (self::jltma_elementor()->preview->is_preview_mode() || self::jltma_elementor()->editor->is_edit_mode()) {
			return true;
		}
		return false;
	}


	/**
	 * Retrive the list of Contact Form 7 Forms [ if plugin activated ]
	 */

	public static function maad_el_retrive_cf7()
	{
		if (function_exists('wpcf7')) {
			$wpcf7_form_list = get_posts(array(
				'post_type' => 'wpcf7_contact_form',
				'showposts' => 999,
			));
			$options = array();
			$options[0] = esc_html__('Select a Form', MELA_TD);
			if (!empty($wpcf7_form_list) && !is_wp_error($wpcf7_form_list)) {
				foreach ($wpcf7_form_list as $post) {
					$options[$post->ID] = $post->post_title;
				}
			} else {
				$options[0] = esc_html__('Create a Form First', MELA_TD);
			}
			return $options;
		}
	}

	public static function get_page_template_options($type = '')
	{

		$page_templates = self::ma_get_page_templates($type);

		$options[-1]   = __('Select', MELA_TD);

		if (count($page_templates)) {
			foreach ($page_templates as $id => $name) {
				$options[$id] = $name;
			}
		} else {
			$options['no_template'] = __('No saved templates found!', MELA_TD);
		}

		return $options;
	}


	public static function ma_get_page_templates($type = '')
	{
		$args = [
			'post_type'         => 'elementor_library',
			'posts_per_page'    => -1,
		];

		if ($type) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'elementor_library_type',
					'field'    => 'slug',
					'terms' => $type,
				]
			];
		}

		$page_templates = get_posts($args);

		$options = array();

		if (!empty($page_templates) && !is_wp_error($page_templates)) {
			foreach ($page_templates as $post) {
				$options[$post->ID] = $post->post_title;
			}
		}
		return $options;
	}


	// Get all forms of Ninja Forms plugin
	public static function ma_el_get_ninja_forms()
	{
		if (class_exists('Ninja_Forms')) {
			$options = array();

			$contact_forms = Ninja_Forms()->form()->get_forms();

			if (!empty($contact_forms) && !is_wp_error($contact_forms)) {

				$i = 0;

				foreach ($contact_forms as $form) {
					if ($i == 0) {
						$options[0] = esc_html__('Select a Contact form', MELA_TD);
					}
					$options[$form->get_id()] = $form->get_setting('title');
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}


	// Get all forms of WPForms plugin
	public static function ma_el_get_wpforms_forms()
	{
		if (class_exists('WPForms')) {
			$options = array();

			$args = array(
				'post_type'         => 'wpforms',
				'posts_per_page'    => -1
			);

			$contact_forms = get_posts($args);

			if (!empty($contact_forms) && !is_wp_error($contact_forms)) {

				$i = 0;

				foreach ($contact_forms as $post) {
					if ($i == 0) {
						$options[0] = esc_html__('Select a Contact form', MELA_TD);
					}
					$options[$post->ID] = $post->post_title;
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}


	// get weForms
	public static function ma_el_get_weforms()
	{
		$wpuf_form_list = get_posts(array(
			'post_type' => 'wpuf_contact_form',
			'showposts' => 999,
		));

		$options = array();

		if (!empty($wpuf_form_list) && !is_wp_error($wpuf_form_list)) {
			$options[0] = esc_html__('Select weForm', MELA_TD);
			foreach ($wpuf_form_list as $post) {
				$options[$post->ID] = $post->post_title;
			}
		} else {
			$options[0] = esc_html__('Create a Form First', MELA_TD);
		}

		return $options;
	}

	// Get forms of Caldera plugin
	public static function ma_el_get_caldera_forms()
	{
		if (class_exists('Caldera_Forms')) {
			$options = array();

			$contact_forms = \Caldera_Forms_Forms::get_forms(true, true);

			if (!empty($contact_forms) && !is_wp_error($contact_forms)) {

				$i = 0;

				foreach ($contact_forms as $form) {
					if ($i == 0) {
						$options[0] = esc_html__('Select a Contact form', MELA_TD);
					}
					$options[$form['ID']] = $form['name'];
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}


	// Get forms of Gravity Forms plugin
	public static function ma_el_get_gravity_forms()
	{
		if (class_exists('GFCommon')) {
			$options = array();

			$contact_forms = \RGFormsModel::get_forms(null, 'title');

			if (!empty($contact_forms) && !is_wp_error($contact_forms)) {

				$i = 0;

				foreach ($contact_forms as $form) {
					if ($i == 0) {
						$options[0] = esc_html__('Select a Contact form', MELA_TD);
					}
					$options[$form->id] = $form->title;
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}

	// Heading Tags
	public static function ma_el_heading_tags()
	{
		$heading_tags = [
			'h1'   => esc_html__('H1', MELA_TD),
			'h2'   => esc_html__('H2', MELA_TD),
			'h3'   => esc_html__('H3', MELA_TD),
			'h4'   => esc_html__('H4', MELA_TD),
			'h5'   => esc_html__('H5', MELA_TD),
			'h6'   => esc_html__('H6', MELA_TD)
		];

		return $heading_tags;
	}

	// Title Tags
	public static function ma_el_title_tags()
	{
		$title_tags = [
			'h1'   => esc_html__('H1', MELA_TD),
			'h2'   => esc_html__('H2', MELA_TD),
			'h3'   => esc_html__('H3', MELA_TD),
			'h4'   => esc_html__('H4', MELA_TD),
			'h5'   => esc_html__('H5', MELA_TD),
			'h6'   => esc_html__('H6', MELA_TD),
			'div'  => esc_html__('div', MELA_TD),
			'span' => esc_html__('span', MELA_TD),
			'p'    => esc_html__('p', MELA_TD),
		];

		return $title_tags;
	}


	// Master Addons Position
	public static function ma_el_content_positions()
	{
		$position_options = [
			''              => esc_html__('Default', MELA_TD),
			'top-left'      => esc_html__('Top Left', MELA_TD),
			'top-center'    => esc_html__('Top Center', MELA_TD),
			'top-right'     => esc_html__('Top Right', MELA_TD),
			'center'        => esc_html__('Center', MELA_TD),
			'center-left'   => esc_html__('Center Left', MELA_TD),
			'center-right'  => esc_html__('Center Right', MELA_TD),
			'bottom-left'   => esc_html__('Bottom Left', MELA_TD),
			'bottom-center' => esc_html__('Bottom Center', MELA_TD),
			'bottom-right'  => esc_html__('Bottom Right', MELA_TD),
		];

		return $position_options;
	}



	// Master Addons Transition
	public static function ma_el_transition_options()
	{
		$transition_options = [
			''                    => __('None', MELA_TD),
			'fade'                => __('Fade', MELA_TD),
			'scale-up'            => __('Scale Up', MELA_TD),
			'scale-down'          => __('Scale Down', MELA_TD),
			'slide-top'           => __('Slide Top', MELA_TD),
			'slide-bottom'        => __('Slide Bottom', MELA_TD),
			'slide-left'          => __('Slide Left', MELA_TD),
			'slide-right'         => __('Slide Right', MELA_TD),
			'slide-top-small'     => __('Slide Top Small', MELA_TD),
			'slide-bottom-small'  => __('Slide Bottom Small', MELA_TD),
			'slide-left-small'    => __('Slide Left Small', MELA_TD),
			'slide-right-small'   => __('Slide Right Small', MELA_TD),
			'slide-top-medium'    => __('Slide Top Medium', MELA_TD),
			'slide-bottom-medium' => __('Slide Bottom Medium', MELA_TD),
			'slide-left-medium'   => __('Slide Left Medium', MELA_TD),
			'slide-right-medium'  => __('Slide Right Medium', MELA_TD),
		];

		return $transition_options;
	}


	// Master Addons Animations
	public static function jltma_animation_options()
	{
		$transition_options = [
			''                             =>  esc_html__('None', MELA_TD),
			'jltma-fade-in'                =>  esc_html__('Fade In', MELA_TD),
			'jltma-fade-in-down'           =>  esc_html__('Fade In Down', MELA_TD),
			'jltma-fade-in-down-1'         =>  esc_html__('Fade In Down 1', MELA_TD),
			'jltma-fade-in-down-2'         =>  esc_html__('Fade In Down 2', MELA_TD),
			'jltma-fade-in-up'             =>  esc_html__('Fade In Up', MELA_TD),
			'jltma-fade-in-up-1'           =>  esc_html__('Fade In Up 1', MELA_TD),
			'jltma-fade-in-up-2'           =>  esc_html__('Fade In Up 2', MELA_TD),
			'jltma-fade-in-left'           =>  esc_html__('Fade In Left', MELA_TD),
			'jltma-fade-in-left-1'         =>  esc_html__('Fade In Left 1', MELA_TD),
			'jltma-fade-in-left-2'         =>  esc_html__('Fade In Left 2', MELA_TD),
			'jltma-fade-in-right'          =>  esc_html__('Fade In Right', MELA_TD),
			'jltma-fade-in-right-1'        =>  esc_html__('Fade In Right 1', MELA_TD),
			'jltma-fade-in-right-2'        =>  esc_html__('Fade In Right 2', MELA_TD),

			// Slide Animation
			'jltma-slide-from-right'       =>  esc_html__('Slide From Right', MELA_TD),
			'jltma-slide-from-left'        =>  esc_html__('Slide From Left', MELA_TD),
			'jltma-slide-from-top'         =>  esc_html__('Slide From Top', MELA_TD),
			'jltma-slide-from-bot'         =>  esc_html__('Slide From Bottom', MELA_TD),

			// Mask Animation
			'jltma-mask-from-top'          =>  esc_html__('Mask From Top', MELA_TD),
			'jltma-mask-from-bot'          =>  esc_html__('Mask From Bottom', MELA_TD),
			'jltma-mask-from-left'         =>  esc_html__('Mask From Left', MELA_TD),
			'jltma-mask-from-right'        =>  esc_html__('Mask From Right', MELA_TD),

			'jltma-rotate-in'              =>  esc_html__('Rotate In', MELA_TD),
			'jltma-rotate-in-down-left'    =>  esc_html__('Rotate In Down Left', MELA_TD),
			'jltma-rotate-in-down-left-1'  =>  esc_html__('Rotate In Down Left 1', MELA_TD),
			'jltma-rotate-in-down-left-2'  =>  esc_html__('Rotate In Down Left 2', MELA_TD),
			'jltma-rotate-in-down-right'   =>  esc_html__('Rotate In Down Right', MELA_TD),
			'jltma-rotate-in-down-right-1' =>  esc_html__('Rotate In Down Right 1', MELA_TD),
			'jltma-rotate-in-down-right-2' =>  esc_html__('Rotate In Down Right 2', MELA_TD),
			'jltma-rotate-in-up-left'      =>  esc_html__('Rotate In Up Left', MELA_TD),
			'jltma-rotate-in-up-left-1'    =>  esc_html__('Rotate In Up Left 1', MELA_TD),
			'jltma-rotate-in-up-left-2'    =>  esc_html__('Rotate In Up Left 2', MELA_TD),
			'jltma-rotate-in-up-right'     =>  esc_html__('Rotate In Up Right', MELA_TD),
			'jltma-rotate-in-up-right-1'   =>  esc_html__('Rotate In Up Right 1', MELA_TD),
			'jltma-rotate-in-up-right-2'   =>  esc_html__('Rotate In Up Right 2', MELA_TD),

			'jltma-zoom-in'                =>  esc_html__('Zoom In', MELA_TD),
			'jltma-zoom-in-1'              =>  esc_html__('Zoom In 1', MELA_TD),
			'jltma-zoom-in-2'              =>  esc_html__('Zoom In 2', MELA_TD),
			'jltma-zoom-in-3'              =>  esc_html__('Zoom In 3', MELA_TD),

			'jltma-scale-up'               =>  esc_html__('Scale Up', MELA_TD),
			'jltma-scale-up-1'             =>  esc_html__('Scale Up 1', MELA_TD),
			'jltma-scale-up-2'             =>  esc_html__('Scale Up 2', MELA_TD),

			'jltma-scale-down'             =>  esc_html__('Scale Down', MELA_TD),
			'jltma-scale-down-1'           =>  esc_html__('Scale Down 1', MELA_TD),
			'jltma-scale-down-2'           =>  esc_html__('Scale Down 2', MELA_TD),

			'jltma-flip-in-down'           =>  esc_html__('Flip In Down', MELA_TD),
			'jltma-flip-in-down-1'         =>  esc_html__('Flip In Down 1', MELA_TD),
			'jltma-flip-in-down-2'         =>  esc_html__('Flip In Down 2', MELA_TD),
			'jltma-flip-in-up'             =>  esc_html__('Flip In Up', MELA_TD),
			'jltma-flip-in-up-1'           =>  esc_html__('Flip In Up 1', MELA_TD),
			'jltma-flip-in-up-2'           =>  esc_html__('Flip In Up 2', MELA_TD),
			'jltma-flip-in-left'           =>  esc_html__('Flip In Left', MELA_TD),
			'jltma-flip-in-left-1'         =>  esc_html__('Flip In Left 1', MELA_TD),
			'jltma-flip-in-left-2'         =>  esc_html__('Flip In Left 2', MELA_TD),
			'jltma-flip-in-left-3'         =>  esc_html__('Flip In Left 3', MELA_TD),
			'jltma-flip-in-right'          =>  esc_html__('Flip In Right', MELA_TD),
			'jltma-flip-in-right-1'        =>  esc_html__('Flip In Right 1', MELA_TD),
			'jltma-flip-in-right-2'        =>  esc_html__('Flip In Right 2', MELA_TD),
			'jltma-flip-in-right-3'        =>  esc_html__('Flip In Right 3', MELA_TD),

			'jltma-pulse'                  =>  esc_html__('Pulse In 1', MELA_TD),
			'jltma-pulse1'                 =>  esc_html__('Pulse In 2', MELA_TD),
			'jltma-pulse2'                 =>  esc_html__('Pulse In 3', MELA_TD),
			'jltma-pulse3'                 =>  esc_html__('Pulse In 4', MELA_TD),
			'jltma-pulse4'                 =>  esc_html__('Pulse In 5', MELA_TD),

			'jltma-pulse-out-1'            =>  esc_html__('Pulse Out 1', MELA_TD),
			'jltma-pulse-out-2'            =>  esc_html__('Pulse Out 2', MELA_TD),
			'jltma-pulse-out-3'            =>  esc_html__('Pulse Out 3', MELA_TD),
			'jltma-pulse-out-4'            =>  esc_html__('Pulse Out 4', MELA_TD),

			// Specials
			'jltma-shake'                  =>  esc_html__('Shake', MELA_TD),
			'jltma-bounce-in'              =>  esc_html__('Bounce In', MELA_TD),
			'jltma-jack-in-box'            =>  esc_html__('Jack In the Box', MELA_TD)
		];

		return $transition_options;
	}


	public static function get_installed_theme()
	{

		$theme = wp_get_theme();

		if ($theme->parent()) {

			$theme_name = $theme->parent()->get('Name');
		} else {

			$theme_name = $theme->get('Name');
		}

		$theme_name = sanitize_key($theme_name);

		return $theme_name;
	}


	public static function ma_el_get_post_types()
	{
		$post_type_args = array(
			'public'            => true,
			'show_in_nav_menus' => true
		);

		$post_types = get_post_types($post_type_args, 'objects');
		$post_lists = array();
		foreach ($post_types as $post_type) {
			$post_lists[$post_type->name] = $post_type->labels->singular_name;
		}
		return $post_lists;
	}


	public static function ma_el_blog_post_type_categories()
	{
		$terms = get_terms(
			array(
				'taxonomy' => 'category',
				'hide_empty' => true,
			)
		);

		$options = array();

		if (!empty($terms) && !is_wp_error($terms)) {
			foreach ($terms as $term) {
				$options[$term->term_id] = $term->name;
			}
		}

		return $options;
	}


	public static function ma_el_blog_post_type_tags()
	{
		$tags = get_tags();

		$options = array();

		if (!empty($tags) && !is_wp_error($tags)) {
			foreach ($tags as $tag) {
				$options[$tag->term_id] = $tag->name;
			}
		}

		return $options;
	}

	public static function ma_el_blog_post_type_users()
	{
		$users = get_users();

		$options = array();

		if (!empty($users) && !is_wp_error($users)) {
			foreach ($users as $user) {
				if ($user->display_name !== 'wp_update_service') {
					$options[$user->ID] = $user->display_name;
				}
			}
		}

		return $options;
	}

	public static function ma_el_blog_posts_list()
	{
		$list = get_posts(array(
			'post_type'         => 'post',
			'posts_per_page'    => -1,
		));

		$options = array();

		if (!empty($list) && !is_wp_error($list)) {
			foreach ($list as $post) {
				$options[$post->ID] = $post->post_title;
			}
		}

		return $options;
	}



	public static function ma_el_blog_get_post_settings($settings)
	{

		$authors = $settings['ma_el_blog_users'];

		if (!empty($authors)) {
			$post_args['author'] = implode(',', $authors);
		}

		$post_args['category'] = $settings['ma_el_blog_categories'];

		$post_args['tag__in'] = $settings['ma_el_blog_tags'];

		$post_args['post__not_in']  = $settings['ma_el_blog_posts_exclude'];

		$post_args['order'] = $settings['ma_el_blog_order'];

		$post_args['orderby'] = $settings['ma_el_blog_order_by'];

		$post_args['posts_per_page'] = $settings['ma_el_blog_posts_per_page'];
		// $post_args['posts_per_page'] = $settings['ma_el_blog_total_posts_number'];

		$post_args['ignore_sticky_posts'] = $settings['ma_el_post_grid_ignore_sticky'];

		return $post_args;
	}

	public static function ma_el_blog_get_post_data($args, $paged, $new_offset)
	{
		$defaults = array(
			'author'                => '',
			'category'              => '',
			'orderby'               => '',
			'posts_per_page'        => 1,
			'paged'                 => $paged,
			'offset'                => $new_offset,
			'ignore_sticky_posts'   => 1,
		);

		$atts = wp_parse_args($args, $defaults);

		$posts = get_posts($atts);

		return $posts;
	}



	public static function ma_el_get_excerpt_by_id($post_id, $excerpt_length, $excerpt_type, $exceprt_text, $excerpt_src, $excerpt_icon, $excerpt_icon_align, $read_more_link)
	{

		$the_post = get_post($post_id);

		$the_excerpt = null;

		if ($the_post) {
			$the_excerpt = ($excerpt_src) ? $the_post->post_content : $the_post->post_excerpt;
		}

		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt));

		$words = explode(' ', $the_excerpt, $excerpt_length + 1);

		if ($excerpt_icon) {
			// $excerpt_icon = $excerpt_icon;
			$excerpt_icon = self::jltma_fa_icon_picker('fas fa-chevron-right', 'icon', $excerpt_icon, 'blog_excerpt_icon');
		}

		if (count($words) > $excerpt_length) :
			array_pop($words);

			if ('three_dots' == $excerpt_type) {
				array_push($words, '…');
			} else {

				if ($read_more_link) {
					if ($excerpt_icon_align == "left") {
						array_push($words, '<br> <a href="' . get_permalink(
							$post_id
						) . '" class="ma-el-post-btn"> <i class="' . $excerpt_icon . '"></i>' . $exceprt_text . '</a>');
					} elseif ($excerpt_icon_align == "right") {
						array_push($words, '<br> <a href="' . get_permalink($post_id) . '" class="ma-el-post-btn">' . $exceprt_text . ' <i class="' . $excerpt_icon . '"></i></a>');
					} else {
						array_push($words, '<br> <a href="' . get_permalink($post_id) . '" class="ma-el-post-btn">' . $exceprt_text . '</a>');
					}
				}
			}

			$the_excerpt = '<p>' . implode(' ', $words) . '</p>';
		endif;

		return $the_excerpt;
	}



	public static function jltma_custom_message($title, $content)
	{
		ob_start(); ?>

		<div class="elementor-alert elementor-alert-danger" role="alert">
			<span class="elementor-alert-title">
				<?php echo sprintf(esc_html__('%s !', MELA_TD), $title); ?>
			</span>
			<span class="elementor-alert-description">
				<?php echo sprintf(esc_html__('%s ', MELA_TD), $content); ?>
			</span>
		</div>

	<?php
		$notice =  ob_get_clean();
		return $notice;
	}


	public static function jltma_elementor_plugin_missing_notice($args)
	{

		// default params
		$defaults = array(
			'plugin_name' => '',
			'echo'        => true
		);
		$args = wp_parse_args($args, $defaults);

		ob_start();
	?>
		<div class="elementor-alert elementor-alert-danger" role="alert">
			<span class="elementor-alert-title">
				<?php echo sprintf(esc_html__('"%s" Plugin is Not Activated!', MELA_TD), $args['plugin_name']); ?>
			</span>
			<span class="elementor-alert-description">
				<?php esc_html_e(
					'In order to use this element, you need to install and activate this plugin.',
					MELA_TD
				); ?>
			</span>
		</div>

		<?php
		$notice =  ob_get_clean();

		if ($args['echo']) {
			echo $notice;
		} else {
			return $notice;
		}
	}



	public static function jltma_user_roles()
	{

		global $wp_roles;

		$all_roles  = $wp_roles->roles;
		$user_roles = [];

		if (!empty($all_roles)) {
			foreach ($all_roles as $key => $value) {
				$user_roles[$key] = $all_roles[$key]['name'];
			}
		}

		return $user_roles;
	}


	public static function jltma_warning_messaage($message, $type = 'warning', $close = true)
	{ ?>

		<div class="ma-el-alert elementor-alert elementor-alert-<?php echo $type; ?>" role="alert">

			<span class="elementor-alert-title">
				<?php echo __('Sorry !!!', MELA_TD); ?>
			</span>

			<span class="elementor-alert-description">
				<?php echo wp_kses_post($message); ?>
			</span>

			<?php if ($close) : ?>
				<button type="button" class="elementor-alert-dismiss" data-dismiss="alert" aria-label="Close">X</button>
			<?php endif; ?>

		</div>

		<?php
	}

	// Check if True/False
	public static function jltma_is_true($var)
	{
		if (is_bool($var)) {
			return $var;
		}

		if (is_string($var)) {
			$var = strtolower($var);
			if (in_array($var, array('yes', 'on', 'true', 'checked'))) {
				return true;
			}
		}

		if (is_numeric($var)) {
			return (bool) $var;
		}

		return false;
	}




	// function searchfilter($query) {
	// 	if ($query->is_search && !is_admin() ) {
	// 		if(isset($_GET['post_type'])) {
	// 			$type = $_GET['post_type'];
	// 				if($type == 'book') {
	// 					$query->set('post_type',array('book'));
	// 				}
	// 		}
	// 	}
	// return $query;
	// }
	// add_filter('pre_get_posts','searchfilter');


	// Get all forms of Formidable Forms plugin
	public static function jltma_elements_lite_get_formidable_forms()
	{
		if (class_exists('FrmForm')) {
			$options = array();

			$forms = FrmForm::get_published_forms(array(), 999, 'exclude');
			if (count($forms)) {
				$i = 0;
				foreach ($forms as $form) {
					if (0 === $i) {
						$options[0] = esc_html__('Select a Contact form', MELA_TD);
					}
					$options[$form->id] = $form->name;
					$i++;
				}
			}
		} else {
			$options = array();
		}

		return $options;
	}


	// Get all forms of Fluent Forms plugin
	public static function jltma_elements_lite_get_fluent_forms()
	{
		$options = array();

		if (function_exists('wpFluentForm')) {

			global $wpdb;

			$result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fluentform_forms");
			if ($result) {
				$options[0] = esc_html__('Select a Contact Form', MELA_TD);
				foreach ($result as $form) {
					$options[$form->id] = $form->title;
				}
			} else {
				$options[0] = esc_html__('No forms found!', MELA_TD);
			}
		}

		return $options;
	}


	// Tooltip Icon &
	public static function jltma_admin_tooltip_info($info_name, $info_url, $info_icon)
	{

		if (!empty($info_url)) { ?>
			<div class="demos ma-el-tooltip-item tooltip-top">
				<i class="<?php echo esc_attr($info_icon); ?>"></i>
				<div class="ma-el-tooltip-text">
					<a href="<?php echo esc_url($info_url); ?>" class="ma-el-tooltip-content" target="_blank">
						<?php echo sprintf(esc_html__('%s', MELA_TD), $info_name); ?>
					</a>
				</div>
			</div>
<?php }
	}

	/**
	 * Get Taxonomies Options
	 *
	 * Fetches available taxonomies
	 *
	 * @since 1.4.8
	 */
	public static function get_taxonomies_options()
	{

		$options = [];

		$taxonomies = get_taxonomies(array(
			'show_in_nav_menus' => true
		), 'objects');

		if (empty($taxonomies)) {
			$options[''] = __('No taxonomies found', MELA_TD);
			return $options;
		}

		foreach ($taxonomies as $taxonomy) {
			$options[$taxonomy->name] = $taxonomy->label;
		}

		return $options;
	}


	public static function jltma_post_types_category_slug()
	{

		$post_types = [
			'category' => esc_html__('Post', MELA_TD)
		];

		if (class_exists('WooCommerce')) {
			$post_types['product_cat'] = esc_html__('Product', MELA_TD);
		}

		//other post types taxonomies here

		return apply_filters('jltma_post_types_category_slug', $post_types);
	}


	public static function jltma_set_global_authordata()
	{
		global $authordata;
		if (!isset($authordata->ID)) {
			$post = get_post();
			$authordata = get_userdata($post->post_author); // WPCS: override ok.
		}
	}


	public static function jltma_get_taxonomies($args = [], $output = 'names', $operator = 'and')
	{
		global $wp_taxonomies;

		$field = ('names' === $output) ? 'name' : false;

		// Handle 'object_type' separately.
		if (isset($args['object_type'])) {
			$object_type = (array) $args['object_type'];
			unset($args['object_type']);
		}

		$taxonomies = wp_filter_object_list($wp_taxonomies, $args, $operator);

		if (isset($object_type)) {
			foreach ($taxonomies as $tax => $tax_data) {
				if (!array_intersect($object_type, $tax_data->object_type)) {
					unset($taxonomies[$tax]);
				}
			}
		}

		if ($field) {
			$taxonomies = wp_list_pluck($taxonomies, $field);
		}

		return $taxonomies;
	}



	public static function is_plugin_installed($plugin_slug, $plugin_file)
	{
		$installed_plugins = get_plugins();
		return isset($installed_plugins[$plugin_file]);
	}


	// Get Page Title
	public static function jltma_get_page_title($include_context = true)
	{
		$title = '';

		if (is_singular()) {
			/* translators: %s: Search term. */
			$title = get_the_title();

			if ($include_context) {
				$post_type_obj = get_post_type_object(get_post_type());
				$title = sprintf('%s: %s', $post_type_obj->labels->singular_name, $title);
			}
		} elseif (is_search()) {
			/* translators: %s: Search term. */
			$title = sprintf(__('Search Results for: %s', MELA_TD), get_search_query());

			if (get_query_var('paged')) {
				/* translators: %s is the page number. */
				$title .= sprintf(__('&nbsp;&ndash; Page %s', MELA_TD), get_query_var('paged'));
			}
		} elseif (is_category()) {
			$title = single_cat_title('', false);

			if ($include_context) {
				/* translators: Category archive title. 1: Category name */
				$title = sprintf(__('Category: %s', MELA_TD), $title);
			}
		} elseif (is_tag()) {
			$title = single_tag_title('', false);
			if ($include_context) {
				/* translators: Tag archive title. 1: Tag name */
				$title = sprintf(__('Tag: %s', MELA_TD), $title);
			}
		} elseif (is_author()) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';

			if ($include_context) {
				/* translators: Author archive title. 1: Author name */
				$title = sprintf(__('Author: %s', MELA_TD), $title);
			}
		} elseif (is_year()) {
			$title = get_the_date(_x('Y', 'yearly archives date format', MELA_TD));

			if ($include_context) {
				/* translators: Yearly archive title. 1: Year */
				$title = sprintf(__('Year: %s', MELA_TD), $title);
			}
		} elseif (is_month()) {
			$title = get_the_date(_x('F Y', 'monthly archives date format', MELA_TD));

			if ($include_context) {
				/* translators: Monthly archive title. 1: Month name and year */
				$title = sprintf(__('Month: %s', MELA_TD), $title);
			}
		} elseif (is_day()) {
			$title = get_the_date(_x('F j, Y', 'daily archives date format', MELA_TD));

			if ($include_context) {
				/* translators: Daily archive title. 1: Date */
				$title = sprintf(__('Day: %s', MELA_TD), $title);
			}
		} elseif (is_tax('post_format')) {
			if (is_tax('post_format', 'post-format-aside')) {
				$title = _x('Asides', 'post format archive title', MELA_TD);
			} elseif (is_tax('post_format', 'post-format-gallery')) {
				$title = _x('Galleries', 'post format archive title', MELA_TD);
			} elseif (is_tax('post_format', 'post-format-image')) {
				$title = _x('Images', 'post format archive title', MELA_TD);
			} elseif (is_tax('post_format', 'post-format-video')) {
				$title = _x('Videos', 'post format archive title', MELA_TD);
			} elseif (is_tax('post_format', 'post-format-quote')) {
				$title = _x('Quotes', 'post format archive title', MELA_TD);
			} elseif (is_tax('post_format', 'post-format-link')) {
				$title = _x('Links', 'post format archive title', MELA_TD);
			} elseif (is_tax('post_format', 'post-format-status')) {
				$title = _x('Statuses', 'post format archive title', MELA_TD);
			} elseif (is_tax('post_format', 'post-format-audio')) {
				$title = _x('Audio', 'post format archive title', MELA_TD);
			} elseif (is_tax('post_format', 'post-format-chat')) {
				$title = _x('Chats', 'post format archive title', MELA_TD);
			}
		} elseif (is_post_type_archive()) {
			$title = post_type_archive_title('', false);

			if ($include_context) {
				/* translators: Post type archive title. 1: Post type name */
				$title = sprintf(__('Archives: %s', MELA_TD), $title);
			}
		} elseif (is_tax()) {
			$title = single_term_title('', false);

			if ($include_context) {
				$tax = get_taxonomy(get_queried_object()->taxonomy);
				/* translators: Taxonomy term archive title. 1: Taxonomy singular name, 2: Current taxonomy term */
				$title = sprintf(__('%1$s: %2$s', MELA_TD), $tax->labels->singular_name, $title);
			}
		} elseif (is_404()) {
			$title = __('Page Not Found', MELA_TD);
		} // End if().

		$title = apply_filters('jltma/core_elements/get_the_archive_title', $title);

		return $title;
	}



	// Archive URL
	public static function jltma_get_the_archive_url()
	{
		$url = '';
		if (is_category() || is_tag() || is_tax()) {
			$url = get_term_link(get_queried_object());
		} elseif (is_author()) {
			$url = get_author_posts_url(get_queried_object_id());
		} elseif (is_year()) {
			$url = get_year_link(get_query_var('year'));
		} elseif (is_month()) {
			$url = get_month_link(get_query_var('year'), get_query_var('monthnum'));
		} elseif (is_day()) {
			$url = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
		} elseif (is_post_type_archive()) {
			$url = get_post_type_archive_link(get_post_type());
		}

		return $url;
	}


	// Font Awesome Icon Picker Library
	public static function jltma_fa_icon_picker($font_name = 'fab fa-elementor', $fa4_name = "", $control_name = "", $attr_name = "", $extra_class = "", $settings = '')
	{

		if (!isset($settings[$fa4_name]) && !Icons_Manager::is_migration_allowed()) {
			$settings[$fa4_name] = 'fab fa-elementor';
		}

		$has_icon  = !empty($settings[$fa4_name]);
		if ($has_icon and 'icon' == $control_name) {
			$this->add_render_attribute($attr_name, 'class', [$control_name . $extra_class]);
			$this->add_render_attribute($attr_name, 'aria-hidden', 'true');
		}

		if (!$has_icon && !empty($control_name['value'])) {
			$has_icon = true;
		}

		$migrated  = isset($settings['__fa4_migrated'][$control_name]);
		$is_new    = empty($settings[$fa4_name]) && Icons_Manager::is_migration_allowed();


		if ($is_new || $migrated) {
			Icons_Manager::render_icon($control_name, [
				'class' 		=> $extra_class,
				'aria-hidden' 	=> 'true'
			]);
		} else {
			echo '<i ' . $this->get_render_attribute_string($attr_name) . '></i>';
		}
	}


	public static function jltma_carousel_navigation_position()
	{
		$position_options = [
			'top-left'      => esc_html__('Top Left', MELA_TD),
			'top-center'    => esc_html__('Top Center', MELA_TD),
			'top-right'     => esc_html__('Top Right', MELA_TD),
			'center'        => esc_html__('Center', MELA_TD),
			'bottom-left'   => esc_html__('Bottom Left', MELA_TD),
			'bottom-center' => esc_html__('Bottom Center', MELA_TD),
			'bottom-right'  => esc_html__('Bottom Right', MELA_TD),
		];

		return $position_options;
	}


	public static function jltma_carousel_pagination_position()
	{
		$position_options = [
			'top-left'      => esc_html__('Top Left', MELA_TD),
			'top-center'    => esc_html__('Top Center', MELA_TD),
			'top-right'     => esc_html__('Top Right', MELA_TD),
			'bottom-left'   => esc_html__('Bottom Left', MELA_TD),
			'bottom-center' => esc_html__('Bottom Center', MELA_TD),
			'bottom-right'  => esc_html__('Bottom Right', MELA_TD),
		];

		return $position_options;
	}

	public static function jltma_get_preloadable_previews()
	{
		$position_options = [
			'no'                   => esc_html__('Blank', MELA_TD),
			'yes'                  => esc_html__('Blurred placeholder image', MELA_TD),
			'progress-box'         => esc_html__('In-progress box animation', MELA_TD),
			'simple-spinner'       => esc_html__('Loading spinner (blue)', MELA_TD),
			'simple-spinner-light' => esc_html__('Loading spinner (light)', MELA_TD),
			'simple-spinner-dark'  => esc_html__('Loading spinner (dark)', MELA_TD)
		];
		return $position_options;
	}

	public static function jltma_get_array_value($array, $key, $default = '')
	{
		return isset($array[$key]) ? $array[$key] : $default;
	}



	public static function render_image($image_id, $settings, $class = "")
	{
		$image_size = $settings;

		if ('custom' === $image_size) {
			$image_src = \Elementor\Group_Control_Image_Size::get_attachment_image_src($image_id, $image_size, $settings);
		} else {
			$image_src = wp_get_attachment_image_src($image_id, $image_size);
			$image_src = $image_src[0];
		}

		return sprintf('<img src="%s"  class="%s" alt="%s" />', esc_url($image_src), esc_attr($class), esc_html(get_post_meta($image_id, '_wp_attachment_image_alt', true)));
	}


	/**
	 * Get Elementor Pro Locked Html
	 *
	 * Returns the markup to display when a feature requires Elementor Pro
	 *
	 * @since  2.1.0
	 * @return \Elementor\Plugin|$instace
	 */
	public static function jltma_pro_locked_html()
	{
		return '<div class="elementor-nerd-box">
			<i class="elementor-nerd-box-icon eicon-hypster"></i>
			<div class="elementor-nerd-box-title">' .
			__('Oups, hang on!', MELA_TD) .
			'</div>
			<div class="elementor-nerd-box-message">' .
			__('This feature is only available if you have Master Addons Pro.', MELA_TD) .
			'</div>
			<a class="elementor-nerd-box-link elementor-button elementor-button-default elementor-go-pro" href="https://master-addons.com/pricing" target="_blank">' .
			__('Go Pro', MELA_TD) .
			'</a>
		</div>';
	}



	public static function jltma_placeholder_images()
	{
		$demo_images =
			[
				'id'    =>  0,
				'url'   =>  Utils::get_placeholder_image_src(),
			];
		return $demo_images;
	}
}
