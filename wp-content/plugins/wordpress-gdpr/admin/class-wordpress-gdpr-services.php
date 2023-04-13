<?php

class WordPress_GDPR_Services extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct Service Post Type Class
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @param [type] $plugin_name [description]
     * @param [type] $version     [description]
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Init Service Post type Class if enabled
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @return [type] [description]
     */
    public function init()
    {
        global $wordpress_gdpr_options;
        $this->options = $wordpress_gdpr_options;

        $this->register_service_post_type();
        $this->register_service_taxonomy();            
    }

    /*
     * Register Service Post Type
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @return  [type]                       [description]
     */
    public function register_service_post_type()
    {
        $singular = __('GDPR Service', 'wordpress-gdpr');
        $plural = __('GDPR Services', 'wordpress-gdpr');

        $labels = array(
            'name' => $plural,
            'all_items' => sprintf(__('Services', 'wordpress-gdpr'), $plural),
            'singular_name' => $singular,
            'add_new' => sprintf(__('New %s', 'wordpress-gdpr'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'wordpress-gdpr'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'wordpress-gdpr'), $singular),
            'new_item' => sprintf(__('New %s', 'wordpress-gdpr'), $singular),
            'view_item' => sprintf(__('View %s', 'wordpress-gdpr'), $singular),
            'search_items' => sprintf(__('Search %s', 'wordpress-gdpr'), $plural),
            'not_found' => sprintf(__('No %s found', 'wordpress-gdpr'), $plural),
            'not_found_in_trash' => sprintf(__('No %s found in trash', 'wordpress-gdpr'), $plural),
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'menu_position' => 120,
            'query_var' => 'services',
            'supports' => array('title', 'editor', 'author', 'page-attributes'),
            'menu_icon' => 'dashicons-image-filter',
            'show_in_menu' => 'wordpress_gdpr_options_options'
        );

        register_post_type('gdpr_service', $args);
    }

    /**
     * Register Service Categories and Service Filter Taxonomies.
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @return  [type]                       [description]
     */
    public function register_service_taxonomy()
    {
        // Service Category
        $singular = __('GDPR Service Category', 'wordpress-gdpr');
        $plural = __('GDPR Service Categories', 'wordpress-gdpr');

        $labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'search_items' => sprintf(__('Search %s', 'wordpress-gdpr'), $plural),
            'all_items' => sprintf(__('All %s', 'wordpress-gdpr'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'wordpress-gdpr'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'wordpress-gdpr'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'wordpress-gdpr'), $singular),
            'update_item' => sprintf(__('Update %s', 'wordpress-gdpr'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'wordpress-gdpr'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'wordpress-gdpr'), $singular),
            'menu_name' => $plural,
        );

        $args = array(
                'labels' => $labels,
                'public' => false,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'sort' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                // 'show_in_nav_menus' => true,
                // 'show_in_menu' => true
        );

        register_taxonomy('gdpr_service_categories', 'gdpr_service', $args);
    }

    public function add_taxonomy_submenu() { 
        add_submenu_page(
            'wordpress_gdpr_options_options', 
            __('Service Categories', 'wordpress-gdpr'), 
            __('Service Categories', 'wordpress-gdpr'), 
            'manage_options', 
            'edit-tags.php?taxonomy=gdpr_service_categories&post_type=gdpr_service'
        ); 
    }  

    /**
     * Columns Head.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://www.welaunch.io
     *
     * @param string $columns Columnd
     *
     * @return string
     */
    public function columns_head($columns)
    {
        $output = array();
        foreach ($columns as $column => $name) {
            $output[$column] = $name;

            if ($column === 'title') {
                $output['cookies'] = __('Cookies', 'wordpress-gdpr');
                $output['deactivatable'] = __('Deactivatable', 'wordpress-gdpr');
            }
        }

        return $output;
    }

    /**
     * Columns Content.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://www.welaunch.io
     *
     * @param string $column_name Column Name
     *
     * @return string
     */
    public function columns_content($column_name)
    {
        global $post;

        if ($column_name == 'cookies') {
            echo get_post_meta($post->ID, 'cookies', true);
        }

        if ($column_name == 'deactivatable') {
            echo get_post_meta($post->ID, 'deactivatable', true);
        }
    }

    /**
     * Add custom ticket metaboxes
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @param   [type]                       $post_type [description]
     * @param   [type]                       $post      [description]
     */
    public function add_custom_metaboxes($post_type, $post)
    {
        add_meta_box('gdpr-meta', 'Meta Information', array($this, 'meta_fields'), 'gdpr_service', 'normal', 'high');
    }

    /**
     * Display Metabox meta_fields
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @return  [type]                       [description]
     */
    public function meta_fields()
    {
        global $post;

        wp_nonce_field(basename(__FILE__), 'gdpr_service_meta_nonce');

        $html = "";

        // Activated or Not Field
        $checked = "";
        $deactivatable = get_post_meta($post->ID, 'deactivatable' , true);
        if($deactivatable == "1" || $deactivatable === "") {
            $checked = 'checked="checked"';
        }
        $html .= '<p class="post-attributes-label-wrapper">';
            $html .= '<label for="deactivatable"> <input ' . $checked . ' type="checkbox" value="1" name="deactivatable">';
            $html .= '  <b>' . __('Capable of being deactivated', 'wordpress-gdpr') . '</b></label>';
        $html .= '</p>';

        // Enabled by Default
        $checked = "";
        $defaultEnabled = get_post_meta($post->ID, 'defaultEnabled' , true);
        if($defaultEnabled == "1") {
            $checked = 'checked="checked"';
        }
        $html .= '<p class="post-attributes-label-wrapper">';
            $html .= '<label for="defaultEnabled"> <input ' . $checked . ' type="checkbox" value="1" name="defaultEnabled">';
            $html .= '  <b>' . __('Enabled by Default', 'wordpress-gdpr') . '</b></label>';
        $html .= '</p>';

        // PixelYourSite
        $checked = "";
        $pixelyoursite = get_post_meta($post->ID, 'pixelyoursite' , true);
        if($pixelyoursite == "1") {
            $checked = 'checked="checked"';
        }
        $html .= '<p class="post-attributes-label-wrapper">';
            $html .= '<label for="pixelyoursite"> <input ' . $checked . ' type="checkbox" value="1" name="pixelyoursite">';
            $html .= '  <b>' . __('Is PixelYourSite Service?', 'wordpress-gdpr') . '</b></label>';
        $html .= '</p>';

        // Adsense
        $checked = "";
        $adsense = get_post_meta($post->ID, 'adsense' , true);
        if($adsense == "1") {
            $checked = 'checked="checked"';
        }
        $html .= '<p class="post-attributes-label-wrapper">';
            $html .= '<label for="adsense"> <input ' . $checked . ' type="checkbox" value="1" name="adsense">';
            $html .= '  <b>' . __('Is Adsense Service?', 'wordpress-gdpr') . '</b></label>';
        $html .= '</p>';

        $cookies = get_post_meta($post->ID, 'cookies' , true);
        $html .= '<p class="post-attributes-label-wrapper">';
            $html .= '<label for="cookies"><b>' . __('Cookies Set by this Service (seperated by Comma)', 'wordpress-gdpr') . '</b></label>';
        $html .= '</p>';
        $html .= '<input style="width: 100%;" type="text" value="' . $cookies . '" name="cookies">';

        // Head Script Tag Meta Field
        $head_script = get_post_meta($post->ID, 'head_script' , true);
        $html .= '<p class="post-attributes-label-wrapper">';
            $html .= '<label for="head_script"><b>' . __('Head Script Tag', 'wordpress-gdpr') . '</b></label>';
        $html .= '</p>';
        $html .= '<textarea style="width: 100%;" name="head_script" rows="10">' . $head_script . '</textarea>';

       // Body Script Tag Meta Field
        $body_script = get_post_meta($post->ID, 'body_script' , true);
        $html .= '<p class="post-attributes-label-wrapper">';
            $html .= '<label for="body_script"><b>' . __('Body Script Tag', 'wordpress-gdpr') . '</b></label>';
        $html .= '</p>';
        $html .= '<textarea style="width: 100%;" name="body_script" rows="10">' . $body_script . '</textarea>';


        echo $html;    
    }

   /**
     * Save Custom Metaboxes
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @param   [type]                       $post_id [description]
     * @param   [type]                       $post    [description]
     * @return  [type]                                [description]
     */
    public function save_custom_metaboxes($post_id, $post)
    {
        if($post->post_type !== "gdpr_service") {
            return false;
        }

        // Is the user allowed to edit the post or page?
        if (!current_user_can('edit_post', $post->ID)) {
            return $post->ID;
        }

        if ($post->post_type == 'revision') {
            return false;
        }

        if (!isset($_POST['gdpr_service_meta_nonce']) || !wp_verify_nonce($_POST['gdpr_service_meta_nonce'], basename(__FILE__))) {
            return false;
        }

        if(!isset($_POST['deactivatable'])) {
            $_POST['deactivatable'] = "0";
        }

        if(!isset($_POST['defaultEnabled'])) {
            $_POST['defaultEnabled'] = "0";
        }

        if(!isset($_POST['pixelyoursite'])) {
            $_POST['pixelyoursite'] = "0";
        }

        if(!isset($_POST['adsense'])) {
            $_POST['adsense'] = "0";
        }
        
        update_post_meta($post->ID, 'deactivatable', $_POST['deactivatable']);
        update_post_meta($post->ID, 'defaultEnabled', $_POST['defaultEnabled']);
        update_post_meta($post->ID, 'pixelyoursite', $_POST['pixelyoursite']);
        update_post_meta($post->ID, 'adsense', $_POST['adsense']);
        update_post_meta($post->ID, 'cookies', $_POST['cookies']);
        update_post_meta($post->ID, 'head_script', $_POST['head_script']);
        update_post_meta($post->ID, 'body_script', $_POST['body_script']);

    }
}