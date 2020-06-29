<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$wcdppm_cpt_rules = new YITH_WCGPF_Google_Product_Feed_Template_List_Table();

$admin_url = admin_url('post-new.php');
$params = array(
    'post_type' => 'yith-wcgpf-template'
);

$add_new_url = esc_url(add_query_arg($params, $admin_url));

?>

<div class="wrap">
    <h1><?php esc_html_e('Feed Configuration Template List', 'yith-google-product-feed-for-woocommerce') ?><a href="<?php echo $add_new_url; ?>" class="add-new-h2"><?php esc_html_e('Add new','yith-google-product-feed-for-woocommerce')?></a> </h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <input type="hidden" name="page" value="yit_google_product_feed_for_woocommerce" />
                    </form>
                    <form method="post">
                        <?php
                        $wcdppm_cpt_rules->views();
                        $wcdppm_cpt_rules->prepare_items();
                        $wcdppm_cpt_rules->display(); ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>