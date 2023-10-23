<?php
/**
 * Select Badge Image Field
 *
 * @var array $args Field Args.
 *
 * @package YITH\BadgeManagement\Views
 */

if ( empty( $args['value'] ) ) {
	$args['value'] = current( $args['local_badges'] );
}

?>

<div class="yith-wcbm-badge-library-wrapper">
	<?php if ( ! empty( $args['custom_label'] ) ) : ?>
		<div class="yith-wcbm-badge-library-title">
			<?php echo $args['custom_label']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>

	<div class="yith-wcbm-badge-list-container yith-wcbm-badge-library__badges" id="<?php echo esc_attr( $args['id'] ); ?>" data-assets-url="<?php echo esc_url_raw( $args['url'] ); ?>">
		<?php if ( ! empty( $args['allow_upload'] ) && 'yes' === $args['allow_upload'] ) : ?>
			<a class="yith-wcbm-badge-list-element yith-wcbm-badge-list-element--premium yith-wcbm-upload-image" href="<?php echo esc_url( yith_wcbm_get_panel_url( 'premium' ) ); ?>" target="_blank">
				<span class="yith-wcbm-upload-image-field-content">
						<span class="yith-wcbm-upload-image-field">
							<span class="yith-icon yith-icon-upload"></span>
							<?php esc_html_e( 'Upload image', 'yith-woocommerce-badges-management' ); ?>
						</span>
				</span>
				<span class="yith-wcbm-badge-list-element-premium">
					<?php echo esc_html_x( 'Premium', '[ADMIN] label shown in premium contents', 'yith-woocommerce-badges-management' ); ?>
				</span>
			</a>
		<?php endif; ?>

		<?php foreach ( $args['library'] as $badge => $badge_url ) : ?>
			<label class="yith-wcbm-badge-list-element yith-wcbm-badge-library__badge <?php echo $badge === $args['value'] ? 'yith-wcbm-badge-list-element--selected' : ''; ?>" style="background-image: url('<?php echo esc_url_raw( $badge_url ); ?>')">
				<input type="radio" class="yith-wcbm-badge-list-element--input" value="<?php echo esc_html( $badge ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" <?php checked( $badge, $args['value'] ); ?>>
			</label>
		<?php endforeach; ?>

	</div>
</div>
