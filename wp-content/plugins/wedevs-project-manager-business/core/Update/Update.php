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
        $license_status = $self->get_license_status();

        if ( ! empty( $license_status ) && ! empty( $response ) ) {
            if ( $response->update != $license_status->update ) {
                $license_status->update  = $response->update;
                $license_status->support = $response->support;
                $license_status->sig     = $response->sig;

                update_option( $self->license_status, $license_status );
            }
        }

    }

    /**
     * Prompts the user to add license key if it's not already filled out
     *
     * @return void
     */
    function license_enter_notice() {
        if ( $key = $this->get_license_key() ) {
            return;
        }

        if ( pm_current_user_can_update_core() ) {
            ?>
            <div class="error">
                <p><?php printf( __( 'Please <a href="%s">enter</a> your <strong>%s</strong> plugin license key to get the pro features, regular update and support.', 'pm-pro' ), admin_url( 'admin.php?page=pm_projects#/license' ), $this->name ); ?></p>
            </div>
            <?php
        } else {
            ?>
            <div class="error">
                <p><?php printf( __( 'Please notify the site administrator to activate <strong>Project Manager Pro</strong> license key for getting the pro features.', 'pm-pro' ) ); ?></p>
            </div>
            <?php
        }

    }

    /**
     * Check activation every 12 hours to the server
     *
     * @return void
     */
    function license_check_notice() {

        if ( ! $key = $this->get_license_key() ) {
            return;
        }

        if ( pm_current_user_can_update_core() ) {

            $error = sprintf( __( ' Error: Please activate your <a href="%s">license</a>', 'pm-pro' ), admin_url( 'admin.php?page=pm_projects#/license' ) );
        } else {
            $error = sprintf( __( ' Error: Please notify the site administrator to activate the license', 'pm-pro' ) );
        }

        $license_status = get_option( $this->license_status );

        if ( $license_status && $license_status->activated ) {

            $status = get_transient( $this->option );
            if ( false === $status ) {
                $status   = $this->activation();
                $duration = 60 * 60 * 12; // 12 hour

                set_transient( $this->option, $status, $duration );
            }

            if ( $status && $status->success ) {

                // notice if validity expires
                if ( isset( $status->update ) ) {
                    $update = strtotime( $status->update );

                    if ( time() > $update ) {

                        if ( pm_current_user_can_update_core() ) {
                            echo '<div class="error">';
                            printf( __( '<p>Your <strong>%s</strong> License has been expired. Please <a href="https://wedevs.com/account/" target="_blank">renew your license</a>.</p>', 'pm-pro' ), $this->name );
                            echo '</div>';
                        } else {
                            echo '<div class="error">';
                            printf( __( '<p>The <strong>Project Manager Pro</strong> license has been expired, Please inform your administrator</p>' ) );
                            echo '</div>';
                        }

                    } else {
                        $current = date( 'Y-m-d', time() );
                        $expire = date( 'Y-m-d', $update );

                        $current = date_create( $current );
                        $expire = date_create( $expire );
                        $diff = date_diff( $current, $expire );
                        $logo = pm_config('frontend.assets_url') . '/images/pm-logo.png';

                        if ( $diff->days && $diff->days <= 30 ) {
                            if ( pm_current_user_can_update_core() ) {
                                ?>
                                 <div class="error">
                                    <div class="license-content-wrap">
                                        <div class="left-content">
                                            <div class="logo-wrap"><img class="logo" src="<?php echo $logo; ?>"></div>
                                            <div>
                                                <div><strong><?php _e( 'Your License is About to Expire!', 'pm-pro' ) ?></strong></div>
                                                <div><?php printf( __( 'Your WP Project Manager Pro license will expire in %s (%s). Please renew your license to keep using the plugin.', 'pm-pro' ), human_time_diff( $update, time() ), date( 'F j, Y', $update ) ); ?></div>
                                            </div>
                                        </div>
                                        <div class="right-content">
                                            <a class="license-button" target="__blank" href="https://wedevs.com/account"><?php printf( __( 'Renew License', 'pm-pro' ) ); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="error">
                                    <div class="license-content-wrap">
                                        <div class="left-content">
                                            <div class="logo-wrap"><img class="logo" src="<?php echo $logo; ?>"></div>
                                            <div>
                                                <div><strong><?php _e( 'Your License is About to Expire!', 'pm-pro' ) ?></strong></div>
                                                <div><?php printf( __( 'Your WP Project Manager Pro license will expire in %s (%s). Please inform your administrator.', 'pm-pro' ), human_time_diff( $update, time() ), date( 'F j, Y', $update ) ); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <style>
                                .license-content-wrap {
                                    display: flex;
                                    align-items: center;
                                    padding: 10px 0;
                                    color: #444;
                                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                                    font-size: 13px;
                                    line-height: 1.4em;
                                }
                                .license-content-wrap .logo {
                                    height: 40px;
                                }
                                .license-content-wrap .left-content {
                                    display: flex;
                                    align-items: center;
                                    flex: 1;
                                }
                                .license-content-wrap .logo-wrap {
                                    margin-right: 15px;
                                }
                                .license-content-wrap .license-button {
                                    white-space: nowrap;
                                    height: auto;
                                    line-height: auto;
                                    background: #6f56a5;
                                    padding: 6px 10px;
                                    margin: 0;
                                    border-radius: 3px;
                                    display: inline-block;
                                    color: #fff;
                                    text-decoration: none;
                                }
                            </style>

                            <?php

                            //printf( __( 'Your license will expire in %s (%s).', 'pm-pro' ), human_time_diff( $update, time() ), date( 'F j, Y', $update ) );
                        }
                    }
                }
                return;
            }

            // may be the request didn't completed
            if ( ! isset( $status->error ) ) {
                return;
            }

            $error = $status->error;
        }
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

        if ( current_user_can( 'activate_plugins' ) ) {
            $submenu['pm_projects'][] = array( __( 'License', 'pm-pro' ), 'activate_plugins', 'admin.php?page=pm_projects#/license' );
            add_action( 'admin_print_styles-' . $hook, array( $this, 'scripts' ) );
        }
    }

    public function scripts() {
        if ( ! $this->is_license_active() ) {
            wp_enqueue_script(
                'pm-pro-license',
                pm_pro_config('define.url') . 'views/assets/js/pm-pro-license.js',
                ['pm-const'],
                true
            );
        }
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
            'license' => $this->get_license_key(),
            'status'  => $this->get_license_status(),
            'message' => ''
        );

        if ( isset( $data['status']->update ) ) {
            $update = strtotime( $data['status']->update );
            $expired = false;

            if ( time() > $update ) {
                $string  = __( 'has been expired %s ago', 'pm-pro' );
                $expired = true;
            } else {
                $string = __( 'will expire in %s', 'pm-pro' );
            }

            $data['message'] = sprintf( __( 'Your license %s (%s).', 'pm-pro' ), sprintf( $string, human_time_diff( $update, time() ) ), date( 'F j, Y', $update ) );
        }

        wp_send_json_success( $data );
    }

    public static function is_license_active() {

        $license = get_option( 'cpm_license', array() );

        if ( ! $key = $license ) {
            return false;
        }

        $license_status = get_option( 'cpm_license_status', array() );

        if ( empty( $license_status ) ) {
            return false;
        }

        $update = strtotime( $license_status->update );

        if ( time() > $update ) {
            return false;
        }

        return true;
    }
}
