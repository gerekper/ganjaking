<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\Helper;
use ElementorPro\Modules\AssetsManager\AssetTypes\Fonts_Manager;
use ElementorPro\Modules\AssetsManager\AssetTypes\Fonts\Custom_Fonts;
// disable phpcs because of a bug with wordpress sniffs with fix not yet released.
// phpcs:ignoreFile
if (!\defined('ABSPATH')) {
    exit;
}
class PdfHtmlTemplates
{
    const CPT = 'dce_html_template';
    const FONTS_CACHE_OPTION = 'dce_html_template_fonts_cache';
    const TEMPLATE_META_KEY = 'dce_html_template';
    const FIELD_IS_TEMPLATE = 'dce-html-is-template';
    const FIELD_TEMPLATE_ID = 'dce-html-template-id';
    const FIELD_CODE = 'dce-html-code';
    const FIELD_PREVIEW_FORM_DATA = 'dce-preview-form-data';
    const FIELD_PREVIEW_POST = 'dce-preview-post';
    const FIELD_FORMAT = 'dce-html-format';
    const FIELD_ORIENTATION = 'dce-html-orientation';
    const DEFAULT_HTML_CODE = <<<EOF
<head>
<style>
@page {
\theader: html_myHeader;
}
body {
\tfont-family: chelvetica;
}
h1 {
\tfont-family: ctimes;
}
code {
\tfont-family: ccourier; color: orange;
}
</style>
</head>
<body>
<htmlpageheader name="myHeader">
\tPage {PAGENO}/{nbpg}
</htmlpageheader>
<h1>
\tDynamic.ooo PDF Generator
</h1>
<p>
Hi {{ form.name }}, your favorite animals are:
<ul>
{% for animal in form_raw.animals %}
\t<li>
\t{{ animal }}
\t</li>
{% endfor %}
</ul>
</p>
<p>
\tNotice that this is a Timber Template to be used with expressions like <code>{{ '{{ form.name }}' }}</code> and not tokens like <code>[form:name]</code>. Read this example code for more details.
</p>
</body>
EOF;
    const DEFAULT_PREVIEW_DATA = <<<EOF
name|Joe
animals|Dog,Llama
EOF;
    public $post_type_object;
    public function __construct()
    {
        $this->register_post_type();
        add_action('wp_ajax_dce_pdf_button', [$this, 'pdf_button_ajax']);
        add_action('wp_ajax_nopriv_dce_pdf_button', [$this, 'pdf_button_ajax']);
        add_action('wp_ajax_dce_preview_pdf_html_template', [$this, 'preview_pdf_html_template']);
        add_action('wp_ajax_dce_get_posts', [$this, 'dce_get_posts_ajax_callback']);
        add_action('add_meta_boxes_' . self::CPT, [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::CPT, [$this, 'save_post_meta'], 10, 3);
        add_action('save_post_elementor_font', function () {
            delete_option(self::FONTS_CACHE_OPTION);
        }, 100);
    }
    public function pdf_button_ajax()
    {
        $post_id = $_POST['post_id'];
        $element_id = $_POST['element_id'];
        if (isset($_POST['queried_id'])) {
            $queried_id = $_POST['queried_id'];
        } else {
            $queried_id = $post_id;
        }
        if (get_post_status($post_id) !== 'publish' && !current_user_can('read_post', $post_id)) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        if (get_post_status($queried_id) !== 'publish' && !current_user_can('read_post', $queried_id)) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        $pdf_button = Helper::get_elementor_element_from_post_data($post_id, $element_id, $queried_id);
        $settings = $pdf_button['settings'];
        $get_template_from = $settings['html_converter_get_template_from'];
        switch ($get_template_from) {
            case 'post':
                $format = $settings['dce_pdf_button_html_conv_format'];
                $orientation = $settings['dce_pdf_button_orientation'] === 'landscape' ? 'L' : 'P';
                $this->generate_pdf_from_elementor_post($queried_id, $format, $orientation, \false);
                break;
            case 'html_template':
                $t = $settings['html_converter_html_template'];
                $this->generate_pdf_from_template_id($t, [], \false);
        }
        die;
    }
    // Code from https://rudrastyh.com/wordpress/select2-for-metaboxes-with-ajax.html
    public function dce_get_posts_ajax_callback()
    {
        if (!current_user_can('administrator')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        // we will pass post IDs and titles to this array
        $return = array();
        $args = [
            's' => $_GET['q'],
            // the search query
            'post_status' => 'publish',
            // if you don't want drafts to be returned
            'ignore_sticky_posts' => 1,
            'posts_per_page' => 50,
        ];
        if ($_GET['dce_post_type'] ?? \false) {
            $args['post_type'] = $_GET['dce_post_type'];
        }
        // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
        $search_results = new \WP_Query($args);
        while ($search_results->have_posts()) {
            $search_results->the_post();
            // shorten the title a little
            $title = \mb_strlen($search_results->post->post_title) > 50 ? \mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
            $return[] = array($search_results->post->ID, $title);
            // array( Post ID, Post Title )
        }
        echo wp_json_encode($return);
        die;
    }
    public function register_post_type()
    {
        $labels = ['name' => _x('HTML Templates', 'CPT Name', 'dynamic-content-for-elementor'), 'singular_name' => _x('HTML Template', 'CPT Singular Name', 'dynamic-content-for-elementor'), 'add_new' => __('Add New', 'dynamic-content-for-elementor'), 'add_new_item' => __('Add New HTML Template', 'dynamic-content-for-elementor'), 'edit_item' => __('Edit HTML Template', 'dynamic-content-for-elementor'), 'new_item' => __('New HTML Template', 'dynamic-content-for-elementor'), 'all_items' => __('All HTML Template', 'dynamic-content-for-elementor'), 'view_item' => __('View HTML Template', 'dynamic-content-for-elementor'), 'search_items' => __('Search HTML Template', 'dynamic-content-for-elementor'), 'not_found' => __('No HTML Template found', 'dynamic-content-for-elementor'), 'not_found_in_trash' => __('No HTML Template found in trash', 'dynamic-content-for-elementor'), 'parent_item_colon' => '', 'menu_name' => _x('HTML Templates', 'CPT Menu Name', 'dynamic-content-for-elementor')];
        $args = ['labels' => $labels, 'public' => \false, 'rewrite' => \false, 'show_ui' => \true, 'show_in_menu' => \false, 'show_in_nav_menus' => \false, 'exclude_from_search' => \true, 'capability_type' => 'post', 'hierarchical' => \false, 'supports' => ['title']];
        $this->post_type_object = register_post_type(self::CPT, $args);
    }
    // Convert Elementor font settings to Mpdf. For example bold, italic to "BI".
    // Return false if not supported. For example when using weight as a number.
    private function get_font_settings($weight, $style)
    {
        if ($weight === 'normal') {
            if ($style === 'normal') {
                return 'R';
            } elseif ($style === 'italic') {
                return 'I';
            }
        } elseif ($weight === 'bold') {
            if ($style === 'normal') {
                return 'B';
            } elseif ($style === 'italic') {
                return 'BI';
            }
        }
        return \false;
    }
    // Credit: https://gist.github.com/kamalahmed/cb1ef003d384715ad38fffff379d5a4c
    private function attachment_url_to_path($url)
    {
        $parsed_url = wp_parse_url($url);
        if (empty($parsed_url['path'])) {
            return \false;
        }
        $file = ABSPATH . \ltrim($parsed_url['path'], '/');
        if (\file_exists($file)) {
            return $file;
        }
        return \false;
    }
    /**
     * Check if a font has an otl table.
     *
     * Works by trying to creat a pdf with OTL on and catch a potential error.
     */
    private function font_has_otl($dir, $filename)
    {
        $dirs = [$dir];
        $conf = ['test' => ['R' => $filename, 'useOTL' => 0xff, 'useKashida' => 75]];
        try {
            $mpdf = new \DynamicOOOS\Mpdf\Mpdf(['tempDir' => \sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'mpdf', 'fontDir' => $dirs, 'fontdata' => $conf, 'default_font' => 'test', 'mono_fonts' => ['test'], 'serif_fonts' => ['test'], 'sans_fonts' => ['test']]);
            $mpdf->WriteHTML('hello world');
            $mpdf->Output(null, 'S');
        } catch (\DynamicOOOS\Mpdf\Exception\FontException $e) {
            return \false;
        }
        return \true;
    }
    // given a font by its CPT id, if supported return an array of containing:
    // - dirs: the directories where the font files are contained.
    // - config: the font configuration array.
    private function get_font($id)
    {
        $directories = [];
        $config = [];
        $saved = get_post_meta($id, Custom_Fonts::FONT_META_KEY, \true);
        $has_otl = \true;
        foreach ($saved as $variation) {
            $url = $variation['ttf']['url'] ?? '';
            if (empty($url)) {
                continue;
            }
            $font_settings = $this->get_font_settings($variation['font_weight'], $variation['font_style']);
            if (!$font_settings) {
                continue;
            }
            $path = $this->attachment_url_to_path($url);
            if (!$path) {
                continue;
            }
            $file_name = \basename($path);
            $dir = \dirname($path);
            if (!$this->font_has_otl($dir, $file_name)) {
                $has_otl = \false;
            }
            $directories[$dir] = \true;
            $config[$font_settings] = $file_name;
        }
        if (empty($config)) {
            return \false;
        } else {
            if ($has_otl) {
                $config['useOTL'] = 0xff;
                $config['useKashida'] = 75;
            }
            return ['dirs' => $directories, 'config' => $config];
        }
    }
    private function get_font_type($id)
    {
        $term_obj = get_the_terms($id, Fonts_Manager::TAXONOMY);
        if (\is_array($term_obj)) {
            $type_obj = \array_shift($term_obj);
            return $type_obj->slug;
        }
        return \false;
    }
    // Find suitable Elementor custom fonts and return an associative array with:
    // - 'fonts' : fontData as used by Mpdf.
    // - 'dirs' : fontDirs as used by Mpdf.
    public function get_fonts()
    {
        if (!\class_exists(Fonts_Manager::class)) {
            return ['dirs' => [], 'fonts' => []];
        }
        $fonts_cache = get_option(self::FONTS_CACHE_OPTION);
        if (\is_array($fonts_cache)) {
            return $fonts_cache;
        }
        $fonts = new \WP_Query(['post_type' => Fonts_Manager::CPT, 'posts_per_page' => -1]);
        $directories = [];
        $fonts_config = [];
        foreach ($fonts->posts as $font) {
            if ('custom' !== $this->get_font_type($font->ID)) {
                continue;
            }
            $font_name = \strtolower($font->post_title);
            $font_name = \str_replace(' ', '', $font_name);
            $res = $this->get_font($font->ID);
            if (\is_array($res)) {
                $directories += $res['dirs'];
                $fonts_config[$font_name] = $res['config'];
            }
        }
        $res = ['dirs' => \array_keys($directories), 'fonts' => $fonts_config];
        update_option(self::FONTS_CACHE_OPTION, $res);
        return $res;
    }
    private function get_form_data($text)
    {
        $lines = \explode("\n", $text);
        $data = [];
        foreach ($lines as $line) {
            if (!(\strpos($line, '|') > 0)) {
                continue;
            }
            list($name, $value) = \explode('|', $line);
            if (\strpos($value, ',') > 0) {
                $value = \explode(',', $value);
            }
            $data[$name] = $value;
        }
        return $data;
    }
    /**
     * @param integer $template_id
     */
    public function generate_pdf_from_template_id($template_id, $bindings, $return_string)
    {
        $post_data = get_post_meta($template_id, self::TEMPLATE_META_KEY, \true);
        if (!$post_data) {
            throw new \Error(esc_html__('PDF HTML: Could not fetch HTML Template, was it deleted?', 'dynamic-content-for-elementor'));
        }
        return $this->generate_pdf_from_html_template($post_data, $bindings, $return_string);
    }
    public function timber_expand($template, $var_bindings)
    {
        // We don't need timber templates inside files, and they can cause
        // permission problems, so remove them:
        $fixpaths = function ($paths) {
            return [];
        };
        add_filter('timber/loader/paths', $fixpaths);
        $context = \Timber\Timber::get_context();
        $context += $var_bindings;
        $context['post'] = new \Timber\Post();
        $context['current_user'] = new \Timber\User();
        \ob_start();
        \Timber\Timber::render_string($template, $context);
        remove_filter('timber/loader/paths', $fixpaths);
        return \ob_get_clean();
    }
    /**
     * @param array $post_data
     * @param array $var_bindings
     */
    private function generate_pdf_from_html_template($post_data, $var_bindings, $return_string = \false)
    {
        if (!($post_data[self::FIELD_IS_TEMPLATE] ?? \false)) {
            $code = $this->timber_expand($post_data[self::FIELD_CODE], $var_bindings);
        } else {
            $code = \Elementor\Plugin::instance()->frontend->get_builder_content($post_data[self::FIELD_TEMPLATE_ID], \true);
            if (!$code) {
                throw new \Error(esc_html__('PDF HTML: Could not fetch Elementor Template', 'dynamic-content-for-elementor'));
            }
        }
        $code = apply_filters('dynamicooo/html-pdf/html-template', $code);
        return $this->generate_pdf($code, $post_data[self::FIELD_FORMAT], $post_data[self::FIELD_ORIENTATION], $return_string);
    }
    private function generate_pdf_from_elementor_post($post_id, $format, $orientation, $return_string)
    {
        $code = \Elementor\Plugin::instance()->frontend->get_builder_content($post_id, \true);
        $this->generate_pdf($code, $format, $orientation, $return_string);
    }
    private function generate_pdf($code, $format, $orientation, $return_string)
    {
        $fonts = $this->get_fonts();
        $font_dirs = $fonts['dirs'];
        $font_data = $fonts['fonts'];
        $mpdf = new \DynamicOOOS\Mpdf\Mpdf(['tempDir' => \sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'mpdf', 'fontDir' => $font_dirs, 'fontdata' => $font_data, 'default_font' => 'ctimes', 'mono_fonts' => ['ccourier'], 'serif_fonts' => ['ctimes'], 'sans_fonts' => ['chelvetica'], 'format' => $format . '-' . $orientation]);
        $mpdf->WriteHTML($code);
        return $mpdf->Output(null, $return_string ? 'S' : null);
    }
    public function preview_pdf_html_template()
    {
        // Check if our nonce is set.
        if (!isset($_POST[self::CPT . '_nonce'])) {
            wp_send_json_error(['message' => 'Nonce missing']);
        }
        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST[self::CPT . '_nonce'], self::CPT)) {
            wp_send_json_error(['message' => 'Nonce Verification Error']);
        }
        if (!current_user_can('administrator')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        try {
            $post_data = stripslashes_deep($_POST);
            $raw_form_data = $this->get_form_data($post_data[self::FIELD_PREVIEW_FORM_DATA]);
            $form_data = \array_map(function ($value) {
                if (\is_array($value)) {
                    return \implode(', ', $value);
                }
                return $value;
            }, $raw_form_data);
            if ($post_data[self::FIELD_PREVIEW_POST] ?? \false) {
                \Elementor\Plugin::instance()->db->switch_to_post($post_data[self::FIELD_PREVIEW_POST]);
            }
            // set the global dce_form so that preview data works with the
            // widget Text Editor with tokens inside Elementor Templates:
            global $dce_form;
            $dce_form = $form_data;
            $bindings = ['form' => $form_data, 'form_raw' => $raw_form_data];
            $this->generate_pdf_from_html_template($post_data, $bindings);
        } catch (\DynamicOOOS\Mpdf\MpdfException $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
        die;
    }
    public function save_post_meta($post_id, $post, $update)
    {
        // If this is an autosave, our form has not been submitted,
        // so we don't want to do anything.
        if (\defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        // Check the user's permissions.
        if (!current_user_can('administrator', $post_id)) {
            return $post_id;
        }
        // Check if our nonce is set.
        if (!isset($_POST[self::CPT . '_nonce'])) {
            return $post_id;
        }
        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST[self::CPT . '_nonce'], self::CPT)) {
            return $post_id;
        }
        $this->save_meta($post_id, $_POST);
    }
    private function save_meta($post_id, $post_data)
    {
        if (!isset($post_data[self::FIELD_CODE])) {
            return;
        }
        $post_data = stripslashes_deep($post_data);
        $data = [self::FIELD_CODE => $post_data[self::FIELD_CODE] ?? '', self::FIELD_IS_TEMPLATE => $post_data[self::FIELD_IS_TEMPLATE] ?? '', self::FIELD_TEMPLATE_ID => $post_data[self::FIELD_TEMPLATE_ID] ?? '', self::FIELD_PREVIEW_FORM_DATA => $post_data[self::FIELD_PREVIEW_FORM_DATA] ?? '', self::FIELD_PREVIEW_POST => $post_data[self::FIELD_PREVIEW_POST] ?? '', self::FIELD_FORMAT => $post_data[self::FIELD_FORMAT] ?? '', self::FIELD_ORIENTATION => $post_data[self::FIELD_ORIENTATION] ?? ''];
        update_post_meta($post_id, self::TEMPLATE_META_KEY, $data);
    }
    public function get_attribute_string($attributes)
    {
        $attributes_array = [];
        foreach ($attributes as $name => $value) {
            $attributes_array[] = \sprintf('%s="%s"', $name, esc_attr($value));
        }
        return \implode(' ', $attributes_array);
    }
    public function render_code_metabox($post)
    {
        wp_enqueue_script('dce-pdf-html-template', DCE_URL . 'assets/js/pdf-html-templates.js', [], DCE_VERSION, \true);
        $data = get_post_meta($post->ID, self::TEMPLATE_META_KEY, \true);
        wp_nonce_field(self::CPT, self::CPT . '_nonce');
        $attr = ['name' => self::FIELD_IS_TEMPLATE, 'id' => self::FIELD_IS_TEMPLATE, 'type' => 'checkbox'];
        $checked = $data[self::FIELD_IS_TEMPLATE] ?? \false ? ' checked' : '';
        echo '<p><input ' . $this->get_attribute_string($attr) . $checked . '>';
        echo '<label for="' . self::FIELD_IS_TEMPLATE . '">' . __('Get the HTML from an Elementor Template (easier, but you have less control of the result).', 'dynamic-content-for-elementor') . '</label></p>';
        $template_id = $data[self::FIELD_TEMPLATE_ID] ?? \false;
        echo '<div id="dce-html-template-section">';
        echo '<p class="dce-pdf-template-notice">';
        echo __('Tokens inside the <b>Text Editor</b> or <b>Heading</b> widgets do not get evaluated. Please use the widget <b>Text Editor with Tokens</b> instead.', 'dynamic-content-for-elementor');
        echo '</p>';
        $label = __('Select the Elementor Template', 'dynamic-content-for-elementor');
        $this->render_select2(self::FIELD_TEMPLATE_ID, $template_id, $label);
        echo '</div>';
        $attr = ['name' => self::FIELD_CODE, 'id' => self::FIELD_CODE];
        $code = $data[self::FIELD_CODE] ?? self::DEFAULT_HTML_CODE;
        echo '<div id="dce-html-code-section">';
        if (!\class_exists('\\Timber\\Timber')) {
            $text = __('Notice: The free WordPress <a href="%s">Plugin Timber</a> is required for HTML Template to work.', 'dynamic-content-for-elementor');
            $text = \sprintf($text, 'https://wordpress.org/plugins/timber-library/');
            echo '<p class="dce-pdf-template-notice">' . $text . '</p>';
        }
        echo '<textarea ' . $this->get_attribute_string($attr) . ' >' . esc_textarea($code) . '</textarea>';
        echo '</div>';
        $this->enqueue_code_editor_scripts(self::FIELD_CODE);
    }
    public function render_preview_metabox($post)
    {
        $btn_url = admin_url('admin-ajax.php');
        $attr = ['data-action' => 'dce_preview_pdf_html_template', 'data-url' => $btn_url, 'id' => 'dce-preview-pdf', 'type' => 'button', 'class' => 'dce-pdf-preview-button'];
        echo '<button ' . $this->get_attribute_string($attr) . ' >' . __('Preview PDF', 'dynamic-content-for-elementor') . '</button>';
        echo '<div style="display: none" id="dce-preview-error" class="notice inline notice-alt notice-error"></div>';
    }
    // This function will only present the font options to the user.
    public function render_fonts_metabox($post)
    {
        echo '<p>' . __('The following fonts can be used with the <code>font-family</code> CSS property. ', 'dynamic-content-for-elementor') . '</p>';
        echo '<h4>' . __('Core Fonts', 'dynamic-content-for-elementor') . '</h4>';
        echo '<p>';
        echo __('The available core fonts are: ', 'dynamic-content-for-elementor');
        echo '<code>ctimes</code>, <code>chelvetica</code>, <code>ccourier</code>';
        echo '</p>';
        echo '<p>' . __('RTL languages: please notice that you cannot use the core fonts in a page that contains also an RTL language, like Arabic or Hebrew. Upload them as Custom Fonts if you need them.', 'dynamic-content-for-elementor') . '</p>';
        echo '<h4>' . __('Custom Fonts', 'dynamic-content-for-elementor') . '</h4>';
        $text = __('Custom Fonts can be added in the <a href="%s">Elementor Custom Fonts menu page</a>. Only the TTF type is supported. Weight can only be normal or bold, style can only be normal or italic. The following are the ones that were detected:', 'dynamic-content-for-elementor');
        $text = \sprintf($text, admin_url('edit.php?post_type=elementor_font'));
        echo '<p>' . $text . '</p>';
        $font_data = $this->get_fonts()['fonts'];
        echo '<ul>';
        foreach ($font_data as $font_name => $data) {
            echo '<li><code>' . $font_name . '</code>, ' . __('weight-style variants:', 'dynamic-content-for-elementor') . ' (';
            echo '<ul style="display: inline;">';
            foreach ($data as $config => $_) {
                if ('useOTL' === $config || 'useKashida' === $config) {
                    continue;
                }
                echo '<li style="display: inline;">';
                $bold = __('bold', 'dynamic-content-for-elementor');
                $normal = __('normal', 'dynamic-content-for-elementor');
                $italic = __('italic', 'dynamic-content-for-elementor');
                echo \strpos($config, 'B') !== \false ? $bold : $normal;
                echo '-';
                echo \strpos($config, 'I') !== \false ? $italic : $normal;
            }
            echo '</ul>';
            echo ')';
        }
        echo '</ul>';
    }
    public function render_images_metabox($post)
    {
        $media_url = get_admin_url() . '/upload.php';
        echo '<p>' . __('To insert an image, first go to the ', 'dynamic-content-for-elementor');
        echo "<a href='{$media_url}'>" . __('WordPress Media Library', 'dynamic-content-for-elementor') . '</a>';
        echo __(', select an image and find its ID. Then you can use the image like this:', 'dynamic-content-for-elementor') . '</p>';
        echo '<code>&lt;img src="{{ Image( &lt;ID&gt; ).file_loc }}"&gt;</code>';
        echo '<p>' . __('Notice how we used <code>.file_loc</code> , which is a file system path, instead of a URL. Avoid image URLs as they will be slow to fetch.', 'dynamic-content-for-elementor') . '</p>';
        echo '<p>' . __('To insert a signature you can use:', 'dynamic-content-for-elementor') . '</p>';
        echo '<code>&lt;img src="{{ form_raw.signature_field_id }}"&gt;</code>';
    }
    // Render a select2 input where $id is its id, and $post_id is the
    // preselected post id.
    public function render_select2($id, $post_id, $label)
    {
        // do not forget about WP Nonces for security purposes
        $attr = ['name' => $id, 'id' => $id, 'style' => 'width:99%;max-width:25em;'];
        if ($post_id) {
            $title = wp_kses_post(get_the_title($post_id));
            $title = \mb_strlen($title) > 50 ? \mb_substr($title, 0, 49) . '...' : $title;
        }
        echo '<p><label for="' . $attr['id'] . '">' . $label . '</label><br />';
        echo '<select ' . $this->get_attribute_string($attr) . '>';
        if ($post_id) {
            echo '<option value="' . esc_attr($post_id) . '">' . esc_html($title) . '</option>';
        }
        echo '</select></p>';
    }
    public function render_preview_data_metabox($post)
    {
        wp_enqueue_script('dce-pdf-html-template', DCE_URL . 'assets/js/pdf-html-templates.js', [], DCE_VERSION, \true);
        $data = get_post_meta($post->ID, self::TEMPLATE_META_KEY, \true);
        $post_id = $data[self::FIELD_PREVIEW_POST] ?? \false;
        $label = __('Select a Post to get things like Post Title, ACF fields etc. for the preview (can leave empty if these are not used)', 'dynamic-content-for-elementor');
        $this->render_select2(self::FIELD_PREVIEW_POST, $post_id, $label);
        $attr = ['name' => self::FIELD_PREVIEW_FORM_DATA, 'id' => self::FIELD_PREVIEW_FORM_DATA, 'style' => 'width: 100%; height: 10em;'];
        $form_data = $data[self::FIELD_PREVIEW_FORM_DATA] ?? self::DEFAULT_PREVIEW_DATA;
        echo '<p><label for="' . $attr['id'] . '">' . __('Here you can insert form data so that you can see them in the preview. The name of the field is followed by a | and then by its value. For fields that allow multiple selection like Checkbox you can separate the selected values by a comma.', 'dynamic-content-for-elementor') . '</label>';
        echo '<textarea ' . $this->get_attribute_string($attr) . ' >' . esc_textarea($form_data) . '</textarea></p>';
    }
    public function render_dimensions_metabox($post)
    {
        $data = get_post_meta($post->ID, self::TEMPLATE_META_KEY, \true);
        $format_attr = ['name' => self::FIELD_FORMAT, 'id' => self::FIELD_FORMAT];
        $orientation_attr = ['name' => self::FIELD_ORIENTATION, 'type' => 'radio'];
        $selected_format = $data[self::FIELD_FORMAT] ?? 'A4';
        $selected_orientation = $data[self::FIELD_ORIENTATION] ?? 'P';
        $formats = ['A4', 'A5', 'A6', 'Letter', 'Legal', 'Executive', 'Folio'];
        echo '<select ' . $this->get_attribute_string($format_attr) . ' >';
        foreach ($formats as $format) {
            if ($format === $selected_format) {
                echo '<option selected>';
            } else {
                echo '<option>';
            }
            echo $format . '</option>';
        }
        echo '</select>';
        echo '<p>';
        echo '<label for="portrait">' . __('Portrait', 'dynamic-content-for-elementor') . '</label>';
        $checked = $selected_orientation === 'P' ? ' checked ' : '';
        echo '<input value="P" ' . $this->get_attribute_string($orientation_attr) . $checked . '>';
        echo '<label for="landscape">' . __('Landscape', 'dynamic-content-for-elementor') . '</label>';
        $checked = $selected_orientation === 'L' ? ' checked ' : '';
        echo '<input value="L" ' . $this->get_attribute_string($orientation_attr) . $checked . '>';
        echo '</p>';
    }
    private function get_code_editor_settings()
    {
        // TODO: Handle `enqueue_code_editor_scripts` to work with `lint => 'true'`.
        return ['type' => 'text/html', 'codemirror' => ['indentUnit' => 2, 'tabSize' => 2, 'mode' => ['name' => 'twig', 'base' => 'text/html']]];
    }
    private function enqueue_code_editor_scripts($field_code_id)
    {
        wp_enqueue_script('htmlhint');
        wp_enqueue_script('csslint');
        wp_add_inline_script(
            // fix as described here: https://make.wordpress.org/core/2017/10/22/code-editing-improvements-in-wordpress-4-9/
            'wp-codemirror',
            'window.CodeMirror = wp.CodeMirror;'
        );
        wp_enqueue_script('codemirror-twig', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.29.0/mode/twig/twig.min.js', ['wp-codemirror'], DCE_VERSION, \false);
        /**
         * Some of the plugins may load 'code-editor' for their needs and change the default behavior, so it should
         * re-initialize the code editor with 'custom code' settings.
         */
        if (wp_script_is('code-editor')) {
            wp_add_inline_script('code-editor', \sprintf('wp.codeEditor.initialize( jQuery( "#%s"), %s );', $field_code_id, wp_json_encode(wp_get_code_editor_settings($this->get_code_editor_settings()))));
        } else {
            wp_enqueue_code_editor($this->get_code_editor_settings());
            wp_add_inline_script('code-editor', \sprintf('wp.codeEditor.initialize( jQuery( "#%s") );', $field_code_id));
        }
    }
    public function add_meta_boxes()
    {
        add_meta_box('elementor-pdf-html-code-metabox', __('HTML', 'dynamic-content-for-elementor'), [$this, 'render_code_metabox'], self::CPT, 'normal', 'default');
        add_meta_box('elementor-pdf-html-preview-metabox', __('Preview', 'dynamic-content-for-elementor'), [$this, 'render_preview_metabox'], self::CPT, 'side', 'default');
        add_meta_box('elementor-pdf-html-dimensions-metabox', __('Dimensions', 'dynamic-content-for-elementor'), [$this, 'render_dimensions_metabox'], self::CPT, 'normal', 'default');
        add_meta_box('elementor-pdf-html-fonts-metabox', __('Fonts', 'dynamic-content-for-elementor'), [$this, 'render_fonts_metabox'], self::CPT, 'normal', 'default');
        add_meta_box('elementor-pdf-html-preview-data-metabox', __('Preview Data', 'dynamic-content-for-elementor'), [$this, 'render_preview_data_metabox'], self::CPT, 'normal', 'default');
        add_meta_box('elementor-pdf-html-image-metabox', __('Inserting Images', 'dynamic-content-for-elementor'), [$this, 'render_images_metabox'], self::CPT, 'side', 'default');
    }
}
