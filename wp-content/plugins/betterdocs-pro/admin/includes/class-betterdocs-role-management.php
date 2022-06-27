<?php
/**
 * Role Management Class for BetterDocs
 */
class BetterDocs_Role_Management {

    private static $instance = null;
    protected $default_capabilities = [
        'administrator' => [
            'edit_docs',
            'edit_others_docs',
            'delete_docs',
            'publish_docs',
            'read_private_docs',
            'delete_private_docs',
            'delete_published_docs',
            'delete_others_docs',
            'edit_private_docs',
            'edit_published_docs',
            'manage_doc_terms',
            'edit_doc_terms',
            'delete_doc_terms',
            'manage_knowledge_base_terms',
            'edit_knowledge_base_terms',
            'delete_knowledge_base_terms'
        ],
        'editor' => [
            'edit_docs',
            'edit_others_docs',
            'publish_docs',
            'edit_published_docs',
            'edit_private_docs',
            'read_private_docs',
            'delete_published_docs',
            'delete_private_docs',
            'delete_docs',
            'delete_others_docs',
            'manage_doc_terms',
            'edit_doc_terms',
            'delete_doc_terms',
            'manage_knowledge_base_terms',
            'edit_knowledge_base_terms',
            'delete_knowledge_base_terms'
        ],
        'author' => [
            'edit_docs',
            'edit_published_docs',
            'publish_docs',
            'delete_docs',
            'delete_published_docs'
        ],
        'contributor' => [
            'edit_docs',
            'delete_docs'
        ],
        'other_roles' => [
            'edit_docs',
            'delete_docs',
        ]
    ];

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
        add_action( 'betterdocs_assign_default_caps', array( $this ,'default_capabilities_for_others' ), 10 );
        add_filter( 'betterdocs_advanced_settings_sections', array( $this, 'settings' ), 10, 1 );
        add_action( 'bdocs_settings_saved', array( $this, 'selected_roles_callback' ), 10, 1 );
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

    public function selected_roles_callback( $settings ) {
        do_action( 'betterdocs_add_capabilities', BetterDocs_Settings::get_role_cap_mapper( $settings ) );
    }

    public function default_capabilities_for_others() {
        $default_roles            =  array('subscriber', 'editor', 'author', 'contributor');
        $existing_article_roles   =  ( BetterDocs_DB::get_settings('article_roles') == 'off' || BetterDocs_DB::get_settings('article_roles') == 'administrator'  ) ? array('administrator') : BetterDocs_DB::get_settings('article_roles');
        $existing_settings_roles  =  ( BetterDocs_DB::get_settings('settings_roles') == 'off' || BetterDocs_DB::get_settings('settings_roles') == 'administrator' ) ? array('administrator') : BetterDocs_DB::get_settings('settings_roles');
        $existing_analytics_roles =  ( BetterDocs_DB::get_settings('analytics_roles') == 'off' || BetterDocs_DB::get_settings('analytics_roles') == 'administrator' ) ? array('administrator') : BetterDocs_DB::get_settings('analytics_roles');

        $non_selected_roles = array(
            'article_roles'   => array_values( array_diff( $default_roles, $existing_article_roles ) ),
            'settings_roles'  => array_values( array_diff( $default_roles, $existing_settings_roles ) ),
            'analytics_roles' => array_values( array_diff( $default_roles, $existing_analytics_roles ) )
        );

        $selected_roles = array(
            'article_roles'   => $existing_article_roles,
            'settings_roles'  => $existing_settings_roles,
            'analytics_roles' => $existing_analytics_roles
        );

        $map_non_selected_roles = BetterDocs_Settings::get_role_cap_mapper( $non_selected_roles );
        $map_selected_roles     = BetterDocs_Settings::get_role_cap_mapper( $selected_roles );

        foreach( $map_non_selected_roles as $key => $values ) {
            $roles = ! empty( $values['roles'] ) ? $values['roles'] : '' ;
            if( ! empty( $roles ) ) {
                foreach( $roles as $role ) {
                    $role_object = get_role( $role );
                    if( is_null( $role_object ) || ! $role_object instanceof \WP_Role ) {
                        continue;
                    }
                    if( $key == 'write_docs' ) {
                        $role_default_caps = ! empty( $this->default_capabilities[$role] ) ? $this->default_capabilities[$role] : $this->default_capabilities['other_roles'];
                        foreach( $role_default_caps as $cap ) {
                            $role_object->remove_cap( $cap );
                        }
                    } else {
                        $role_object->remove_cap( $key );
                    }
                }
            }
        }

        foreach( $map_selected_roles as $key => $values ) {
            $roles = ! empty( $values['roles'] ) ? $values['roles'] : '' ;
            if( ! empty( $roles ) ) {
                foreach( $roles as $role ) {
                    $role_object = get_role( $role );
                    if( is_null( $role_object ) || ! $role_object instanceof \WP_Role ) {
                        continue;
                    }
                    if( $key == 'write_docs' ) {
                        $role_default_caps = ! empty( $this->default_capabilities[$role] ) ? $this->default_capabilities[$role] : $this->default_capabilities['other_roles'];
                        foreach( $role_default_caps as $cap ) {
                            if( ! $role_object->has_cap( $cap ) ) {
                                $role_object->add_cap( $cap );
                            }
                        }
                    } else {
                        if( ! $role_object->has_cap( $key ) ) {
                            $role_object->add_cap( $key );
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
BetterDocs_Role_Management::get_instance();