<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SvgMorphing extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-gsap-lib', 'dce-morphSVG-lib', 'dce-svgmorph'];
    }
    public function get_style_depends()
    {
        return ['dce-svg'];
    }
    private $coeff = 1;
    protected $svg_shapes = array('path' => 'path', 'polyline' => 'polyline');
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $idWidget = $this->get_id();
        $this->start_controls_section('section_svg_controls', ['label' => __('Controls', 'dynamic-content-for-elementor')]);
        $this->add_control('svg_trigger', ['label' => __('Trigger', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['animation' => __('Animation', 'dynamic-content-for-elementor'), 'rollover' => __('Rollover', 'dynamic-content-for-elementor'), 'scroll' => __('Scroll', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'animation', 'prefix_class' => 'svg-trigger-', 'separator' => 'after', 'render_type' => 'template']);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'home' => __('Home URL', 'dynamic-content-for-elementor'), 'custom' => __('Custom URL', 'dynamic-content-for-elementor')], 'condition' => ['svg_trigger' => 'rollover']]);
        $this->add_control('link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'condition' => ['link_to' => 'custom', 'svg_trigger' => 'rollover'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->add_control('one_by_one', ['label' => __('One by one', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before', 'condition' => ['svg_trigger' => 'scroll']]);
        $this->add_control('playpause_control', ['label' => __('Animation Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'running', 'description' => __('In pause mode, it is possible to shape the shapes. You can manage the animation between one scene and another in play mode.', 'dynamic-content-for-elementor'), 'toggle' => \false, 'options' => ['running' => ['title' => __('Play', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-play'], 'paused' => ['title' => __('Pause', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-pause']], 'frontend_available' => \true, 'separator' => 'before', 'render_type' => 'ui', 'condition' => ['svg_trigger!' => 'rollover']]);
        $this->add_control('yoyo', ['label' => __('Yoyo', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'separator' => 'before', 'condition' => ['svg_trigger' => 'animation']]);
        $this->add_control('repeat_morph', ['label' => __('Repeat', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'frontend_available' => \true, 'description' => __('Infinite: -1 or do not repeat: 0', 'dynamic-content-for-elementor'), 'default' => -1, 'min' => -1, 'max' => 25, 'step' => 1, 'condition' => ['svg_trigger!' => 'rollover', 'one_by_one' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_creative_svg', ['label' => __('SVG & Viewbox', 'dynamic-content-for-elementor')]);
        $this->add_control('type_of_shape', ['label' => __('Shape Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $this->svg_shapes, 'default' => 'path', 'description' => __('Type of SVG sequence', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'label_block' => \true]);
        $this->add_control('enable_image', ['label' => __('Pattern image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('viewBox_heading', ['label' => __('SVG ViewBox', 'dynamic-content-for-elementor'), 'description' => __('The pixel size of the document you drew the shapes on', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('viewbox_width', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_control('viewbox_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_responsive_control('svg_width', ['label' => __('Content Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'separator' => 'before', 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 0.1], 'px' => ['min' => 1, 'max' => 3500]], 'selectors' => ['{{WRAPPER}} svg.dce-svg-morph' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('svg_height', ['label' => __('Content Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 0.1], 'px' => ['min' => 1, 'max' => 2000]], 'selectors' => ['{{WRAPPER}} svg.dce-svg-morph' => 'height: {{SIZE}}{{UNIT}};']]);
        // Loop
        // Direction
        // easing
        // https://jakearchibald.github.io/svgomg/
        $repeater = new \Elementor\Repeater();
        $chid = $repeater->get_name();
        $repeater->add_control('id_shape', ['label' => 'ID', 'type' => Controls_Manager::TEXT, 'default' => 'shape-']);
        $repeater->add_control('shape_numbers', ['label' => __('Numbers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'default' => '']);
        $repeater->add_control('transform_heading', ['label' => __('Transform', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $repeater->add_control('shape_rotation', ['label' => __('Rotation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'render_type' => 'ui', 'range' => ['px' => ['min' => -180, 'max' => 180, 'step' => 1]], 'label_block' => \true]);
        $repeater->add_control('position_heading', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $repeater->add_control('shape_x', ['label' => __('X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'render_type' => 'ui', 'range' => ['px' => ['min' => -500, 'max' => 500, 'step' => 1]], 'label_block' => \false]);
        $repeater->add_control('shape_y', ['label' => __('Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'render_type' => 'ui', 'range' => ['px' => ['min' => -500, 'max' => 500, 'step' => 1]], 'label_block' => \false]);
        $repeater->add_control('style_heading', ['label' => __('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $repeater->add_control('fill_image', ['label' => __('Fill Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => ''], 'dynamic' => ['active' => \true]]);
        $repeater->add_control('fill_color', ['label' => __('Fill Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#FF0000']);
        $repeater->add_control('stroke_color', ['label' => __('Stroke Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#000000']);
        $repeater->add_control('stroke_width', ['label' => __('Stroke Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 60, 'step' => 1]], 'label_block' => \false]);
        $repeater->add_control('animation_heading', ['label' => __('Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $repeater->add_control('speed_morph', ['label' => __('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'label_block' => \false, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 0.2, 'max' => 5, 'step' => 0.1]], 'frontend_available' => \true]);
        $repeater->add_control('duration_morph', ['label' => __('Step Duration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'label_block' => \false, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 0.1, 'max' => 10, 'step' => 0.1]], 'frontend_available' => \true]);
        $repeater->add_control('easing_morph', ['label' => __('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_ease(), 'default' => '', 'frontend_available' => \true, 'label_block' => \false]);
        $repeater->add_control('easing_morph_ease', ['label' => __('Equation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Default', 'dynamic-content-for-elementor')] + Helper::get_gsap_timing_functions(), 'default' => '', 'frontend_available' => \true, 'label_block' => \false]);
        $this->end_controls_section();
        $this->start_controls_section('section_svg_animations', ['label' => __('Animations', 'dynamic-content-for-elementor')]);
        $this->add_control('playpause_info_animation', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __('You\'re on pause mode. It would be better to be in play mode. If you\'re watching the scene in pause mode, you won\'t see the changes to the parameters of the animations', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'separator' => 'after', 'condition' => ['playpause_control' => 'paused']]);
        $this->add_control('speed_morph', ['label' => __('Speed Transition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.7], 'range' => ['px' => ['min' => 0.2, 'max' => 5, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('duration_morph', ['label' => __('Step Duration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 12, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('easing_morph', ['label' => __('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_gsap_ease(), 'default' => 'easeInOut', 'frontend_available' => \true, 'label_block' => \false]);
        $this->add_control('easing_morph_ease', ['label' => __('Equation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_gsap_timing_functions(), 'default' => 'Power3', 'frontend_available' => \true, 'label_block' => \false]);
        $this->end_controls_section();
        $count = 0;
        foreach ($this->svg_shapes as $svgs) {
            if ($svgs == 'polygon') {
                $default_shape = [['id_shape' => $svgs . '_1', 'shape_numbers' => '700,84.4 1047.1,685.6 352.9,685.6 352.9,685.6 352.9,685.6 352.9,685.6'], ['id_shape' => $svgs . '_2', 'shape_numbers' => '983.4,101.6 983.4,668.4 416.6,668.4 416.6,101.9 416.6,101.9 416.6,101.9'], ['id_shape' => $svgs . '_3', 'shape_numbers' => '890.9,54.3 1081.8,385 890.9,715.7 509.1,715.7 318.2,385 509.1,54.3'], ['id_shape' => $svgs . '_4', 'shape_numbers' => '983.4,101.6 779,385 983.4,668.4 416.6,668.4 611,388 416.6,101.9']];
            } elseif ($svgs == 'path') {
                $default_shape = [['id_shape' => $svgs . '_1', 'shape_numbers' => 'M438.7,254.2L587,508.4H293.5H0l148.3-254.2L293.5,0L438.7,254.2z'], ['id_shape' => $svgs . '_2', 'shape_numbers' => 'M600,259.8L450,519.6H150L0,259.8L150,0h300L600,259.8z'], ['id_shape' => $svgs . '_3', 'shape_numbers' => 'M568,568H0l172.5-284L0,0h568L395.5,287L568,568z'], ['id_shape' => $svgs . '_4', 'shape_numbers' => 'M568,568H0l1.7-284L0,0h568l-1.7,287L568,568z']];
            } elseif ($svgs == 'polyline') {
                $default_shape = [['id_shape' => $svgs . '_1', 'shape_numbers' => '0.3,131.7 142.3,42.7 210.3,239.7 265.3,8.7 307.3,220.7 378.3,1.7 443.3,232.7 554.3,175.7 '], ['id_shape' => $svgs . '_2', 'shape_numbers' => '0.2,103.2 157.2,190.2 211.2,65.2 269.2,160.2 361.2,1.2 438.2,227.2 488.2,30.2 554.2,147.2 ']];
            }
            $this->start_controls_section('section_svg_' . $svgs, ['label' => $svgs, 'condition' => ['type_of_shape' => $svgs]]);
            $this->add_control('playpause_info_' . $svgs, ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __('You are in play mode. It would be better to be in Pause Mode. If you are watching the scene in play mode, it is difficult to change the parameters of the shapes. Pause and switch between shapes by clicking on the block', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'separator' => 'after', 'condition' => ['playpause_control' => 'running']]);
            $this->add_control('repeater_shape_' . $svgs, ['label' => 'Shape ' . $svgs, 'type' => Controls_Manager::REPEATER, 'default' => $default_shape ?? '', 'fields' => $repeater->get_controls(), 'title_field' => '{{{ id_shape }}}', 'frontend_available' => \true]);
            $this->end_controls_section();
            $count++;
        }
        // Section for pattern image
        $this->start_controls_section('section_svg_bgimage', ['label' => __('Pattern image', 'dynamic-content-for-elementor'), 'condition' => ['enable_image' => 'yes']]);
        $this->add_control('playpause_info_image', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __('You are in play mode. It would be better to be in Pause Mode. If you are watching the scene in play mode, it is difficult to change the parameters of the shapes. Pause and switch between shapes by clicking on the block', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'separator' => 'after', 'condition' => ['playpause_control' => 'running']]);
        $this->add_control('svg_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => ''], 'frontend_available' => \true, 'show_label' => \false, 'dynamic' => ['active' => \true]]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'image', 'default' => 'thumbnail']);
        $this->add_responsive_control('svg_size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '100', 'unit' => '%'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => 1, 'max' => 200], 'px' => ['min' => 1, 'max' => 2000]]]);
        $this->add_control('svgimage_x', ['label' => __('X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -500, 'max' => 500, 'step' => 1]], 'label_block' => \false]);
        $this->add_control('svgimage_y', ['label' => __('Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0'], 'size_units' => ['%', 'px'], 'range' => ['%' => ['min' => -100, 'max' => 100], 'px' => ['min' => -500, 'max' => 500, 'step' => 1]], 'label_block' => \false]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Style', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('svg_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'prefix_class' => 'align-', 'default' => 'left', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
    }
    protected function realHeight($imgid, $imgsize, $imgformat)
    {
        $imageData = wp_get_attachment_image_src($imgid, $imgformat);
        $h = $imageData[2];
        $w = $imageData[1];
        $imageProportion = $h / $w;
        $realHeight = $imgsize * $imageProportion;
        return $realHeight;
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        $widgetId = $this->get_id();
        $runAnimation = $settings['playpause_control'];
        if ($settings['svg_trigger'] == 'rollover' || $settings['svg_trigger'] == 'scroll') {
            $runAnimation = 'paused';
        }
        $keyVector = 'd';
        //'d' -> path, 'points' -> polyline
        if ($settings['type_of_shape'] == 'polygon' || $settings['type_of_shape'] == 'polyline') {
            $keyVector = 'points';
            // -> Polygon
        }
        $image_id = $settings['svg_image']['id'];
        $image_url = Group_Control_Image_Size::get_attachment_image_src($image_id, 'image', $settings);
        $this->coeff = '0.5';
        $this->add_render_attribute('_wrapper', 'data-coeff', $this->coeff);
        if ($settings['svgimage_x']['size'] == '') {
            $posX = 0;
        } else {
            $posX = $settings['svgimage_x']['size'];
        }
        if ($settings['svgimage_y']['size'] == '') {
            $posY = 0;
        } else {
            $posY = $settings['svgimage_y']['size'];
        }
        $viewBoxW = $settings['viewbox_width'];
        $viewBoxH = $settings['viewbox_height'];
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = \false;
                }
                break;
            case 'home':
                $link = esc_url(get_home_url());
                break;
            case 'none':
            default:
                $link = \false;
                break;
        }
        ?>
		<div class="dce-svg-morph-wrap">
			<?php 
        $target = !empty($settings['link']['is_external']) ? 'target="_blank"' : '';
        if ($link) {
            echo '<a href="' . $link . '" ' . $target . '>';
        }
        ?>
			<svg id="dce-svg-<?php 
        echo $widgetId;
        ?>" class="dce-svg-morph" data-morphid="0" data-run="<?php 
        echo $runAnimation;
        ?>" version="1.1" xmlns="http://www.w3.org/2000/svg"  width="100%" height="100%" viewBox="0 0 <?php 
        echo $viewBoxW;
        ?> <?php 
        echo $viewBoxH;
        ?>" preserveAspectRatio="xMidYMid meet" xml:space="preserve" style="transform: rotate(<?php 
        echo $settings['repeater_shape_' . $settings['type_of_shape']][0]['shape_rotation']['size'];
        ?>deg) translate(<?php 
        echo $settings['repeater_shape_' . $settings['type_of_shape']][0]['shape_x']['size'];
        ?>px,<?php 
        echo $settings['repeater_shape_' . $settings['type_of_shape']][0]['shape_y']['size'];
        ?>px);">

				<?php 
        if ($settings['enable_image']) {
            ?>
				<defs>
					<?php 
            $heightPattern = $settings['svg_size']['size'] . $settings['svg_size']['unit'];
            if ($settings['svg_image']['url'] != '') {
                $heightPattern = $this->realHeight($image_id, $settings['svg_size']['size'], $settings['image_size']) . $settings['svg_size']['unit'];
            }
            ?>
					<pattern id="pattern-<?php 
            echo $widgetId;
            ?>" patternUnits="userSpaceOnUse" patternContentUnits="userSpaceOnUse" width="<?php 
            echo $settings['svg_size']['size'] . $settings['svg_size']['unit'];
            ?>" height="<?php 
            echo $heightPattern;
            ?>" x="<?php 
            echo $posX . $settings['svgimage_x']['unit'];
            ?>" y="<?php 
            echo $posY . $settings['svgimage_y']['unit'];
            ?>">
						<?php 
            if ($settings['svg_image']['url'] != '') {
                ?>
							<image id="img-patt-base" xlink:href="<?php 
                echo $image_url;
                ?>" width="<?php 
                echo $settings['svg_size']['size'] . $settings['svg_size']['unit'];
                ?>" height="<?php 
                echo $this->realHeight($image_id, $settings['svg_size']['size'], $settings['image_size']) . $settings['svg_size']['unit'];
                ?>"> </image>
						<?php 
            }
            //
            if ($settings['repeater_shape_' . $settings['type_of_shape']]) {
                $count = 0;
                $repeater_shape = $settings['repeater_shape_' . $settings['type_of_shape']];
                foreach ($repeater_shape as $item) {
                    if ($item['fill_image']['url'] != '') {
                        $image_id_pattern = $item['fill_image']['id'];
                        $image_url_pattern = Group_Control_Image_Size::get_attachment_image_src($image_id_pattern, 'image', $settings);
                        $visible = ' style="opacity:1"';
                        if ($count > 0) {
                            $visible = ' style="opacity:0"';
                        }
                        ?>

										<image id="img-patt-<?php 
                        echo $count;
                        ?>" class="dce-shape-image dce-shape-image-repeater-item-<?php 
                        echo $item['_id'];
                        ?>" xlink:href="<?php 
                        echo $image_url_pattern;
                        ?>" width="<?php 
                        echo $settings['svg_size']['size'] . $settings['svg_size']['unit'];
                        ?>" height="<?php 
                        echo $this->realHeight($image_id_pattern, $settings['svg_size']['size'], $settings['image_size']) . $settings['svg_size']['unit'];
                        ?>"<?php 
                        echo $visible;
                        ?>> </image>
										<?php 
                    }
                    $count++;
                }
            }
            ?>
					</pattern>
				</defs>
				<?php 
        }
        ?>

				<?php 
        $fill_color = $settings['repeater_shape_' . $settings['type_of_shape']][0]['fill_color'];
        $fill_image = $settings['repeater_shape_' . $settings['type_of_shape']][0]['fill_image']['id'];
        $fill_element = $fill_color;
        if ($fill_image || $image_url) {
            $fill_element = 'url(#pattern-' . $this->get_id() . ')';
        }
        ?>
				<<?php 
        echo $settings['type_of_shape'];
        ?> id="forma-<?php 
        echo $widgetId;
        ?>" fill="<?php 
        echo $fill_element;
        ?>" stroke-width="<?php 
        echo $settings['repeater_shape_' . $settings['type_of_shape']][0]['stroke_width']['size'];
        ?>" stroke="<?php 
        echo $settings['repeater_shape_' . $settings['type_of_shape']][0]['stroke_color'];
        ?>" stroke-miterlimit="10" <?php 
        echo $keyVector;
        ?>="<?php 
        echo $settings['repeater_shape_' . $settings['type_of_shape']][0]['shape_numbers'];
        ?>"/>
			</svg>
			<?php 
        if ($link) {
            echo '</a>';
        }
        ?>
		</div>

		<?php 
    }
    protected function content_template()
    {
        ?>
		<#
		var currentItem     = ( editSettings.activeItemIndex >= 0 ) ? editSettings.activeItemIndex : false;


		var morphid = ( currentItem ) ? currentItem-1 : 0;

		var idWidget = id;
		//
		var viewBoxW = settings.viewbox_width;
		var viewBoxH = settings.viewbox_height;

		var typeShape = settings.type_of_shape;

		// PATTERN Image

		var image = {
			id: settings.svg_image.id,
			url: settings.svg_image.url,
			size: settings.image_size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};
		var bgImage = elementor.imagesManager.getImageUrl( image );

		var sizeImage = settings.svg_size.size;
		var sizeUnitImage = settings.svg_size.unit;
		var enable_image  = settings.enable_image;

		var image_x = settings.svgimage_x.size;
		var image_y = settings.svgimage_y.size;
		if(image_x == '') image_x = '0';
		if(image_y == '') image_y = '0';

		var sizeUnitXImage = settings.svgimage_x.unit;
		var sizeUnitYImage = settings.svgimage_y.unit;

		var runAnimation = settings.playpause_control;
		if( settings.svg_trigger == 'rollover' || settings.svg_trigger == 'scroll' ){
			runAnimation = 'paused';
		}


		eval('var shapeNumbers = settings.repeater_shape_'+typeShape+';');

		var indexShape = 0;
		if(morphid){
			indexShape = morphid;
		}

		if(shapeNumbers[indexShape] != undefined && shapeNumbers.length){

			var firstShape = shapeNumbers[indexShape]['shape_numbers'] || '';
			if(firstShape == '') firstShape = shapeNumbers[indexShape-1]['shape_numbers']


			var firstFill = shapeNumbers[indexShape]['fill_color'] || '#ccc';

			var firstStrokeColor = shapeNumbers[indexShape]['stroke_color'] || '#000';
			var firstStrokeWidth = shapeNumbers[indexShape]['stroke_width']['size'] || 0;

			// -- Fill --
			var fill_element = firstFill;


			var firstPosX = shapeNumbers[indexShape]['shape_x']['size'] || 0;
			var firstPosY = shapeNumbers[indexShape]['shape_y']['size'] || 0;
			var firstRotation = shapeNumbers[indexShape]['shape_rotation']['size'] || 0;

			var keyVector = 'd';
			if(typeShape == 'polygon' || typeShape == 'polyline') keyVector = 'points';


			var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();

			dce_getimageSizes(bgImage, function (data) {

				if (jQuery("iframe#elementor-preview-iframe").length) {
					var pattern = iFrameDOM.find('pattern#pattern-'+idWidget);
					var patternImage = iFrameDOM.find('pattern#pattern-'+idWidget+' image');

					if(patternImage.length){

						var realHeight = data.coef * settings.svg_size.size;
						pattern.attr('height',realHeight+settings.svg_size.unit);
						patternImage.attr('height',realHeight+settings.svg_size.unit);
					}
				}

			});
			var link_url;

			if ( 'custom' === settings.link_to ) {
				link_url = settings.link.url;
			}
			#>

			<div class="dce-svg-morph-wrap">

				<# if ( link_url ) {
					#><a href="{{ link_url }}"><#
				}
				#>
			   	<!--  <div>{{morphid}}</div> -->
				<svg id="dce-svg-{{idWidget}}" class="dce-svg-morph" data-run="{{runAnimation}}" data-morphid="{{morphid}}" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke-miterlimit="10" width="100%" height="100%" viewBox="0 0 {{viewBoxW}} {{viewBoxH}}" preserveAspectRatio="xMidYMid meet" xml:space="preserve" style="transform: rotate({{firstRotation}}deg) translate({{firstPosX}}px,{{firstPosY}}px);">

				<# if(enable_image){
					// patternTransform="rotate(10)"
					#>
					<defs>
						<pattern id="pattern-{{idWidget}}" patternUnits="userSpaceOnUse" patternContentUnits="userSpaceOnUse" width="{{sizeImage}}{{sizeUnitImage}}" height="{{sizeImage}}{{sizeUnitImage}}" x="{{image_x}}{{sizeUnitXImage}}" y="{{image_y}}{{sizeUnitYImage}}">

							<# if(bgImage){ #>
								<image id="img-patt-base" xlink:href="{{bgImage}}" width="{{sizeImage}}{{sizeUnitImage}}" height="{{sizeImage}}{{sizeUnitImage}}"> </image>
							<# }


							if ( shapeNumbers.length ) {
								var count = 0;
								var image_url_pattern = '';
								_.each( shapeNumbers, function( item ) {


								var image_pattern = {
									id: item.fill_image.id,
									url: item.fill_image.url,
									size: settings.image_size,
									dimension: settings.image_custom_dimension,
									model: view.getEditModel()
								};
								image_url_pattern =  elementor.imagesManager.getImageUrl( image_pattern );

								if(image_url_pattern){

									var visible = ' style=\"opacity:1\"';
									if(count > 0) visible = ' style=\"opacity:0\"';



								#>
									<image id="img-patt-{{count}}" class="dce-shape-image elementor-repeater-item-{{item._id}}"  xlink:href="{{image_url_pattern}}" width="{{sizeImage}}{{sizeUnitImage}}" height="{{sizeImage}}{{sizeUnitImage}}"{{visible}}> </image>
								<# }

									count ++;
								});
							} #>

						</pattern>
					</defs>
				<# }
					if(bgImage || image_url_pattern){
						fill_element = 'url(#pattern-'+idWidget+')';
					}

				#>

					<{{typeShape}} id="forma-{{idWidget}}" fill="{{fill_element}}" stroke-width="{{firstStrokeWidth}}" stroke="{{firstStrokeColor}}" {{keyVector}}="{{firstShape}}"/>
				</svg>
				<#
				if ( link_url ) {
					#></a><#
				}
				#>

			</div>

		<# } #>
		<?php 
    }
}
