<?php
namespace memberpress\courses\helpers;
use memberpress\courses\helpers as helpers;
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class App {
  public static function info_tooltip($id, $title, $info) {
    ?>
    <span id="admin-tooltip-<?php echo $id; ?>" class="admin-tooltip">
      <i class="mpcs-icon mpcs-info-circled mpcs-info-icon"></i>
      <span class="data-title hidden"><?php echo $title; ?></span>
      <span class="data-info hidden"><?php echo $info; ?></span>
    </span>
    <?php
  }

  /**
   * Checks if we are in Classroom Mode
   *
   * @return bool
   */
  public static function is_classroom(){
    $options = \get_option('mpcs-options');
    $classroom_mode = helpers\Options::val($options,'classroom-mode', 1);
    return $classroom_mode == '1';
  }

  /**
   * Determine if current post uses Gutenberg
   *
   * @return bool
   */
  public static function is_gutenberg_page() {
    if ( function_exists( 'is_gutenberg_page' ) &&
      is_gutenberg_page()
    ) {
      // The Gutenberg plugin is on.
      return true;
    }
    $current_screen = get_current_screen();
    if ( method_exists( $current_screen, 'is_block_editor' ) &&
      $current_screen->is_block_editor()
    ) {
      // Gutenberg page on 5+.
      return true;
    }
    return false;
  }

}
