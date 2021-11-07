<?php
/**
 * Custom Post Type for GDPRs and Taxonomies.
 */
class WordPress_GDPR_Requests extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;
    /**
     * Constructor.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @param string $plugin_name
     * @param string $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->prefix = 'wordpress_gdpr_request_';

        add_filter('manage_gdpr_request_posts_columns', array($this, 'columns_head'));
        add_action('manage_gdpr_request_posts_custom_column', array($this, 'columns_content'), 10, 1);
    }

    /**
     * Init.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @return bool
     */
    public function init()
    {
        global $wordpress_gdpr_options, $wp_version;

        $this->options = $wordpress_gdpr_options;

        if($this->get_option('useWPCoreFunctions') && version_compare( $wp_version, '4.9.6', '>=' )) {
            return false;
        }

        $this->register_gdpr_request_post_type();
    }

    /**
     * Register GDPR Post Type.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @return bool
     */
    public function register_gdpr_request_post_type()
    {
        $singular = __('GDPR Request', 'wordpress-gdpr');
        $plural = __('GDPR Requests', 'wordpress-gdpr');

        $labels = array(
            'name' => $plural,
            'all_items' => sprintf(__('Requests', 'wordpress-gdpr'), $plural),
            'singular_name' => $singular,
            'add_new' => sprintf(__('New %s', 'wordpress-gdpr'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'wordpress-gdpr'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'wordpress-gdpr'), $singular),
            'new_item' => sprintf(__('New %s', 'wordpress-gdpr'), $singular),
            'view_item' => sprintf(__('View %s', 'wordpress-gdpr'), $plural),
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
            'query_var' => 'gdpr_requests',
            'supports' => array('title', 'editor', 'author', 'revisions', 'thumbnail'),
            'menu_icon' => 'dashicons-businessman',
            'show_in_menu' => 'wordpress_gdpr_options_options'
        );

        register_post_type('gdpr_request', $args);
    }


    /**
     * Columns Head.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
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
                $output['user'] = __('User', 'wordpress-gdpr');
                $output['confirmed'] = __('Email confirmed', 'wordpress-gdpr');
                $output['status'] = __('Status', 'wordpress-gdpr');
                $output['userid'] = __('User Identifier', 'wordpress-gdpr');
                $output['actions'] = __('Actions', 'wordpress-gdpr');
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
     * @link    http://plugins.db-dzine.com
     *
     * @param string $column_name Column Name
     *
     * @return string
     */
    public function columns_content($column_name)
    {
        global $post;

        if ($column_name == 'user') {
            $user = array();
            $user['firstname'] = get_post_meta($post->ID, 'gdpr_firstname', true);
            $user['lastname'] = get_post_meta($post->ID, 'gdpr_lastname', true);
            $user['email'] = get_post_meta($post->ID, 'gdpr_email', true);

            echo implode('<br/>', array_filter($user));
        }

        if ($column_name == 'confirmed') {
            echo get_post_meta($post->ID, 'gdpr_confirmed', true);
        }

        if ($column_name == 'status') {
            echo get_post_meta($post->ID, 'gdpr_status', true);
        }

        if ($column_name == 'userid') {
            $user_id = get_post_meta($post->ID, 'gdpr_user_id', true);
            if(empty($user_id)) {
                $user_id = get_post_meta($post->ID, 'gdpr_email', true);
            }
            echo $user_id;
        }

        if ($column_name == 'actions') {
            $user_id = get_post_meta($post->ID, 'gdpr_user_id', true);
            $type = get_post_meta($post->ID, 'gdpr_type', true);
            $actions = array();
            if($type == "forget-me") {
                $actions[] = 
                '<form style="display: inline;" action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="GET">
                    <input type="hidden" name="wordpress_gdpr[redirect]" value="' . esc_url($_SERVER['REQUEST_URI']) . '">
                    <input type="hidden" name="wordpress_gdpr[user_id]" value="' . $user_id . '">
                    <input type="hidden" name="wordpress_gdpr[post_id]" value="' . $post->ID . '">
                    <input type="submit" name="wordpress_gdpr[delete-data]" class="button" value="' . __('Delete Data', 'wordpress-gdpr') . '">
                </form>';
            }
            if($type == "request-data") {
                $actions[] = 
                '<form style="display: inline;" action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="GET">
                    <input type="hidden" name="wordpress_gdpr[redirect]" value="' . esc_url($_SERVER['REQUEST_URI']) . '">
                    <input type="hidden" name="wordpress_gdpr[user_id]" value="' . $user_id . '">
                    <input type="hidden" name="wordpress_gdpr[post_id]" value="' . $post->ID . '">
                    <input type="submit" name="wordpress_gdpr[request-data]" class="button" value="' . __('Export Data', 'wordpress-gdpr') . '">
                </form>';
            }
            if($type == "request-data") {
                $actions[] = 
                '<form style="display: inline;" action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="GET">
                    <input type="hidden" name="wordpress_gdpr[redirect]" value="' . esc_url($_SERVER['REQUEST_URI']) . '">
                    <input type="hidden" name="wordpress_gdpr[user_id]" value="' . $user_id . '">
                    <input type="hidden" name="wordpress_gdpr[post_id]" value="' . $post->ID . '">
                    <input type="submit" name="wordpress_gdpr[send-data]" class="button" value="' . __('Send Data', 'wordpress-gdpr') . '">
                </form>';
            }
            $actions[] = 
            '<form style="display: inline;" action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="GET">
                <input type="hidden" name="wordpress_gdpr[redirect]" value="' . esc_url($_SERVER['REQUEST_URI']) . '">
                <input type="hidden" name="wordpress_gdpr[post_id]" value="' . $post->ID . '">
                <input type="submit" name="wordpress_gdpr[set-done]" class="button" value="' . __('Manually Done', 'wordpress-gdpr') . '">
            </form>';

            echo implode(' ', array_filter($actions));
        }
    }

/**
     * Add custom ticket metaboxes
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $post_type [description]
     * @param   [type]                       $post      [description]
     */
    public function add_custom_metaboxes($post_type, $post)
    {
        add_meta_box('wordpress-gdpr-address', 'User', array($this, 'user'), 'gdpr_request', 'normal', 'high');
    }

    /**
     * Display Metabox user
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function user()
    {
        global $post, $wordpress_gdpr_request_options;

        wp_nonce_field(basename(__FILE__), 'wordpress_gdpr_request_meta_nonce');

        $gdpr_firstname = get_post_meta($post->ID, 'gdpr_firstname', true);
        $gdpr_lastname = get_post_meta($post->ID, 'gdpr_lastname', true);
        $gdpr_email = get_post_meta($post->ID, 'gdpr_email', true);
        $gdpr_type = get_post_meta($post->ID, 'gdpr_type', true);
        $gdpr_unique = get_post_meta($post->ID, 'gdpr_unique', true);
        $gdpr_status = get_post_meta($post->ID, 'gdpr_status', true);
        $gdpr_confirmed = get_post_meta($post->ID, 'gdpr_confirmed', true);
        $gdpr_user_id = get_post_meta($post->ID, 'gdpr_user_id', true);

        echo '<div class="wordpress-gdpr-container">';
            echo '<div class="wordpress-gdpr-row">';
                echo '<div class="wordpress-gdpr-col-sm-6">';
                    echo '<label for="gdpr_firstname">' . __( 'Firstname', 'wordpress-gdpr' ) . '</label><br/>';
                    echo '<input class="wordpress-gdpr-input-field" name="gdpr_firstname" value="' . $gdpr_firstname . '" type="text">';
                echo '</div>';
            
                echo '<div class="wordpress-gdpr-col-sm-6">';
                    echo '<label for="gdpr_lastname">' . __( 'Last name', 'wordpress-gdpr' ) . '</label><br/>';
                    echo '<input class="wordpress-gdpr-input-field" name="gdpr_lastname" value="' . $gdpr_lastname . '" type="text">';
                echo '</div>';

                echo '<div class="wordpress-gdpr-col-sm-6">';
                    echo '<label for="gdpr_email">' . __( 'Email', 'wordpress-gdpr' ) . '</label><br/>';
                    echo '<input class="wordpress-gdpr-input-field" name="gdpr_email" value="' . $gdpr_email . '" type="text">';
                echo '</div>';
                
            echo '</div>';
        echo '</div>';
    }

    /**
     * Save Custom Metaboxes
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $post_id [description]
     * @param   [type]                       $post    [description]
     * @return  [type]                                [description]
     */
    public function save_custom_metaboxes($post_id, $post)
    {
        global $wordpress_gdpr_request_options;

        if($post->post_type !== "gdpr_request") {
            return false;
        }

        // Is the user allowed to edit the post or page?
        if (!current_user_can('edit_post', $post->ID)) {
            return $post->ID;
        }

        if ($post->post_type == 'revision') {
            return false;
        }

        if (!isset($_POST['wordpress_gdpr_request_meta_nonce']) || !wp_verify_nonce($_POST['wordpress_gdpr_request_meta_nonce'], basename(__FILE__))) {
            return false;
        }

        $possible_inputs = array(
            'gdpr_firstname',
            'gdpr_lastname',
            'gdpr_email',
            'gdpr_type',
            'gdpr_unique',
            'gdpr_status',
            'gdpr_confirmed',
            'gdpr_user_id',
        );

        // Add values of $ticket_meta as custom fields
        foreach ($possible_inputs as $possible_input) {
            $val = isset($_POST[$possible_input]) ? $_POST[$possible_input] : '';
            update_post_meta($post->ID, $possible_input, $val);
        }
    }

    public function check_action()
    {       
        if(!isset($_GET['wordpress_gdpr']) || !is_admin()) {
            return false;
        }

        if(!isset($_GET['wordpress_gdpr']['set-done'])) {
            return false;
        }

        if(isset($_GET['wordpress_gdpr']['post_id'])) {
            update_post_meta($_GET['wordpress_gdpr']['post_id'], 'gdpr_status', __('Done', 'wordpress-gdpr') );
        } else {
            wp_die( __('Post id not found', 'wordpress-gdpr'));
        }

        wp_redirect( $_GET['wordpress_gdpr']['redirect'] );
        exit;
    }
}