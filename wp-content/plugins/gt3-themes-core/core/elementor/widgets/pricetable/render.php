<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_PriceTable $widget */


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
	'gt3_pricetable_module_wrapper',
	isset($settings['view_type']) ? esc_attr($settings['view_type']) : '',
));

?>
<div <?php $widget->print_render_attribute_string('wrapper') ?>>

	<table class="gt3_pricetable">
		<thead class="gt3_pricetable_header">
			<tr>
				<th></th>
				<?php 
					foreach($settings['items'] as $item) {

						$active_package = false;

						if (!empty($item['package_is_active']) && $item['package_is_active'] == 'yes') {
							$active_package = true;
						}

						?><th<?php 

						if($active_package){
							echo ' class="gt3_pricetable__active"'; 
						} 
						?>><?php

						?><div class="gt3_price_item-cost-elementor">
							<?php
							if(!empty($item['price_prefix'])) {
								echo '<span class="price_item_prefix-elementor">'.esc_html($item['price_prefix']).'</span>';
							}
							echo esc_html($item['price']);
							if(!empty($item['price_suffix'])) {
								echo '<span class="price_item_suffix-elementor">'.esc_html($item['price_suffix']).'</span>';
							}
							?>
						</div><?php

						if (!empty($item['title'])) {
							?><div class="price_item_title-elementor"><h3><?php echo esc_html($item['title']); ?></h3></div><?php
						}		

						?></th><?php
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?php 
				for ($i = 1; $i <= 8; $i++) {
					if (!empty($settings['content_item_title_'.$i])) {
						?><tr>
							<td class="gt3_pricetable__content_item_title"><?php echo esc_html($settings['content_item_title_'.$i]); ?></td>
							<?php 
								for ($j = 0; $j < count($settings['items']); $j++) {

									if ($settings['items'][$j]['content_item_content_'.$i]) {

										$active_package = false;

										if (!empty($settings['items'][$j]['package_is_active']) && $settings['items'][$j]['package_is_active'] == 'yes') {
											$active_package = true;
										}

										?><td<?php 

										if($active_package){
											echo ' class="gt3_pricetable__active"'; 
										} 
										?>><?php echo wp_kses_post($settings['items'][$j]['content_item_content_'.$i]); ?></td><?php
									}

								}
							?>
						</tr><?php
					}
				}

			$buttons_td = '';

			ob_start();
			for ($j = 0; $j < count($settings['items']); $j++) {

				$active_package = false;

				if (!empty($settings['items'][$j]['package_is_active']) && $settings['items'][$j]['package_is_active'] == 'yes') {
					$active_package = true;
				}


				?><td<?php 
					if($active_package){
						echo ' class="gt3_pricetable__active"'; 
					} 
				?>><?php 

				if(!empty($settings['items'][$j]['button_text']) && !empty($settings['items'][$j]['button_link']['url'])) {
					$widget->add_render_attribute('link', 'href', $settings['items'][$j]['button_link']['url']);
					$widget->add_render_attribute('link', 'class', 'shortcode_button button_size_normal');
					if(!empty($settings['items'][$j]['button_link']['is_external'])) {
						$widget->add_render_attribute('link', 'target', '_blank');
					}
					if(!empty($settings['items'][$j]['button_link']['nofollow'])) {
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
							<span><?php echo esc_html($settings['items'][$j]['button_text']) ?></span>
							<?php
							if($settings['button_icon_position'] == 'right' && (bool) $settings['button_icon_en']) {
								echo '<div '.$widget->get_render_attribute_string('button_icon').'></div>';
							}
							?>
						</a>
					</div>
					<?php
					// Button end
					if(!empty($settings['items'][$j]['label_text'])) {
						?><div class="gt3_pricetable__lavel"><?php echo esc_html($settings['items'][$j]['label_text']); ?></div><?php
					}
				}


				?></td><?php
			}
			$buttons_td .= ob_get_clean();

			if (!empty($buttons_td)) {
				?><tr><td></td><?php
				echo $buttons_td;
				?></tr><?php
			}

			?>
		</tbody>
	</table>
</div>