<p>
  <b>1.</b> The plugin DOES NOT create backups. Please create a backup manually or install the <a class="open-wpr-upsell" href="#">free WP Reset plugin</a> which has snapshots that wlll enable you to undo a reset. If something is not clear open a ticket on the official <a target="_blank" href="https://wordpress.org/support/plugin/wordpress-database-reset/">support forum</a>. All tickets are answered within a few hours.
</p>

<p><b>2.</b> <?php _e( 'Select the database table(s) you would like to reset', 'wordpress-database-reset' ) ?>:</p>

<div id="select-container">
  <a href='#' id="select-all"><?php _e( 'Select All Tables', 'wordpress-database-reset' ) ?></a>
  <select id="wp-tables" multiple="multiple" name="db-reset-tables[]">
    <?php foreach ( $this->wp_tables as $key => $value ) : ?>
      <option value="<?php echo $key ?>"><?php echo $key ?></option>
    <?php endforeach ?>
  </select>
</div>

<p id="reactivate" style="display: none;">&bull;
  <label for="db-reset-reactivate-theme-data">
    <input type="checkbox" name="db-reset-reactivate-theme-data" id="db-reset-reactivate-theme-data" checked="checked" value="true" />
    <em><?php _e( 'You selected the options table. Reactivate current theme and plugins after reset?', 'wordpress-database-reset' ) ?></em>
  </label>
</p>

<p id="disclaimer" style="display: none;">&bull;
  <em><?php printf( __( 'You selected the users table. Only the <strong><u>%s</u></strong> user will be restored', 'wordpress-database-reset' ), $this->user->user_login ) ?>.</em>
</p>

<hr>
