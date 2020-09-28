<?php

/**
 * Sends email verification notices.  Also validates the confirmation from the email.
 * @since 2.9.0
 */
class WC_Wishlists_Email_Verification {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Wishlists_Email_Verification();
		}
	}

	private function __construct() {
		add_action( 'woocommerce_init', array( $this, 'on_init' ), 8 );
		add_action( 'wc_wishlists_created', array( $this, 'on_wishlist_created' ), 10, 2 );
		add_action( 'wc_wishlists_updated', array( $this, 'on_wishlist_updated' ), 10, 2 );
	}

	public function on_init() {
		global $wpdb;
		if ( isset( $_GET['wcwlemailconfirmationcode'] ) ) {

			$hash = $_GET['wcwlemailconfirmationcode'];
			$sql  = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wishlist_email_validation_key' AND meta_value = %s", $hash );
			$wlid = $wpdb->get_var( $sql );

			if ( $wlid ) {
				$email = get_post_meta( $wlid, '_wishlist_email', true );
				update_post_meta( $wlid, '_wishlist_email_validated', $email );
				delete_post_meta( $wlid, '_wishlist_email_validation_key', $hash );

				wc_add_notice( __( 'Your email has been confirmed', 'wc_wishlist' ) );
				wp_redirect( get_permalink( wc_get_page_id( 'shop' ) ) );
				die();
			}
		}
	}

	public function on_wishlist_created( $wishlist_id, $args ) {
		$current_user = wp_get_current_user();

		/** Generate email validation key.  Since 2.9.0 * */
		if ( is_user_logged_in() && ! empty( $args['wishlist_owner_email'] ) && $current_user->user_email == $args['wishlist_owner_email'] ) {
			update_post_meta( $wishlist_id, '_wishlist_email_validated', $args['wishlist_owner_email'] );
		} else {
			$random_hash = wp_generate_password( 16, false ) . wp_generate_password( 16, false );
			update_post_meta( $wishlist_id, '_wishlist_email_validated', false );
			update_post_meta( $wishlist_id, '_wishlist_email_validation_key', $random_hash );


			if ( ! empty( $args['wishlist_owner_email'] ) ) {


				$email = WC_Emails::instance();
				$email->emails['WC_Wishlists_Mail_Email_Verification']->trigger( $args['wishlist_owner_email'], $wishlist_id, $random_hash );

				/*
				$link    = add_query_arg( array( 'wcwlemailconfirmationcode' => $random_hash ), get_site_url() );
				$message = 'We received your request to add your email to your wishlist.  Before we begin using this email address, we want to be certain we have your permission. Confirm by visiting this link in your browser <a href="' . $link . '">' . $link . '</a>.  If you did not make this request you can safely ignore this email.';

				wp_mail( $args['wishlist_owner_email'], __( 'Response Required:  Confirm your email address for your wishlist', 'wc_wishlist' ), $message );
			*/
			}
		}
	}

	public function on_wishlist_updated( $wishlist_id, $args ) {

		$current_user = wp_get_current_user();

		$validated_email = get_post_meta( $wishlist_id, '_wishlist_email_validated', true );
		/** Generate email validation key.  Since 2.9.0 * */
		if ( ! empty( $args['wishlist_owner_email'] ) && (
				( $validated_email == $args['wishlist_owner_email'] ) ||
				( is_user_logged_in() && $current_user->user_email == $args['wishlist_owner_email'] ) )
		) {
			update_post_meta( $wishlist_id, '_wishlist_email_validated', $args['wishlist_owner_email'] );
		} else {

			$is_valid = $args['wishlist_owner_email'] === $validated_email;
			if ( ! $is_valid ) {
				$random_hash = wp_generate_password( 16, false ) . wp_generate_password( 16, false );
				update_post_meta( $wishlist_id, '_wishlist_email_validated', false );
				update_post_meta( $wishlist_id, '_wishlist_email_validation_key', $random_hash );

				if ( ! empty( $args['wishlist_owner_email'] ) ) {

					$email = WC_Emails::instance();
					$email->emails['WC_Wishlists_Mail_Email_Verification']->trigger( $args['wishlist_owner_email'], $wishlist_id, $random_hash );

					/*
										$link    = add_query_arg( array( 'wcwlemailconfirmationcode' => $random_hash ), get_site_url() );
										$message = 'We received your request to add your email to your wishlist.  Before we begin using this email address, we want to be certain we have your permission. Confirm by visiting this link in your browser ' . $link . ' .  If you did not make this request you can safely ignore this email.';

										wp_mail( $args['wishlist_owner_email'], __( 'Response Required:  Confirm your email address for your wishlist', 'wc_wishlist' ), $message );
					*/
				}
			}
		}
	}
}