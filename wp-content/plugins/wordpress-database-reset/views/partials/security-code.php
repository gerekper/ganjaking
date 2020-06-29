<p>
  <b>3.</b> <?php _e('Enter the security code into the text box below', 'wordpress-database-reset') ?>:
  <span id="security-code"><?php echo $this->code ?></span>
</p>

<input type="hidden" name="db-reset-code" value="<?php echo $this->code ?>" />
<input type="text" name="db-reset-code-confirm" id="db-reset-code-confirm" value="" placeholder="" />
<?php wp_nonce_field('reset_nounce', 'submit_reset_form'); ?>