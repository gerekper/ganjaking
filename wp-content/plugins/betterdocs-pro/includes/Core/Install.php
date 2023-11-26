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
        add_action( 'init', [$this, 'check_db_updates'], 1 );
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
            $this->check_db_updates();

            // Set admin roles
            $this->container->get( Roles::class )->setup( true );

            // Save default settings.
            // $this->container->get( Settings::class )->save_default_settings();

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
            $this->check_db_updates();

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

    public function check_db_updates() {
        global $wpdb;

        $_db_version      = get_option( 'betterdocs_pro_db_version', '1.0' );
        $_db_code_version = betterdocs_pro()->db_version;
        $requires_update  = version_compare( $_db_version, $_db_code_version, '<' );

        if ( ! $requires_update ) {
            return;
        }

        // Update DB Version
        update_option( 'betterdocs_pro_db_version', $_db_code_version );
    }
}
