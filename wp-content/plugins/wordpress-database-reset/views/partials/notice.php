<?php if ( $this->notice_success ) : ?>
  <div class="updated notice is-dismissible">
    <p><strong><?php echo $this->notice_success ?>.</strong></p>
  </div>
<?php elseif ( $this->notice_error ) : ?>
  <div class="error notice is-dismissible">
    <p><strong><?php echo $this->notice_error; ?></strong></p>
  </div>
<?php endif ?>

<?php if ( $this->notice_success ) : ?>
  <div class="notice-info notice is-dismissible" id="rate-plugin-notice" style="display: none;">
    <p><strong>Please <a href="https://wordpress.org/support/plugin/wordpress-database-reset/reviews/#new-post" target="_blank">rate the plugin &starf;&starf;&starf;&starf;&starf;</a>. It's what keeps it free &amp; maintained. Thank you!</strong></p>
  </div>
<?php endif ?>
