<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use DynamicContentForElementor\Helper;
use Elementor\Repeater;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class BgCanvas extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-threejs-lib', 'dce-threejs-EffectComposer', 'dce-threejs-RenderPass', 'dce-threejs-ShaderPass', 'dce-threejs-BloomPass', 'dce-threejs-FilmPass', 'dce-threejs-HalftonePass', 'dce-threejs-DotScreenPass', 'dce-threejs-GlitchPass', 'dce-threejs-AsciiEffect', 'dce-threejs-CopyShader', 'dce-threejs-HalftoneShader', 'dce-threejs-RGBShiftShader', 'dce-threejs-DotScreenShader', 'dce-threejs-ConvolutionShader', 'dce-threejs-FilmShader', 'dce-threejs-ColorifyShader', 'dce-threejs-VignetteShader', 'dce-threejs-DigitalGlitch', 'dce-threejs-PixelShader', 'dce-threejs-LuminosityShader', 'dce-threejs-SobelOperatorShader', 'dce-gsap-lib', 'dce-bgcanvas-js'];
    }
    public function get_style_depends()
    {
        return ['dce-bgCanvas'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_bgcanvas', ['label' => __('Image', 'dynamic-content-for-elementor')]);
        $this->add_control('bgcanvas_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'image', 'default' => 'thumbnail', 'condition' => ['bgcanvas_image[id]!' => '']]);
        $this->add_responsive_control('bgcanvas_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 400, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'render_type' => 'template', 'size_units' => ['px', '%', 'vh'], 'separator' => 'after', 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-container-bgcanvas' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_postprocessing', ['label' => __('Postprocessing & Shaders', 'dynamic-content-for-elementor')]);
        $this->add_control('postprocessing_film', ['label' => '<b>' . __('Film', 'dynamic-content-for-elementor') . '</b>', 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_film_grayscale', ['label' => __('Gray Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'condition' => ['postprocessing_film!' => '']]);
        $this->add_control('postprocessing_film_noiseIntensity', ['label' => __('Noise Intensity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 0.35], 'range' => ['px' => ['min' => 0.01, 'max' => 1, 'step' => 0.01]], 'condition' => ['postprocessing_film!' => '']]);
        $this->add_control('postprocessing_film_scanlinesIntensity', ['label' => __('Scanlines Intensity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 0.035], 'range' => ['px' => ['min' => 0.01, 'max' => 1, 'step' => 0.001]], 'condition' => ['postprocessing_film!' => '']]);
        $this->add_control('postprocessing_film_scanlinesCount', ['label' => __('Scanlines Count', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 648], 'range' => ['px' => ['min' => 1, 'max' => 1000, 'step' => 1]], 'condition' => ['postprocessing_film!' => '']]);
        $this->add_control('postprocessing_halftone', ['label' => '<b>' . __('Halftone', 'dynamic-content-for-elementor') . '</b>', 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_halftone_shape', ['label' => __('Shape', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'options' => ['1' => __('Dots', 'dynamic-content-for-elementor'), '2' => __('Ellipse', 'dynamic-content-for-elementor'), '3' => __('Lines', 'dynamic-content-for-elementor'), '4' => __('Squre', 'dynamic-content-for-elementor')], 'default' => '1', 'condition' => ['postprocessing_halftone!' => '']]);
        $this->add_control('postprocessing_halftone_grayscale', ['label' => __('Gray Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'condition' => ['postprocessing_halftone!' => '']]);
        $this->add_control('postprocessing_halftone_radius', ['label' => __('Dot Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'condition' => ['postprocessing_halftone!' => '']]);
        $this->add_control('postprocessing_rgbShiftShader', ['label' => '<b>' . __('RGB Shift Shader', 'dynamic-content-for-elementor') . '</b>', 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_rgbshift_amount', ['label' => __('Amount', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 15], 'range' => ['px' => ['min' => 1, 'max' => 30, 'step' => 0.001]], 'condition' => ['postprocessing_rgbShiftShader!' => '']]);
        $this->add_control('postprocessing_glitch', ['label' => '<b>' . __('Glitch', 'dynamic-content-for-elementor') . '</b>', 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        // ----------- dot
        $this->add_control('postprocessing_dot', ['label' => '<b>' . __('Dot', 'dynamic-content-for-elementor') . '</b>', 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_dot_scale', ['label' => __('Dot Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0.1, 'max' => 10, 'step' => 0.1]], 'condition' => ['postprocessing_dot!' => '']]);
        $this->add_control('postprocessing_dot_angle', ['label' => __('Dot Angle', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 0.5], 'range' => ['px' => ['min' => -1, 'max' => 1, 'step' => 0.01]], 'condition' => ['postprocessing_dot!' => '']]);
        $this->add_control('postprocessing_pixels', ['label' => '<b>' . __('Pixels', 'dynamic-content-for-elementor') . '</b>', 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_pixels_size', ['label' => __('Pixels Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 16], 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'condition' => ['postprocessing_pixels!' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['bgcanvas_image']['id'], 'image', $settings);
        ?>
		<div class="dce-container-bgcanvas" data-bgcanvasimage="<?php 
        echo $image_url;
        ?>">
			<div class="scene js-scene"></div>
		</div>
		<?php 
    }
    protected function content_template()
    {
        ?>

		<#
		var image = {
			id: settings.bgcanvas_image.id,
			url: settings.bgcanvas_image.url,
			size: settings.image_size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};
		var url_image = elementor.imagesManager.getImageUrl( image );
		#>
		<div class="dce-container-bgcanvas" data-bgcanvasimage="{{url_image}}">
			<div class="scene js-scene"></div>
		</div>
		<?php 
    }
}
