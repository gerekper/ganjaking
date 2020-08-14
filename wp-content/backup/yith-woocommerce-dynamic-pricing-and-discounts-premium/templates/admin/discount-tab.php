<div class="wrap">
    <h1><?php echo $title ?><a href="<?php echo esc_url( add_query_arg( array( 'post_type' => 'ywdpd_discount', 'ywdpd_discount_type' => $type ), admin_url('post-new.php') ) ) ?>" class="add-new-h2">Add New</a></h1>

    <div id="poststuff">

                    <form method="post">
                        <input type="hidden" name="page" value="yith_woocommerce_dynamic_pricing_and_discounts" />
                    </form>
                    <form method="post" id="ywdpd-discount-list-table" data-type="<?php echo $type ?>">
                        <?php
                        $this->cpt_obj_discount->views();
                        $this->cpt_obj_discount->prepare_items();
                        $this->cpt_obj_discount->search_box( 'Search rule', 'rule' );
                        $this->cpt_obj_discount->display(); ?>
                    </form>

    </div>
</div>