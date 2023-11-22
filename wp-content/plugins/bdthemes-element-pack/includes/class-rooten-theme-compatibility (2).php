<?php

namespace ElementPack\Includes;

use Elementor\Plugin;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Rooten Theme Support setup
 * @since 3.1.0
 */
class Element_Pack_Rooten_Theme_Compatibility {

    /**
     * Instance of Rooten Theme Support Class
     * @var Rooten Theme Support Class
     */
    private static $_instance = null;

    /**
     * Instance of Elemenntor Frontend class.
     * @var \Elementor\Frontend()
     */
    private static $elementor_instance;

    /**
     * Instance of Rooten Theme Support Class
     * @return Rooten Theme Support Class Instance of Rooten Theme Support Class
     */
    public static function instance() {
        if ( !isset(self::$_instance) ) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Constructor
     */
    private function __construct() {

        self::$elementor_instance = Plugin::instance();

        add_action('init', array($this, 'rooten_custom_template_posttype'));
        add_action('admin_menu', array($this, 'register_admin_menu'), 50);

        add_action('template_redirect', array($this, 'block_template_frontend'));
        add_action('admin_init', [$this, 'rooten_custom_template_metabox'], 10, 5);
        add_action('save_post', array($this, 'rooten_custom_template_save_metabox'));


        // add_filter('rwmb_meta_boxes', [$this, 'rooten_custom_template_metabox']);
        // add_action('rwmb_rooten_template_type_after_save_field', [$this, 'rooten_saved_transient_delete'], 10, 5);

        add_filter('single_template', array($this, 'load_canvas_template'));

        add_filter('manage_bdt-custom-template_posts_columns', array($this, 'set_shortcode_columns'));

        add_action('manage_bdt-custom-template_posts_custom_column', array($this, 'render_shortcode_column'), 10, 2);

        add_shortcode('rooten_custom_template', array($this, 'shortcode_template'));
    }

    /**
     * Register Post type for header footer templates
     */
    public function rooten_custom_template_posttype() {

        $labels = array(
            'name'               => __('Rooten Custom Template', 'rooten'),
            'singular_name'      => __('Rooten Custom Template', 'rooten'),
            'menu_name'          => __('Rooten Custom Template', 'rooten'),
            'name_admin_bar'     => __('Rooten Custom Template', 'rooten'),
            'add_new'            => __('Add New', 'rooten'),
            'add_new_item'       => __('Add New Custom Template', 'rooten'),
            'new_item'           => __('New Custom Template', 'rooten'),
            'edit_item'          => __('Edit Custom Template', 'rooten'),
            'view_item'          => __('View Custom Template', 'rooten'),
            'all_items'          => __('All Rooten Custom Templates', 'rooten'),
            'search_items'       => __('Search Custom Templates', 'rooten'),
            'parent_item_colon'  => __('Parent Custom Templates:', 'rooten'),
            'not_found'          => __('No Custom Templates found.', 'rooten'),
            'not_found_in_trash' => __('No Custom Templates found in Trash.', 'rooten'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'rewrite'             => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'exclude_from_search' => true,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'menu_icon'           => 'dashicons-editor-kitchensink',
            'supports'            => array('title', 'thumbnail', 'elementor'),
        );

        register_post_type('bdt-custom-template', $args);
    }

    /**
     * Register the admin menu for Rooten Custom Template.
     * @since 3.1.0
     * Show menu in Appearance -> Rooten Custom Template
     */
    public function register_admin_menu() {
        add_submenu_page(
            'themes.php',
            __('Rooten Custom Template', 'rooten'),
            __('Rooten Custom Template', 'rooten'),
            'edit_pages',
            'edit.php?post_type=bdt-custom-template'
        );
    }

    public function rooten_custom_template_metabox() {
        add_meta_box('_rooten_custom_template', esc_html__('Template Settings', 'rooten'), [$this, 'rooten_custom_template_metabox_callback'], 'bdt-custom-template', 'side');
    }

    public function rooten_custom_template_metabox_callback($post) {
        wp_nonce_field('rooten_custom_template_nonce_action', 'rooten_custom_template_nonce_field');
        $templates  = [
            'header' => esc_html__('Header', 'rooten'),
            'footer' => esc_html__('Footer', 'rooten'),
            '404'    => esc_html__('404 Page', 'rooten'),
            'others' => esc_html__('Other', 'rooten'),
        ];
        $saved_page = get_post_meta($post->ID, 'rooten_template_type', true);
        $content    = '';
        foreach ( $templates as $index => $page ) {
            $_page   = ucwords($page);
            $checked = ($index == $saved_page) ? "checked='checked'" : '';
            $content .= <<<BDT
			<input type="radio" name="rooten_template_type" id="rooten_template_type" value="{$index}" {$checked}  />
			<label for="rooten_template_type">{$_page}</label><br>
BDT;
        }
        echo $content;
    }

    public function rooten_custom_template_save_metabox($post_id) {
        if ( !$this->is_secured('rooten_custom_template_nonce_field', 'rooten_custom_template_nonce_action', $post_id) ) {
            return $post_id;
        }
        $template = isset($_POST['rooten_template_type']) ? $_POST['rooten_template_type'] : '';
        $template = sanitize_text_field($template);
        update_post_meta($post_id, 'rooten_template_type', $template);
    }

    private function is_secured($nonce_field, $action, $post_id) {
        $nonce = isset($_POST[$nonce_field]) ? $_POST[$nonce_field] : '';

        if ( $nonce == '' ) {
            return false;
        }
        if ( !wp_verify_nonce($nonce, $action) ) {
            return false;
        }

        if ( !current_user_can('edit_post', $post_id) ) {
            return false;
        }

        if ( wp_is_post_autosave($post_id) ) {
            return false;
        }

        if ( wp_is_post_revision($post_id) ) {
            return false;
        }

        return true;
    }

    /**
     * Convert the Template name to be added in the notice.
     *
     * @param String $template_type Template type name.
     *
     * @return String $template_type Template type name.
     * @since 3.1.0
     *
     */
    public function template_location($template_type) {
        $template_type = ucfirst(str_replace('type_', '', $template_type));

        return $template_type;
    }

    /**
     * Don't display the elementor header footer templates on the frontend for non edit_posts capable users.
     *
     * @since 3.1.0
     */
    public function block_template_frontend() {
        if ( is_singular('bdt-custom-template') && !current_user_can('edit_posts') ) {
            wp_redirect(site_url(), 301);
            die;
        }
    }

    /**
     * Single template function which will choose our template
     *
     * @param String $single_template Single template.
     * @since 3.1.0
     */
    function load_canvas_template($single_template) {

        global $post;

        if ( 'bdt-custom-template' == $post->post_type ) {
            return ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';
        }

        return $single_template;
    }

    /**
     * Set shortcode column for template list.
     *
     * @param array $columns template list columns.
     */
    function set_shortcode_columns($columns) {

        $date_column = $columns['date'];

        unset($columns['date']);

        $columns['shortcode'] = __('Shortcode', 'rooten');
        $columns['date']      = $date_column;

        return $columns;
    }

    /**
     * Display shortcode in template list column.
     *
     * @param array $column template list column.
     * @param int $post_id post id.
     */
    function render_shortcode_column($column, $post_id) {

        switch ($column) {
            case 'shortcode':
                ob_start();
                ?>
                <span class="bdt-shortcode-col-wrap">
                    <input type="text" onfocus="this.select();" readonly="readonly"
                           value="[rooten_custom_template id='<?php echo esc_attr($post_id); ?>']"
                           class="regular-text code">
                </span>

                <?php

                ob_get_contents();
                break;
        }
    }

    /**
     * Callback to shortcode.
     *
     * @param array $atts attributes for shortcode.
     */
    public function shortcode_template($atts) {

        $atts = shortcode_atts(
            array(
                'id' => '',
            ),
            $atts,
            'rooten_custom_template'
        );

        $id = !empty($atts['id']) ? intval($atts['id']) : '';

        if ( empty($id) ) {
            return '';
        }

        // if (class_exists('\Elementor\Post_CSS_File')) {

        // 	// Load elementor styles.
        // 	$css_file = new \Elementor\Post_CSS_File($id);
        // 	$css_file->enqueue();
        // }

        return self::$elementor_instance->frontend->get_builder_content_for_display($id);
    }
}

Element_Pack_Rooten_Theme_Compatibility::instance();
