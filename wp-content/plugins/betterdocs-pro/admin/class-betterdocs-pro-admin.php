<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/admin
 * @author     WPDeveloper <support@wpdeveloper.net>
 */
class Betterdocs_Pro_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		if ( BetterDocs_Multiple_Kb::$enable == 1 ) {
			add_action( 'betterdocs_admin_menu', array( $this, 'add_multiple_kb_menu' ) );
		}
		add_action( 'wp_ajax_update_doc_cat_order', array( $this, 'update_doc_cat_order' ) );
		add_action( 'wp_ajax_update_doc_order_by_category', array( $this, 'update_doc_order_by_category' ) );
		add_action( 'wp_ajax_update_docs_term', array( $this, 'update_docs_term' ) );
		add_action( 'save_post_docs', array( $this, 'update_new_post_doc_order_by_category' ) );
		add_action( 'wp_ajax_betterdocs_dark_mode', array( $this, 'dark_mode' ) );
		
		$alphabetically_order_post = BetterDocs_DB::get_settings('alphabetically_order_post');
		if ( $alphabetically_order_post != 1 ) {
			add_filter( 'betterdocs_articles_args', array( $this, 'docs_args' ), 11, 2 );
			add_filter( 'betterdocs_sub_cat_articles_args', array( $this, 'docs_args' ), 11, 2 );
		}
		
		add_action( 'new_to_auto-draft', array( $this, 'auto_add_category') );

	}

	/**
	 * Auto Add in Category, Adding from Sorting
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function auto_add_category( $post ){
		if ( ! strpos( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) ) {
			return;
		}
		if ( empty( $_GET['cat'] ) ) {
			return;
		}
		$cat = wp_unslash( $_GET['cat'] );
		if ( false === ( $cat = get_term_by( 'term_id', $cat, 'doc_category' ) ) ) {
			return;
		}
		wp_set_post_terms( $post->ID, array( $cat->term_id ), 'doc_category', false );
	}

	public function body_classes( $classes ){
		$dark_mode = false;
		$saved_settings = get_option( 'betterdocs_settings', false );
		$dark_mode = isset( $saved_settings['dark_mode'] ) ? $saved_settings['dark_mode'] : false;
		$dark_mode = ! empty( $dark_mode ) ? boolval( $dark_mode ) : false;
		if( $dark_mode === true ) {
			$classes .= ' betterdocs-dark-mode ';
		}
		return $classes;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Betterdocs_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Betterdocs_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/betterdocs-pro-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Betterdocs_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Betterdocs_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$tax = function_exists( 'get_current_screen' ) ? get_current_screen() : '';
		if( ! in_array( $hook, array( 'toplevel_page_betterdocs-admin', 'betterdocs_page_betterdocs-settings', 'betterdocs_page_betterdocs-analytics', 'edit-tags.php', 'edit.php' ) )) {
			if( $tax->taxonomy !== 'doc_category' ) {
				return;
			} else {
				return;
			}
		}

		wp_enqueue_script( 'clipboard', BETTERDOCS_PUBLIC_URL . 'js/clipboard.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/betterdocs-pro-admin.js', array( 'jquery', 'clipboard' ), $this->version, false );
		
		$dark_mode = false;
		if( class_exists( 'BetterDocs_DB' ) ) {
			$dark_mode = BetterDocs_DB::get_settings( 'dark_mode' );
		}

		wp_localize_script(
			$this->plugin_name,
			'docs_cat_ordering_data',
			array(
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'doc_cat_order_nonce' => wp_create_nonce( 'doc_cat_order_nonce' ),
				'knowledge_base_order_nonce' => wp_create_nonce( 'knowledge_base_order_nonce' ),
				'paged'               => isset( $_GET['paged'] ) ? absint( wp_unslash( $_GET['paged'] ) ) : 0,
				'per_page_id'         => "edit_{$tax->taxonomy}_per_page",
				'menu_title'          => __( 'Switch to BetterDocs UI', 'betterdocs-pro' ),
				'dark_mode'           => ! empty( $dark_mode ) ? boolval( $dark_mode ) : false,
			)
		);

	}

    public static function header_template(){
        ?>
            <div class="betterdocs-settings-header">
                <div class="betterdocs-header-full">
                    <img src="<?php echo BETTERDOCS_ADMIN_URL ?>assets/img/betterdocs-icon.svg" alt="">
					<h2 class="title"><?php _e( 'BetterDocs', 'betterdocs-pro' ); ?></h2>
					
					<div class="betterdocs-header-button">
						<a href="edit.php?post_type=docs&bdocs_view=classic" class="betterdocs-button betterdocs-button-secondary"><?php _e( 'Switch to Classic UI', 'betterdocs-pro' ); ?></a>
						<a href="post-new.php?post_type=docs" class="betterdocs-button betterdocs-button-primary"><?php _e( 'Add New Doc', 'betterdocs-pro' ); ?></a>
						<?php if ( BetterDocs_Multiple_Kb::$enable == 1 ) { ?>
						<select name="dashboard-select-kb" id="dashboard-select-kb" onchange="javascript:location.href = 'admin.php?page=betterdocs-admin&knowledgebase=' + this.value;">
							<option value="all"><?php esc_html_e( 'All Knowledge Base', 'betterdocs' ) ?></option>
							<?php 
							$terms_object = array(
								'taxonomy' => 'knowledge_base',
								'hide_empty' => true,
								'parent' => 0
							);

							$taxonomy_objects = get_terms($terms_object);
							if ( $taxonomy_objects && ! is_wp_error( $taxonomy_objects ) ) :
								foreach ( $taxonomy_objects as $term ) :
									$selected = ( isset($_GET['knowledgebase'] ) && $term->slug == $_GET['knowledgebase']) ? ' selected' : '';
									echo '<option value="' . $term->slug . '"' . $selected . '>' . $term->name . '</option>';
								endforeach;
							endif;

							?>
						</select>
						<?php } ?>
					</div>
					<div class="betterdocs-switch-mode">
					  <label for='betterdocs-mode-toggle'>
					    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="dayIcon" x="0px" y="0px" viewBox="0 0 35 35" style="enable-background:new 0 0 35 35;" xml:space="preserve">
					      <g id="Sun">
					        <g>
					          <path style="fill-rule:evenodd;clip-rule:evenodd;" d="M6,17.5C6,16.672,5.328,16,4.5,16h-3C0.672,16,0,16.672,0,17.5    S0.672,19,1.5,19h3C5.328,19,6,18.328,6,17.5z M7.5,26c-0.414,0-0.789,0.168-1.061,0.439l-2,2C4.168,28.711,4,29.086,4,29.5    C4,30.328,4.671,31,5.5,31c0.414,0,0.789-0.168,1.06-0.44l2-2C8.832,28.289,9,27.914,9,27.5C9,26.672,8.329,26,7.5,26z M17.5,6    C18.329,6,19,5.328,19,4.5v-3C19,0.672,18.329,0,17.5,0S16,0.672,16,1.5v3C16,5.328,16.671,6,17.5,6z M27.5,9    c0.414,0,0.789-0.168,1.06-0.439l2-2C30.832,6.289,31,5.914,31,5.5C31,4.672,30.329,4,29.5,4c-0.414,0-0.789,0.168-1.061,0.44    l-2,2C26.168,6.711,26,7.086,26,7.5C26,8.328,26.671,9,27.5,9z M6.439,8.561C6.711,8.832,7.086,9,7.5,9C8.328,9,9,8.328,9,7.5    c0-0.414-0.168-0.789-0.439-1.061l-2-2C6.289,4.168,5.914,4,5.5,4C4.672,4,4,4.672,4,5.5c0,0.414,0.168,0.789,0.439,1.06    L6.439,8.561z M33.5,16h-3c-0.828,0-1.5,0.672-1.5,1.5s0.672,1.5,1.5,1.5h3c0.828,0,1.5-0.672,1.5-1.5S34.328,16,33.5,16z     M28.561,26.439C28.289,26.168,27.914,26,27.5,26c-0.828,0-1.5,0.672-1.5,1.5c0,0.414,0.168,0.789,0.439,1.06l2,2    C28.711,30.832,29.086,31,29.5,31c0.828,0,1.5-0.672,1.5-1.5c0-0.414-0.168-0.789-0.439-1.061L28.561,26.439z M17.5,29    c-0.829,0-1.5,0.672-1.5,1.5v3c0,0.828,0.671,1.5,1.5,1.5s1.5-0.672,1.5-1.5v-3C19,29.672,18.329,29,17.5,29z M17.5,7    C11.71,7,7,11.71,7,17.5S11.71,28,17.5,28S28,23.29,28,17.5S23.29,7,17.5,7z M17.5,25c-4.136,0-7.5-3.364-7.5-7.5    c0-4.136,3.364-7.5,7.5-7.5c4.136,0,7.5,3.364,7.5,7.5C25,21.636,21.636,25,17.5,25z" />
					        </g>
					      </g>
					    </svg>
					  </label>
					  <input class='betterdocs-mode-toggle' id='betterdocs-mode-toggle' type='checkbox'>
					  <label class='betterdocs-mode-toggle-button' for='betterdocs-mode-toggle'></label>
					  <label for='betterdocs-mode-toggle'>
					    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="nightIcon" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
					      <path d="M96.76,66.458c-0.853-0.852-2.15-1.064-3.23-0.534c-6.063,2.991-12.858,4.571-19.655,4.571  C62.022,70.495,50.88,65.88,42.5,57.5C29.043,44.043,25.658,23.536,34.076,6.47c0.532-1.08,0.318-2.379-0.534-3.23  c-0.851-0.852-2.15-1.064-3.23-0.534c-4.918,2.427-9.375,5.619-13.246,9.491c-9.447,9.447-14.65,22.008-14.65,35.369  c0,13.36,5.203,25.921,14.65,35.368s22.008,14.65,35.368,14.65c13.361,0,25.921-5.203,35.369-14.65  c3.872-3.871,7.064-8.328,9.491-13.246C97.826,68.608,97.611,67.309,96.76,66.458z" />
					    </svg>
					  </label>
					</div>
                </div>
            </div>
        <?php
    }

	public function betterdocs_menu_slug() {
		return 'betterdocs-admin';
	}

	public function betterdocs_admin_output() {
		return array( $this, 'betterdocs_admin_display' );
	}

	public function betterdocs_admin_display() {
		
		$terms_object = array(
			'taxonomy' => 'doc_category',
			'orderby' => 'meta_value_num',
			'meta_key' => 'doc_category_order',
			'order' => 'ASC',
			'hide_empty' => false,
		);

		if ( BetterDocs_Multiple_Kb::$enable == 1 && isset( $_GET['knowledgebase'] ) && $_GET['knowledgebase'] !== 'all' ) {
			$terms_object['meta_query'] = array( 
				array(
					'key'       => 'doc_category_knowledge_base',
					'value'     => $_GET['knowledgebase'],
					'compare'   => 'LIKE'
				)
			);
		}

		$terms = get_terms($terms_object);

		if( file_exists( BETTERDOCS_PRO_ADMIN_DIR_PATH . 'partials/betterdocs-pro-admin-sorting-display.php' ) ) {
            return include_once BETTERDOCS_PRO_ADMIN_DIR_PATH . 'partials/betterdocs-pro-admin-sorting-display.php';
        }
	}

	/**
     * This method is responsible for adding multiple KB in Menu
     * @return void
     */

    public function add_multiple_kb_menu( $pages ) {

        $pages['edit-tags.php?taxonomy=knowledge_base&post_type=docs'] = array(
			'title'      => __('Multiple KB', 'betterdocs-pro'),
			'callback'   => ''
        );

        return $pages;
	}
	
	public function highlight_admin_menu( $parent_file ) {

		global $current_screen;

		if( $current_screen->post_type === 'docs' ) {

			$parent_file = 'betterdocs-admin';

		}

        return $parent_file;
	}

	public function highlight_admin_submenu( $parent_file, $submenu_file ) {

		global $current_screen, $pagenow;

        if ( $current_screen->post_type == 'docs' ) {

            if ( $pagenow == 'post.php' ) {

				$submenu_file = 'betterdocs-admin';
				
			}

			if ( $pagenow == 'post-new.php' ) {

				$submenu_file = 'post-new.php?post_type=docs';
				
			}

			if( $current_screen->id === 'edit-doc_category' ) {

				$submenu_file = 'edit-tags.php?taxonomy=doc_category&post_type=docs';

			}

			if( $current_screen->id === 'edit-doc_tag' ) {

				$submenu_file = 'edit-tags.php?taxonomy=doc_tag&post_type=docs';

			}

			if( $current_screen->id === 'edit-knowledge_base' ) {

				$submenu_file = 'edit-tags.php?taxonomy=knowledge_base&post_type=docs';

			}
		}

		if( 'betterdocs_page_betterdocs-settings' == $current_screen->id ) {

			$submenu_file = 'betterdocs-settings';

		}

		if( 'betterdocs_page_betterdocs-analytics' == $current_screen->id ) {

			$submenu_file = 'betterdocs-analytics';

		}

		if( 'betterdocs_page_betterdocs-setup' == $current_screen->id ) {

			$submenu_file = 'betterdocs-setup';

		}

        return $submenu_file;
	}

	/**
	 * AJAX Handler to update terms' tax position.
	 */
	public function dark_mode() {

		if ( ! check_ajax_referer( 'doc_cat_order_nonce', 'nonce', false ) ) {
			wp_send_json_error();
		}

		if( isset( $_POST['mode'] ) ) {

			$saved_settings = BetterDocs_DB::get_settings();
			$saved_settings[ 'dark_mode' ] = $_POST['mode'];
	
			if( BetterDocs_DB::update_settings( $saved_settings ) ) {
				wp_send_json_success();
			}
		}

		wp_send_json_error();

	}
	
	/**
	 * 
	 * AJAX Handler to update terms' tax position.
	 * 
	 */
	public function update_doc_cat_order() {

		if ( ! check_ajax_referer( 'doc_cat_order_nonce', 'doc_cat_order_nonce', false ) ) {
			wp_send_json_error();
		}

		$taxonomy_ordering_data = filter_var_array( wp_unslash( $_POST['taxonomy_ordering_data'] ), FILTER_SANITIZE_NUMBER_INT );
		$base_index             = filter_var( wp_unslash( $_POST['base_index'] ), FILTER_SANITIZE_NUMBER_INT ) ;
		
		foreach ( $taxonomy_ordering_data as $order_data ) {

			if ( $base_index > 0 ) {

				$current_position = get_term_meta( $order_data['term_id'], 'doc_category_order', true );
				
				if ( (int) $current_position < (int) $base_index ) {
					continue;
				}
			}

			update_term_meta( $order_data['term_id'], 'doc_category_order', ( (int) $order_data['order'] + (int) $base_index ) );
		
		}

		wp_send_json_success();
	}
	
	/**
	 * AJAX Handler to update docs position.
	 */
	public function update_doc_order_by_category() {
		
		if ( ! check_ajax_referer( 'doc_cat_order_nonce', 'doc_cat_order_nonce', false ) ) {
			wp_send_json_error();
		}

		$docs_ordering_data = filter_var_array( wp_unslash( $_POST['docs_ordering_data'] ), FILTER_SANITIZE_NUMBER_INT );
		$term_id = intval( $_POST['list_term_id'] );
		
		if( ! $term_id ) {
			wp_send_json_error();
		}

		if( update_term_meta( $term_id, '_docs_order', implode( ',', $docs_ordering_data ) ) ) {
			wp_send_json_success();
		}

		wp_send_json_error();

	}
	
	/**
	 * AJAX Handler to update docs position.
	 */

	public function update_docs_term() {

		if ( ! check_ajax_referer( 'doc_cat_order_nonce', 'doc_cat_order_nonce', false ) ) {
			wp_send_json_error();
		}

		$object_id = intval( $_POST['object_id'] );
		$term_id = intval( $_POST['list_term_id'] );
		$prev_term_id = intval( isset( $_POST['prev_term_id'] ) ? $_POST['prev_term_id'] : 0 );

		if( ! $term_id || ! $object_id ) {

			wp_send_json_error();
		
		}

		global $wpdb;

		if( $prev_term_id ) {

			wp_remove_object_terms( $object_id, $prev_term_id, 'doc_category' );

		}

		$terms_added = wp_set_object_terms( $object_id, $term_id, 'doc_category' );
		
		if( ! is_wp_error( $terms_added ) ) {

			wp_send_json_success();

		}

		wp_send_json_error();
	}

	/**
	 * Update docs_term meta when new post created
	 */
	
	public function update_new_post_doc_order_by_category($post_id) {

		$term_list = wp_get_post_terms( $post_id, 'doc_category', array( 'fields' => 'ids' ) );
		
		if($term_list) {

			foreach ($term_list as $term_id){

				$term = get_term( $term_id, 'doc_category' );
				$term_slug = $term->slug;
				$term_meta = get_term_meta( $term_id, '_docs_order');
				$term_meta_arr = explode(",", $term_meta[0]);

				if( ! in_array( $post_id, $term_meta_arr ) ) {

					array_unshift($term_meta_arr, $post_id);
					$docs_ordering_data = filter_var_array( wp_unslash( $term_meta_arr ), FILTER_SANITIZE_NUMBER_INT );
					$val = implode( ',', $docs_ordering_data );
					update_term_meta( $term_id, '_docs_order', implode( ',', $docs_ordering_data ) );
				
				}
			}

		}
	}

	/** 
	 * 
	 * Update docs query arguments
	 * 
	 */

	public function docs_args($args, $term_id = null) {
		
		if (is_null($term_id)) {
			return $args;
		}

		$docs_order = get_term_meta($term_id, '_docs_order', true);

		global $wpdb;

		if ( !empty( $docs_order ) ) {

			$docs_order = explode( ',', $docs_order );

			$new_ids = [];
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = $term_id");

			if (!is_null($results) && !empty($results) && is_array($results)) {
				
				$object_ids = array_filter($results, function ($value) use ($docs_order) {
					return !in_array($value->object_id, $docs_order);
				});

				if ( !empty( $object_ids ) ) {

					array_walk($object_ids, function ($value) use (&$new_ids) {
						$new_ids[] = $value->object_id;
					});

				}

			}

			$args['orderby'] = 'post__in';
			$args['post__in'] = array_merge($new_ids, $docs_order);
		}

		return $args;
	}

}
