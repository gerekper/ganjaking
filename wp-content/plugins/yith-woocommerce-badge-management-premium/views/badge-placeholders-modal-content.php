<?php
/**
 * Badge Placeholders Modal Content
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Views
 */

$placholders = yith_wcbm_get_badges_placeholders();

?>

<div class="yith-wcbm-placeholders-list">
	<?php foreach ( $placholders as $placholder => $options ) : ?>
		<div class="yith-wcbm-placeholder-wrapper">
			<div class="yith-wcbm-placeholder-content">
				<span class="yith-wcbm-placeholder-description">
					<?php echo esc_html( $options['desc'] ?? '' ); ?>
				</span>
				<span class="yith-wcbm-placeholder-input">
					<?php
					yith_plugin_fw_get_field(
						array(
							'id'    => 'yith-wcbep-placeholder-' . $placholder,
							'type'  => 'copy-to-clipboard',
							'value' => '{{' . $placholder . '}}',
						),
						true,
						false
					);
					?>
				</span>
			</div>
			<?php if ( ! empty( $options['note'] ) ) : ?>
				<span class="yith-wcbm-placeholder-note">
					<?php echo esc_html( $options['note'] ); ?>
				</span>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
