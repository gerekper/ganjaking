<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpdeveloper.com
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
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
class Betterdocs_Pro_Admin
{
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
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		if (BetterDocs_Multiple_Kb::$enable == 1) {
			add_filter('betterdocs_admin_menu', array($this, 'add_multiple_kb_menu'), 10, 1);
		}
		add_action('wp_ajax_update_doc_cat_order', array($this, 'update_doc_cat_order'));
		add_action('wp_ajax_update_doc_order_by_category', array($this, 'update_doc_order_by_category'));
		add_action('wp_ajax_update_docs_term', array($this, 'update_docs_term'));
		add_action('save_post_docs', array($this, 'update_new_post_doc_order_by_category'));
		add_action('betterdocs_single_post_nav', array($this, 'single_post_nav'));
        add_filter('betterdocs_highlight_admin_menu', array($this, 'highlight_admin_menu'), 1);
        add_filter('betterdocs_highlight_admin_submenu', array($this, 'highlight_admin_submenu'), 1);
		add_filter('betterdocs_articles_args', array($this, 'docs_args'), 11, 2);
		add_action('new_to_auto-draft', array($this, 'auto_add_category'));
		add_filter('betterdocs_advanced_settings_sections', array( $this, 'enable_internal_kb_fields' ), 10, 1 );
	}

    public function body_classes($classes)
    {
        $classes .= 'betterdocs-pro';
        return $classes;
    }

	/**
	 * Auto Add in Category, Adding from Sorting
	 *
	 * @param WP_Post $post
	 * @return void
	 */
	public function auto_add_category($post)
	{
		if (!strpos($_SERVER['REQUEST_URI'], 'wp-admin/post-new.php')) {
			return;
		}
		if (empty($_GET['cat'])) {
			return;
		}
		$cat = wp_unslash($_GET['cat']);
		if (false === ($cat = get_term_by('term_id', $cat, 'doc_category'))) {
			return;
		}
		wp_set_post_terms($post->ID, array($cat->term_id), 'doc_category', false);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook)
	{
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

        $tax = function_exists('get_current_screen') ? get_current_screen() : '';
        if (!in_array($hook, array('toplevel_page_betterdocs-admin', 'betterdocs_page_betterdocs-settings', 'betterdocs_page_betterdocs-analytics', 'edit-tags.php', 'edit.php'))) {
            if ($tax->taxonomy !== 'doc_category') {
                return;
            } else {
                return;
            }
        }

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/betterdocs-pro-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook)
	{
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
		$tax = function_exists('get_current_screen') ? get_current_screen() : '';
		if (!in_array($hook, array('toplevel_page_betterdocs-admin', 'betterdocs_page_betterdocs-settings', 'betterdocs_page_betterdocs-analytics', 'edit-tags.php', 'edit.php'))) {
			if ($tax->taxonomy !== 'doc_category') {
				return;
			} else {
				return;
			}
		}

		wp_enqueue_script(
		    $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/betterdocs-pro-admin.js',
            array('jquery', 'clipboard'), $this->version, false
        );



		wp_localize_script(
			$this->plugin_name,
			'docs_cat_ordering_data',
			array(
				'ajaxurl'             => admin_url('admin-ajax.php'),
				'doc_cat_order_nonce' => wp_create_nonce('doc_cat_order_nonce'),
				'knowledge_base_order_nonce' => wp_create_nonce('knowledge_base_order_nonce'),
				'paged'               => isset($_GET['paged']) ? absint(wp_unslash($_GET['paged'])) : 0,
				'per_page_id'         => "edit_{$tax->taxonomy}_per_page",
				'menu_title'          => __('Switch to BetterDocs UI', 'betterdocs-pro')
			)
		);
	}

	/**
	 * This method is responsible for adding multiple KB in Menu
	 * @return void
	 */

	public function add_multiple_kb_menu($pages) {
		$pages['mkb'] = array(
			'parent_slug' => 'betterdocs-admin',
			'page_title'  => 'Multiple KB',
			'menu_title'  => 'Multiple KB',
			'text_domain' => 'betterdocs-pro',
			'capability'  => 'manage_knowledge_base_terms',
			'menu_slug'   => 'edit-tags.php?taxonomy=knowledge_base&post_type=docs',
			'callback'    => ''
		);
		return $pages;
	}

	public function highlight_admin_menu($parent_file)
	{

		global $current_screen;

		if ($current_screen->post_type === 'docs') {

			$parent_file = 'betterdocs-admin';
		}

		return $parent_file;
	}

	public function highlight_admin_submenu($submenu_file)
	{
		global $current_screen, $pagenow;

		if ($current_screen->post_type == 'docs') {

			if ($pagenow == 'post.php') {
				$submenu_file = 'betterdocs-admin';
			}

			if ($pagenow == 'post-new.php') {
				$submenu_file = 'post-new.php?post_type=docs';
			}

			if ($current_screen->id === 'edit-doc_category') {
				$submenu_file = 'edit-tags.php?taxonomy=doc_category&post_type=docs';
			}

			if ($current_screen->id === 'edit-doc_tag') {
				$submenu_file = 'edit-tags.php?taxonomy=doc_tag&post_type=docs';
			}

			if ($current_screen->id === 'edit-knowledge_base') {
				$submenu_file = 'edit-tags.php?taxonomy=knowledge_base&post_type=docs';
			}
		}

		if ('betterdocs_page_betterdocs-settings' == $current_screen->id) {
			$submenu_file = 'betterdocs-settings';
		}

		if ('betterdocs_page_betterdocs-analytics' == $current_screen->id) {
			$submenu_file = 'betterdocs-analytics';
		}

		if ('betterdocs_page_betterdocs-setup' == $current_screen->id) {
			$submenu_file = 'betterdocs-setup';
		}

		return $submenu_file;
	}



	/**
	 *
	 * AJAX Handler to update terms' tax position.
	 *
	 */
	public function update_doc_cat_order()
	{
		if (!check_ajax_referer('doc_cat_order_nonce', 'doc_cat_order_nonce', false)) {
			wp_send_json_error();
		}

		$taxonomy_ordering_data = filter_var_array(wp_unslash($_POST['taxonomy_ordering_data']), FILTER_SANITIZE_NUMBER_INT);
		$base_index             = filter_var(wp_unslash($_POST['base_index']), FILTER_SANITIZE_NUMBER_INT);

		foreach ($taxonomy_ordering_data as $order_data) {
			if ($base_index > 0) {
				$current_position = get_term_meta($order_data['term_id'], 'doc_category_order', true);

				if ((int) $current_position < (int) $base_index) {
					continue;
				}
			}
			update_term_meta($order_data['term_id'], 'doc_category_order', ((int) $order_data['order'] + (int) $base_index));
		}
		wp_send_json_success();
	}

	/**
	 * AJAX Handler to update docs position.
	 */
	public function update_doc_order_by_category()
	{
		if (!check_ajax_referer('doc_cat_order_nonce', 'doc_cat_order_nonce', false)) {
			wp_send_json_error();
		}

		$docs_ordering_data = filter_var_array(wp_unslash($_POST['docs_ordering_data']), FILTER_SANITIZE_NUMBER_INT);
		$term_id = intval($_POST['list_term_id']);

		if (!$term_id) {
			wp_send_json_error();
		}

		if (update_term_meta($term_id, '_docs_order', implode(',', $docs_ordering_data))) {
			wp_send_json_success();
		}
	}

	/**
	 * AJAX Handler to update docs position.
	 */
	public function update_docs_term()
	{
		if (!check_ajax_referer('doc_cat_order_nonce', 'doc_cat_order_nonce', false)) {
			wp_send_json_error();
		}

		$object_id = intval($_POST['object_id']);
		$term_id = intval($_POST['list_term_id']);
		$prev_term_id = intval(isset($_POST['prev_term_id']) ? $_POST['prev_term_id'] : 0);

		if (!$term_id || !$object_id) {

			wp_send_json_error();
		}

		global $wpdb;

		if ($prev_term_id) {

			wp_remove_object_terms($object_id, $prev_term_id, 'doc_category');
		}

		$terms_added = wp_set_object_terms($object_id, $term_id, 'doc_category');

		if (!is_wp_error($terms_added)) {

			wp_send_json_success();
		}

		wp_send_json_error();
	}

	public function enable_internal_kb_fields( $settings ) {
		unset( $settings['internal_kb_section']['fields']['enable_content_restriction']['disable'] );
		unset( $settings['internal_kb_section']['fields']['content_visibility']['disable'] );
		unset( $settings['internal_kb_section']['fields']['restrict_template']['disable'] );
		unset( $settings['internal_kb_section']['fields']['restrict_category']['disable'] );
		unset( $settings['internal_kb_section']['fields']['restricted_redirect_url']['disable'] );
		return $settings;
	}

	/**
	 * Update docs_term meta when new post created
	 */

	public function update_new_post_doc_order_by_category($post_id)
	{
		$term_list = wp_get_post_terms($post_id, 'doc_category', array('fields' => 'ids'));

		if (!empty($term_list)) {
			foreach ($term_list as $term_id) {
				$term = get_term($term_id, 'doc_category');
				$term_slug = $term->slug;
				$term_meta = get_term_meta($term_id, '_docs_order');
				if (!empty($term_meta)) {
					$term_meta_arr = explode(",", $term_meta[0]);

					if (!in_array($post_id, $term_meta_arr)) {
						array_unshift($term_meta_arr, $post_id);
						$docs_ordering_data = filter_var_array(wp_unslash($term_meta_arr), FILTER_SANITIZE_NUMBER_INT);
						$val = implode(',', $docs_ordering_data);
						update_term_meta($term_id, '_docs_order', implode(',', $docs_ordering_data));
					}
				}
			}
		}
	}

	/**
	 *
	 * Update docs query arguments
	 *
	 */

	public function docs_args($args, $term_id = null)
	{
		if (is_null($term_id) || isset($args['orderby'])) {
			return $args;
		}

		$docs_order = get_term_meta($term_id, '_docs_order', true);

		global $wpdb;

		if (!empty($docs_order)) {

			$docs_order = explode(',', $docs_order);

			$new_ids = [];
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = $term_id");

			if (!is_null($results) && !empty($results) && is_array($results)) {

				$object_ids = array_filter($results, function ($value) use ($docs_order) {
					return !in_array($value->object_id, $docs_order);
				});

				if (!empty($object_ids)) {

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

	public function get_prev($array, $key)
	{
		$currentKey = array_search($key, $array);
		if ($currentKey > 0 || $currentKey != 0) {
			$nextKey = $currentKey - 1;
			$prev_post = $array[$nextKey];
			$nav = '<a rel="prev" class="next-post" href="' . get_post_permalink($prev_post) . '"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 11.957031 13.988281 C 11.699219 14.003906 11.457031 14.117188 11.28125 14.308594 L 1.015625 25 L 11.28125 35.691406 C 11.527344 35.953125 11.894531 36.0625 12.242188 35.976563 C 12.589844 35.890625 12.867188 35.625 12.964844 35.28125 C 13.066406 34.933594 12.972656 34.5625 12.71875 34.308594 L 4.746094 26 L 48 26 C 48.359375 26.003906 48.695313 25.816406 48.878906 25.503906 C 49.058594 25.191406 49.058594 24.808594 48.878906 24.496094 C 48.695313 24.183594 48.359375 23.996094 48 24 L 4.746094 24 L 12.71875 15.691406 C 13.011719 15.398438 13.09375 14.957031 12.921875 14.582031 C 12.753906 14.203125 12.371094 13.96875 11.957031 13.988281 Z "></path></g></svg>' . esc_html(get_the_title($prev_post)) . '</a>';
		} else {
			$nav = '';
		}
		return $nav;
	}

	public function get_next($array, $key)
	{
		$currentKey = array_search($key, $array);
		if (!empty($array) && end($array) != $array[$currentKey]) {
			$nextKey = $currentKey + 1;
			$next_post = $array[$nextKey];
			$nav = '<a rel="next" class="next-post" href="' . get_post_permalink($next_post) . '">' . esc_html(get_the_title($next_post)) . '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 38.035156 13.988281 C 37.628906 13.980469 37.257813 14.222656 37.09375 14.59375 C 36.933594 14.96875 37.015625 15.402344 37.300781 15.691406 L 45.277344 24 L 2.023438 24 C 1.664063 23.996094 1.328125 24.183594 1.148438 24.496094 C 0.964844 24.808594 0.964844 25.191406 1.148438 25.503906 C 1.328125 25.816406 1.664063 26.003906 2.023438 26 L 45.277344 26 L 37.300781 34.308594 C 36.917969 34.707031 36.933594 35.339844 37.332031 35.722656 C 37.730469 36.105469 38.363281 36.09375 38.746094 35.691406 L 49.011719 25 L 38.746094 14.308594 C 38.5625 14.109375 38.304688 13.996094 38.035156 13.988281 Z "></path></g></svg></a>';
		} else {
			$nav = '';
		}
		return $nav;
	}

    public function get_last_post_id()
    {
        global $wpdb;

        $query = "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1";

        $result = $wpdb->get_results($query);
        $row = $result[0];
        $id = $row->ID;

        return $id;
    }

	public function single_post_nav()
	{
		$nav = '';
		global $wp_query;
		if(isset($wp_query->queried_object->ID)) {
            $post_id = $wp_query->queried_object->ID;
        } else {
            $post_id = $this->get_last_post_id();
        }

		$term = get_the_terms($post_id, 'doc_category');
		if ($term) {
			// $docs_order = (isset($term[0]->slug) && $term[0]->slug != 'uncategorized') ? rtrim(get_term_meta($term[0]->term_id, '_docs_order', true), ',') : '';
			$alphabetic_order = BetterDocs_DB::get_settings('alphabetically_order_post');
			$list_args = BetterDocs_Helper::list_query_arg('docs', BetterDocs_Multiple_Kb::$enable == 1, $term[0]->slug, -1, $alphabetic_order);
			$args = apply_filters('betterdocs_articles_args', $list_args, $term[0]->term_id);
			$query = new WP_Query($args);

			$docs_order = implode(',', array_map(function ($post) {
				return $post->ID;
			}, $query->posts));

			if ($docs_order) {
				$docs_order = explode(',', $docs_order);
				$docs_order_terms = array_values(array_filter($docs_order, function ($var) {
					return get_post_status($var) === 'publish';
				}));
				$nav .= $this->get_prev($docs_order_terms, $wp_query->queried_object->ID);
				$nav .= $this->get_next($docs_order_terms, $wp_query->queried_object->ID);
			} else {
				$nav .= previous_post_link('%link', '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 11.957031 13.988281 C 11.699219 14.003906 11.457031 14.117188 11.28125 14.308594 L 1.015625 25 L 11.28125 35.691406 C 11.527344 35.953125 11.894531 36.0625 12.242188 35.976563 C 12.589844 35.890625 12.867188 35.625 12.964844 35.28125 C 13.066406 34.933594 12.972656 34.5625 12.71875 34.308594 L 4.746094 26 L 48 26 C 48.359375 26.003906 48.695313 25.816406 48.878906 25.503906 C 49.058594 25.191406 49.058594 24.808594 48.878906 24.496094 C 48.695313 24.183594 48.359375 23.996094 48 24 L 4.746094 24 L 12.71875 15.691406 C 13.011719 15.398438 13.09375 14.957031 12.921875 14.582031 C 12.753906 14.203125 12.371094 13.96875 11.957031 13.988281 Z "></path></g></svg> %title', TRUE, ' ', 'doc_category');
				$nav .= next_post_link('%link', '%title <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 38.035156 13.988281 C 37.628906 13.980469 37.257813 14.222656 37.09375 14.59375 C 36.933594 14.96875 37.015625 15.402344 37.300781 15.691406 L 45.277344 24 L 2.023438 24 C 1.664063 23.996094 1.328125 24.183594 1.148438 24.496094 C 0.964844 24.808594 0.964844 25.191406 1.148438 25.503906 C 1.328125 25.816406 1.664063 26.003906 2.023438 26 L 45.277344 26 L 37.300781 34.308594 C 36.917969 34.707031 36.933594 35.339844 37.332031 35.722656 C 37.730469 36.105469 38.363281 36.09375 38.746094 35.691406 L 49.011719 25 L 38.746094 14.308594 C 38.5625 14.109375 38.304688 13.996094 38.035156 13.988281 Z "></path></g></svg>', TRUE, ' ', 'doc_category');
			}
		}
		echo $nav;
	}
}
