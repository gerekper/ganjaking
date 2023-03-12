<?php
$current_user = wp_get_current_user();
$lists        = WC_Wishlists_User::get_wishlists();

$current_user = wp_get_current_user();

$post_args = array();
foreach ( $_POST as $key => $value ) {
	$post_args[ $key ] = sanitize_text_field( $value );
}

$defaults = array(
	'wishlist_title'               => '',
	'wishlist_description'         => '',
	'wishlist_type'                => 'list',
	'wishlist_sharing'             => 'Private',
	'wishlist_status'              => is_user_logged_in() ? 'active' : 'temporary',
	'wishlist_owner_email'         => is_user_logged_in() ? $current_user->user_email : '',
	'wishlist_owner_notifications' => false,
	'wishlist_first_name'          => is_user_logged_in() ? $current_user->user_firstname : '',
	'wishlist_last_name'           => is_user_logged_in() ? $current_user->user_lastname : '',
);

$defaults = apply_filters('wc_wishlists_create_list_args', $defaults);
$args = wp_parse_args( $post_args, $defaults );

?>

<?php do_action( 'woocommerce_wishlists_before_wrapper' ); ?>
<div id="wl-wrapper" class="woocommerce">
	<?php if ( function_exists( 'wc_print_messages' ) ) : ?>
		<?php wc_print_messages(); ?>
	<?php else : ?>
		<?php WC_Wishlist_Compatibility::wc_print_notices(); ?>
	<?php endif; ?>
	<?php $max_list_count = apply_filters( 'wc_wishlists_max_user_list_count', '*' ); ?>
	<?php if ( $max_list_count === '*' || ( empty( $lists ) || count( $lists ) < $max_list_count ) ): ?>
        <div class="wl-form">
            <form action="" enctype="multipart/form-data" method="post">
                <input type="hidden" name="wl_return_to" value="<?php esc_attr_e( $_GET['wl_return_to'] ?? '' ); ?>"/>
				<?php echo WC_Wishlists_Plugin::action_field( 'create-list' ); ?>
				<?php echo WC_Wishlists_Plugin::nonce_field( 'create-list' ); ?>

                <p class="form-row form-row-wide">
                    <label for="wishlist_title"><?php _e( 'Name your list', 'wc_wishlist' ); ?>
                        <abbr class="required" title="required">*</abbr></label>
                    <input type="text" name="wishlist_title" id="wishlist_title" class="input-text"
                           value="<?php esc_attr_e( $args['wishlist_title'] ); ?>"/>
                </p>
                <p class="form-row form-row-wide">
                    <label for="wishlist_description"><?php _e( 'Describe your list', 'wc_wishlist' ); ?></label>
                    <textarea name="wishlist_description" id="wishlist_description"></textarea>
                </p>
                <hr/>
                <div class="form-row">
                    <strong><?php _e( 'Privacy Settings', 'wc_wishlist' ); ?>
                        <abbr class="required" title="required">*</abbr></strong>
                    <table class="wl-rad-table">
	                    <?php if ( apply_filters( 'wc_wishlist_allow_public_lists', true ) ):  ?>
                        <tr>
                            <td><input <?php checked($args['wishlist_sharing'] == 'Public'); ?> type="radio" name="wishlist_sharing" id="rad_pub" value="Public">
                            </td>
                            <td><label for="rad_pub"><?php _e( 'Public', 'wc_wishlist' ); ?>
                                    <span class="wl-small">- <?php _e( 'Anyone can search for and see this list. You can also share using a link.', 'wc_wishlist' ); ?></span></label>
                            </td>
                        </tr>
                        <?php endif; ?>
	                    <?php if ( apply_filters( 'wc_wishlist_allow_shared_lists', true ) ): ?>
                        <tr>
                            <td><input <?php checked($args['wishlist_sharing'] == 'Shared'); ?> type="radio" name="wishlist_sharing" id="rad_shared" value="Shared"></td>
                            <td><label for="rad_shared"><?php _e( 'Shared', 'wc_wishlist' ); ?>
                                    <span class="wl-small">- <?php _e( 'Only people with the link can see this list. It will not appear in public search results.', 'wc_wishlist' ); ?></span></label>
                            </td>
                        </tr>
                        <?php endif; ?>
	                    <?php if ( apply_filters( 'wc_wishlist_allow_private_lists', true ) ): ?>
                        <tr>
                            <td><input <?php checked($args['wishlist_sharing'] == 'Private'); ?> type="radio" name="wishlist_sharing" id="rad_priv" value="Private"></td>
                            <td><label for="rad_priv"><?php _e( 'Private', 'wc_wishlist' ); ?>
                                    <span class="wl-small">- <?php _e( 'Only you can see this list.', 'wc_wishlist' ); ?></span></label>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <p class="form-row form-row-full"><?php _e( 'Enter a name you would like associated with this list.  If your list is public, users can find it by searching for this name.', 'wc_wishlist' ); ?></p>
                <p class="form-row form-row-first">
                    <label for="wishlist_first_name"><?php _e( 'First Name', 'wc_wishlist' ); ?><?php do_action( 'woocommerce_wishlist_after_first_name_field_label' ); ?></label>
					<?php if ( is_user_logged_in() ) : ?>
                        <input type="text" name="wishlist_first_name" id="wishlist_first_name" class="input-text"
                               value="<?php echo empty( $args['wishlist_first_name'] ) ? esc_attr( $current_user->user_firstname ) : esc_attr( $args['wishlist_first_name'] ); ?>"/>
					<?php else : ?>
                        <input type="text" name="wishlist_first_name" id="wishlist_first_name" class="input-text"
                               value="<?php echo esc_attr( $args['wishlist_first_name'] ); ?>"/>
					<?php endif; ?>
                </p>
                <p class="form-row form-row-last">
                    <label for="wishlist_last_name"><?php _e( 'Last Name', 'wc_wishlist' ); ?><?php do_action( 'woocommerce_wishlist_after_last_name_field_label' ); ?></label>

					<?php if ( is_user_logged_in() ) : ?>
                        <input type="text" name="wishlist_last_name" id="wishlist_last_name" class="input-text"
                               value="<?php echo empty( $args['wishlist_last_name'] ) ? esc_attr( $current_user->user_lastname ) : esc_attr( $args['wishlist_last_name'] ); ?>"/>

					<?php else : ?>
                        <input type="text" name="wishlist_last_name" id="wishlist_last_name" class="input-text"
                               value="<?php echo esc_attr( $args['wishlist_last_name'] ); ?>"/>
					<?php endif; ?>
                </p>
                <div class="wl-clear"></div>
                <p class="form-row">
                    <label for="wishlist_owner_email"><?php _e( 'Email Associated with the List', 'wc_wishlist' ); ?><?php do_action( 'woocommerce_wishlist_after_email_field_label' ); ?></label>
                    <input type="text" name="wishlist_owner_email" id="wishlist_owner_email"
                           value="<?php echo( is_user_logged_in() ? $current_user->user_email : esc_attr( $args['wishlist_owner_email'] ) ); ?>"
                           class="input-text"/>
                </p>


				<?php if ( WC_Wishlists_Settings::get_setting( 'wc_wishlist_notifications_enabled', 'disabled' ) == 'enabled' ): ?>
                    <div class="wl-clear"></div>
                    <p class="form-row"><?php _e( 'Email Notifications', 'wc_wishlist' ); ?></p>
                    <div class="form-row">
                        <table class="wl-rad-table">
                            <tr>
                                <td>
                                    <input type="radio" id="rad_notification_yes" name="wishlist_owner_notifications"
                                           value="yes" <?php checked( true ); ?>>
                                </td>
                                <td><label for="rad_notification_yes"><?php _e( 'Yes', 'wc_wishlist' ); ?>
                                        <span class="wl-small">- <?php _e( 'Send me an email if a price reduction occurs.', 'wc_wishlist' ); ?></span></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="radio" id="rad_notification_no" name="wishlist_owner_notifications"
                                           value="no">
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
					<?php if ( function_exists( 'gglcptch_display' ) ) {
						echo gglcptch_display();
					}; ?>
                </p>

                <p class="form-row">
                    <input type="submit" class="button alt" name="update_wishlist"
                           value="<?php _e( 'Create List', 'wc_wishlist' ); ?>">
                </p>


            </form>
        </div><!-- /wl form -->
	<?php else: ?>

        <div class="woocommerce">
            <div class="woocommerce-error"
                 role="alert"><?php _e( 'Unable to add a new list.  Review your existing lists below.', 'wc_wishlist' ); ?></div>
        </div>


        <table class="shop_table cart wl-table wl-manage" cellspacing="0">
            <thead>
            <tr>
                <th class="product-name"><?php _e( 'List Name', 'wc_wishlist' ); ?></th>
                <th class="wl-date-added"><?php _e( 'Date Added', 'wc_wishlist' ); ?></th>
            </tr>
            </thead>
            <tbody>

			<?php foreach ( $lists as $list ) : ?>
				<?php
				$sharing = $list->get_wishlist_sharing();
				?>

                <tr class="cart_table_item">
                    <td class="product-name">
                        <strong><a href="<?php $list->the_url_edit(); ?>"><?php $list->the_title(); ?></a></strong>
                        <div class="row-actions">
									<span class="edit">
										<small><a href="<?php $list->the_url_edit(); ?>"><?php _e( 'Manage this list', 'wc_wishlist' ); ?></a></small>
									</span>
                            |
                            <span class="trash">
										<small><a class="ico-delete wlconfirm"
                                                  data-message="<?php _e( 'Are you sure you want to delete this list?', 'wc_wishlist' ); ?>"
                                                  href="<?php $list->the_url_delete(); ?>"><?php _e( 'Delete', 'wc_wishlist' ); ?></a></small>
									</span>
							<?php if ( $sharing == 'Public' || $sharing == 'Shared' ) : ?>
                                |
                                <span class="view">
											<small><a href="<?php $list->the_url_view(); ?>&preview=true"><?php _e( 'Preview', 'wc_wishlist' ); ?></a></small>
										</span>
							<?php endif; ?>
                        </div>
						<?php if ( $sharing == 'Public' || $sharing == 'Shared' ) : ?>
							<?php woocommerce_wishlists_get_template( 'wishlist-sharing-menu.php', array( 'id' => $list->id ) ); ?>
						<?php endif; ?>
                    </td>
                    <td class="wl-date-added"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $list->post->post_date ) ); ?></td>
                </tr>
			<?php endforeach; ?>
            </tbody>
        </table>
	<?php endif; ?>
</div><!-- /wishlist-wrapper -->
<?php do_action( 'woocommerce_wishlists_after_wrapper' ); ?>
