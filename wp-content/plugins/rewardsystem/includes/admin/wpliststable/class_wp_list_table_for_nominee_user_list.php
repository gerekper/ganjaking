<?php

// Integrate WP List Table for Master Log

if (!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ;
}

class WP_List_Table_For_Nominee extends WP_List_Table {

	// Prepare Items
	public function prepare_items() {
		global $wpdb;

		$this->process_bulk_action();
		$columns = $this->get_columns();

		$hidden = $this->get_hidden_columns();

		$user = get_current_user_id();
		$screen = get_current_screen();
		$perPage = RSTabManagement::rs_get_value_for_no_of_item_perpage($user, $screen);
		$currentPage = $this->get_pagenum();
		$newdata = $wpdb->get_results("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='rs_selected_nominee' AND meta_value != ''", ARRAY_A);
		$num_rows = count($newdata);
		$data = $this->table_data();
		
		usort($data, array( &$this, 'sort_data' ));        
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args(array(
			'total_items' => $num_rows,
			'per_page' => $perPage,
		));

		$data = array_slice($data, ( ( $currentPage - 1 ) * $perPage ), $perPage);

		$this->_column_headers = array( $columns, $hidden );

		$this->items = $data;
	}

	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'sno' => __('S.No', 'rewardsystem'),
			'buyer' => __('Buyer', 'rewardsystem'),
			'nominee' => __('Nominee', 'rewardsystem'),
			'action' => __('Action', 'rewardsystem'),
		);

		return $columns;
	}

	public function get_hidden_columns() {
		return array();
	}

	public function column_cb( $item ) {
		return sprintf(
				'<input type="checkbox" name="id[]" value="%s" />', $item['cb']
		);
	}

	public function get_bulk_actions() {
		$columns = array(
			'delete' => __('Delete', 'rewardsystem'),
			'delete_all' => __('Delete All', 'rewardsystem'),
		);

		return $columns;
	}

	public function process_bulk_action() {
		global $wpdb;
		$getusers = $wpdb->get_results("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='rs_selected_nominee' AND meta_value != ''", ARRAY_A);
		if ('delete_all' === $this->current_action()) {
			if (is_array($getusers) && !empty($getusers)) {
				foreach ($getusers as $eachuser) {
					$user_id = isset($eachuser['user_id']) ? $eachuser['user_id'] : '0';
					update_user_meta($user_id, 'rs_selected_nominee', '');
				}
			}
		} elseif ('delete' === $this->current_action()) {
			$newupdates = array();
			$ids = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : array();
			if (is_array($ids) && !empty($ids)) {
				foreach ($ids as $id) {
					update_user_meta($id, 'rs_selected_nominee', '');
				}
			}
		}
	}

	private function table_data() {
		global $wpdb;
		$data = array();
		$i = 1;
		$getusers = $wpdb->get_results("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='rs_selected_nominee' AND meta_value != ''", ARRAY_A);
		if (is_array($getusers) && !empty($getusers)) {
			foreach ($getusers as $eachuser) {
				$user_id = isset($eachuser['user_id']) ? $eachuser['user_id'] : '0';
				$buyer_name = get_user_by('id', $user_id);
				$getnominee = get_user_meta($user_id, 'rs_selected_nominee', true);
				if (!empty($getnominee)) {
					$nominee_id = get_user_by('id', $getnominee);
					$nominee_name = is_object($nominee_id) ? $nominee_id->user_login : 'Guest';
					$checked = '';
					if ('yes' == get_user_meta($user_id, 'rs_enable_nominee', true)) {
						$checked = "checked='checked'";
					} else {
						$checked = '';
					}
					$data[] = array(
						'cb' => $user_id,
						'sno' => $i,
						'buyer' => is_object($buyer_name) ? $buyer_name->user_login : 'Guest',
						'nominee' => $nominee_name,
						'action' => '<label class="switch"><input type="checkbox" class="rs_enable_disable" ' . $checked . '" id="rs_enable_disable" data-userid="' . $user_id . '" data-nomineeid="' . $getnominee . '"><div class="slider round"></div></label>',
					);
					$i++;
				}
			}
		}
		return $data;
	}

	public function column_id( $item ) {
		return $item['sno'];
	}

	public function column_default( $item, $column_name ) {
		switch ($column_name) {
			case 'sno':
			case 'buyer':
			case 'nominee':
			case 'action':
				return $item[$column_name];
			default:
				return print_r($item, true);
		}
	}

	private function sort_data( $a, $b ) {

		$orderby = 'sno';
		$order = 'asc';

		if (!empty($_GET['orderby'])) {
			$orderby = wc_clean(wp_unslash($_GET['orderby']));
		}

		if (!empty($_GET['order'])) {
			$order = wc_clean(wp_unslash($_GET['order']));
		}
		$result = strnatcmp($a[$orderby], $b[$orderby]);

		if ('asc' == $order) {
			return $result;
		}

		return -$result;
	}
}
