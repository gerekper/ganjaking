<?php

namespace WPDeveloper\BetterDocsPro\Core;

use Plugin_Upgrader;
use Automatic_Upgrader_Skin;
use WPDeveloper\BetterDocsPro\Utils\Helper;

class Installer {
    private $active_plugins = [];
    private $slug           = 'betterdocs';
    private $basename       = 'betterdocs/betterdocs.php';

    public function __construct() {
        $this->active_plugins = get_option( 'active_plugins' );
        if ( ! isset( $this->active_plugins[$this->basename] ) ) {
            // Install & Activate Free Plugin
            if ( $this->install() ) {
                set_transient( 'betterdocs_maybe_redirect', true );
            }
        }
    }

    public function install() {
        $is_installed = $this->get_installed_plugin_data();
        $plugin_data  = $this->get_plugin_data();

        set_transient( 'maybe_betterdocs_installed_by_pro', true );

        if ( $is_installed ) {
            if ( isset( $plugin_data->version ) && $is_installed['Version'] != $plugin_data->version ) {
                $this->upgrade_or_install_plugin();
            }

            if ( Helper::is_plugin_active( $this->basename ) ) {
                return false;
            } else {
                activate_plugin( $this->safe_path( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->basename ), '', false, true );
                return true;
            }
        } else {
            $download_link = isset( $plugin_data->download_link ) ? $plugin_data->download_link : '';
            if ( ! empty( $download_link ) && $this->upgrade_or_install_plugin( $download_link, false ) ) {
                return true;
            }
        }

        return false;
    }

    public function get_installed_plugin_data() {
        $plugins = Helper::get_plugins();
        return isset( $plugins[$this->basename] ) ? $plugins[$this->basename] : false;
    }

    protected function get_plugin_data() {
        $installed_plugin = false;
        if ( $this->basename ) {
            $installed_plugin = $this->get_installed_plugin_data();
        }

        if ( $installed_plugin ) {
            return $installed_plugin;
        }

        $args = [
            'slug'   => $this->slug,
            'fields' => [
                'version' => false
            ]
        ];

        $response = wp_remote_post(
            'http://api.wordpress.org/plugins/info/1.0/',
            [
                'body' => [
                    'action'  => 'plugin_information',
                    'request' => serialize( (object) $args )
                ]
            ]
        );

        if ( is_wp_error( $response ) ) {
            return false;
        } else {
            $response = unserialize( wp_remote_retrieve_body( $response ) );

            if ( $response ) {
                return $response;
            } else {
                return false;
            }
        }
    }

    public function upgrade_or_install_plugin( $basename = '', $upgrade = true ) {
        if ( empty( $basename ) ) {
            $basename = $this->basename;
        }

        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';
        include_once ABSPATH . 'wp-includes/pluggable.php';

        $skin     = new Automatic_Upgrader_Skin;
        $upgrader = new Plugin_Upgrader( $skin );

        if ( $upgrade == true ) {
            $upgrader->upgrade( $basename );
        } else {
            $upgrader->install( $basename );
            activate_plugin( $upgrader->plugin_info(), '', false, true );
        }

        return $skin->result;
    }

    public function safe_path( $path ) {
        $path = str_replace( ['//', '\\\\'], ['/', '\\'], $path );
        return str_replace( ['/', '\\'], DIRECTORY_SEPARATOR, $path );
    }
}
