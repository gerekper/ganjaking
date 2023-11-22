<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DiscoverTokens extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Get Script Depends
     *
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-discover-tokens', 'font-awesome'];
    }
    /**
     * Get Style Depends
     *
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-icon'];
    }
    /**
     * Post Meta Hidden
     *
     * @var array<string>
     */
    protected $hidden_post_meta = ['dce_widgets', 'dyncontel_elementor_templates', 'to_ping', 'pinged', 'filter', 'rich_editing', 'syntax_highlighting', 'comment_shortcuts', 'use_ssl', 'show_admin_bar_front', 'wp_capabilities', 'dismissed_wp_pointers', 'show_welcome_panel', 'wp_dashboard_quick_press_last_post_id', 'wp_elementor_connect_common_data', 'wp_user-settings', 'search-filter-show-welcome-notice', 'elementor_introduction', 'elementor_preferences', 'elementor_admin_notices', 'session_tokens', 'submitted_on_id', 'submitted_by_id'];
    /**
     * Register Controls
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_tokens', ['label' => $this->get_title()]);
        $this->add_control('type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['acf' => 'acf', 'author' => 'author', 'date' => 'date', 'expr' => 'expr', 'jet' => 'jet', 'metabox' => 'metabox', 'option' => 'option', 'post' => 'post', 'product' => 'product', 'query' => 'query', 'system' => 'system', 'term' => 'term', 'user' => 'user', 'wp_query' => 'wp_query'], 'default' => 'post']);
        $this->add_control('notice_resource_intensive', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('This token can be resource intensive as it queries the database and the speed depends on how many elements you have on your site', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['type' => 'query']]);
        $this->end_controls_section();
    }
    /**
     * Safe Render
     *
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        // Not visible in frontend if not administrator
        if (!current_user_can('administrator') || empty($settings)) {
            return;
        }
        // Check if tokens are active
        if (get_option('dce_tokens_status') === 'disable') {
            Helper::notice('', __('Tokens are not active. You can activate it from WP Dashboard > Dynamic.ooo > Settings > Tokens', 'dynamic-content-for-elementor'));
            return;
        }
        Helper::notice('', __('This widget is only visible to administrators in order to better understand the tokens syntax, so you can put it on any public page without problem.', 'dynamic-content-for-elementor'));
        $type = sanitize_text_field($settings['type']);
        $this->show_list($type);
    }
    /**
     * Show the list for a single type
     *
     * @param string $type
     * @return void
     */
    protected function show_list(string $type)
    {
        echo '<h4>' . $type . '</h4>';
        $realtype = $type === 'jet' || $type === 'metabox' ? 'post' : $type;
        $tokens = Tokens::get_tokens_list();
        // Check if this type is active
        $active_tokens = get_option('dce_active_tokens', \array_keys($tokens));
        if (!\in_array($realtype, get_option('dce_active_tokens', $active_tokens), \true)) {
            _e('This token type is not active. If you want to use it you can activate it from WP Dashboard > Dynamic.ooo > Settings > Tokens > Active Tokens', 'dynamic-content-for-elementor');
            return;
        }
        if (!$this->check_plugin_dependencies($type)) {
            return;
        }
        $demo = $this->get_demo();
        $this->render_description($type);
        $this->start_table();
        // Get demo fields
        $fields = $demo[$type]['fields'] ?? [];
        $function_to_populate = '';
        // Generate fields with functions
        if (!empty($demo[$type]['functions'])) {
            foreach ($demo[$type]['functions'] as $function) {
                if (empty($function['filters'])) {
                    if (!empty($function['parameters'])) {
                        $function_to_populate = \call_user_func($function['name'], \call_user_func($function['parameters']));
                    } else {
                        $function_to_populate = \call_user_func($function['name']);
                    }
                    if (empty($function_to_populate)) {
                        continue;
                    }
                    if (!empty($function['prefix'])) {
                        $function_to_populate = $this->add_prefix($function['prefix'], $function_to_populate);
                    }
                } else {
                    foreach ($function['filters'] as $filter) {
                        if (!empty($function['parameters'])) {
                            $function_to_populate = \call_user_func($function['name'], \call_user_func($function['parameters']));
                        } else {
                            $function_to_populate = \call_user_func($function['name']);
                        }
                        if (empty($function_to_populate)) {
                            continue;
                        }
                        if (!empty($function['prefix'])) {
                            $function_to_populate = $this->add_prefix($function['prefix'], $function_to_populate);
                        }
                        $function_to_populate = $this->add_filter($filter, $function_to_populate);
                        $fields = \array_merge($fields, \array_keys($function_to_populate));
                    }
                }
                $fields = \array_merge($fields, \array_keys($function_to_populate));
            }
        }
        // Generate examples with |ID
        if (!empty($demo[$type]['generate_id'])) {
            foreach ($demo[$type]['generate_id'] as $generate_id) {
                if (!empty($generate_id['parameters'])) {
                    $function_to_populate = \call_user_func($generate_id['name'], \call_user_func($generate_id['parameters']));
                } else {
                    $function_to_populate = \call_user_func($generate_id['name']);
                }
                if (empty($function_to_populate)) {
                    continue;
                }
                if (!empty($generate_id['prefix'])) {
                    $function_to_populate = $this->add_prefix($generate_id['prefix'], $function_to_populate);
                }
            }
            $fields = \array_merge($fields, \array_keys($function_to_populate));
        }
        // Show all fields
        foreach ($fields as $field) {
            if (!isset($demo[$type]['hidden']) || $this->is_not_hidden($field, $demo[$type]['hidden'])) {
                if (!empty($demo[$type]['prefix'])) {
                    $prefix = $demo[$type]['prefix'];
                    $token = '[' . $type . ':' . $prefix . ':' . $field . ']';
                } else {
                    $token = '[' . $type . ':' . $field . ']';
                }
                $this->show_token($type, $field, $token);
            }
        }
        $this->end_table();
    }
    /**
     * Start the table
     *
     * @return void
     */
    protected function start_table()
    {
        echo '<table>';
        echo '<tr>';
        echo '<th>' . __('What', 'dynamic-content-for-elementor') . '</th>';
        echo '<th>' . __('Token', 'dynamic-content-for-elementor') . '</th>';
        echo '<th>' . __('Result', 'dynamic-content-for-elementor') . '</th>';
        echo '</tr>';
    }
    /**
     * End the table
     *
     * @return void
     */
    protected function end_table()
    {
        echo '</table>';
    }
    /**
     * Check if a field is set as hidden in the demo
     *
     * @param string $field
     * @param array<string> $hidden
     * @return boolean
     */
    protected function is_not_hidden(string $field, array $hidden)
    {
        $field = $this->remove_filters($field);
        $underscore_field_accepted = ['_thumbnail_id'];
        if ('_' === $field[0] && !\in_array($field, $underscore_field_accepted, \true)) {
            return \false;
        }
        if (\in_array($field, $hidden, \true)) {
            return \false;
        }
        return \true;
    }
    /**
     * Remove filters from a token
     *
     * @param string $field
     * @return string
     */
    protected function remove_filters(string $field)
    {
        return \explode('|', $field)[0];
    }
    /**
     * Show the token in the table
     *
     * @param string $type
     * @param string $field
     * @param string $token
     * @return void
     */
    protected function show_token($type = '', $field = '', $token = '')
    {
        if (empty($type) || empty($field) || empty($token)) {
            return;
        }
        echo '<tr>';
        echo '<td>' . $this->remove_filters($field) . '</td>';
        $token = $this->maybe_sanitize($type, $field, $token);
        echo '<td><code>' . $token . '</code>';
        $this->render_copy_button($token);
        echo '</td>';
        echo '<td>' . Helper::get_dynamic_value($token) . '</td>';
        echo '</tr>';
    }
    /**
     * Render the copy button
     *
     * @param string $token
     * @return void
     */
    protected function render_copy_button(string $token)
    {
        $this->set_render_attribute('copy', 'data-clipboard-text', $token);
        $this->set_render_attribute('copy', 'class', 'copy');
        $this->set_render_attribute('copy', 'style', 'cursor: pointer');
        ?>
		<span <?php 
        echo $this->get_render_attribute_string('copy');
        ?>>
		<?php 
        echo '<i class="icon icon-dce-copy" aria-hidden="true"></i>';
    }
    /**
     * Add a prefix to a token
     *
     * @param string $prefix
     * @param array<string> $fields
     * @return array<string>
     */
    protected function add_prefix(string $prefix, array $fields)
    {
        $fields_with_prefix = [];
        foreach ($fields as $key => $value) {
            if (\str_contains($prefix, '|')) {
                $fields_with_prefix[$prefix . $key] = '';
            } else {
                $fields_with_prefix[$prefix . ':' . $key] = '';
            }
        }
        return $fields_with_prefix;
    }
    /**
     * Add a filter to a token
     *
     * @param string $filter
     * @param array<string> $fields
     * @return array<string>
     */
    protected function add_filter(string $filter, array $fields)
    {
        if (empty($filter)) {
            return $fields;
        }
        $fields_with_filter = [];
        foreach ($fields as $key => $value) {
            $fields_with_filter[$key . '|' . $filter] = '';
        }
        return $fields_with_filter;
    }
    /**
     * Check plugin dependencies
     *
     * @param string $type
     * @return boolean
     */
    protected function check_plugin_dependencies(string $type)
    {
        $plugin_depends = $this->get_demo_value($type, 'plugin_depends');
        if (!empty($plugin_depends) && !Helper::check_plugin_dependencies(\false, $plugin_depends)) {
            $plugins = Helper::to_string(Helper::check_plugin_dependencies(\true, $plugin_depends));
            $message = \sprintf(__('You need %1$s plugin to use this token', 'dynamic-content-for-elementor'), $plugins);
            Helper::notice('', $message);
            return \false;
        }
        return \true;
    }
    /**
     * Sanitize a token if required
     *
     * @param string $type
     * @param string $field
     * @param string $token
     * @return string
     */
    protected function maybe_sanitize(string $type, string $field, string $token)
    {
        $sanitizations = $this->get_demo_value($type, 'sanitizations');
        $sanitize_all = $this->get_demo_value($type, 'sanitize_all');
        if (!empty($sanitizations)) {
            foreach ($sanitizations as $sanitization_field => $sanitization_function) {
                if (\str_starts_with($field, $sanitization_field) && !empty($sanitization_function)) {
                    return $this->add_sanitization($token, $sanitization_function);
                }
            }
        }
        if (empty($sanitize_all)) {
            return $token;
        }
        if (!empty($sanitize_all['exclude_filters'])) {
            // Don't sanitize some filters
            foreach ($sanitize_all['exclude_filters'] as $key => $filter) {
                if (\str_contains($token, '|' . $filter)) {
                    return $token;
                }
            }
        }
        return $this->add_sanitization($token, $sanitize_all['function'] ?? '');
    }
    /**
     * Add a sanitization function to the token
     *
     * @param string $token
     * @param string $function
     * @return string
     */
    protected function add_sanitization(string $token, string $function)
    {
        return \str_replace(']', '|' . $function . ']', $token);
    }
    /**
     * Show the type description
     *
     * @param string $type
     * @return void
     */
    protected function render_description(string $type)
    {
        if (empty($type)) {
            return;
        }
        echo '<p>' . $this->get_demo_value($type, 'description') . '</p>';
    }
    /**
     * Get a value from demo
     *
     * @param string $type
     * @param string $value
     * @return mixed
     */
    protected function get_demo_value(string $type, string $value)
    {
        if (empty($type) || empty($value)) {
            return;
        }
        $demo = $this->get_demo();
        if (isset($demo[$type][$value])) {
            return $demo[$type][$value];
        }
    }
    /**
     * Retrieve all Meta Box Fields for the current post ID
     *
     * @return array<string,mixed>
     */
    protected static function get_all_metabox_fields()
    {
        if (get_the_ID() === \false) {
            // phpstan
            return [];
        }
        return rwmb_get_object_fields(get_the_ID());
    }
    /**
     * Retrieve all WP_Query vars
     *
     * @return array<string,mixed>
     */
    protected static function get_all_wp_query_vars()
    {
        global $wp_query;
        return (array) $wp_query->query_vars;
    }
    /**
     * Retrieve all post types set as public
     *
     * @return array<string,mixed>
     */
    protected static function get_public_post_types()
    {
        return get_post_types(['public' => \true, 'publicly_queryable' => \true, 'exclude_from_search' => \false]);
    }
    /**
     * Get superglobal GET
     *
     * @return array<mixed>
     */
    protected static function get_all_system_get()
    {
        return $_GET;
        // phpcs:ignore WordPress.Security.NonceVerification
    }
    /**
     * Get superglobal POST
     *
     * @return array<mixed>
     */
    protected static function get_all_system_post()
    {
        return $_POST;
    }
    /**
     * Get superglobal COOKIE
     *
     * @return array<mixed>
     */
    protected static function get_all_system_cookie()
    {
        return $_COOKIE;
    }
    /**
     * Get superglobal SESSION
     *
     * @return array<mixed>
     */
    protected static function get_all_system_session()
    {
        return $_SESSION ?? [];
    }
    /**
     * Get superglobal SERVER
     *
     * @return array<mixed>
     */
    protected static function get_all_system_server()
    {
        return $_SERVER;
    }
    /**
     * Generate posts id useful for demo on token post
     *
     * @return array<string>|false
     */
    protected static function generate_post_id()
    {
        $sample = [];
        $args = ['posts_per_page' => 5];
        $query = new \WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $sample[get_the_ID()] = '';
            }
        }
        wp_reset_postdata();
        return $sample;
    }
    /**
     * Generate products id useful for demo on token product
     *
     * @return array<string>|false
     */
    protected static function generate_product_id()
    {
        $sample = [];
        $args = ['post_type' => 'product', 'posts_per_page' => 5];
        $query = new \WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $sample[get_the_ID()] = '';
            }
        }
        wp_reset_postdata();
        return $sample;
    }
    /**
     * Generate users id useful for demo on token user
     *
     * @return array<string>|false
     */
    protected static function generate_user_id()
    {
        $sample = [];
        $args = ['number' => 5];
        $query = new \WP_User_Query($args);
        if ($query->get_results()) {
            foreach ($query->get_results() as $user) {
                $sample[$user->ID] = '';
            }
        }
        return $sample;
    }
    /**
     * Generate terms id useful for demo on token term
     *
     * @return array<string>|false
     */
    protected static function generate_term_id()
    {
        $sample = [];
        $args = ['number' => 5];
        $query = new \WP_Term_Query($args);
        $terms = $query->get_terms();
        if (\is_array($terms)) {
            foreach ($terms as $term) {
                if ($term instanceof \WP_Term) {
                    $sample[$term->term_id] = '';
                }
            }
        }
        return $sample;
    }
    /**
     * Retrieve all author fields
     *
     * @return mixed
     */
    protected static function get_author_meta()
    {
        return get_user_meta(\intval(get_the_author_meta('ID')));
    }
    protected static function get_all_options()
    {
        $all_options = \array_combine(\array_keys(wp_load_alloptions()), \array_keys(wp_load_alloptions()));
        $all_options = \array_filter($all_options, function ($option) {
            if ((Tokens::OPTIONS_WHITELIST[$option] ?? \false) || Helper::is_jetengine_active() && jet_engine()->options_pages->registered_pages[$option]) {
                return \true;
            }
            return \false;
        });
        // ACF Fields Options
        if (Helper::is_acfpro_active()) {
            $acf_options = get_fields('option');
            foreach ($acf_options as $key => $value) {
                $all_options['options_' . $key] = $value;
            }
        }
        // Meta Box Settings Pages
        if (Helper::is_metabox_active()) {
            $all_meta_box_settings = rwmb_get_registry('field')->get_by_object_type('setting');
            foreach ($all_meta_box_settings as $key => $value) {
                $all_options[$key] = \array_values(get_option($key))[0] ?? '';
            }
        }
        return $all_options;
    }
    /**
     * Demo content for all types
     *
     * @return array<mixed>
     */
    protected function get_demo()
    {
        return [
            // *******************************************************************************************
            // ACF
            // *******************************************************************************************
            'acf' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving Advanced Custom Fields', 'dynamic-content-for-elementor'),
                // Plugin dependencies
                'plugin_depends' => ['acf'],
                // Functions to populate fields
                'functions' => [['name' => 'get_fields', 'parameters' => 'get_the_id']],
            ],
            // *******************************************************************************************
            // Author
            // *******************************************************************************************
            'author' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving the author fields of the current page. An author is the user who created the current post or page', 'dynamic-content-for-elementor'),
                // Static fields to show
                'fields' => ['email', 'nicename', 'roles', 'display_name', 'ID', 'ID:count_user_posts'],
                // Fields to hide when executed 'functions'
                'hidden' => ['password'],
                // Functions to populate fields
                'functions' => [['name' => 'self::get_author_meta']],
            ],
            // *******************************************************************************************
            // JetEngine
            // *******************************************************************************************
            'jet' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving JetEngine Fields', 'dynamic-content-for-elementor'),
                // Plugin dependencies
                'plugin_depends' => ['jet-engine'],
                // Static fields to show
                'fields' => [],
                // Fields to hide when executed 'functions'
                'hidden' => $this->hidden_post_meta,
                // Functions to populate fields
                'functions' => [['name' => 'get_post_meta', 'parameters' => 'get_the_id']],
            ],
            // *******************************************************************************************
            // Meta Box
            // *******************************************************************************************
            'metabox' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving Meta Box Fields', 'dynamic-content-for-elementor'),
                // Plugin dependencies
                'plugin_depends' => ['meta-box'],
                // Static fields to show
                'fields' => [],
                // Fields to hide when executed 'functions'
                'hidden' => $this->hidden_post_meta,
                // Functions to populate fields
                'functions' => [['name' => 'self::get_all_metabox_fields']],
            ],
            // *******************************************************************************************
            // Post
            // *******************************************************************************************
            'post' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving the fields of the current page', 'dynamic-content-for-elementor'),
                // Static fields to show
                'fields' => ['title', 'ID', '_thumbnail_id', 'date', 'permalink', 'type', 'author'],
                // Fields to hide when executed 'functions'
                'hidden' => $this->hidden_post_meta,
                // Functions to populate fields
                'functions' => [['name' => 'get_post_meta', 'parameters' => 'get_the_id']],
                // Sanitizations
                'sanitizations' => ['title' => 'esc_html'],
                // Functions to generate ID
                'generate_id' => [['name' => 'self::generate_post_id', 'prefix' => 'title|']],
            ],
            // *******************************************************************************************
            // Expression
            // *******************************************************************************************
            'expr' => [
                // Description to show before the table
                'description' => __('This token is useful to make expressions. It supports nested Tokens', 'dynamic-content-for-elementor'),
                // Static fields to show
                'fields' => ['1+1', '5*2', '10/2', '100-50', '5+(8*2)/3', '5+(8*2)/3|ceil', '5+(8*2)/3|round', '[post:my_number]*2', '[post:my_number]*[acf:my_number_2|123]'],
            ],
            // *******************************************************************************************
            // User
            // *******************************************************************************************
            'user' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving the fields of the current user', 'dynamic-content-for-elementor'),
                // Static fields to show
                'fields' => ['ID', 'email', 'nicename', 'roles', 'ID|get_avatar_url', 'ID|get_avatar'],
                // Fields to hide when executed 'functions'
                'hidden' => ['password'],
                // Functions to populate fields
                'functions' => [['name' => 'get_user_meta', 'parameters' => 'get_current_user_id']],
                // Functions to generate ID
                'generate_id' => [['name' => 'self::generate_user_id', 'prefix' => 'nicename|']],
            ],
            // *******************************************************************************************
            // Product
            // *******************************************************************************************
            'product' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving the fields of the current WooCommerce product', 'dynamic-content-for-elementor'),
                // Plugin dependencies
                'plugin_depends' => ['woocommerce'],
                // Static fields to show
                'fields' => ['title', '_thumbnail_id', 'date', 'permalink', 'type', 'author'],
                // Fields to hide when executed 'functions'
                'hidden' => [$this->hidden_post_meta],
                // Functions to populate fields
                'functions' => [['name' => 'get_post_meta', 'parameters' => 'get_the_id']],
                // Sanitizations
                'sanitizations' => ['title' => 'esc_html'],
                // Functions to generate ID
                'generate_id' => [['name' => 'self::generate_product_id', 'prefix' => 'title|']],
            ],
            // *******************************************************************************************
            // Option
            // *******************************************************************************************
            'option' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving options. Options are pieces of data that WordPress uses to store various preferences and configuration settings. You can also retrieve Advanced Custom Fields and JetEngine Fields created on options pages and Meta Box Fields created on Setting Pages', 'dynamic-content-for-elementor'),
                // Static fields to show
                'fields' => [],
                // Functions to populate fields
                'functions' => [['name' => 'self::get_all_options']],
            ],
            // *******************************************************************************************
            // Query
            // *******************************************************************************************
            'query' => [
                // Description to show before the table
                'description' => __('This token is useful for to generate a query on your posts, pages, users, and taxonomies', 'dynamic-content-for-elementor'),
                // Static fields to show
                'fields' => ['user|count', 'user', 'user|options'],
                // Fields to hide when executed 'functions'
                'hidden' => ['term:nav_menu', 'term:link_category', 'term:post_format', 'term:wp_theme', 'term:wp_template_part_area', 'term:elementor_library_type', 'term:elementor_library_category', 'term:translation_priority', 'term:elementor_font_type'],
                // Function to sanitize all fields
                'sanitize_all' => ['function' => 'wp_kses_post_deep', 'exclude_filters' => ['count']],
                // Functions to populate fields
                'functions' => [['name' => 'self::get_public_post_types', 'filters' => ['count', '', 'options']], ['name' => 'get_taxonomies', 'prefix' => 'term', 'filters' => ['count', '', 'options']]],
            ],
            // *******************************************************************************************
            // System
            // *******************************************************************************************
            'system' => [
                // Description to show before the table
                'description' => __('This token is useful to fetch the current request parameters and data', 'dynamic-content-for-elementor'),
                // Fields to hide when executed 'functions'
                'hidden' => ['post:actions'],
                // Function to sanitize all fields
                'sanitize_all' => ['function' => 'wp_kses_post_deep'],
                // Functions to populate fields
                'functions' => [['name' => 'self::get_all_system_get', 'prefix' => 'get'], ['name' => 'self::get_all_system_post', 'prefix' => 'post'], ['name' => 'self::get_all_system_cookie', 'prefix' => 'cookie'], ['name' => 'self::get_all_system_server', 'prefix' => 'server'], ['name' => 'self::get_all_system_session', 'prefix' => 'session']],
            ],
            // *******************************************************************************************
            // Date
            // *******************************************************************************************
            'date' => [
                // Description to show before the table
                'description' => __('This token is useful when you need to work with date', 'dynamic-content-for-elementor'),
                // Static fields to show
                'fields' => ['now|l j F Y', 'now|d/m/Y', 'now|d/m/Y H:i', 'now|d/m/Y H:i:s', '+10 seconds|d/m/Y H:i:s', '+10 minutes|d/m/Y H:i', '+1 hour|d/m/Y H:i', '+1 day|d/m/Y', '+2 days|d/m/Y', '+1 week|d/m/Y', '+1 month|d/m/Y', '+1 year|d/m/Y'],
            ],
            // *******************************************************************************************
            // Term
            // *******************************************************************************************
            'term' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving the fields of the current term on a Term Archive or the terms on posts', 'dynamic-content-for-elementor'),
                // Static fields to show
                'fields' => ['name', 'name|first', 'term_id', 'term_id|first', 'slug', 'slug|first'],
                // Functions to generate ID
                'generate_id' => [['name' => 'self::generate_term_id', 'prefix' => 'name|']],
            ],
            // *******************************************************************************************
            // WP_Query
            // *******************************************************************************************
            'wp_query' => [
                // Description to show before the table
                'description' => __('This token is useful for retrieving the query vars of the current WP_Query', 'dynamic-content-for-elementor'),
                // Functions to populate fields
                'functions' => [['name' => 'self::get_all_wp_query_vars', 'prefix' => 'query_vars']],
            ],
        ];
    }
}
