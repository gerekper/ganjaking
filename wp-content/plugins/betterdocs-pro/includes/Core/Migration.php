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
        if( method_exists( $this, "v$version") ) {
            call_user_func([$this, "v$version"]);
        }
    }

    public function v250(){
        /**
         * Licensing DB Migration
         */
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

        // Settings migration
        betterdocs()->settings->v250();

        // Flush Rewrite Rules
        set_transient( 'betterdocs_flush_rewrite_rules', true );
    }
}
