<div class="wrap card">
  <h1><?php _e( 'Database Reset', 'wordpress-database-reset' ) ?></h1>

  <?php include( 'partials/notice.php' ) ?>

  <form method="post" id="db-reset-form">
    <?php include( 'partials/select-tables.php' ) ?>
    <?php include( 'partials/security-code.php' ) ?>
    <?php include( 'partials/submit-button.php' ) ?>
  </form>

</div>

<div class="wrap card">
<p>Like the plugin? Please <a href="https://wordpress.org/support/plugin/wordpress-database-reset/reviews/#new-post" target="_blank">rate it &starf;&starf;&starf;&starf;&starf;</a>. It's what keeps it free &amp; maintained.<br><b>Thank you!</b></p>
</div>

<div class="wrap card" id="wp-reset-ad">
  <a href="#" class="open-wpr-upsell"><img src="<?php echo plugins_url( 'assets/images/wp-reset-icon.png', DB_RESET_FILE ); ?>" alt="WP Reset - used on +200,000 sites" title="WP Reset - used on +200,000 sites"></a>
  <p>Need more control? Reset theme options, media files, transients or <em>.htaccess</em> file? Delete all uploads, plugins and themes? Or create database snapshots and restore them with one click? <a href="#" class="open-wpr-upsell">Install the free WP Reset plugin</a>. It's used on <b>+200,000 sites daily</b>!</p>
</div>

 <div id="wpr-upsell-dialog" style="display: none;" title="WP Reset">
  <span class="ui-helper-hidden-accessible"><input type="text"/></span>
  <div style="padding: 20px; font-size: 15px;">
    <ul>
      <li>Free plugin used on +200,000 sites</li>
      <li>Simple &amp; easy to use with clear instructions</li>
      <li>Use it to quickly reset any data in WordPress</li>
      <li>Extremely usefull for cleaning demo data from themes &amp; plugins</li>
      <li>Create Database Snapshot to revert WP to previous states with 1 click</li>
      <li>Compare current &amp; previous snapshots to see changes done by plugins</li>
    </ul>
    <p class="upsell-footer"><a class="button button-primary install-wpr">Install &amp; Activate WP Reset</a></p>
  </div>
 </div>
