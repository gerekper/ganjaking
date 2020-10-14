<?php
namespace memberpress\downloads\helpers;
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class App {
  public static function info_tooltip($id, $title, $info) {
    ?>
    <span id="admin-tooltip-<?php echo $id; ?>" class="admin-tooltip">
      <span class="info-icon dashicons dashicons-info"></span>
      <span class="data-title hidden"><?php echo $title; ?></span>
      <span class="data-info hidden"><?php echo $info; ?></span>
    </span>
    <?php
  }
}

