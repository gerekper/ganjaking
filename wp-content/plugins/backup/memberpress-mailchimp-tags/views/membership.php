<div id="mepr-mailchimptags-tags" class="mepr-product-adv-item">
  <input type="checkbox" name="meprmailchimptags_add_tags" id="meprmailchimptags_add_tags" data-apikey="<?php echo $this->apikey(); ?>" data-listid="<?php echo $this->list_id(); ?>" <?php checked($add_tag); ?> />
  <label for="meprmailchimptags_add_tags"><?php _e('MailChimp Merge Tag for this Membership', 'memberpress-mailchimp-tags'); ?></label>

  <?php MeprAppHelper::info_tooltip('meprmailchimptags-add-merge-tag',
    __('Enable MailChimp Merge Tag for this Membership', 'memberpress-mailchimp-tags'),
    __('If this is set then anyone who is active will have this tag applied and set to "active" in your MailChimp list. If the user becomes inactive on this membership then the tag will be set to "inactive".', 'memberpress-mailchimp-tags') . '<br/><br/>' . __('You can then create segments in your MailChimp list based on who is and who is not active for this membership level.', 'memberpress-mailchimp-tags') . '<br/><br/>' . __('Note: Changing the tag values here after some are applied to contact records in MailChimp, will prevent them from being removed later from those records if they become inactive.', 'memberpress-mailchimp-tags'));
  ?>

  <div id="meprmailchimptags_tags_area" class="mepr-hidden product-options-panel">
    <label><?php _e('MailChimp Merge Tag: ', 'memberpress-mailchimp-tags'); ?></label>
    <select name="meprmailchimptags_tag_id" id="meprmailchimptags_tag_id" data-tagid="<?php echo $tag; ?>" class="mepr-text-input form-field"></select>
  </div>
</div>