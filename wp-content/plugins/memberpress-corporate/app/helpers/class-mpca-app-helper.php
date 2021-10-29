<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/*
 * Instantiate the helper in your controller and call its functions from the
 * view template.
 *
 * Example:
 *
 *   // controller
 *   $app_helper = new MPCA_App_Helper();
 *
 *   // view template
 *   <?php echo $app_helper->clipboard_input($url_to_copy) ?>
 */
class MPCA_App_Helper {
  public static function clipboard_input($value, $input_classes='', $icon_classes='') {
    if(empty($icon_classes)) {
      $icon_classes = 'mpca-16';
    }

    ?>
      <input type="text" class="mpca-clipboard-input <?php echo $input_classes; ?>" onfocus="this.select();" onclick="this.select();" readonly="true" value="<?php echo $value; ?>" />
      <span class="mpca-clipboard">
        <i class="mpca-clipboardjs mpca-icon mpca-icon-clipboard <?php echo $icon_classes; ?>" data-clipboard-text="<?php echo $value; ?>"></i>
      </span>
    <?php
  }
}
