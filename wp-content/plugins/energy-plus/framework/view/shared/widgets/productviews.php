<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<h1><?php esc_html_e('Product Views', 'energyplus'); ?></h1>
<?php if (0 === count($result)) { ?>
  <div class="__A__EmptyTable <?php if (0 < count($result)) echo ' d-none'; else echo 'd-flex'; ?> align-items-center justify-content-center text-center">
    <div><span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No products viewed today', 'energyplus'); ?></div>
  </div>
<?php } else { ?>
  <div class="__A__Widget_Content_Inner">
    <table class="table">
      <?php foreach ($result AS $r) { ?>
        <tr>
          <td>
            <?php if ( '1' === $r['type']) { ?>
            <a href="<?php echo get_permalink($r['id']); ?>"><?php echo esc_html(get_the_title($r['id'])); ?></a>
          <?php } else { ?>
            <?php echo get_term_by( 'id', $r['id'], 'product_cat' )->name; ?>&nbsp; <span class="badge-pill badge-secondary __A__Cat"><?php esc_html_e('CATEGORY', 'energyplus'); ?></span>
          <?php } ?>
          </td>
          <td class="text-right">
            <?php echo intval($r['cnt'])?>
          </td>
        </tr>
      <?php } ?>
    </table>
  </div>
<?php } ?>
