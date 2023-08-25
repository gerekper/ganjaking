<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WPDeveloper\BetterDocs\Utils\Base;
use WPDeveloper\BetterDocs\Utils\Database;

class Migration extends Base {
    /**
     * Database
     * @var Database
     */
    private $database;

    public function __construct( Database $database ) {
        $this->database = $database;
    }

    public function init( $version ) {
        if( $version > 250 ) {
            for( $_version = 250; $_version <= $version; $_version++ ) {
                if( method_exists( $this, "v$_version") ) {
                    call_user_func([$this, "v$_version"]);
                }
            }
        }

        /**
         * Settings Migration
         */
        betterdocs()->settings->migration( $version );

        /**
         * License Migration
         */
        $this->license_migration();
    }

    public function v252(){
        $this->flush();
    }

    public function v250(){
        // Settings migration
        betterdocs()->settings->v250();

        $this->flush();
    }

    /**
     * Licensing DB Migration
     */
    public function license_migration(){
        $_has_license = get_option( BETTERDOCS_PRO_SL_DB_PREFIX . '_license', false );

        if( $_has_license ) {
            return;
        }

        $old_license_key = get_option( 'betterdocs-pro-license-key', '' );
        if( ! empty( $old_license_key ) ) {
            update_option( BETTERDOCS_PRO_SL_DB_PREFIX . '_license', $old_license_key, 'no' );

            $license_status = get_option( 'betterdocs-pro-license-status' );
            update_option( BETTERDOCS_PRO_SL_DB_PREFIX . '_license_status', $license_status, 'no' );

            $license_data = get_transient( 'betterdocs-pro-license_data' );
            if( $license_data ) {
                set_transient( BETTERDOCS_PRO_SL_DB_PREFIX . '_license_data', $license_data, MONTH_IN_SECONDS * 3 );
            }
        }
    }

    // Flush Rewrite Rules
    private function flush(){
        set_transient( 'betterdocs_flush_rewrite_rules', true );
    }
}
