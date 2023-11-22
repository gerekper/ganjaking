<?php

namespace DynamicContentForElementor\Widgets;

use DynamicContentForElementor\Helper;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class ThreesixtySlider extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-threesixtyslider-lib', 'dce-360-slider'];
    }
    public function get_style_depends()
    {
        return ['dce-threesixtySlider'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_threesixtyslider', ['label' => $this->get_title()]);
        $this->add_responsive_control('width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 400, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['px' => ['min' => 0, 'max' => 2000], '%' => ['min' => 10, 'max' => 100]], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-threesixty' => 'max-width: {{SIZE}}{{UNIT}};'], 'frontend_available' => \true]);
        $pathimg = plugins_url('/assets/lib/threesixty-slider/imagesCube/', DCE__FILE__);
        $pathimg = \str_replace(get_home_url(), '', $pathimg);
        $this->add_control('pathimages', ['label' => __('Images path', 'dynamic-content-for-elementor'), 'label_block' => \true, 'description' => __('The absolute path from root folder of the images for the 360.<br>The images in the folder must be called with the sequential number (e.g.:1.png, or 1.svg or 1.jpg. So 2.jpg, 3.jpg, 4.jpg etc.)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => $pathimg, 'placeholder' => '/wp-content/uploads/360/', 'frontend_available' => \true]);
        $this->add_control('navigation', ['label' => __('Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('disable_spin', ['label' => __('Disable the initial spin on load', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('play_speed', ['label' => __('Play Speed (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 100, 'unit' => 'ms'], 'size_units' => ['ms'], 'range' => ['ms' => ['min' => 100, 'max' => 1000, 'step' => 100]], 'render_type' => 'template', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('navigation_position', ['label' => __('Navigation Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['top-left' => __('Top Left', 'dynamic-content-for-elementor'), 'top-center' => __('Top Center', 'dynamic-content-for-elementor'), 'top-right' => __('Top Right', 'dynamic-content-for-elementor'), 'center-left' => __('Center Left', 'dynamic-content-for-elementor'), 'center-center' => __('Center Center', 'dynamic-content-for-elementor'), 'center-right' => __('Center Right', 'dynamic-content-for-elementor'), 'bottom-left' => __('Bottom Left', 'dynamic-content-for-elementor'), 'bottom-center' => __('Bottom Center', 'dynamic-content-for-elementor'), 'bottom-right' => __('Bottom Right', 'dynamic-content-for-elementor')], 'default' => 'bottom-center', 'frontend_available' => \true, 'condition' => ['navigation!' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $pathimages = $settings['pathimages'];
        if (\strpos($settings['pathimages'], get_home_url()) === \false) {
            $settings['pathimages'] = get_home_url() . $settings['pathimages'];
        } else {
            $pathimages = \str_replace(get_home_url(), '', $pathimages);
        }
        if (\substr($pathimages, 0, 1) === '/') {
            $pathimages = \substr($pathimages, 1);
        }
        $path = ABSPATH . $pathimages;
        $images = \glob($path . '*.*');
        $total_frame = 0;
        foreach ($images as $image) {
            $pieces = \explode('.', $image);
            $ext = \strtolower(\array_pop($pieces));
            if (\in_array($ext, array('svg', 'jpg', 'png', 'gif', 'webp'))) {
                $total_frame++;
                $settings['format_file'] = $ext;
            }
        }
        ?>
		<div class="threesixty dce-threesixty"
			data-pathimages="<?php 
        echo $settings['pathimages'];
        ?>"
			data-format_file="<?php 
        echo $settings['format_file'];
        ?>"
			data-total_frame="<?php 
        echo $total_frame;
        ?>"
			data-end_frame="<?php 
        echo $total_frame;
        ?>"
			>
			<div class="spinner">
				<span>0%</span>
			</div>
			<ol class="threesixty_images"></ol>
		</div>
		<?php 
    }
}
