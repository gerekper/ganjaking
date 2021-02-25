<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <h2><?php _e('Display Name - Options', 'mpdn'); ?></h2>
  <br/>
  <form action="" method="post">
    <label for="mpdn-set-to"><?php _e('Set Display Name To', 'mpdn'); ?></label>
    <br/>
    <select name="mpdn-set-to" id="mpdn-set-to">
      <option value="first_name" <?php selected($selected, 'first_name'); ?>><?php _e('First Name', 'mpdn'); ?></option>
      <option value="last_name" <?php selected($selected, 'last_name'); ?>><?php _e('Last Name', 'mpdn'); ?></option>
      <option value="full_name" <?php selected($selected, 'full_name'); ?>><?php _e('Full Name', 'mpdn'); ?></option>
      <option value="full_name_last_first" <?php selected($selected, 'full_name_last_first'); ?>><?php _e('Full Name, Last Name First', 'mpdn'); ?></option>
      <option value="first_name_last_initial" <?php selected($selected, 'first_name_last_initial'); ?>><?php _e('First Name, Last Initial', 'mpdn'); ?></option>
      <option value="last_name_first_initial" <?php selected($selected, 'last_name_first_initial'); ?>><?php _e('Last Name, First Initial', 'mpdn'); ?></option>
      <option value="user_login" <?php selected($selected, 'user_login'); ?>><?php _e('Username', 'mpdn'); ?></option>
      <option value="user_email" <?php selected($selected, 'user_email'); ?>><?php _e('Email Address', 'mpdn'); ?></option>
    </select>
    <br/><br/>
    <input type="checkbox" name="mpdn-force-update" id="mpdn-force-update" <?php checked($checked); ?> />
    <label for="mpdn-force-update" style="vertical-align:top;"><?php _e('Force update of Display Name on User Login', 'mpdn'); ?></label>
    <br/><br/>
    <input type="checkbox" name="mpdn-force-profile-save" id="mpdn-force-profile-save" <?php checked($checked2); ?> />
    <label for="mpdn-force-profile-save" style="vertical-align:top;"><?php _e('Force update of Display Name when saving WordPress User Profiles', 'mpdn'); ?></label>
    <br/><br/>
    <input type="submit" name="mpdn-admin-page-submit" class="button button-primary" value="<?php _e('Save Options', 'mpdn'); ?>" />
  </form>
</div>
