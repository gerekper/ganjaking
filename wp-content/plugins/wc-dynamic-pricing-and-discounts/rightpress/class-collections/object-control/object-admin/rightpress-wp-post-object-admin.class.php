<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-object-admin.class.php';
require_once 'interfaces/rightpress-wp-post-object-admin-interface.php';

/**
 * WordPress Post Object Admin Class
 *
 * Note: This class is used for custom post types and custom order types
 *
 * @class RightPress_WP_Post_Object_Admin
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Post_Object_Admin extends RightPress_WP_Object_Admin implements RightPress_WP_Post_Object_Admin_Interface
{

    protected $allowed_views        = null;
    protected $allowed_bulk_actions = null;
    protected $list_columns         = null;
    protected $post_actions         = null;
    protected $meta_boxes           = null;
    protected $meta_boxes_whitelist = null;

    // Cache objects when generating views
    protected $object_view_cache = array();

    // Flag to prevent infinite loops on save_post
    protected $saved_post = false;

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

        // Set up hooks
        $this->set_up_admin_object_list_page_hooks();
        $this->set_up_admin_object_edit_page_hooks();

        // Display admin post notices
        add_action('admin_notices', array($this, 'display_admin_post_notices'));
        add_filter('redirect_post_location', array($this, 'maybe_preserve_admin_post_notices'), 10, 2);

        // TODO: Change post update messages like WooCommerce does
    }


    /**
     * =================================================================================================================
     * ADMIN OBJECT LIST PAGE
     * =================================================================================================================
     */

    /**
     * Set up admin object list page hooks
     *
     * @access public
     * @return void
     */
    public function set_up_admin_object_list_page_hooks()
    {

        // Get post type
        $post_type = $this->get_post_type();

        // Maybe customize post list query
        add_action('parse_request', array($this, 'maybe_customize_post_list_query'));

        // Views
        add_filter('views_edit-' . $post_type, array($this, 'manage_list_views'));

        // Bulk actions
        add_filter('bulk_actions-edit-' . $post_type, array($this, 'manage_list_bulk_actions'));

        // Filters
        add_action('restrict_manage_posts', array($this, 'add_list_filters'));

        // Search
        add_filter('posts_join', array($this, 'expand_list_search_context_join'));
        add_filter('posts_where', array($this, 'expand_list_search_context_where'));
        add_filter('posts_groupby', array($this, 'expand_list_search_context_group_by'));

        // Columns
        add_filter('manage_' . $post_type . '_posts_columns', array($this, 'manage_list_columns'));

        // Column values
        add_action('manage_' . $post_type . '_posts_custom_column', array($this, 'print_column_value'), 10, 2);

        // Remove default post row actions
        RightPress_Help::add_late_filter('post_row_actions', array($this, 'remove_default_post_row_actions'));
    }

    /**
     * Maybe customize post list query
     *
     * @access public
     * @param object $query
     * @return void
     */
    public function maybe_customize_post_list_query(&$query)
    {

        global $typenow;

        // Check if request is for our post type in admin area
        if (is_admin() && $typenow === $this->get_post_type()) {

            // Customize post list query
            $this->customize_post_list_query($query);
        }
    }

    /**
     * Customize post list query
     *
     * @access public
     * @param object $query
     * @return void
     */
    public function customize_post_list_query(&$query)
    {

        global $wp_post_statuses;

        // Reference query vars
        $query_vars = &$query->query_vars;

        // If no status was set in request, add all custom statuses that should be displayed in admin all list
        if (empty($query_vars['post_status'])) {

            $statuses = array();

            // Iterate over all custom statuses
            foreach ($this->get_controller()->get_status_list() as $key => $values) {

                // Get prefixed status key
                $prefixed_key = $this->get_controller()->prefix_status($key);

                // Check if posts with this status should be displayed in admin all list
                if (isset($wp_post_statuses[$prefixed_key]) && $wp_post_statuses[$prefixed_key]->show_in_admin_all_list) {

                    // Add to array
                    $statuses[] = $prefixed_key;
                }
            }

            // Set custom statuses to query
            $query_vars['post_status'] = $statuses;
        }

        // Search by post id
        if (!empty($_GET[$this->get_post_type() . '_id'])) {
            $query_vars['post__in'] = (array) $_GET[$this->get_post_type() . '_id'];
        }
    }

    /**
     * Manage list views
     *
     * @access public
     * @param array $views
     * @return array
     */
    public function manage_list_views($views)
    {

        global $wp_post_statuses;

        $new_views = array();

        // Get list of allowed views
        $allowed_views = $this->get_allowed_views();

        // Add custom statuses that should be preserved
        foreach ($this->get_controller()->get_status_list() as $key => $values) {

            // Get prefixed status key
            $prefixed_key = $this->get_controller()->prefix_status($key);

            // Check if status should be displayed as view
            if (isset($wp_post_statuses[$prefixed_key]) && $wp_post_statuses[$prefixed_key]->show_in_admin_status_list) {

                // Add to allowed views
                $allowed_views[] = $prefixed_key;
            }
        }

        // Format new views array
        foreach ($views as $view_key => $view) {
            if (in_array($view_key, $allowed_views)) {
                $new_views[$view_key] = $view;
            }
        }

        return $new_views;
    }

    /**
     * Get allowed views
     *
     * @access public
     * @return array
     */
    public function get_allowed_views()
    {

        if ($this->allowed_views === null) {
            $this->allowed_views = $this->register_allowed_views();
        }

        return $this->allowed_views;
    }

    /**
     * Register allowed views
     *
     * @access public
     * @return array
     */
    public function register_allowed_views()
    {

        return array('all', 'trash');
    }

    /**
     * Manage list bulk actions
     *
     * @access public
     * @param array $actions
     * @return array
     */
    public function manage_list_bulk_actions($actions)
    {

        $new_actions = array();

        $allowed_bulk_actions = $this->get_allowed_bulk_actions();

        foreach ($actions as $action_key => $action) {
            if (in_array($action_key, $allowed_bulk_actions)) {
                $new_actions[$action_key] = $action;
            }
        }

        return $new_actions;
    }

    /**
     * Get allowed bulk actions
     *
     * @access public
     * @return array
     */
    public function get_allowed_bulk_actions()
    {

        if ($this->allowed_bulk_actions === null) {
            $this->allowed_bulk_actions = $this->register_allowed_bulk_actions();
        }

        return $this->allowed_bulk_actions;
    }

    /**
     * Register allowed bulk actions
     *
     * @access public
     * @return array
     */
    public function register_allowed_bulk_actions()
    {

        return array('trash', 'untrash', 'delete');
    }

    /**
     * Add filtering capabilities
     *
     * @access public
     * @return void
     */
    public function add_list_filters()
    {

        // To be overriden by child classes
    }

    /**
     * Expand list search context
     *
     * @access public
     * @param string $join
     * @return string
     */
    public function expand_list_search_context_join($join)
    {

        global $typenow;
        global $pagenow;
        global $wpdb;

        if ($pagenow === 'edit.php' && $typenow === $this->get_post_type() && isset($_GET['s']) && $_GET['s'] !== '') {
            $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' pm ON ' . $wpdb->posts . '.ID = pm.post_id ';
        }

        return $join;
    }

    /**
     * Expand list search context with more fields
     *
     * @access public
     * @param string $where
     * @return string
     */
    public function expand_list_search_context_where($where)
    {

        global $typenow;
        global $pagenow;
        global $wpdb;

        // Get plugin configuration
        $meta_fields    = $this->get_post_search_meta_fields();
        $contexts       = $this->get_post_search_contexts();

        // Search
        if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === $this->get_post_type() && !empty($_GET['s']) && (!empty($meta_fields) || !empty($contexts))) {

            $search_phrase = trim($_GET['s']);
            $exact_match = false;
            $context = null;

            // Exact match?
            if (preg_match('/^\".+\"$/', $search_phrase) || preg_match('/^\'.+\'$/', $search_phrase)) {
                $exact_match = true;
                $search_phrase = substr($search_phrase, 1, -1);
            }
            else if (preg_match('/^\\\\\".+\\\\\"$/', $search_phrase) || preg_match('/^\\\\\'.+\\\\\'$/', $search_phrase)) {
                $exact_match = true;
                $search_phrase = substr($search_phrase, 2, -2);
            }
            // Search with context?
            else {

                foreach ($contexts as $context_key => $context_value) {
                    if (preg_match('/^' . $context_key . '\:/i', $search_phrase)) {
                        $context = $context_value;
                        $search_phrase = trim(preg_replace('/^' . $context_key . '\:/i', '', $search_phrase));
                        break;
                    }
                }
            }

            // Search by ID
            if ($context === 'ID') {

                $replacement = $wpdb->prepare(
                    '(' . $wpdb->posts . '.ID LIKE %s)',
                    $search_phrase
                );
            }
            // Search within other context
            else if ($context) {

                $replacement = $wpdb->prepare(
                    '(pm.meta_key LIKE %s) AND (pm.meta_value LIKE %s)',
                    $context,
                    $search_phrase
                );
            }
            // Regular search
            else {

                $whitelist = 'pm.meta_key IN (\'' . join('\', \'', $meta_fields) . '\')';

                // Exact match?
                if ($exact_match) {

                    $replacement = $wpdb->prepare(
                        '(' . $wpdb->posts . '.ID LIKE %s) OR (pm.meta_value LIKE %s)',
                        $search_phrase,
                        $search_phrase
                    );

                    $replacement = '(' . $whitelist . ' AND ' . $replacement . ')';
                }
                // Regular match
                else {
                    $replacement = '(' . $whitelist . ' AND ((' . $wpdb->posts . '.ID LIKE $1) OR (pm.meta_value LIKE $1)))';
                }
            }

            $where = preg_replace('/\(\s*' . $wpdb->posts . '.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/', $replacement, $where);
        }

        return $where;
    }

    /**
     * Expand list search context with more fields - group results by id
     *
     * @access public
     * @param string $groupby
     * @return string
     */
    public function expand_list_search_context_group_by($groupby)
    {

        global $typenow;
        global $pagenow;
        global $wpdb;

        if ($pagenow === 'edit.php' && $typenow === $this->get_post_type() && isset($_GET['s']) && $_GET['s'] !== '') {
            $groupby = $wpdb->posts . '.ID';
        }

        return $groupby;
    }

    /**
     * Get post search meta fields
     *
     * Note: Plugins can override this method to provide search by meta capability
     *
     * @access public
     * @return array
     */
    public function get_post_search_meta_fields()
    {

        return array();
    }

    /**
     * Get post search contexts
     *
     * Note: Plugins can override this method to provide search by context capability
     *
     * @access public
     * @return array
     */
    public function get_post_search_contexts()
    {

        return array();
    }

    /**
     * Manage list columns
     *
     * @access public
     * @param array $columns
     * @return array
     */
    public function manage_list_columns($columns)
    {

        global $typenow;

        $new_columns = array();

        // Leave only allowed default columns
        foreach ($columns as $key => $label) {

            $allowed_keys = array();

            if ($this->get_controller()->is_editable()) {
                $allowed_keys[] = 'cb';
            }

            if (in_array($key, $allowed_keys)) {
                $new_columns[$key] = $label;
            }
        }

        // Add custom columns
        foreach ($this->get_list_columns() as $key => $label) {
            $new_columns[$key] = $label;
        }

        return $new_columns;
    }

    /**
     * Get list columns
     *
     * @access public
     * @return array
     */
    public function get_list_columns()
    {

        if ($this->list_columns === null) {
            $this->list_columns = $this->register_list_columns();
        }

        return $this->list_columns;
    }

    /**
     * Register list columns
     *
     * @access public
     * @return array
     */
    public function register_list_columns()
    {

        return array();
    }

    /**
     * Print column value
     *
     * @access public
     * @param array $column
     * @param int $post_id
     * @return void
     */
    public function print_column_value($column, $post_id)
    {

        // To be overriden by child classes
    }

    /**
     * Remove default post row actions
     *
     * @access public
     * @param array $actions
     * @return array
     */
    public function remove_default_post_row_actions($actions)
    {

        global $post;

        if (RightPress_Help::post_type_is($post, $this->get_post_type())) {
            $actions = array();
        }

        return $actions;
    }


    /**
     * =================================================================================================================
     * ADMIN OBJECT EDIT PAGE
     * =================================================================================================================
     */

    /**
     * Set up admin object edit page hooks
     *
     * @access public
     * @return void
     */
    public function set_up_admin_object_edit_page_hooks()
    {

        // Add enctype attribute to form tag
        add_action('post_edit_form_tag', array($this, 'add_enctype_to_post_edit_form'));

        // Add meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'), 10, 2);

        // Remove irrelevant meta boxes
        RightPress_Help::add_late_action('add_meta_boxes', array($this, 'remove_irrelevant_meta_boxes'), 2, -1);

        // Maybe save admin submitted data
        add_action('save_post', array($this, 'maybe_save_admin_submitted_data'), 9, 2);
    }

    /**
     * Get post actions
     *
     * @access public
     * @param object $object
     * @return array
     */
    public function get_post_actions($object = null)
    {

        if ($this->post_actions === null) {
            $this->post_actions = $this->register_post_actions($object);
        }

        return $this->post_actions;
    }

    /**
     * Register post actions
     *
     * @access public
     * @param object $object
     * @return array
     */
    public function register_post_actions($object = null)
    {

        return array();
    }

    /**
     * Add enctype attribute to post edit form
     *
     * @access public
     * @param object $post
     * @return void
     */
    public function add_enctype_to_post_edit_form($post)
    {

        if (RightPress_Help::post_type_is($post, $this->get_post_type())) {
            echo ' enctype="multipart/form-data" ';
        }
    }

    /**
     * Add meta boxes
     *
     * @access public
     * @param string $post_type
     * @param object $post
     * @return void
     */
    public function add_meta_boxes($post_type, $post)
    {

        if ($post_type === $this->get_post_type()) {

            foreach ($this->get_meta_boxes() as $key => $args) {

                add_meta_box(
                    $this->format_meta_box_key($key),
                    $args['title'],
                    array($this, ('print_meta_box_' . $key)),
                    $post_type,
                    $args['context'],
                    $args['priority']
                );
            }
        }
    }

    /**
     * Format meta box key
     *
     * @access public
     * @param string $key
     * @return string
     */
    public function format_meta_box_key($key)
    {

        return str_replace('_', '-', $this->get_post_type() . '-' . $key);
    }

    /**
     * Get meta boxes
     *
     * @access public
     * @return array
     */
    public function get_meta_boxes()
    {

        if ($this->meta_boxes === null) {
            $this->meta_boxes = $this->register_meta_boxes();
        }

        return $this->meta_boxes;
    }

    /**
     * Register meta boxes
     *
     * @access public
     * @return array
     */
    public function register_meta_boxes()
    {

        return array();
    }

    /**
     * Remove irrelevant meta boxes
     *
     * @access public
     * @param string $post_type
     * @param object $post
     * @return void
     */
    public function remove_irrelevant_meta_boxes($post_type, $post)
    {

        global $wp_meta_boxes;

        // Check post type
        if ($post_type === $this->get_post_type()) {

            // Get meta boxes for current view
            $screen = get_current_screen();
            $meta_boxes = isset($wp_meta_boxes[$screen->id]) ? $wp_meta_boxes[$screen->id] : array();

            // Get meta boxes whitelist
            $whitelist = $this->get_meta_boxes_whitelist();

            // Iterate over meta boxes
            foreach ($meta_boxes as $context => $by_context) {
                foreach ($by_context as $subcontext => $by_subcontext) {
                    foreach ($by_subcontext as $meta_box_id => $meta_box) {
                        if (!in_array($meta_box_id, $whitelist, true)) {
                            remove_meta_box($meta_box_id, $post_type, $context);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get meta boxes whitelist
     *
     * @access public
     * @return array
     */
    public function get_meta_boxes_whitelist()
    {

        if ($this->meta_boxes_whitelist === null) {

            // Register meta boxes whitelist
            $whitelist = $this->register_meta_boxes_whitelist();

            // Add own meta boxes to whitelist
            foreach ($this->get_meta_boxes() as $key => $args) {
                $whitelist[] = $this->format_meta_box_key($key);
            }

            // Set meta boxes whitelist
            $this->meta_boxes_whitelist = $whitelist;
        }

        return $this->meta_boxes_whitelist;
    }

    /**
     * Register meta boxes whitelist
     *
     * @access public
     * @return array
     */
    public function register_meta_boxes_whitelist()
    {

        return array();
    }

    /**
     * Maybe save admin submitted data
     *
     * @access public
     * @param int $post_id
     * @param object $post
     * @param array $posted
     * @return void
     */
    public function maybe_save_admin_submitted_data($post_id, $post, $posted = array())
    {

        // Check if required properties were passed in
        if (empty($post_id) || !is_a($post, 'WP_Post')) {
            return;
        }

        // Get post type
        $post_type = $this->get_post_type();

        // Check post type
        if ($post->post_type !== $post_type) {
            return;
        }

        // Already saved
        if ($this->saved_post) {
            return;
        }

        // Make sure it is not a draft save action
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || is_int(wp_is_post_autosave($post)) || is_int(wp_is_post_revision($post))) {
            return;
        }

        // Make sure the correct post ID was passed from form
        if (empty($_POST['post_ID']) || absint($_POST['post_ID']) !== $post_id) {
            return;
        }

        // Get posted values
        $posted = !empty($posted) ? $posted : $_POST;

        // Validate nonce
        if (empty($_POST['rightpress_post_nonce']) || !wp_verify_nonce(wp_unslash($_POST['rightpress_post_nonce']), 'rightpress_save_admin_submitted_data')) {
            return;
        }

        // Make sure user has permission to save data
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Check if object is editable
        if (!$this->get_controller()->is_editable()) {
            return;
        }

        // Set flag
        $this->saved_post = true;

        // Save admin submitted data
        $this->save_admin_submitted_data($post_id, $posted);
    }

    /**
     * Save admin submitted data
     *
     * @access public
     * @param int $object_id
     * @param array $posted
     * @return void
     */
    public function save_admin_submitted_data($object_id, $posted)
    {

        // Get post type
        $post_type = $this->get_post_type();

        // Get method name
        if (!empty($posted[$post_type . '_button']) && $posted[$post_type . '_button'] === 'actions' && !empty($posted[$post_type . '_actions'])) {
            $method = 'handle_action_' . $posted[$post_type . '_actions'];
        }
        else {
            $method = 'handle_action_save';
        }

        // Get data for this specific post type
        $data = isset($posted[$post_type]) ? $posted[$post_type] : array();

        try {

            // Handle action
            $this->$method($object_id, $data, $posted);
        }
        catch (RightPress_Exception $e) {

            // Display error
            add_settings_error(
                $post_type,
                'post_updated',
                $e->getMessage()
            );
        }
    }


    /**
     * =================================================================================================================
     * OTHER METHODS
     * =================================================================================================================
     */

    /**
     * Get object for view
     *
     * @access protected
     * @param int $object_id
     * @return object
     */
    protected function get_object_for_view($object_id)
    {

        // Instantiate and cache new object
        if (!isset($this->object_view_cache[$object_id])) {
            $this->object_view_cache[$object_id] = $this->get_controller()->get_object($object_id);
        }

        // Return cached object
        return $this->object_view_cache[$object_id];
    }

    /**
     * Display admin post notices
     *
     * @access public
     * @return void
     */
    public function display_admin_post_notices()
    {

        global $typenow;

        // Reference object post type
        $post_type = $this->get_post_type();

        // Check if request is for our post type in admin area
        if (is_admin() && $typenow === $post_type) {

            // Get notices
            $notices = get_settings_errors($post_type);

            // No notices are set
            if (empty($notices)) {

                // Format transient name
                $transient_name = $post_type . '_admin_notices';

                // Attempt to load notices from transient
                if ($notices = get_transient($transient_name)) {

                    // Iterate over notices
                    foreach ($notices as $settings_error) {

                        // Add notice
                        add_settings_error(
                            $post_type,
                            $settings_error['code'],
                            $settings_error['message'],
                            $settings_error['type']
                        );
                    }

                    // Delete transient
                    delete_transient($transient_name);
                }
            }

            // Print admin post notices
            settings_errors($post_type);
        }
    }

    /**
     * Maybe preserve admin post notices
     */
    public function maybe_preserve_admin_post_notices($location, $post_id)
    {

        // Reference object post type
        $post_type = $this->get_post_type();

        // Check post type
        if (get_post_type($post_id) === $post_type) {

            // Get notices
            $notices = get_settings_errors($post_type);

            // Check if any notices are set
            if (!empty($notices)) {

                // Save to transient
                set_transient($post_type . '_admin_notices', $notices, 5);
            }
        }

        return $location;
    }





}
