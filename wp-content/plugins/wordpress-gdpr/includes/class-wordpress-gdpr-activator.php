<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.welaunch.io
 * @since      1.0.0
 *
 * @package    WordPress_GDPR
 * @subpackage WordPress_GDPR/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WordPress_GDPR
 * @subpackage WordPress_GDPR/includes
 * @author     Daniel Barenkamp <contact@db-dzine.de>
 */
class WordPress_GDPR_Activator {


    /**
     * On plugin activation -> Assign Caps
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @return  [type]                       [description]
     */
	public function activate() 
    {
        $transient_name = 'wordpress_gdpr_pages';
        delete_transient($transient_name);  

        global $wpdb;

        if ( is_multisite() ) {

            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                $this->create_table();
                restore_current_blog();
            }
        } else {
            $this->create_table();
        }
	}

    public function create_table() {

        global $wpdb;

        $db_name = $wpdb->prefix . 'gdpr_consent_log';
     
        // create the ECPT metabox database table
        if($wpdb->get_var("show tables like '$db_name'") != $db_name) 
        {
            $sql = "CREATE TABLE " . $db_name . " (
                `id` bigint(9) NOT NULL AUTO_INCREMENT,
                `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                `modified` TIMESTAMP NOT NULL,
                `consents` LONGTEXT COLLATE utf8_unicode_ci NULL,
                UNIQUE KEY id (id),
                UNIQUE KEY ip (ip)
            );";
     
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}