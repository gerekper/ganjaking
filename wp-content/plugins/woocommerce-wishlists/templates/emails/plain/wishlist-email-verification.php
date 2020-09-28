<?php $link = add_query_arg( array( 'wcwlemailconfirmationcode' => $email_confirmation_hash ), get_site_url() ); ?>
<?php printf( __( 'We received your request to add your email to your wishlist. Before we begin using this email address, we want to be certain we have your permission. Confirm by visiting this link in your browser %s', 'wc_wishlist' ), $anchor ); ?>
<?php _e( 'If you did not make this request you can safely ignore this email.', 'wc_wishlist' ); ?>
