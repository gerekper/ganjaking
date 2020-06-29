<?php
namespace WeDevs\PM_Pro\Core\WP;

use WeDevs\PM\Role\Models\Role;

class Deactive {
    function __construct() {
        $this->deactivate_daily_digest();
        $this->deactivata_modules();
        $this->deactive_client_role();
    }

    /**
     * Deactivation actions
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function deactivate_daily_digest() {
        wp_clear_scheduled_hook( 'pm_daily_digest' );
    }

    public function deactivata_modules() {
        //delete_option( 'pm_pro_active_modules' );
    }

    public function deactive_client_role() {
        if ( class_exists('WeDevs\\PM\\Role\\Models\\Role') ) {
            Role::where( 'slug', 'client' )->update( [ 'status' => 0 ] );
        }
    }
}