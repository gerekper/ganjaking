<?php
/**
 * Wishlist ask an estimate form
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist             \YITH_WCWL_Wishlist wishlist
 * @var $ask_an_estimate_text string Ask an estimate label
 * @var $ask_estimate_url     string Ask an estimate destination url
 * @var $additional_info      bool Whether to show Additional info textarea in Ask an estimate form
 * @var $ask_an_estimate_icon string Icon to use for Ask an Estimate button icon
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="ask_an_estimate_popup">
	<form action="<?php echo esc_url( $ask_estimate_url ); ?>" method="post" class="wishlist-ask-an-estimate-popup">
		<div class="yith-wcwl-popup-content">
			<?php
			/**
			 * APPLY_FILTERS: yith_wcwl_show_popup_heading_icon_instead_of_title
			 *
			 * Filter whether to show the icon in the 'Ask for an estimate' popup.
			 *
			 * @param bool   $show_icon    Whether to show icon or not
			 * @param string $heading_icon Heading icon
			 *
			 * @return bool
			 */
			if ( apply_filters( 'yith_wcwl_show_popup_heading_icon_instead_of_title', true, 'fa-envelope-open-o' ) ) :
				/**
				 * APPLY_FILTERS: yith_wcwl_ask_and_estimate_popup_heading_icon_class
				 *
				 * Filter the heading icon in the 'Ask for an estimate' popup.
				 *
				 * @param string $heading_icon Heading icon
				 *
				 * @return string
				 */
				?>
				<i class="fa <?php echo esc_attr( apply_filters( 'yith_wcwl_ask_and_estimate_popup_heading_icon_class', 'fa-envelope-open-o' ) ); ?> heading-icon"></i>
			<?php else : ?>
				<h3><?php esc_html_e( 'Ask for an estimate for this list', 'yith-woocommerce-wishlist' ); ?></h3>
			<?php endif; ?>

			<p class="popup-description">
				<?php
				/**
				 * APPLY_FILTERS: yith_wcwl_ask_for_an_estimate_popup_text
				 *
				 * Filter the heading text in the 'Ask for an estimate' popup.
				 *
				 * @param string $heading_text Heading text
				 *
				 * @return string
				 */
				echo esc_html( apply_filters( 'yith_wcwl_ask_for_an_estimate_popup_text', __( 'Ask for an estimate for this list:', 'yith-woocommerce-wishlist' ) ) );
				?>
			</p>

			<?php if ( ! is_user_logged_in() ) : ?>
				<p class="form-row form-row-wide">
					<label for="reply_email">
						<?php
						/**
						 * APPLY_FILTERS: yith_wcwl_ask_estimate_reply_mail_label
						 *
						 * Filter the label for the email field in the 'Ask for an estimate' popup.
						 *
						 * @param string $label Label
						 *
						 * @return string
						 */
						echo esc_html( apply_filters( 'yith_wcwl_ask_estimate_reply_mail_label', __( 'Your email', 'yith-woocommerce-wishlist' ) ) );
						?>
					</label>
					<input type="email" value="" name="reply_email" id="reply_email">
				</p>
			<?php endif; ?>

			<?php
			if ( ! empty( $ask_an_estimate_fields ) ) {
				foreach ( $ask_an_estimate_fields as $field_id => $field ) {
					$field['label']       = apply_filters( 'wpml_translate_single_string', $field['label'], 'ask-an-estimate-form', "field_{$field_id}_label" );
					$field['placeholder'] = apply_filters( 'wpml_translate_single_string', $field['placeholder'], 'ask-an-estimate-form', "field_{$field_id}_placeholder" );
					$field['description'] = apply_filters( 'wpml_translate_single_string', $field['description'], 'ask-an-estimate-form', "field_{$field_id}_description" );

					woocommerce_form_field( $field_id, $field );
				}
			}
			?>

			<?php if ( $additional_info ) : ?>
				<p class="form-row form-row-wide">
					<?php if ( ! empty( $additional_info_label ) ) : ?>
						<label for="additional_notes"><?php echo esc_html( $additional_info_label ); ?></label>
					<?php endif; ?>
					<textarea id="additional_notes" name="additional_notes"></textarea>
				</p>
			<?php endif; ?>
		</div>

		<div class="yith-wcwl-popup-footer">
			<input type="hidden" name="ask_an_estimate" value="<?php echo esc_attr( $wishlist->get_token() ); ?>"/>
			<button class="btn button ask-an-estimate-button ask-an-estimate-button-popup alt" id="ask_an_estimate">
				<?php
				/**
				 * APPLY_FILTERS: yith_wcwl_ask_an_estimate_icon
				 *
				 * Filter the icon for the 'Ask for an estimate'.
				 *
				 * @param string $icon Icon
				 *
				 * @return string
				 */
				echo yith_wcwl_kses_icon( apply_filters( 'yith_wcwl_ask_an_estimate_icon', $ask_an_estimate_icon ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<?php
				/**
				 * APPLY_FILTERS: yith_wcwl_ask_an_estimate_text
				 *
				 * Filter the text for the 'Ask for an estimate'.
				 *
				 * @param string $text Text
				 *
				 * @return string
				 */
				echo esc_html( apply_filters( 'yith_wcwl_ask_an_estimate_text', $ask_an_estimate_text ) );
				?>
			</button>
		</div>
	</form>
</div>
