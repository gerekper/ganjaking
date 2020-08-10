<?php
/*
	Copyright (C) 2015-20 CERBER TECH INC., https://cerber.tech
	Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com

    Licenced under the GNU GPL.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/*

*========================================================================*
|                                                                        |
|	       ATTENTION!  Do not change or edit this file!                  |
|                                                                        |
*========================================================================*

*/

if ( ! defined( 'WPINC' ) ) { exit; }

// Only this filter lets user manage table columns. See render_list_table_columns_preferences()
add_filter( "manage_wp-cerber_page_cerber-nexus_columns", 'nexus_slave_list_cols' );
function nexus_slave_list_cols() {
	if ( ! crb_get_configurable_screen() ) {
		return array();
	}
	$cols = array(
		'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
		'site_name'   => __( 'Website', 'wp-cerber' ),
		'site_url'    => 'Homepage',
		//'site_status' => __( 'Status', 'wp-cerber' ),
		'wp_v'        => __( 'WordPress', 'wp-cerber' ),
		'plugin_v'    => 'WP Cerber',
		//'new_users'  => __( 'New Users', 'wp-cerber' ),
		'updates'     => __( 'Updates', 'wp-cerber' ),
		'last_scan'   => __( 'Malware Scan', 'wp-cerber' ),
		'srv_name'    => __( 'Server', 'wp-cerber' ),
		'srv_country' => __( 'Server Country', 'wp-cerber' ),
		'site_grp'    => __( 'Group', 'wp-cerber' ),
		'site_owner'  => __( 'Owner', 'wp-cerber' ),
		'site_notes'  => __( 'Notes', 'wp-cerber' ),
	);

	if ( ! lab_lab() ) {
		unset( $cols['server_country'] );
	}

    return $cols;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CRB_Slave_Table extends WP_List_Table {
    private $settings;
	private $show_url;
	private $hide_ip;
	private $base_switch;
    private $base_sites;

	function __construct() {
		parent::__construct( array(
			'singular' => 'Site',
			'plural'   => 'Sites',
			'ajax'     => false
		) );

		$this->settings    = get_site_option( '_cerber_slist_screen', array() );
		$this->show_url    = crb_array_get( $this->settings, 'url_name' );
		$this->hide_ip     = crb_array_get( $this->settings, 'srv_ip' );
		$this->base_switch = wp_nonce_url( cerber_admin_link() . '&cerber_admin_do=nexus_switch&back=' . urlencode( $_SERVER['REQUEST_URI'] ), 'control', 'cerber_nonce' );
		$this->base_sites  = cerber_admin_link( 'nexus_sites' );
	}

	// Columns definition
	function get_columns() {
		return nexus_slave_list_cols();
	}

	/*protected function get_default_primary_column_name() {
		return 'site_name';
	}*/

	// Sortable columns
	function get_sortable_columns() {
		return array(
			'site_name'   => array( 'site_name', false ), // true means dataset is already sorted by ASC
			'site_url'    => array( 'site_url', false ),
			'srv_name'    => array( 'server_id', false ),
			'srv_country' => array( 'server_country', false ),
			'wp_v'        => array( 'wp_v', false ),
			'plugin_v'    => array( 'plugin_v', false ),
			'updates'     => array( 'updates', false ),
			'last_scan'   => array( 'last_scan', false ),
			'site_grp'    => array( 'group_id', false ),
		);
	}
	// Bulk actions
	function get_bulk_actions() {
		return array(
			'nexus_upgrade_cerber'  => __( 'Upgrade WP Cerber', 'wp-cerber' ),
			'nexus_upgrade_plugins' => __( 'Upgrade all active plugins', 'wp-cerber' ),
			'nexus_delete_slave'    => __( 'Delete website', 'wp-cerber' ),
		);
	}

	protected function extra_tablenav( $which ) {

		if ( $which == 'top' ) {
			?>

            <div class="alignleft actions">
				<?php

				$filter = '';

				$groups = nexus_get_groups( true );
				if ( count( $groups ) > 1 ) {
					$groups = array( '-1' => __( 'All groups', 'wp-cerber' ) ) + $groups;
					$filter .= cerber_select( 'filter_group_id', $groups, crb_array_get( $_GET, 'filter_group_id', '-1', '\d+' ) );
				}

				$servers = cerber_get_set( 'nexus_servers' );
				if ( count( $servers ) > 1 ) {
					$list = array();
					foreach ( $servers as $id => $server ) {
						$list[ $id ] = $server[1];
					}
					$list = array( '*' => __( 'All servers', 'wp-cerber' ) ) + $list;
					$filter .= cerber_select( 'filter_server_id', $list, crb_array_get( $_GET, 'filter_server_id', '*', '[\w\.\:]+' ) );
				}

				//$countries = wp_cache_get( 'cerber_nexus', 'countries' );
				$countries = cerber_get_set( 'nexus_countries' );
				if ( count( $countries ) > 1 ) {
					$list = array( '*' => __( 'All countries', 'wp-cerber' ) ) + $countries;
					$filter .= cerber_select( 'filter_country', $list, crb_array_get( $_GET, 'filter_country', '*', '\w+' ) );
				}

				if ( $filter ) {
					echo '<div id="crb-top-filter">' . $filter . '<input type="submit" value="Filter" class="button button-primary action"></div>';
				}

				// for_tb_blur is for removing focus from closing button
				echo '<input type="button" alt="' . CRB_ADD_SLAVE_LNK . '" title="' . __( 'Add a slave website', 'wp-cerber' ) . '" class="thickbox button" value="Add">';
				?>
                <script>
                    jQuery(document).ready(function ($) {
                        $('.thickbox').on('click', function () {
                            setTimeout(function () {
                                $('#TB_closeWindowButton').blur();
                            }, 50);
                        });
                    });
                </script>
            </div>

			<?php
		}
	}

		// Retrieve data from the DB
	function prepare_items() {
		global $_wp_column_headers;
		// pagination
		$per_page = crb_admin_get_per_page();

		$table       = cerber_get_db_prefix() . CERBER_MS_TABLE;
		$where       = array();
		$join        = '';
		$total_items = 0;

		// Sorting
		$orderby = crb_array_get( $_REQUEST, 'orderby', 'id' );
		$order = crb_array_get( $_REQUEST, 'order', 'DESC' );
		$orderby = sanitize_sql_orderby( $orderby . ' ' . $order ); // !works only with fields, not tables references!
		$orderby = ' ORDER BY ' . $table . '.' . $orderby . ' ';

		// Pagination, part 1, SQL
		$current_page = $this->get_pagenum();
		if ( $current_page > 1 ) {
			$offset = ( $current_page - 1 ) * $per_page;
			$limit  = ' LIMIT ' . $offset . ',' . $per_page;
		}
		else {
			$limit = 'LIMIT ' . $per_page;
		}

		if ( $group_id = cerber_get_get( 'filter_group_id', '\d+' ) ) {
			$where[] = 'group_id = ' . absint( $group_id );
		}

		if ( $server_id = cerber_get_get( 'filter_server_id', '[\w\.\:]+' ) ) {
			$where[] = 'server_id = "' . cerber_real_escape( $server_id ) . '"';
		}

		if ( $country = cerber_get_get( 'filter_country', '\w+' ) ) {
			$where[] = 'server_country = "' . cerber_real_escape( $country ) . '"';
		}

		// Search
		if ( $term = cerber_get_get( 's' ) ) {
			$term = stripslashes( $term );
			$s = '"%' . cerber_real_escape( $term ) . '%"';
			if ( preg_match( '/[^A-Z\d\-\/\.\:]/i', $term ) ) {
				// Mixing columns with different collations for non-latin symbols generates MySQL error
				$where[] = ' (site_name LIKE ' . $s . ' OR site_name_remote LIKE ' . $s . ' OR site_notes LIKE ' . $s . ' OR details LIKE ' . $s . ') ';
			}
			else {
				$where[] = ' (site_name LIKE ' . $s . ' OR site_name_remote LIKE ' . $s . ' OR site_notes LIKE ' . $s . ' OR details LIKE ' . $s . ' OR site_url LIKE ' . $s . ') ';
			}
		}

		if ( ! empty( $where ) ) {
			$where = ' WHERE ' . implode( ' AND ', $where );
		}
		else {
			$where = '';
		}

		// Retrieving actual data

		$query = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$table . " $join $where $orderby $limit";

		if ( $this->items = cerber_db_get_results( $query ) ) {
			$total_items = cerber_db_get_var( 'SELECT FOUND_ROWS()' );
		}

		if ( ! empty( $term ) ) {
			echo '<div style="margin-top:15px;"><b>' . __( 'Search results for:', 'wp-cerber' ) . '</b> “' . htmlspecialchars( $term ) . '”</div>';
		}

		// Pagination, part 2
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );

		/*$this->set_pagination_args( array(
			'total_items' => count( $themes ),
			'per_page' => $per_page,
			'infinite_scroll' => true,
		) );*/
	}

	function single_row( $item ) {
		echo '<tr class="crb-slave-site" data-slave-id="' . $item['id'] . '" data-slave-name="' . $item['site_name'] . '">';
		if ( ! empty( $item['details'] ) ) {
			$item['details'] = unserialize( $item['details'] );
		}
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	function column_cb( $item ) {
		return '<input type="checkbox" name="ids[]" value="' . $item['id'] . '" />';
	}

	function column_site_name( $item ) {

		$set     = array();

		$edit = cerber_admin_link( 'nexus_sites', array( 'site_id' => $item['id'] ) );
		$set['edit'] = '<a href="' . $edit . '">' . __( 'Edit', 'wp-cerber' ) . '</a>';

		//$login        = $item['site_url'] ;
		//$set['login'] = ' <a href="' . $login . '" target="_blank">' . __( 'Log in', 'wp-cerber' ) . '</a>';

		$switch        = $this->base_switch . '&nexus_site_id=' . $item['id'];
		$set['switch'] = ' <a href="' . $switch . '">' . __( 'Switch to', 'wp-cerber' ) . '</a>';

		/*$set['delete'] = '<a href="' . wp_nonce_url( add_query_arg( array(
				'cerber_admin_do' => 'nexus_delete_slave',
				'site_id'         => $item['id']
			) ), 'control', 'cerber_nonce' ) . '" ' . $onclick . '>' . __( 'Delete', 'wp-cerber' ) . '</a>';
		*/

		$url = ( $this->show_url ) ? '<div class="crb-slave-url">' . $item['site_url'] . '</div>' : '';

		return '<strong><a class="row-title" href="' . $switch . '">' . $item['site_name'] . '</a></strong>' . $url . $this->row_actions( $set );
	}

	/**
	 * @param array $item // not object!
	 * @param string $column_name
	 *
	 * @return string
	 */
	function column_default( $item, $column_name ) {
		static $pup, $base_scan, $groups, $servers;
		if ( ! $groups ) {
			$groups = nexus_get_groups();
		}
		if ( ! $base_scan ) {
			$base_scan = wp_nonce_url( cerber_admin_link( 'scan_main' ) . '&amp;cerber_admin_do=nexus_switch', 'control', 'cerber_nonce' );
		}
		if ( ! $pup ) {
			$pup = nexus_get_update( CERBER_PLUGIN_ID );
		}
		//return $item[ $column_name ]; // raw output as is
		$val = crb_array_get( $item, $column_name, null );
		switch ( $column_name ) {
			case 'site_status':
				if ( ! $val ) {
					return 'Up';
				}

				return 'Down';
				break;
			case 'site_url':
				return '<a href="' . $val . '" target="_blank">' . str_replace( array('.', '/'), array('<wbr>.','<wbr>/'), $val ) . '</a>';
				break;
			case 'last_scan':
				if ( ! $item['refreshed'] ) {
					return __( 'Unknown', 'wp-cerber' );
				}

				$v = '';
				if ( $val ) {
					$nums = cerber_get_set( '_nexus_tmp_' . $item['id'] );
					if ( ! empty( $nums[ CERBER_VULN ] ) ) {
						$v = '<br/><span style="color: red;">' . __( 'Vulnerabilities', 'wp-cerber' ) . ' ' . $nums[ CERBER_VULN ] . '</span>';
					}
					$txt = cerber_auto_date( $val );

					return '<a href="' . $base_scan . '&nexus_site_id=' . $item['id'] . '">' . $txt . '</a>' . $v;
				}

				return '<span style="color: red;">' . __( 'Never', 'wp-cerber' ) . '</span>';
				break;
			case 'wp_v':
				if ( $val && version_compare( $val, cerber_get_wp_version(), '<' ) ) {
					return $val . ' <i style="color: red; font-size: 1.2em;" class="dashicons dashicons-warning"></i>';
				}
				return $val;
			case 'plugin_v':
				if ( $val ) {
					$ret = '<span class="crb-slave-ver">' . $val . '</span>' . ( ( $item['site_key'] ) ? '<span class="crb-vpro" title="Valid until ' . cerber_date( $item['site_key'], false ) . '">PRO</span>' : '' );
					if ( isset( $pup['new_version'] ) && version_compare( $val, $pup['new_version'], '<' ) ) {
						$ret .= ' <i style="color: red; font-size: 1.2em;" class="dashicons dashicons-warning"></i>';
					}
				}
				else {
					$ret = '-';
				}

				return $ret;
			case 'updates':
				return ( ! empty( $item['refreshed'] ) ) ? '<a href="#">' . $val . '</a>' : '';
				//return ( $val ) ? '<a href="#">' . $val . '</a>' : $val;
			case 'srv_name':
				$srv = nexus_get_srv_info( $item['server_id'] );

				if ( ! $srv ) {
					nexus_refresh_slave_srv( $item['id'] );
					$srv = nexus_get_srv_info( $item['server_id'] );
					if ( ! $srv ) {
						return 'Updating...';
					}
				}

				$ret = '<a href="' . $this->base_sites . '&filter_server_id=' . $item['server_id'] . '">' . str_replace( '.', '<wbr>.', $srv[1] ) . '</a>';

				if ( $this->hide_ip ) {
					return $ret;
				}

				$ret .= '<br/><span style="color: #555;">' . $item['server_id'] . '</span>';

				return $ret;
			case 'srv_country':
				return crb_country_html( $item['server_country'] );
			case 'site_grp':
				return crb_array_get( $groups, $item['group_id'], 'Unknown' );
				break;
			case 'site_owner':
				if ( ! $owner = crb_array_get( $item['details'], 'owner_biz' ) ) {
					$owner = crb_array_get( $item['details'], 'first_name', '' ) . ' ' . crb_array_get( $item['details'], 'last_name', '' );
				}

				return trim( $owner );
				break;
		}

		return htmlspecialchars( $val );
	}

	function no_items() {
		if ( ! empty( $_GET['s'] ) ) {
			parent::no_items();
		}
		else {
			$no_master = wp_nonce_url( add_query_arg( array(
				'cerber_admin_do' => 'nexus_set_role',
				'nexus_set_role'  => 'none',
			) ), 'control', 'cerber_nonce' );

			echo __( 'No websites configured.', 'wp-cerber' ) . ' <a class="thickbox" href="' . CRB_ADD_SLAVE_LNK . '">' . __( 'Add a new one', 'wp-cerber' ) . '</a> | <a href="' . $no_master . '">' . __( 'Disable master mode', 'wp-cerber' ) . '</a>';
		}
	}
}