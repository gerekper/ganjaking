<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @var YITH_Commissions_list_Table $commissions_table
 */
?>

<div class="wrap">

    <h2><?php echo $page_title ?></h2>

    <?php $commissions_table->views(); ?>

    <form id="commissions-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $commissions_table->add_search_box( __( 'Search commissions', 'yith-woocommerce-product-vendors' ), 's' ); ?>
        <?php $commissions_table->display(); ?>
    </form>
</div>