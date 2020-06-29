<div id="mepr-mailchimptags" class="mepr-autoresponder-config">
  <input type="checkbox" name="meprmailchimptags_enabled" id="meprmailchimptags_enabled" <?php checked($this->is_enabled()); ?> />
  <label for="meprmailchimptags_enabled"><?php _e('Enable MailChimp 3.0', 'memberpress-mailchimp-tags'); ?></label>
</div>
<div id="mailchimptags_hidden_area" class="mepr-options-sub-pane">
  <div id="meprmailchimptags-api-key">
    <label>
      <span><?php _e('MailChimp API Key:', 'memberpress-mailchimp-tags'); ?></span>
      <input type="text" name="meprmailchimptags_api_key" id="meprmailchimptags_api_key" value="<?php echo $this->apikey(); ?>" class="mepr-text-input form-field" size="90" />
      <span id="mepr-mailchimptags-valid" class="mepr-active mepr-hidden"></span>
      <span id="mepr-mailchimptags-invalid" class="mepr-inactive mepr-hidden"></span>
    </label>
    <div>
      <span class="description">
        <?php _e('You can find your API key under your Account settings at mailchimp.com.', 'memberpress-mailchimp-tags'); ?>
      </span>
    </div>
  </div>
  <br/>
  <div id="meprmailchimptags-list-id">
    <label>
      <span><?php _e('MailChimp List:', 'memberpress-mailchimp-tags'); ?></span>
      <select name="meprmailchimptags_list_id" id="meprmailchimptags_list_id" data-listid="<?php echo $this->list_id(); ?>" class="mepr-text-input form-field"></select>
    </label>
    <div>
      <span class="description"><?php _e('This is the list you want associated with your members.', 'memberpress-mailchimp-tags'); ?></span>
    </div>
  </div>
  <br/>
  <div id="meprmailchimptags-optin-tags">
    <label>
      <span><?php _e('MailChimp Global Merge Tag:', 'memberpress-mailchimp-tags'); ?></span>
      <select name="meprmailchimptags_tag_id" id="meprmailchimptags_tag_id" data-tagid="<?php echo $this->global_tag_id(); ?>" class="mepr-text-input form-field"></select>
    </label>
    <div>
      <span class="description"><?php _e('This merge tag will be set to a value of "1" for all MemberPress subscribers.', 'memberpress-mailchimp-tags'); ?></span>
    </div>
  </div>
  <br/>
  <div id="meprmailchimptags-double-optin">
    <input type="checkbox" name="meprmailchimptags_double_opt_in" id="meprmailchimptags_double_opt_in" class="form-field" <?php checked($this->double_opt_in()); ?> />
    <label for="meprmailchimptags_double_opt_in">
      <span style="vertical-align:top"><?php _e('Enable Double Opt-In Email', 'memberpress-mailchimp-tags'); ?></span>
    </label>
    <div>
      <span class="description"><?php _e('When enabled, the member must click a confirmation link in their email before they will be subscribed to your list. (Recommended)', 'memberpress-mailchimp-tags'); ?></span>
    </div>
  </div>
  <br/>
  <div id="meprmailchimptags-optin">
    <label>
      <input type="checkbox" name="meprmailchimptags_optin" id="meprmailchimptags_optin" <?php checked($this->is_optin_enabled()); ?> />
      <span><?php _e('Enable Opt-In Checkbox', 'memberpress-mailchimp-tags'); ?></span>
    </label>
    <div>
      <span class="description">
        <?php _e('If checked, an opt-in checkbox will appear on all of your membership registration forms. If the user does not opt-in, they will be added to your list with the Global Merge Tag value of 0 instead of 1.', 'memberpress-mailchimp-tags'); ?>
      </span>
    </div>
  </div>
  <div id="meprmailchimptags-optin-text" class="mepr-hidden mepr-options-panel">
    <label><?php _e('Signup Checkbox Label:', 'memberpress-mailchimp-tags'); ?>
      <input type="text" name="meprmailchimptags_optin_text" id="meprmailchimptags_optin_text" value="<?php echo $this->optin_text(); ?>" class="form-field" size="75" />
    </label>
    <div>
      <span class="description">
        <?php _e('This is the text that will display on the signup form next to your mailing list opt-in checkbox.', 'memberpress-mailchimp-tags'); ?>
      </span>
    </div>
  </div>
</div>