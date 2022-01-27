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
        add_filter( 'betterdocs_advanced_settings_sections', array( $this, 'settings' ) );
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
    public function settings($settings) {
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

        $settings['internal_kb_section'] = array(
            'title' => __('Internal Knowledge Base', 'betterdocs-pro'),
            'priority'    => 1,
            'fields' => apply_filters( 'betterdocs_internal_kb_fields', array(
                'content_restriction_title' => array(
                    'type'        => 'title',
                    'label'       => __('Internal Knowledge Base', 'betterdocs-pro'),
                    'priority'    => 0,
                ),
                'enable_content_restriction' => array(
                    'type'      => 'checkbox',
                    'priority'  => 1,
                    'label'     => __( 'Enable/Disable', 'betterdocs-pro' ),
                    'default'   => '',
                    'dependency' => array(
                        1 => array(
                            'fields' => array( 'content_visibility', 'restrict_template', 'restrict_kb', 'restrict_category', 'restricted_redirect_url' ),
                        )
                    )
                ),
                'content_visibility' => array(
                    'type'        => 'select',
                    'label'       => __('Restrict Access to', 'betterdocs-pro'),
                    'help'        => __('<strong>Note:</strong> Only selected User Roles will be able to view your Knowledge Base' , 'betterdocs-pro'),
                    'priority'    => 2,
                    'multiple'    => true,
                    'default'     => 'all',
                    'options'     => BetterDocs_Settings::get_all_user_roles()
                ),
                'restrict_template' => array(
                    'type'        => 'select',
                    'label'       => __('Restriction on Docs', 'betterdocs-pro'),
                    'help'        => __('<strong>Note:</strong> Selected Docs pages will be restricted' , 'betterdocs-pro'),
                    'priority'    => 3,
                    'multiple'    => true,
                    'default'     => 'all',
                    'options'     => BetterDocs_Settings::get_texanomy()
                ),
                'restrict_category' => array(
                    'type'        => 'select',
                    'label'       => __('Restriction on Docs Categories', 'betterdocs-pro'),
                    'help'        => __('<strong>Note:</strong> Selected Docs categories will be restricted ' , 'betterdocs-pro'),
                    'priority'    => 5,
                    'multiple'    => true,
                    'default'     => 'all',
                    'options'     => BetterDocs_Settings::get_terms_list('doc_category')
                ),
                'restricted_redirect_url' => array(
                    'type'      => 'text',
                    'label'     => __('Redirect URL' , 'betterdocs-pro'),
                    'help'        => __('<strong>Note:</strong> Set a custom URL to redirect users without permissions when they try to access internal knowledge base. By default, restricted content will redirect to the "404 not found" page' , 'betterdocs-pro'),
                    'default'   => '',
                    'placeholder'   => 'https://',
                    'priority'	=> 6,
                ),
            ))
        );
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
        $user_roles = $current_user->roles;
        if( empty( $user_roles ) ) {
            return;
        }
        
        $roles = $user_roles;
        $saved_settings = BetterDocs_DB::get_settings();
       
        $current_check_against = null;
        if( isset( $saved_settings[ $for ] ) ) {
            $current_check_against = $saved_settings[ $for ];
        }
      
        if( is_null( $current_check_against ) || ! is_array( $current_check_against ) || $current_check_against == 'off' ) {
            return 'administrator';
        }
	    
        //if more than once roles are assigned to a user, run the if portion or if the role is single run the else portion
        if( count( $roles ) > 1 ) {
        	foreach ( $roles as $role ) {
		        if ( in_array( $role, $current_check_against ) && $role != 'subscriber' ) {
			        if ( $giveRole ) {
				        return $role;
			        }
			        return $current_user->allcaps;
		        }
	        }
        } else {
        	$role = isset( $roles[0] ) ? $roles[0] : '';
	        if ( in_array( $role, $current_check_against ) ) {
		        if ( $giveRole ) {
			        return $role;
		        }
		        return $current_user->allcaps;
	        }
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
            } else {
            	$current_user_role = wp_get_current_user();
            	$current_user_role->add_cap( $default_caps );
            	return $default_caps;
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