<?php
/**
 * Template of Best Seller Badge
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

?>

<div class="yith-wcbsl-badge-wrapper <?php echo $class; ?>">
    <div class="yith-wcbsl-badge-content">
        <?php
        $badge_text = get_option('yith-wcbsl-badge-text', _x('Best Seller','Text of "Bestseller" Badge' ,'yith-woocommerce-best-sellers') );
        echo $badge_text;
        ?>
    </div>
</div>
