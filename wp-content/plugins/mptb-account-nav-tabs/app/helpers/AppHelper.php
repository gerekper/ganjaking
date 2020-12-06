<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class MantAppHelper {
  public static function render_admin_page_tabs() {
    global $mant_app_ctrl;
    $tabs = $mant_app_ctrl->get_tabs();
    $count = 1;

    if($tabs !== false && is_array($tabs)) {
      foreach($tabs as $tab) {
        self::render_admin_page_tab($count++, $tab->name, $tab->type, $tab->url, $tab->content, $tab->new_tab);
      }
    }
    else { // Output a blank tab row
      self::render_admin_page_tab($count, '', 'content', '', '', '');
    }
  }

  public static function render_admin_page_tab($id, $name, $type, $url, $content, $new_tab) {
    $url_hidden = ($type == 'content') ? 'mant-hidden' : '';
    $content_hidden = ($type == 'url') ? 'mant-hidden' : '';
    ?>
      <div class="mant-tab mant-tab-<?php echo $id; ?>" dataid="<?php echo $id; ?>">
        <div>
          <img src="<?php echo MANTURL . 'images/remove.png'; ?>" class="mant-tab-remove" dataid="<?php echo $id; ?>" style="float:right;" />
        </div>
        <span style="clear:both;"><!-- clear float --></span>
        <label for="mant-tab-name-<?php echo $id; ?>" class="mant-tab-main-label"><?php _e('Tab Title', 'mant'); ?> <small>(<?php _e('Keep title length short', 'mant'); ?>)</small></label>
        <br/>
        <input type="text" name="mant-tab[<?php echo $id; ?>][name]" id="mant-tab-name-<?php echo $id; ?>" class="mant-tab-text" value="<?php echo stripslashes($name); ?>" />
        <br/><br/>
        <label class="mant-tab-main-label"><?php _e('Tab Type', 'mant'); ?></label>
        <br/>
        <input type="radio" name="mant-tab[<?php echo $id; ?>][type]" id="mant-tab-type-<?php echo $id; ?>-content" class="mant-tab-radio" value="content" dataid="<?php echo $id; ?>" datatype="content" <?php checked(($type == 'content')); ?> />
        <label for="mant-tab-type-<?php echo $id; ?>-content" class="mant-tab-radio-label"><?php _e('Custom Content', 'mant'); ?></label>
        <br/><br/>
        <input type="radio" name="mant-tab[<?php echo $id; ?>][type]" id="mant-tab-type-<?php echo $id; ?>-url" class="mant-tab-radio" value="url" dataid="<?php echo $id; ?>" datatype="url" <?php checked(($type == 'url')); ?> />
        <label for="mant-tab-type-<?php echo $id; ?>-url" class="mant-tab-radio-label"><?php _e('URL', 'mant'); ?></label>
        <br/><br/>
        <div id="mant-tab-hidden-content-<?php echo $id; ?>" class="mant-hidden-content-area <?php echo $content_hidden; ?>">
          <?php
            if(wp_doing_ajax()) {
              ?>
                <textarea style="height:200px;width:95%;" autocomplete="off" name="mant-tab[<?php echo $id; ?>][content]" id="<?php echo $id; ?>">Enter your content here. After saving once, this field will become a full WYSIWYG editor.</textarea>
              <?php
            }
            else {
              $editor_settings = array(
                'textarea_name' => 'mant-tab['.$id.'][content]',
                'teeny'         => true,
                'editor_height' => 200
              );
              wp_editor(stripslashes($content), 'navtabcontent' . $id, $editor_settings);
            }
          ?>
        </div>
        <div id="mant-tab-hidden-url-<?php echo $id; ?>" class="mant-hidden-content-area <?php echo $url_hidden; ?>">
          <label for="mant-tab-url-<?php echo $id; ?>"><?php _e('Enter URL', 'mant'); ?></label>
          <br/>
          <input type="text" name="mant-tab[<?php echo $id; ?>][url]" id="mant-tab-url-<?php echo $id; ?>" class="mant-tab-text" value="<?php echo stripslashes($url); ?>" />
          <br/><br/>
          <input type="checkbox" name="mant-tab[<?php echo $id; ?>][new_tab]" id="mant-tab-new-tab-<?php echo $id; ?>" class="mant-tab-checkbox" <?php checked($new_tab); ?> />
          <label for="mant-tab-new-tab-<?php echo $id; ?>"><?php _e('Open url in new tab', 'mant'); ?></label>
        </div>
        <br/>
      </div>
    <?php
  }
}
