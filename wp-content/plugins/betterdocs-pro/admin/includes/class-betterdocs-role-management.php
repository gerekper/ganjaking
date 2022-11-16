<?php
/**
 * Role Management Class for BetterDocs
 */

class BetterDocs_Role_Management {

    private static $instance = null;
    protected static $default_capabilities;

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
        self::$default_capabilities = class_exists('Betterdocs_Role_Management_Lite') ? Betterdocs_Role_Management_Lite::$default_capabilities : '';
        add_filter( 'betterdocs_advanced_settings_sections', array( $this, 'settings' ), 10, 1 );
        add_action( 'bdocs_settings_saved', array( $this, 'saved_settings' ), 10, 1 );
    }

    /**
     * Settings in Settings Menu
     *
     * @param array $settings
     * @return array
     */
    public function settings( $settings ) {
        $settings['role_management_section'] = array(
            'title' => __('Role Management', 'betterdocs-pro'),
            'priority'    => 0,
            'fields' => array(
                'rms_title' => array(
                    'type'        => 'title',
                    'label'       => __('Role Management', 'betterdocs-pro'),
                    'priority'    => 0,
                ),
                'article_roles' => array(
                    'type'        => 'select',
                    'label'       => __('Who Can Write Docs?', 'betterdocs-pro'),
                    'priority'    => 1,
                    'multiple' => true,
                    'default' => 'administrator',
                    'options' => BetterDocs_Settings::get_roles()
                ),
                'settings_roles' => array(
                    'type'        => 'select',
                    'label'       => __('Who Can Edit Settings?', 'betterdocs-pro'),
                    'priority'    => 1,
                    'multiple' => true,
                    'default' => 'administrator',
                    'options' => BetterDocs_Settings::get_roles()
                ),
                'analytics_roles' => array(
                    'type'        => 'select',
                    'label'       => __('Who Can Check Analytics?', 'betterdocs-pro'),
                    'priority'    => 1,
                    'multiple'    => true,
                    'default'     => 'administrator',
                    'options'     => BetterDocs_Settings::get_roles()
                ),
            )
        );

        return $settings;
    }

    public static function remove_caps( $remove_default_caps = false ){
        $_given_caps = class_exists('BetterDocs_Settings') ? BetterDocs_Settings::get_role_cap_mapper() : '';
        /**
         * This will run while pro is activating.
         */
        if( $remove_default_caps ) {
            unset(self::$default_capabilities['administrator']);

            foreach( self::$default_capabilities as $role => $caps ) {
                $role_object = get_role( $role );
                if( $role_object instanceof \WP_Role ) {
                    foreach( $caps as $_cap ) {
                        $role_object->remove_cap( $_cap );
                    }
                }
            }

            self::saved_settings();
            return;
        }

        /**
         * This will run while pro is deactivating.
         */
        if( ! empty( $_given_caps ) ) {
            foreach( $_given_caps as $cap => $_roles ) {
                if( ! empty( $_roles['roles'] ) ) {
                    foreach( $_roles['roles'] as $_role ) {
                        if( $_role === 'administrator' ) {
                            continue;
                        }
                        $role = get_role( $_role );
                        if( ! is_null( $role ) && $role instanceof \WP_Role ) {
                            $_new_cap = $cap === 'write_docs' ? 'edit_docs' : $cap;
                            $role->remove_cap( $_new_cap );
                            foreach( self::$default_capabilities[ $_role ] as $_cap ) {
                                $role->remove_cap( $_cap );
                            }
                        }
                    }
                }
            }
            delete_option('_betterdocs_caps_assigned');
        }
    }

    public static function saved_settings( $settings = [] ) {
        $_given_caps = BetterDocs_Settings::get_role_cap_mapper( $settings );

        if( ! empty( $_given_caps ) ) {
            foreach( $_given_caps as $cap => $_roles ) {
                if( ! empty( $_roles['roles'] ) ) {
                    foreach( $_roles['roles'] as $_role ) {
                        if( $_role === 'administrator' ) {
                            continue;
                        }
                        $role = get_role( $_role );
                        if( ! is_null( $role ) && $role instanceof \WP_Role ) {
                            $_new_cap = $cap === 'write_docs' ? 'edit_docs' : $cap;
                            if( $_new_cap === 'edit_docs' ) {
                                foreach( self::$default_capabilities[ $_role ] as $_cap ) {
                                    $role->add_cap( $_cap );
                                }
                            } else {
                                $role->add_cap( $_new_cap );
                            }
                        }
                    }
                }
            }
        }
    }
}
/**
 * Initialize the Role Management Class
 */
if (is_admin()) {
    BetterDocs_Role_Management::get_instance();
}