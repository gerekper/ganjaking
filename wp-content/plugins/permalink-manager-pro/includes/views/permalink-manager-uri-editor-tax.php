<?php
/**
* Permalink Manager Pro: Custom class for Permalink Editor for Terms
*/
class Permalink_Manager_Tax_Uri_Editor_Table extends WP_List_Table {

	public function __construct() {
		global $status, $page;

		parent::__construct(array(
			'singular'	=> 'slug',
			'plural'	=> 'slugs'
		));
	}

	/**
	* Get the HTML output with the WP_List_Table
	*/
	public function display_admin_section() {
		global $wpdb;

		$output = "<form id=\"permalinks-post-types-table\" class=\"slugs-table\" method=\"post\">";
		$output .= wp_nonce_field('permalink-manager', 'uri_editor', true, true);
		$output .= Permalink_Manager_Admin_Functions::generate_option_field('pm_session_id', array('value' => uniqid(), 'type' => 'hidden'));
		$output .= Permalink_Manager_Admin_Functions::section_type_field('taxonomies');

		// Bypass
		ob_start();

		$this->prepare_items();
		$this->display();
		$output .= ob_get_contents();

		ob_end_clean();

		$output .= "</form>";

		return $output;
	}

	function get_table_classes() {
		return array( 'widefat', 'striped', $this->_args['plural'] );
	}

	/**
	* Override the parent columns method. Defines the columns to use in your listing table
	*/
	public function get_columns() {
		return apply_filters('permalink_manager_uri_editor_columns', array(
			//'cb'				=> '<input type="checkbox" />', //Render a checkbox instead of text
			'item_title'		=> __('Term title', 'permalink-manager'),
			'item_uri'	=> __('Full URI & Permalink', 'permalink-manager'),
			'count'	=> __('Count', 'permalink-manager'),
		));
	}

	/**
	* Hidden columns
	*/
	public function get_hidden_columns() {
		return array('post_date_gmt');
	}

	/**
	* Sortable columns
	*/
	public function get_sortable_columns() {
		return array(
			'item_title' => array('name', false)
		);
	}

	/**
	* Data inside the columns
	*/
	public function column_default($item, $column_name) {
		global $permalink_manager_options;

		$uri = Permalink_Manager_URI_Functions_Tax::get_term_uri($item['term_id'], true);
		$uri = (!empty($permalink_manager_options['general']['decode_uris'])) ? urldecode($uri) : $uri;

		$field_args_base = array('type' => 'text', 'value' => $uri, 'without_label' => true, 'input_class' => 'custom_uri', 'extra_atts' => "data-element-id=\"tax-{$item['term_id']}\"");
		$term = get_term($item['term_id']);
		$permalink = get_term_link(intval($item['term_id']), $item['taxonomy']);
		$all_terms_link = admin_url("edit.php?{$term->taxonomy}={$term->slug}");

		$output = apply_filters('permalink_manager_uri_editor_column_content', '', $column_name, $term);
		if(!empty($output)) { return $output; }

		switch($column_name) {

			case 'item_title':
				$output = $item[ 'name' ];
				$output .= '<div class="extra-info small">';
				$output .= sprintf("<span><strong>%s:</strong> %s</span>", __("Slug", "permalink-manager"), urldecode($item['slug']));
				$output .= apply_filters('permalink_manager_uri_editor_extra_info', '', $column_name, $term);
				$output .= '</div>';

				$output .= '<div class="row-actions">';
				$output .= sprintf("<span class=\"edit\"><a href=\"%s\" title=\"%s\">%s</a> | </span>", esc_url(get_edit_tag_link($item['term_id'], $item['taxonomy'])), __('Edit', 'permalink-manager'), __('Edit', 'permalink-manager'));
				$output .= '<span class="view"><a target="_blank" href="' . $permalink . '" title="' . __('View', 'permalink-manager') . ' ' . $item[ 'name' ] . '" rel="permalink">' . __('View', 'permalink-manager') . '</a> | </span>';
				$output .= '<span class="id">#' . $item[ 'term_id' ] . '</span>';
				$output .= '</div>';
				return $output;

			case 'item_uri':
				// Get auto-update settings
				$auto_update_val = get_term_meta($item['term_id'], "auto_update_uri", true);
				$auto_update_uri = (!empty($auto_update_val)) ? $auto_update_val : $permalink_manager_options["general"]["auto_update_uris"];
				if($auto_update_uri) {
					$field_args_base['readonly'] = true;
					$field_args_base['append_content'] = sprintf('<p class="small uri_locked">%s %s</p>', '<span class="dashicons dashicons-lock"></span>', __('The above permalink will be automatically updated and is locked for editing.', 'permalink-manager'));
				}

				$output .= '<div class="custom_uri_container">';
				$output .= Permalink_Manager_Admin_Functions::generate_option_field("uri[tax-{$item['term_id']}]", $field_args_base);
				$output .= "<span class=\"duplicated_uri_alert\"></span>";
				$output .= sprintf("<a class=\"small post_permalink\" href=\"%s\" target=\"_blank\"><span class=\"dashicons dashicons-admin-links\"></span> %s</a>", $permalink, urldecode($permalink));
				$output .= '</div>';
				return $output;

			case 'count':
				return "<a href=\"{$all_terms_link}\">{$term->count}</a>";

			default:
				return $item[$column_name];
		}
	}

	/**
	* Sort the data
	*/
	private function sort_data($a, $b) {
		// Set defaults
		$orderby = (!empty($_GET['orderby'])) ? sanitize_sql_orderby($_GET['orderby']) : 'name';
		$order = (!empty($_GET['order'])) ? sanitize_sql_orderby($_GET['order']) : 'asc';
		$result = strnatcasecmp( $a[$orderby], $b[$orderby] );

		return ($order === 'asc') ? $result : -$result;
	}

	/**
	* The button that allows to save updated slugs
	*/
	function extra_tablenav($which) {
		$button_top = __( 'Save all the URIs below', 'permalink-manager' );
		$button_bottom = __( 'Save all the URIs above', 'permalink-manager' );

		$html = '<div class="alignleft actions">';
		$html .= get_submit_button( ${"button_$which"}, 'primary', "update_all_slugs[{$which}]", false, array( 'id' => 'doaction', 'value' => 'update_all_slugs' ) );
		$html .= '</div>';

		if($which == 'top') {
			$html .= '<div class="alignleft">';
			$html .= $this->search_box(__('Search', 'permalink-manager'), 'search-input');
			$html .= '</div>';
		}

		echo $html;
	}

	/**
	* Search box
	*/
	public function search_box($text = '', $input_id = '') {
		$search_query = (!empty($_REQUEST['s'])) ? esc_attr($_REQUEST['s']) : "";

    $output = "<p class=\"search-box\">";
		$output .= "<label class=\"screen-reader-text\" for=\"{$input_id}\">{$text}:</label>";
		$output .= Permalink_Manager_Admin_Functions::generate_option_field('s', array('value' => $search_query, 'type' => 'search'));
		$output .= get_submit_button($text, 'button', false, false, array('id' => 'search-submit', 'name' => 'search-submit'));
  	$output .= "</p>";

		return $output;
	}

	/**
	* Prepare the items for the table to process
	*/
	public function prepare_items() {
		global $wpdb, $permalink_manager_options, $permalink_manager_uris, $active_subsection, $current_admin_tax;

		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$current_page = $this->get_pagenum();

		// Get query variables
		$per_page = $permalink_manager_options['screen-options']['per_page'];
		$taxonomies_array = Permalink_Manager_Helper_Functions::get_taxonomies_array();
		$taxonomies = ($active_subsection && $active_subsection == 'all_taxs') ? "'" . implode("', '", $taxonomies_array) . "'" : "'{$current_admin_tax}'";
		$search_query = (!empty($_REQUEST['s'])) ? esc_sql($_REQUEST['s']) : "";

		// SQL query parameters
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? sanitize_sql_orderby($_REQUEST['order']) : 'desc';
		$orderby = (isset($_REQUEST['orderby'])) ? sanitize_sql_orderby($_REQUEST['orderby']) : 't.term_id';
		$offset = ($current_page - 1) * $per_page;

		// Grab terms from database
		$sql_parts['start'] = "SELECT t.*, tt.taxonomy FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tt.term_id = t.term_id) ";
		if($search_query) {
			$sql_parts['where'] = "WHERE (LOWER(t.name) LIKE LOWER('%{$search_query}%') ";

			// Search in array with custom URIs
			$found = Permalink_Manager_Helper_Functions::search_uri($search_query, 'taxonomies');
			if($found) {
				$sql_parts['where'] .= sprintf("OR t.term_id IN (%s) ", implode(',', (array) $found));
			}
			$sql_parts['where'] .= ") AND tt.taxonomy IN ({$taxonomies}) ";
		} else {
			$sql_parts['where'] = "WHERE tt.taxonomy IN ({$taxonomies}) ";
		}

		// Do not display excluded terms in Bulk URI Editor
		$excluded_terms = (array) apply_filters('permalink_manager_excluded_term_ids', array());
		if(!empty($excluded_terms)) {
			$sql_parts['where'] .= sprintf("AND t.term_id NOT IN ('%s') ", implode("', '", $excluded_terms));
		}

		$sql_parts['end'] = "ORDER BY {$orderby} {$order}";

		// Prepare the SQL query
		$sql_query = implode("", $sql_parts);

		// Count items
		$count_query = preg_replace('/SELECT(.*)FROM/', 'SELECT COUNT(*) FROM', $sql_query);
		$total_items = $wpdb->get_var($count_query);

		// Pagination support
		$sql_query .= sprintf(" LIMIT %d, %d", $offset, $per_page);

		// Get items
		$sql_query = apply_filters('permalink_manager_filter_uri_editor_query', $sql_query, $this, $sql_parts, $is_taxonomy = true);
		$all_items = $wpdb->get_results($sql_query, ARRAY_A);

		// Debug SQL query
		if(isset($_REQUEST['debug_editor_sql'])) {
			$debug_txt = "<textarea style=\"width:100%;height:300px\">{$sql_query} \n\nOffset: {$offset} \nPage: {$current_page}\nPer page: {$per_page} \nTotal: {$total_items}</textarea>";
			wp_die($debug_txt);
		}

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		));

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $all_items;
	}

}
