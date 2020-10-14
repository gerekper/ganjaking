<div class="mp-form-row">
  <div class="mepr-mailchimptags-signup-field">
    <div id="mepr-mailchimptags-checkbox">
      <input type="checkbox" name="meprmailchimptags_opt_in" id="meprmailchimptags_opt_in" class="mepr-form-checkbox" <?php checked($optin); ?> />
      <span class="mepr-mailchimptags-message"><?php echo $this->optin_text(); ?></span>
    </div>
    <div id="mepr-mailchimptags-privacy">
      <small>
        <a href="http://mailchimp.com/legal/privacy/" class="mepr-mailchimp-privacy-link" target="_blank"><?php _e('We Respect Your Privacy', 'memberpress-mailchimp-tags'); ?></a>
      </small>
    </div>
  </div>
</div>
