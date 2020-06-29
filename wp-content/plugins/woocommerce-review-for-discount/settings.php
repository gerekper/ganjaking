<div class="wrap woocommerce">
	<div id="icon-edit-comments" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
    	<a href="admin.php?page=wc-review-discount&amp;tab=discounts" class="nav-tab <?php echo ($tab == 'discounts') ? 'nav-tab-active' : ''; ?>"><?php _e('Discounts', 'wc_review_discount'); ?></a>
        <a href="admin.php?page=wc-review-discount&amp;tab=new" class="nav-tab <?php echo ($tab == 'new') ? 'nav-tab-active' : ''; ?>"><?php _e('New Discount', 'wc_review_discount'); ?></a>
        <a href="admin.php?page=wc-review-discount&amp;tab=email" class="nav-tab <?php echo ($tab == 'email') ? 'nav-tab-active' : ''; ?>"><?php _e('Email Settings', 'wc_review_discount'); ?></a>
    </h2>

    <?php if (isset($_GET['created'])): ?>
    <div id="message" class="updated"><p><?php _e('Discount created!', 'wc_review_discount'); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div id="message" class="error"><p><?php echo esc_html($_GET['error']); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
    <div id="message" class="updated"><p><?php _e('Discount deleted!', 'wc_review_discount'); ?></p></div>
    <?php endif; ?>

    <form action="admin-post.php" method="post">
        <input type="hidden" name="action" value="sfn_rd_update_settings" />

        <h3><?php _e('Discounts', 'wc_review_discount'); ?></h3>
        <p><?php _e("Below are the active discounts for product reviews. Click the \"Type\" to see/edit an existing discount's details. When a user submits a review, a new, unique discount code is automatically generated, and emailed to the user. The email settings can be updated <a href=\"admin.php?page=wc-review-discount&tab=email\">here</a>, and active/sent discount codes can be tracked <a href=\"edit.php?post_type=shop_coupon\">here</a>.", 'wc_review_discount'); ?></p>

        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th scope="col" id="type" class="manage-column column-type" style=""><?php _e('Type', 'wc_review_discount'); ?></th>
                    <th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Discount', 'wc_review_discount'); ?></th>
                    <th scope="col" id="products" class="manage-column column-products" style=""><?php _e('Product IDs', 'wc_review_discount'); ?></th>
                    <th scope="col" id="usage_count" class="manage-column column-usage_count" style=""><?php _e('Coupons Created', 'wc_review_discount'); ?></th>
                    <th scope="col" id="expiry_date" class="manage-column column-expiry_date" style=""><?php _e('Expiry', 'wc_review_discount'); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th scope="col" id="type" class="manage-column column-type" style=""><?php _e('Type', 'wc_review_discount'); ?></th>
                    <th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Discount', 'wc_review_discount'); ?></th>
                    <th scope="col" id="products" class="manage-column column-products" style=""><?php _e('Product IDs', 'wc_review_discount'); ?></th>
                    <th scope="col" id="usage_count" class="manage-column column-usage_count" style=""><?php _e('Coupons Created', 'wc_review_discount'); ?></th>
                    <th scope="col" id="expiry_date" class="manage-column column-expiry_date" style=""><?php _e('Expiry', 'wc_review_discount'); ?></th>
                </tr>
            </tfoot>
            <tbody id="the_list">
                <?php
                $types = (function_exists('wc_get_coupon_types')) ? wc_get_coupon_types() : $woocommerce->get_coupon_discount_types();

                if (! empty($discounts)):
                    foreach ($discounts as $discount):
                ?>
                <tr scope="row">
                    <td class="post-title column-title">
                        <strong><a class="row-title" href="admin.php?page=wc-review-discount&tab=edit&id=<?php echo $discount->id; ?>"><?php echo $types[$discount->type]; ?></a></strong>
                        <div class="row-actions">
                            <span class="edit"><a href="admin.php?page=wc-review-discount&tab=edit&id=<?php echo $discount->id; ?>"><?php _e('Edit', 'wc_review_discount'); ?></a></span>
                            |
                            <span class="trash"><a onclick="return confirm('Really delete this entry?');" href="admin.php?page=wc-review-discount&tab=delete&id=<?php echo $discount->id; ?>"><?php _e('Delete', 'wc_review_discount'); ?></a></span>
                        </div>
                    </td>
                    <td><?php echo $discount->amount; ?></td>
                    <td>
                    <?php if (empty($discount->product_ids)): ?>
                    -
                    <?php
                    else:
                        $prods = unserialize($discount->product_ids);
                        $pIds = '';
                        foreach ($prods as $pId) {
                            $pIds .= $pId .',';
                        }
                        $pIds = rtrim($pIds, ',');
                        echo $pIds;
                    endif;
                    ?>
                    </td>
                    <td>
                    <?php echo $discount->sent; ?>
                    </td>
                    <td>
                    <?php
                    if ($discount->expiry_value != 0) {
                        echo $discount->expiry_value .' '. $discount->expiry_type;
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr scope="row">
                    <th colspan="6"><?php _e('No discounts available', 'wc_review_discount'); ?></th>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>
