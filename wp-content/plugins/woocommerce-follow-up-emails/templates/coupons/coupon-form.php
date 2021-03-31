<?php
$categories = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );
$coupon_id  = isset($_GET['id']) ? absint( $_GET['id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
$data       = FUE_Coupons::get_coupon_data( $coupon_id );

if ( $coupon_id > 0 )
	$action = 'update';
else
	$action = 'create';

?>
<div class="wrap woocommerce">
<div class="icon32"><img src="<?php echo esc_url( FUE_TEMPLATES_URL ) .'/images/send_mail.png'; ?>" /></div>
	<h2>
		<?php
		if ( $action == 'create' ) {
			esc_html_e('Create a New Coupon', 'follow_up_emails');
		} else {
			esc_html_e('Update Coupon', 'follow_up_emails');
		}
		?>
	</h2>
	<form action="admin-post.php" method="post">

		<div id="poststuff">
			<div id="post-body">
				<div class="postbox-container" id="postbox-container-2" style="float:none;">
					<div id="normal-sortables">
						<div id="woocommerce-coupon-data" class="postbox">
							<div class="handlediv"><br/></div>
							<h3 class="hndle"><span><?php esc_html_e( 'Coupon Data', 'follow_up_emails'); ?></span></h3>
							<div class="inside">
								<div id="coupon_options" class="panel-wrap coupon_data" style="padding-top: 0px;">

									<div class="wc-tabs-back"></div>

									<ul class="coupon_data_tabs wc-tabs" style="display: none;">
										<?php
										$coupon_data_tabs = apply_filters( 'woocommerce_coupon_data_tabs', array(
											'general' => array(
												'label'  => __( 'General', 'follow_up_emails' ),
												'target' => 'general_coupon_data',
												'class'  => 'general_coupon_data',
											),
											'usage_restriction' => array(
												'label'  => __( 'Usage Restriction', 'follow_up_emails' ),
												'target' => 'usage_restriction_coupon_data',
												'class'  => '',
											),
											'usage_limit' => array(
												'label'  => __( 'Usage Limits', 'follow_up_emails' ),
												'target' => 'usage_limit_coupon_data',
												'class'  => '',
											)
										) );

										foreach ( $coupon_data_tabs as $key => $tab ) {
											?><li class="<?php echo esc_attr( $key ); ?>_options <?php echo esc_attr( $key ); ?>_tab <?php echo esc_attr( implode( ' ' , (array) $tab['class'] ) ); ?>">
											<a href="#<?php echo esc_attr( $tab['target'] ); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
											</li><?php
										}
										?>
									</ul>
									<div id="general_coupon_data" class="panel woocommerce_options_panel">
										<div class="options_group">
											<p class="form-field">
												<label for="name"><?php esc_html_e('Name', 'follow_up_emails'); ?></label>
												<input type="text" name="name" id="name" value="<?php echo esc_attr($data['name']); ?>" class="short" />
												<span class="description"><?php esc_html_e('For internal use only', 'follow_up_emails'); ?></span>
											</p>
											<p class="form-field">
												<label for="prefix"><?php esc_html_e('Coupon Prefix', 'follow_up_emails'); ?></label>
												<input type="text" name="prefix" id="prefix" value="<?php echo esc_attr($data['prefix']); ?>" class="input-text sized" size="45" />
												<select id="prefixes">
													<option value=""><?php esc_html_e('Choose a Variable', 'follow_up_emails'); ?></option>
													<option value="{customer_first_name}"><?php esc_html_e('Customer\'s First Name', 'follow_up_emails'); ?></option>
													<option value="{customer_last_name}"><?php esc_html_e('Customer\'s Last Name', 'follow_up_emails'); ?></option>
												</select>
												<span class="description"><?php esc_html_e('Add a prefix to the generated coupon code', 'follow_up_emails'); ?></span>
											</p>
											<p class="form-field">
												<label for="type"><?php esc_html_e('Discount type', 'follow_up_emails'); ?></label>
												<select id="type" name="type">
													<?php
													$types = self::get_discount_types();

													foreach ($types as $key => $type) {
														echo '<option value="'. esc_attr( $key ) .'" '. selected($data['type'], $key, false) .'>'. esc_html( $type ) .'</option>';
													}
													?>
												</select>
											</p>
											<p class="form-field">
												<label for="amount"><?php esc_html_e('Coupon Amount', 'follow_up_emails'); ?></label>
												<input type="text" name="amount" id="amount" class="short" value="<?php echo esc_attr($data['amount']); ?>" placeholder="0.0" />
												<span class="description"><?php esc_html_e('e.g. 5.99 (do not include the percent symbol)', 'follow_up_emails'); ?></span>
											</p>
											<p class="form-field">
												<label for="free_shipping"><?php esc_html_e('Allow free shipping', 'follow_up_emails'); ?></label>
												<input type="checkbox" class="checkbox" name="free_shipping" id="free_shipping" value="yes" <?php if ($data['free_shipping'] != 0) echo 'checked'; ?> />
												<span class="description"><?php echo wp_kses_post( sprintf( __('Check this box if the coupon grants free shipping. The <a href="%s"> free shipping method</a> must be enabled and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'follow_up_emails'), 'admin.php?page=wc-settings&tab=shipping&section=WC_Shipping_Free_Shipping' )); ?></span>
											</p>
										</div>
										<div class="options_group">
											<p class="form-field">
												<label for="expiry"><?php esc_html_e('Expiry', 'follow_up_emails'); ?></label>
												<select name="expiry_value">
													<option value="" <?php if ($data['expiry_value'] == 0) echo 'selected'; ?>><?php esc_html_e('Does not expire', 'follow_up_emails'); ?></option>
													<?php for ($x = 1; $x <= 30; $x++): ?>
														<option value="<?php echo esc_attr( $x ); ?>" <?php if ($data['expiry_value'] == $x) echo 'selected'; ?>><?php echo esc_attr( $x ); ?></option>
													<?php endfor; ?>
												</select>
												<select name="expiry_type">
													<option value="" <?php if ($data['expiry_type'] == '') echo 'selected'; ?>>-</option>
													<option value="days" <?php if ($data['expiry_type'] == 'days') echo 'selected'; ?>><?php esc_html_e('days', 'follow_up_emails'); ?></option>
													<option value="weeks" <?php if ($data['expiry_type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e('weeks', 'follow_up_emails'); ?></option>
													<option value="months" <?php if ($data['expiry_type'] == 'months') echo 'selected'; ?>><?php esc_html_e('months', 'follow_up_emails'); ?></option>
												</select>
												<span class="description"><?php esc_html_e('after the discount has been sent to the user', 'follow_up_emails'); ?></span>
											</p>
										</div>
									</div>
									<div id="usage_restriction_coupon_data" class="panel woocommerce_options_panel">
										<div class="options_group">
											<p class="form-field">
												<label for="minimum_amount"><?php esc_html_e('Minimum spend', 'follow_up_emails'); ?></label>
												<input type="text" class="short" name="minimum_amount" id="minimum_amount" value="<?php echo esc_attr( $data['minimum_amount'] ); ?>" placeholder="<?php esc_attr_e('No minimum', 'follow_up_emails'); ?>">
												<span class="description"><?php esc_html_e('This field allows you to set the minimum subtotal needed to use the coupon.', 'follow_up_emails'); ?></span>
											</p>
											<p class="form-field">
												<label for="maximum_amount"><?php esc_html_e('Maximum spend', 'follow_up_emails'); ?></label>
												<input type="text" class="short" name="maximum_amount" id="maximum_amount" value="<?php echo esc_attr( $data['maximum_amount'] ); ?>" placeholder="<?php esc_attr_e('No maximum', 'follow_up_emails'); ?>">
												<span class="description"><?php esc_html_e('This field allows you to set the maximum subtotal allowed when using the coupon.', 'follow_up_emails'); ?></span>
											</p>
										</div>
										<div class="options_group">
											<p class="form-field">
												<label for="individual"><?php esc_html_e('Individual use', 'follow_up_emails'); ?></label>
												<input type="checkbox" class="checkbox" name="individual_use" id="individual" value="yes" <?php if ($data['individual'] != 0) echo 'checked'; ?> />
												<span class="description"><?php esc_html_e('Check this box if the coupon cannot be used in conjunction with other coupons', 'follow_up_emails'); ?></span>
											</p>

											<p class="form-field">
												<label for="exclude_sale_items"><?php esc_html_e('Exclude sale items', 'follow_up_emails'); ?></label>
												<input type="checkbox" value="yes" <?php if ($data['exclude_sale_items'] != 0) echo 'checked'; ?> id="exclude_sale_items" name="exclude_sale_items" style="" class="checkbox">
												<span class="description"><?php esc_html_e('Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'follow_up_emails'); ?></span>
											</p>
										</div>


										<div class="options_group">
											<p class="form-field">
												<label for="product_ids"><?php esc_html_e( 'Products', 'follow_up_emails' ); ?></label>
												<select
													id="product_ids"
													name="product_ids[]"
													class="ajax_select2_products_and_variations"
													multiple
													data-placeholder="<?php esc_attr_e( 'Search for products&hellip;', 'follow_up_emails' ); ?>"
												>
												<?php
													if ( ! is_array( $data['products'] ) ) {
														$data['products'] = explode( ',', $data['products'] );
													}
													$product_ids = array_filter( array_map( 'absint', $data['products'] ) );

													foreach ( $product_ids as $product_id ) {
														$product      = WC_FUE_Compatibility::wc_get_product( $product_id );
														$product_name = $product ? htmlspecialchars( wp_kses_post( $product->get_formatted_name() ) ) : '';
												?>
													<option value="<?php echo esc_attr( $product_id ); ?>" selected><?php echo esc_html( $product_name ); ?></option>
												<?php
													}
												?>
												</select>
												<span class="description"><?php esc_html_e( 'Products which need to be in the cart to use this coupon or, for &quot;Product Discounts&quot;, which products are discounted.', 'follow_up_emails' ); ?></span>
											</p>
											<p class="form-field">
												<label for="exclude_product_ids"><?php esc_html_e( 'Exclude Products', 'follow_up_emails' ); ?></label>
												<select
													id="exclude_product_ids"
													name="exclude_product_ids[]"
													class="ajax_select2_products_and_variations"
													multiple
													data-placeholder="<?php esc_attr_e( 'Search for products&hellip;', 'follow_up_emails' ); ?>"
												>
												<?php
													if ( ! is_array( $data['exclude_products'] ) ) {
														$data['exclude_products'] = explode( ',', $data['exclude_products'] );
													}
													$product_ids = array_filter( array_map( 'absint', $data['exclude_products'] ) );

													foreach ( $product_ids as $product_id ) {
														$product      = WC_FUE_Compatibility::wc_get_product( $product_id );
														$product_name = $product ? htmlspecialchars( wp_kses_post( $product->get_formatted_name() ) ) : '';
												?>
													<option value="<?php echo esc_attr( $product_id ); ?>" selected><?php echo esc_html( $product_name ); ?></option>
												<?php
													}
												?>
												</select>
												<span class="description"><?php esc_html_e( 'Products which must not be in the cart to use this coupon or, for &quot;Product Discounts&quot;, which products are not discounted.', 'follow_up_emails' ); ?></span>
											</p>
										</div>
										<div class="options_group">
											<p class="form-field">
												<label for="product_categories"><?php esc_html_e( 'Product Categories', 'follow_up_emails' ); ?></label>
												<select id="product_categories" name="product_categories[]" class="select2" multiple="multiple" data-placeholder="Any category" style="width: 100%">
													<?php
													foreach ($categories as $category) :
														$selected = ( ! in_array( $category->term_id, $data['categories'] ) ) ? '' : 'selected';
														?>
														<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $category->name ); ?></option>
													<?php endforeach; ?>
												</select>
												<span class="description"><?php esc_html_e( 'A product must be in this category for the coupon to remain valid or, for &quot;Product Discounts&quot;, products in these categories will be discounted.', 'follow_up_emails' ); ?></span>
											</p>
											<p class="form-field">
												<label for="exclude_product_categories"><?php esc_html_e( 'Exclude Categories', 'follow_up_emails' ); ?></label>
												<select id="exclude_product_categories" name="exclude_product_categories[]" class="select2" multiple="multiple" data-placeholder="No categories" style="width: 100%">
													<?php
													foreach ($categories as $category) :
														$selected = ( ! in_array( $category->term_id, $data['exclude_categories'] ) ) ? '' : 'selected';
														?>
														<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $category->name ); ?></option>
													<?php endforeach; ?>
												</select>
												<span class="description"><?php esc_html_e( 'Product must not be in this category for the coupon to remain valid or, for &quot;Product Discounts&quot;, products in these categories will not be discounted.', 'follow_up_emails' ); ?></span>
											</p>
										</div>
									</div>
									<div id="usage_limit_coupon_data" class="panel woocommerce_options_panel">
										<p class="form-field usage_limit_field">
											<label for="usage_limit"><?php esc_html_e('Usage limit per coupon', 'follow_up_emails'); ?></label>
											<input type="number" min="0" step="1" class="short" name="usage_limit" id="usage_limit" value="<?php echo esc_attr( $data['usage_limit'] ); ?>" placeholder="Unlimited usage">
											<span class="description"><?php esc_html_e('How many times this coupon can be used before it is void', 'follow_up_emails'); ?></span>
										</p>
										<p class="form-field usage_limit_per_user_field">
											<label for="usage_limit_per_user"><?php esc_html_e('Usage limit per user', 'follow_up_emails'); ?></label>
											<input type="number" min="0" step="1" placeholder="Unlimited usage" value="<?php echo esc_attr( $data['usage_limit_per_user'] ); ?>" id="usage_limit_per_user" name="usage_limit_per_user" style="" class="short">
											<span class="description"><?php esc_html_e('How many times this coupon can be used by an individual user. Uses billing email for guests, and user ID for logged in users.', 'follow_up_emails'); ?></span>
										</p>
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
			<input type="hidden" name="action" value="fue_save_coupon" />
			<?php wp_nonce_field( 'fue-save-coupon' ); ?>
			<?php if ( $action == 'create' ): ?>
				<input type="submit" name="save" value="<?php esc_attr_e('Create Coupon', 'follow_up_emails'); ?>" class="button-primary" />
			<?php else: ?>
				<input type="hidden" name="id" value="<?php echo esc_attr( $data['id'] ); ?>" />
				<input type="submit" name="save" value="<?php esc_attr_e('Update Coupon', 'follow_up_emails'); ?>" class="button-primary" />
			<?php endif; ?>
		</p>
	</form>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		// TABS
		$('ul.coupon_data_tabs').show();
		$('div.panel-wrap').each(function(){
			$(this).find('div.panel').slice(1).hide();
		});
		$( '#coupon_options' ).on( 'click', 'ul.coupon_data_tabs a', function() {
			var panel_wrap =  $(this).closest('div.panel-wrap');
			$('ul.coupon_data_tabs li', panel_wrap).removeClass('active');
			$(this).parent().addClass('active');
			$('div.panel', panel_wrap).hide();
			$( $(this).attr('href') ).show();
			return false;
		} );
		$( 'ul.coupon_data_tabs li:visible' ).eq( 0 ).find( 'a' ).trigger( 'click' );

		jQuery( '#prefixes' ).on( 'change', function() {
			jQuery("#prefix").val(jQuery(this).val());
		} );

		jQuery( ':input.ajax_select2_products_and_variations' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
				placeholder: jQuery( this ).data( 'placeholder' ),
				width:       '100%',
				minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : 3,
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url:         ajaxurl,
					dataType:    'json',
					quietMillis: 250,
					data: function( params ) {
						return {
							term:     params.term,
							action:   jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
							security: '<?php echo esc_js( wp_create_nonce( 'search-products' ) ); ?>',
						};
					},
					processResults: function( data ) {
						var products = [];
						if ( data ) {
							jQuery.each( data, function( id, text ) {
								products.push( { id: id, text: text } );
							} );
						}
						return { results: products };
					},
					cache: true
				},
			};

			jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
		} );

		jQuery(":input.select2").select2().addClass( 'enhanced' );
		jQuery(".tips, .help_tip").tipTip({
			'attribute' : 'title',
			'fadeIn' : 50,
			'fadeOut' : 50,
			'delay' : 200
		});
	});
</script>
