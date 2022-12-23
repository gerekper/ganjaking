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
		add_action('betterdocs_single_post_nav', array($this, 'single_post_nav'));
        add_filter('betterdocs_highlight_admin_menu', array($this, 'highlight_admin_menu'), 1);
        add_filter('betterdocs_highlight_admin_submenu', array($this, 'highlight_admin_submenu'), 1);
		add_action('betterdocs_doc_category_add_form_after', array($this, 'add_categoey_thumb'));
		add_action('betterdocs_doc_category_update_form_after', array($this, 'update_categoey_thumb'), 10, 1);
		add_filter('betterdocs_advanced_settings_sections', array( $this, 'enable_internal_kb_fields' ), 10, 1 );
	}

    public function body_classes($classes)
    {
        $classes .= 'betterdocs-pro';
        return $classes;
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
			'page_title'  => __('Multiple KB', 'betterdocs-pro'),
			'menu_title'  => __('Multiple KB', 'betterdocs-pro'),
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


	public function enable_internal_kb_fields( $settings ) {
		unset( $settings['internal_kb_section']['fields']['enable_content_restriction']['disable'] );
		unset( $settings['internal_kb_section']['fields']['content_visibility']['disable'] );
		unset( $settings['internal_kb_section']['fields']['restrict_template']['disable'] );
		unset( $settings['internal_kb_section']['fields']['restrict_category']['disable'] );
		unset( $settings['internal_kb_section']['fields']['restricted_redirect_url']['disable'] );
		return $settings;
	}

	public function get_prev($array, $key)
	{
		$currentKey = array_search($key, $array);
		if ($currentKey > 0 || $currentKey != 0) {
			$nextKey = $currentKey - 1;
			$prev_post = $array[$nextKey];
			$nav = '<a rel="prev" class="next-post" href="' . get_post_permalink($prev_post) . '"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 11.957031 13.988281 C 11.699219 14.003906 11.457031 14.117188 11.28125 14.308594 L 1.015625 25 L 11.28125 35.691406 C 11.527344 35.953125 11.894531 36.0625 12.242188 35.976563 C 12.589844 35.890625 12.867188 35.625 12.964844 35.28125 C 13.066406 34.933594 12.972656 34.5625 12.71875 34.308594 L 4.746094 26 L 48 26 C 48.359375 26.003906 48.695313 25.816406 48.878906 25.503906 C 49.058594 25.191406 49.058594 24.808594 48.878906 24.496094 C 48.695313 24.183594 48.359375 23.996094 48 24 L 4.746094 24 L 12.71875 15.691406 C 13.011719 15.398438 13.09375 14.957031 12.921875 14.582031 C 12.753906 14.203125 12.371094 13.96875 11.957031 13.988281 Z "></path></g></svg>' . wp_kses(get_the_title($prev_post), BETTERDOCS_PRO_KSES_ALLOWED_HTML) . '</a>';
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
			$nav = '<a rel="next" class="next-post" href="' . get_post_permalink($next_post) . '">' . wp_kses(get_the_title($next_post), BETTERDOCS_PRO_KSES_ALLOWED_HTML) . '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 38.035156 13.988281 C 37.628906 13.980469 37.257813 14.222656 37.09375 14.59375 C 36.933594 14.96875 37.015625 15.402344 37.300781 15.691406 L 45.277344 24 L 2.023438 24 C 1.664063 23.996094 1.328125 24.183594 1.148438 24.496094 C 0.964844 24.808594 0.964844 25.191406 1.148438 25.503906 C 1.328125 25.816406 1.664063 26.003906 2.023438 26 L 45.277344 26 L 37.300781 34.308594 C 36.917969 34.707031 36.933594 35.339844 37.332031 35.722656 C 37.730469 36.105469 38.363281 36.09375 38.746094 35.691406 L 49.011719 25 L 38.746094 14.308594 C 38.5625 14.109375 38.304688 13.996094 38.035156 13.988281 Z "></path></g></svg></a>';
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

		$terms = get_the_terms($post_id, 'doc_category');
		if ( $terms && ! is_wp_error( $terms ) ) {
			// $docs_order = (isset($term[0]->slug) && $term[0]->slug != 'uncategorized') ? rtrim(get_term_meta($term[0]->term_id, '_docs_order', true), ',') : '';
			$alphabetic_order = BetterDocs_DB::get_settings('alphabetically_order_post');
			$list_args = BetterDocs_Helper::list_query_arg('docs', BetterDocs_Multiple_Kb::$enable == 1, $terms[0]->slug, -1, $alphabetic_order);
			$args = apply_filters('betterdocs_articles_args', $list_args, $terms[0]->term_id);
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

    public function add_categoey_thumb()
    {
        echo '<div class="form-field term-group">
            <label for="doc-category-image-thumb">' . esc_html__('Category Cover Image for Handbook Layout', 'betterdocs-pro') . '</label>
            <input type="hidden" class="doc-category-image-id" name="term_meta[thumb-id]" value="">
            <div class="doc-category-image-wrapper betterdocs-category-thumb">
                <img width="100" src="' . BETTERDOCS_PRO_ADMIN_URL . 'assets/img/cat-grid-2.png" alt="">
            </div>
            <p>
                <input type="button" class="button button-secondary betterdocs_tax_media_button"
                    id="betterdocs_cat_thumb_button" name="betterdocs_cat_thumb_button"
                    value="' . esc_html__('Add Image', 'betterdocs-pro') . '" />
                <input type="button" class="button button-secondary doc_tax_media_remove" id="doc_cat_thumb_remove"
                    name="doc_cat_thumb_remove"
                    value="' . esc_html__('Remove Image', 'betterdocs-pro') . '" />
            </p>
        </div>';
    }

    public function update_categoey_thumb($term)
    {
        $cat_thumb_id = get_term_meta($term->term_id, 'doc_category_thumb-id', true);
    ?>
        <tr class="form-field term-group-wrap batterdocs-cat-media-upload">
            <th scope="row">
                <label><?php esc_html_e('Category Cover Image for Handbook Layout', 'betterdocs-pro'); ?></label>
            </th>
            <td>
                <input type="hidden" class="doc-category-image-id" name="term_meta[thumb-id]" value="<?php echo esc_attr($cat_thumb_id); ?>">
                <div class="doc-category-image-wrapper betterdocs-category-thumb">
                    <?php
                    if ($cat_thumb_id) {
                        echo wp_get_attachment_image($cat_thumb_id, 'thumbnail');
                    } else {
                        echo '<img width="100" src="' . BETTERDOCS_PRO_ADMIN_URL . 'assets/img/cat-grid-2.png" alt="">';
                    }
                    ?>
                </div>
                <p>
                    <input type="button" class="button button-secondary betterdocs_tax_media_button" id="betterdocs_tax_media_button" name="betterdocs_tax_media_button" value="<?php esc_html_e('Add Image', 'betterdocs-pro'); ?>" />
                    <input type="button" class="button button-secondary doc_tax_media_remove" id="doc_tax_media_remove" name="doc_tax_media_remove" value="<?php esc_html_e('Remove Image', 'betterdocs-pro'); ?>" />
                </p>
            </td>
        </tr>
    <?php }
}
