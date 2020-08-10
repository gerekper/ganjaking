<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php
// General
if (1 === $type) { ?>
  <div class="energyplus-title __A__GP">
  <?php if (!isset($no_button)) { ?>
      <span class="energyplus-title--buttons"><?php echo wp_kses_post($buttons) ?></span>
    <?php } ?></h3>
  </div>
  <div class="__A__Clear_Both"></div>
<?php } ?>
