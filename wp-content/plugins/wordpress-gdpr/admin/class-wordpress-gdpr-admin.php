<?php

class WordPress_GDPR_Admin extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct GDPR Admin Class
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://www.welaunch.io
     * @param   string                         $plugin_name
     * @param   string                         $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Load Extensions
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://www.welaunch.io
     * @return  boolean
     */
    public function load_extensions()
    {
        if(!is_admin() || !current_user_can('administrator') || (defined('DOING_AJAX') && DOING_AJAX && (isset($_POST['action']) && !$_POST['action'] == "wordpress_gdpr_options_ajax_save") )) {
            return false;
        }

        // Load the theme/plugin options
        if (file_exists(plugin_dir_path(dirname(__FILE__)).'admin/options-init.php')) {
            require_once plugin_dir_path(dirname(__FILE__)).'admin/options-init.php';
        }
        return true;
    }

    /**
     * Init
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://www.welaunch.io
     * @return  boolean
     */
    public function init()
    {
        global $wordpress_gdpr_options;

        if(!is_admin() || !current_user_can('administrator') || (defined('DOING_AJAX') && DOING_AJAX)){
            $wordpress_gdpr_options = get_option('wordpress_gdpr_options');
        }

        $this->options = $wordpress_gdpr_options;
    }

    public function reorder_menu_items()
    {
        global $submenu;

        if(isset($submenu['wordpress_gdpr_options_options'])) {
            
            $old_array = $submenu['wordpress_gdpr_options_options'];

            $new_array = array();

            $new_array['2_service_categories'] = isset($submenu['wordpress_gdpr_options_options'][0]) ? $submenu['wordpress_gdpr_options_options'][0] : array();
            $new_array['3_consent_log'] = isset($submenu['wordpress_gdpr_options_options'][1]) ? $submenu['wordpress_gdpr_options_options'][1] : array();
            $new_array['0_requests'] = isset($submenu['wordpress_gdpr_options_options'][2]) ? $submenu['wordpress_gdpr_options_options'][2] : array();
            $new_array['1_services'] = isset($submenu['wordpress_gdpr_options_options'][3]) ? $submenu['wordpress_gdpr_options_options'][3] : array();
            $new_array['4_settings'] = isset($submenu['wordpress_gdpr_options_options'][4]) ? $submenu['wordpress_gdpr_options_options'][4] : array();
            $new_array['5_import'] = isset($submenu['wordpress_gdpr_options_options'][6]) ? $submenu['wordpress_gdpr_options_options'][6] : array();
            $new_array = array_filter($new_array);
            ksort($new_array);

            $submenu['wordpress_gdpr_options_options'] = $new_array;

        }

        return $submenu;
    }

    public function menu_highlight( $parent_file ){ 

        global $current_screen;

        $taxonomy = $current_screen->taxonomy;
        
        if ( $taxonomy == 'gdpr_service_categories' ) {
            $parent_file = 'wordpress_gdpr_options_options';
        }

        return $parent_file; 
    }   

}