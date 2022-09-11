<?php
/**
 * The template for displaying the waitlist elements on a single event page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/waitlist-event.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 1.9.0
 */
?>
<div class="wcwl_elements wcwl_nojs">
	<?php
	$user_email = $user ? $user->user_email : '';
	if ( 'yes' == get_option( 'woocommerce_waitlist_registration_needed' ) && ! $user_email ) { ?>
		<div class="wcwl_notice woocommerce-info">
			<?php echo esc_html( $registration_required_text ); ?>
		</div>
	<?php } else { ?>
		<div class="wcwl_intro">
			<p><?php echo esc_html( $intro ); ?></p>
		</div>
		<?php if ( $notice ) { ?>
			<div class="wcwl_notice woocommerce-message">
				<div aria-live="polite">
					<p><?php echo esc_html( $notice ); ?></p>
				</div>
				<button type="button" class="wcwl_notice_dismiss">
					<span class="screen-reader-text"><?php echo esc_html( $dismiss_notification_text ); ?></span>
				</button>
			</div>
		<?php } ?>
		<?php if ( $opt_in && ! $on_waitlist ) { ?>
			<div class="wcwl_optin">
				<input type="checkbox" name="wcwl_optin" id="wcwl_optin">
				<label for="wcwl_optin"><?php echo esc_html( $opt_in_text ); ?></label>
			</div>
		<?php } ?>
		<div class="wcwl_email_elements <?php echo esc_attr( $email_class ); ?>">
			<label for="wcwl_email_<?php echo esc_attr( $product_id ); ?>" class="wcwl_email_label wcwl_visually_hidden"><?php echo esc_html( $email_address_label_text ); ?></label>
			<input type="email" value="<?php echo esc_attr( $user_email ); ?>" id="wcwl_email_<?php echo esc_attr( $product_id ); ?>" name="wcwl_email" class="wcwl_email" placeholder="<?php echo esc_attr( $email_address_placeholder_text ); ?>" <?php if ( $user_email ) { echo 'disabled'; } ?>/>
		</div>
		<a class="wcwl_control" rel="nofollow" href="<?php echo esc_url( $url ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wcwl-ajax-process-user-request-nonce' ) ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-context="<?php echo esc_attr( $context ); ?>" data-wpml-lang="<?php echo esc_attr( $lang ); ?>">
			<button type="button" class="woocommerce_waitlist button"><?php echo esc_html( wcwl_get_button_text( $context ) ); ?></button>
			<div aria-live="polite" class="wcwl_visually_hidden"></div>
			<div class="spinner"></div>
		</a>
	<?php } ?>
</div><!-- wcwl_elements -->
