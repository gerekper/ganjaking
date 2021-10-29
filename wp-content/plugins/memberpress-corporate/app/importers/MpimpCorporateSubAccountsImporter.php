<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/*
 * Note: Class name and format are required by `MpimpImporterFactory` for proper parsing.
 */
class MpimpCorporateSubAccountsImporter extends MpimpUsersImporter {
  public function __construct() {
    //
  }

  public function form() {
    $parent = isset($_REQUEST['parent']) ? $_REQUEST['parent'] : '';
    $ca = isset($_REQUEST['ca']) ? $_REQUEST['ca'] : '';
    ?>
      <br>
      <label for="parent_autocomplete"><?php _e('Parent User', 'memberpress-corporate'); ?>
        <input type="text"
          name="parent_autocomplete"
          id="parent_autocomplete"
          class="mepr_suggest_user"
          value="<?php echo $parent; ?>"
          autocomplete="on" />
      </label>
      <br>
      <br>

      <label for="ca_select"><?php _e('Corporate Account', 'memberpress-corporate'); ?>
        <select id="ca_select" data-ca="<?php echo $ca; ?>" name="args[corporate_account_id]">
        </select>
      </label>

      <br/>
      <input type="checkbox" name="args[notice]" <?php checked(true); ?> />
      <?php _e('Send NEW members a password reset link (does not email existing members)', 'memberpress-corporate'); ?>

      <br/><br/>
      <input type="checkbox" name="args[welcome]" <?php checked(false); ?> />
      <?php _e('Send NEW members the welcome email if they haven\'t received it already for the membership.', 'memberpress-corporate'); ?>
    <?php
  }

  public function import($row, $args) {
    if( !isset($args['corporate_account_id']) || empty($args['corporate_account_id']) ) {
      throw new MpimpStopImportException( __('A corporate parent account is required for import.', 'memberpress-corporate') );
    }

    $ca = new MPCA_Corporate_Account($args['corporate_account_id']);

    // This is in import_users but we kind of need it here too
    $required = array('username', 'email');
    $this->check_required('users', array_keys($row), $required);

    $ignored = apply_filters('mpca-import-ignored-cols', array('password', 'role'), $row, $args);
    if(!(is_admin() && MeprUtils::is_mepr_admin())) {
      foreach ($ignored as $ignore) {
        unset($row[$ignore]);
      }
    }
    // {email|username}_exists method returns false or ID
    $user_id = email_exists($row['email']);
    if($user_id === false) {
      $user_id = username_exists($row['username']);
    }

    if(isset($row['action']) && $row['action'] == 'remove') {
      if($user_id) {
        $ca->remove_sub_account_user($user_id);
        $user = new MeprUser($user_id);
        return sprintf(
          __('Corporate Account Sub User (username=%1$s, ID=%2$s) was removed successfully', 'memberpress-corporate'),
          $user->user_login,
          $user->ID
        );
      }
      else {
        return sprintf(
          __('Corporate Account Sub User (email=%1$s) was not found', 'memberpress-corporate'),
          $row['email']
        );
      }
    }
    else {
      $validated = $ca->validate();

      if( is_wp_error($validated) )  {
        throw new MpimpStopImportException( __($validated->get_error_message(), 'memberpress-corporate') );
      }

      // This should be caught in MpimpUserImporter, but we'll just stop here
      if($user_id && user_can($user_id, 'manage_options') === true) {
        throw new Exception( __('Admin users cannot be imported as sub accounts', 'memberpress-corporate') );
      }

      if($user_id) {
        $u = new MeprUser($user_id);
        if($row['email'] !== $u->user_email) {
          if(current_user_can( 'manage_options' )){
            throw new Exception( sprintf(__('User (email=%s, username=%s) already exists with this username. Please choose a different USERNAME.', 'memberpress-corporate'), $u->user_email, $u->user_login) );
          }else{
            throw new Exception( sprintf(__('User (username=%s) already exists. Please choose a different USERNAME.', 'memberpress-corporate'), $u->user_login) );
          }
        }
        else if($row['username'] !== $u->user_login) {
          if(current_user_can( 'manage_options' )){
            throw new Exception( sprintf(__('User (email=%s, username=%s) already exists with this email. Please choose a different EMAIL.', 'memberpress-corporate'), $u->user_email, $u->user_login) );
          }
          else{
            throw new Exception( sprintf(__('User (email=%s) already exists. Please choose a different EMAIL.', 'memberpress-corporate'), $u->user_email) );
          }
        }
      }

      // Call out to MpimpUserImporter to create the user, generate the password and send password reset email
      $res = $this->import_user($row,$args,array('action'));

      // Associate sub account with corporate account
      $result = $ca->add_sub_account_user($res['user_id']);

      // Display any errors from assoc sub account
      if( is_wp_error($result) ) {
        throw new MpimpStopImportException( __($result->get_error_message(), 'memberpress-corporate') );
      }

      $transaction = $ca->get_user_sub_account_transaction($res['user_id']);

      // Send welcome email
      if(is_array($args) && isset($args['welcome'])) {
        $mailer = MeprEmailFactory::fetch('Mepr_Sub_Account_Welcome_Email');
        $mailer->send_sub_account_welcome_email($transaction);
      }

      if($res['exists']) {
        return sprintf(
          __('Corporate Account User (username=%1$s, ID=%2$s, transaction_id=%3$s, corporate_account_id=%4$s, parent_transaction_id=%5$s) already existed and was updated successfully', 'memberpress-corporate'),
          $res['user']['user_login'],
          $res['user_id'],
          $transaction->id,
          $transaction->corporate_account_id,
          $transaction->parent_transaction_id
        );
      }
      else {
        return sprintf(
          __('Corporate Account User (username=%1$s, ID=%2$s, transaction_id=%3$s, corporate_account_id=%4$s, parent_transaction_id=%5$s) was created successfully', 'memberpress-corporate'),
          $res['user']['user_login'],
          $res['user_id'],
          $transaction->id,
          $transaction->corporate_account_id,
          $transaction->parent_transaction_id
        );
      }
    }
  }
}
