<?php
/**
 * Terms & Conditions pupup template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Terms & Condtions Popup
 * @version 1.0.0
 */
?>

<div class="woocommerce" data-type="<?php echo esc_attr( $type ); ?>">
	<?php if ( $show_title ) : ?>
		<h2 class="popup-title"><?php echo esc_html( $contents['title'] ); ?></h2>
	<?php endif; ?>
		<div class="popup-content" <?php echo $content_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> >
			<div class="terms-content">
				<?php echo wpautop( do_shortcode( $contents['content'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	<?php if ( $popup_button ) : ?>
		<div class="popup-footer">
			<a id="agree_<?php echo esc_attr( $type ); ?>_button" href="#" class="agree-button <?php echo ( 'button' === $button_style ) ? 'btn button' : ''; ?>"><?php echo esc_html( $popup_button_text ); ?></a>
		</div>
		<?php do_action( 'yith_wctc_end_terms_and_conditions_popup' ); ?>
	<?php endif; ?>
</div>
