<?php
/**
 * Privacy Policy Settings
 *
 * @since 1.0.19
 */

// Add privacy policy field.
add_action( 'register_form', 'loginpress_add_privacy_policy_field' );
function loginpress_add_privacy_policy_field() {

  $loginpress_setting = get_option( 'loginpress_setting' );
  $privacy_policy = isset( $loginpress_setting['privacy_policy'] ) ? $loginpress_setting['privacy_policy'] : __( sprintf( __( '%1$sPrivacy Policy%2$s.', 'loginpress' ), '<a href="' . admin_url( 'admin.php?page=loginpress-settings' ) . '">', '</a>' ) );
  ?>
  <p>
    <label for="lp_privacy_policy"><br />
      <input type="checkbox" name="lp_privacy_policy" id="lp_privacy_policy" class="checkbox" />
      <?php echo $privacy_policy;?>
    </label>
  </p>
  <?php
}

// Add validation. In this case, we make sure lp_privacy_policy is required.
add_filter( 'registration_errors', 'loginpresss_privacy_policy_auth', 10, 3 );

function loginpresss_privacy_policy_auth( $errors, $sanitized_user_login, $user_email ) {

  if ( ! isset( $_POST['lp_privacy_policy'] ) ) :

    $errors->add( 'policy_error', "<strong>ERROR</strong>: Please accept the privacy policy." );
    return $errors;
  endif;
  return $errors;
}


// Lastly, save our extra registration user meta.
// add_action( 'user_register', 'loginpress_privacy_policy_save' );

function loginpress_privacy_policy_save( $user_id ) {

  if ( isset( $_POST['lp_privacy_policy'] ) )
     update_user_meta( $user_id, 'lp_privacy_policy', $_POST['lp_privacy_policy'] );
}
