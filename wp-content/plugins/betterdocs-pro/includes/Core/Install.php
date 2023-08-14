<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WPDeveloper\BetterDocs\Core\Roles;
use WPDeveloper\BetterDocs\Core\Settings;
use WPDeveloper\BetterDocs\Utils\Database;
use WPDeveloper\BetterDocs\Dependencies\DI\Container;

class Install {
    /**
     * Container
     * @var Container
     */
    public $container;
    /**
     * Database
     * @var Database
     */
    public $database;

    public function __construct() {
        register_activation_hook( BETTERDOCS_PRO_FILE, [$this, 'activate'] );
        register_deactivation_hook( BETTERDOCS_PRO_FILE, [$this, 'deactivate'] );

        add_action( 'init', [$this, 'init'], -999 );
        add_action( 'init', [$this, 'check_version'], 5 );
    }

    public function init() {
        if( ! class_exists( '\WPDeveloper\BetterDocs\Plugin' ) ) {
            return;
        }

        $this->container = betterdocs()->container;
        $this->database  = $this->container->get( Database::class );

        if ( $this->database->get_transient( 'betterdocs_pro_activated' ) ) {
            // Create DB Tables if not created.
            $this->setup_db_tables();

            // Set admin roles
            $this->container->get( Roles::class )->setup( true );

            // Save default settings.
            $this->container->get( Settings::class )->save_default_settings();

            $this->database->delete_transient( 'betterdocs_pro_activated' );
        }
    }

    public function activate() {
        // Flush all the existing rewrite rules
        set_transient( 'betterdocs_flush_rewrite_rules', true );

        // This flag will re-run some checks after activation of the plugin.
        set_transient( 'betterdocs_pro_activated', true );
    }

    public function deactivate() {
        // Flush all the existing rewrite rules
        set_transient( 'betterdocs_flush_rewrite_rules', true );

        // Remove roles
        if( $this->container !== null ) {
            $this->container->get( Roles::class )->setup( true );
        }
    }

    public function check_version() {
        $betterdocs_pro_version  = get_option( 'betterdocs_pro_version', '2.2.7' );
        $betterdocs_code_version = betterdocs_pro()->version;
        $requires_update         = version_compare( $betterdocs_pro_version, $betterdocs_code_version, '<' );

        if ( $requires_update && did_action( 'betterdocs_loaded' ) >= 1 ) {
            // Re-check if any db setup needed
            $this->setup_db_tables();

            // Re-check if any migration is needed.
            $this->container->get( Migration::class )->init( str_replace( '.', '', $betterdocs_code_version ) );

            $this->update_version();
        }
    }

    /**
     * Update BetterDocs Pro version to current.
     */
    private function update_version() {
        update_option( 'betterdocs_pro_version', betterdocs_pro()->version );
    }

    public function setup_db_tables() {
        global $wpdb;

        $betterdocs_db_version      = get_option( 'betterdocs_pro_db_version', '1.0' );
        $betterdocs_db_code_version = betterdocs_pro()->db_version;
        $requires_update            = version_compare( $betterdocs_db_version, $betterdocs_db_code_version, '<' );

        if ( $requires_update ) {
            $charset_collate = $wpdb->get_charset_collate();
            $table_name      = $wpdb->prefix . 'betterdocs_analytics';

            $analytics_table = "CREATE TABLE $table_name (
                id bigint NOT NULL AUTO_INCREMENT,
                post_id bigint DEFAULT 0 NOT NULL,
                impressions bigint DEFAULT 0 NOT NULL,
                unique_visit bigint DEFAULT 0 NOT NULL,
                happy bigint DEFAULT 0 NOT NULL,
                sad bigint DEFAULT 0 NOT NULL,
                normal bigint DEFAULT 0 NOT NULL,
                created_at date DEFAULT '0000-00-00' NOT NULL,
                PRIMARY KEY (id),
                KEY post_id (post_id),
                KEY impressions (impressions),
                KEY unique_visit (unique_visit),
                KEY happy (happy),
                KEY sad (sad),
                KEY normal (normal),
                KEY created_at (created_at)
            ) {$charset_collate};";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $analytics_table );

            // Update DB Version
            update_option( 'betterdocs_pro_db_version', $betterdocs_db_code_version );
        }
    }
}
