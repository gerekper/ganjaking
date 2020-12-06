<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="wrap">
  <?php WafpAppHelper::plugin_title(__('Links &amp; Banners','affiliate-royale', 'easy-affiliate')); ?>
  <span class="description"><?php _e('To create a banner just hit "Add New" enter a Target URL and hit "Update Links &amp; Banners." To create a simple link just hit "Add New," enter a Target URL &amp; an image file and then hit "Update Links &amp; Banners."', 'affiliate-royale', 'easy-affiliate'); ?></span>
  <br/>
  <form name="wafp_options_form" method="post" action="" enctype="multipart/form-data">
    <input type="hidden" name="action" value="process-form">
    <?php wp_nonce_field('update-links'); ?>
    <table class="widefat post fixed">
      <thead>
        <tr>
          <th class="manage-column" width="7%"><?php _e('Link Type', 'affiliate-royale', 'easy-affiliate'); ?></th>
          <th class="manage-column" width="20%"><?php _e('Target URL', 'affiliate-royale', 'easy-affiliate'); ?></th>
          <th class="manage-column" width="13%"><?php _e('Slug', 'affiliate-royale', 'easy-affiliate'); ?></th>
          <th class="manage-column" width="30%"><?php _e('Text/Image', 'affiliate-royale', 'easy-affiliate'); ?></th>
          <th class="manage-column" width="30%"><?php _e('Info', 'affiliate-royale', 'easy-affiliate'); ?></th>
        </tr>
      </thead>
      <tbody>
      <?php
        $row_index = 0;
        foreach($links as $link):
        $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
      ?>
        <tr id="wafp-link-<?php echo $link->rec->id; ?>" class="<?php echo $alternate; ?>">
          <td><strong><?php echo (isset($link->rec->image) and !empty($link->rec->image))?"Banner":"Text"; ?></strong></td>
          <td valign="bottom">
            <input type="text" id="wafp_link_url[<?php echo $link->rec->id; ?>]" name="wafp_link_url[<?php echo $link->rec->id; ?>]" style="width: 100%;" value="<?php echo isset($_POST['wafp_link_url'][$link->rec->id])?$_POST['wafp_link_url'][$link->rec->id]:$link->rec->target_url; ?>" />
          </td>
          <td valign="bottom">
            <input type="text" id="wafp_link_slug[<?php echo $link->rec->id; ?>]" name="wafp_link_slug[<?php echo $link->rec->id; ?>]" style="width: 100%;" value="<?php echo isset($_POST['wafp_link_slug'][$link->rec->id])?$_POST['wafp_link_slug'][$link->rec->id]:$link->rec->slug; ?>" />
          </td>
          <td valign="bottom">
            <?php if(isset($link->rec->image) and !empty($link->rec->image)): ?>
                  <img src="<?php echo $link->rec->image; ?>" style="max-width: 400px; max-height: 400px;" />
                  <?php if ($link->rec->width and $link->rec->height): ?>
                  <span>(<?php echo $link->rec->width; ?>x<?php echo $link->rec->height; ?>)</span>
                  <?php endif; ?>
                  <br/>
                  <label for="wafp_link_image_<?php echo $link->rec->id ?>">
                    <input id="wafp_link_image_<?php echo $link->rec->id ?>" type="text" size="36" name="wafp_link_image[<?php echo $link->rec->id ?>]" value="<?php echo $link->rec->image?>">
                    <input id="wafp_link_image_button_<?php echo $link->rec->id ?>" class="wafp-upload-button" type="button" value="<?php _e('Choose...', 'affiliate-royale', 'easy-affiliate'); ?>">
                  </label>
              <br/><?php _e("Enter an URL or choose an image for the banner.", 'affiliate-royale', 'easy-affiliate'); ?>
            <?php else: ?>
                  <label for="wafp_link_description_<?php echo $link->rec->id ?>">
                    <input id="wafp_link_description_<?php echo $link->rec->id ?>" type="text" size="36" name="wafp_link_description[<?php echo $link->rec->id ?>]" value="<?php echo stripslashes($link->rec->description); ?>">
                  </label>
                  <br/><?php _e("Enter a link description for your URL.", 'affiliate-royale', 'easy-affiliate'); ?>
            <?php endif; ?>
          </td>
          <td>
            <textarea id="wafp_link_info_<?php echo $link->rec->id; ?>" class="wafp-link-textarea" name="wafp_link_info[<?php echo $link->rec->id; ?>]"><?php echo stripslashes($link->rec->info); ?></textarea>
            <a href="javascript:wafp_delete_link( <?php echo $link->rec->id; ?>, '<?php _e('Are you sure you want to delete this link?', 'affiliate-royale', 'easy-affiliate'); ?>' );" style="float: right;"><i class="ar-icon-cancel-circled ar-16"> </i></a>
          </td>
        </tr>
      <?php endforeach; ?>
        <tr class="wafp-new-link wafp-hidden">
          <td><select onchange="jQuery('#wafp_new_link_description').toggle(); jQuery('#wafp_new_link_upload').toggle()"><option value="text"><?php _e("Text", 'affiliate-royale', 'easy-affiliate'); ?>&nbsp;</option><option value="banner"><?php _e("Banner", 'affiliate-royale', 'easy-affiliate'); ?></option></select></td>
          <td valign="bottom">
            <input type="text" id="wafp_new_link_url" name="wafp_new_link_url" style="width: 100%;" value="" /><br>
            <input type="checkbox" name="wafp_new_default_link"> <?php echo __('Default link', 'affiliate-royale', 'easy-affiliate'); ?>
          </td>
          <td valign="bottom">
            <input type="text" id="wafp_new_link_slug" name="wafp_new_link_slug" style="width: 100%;" value="" /><br>
          </td>
          <td valign="bottom">
            <div id="wafp_new_link_description">
               <label for="wafp_new_link_description">
                 <input id="wafp_new_link_description" type="text" size="36" name="wafp_new_link_description" value="">
               </label>
               <br/><?php _e("Enter a link description for your URL.", 'affiliate-royale', 'easy-affiliate'); ?>
            </div>
            <div id="wafp_new_link_upload" style="display:none;">
              <label for="wafp_new_link_image">
                <input id="wafp_new_link_image" type="text" size="36" name="wafp_new_link_image" value="">
                <input id="wafp_new_link_image_button" class="wafp-upload-button" type="button" value="<?php _e('Choose...', 'affiliate-royale', 'easy-affiliate'); ?>">
              </label>
              <br/><?php _e("Enter an URL or choose an image for the banner.", 'affiliate-royale', 'easy-affiliate'); ?>
            </div>
          </td>
          <td>
            <textarea id="wafp_new_link_info" name="wafp_new_link_info" class="wafp-link-textarea"></textarea>
            <a href="javascript:wafp_toggle_new_form();" style="float: right;"><i class="ar-icon-cancel-circled ar-16"> </i></a>
          </td>
        </tr>
      </tbody>
    </table>
    <p class="wafp-display-new-form"><a href="javascript:wafp_toggle_new_form();"><i class="ar-icon-plus-circled ar-24"> </i></a></p>
    <p>
      <?php _e('Default affiliate link:', 'affiliate-royale', 'easy-affiliate'); ?>
      <?php $delim = preg_match('/\?/',home_url()) ? '&' : '?'; ?>
      <?php $std_url = home_url("{$delim}aff=superaffiliate"); ?>
      <select id="wafp_default_link" name="wafp_default_link">
        <option value="0"><?php printf(__('Standard (%s)','affiliate-royale', 'easy-affiliate'), $std_url); ?></option>
        <?php foreach($links as $link): ?>
          <option value="<?php echo $link->rec->id ?>" <?php if ($link->rec->id == $default_link_id) echo "selected"?>><?php echo $link->rec->target_url ?></option>
        <?php endforeach; ?>
      </select>

      <div class="wafp_custom_default_redirect_wrap">
        <label for="wafp_custom_default_redirect">
          <input type="checkbox" name="wafp_custom_default_redirect" id="wafp_custom_default_redirect"<?php checked($wafp_options->custom_default_redirect); ?>/>&nbsp;<?php _e('Custom Standard Link Redirection','affiliate-royale', 'easy-affiliate'); ?>
        </label>
        <p class="description"><?php printf(__('This will override the default behavior of the standard link (%s) so it will redirect to a custom url rather than it\'s default behavior to just track and then pass-through.','affiliate-royale', 'easy-affiliate'), $std_url); ?></p>
        <div class="wafp-options-pane wafp_custom_default_redirect_url_wrap wafp-hidden">
          <div><?php _e('Standard Link Redirect URL:', 'affiliate-royale', 'easy-affiliate'); ?>&nbsp;<input type="text" name="wafp_custom_default_redirect_url" id="wafp_custom_default_redirect_url" value="<?php echo $wafp_options->custom_default_redirect_url; ?>" />&nbsp;<span id="wafp_custom_default_redirect_error" class="wafp-hidden wafp-inline-error"><?php _e('The Standard Link Redirect URL must be a properly formatted URL', 'affiliate-royale', 'easy-affiliate'); ?></span>
        </div>
      </div>
    </p>
    <input type="submit" name="Submit" class="button-primary wafp-link-update-button" value="<?php _e('Update Links &amp; Banners', 'affiliate-royale', 'easy-affiliate') ?>" />
  </form>
</div>

