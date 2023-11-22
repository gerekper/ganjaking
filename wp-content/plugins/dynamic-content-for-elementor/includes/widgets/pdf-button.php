<?php

namespace DynamicContentForElementor\Widgets;

use ElementorPro\Modules\QueryControl\Module as QueryModule;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class PdfButton extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-pdf-button'];
    }
    public function get_style_depends()
    {
        return [];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_dce_pdf', ['label' => __('PDF', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_pdf_button_converter', ['label' => __('Converter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'description' => __('The JS converter is the most accurate, but better used for short content, ideally fitting in only one page. Use the other converters for long text spanning multiple pages.', 'dynamic-content-for-elementor'), 'options' => ['js' => 'JS', 'html' => 'HTML', 'browser' => 'Browser', 'dompdf' => esc_html__('DomPDF (deprecated)', 'dynamic-content-for-elementor'), 'tcpdf' => esc_html__('TCPDF (deprecated)', 'dynamic-content-for-elementor')], 'condition' => ['dce_pdf_rtl' => ''], 'toggle' => \false, 'default' => 'js']);
        $warning = __('The JS converter uses the html2canvas library, which is often not reliable. We provide it hoping that it will be useful but unfortunately we cannot fix the problems of the underlying library. If you need more reliability you should use the HTML converter with hand written HTML code.', 'dynamic-content-for-elementor');
        $this->add_control('dce_pdf_js_warning', ['type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => $warning, 'separator' => 'before', 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['dce_pdf_button_converter' => ['js']]]);
        $this->add_control('dce_pdf_rtl_button_converter', ['label' => __('Converter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['browser' => 'Browser', 'tcpdf' => 'TCPDF'], 'condition' => ['dce_pdf_rtl!' => ''], 'toggle' => \false, 'default' => 'tcpdf']);
        $this->add_control('dce_pdf_button_preview', ['label' => __('Open in Browser', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Open the PDF in the browser instead of downloading it. The pdf title will not be used.', 'dynamic-content-for-elementor'), 'label_block' => \true]);
        $this->add_control('dce_pdf_button_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '[post:name]', 'description' => __('The PDF file name, the .pdf extension will automatically added', 'dynamic-content-for-elementor'), 'label_block' => \true]);
        $this->add_control('html_converter_get_template_from', ['label' => __('Get Template from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['post' => __('Current Post', 'dynamic-content-for-elementor'), 'html_template' => __('HTML Template', 'dynamic-content-for-elementor')], 'condition' => ['dce_pdf_button_converter' => 'html'], 'toggle' => \false, 'default' => 'post']);
        $this->add_control('dce_pdf_button_body', ['label' => __('Body', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['post' => ['title' => __('Current Post', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'condition' => ['dce_pdf_button_converter!' => 'html'], 'toggle' => \false, 'default' => 'post']);
        $this->add_control('dce_pdf_button_container', ['label' => __('HTML Container', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'body', 'placeholder' => __('body', 'dynamic-content-for-elementor'), 'label_block' => \true, 'description' => __('Use jQuery selector to identify the content for this PDF', 'dynamic-content-for-elementor'), 'condition' => ['dce_pdf_button_body' => 'post', 'dce_pdf_button_converter!' => 'html']]);
        $this->add_control('dce_pdf_button_template', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => __('Use an Elementor Template as body for this PDF.', 'dynamic-content-for-elementor'), 'condition' => ['dce_pdf_button_body' => 'template']]);
        $paper_sizes = \array_keys(@\DynamicOOOS\Dompdf\Adapter\CPDF::$PAPER_SIZES);
        $tmp = array();
        foreach ($paper_sizes as $asize) {
            $tmp[$asize] = \strtoupper($asize);
        }
        $paper_sizes = $tmp;
        $this->add_control('dce_pdf_button_size', ['label' => __('Page Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'a4', 'options' => $paper_sizes, 'condition' => ['dce_pdf_button_converter' => ['dompdf', 'tcpdf']]]);
        // page sizes supported by jsPDF
        $js_paper_sizes = [];
        // a/b/c0 - a/b/c10 formats
        for ($i = 0; $i <= 10; $i++) {
            $js_paper_sizes['a' . $i] = 'A' . $i;
            $js_paper_sizes['b' . $i] = 'B' . $i;
            $js_paper_sizes['c' . $i] = 'C' . $i;
        }
        $js_paper_sizes += ['dl' => 'dl', 'letter' => 'letter', 'government-letter' => 'government-letter', 'legal' => 'legal', 'junior-legal' => 'junior-legal', 'ledger' => 'ledger', 'tabloid' => 'tabloid', 'credit-card' => 'credit-card'];
        $this->add_control('dce_pdf_button_size_js', ['label' => __('Page Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'a4', 'options' => $js_paper_sizes, 'condition' => ['dce_pdf_button_converter' => 'js']]);
        $html_formats = ['A4', 'A5', 'A6', 'Letter', 'Legal', 'Executive', 'Folio'];
        $html_format_options = [];
        // TODO: format translations
        foreach ($html_formats as $f) {
            $html_format_options[$f] = $f;
        }
        $this->add_control('dce_pdf_button_html_conv_format', ['label' => __('Page Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'A4', 'options' => $html_format_options, 'condition' => ['dce_pdf_button_converter' => 'html', 'html_converter_get_template_from!' => 'html_template']]);
        $this->add_control('dce_pdf_button_orientation', ['label' => __('Page Orientation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['portrait' => __('Portrait', 'dynamic-content-for-elementor'), 'landscape' => __('Landscape', 'dynamic-content-for-elementor')], 'toggle' => \false, 'default' => 'portrait', 'conditions' => ['relation' => 'or', 'terms' => [['terms' => [['name' => 'dce_pdf_button_converter', 'operator' => '!=', 'value' => 'browser'], ['name' => 'dce_pdf_button_converter', 'operator' => '!=', 'value' => 'html']]], ['terms' => [['name' => 'dce_pdf_button_converter', 'operator' => '==', 'value' => 'html'], ['name' => 'html_converter_get_template_from', 'operator' => '!=', 'value' => 'html_template']]]]]]);
        if (\class_exists(QueryModule::class)) {
            $this->add_control('html_converter_html_template', ['label' => __('HTML Template', 'dynamic-content-for-elementor'), 'type' => QueryModule::QUERY_CONTROL_ID, 'options' => [], 'label_block' => \true, 'autocomplete' => ['object' => QueryModule::QUERY_OBJECT_POST, 'display' => 'detailed', 'query' => ['post_type' => \DynamicContentForElementor\PdfHtmlTemplates::CPT]], 'condition' => ['dce_pdf_button_converter' => 'html', 'html_converter_get_template_from' => 'html_template']]);
        } else {
            $this->add_control('html_converter_html_template', [
                'label' => __('HTML Template', 'dynamic-content-for-elementor'),
                'type' => 'ooo_query',
                // 'placeholder' => __( '', 'dynamic-content-for-elementor' ),
                'label_block' => \true,
                'query_type' => 'posts',
                'dynamic' => ['active' => \false],
                'object_type' => \DynamicContentForElementor\PdfHtmlTemplates::CPT,
                'condition' => ['dce_pdf_button_converter' => 'html', 'html_converter_get_template_from' => 'html_template'],
            ]);
        }
        $this->add_control('dce_pdf_button_margin', ['label' => __('Page Margins', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'default' => ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px', 'isLinked' => \true], 'size_units' => ['px', '%', 'em'], 'condition' => ['dce_pdf_button_converter' => ['dompdf', 'tcpdf']]]);
        $this->add_control('dce_pdf_button_margins_js', ['label' => __('Page Margins', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'mm', 'isLinked' => \true], 'size_units' => ['pt', 'mm', 'in', 'px'], 'condition' => ['dce_pdf_button_converter' => ['js']]]);
        $this->add_control('dce_pdf_button_dpi', ['label' => __('DPI', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '96', 'options' => ['72' => '72', '96' => '96', '150' => '150', '200' => '200', '240' => '240', '300' => '300'], 'condition' => ['dce_pdf_button_converter' => 'dompdf']]);
        $this->add_control('dce_pdf_button_styles', ['label' => __('Use Styles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['unstyled' => __('No Style', 'dynamic-content-for-elementor'), 'only-elementor' => __('Only Elementor', 'dynamic-content-for-elementor'), 'all' => __('Elementor & Theme', 'dynamic-content-for-elementor')], 'toggle' => \false, 'default' => 'only-elementor', 'condition' => ['dce_pdf_button_converter' => ['dompdf', 'tcpdf']]]);
        $this->add_control('dce_pdf_rtl', ['label' => __('RTL Language', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Right-to-left languages are written and read from right-to-left', 'dynamic-content-for-elementor'), 'default' => '', 'condition' => ['dce_pdf_button_converter' => ['dompdf', 'tcpdf']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_button', ['label' => __('Button', 'dynamic-content-for-elementor')]);
        $this->add_control('button_type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), 'info' => __('Info', 'dynamic-content-for-elementor'), 'success' => __('Success', 'dynamic-content-for-elementor'), 'warning' => __('Warning', 'dynamic-content-for-elementor'), 'danger' => __('Danger', 'dynamic-content-for-elementor')], 'prefix_class' => 'elementor-button-']);
        $this->add_control('text', ['label' => __('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'default' => __('Download PDF', 'dynamic-content-for-elementor'), 'placeholder' => __('Download PDF', 'dynamic-content-for-elementor')]);
        $this->add_control('link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        // obsolete controls for old converters
        $this->add_control('is_external', ['label' => __('Open in a new window', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN]);
        $this->add_control('download', ['label' => __('Force Download', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'separator' => 'after']);
        $this->add_control('nofollow', ['label' => __('Add nofollow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'prefix_class' => 'elementor%s-align-', 'default' => '']);
        $this->add_control('size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'style_transfer' => \true]);
        $this->add_control('selected_icon', ['label' => __('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'label_block' => \true, 'fa4compatibility' => 'icon']);
        $this->add_control('icon_align', ['label' => __('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => __('Before', 'dynamic-content-for-elementor'), 'right' => __('After', 'dynamic-content-for-elementor')], 'condition' => ['selected_icon[value]!' => '']]);
        $this->add_control('icon_indent', ['label' => __('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('icon_size', ['label' => __('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 10, 'max' => 60]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_control('view', ['label' => __('View', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => 'traditional']);
        $this->add_control('button_css_id', ['label' => __('Button ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'default' => '', 'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id', 'dynamic-content-for-elementor'), 'label_block' => \false, 'description' => __('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'dynamic-content-for-elementor'), 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->start_controls_tabs('tabs_button_style');
        $this->start_controls_tab('tab_button_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('button_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};', '{{WRAPPER}} a.elementor-button:hover svg, {{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} a.elementor-button:focus svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};']]);
        $this->add_control('button_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};']]);
        $this->add_control('hover_animation', ['label' => __('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border', 'selector' => '{{WRAPPER}} .elementor-button', 'separator' => 'before']);
        $this->add_control('border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'button_box_shadow', 'selector' => '{{WRAPPER}} .elementor-button']);
        $this->add_responsive_control('text_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
    }
    /**
     * Provides settings for the js converter with better key names and defaults.
     */
    public static function polish_settings($settings)
    {
        $defaults = ['converter' => 'js', 'source_type' => 'post', 'page_size' => 'a4', 'orientation' => 'portrait', 'margins' => ['unit' => 'mm', 'top' => 0, 'bottom' => 0, 'left' => 0, 'up' => 0, 'right' => 0, 'isLinked' => \true], 'title' => \time(), 'selector' => 'body', 'template_id' => 0, 'preview' => 'no'];
        $tr = ['dce_pdf_button_size_js' => 'page_size', 'dce_pdf_button_body' => 'source_type', 'dce_pdf_button_template' => 'template_id', 'dce_pdf_button_container' => 'selector', 'dce_pdf_button_title' => 'title', 'dce_pdf_button_converter' => 'converter', 'dce_pdf_button_orientation' => 'orientation', 'dce_pdf_button_margins_js' => 'margins', 'dce_pdf_button_preview' => 'preview'];
        $new = [];
        // Filter out unneeded settings and those whose value is ''.
        // Also give the keys a shorter name using the $tr array.
        foreach ($tr as $kfrom => $kto) {
            if (isset($settings[$kfrom]) && $settings[$kfrom] != '') {
                $new[$kto] = $settings[$kfrom];
            }
        }
        $polished = $new + $defaults;
        // Set default margins.
        foreach ($polished['margins'] as $key => $val) {
            if (!$val) {
                $polished['margins'][$key] = $defaults['margins'][$key];
            }
        }
        return $polished;
    }
    /**
     * wp_footer action: Echoes the script that defines the function downloadPDF.
     */
    public function browserconv_insert_scripts()
    {
        $psettings = self::polish_settings($this->get_settings_for_display());
        $selector = $psettings['selector'];
        // If the source is a template we get its html code and add it to the
        // page, so that it can be rendered directly here.
        if ($psettings['source_type'] === 'template') {
            $tbody = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($psettings['template_id'], \true);
            if (!$tbody) {
                echo __('Error: could not fetch the template for generating the pdf', 'dynamic-content-for-elementor');
                return;
            }
            $selector = '#dce-pdftemplate';
            echo \preg_replace('/<div/', '<div id="dce-pdftemplate"', $tbody, 1);
        }
        $selector = wp_json_encode($selector);
        $is_template = $psettings['source_type'] === 'template' ? 'true' : 'false';
        $script = <<<EOD
<script>
 (function(\$) {
   isTemplate = {$is_template}
   let el
   if ( isTemplate ) {
\t el = \$({$selector});
\t el.remove();
   }
   window.downloadPDF = function() {
\t if ( ! isTemplate ) {
\t   el = \$({$selector});
\t }
\t var restorepage = \$('body').html();
\t var printcontent = el.clone();
\t \$('body').empty().html(printcontent);
\t debugger;
\t window.print();
\t \$('body').html(restorepage);
\t return false;
   }
 })(jQuery)
</script>
EOD;
        echo $script;
    }
    /**
     * wp_footer action: Echoes the js converter scripts that will immediately
     * render the pdf and trigger the download.
     */
    public function jsconv_enqueue_scripts()
    {
        $psettings = self::polish_settings($this->get_settings_for_display());
        $selector = $psettings['selector'];
        // If the source is a template we get its html code and add it to the
        // page, so that it can be rendered directly here.
        if ($psettings['source_type'] === 'template') {
            $tbody = \Elementor\Plugin::$instance->frontend->get_builder_content($psettings['template_id']);
            if (!$tbody) {
                echo __('PDF Button: Could not fetch template. Make sure it exists.', 'dynamic-content-for-elementor');
                return;
            }
            $selector = '#dce-pdftemplate';
            echo \preg_replace('/<div/', '<div id="dce-pdftemplate"', $tbody, 1);
        }
        $loc = ['isTemplate' => $psettings['source_type'] === 'template' ? \true : \false, 'marginLeft' => $psettings['margins']['left'], 'marginTop' => $psettings['margins']['top'], 'marginRight' => $psettings['margins']['right'], 'marginBottom' => $psettings['margins']['bottom'], 'marginUnit' => $psettings['margins']['unit'], 'pageSize' => $psettings['page_size'], 'orientation' => $psettings['orientation'], 'selector' => $selector, 'title' => $psettings['title'], 'preview' => $psettings['preview']];
        wp_enqueue_script('dce-pdf-jsconv');
        wp_localize_script('dce-pdf-jsconv', 'jsconv', $loc);
    }
    /**
     * Return the js code that reload the page with the added parameter that
     * triggers the pdf download.
     */
    protected function jsconv_button_onclick()
    {
        return <<<EOD
var url = window.location.href;
var elid = "{$this->get_id()}";
if (url.indexOf('?') > -1){
\turl += '&downloadPDF=' + elid;
}else{
   url += '?downloadPDF=' + elid;
}
var a = document.createElement('a');
a.href = url;
document.body.appendChild(a);
a.click();
// prevents defaults action:
return false;
EOD;
    }
    /**
     * Render button widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        $converter = $settings['dce_pdf_button_converter'];
        if ($converter === 'html') {
            $post_id = Helper::get_current_post_id();
            $el_id = $this->get_id();
            $queried_id = get_the_ID();
            $this->add_render_attribute('button', 'data-title', $settings['dce_pdf_button_title']);
            $this->add_render_attribute('button', 'data-preview', $settings['dce_pdf_button_preview']);
            $this->add_render_attribute('button', 'data-converter', 'html');
            $this->add_render_attribute('button', 'data-post-id', (string) $post_id);
            $ajax_url = admin_url('admin-ajax.php');
            $this->add_render_attribute('button', 'data-ajax-url', $ajax_url);
            $ajax_action = 'dce_pdf_button';
            $this->add_render_attribute('button', 'data-ajax-action', $ajax_action);
            if ($queried_id !== \false) {
                $this->add_render_attribute('button', 'data-queried-id', \strval($queried_id));
            }
            $this->add_render_attribute('button', 'data-element-id', $el_id);
            $this->add_render_attribute('button', 'style', 'cursor: pointer;');
        } elseif ($converter === 'js') {
            $this->add_render_attribute('button', 'onclick', esc_attr($this->jsconv_button_onclick()));
            $this->add_render_attribute('button', 'style', 'cursor: pointer;');
            $download_id = isset($_GET['downloadPDF']) ? sanitize_text_field($_GET['downloadPDF']) : 0;
            if ($download_id === $this->get_id()) {
                add_action('elementor/frontend/after_enqueue_scripts', [$this, 'jsconv_enqueue_scripts'], 1000, 0);
            }
        } elseif ($settings['dce_pdf_button_converter'] == 'browser') {
            $this->add_render_attribute('button', 'onclick', 'downloadPDF()');
            $this->add_render_attribute('button', 'href', '#');
            $this->browserconv_insert_scripts();
        } else {
            $pdf_url = DCE_URL . 'assets/pdf.php';
            $pdf_url .= '?post_id=' . get_the_ID();
            $pdf_url .= '&element_id=' . $this->get_id();
            $this->add_render_attribute('button', 'href', $pdf_url);
            if ($settings['is_external']) {
                $this->add_render_attribute('button', 'target', '_blank');
            }
            if ($settings['nofollow']) {
                $this->add_render_attribute('button', 'rel', 'nofollow');
            }
            if ($settings['download']) {
                $this->add_render_attribute('button', 'download', '');
            }
        }
        $this->add_render_attribute('wrapper', 'class', 'elementor-button-wrapper');
        $this->add_render_attribute('wrapper', 'class', 'elementor-button-pdf-wrapper');
        $this->add_render_attribute('button', 'class', 'elementor-button-link');
        $this->add_render_attribute('button', 'class', 'elementor-button');
        $this->add_render_attribute('button', 'role', 'button');
        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button', 'id', $settings['button_css_id']);
        }
        if (!empty($settings['size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['size']);
        }
        if ($settings['hover_animation']) {
            $this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('wrapper');
        ?>>
			<a <?php 
        echo $this->get_render_attribute_string('button');
        ?>>
				<?php 
        $this->render_text();
        ?>
			</a>
		</div>
		<?php 
    }
    public function get_settings_for_display($setting_key = null)
    {
        if ($setting_key !== null) {
            $setting = parent::get_settings_for_display($setting_key);
            if ($setting_key === 'dce_pdf_button_title') {
                $setting = Helper::get_dynamic_value($setting);
            }
            return $setting;
        }
        $settings = parent::get_settings_for_display();
        $settings['dce_pdf_button_title'] = Helper::get_dynamic_value($settings['dce_pdf_button_title']);
        return $settings;
    }
    /**
     * Render button text.
     *
     * Render button widget text.
     *
     * @since 1.5.0
     * @access protected
     */
    protected function render_text()
    {
        $settings = $this->get_settings_for_display();
        $migrated = isset($settings['__fa4_migrated']['selected_icon']);
        $is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();
        if (!$is_new && empty($settings['icon_align'])) {
            // @todo: remove when deprecated
            // added as bc in 2.6
            //old default
            $settings['icon_align'] = $this->get_settings('icon_align');
        }
        $this->add_render_attribute(['content-wrapper' => ['class' => ['elementor-button-content-wrapper', 'dce-flex']], 'icon-align' => ['class' => ['elementor-button-icon', 'elementor-align-icon-' . $settings['icon_align']]], 'text' => ['class' => 'elementor-button-text']]);
        $this->add_inline_editing_attributes('text', 'none');
        ?>
		<span <?php 
        echo $this->get_render_attribute_string('content-wrapper');
        ?>>
		<?php 
        if (!empty($settings['icon']) || !empty($settings['selected_icon']['value'])) {
            ?>
			<span <?php 
            echo $this->get_render_attribute_string('icon-align');
            ?>>
					<?php 
            if ($is_new || $migrated) {
                Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
            } else {
                ?>
				<i class="<?php 
                echo esc_attr($settings['icon']);
                ?>" aria-hidden="true"></i>
			<?php 
            }
            ?>
		</span>
		<?php 
        }
        ?>
		<span <?php 
        echo $this->get_render_attribute_string('text');
        ?>><?php 
        echo $settings['text'];
        ?></span>
		</span>
		<?php 
    }
    public function on_import($element)
    {
        return Icons_Manager::on_import_migration($element, 'icon', 'selected_icon');
    }
}
