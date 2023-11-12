<?php
/**
 * UAEL Business Scheduler Module Template.
 *
 * @package UAEL
 */

	// Main wrapper class.
?>
	<div class="uael-business-scheduler-box-wrapper">
	<?php
	if ( count( $settings['business_days_timings'] ) ) {
		$count = 0;
		?>
		<div class="uael-days">
			<?php
			foreach ( $settings['business_days_timings'] as $item ) {
				$repeater_setting__enter_day = $this->get_repeater_setting_key( 'enter_day', 'business_days_timings', $count );

				$this->add_inline_editing_attributes( $repeater_setting__enter_day );
				$repeater_setting__enter_time = $this->get_repeater_setting_key( 'enter_time', 'business_days_timings', $count );
				$this->add_inline_editing_attributes( $repeater_setting__enter_time );

				$this->add_render_attribute( 'uael-inner-element', 'class', 'uael-inner' );
				$this->add_render_attribute( 'uael-inner-heading-time', 'class', 'inner-heading-time' );
				$this->add_render_attribute( 'uael-bs-background' . $item['_id'], 'class', 'elementor-repeater-item-' . $item['_id'] );
				$this->add_render_attribute( 'uael-bs-background' . $item['_id'], 'class', 'top-border-divider' );
				if ( 'yes' === $item['highlight_this'] ) {
					$this->add_render_attribute( 'uael-bs-background' . $item['_id'], 'class', 'uael-highlight-background' );
				} elseif ( 'yes' === $settings['striped_effect_feature'] ) {
					$this->add_render_attribute( 'uael-bs-background' . $item['_id'], 'class', 'stripes' );
				} else {
					$this->add_render_attribute( 'uael-bs-background' . $item['_id'], 'class', 'bs-background' );
				}
				$this->add_render_attribute( 'uael-highlight-day' . $item['_id'], 'class', 'heading-date' );
				$this->add_render_attribute( 'uael-highlight-time' . $item['_id'], 'class', 'heading-time' );
				if ( 'yes' === $item['highlight_this'] ) {
					$this->add_render_attribute( 'uael-highlight-day' . $item['_id'], 'class', 'uael-business-day-highlight' );
					$this->add_render_attribute( 'uael-highlight-time' . $item['_id'], 'class', 'uael-business-timing-highlight' );
				} else {
					$this->add_render_attribute( 'uael-highlight-day' . $item['_id'], 'class', 'uael-business-day' );
					$this->add_render_attribute( 'uael-highlight-time' . $item['_id'], 'class', 'uael-business-time' );
				}
				?>
				<!-- CURRENT_ITEM div -->
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-bs-background' . esc_attr( $item['_id'] ) ) ); ?>>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-inner-element' ) ); ?>>
						<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-highlight-day' . esc_attr( $item['_id'] ) ) ); ?>>
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( $repeater_setting__enter_day ) ); ?>><?php echo wp_kses_post( $item['enter_day'] ); ?></span>
						</span>

						<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-highlight-time' . esc_attr( $item['_id'] ) ) ); ?>>
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael-inner-heading-time' ) ); ?>>
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( $repeater_setting__enter_time ) ); ?>><?php echo wp_kses_post( $item['enter_time'] ); ?></span>
							</span>
						</span>
					</div>
				</div>
				<?php
				$count++;
			}
			?>
		</div>
	<?php	} ?>
	</div>
	<?php

