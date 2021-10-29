<?php
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
if (!class_exists('HMWP_blocked_ips_Table')) {

	class HMWP_blocked_ips_Table extends WP_List_Table {

		function __construct() {
			global $status, $page;
			parent::__construct(array(
				'singular' => 'hmwp_blocked_ip',
				'plural' => 'hmwp_blocked_ips',
			));
		}

		public function get_views() {
			global $wpdb, $current_user;
			$blocked_ips_table = $wpdb->prefix . 'hmwp_blocked_ips';
			$page_link = admin_url('admin.php?page=hmwp_blocked_ips');
			$views = array();
			$current = (!empty($_REQUEST['filterby']) ? $_REQUEST['filterby'] : 'all');
			$top_links = array('all' => __('All', 'hide_my_wp'), 'blocked' => __('Blocked IPs', 'hide_my_wp'), 'whitelist' => __('Whitelisted IP', 'hide_my_wp'));
			$totalCount = array(
				'all' => $wpdb->get_var("SELECT COUNT(id) FROM `{$blocked_ips_table}`"),
				'blocked' => $wpdb->get_var("SELECT COUNT(id) FROM `{$blocked_ips_table}` WHERE `allow`='0'"),
				'whitelist' => $wpdb->get_var("SELECT COUNT(id) FROM `{$blocked_ips_table}` WHERE `allow`='1'")
			);
			foreach ($top_links as $key => $val) {
				$class = ($current == $key ? ' class="current"' : '');
				$link = ($key != 'all') ? add_query_arg('filterby', $key, $page_link) : $page_link;
				$count = $totalCount[$key];
				$views[$key] = "<a href='{$link}' {$class} >{$val} <span class='count'>({$count})</span></a></a>";
			}
			return $views;
		}

		function column_default($item, $column_name) {
			return $item[$column_name];
		}

		function get_columns() {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'ip' => __('IP Address', 'hide_my_wp'),
				'source' => __('Source', 'hide_my_wp'),
				'created' => __('Date', 'hide_my_wp')
			);
			return $columns;
		}

		function get_sortable_columns() {
			$sortable_columns = array(
				'id' => array('id', false),
				'ip' => array('ip', true),
				'source' => array('source', true),
				'created' => array('created', true)
			);
			return $sortable_columns;
		}

		function single_row($item) {
			$rowClass = ($item['allow'] == '1') ? 'whitelisted' : 'banned';
			echo '<tr class="' . $rowClass . '">';
			$this->single_row_columns($item);
			echo '</tr>';
		}

		function column_cb($item) {
			return sprintf('<input type="checkbox" name="blocked_ip[]" value="%s" />', $item['id']);
		}

		function column_ip($item) {
			global $wpdb;
			$item_link = admin_url('admin.php?page=hmwp_blocked_ips&id=' . $item['id']);
			$actions = array();
			if ($item['allow'] == 1) {
				$ban_link = wp_nonce_url(add_query_arg('ip_action', 'ban_ip', $item_link), 'hmwp_blocked_ips_action_ban');
				$actions['ban'] = sprintf('<a href="%s">%s</a>', esc_url($ban_link), __('Ban this IP', 'hide_my_wp'));
			} else {
				$whitelist_link = wp_nonce_url(add_query_arg('ip_action', 'whitelist_ip', $item_link), 'hmwp_blocked_ips_action_whitelist');
				$actions['whitelist'] = sprintf('<a href="%s">%s</a>', esc_url($whitelist_link), __('Whitelist this IP', 'hide_my_wp'));
			}
			$delete_link = wp_nonce_url(add_query_arg('ip_action', 'delete_ip', $item_link), 'hmwp_blocked_ips_action_delete');
			$actions['delete'] = sprintf('<a href="%s" onclick="return confirm(\'Are you sure you want to delelte this IP?\');">%s</a>', esc_url($delete_link), __('Delete', 'hide_my_wp'));

			return sprintf('%s %s', $item['ip'], $this->row_actions($actions));
		}

		function column_source($item) {
			$source = $item['source'];
			switch ($item['source']) {
				case 'blocked_countries':
					$source = __('Blocked Countries', 'hide_my_wp');
					break;
				case 'allowed_countries':
					$source = __('Outside Allowed Countries', 'hide_my_wp');
					break;
				case 'trust_network':
					$source = __('Trust Network', 'hide_my_wp');
					break;
				case 'blocked_ips':
					$source = __('Manual Blocking', 'hide_my_wp');
					break;
				default:
					break;
			}
			return $source;
		}

		function column_created($item) {
			$format = get_option('date_format') . ' ' . get_option('time_format');
			return date($format, strtotime($item['created']));
		}

		function extra_tablenav($which) {
			global $wpdb, $testiURL, $tablename, $tablet;
			$source_filter = ( isset($_REQUEST['source_filter']) ? $_REQUEST['source_filter'] : '');
			if ($which == "top") {
				?>
				<div class="alignleft actions">
					<label for="filter-by-source" class="screen-reader-text"><?php _e('Filter by Source', 'hide_my_wp') ?></label>
					<select name="source_filter" class="source_filter" id="filter-by-source">
						<option value=""><?php _e('Filter by Source', 'hide_my_wp') ?></option>
						<option value="blocked_countries" <?php selected('blocked_countries', $source_filter); ?>><?php _e('Blocked Countries', 'hide_my_wp') ?></option>
						<option value="allowed_countries" <?php selected('allowed_countries', $source_filter); ?>><?php _e('Outside Allowed Countries', 'hide_my_wp') ?></option>
						<option value="trust_network" <?php selected('trust_network', $source_filter); ?>><?php _e('Trust Network', 'hide_my_wp') ?></option>
						<option value="blocked_ips" <?php selected('blocked_ips', $source_filter); ?>><?php _e('Manual Blocking', 'hide_my_wp') ?></option>
					</select>
					<input type="submit" name="filter_action" id="post-query-submit" class="button" value="<?php _e('Filter', 'hide_my_wp') ?>">
				</div>
				<?php
			}
			if ($which == "bottom") {
				//The code that goes after the table is there
			}
		}

		function get_bulk_actions() {
			$actions = array(
				'whitelist' => __('Whitelist', 'hide_my_wp'),
				'ban' => __('Ban', 'hide_my_wp'),
				'delete' => __('Delete', 'hide_my_wp')
			);
			return $actions;
		}

		function process_actions() {
			global $wpdb, $current_user;
			$blocked_ips_table = $wpdb->prefix . 'hmwp_blocked_ips';
			$page_link = admin_url('admin.php?page=hmwp_blocked_ips');
			$action = HMWP_MS_Utils::get('ip_action');
			$id = (int) HMWP_MS_Utils::get('id');
			if ($action && $id) {
				switch ($action) {
					case 'whitelist_ip':
						check_admin_referer('hmwp_blocked_ips_action_whitelist');
						if (!current_user_can('activate_plugins')) {
							wp_die(__('You are not allowed to modify this item.', 'hide_my_wp'));
						}
						$result = $wpdb->query($wpdb->prepare("UPDATE `{$blocked_ips_table}` SET `allow`='1' WHERE `id`= %d", $id));
						if (!$result) {
							wp_die(__('There is an error while whitelisting this IP...', 'hide_my_wp'));
						}
						$page_link = add_query_arg('whitelisted', 1, $page_link);
						break;
					case 'ban_ip':
						check_admin_referer('hmwp_blocked_ips_action_ban');
						if (!current_user_can('activate_plugins')) {
							wp_die(__('You are not allowed to modify this item.', 'hide_my_wp'));
						}
						$result = $wpdb->query($wpdb->prepare("UPDATE `{$blocked_ips_table}` SET `allow`='0' WHERE `id`= %d", $id));
						if (!$result) {
							wp_die(__('There is an error while banning this IP...', 'hide_my_wp'));
						}
						$page_link = add_query_arg('banned', 1, $page_link);
						break;
					case 'delete_ip':
						check_admin_referer('hmwp_blocked_ips_action_delete');
						if (!current_user_can('activate_plugins')) {
							wp_die(__('You are not allowed to delete this item.', 'hide_my_wp'));
						}
						$result = $wpdb->query($wpdb->prepare("DELETE FROM `{$blocked_ips_table}` WHERE id = %d", $id));
						if (!$result) {
							wp_die(__('There is an error while deleting this IP', 'hide_my_wp'));
						}
						$page_link = add_query_arg('deleted', 1, $page_link);
						break;
				}
				wp_redirect($page_link);
				exit;
			}
			/**
			 * Handle Bulk Action
			 */
			if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
				$nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
				$action = 'bulk-' . $this->_args['plural'];
				if (!wp_verify_nonce($nonce, $action)) {
					wp_die('Security check failed!');
				}
			}
			$bulk_action = $this->current_action();
			$ids = HMWP_MS_Utils::get('blocked_ip');
			if (!empty($ids)) {
				switch ($bulk_action) {
					case 'whitelist':
						foreach ($ids as $id) {
							$result = $wpdb->query($wpdb->prepare("UPDATE `{$blocked_ips_table}` SET `allow`='1' WHERE `id`= %d", $id));
						}
						$page_link = add_query_arg('whitelisted', count($ids), $page_link);
						break;
					case 'ban':
						foreach ($ids as $id) {
							$result = $wpdb->query($wpdb->prepare("UPDATE `{$blocked_ips_table}` SET `allow`='0' WHERE `id`= %d", $id));
						}
						$page_link = add_query_arg('banned', count($ids), $page_link);
						break;
					case 'delete':
						foreach ($ids as $id) {
							$result = $wpdb->query($wpdb->prepare("DELETE FROM `{$blocked_ips_table}` WHERE id = %d", $id));
						}
						$page_link = add_query_arg('deleted', count($ids), $page_link);
						break;
					default:
						break;
				}
				wp_redirect($page_link);
				exit;
			}
			return;
		}

		function usort_reorder($a, $b) {
			$orderby = (!empty($_GET['orderby']) ) ? $_GET['orderby'] : 'id';
			$order = (!empty($_GET['order']) ) ? $_GET['order'] : 'desc';
			$result = strcmp($a[$orderby], $b[$orderby]);
			return ( $order === 'asc' ) ? $result : -$result;
		}

		public function prepare_items($search = '') {
			global $wpdb, $current_user;
			$blocked_ips_table = $wpdb->prefix . 'hmwp_blocked_ips';
			/**
			 * Process Actions
			 */
			$this->process_actions();
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array($columns, $hidden, $sortable);
			$per_page = 20;
			$current_page = $this->get_pagenum();
			$offset = 0;
			if ($current_page > 1) {
				$offset = $per_page * ( $current_page - 1 );
			}

			$item_sql = "SELECT * FROM `{$blocked_ips_table}` WHERE 1=1 ";
			$where = "WHERE 1=1 ";
			$filterby = ( isset($_REQUEST['filterby']) ? $_REQUEST['filterby'] : '');
			if (!empty($filterby)) {
				if ($filterby == 'blocked') {
					$where .= " AND `allow`='0' ";
				} elseif ($filterby == 'whitelist') {
					$where .= " AND `allow`='1' ";
				}
			}
			$source_filter = ( isset($_REQUEST['source_filter']) ? $_REQUEST['source_filter'] : '');
			if (!empty($source_filter)) {
				$where .= " AND `source`='{$source_filter}' ";
			}
			if (!empty($search)) {
				$where .= " AND (ip LIKE '%{$search}%' OR source LIKE '%{$search}%')";
			}
			$total = $wpdb->get_var("SELECT COUNT(id) FROM `{$blocked_ips_table}` {$where};");
			$items = $wpdb->get_results("SELECT * FROM `{$blocked_ips_table}` {$where} ORDER BY id DESC LIMIT {$per_page} OFFSET {$offset};", ARRAY_A);

			usort($items, array(&$this, 'usort_reorder'));

			$this->items = $items;
			$this->set_pagination_args(array(
				'total_items' => $total,
				'per_page' => $per_page,
				'total_pages' => ceil($total / $per_page)
			));
		}

	}

}