<?php

namespace Happy_Addons\Elementor;

defined('ABSPATH') || die();


use Happy_Addons\Elementor\Conditions_Cache;

class Theme_Builder {
    public static $instance = null;

    protected $templates;
    protected $current_theme;
    protected $current_template;
    protected $current_location;

    private $cache;
    private $location_cache;

    const CPT = 'ha_library';
    const TEMPLATE_TYPE = ['header' => 'Header', 'footer' => 'Footer', 'single' => 'Single', 'archive' => 'Archive'];
    const TAB_BASE = "edit.php?post_type=ha_library";

    public $header_template;
    public $footer_template;
    public $singular_template;


    public function __construct() {
        add_action('wp', array($this, 'hooks'));
        $this->cache = new Conditions_Cache();

        add_filter('query_vars', [$this, 'add_query_vars_filter']);
        add_filter('views_edit-' . self::CPT, [$this, 'admin_print_tabs']);
        add_action('init', [$this, 'create_themebuilder_cpt'], 0);
        add_action('admin_menu', [$this, 'modify_menu'], 90);
        add_action('pre_get_posts', [$this, 'add_role_filter_to_posts_query']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'ha_template_element_scripts']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'edit_template_condition_modal'], 10, 2);

        add_filter('elementor/document/config', [$this, 'ha_template_document_title'], 10, 2);

        // Admin Actions
        add_action('admin_action_ha_library_new_post', [$this, 'admin_action_new_post']);

        add_action('current_screen', function () {
            $current_screen = get_current_screen();
            if (!$current_screen || !strstr($current_screen->post_type, 'ha_library')) {
                return;
            }
            add_action('in_admin_header', function () {
                $this->render_admin_top_bar();
            });
            // add_action('admin_head', [$this, 'add_new_template_template']);
            add_action('in_admin_footer', [$this, 'add_new_template_template'], 10, 2);
        });

        add_action('manage_' . self::CPT . '_posts_columns', [__CLASS__, 'admin_columns_headers']);
        add_action('manage_' . self::CPT . '_posts_custom_column', [$this, 'admin_columns_content'], 10, 2);

        //Override Single Post Template
        add_filter('template_include', [$this, 'ha_theme_builder_content'], 999);
        add_action('happyaddons_theme_builder_render', array($this, 'single_blog_content_elementor'), 999);

        // Register Ajax Handles
        // add_action( 'wp_ajax_ha_cond_template_type', [$this, 'ha_get_template_type'] );
    }

    public function add_query_vars_filter($vars) {
        $vars[] = "ha_library_type";
        return $vars;
    }

    // Register Custom Post Type Theme Builder
    public function create_themebuilder_cpt() {

        $labels = array(
            'name' => _x('Theme Builder', 'Post Type General Name', 'happy-elementor-addons'),
            'singular_name' => _x('Theme Builder', 'Post Type Singular Name', 'happy-elementor-addons'),
            'menu_name' => _x('Theme Builder', 'Admin Menu text', 'happy-elementor-addons'),
            'name_admin_bar' => _x('Theme Builder', 'Add New on Toolbar', 'happy-elementor-addons'),
            'archives' => __('Theme Builder Archives', 'happy-elementor-addons'),
            'attributes' => __('Theme Builder Attributes', 'happy-elementor-addons'),
            'parent_item_colon' => __('Parent Theme Builder:', 'happy-elementor-addons'),
            'all_items' => __('All Theme Builder', 'happy-elementor-addons'),
            'add_new_item' => __('Add New Theme Builder', 'happy-elementor-addons'),
            'add_new' => __('Add New', 'happy-elementor-addons'),
            'new_item' => __('New Theme Builder', 'happy-elementor-addons'),
            'edit_item' => __('Edit Theme Builder', 'happy-elementor-addons'),
            'update_item' => __('Update Theme Builder', 'happy-elementor-addons'),
            'view_item' => __('View Theme Builder', 'happy-elementor-addons'),
            'view_items' => __('View Theme Builder', 'happy-elementor-addons'),
            'search_items' => __('Search Theme Builder', 'happy-elementor-addons'),
            'not_found' => __('Not found', 'happy-elementor-addons'),
            'not_found_in_trash' => __('Not found in Trash', 'happy-elementor-addons'),
            'featured_image' => __('Featured Image', 'happy-elementor-addons'),
            'set_featured_image' => __('Set featured image', 'happy-elementor-addons'),
            'remove_featured_image' => __('Remove featured image', 'happy-elementor-addons'),
            'use_featured_image' => __('Use as featured image', 'happy-elementor-addons'),
            'insert_into_item' => __('Insert into Theme Builder', 'happy-elementor-addons'),
            'uploaded_to_this_item' => __('Uploaded to this Theme Builder', 'happy-elementor-addons'),
            'items_list' => __('Theme Builder list', 'happy-elementor-addons'),
            'items_list_navigation' => __('Theme Builder list navigation', 'happy-elementor-addons'),
            'filter_items_list' => __('Filter Theme Builder list', 'happy-elementor-addons'),
        );
        $args = array(
            'label' => __('Theme Builder', 'happy-elementor-addons'),
            'description' => __('', 'happy-elementor-addons'),
            'labels' => $labels,
            'supports' => array('title', 'elementor'),
            'taxonomies' => array(),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => '',
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'exclude_from_search' => true,
            'capability_type' => 'page',
        );
        register_post_type('ha_library', $args);
    }


    public function modify_menu() {
        add_submenu_page(
            Dashboard::PAGE_SLUG, // Parent slug
            'Theme Builder', // Page title
            'Theme Builder', // Menu title
            'manage_options', // Capability
            'edit.php?post_type=ha_library',  // Slug
            false // Function
        );
    }

    public function add_role_filter_to_posts_query($query) {
        /**
         * No use on front
         * pre get posts runs everywhere
         * even if you test $pagenow after, bail as soon as possible
         */
        if (!is_admin()) {
            return;
        }

        global $pagenow;

        /**
         * use $query parameter instead of global $post_type
         */
        if ('edit.php' === $pagenow && self::CPT === $query->query['post_type']) {

            if (isset($_GET['ha_library_type'])) {
                $meta_query = array(
                    array(
                        'key' => '_ha_library_type',
                        'value' => sanitize_text_field( $_GET['ha_library_type']),
                        'compare' => '=='
                    )
                );
                $query->set('meta_query', $meta_query);
                $query->set('meta_key', '_ha_library_type');
            }
        }
    }

    private function render_admin_top_bar() {
?>
        <div id="ha-admin-top-bar-root">
            <div class="ha-admin-top-bar">
                <div class="ha-admin-top-bar__main-area">
                    <div class="ha-admin-top-bar__heading">
                        <div class="ha-admin-top-bar__heading-logo">
                            <svg version="1.1" x="0px" y="0px" viewBox="0 0 110 118" enable-background="new 0 0 110 118" xml:space="preserve">
                                <g>
                                    <g>
                                        <path fill="#ffffff" d="M101.1,27.8c1,0,1.9-0.2,2.9-0.2c1.9-0.2,3.1-1.9,2.9-3.6c-0.2-1.9-1.9-3.2-3.5-2.9
			c-12.8,1.5-24.9-6.3-28.8-18.7c-0.6-1.7-2.5-2.7-4.1-2.1c-1.6,0.6-2.7,2.5-2.1,4.2C72.9,18.7,86.5,28.4,101.1,27.8z" />
                                        <path fill="#ffffff" d="M105.9,40.6c-1-2.3-3.3-3.8-5.8-3.8c-3.3,0.2-6.8,0-10.3-0.8C75.4,33,64.5,22.7,59.5,9.7
			c-0.8-2.3-3.3-4-5.8-3.8C27,6.5,3.7,26.9,0.4,55.5c-2.9,26.3,13,51.5,37.5,59.7c31.7,10.5,64.5-9.5,71.1-42.1
			C111.2,61.8,109.8,50.5,105.9,40.6z M63.9,44.8c0.4-1.7,2.1-2.9,3.9-2.5l13.6,2.9c1.6,0.4,2.9,2.1,2.5,4c-0.4,1.7-2.1,2.9-3.9,2.5
			l-13.6-2.9C64.7,48.2,63.4,46.5,63.9,44.8z M33.8,40.4c0.8-4.2,4.9-6.9,9.1-6.1c4.1,0.8,6.8,5,6,9.3c-0.8,4.2-4.9,6.9-9.1,6.1
			C35.6,48.8,33,44.6,33.8,40.4z M86.5,79.3C79.7,95.7,61.6,105,43.9,99.1c-13.2-4.4-22.5-16.8-23.7-30.5C20,65,22.9,62,26.4,62.7
			l56,9.3C85.7,72.6,87.8,76.1,86.5,79.3z" />
                                        <path fill="#ffffff" d="M58.9,83.9c-6.8-1.5-13.4,1.3-17.1,6.3c-0.8,1.1-0.4,2.7,0.8,3.2c2.1,1.1,4.5,1.9,7,2.5
			c6.6,1.5,13.2,0.2,18.5-2.7c1.2-0.6,1.4-2.3,0.6-3.4C66.3,86.9,62.8,84.8,58.9,83.9z" />
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <h1 class="ha-admin-top-bar__heading-title">Theme Builder</h1>
                    </div>
                    <div class="ha-admin-top-bar__main-area-buttons">
                        <a class="page-title-action" id="ha-template-library-add-new" href="http://ha.test/wp-admin/post-new.php?post_type=ha_library">Add New</a>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    public static function admin_columns_headers($posts_columns) {
        $offset = 2;

        $posts_columns = array_slice($posts_columns, 0, $offset, true) + [
            'type' => __('Type', 'happy-elementor-addons'),
            'condition' => __('Conditions', 'happy-elementor-addons'),
        ] + array_slice($posts_columns, $offset, null, true);

        return $posts_columns;
    }

    public function admin_columns_content( $column_name, $post_id ) {

        // $instance = self::instance();

        if ('type' === $column_name) {

            $type       = get_post_meta($post_id, '_ha_library_type', true);
            $isActive   = get_post_meta($post_id, '_ha_template_active', true);

            echo ucfirst($type);

            echo "<span id='htlt-", $post_id, "'>";

            if ($isActive) {

                echo " - <b>Active</b>";

            }

            echo "</span>";
        }

        if ( 'condition' === $column_name ) {

            // generate display condition from document conditions
            $includeConditions     = [];
            $excludeConditions      = [];

            // get doc conditions
            $documentConditions    = $this->get_document_conditions($post_id);

            if( !empty( $documentConditions ) ) {
                foreach( $documentConditions AS $key => $condition ) {
                    if( 'include' === $condition['type'] ) {
                        $sub_page_id            = !empty( $condition['sub_id'] ) ? '#' . get_the_title( $condition['sub_id'] ) : '';
                        $con_label              = !empty( $condition['sub_name'] ) && 'all' !== $condition['sub_name'] ? Condition_Manager::instance()->get_name($condition['sub_name']) . $sub_page_id : Condition_Manager::instance()->get_all_name($condition['name']);
                        $includeConditions[]    = $con_label;
                    } else if ( 'exclude' === $condition['type'] ) {
                        $sub_page_id        = !empty( $condition['sub_id'] ) ? '#' . get_the_title( $condition['sub_id'] ) : '';
                        $con_label          =  !empty( $condition['sub_name'] ) && 'all' !== $condition['sub_name'] ? Condition_Manager::instance()->get_name($condition['sub_name']) . $sub_page_id : Condition_Manager::instance()->get_all_name($condition['name']);
                        $excludeConditions[] = $con_label;
                    } else {
                        // not use this..
                    }

                }
            }

            echo '<b>Include : </b> ' . implode( ', ', $includeConditions ) . '<br/>' . '<b>Exclude : </b> ' . implode( ', ', $excludeConditions );

        }
    }

    public function admin_print_tabs($views) {
        $getActive = get_query_var('ha_library_type');
        // var_dump($getActive);
    ?>
        <div id="happyaddon-template-library-tabs-wrapper" class="nav-tab-wrapper">
            <a class="nav-tab <?= !($getActive) ? 'nav-tab-active' : ''; ?>" href="<?= admin_url(self::TAB_BASE) ?>">All</a>
            <?php
            foreach (self::TEMPLATE_TYPE as $key => $value) {
                $active = ($getActive == $key) ? 'nav-tab-active' : '';
                $admin_filter_url = admin_url(self::TAB_BASE . '&ha_library_type=' . $key);
                echo '<a class="nav-tab ' . $active . '" href="' . $admin_filter_url . '">' . $value . '</a>';
            }
            ?>
        </div>
        <br>
<?php
        return $views;
    }

    /**
     * @since 2.3.0
     * @access public
     */
    public function add_new_template_template() {
        ob_start();
        include(HAPPY_ADDONS_DIR_PATH . 'templates/admin/new-template.php');
        $template = ob_get_clean();
        echo $template;
    }

    // public function edit_template_template() {
    //     ob_start();
    //     include(HAPPY_ADDONS_DIR_PATH . 'templates/admin/edit-template.php');
    //     $template = ob_get_clean();
    //     echo $template;
    // }

    public function edit_template_condition_modal() {
        if (self::CPT === get_post_type()) {
            ob_start();
            include(HAPPY_ADDONS_DIR_PATH . 'templates/admin/edit-template-condition.php');
            $template = ob_get_clean();
            echo $template;
        }
    }

    /**
     * Admin action new post.
     *
     * When a new post action is fired the title is set to 'Elementor' and the post ID.
     *
     * Fired by `admin_action_elementor_new_post` action.
     *
     * @since 1.9.0
     * @access public
     */
    public function admin_action_new_post() {

        // echo '<pre>';
        // var_dump($_REQUEST);
        // echo '</pre>';

        // die();

        check_admin_referer('ha_library_new_post_action');

        if (empty($_GET['post_type'])) {
            $post_type = 'post';
        } else {
            $post_type = sanitize_text_field($_GET['post_type']);
        }

        $post_type_object = get_post_type_object($post_type);

        if (!current_user_can($post_type_object->cap->edit_posts)) {
            return;
        }

        if (empty($_GET['template_type'])) {
            $type = 'post';
        } else {
            $type = sanitize_text_field($_GET['template_type']);
        }
        
        $post_data = isset($_GET['post_data']) ? ha_sanitize_array_recursively($_GET['post_data']) : [];

        // $template_display_type = isset($_GET['template_display_type']) ? $_GET['template_display_type'] : '';
        // $template_display_type_singular = isset($_GET['template_display_type_singular']) ? $_GET['template_display_type_singular'] : '';
        // $template_display_type_selected = isset($_GET['template_display_type_selected']) ? $_GET['template_display_type_selected'] : [];

        $conditions = [];

        // if (!empty($template_display_type)) {
        //     $conditions .= $template_display_type;
        //     if ($template_display_type == 'singular') {
        //         $conditions .= '/' . $template_display_type_singular;
        //         if ($template_display_type_singular == 'selective') {
        //             $vals = implode(',', $template_display_type_selected);
        //             $conditions .= '/' . $vals;
        //         }
        //     }
        // }

        $meta = [];



        /**
         * Create new post meta data.
         *
         * Filters the meta data of any new post created.
         *
         * @since 2.0.0
         *
         * @param array $meta Post meta data.
         */
        // $meta = apply_filters( 'elementor/admin/create_new_post/meta', $meta );

        $meta['display_conditions'] = $conditions;
        $post_data['post_type'] = $post_type;

        $document = $this->create_template_document($type, $post_data, $meta);

        if (is_wp_error($document)) {
            wp_die($document); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        wp_redirect($this->get_edit_url($document));

        die;
    }

    protected function create_template_document($type, $post_data, $meta) {
        if (empty($post_data['post_title'])) {
            $post_data['post_title'] = esc_html__('Elementor', 'happy-elementor-addons');
            $update_title = true;
        }

        $meta_data['_elementor_edit_mode'] = 'builder';

        // Save the type as-is for plugins that hooked at `wp_insert_post`.
        $meta_data['_ha_library_type']  = $type;
        $meta_data['_ha_display_cond']  = $meta['display_conditions'];
        $meta_data['_wp_page_template'] = 'elementor_canvas';

        $post_data['meta_input'] = $meta_data;

        $post_id = wp_insert_post($post_data);

        if (!empty($update_title)) {
            $post_data['ID'] = $post_id;
            $post_data['post_title'] .= ' #' . $post_id;

            // The meta doesn't need update.
            unset($post_data['meta_input']);

            wp_update_post($post_data);
        }

        return $post_id;
    }

    public function get_edit_url($id) {
        $url = add_query_arg(
            [
                'post' => $id,
                'action' => 'elementor',
            ],
            admin_url('post.php')
        );

        return $url;
    }


    public function hooks() {
        $this->current_template = basename(get_page_template_slug());
        if ($this->current_template == 'elementor_canvas') {
            return;
        }

        $this->current_theme = get_template();

        switch ($this->current_theme) {
            case 'astra':
                new Theme_Hooks\Astra(self::template_ids());
                break;

            case 'generatepress':
            case 'generatepress-child':
                new Theme_Hooks\Generatepress(self::template_ids());
                break;

            case 'oceanwp':
            case 'oceanwp-child':
                new Theme_Hooks\Oceanwp(self::template_ids());
                break;

            case 'bb-theme':
            case 'bb-theme-child':
                new Theme_Hooks\Bbtheme(self::template_ids());
                break;

            case 'genesis':
            case 'genesis-child':
                new Theme_Hooks\Genesis(self::template_ids());
                break;

            case 'twentynineteen':
                new Theme_Hooks\TwentyNineteen(self::template_ids());
                break;

            case 'my-listing':
            case 'my-listing-child':
                new Theme_Hooks\MyListing(self::template_ids());
                break;

            default:
                new Theme_Hooks\Theme_Support();
                break;
        }
    }

    public static function template_ids() {
        $cached = wp_cache_get('ha_template_ids');
        if (false !== $cached) {
            return $cached;
        }

        $instance = self::instance();
        $instance->the_filter();

        $ids = [
            $instance->header_template,
            $instance->footer_template,
            $instance->singular_template
        ];

        if ($instance->header_template != null) {
            if (class_exists('\Elementor\Core\Files\CSS\Post')) {
                $css_file = new \Elementor\Core\Files\CSS\Post($instance->header_template);
                $css_file->enqueue();
            }
        }

        if ($instance->footer_template != null) {
            if (class_exists('\Elementor\Core\Files\CSS\Post')) {
                $css_file = new \Elementor\Core\Files\CSS\Post($instance->footer_template);
                $css_file->enqueue();
            }
        }

        wp_cache_set('ha_template_ids', $ids);
        return $ids;
    }


    public function get_document_instances($post_id) {

        $summary = [];

        $document_conditions = $this->get_document_conditions($post_id);

        $summary = [];

        if (!empty($document_conditions)) {
            foreach ($document_conditions as $document_condition) {
                if ('exclude' === $document_condition['type']) {
                    // continue;
                }

                // print_r($document_condition);

                $condition_name = !empty($document_condition['sub_name']) ? $document_condition['sub_name'] : $document_condition['name'];

                // $condition = $this->get_condition($condition_name);
                // if (!$condition) {
                //     continue;
                // }

                if (!empty($document_condition['sub_id'])) {
                    $instance_label = Condition_Manager::instance()->get_name($condition_name) . " #{$document_condition['sub_id']}";
                } else {
                    // $instance_label = $condition_name->get_all_label();
                    $instance_label = Condition_Manager::instance()->get_all_name($condition_name);
                }

                $summary[$condition_name] = $instance_label;
            }
        }

        return $summary;
    }
    /**
     * @param Theme_Document $document
     *
     * @return array
     */
    public function get_document_conditions($post_id) {
        $saved_conditions = get_post_meta($post_id, '_ha_display_cond', true);

        $conditions = [];

        if (is_array($saved_conditions)) {
            foreach ($saved_conditions as $condition) {
                $conditions[] = $this->parse_condition($condition);
            }
        }

        return $conditions;
    }

    private function get_template_by_location($location) {
        $templates = $this->cache->get_by_location($location);

        return $templates;
    }

    protected function the_filter() {
        $arg = [
            'posts_per_page'   => -1,
            'orderby'          => 'id',
            'order'            => 'DESC',
            'post_status'      => 'publish',
            'post_type'        => self::CPT
        ];

        $this->templates = get_posts($arg);

        $this->templates = null;

        // more conditions can be triggered at once
        // don't use switch case
        // may impliment and callable by dynamic class in future


        // entire site
        if (!is_admin()) {
            $filters = [[
                'key'     => 'condition_a',
                'value'   => 'general',
            ]];
            $this->load_template_element($filters);
        }

        // all pages, all posts, 404 page
        if (is_page()) {
            $filters = [
                [
                    'key'     => 'condition_a',
                    'value'   => 'singular',
                ],
                [
                    'key'     => 'condition_singular',
                    'value'   => 'all_pages',
                ]
            ];
            $this->load_template_element($filters);
        } elseif (is_single()) {
            $filters = [
                [
                    'key'     => 'condition_a',
                    'value'   => 'posts',
                ]
            ];
            $this->load_template_element($filters);
        } elseif (is_404()) {
            $filters = [
                [
                    'key'     => 'condition_a',
                    'value'   => 'singular',
                ],
                [
                    'key'     => 'condition_singular',
                    'value'   => '404page',
                ]
            ];
            $this->load_template_element($filters);
        }
    }

    private function check_elementor_content($post_id) {
        $elContent = get_post_meta($post_id, '_elementor_data', true);

        if ($elContent) {
            return true;
        }

        return false;
    }

    public function get_public_post_types() {
        $post_type_args = [
            // Default is the value $public.
            'show_in_nav_menus' => true,
        ];

        // Keep for backwards compatibility
        if (!empty($args['post_type'])) {
            $post_type_args['name'] = $args['post_type'];
            unset($args['post_type']);
        }

        $post_type_args = wp_parse_args($post_type_args);

        $_post_types = get_post_types($post_type_args, 'objects');

        $post_types = [];

        foreach ($_post_types as $post_type => $object) {
            $post_types[$post_type] = $object->label;
        }

        return $post_types;
    }

    public function ha_theme_builder_content($template) {
        $location = '';

        if (is_singular(array_keys($this->get_public_post_types())) || is_404()) {
            $location = 'single';

            $isBuiltWithElementor = $this->check_elementor_content(get_the_ID());

            if ($isBuiltWithElementor) {
                return $template;
            }
        } elseif (function_exists('is_shop') && \is_shop()) {
            $location = 'archive';
        } elseif (is_archive() || is_tax() || is_home() || is_search()) {
            $location = 'archive';
        }

        if (is_plugin_active('elementor-pro/elementor-pro.php')) {
            $document = \ElementorPro\Plugin::elementor()->documents->get_doc_for_frontend(get_the_ID());
            $page_templates_module = \ElementorPro\Plugin::elementor()->modules_manager->get_modules('page-templates');

            if ($document && $document instanceof \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document) {
                // For editor preview iframe.
                $location = $document->get_location();

                if ('header' === $location || 'footer' === $location) {
                    $page_template = $page_templates_module::TEMPLATE_HEADER_FOOTER;
                    $template_path = $page_templates_module->get_template_path($page_template);
                    $page_templates_module->set_print_callback(function () use ($location) {
                        \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->do_location($location);
                    });
                    $template = $template_path;
                    return $template;
                }
            }
        }
        if ($location) {
            $location_documents = Condition_Manager::instance()->get_documents_for_location($location);

            if (empty($location_documents)) {
                return $template;
            }

            if ('single' === $location || 'archive' === $location) {

                $first_key = key($location_documents);
                $theme_document = $location_documents[$first_key];

                $templateType = get_post_meta($theme_document, '_wp_page_template', true);

                $this->singular_template = $theme_document;

                if ($theme_document) {
                    switch ($templateType) {
                        case "elementor_canvas":
                            $template = HAPPY_ADDONS_DIR_PATH . 'templates/builder/singular/canvas.php';
                            break;
                        case "elementor_header_footer":
                            $template = HAPPY_ADDONS_DIR_PATH . 'templates/builder/singular/fullwidth.php';
                            break;
                        default:
                            // $template = $template;
                            $template = HAPPY_ADDONS_DIR_PATH . 'templates/builder/singular/fullwidth.php';
                            break;
                    }
                }
            }
        }

        return $template;
    }

    /*
    * Render Elementor single blog content
    */
    public function single_blog_content_elementor($post) {
        $templates = $this->singular_template;
        // $firstKey = key($templates);
        if (!empty($templates)) {
            echo self::render_builder_data($templates);
        } else {
            the_content();
        }
    }

    protected function load_template_element($filters) {
        $template_id = array();

        if ($this->templates != null) {
            foreach ($this->templates as $template) {
                $template = $this->get_full_data($template);
                $match_found = true;

                // WPML Language Check
                if (defined('ICL_LANGUAGE_CODE')) :
                    $current_lang = apply_filters('wpml_post_language_details', NULL, $template['ID']);

                    if (!empty($current_lang) && !$current_lang['different_language'] && ($current_lang['language_code'] == ICL_LANGUAGE_CODE)) :
                        $template_id[$template['type']] = $template['ID'];
                    endif;
                endif;

                foreach ($filters as $filter) {
                    if ($filter['key'] == 'condition_singular_id') {
                        $ids = explode(',', $template[$filter['key']]);
                        if (!in_array($filter['value'], $ids)) {
                            $match_found = false;
                        }
                    } elseif ($template[$filter['key']] != $filter['value']) {
                        $match_found = false;
                    }
                    if ($filter['key'] == 'condition_a' && $template[$filter['key']] == 'singular' && count($filters) < 2) {
                        $match_found = false;
                    }
                }

                if ($match_found == true) {

                    if ($template['type'] == 'header') {
                        $this->header_template = isset($template_id['header']) ? $template_id['header'] : $template['ID'];
                    }
                    if ($template['type'] == 'footer') {
                        $this->footer_template = isset($template_id['footer']) ? $template_id['footer'] : $template['ID'];
                    }
                    if ($template['type'] == 'single') {
                        $this->single_template = isset($template_id['single']) ? $template_id['single'] : $template['ID'];
                    }
                }
            }
        }
    }

    protected function get_full_data($post) {
        if ($post != null) {
            $tpl_type = get_post_meta($post->ID, '_ha_library_type', true);
            $tpl_cond = get_post_meta($post->ID, '_ha_display_cond', true);

            //$parsed_cond = $this->parse_condition($tpl_cond);

            $conditions = [];

            if (is_array($tpl_cond)) {
                foreach ($tpl_cond as $condition) {
                    $conditions[] = $this->parse_condition($condition);
                }
            }


            return array_merge((array)$post, [
                'type' => $tpl_type,
                'condition_a' => $parsed_cond['name'],
                'condition_singular' => $parsed_cond['sub_name'],
                'condition_singular_id' => $parsed_cond['sub_id'],
            ]);
        }
    }

    protected function parse_condition($condition) {
        // list($name, $sub_name, $sub_id) = array_pad(explode('/', $condition), 3, '');
        // return compact('name', 'sub_name', 'sub_id');

        list($type, $name, $sub_name, $sub_id) = array_pad(explode('/', $condition), 4, '');
        return compact('type', 'name', 'sub_name', 'sub_id');
    }

    public static function render_builder_data($content_id) {
        $_elementor = \Elementor\Plugin::instance();
        $has_css = false;

        if (('internal' === get_option('elementor_css_print_method')) || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
            $has_css = true;
        }
        return $_elementor->frontend->get_builder_content_for_display($content_id, $has_css);
    }

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private static function lang($string) {
        switch ($string) {
            case "general":
                $lang = 'Entire Website';
                break;
            case "archive":
                $lang = 'All Archives--';
                break;
            case "singular":
                $lang = 'Singular: ';
                break;
            case "singular-selective":
                $lang = 'Pages: ';
                break;
            case "all_pages":
                $lang = 'All Pages';
                break;
            case "all":
                $lang = 'All';
                break;
            case "posts":
                $lang = 'All Posts';
                break;
            default:
                $lang = '-';
                break;
        }
        return $lang;
    }


    function ha_template_element_scripts() {
        if (self::CPT === get_post_type()) {
            wp_enqueue_script(
                'happy-addons-template-elements',
                HAPPY_ADDONS_ASSETS . 'admin/js/template-elements.min.js',
                ['jquery', 'happy-elementor-addons-editor'],
                HAPPY_ADDONS_VERSION,
                true
            );

            wp_enqueue_script(
                'happy-addons-micromodal',
                'https://unpkg.com/micromodal@0.4.10/dist/micromodal.js',
                [],
                HAPPY_ADDONS_VERSION,
                true
            );
        }
    }

    public function render_builder_data_location($location) {
        // $teplates = Condition_Manager::instance()->get_location_templates($location);

        $teplates = Condition_Manager::instance()->get_documents_for_location($location);
        $first_key = key($teplates);
        $valid_template = $teplates[$first_key];

        return $this->render_builder_data($valid_template);
    }

    function _is_elementor_pro() {
        $file_path = 'elementor/elementor.php';
        $installed_plugins = get_plugins();

        return isset($installed_plugins[$file_path]);
    }

    public function ha_template_document_title($config, $post_id) {
        $tpl_type = get_post_meta($post_id, '_ha_library_type', true);

        if (self::CPT === get_post_type($post_id)) {
            $title = "";
            switch ($tpl_type) {
                case "header":
                    $title = "Header Settings";
                    break;

                case "footer":
                    $title = "Footer Settings";
                    break;

                case "single":
                    $title = "Singular Settings";
                    break;

                case "archive":
                    $title = "Archive Settings";
                    break;
            }
            $config['settings']['panelPage']['title'] = $title;
        }
        return $config;
    }

    public function pr($data=[])
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

Theme_Builder::instance();
