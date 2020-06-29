<?php

/**
 * Installer for the recommendation engine extension.  Creates the woocommerce_sessions_history database table. 
 */
class WC_Recommender_Installer {
	/**
	 * Execute the installation and update the woocommerce_recommender_db_version option. 
	 * @global type $woocommerce_recommender
	 */
	public function install() {
		global $woocommerce_recommender;
		$this->install_database();
		update_option("woocommerce_recommender_db_version", $woocommerce_recommender->version);
	}

	/**
	 * Creates the session history table for the current site.  Uses dbDelta. 
	 * @global wpdb $wpdb
	 * @global WooCommerce $woocommerce
	 * @global WC_Recommender $woocommerce_recommender
	 */
	private function install_database() {
		global $wpdb, $woocommerce, $woocommerce_recommender;

		$wpdb->hide_errors();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		

		$sql = "CREATE TABLE {$woocommerce_recommender->db_tbl_session_activity} (
                activity_id int(11) NOT NULL AUTO_INCREMENT,
                session_id varchar(32) NOT NULL,
                activity_type varchar(255) NOT NULL,
                product_id bigint(20) NOT NULL,
                order_id varchar(45) NOT NULL DEFAULT '0',
                user_id varchar(45) NOT NULL DEFAULT '0',
                activity_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY  ( activity_id ) )";

		@dbDelta($sql);

		$sql = "DROP TABLE {$woocommerce_recommender->db_tbl_recommendations}";
		$wpdb->query($sql);
		
		$sql = "CREATE TABLE {$woocommerce_recommender->db_tbl_recommendations} (
		ID bigint(20) NOT NULL AUTO_INCREMENT,
		rkey varchar(255) NOT NULL,
		product_id bigint(20) NOT NULL,
		related_product_id bigint(20) NOT NULL,
		score float NOT NULL,
		PRIMARY KEY  ( ID ) )";
		
		@dbDelta($sql);
	}

	/**
	 * Update the woocommerce_recommender_db_version to 0. 
	 */
	public function uninstall() {
		update_option("woocommerce_recommender_db_version", 0);
	}

}

/**
 * Installation functions
 */
function activate_woocommerce_recommender() {
	$installer = new WC_Recommender_Installer();
	$installer->install();

	update_option('woocommerce_recommender_installed', 1);
}

/**
 * On plugin deactivation, trigger the uninstall function.  This does not destroy any tables or data. 
 */
function deactivate_woocommerce_recommender() {
	$installer = new WC_Recommender_Installer();
	$installer->uninstall();

	update_option('woocommerce_recommender_installed', 0);
}

/**
 * Call the installation function on the WC_Recommender_Installer object.  
 * 
 *
 * This is triggerd from the main WC_Recommender class on activation and on version changes. 
 */
function install_woocommerce_recommender() {
	$installer = new WC_Recommender_Installer();
	$installer->install();
}
