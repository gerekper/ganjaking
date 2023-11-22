<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\TemplateSystem;
if (!\defined('ABSPATH')) {
    exit;
}
class Metabox
{
    public function __construct()
    {
        // metabox Template in page
        add_action('add_meta_boxes', [$this, 'add_metabox_to_registered_post_types'], 1, 2);
        add_action('save_post', [$this, 'save_metaboxdata_template'], 1, 2);
        // metabox Template in elementor_library for Demo
        add_action('add_meta_boxes', [$this, 'metabox_demo_id'], 1, 2);
        add_action('save_post', [$this, 'save_metaboxdata_demo_id'], 1, 2);
        // metabox Template for terms
        add_action('admin_init', [$this, 'taxonomybox_init']);
    }
    /**
     * Add a metabox to the registered post types.
     *
     * @param string $post_type The type of post. Defaults to 'post'.
     * @param bool $post The post object. Defaults to false.
     * @return void
     */
    public static function add_metabox_to_registered_post_types($post_type = 'post', $post = \false)
    {
        if (\in_array($post_type, TemplateSystem::get_registered_types())) {
            add_meta_box('dce_metabox', 'Dynamic.ooo ' . __('Template System', 'dynamic-content-for-elementor'), [self::class, 'metabox_template_select'], null, 'side');
        }
    }
    /**
     * Display the metabox template select options.
     *
     * @param \WP_Post $post_object The post object.
     */
    public static function metabox_template_select($post_object)
    {
        $templates = \DynamicContentForElementor\Helper::get_all_templates(\true);
        $elementor_templates = get_post_meta($post_object->ID, 'dyncontel_elementor_templates', \true);
        $html = '';
        if (!empty($templates)) {
            $html .= self::build_template_select_html($templates, $elementor_templates, $post_object);
        }
        echo $html;
    }
    /**
     * Build the HTML for the template select options.
     *
     * @param array<mixed> $templates Array of templates.
     * @param string $elementor_templates The current Elementor templates.
     * @param \WP_Post $post_object The post object.
     * @return string The HTML for the template select options.
     */
    private static function build_template_select_html($templates, $elementor_templates, $post_object)
    {
        $html = '<label for="dce_post_template"><strong>' . esc_html__('Assign an Elementor Template', 'dynamic-content-for-elementor') . '</strong></label><br /><select id="dce_post_template" name="dyncontel_elementor_templates" class="js-dce-select">';
        foreach ($templates as $key => $template) {
            $selected = selected($elementor_templates, $key, \false);
            $html .= '<option value="' . $key . '"' . $selected . '>' . $template . '</option>';
        }
        $html .= '</select>';
        if ($post_object->post_parent) {
            $elementor_templates_parent = get_post_meta($post_object->ID, 'dyncontel_elementor_templates_parent', \true);
            $checked = $elementor_templates_parent ? ' checked' : '';
            $html .= '<br /><label for="dce_post_template_parent"><input type="checkbox" value="1" name="dyncontel_elementor_templates_parent" id="dce_post_template_parent"' . $checked . '>' . __('From Parent', 'dynamic-content-for-elementor') . '</label>';
        }
        return $html;
    }
    /**
     * Save the metabox data.
     *
     * @param int $post_id The ID of the post being saved.
     * @param \WP_Post $post The post object.
     * @return void
     */
    public static function save_metaboxdata_template($post_id, $post)
    {
        if (\defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (\in_array($post->post_type, TemplateSystem::get_registered_types())) {
            $elementor_templates = array('dyncontel_elementor_templates', 'dyncontel_elementor_templates_parent');
            foreach ($elementor_templates as $template) {
                if (\array_key_exists($template, $_POST)) {
                    update_post_meta($post_id, $template, sanitize_text_field($_POST[$template]));
                } else {
                    if ($template == 'dyncontel_elementor_templates_parent') {
                        delete_post_meta($post_id, $template);
                    }
                }
            }
        }
    }
    /**
     * Add a metabox for the demo ID.
     *
     * @param string $post_type The type of the post.
     * @param \WP_Post $post The post object.
     */
    public static function metabox_demo_id($post_type, $post)
    {
        if ($post_type == 'elementor_library') {
            add_meta_box('dce_metabox', __('Template Preview', 'dynamic-content-for-elementor'), [self::class, 'metabox_demo_id_post'], null, 'side');
        }
    }
    /**
     * Output the contents of the demo ID metabox.
     *
     * @param \WP_Post $post_object The post object.
     */
    public static function metabox_demo_id_post($post_object)
    {
        $html = '';
        $all_posts = \DynamicContentForElementor\Helper::get_all_posts(null, \true);
        $proModule = WP_PLUGIN_DIR . '/elementor-pro/modules/theme-builder/module.php';
        if (\file_exists($proModule) && \DynamicContentForElementor\Helper::is_elementorpro_active()) {
            include_once $proModule;
            $document = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_document($post_object->ID);
            // On view theme document show it's preview content.
            if ($document) {
                $preview_type = $document->get_settings('preview_type');
                $preview_id = $document->get_settings('preview_id');
                $demo_id = $preview_id;
                update_post_meta($post_object->ID, 'demo_id', $preview_id);
            }
        }
        $demo_id = get_post_meta($post_object->ID, 'demo_id', \true);
        if (!empty($all_posts)) {
            $html .= '<label for="dce_post_demoid"><strong><span class="dashicons dashicons-admin-network"></span> ' . esc_html__('Select post', 'dynamic-content-for-elementor') . '</strong></label><br /><select id="dce_post_demoid" name="demo_id" class="js-dce-select">';
            foreach ($all_posts as $tkey => $ttmp) {
                if (isset($ttmp['options'])) {
                    $html .= '<optgroup label="' . $ttmp['label'] . '">';
                    foreach ($ttmp['options'] as $akey => $atmp) {
                        $selected = $demo_id && $demo_id == $akey ? ' selected="selected"' : '';
                        $html .= '<option value="' . $akey . '"' . $selected . '>' . $atmp . '</option>';
                    }
                    $html .= '</optgroup>';
                } else {
                    $selected = $demo_id && $demo_id == $tkey ? ' selected="selected"' : '';
                    $html .= '<option value="' . $tkey . '"' . $selected . '>' . $ttmp . '</option>';
                }
            }
            $html .= '</select></p>';
        }
        echo $html;
    }
    /**
     * Save the demo ID metabox data.
     *
     * @param int $post_id The ID of the post being saved.
     * @param \WP_Post $post The post object.
     * @return void
     */
    public static function save_metaboxdata_demo_id($post_id, $post)
    {
        if (\defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        // if post type is different from our selected one, do nothing
        if ($post->post_type == 'elementor_library' && isset($_POST['demo_id']) && current_user_can('edit_post', $post_id)) {
            update_post_meta($post_id, 'demo_id', sanitize_text_field($_POST['demo_id']));
            // se esiste anche Elementor Pro aggiorno pure lui
            $proSettings = get_post_meta($post_id, '_elementor_page_settings', \true);
            if (empty($proSettings)) {
                $proSettings = array();
            }
            $proSettings['preview_id'] = sanitize_text_field($_POST['demo_id']);
            update_post_meta($post_id, '_elementor_page_settings', $proSettings);
        }
    }
    /**
     * Register the custom metaboxes for all public taxonomies.
     */
    public static function taxonomybox_init()
    {
        $args = ['public' => \true];
        $output = 'names';
        // names or objects, note names is the default
        $operator = 'and';
        $taxonomies_registered = get_taxonomies($args, $output, $operator);
        foreach ($taxonomies_registered as $taxonomy) {
            add_action($taxonomy . '_add_form_fields', [self::class, 'taxonomyname_metabox_add'], 10, 1);
            add_action($taxonomy . '_edit_form_fields', [self::class, 'taxonomyname_metabox_edit'], 10, 1);
            add_action('created_' . $taxonomy, [self::class, 'save_taxonomyname_metadata'], 10, 1);
            add_action('edited_' . $taxonomy, [self::class, 'save_taxonomyname_metadata'], 10, 1);
        }
    }
    /**
     * Render add metabox for taxonomy terms.
     *
     * This function is hooked into "{$taxonomy}_add_form_fields" action hook.
     * It displays the metabox only if the user has 'manage_options' capability.
     *
     * @param \WP_Term $tag Current taxonomy term object.
     *
     * @return void
     */
    public static function taxonomyname_metabox_add($tag)
    {
        // Only proceed if the user has the 'manage_options' capability.
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
		<div id="dce_termbox" class="dce-term-box">
			<div class="dce-term-head">
				<h3><?php 
        echo DCE_PRODUCT_NAME . ' ' . __('Template', 'dynamic-content-for-elementor');
        ?></h3>
			</div>
			<div class="form-field dce-term dce-term-add">
		<?php 
        echo self::render_select_metabox($tag, 'add');
        ?>
			</div>
		</div>
			<style>#dce_termbox { display: none; }</style>
			<?php 
    }
    /**
     * Render edit metabox for taxonomy terms.
     *
     * This function is hooked into "{$taxonomy}_edit_form_fields" action hook.
     * It displays the metabox only if the user has 'manage_options' capability.
     *
     * @param \WP_Term $tag Current taxonomy term object.
     *
     * @return void
     */
    public static function taxonomyname_metabox_edit($tag)
    {
        // Only proceed if the user has the 'manage_options' capability.
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
		<tr class="form-field dce-term dce-term-edit">
			<th scope="row" valign="top">
				<label for="dynamic_content"><?php 
        echo DCE_PRODUCT_NAME . ' ' . __('Template', 'dynamic-content-for-elementor');
        ?></label>
			</th>
			<td>
				<?php 
        echo self::render_select_metabox($tag, 'edit');
        ?>
			</td>
		</tr>
		<?php 
    }
    /**
     * Save metadata for taxonomy terms.
     *
     * This function is hooked into "created_{$taxonomy}" and "edited_{$taxonomy}" action hooks.
     * It saves the metadata only if the user has 'manage_options' capability.
     *
     * @param int $term_id Term ID.
     *
     * @return void
     */
    public static function save_taxonomyname_metadata($term_id)
    {
        // Only proceed if the user has the 'manage_options' capability.
        if (!current_user_can('manage_options')) {
            return;
        }
        if (isset($_POST['dynamic_content_head'])) {
            update_term_meta($term_id, 'dynamic_content_head', sanitize_text_field($_POST['dynamic_content_head']));
        }
        if (isset($_POST['dynamic_content_block'])) {
            update_term_meta($term_id, 'dynamic_content_block', sanitize_text_field($_POST['dynamic_content_block']));
        }
        if (isset($_POST['dynamic_content_single'])) {
            update_term_meta($term_id, 'dynamic_content_single', sanitize_text_field($_POST['dynamic_content_single']));
        }
    }
    /**
     * Generate the HTML for select options.
     *
     * @param array<string,string>  $templates An array of templates.
     * @param \WP_Term $tag       The term object.
     * @param string $name      The name of the option.
     * @param string $mode      The mode of the operation ('add' or 'edit').
     *
     * @return string The HTML string for the options.
     */
    public static function generate_options($templates, $tag, $name, $mode)
    {
        $output = '';
        foreach ($templates as $key => $value) {
            // Initialize the 'selected' attribute as an empty string
            $selected = '';
            // If we are in 'edit' mode and this option is the selected one, mark it as 'selected'
            if ($mode == 'edit' && get_term_meta($tag->term_id, $name, \true) == $key) {
                $selected = ' selected';
            }
            // Generate the option element for the select
            $output .= \sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
        }
        return $output;
    }
    /**
     * Render a select metabox.
     *
     * @param object $tag  The term object.
     * @param string $mode The mode of the operation ('add' or 'edit').
     */
    public static function render_select_metabox($tag, $mode)
    {
        // Get all templates
        $templates = \DynamicContentForElementor\Helper::get_all_templates(\true);
        // Define the labels for the select elements
        $labels = ['dynamic_content_head' => __('Head', 'dynamic-content-for-elementor'), 'dynamic_content_block' => __('Blocks/Canvas', 'dynamic-content-for-elementor'), 'dynamic_content_single' => __('Single', 'dynamic-content-for-elementor')];
        // Loop over each label and create a select element for it
        foreach ($labels as $name => $label) {
            // Output the label
            \printf('<label>%s</label>', $label);
            // Start the select element
            \printf('<select class="js-dce-select" id="%s" name="%s">', $name, $name);
            // Generate the options for the select
            echo self::generate_options($templates, $tag, $name, $mode);
            // End the select and insert a line break
            echo '</select><br>';
        }
    }
}
