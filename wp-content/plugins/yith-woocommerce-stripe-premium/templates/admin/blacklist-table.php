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
 * @var YITH_Stripe_Blacklist_Table $blacklist_table
 */
?>

<div class="wrap">

    <h2><?php _e( 'Stripe Blacklist', 'yith-woocommerce-stripe' ) ?></h2>

    <?php $blacklist_table->views(); ?>

    <form id="commissions-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <input type="hidden" name="tab" value="<?php echo $_REQUEST['tab'] ?>" />
        <?php $blacklist_table->add_search_box( __( 'Search bans', 'yith-woocommerce-stripe' ), 's' ); ?>
        <?php $blacklist_table->display(); ?>
    </form>
</div>