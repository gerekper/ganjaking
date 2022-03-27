<?php
/**
 * WooCommerce Cart Notices
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cart Notices to newer
 * versions in the future. If you wish to customize WooCommerce Cart Notices for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-cart-notices/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * The Admin UI for the WooCommerce Cart Notices plugin.  This renders the
 * two screens: the main list of cart notices, and the create/update page.
 * The following globals and variables are expected:
 *
 * @global \WC_Cart_Notices wc_cart_notices() the cart notices main class
 *
 * @var string $tab current tab, one of 'list', 'new' or 'edit
 * @var array $notices array of notice objects, if $tab is 'list'
 * @var stdClass $notice notice object, if the tab is 'new' or 'edit'
 */

/* show any error messages */
wc_cart_notices()->get_admin_instance()->message_handler->show_messages(); ?>

<style type="text/css">
	tr.inactive {
		background-color: #F4F4F4; color:#555555;
	}
	p.note {
		border: 1px solid #DDDDDD;
		float: left;
		margin-top: 0;
		padding: 8px;
	}
	.woocommerce table.form-table li.shortcode .woocommerce-help-tip {
		margin: 2px -24px 0 0;
		float: right;
	}
</style>

<div class="wrap woocommerce">

	<div id="icon-edit-comments" class="icon32"><br></div>

	<?php if ( isset( $_GET['result'] ) ) : /* show any action messages */ ?>

		<div id="message" class="updated">
			<p><strong><?php echo esc_html__( 'Cart Notice', 'woocommerce-cart-notices' ) . ' ' . $_GET['result']; ?></strong></p>
		</div>

	<?php endif; ?>

	<?php if ( 'list' === $tab ) : ?>

		<h1 class="wp-heading-inline">
			<?php esc_html_e( 'Cart Notices', 'woocommerce-cart-notices' ); ?>
		</h1>

		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . wc_cart_notices()->id . '&amp;tab=new' ) ); ?>" class="page-title-action">
			<?php esc_html_e( 'Add cart notice', 'woocommerce-cart-notices' ); ?>
		</a>

		<hr class="wp-header-end">
		<br>

		<table class="wp-list-table widefat fixed posts">

			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-type" style="">
						<?php esc_html_e( 'Name', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="type" class="manage-column column-amount" style="">
						<?php esc_html_e( 'Type', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="message" class="manage-column column-products" style="">
						<?php esc_html_e( 'Message', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="action" class="manage-column column-usage_count" style="">
						<?php esc_html_e( 'Call to Action', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="action_url" class="manage-column column-usage_count" style="">
						<?php esc_html_e( 'Call to Action URL', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="data" class="manage-column column-usage_count" style="">
						<?php esc_html_e( 'Other', 'woocommerce-cart-notices' ); ?>
					</th>
				</tr>
			</thead>

			<tbody id="the_list">

				<?php if ( empty( $notices ) ) : ?>

					<tr scope="row">
						<th colspan="6"><?php esc_html_e( 'No notices configured', 'woocommerce-cart-notices' ); ?></th>
					</tr>

				<?php else : ?>

					<?php foreach ( $notices as $notice ) : ?>

						<tr scope="row" class="<?php echo $notice->enabled ? 'active' : 'inactive' ?>">
							<td class="post-title column-title">
								<strong>
									<a class="row-title" href="admin.php?page=<?php echo wc_cart_notices()->id; ?>&amp;tab=edit&amp;id=<?php echo $notice->id; ?>">
										<?php echo stripslashes( $notice->name ); ?>
									</a>
								</strong>
								<div class="row-actions">
									<span class="edit">
										<a href="admin.php?page=<?php echo wc_cart_notices()->id; ?>&amp;tab=edit&amp;id=<?php echo $notice->id; ?>">
											<?php esc_html_e( 'Edit', 'woocommerce-cart-notices' ); ?>
										</a>
									</span>
									|
									<span class="enable">
										<a href="admin.php?page=<?php echo wc_cart_notices()->id; ?>&amp;action=<?php echo $notice->enabled ? 'disable' : 'enable' ?>&amp;id=<?php echo $notice->id; ?>">
											<?php echo $notice->enabled ? esc_html__( 'Disable', 'woocommerce-cart-notices' ) : esc_html__( 'Enable', 'woocommerce-cart-notices' ); ?>
										</a>
									</span>
									|
									<span class="trash">
										<a onclick="return confirm( 'Really delete this entry?' );" href="admin.php?page=<?php echo wc_cart_notices()->id ?>&amp;action=delete&amp;id=<?php echo $notice->id; ?>">
											<?php esc_html_e( 'Delete', 'woocommerce-cart-notices' ); ?>
										</a>
									</span>
								</div>
							</td>
							<td>
								<?php esc_html_e( $notice->type, 'woocommerce-cart-notices' ); ?>
							</td>
							<td>
								<?php echo htmlspecialchars( $notice->message ); ?>
							</td>
							<td>
								<?php esc_html_e( $notice->action, 'woocommerce-cart-notices' ); ?>
							</td>
							<td>
								<?php echo esc_url( $notice->action_url ); ?>
							</td>
							<td>
								<?php

								switch ( $notice->type ) :

									case 'minimum_amount':

										/* translators: %s - formatted amount quantity */
										echo sprintf( esc_html__( 'Target amount: %s', 'woocommerce-cart-notices' ), wc_cart_notices()->get_minimum_order_amount( $notice ) ? get_woocommerce_currency_symbol() . wc_cart_notices()->get_minimum_order_amount( $notice ) : 'none configured' ) . '<br/>';
										/* translators: %s - formatted amount quantity */
										echo sprintf( esc_html__( 'Threshold amount: %s', 'woocommerce-cart-notices' ), isset( $notice->data['threshold_order_amount'] ) ? get_woocommerce_currency_symbol() . $notice->data['threshold_order_amount'] : 'none configured' );

									break;

									case 'deadline':

										echo sprintf( esc_html__( 'Deadline Hour: %s', 'woocommerce-cart-notices' ), $notice->data['deadline_hour'] ? $notice->data['deadline_hour'] : '<em>' . _x( 'none', 'No deadline hour', 'woocommerce-cart-notices' ) . '</em>' ) . '<br/>';
										echo sprintf( esc_html__( 'Active Days: %s', 'woocommerce-cart-notices' ), $notice->data['deadline_days_names'] ? implode( ', ', $notice->data['deadline_days_names'] ) : '<em>' . _x( 'none', 'No active days', 'woocommerce-cart-notices' ) . '</em>' );

									break;

									case 'referer':

										/* translators: %s - website */
										echo sprintf( esc_html__( 'Referring Site: %s', 'woocommerce-cart-notices' ), $notice->data['referer'] ? $notice->data['referer'] : '<em>' . _x( 'none', 'No referring site', 'woocommerce-cart-notices' ) . '</em>' );

									break;

									case 'products':

										echo sprintf( esc_html__( 'Products: %s', 'woocommerce-cart-notices' ), $notice->data['products'] ? implode( ', ', $notice->data['products'] ) : '<em>' . _x( 'none', 'No products', 'woocommerce-cart-notices' ) . '</em>' );

										if ( isset( $notice->data['minimum_quantity'] ) && '' !== $notice->data['minimum_quantity'] ) {
											echo '<br/>' . sprintf( esc_html__( 'Minimum quantity: %s', 'woocommerce-cart-notices' ), $notice->data['minimum_quantity'] );
										}

										if ( isset( $notice->data['maximum_quantity'] ) && '' !== $notice->data['maximum_quantity'] ) {
											echo '<br/>' . sprintf( esc_html__( 'Maximum quantity: %s', 'woocommerce-cart-notices' ), $notice->data['maximum_quantity'] );
										}

									break;

									case 'categories':
										echo sprintf( esc_html__( 'Categories: %s', 'woocommerce-cart-notices' ), $notice->data['categories'] ? implode( ', ', $notice->data['categories'] ) : '<em>' . _x( 'none', 'No categories', 'woocommerce-cart-notices' ) . '</em>' );
									break;

								endswitch;

								?>
							</td>
						</tr>

					<?php endforeach; ?>

				<?php endif; ?>

			</tbody>

			<tfoot>
				<tr>
					<th scope="col" id="name" class="manage-column column-type" style="">
						<?php esc_html_e( 'Name', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="type" class="manage-column column-amount" style="">
						<?php esc_html_e( 'Type', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="message" class="manage-column column-products" style="">
						<?php esc_html_e( 'Message', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="action" class="manage-column column-usage_count" style="">
						<?php esc_html_e( 'Call to Action', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="action_url" class="manage-column column-usage_count" style="">
						<?php esc_html_e( 'Call to Action URL', 'woocommerce-cart-notices' ); ?>
					</th>
					<th scope="col" id="data" class="manage-column column-usage_count" style="">
						<?php esc_html_e( 'Other', 'woocommerce-cart-notices' ); ?>
					</th>
				</tr>
			</tfoot>

		</table>

		<br/>

		<h3><?php esc_html_e( 'Shortcode Reference', 'woocommerce-cart-notices' ); ?></h3>

		<p><?php esc_html_e( 'In addition to the default placement on the cart/checkout pages, you can embed one or all of the notices anywhere on the site with the following shortcodes:', 'woocommerce-cart-notices' ) ?></p>

		<ul>
			<li><?php
				/* translators: %s - shortcode snippet */
				printf( esc_html__( '%s will embed all notices',                         'woocommerce-cart-notices' ), '<code>[woocommerce_cart_notice]</code>' ) ?></li>
			<li><?php
				/* translators: %s - shortcode snippet */
				printf( esc_html__( '%s will embed just the notice named XXX',           'woocommerce-cart-notices' ), "<code>[woocommerce_cart_notice name='XXX']</code>" ) ?></li>
			<li><?php
				/* translators: %s - shortcode snippet */
				printf( esc_html__( '%s will embed just the minimum amount notices',     'woocommerce-cart-notices' ), "<code>[woocommerce_cart_notice type='minimum_amount']</code>" ) ?></li>
			<li><?php
				/* translators: %s - shortcode snippet */
				printf( esc_html__( '%s will embed just the deadline notices',           'woocommerce-cart-notices' ), "<code>[woocommerce_cart_notice type='deadline']</code>" ) ?></li>
			<li><?php
				/* translators: %s - shortcode snippet */
				printf( esc_html__( '%s will embed just the referer notices',            'woocommerce-cart-notices' ), "<code>[woocommerce_cart_notice type='referer']</code>" ) ?></li>
			<li><?php
				/* translators: %s - shortcode snippet */
				printf( esc_html__( '%s will embed just the products in cart notices',   'woocommerce-cart-notices' ), "<code>[woocommerce_cart_notice type='products']</code>" ) ?></li>
			<li><?php
				/* translators: %s - shortcode snippet */
				printf( esc_html__( '%s will embed just the categories in cart notices', 'woocommerce-cart-notices' ), "<code>[woocommerce_cart_notice type='categories']</code>" ) ?></li>
		</ul>

	<?php elseif ( 'new' === $tab || 'edit' === $tab ) : ?>

		<form action="admin-post.php" method="post">

			<h1 class="wp-heading-inline">
				<?php echo 'new' === $tab
					? esc_html__( 'Create a new cart notice', 'woocommerce-cart-notices' )
					: esc_html__( 'Update cart notice', 'woocommerce-cart-notices' ); ?>
			</h1>

			<table class="form-table">
				<tbody>
					<?php

					/**
					 * Fires before the settings are output for a notice.
					 * Notify Diego Z if this changes {BR 2016-11-28}
					 *
					 * @since 1.6.1
					 * @param stdClass $notice notice object
					 */
					do_action( 'wc_cart_notices_admin_notice_settings_before', $notice );

					?>
					<tr valign="top">
						<th scope="row">
							<label for="notice_type"><?php esc_html_e( 'Type', 'woocommerce-cart-notices' ); ?></label>
						</th>
						<td>
							<?php if ( 'new' === $tab ) : ?>

								<select name="notice_type" id="notice_type">
									<option value=""><?php esc_html_e( 'Choose One', 'woocommerce-cart-notices' ); ?></option>
									<?php foreach ( wc_cart_notices()->get_admin_instance()->get_notice_types() as $value => $name ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $notice->type, $value ); ?>><?php echo esc_html( $name ); ?></option>
									<?php endforeach; ?>
								</select>

							<?php elseif ( 'edit' === $tab ) : ?>

								<p><?php echo esc_html( $notice->type ); /* read-only */ ?></p>

							<?php endif; ?>

							<p class="description minimum_amount_notice_data notice_data" style="<?php echo 'minimum_amount' !== $notice->type ? 'display:none;' : ''; ?>">
								<?php esc_html_e( 'This notice will appear on the cart/checkout pages only when the order total is less than the Target Amount, and/or is greater than or equal to the Threshold Amount and is convenient for encouraging customers to increase their order to qualify for free shipping.', 'woocommerce-cart-notices' ); ?>
							</p>
							<p class="description deadline_notice_data notice_data" style="<?php echo 'deadline' !== $notice->type ? 'display:none;' : ''; ?>">
								<?php esc_html_e( 'This notice will appear on the cart/checkout pages only on the Active Days, and up to the Deadline Hour, based on your WordPress timezone.', 'woocommerce-cart-notices' ); ?>
							</p>
							<p class="description referer_notice_data notice_data" style="<?php echo 'referer' !== $notice->type ? 'display:none;' : ''; ?>">
								<?php esc_html_e( 'This notice will appear on the cart/checkout pages only when the customer originated from the configured site.', 'woocommerce-cart-notices' ); ?>
							</p>
							<p class="description products_notice_data notice_data" style="<?php echo 'products' !== $notice->type ? 'display:none;' : ''; ?>">
								<?php esc_html_e( 'This notice will appear on the cart/checkout pages when any of the configured products appear within the cart.', 'woocommerce-cart-notices' ); ?>
							</p>
							<p class="description categories_notice_data notice_data" style="<?php echo 'categories' !== $notice->type ? 'display:none;' : ''; ?>">
								<?php esc_html_e( 'This notice will appear on the cart/checkout pages when any of the cart products belong to any of the categories configured below.', 'woocommerce-cart-notices' ); ?>
							</p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="notice_name">
								<?php esc_html_e( 'Name', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="notice_name" id="notice_name" value="<?php echo esc_attr( $notice->name ); ?>" class="regular-text" />
							<span class="description">
								<?php esc_html_e( 'Provide a name so you can easily recognize this notice within the admin.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="notice_enabled">
								<?php esc_html_e( 'Enabled', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="checkbox" name="notice_enabled" id="notice_enabled" value="1" <?php checked( 'new' === $tab ? 1 : $notice->enabled, 1 ); ?>/>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="notice_message">
								<?php esc_html_e( 'Notice Message', 'woocommerce-cart-notices' ); ?>
							</label>
							<br /><br />
							<span class="description">
								<?php esc_html_e( 'Depending on the notice type you may use the following variables:', 'woocommerce-cart-notices' ); ?>
								<ul>
									<?php

									$shortcodes = array(
										'{amount_under}'   => __( "With type 'Minimum Amount' this is the amount required to meet the minimum order amount.", 'woocommerce-cart-notices' ),
										'{time}'           => __( "With type 'Deadline' this is the amount of time remaining, ie '1 hour 15 minutes' or '25 minutes', etc.", 'woocommerce-cart-notices' ),
										'{products}'       => __( "With type 'Products in Cart' or 'Categories in Cart' these are the matching product names.", 'woocommerce-cart-notices' ),
										'{quantity}'       => __( "With type 'Products in Cart' this is the product quantity.", 'woocommerce-cart-notices' ),
										'{quantity_under}' => __( "With type 'Products in Cart' and 'Maximum Quantity for Notice' configured this is the product quantity less than the maximum.", 'woocommerce-cart-notices' ),
										'{quantity_over}'  => __( "With type 'Products in Cart' and 'Minimum Quantity for Notice' configured this is the product quantity over the minimum.", 'woocommerce-cart-notices' ),
										'{categories}'     => __( "With type 'Categories in Cart' these are the matching category names.", 'woocommerce-cart-notices' ),
									);

									foreach( $shortcodes as $shortcode => $help_tip ) {

										echo '<li class="shortcode"><strong>' . $shortcode . '</strong> ' . wc_help_tip( $help_tip ) . '</li>';
									}

									?>
								</ul>
							</span>
						</th>
						<td>
							<textarea name="notice_message" id="notice_message" rows="12" cols="80"><?php echo esc_textarea( $notice->message ); ?></textarea>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="call_to_action">
								<?php esc_html_e( 'Call to Action', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="call_to_action" id="call_to_action" value="<?php echo esc_attr( $notice->action ); ?>" class="regular-text" />
							<span class="description">
								<?php esc_html_e( 'Optional call to action button text, rendered next to the cart notice', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="call_to_action_url">
								<?php esc_html_e( 'Call to Action URL', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="call_to_action_url" id="call_to_action_url" value="<?php echo esc_attr( $notice->action_url ); ?>" class="regular-text" />
							<span class="description">
								<?php esc_html_e( 'Optional call to action url, this is where the user will go upon clicking the Call to Action button', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="minimum_amount_notice_data notice_data" style="<?php echo 'minimum_amount' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="minimum_order_amount">
								<?php esc_html_e( 'Target Amount', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="minimum_order_amount" id="minimum_order_amount" value="<?php echo isset( $notice->data['minimum_order_amount'] ) ? esc_attr( $notice->data['minimum_order_amount'] ) : ''; ?>" class="regular-text" />
							<span class="description">
								<?php /* translators: Placeholders: %1$s - <strong>, %2$s - </strong>, %3$s - <a>, %4$s - </a> */
								echo sprintf( esc_html__( 'Target cart total for the notice - the customer\'s cart total must be %1$sless than%2$s the target to display the notice. Leave blank to use the "Minimum order amount" from %3$san enabled Free shipping method%4$s instead.', 'woocommerce-cart-notices' ),
									'<strong>', '</strong>',
									'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping' ) . '">',
									'</a>'
								); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="minimum_amount_notice_data notice_data" style="<?php echo 'minimum_amount' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="threshold_order_amount">
								<?php esc_html_e( 'Threshold Amount', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="threshold_order_amount" id="threshold_order_amount" value="<?php echo isset( $notice->data['threshold_order_amount'] ) ? esc_attr( $notice->data['threshold_order_amount'] ) : ''; ?>" class="regular-text" />
							<span class="description">
								<?php /* translators: Placeholders: %1$s - <strong>, %2$s - </strong> */
								echo sprintf( esc_html__( 'Optional threshold amount to activate the notice. If set, the cart must contain %1$sat least%2$s this total amount for the notice to be displayed.', 'woocommerce-cart-notices' ),
									'<strong>', '</strong>'
								); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="deadline_notice_data notice_data" style="<?php echo 'deadline' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="deadline_hour">
								<?php esc_html_e( 'Deadline Hour', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="deadline_hour" id="deadline_hour" value="<?php echo isset( $notice->data['deadline_hour'] ) ? esc_attr( $notice->data['deadline_hour'] ) : ''; ?>" class="regular-text" />
							<span class="description">
								<?php esc_html_e( 'Deadline hour in 24-hour format, this can be 1 to 24.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<?php $days = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat' ); ?>
					<tr valign="top" class="deadline_notice_data notice_data" style="<?php echo 'deadline' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label>
								<?php esc_html_e( 'Active Days', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<?php

							foreach ( $days as $key => $name ) :

								$input_name = 'deadline_' . strtolower( $name );
								$value      = isset( $notice->data['deadline_days'][ $key ] ) ? $notice->data['deadline_days'][ $key ] : 0;

								echo '<input id="' . esc_attr( $input_name ) . '" name="deadline_days[' . $key . ']" type="checkbox" value="1" ' . checked( $value, 1, false ) . ' />';
								echo '<label for="' . esc_attr( $input_name ) . '">' . esc_html__( $name, 'woocommerce-cart-notices' ) . '</label>&nbsp;&nbsp;&nbsp;';

							endforeach;

							?>
							<p class="description">
								<?php esc_html_e( 'Select the days on which you want this notice to be active.', 'woocommerce-cart-notices' ); ?>
							</p>
						</td>
					</tr>

					<tr valign="top" class="referer_notice_data notice_data" style="<?php echo 'referer' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="referer">
								<?php esc_html_e( 'Referring Site', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="referer" id="referer" value="<?php echo isset( $notice->data['referer'] ) ? esc_attr( $notice->data['referer'] ) : ''; ?>" class="regular-text" />
							<span class="description">
								<?php esc_html_e( 'When the visitor originates from this server, they will be shown the referer cart notice. Example: www.google.com.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="products_notice_data notice_data" style="<?php echo 'products' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="product_ids">
								<?php esc_html_e( '"Show Notice" Products', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>

							<select
								name="product_ids[]"
								class="wc-product-search"
								style="width: 25em;"
								multiple="multiple"
								data-multiple="true"
								data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-cart-notices' ); ?>"
								data-action="woocommerce_json_search_products_and_variations">
								<?php if ( isset( $notice->data['products'] ) ) : ?>
									<?php foreach ( $notice->data['products'] as $value => $title ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>" selected="selected"><?php echo esc_html( $title ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>

							</select>

							<span class="description">
								<?php esc_html_e( 'Show the notice if any selected product is in the cart.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="products_notice_data notice_data" style="<?php echo 'products' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="minimum_product_quantity">
								<?php esc_html_e( 'Minimum Quantity for Notice', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="minimum_quantity" id="minimum_product_quantity" value="<?php echo isset( $notice->data['minimum_quantity'] ) ? esc_attr( $notice->data['minimum_quantity'] ) : ''; ?>" class="regular-text" />
							<span class="description">
								<?php esc_html_e( 'Optional minimum product quantity required to activate the notice.  If set, the quantity of the products selected above must be greater than or equal to this amount.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="products_notice_data notice_data" style="<?php echo 'products' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="maximum_product_quantity">
								<?php esc_html_e( 'Maximum Quantity for Notice', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="maximum_quantity" id="maximum_product_quantity" value="<?php echo isset( $notice->data['maximum_quantity'] ) ? esc_attr( $notice->data['maximum_quantity'] ) : ''; ?>" class="regular-text" />
							<span class="description">
								<?php esc_html_e( 'Optional maximum product quantity allowed to activate the notice.  If set, the quantity of the products selected above must be less than or equal to this amount.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="products_notice_data notice_data" style="<?php echo 'products' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="product_ids">
								<?php esc_html_e( '"Hide Notice" Products', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>

							<select
									name="hide_product_ids[]"
									class="wc-product-search"
									style="width: 25em;"
									multiple="multiple"
									data-multiple="true"
									data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-cart-notices' ); ?>"
									data-action="woocommerce_json_search_products_and_variations">
								<?php if ( isset( $notice->data['hide_products'] ) ) : ?>
									<?php foreach ( $notice->data['hide_products'] as $value => $title ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>" selected="selected"><?php echo esc_html( $title ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>

							</select>

							<span class="description">
								<?php esc_html_e( 'Hide the notice if any selected product is in the cart.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="products_notice_data notice_data" style="<?php echo 'products' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="shipping_countries">
								<?php esc_html_e( 'Shipping Countries', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>
							<select
								id="shipping_countries"
								name="shipping_countries[]"
								class="wc-enhanced-select"
								multiple="multiple"
								data-placeholder="<?php esc_attr_e( 'Choose Countries&hellip;', 'woocommerce-cart-notices' ) ?>">
								<?php foreach ( WC()->countries->countries as $code => $name ) : ?>
									<?php $selected = isset( $notice->data['shipping_countries'] ) && is_array( $notice->data['shipping_countries'] ) && in_array( $code, $notice->data['shipping_countries'] ); ?>
									<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $selected, true, true ); ?>><?php echo esc_html( $name ); ?></option>
								<?php endforeach; ?>
							</select>
							<span class="description">
								<?php esc_html_e( 'Optional list of countries used to trigger the message when the shipping country is available and matches one of the countries selected here.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="categories_notice_data notice_data" style="<?php echo 'categories' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="category_ids">
								<?php esc_html_e( '"Show" Categories', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>

							<select
								class="sv-wc-enhanced-search"
								name="category_ids[]"
								style="min-width: 300px;"
								multiple="multiple"
								data-action="wc_cart_notices_json_search_product_categories"
								data-minimum_input_length="2"
								data-nonce="<?php echo wp_create_nonce( 'search-product-categories' ); ?>"
								data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'woocommerce-cart-notices' ) ?>">
								<?php if ( isset( $notice->data['categories'] ) && is_array( $notice->data['categories'] ) ) : ?>
									<?php foreach ( $notice->data['categories'] as $value => $title ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>" selected="selected"><?php echo esc_html( $title ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>

							<?php Framework\SV_WC_Helper::render_select2_ajax(); ?>
							<span class="description">
								<?php esc_html_e( 'Show the notice if any selected category is in the cart.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>

					<tr valign="top" class="categories_notice_data notice_data" style="<?php echo 'categories' !== $notice->type ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="category_ids">
								<?php esc_html_e( '"Hide" Categories', 'woocommerce-cart-notices' ); ?>
							</label>
						</th>
						<td>

							<select
									class="sv-wc-enhanced-search"
									name="hide_category_ids[]"
									style="min-width: 300px;"
									multiple="multiple"
									data-action="wc_cart_notices_json_search_product_categories"
									data-minimum_input_length="2"
									data-nonce="<?php echo wp_create_nonce( 'search-product-categories' ); ?>"
									data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'woocommerce-cart-notices' ) ?>">
								<?php if ( isset( $notice->data['hide_categories'] ) && is_array( $notice->data['hide_categories'] ) ) : ?>
									<?php foreach ( $notice->data['hide_categories'] as $value => $title ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>" selected="selected"><?php echo esc_html( $title ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>

							<?php Framework\SV_WC_Helper::render_select2_ajax(); ?>
							<span class="description">
								<?php esc_html_e( 'Hide the notice if any selected category is in the cart.', 'woocommerce-cart-notices' ); ?>
							</span>
						</td>
					</tr>
					<?php

					/**
					 * Fires after the settings are output for a notice.
					 * Notify Diego Z if this changes {BR 2016-11-28}
					 *
					 * @since 1.6.1
					 * @param stdClass $notice notice object
					 */
					do_action( 'wc_cart_notices_admin_notice_settings_after', $notice );

					?>
				</tbody>

			</table>

			<p class="submit">

				<?php if ( 'new' === $tab ) : ?>

					<input type="hidden" name="action" value="cart_notice_new" />
					<input type="submit" name="save" value="<?php esc_attr_e( 'Create cart notice', 'woocommerce-cart-notices' ); ?>" class="button-primary" />

				<?php elseif ( 'edit' === $tab ) : ?>

					<input type="hidden" name="action" value="cart_notice_edit" />
					<input type="hidden" name="id" value="<?php echo esc_attr( $notice->id ); ?>" />
					<input type="submit" name="save" value="<?php esc_attr_e( 'Update cart notice', 'woocommerce-cart-notices' ); ?>" class="button-primary" />

			    <?php endif; ?>

			</p>

		</form>

		<?php if ( 'edit' === $tab ) : ?>

			<?php

			// display an example notice, when possible.  No real good way of doing this for the products/categories notices since they rely on the cart
			switch ( $notice->type ) :

				case 'minimum_amount':

					$minimum_order_amount   = wc_cart_notices()->get_minimum_order_amount( $notice );
					$threshold_order_amount = isset( $notice->data['threshold_order_amount'] ) ? $notice->data['threshold_order_amount'] : null;

					// determine a cart contents total that is most likely to cause a notice to be displayed
					$cart_contents_total = 0;

					if ( is_numeric( $minimum_order_amount ) ) {
						$cart_contents_total = $minimum_order_amount - 1;
					} elseif ( is_numeric( $threshold_order_amount ) ) {
						$cart_contents_total = $threshold_order_amount + 1;
					}

					$example_notice = wc_cart_notices()->get_minimum_amount_notice( $notice, array( 'cart_contents_total' => $cart_contents_total ) );

					$example_notice = $example_notice ? $example_notice : '<em>' . esc_html__( 'Cannot render this example notice without shipping zone.', 'woocommerce-cart-notices' ) . '</em>';

				break;

				case 'deadline':
					$example_notice = wc_cart_notices()->get_deadline_notice( $notice );
				break;

				case 'referer':
					$example_notice = wc_cart_notices()->get_referer_notice( $notice );
				break;

			endswitch;

			?>

			<?php if ( isset( $example_notice ) ) : ?>

				<h3><?php _e( 'Example Notice', 'woocommerce-cart-notices' ); ?></h3>

				<p style="float:left;padding-top:8px;margin-right:8px;margin-top:0;">

					<?php

					if ( 'minimum_amount' === $notice->type ) {

						if ( is_numeric( $minimum_order_amount ) && ! is_numeric( $threshold_order_amount ) ) {

							printf(
								/* translators: %s - Formatted minimum order amount */
								__( 'With the current configuration your cart notice will display when the order total is less than %s and will resemble:', 'woocommerce-cart-notices' ),
								get_woocommerce_currency_symbol() . $minimum_order_amount
							);

						} elseif ( ! is_numeric( $minimum_order_amount ) && is_numeric( $threshold_order_amount ) ) {

							printf(
								/* translators: %s - Formatted amount */
								__( 'With the current configuration your cart notice will display when the order total is greater than or equal to %s and will resemble:', 'woocommerce-cart-notices' ),
								'<strong>' . get_woocommerce_currency_symbol() . $threshold_order_amount . '</strong>'
							);

						} elseif ( is_numeric( $minimum_order_amount ) && is_numeric( $threshold_order_amount ) ) {

							printf(
								/* translators: Placeholders: %1$s Threshold order amount, %2$s Target amount */
								__( 'With the current configuration your cart notice will display when the order total is between %1$s and %2$s and will resemble:', 'woocommerce-cart-notices' ),
								'<strong>' . get_woocommerce_currency_symbol() . $threshold_order_amount . '</strong>',
								'<strong>' . get_woocommerce_currency_symbol() . $minimum_order_amount . '</strong>'
							);

						}

					} else {

						esc_html_e( 'With the current configuration your cart notice will resemble: ', 'woocommerce-cart-notices' );
					}

					?>
				</p>
				<?php

				if ( $example_notice ) {
					echo $example_notice;
				} else {
					echo '<p style="float:left;padding-top:8px;margin-top:0;"><em>' . esc_html__( 'No notice', 'woocommerce-cart-notices' ) . '</em></p>';
				}

				?>
				<div style="clear:left;"></div>

			<?php endif; ?>

		<?php endif; ?>

		<script type="text/javascript">

			var default_messages = {
				'minimum_amount' : '<?php _e( 'Add <strong>{amount_under}</strong> to your cart in order to receive free shipping!', 'woocommerce-cart-notices' ); ?>',
				'deadline' : '<?php _e( 'Order within the next <strong>{time}</strong> and your order ships today!', 'woocommerce-cart-notices' ) ?>'
			};

			jQuery( 'select#notice_type' ).change( function() {

				// show/hide descriptions and inputs based on the currently selected notice type
				jQuery( '.notice_data' ).hide();

				var notice_type = jQuery( 'select#notice_type option:selected' ).val();

				if ( notice_type ) {
					jQuery( '.' + notice_type + '_notice_data' ).show();
				}

				<?php if ( 'new' === $tab ) : /* Set some helpful defaults for the notice message field */ ?>

					var notice_message = jQuery( '#notice_message' );
					if ( notice_type === 'minimum_amount' ) {
						if ( ! notice_message.val() || notice_message.val() === default_messages['deadline'] ) {
							notice_message.val(default_messages['minimum_amount']);
						}
					} else if ( 'deadline' === notice_type ) {
						if ( ! notice_message.val() || notice_message.val() === default_messages['minimum_amount'] ) {
							notice_message.val(default_messages['deadline']);
						}
					} else if ( notice_message.val() === default_messages['minimum_amount'] || notice_message.val() === default_messages['deadline'] ) {
						notice_message.val( '' );
					}

				<?php endif; ?>
			} );

			// Edit prompt
			jQuery( function() {
				var changed = false;

				jQuery( 'input, textarea, select, checkbox' ).change( function() {
					changed = true;
				} );

				window.onbeforeunload = function() {
					if ( changed ) {
						return 'The changes you made will be lost if you navigate away from this page.';
					}
					return null;
				};

				jQuery( 'input[type=submit]' ).click( function() {
					window.onbeforeunload = '';
				} );
			} );

			// help tip handler
			jQuery( ".help_tip" ).tipTip();

		</script>

	<?php endif; ?>

</div>
