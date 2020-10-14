<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
use memberpress\courses\helpers as helpers;
use memberpress\courses\models as models;
?>

<div><?php printf(__('You can also change your settings in the <a href="%s">WordPress Customizer</a>', 'memberpress-courses'), admin_url( '/customize.php?autofocus[section]=mpcs_classroom&url=' ) . home_url( 'courses' ) ) ?></div>

<table class="form-table">
  <tbody>

  <tr valign="top">
      <th scope="row">
        <label for="mpcs-options[courses-slug]"><?php _e('Courses Slug', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-courses-slug',
                __('Classroom Mode', 'memberpress-courses'),
                __('Use this field to change the permalink base of your courses to something other than /courses/', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <label>
          <input id="mpcs_options_courses_slug" name="mpcs-options[courses-slug]" class="" type="text" placeholder="<?php esc_attr_e( 'courses', 'memberpress-courses' ); ?>" value="<?php echo helpers\Options::val($options,'courses-slug'); ?>" />
        </label>

      </td>
    </tr>

    <tr valign="top">
      <th scope="row">
        <label for="mpcs-options[classroom-mode]"><?php _e('Classroom Mode', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-classroom-mode',
                __('Classroom Mode', 'memberpress-courses'),
                __('Use this field to switch to Classroom Mode.', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <label class="switch">
          <input id="mpcs_options_classroom_mode" name="mpcs-options[classroom-mode]" class="" type="checkbox" value="1" <?php checked( 1, helpers\Options::val($options,'classroom-mode', 1) ); ?> />
          <span class="slider round"></span>
        </label>

      </td>
    </tr>

    <tr valign="top" class="requires-classroom-mode <?php echo helpers\Options::val($options,'classroom-mode') ? '' : 'hidden'; ?>">
      <th scope="row">
        <label for="mpcs-options[complete-link-css]"><?php _e('Brand Color', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-complete-link-css',
                __('Brand Color', 'memberpress-courses'),
                __('Use this field to change Navbar background color', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <input name="mpcs-options[brand-color]" class="mpcs-color-field" type="text" value="<?php echo helpers\Options::val($options,'brand-color', '#2c3637'); ?>" data-default-color="#2c3637" />
      </td>
    </tr>

    <tr valign="top" class="requires-classroom-mode <?php echo helpers\Options::val($options,'classroom-mode') ? '' : 'hidden'; ?>">
      <th scope="row">
        <label for="mpcs-options[complete-link-css]"><?php _e('Accent Color', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-complete-link-css',
                __('Accent Color', 'memberpress-courses'),
                __('Use this field to change accent color', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <input name="mpcs-options[accent-color]" class="mpcs-color-field" type="text" value="<?php echo helpers\Options::val($options,'accent-color','#2c3637'); ?>" data-default-color="#2c3637" />
      </td>
    </tr>

    <tr valign="top" class="requires-classroom-mode <?php echo helpers\Options::val($options,'classroom-mode') ? '' : 'hidden'; ?>">
      <th scope="row">
        <label for="mpcs-options[progress-color]"><?php _e('Progress Color', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-progress-color',
                __('Progress Color', 'memberpress-courses'),
                __('Use this field to change progress color.', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <input name="mpcs-options[progress-color]" class="mpcs-color-field" type="text" value="<?php echo helpers\Options::val($options,'progress-color','#1da69a'); ?>" data-default-color="#1da69a" />
      </td>
    </tr>

    <tr valign="top" class="requires-classroom-mode <?php echo helpers\Options::val($options,'menu-text-color') ? '' : 'hidden'; ?>">
      <th scope="row">
        <label for="mpcs-options[menu-text-color]"><?php _e('Menu Text Color', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-menu-text-color',
                __('Menu Text Color', 'memberpress-courses'),
                __('Use this field to change text color of menu items.', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <input name="mpcs-options[menu-text-color]" class="mpcs-color-field" type="text" value="<?php echo helpers\Options::val($options,'menu-text-color','#ffffff'); ?>" data-default-color="#ffffff" />
      </td>
    </tr>

    <tr valign="top" class="requires-classroom-mode <?php echo helpers\Options::val($options,'classroom-logo') ? '' : 'hidden'; ?>">
      <th scope="row">
        <label for="mpcs-options[complete-link-css]"><?php _e('Logo', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-complete-link-css',
                __('Logo', 'memberpress-courses'),
                __('Use this field to add custom logo to your classroom header.', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <input type="hidden" name="mpcs-options[classroom-logo]" id="mpcs-options-classroom-logo" value="<?php echo helpers\Options::val($options,'classroom-logo') ?>">
        <div id="mpcs-options-logo-preview">
          <img src="<?php echo wp_get_attachment_url(helpers\Options::val($options,'classroom-logo')) ?>" alt="" class="src">
        </div>

        <div class="mpcs-logo-actions">
          <a class="button-tertiary" id="mpcs-options-logo-replace" href="#0" title="Replace Logo">Replace</a>
          |
          <a class="button-tertiary" id="mpcs-options-logo-remove" href="#0" title="Remove Logo">Remove</a>

        </div>

        <div id="plupload-upload-ui" class="hide-if-no-js">
          <div id="drag-drop-area">
            <div class="drag-drop-inside">
              <p class="drag-drop-info"><?php _e('Upload Logo Image', 'memberpress-courses'); ?></p>
              <p><?php _ex('or', 'Uploader: Upload Logo Image - or - Select Image', 'memberpress-courses'); ?></p>
              <p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Image', 'memberpress-courses'); ?>" class="button" /></p>
            </div>
          </div>
        </div>
      </td>
    </tr>

    <tr valign="top" class="requires-classroom-mode <?php echo helpers\Options::val($options,'classroom-mode') ? '' : 'hidden'; ?>">
      <th scope="row">
        <label for="mpcs-options[complete-link-css]"><?php _e('Complete Link CSS', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-complete-link-css',
                __('Complete Link CSS Classes', 'memberpress-courses'),
                __('Use this field to add custom CSS classes to the "Complete Lesson/Section/Course" link in each of your Lessons.', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <input type="text" name="mpcs-options[complete-link-css]" class="regular-text" value="<?php echo helpers\Options::val($options,'complete-link-css'); ?>" />
      </td>
    </tr>



    <tr valign="top" class="requires-classroom-mode <?php echo helpers\Options::val($options,'classroom-mode') ? '' : 'hidden'; ?>">
      <th scope="row">
        <label for="mpcs-options[previous-link-css]"><?php _e('Previous Link CSS', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-previous-link-css',
                __('Previous Lesson/Section Link CSS Classes', 'memberpress-courses'),
                __('Use this field to add custom CSS classes to the "Previous Lesson/Section" link in each of your Lessons.', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <input type="text" name="mpcs-options[previous-link-css]" class="regular-text" value="<?php echo helpers\Options::val($options,'previous-link-css'); ?>" />
      </td>
    </tr>
    <tr valign="top" class="requires-classroom-mode <?php echo helpers\Options::val($options,'classroom-mode') ? '' : 'hidden'; ?>">
      <th scope="row">
        <label for="mpcs-options[breadcrumb-link-css]"><?php _e('Breadcrumb Link CSS', 'memberpress-courses'); ?></label>
        <?php helpers\App::info_tooltip('mpcs-breadcrumb-link-css',
                __('Breadcrumb Link CSS Classes', 'memberpress-courses'),
                __('Use this field to add custom CSS classes to the breadcrumb links in each of your Lessons.', 'memberpress-courses'));
        ?>
      </th>
      <td>
        <input type="text" name="mpcs-options[breadcrumb-link-css]" class="regular-text" value="<?php echo helpers\Options::val($options,'breadcrumb-link-css'); ?>" />
      </td>
    </tr>
    <?php do_action('mpc_admin_general_options'); ?>
  </tbody>
</table>
