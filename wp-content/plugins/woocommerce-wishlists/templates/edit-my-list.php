<?php $wishlist = new WC_Wishlists_Wishlist( $_GET['wlid'] ); ?>

<?php
$current_owner_key = WC_Wishlists_User::get_wishlist_key();
$sharing           = $wishlist->get_wishlist_sharing();
$sharing_key       = $wishlist->get_wishlist_sharing_key();
$wl_owner          = $wishlist->get_wishlist_owner();

$notifications = get_post_meta( $wishlist->id, '_wishlist_owner_notifications', true );
if ( empty( $notifications ) ) {
	$notifications = 'yes';
}

$wishlist_items = WC_Wishlists_Wishlist_Item_Collection::get_items( $wishlist->id, true );

$treat_as_registry = false;
?>

<?php
if ( $wl_owner != WC_Wishlists_User::get_wishlist_key() && !current_user_can( 'manage_woocommerce' ) ) :

	die();

endif;
?>

<?php do_action( 'woocommerce_wishlists_before_wrapper' ); ?>
<div id="wl-wrapper" class="product woocommerce"> <!-- product class so woocommerce stuff gets applied in tabs -->

	<?php if ( function_exists( 'wc_print_messages' ) ) : ?>
		<?php wc_print_messages(); ?>
	<?php else : ?>
		<?php WC_Wishlist_Compatibility::wc_print_notices(); ?>
	<?php endif; ?>

    <div class="wl-intro">
        <h2 class="entry-title"><?php $wishlist->the_title(); ?></h2>
        <div class="wl-intro-desc">
			<?php $wishlist->the_content(); ?>
        </div>
		<?php if ( $sharing == 'Public' || $sharing == 'Shared' ) : ?>
            <div class="wl-share-url">
                <strong><?php _e( 'Wishlist URL:', 'wc_wishlist' ); ?> </strong><?php echo $wishlist->the_url_view( $sharing == 'Shared' ); ?>
            </div>
		<?php endif; ?>
		<?php if ( $sharing == 'Public' || $sharing == 'Shared' ) : ?>
			<?php if ( $wishlist_items && count( $wishlist_items ) ) : ?>
                <div class="wl-meta-share">
					<?php woocommerce_wishlists_get_template( 'wishlist-sharing-menu.php', array( 'id' => $wishlist->id ) ); ?>
                </div>
			<?php endif; ?>
		<?php endif; ?>

        <p>
            <a class="wlconfirm"
               data-message="<?php _e( 'Are you sure you want to delete this list?', 'wc_wishlist' ); ?>"
               href="<?php $wishlist->the_url_delete(); ?>"><?php _e( 'Delete list', 'wc_wishlist' ); ?></a>
			<?php if ( ( $sharing == 'Public' || $sharing == 'Shared' ) && count( $wishlist_items ) ) : ?>
                |
                <a rel="nofollow"
                   href="<?php $wishlist->the_url_view(); ?>&preview=true"><?php _e( 'Preview List', 'wc_wishlist' ); ?></a>
			<?php endif; ?>
        </p>
    </div>

    <div class="wl-tab-wrap woocommerce-tabs">

        <ul class="wl-tabs tabs">
            <li class="wl-items-tab"><a href="#tab-wl-items"><?php _e( 'List Items', 'wc_wishlist' ); ?></a></li>
            <li class="wl-settings-tab"><a href="#tab-wl-settings"><?php _e( 'Settings', 'wc_wishlist' ); ?></a>
            </li>
        </ul>

        <div class="wl-panel panel" id="tab-wl-items">
			<?php if ( sizeof( $wishlist_items ) > 0 ) : ?>
                <form action="<?php $wishlist->the_url_edit(); ?>" method="post" class="wl-form" id="wl-items-form">
                    <input type="hidden" name="wlid" value="<?php echo $wishlist->id; ?>"/>
					<?php WC_Wishlists_Plugin::nonce_field( 'manage-list' ); ?>
					<?php echo WC_Wishlists_Plugin::action_field( 'manage-list' ); ?>
                    <input type="hidden" name="wlmovetarget" id="wlmovetarget" value="0"/>

                    <div class="wl-row">
                        <table width="100%" cellpadding="0" cellspacing="0" class="wl-actions-table">
                            <tbody>
                            <tr>
                                <td>
                                    <select class="wl-sel move-list-sel" name="wlupdateaction" id="wleditaction1">
                                        <option selected="selected"><?php _e( 'Actions', 'wc_wishlist' ); ?></option>
                                        <option value="quantity"><?php _e( 'Update Quantities', 'wc_wishlist' ); ?></option>
										<?php if ( !class_exists( 'WC_Catalog_Visibility_Options' ) ): ?>
                                            <option value="add-to-cart"><?php _e( 'Add to Cart', 'wc_wishlist' ); ?></option>
                                            <option value="quantity-add-to-cart"><?php _e( 'Update Quantities and Add to Cart', 'wc_wishlist' ); ?></option>
										<?php endif; ?>
                                        <option value="remove"><?php _e( 'Remove from List', 'wc_wishlist' ); ?></option>
                                        <optgroup label="<?php _e( 'Move to another List', 'wc_wishlist' ); ?>">
											<?php $lists = WC_Wishlists_User::get_wishlists(); ?>
											<?php if ( $lists && count( $lists ) ) : ?>
												<?php foreach ( $lists as $list ) : ?>
													<?php if ( $list->id != $wishlist->id ) : ?>
                                                        <option value="<?php echo $list->id; ?>"><?php $list->the_title(); ?>
                                                            ( <?php echo $wishlist->get_wishlist_sharing( true ); ?> )
                                                        </option>
													<?php endif; ?>
												<?php endforeach; ?>
											<?php endif; ?>
                                            <option value="create"><?php _e( '+ Create A New List', 'wc_wishlist' ); ?></option>
                                        </optgroup>
                                    </select>
                                <td>
                                    <button class="button small wl-but wl-add-to btn-apply"><?php _e( 'Apply Action', 'wc_wishlist' ); ?></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div><!-- wl-row wl-clear -->

                    <table class="cart wl-table manage  shop_table shop_table_responsive" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="check-column"><input type="checkbox" name="" value=""/></th>
                            <th class="product-remove">&nbsp;</th>
                            <th class="product-thumbnail">&nbsp;</th>
                            <th class="product-name"><?php _e( 'Product', 'wc_wishlist' ); ?></th>
                            <th class="product-price"><?php _e( 'Price', 'wc_wishlist' ); ?></th>
                            <th class="product-quantity ctr"><?php _e( 'Qty', 'wc_wishlist' ); ?></th>
							<?php if ( WC_Wishlists_Settings::get_setting( 'wc_wishlist_show_previously_ordered', 'no' ) == 'yes' ): ?>
                                <th class="product-quantity ctr"><?php echo apply_filters( 'wc_wishlist_show_previously_ordered_column_heading', WC_Wishlists_Settings::get_setting( 'wc_wishlist_show_previously_ordered_column_heading', __( 'Ordered', 'wc_wishlist' ) ) ); ?></th>
							<?php endif; ?>
							<?php if ( ( apply_filters( 'woocommerce_wishlist_purchases_enabled', true, $wishlist ) ) ): ?>
                                <th></th>
							<?php endif; ?>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
						foreach ( $wishlist_items as $wishlist_item_key => $item ) {

							//$_product   = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $wishlist_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $item['product_id'], $item, $wishlist_item_key );
							$_product   = wc_get_product( $item['data'] );
							if ( $_product->exists() && $item['quantity'] > 0 ) {
								$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $item ) : '', $item, $wishlist_item_key );

								?>
                                <tr class="cart_table_item">
                                    <td class="check-column">
                                        <input type="checkbox" name="wlitem[]"
                                               value="<?php echo $wishlist_item_key; ?>"/>
                                    </td>
                                    <td class="product-remove">
                                        <a rel="nofollow"
                                           href="<?php echo woocommerce_wishlist_url_item_remove( $wishlist->id, $wishlist_item_key ); ?>"
                                           class="remove wlconfirm"
                                           title="<?php _e( 'Remove this item from your wishlist', 'wc_wishlist' ); ?>"
                                           data-message="<?php esc_attr( _e( 'Are you sure you would like to remove this item from your list?', 'wc_wishlist' ) ); ?>">&times;</a>
                                    </td>

                                    <!-- The thumbnail -->
                                    <td class="product-thumbnail">
										<?php
										$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $item, $wishlist_item_key );

										if ( !$product_permalink ) {
											echo $thumbnail;
										} else {
											printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
										}
										?>
                                    </td>

                                    <td class="product-name" data-title="<?php _e( 'Product', 'wc_wishlist' ); ?>">
										<?php
										if ( !$product_permalink ) {
											echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $item, $wishlist_item_key ) . '&nbsp;';
										} else {
											echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $item, $wishlist_item_key );
										}

										// Meta data
										if ( function_exists( 'wc_get_formatted_cart_item_data' ) ) {
											echo wc_get_formatted_cart_item_data( $item );
										} else {
											echo WC()->cart->get_item_data( $item );
										}


										// Availability
										$availability = $_product->get_availability();

										if ($availability && $availability['availability'] ) :
											echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>', $availability['availability'] );
										endif;
										?>

										<?php do_action( 'woocommerce_wishlist_after_list_item_name', $item, $wishlist ); ?>
                                    </td>

                                    <!-- Product price -->
                                    <td class="product-price" data-title="<?php _e( 'Price', 'wc_wishlist' ); ?>">
										<?php
										if ( WC_Wishlist_Compatibility::is_wc_version_gte_2_1() ) {
											$price = WC()->cart->get_product_price( $item['data'] );
											$price = apply_filters( 'woocommerce_cart_item_price', $price, $item, $wishlist_item_key );
										} else {
											$product_price = ( get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' ) ? wc_get_price_excluding_tax( $_product ) : $_product->get_price();
											$price         = apply_filters( 'woocommerce_cart_item_price_html', wc_price( $product_price ), $item, $wishlist_item_key );
										}
										?>

										<?php echo apply_filters( 'woocommerce_wishlist_list_item_price', $price, $item, $wishlist ); ?>
                                    </td>

                                    <!-- Quantity inputs -->
                                    <td class="product-quantity" data-title="<?php _e( 'Quantity', 'wc_wishlist' ); ?>">
										<?php
										if ( $_product->is_sold_individually() ) {
											$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" class="input-text qty text" value="1" />', $wishlist_item_key );
										} else {
											$product_quantity_value = apply_filters( 'woocommerce_wishlist_list_item_quantity_value', $item['quantity'], $item, $wishlist );

											$step = apply_filters( 'woocommerce_quantity_input_step', '1', $_product );
											$min  = apply_filters( 'woocommerce_quantity_input_min', '', $_product );
											$max  = apply_filters( 'woocommerce_quantity_input_max', $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(), $_product );

											$product_quantity = sprintf( '<div class="quantity"><input type="text" name="cart[%s][qty]" step="%s" min="%s" max="%s" value="%s" size="4" title="' . _x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) . '" class="input-text qty text" maxlength="12" /></div>', $wishlist_item_key, $step, $min, $max, esc_attr( $product_quantity_value ) );
										}
										?>

										<?php echo $product_quantity; ?>

                                    </td>

									<?php if ( WC_Wishlists_Settings::get_setting( 'wc_wishlist_show_previously_ordered', 'no' ) == 'yes' ): ?>
                                        <td class="product-quantity"
                                            data-title="<?php echo apply_filters( 'wc_wishlist_show_previously_ordered_column_heading', WC_Wishlists_Settings::get_setting( 'wc_wishlist_show_previously_ordered_column_heading', __( 'Ordered', 'wc_wishlist' ) ) ); ?>">
											<?php printf( '<div class="quantity"><input class="input-text qty text" maxlength="12" type="text" name="cart[%s][ordered_qty]" value="' . ( isset( $item['ordered_total'] ) ? intval( $item['ordered_total'] ) : 0 ) . '" /></div>', $wishlist_item_key ); ?>
                                        </td>
									<?php endif; ?>

                                    <td class="product-purchase">
										<?php if ( apply_filters( 'woocommerce_wishlist_user_can_purcahse', true, $_product ) ): ?>
											<?php if ( $_product->get_type() != 'external' && $_product->is_in_stock() ) : ?>
                                                <a rel="nofollow"
                                                   href="<?php echo woocommerce_wishlist_url_item_add_to_cart( $wishlist->id, $wishlist_item_key, $wishlist->get_wishlist_sharing() == 'Shared' ? $wishlist->get_wishlist_sharing_key() : false, 'edit' ); ?>"
                                                   class="wishlist-add-to-cart-button button alt"><?php _e( 'Add to Cart', 'wc_wishlist' ); ?></a>
											<?php elseif ( $_product->get_type() == 'external' ) : ?>
                                                <a rel="nofollow"
                                                   href="<?php echo esc_url( $_product->add_to_cart_url() ); ?>"
                                                   rel="nofollow"
                                                   class="single_add_to_cart_button button alt"><?php echo esc_html( $_product->single_add_to_cart_text() ); ?></a>
											<?php endif; ?>
										<?php endif; ?>
                                    </td>
                                </tr>
								<?php
							}
						}
						?>

                        <tr>

                            <td class="check-column"></td>
                            <td class="product-remove">&nbsp;</td>
                            <td class="product-thumbnail">&nbsp;</td>
                            <td class="product-name"></td>
                            <td class="product-price"></td>
                            <td class="product-quantity ctr"></td>
	                        <?php if ( WC_Wishlists_Settings::get_setting( 'wc_wishlist_show_previously_ordered', 'no' ) == 'yes' ): ?>
                                <td class="product-quantity ctr"></td>
	                        <?php endif; ?>
	                        <?php if ( ( apply_filters( 'woocommerce_wishlist_purchases_enabled', true, $wishlist ) ) ): ?>
                                    <td class="product-purchase">
                                        <a rel="nofollow"
                                           href="<?php echo woocommerce_wishlist_url_add_all_to_cart( $wishlist->id, $wishlist->get_wishlist_sharing() == 'Shared' ? $wishlist->get_wishlist_sharing_key() : false ); ?>"
                                           class="button alt wl-add-all"><?php _e( 'Add All To Cart', 'wc_wishlist' ); ?></a>
                                    </td>
	                        <?php endif; ?>
                        </tr>

                        </tbody>
                    </table>
                    <div class="wl-row">
                        <table width="100%" cellpadding="0" cellspacing="0" class="wl-actions-table">
                            <tbody>
                            <tr>
                                <td>
                                    <select class="wl-sel move-list-sel" name="wleditaction2" id="wleditaction2">
                                        <option selected="selected"><?php _e( 'Actions', 'wc_wishlist' ); ?></option>
                                        <option value="quantity"><?php _e( 'Update Quantities', 'wc_wishlist' ); ?></option>
										<?php if ( !class_exists( 'WC_Catalog_Visibility_Options' ) ): ?>
                                            <option value="add-to-cart"><?php _e( 'Add to Cart', 'wc_wishlist' ); ?></option>
                                            <option value="quantity-add-to-cart"><?php _e( 'Update Quantities and Add to Cart', 'wc_wishlist' ); ?></option>
										<?php endif; ?>
                                        <option value="remove"><?php _e( 'Remove from List', 'wc_wishlist' ); ?></option>
                                        <optgroup label="<?php _e( 'Move to another list', 'wc_wishlist' ); ?>">
											<?php $lists = WC_Wishlists_User::get_wishlists(); ?>
											<?php if ( $lists && count( $lists ) ) : ?>
												<?php foreach ( $lists as $list ) : ?>
													<?php if ( $list->id != $wishlist->id ) : ?>
                                                        <option value="<?php echo $list->id; ?>"><?php $list->the_title(); ?>
                                                            ( <?php echo $wishlist->get_wishlist_sharing( true ); ?> )
                                                        </option>
													<?php endif; ?>
												<?php endforeach; ?>
											<?php endif; ?>
                                            <option value="create"><?php _e( '+ Create A New List', 'wc_wishlist' ); ?></option>
                                        </optgroup>
                                    </select>
                                </td>
                                <td>
                                    <button class="button small wl-but wl-add-to btn-apply"><?php _e( 'Apply Action', 'wc_wishlist' ); ?></button>
                                </td>
                            </tr>



                            </tbody>
                        </table>

                        <div class="wl-clear"></div>
                    </div><!-- wl-row wl-clear -->
                </form>

			<?php else : ?>
				<?php $shop_url = get_permalink( wc_get_page_id( 'shop' ) ); ?>
				<?php _e( 'You do not have anything in this list.', 'wc_wishlist' ); ?>
                <a href="<?php echo $shop_url; ?>"><?php _e( 'Go Shopping', 'wc_wishlist' ); ?></a>.

			<?php endif; ?>


        </div><!-- /tab-wl-items -->

        <div class="wl-panel panel" id="tab-wl-settings">
            <div class="wl-form">
                <form action="" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="wlid" value="<?php echo $wishlist->id; ?>"/>
					<?php echo WC_Wishlists_Plugin::action_field( 'edit-list' ); ?>
					<?php echo WC_Wishlists_Plugin::nonce_field( 'edit-list' ); ?>
                    <p class="form-row form-row-wide">
                        <label for="wishlist_title"><?php _e( 'Name your list', 'wc_wishlist' ); ?>
                            <abbr class="required" title="required">*</abbr></label>
                        <input required type="text" name="wishlist_title" id="wishlist_title" class="input-text"
                               value="<?php echo esc_attr( $wishlist->post->post_title ); ?>"/>
                    </p>
                    <p class="form-row form-row-wide">
                        <label for="wishlist_description"><?php _e( 'Describe your list', 'wc_wishlist' ); ?></label>
                        <textarea name="wishlist_description"
                                  id="wishlist_description"><?php echo esc_textarea( $wishlist->post->post_content ); ?></textarea>
                    </p>
                    <hr/>
                    <div class="form-row">
                        <strong><?php _e( 'Privacy Settings', 'wc_wishlist' ); ?>
                            <abbr class="required" title="required">*</abbr></strong>
                        <table class="wl-rad-table">
							<?php if ( apply_filters( 'wc_wishlist_allow_public_lists', true ) ): ?>
                                <tr>
                                    <td>
                                        <input type="radio" name="wishlist_sharing" id="rad_pub"
                                               value="Public" <?php checked( 'Public', $sharing ); ?>>
                                    </td>
                                    <td><label for="rad_pub"><?php _e( 'Public', 'wc_wishlist' ); ?>
                                            <span class="wl-small">- <?php _e( 'Anyone can search for and see this list. You can also share using a link.', 'wc_wishlist' ); ?></span></label>
                                    </td>
                                </tr>
							<?php endif; ?>
							<?php if ( apply_filters( 'wc_wishlist_allow_shared_lists', true ) ): ?>
                                <tr>
                                    <td>
                                        <input type="radio" name="wishlist_sharing" id="rad_shared"
                                               value="Shared" <?php checked( 'Shared', $sharing ); ?>>
                                    </td>
                                    <td><label for="rad_shared"><?php _e( 'Shared', 'wc_wishlist' ); ?>
                                            <span class="wl-small">- <?php _e( 'Only people with the link can see this list. It will not appear in public search results.', 'wc_wishlist' ); ?></span></label>
                                    </td>
                                </tr>
							<?php endif; ?>
							<?php if ( apply_filters( 'wc_wishlist_allow_private_lists', true ) ): ?>
                                <tr>
                                    <td>
                                        <input type="radio" name="wishlist_sharing" id="rad_priv"
                                               value="Private" <?php checked( 'Private', $sharing ); ?>>
                                    </td>
                                    <td><label for="rad_priv"><?php _e( 'Private', 'wc_wishlist' ); ?>
                                            <span class="wl-small">- <?php _e( 'Only you can see this list.', 'wc_wishlist' ); ?></span></label>
                                    </td>
                                </tr>
							<?php endif; ?>
                        </table>
                    </div>
                    <p class="form-row"><?php _e( 'Enter a name you would like associated with this list.  If your list is public, users can find it by searching for this name.', 'wc_wishlist' ); ?></p>

                    <p class="form-row form-row-first">
                        <label for="wishlist_first_name"><?php _e( 'First Name', 'wc_wishlist' ); ?></label>
                        <input type="text" name="wishlist_first_name" id="wishlist_first_name"
                               value="<?php echo esc_attr( get_post_meta( $wishlist->id, '_wishlist_first_name', true ) ); ?>"
                               class="input-text"/>
                    </p>

                    <p class="form-row form-row-last">
                        <label for="wishlist_last_name"><?php _e( 'Last Name', 'wc_wishlist' ); ?></label>
                        <input type="text" name="wishlist_last_name" id="wishlist_last_name"
                               value="<?php echo esc_attr( get_post_meta( $wishlist->id, '_wishlist_last_name', true ) ); ?>"
                               class="input-text"/>
                    </p>

                    <div class="wl-clear"></div>
                    <p class="form-row">
                        <label for="wishlist_owner_email"><?php _e( 'Email Associated with the List', 'wc_wishlist' ); ?></label>
                        <input type="text" name="wishlist_owner_email" id="wishlist_owner_email"
                               value="<?php echo esc_attr( get_post_meta( $wishlist->id, '_wishlist_email', true ) ); ?>"
                               class="input-text"/>
                    </p>

					<?php if ( WC_Wishlists_Settings::get_setting( 'wc_wishlist_notifications_enabled', 'disabled' ) == 'enabled' ): ?>
                        <div class="wl-clear"></div>
                        <p class="form-row"><?php _e( 'Email Notifications', 'wc_wishlist' ); ?></p>
                        <div class="form-row">
                            <table class="wl-rad-table">
                                <tr>
                                    <td>
                                        <input type="radio" id="rad_notification_yes"
                                               name="wishlist_owner_notifications"
                                               value="yes" <?php checked( 'yes', $notifications ); ?>>
                                    </td>
                                    <td><label for="rad_notification_yes"><?php _e( 'Yes', 'wc_wishlist' ); ?>
                                            <span class="wl-small">- <?php _e( 'Send me an email if a price reduction occurs.', 'wc_wishlist' ); ?></span></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="radio" id="rad_notification_no" name="wishlist_owner_notifications"
                                               value="no" <?php checked( 'no', $notifications ); ?>>
                                    </td>
                                    <td><label for="rad_notification_no"><?php _e( 'No', 'wc_wishlist' ); ?>
                                            <span class="wl-small">- <?php _e( 'Do not send me an email if a price reduction occurs.', 'wc_wishlist' ); ?></span></label>
                                    </td>
                                </tr>
                            </table>
                        </div>
					<?php endif; ?>

                    <div class="wl-clear"></div>

                    <p class="form-row">
                        <input type="submit" class="button alt" name="update_wishlist"
                               value="<?php _e( 'Save Changes', 'wc_wishlist' ); ?>">
                    </p>
                </form>
                <div class="wl-clear"></div>
            </div><!-- /wl form -->

        </div><!-- /tab-wl-settings panel -->
    </div><!-- /wishlist-wrapper -->

	<?php woocommerce_wishlists_get_template( 'wishlist-email-form.php', array( 'wishlist' => $wishlist ) ); ?>
</div>

<?php do_action( 'woocommerce_wishlists_after_wrapper' ); ?>
