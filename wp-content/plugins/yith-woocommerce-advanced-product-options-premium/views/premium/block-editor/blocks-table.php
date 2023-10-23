<?php
/**
 * Blocks Table Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

global $wpdb;

$blocks = apply_filters( 'yith_wapo_admin_blocks', YITH_WAPO_DB()->yith_wapo_get_blocks() );
$nonce  = wp_create_nonce( 'wapo_action' );

?>

	<div id="yith_wapo_panel_blocks" class="yith-wapo">

			<?php if ( count( $blocks ) > 0 ) : ?>

                <div class="list-table-title">
                    <a href="admin.php?page=yith_wapo_panel&tab=blocks&block_id=new" class="yith-add-button yith-wapo-add-block"><?php echo esc_html__( 'Add block', 'yith-woocommerce-product-add-ons' ); ?></a>
                </div>

				<table class="yith-plugin-fw__boxed-table widefat">
					<thead>
						<tr class="list-table">
							<th class="name"><?php echo esc_html_x( 'Name', '[ADMIN] Block list page', 'yith-woocommerce-product-add-ons' ); ?></th>
							<th class="priority"><?php echo esc_html_x( 'Priority', '[ADMIN] Block list page', 'yith-woocommerce-product-add-ons' ); ?></th>
							<th class="show-on"><?php echo esc_html_x( 'Show on', '[ADMIN] Block list page', 'yith-woocommerce-product-add-ons' ); ?></th>
							<?php if ( class_exists( 'YITH_Vendors' ) ) : ?>
								<th class="vendor"><?php echo esc_html_x( 'Vendor', '[ADMIN] Block list page', 'yith-woocommerce-product-add-ons' ); ?></th>
							<?php endif; ?>
							<th class="active"><?php echo esc_html_x( 'Active', '[ADMIN] Block list page', 'yith-woocommerce-product-add-ons' ); ?></th>
                            <th class="actions"></th>
						</tr>
					</thead>
					<tbody id="sortable-blocks">

					<?php
					foreach ( $blocks as $key => $block_id ) :

                        /**
                         * @var YITH_WAPO_Block $block
                         */
                        $block = yith_wapo_instance_class(
                            'YITH_WAPO_Block',
                            array(
                                'id'   => $block_id,
                            )
                        );

                        $block_name       = $block->get_name();
                        $block_priority   = $block->get_priority();
                        $block_visibility = $block->get_visibility();
                        $block_vendor_id  = $block->get_vendor_id();

						$show_in     = $block->get_rule( 'show_in', 'all' );
                        $excl_active = wc_string_to_bool( $block->get_rule( 'exclude_products', 'no' ) );

						?>
							<tr id="block-<?php echo esc_attr( $block_id ); ?>" class="block-element" data-id="<?php echo esc_attr( $block_id ); ?>" data-priority="<?php echo esc_attr( $block_priority ); ?>">
								<td class="name">
									<a href="admin.php?page=yith_wapo_panel&tab=blocks&block_id=<?php echo esc_attr( $block_id ); ?>">
										<?php echo esc_html( ! empty( $block_name ) ? $block_name : '-' ); ?>
									</a>
								</td>
								<td class="priority">
									<?php echo esc_html( round( $block_priority ) ); ?>
								</td>
                                <td class="show-on">
                                    <?php
                                    $included_products   = (array) $block->get_rule( 'show_in_products' );
                                    $included_categories = (array) $block->get_rule( 'show_in_categories' );

                                    if ( 'all' === $show_in ) {
                                        // translators: Blocks table - Show on column.
                                        echo __( 'All products', 'yith-woocommerce-product-add-ons' );
                                    } else {
                                        echo
                                            // translators: Block options page, "Show on" column.
                                            '<span class="wapo-text-dark">' . esc_html__( 'Products', 'yith-woocommerce-product-add-ons' ) . ': </span>';

                                        if ( 'all' !== $show_in && is_array( $included_products ) ) {
                                            foreach ( $included_products as $key => $value ) {
                                                if ( $value > 0 ) {
                                                    $_product = wc_get_product( $value );
                                                    if ( is_object( $_product ) ) {
                                                        echo '<a href="' . esc_attr( $_product->get_permalink() ) . '" target="_blank">'
                                                            . esc_html( $_product->get_name() ). '</a>';
                                                        if ( $key !== array_key_last( $included_products ) ) {
                                                            echo ', ';
                                                        }
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                            }
                                        } else {
                                            echo
                                                // translators: Block options page, "Show on" column.
                                            esc_html__( 'All products', 'yith-woocommerce-product-add-ons' );
                                        }
                                        ?>
                                        <br>
                                        <?php
                                        echo
                                            // translators: Block options page, "Show on" column.
                                            '<span class="wapo-text-dark">' . esc_html__( 'Categories', 'yith-woocommerce-product-add-ons' ) . ': </span>';

                                        if ( 'all' !== $show_in && is_array( $included_categories ) ) {
                                            foreach ( $included_categories as $key => $value ) {
                                                $category = get_term_by( 'id', $value, 'product_cat' );
                                                if ( is_object( $category ) ) {
                                                    echo '<a href="' . esc_attr( get_term_link( $category->term_id, 'product_cat' ) ) . '" target="_blank">'
                                                        . esc_html( $category->name );
                                                    if ( $key !== array_key_last( $included_categories ) ) {
                                                        echo ', ';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                            }
                                        } else {
                                            echo
                                                // translators: Block options page, "Show on" column.
                                            esc_html__( 'All categories', 'yith-woocommerce-product-add-ons' );
                                        }
                                    }


                                    ?>
                                </td>
								<?php if ( class_exists( 'YITH_Vendors' ) ) : ?>
									<td class="vendor" data-vendor-id="<?php echo esc_attr( $block_vendor_id ); ?>">
										<?php
										if ( $block_vendor_id > 0 ) {
											$vendor = yith_get_vendor( $block_vendor_id, 'vendor' );
											if ( is_object( $vendor ) && $vendor->is_valid() ) {
												// $vendor
												$vendor_id   = version_compare( YITH_WPV_VERSION, '4.0', '>=' ) ? $vendor->get_id() : $vendor->id;
												$vendor_url  = version_compare( YITH_WPV_VERSION, '4.0', '>=' ) ? $vendor->get_url( 'admin' ) : get_edit_term_link( $vendor_id, $vendor->taxonomy );
												$vendor_name = version_compare( YITH_WPV_VERSION, '4.0', '>=' ) ? $vendor->get_name() : $vendor->name;
												?>
												<a href="<?php echo esc_url( $vendor_url ); ?>" target="_blank"><?php echo esc_html( stripslashes( $vendor_name ) ); ?></a>
												<?php
											} else {
												echo '-';
											}
										} else {
											echo '-';
										}
										?>
									</td>
								<?php endif; ?>
								<td class="active">
                                    <?php
										yith_plugin_fw_get_field(
											array(
												'id'    => 'yith-wapo-active-block-' . $block_id,
												'type'  => 'onoff',
												'value' => '1' === $block_visibility ? 'yes' : 'no',
											),
											true
										);
									?>
								</td>
                                <td class="actions">
                                    <?php
                                    $actions = array(
                                        'edit'   => array(
                                            'title' => _x( 'Edit', '[ADMIN] Block list page (action)', 'yith-woocommerce-product-add-ons' ),
                                            'action' => 'edit',
                                            'url' => add_query_arg(
                                                array(
                                                    'page'     => 'yith_wapo_panel',
                                                    'tab'      => 'blocks',
                                                    'block_id' => $block_id,
                                                ),
                                                admin_url( 'admin.php' )
                                            ),
                                        ),
                                        'duplicate' => array(
                                            'title' => _x( 'Duplicate', '[ADMIN] Block list page (action)', 'yith-woocommerce-product-add-ons' ),
                                            'action' => 'duplicate',
                                            'icon' => 'clone',
                                            'url'  => add_query_arg(
                                                array(
                                                    'page'        => 'yith_wapo_panel',
                                                    'wapo_action' => 'duplicate-block',
                                                    'block_id'    => $block_id,
                                                    'nonce'       => $nonce,
                                                ),
                                                admin_url( 'admin.php' )
                                            ),
                                        ),
                                        'delete' => array(
                                            'title' => _x( 'Delete', '[ADMIN] Block list page (action)', 'yith-woocommerce-product-add-ons' ),
                                            'action' => 'delete',
                                            'icon' => 'trash',
                                            'url'  => add_query_arg(
                                                array(
                                                    'page'        => 'yith_wapo_panel',
                                                    'wapo_action' => 'remove-block',
                                                    'block_id'    => $block_id,
                                                    'nonce'       => $nonce,
                                                ),
                                                admin_url( 'admin.php' )
                                            ),
                                            'confirm_data' => array(
                                                'title'               => _x( 'Confirm delete', '[ADMIN] Block list page (action)', 'yith-woocommerce-product-add-ons' ),
                                                'message'             => _x( 'Are you sure you want to delete this block?', '[ADMIN] Block list page (action)', 'yith-woocommerce-product-add-ons' ),
                                                'confirm-button'      => _x( 'Yes, delete', 'Delete confirmation action', 'yith-woocommerce-product-add-ons' ),
                                                'confirm-button-type' => 'delete',
                                            ),
                                        ),
                                        'move'   => array(
                                            'title' => _x( 'Move', '[ADMIN] Block list page (action)', 'yith-woocommerce-product-add-ons' ),
                                            'action' => 'move',
                                            'icon' => 'drag',
                                            'url'  => '#',
                                        ),
                                    );

                                    yith_plugin_fw_get_action_buttons( $actions, true );
                                    ?>
                                </td>
							</tr>

						<?php endforeach; ?>
					</tbody>
				</table>


			<?php else : ?>

				<div id="empty-state">
					<img src="<?php echo esc_attr( YITH_WAPO_URL ); ?>/assets/img/empty-state.png">
					<p>
						<?php echo
                        // translators: [ADMIN] Block list page (empty table)
                        esc_html__( 'You have no options blocks created yet.', 'yith-woocommerce-product-add-ons' );
                        ?>
                        <br />
						<?php
                        // translators: [ADMIN] Block list page (empty table)
                        echo esc_html__( 'Now build your first block!', 'yith-woocommerce-product-add-ons' );
                        ?>
					</p>
					<a href="admin.php?page=yith_wapo_panel&tab=blocks&block_id=new" class="yith-add-button">
                        <?php
                        echo
                        // translators: [ADMIN] Block list page (empty table)
                        esc_html__( 'Add block', 'yith-woocommerce-product-add-ons' );
                    ?></a>
				</div>

			<?php endif; ?>

	</div>
