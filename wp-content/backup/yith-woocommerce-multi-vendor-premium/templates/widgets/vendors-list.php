<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$vendors = YITH_Vendors()->get_vendors( array( 'enabled_selling' => true ) );
?>

<div class="clearfix widget vendors-list">
    <h3 class="widget-title"><?php echo $title ?></h3>
    <?php
    if( ! empty( $vendors ) ) : ?>
        <ul>
        <?php
        foreach( $vendors as $vendor ) :
            $product_number = count ( $vendor->get_products() );
            if( isset( $hide_empty ) && ! empty( $hide_empty ) && empty( $product_number ) || empty( $vendor->owner ) ) {
                continue;
            }
            ?>
            <li>
                <a class="vendor-store-url" href="<?php echo $vendor->get_url() ?>">
                    <?php echo $vendor->name ?>
                </a>
                <?php
                if( isset( $show_product_number ) && ! empty( $show_product_number ) ) {
                    echo " ({$product_number}) ";
                }
                ?>
            </li>
        <?php
        endforeach; ?>
        </ul>
    <?php endif; ?>
</div>