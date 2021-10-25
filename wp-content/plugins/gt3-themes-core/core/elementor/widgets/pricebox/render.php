<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_PriceBox $widget */


$settings = array(
	'header_img'       => array( 'url' => Utils::get_placeholder_image_src(), ),
	'pre_title'        => '',
	'header_img_2'     => array( 'url' => Utils::get_placeholder_image_src(), ),
	'title'            => '',
	'price_prefix'     => '',
	'price'            => '',
	'price_suffix'     => '',
	'content'          => '',
	'button_text'      => '',
	'button_link'      => array( 'url' => '#', 'is_external' => false, 'nofollow' => false, ),
	'add_label'        => false,
	'label_text'       => '',
	'button_border_en' => false,
	'button_border'    => '',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('gt3_item_cost_wrapper', 'class', 'gt3_item_cost_wrapper');

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_pricebox_module_wrapper',
	isset($settings['view_type']) ? esc_attr($settings['view_type']) : '',
));
?>
<div <?php $widget->print_render_attribute_string('wrapper') ?>>
 	<?php if (isset($settings['view_type']) && $settings['view_type'] == 'type2') { ?>
		<div class="gt3_price_item-cost-elementor">
			<?php
			if(!empty($settings['price_prefix'])) {
				echo '<span class="price_item_prefix-elementor">'.esc_html($settings['price_prefix']).'</span>';
			}
			echo esc_html($settings['price']);
			if(!empty($settings['price_suffix'])) {
				echo '<span class="price_item_suffix-elementor">'.esc_html($settings['price_suffix']).'</span>';
			}
			?>
		</div>
	<?php } ?>
	<div class="gt3_price_item-elementor">
		<div class="gt3_price_item_wrapper-elementor">
			<div <?php $widget->print_render_attribute_string('gt3_item_cost_wrapper') ?>>
				<?php
				if(!empty($settings['header_img']['id'])) {
					echo '<div class="img_wrapper-price"><img src="'.$settings['header_img']['url'].'" alt="price_img" /></div>';
				}
				if(!empty($settings['pre_title'])) { ?>
					<div class="price_item_description-elementor"><?php echo esc_html($settings['pre_title']) ?></div>
				<?php }

				if(!empty($settings['title'])) { ?>
					<div class="price_item_title-elementor"><h3><?php echo esc_html($settings['title']) ?></h3></div>
				<?php }
				if(!empty($settings['header_img_2']['id'])) {
					echo '<div class="img_wrapper-price_2"><img src="'.$settings['header_img_2']['url'].'" alt="price_img_2" /></div>';
				}
				?>
			</div>
			<div class="gt3_price_item-cost-elementor">
				<?php
				if(!empty($settings['price_prefix'])) {
					echo '<span class="price_item_prefix-elementor">'.esc_html($settings['price_prefix']).'</span>';
				}
				echo esc_html($settings['price']);
				if(!empty($settings['price_suffix'])) {
					echo '<span class="price_item_suffix-elementor">'.esc_html($settings['price_suffix']).'</span>';
				}
				?>
			</div>
			<?php
			if(!empty($settings['add_label']) && !empty($settings['label_text'])) {
				echo '<div class="label_text"><span>'.esc_html($settings['label_text']).'</span></div>';
			}
			?>
			<div class="gt3_price_item_body-elementor">
				<div class="items_text-price"><?php echo ''.$settings['content'] ?></div>
				<?php
				// Button
				if(!empty($settings['button_text']) && !empty($settings['button_link']['url'])) {
					$widget->add_render_attribute('link', 'href', $settings['button_link']['url']);
					$widget->add_render_attribute('link', 'class', 'shortcode_button button_size_normal');
					if(!empty($settings['button_link']['is_external'])) {
						$widget->add_render_attribute('link', 'target', '_blank');
					}
					if(!empty($settings['button_link']['nofollow'])) {
						$widget->add_render_attribute('link', 'rel', 'nofollow');
					}
					if((bool) $settings['button_border_en']) {
						$widget->add_render_attribute('link', 'class', 'bordered');
					}

					$widget->add_render_attribute('button_icon', 'class', 'price-button-icon');

					if((bool) $settings['button_icon_en']) {
						$widget->add_render_attribute('button_icon', 'class', $settings['button_icon']);
					}
					?>
					<div class="price_button-elementor">
						<a <?php $widget->print_render_attribute_string('link') ?>>
							<?php
							if($settings['button_icon_position'] == 'left' && (bool) $settings['button_icon_en']) {
								echo '<div '.$widget->get_render_attribute_string('button_icon').'></div>';
							}
							?>
							<span><?php echo esc_html($settings['button_text']) ?></span>
							<?php
							if($settings['button_icon_position'] == 'right' && (bool) $settings['button_icon_en']) {
								echo '<div '.$widget->get_render_attribute_string('button_icon').'></div>';
							}
							?>
						</a>
					</div>
					<?php
					// Button end
					if($settings['package_is_active'] == 'yes') {
						echo '<div class="featured-label_icon-price"></div>';
					}
				}
				?>
			</div>
		</div>
	</div>
</div>
<?php


