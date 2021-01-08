<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <h2><?php _e('Manual Member Approval', 'mpmma'); ?></h2>
  <br/>
  <div class="mpmma-tabs-wrapper">
    <form method="post" action="">
      <input type="checkbox" name="mpmma_logged_in_users" id="mpmma_logged_in_users" <?php checked($logged_in); ?> />
      <label for="mpmma_logged_in_users"><?php _e('Require approval even if member is already logged in?', 'mpmma'); ?></label>
      <br/>
      <small><?php _e('Even if an existing member is logged in, and they subscribe to one of the selected memberships below, hold them for approval.', 'mpmma'); ?></small>
      <br/><br/>
      <input type="checkbox" name="mpmma_allow_logins" id="mpmma_allow_logins" <?php checked($allow_logins); ?> />
      <label for="mpmma_allow_logins"><?php _e('Allow members to login while pending approval?', 'mpmma'); ?></label>
      <br/>
      <small><?php _e('This will not give them access to the content as long as your Rules are set correctly in MemberPress. But it will allow them to login so they can access their account and other items on the site that do not require approval.', 'mpmma'); ?></small>
      <br/><br/>
      <input type="checkbox" name="mpmma_allow_logins_rejected" id="mpmma_allow_logins_rejected" <?php checked($allow_logins_rejected); ?> />
      <label for="mpmma_allow_logins_rejected"><?php _e('Allow members to login while rejected?', 'mpmma'); ?></label>
      <br/>
      <small><?php _e('This will not give them access to the content as long as your Rules are set correctly in MemberPress. But it will allow them to login so they can access their account and other items on the site that do not require approval.', 'mpmma'); ?></small>
      <br/><br/>
      <input type="checkbox" name="mpmma_use_template" id="mpmma_use_template" <?php checked($use_template); ?> />
      <label for="mpmma_use_template"><?php _e('Wrap emails below in MemberPress default gray template?', 'mpmma'); ?></label>
      <br/><br/>
      <label for="mpmma_memberships"><?php _e('Select Memberships Requiring Manual Approval', 'mpmma'); ?></label>
      <br/>
      <small><?php _e('NOTE: Because of limitations in MemberPress it is not possible to prevent payment from processing until the member is approved. Therefoe if your membership is paid, you may need to refund the member if you deny them.', 'mpmma'); ?></small>
      <br/>
      <small><?php _e('CTRL + CLICK to select multiple Memberships. (COMMAND + CLICK on Mac)', 'mpmma'); ?></small>
      <br/>
      <select name="mpmma_memberships[]" id="mpmma_memberships" style="width:98%;min-height:200px;max-height:350px;" multiple>
        <?php foreach($all_memberships as $m): ?>
          <option value="<?php echo $m->ID; ?>" <?php selected(true, in_array($m->ID, $memberships)); ?>><?php echo $m->post_title; ?></option>
        <?php endforeach; ?>
      </select>
      <br/><br/>
      <h3><?php _e('Email Sent to Member When Held for Approval:', 'mpmma'); ?></h3>
      <input type="checkbox" name="mpmma_held_disabled" id="mpmma_held_disabled" <?php checked($held_disabled); ?> />
      <label for="mpmma_held_disabled"><?php _e('Disable This Email', 'mpmma'); ?></label>
      <br/><br/>
      <span class="held_hidden_box">
        <label for="mpmma_held_subject"><?php _e('Held For Approval Email Subject', 'mpmma'); ?></label>
        <br/>
        <input type="text" name="mpmma_held_subject" id="mpmma_held_subject" value="<?php echo $held_subject; ?>" style="width:50%;min-width:150px;" />
        <br/><br/>
        <?php
          $editor_settings = array(
            'textarea_name' => 'mpmma_held_body',
            'teeny'         => true,
            'editor_height' => 200
          );
          wp_editor($held_body, 'heldemail', $editor_settings);
        ?>
        <br/></br>
      </span>
      <h3><?php _e('Email Sent to MemberPress Admin Emails When Held for Approval:', 'mpmma'); ?></h3>
      <label for="mpmma_admin_held_subject"><?php _e('Admin - Held For Approval Email Subject', 'mpmma'); ?></label>
      <br/>
      <input type="text" name="mpmma_admin_held_subject" id="mpmma_admin_held_subject" value="<?php echo $admin_held_subject; ?>" style="width:50%;min-width:150px;" />
      <br/><br/>
      <?php
        $editor_settings = array(
          'textarea_name' => 'mpmma_admin_held_body',
          'teeny'         => true,
          'editor_height' => 200
        );
        wp_editor($admin_held_body, 'adminheldemail', $editor_settings);
      ?>
      <br/></br>
      <h3><?php _e('Email Sent to Member When Approved:', 'mpmma'); ?></h3>
      <label for="mpmma_approved_subject"><?php _e('Approved Email Subject', 'mpmma'); ?></label>
      <br/>
      <input type="text" name="mpmma_approved_subject" id="mpmma_approved_subject" value="<?php echo $approved_subject; ?>" style="width:50%;min-width:150px;" />
      <br/><br/>
      <?php
        $editor_settings = array(
          'textarea_name' => 'mpmma_approved_body',
          'teeny'         => true,
          'editor_height' => 200
        );
        wp_editor($approved_body, 'approvedemail', $editor_settings);
      ?>
      <br/></br>
      <h3><?php _e('Email Sent to Member When Rejected:', 'mpmma'); ?></h3>
      <label for="mpmma_rejected_subject"><?php _e('Rejected Email Subject', 'mpmma'); ?></label>
      <br/>
      <input type="text" name="mpmma_rejected_subject" id="mpmma_rejected_subject" value="<?php echo $rejected_subject; ?>" style="width:50%;min-width:150px;" />
      <br/><br/>
      <?php
        $editor_settings = array(
          'textarea_name' => 'mpmma_rejected_body',
          'teeny'         => true,
          'editor_height' => 200
        );
        wp_editor($rejected_body, 'rejectedemail', $editor_settings);
      ?>
      <br/></br>
      <input type="submit" name="mpmma_admin_page_save" class="button button-primary" value="<?php _e('Save', 'mpmma'); ?>" />
    </form>
  </div>
</div>
