<?php
/**
 * Custom post types
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Mfn_Post_Type')) {
	class Mfn_Post_Type
	{
		protected $fields = array();
		protected $builder = false;

		/**
		 * Mfn_Post_Type constructor
		 */

		public function __construct()
		{
			// fires when styles are printed for a specific admin page based on $hook_suffix
  		add_action('admin_enqueue_scripts', array($this, 'enqueue'));

			// runs after the basic admin panel menu structure is in place
			add_action('admin_menu', array($this, 'meta_box'));

      // triggered whenever a post or page is created or updated
  		add_action('save_post', array($this, 'save_box'));
		}

    /**
     * Enqueue styles and scripts
     */

    public function enqueue($hook)
    {
			if ( ! in_array( $hook, array('post.php','post-new.php') ) ) {
				return;
	    }

      wp_enqueue_style('mfn-opts', get_theme_file_uri('/muffin-options/css/options.css'), false, MFN_THEME_VERSION, 'all');

      if (is_rtl()) {
				wp_enqueue_style('mfn-opts-rtl', get_theme_file_uri('/muffin-options/css/options-rtl.css'), false, MFN_THEME_VERSION, 'all');
			}
    }

		/**
		 * Get layouts
		 */

		public function get_layouts(){

			$layouts = array(
				0 => '-- Theme Options --',
			);

			$args = array(
				'post_type' => 'layout',
				'posts_per_page'=> -1,
			);
			$lay = get_posts($args);

			if (is_array($lay)) {
				foreach ($lay as $v) {
					$layouts[$v->ID] = $v->post_title;
				}
			}

			return $layouts;
		}

		/**
		 * Get menus
		 */

		public function get_menus(){

			$aMenus = array(
				0 => '-- Default --'
			);

			$oMenus = get_terms('nav_menu', array( 'hide_empty' => false ));

			if (is_array($oMenus)) {
				foreach ($oMenus as $menu) {
					$aMenus[ $menu->term_id ] = $menu->name;

					$term_trans_id = apply_filters('wpml_object_id', $menu->term_id, 'nav_menu', false);
					if ($term_trans_id != $menu->term_id) {
						unset($aMenus[ $menu->term_id ]);
					}
				}
			}

			return $aMenus;
		}

		/**
		 * Add meta box
		 */

		public function meta_box()
		{
			add_meta_box(
    		$this->fields['id'],
    		$this->fields['title'],
    		array($this, 'show_box'),
    		$this->fields['page'],
    		$this->fields['context'],
    		$this->fields['priority']
      );
		}

		/**
		 * Fill meta box with fields
		 */

		public function show_box()
		{
			global $post;

			echo '<div id="mfn-wrapper">';
  			echo '<input type="hidden" name="mfn-builder-nonce" value="'. wp_create_nonce('mfn-builder-nonce') .'" />';

				// muffin builder

				if( method_exists($this->builder, 'show') ){
					$this->builder->set_fields();
					$this->builder->show();
				}

				// meta fields

				echo '<table class="form-table">';
    			echo '<tbody>';
      			foreach ($this->fields['fields'] as $field) {

      				$meta = get_post_meta($post->ID, $field['id'], true);

      				if (! key_exists('std', $field)) {
      					$field['std'] = false;
      				}

							if( ! $meta && $meta !== '0' ){
								$meta = stripslashes(htmlspecialchars(($field['std']), ENT_QUOTES));
							}

      				Mfn_Builder_Admin::field($field, $meta);

      			}
    			echo '</tbody>';
  			echo '</table>';

			echo '</div>';
		}

		/**
		 * Save custom meta fileds
		 */

		function save_box($post_id)
 		{
 			// verify nonce

 			if ( key_exists('mfn-builder-nonce', $_POST) ) {
 				if ( ! wp_verify_nonce($_POST['mfn-builder-nonce'], 'mfn-builder-nonce') ) {
 					return $post_id;
 				}
 			}

 			// check autosave

 			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
 				return $post_id;
 			}

 			// check permissions

			if( isset($_POST['post_type']) && ('page' == $_POST['post_type']) ){
				if ( ! current_user_can('edit_page', $post_id) ) {
					return $post_id;
				}
			} elseif ( ! current_user_can('edit_post', $post_id) ) {
				return $post_id;
			}

			// muffin builder

			if( method_exists($this->builder, 'save') ){
				$this->builder->save( $post_id );
			}

			// save values

			if( isset($this->fields['fields']) && is_array($this->fields['fields']) ){
				foreach ($this->fields['fields'] as $field) {

	 				if ( isset($_POST[$field['id']]) ) {
	 					$new = $_POST[$field['id']];
	 				} else {
	 					continue;
	 				}

					$old = get_post_meta($post_id, $field['id'], true);

	 				if ( isset($new) && $new != $old ) {
	 					update_post_meta($post_id, $field['id'], $new);
	 				} elseif ( '' == $new && $old ) {
	 					delete_post_meta($post_id, $field['id'], $old);
	 				}

	 			}
			}

 		}

	}
}
