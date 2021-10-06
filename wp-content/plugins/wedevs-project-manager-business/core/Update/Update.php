<?php

namespace WeDevs\PM_Pro\Core\Update;

use WeDevs\PM_Pro\Core\WP\Enqueue_Scripts;

/**
 * The Updater Class
 */
class Update extends \WeDevs_Updater {

    private static $_instance;

    public static function getInstance( $plan ) {
        if ( !self::$_instance ) {
            self::$_instance = new self( $plan );
        }

        return self::$_instance;
    }

    function __construct( $plan ) {
        global $wedevs_license_progress;
        $wedevs_license_progress = true;

        $version     = pm_pro_config( 'app.version' );
        $name        = pm_pro_config( 'app.name' );
        $pm_pro_file = pm_pro_config( 'define.pm_pro_file' );
        $product_id  = pm_pro_config( 'app.product_id' );

        parent::__construct( $pm_pro_file, $name, $product_id, $plan, $version );

        $this->api_endpoint    = 'http://api.wedevs.com/';

        //if ( ! $this->is_local_server() ) {
            add_action( 'pm_menu_before_load_scripts', array( $this, 'admin_menu' ) );
        //}

        if ( is_multisite() ) {
            if ( is_main_site() ) {
                add_action( 'admin_notices', array($this, 'license_enter_notice') );
                add_action( 'admin_notices', array($this, 'license_check_notice') );
            }
        } else {
            add_action( 'admin_notices', array($this, 'license_enter_notice') );
            add_action( 'admin_notices', array($this, 'license_check_notice') );
        }

        if ( ! wp_next_scheduled( 'pm_pro_license_update' ) ) {
            wp_schedule_event( time(), 'daily', 'pm_pro_license_update' );
        }
    }


    public static function update_license() {
        $self = self::getInstance( pm_pro_config( 'app.plan' ) );

        $response = $self->activation( 'check' );
        $license_status = 'valid';

       
            if ( $response->update != $license_status->update ) {
                $license_status->update  = $response->update;
                $license_status->support = $response->support;
                $license_status->sig     = $response->sig;

                update_option( $self->license_status, 'valid' );
            }
        

    }

    /**
     * Prompts the user to add license key if it's not already filled out
     *
     * @return void
     */
    function license_enter_notice() {
       
            return;
       

        ?>
        <div class="error">
            <p><?php printf( __( 'Please <a href="%s">enter</a> your <strong>%s</strong> plugin license key to get the pro features, regular update and support.', 'pm-pro' ), admin_url( 'admin.php?page=pm_projects#/license' ), $this->name ); ?></p>
        </div>
        <?php
    }

    /**
     * Check activation every 12 hours to the server
     *
     * @return void
     */
    function license_check_notice() {
       
            return;
       

        $error = sprintf( __( ' Error: Please activate your <a href="%s">license</a>', 'pm-pro' ), admin_url( 'admin.php?page=pm_projects#/license' ) );

        $license_status = get_option( $this->license_status );

        

            $status = get_transient( $this->option );
           
            

                // notice if validity expires
                if ( isset( $status->update ) ) {
                    $update = strtotime( $status->update );

                   
                        $current = date( 'Y-m-d', time() );
                        $expire = date( 'Y-m-d', '2030-01-01' );

                        $current = date_create( $current );
                        $expire = date_create( $expire );
                        $diff = date_diff( $current, $expire );
                        $logo = pm_config('frontend.assets_url') . '/images/pm-logo.png';

                        
                    
                }
                return;
            

            // may be the request didn't completed
           

            $error = '';
       
        ?>
        <div class="error">
            <p><strong><?php echo $this->name; ?></strong> <?php echo $error; ?></p>
        </div>
        <?php
    }

    /**
     * Add admin menu
     *
     * @param  string $hook
     * @param  string $capability
     *
     * @return void
     */
    public function admin_menu( $hook ) {
        global $submenu;

       
    }

    public function scripts() {
      
    }

    /**
     * Add license routes into the router
     *
     * @param  array $routes
     *
     * @return array
     */
    public function vue_routes( $routes ) {
        $routes[] = array(
            'path'      => '/license',
            'name'      => 'license',
            'component' => 'License'
        );

        return $routes;
    }

    /**
     * License status checking
     *
     * @return void
     */
    public function weforms_license_status() {
        $data = array(
            'license' => '1415b451be1a13c283ba771ea52d38bb',
            'status'  => 'valid',
            'message' => 'success'
        );
        
            $update = strtotime( $data['status']->update );
            $expired = false;
            $data['message'] = sprintf( __( 'Your license %s (%s).', 'pm-pro' ), sprintf( $string, human_time_diff( '01 jan, 2030', time() ) ), date( 'F j, Y', '01 jan, 2030' ) );
       

        wp_send_json_success( $data );
    }

    public static function is_license_active() { 
	return true;

        $license = get_option( 'cpm_license', array() );

       

        $license_status = 'valid';

       
        $update = strtotime( $license_status->update );

        

        return true;
    }
}
