<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="esaf-page-title"><?php _e('Email Notifications', 'affiliate-royale', 'easy-affiliate'); ?></div>
<?php
$emails = (object)array(
  'welcome' => (object)array(
    'tooltip_title' => __('Send a Welcome Email to Affiliates', 'easy-affiliate', 'affiliate-royale'),
    'tooltip_body' => __('When this is checked, Easy Affiliate will send a welcome email to each affiliate when they\'re accepted into your affiliate program.', 'easy-affiliate', 'affiliate-royale'),
    'id' => $wafp_options->welcome_email_str,
    'send_label' => __('Send Welcome Email', 'easy-affiliate', 'affiliate-royale'),
    'send' => $wafp_options->welcome_email,
    'subject_id' => $wafp_options->welcome_email_subject_str,
    'subject' => $wafp_options->welcome_email_subject,
    'body_id' => $wafp_options->welcome_email_body_str,
    'body' => $wafp_options->welcome_email_body,
  ),
  'sale' => (object)array(
    'tooltip_title' => __('Send a Sale Notification Email to Affiliates', 'easy-affiliate', 'affiliate-royale'),
    'tooltip_body' => __('When this is checked, Easy Affiliate will send a sale notification email to each affiliate when they\'ve referred a sale and are entitled to a commission.', 'easy-affiliate', 'affiliate-royale'),
    'id' => $wafp_options->affiliate_email_str,
    'send_label' => __('Send Affiliate Sale Email', 'easy-affiliate', 'affiliate-royale'),
    'send' => $wafp_options->affiliate_email,
    'subject_id' => $wafp_options->affiliate_email_subject_str,
    'subject' => $wafp_options->affiliate_email_subject,
    'body_id' => $wafp_options->affiliate_email_body_str,
    'body' => $wafp_options->affiliate_email_body,
  ),
  'commission' => (object)array(
    'tooltip_title' => __('Send an Affiliate Commission Notification Email to the Admin', 'easy-affiliate', 'affiliate-royale'),
    'tooltip_body' => __('When this is checked, Easy Affiliate will send a commission notification email to the Admin when an affiliate has referred a sale.', 'easy-affiliate', 'affiliate-royale'),
    'id' => $wafp_options->admin_email_str,
    'send_label' => __('Send Admin Commission Email', 'easy-affiliate', 'affiliate-royale'),
    'send' => $wafp_options->admin_email,
    'subject_id' => $wafp_options->admin_email_subject_str,
    'subject' => $wafp_options->admin_email_subject,
    'body_id' => $wafp_options->admin_email_body_str,
    'body' => $wafp_options->admin_email_body,
  ),
);
?>
<?php foreach($emails as $slug => $email): ?>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo $email->id; ?>"><?php echo $email->send_label; ?></label>
          <?php WafpAppHelper::info_tooltip("esaf-options-email-{$slug}",$email->tooltip_title,$email->tooltip_body); ?>
        </th>
        <td>
          <input type="checkbox" name="<?php echo $email->id; ?>" id="<?php echo $email->id; ?>" class="esaf-toggle-checkbox" data-box="esaf-options-email-<?php echo $slug; ?>-box" <?php checked($email->send); ?> />
        </td>
      </tr>
    </tbody>
  </table>
  <div class="esaf-sub-box esaf-options-email-<?php echo $slug; ?>-box">
    <div class="esaf-arrow esaf-gray esaf-up esaf-sub-box-arrow"> </div>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo $email->subject_id; ?>"><?php _e('Subject', 'easy-affiliate', 'affiliate-royale'); ?></label>
          </th>
          <td>
            <input class="form-field regular-text" type="text" id="<?php echo $email->subject_id; ?>" name="<?php echo $email->subject_id; ?>" value="<?php echo $email->subject; ?>" />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <label for="<?php echo $email->body_id; ?>"><?php _e('Body', 'easy-affiliate', 'affiliate-royale'); ?></label>
          </th>
          <td>
            <textarea style="min-height: 150px;" class="form-field large-text" id="<?php echo $email->body_id; ?>" name="<?php echo $email->body_id; ?>"><?php echo $email->body; ?></textarea>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

<?php endforeach; ?>

