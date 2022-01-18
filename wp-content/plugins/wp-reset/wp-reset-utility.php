<?php
/*
 * WP Reset PRO
 * Utility & Helper functions
 * (c) WebFactory Ltd, 2015 - 2021
 */

// include only file
if (!defined('ABSPATH')) {
  die('Do not open this file directly.');
}

class WP_Reset_Utility
{
  /**
   * Whitelabel filter
   *
   * @return bool display contents if whitelabel is not enabled or not hidden
   */
  static function whitelabel_filter()
  {
    global $wp_reset, $wp_reset_licensing;
    $options = $wp_reset->get_options();

    if (!$wp_reset_licensing->is_active('white_label')) {
      return true;
    }

    if (!$options['whitelabel']) {
      return true;
    }

    return false;
  } // whitelabel_filter


  /**
   * Creates a fancy, iOS alike toggle switch
   *
   * @param string $name ID used for checkbox.
   * @param array $options Various options: value, saved_value, option_key, class
   * @param boolean $echo Default: true.
   * @return void
   */
  static function create_toogle_switch($name, $options = array(), $echo = true)
  {
    $default_options = array('value' => '1', 'saved_value' => '', 'option_key' => $name, 'class' => '');
    $options = array_merge($default_options, $options);

    $out = "\n";
    $out .= '<div class="toggle-wrapper ' . $options['class'] . '">';
    $out .= '<input type="checkbox" id="' . $name . '" ' . self::checked($options['value'], $options['saved_value']) . ' type="checkbox" value="' . $options['value'] . '" name="' . $options['option_key'] . '">';
    $out .= '<label for="' . $name . '" class="toggle"><span class="toggle_handler"></span></label>';
    $out .= '</div>';

    if ($echo) {
      echo $out;
    } else {
      return $out;
    }
  } // create_toggle_switch


  /**
   * Helper for creating checkboxes.
   *
   * @param string $value Checkbox value, in HTML.
   * @param array $current Current, saved value of checkbox.
   * @param boolean $echo Default: false.
   *
   * @return void|string
   */
  static function checked($value, $current, $echo = false)
  {
    $out = '';

    if (!is_array($current)) {
      $current = (array) $current;
    }

    if (in_array($value, $current)) {
      $out = ' checked="checked" ';
    }

    if ($echo) {
      echo $out;
    } else {
      return $out;
    }
  } // checked


  /**
   * Format file size to human readable string
   *
   * @param int  $bytes  Size in bytes to format.
   *
   * @return string
   */
  static function format_size($bytes)
  {
    if ($bytes > 1073741824) {
      return number_format_i18n($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes > 1048576) {
      return number_format_i18n($bytes / 1048576, 1) . ' MB';
    } elseif ($bytes > 1024) {
      return number_format_i18n($bytes / 1024, 1) . ' KB';
    } else {
      return number_format_i18n($bytes, 0) . ' bytes';
    }
  } // format_size


  /**
   * Create select options for select
   *
   * @param array $options options
   * @param string $selected selected value
   * @param bool $output echo, if false return html as string
   * @return string html with options
   */
  static function create_select_options($options, $selected = null, $output = true)
  {
    $out = "\n";

    if (is_array($options) && !empty($options) && !isset($options[0]['val'])) {
      $tmp = array();
      foreach ($options as $val => $label) {
        $tmp[] = array('val' => $val, 'label' => $label);
      } // foreach
      $options = $tmp;
    }

    foreach ($options as $tmp) {
      if ($selected == $tmp['val']) {
        $out .= "<option selected=\"selected\" value=\"{$tmp['val']}\">{$tmp['label']}&nbsp;</option>\n";
      } else {
        $out .= "<option value=\"{$tmp['val']}\">{$tmp['label']}&nbsp;</option>\n";
      }
    }

    if ($output) {
      echo $out;
    } else {
      return $out;
    }
  } //  create_select_options


  /**
   * Get table size and row count as html
   *
   * @return string html with table details
   */
  static function get_table_details()
  {
    global $wpdb, $wp_reset;

    $tbl_core = $tbl_custom = $tbl_size = $tbl_rows = 0;
    $table_status = $wpdb->get_results('SHOW TABLE STATUS');
    if (is_array($table_status)) {
      foreach ($table_status as $index => $table) {
        if (0 !== stripos($table->Name, $wpdb->prefix)) {
          continue;
        }
        if (empty($table->Engine)) {
          continue;
        }

        $tbl_rows += $table->Rows;
        $tbl_size += $table->Data_length + $table->Index_length;
        if (in_array($table->Name, $wp_reset->core_tables)) {
          $tbl_core++;
        } else {
          $tbl_custom++;
        }
      } // foreach
    } else {
      return ' no tables found.';
    }
    return ' totaling ' . self::format_size($tbl_size) . ' in ' . number_format($tbl_rows) . ' rows.';
  } // get_table_details

  /**
   * Compress file as gz
   *
   * @return string source file path
   */
  static function gzCompressFile($source, $level = 9){ 
    $dest = $source . '.gz'; 
    $mode = 'wb' . $level; 
    $error = false; 
    if ($fp_out = gzopen($dest, $mode)) { 
        if ($fp_in = fopen($source,'rb')) { 
            while (!feof($fp_in)) 
                gzwrite($fp_out, fread($fp_in, 1024 * 512)); 
            fclose($fp_in); 
        } else {
            $error = true; 
        }
        gzclose($fp_out); 
    } else {
        $error = true; 
    }
    if ($error)
        return false; 
    else
        return $dest; 
  } 
} // WP_Reset_Utility
