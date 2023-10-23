<?php
/**
 * Block Editor Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var $block_id
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$block = yith_wapo_instance_class(
    'YITH_WAPO_Block',
    array(
        'id'   => $block_id,
    )
);
$nonce = wp_create_nonce( 'wapo_action' );

?>

	<div id="yith-wapo-panel-block" class="yith-wapo" data-block-id="<?php echo esc_attr( $block_id ?? '' ) ?>">

			<a href="admin.php?page=yith_wapo_panel&tab=blocks" class="back-to-block-list">< <?php echo esc_html_x( 'Back to blocks list', '[ADMIN] Edit block page', 'yith-woocommerce-product-add-ons' ); ?></a>
			<div class="list-table-title">
				<h2>
                    <?php
                    // translators: [ADMIN] Edit block page
                    echo is_numeric( $block_id ) ? esc_html__( 'Edit block','yith-woocommerce-product-add-ons' ) : esc_html__( 'Add new block', 'yith-woocommerce-product-add-ons' ); ?>
                </h2>
			</div>

			<form action="admin.php?page=yith_wapo_panel&tab=blocks&block_id=<?php echo esc_attr( $block_id ); ?>" method="post" id="block">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">

				<!-- Option field -->
				<div class="field-wrap">
					<label for="block-name">
                        <?php
                        // translators: [ADMIN] Edit block page
                        echo esc_html__( 'Block name',  'yith-woocommerce-product-add-ons' ); ?>
                    </label>
					<div class="field block-option">
                        <?php

                        $block_name = '';

                        if ( ! empty( $block->get_name() ) ) {
                            $block_name = $block->get_name();
                        } elseif ( isset( $_REQUEST['block_name'] ) && ! empty( $_REQUEST['block_name'] ) ) {
                            $block_name = $_REQUEST['block_name'];
                        }

                        $block_name_field = array(
                            'id'   => 'block-name',
                            'type' => 'text',
                            'name' => 'block_name',
                            'value' => $block_name
                        );
                        yith_plugin_fw_get_field( $block_name_field, true )
                        ?>
						<span class="description"><?php
                            // translators: [ADMIN] Edit block page
                            echo esc_html__( 'Enter a name to identify this block of options.', 'yith-woocommerce-product-add-ons' ); ?>
                        </span>
					</div>
				</div>
				<!-- End option field -->

				<!-- Option field -->
				<div class="field-wrap">
					<label for="block-priority"><?php
                        // translators: [ADMIN] Edit block page
                        echo esc_html__( 'Block priority level', 'yith-woocommerce-product-add-ons' ); ?>
                    </label>
					<div class="field block-option">
                        <?php

                        $block_priority = 1;

                        if ( ! empty( $block->get_priority() ) ) {
                            $block_priority = $block->get_priority();
                        } elseif ( isset( $_REQUEST['block_priority'] ) && ! empty( $_REQUEST['block_priority'] ) ) {
                            $block_priority = $_REQUEST['block_priority'];
                        }

                        $block_priority_field = array(
                            'id'    => 'block-priority',
                            'type'  => 'number',
                            'name'  => 'block_priority',
                            'value' => esc_attr( round( $block_priority ) ),
                            'min'   => 0,
                            'max'   => 9999
                        );
                        yith_plugin_fw_get_field( $block_priority_field, true )
                        ?>
						<span class="description">
							<?php
                            // translators: [ADMIN] Edit block page
                            echo esc_html__( 'Set the priority level assigned to this rule. The priority level is important to arrange the different rules that apply to the same products. 1 is the highest priority level.',  'yith-woocommerce-product-add-ons' ); ?>
						</span>
					</div>
				</div>
				<!-- End option field -->

				<!-- BLOCK RULES -->
				<?php
				yith_wapo_get_view(
					'block-editor/block-rules.php',
					array(
						'block' => $block
					),
                    defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
				);
				?>

				<div id="addons-tabs">
					<a href="#addons-tabs" id="-addons" class="selected">
                        <?php
                        // translators: [ADMIN] Edit block page
                        echo strtoupper( esc_html__( 'Options', 'yith-woocommerce-product-add-ons' ) ); ?>
                    </a>
				</div>

				<div id="addons-tab">
					<div id="block-addons">
						<div id="block-addons-container">
							<ul id="sortable-addons">
								<?php
								$addons       = YITH_WAPO()->db->yith_wapo_get_addons_by_block_id( $block_id );
								$total_addons = count( $addons );
								if ( $total_addons > 0 ) :
									$addons = apply_filters( 'yith_wapo_admin_addons', $addons );
									foreach ( $addons as $key => $addon ) :
                                        /**
                                         * @var YITH_WAPO_Addon $addon
                                         */
										if ( yith_wapo_is_addon_type_available( $addon->get_type() ) ) :
											$total_options = is_array( $addon->options ) && isset( array_values( $addon->options )[1] ) ? count( array_values( $addon->options )[1] ) : 0; // Count of labels.
											?>
											<li id="addon-<?php echo esc_attr( $addon->get_id() ); ?>" data-id="<?php echo esc_attr( $addon->get_id() ); ?>" class="addon-element" data-priority="<?php echo esc_attr( ! empty( floatval( $addon->get_priority() ) ) ? floatval( $addon->get_priority() ) : floatval( $addon->get_id() ) ); ?>">
                                                <div class="addon-editor-left">
                                                    <span class="addon-icon <?php echo esc_attr( $addon->get_type() ); ?>">
                                                        <span class="wapo-icon wapo-icon-<?php echo esc_attr( $addon->get_type() ); ?>"></span>
                                                    </span>
                                                        <span class="addon-name">
                                                            <?php $option_url = add_query_arg(
                                                                array(
                                                                    'page'           => 'yith_wapo_panel',
                                                                    'tab'            => 'blocks',
                                                                    'block_id'       => $block->get_id(),
                                                                    'addon_id'       => $addon->get_id(),
                                                                    'addon_type'     => $addon->get_type(),
                                                                ),
                                                                admin_url( '/admin.php' )
                                                            ); ?>
                                                        <a href="<?php echo esc_url( $option_url ) ?>">
                                                            <?php
                                                            echo esc_html( $addon->get_setting( 'title' ) ? $addon->get_setting( 'title' ) . ' - ' : '' );
                                                            echo esc_html( YITH_WAPO()->get_addon_name_by_slug( $addon->get_type() ) );

                                                            if ( strpos( $addon->get_type(), 'html' ) === false ) {
                                                                echo ' (' . esc_html( $total_options ) . ' ';
                                                                // translators: [ADMIN] Edit block page.
                                                                echo 1 === $total_options ? esc_html_x( 'option', 'singular option on Add-on title. Ex: Date (1 option)', 'yith-woocommerce-product-add-ons' ) : esc_html_x( 'options', 'several options on Add-on title. Ex: Date (2 options)', 'yith-woocommerce-product-add-ons' );
                                                                echo ')';
                                                            }
                                                            do_action( 'yith_wapo_admin_after_addon_title', $addon );
                                                            ?>
                                                        </a>
                                                    </span>
                                                </div>

                                                <div class="addon-editor-right">
                                                    <span class="addon-onoff">
                                                        <?php
                                                        yith_plugin_fw_get_field(
                                                            array(
                                                                'id' => 'yith-wapo-active-addon-' . $addon->get_id(),
                                                                'type' => 'onoff',
                                                                'value' => '1' === $addon->get_visibility() ? 'yes' : 'no',
                                                            ),
                                                            true
                                                        );
                                                        ?>
												    </span>
                                                    <span class="addon-actions"">

                                                        <?php
                                                        $actions = array(
                                                            'edit'   => array(
                                                                // translators: [ADMIN] Edit block page (block actions in the table)
                                                                'title' => __( 'Edit', 'yith-woocommerce-product-add-ons' ),
                                                                'action' => 'edit',
                                                                'url' => add_query_arg(
                                                                    array(
                                                                        'page'       => 'yith_wapo_panel',
                                                                        'tab'        => 'blocks',
                                                                        'block_id'   => $block->get_id(),
                                                                        'addon_id'   => $addon->get_id(),
                                                                        'addon_type' => $addon->get_type(),
                                                                        'nonce'      => $nonce,
                                                                    ),
                                                                    admin_url( 'admin.php' )
                                                                ),
                                                            ),
                                                            'duplicate' => array(
                                                                // translators: [ADMIN] Edit block page (block actions in the table)
                                                                'title' => __( 'Duplicate', 'yith-woocommerce-product-add-ons' ),
                                                                'action' => 'duplicate',
                                                                'icon' => 'clone',
                                                                'url'  => add_query_arg(
                                                                    array(
                                                                        'page'       => 'yith_wapo_panel',
                                                                        'tab'        => 'blocks',
                                                                        'wapo_action' => 'duplicate-addon',
                                                                        'block_id'   => $block->get_id(),
                                                                        'addon_id'   => $addon->get_id(),
                                                                        'nonce'      => $nonce,
                                                                    ),
                                                                    admin_url( 'admin.php' )
                                                                ),
                                                            ),
                                                            'delete' => array(
                                                                // translators: [ADMIN] Edit block page (block actions in the table)
                                                                'title' => __( 'Delete', 'yith-woocommerce-product-add-ons' ),
                                                                'action' => 'delete',
                                                                'icon' => 'trash',
                                                                'url'  => add_query_arg(
                                                                    array(
                                                                        'page'        => 'yith_wapo_panel',
                                                                        'wapo_action' => 'remove-addon',
                                                                        'block_id'    => $block->get_id(),
                                                                        'addon_id'    => $addon->get_id(),
                                                                        'nonce'       => $nonce,
                                                                    ),
                                                                    admin_url( 'admin.php' )
                                                                ),
                                                                'confirm_data' => array(
                                                                    // translators: [ADMIN] Edit block page (delete action)
                                                                    'title'               => __( 'Confirm delete', 'yith-woocommerce-product-add-ons' ),
                                                                    // translators: [ADMIN] Edit block page (delete action)
                                                                    'message'             => __( 'Are you sure you want to delete this add-on?', 'yith-woocommerce-product-add-ons' ),
                                                                    // translators: [ADMIN] Edit block page (delete action)
                                                                    'confirm-button'      => __( 'Yes, delete', 'yith-woocommerce-product-add-ons' ),
                                                                    'confirm-button-type' => 'delete',
                                                                ),
                                                            ),
                                                            'move'   => array(
                                                                // translators: [ADMIN] Edit block page (Move action).
                                                                'title' => __( 'Move', 'yith-woocommerce-product-add-ons' ),
                                                                'action' => 'move',
                                                                'icon' => 'drag',
                                                                'url'  => '#',
                                                            ),
                                                        );

                                                        yith_plugin_fw_get_action_buttons( $actions, true );
                                                        ?>
												    </span>
                                                </div>
											</li>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
							<div id="add-option">
								<?php if ( ! $total_addons > 0 ) : ?>
									<p class="start-new-option"><?php
                                        // translators: [ADMIN] Edit block page > block not empty
                                        echo esc_html__( 'Start to add your options to this block!', 'yith-woocommerce-product-add-ons' ); ?></p>
								<?php endif; ?>
								<input type="submit" name="add_options_after_save" class="add-new-addon-button" value="<?php
                                // translators: [ADMIN] Edit block page > block not empty
                                echo '+ ' . esc_html__( 'Add option', 'yith-woocommerce-product-add-ons' ); ?>"
                                >

							</div>
						</div>
					</div>
				</div>

				<input type="hidden" name="wapo_action" value="save-block">
				<input type="hidden" name="id" value="<?php echo esc_attr( $block_id ); ?>">
				<!-- YITH WooCommerce Multi Vendor Integration -->
				<?php
				// manage_option capability prevent to assign rules to specific vendor if the admin create the rules.
				if ( function_exists( 'yith_get_vendor' ) && ! current_user_can( 'manage_options' ) ) {
					$vendor = yith_get_vendor( 'current', 'user' );
					if ( $vendor->is_valid() ) {
						$vendor_id = version_compare( YITH_WPV_VERSION, '4.0', '>=' ) ? $vendor->get_id() : $vendor->id;
						printf( '<input type="hidden" name="vendor_id" value="%1$s">', esc_attr( $vendor_id ) );
					}
				}
				?>
				<div id="save-button">
					<button name="save-block-button" class="yith-save-button"><?php
                        // translators: [ADMIN] Edit block page > Save button
                        echo esc_html__( 'Save', 'yith-woocommerce-product-add-ons' ); ?></button>
				</div>

			</form>

	</div>

	<?php
	if ( isset( $_REQUEST['addon_id'] ) || isset( $_REQUEST['add_options_after_save'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		yith_wapo_get_view(
			'addon-editor/addon-editor.php',
			array(
				'block_id' => $block_id,
				'block'    => $block
			),
            defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
		);
	}
	?>

