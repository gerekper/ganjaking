<div class="wrap woocommerce">
    <div id="icon-edit-comments" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="admin.php?page=wc-review-discount&amp;tab=discounts" class="nav-tab <?php echo ($tab == 'discounts') ? 'nav-tab-active' : ''; ?>"><?php _e('Discounts', 'wc_review_discount'); ?></a>
        <a href="admin.php?page=wc-review-discount&amp;tab=new" class="nav-tab <?php echo ($tab == 'new') ? 'nav-tab-active' : ''; ?>"><?php _e('New Discount', 'wc_review_discount'); ?></a>
        <a href="admin.php?page=wc-review-discount&amp;tab=email" class="nav-tab <?php echo ($tab == 'email') ? 'nav-tab-active' : ''; ?>"><?php _e('Email Settings', 'wc_review_discount'); ?></a>
    </h2>

    <form action="admin-post.php" method="post">

        <h3><?php _e('Email Contents', 'wc_review_discount'); ?></h3>
        <p><?php _e('Use the below form and variables to create the email template that will be sent to your users upon a successful completion and rewarding of their discount for a review. All emails will be sent from the email defined in your <a href="admin.php?page=wc-settings&tab=email">settings</a>.', 'wc_review_discount'); ?></p>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <td colspan="2">
                            <span class="description">
                                <?php _e('You may use the following variables:', 'wc_review_discount'); ?><br />
                                <ul>
                                    <li><b>{store_name}</b>: <?php _e("Current store's name", 'wc_review_discount'); ?> (<?php bloginfo('name'); ?>)</li>
                                    <li><b>{code}</b>: <?php _e('Auto-generated coupon code', 'wc_review_discount'); ?></li>
                                    <li><b>{discount_amount}</b>: <?php _e('Amount of the coupon (e.g. 15% or $15)', 'wc_review_discount'); ?></li>
                                    <li><b>{product_name}</b>: <?php _e('The name of the product that was reviewed', 'wc_review_discount'); ?></li>
                                    <li><b>{valid_products}</b>: <?php _e('List of products/categories where the coupon is valid', 'wc_review_discount'); ?></li>
                                </ul>
                            </span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="subject"><?php _e('Email subject', 'wc_review_discount'); ?></label>
                </th>
                <td>
                    <input type="text" name="subject" id="subject" value="<?php echo $emailSettings['subject']; ?>" class="regular-text" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="message"><?php _e('Email body', 'wc_review_discount'); ?></label>
                </th>
                <td>
                    <textarea name="message" id="message" rows="10" cols="80"><?php echo $emailSettings['message']; ?></textarea>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="hidden" name="action" value="sfn_rd_email" />
            <input type="submit" name="save" value="<?php _e('Save Email Settings', 'wc_review_discount'); ?>" class="button-primary" />
        </p>
    </form>
</div>