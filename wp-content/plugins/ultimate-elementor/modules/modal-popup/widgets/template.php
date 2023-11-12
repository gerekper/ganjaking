<?php
/**
 * UAEL Modal Popup widget Template.
 *
 * @package UAEL
 */

$class = ( $is_editor && 'yes' === $settings['preview_modal'] ) ? 'uael-show-preview' : '';

$this->add_render_attribute( 'inner-wrapper', 'id', 'modal-' . $node_id );

$this->add_render_attribute(
	'inner-wrapper',
	'class',
	array(
		'uael-modal',
		'uael-center-modal',
		'uael-modal-custom',
		'uael-modal-' . $settings['content_type'],
		$settings['modal_effect'],
		$class,
		( $is_editor ) ? 'uael-modal-editor' : '',
		'uael-aspect-ratio-' . $settings['video_ratio'],
	)
);

?>
<div <?php echo wp_kses_post( $this->get_parent_wrapper_attributes( $settings ) ); ?>>
	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'inner-wrapper' ) ); ?>>
		<div class="uael-content">
			<?php
			if (
				(
					( 'icon' === $settings['close_source'] && ( ! empty( $settings['close_icon'] ) || ! empty( $settings['new_close_icon'] ) ) ) ||
					( 'img' === $settings['close_source'] && '' !== $settings['close_photo']['url'] )
				) &&
				(
					'top-left' !== $settings['icon_position'] &&
					'top-right' !== $settings['icon_position']
				)
			) {
				$this->render_close_icon();
			}
			if ( '' !== $settings['title'] ) {
				?>
			<div class="uael-modal-title-wrap">
				<<?php echo esc_attr( $title_tag ); ?> class="uael-modal-title elementor-inline-editing" data-elementor-setting-key="title" data-elementor-inline-editing-toolbar="basic"><?php echo wp_kses_post( $this->get_settings_for_display( 'title' ) ); ?></<?php echo esc_attr( $title_tag ); ?>>
			</div>
			<?php } ?>
			<div class="uael-modal-text uael-modal-content-data clearfix">
			<?php echo do_shortcode( $this->get_modal_content( $settings, $node_id ) ); ?>
			</div>
		</div>
	</div>

	<?php
	if (
		(
			( 'icon' === $settings['close_source'] && ( ! empty( $settings['close_icon'] ) || ! empty( $settings['new_close_icon'] ) ) ) ||
			( 'img' === $settings['close_source'] && '' !== $settings['close_photo'] )
		) &&
			(
				'top-left' === $settings['icon_position'] ||
				'top-right' === $settings['icon_position']
			)
		) {
			$this->render_close_icon();
	}
	?>
	<div class="uael-overlay"></div>
</div>

<div class="uael-modal-action-wrap">
	<?php
		$action_html = $this->render_action_html();
		echo wp_kses_post( sanitize_text_field( $action_html ) );
	?>
</div>
