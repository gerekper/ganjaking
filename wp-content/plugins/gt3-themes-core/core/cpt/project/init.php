<?php

	/**
	 *
	 */
	class GT3ProjectRegister {
		private static $instance = null;

		private $cpt;
		private $taxonomy;
		private $slug;

		public static function instance(){
			if(!self::$instance instanceof self) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {
			$this->cpt          = 'project';
			$this->taxonomy     = $this->cpt . '_category';
			$this->taxonomy_pos = $this->cpt . '_position';
			$this->tag          = $this->cpt . '_tag';
			$this->slug         = $this->cpt;
			$slug_option        = function_exists( 'gt3_option' ) ? gt3_option( 'project_slug' ) : '';
			$this->slug         = empty( $slug_option ) ? $this->cpt : sanitize_title( $slug_option );

			$this->single_label  = apply_filters( "gt3_project_single_label_filter", esc_html__('Project', 'gt3_themes_core'));
	        $this->multiple_label  = apply_filters( "gt3_project_multiple_label_filter", __('Projects', 'gt3_themes_core'));

			add_action( 'init', array( $this, 'register' ) );
		}

		public function register(){
			$this->registerPostType();
			$this->registerTax();
			add_filter('single_template', array( $this, 'register_single_template' ));
			add_filter('archive_template', array( $this, 'register_archive_template' ));
			add_filter('post_gallery', array( $this, 'shortcode_gallery' ), 20, 3);
			add_action('wp_footer', array( $this, 'wp_footer' ));
		}

		private function getSlug(){
			return $this->slug;
		}

		private function registerPostType(){

			register_post_type(
				$this->cpt,
				array(
					'label'           => $this->single_label,
					'labels'          => array(
						'name'               => $this->multiple_label,
						'singular_name'      => $this->single_label,
						'menu_name'          => $this->single_label,
						'name_admin_bar'     => $this->single_label,
						'add_new'            => wp_sprintf( __('Add %s', 'gt3_themes_core'), $this->single_label ),
						'add_new_item'       => wp_sprintf( __('Add New %s', 'gt3_themes_core'), $this->single_label ),
						'new_item'           => wp_sprintf( __('New %s', 'gt3_themes_core'), $this->single_label ),
						'edit_item'          => wp_sprintf( __('Edit %s', 'gt3_themes_core'), $this->single_label ),
						'view_item'          => wp_sprintf( __('View %s', 'gt3_themes_core'), $this->single_label ),
						'all_items'          => wp_sprintf( __('All %s', 'gt3_themes_core'), $this->multiple_label ),
						'search_items'       => wp_sprintf( __('Search %s', 'gt3_themes_core'), $this->multiple_label ),
						'parent_item_colon'  => wp_sprintf( __('Parent %s', 'gt3_themes_core'), $this->multiple_label ),
						'not_found'          => wp_sprintf( __('No %s found.', 'gt3_themes_core'), $this->multiple_label ),
						'not_found_in_trash' => wp_sprintf( __('No %s found in Trash.', 'gt3_themes_core'), $this->multiple_label )
					),
					'public'          => true,
					'has_archive'     => true,
					'capability_type' => 'post',
					'rewrite'         => array(
						'slug' => $this->slug
					),
					'menu_position'   => 5,
					'show_ui'         => true,
					'supports'        => array(
						'title',
						'editor',
						'thumbnail',
						'page-attributes'
					),
					'menu_icon'       => 'dashicons-analytics',
					'taxonomies'      => array( $this->taxonomy_pos )
				)
			);
		}

		private function registerTax(){
			$labels = array(
				'name'              => wp_sprintf( __('%s Categories', 'gt3_themes_core'), $this->single_label ),
				'singular_name'     => wp_sprintf( __('%s Category', 'gt3_themes_core'), $this->single_label ),
				'search_items'      => wp_sprintf( __('Search %s Categories', 'gt3_themes_core'), $this->single_label ),
				'all_items'         => wp_sprintf( __('All %s Categories', 'gt3_themes_core'), $this->single_label ),
				'parent_item'       => wp_sprintf( __('Parent %s Category', 'gt3_themes_core'), $this->single_label ),
				'parent_item_colon' => wp_sprintf( __('Parent %s Category:', 'gt3_themes_core'), $this->single_label ),
				'edit_item'         => wp_sprintf( __('Edit %s Category', 'gt3_themes_core'), $this->single_label ),
				'update_item'       => wp_sprintf( __('Update %s Category', 'gt3_themes_core'), $this->single_label ),
				'add_new_item'      => wp_sprintf( __('Add New  %s Category', 'gt3_themes_core'), $this->single_label ),
				'new_item_name'     => wp_sprintf( __('New %s Category Name', 'gt3_themes_core'), $this->single_label ),
				'menu_name'         => wp_sprintf( __('%s Categories', 'gt3_themes_core'), $this->single_label ),
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
					'rewrite'           => array( 'slug' => $this->slug. '-' . __('category', 'gt3_themes_core') ),
				)
			);

			$labels = array(
				'name' => wp_sprintf( __('%s Tags', 'gt3_themes_core'), $this->single_label ),
				'singular_name' => wp_sprintf( __('%s Tag', 'gt3_themes_core'), $this->single_label ),
				'search_items' => wp_sprintf( __('Search %s Tags', 'gt3_themes_core'), $this->single_label ),
				'all_items' => wp_sprintf( __('All %s Tags', 'gt3_themes_core'), $this->single_label ),
				'parent_item_colon' => wp_sprintf( __('Parent %s Tag:', 'gt3_themes_core'), $this->single_label ),
				'edit_item' => wp_sprintf( __('Edit %s Tag', 'gt3_themes_core'), $this->single_label ),
				'update_item' => wp_sprintf( __('Update %s Tag', 'gt3_themes_core'), $this->single_label ),
				'add_new_item' => wp_sprintf( __('Add New %s Tag', 'gt3_themes_core'), $this->single_label ),
				'new_item_name' => wp_sprintf( __('New %s Tag Name', 'gt3_themes_core'), $this->single_label ),
				'menu_name' => wp_sprintf( __('%s Tags', 'gt3_themes_core'), $this->single_label ),
			);

			register_taxonomy($this->tag, array($this->cpt), array(
				'hierarchical' => false,
				'labels' => $labels,
				'show_ui' => true,
				'show_admin_column' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => $this->slug.__('-tag','gt3_themes_core') ),
			));
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
	            	return plugin_dir_path(dirname( __FILE__ )).'/project/archive-'.$this->cpt.'.php';
	            }
	        }

	        return $archive;
	    }

		public function shortcode_gallery($content, $attr, $instance){
			global $post;

			if(get_post_type() != $this->cpt) {
				return $content;
			} else {
				ob_start();
				?>
				<div class="project_gallery">
					<div class="header_panel">

					</div>
					<div class="content_wrapper">

					</div>
					<div class="footer_panel">

					</div>
				</div>

				<?php
				$GLOBALS['gt3themes_project_gallery_footer'] = ob_get_clean();
			}

			return $content;

		}

		public function wp_footer(){
			if (isset($GLOBALS['gt3themes_project_gallery_footer']) && !empty($GLOBALS['gt3themes_project_gallery_footer'])) {
				echo $GLOBALS['gt3themes_project_gallery_footer'];
			}
		}


	}

	GT3ProjectRegister::instance();
