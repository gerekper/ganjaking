<?php

namespace ElementPack\Modules\WcProducts\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Table extends Elementor_Skin_Base {

	public function get_id() {
		return 'bdt-table';
	}

	public function get_title() {
		return esc_html__('Table', 'bdthemes-element-pack');
	}

	public function render_header() {


		$settings = $this->parent->get_settings();
		$id = $this->parent->get_id();
		$page_length = (isset($settings['show_per_page']) && !empty($settings['show_per_page'])) ? $settings['show_per_page'] : '10';


		$this->parent->add_render_attribute('wc-products', 'class', ['bdt-wc-products', 'bdt-wc-products-skin-table']);

		// $orderColumn = 	$settings['product_order_column'];

		$this->parent->add_render_attribute(
			[
				'wc-products' => [
					'data-settings' => [
						wp_json_encode([
							"order"			=> [],
							'paging'    	=> ($settings['show_pagination']) ? true : false,
							'info'      	=> ($settings['show_pagination'] and $settings['show_info']) ? true : false,
							'bLengthChange' => ($settings['show_change_length']) ? true : false,
							'searching' 	=> ($settings['show_searching']) ? true : false,
							'ordering'  	=> ($settings['show_ordering']) ? true : false,
							'pageLength'  	=> (int) $page_length,
							// 'orderColumn'  	=> $orderColumn,
							'orderColumnQry' => $settings['posts_order'],
							'hideHeader'	=> (!empty($settings['hide_header']) ? $settings['hide_header'] : 'no'),
						])
					]
				]
			]
		);

?>
		<div <?php echo $this->parent->get_render_attribute_string('wc-products'); ?>>

			<?php

		}

		public function render_loop_item() {
			$settings = $this->parent->get_settings_for_display();
			$id = 'bdt-wc-products-skin-table-' . $this->parent->get_id();
			$this->parent->render_query($settings['posts_per_page']);
			$wp_query = $this->parent->get_query();

			if ($wp_query->have_posts()) {

				$this->parent->add_render_attribute('wc-product-table', 'class', ['bdt-table-middle', 'bdt-wc-product', 'bdt-table', 'bdt-table-striped']);

				$this->parent->add_render_attribute('wc-product-table', 'id', esc_attr($id));

				if ($settings['cell_border']) {
					$this->parent->add_render_attribute('wc-product-table', 'class', 'cell-border');
				}

				if ($settings['stripe']) {
					$this->parent->add_render_attribute('wc-product-table', 'class', 'stripe');
				}

				if ($settings['hover_effect']) {
					$this->parent->add_render_attribute('wc-product-table', 'class', 'hover');
				}

				$this->parent->add_render_attribute('bdt-wc-product-title', 'class', 'bdt-wc-product-title');

			?>
				<table <?php echo $this->parent->get_render_attribute_string('wc-product-table'); ?>>
					<thead>
						<tr>
							<?php if ($settings['show_thumb']) : ?>
								<th class="bdt-thumb" data-orderable="false"><?php esc_html_e('Image', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>

							<?php if ($settings['show_title']) : ?>
								<th class="bdt-title"><?php esc_html_e('Title', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>

							<?php if ($settings['show_excerpt']) : ?>
								<th class="bdt-excerpt "><?php esc_html_e('Description', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>


							<?php if ($settings['show_categories']) : ?>
								<th class="bdt-ep-align bdt-categories "><?php esc_html_e('Categories', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>

							<?php if ($settings['show_tags']) : ?>
								<th class="bdt-ep-align bdt-tags" data-orderable="false"><?php esc_html_e('Tags', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>

							<?php if ($settings['show_rating']) : ?>
								<th class="bdt-ep-align bdt-rating" data-orderable="false"><?php esc_html_e('Rating', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>

							<?php if ($settings['show_price']) : ?>
								<th class="bdt-ep-align bdt-price "><?php esc_html_e('Price', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>

							<?php if ($settings['show_quick_view']) : ?>
								<th class="bdt-ep-align bdt-quick-view-heading" data-orderable="false"><?php esc_html_e('Quick View', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>

							<?php if ($settings['show_quantity']) : ?>
								<th class="bdt-ep-align bdt-cart" data-orderable="false"><?php esc_html_e('Quantity', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>

							<?php if ($settings['show_cart']) : ?>
								<th class="bdt-ep-align bdt-cart" data-orderable="false"><?php esc_html_e('Cart', 'bdthemes-element-pack'); ?></th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php
						while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
							<?php global $product; ?>
							<tr>
								<?php if ($settings['show_thumb']) : ?>
									<td class="bdt-thumb">
										<?php $this->render_image($settings); ?>
									</td>
								<?php endif; ?>


								<?php if ($settings['show_title']) : ?>
									<td class="bdt-title">
										<<?php echo esc_html($settings['title_tags']); ?> <?php echo $this->parent->get_render_attribute_string('bdt-wc-product-title'); ?>>
											<a href="<?php the_permalink(); ?>" class="bdt-link-reset">
												<?php the_title(); ?>
											</a>
										</<?php echo esc_html($settings['title_tags']); ?>>
										<span class="bdt-text-muted bdt-text-small"><?php echo esc_html($product->get_sku()); ?></span>
									</td>
								<?php endif; ?>

								<?php if ($settings['show_excerpt']) : ?>
									<td class="bdt-excerpt">
										<div class="bdt-wc-product-excerpt">
											<?php echo wp_kses_post(element_pack_custom_excerpt($settings['excerpt_limit'])); ?>
										</div>
									</td>
								<?php endif; ?>

								<?php if ($settings['show_categories']) : ?>
									<td class="bdt-ep-align bdt-categories">
										<span class="bdt-wc-product-categories">
											<?php echo wc_get_product_category_list(get_the_ID(), ', ', '<span>', '</span>'); ?>
										</span>
									</td>
								<?php endif; ?>


								<?php if ($settings['show_tags']) : ?>
									<td class="bdt-ep-align">
										<span class="bdt-wc-product-tags bdt-tags">
											<?php echo wc_get_product_tag_list(get_the_ID(), ', ', '<span>', '</span>'); ?>
										</span>
									</td>
								<?php endif; ?>


								<?php if ($settings['show_rating']) : ?>
									<td class="bdt-ep-align bdt-rating">
										<div class="bdt-wc-rating">
											<?php woocommerce_template_loop_rating(); ?>
										</div>
									</td>
								<?php endif; ?>


								<?php if ($settings['show_price']) : ?>
									<td class="bdt-ep-align bdt-price" data-order="<?php echo $product->get_price(); ?>">
										<div class="bdt-wc-product-price">
											<?php woocommerce_template_single_price(); ?>
										</div>
									</td>
								<?php endif; ?>


								<?php if ($settings['show_quick_view']) : ?>
									<td class="bdt-ep-align bdt-quick-view-title">
										<?php $this->parent->render_quick_view($product->get_id()) ?>
									</td>
								<?php endif; ?>

								<?php if ($settings['show_quantity']) : ?>
									<td class="bdt-ep-align">
										<div class="bdt-wc-quantity">

											<?php if ($product->is_purchasable() and $product->is_in_stock()) : ?>
												<?php if ($product->is_type('simple')) : ?>
													<?php woocommerce_quantity_input([], $product); ?>
												<?php endif; ?>
											<?php endif; ?>
										</div>
									</td>
								<?php endif; ?>

								<?php if ($settings['show_cart']) : ?>
									<td class="bdt-ep-align bdt-cart">
										<div class="bdt-wc-add-to-cart">
											<?php woocommerce_template_loop_add_to_cart(); ?>
										</div>
									</td>
								<?php endif; ?>

							</tr>

						<?php endwhile;
						wp_reset_postdata(); ?>

					</tbody>
				</table>
			<?php

			} else {
				echo '<div class="bdt-alert-warning" data-bdt-alert>' . esc_html__('Ops! There is no product', 'bdthemes-element-pack') . '<div>';
			}
		}

		public function render_image($settings) {
			$this->parent->add_render_attribute('product_image_wrapper', 'class', 'bdt-wc-product-image bdt-display-inline-block', true);

			if ('yes' === $settings['open_thumb_in_lightbox']) {
				$this->parent->add_render_attribute('product_image', 'data-elementor-open-lightbox', 'no', true);
				$img_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
				$this->parent->add_render_attribute('product_image', 'href', $img_url[0], true);
				$this->parent->add_render_attribute('product_image_wrapper', 'bdt-lightbox', '');
			} else {
				$this->parent->add_render_attribute('product_image', 'href', get_the_permalink(), true);
			}

			?>
			<div <?php echo $this->parent->get_render_attribute_string('product_image_wrapper'); ?>>
				<a <?php echo $this->parent->get_render_attribute_string('product_image'); ?>>
					<img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id(), 'thumbnail'); ?>" alt="<?php echo get_the_title(); ?>">
				</a>
			</div>
	<?php
		}

		public function render() {
			$this->render_header();
			$this->render_loop_item();
			$this->parent->render_footer();
		}
	}
