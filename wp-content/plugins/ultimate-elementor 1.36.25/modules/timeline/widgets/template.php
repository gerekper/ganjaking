<?php
/**
 * UAEL Timeline Module Template.
 *
 * @package UAEL
 */

$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();
use UltimateElementor\Classes\UAEL_Helper;

$this->add_render_attribute( 'timeline_wrapper', 'class', 'uael-timeline-wrapper' );
$this->add_render_attribute( 'timeline_wrapper', 'class', 'uael-timeline-node' );
if ( 'yes' === $settings['timeline_responsive'] ) {
	$this->add_render_attribute( 'timeline_wrapper', 'class', 'uael-timeline-res-right' );
}
if ( 'yes' === $settings['timeline_cards_box_shadow'] ) {
	$this->add_render_attribute( 'timeline_main', 'class', 'uael-timeline-shadow-yes' );
}

$this->add_render_attribute( 'timeline_main', 'class', 'uael-timeline-main' );
$this->add_render_attribute( 'timeline_days', 'class', 'uael-days' );
$this->add_render_attribute( 'line', 'class', 'uael-timeline__line' );
$this->add_render_attribute( 'line-inner', 'class', 'uael-timeline__line__inner' );
?>
<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_wrapper' ) ); ?>>
	<?php
		$count        = 0;
		$current_side = '';
	?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_main' ) ); ?>>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_days' ) ); ?>>
			<?php foreach ( $dynamic['timelines'] as $index => $item ) { ?>
				<?php
				$this->add_render_attribute(
					array(
						'timeline_single_content' => array( 'class' => 'uael-date' ),
					)
				);

				$heading_setting_key = $this->get_repeater_setting_key( 'timeline_single_heading', 'timelines', $index );
				$this->add_render_attribute( $heading_setting_key, 'class', 'uael-timeline-heading' );
				$this->add_inline_editing_attributes( $heading_setting_key, 'none' );


				$content_setting_key = $this->get_repeater_setting_key( 'timeline_single_content', 'timelines', $index );
				$this->add_render_attribute( $content_setting_key, 'class', 'uael-timeline-desc-content' );
				$this->add_inline_editing_attributes( $content_setting_key, 'advanced' );


				$date_setting_key = $this->get_repeater_setting_key( 'timeline_single_date', 'timelines', $index );
				$this->add_inline_editing_attributes( $date_setting_key, 'none' );


				if ( ! empty( $item['timeline_single_link']['url'] ) ) {

					$this->add_link_attributes( 'url_' . $item['_id'], $item['timeline_single_link'] );

					$timeline_link = $this->get_render_attribute_string( 'url_' . $item['_id'] );
				}
				$this->add_render_attribute( 'card_' . $item['_id'], 'class', 'timeline-icon-new' );
				$this->add_render_attribute( 'card_' . $item['_id'], 'class', 'out-view-timeline-icon' );

				$this->add_render_attribute( 'current_' . $item['_id'], 'class', 'elementor-repeater-item-' . $item['_id'] );
				$this->add_render_attribute( 'current_' . $item['_id'], 'class', 'uael-timeline-field animate-border' );
				$this->add_render_attribute( 'current_' . $item['_id'], 'class', 'out-view' );
				$this->add_render_attribute( 'timeline_alignment' . $item['_id'], 'class', 'uael-day-new' );

				$this->add_render_attribute( 'data_alignment' . $item['_id'], 'class', 'uael-timeline-widget' );
				if ( 0 === $count % 2 ) {
					$current_side = 'Left';
				} else {
					$current_side = 'Right';
				}

				if ( 'Right' === $current_side ) {
					$this->add_render_attribute( 'timeline_alignment' . $item['_id'], 'class', 'uael-day-left' );
					$this->add_render_attribute( 'data_alignment' . $item['_id'], 'class', 'uael-timeline-left' );
				} else {
					$this->add_render_attribute( 'timeline_alignment' . $item['_id'], 'class', 'uael-day-right' );
					$this->add_render_attribute( 'data_alignment' . $item['_id'], 'class', 'uael-timeline-right' );
				}
				$this->add_render_attribute( 'timeline_events' . $item['_id'], 'class', 'uael-events-new' );
				$this->add_render_attribute( 'timeline_events_inner' . $item['_id'], 'class', 'uael-events-inner-new' );

				$this->add_render_attribute( 'timeline_content' . $item['_id'], 'class', 'uael-content' );

				if ( '' !== $item['timeline_single_heading'] || '' !== $item['timeline_single_content'] ) {
					?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'current_' . esc_attr( $item['_id'] ) ) ); ?>>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'data_alignment' . esc_attr( $item['_id'] ) ) ); ?>>
							<div class="uael-timeline-marker">
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'card_' . esc_attr( $item['_id'] ) ) ); ?>>
									<?php

									if ( 'yes' !== $item['timeline_content_advanced'] ) {

										if ( UAEL_Helper::is_elementor_updated() ) {
											if ( isset( $settings['timeline_all_icon'] ) || isset( $settings['new_timeline_all_icon'] ) ) {
												$timeline_all_migrated = isset( $settings['__fa4_migrated']['new_timeline_all_icon'] );
												$timeline_all_is_new   = ! isset( $settings['timeline_all_icon'] );

												if ( $timeline_all_migrated || $timeline_all_is_new ) {

													\Elementor\Icons_Manager::render_icon( $settings['new_timeline_all_icon'], array( 'aria-hidden' => 'true' ) );
												} elseif ( ! empty( $settings['timeline_all_icon'] ) ) {
													?>
													<i class="<?php echo esc_attr( $settings['timeline_all_icon'] ); ?>" aria-hidden="true"></i>
													<?php
												}
											}
										} elseif ( ! empty( $settings['timeline_all_icon'] ) ) {
											?>
										<i class="<?php echo esc_attr( $settings['timeline_all_icon'] ); ?>" aria-hidden="true"></i>
											<?php
										}
									} else {
										if ( UAEL_Helper::is_elementor_updated() ) {
											if ( isset( $item['timeline_single_icon'] ) || isset( $item['new_timeline_single_icon'] ) ) {

												$timeline_single_migrated = isset( $item['__fa4_migrated']['new_timeline_single_icon'] );
												$timeline_single_is_new   = ! isset( $item['timeline_single_icon'] );


												if ( $timeline_single_migrated || $timeline_single_is_new ) {
													\Elementor\Icons_Manager::render_icon( $item['new_timeline_single_icon'], array( 'aria-hidden' => 'true' ) );
												} elseif ( ! empty( $item['timeline_single_icon'] ) ) {
													?>
													<i class="<?php echo esc_attr( $item['timeline_single_icon'] ); ?>" aria-hidden="true"></i>
													<?php
												}
											}
										} elseif ( isset( $item['timeline_single_icon'] ) ) {
											?>
											<i class="<?php echo esc_attr( $item['timeline_single_icon'] ); ?>" aria-hidden="true"></i>
											<?php
										}
									}
									?>
								</span>
							</div>

							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_alignment' . esc_attr( $item['_id'] ) ) ); ?>>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_events' . esc_attr( $item['_id'] ) ) ); ?>>
									<?php if ( ! empty( $item['timeline_single_link']['url'] ) ) { ?>
										<a <?php echo wp_kses_post( $timeline_link ); ?> >
									<?php } ?>
									<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_events_inner' . esc_attr( $item['_id'] ) ) ); ?>>
										<?php
										if ( '' !== $item['timeline_single_date'] ) {
											?>
											<div class="uael-timeline-date-hide uael-date-inner"><div class="inner-date-new"><p <?php echo wp_kses_post( $this->get_render_attribute_string( $date_setting_key ) ); ?>><?php echo esc_html( $item['timeline_single_date'] ); ?></p></div>
											</div>
										<?php } ?>
										<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'timeline_content' . esc_attr( $item['_id'] ) ) ); ?>>
											<?php do_action( 'uael_timeline_above_heading', $item ); ?>
											<?php
											if ( '' !== $item['timeline_single_heading'] ) {
												$heading_size_tag = UAEL_Helper::validate_html_tag( $settings['timeline_heading_tag'] );
												?>
											<div class="uael-timeline-heading-text">
												<<?php echo esc_attr( $heading_size_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( $heading_setting_key ) ); ?>><?php echo $this->parse_text_editor( $item['timeline_single_heading'] ); ?></<?php echo esc_attr( $heading_size_tag ); ?>> <?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</div>
											<?php } ?>
											<?php do_action( 'uael_timeline_below_heading', $item ); ?>
											<?php do_action( 'uael_timeline_above_content', $item ); ?>
											<?php
											if ( '' !== $item['timeline_single_content'] ) {
												?>
												<div <?php echo wp_kses_post( $this->get_render_attribute_string( $content_setting_key ) ); ?>><?php echo $this->parse_text_editor( $item['timeline_single_content'] ); ?></div> <?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<?php } ?>
											<?php do_action( 'uael_timeline_below_content', $item ); ?>
										</div>
											<?php if ( 'yes' === $settings['show_card_arrow'] ) { ?>
												<div class="uael-timeline-arrow"></div>
											<?php } ?>
									</div>
									<?php if ( ! empty( $item['timeline_single_link']['url'] ) ) { ?>
										</a>
									<?php } ?>
								</div>
							</div>
							<?php if ( 'center' === $settings['timeline_align'] ) { ?>
								<div class="uael-timeline-date-new">
									<div class="uael-date-new"><div class="inner-date-new"><div <?php echo wp_kses_post( $this->get_render_attribute_string( $date_setting_key ) ); ?>><?php echo esc_html( $item['timeline_single_date'] ); ?></div></div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php
				}
				++$count;
				?>
			<?php } ?>
		</div>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'line' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'line-inner' ) ); ?>></div>
		</div>
	</div>
</div>

