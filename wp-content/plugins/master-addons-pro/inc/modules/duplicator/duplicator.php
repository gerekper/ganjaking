<?php

namespace MasterAddons\Modules;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 7/4/2020
 */
if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Extension_Post_Page_Duplicator
{

	private static $instance = null;

	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct()
	{
		add_action('admin_action_jltma_duplicate', [$this, 'jltma_duplicate_post_as_draft']);
		add_filter('post_row_actions', [$this, 'jltma_duplicator_row_actions'], 10, 2);
		add_filter('page_row_actions', [$this, 'jltma_duplicator_row_actions'], 10, 2);
	}


	/*
	 * Add the duplicate link to action list for post_row_actions
	 */
	public function jltma_duplicator_row_actions($actions, $post)
	{
		if (current_user_can('edit_posts')) {

			$jltma_duplicate_link = admin_url('admin.php?action=jltma_duplicate&post=' . $post->ID);
			$jltma_duplicate_link = wp_nonce_url($jltma_duplicate_link, 'jltma_post_duplicator_nonce');

			$actions['jltma_duplicate'] = sprintf('<a href="%s" title="%s" rel="permalink">%s</a>', $jltma_duplicate_link,  __('Duplicate this item ' . $post->post_title, MELA_TD), esc_html__('MA Duplicator', MELA_TD));
		}
		return $actions;
	}


	/*
	 * Function creates post duplicate as a draft and redirects then to the edit post screen
	 */
	public function jltma_duplicate_post_as_draft()
	{

		global $wpdb;

		if (!(isset($_GET['post']) || isset($_POST['post'])  || (isset($_REQUEST['action']) && 'jltma_duplicate' == $_REQUEST['action']))) {
			wp_die('No post to duplicate has been supplied!');
		}

		/*
		 * Nonce verification
		 * Return if nonce is not valid
		 */
		if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'jltma_post_duplicator_nonce')) {
			return;
		}

		/*
		 * get the original post id
		 */
		$post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
		/*
		 * and all the original post data then
		 */
		$post = get_post($post_id);

		/*
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;

		/*
		 * if post data exists, create the post duplicate
		 */
		if (isset($post) && $post != null) {

			/*
			 * new post data array
			 */
			$args = array(
				'post_title'     => $post->post_title,
				'post_type'      => $post->post_type,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_author'    => $new_post_author,
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);

			/*
			 * insert the post by wp_insert_post() function
			 */
			$new_post_id = wp_insert_post($args);

			/*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
				wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
			}

			/*
			 * duplicate all post meta just in two SQL queries
			 */
			$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
			if (count($post_meta_infos) != 0) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach ($post_meta_infos as $meta_info) {
					$meta_key = $meta_info->meta_key;
					if ($meta_key == '_wp_old_slug') continue;
					$meta_value = addslashes($meta_info->meta_value);
					$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}
				$sql_query .= implode(" UNION ALL ", $sql_query_sel);
				$wpdb->query($sql_query);
			}


			/*
			 * finally, redirect to the edit post screen for the new draft
			 */
			$redirect_url = admin_url('edit.php?post_type=' . $post->post_type);
			wp_safe_redirect($redirect_url);

			exit;
		} else {
			wp_die('Post creation failed, could not find original post: ' . $post_id);
		}
	}
}

Extension_Post_Page_Duplicator::get_instance();
