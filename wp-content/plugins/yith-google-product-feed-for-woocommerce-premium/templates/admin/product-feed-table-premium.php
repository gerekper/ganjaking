
<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$table = new YITH_WCGPF_Google_Product_Feed_List_Table();


$admin_url = admin_url('post-new.php');
$params = array(
    'post_type' => 'yith-wcgpf-feed'
);

$add_new_url = esc_url(add_query_arg($params, $admin_url));

?>

<div class="wrap">
    <h1><?php esc_html_e('Product Feed Table', 'yith-google-product-feed-for-woocommerce') ?><a href="<?php echo $add_new_url; ?>" class="add-new-h2"><?php esc_html_e('Add new product feed','yith-google-product-feed-for-woocommerce')?></a> </h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <input type="hidden" name="page" value="yith_google_product_feed_for_woocommerce" />
                    </form>
                    <form method="post">
                        <?php
                        $table->views();
                        $table->prepare_items();
                        $table->display(); ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <div class="yith-wcgpf-note">
        <b><?php esc_html_e('* Note: If you have problems using the "Feed Url" you can try using the "Feed File" url ','yith-google-product-feed-for-woocommerce') ?></b>
    </div>
</div>