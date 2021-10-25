<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Button $widget */

$settings = array(
	'button_title'          => esc_html__('Button title', 'gt3_themes_core'),
	'link'                  => array(
		'url'         => '#',
		'is_external' => false,
		'nofollow'    => false,
	),
	'button_size_elementor' => 'normal',
	'button_alignment'      => 'center',
	'btn_border_rounded'    => false,
	'btn_icon'              => 'none',
	'icon_position'         => 'left',
	'button_icon'           => '',
	'image_size'            => array(
		'size' => 32,
		'unit' => 'px',
	),
	'image'                 => array(
		'url' => Utils::get_placeholder_image_src(),
	),
	'button_hover'          => 'none',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('_wrapper', 'class', 'gt3-core-button--alignment_'.$settings['button_alignment']);
$data_settings = array();

// Wrapper
$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_module_button_elementor',
	'size_'.$settings['button_size_elementor'],
	'alignment_'.$settings['button_alignment'],
	'button_icon_'.$settings['btn_icon'],
	'hover_'.$settings['button_hover'],
	$settings['btn_border_rounded'] ? 'rounded' : '',
));

if(!!$settings['button_is_modal']) {
	$modal_id = 'modal-'.$widget->get_id();

	$data_settings = array_merge($data_settings, array(
		'modal'    => true,
		'modal_id' => $modal_id,
	));

	add_action('wp_footer', function() use ($modal_id, $settings){
		?>
		<div class="modal gt3-core-button-modal" style="visibility: hidden" id="<?php echo $modal_id; ?>">
			<div class="modal-content-wrapper">
				<?php if(!empty($settings['modal_header'])) {
					?>
					<div class="modal-header">
						<h3><?php echo esc_html($settings['modal_header']); ?></h3>
						<button class="close_button_modal"><svg xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="32" height="32" viewBox="0 0 512 512"><path d="M359.542,152.458c-4.167-4.167-10.917-4.167-15.083,0L256,240.917l-88.458-88.458c-4.167-4.167-10.917-4.167-15.083,0
c-4.167,4.167-4.167,10.917,0,15.083L240.917,256l-88.458,88.458c-4.167,4.167-4.167,10.917,0,15.083
c2.083,2.083,4.813,3.125,7.542,3.125s5.458-1.042,7.542-3.125L256,271.083l88.458,88.458c2.083,2.083,4.813,3.125,7.542,3.125
c2.729,0,5.458-1.042,7.542-3.125c4.167-4.167,4.167-10.917,0-15.083L271.083,256l88.458-88.458
C363.708,163.375,363.708,156.625,359.542,152.458z"></path></svg></button>
					</div>
					<?php
				}
				?>
				<div class="modal-content">
					<?php
					echo do_shortcode($settings['modal_content']);
					?>
				</div>
			</div>
		</div>

		<?php
	});

}

// Icon
$icon = '';
$text = '';
switch($settings['btn_icon']) {
	case 'default':
		$icon = '<span class="elementor_btn_icon_container"><span class="elementor_gt3_btn_icon gt3_icon_default"></span></span>';
		break;
	case 'icon':
		$icon = '<span class="elementor_btn_icon_container"><span class="elementor_gt3_btn_icon '.esc_attr($settings['button_icon']).'"></span></span>';
		break;
	case 'image':
		if(isset($settings['image']['id']) && (bool) $settings['image']['id']) {
			$image = wp_get_attachment_image_src($settings['image']['id'],'full');
			if($image) {
				$image_obj = wp_prepare_attachment_for_js($settings['image']['id']);
				if (strpos($image[0],'.svg') !== false) {
					$icon = '<span class="elementor_btn_icon_container"><span class="icon_svg_btn">'.file_get_contents($image[0]).'</span></span>';
				} else {
					$icon = '<span class="elementor_btn_icon_container"><img src="'.aq_resize($image[0], (int)$settings['image_size']['size']*2, '', true, true, true).'" alt="'.$image_obj['alt'].'" style="width:'.esc_attr((int)$settings['image_size']['size']).'px;" /></span>';
				}
			}
		}
		break;
	default:
		break;
}
if(!empty($settings['button_title'])) {
	$text = '<span class="elementor_gt3_btn_text">'.esc_html($settings['button_title']).'</span>';
}

if($settings['icon_position'] == 'left') {
	$text = $icon.$text;
} else {
	$text = $text.$icon;
}

$widget->add_render_attribute('href', 'class', array(
	'button_size_elementor_'.$settings['button_size_elementor'],
	'alignment_'.$settings['button_alignment'],
	'border_icon_'.$settings['btn_icon'],
	'hover_'.$settings['button_hover'],
	'btn_icon_position_'.$settings['icon_position'],
));
if(empty($settings['link']['url'])) {
	$settings['link']['url'] = '#';
}
$widget->add_render_attribute('href', 'href', esc_url($settings['link']['url']));

if($settings['link']['is_external']) {
	$widget->add_render_attribute('href', 'target', '_blank');
}

if(!empty($settings['link']['nofollow'])) {
	$widget->add_render_attribute('href', 'rel', 'nofollow');
}

?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<a <?php $widget->print_render_attribute_string('href') ?>>
				<span class="gt3_module_button__container">
					<?php if($settings['button_hover'] == 'type5') { ?>
						<?=$text?>
						<span class="gt3_module_button__cover front"></span>
						<span class="gt3_module_button__cover back"></span>
					<?php } else { ?>
						<span class="gt3_module_button__cover front"><?=$text?></span>
						<?php if($settings['button_hover'] == 'type2') { ?><span class="gt3_module_button__cover back"><?=$text?></span><?php } ?>
					<?php } ?>
				</span>
		</a>
	</div>

<?php
$widget->print_data_settings($data_settings);




