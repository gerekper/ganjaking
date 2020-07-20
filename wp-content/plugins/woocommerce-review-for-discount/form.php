<div class="wrap woocommerce">
	<div id="icon-edit-comments" class="icon32"><br></div>
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="admin.php?page=wc-review-discount&amp;tab=discounts" class="nav-tab <?php echo ($tab == 'discounts') ? 'nav-tab-active' : ''; ?>"><?php _e('Discounts', 'wc_review_discount'); ?></a>
		<a href="admin.php?page=wc-review-discount&amp;tab=new" class="nav-tab <?php echo ($tab == 'new') ? 'nav-tab-active' : ''; ?>"><?php _e('New Discount', 'wc_review_discount'); ?></a>
		<a href="admin.php?page=wc-review-discount&amp;tab=email" class="nav-tab <?php echo ($tab == 'email') ? 'nav-tab-active' : ''; ?>"><?php _e('Email Settings', 'wc_review_discount'); ?></a>
	</h2>

	<style type="text/css">
	.chosen-container-multi .chosen-choices li.search-field input[type=text] {height: auto;}
	</style>
	<form action="admin-post.php" method="post">

		<?php if ( $discount['id'] == 0 ): ?>
		<h3><?php _e('Create a New Discount', 'wc_review_discount'); ?></h3>
		<p><?php _e("Create a new discount for a product review. The settings for a discount are similar to a standard <a href=\"edit.php?post_type=shop_coupon\">coupon</a>, but are limited to a single use and are only enabled for successful reviews of your products.", 'wc_review_discount'); ?></p>
		<?php else: ?>
		<h3><?php _e('Edit Discount', 'wc_review_discount'); ?></h3>
		<p><?php _e('The settings for a discount are similar to a standard <a href="edit.php?post_type=shop_coupon">coupon</a>, but are limited to a single use and are only enabled for successful reviews of your products. You can edit the discount below.', 'wc_review_discount'); ?></p>
		<?php endif; ?>

		<div id="poststuff">
			<div id="post-body">
				<div class="postbox-container" id="postbox-container-2" style="float:none;">
					<div id="normal-sortables">
						<div id="woocommerce-coupon-data" class="postbox">
							<div class="handlediv"><br/></div>
							<h3 class="hndle"><span><?php _e('Coupon Data', 'wc_review_discount'); ?></span></h3>
							<div class="inside">
								<div id="coupon_options" class="panel-wrap coupon_data" style="padding-top: 0px;">

									<div class="wc-tabs-back"></div>

									<ul class="coupon_data_tabs wc-tabs" style="display: none;">
										<?php
										$coupon_data_tabs = apply_filters( 'wrd_coupon_data_tabs', array(
											'general' => array(
												'label'  => __( 'General', 'woocommerce' ),
												'target' => 'general_coupon_data',
												'class'  => 'general_coupon_data',
											),
											'usage_restriction' => array(
												'label'  => __( 'Usage Restriction', 'woocommerce' ),
												'target' => 'usage_restriction_coupon_data',
												'class'  => '',
											)
										) );

										foreach ( $coupon_data_tabs as $key => $tab ) {
											?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , (array) $tab['class'] ); ?>">
											<a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
											</li><?php
										}
										?>
									</ul>
									<div id="general_coupon_data" class="panel woocommerce_options_panel">
										<div class="options_group">
											<p class="form-field">
												<label for="type"><?php _e('Discount type', 'wc_review_discount'); ?></label>
												<select id="type" name="type">
													<?php
													$types = (function_exists('wc_get_coupon_types')) ? wc_get_coupon_types() : $woocommerce->get_coupon_discount_types();

													foreach ($types as $key => $type) {
														$selected = ($discount['type'] == $key) ? 'selected' : '';
														echo '<option value="'. $key .'" '. $selected .'>'. $type .'</option>';
													}
													?>
												</select>
											</p>
											<p class="form-field">
												<label for="amount"><?php _e('Coupon Amount', 'wc_review_discount'); ?></label>
												<input type="text" name="amount" id="amount" class="short" value="<?php echo esc_attr($discount['amount']); ?>" placeholder="0.0" />
												<img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('e.g. 5.99 (do not include the percent symbol)', 'follow_up_emails'); ?>">
											</p>
											<p class="form-field">
												<label for="free_shipping"><?php _e('Enable free shipping', 'wc_review_discount'); ?></label>
												<input type="checkbox" class="checkbox" name="free_shipping" id="free_shipping" value="yes" <?php if ($discount['free_shipping'] != 0) echo 'checked'; ?> />
												<span class="description"><?php _e('Check this box if the coupon grants free shipping. The <a href="admin.php?page=wc-settings&tab=shipping&section=WC_Shipping_Free_Shipping">free shipping method</a> must be enabled and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'wc_review_discount'); ?></span>
											</p>
										</div>
										<div class="options_group">
											<p class="form-field">
												<label for="sending_mode"><?php _e('When do we send the discount code', 'wc_review_discount'); ?></label>
												<select name="sending_mode" id="sending_mode">
													<option value="immediately" <?php if ($discount['send_mode'] == 'immediately') echo 'selected'; ?>><?php _e('Immediately after posting the review', 'wc_review_discount'); ?></option>
													<option value="approved" <?php if ($discount['send_mode'] == 'approved') echo 'selected'; ?>><?php _e('Only after review has been approved', 'wc_review_discount'); ?></option>
												</select>
											</p>

											<?php if (function_exists('woocommerce_customer_bought_product')): ?>
												<p class="form-field">
													<label for="verified"><?php _e('Only send to verified owners', 'wc_review_discount'); ?></label>
													<select name="send_to_verified" id="verified">
														<option value="0" <?php if ($discount['verified'] == 0) echo 'selected'; ?>><?php _e('No', 'wc_review_discount'); ?></option>
														<option value="1" <?php if ($discount['verified'] == 1) echo 'selected'; ?>><?php _e('Yes', 'wc_review_discount'); ?></option>
													</select>
												</p>
											<?php endif; ?>

											<p class="form-field">
												<label for="expiry"><?php _e('Expiry', 'wc_review_discount'); ?></label>
												<select name="expiry_value">
													<option value="" <?php selected( 0, $discount['expiry_value'] ); ?>><?php _e('Does not expire', 'wc_review_discount'); ?></option>
													<?php for ($x = 1; $x <= 30; $x++): ?>
														<option value="<?php echo $x; ?>" <?php selected( $x, $discount['expiry_value'] ); ?>><?php echo $x; ?></option>
													<?php endfor; ?>
												</select>
												<select name="expiry_type">
													<option value="" <?php selected( '', $discount['expiry_type'] ); ?>>-</option>
													<option value="days" <?php selected( 'days', $discount['expiry_type'] ); ?>><?php _e('days', 'wc_review_discount'); ?></option>
													<option value="weeks" <?php selected( 'weeks', $discount['expiry_type'] ); ?>><?php _e('weeks', 'wc_review_discount'); ?></option>
													<option value="months" <?php selected( 'months', $discount['expiry_type'] ); ?>><?php _e('months', 'wc_review_discount'); ?></option>
												</select>
												<img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('after the discount has been sent to the user', 'wc_review_discount'); ?>">
											</p>
										</div>
									</div>
									<div id="usage_restriction_coupon_data" class="panel woocommerce_options_panel">
										<div class="options_group">
											<p class="form-field">
												<label for="individual"><?php _e('Individual use', 'wc_review_discount'); ?></label>
												<input type="checkbox" class="checkbox" name="individual_use" id="individual" value="yes" <?php checked( 1, $discount['individual'] ); ?> />
												<span class="description"><?php _e('Check this box if the coupon cannot be used in conjunction with other coupons', 'wc_review_discount'); ?></span>
											</p>

											<p class="form-field">
												<label for="unique_email"><?php _e('One coupon per email', 'wc_review_discount'); ?></label>
												<input type="checkbox" class="checkbox" value="1" name="unique_email" id="unique_email" <?php checked( 1, $discount['unique_email'] ); ?> />
												<span class="description"><?php _e('Limit the sending of coupons to one per email address.', 'wc_review_discount'); ?></span>
											</p>

											<p class="form-field">
												<label for="exclude_sale_items"><?php _e('Do not apply to sale items', 'wc_review_discount'); ?></label>
												<input type="checkbox" class="checkbox" name="exclude_sale_items" id="exclude_sale_items" value="yes" <?php checked( 'yes', $discount['exclude_sale_items'] ); ?> />
												<span class="description"><?php _e('Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'follow_up_emails'); ?></span>
											</p>
										</div>

										<div class="options_group">
											<p class="form-field">
												<label for="all_products"><?php _e('Apply to all products', 'wc_review_discount'); ?></label>
												<input type="checkbox" class="checkbox" name="all_products" id="all_products" value="yes" <?php checked( 'yes', $discount['all_products'] ); ?> />
											</p>

											<div class="hide-if-all-products">
												<p>
													<strong><?php _e('Select the products/categories that a user can submit a review for, and be rewarded with a discount code. This allows you to limit the reward of discounts to drive reviews of specific products/categories, or to have different discounts for reviews of different products/categories.', 'wc_review_discount'); ?></strong>
												</p>

												<p class="form-field">
													<label for="product_ids"><?php esc_html_e( 'Products', 'wc_review_discount' ); ?></label>
													<select
														class="sfn-product-search"
														id="product_ids"
														name="product_ids[]"
														multiple="multiple"
														data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wc_review_discount' ); ?>"
														style="width: 100%;"
													>
													<?php
														$product_ids = array_filter( array_map( 'absint', $discount['products'] ) );

														foreach ( $product_ids as $product_id ) :
															$product      = wc_get_product( $product_id );
															$product_name = $product ? htmlspecialchars( wp_kses_post( $product->get_formatted_name() ) ) : '';
													?>
														<option value="<?php echo esc_attr( $product_id ); ?>" selected="selected"><?php echo esc_html( $product_name ); ?></option>
													<?php endforeach; ?>
													</select>
												</p>

												<p class="form-field">
													<label for="product_cats"><?php esc_html_e( 'Product categories', 'wc_review_discount' ); ?></label>
													<select id="product_cats" name="product_cats[]" class="multiple-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'wc_review_discount' ); ?>" style="width:100%;">
														<?php foreach ( $cats as $category ) : ?>
															<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( true, in_array( $category->term_id, $discount['categories'] ) ); ?>><?php echo esc_html( $category->name ); ?></option>
														<?php endforeach; ?>
													</select>
												</p>

												<p class="form-field">
													<label for="limit"><?php _e('Limit coupon validity', 'wc_review_discount'); ?></label>
													<input type="checkbox" class="checkbox" value="1" name="limit" id="limit" <?php if ($discount['limit'] != 0) echo 'checked'; ?> />
													<span class="description"><?php _e('Checking this box will also limit the usage of the coupon to the products/categories defined above.', 'wc_review_discount'); ?></span>
												</p>
											</div>

										</div>

									</div>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					<div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>
				</div>
				<div class="clear"></div>
			</div> <!-- /post-body -->
			<br class="clear">
		</div>

		<p class="submit">
			<?php if ($discount['id'] == 0): ?>
			<input type="hidden" name="action" value="sfn_rd_new" />
			<input type="submit" name="save" value="<?php _e('Create Discount', 'wc_review_discount'); ?>" class="button-primary" />
			<?php else: ?>
			<input type="hidden" name="id" value="<?php echo $discount['id']; ?>" />
			<input type="hidden" name="action" value="sfn_rd_edit" />
			<input type="submit" name="save" value="<?php _e('Update Discount', 'wc_review_discount'); ?>" class="button-primary" />
			<?php endif; ?>
		</p>
	</form>
	<script type="text/javascript">
		(function($) {
			// TABS
			$('ul.coupon_data_tabs').show();
			$('div.panel-wrap').each(function(){
				$(this).find('div.panel:not(:first)').hide();
			});
			$('#coupon_options').on("click", "ul.coupon_data_tabs a", function(){
				var panel_wrap =  $(this).closest('div.panel-wrap');
				$('ul.coupon_data_tabs li', panel_wrap).removeClass('active');
				$(this).parent().addClass('active');
				$('div.panel', panel_wrap).hide();
				$( $(this).attr('href') ).show();
				return false;
			});
			$('ul.coupon_data_tabs li:visible').eq(0).find('a').click();

			$("#all_products").change(function() {
				if ($(this).is(":checked")) {
					$(".hide-if-all-products").hide();
				} else {
					$(".hide-if-all-products").show();
				}
			}).change();
		})(jQuery);
	</script>
</div>
