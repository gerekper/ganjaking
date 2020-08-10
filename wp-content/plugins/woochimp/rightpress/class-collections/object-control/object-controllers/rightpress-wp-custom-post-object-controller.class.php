<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-object-controller.class.php';
require_once 'interfaces/rightpress-wp-custom-post-object-controller-interface.php';

/**
 * WordPress Custom Post Type Controller
 *
 * @class RightPress_WP_Custom_Post_Object_Controller
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Custom_Post_Object_Controller extends RightPress_WP_Object_Controller implements RightPress_WP_Custom_Post_Object_Controller_Interface
{

    protected $supports_metadata = true;

    // Custom taxonomies with terms
    protected $taxonomy_term_list = null;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Call parent constructor
        parent::__construct();

        // Register custom post type
        add_action('init', array($this, 'register_custom_post_type'), 0);

        // Register custom taxonomies
        add_action('init', array($this, 'register_custom_taxonomies'), 0);

        // Register custom post statuses
        add_action('init', array($this, 'register_custom_post_statuses'), 0);
    }

    /**
     * Get status prefix
     *
     * @access public
     * @return string
     */
    public function get_status_prefix()
    {

        return str_replace('_', '-', $this->get_plugin_private_prefix());
    }

    /**
     * Get custom taxonomies with terms
     *
     * @access public
     * @return array
     */
    public function get_taxonomies_with_terms()
    {

        // Define custom taxonomies with terms
        if ($this->taxonomy_term_list === null) {
            $this->taxonomy_term_list = $this->define_taxonomies_with_terms();
        }

        // Return taxonomies with terms
        return $this->taxonomy_term_list;
    }

    /**
     * Define custom taxonomies with terms
     *
     * To be overriden by child classes
     *
     * @access public
     * @return array
     */
    public function define_taxonomies_with_terms()
    {

        return array();
    }

    /**
     * Register custom post type
     *
     * @access public
     * @return void
     */
    public function register_custom_post_type()
    {

        // Register post type
        register_post_type($this->get_data_store()->get_post_type(), $this->get_post_type_params());
    }

    /**
     * Register custom taxonomies
     *
     * @access public
     * @return void
     */
    public function register_custom_taxonomies()
    {

        // Iterate over taxonomies of this object
        foreach ($this->get_taxonomies_with_terms() as $taxonomy => $taxonomy_data) {

            // Prefix taxonomy
            $prefixed_taxonomy = $this->prefix_taxonomy($taxonomy);

            // Get post type
            $post_type = $this->get_data_store()->get_post_type();

            // Register taxonomy
            register_taxonomy($prefixed_taxonomy, $post_type, array(
                'label'             => $taxonomy_data['singular'],
                'labels'            => array(
                    'name'          => $taxonomy_data['plural'],
                    'singular_name' => $taxonomy_data['singular'],
                ),
                'public'            => false,
                'show_admin_column' => true,
                'query_var'         => true,
            ));

            // Register taxonomy for object type
            register_taxonomy_for_object_type($prefixed_taxonomy, $post_type);
        }
    }

    /**
     * Prefix taxonomy name
     *
     * @access public
     * @param string $taxonomy
     * @return string
     */
    public function prefix_taxonomy($taxonomy)
    {

        // Prefix taxonomy
        $prefixed_taxonomy = $this->get_data_store()->get_post_type() . '_' . $taxonomy;

        // Ensure it's not over 32 characters long (WordPress restrictions)
        if (strlen($prefixed_taxonomy) > 32) {
            error_log('RightPress_WP_Custom_Post_Object_Controller::prefix_taxonomy(): taxonomy "' . $prefixed_taxonomy . '" is over 32 characters in length.');
            exit;
        }

        // Return prefixed taxonomy
        return $prefixed_taxonomy;
    }

    /**
     * Register custom post statuses
     *
     * @access public
     * @return void
     */
    public function register_custom_post_statuses()
    {

        // Iterate over custom statuses of this object
        foreach ($this->get_status_list() as $key => $values) {

            // Register post status
            register_post_status($this->prefix_status($key), array_merge(array(
                'public'                    => false,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
            ), $values));
        }
    }

    /**
     * Register post type controller class
     *
     * @access public
     * @param array $classes
     * @return array
     */
    public function register_post_type_controller_class($classes)
    {

        $post_type = $this->get_data_store()->get_post_type();
        $classes[$post_type] = get_class($this);
        return $classes;
    }





}
