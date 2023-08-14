<?php

namespace WPDeveloper\BetterDocsPro\Dependencies\WPDeveloper\Licensing;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_Error;
use Exception;

#[\AllowDynamicProperties]
class LicenseManager {
    private static $_instance = null;
    protected $license        = '';
    protected $license_data   = null;
    protected $args           = [
        'version'        => '',
        // 'author'         => '',
        // 'beta'           => '',
        'plugin_file'    => '',
        'item_id'        => 0,
        'item_name'      => '',
        'item_slug'      => '',
        'storeURL'       => '',
        'textdomain'     => '',
        'db_prefix'      => '',
        'scripts_handle' => '',
        'screen_id'      => '',
        'page_slug'      => '',
        'api'            => ''
    ];

    public static function get_instance( $args ) {
        if ( self::$_instance === null ) {
            self::$_instance = new self( $args );
        }

        return self::$_instance;
    }

    public function __construct( $args ) {
        foreach ( $this->args as $property => $value ) {
            if ( ! array_key_exists( $property, $args ) ) {
                throw new Exception( "$property is missing in licensing." );
            }
        }

        $this->args         = wp_parse_args( $args, $this->args );
        $this->license_data = $this->get_license_data();

        if (  ( $this->license_data == null || empty( $this->license_data ) ) && current_user_can( 'activate_plugins' ) ) {
            add_action( 'admin_notices', [$this, 'admin_notices'] );
        }

        add_action( 'admin_enqueue_scripts', [$this, 'enqueue'], 11 );

        if ( isset( $this->args['api'] ) ) {
            switch ( strtolower( $this->args['api'] ) ) {
                case 'rest':
                    if ( ! isset( $this->args['rest'] ) ) {
                        throw new Exception( "rest is missing in licensing." );
                    }
                    new RESTApi( $this );
                    break;
                case 'ajax':
                    if ( ! isset( $this->args['ajax'] ) ) {
                        throw new Exception( "ajax is missing in licensing." );
                    }
                    new AJAXApi( $this );
                    break;
            }
        }

        add_action( 'init', [$this, 'plugin_updater'] );
    }

    public function admin_notices() {
        $message = sprintf(
            __( 'Please %1$sactivate your license%2$s key to enable updates for %3$s.', $this->textdomain ),
            '<a style="text-decoration: none;" href="' . admin_url( 'admin.php?page=' . $this->page_slug ) . '">', '</a>', '<strong>' . $this->item_name . '</strong>'
        );

        $notice = sprintf(
            '<div style="padding: 10px;" class="%1$s-notice wpdeveloper-licensing-notice notice notice-error">%2$s</div>',
            $this->textdomain,
            $message
        );

        echo wp_kses_post( $notice );
    }

    public function plugin_updater() {
        $_license = get_option( "{$this->db_prefix}_license" );

        new PluginUpdater(
            $this->storeURL,
            $this->plugin_file,
            [
                'version' => $this->version, // current version number
                'license' => $_license, // license key (used get_option above to retrieve from DB)
                'item_id' => $this->item_id, // ID of the product
                'author'  => empty( $this->author ) ? 'WPDeveloper' : $this->author, // author of this plugin
                'beta'    => isset( $this->beta ) ? $this->beta : false
            ]
        );
    }

    public function get_args( $name = '' ) {
        return empty( $name ) ? $this->args : $this->args[$name];
    }

    public function enqueue( $hook ) {
        if ( $this->screen_id !== $hook ) {
            return;
        }

        wp_localize_script( $this->scripts_handle, 'wpdeveloperLicenseData', $this->get_license_data() );
    }

    public function get_license_data() {
        $_license        = get_option( "{$this->db_prefix}_license" );
        $_license_status = get_option( "{$this->db_prefix}_license_status" );
        $_license_data   = get_transient( "{$this->db_prefix}_license_data" );

        if ( $_license_data !== false ) {
            $_license_data = (array) $_license_data;
        }

        if ( $_license_data == false || empty( $_license_data ) ) {
            $response = $this->check();
            if ( is_wp_error( $response ) ) {
                return [];
            }

            $_license_data = (array) $response;
        }

        return array_merge( [
            'license_key'    => $_license,
            'license_status' => $_license_status
        ], $_license_data );
    }

    private function hide_license_key( $_license ) {
        $length   = mb_strlen( $_license ) - 10;
        $_license = substr_replace( $_license, mb_substr( preg_replace( '/\S/', '*', $_license ), 5, $length ), 5, $length );

        return $_license;
    }

    public function activate( $args = [] ) {
        $this->license = sanitize_text_field( isset( $args['license_key'] ) ? trim( $args['license_key'] ) : '' );
        $response      = $this->remote_post( 'activate_license' );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        update_option( "{$this->db_prefix}_license", $this->license, 'no' );
        update_option( "{$this->db_prefix}_license_status", $response->license, 'no' );
        set_transient( "{$this->db_prefix}_license_data", $response, MONTH_IN_SECONDS * 3 );

        return $response;
    }

    public function deactivate( $args = [] ) {
        $this->license = get_option( "{$this->db_prefix}_license", '' );
        $response      = $this->remote_post( 'deactivate_license' );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        delete_option( "{$this->db_prefix}_license" );
        delete_option( "{$this->db_prefix}_license_status" );
        delete_transient( "{$this->db_prefix}_license_data" );

        return $response;
    }

    public function check( $args = [] ) {
        $this->license = get_option( "{$this->db_prefix}_license", '' );
        $_license_data = get_transient( "{$this->db_prefix}_license_data" );

        if ( $_license_data !== false ) {
            $_license_data = (array) $_license_data;
        }

        if ( $_license_data !== false && ! empty( $_license_data ) ) {
            return $_license_data;
        }

        $response = $this->remote_post( 'check_license' );

        if ( is_wp_error( $response ) ) {
            delete_transient( "{$this->db_prefix}_license_data" );
            return $response;
        }

        set_transient( "{$this->db_prefix}_license_data", $response, MONTH_IN_SECONDS * 3 );

        return $response;
    }

    /**
     * 'activate_license'
     *
     * @param mixed $args
     * @return mixed
     */
    public function remote_post( $action ) {
        if ( empty( $this->license ) ) {
            return new WP_Error( 'empty_license', __( 'Please provide a valid license.', $this->textdomain ) );
        }

        $api_params = [
            'edd_action'  => $action,
            'license'     => $this->license,
            'item_id'     => $this->item_id,
            'item_name'   => rawurlencode( $this->item_name ), // the name of our product in EDD
            'url'         => home_url(),
            'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production'
        ];

        $response = wp_safe_remote_post(
            $this->storeURL,
            [
                'timeout'   => 15,
                'sslverify' => false,
                'body'      => $api_params
            ]
        );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            if ( is_wp_error( $response ) ) {
                return $response;
            }

            return new WP_Error( 'unknown', __( 'An error occurred, please try again.', $this->textdomain ) );
        }

        $license_data = $this->maybe_error( json_decode( wp_remote_retrieve_body( $response ) ) );

        if ( ! is_wp_error( $license_data ) ) {
            $license_data->license_key = $this->hide_license_key( $this->license );
        }

        return $license_data;
    }

    private function maybe_error( $license_data ) {
        if ( false === $license_data->success ) {
            $message = '';
            switch ( $license_data->error ) {
                case 'expired':
                    $message = sprintf(
                        /* translators: the license key expiration date */
                        __( 'Your license key expired on %s.', $this->textdomain ),
                        date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                    );
                    break;

                case 'disabled':
                case 'revoked':
                    $message = __( 'Your license key has been disabled.', $this->textdomain );
                    break;

                case 'missing':
                    $message = __( 'Invalid license.', $this->textdomain );
                    break;

                case 'invalid':
                case 'site_inactive':
                    $message = __( 'Your license is not active for this URL.', $this->textdomain );
                    break;

                case 'item_name_mismatch':
                    /* translators: the plugin name */
                    $message = sprintf( __( 'This appears to be an invalid license key for %s.', $this->textdomain ), $this->item_name );
                    break;

                case 'no_activations_left':
                    $message = __( 'Your license key has reached its activation limit.', $this->textdomain );
                    break;

                default:
                    $message = __( 'An error occurred, please try again.', $this->textdomain );
                    break;
            }

            return new WP_Error( $license_data->error, wp_kses( $message, 'post' ) );
        }

        return $license_data;
    }

    public function __get( $name ) {
        if ( isset( $this->args[$name] ) ) {
            return $this->args[$name];
        }
    }
}
