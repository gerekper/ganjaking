<?php
/**
 * Role Management Class for BetterDocs
 */
class BetterDocs_Role_Management {
    private static $instance = null;
    /**
     * Get Single Instance for 
     * BetterDocs_Role_Management
     * @return BetterDocs_Role_Management
     */
    public static function get_instance(){
        if( is_null( self::$instance ) ) {
            self::$instance = new STATIC;
        }
        return self::$instance;
    }
    /**
     * Initial Invoked
     */
    public function __construct(){
        add_filter( 'betterdocs_settings_tab', array( $this, 'settings' ) );
        add_filter( 'betterdocs_articles_caps', array( $this, 'caps_check' ), 10, 2 );
        add_filter( 'betterdocs_terms_caps', array( $this, 'caps_check' ), 10, 2 );
        add_filter( 'betterdocs_settings_caps', array( $this, 'settings_caps_check' ), 10, 2 );
        add_filter( 'betterdocs_analytics_caps', array( $this, 'settings_caps_check' ), 10, 2 );
    }
    /**
     * Settings in Settings Menu
     *
     * @param array $settings
     * @return array
     */
    public function settings( $settings ){
        if( ! current_user_can( 'delete_users' ) ) {
            unset( $settings['design'] );
            return $settings;
        }

        $betterdocs_advanced_settings = $settings['betterdocs_advanced_settings'];

        if( is_array( $betterdocs_advanced_settings ) && isset( $betterdocs_advanced_settings['sections']['role_management_section']['fields'] )) {
            foreach( $betterdocs_advanced_settings['sections']['role_management_section']['fields'] as $fkey => $f ) {
                unset( $betterdocs_advanced_settings['sections']['role_management_section']['fields'][ $fkey ]['disable'] );
            }
        }
        $settings['betterdocs_advanced_settings'] = $betterdocs_advanced_settings;

        return $settings;
    }
    /**
     * Check Settings for Roles
     *
     * @param string $for
     * @param boolean $giveRole
     * @return void
     */
    public function check( $for = 'article_roles', $giveRole = false ){
        global $current_user;
        if( empty( $current_user->roles ) ) {
            return;
        }
        $role = $current_user->roles[0];
        $saved_settings = BetterDocs_DB::get_settings();
        $current_check_against = null;
        if( isset( $saved_settings[ $for ] ) ) {
            $current_check_against = $saved_settings[ $for ];
        }
        if( is_null( $current_check_against ) || ! is_array( $current_check_against ) ) {
            return 'administrator';
        }
        if( in_array( $role, $current_check_against ) ) {
            if( $giveRole ) {
                return $role;
            }
            return $current_user->allcaps;
        }
        return false;
    }
    /**
     * Capabilities Check for Write and Read Docs and Category, Tags.
     *
     * @param string $default_caps
     * @param string $roles_for
     * @return void
     */
    public function caps_check( $default_caps, $roles_for ){
        $caps = $this->check( $roles_for );
        
        if( is_string( $caps ) && $caps === 'administrator' ) {
            return $caps;
        }
        
        if( $caps !== false && is_array( $caps ) ) {
            if( array_key_exists( $default_caps, $caps ) ) {
                if( $caps[ $default_caps ] ) {
                    return $default_caps;
                }
            }
        }
        return false;
    }
    /**
     * Capabilities Check for Settings and Getting Started And Analytics Menus
     *
     * @param string $default_caps
     * @param string $roles_for
     * @return void
     */
    public function settings_caps_check( $default_caps, $roles_for ){
        $role = $this->check( $roles_for, true );
        if( $role !== false ) {
            return $role;
        }
        return $default_caps;
    }
}
/**
 * Initialize the Role Management Class
 */
BetterDocs_Role_Management::get_instance();