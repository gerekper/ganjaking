<?php
/**
 * Empty Table
 *
 * @var string $icon    Icon.
 * @var string $message Message.
 * @var string $cta     Call To Action text.
 *
 * @package YITH\MultiCurrencySwitcher
 */

?>
<div class="yith-wcbm-empty">
	<div class="yith-wcbm-empty__icon">
		<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<div class="yith-wcbm-empty__message">
		<?php echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<?php if ( ! ! $cta ) : ?>
		<a class="yith-wcbm-empty__cta" href="<?php echo esc_url_raw( $cta_url ?? '' ); ?>">
			<?php echo esc_html( $cta ); ?>
		</a>
	<?php endif; ?>
</div>
