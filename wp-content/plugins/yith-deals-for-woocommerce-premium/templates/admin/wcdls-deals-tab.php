<?php

$wcdppm_cpt_rules = new YITH_WCDLS_List_Deals();

$admin_url = admin_url('post-new.php');
$params = array(
    'post_type' => 'yith_wcdls_offer'
);

$add_new_url = esc_url(add_query_arg($params, $admin_url));

?>

<div class="wrap">
    <h1><?php esc_html_e('Deals', 'yith-deals-for-woocommerce') ?><a href="<?php echo $add_new_url; ?>" class="add-new-h2"><?php esc_html_e('Add new','yith-deals-for-woocommerce')?></a> </h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <input type="hidden" name="page" value="yith_deals_for_woocommerce" />
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