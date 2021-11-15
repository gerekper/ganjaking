<?php
// namespace WeDevs\PM_Pro\Core\Update;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'WeDevs_License_Update' ) ):

/**
 * WeDevs Plugin update checker
 *
 * @author Tareq Hasan
 * @version 0.3
 */
class WeDevs_Updater {

    protected $base_plugin_key = 'cpm-pro';
    protected $base_url        = 'https://wedevs.com/';
    protected $api_endpoint    = 'http://api.wedevs.com/';

    function __construct( $file, $name, $product_id, $slug, $version ) {

        // bail out if it's a local server
        if ( $this->is_local_server() ) {
            //return;
        }

        $this->file           = $file;
        $this->name           = $name;
        $this->product_id     = $product_id;
        $this->slug           = $slug;
        $this->version        = $version;
        $this->option         = 'cpm_license';
        $this->plugin_name    = plugin_basename( $this->file );
        $this->license_status = 'cpm_license_status';

        // if ( is_multisite() ) {
        //     if ( is_main_site() ) {
        //         add_action( 'admin_notices', array($this, 'license_enter_notice') );
        //         add_action( 'admin_notices', array($this, 'license_check_notice') );
        //     }
        // } else {
        //     add_action( 'admin_notices', array($this, 'license_enter_notice') );
        //     add_action( 'admin_notices', array($this, 'license_check_notice') );
        // }

        add_filter( 'pre_set_site_transient_update_plugins', array($this, 'check_update') );
        add_filter( 'pre_set_transient_update_plugins', array($this, 'check_update') );
        add_filter( 'plugins_api', array( $this, 'check_info'), 99, 3 );

        add_action( 'in_plugin_update_message-' . $this->plugin_name, array( $this, 'plugin_update_message' ) );

        add_action( 'wp_ajax_wedevs-license-form-action', array( $this, 'manage_license'), 10 );
        add_action( 'wp_ajax_wedevs-license-delete-form-action', array( $this, 'delete_license'), 10 );
    }

    /**
    * Handle license submission
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function manage_license() {

        if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wedevs-license-nonce' ) ) {
            wp_send_json_error( __( 'Nonce verification failied', 'pm-pro' ) );
        }

        if ( isset( $_REQUEST['license_product_slug'] ) && ! empty( $_REQUEST['license_product_slug'] ) ) {
            $license_option     = strtolower( str_replace( '-', '_', $_REQUEST['license_product_slug'] ) ) . '_license';
            $license_status_key = strtolower( str_replace( '-', '_', $_REQUEST['license_product_slug'] ) ) . '_license_status';

            if ( empty( $_REQUEST['email'] ) ) {
                wp_send_json_error( __( 'Please enter your email address', 'pm-pro' ) );
            }

            if ( empty( $_REQUEST['key'] ) ) {
                wp_send_json_error( __( 'Please enter your valid license key', 'pm-pro' ) );
            }

            update_option( $license_option, array('email' => $_REQUEST['email'], 'key' => $_REQUEST['key']) );
            delete_transient( $license_option );

            $license_status = get_option( $license_status_key );

            if ( !isset( $license_status->activated ) || $license_status->activated != true ) {
                $response = $this->activation( 'activation' );

                if ( $response && isset( $response->activated ) && $response->activated ) {
                    update_option( $license_status_key, $response );

                    $update = strtotime( $response->update );
                    $expired = false;

                    if ( time() > $update ) {
                        $greeting = __( 'Opps! license invalid. ', 'pm-pro' );
                        $string   = __( 'has been expired %s ago', 'pm-pro' );
                        $expired  = true;
                    } else {
                        $greeting = __( 'Congrats! License activated successfully. ', 'pm-pro' );
                        $string   = __( 'will expire in %s', 'pm-pro' );
                    }

                    $message = sprintf( '%s Your license %s (%s).', $greeting, sprintf( $string, human_time_diff( $update, time() ) ), date( 'F j, Y', strtotime( $response->update ) ) );

                    if ( $expired ) {
                        $message .= sprintf( '<a href="%s" target="_blank">%s</a>', 'https://wedevs.com/account/', __( 'Renew License', 'pm-pro' ) );
                    }

                    wp_send_json_success( array( 'data' => $response, 'message' => $message ) );
                }

                wp_send_json_success( array( 'data' => $response, 'message' => __( 'Invalid license', 'pm-pro' ) ) );

            } else {

                wp_send_json_success( $license_status );

            }
        }

        wp_send_json_error( __( 'Something went wrong', 'pm-pro' ) );
    }

    /**
    * Delete license
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function delete_license() {

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wedevs-license-delete-nonce' ) ) {
            return;
        }

        if ( isset( $_POST['license_product_slug'] ) && ! empty( $_POST['license_product_slug'] ) ) {
            $license_option = strtolower( str_replace( '-', '_', $_POST['license_product_slug'] ) ) . '_license';
            $license_status_key = strtolower( str_replace( '-', '_', $_POST['license_product_slug'] ) ) . '_license_status';

            delete_option( $license_option );
            delete_transient( $license_option );
            delete_option( $license_status_key );

            wp_send_json_success( __( 'License successfully deleted', 'pm-pro' ) );
        }

        wp_send_json_error( __( 'Something wrong, Please try again', 'pm-pro' ) );
    }

    /**
     * Check if the current server is localhost
     *
     * @return boolean
     */
    protected function is_local_server() {
        // return false;
        return in_array( $this->get_ip(), array( '127.0.0.1', '::1' ) );
    }

    /**
    * Get current IP
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function get_ip() {
        $ipaddress = '';

        if ( isset($_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    /**
     * Get license key
     *
     * @return array
     */
    function get_license_key() {
        return get_option( $this->option, array() );
    }

    /**
     * Get license key
     *
     * @return array
     */
    function get_license_status() {
        return get_option( $this->license_status, array() );
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
        ?>
        <div class="error">
            <p><?php printf( __( 'Please <a href="%s">enter</a> your <strong>%s</strong> plugin license key to get regular update and support.', 'pm-pro' ), admin_url( 'admin.php?page=pm_projects#/license' ), $this->name ); ?></p>
        </div>
        <?php
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

        $error = __( ' Error: Please activate your license', 'pm-pro' );

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
                        echo '<div class="error">';
                        echo '<p>Your <strong>' . $this->name . '</strong> License has been expired. Please <a href="https://wedevs.com/account/" target="_blank">renew your license</a>.</p>';
                        echo '</div>';
                    }
                }
                return;
            }

            // may be the request didn't completed
            if ( !isset( $status->error )) {
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
     * Activation request to the plugin server
     *
     * @return object
     */
    public function activation( $request = 'check' ) {
        global $wp_version;

        if ( ! $option = $this->get_license_key() ) {
            return;
        }

        $params = array(
            'timeout'    => ( ( defined( 'DOING_CRON' ) && DOING_CRON ) ? 30 : 3 ),
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
            'body'       => array(
                'request'     => $request,
                'email'       => $option['email'],
                'licence_key' => $option['key'],
                'product_id'  => $this->product_id,
                'instance'    => home_url()
            )
        );

        $response = wp_remote_post( $this->api_endpoint . 'activation', $params );
        $update   = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
            if ( is_wp_error( $response ) ) {
                echo '<div class="error"><p><strong>' . $this->name . ' Activation Error:</strong> ' . $response->get_error_message() . '</p></div>';
                return false;
            }

            if ( $response['response']['code'] != 200 ) {
                echo '<div class="error"><p><strong>' . $this->name . ' Activation Error:</strong> ' . $response['response']['code'] .' - ' . $response['response']['message'] . '</p></div>';
                return false;
            }

            printf('<pre>%s</pre>', print_r( $response, true ) );
        }

        return json_decode( $update );
    }

    /**
     * Integrates into plugin update api check
     *
     * @param object $transient
     * @return object
     */
    function check_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $remote_info = $this->get_info();

        if ( !$remote_info ) {
            return $transient;
        }

        list( $plugin_name, $plugin_version) = $this->get_current_plugin_info();

        if ( version_compare( $plugin_version, $remote_info->latest, '<' ) ) {

            $obj              = new \stdClass();
            $obj->slug        = $this->slug;
            $obj->new_version = $remote_info->latest;
            $obj->url         = $this->base_url;

            if ( isset( $remote_info->latest_url ) ) {
                $obj->package = $remote_info->latest_url;
            }

            $basefile = plugin_basename( $this->file );
            $transient->response[$basefile] = $obj;
        }

        return $transient;
    }

    /**
     * Plugin changelog information popup
     *
     * @param type $false
     * @param type $action
     * @param type $args
     * @return \stdClass|boolean
     */
    function check_info( $false, $action, $args ) {

        if ( 'plugin_information' != $action ) {
            return $false;
        }

        if ( $this->slug == $args->slug ) {

            $remote_info = $this->get_info();

            $obj              = new stdClass();
            $obj->name        = $this->name;
            $obj->slug        = $this->slug;
            $obj->new_version = $remote_info->latest;

            if ( isset( $remote_info->latest_url ) ) {
                $obj->download_link = $remote_info->latest_url;
            }

            $obj->sections = array(
                'changelog' => $remote_info->msg,
            );

            return $obj;
        }

        return $false;
    }

    /**
     * Collects current plugin information
     *
     * @return array
     */
    function get_current_plugin_info() {
        require_once ABSPATH . '/wp-admin/includes/plugin.php';

        $plugin_data    = get_plugin_data( $this->file );
        $plugin_name    = $plugin_data['Name'];
        $plugin_version = $plugin_data['Version'];

        return array($plugin_name, $plugin_version);
    }

    /**
     * Get plugin update information from server
     *
     * @global string $wp_version
     * @global object $wpdb
     * @return boolean
     */
    function get_info() {
        global $wp_version, $wpdb;

        list( $plugin_name, $plugin_version) = $this->get_current_plugin_info();

        if ( is_multisite() ) {
            $wp_install = network_site_url();
        } else {
            $wp_install = home_url( '/' );
        }

        $license = $this->get_license_key();

        $params = array(
            'timeout'    => 15,
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
            'body' => array(
                'name'              => $plugin_name,
                'slug'              => $this->slug,
                'type'              => 'plugin',
                'version'           => $plugin_version,
                'wp_version'        => $wp_version,
                'php_version'       => phpversion(),
                'site_url'          => $wp_install,
                'license'           => isset( $license['key'] ) ? $license['key'] : '',
                'license_email'     => isset( $license['email'] ) ? $license['email'] : '',
                'product_id'        => $this->product_id
            )
        );

        $response = wp_remote_post( $this->api_endpoint . 'update_check', $params );
        $update   = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
            return false;
        }

        return json_decode( $update );
    }

    /**
     * Show plugin udpate message
     *
     * @since  1.0.0
     *
     * @param  array $args
     *
     * @return void
     */
    public function plugin_update_message( $args ) {
        $cache_key    = md5( $this->base_plugin_key . '_plugin_' . sanitize_key( $this->plugin_name ) . '_version_info' );
        $version_info = get_transient( $cache_key );

        if ( false === $version_info ) {
            $version_info = $this->get_info();
            set_transient( $cache_key, $version_info, 3600 );
        }

        if ( version_compare( $this->version, $version_info->latest, '<' ) && empty( $version_info->latest_url ) ) {
            $upgrade_notice = sprintf( '</p><p id="pm-pro-plugin-upgrade-notice" class="%s-plugin-upgrade-notice">Please <a href="%s" target="_blank">activate</a> your license key for getting regular updates and support',
                                $this->base_plugin_key,
                                admin_url( 'admin.php?page=pm_projects#/license' )
                            );
            echo apply_filters( $this->product_id . '_in_plugin_update_message', wp_kses_post( $upgrade_notice ) );
        }
    }

}

endif;
