<?php
/**
 * Terms & Conditions custom template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Terms & Condtions Popup
 * @version 1.0.0
 */

if ( $show_line ) : ?>
	<div class="terms-privacy-conditions">

		<?php if ( 'both' === $terms_type && 'together' === $terms_fields ) : ?>
			<p class="form-row terms">
				<label for="privacy" class="checkbox">
					<input <?php echo ( $hide_checkbox ) ? 'style="display:none;"' : ''; ?> type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_privacy_is_checked_default', isset( $_POST['terms'] ) || $checked ), true ); ?> id="terms" />
					<?php echo $line; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</label>
			</p>
		<?php endif; ?>

		<?php if ( ( 'both' === $terms_type && 'apart' === $terms_fields ) || 'terms' === $terms_type ) : ?>
			<p class="form-row terms">
				<label for="terms" class="checkbox">
					<input <?php echo ( $hide_checkbox ) ? 'style="display:none;"' : ''; ?> type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) || $terms_checked || $hide_checkbox ), true ); ?> id="terms" />
					<?php echo $line_terms; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</label>
			</p>
		<?php endif; ?>

		<?php if ( ( 'both' === $terms_type && 'apart' === $terms_fields ) || 'privacy' === $terms_type ) : ?>
			<p class="form-row terms">
				<label for="privacy" class="checkbox">
					<input <?php echo ( $hide_checkbox ) ? 'style="display:none;"' : ''; ?> type="checkbox" class="input-checkbox" name="<?php echo ( $terms_type == 'privacy' ) ? 'terms' : 'privacy'; ?>" <?php checked( apply_filters( 'woocommerce_privacy_is_checked_default', isset( $_POST['privacy'] ) || $privacy_checked || $hide_checkbox ), true ); ?> id="privacy_checkbox" />
					<?php echo $line_privacy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</label>
			</p>
		<?php endif; ?>

	</div>
<?php endif; ?>
