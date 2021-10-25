<?php

namespace MasterAddons\Modules;

use \Elementor\Element_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;

class Extension_Background_Slider
{
	private static $_instance = null;

	private function __construct()
	{
		add_action('elementor/element/after_section_end', [$this, '_add_controls'], 10, 3);

		add_action('elementor/frontend/element/before_render', [$this, '_before_render'], 10, 1);
		add_action('elementor/frontend/column/before_render', [$this, '_before_render'], 10, 1);
		add_action('elementor/frontend/section/before_render', [$this, '_before_render'], 10, 1);

		add_action('elementor/element/print_template', [$this, '_print_template'], 10, 2);
		// add_action('elementor/widget/print_template', [$this, '_print_template'], 10, 2);
		// add_action('elementor/section/print_template', [$this, '_print_template'], 10, 2);
	}


	public function ma_el_add_js_css()
	{

		// CSS
		wp_enqueue_style('vegas', MELA_PLUGIN_URL . '/assets/vendor/vegas/vegas.min.css');
		// wp_enqueue_style('swiper');

		// JS
		wp_enqueue_script('vegas', MELA_PLUGIN_URL . '/assets/vendor/vegas/vegas.min.js', ['jquery'], MELA_VERSION, true);
		wp_enqueue_script('swiper');
	}

	public function _add_controls($element, $section_id, $args)
	{
		if (('section' === $element->get_name() && 'section_background' === $section_id) || ('column' === $element->get_name() && 'section_style' === $section_id)) {

			$element->start_controls_section(
				'_ma_el_section_bg_slider',
				[
					'label' => MA_EL_BADGE . __(' Background Slider', MELA_TD),
					'tab'   => Controls_Manager::TAB_STYLE
				]
			);

			$element->add_control(
				'ma_el_bg_slider_images',
				[
					'label'     => __('Add Images', MELA_TD),
					'type'      => Controls_Manager::GALLERY,
					'default'   => [],
				]
			);

			$element->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' => 'ma_el_thumbnail',
				]
			);

			/*$slides_to_show = range( 1, 10 );
			$slides_to_show = array_combine( $slides_to_show, $slides_to_show );

			$element->add_control(
				'slides_to_show',
				[
					'label' => __( 'Slides to Show', MELA_TD ),
					'type' => Controls_Manager::SELECT,
					'default' => '3',
					'options' => $slides_to_show,
				]
			);*/
			/*$element->add_control(
                'slide',
                [
                    'label' => __( 'Initial Slide', MELA_TD ),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
					'placeholder' => __( 'Initial Slide', MELA_TD ),
					'default' => __( '0', MELA_TD ),
                ]
            );*/

			$element->add_control(
				'ma_el_slider_transition',
				[
					'label'   => __('Transition', MELA_TD),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'fade'        => __('Fade', MELA_TD),
						'fade2'       => __('Fade2', MELA_TD),
						'slideLeft'   => __('slide Left', MELA_TD),
						'slideLeft2'  => __('Slide Left 2', MELA_TD),
						'slideRight'  => __('Slide Right', MELA_TD),
						'slideRight2' => __('Slide Right 2', MELA_TD),
						'slideUp'     => __('Slide Up', MELA_TD),
						'slideUp2'    => __('Slide Up 2', MELA_TD),
						'slideDown'   => __('Slide Down', MELA_TD),
						'slideDown2'  => __('Slide Down 2', MELA_TD),
						'zoomIn'      => __('Zoom In', MELA_TD),
						'zoomIn2'     => __('Zoom In 2', MELA_TD),
						'zoomOut'     => __('Zoom Out', MELA_TD),
						'zoomOut2'    => __('Zoom Out 2', MELA_TD),
						'swirlLeft'   => __('Swirl Left', MELA_TD),
						'swirlLeft2'  => __('Swirl Left 2', MELA_TD),
						'swirlRight'  => __('Swirl Right', MELA_TD),
						'swirlRight2' => __('Swirl Right 2', MELA_TD),
						'burn'        => __('Burn', MELA_TD),
						'burn2'       => __('Burn 2', MELA_TD),
						'blur'        => __('Blur', MELA_TD),
						'blur2'       => __('Blur 2', MELA_TD),
						'flash'       => __('Flash', MELA_TD),
						'flash2'      => __('Flash 2', MELA_TD),
						'random'      => __('Random', MELA_TD)
					],
					'default' => 'fade',
				]
			);


			$element->add_control(
				'ma_el_slider_animation',
				[
					'label'   => __('Animation', MELA_TD),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'kenburns'          => __('Kenburns', MELA_TD),
						'kenburnsUp'        => __('Kenburns Up', MELA_TD),
						'kenburnsDown'      => __('Kenburns Down', MELA_TD),
						'kenburnsRight'     => __('Kenburns Right', MELA_TD),
						'kenburnsLeft'      => __('Kenburns Left', MELA_TD),
						'kenburnsUpLeft'    => __('Kenburns Up Left', MELA_TD),
						'kenburnsUpRight'   => __('Kenburns Up Right', MELA_TD),
						'kenburnsDownLeft'  => __('Kenburns Down Left', MELA_TD),
						'kenburnsDownRight' => __('Kenburns Down Right', MELA_TD),
						'random'            => __('Random', MELA_TD),
						''                  => __('None', MELA_TD)
					],
					'default' => 'kenburns',
				]
			);

			$element->add_control(
				'ma_el_custom_overlay_switcher',
				[
					'label'        => __('Custom Overlay', MELA_TD),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'label_on'     => __('Show', MELA_TD),
					'label_off'    => __('Hide', MELA_TD),
					'return_value' => 'yes',
				]
			);

			/*$element->add_control(
				'custom_overlay',
				[
					'label' => __( 'Overlay Image', MELA_TD ),
					'type' => Controls_Manager::MEDIA,
					'condition' => [
						'ma_el_custom_overlay_switcher' => 'yes',
					]
				]
			);*/

			$element->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'ma_el_slider_custom_overlay',
					'label'     => __('Overlay Image', MELA_TD),
					'types'     => ['none', 'classic', 'gradient'],
					'selector'  => '{{WRAPPER}} .vegas-overlay',
					'condition' => [
						'ma_el_custom_overlay_switcher' => 'yes',
					]
				]
			);

			$element->add_control(
				'ma_el_slider_overlay',
				[
					'label'     => __('Overlay', MELA_TD),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						''   => __('None', MELA_TD),
						'01' => __('Style 1', MELA_TD),
						'02' => __('Style 2', MELA_TD),
						'03' => __('Style 3', MELA_TD),
						'04' => __('Style 4', MELA_TD),
						'05' => __('Style 5', MELA_TD),
						'06' => __('Style 6', MELA_TD),
						'07' => __('Style 7', MELA_TD),
						'08' => __('Style 8', MELA_TD),
						'09' => __('Style 9', MELA_TD)
					],
					'default'   => '01',
					'condition' => [
						'ma_el_custom_overlay_switcher' => '',
					]
				]
			);
			$element->add_control(
				'ma_el_slider_cover',
				[
					'label'   => __('Cover', MELA_TD),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'true'  => __('True', MELA_TD),
						'false' => __('False', MELA_TD)
					],
					'default' => 'true',
				]
			);
			$element->add_control(
				'ma_el_slider_delay',
				[
					'label'       => __('Delay', MELA_TD),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'placeholder' => __('Delay', MELA_TD),
					'default'     => __('5000', MELA_TD),
				]
			);
			$element->add_control(
				'ma_el_slider_timer_bar',
				[
					'label'   => __('Timer', MELA_TD),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'true'  => __('True', MELA_TD),
						'false' => __('False', MELA_TD)
					],
					'default' => 'true',
				]
			);

			$element->end_controls_section();
		}
	}


	function _before_render(\Elementor\Element_Base $element)
	{

		if ($element->get_name() != 'section' && $element->get_name() != 'column') {
			return;
		}
		$settings = $element->get_settings();

		$element->add_render_attribute('_wrapper', 'class', 'has_ma_el_bg_slider');
		$element->add_render_attribute('ma-el-bs-background-slideshow-wrapper', 'class', 'ma-el-bs-background-slideshow-wrapper');

		$element->add_render_attribute('ma-el-bs-backgroundslideshow', 'class', 'ma-el-at-backgroundslideshow');

		$slides = [];

		if (empty($settings['ma_el_bg_slider_images'])) {
			return;
		}

		$this->ma_el_add_js_css();

		foreach ($settings['ma_el_bg_slider_images'] as $attachment) {
			$image_url = Group_Control_Image_Size::get_attachment_image_src(
				$attachment['id'],
				'ma_el_thumbnail',
				$settings
			);
			$slides[]  = ['src' => $image_url];
		}

		if (empty($slides)) {
			return;
		}

?>

		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery(".elementor-element-<?php echo $element->get_id(); ?>").prepend('<div ' +
					'class="ma-el-section-bs"><div' +
					' class="ma-el-section-bs-inner"></div></div>');
				var bgimage = '<?php echo $settings["ma_el_slider_custom_overlay_image"]['url']; ?>';
				if ('<?php echo $settings["ma_el_custom_overlay_switcher"]; ?>' == 'yes') {

					//if(bgimage == ''){
					//    var bgoverlay = '<?php echo $settings["ma_el_slider_custom_overlay_image"]['url']; ?>';
					//}else{
					var bgoverlay = '<?php echo MELA_PLUGIN_URL . "/assets/vendor/vegas/overlays/00.png"; ?>';
					// }
				} else {
					if ('<?php echo $settings["ma_el_slider_overlay"]; ?>') {
						var bgoverlay = '<?php echo MELA_PLUGIN_URL . "/assets/vendor/vegas/overlays/" . $settings["ma_el_slider_overlay"] . ".png"; ?>';
					} else {
						var bgoverlay = '<?php echo MELA_PLUGIN_URL . "/assets/vendor/vegas/overlays/00.png"; ?>';
					}
				}


				jQuery(".elementor-element-<?php echo $element->get_id(); ?>").children('.ma-el-section-bs').children('' +
					'.ma-el-section-bs-inner').vegas({
					slides: <?php echo json_encode($slides) ?>,
					transition: '<?php echo $settings['ma_el_slider_transition']; ?>',
					animation: '<?php echo $settings['ma_el_slider_animation']; ?>',
					overlay: bgoverlay,
					cover: <?php echo $settings['ma_el_slider_cover']; ?>,
					delay: <?php echo $settings['ma_el_slider_delay']; ?>,
					timer: <?php echo $settings['ma_el_slider_timer_bar']; ?>
				});
				if ('<?php echo $settings["ma_el_custom_overlay_switcher"]; ?>' == 'yes') {
					jQuery(".elementor-element-<?php echo $element->get_id(); ?>").children('.ma-el-section-bs')
						.children('.ma-el-section-bs-inner').children('.vegas-overlay').css('background-image', '');
				}
			});
		</script>
	<?php
	}

	function _print_template($template, $widget)
	{
		if ($widget->get_name() != 'section' && $widget->get_name() != 'column') {
			return $template;
		}

		$old_template = $template;
		ob_start();
	?>

		<# var rand_id=Math.random().toString(36).substring(7) slides_path_string='' , ma_el_transition=settings.ma_el_slider_transition, ma_el_animation=settings.ma_el_slider_animation, ma_el_custom_overlay=settings.ma_el_custom_overlay_switcher, ma_el_overlay='' , ma_el_cover=settings.ma_el_slider_cover, ma_el_delay=settings.ma_el_slider_delay, ma_el_timer=settings.ma_el_slider_timer_bar; if(!_.isUndefined(settings.ma_el_bg_slider_images) && settings.ma_el_bg_slider_images.length){ var slider_data=[]; slides=settings.ma_el_bg_slider_images; for(var i in slides){ slider_data[i]=slides[i].url; } slides_path_string=slider_data.join(); } if(settings.ma_el_custom_overlay_switcher=='yes' ){ ma_el_overlay='00.png' ; }else{ if(settings.ma_el_slider_overlay){ ma_el_overlay=settings.ma_el_slider_overlay + '.png' ; }else{ ma_el_overlay='00.png' ; } } #>

			<div class="ma-el-section-bs">
				<div class="ma-el-section-bs-inner" data-ma-el-bg-slider="{{ slides_path_string }}" data-ma-el-bg-slider-transition="{{ ma_el_transition }}" data-ma-el-bg-slider-animation="{{ ma_el_animation }}" data-ma-el-bg-custom-overlay="{{ ma_el_custom_overlay }}" data-ma-el-bg-slider-overlay="{{ ma_el_overlay }}" data-ma-el-bg-slider-cover="{{ ma_el_cover }}" data-ma-el-bs-slider-delay="{{ ma_el_delay }}" data-ma-el-bs-slider-timer="{{ ma_el_timer }}"></div>
			</div>

	<?php
		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;

		return $template;
	}


	public static function get_instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

Extension_Background_Slider::get_instance();
