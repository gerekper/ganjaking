<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class QrAndBarcodes extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $types1d = ['C39', 'C39+', 'C39E', 'C39E+', 'C93', 'S25', 'S25+', 'I25', 'I25+', 'C128', 'C128A', 'C128B', 'C128C', 'EAN2', 'EAN5', 'EAN8', 'EAN13', 'UPCA', 'UPCE', 'MSI', 'MSI+', 'POSTNET', 'PLANET', 'RMS4CC', 'KIX', 'IMB', 'IMBPRE', 'CODABAR', 'CODE11', 'PHARMA', 'PHARMA2T'];
        $types2d = ['DATAMATRIX', 'PDF417', 'QRCODE', 'RAW', 'RAW2', 'TEST'];
        $this->start_controls_section('section_barcode', ['label' => __('Code', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_barcode_dimension', ['label' => __('Dimension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1d' => ['title' => __('1D', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-barcode'], '2d' => ['title' => __('2D', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-qrcode']], 'label_block' => \true, 'default' => '2d', 'toggle' => \false]);
        $types1d_options = [];
        foreach ($types1d as $key => $value) {
            $types1d_options[$value] = $value;
        }
        $this->add_control('dce_barcode_1d_type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $types1d_options, 'default' => 'EAN13', 'condition' => ['dce_barcode_dimension' => '1d']]);
        $types2d_options = array();
        foreach ($types2d as $key => $value) {
            $types2d_options[$value] = $value;
        }
        $this->add_control('dce_barcode_2d_type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $types2d_options, 'default' => 'QRCODE', 'condition' => ['dce_barcode_dimension' => '2d']]);
        $this->add_control('dce_barcode_type_options', ['label' => __('PDF417 Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'xx,yy,zz', 'condition' => ['dce_barcode_dimension' => '2d', 'dce_barcode_2d_type' => 'PDF417']]);
        $this->add_control('dce_barcode_type_qr', ['label' => __('QR Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => array('L' => 'L', 'M' => 'M', 'Q' => 'Q', 'H' => 'H'), 'default' => 'L', 'condition' => ['dce_barcode_dimension' => '2d', 'dce_barcode_2d_type' => 'QRCODE']]);
        $this->add_control('dce_barcode_code', ['label' => __('Code', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => get_bloginfo('url')]);
        $this->add_control('dce_barcode_render', ['label' => __('Render as', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['SVGcode' => 'SVG', 'PngData' => 'PNG', 'HTML' => 'HTML'], 'default' => 'PngData', 'toggle' => \false]);
        $this->add_control('dce_barcode_cols', ['label' => __('Cols', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 1]);
        $this->add_control('dce_barcode_rows', ['label' => __('Rows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 1]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_code', ['label' => __('Code', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_control('dce_code_color', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-barcode-svg #elements' => 'fill: {{VALUE}} !important;', '{{WRAPPER}} .dce-barcode-html > div' => 'background-color: {{VALUE}} !important;']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_barcode_render' => ['PngData', 'SVGcode']]]);
        $this->add_responsive_control('width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vw' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-barcode' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('space', ['label' => __('Max Width', 'dynamic-content-for-elementor') . ' (%)', 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-barcode' => 'max-width: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'selector' => '{{WRAPPER}} .dce-barcode', 'separator' => 'before']);
        $this->add_responsive_control('image_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-barcode' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'exclude' => ['box_shadow_position'], 'selector' => '{{WRAPPER}} .dce-barcode']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['dce_barcode_code'])) {
            return;
        }
        $code = $settings['dce_barcode_code'];
        switch ($settings['dce_barcode_dimension']) {
            case '1d':
                $type = $settings['dce_barcode_1d_type'];
                $barcode = new \DynamicOOOS\TCPDFBarcode($code, $type);
                break;
            case '2d':
                $type = $settings['dce_barcode_2d_type'];
                if ('QRCODE' === $type) {
                    $type .= ',' . $settings['dce_barcode_type_qr'];
                }
                if ('PDF417' === $type) {
                    $type .= ',' . $settings['dce_barcode_type_options'];
                }
                $barcode = new \DynamicOOOS\TCPDF2DBarcode($code, $type);
                break;
        }
        if (!isset($barcode)) {
            return;
        }
        if ($settings['dce_barcode_render']) {
            $render = 'getBarcode' . $settings['dce_barcode_render'];
            $cols = $settings['dce_barcode_cols'] ?? null;
            $rows = $settings['dce_barcode_rows'] ?? null;
            $color = 'black';
            if ($settings['dce_code_color']) {
                $color = $settings['dce_code_color'];
            }
            if ($settings['dce_barcode_render'] == 'PngData') {
                if ($settings['dce_code_color']) {
                    $color = \sscanf($settings['dce_code_color'], '#%02x%02x%02x');
                } else {
                    $color = [0, 0, 0];
                }
            }
            if ($cols) {
                if ($rows) {
                    $result = $barcode->{$render}($cols, $rows, $color);
                } else {
                    $result = $barcode->{$render}($cols, 10, $color);
                }
            } else {
                $result = $barcode->{$render}(10, 10, $color);
            }
            if ('PngData' === $settings['dce_barcode_render']) {
                $result = '<img class="dce-barcode dce-barcode-png" src="data:image/png;base64,' . \base64_encode($result) . '">';
            } elseif ('SVGcode' === $settings['dce_barcode_render']) {
                $result = \str_replace('<svg ', '<svg class="dce-barcode dce-barcode-svg" ', $result);
            } elseif ('HTML' === $settings['dce_barcode_render']) {
                $result = '<div class="dce-barcode dce-barcode-html" ' . \substr($result, 5);
            }
            echo $result;
        }
    }
}
