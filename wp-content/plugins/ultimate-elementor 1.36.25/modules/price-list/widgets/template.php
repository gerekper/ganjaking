<?php
/**
 * UAEL Price List Module Template.
 *
 * @package UAEL
 */

?>

<div class="uael-price-list uael-price-list-<?php echo esc_attr( $settings['image_position'] ); ?> uael-pl-price-position-<?php echo esc_attr( $settings['price_position'] ); ?>">

	<?php

	foreach ( $settings['price_list'] as $index => $item ) {

		$title_key = $this->get_repeater_setting_key( 'title', 'price_list', $index );
		$this->add_inline_editing_attributes( $title_key, 'basic' );

		$description_key = $this->get_repeater_setting_key( 'item_description', 'price_list', $index );

		$this->add_render_attribute( $description_key, 'class', 'uael-price-list-description' );
		$this->add_inline_editing_attributes( $description_key, 'basic' );

		$this->add_render_attribute( 'item_wrap' . $index, 'class', 'uael-price-list-item' );

		if ( $settings['hover_animation'] ) {
			$this->add_render_attribute( 'item_wrap' . $index, 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}
		$link_complete_box = $settings['link_complete_box'];
		?>
			<?php
			if ( 'yes' === $link_complete_box ) {
				echo wp_kses_post( $this->render_item_header( $item, $settings ) );
			}
			?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'item_wrap' . $index ) ); ?>>

			<?php if ( 'none' !== $settings['image_position'] && ! empty( $item['image']['url'] ) ) { ?>
				<div class="uael-price-list-image">
					<?php $this->render_image( $item, $settings ); ?>
				</div>
			<?php } ?>

			<div class="uael-price-list-text">
				<div class="uael-price-list-header">
					<?php
					if ( empty( $link_complete_box ) || 'no' === $link_complete_box ) {

						echo $this->render_item_header( $item, $settings ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						echo '<div class="uael-price-list-title">';
					}
					?>
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( $title_key ) ); ?>><?php echo wp_kses_post( $item['title'] ); ?></span>
					<?php

					if ( empty( $link_complete_box ) || 'no' === $link_complete_box ) {
						echo wp_kses_post( $this->render_item_footer( $item ) );
					} else {
						echo '</div>';
					}

					if ( 'above' !== $settings['image_position'] && 'below' !== $settings['price_position'] ) {
						?>
						<span class="uael-price-list-separator"></span>
					<?php } ?>

					<?php
					if ( 'below' !== $settings['price_position'] && 'above' !== $settings['image_position'] ) {

						$this->get_price( $index, 'inner' );

					}
					?>
				</div>

				<?php if ( '' !== $item['item_description'] ) { ?>

				<p <?php echo wp_kses_post( $this->get_render_attribute_string( $description_key ) ); ?>><?php echo wp_kses_post( $item['item_description'] ); ?></p>

				<?php } ?>

				<?php
						$this->get_price( $index, 'outer' );
				?>
			</div>
		</div>
		<?php
		if ( 'yes' === $link_complete_box ) {
			echo wp_kses_post( $this->render_item_footer( $item ) );
		}
		?>
	<?php } ?>
</div>
