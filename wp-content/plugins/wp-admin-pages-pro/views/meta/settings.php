<fieldset class="metabox-prefs">
  <legend><?php _e('Admin Pages', 'wu-apc'); ?></legend>
  <label for="display-admin-pages-menu">
    <input name="display-admin-pages-menu" type="checkbox" id="display-admin-pages-menu" value="1" <?php checked($is_menu_visible == 'yes'); ?>><?php _e('Display the "Admin Pages" menu item?', 'wu-apc'); ?>
  </label>
  <?php echo get_submit_button(__('Save Menu Visibility', 'wu-apc'), 'secondary', 'submit-display-admin-pages', false); ?>
</fieldset>