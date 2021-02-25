<?php namespace NinjaTables\Classes;

/**
 * Fired during plugin activation
 *
 * @link       https://wpmanageninja.com
 * @since      1.0.0
 *
 * @package    Wp_table_data_press
 * @subpackage Wp_table_data_press/includes

 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_table_data_press
 * @subpackage Wp_table_data_press/includes
 * @author     Shahjahan Jewel <cep.jewel@gmail.com>
 */
class NinjaTablesActivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 *
	 * @param bool $network_wide
	 */
	public static function activate( $network_wide = false ) {
		global $wpdb;

		if ( $network_wide ) {
			// Retrieve all site IDs from this network (WordPress >= 4.6 provides easy to use functions for that).
			if ( function_exists( 'get_sites' ) && function_exists( 'get_current_network_id' ) ) {
				$site_ids = get_sites( array( 'fields' => 'ids', 'network_id' => get_current_network_id() ) );
			} else {
				$site_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;" );
			}
			// Install the plugin for all these sites.
			foreach ( $site_ids as $site_id ) {
				switch_to_blog( $site_id );
				self::create_datatables_table();
				restore_current_blog();
			}
		}  else {
			self::create_datatables_table();
		}
	}

	/**
	 * Create Table for datatable which will hold the primary info of a table
	 *
	 * @since    1.0.0
	 */
	public static function create_datatables_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . ninja_tables_db_table_name();
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$sql
				= "CREATE TABLE $table_name (
				id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				position int(11),
				table_id int(11) NOT NULL,
				owner_id int(11),
				attribute varchar(255) NOT NULL,
				settings longtext,
				value longtext,
				created_at timestamp NULL,
				updated_at timestamp NULL
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option('_ninja_tables_settings_migration', true);
			update_option('_ninja_tables_sorting_migration', true);
		} else {
		    // check if the new columns is there or not
            do_action('ninja_table_check_db_integrity');
            update_option('_ninja_tables_settings_migration', true);
            update_option('_ninja_tables_sorting_migration', true);
        }

        if(function_exists('ninja_table_clear_all_cache')) {
            ninja_table_clear_all_cache();
        }
	}
}
