<?php

class PAFE_Add_To_Cart_Checkbox extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-add-to-cart-checkbox';
	}

	public function get_title() {
		return __( 'Add To Cart Checkbox', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	public function get_categories() {
		return [ 'pafe-woocommerce-sales-funnels' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'add to cart' ];
	}

	protected function _register_controls() {

		if ( class_exists( 'WooCommerce' ) ) {

			$this->start_controls_section(
				'pafe_add_to_cart_checkbox_section',
				[
					'label' => __( 'Add To Cart Checkbox', 'pafe' ),
				]
			);

			$this->add_control(
				'pafe_add_to_cart_checkbox_show_quantity',
				[
					'label' => __( 'Show Quantity', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'pafe_add_to_cart_checkbox_show_price',
				[
					'label' => __( 'Show Price', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'pafe_add_to_cart_checkbox_show_header',
				[
					'label' => __( 'Show Header', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'pafe_add_to_cart_checkbox_product_id',
				[
					'label' => __( 'Product ID* (Required)', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$repeater->add_control(
				'pafe_add_to_cart_checkbox_quantity',
				[
					'label' => __( 'Quantity* (Required)', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => 1,
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$repeater->add_control(
				'pafe_add_to_cart_checkbox_auto_get_all_product_variations',
				[
					'label' => __( 'Auto get all product variations', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$repeater->add_control(
				'pafe_add_to_cart_checkbox_variation_id',
				[
					'label' => __( 'Variation ID', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'condition' => [
						'pafe_add_to_cart_checkbox_auto_get_all_product_variations' => '',
					],
				]
			);

			$repeater->add_control(
				'pafe_add_to_cart_checkbox_label',
				[
					'label' => __( 'Label', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'condition' => [
						'pafe_add_to_cart_checkbox_auto_get_all_product_variations' => '',
					],
				]
			);

			$this->add_control(
				'pafe_add_to_cart_checkbox_list',
				array(
					'type'    => Elementor\Controls_Manager::REPEATER,
					'fields'  => $repeater->get_controls(),
					'title_field' => '{{{ pafe_add_to_cart_checkbox_product_id }}}',
				)
			);

			$this->end_controls_section();
    	}

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if (!empty($settings['pafe_add_to_cart_checkbox_list'])) :
			$list = $settings['pafe_add_to_cart_checkbox_list'];
	?>
		<form class="pafe-add-to-cart-check-box" data-pafe-add-to-cart-check-box>
			<table class="pafe-add-to-cart-check-box__table">
				<thead>
				<?php if(!empty($settings['pafe_add_to_cart_checkbox_show_header'])) : ?>
					<tr class="pafe-add-to-cart-check-box__header">
						<th class="pafe-add-to-cart-check-box__heading"><?php echo __('Product','pafe'); ?></th>
						<?php if(!empty($settings['pafe_add_to_cart_checkbox_show_quantity'])) : ?>
							<th class="pafe-add-to-cart-check-box__heading"><?php echo __('Quantity','pafe'); ?></th>
						<?php endif; ?>
						<?php if(!empty($settings['pafe_add_to_cart_checkbox_show_price'])) : ?>
							<th class="pafe-add-to-cart-check-box__heading"><?php echo __('Price','pafe'); ?></th>
						<?php endif; ?>
					</tr>
				</thead>
				<?php endif; ?>
				<tbody>
				<!-- <tr class="pafe-add-to-cart-check-box__list"> -->
					<?php
						foreach ($list as $item) :
							if (!empty($item['pafe_add_to_cart_checkbox_product_id'])) :
								$product_id = intval( $item['pafe_add_to_cart_checkbox_product_id'] );
								$product = wc_get_product( $product_id );
								$product_name = $product->get_title() . ' ';

								if ($product->is_type('variable') && $item['pafe_add_to_cart_checkbox_auto_get_all_product_variations'] == 'yes') {
									$product_variations = new WC_Product_Variable( $product_id );
									$variations = $product_variations->get_available_variations();
									
									foreach($variations as $variation ){
								        $variation_id = $variation['variation_id'];

								        $attributes = array();
								        foreach( $variation['attributes'] as $key => $value ){
								            $taxonomy = str_replace('attribute_', '', $key );
								            $taxonomy_label = get_taxonomy( $taxonomy )->labels->singular_name;
								            $term_name = get_term_by( 'slug', $value, $taxonomy )->name;
								            $attributes[] = $taxonomy_label.': '.$term_name;
								        }

								        $label = $product_name . implode( ' | ', $attributes );

								        $price_html = strip_tags($variation['price_html']);

										$product_information = array(
											'product_id' => $product_id,
											'variation_id' => $variation_id,
											'quantity' => $item['pafe_add_to_cart_checkbox_quantity'],
											'price_html' => $price_html,
										);

										?>
											<tr class="pafe-add-to-cart-check-box__item">
												<td class="pafe-add-to-cart-check-box__item-field-group">
													<label class="pafe-add-to-cart-check-box__item-field-label" data-pafe-add-to-cart-check-box-item='<?php echo json_encode($product_information); ?>'>
														<input type="checkbox" class="pafe-add-to-cart-check-box__item-field-input" name="pafe-add-to-cart-check-box-item-<?php echo $product_id; ?>">
														<?php echo "<img src=" . $variation['image']['gallery_thumbnail_src'] .">"; ?>
														<?php echo $label; ?>
													</label>
												</td>
												<?php if(!empty($settings['pafe_add_to_cart_checkbox_show_quantity'])) : ?>
													<td class="pafe-add-to-cart-check-box__item-quantity">
														<input type="number" min="0" value="<?php echo $product_information['quantity']; ?>" name="pafe-add-to-cart-check-box-item-quantity-<?php echo $product_id; ?>">
													</td>
												<?php endif; ?>
												<?php if(!empty($settings['pafe_add_to_cart_checkbox_show_price'])) : ?>
													<td class="pafe-add-to-cart-check-box__item-price">
														<?php echo $product_information['price_html']; ?>
													</td>
												<?php endif; ?>
											</tr>
										<?php
								    }
								} else {

									$label = '';
									$variation_id = $item['pafe_add_to_cart_checkbox_variation_id'];
									$price_html = strip_tags($product->get_price_html());

									if ($product->is_type('variable') && $item['pafe_add_to_cart_checkbox_auto_get_all_product_variations'] == '') {
										$product_variations = new WC_Product_Variable( $product_id );
										$variations = $product_variations->get_available_variations();
										
										foreach($variations as $variation ){
									        if( $variation_id == $variation['variation_id'] ) {
									        	$attributes = array();
										        foreach( $variation['attributes'] as $key => $value ){
										            $taxonomy = str_replace('attribute_', '', $key );
										            $taxonomy_label = get_taxonomy( $taxonomy )->labels->singular_name;
										            $term_name = get_term_by( 'slug', $value, $taxonomy )->name;
										            $attributes[] = $taxonomy_label.': '.$term_name;
										        }

										        $label = $product_name . implode( ' | ', $attributes );

										        $price_html = strip_tags($variation['price_html']);
									        }
								        }
								    }

									if (!empty($item['pafe_add_to_cart_checkbox_label'])) {
										$label = $product_name . $item['pafe_add_to_cart_checkbox_label'];
									}

									$product_information = array(
										'product_id' => $product_id,
										'variation_id' => $variation_id,
										'quantity' => $item['pafe_add_to_cart_checkbox_quantity'],
										'price_html' => $price_html,
									);
					?>
									<tr class="pafe-add-to-cart-check-box__item">
										<td class="pafe-add-to-cart-check-box__item-field-group">
											<label class="pafe-add-to-cart-check-box__item-field-label" data-pafe-add-to-cart-check-box-item='<?php echo json_encode($product_information); ?>'>
												<input type="checkbox" class="pafe-add-to-cart-check-box__item-field-input" name="pafe-add-to-cart-check-box-item-<?php echo $product_id; ?>">
												<?php echo $label; ?>
											</label>
										</td>
										<?php if(!empty($settings['pafe_add_to_cart_checkbox_show_quantity'])) : ?>
											<td class="pafe-add-to-cart-check-box__item-quantity">
												<input type="number" min="0" value="<?php echo $product_information['quantity']; ?>" name="pafe-add-to-cart-check-box-item-quantity-<?php echo $product_id; ?>">
											</td>
										<?php endif; ?>
										<?php if(!empty($settings['pafe_add_to_cart_checkbox_show_price'])) : ?>
											<td class="pafe-add-to-cart-check-box__item-price">
												<?php echo $product_information['price_html']; ?>
											</td>
										<?php endif; ?>
									</tr>
								<?php } ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<!-- </tr> -->
				</tbody>
			</table>
		</form>
	<?php
		endif;
	}
}
