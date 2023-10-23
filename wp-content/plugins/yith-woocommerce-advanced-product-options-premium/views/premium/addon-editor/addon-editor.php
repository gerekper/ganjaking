<?php
/**
 * Addon Editor Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var int $block_id Block ID.
 * @var array $block The block.
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$addon_id      = isset( $_REQUEST['addon_id'] ) ? sanitize_key( $_REQUEST['addon_id'] ) : 'new'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$addon_type    = isset( $_REQUEST['addon_type'] ) ? sanitize_key( $_REQUEST['addon_type'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$template_file = YITH_WAPO_DIR . '/views/addons/' . $addon_type . '.php';

$delete_action = array(
    'title'        => _x( 'Delete', 'Add-on editor panel (option action)', 'yith-woocommerce-product-add-ons' ),
    'action'       => 'delete',
    'icon'         => 'trash',

    'confirm_data' => array(
        'title'               => _x( 'Confirm delete', 'Add-on editor panel (option action)', 'yith-plugin-fw' ),
        'message'             => _x( 'Are you sure you want to delete this option?', 'Add-on editor panel (option action)', 'yith-plugin-fw' ),
        'confirm-button'      => _x( 'Yes, delete', 'Add-on editor panel (option action)', 'yith-plugin-fw' ),
        'confirm-button-type' => 'delete',
    ),
);

if ( yith_wapo_is_addon_type_available( $addon_type ) && ( file_exists( $template_file ) || 'new' === $addon_id ) ) : ?>
	<?php
	$addons_type = YITH_WAPO()->get_addon_types();
	$addon_name  = '';
    $addon_title = '';

    foreach ( $addons_type as $addon ) {
		if ( isset( $addon['slug'] ) && $addon_type === $addon['slug'] ) {
			$addon_name  = $addon['label'] ?? '';
			$addon_title = $addon['name'] ?? '';
		}
	}
	?>
	<div id="yith-wapo-addon-overlay" class="yith-plugin-fw yith-plugin-ui yith-wapo">
		<div id="addon-editor" class="yith-wapo-addon-type-<?php echo esc_html( $addon_type ); ?>" data-addon-type="<?php echo esc_html( $addon_type ); ?>">

			<span href="#" id="close-popup">
				<img src="<?php echo esc_attr( YITH_WAPO_URL ); ?>/assets/img/popup-close.png">
			</span>

			<?php if ( '' !== $addon_type ) :

                $addon = yith_wapo_instance_class(
                    'YITH_WAPO_Addon',
                    array(
                        'id'   => $addon_id,
                        'type' => $addon_type
                    )
                );
                ?>

				<form action="admin.php?page=yith_wapo_panel&tab=blocks" method="post" id="addon">
					<button type="submit" class="submit button-primary" style="display: none;"></button>

					<?php if ( 'new' === $addon_id ) : ?>
						<a href="admin.php?page=yith_wapo_panel&tab=blocks&block_id=<?php echo esc_attr( $block_id ); ?>&addon_id=new" style="margin-bottom: 20px; display: block;">
                            <?php
                            //translators: When creating a new add-on, you can go back to type choice.
                            echo esc_html( '< ' ) . esc_html__( 'back to the type choice','yith-woocommerce-product-add-ons' );
                            ?>
						</a>
					<?php endif; ?>

					<div id="addon-editor-type" class="addon-editor-type-<?php echo esc_html( $addon_type ); ?>">

						<h3><?php echo esc_html( ucfirst( str_replace( 'html', 'HTML', str_replace( '_', ' ', $addon_title ) ) ) ); ?></h3>

						<?php if ( strpos( $addon_type, 'html' ) === false ) : ?>

							<?php
							yith_wapo_get_view(
								'addon-editor/addon-tabs.php',
								array(
									'addon_id'   => $addon_id,
									'addon_type' => $addon_type,
								)
							);
							?>

						<?php endif; ?>

						<div id="addon-container">
							<!-- POPULATE OPTIONS -->
							<div id="tab-options-list">

								<?php
								$options_total = is_array( $addon->options ) && isset( array_values( $addon->options )[1] ) ? count( array_values( $addon->options )[1] ) : 1; // Count of labels.
								if ( 'html_heading' === $addon_type || 'html_separator' === $addon_type || 'html_text' === $addon_type ) :
									yith_wapo_get_view(
										'addons/' . $addon_type . '.php',
                                        array(
                                            'addon'      => $addon,
                                            'addon_type' => $addon_type,
                                        )
									);
								else :
									?>

									<!-- Option field -->
									<div class="field-wrap addon-field-grid" style="margin-top: 20px;">
										<label for="addon-title" style="width: 50px;">
                                            <?php
                                            //translators: Add-on editor panel.
                                            echo esc_html__( 'Title', 'yith-woocommerce-product-add-ons' ) . esc_html( ':' );
                                            ?>
                                        </label>
										<div class="field">
                                            <div class="addon-title-container">
                                                <input type="text" name="addon_title" id="addon-title" value="<?php echo esc_attr( $addon->get_setting( 'title', '', false ) ); ?>">

                                                <div class="title-in-cart enabler revert">
                                                    <?php
                                                    yith_plugin_fw_get_field(
                                                        array(
                                                            'id'    => 'addon-title-in-cart',
                                                            'class' => 'checkbox',
                                                            'name'  => 'addon_title_in_cart',
                                                            'type'  => 'checkbox',
                                                            'value' => $addon->get_setting( 'title_in_cart', 'yes', false ),
                                                        ),
                                                        true
                                                    );
                                                    ?>
                                                    <label for="<?php echo esc_attr( 'addon-title-in-cart' ) ?>">
                                                        <?php
                                                        // translators: Edit add-on panel > Title option
                                                        echo esc_html__( 'Use also as title in cart', 'yith-woocommerce-product-add-ons' ); ?>
                                                    </label>
                                                </div>
                                            </div>
										</div>
                                        <span class="description">
                                            <?php
                                            //translators: Add-on editor panel.
                                            echo esc_html__( 'Enter a title to show before the options.', 'yith-woocommerce-product-add-ons' );
                                            ?>
                                        </span>
									</div>
									<!-- End option field -->
                                <div class="field-wrap addon-title-in-cart-opt-container enabled-by-addon-title-in-cart addon-field-grid" style="<?php echo '1' === $addon->get_setting( 'title_in_cart', 'yes', false ) ? 'display: none;' : '' ?> margin-top: 20px;">
                                    <label for="addon-title-in-cart-opt">
                                        <?php
                                        //translators: Add-on editor panel.
                                        echo esc_html__( 'Title in cart', 'yith-woocommerce-product-add-ons' ) . esc_html( ':' );
                                        ?>
                                    </label>
                                    <div class="field">
                                        <?php
                                        yith_plugin_fw_get_field(
                                            array(
                                                'id'    => 'addon-title-in-cart-opt',
                                                'class' => '',
                                                'name'  => 'addon_title_in_cart_opt',
                                                'type'  => 'text',
                                                'value' => $addon->get_setting( 'title_in_cart_opt', '', false ),
                                            ),
                                            true
                                        );
                                        ?>
                                    </div>
                                    <span class="description">
                                        <?php
                                        //translators: Add-on editor panel.
                                        echo esc_html__( 'Enter a title to show in cart, checkout and order.', 'yith-woocommerce-product-add-ons' );
                                        ?>
                                    </span>
                                </div>


                                <!-- Option field -->
                                <div class="field-wrap addon-field-grid">
                                    <label for="addon-description" style="width: 50px;">
                                        <?php
                                        //translators: Add-on editor panel.
                                        echo esc_html__( 'Description', 'yith-woocommerce-product-add-ons' ) . esc_html( ':' );
                                        ?>
                                    </label>
                                    <div class="field">
                                        <textarea type="text" name="addon_description" id="addon-description"><?php echo esc_attr( $addon->get_setting( 'description', '', false ) ); ?></textarea>
                                    </div>
                                    <span class="description"><?php
                                        //translators: Add-on editor panel.
                                        echo esc_html__( 'Enter a description to show before the options.',  'yith-woocommerce-product-add-ons' );
                                        ?>
                                    </span>
                                </div>
									<!-- End option field -->

									<div id="addon_options">
									<?php
									for ( $x = 0; $x < $options_total; $x++ ) :
										$addon_label = $addon->get_option( 'label', $x, '', false );
										if ( 'product' === $addon_type ) {
											$product_id = $addon->get_option( 'product', $x, '', false ) ? $addon->get_option( 'product', $x, '', false ) : '';
											if ( $product_id > 0 ) {
												$product = wc_get_product( $product_id );
												if ( $product instanceof WC_Product ) {
													$addon_label = $product->get_name();
												}
											}
										}
										?>
										<div class="option <?php echo 1 === $options_total ? 'open' : ''; ?>" data-index="<?php echo esc_attr( $x ); ?>">
											<div class="actions" style="<?php echo 1 === $options_total ? 'display: none;' : ''; ?>">
												<?php
													$actions = array(
														'delete'    => $delete_action
													);
													yith_plugin_fw_get_action_buttons( $actions, true );
													?>
											</div>
											<div class="title">
												<span class="icon"></span>
												<div class="addon-name">
													<div class="name">
														<?php echo esc_html( mb_strtoupper( $addon_name ) ) . ' - <span class="addon-label-text">' . esc_html( substr( $addon_label, 0, 60 ) ) . '</span>';?>
													</div>
													<div class="additional-options">
														<div class="selected-by-default">
															<?php if ( in_array( $addon_type, array( 'checkbox', 'color', 'label', 'product', 'radio', 'select' ), true ) ) : ?>
																<!-- Option field -->
																<div class="field-default">
																	<?php
																	$is_default = $addon->get_option( 'default', $x, 'no', false ) === 'yes';
																	if ( 'new' === $addon_id && 'radio' === $addon_type ) {
																		$is_default = 'yes';
																	}
																	yith_plugin_fw_get_field(
																		array(
																			'id'    => 'option-default-' . $x,
																			'name'  => 'options[default][' . $x . ']',
																			'type'  => 'checkbox',
																			'class' => 'selected-by-default-chbx checkbox',
																			'value' => $is_default,
																		),
																		true
																	);
																	?>
																</div>
                                                                <?php //translators: Add-on editor panel ?>
																<label for="option-default-<?php echo esc_attr( $x ); ?>" class="selected-by-default-chbx"><?php echo esc_html_x( 'Selected by default', 'Add-on editor panel', 'yith-woocommerce-product-add-ons' ); ?></label>
																<!-- End option field -->
															<?php endif; ?>
														</div>
														<div class="enabled">
															<?php
															$enabled = $addon->get_option( 'addon_enabled', $x, 'yes', false );
															yith_plugin_fw_get_field(
																array(
																	'id'      => 'addon-option-enabled-' . $x,
																	'name'    => 'options[addon_enabled][' . $x . ']',
																	'class'   => 'enabler',
																	'default' => 'yes',
																	'type'    => 'onoff',
																	'value'   => $enabled,
																),
																true
															);
															?>
														</div>
													</div>
												</div>
											</div>
											<?php
											yith_wapo_get_view(
												'addons/' . $addon_type . '.php',
												array(
													'addon' => $addon,
													'addon_type' => $addon_type,
													'x' => $x
												)
											);
											?>
										</div>
									<?php endfor; ?>
									</div>

									<div id="add-new-option">+ <?php
                                        //translators: Add-on editor panel.
                                        echo esc_html( yith_wapo_get_string_by_addon_type( 'add_new', $addon_type ) ) . ' ' . esc_html( mb_strtolower( $addon_name ) );
                                        ?>
                                    </div>

									<!-- NEW OPTION TEMPLATE -->
								<?php
								//TODO: Create wp.template() functionality correctly.
								?>
									<?php for ( $temp = $x + 20; $x < $temp; $x++ ) : ?>
										<script type="text/html" id="tmpl-new-option-<?php echo esc_attr( $x ); ?>">
											<div class="option open" data-index="<?php echo esc_attr( $x ); ?>">
											<div class="actions">
													<?php
														$actions = array(
															'delete'    => $delete_action,
														);
														yith_plugin_fw_get_action_buttons( $actions, true );
														?>
												</div>
												<div class="title">
													<span class="icon"></span>
													<div class="addon-name">
														<div class="name">
                                                            <?php echo esc_html( mb_strtoupper( $addon_name ) ); ?> - <span class="addon-label-text"></span>
														</div>
														<div class="additional-options">
															<div class="selected-by-default">
																<?php if ( in_array( $addon_type, array( 'checkbox', 'color', 'label', 'product', 'radio', 'select' ), true ) ) : ?>
																	<!-- Option field -->
																	<div class="field-default">
																		<?php
																		$is_default = $addon->get_option( 'default', $x, 'no', false ) === 'yes';

																		yith_plugin_fw_get_field(
																			array(
																				'id'    => 'option-default-' . $x,
																				'name'  => 'options[default][' . $x . ']',
																				'type'  => 'checkbox',
																				'class' => 'selected-by-default-chbx checkbox',
																				'value' => $is_default,
																			),
																			true
																		);
																		?>
																	</div>
																	<label for="option-default-<?php echo esc_attr( $x ); ?>" class="selected-by-default-chbx">
                                                                        <?php
                                                                        //translators: Add-on editor panel.
                                                                        echo esc_html__( 'Selected by default', 'yith-woocommerce-product-add-ons' );
                                                                        ?>
                                                                    </label>
																	<!-- End option field -->
																<?php endif; ?>
															</div>
															<div class="enabled">
																<?php
																$enabled = $addon->get_option( 'addon_enabled', $x, 'yes', false );

																yith_plugin_fw_get_field(
																	array(
																		'id'      => 'addon-option-enabled-' . $x,
																		'name'    => 'options[addon_enabled][' . $x . ']',
																		'class'   => 'enabler',
																		'default' => 'yes',
																		'type'    => 'onoff',
																		'value'   => $enabled,
																	),
																	true
																);
																?>
															</div>
														</div>
													</div>
												</div>
												<?php
												$new_option = true;
												yith_wapo_get_view(
													'addons/' . $addon_type . '.php',
													array(
														'addon' => $addon,
														'addon_type' => $addon_type,
														'x' => $x
													)
												);
												?>
											</div>
										</script>
									<?php endfor; ?>
									<!-- NEW OPTION TEMPLATE -->

								<?php endif; ?>
							</div>

							<?php

							yith_wapo_get_view(
								'addon-editor/addon-display-settings.php',
								array(
									'addon'      => $addon,
									'addon_id'   => $addon_id,
									'addon_type' => $addon_type,
									'block_id'   => $block_id,
									'block'      => $block,
								),
                                defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
							);
							yith_wapo_get_view(
								'addon-editor/addon-conditional-logic.php',
								array(
									'addon'      => $addon,
									'addon_id'   => $addon_id,
									'addon_type' => $addon_type,
									'block_id'   => $block_id,
									'block'      => $block,
								),
                            );
							yith_wapo_get_view(
								'addon-editor/addon-advanced-settings.php',
								array(
									'addon'      => $addon,
									'addon_id'   => $addon_id,
									'addon_type' => $addon_type,
									'block_id'   => $block_id,
									'block'      => $block,
								),
                                defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
							);
							?>
						</div><!-- #options-container -->
					</div><!-- #options-editor-radio -->

					<input type="hidden" name="wapo_action" value="save-addon">
					<input type="hidden" name="addon_id" value="<?php echo esc_attr( $addon_id ); ?>">
					<input type="hidden" name="addon_type" value="<?php echo esc_attr( $addon_type ); ?>">
					<input type="hidden" name="block_id" value="<?php echo esc_attr( $block_id ); ?>">
					<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'wapo_admin' ) ); ?>">
                    <input type="hidden" name="block_name" value="<?php echo ! empty( $block->get_name() ) ? $block->get_name() : ( $_REQUEST['block_name'] ?? '' ); ?>">
                    <input type="hidden" name="block_priority" value="<?php echo ! empty( $block->get_priority() ) ? $block->get_priority() : ( $_REQUEST['block_priority'] ?? '' ); ?>">
                    <input type="hidden" name="block_rule_show_in" value="<?php echo ! empty( $block->get_rule( 'show_in', '' ) ) ? $block->get_rule( 'show_in' ) : ( $_REQUEST['block_rule_show_in'] ?? '' ); ?>">
                    <input type="hidden" name="block_rule_show_in_products" value="<?php echo ! empty( $block->get_rule( 'show_in_products', '' ) ) ? base64_encode( serialize( $block->get_rule( 'show_in_products' ) ) ) : ( isset( $_REQUEST['block_rule_show_in_products'] ) ? base64_encode( serialize( $_REQUEST['block_rule_show_in_products'] ) ) : '' ); ?>">
                    <input type="hidden" name="block_rule_show_in_categories" value="<?php echo ! empty( $block->get_rule( 'show_in_categories', '' ) ) ? base64_encode( serialize( $block->get_rule( 'show_in_categories' ) ) ) : ( isset( $_REQUEST['block_rule_show_in_categories'] ) ? base64_encode( serialize( $_REQUEST['block_rule_show_in_categories'] ) ) : '' ); ?>">
                    <input type="hidden" name="block_rule_exclude_products" value="<?php echo ! empty( $block->get_rule( 'exclude_products', '' ) ) ? $block->get_rule( 'exclude_products' ) : ( $_REQUEST['block_rule_exclude_products'] ?? '' ); ?>">
                    <input type="hidden" name="block_rule_exclude_products_products" value="<?php echo ! empty( $block->get_rule( 'exclude_products_products', '' ) ) ? base64_encode( serialize( $block->get_rule( 'exclude_products_products' ) ) ) : ( isset( $_REQUEST['block_rule_exclude_products_products'] ) ? base64_encode( serialize( $_REQUEST['block_rule_exclude_products_products'] ) ) : '' ); ?>">
                    <input type="hidden" name="block_rule_exclude_products_categories" value="<?php echo ! empty( $block->get_rule( 'exclude_products_categories', '' ) ) ? base64_encode( serialize( $block->get_rule( 'exclude_products_categories' ) ) ) : ( isset( $_REQUEST['block_rule_exclude_products_categories'] ) ? base64_encode( serialize( $_REQUEST['block_rule_exclude_products_categories'] ) ) : '' ); ?>">
                    <input type="hidden" name="block_rule_show_to" value="<?php echo ! empty( $block->get_rule( 'show_to', '' ) ) ? $block->get_rule( 'show_to' ) : ( isset( $_REQUEST['block_rule_show_to'] ) && ! empty( $_REQUEST['block_rule_show_to'] ) ? $_REQUEST['block_rule_show_to'] : 'all' ); ?>">
                    <input type="hidden" name="block_rule_show_to_user_roles" value="<?php echo ! empty( $block->get_rule( 'show_to_user_roles', '' ) ) ? base64_encode( serialize( $block->get_rule( 'show_to_user_roles' ) ) ) : ( isset( $_REQUEST['block_rule_show_to_user_roles'] ) ? base64_encode( serialize( $_REQUEST['block_rule_show_to_user_roles'] ) ) : '' ); ?>">

                    <div id="addon-editor-buttons">
						<button type="reset" class="cancel button-secondary"><?php
                            //translators: Add-on editor panel.
                            echo esc_html__( 'Cancel', 'yith-woocommerce-product-add-ons' );
                            ?>
                        </button>
						<button type="submit" class="submit button-primary"><?php
                            //translators: Add-on editor panel.
                            echo esc_html__( 'Save', 'yith-woocommerce-product-add-ons' );
                            ?>
                        </button>
					</div>

				</form>

			<?php elseif ( 'new' === $addon_id ) : ?>

				<div id="types">
					<h3>
                        <?php
                        //translators: Add-on editor panel.
                        echo esc_html__( 'Add HTML element', 'yith-woocommerce-product-add-ons' );
                        ?>
                    </h3>
					<div class="types">
						<?php foreach ( YITH_WAPO()->get_html_types() as $key => $html_type ) : ?>

                            <?php
                            $html_url = add_query_arg(
                                array(
                                    'page'           => 'yith_wapo_panel',
                                    'tab'            => 'blocks',
                                    'block_id'       => $block_id,
                                    'addon_id'       => 'new',
                                    'addon_type'     => $html_type['slug'],
                                    'block_name'     => isset( $_REQUEST['block_name'] ) && ! empty( $_REQUEST['block_name'] ) ? $_REQUEST['block_name'] : '',
                                    'block_priority' => isset( $_REQUEST['block_priority'] ) && ! empty( $_REQUEST['block_priority'] ) ? $_REQUEST['block_priority'] : '',
                                    'block_rule_show_in'                     => isset( $_REQUEST['block_rule_show_in'] ) && ! empty( $_REQUEST['block_rule_show_in'] ) ? $_REQUEST['block_rule_show_in'] : '',
                                    'block_rule_show_in_products'            => isset( $_REQUEST['block_rule_show_in_products'] ) && ! empty( $_REQUEST['block_rule_show_in_products'] ) ? $_REQUEST['block_rule_show_in_products'] : '',
                                    'block_rule_show_in_categories'          => isset( $_REQUEST['block_rule_show_in_categories'] ) && ! empty( $_REQUEST['block_rule_show_in_categories'] ) ? $_REQUEST['block_rule_show_in_categories'] : '',
                                    'block_rule_exclude_products'            => isset( $_REQUEST['block_rule_exclude_products'] ) && ! empty( $_REQUEST['block_rule_exclude_products'] ) ? $_REQUEST['block_rule_exclude_products'] : '',
                                    'block_rule_exclude_products_products'   => isset( $_REQUEST['block_rule_exclude_products_products'] ) && ! empty( $_REQUEST['block_rule_exclude_products_products'] ) ? $_REQUEST['block_rule_exclude_products_products'] : '',
                                    'block_rule_exclude_products_categories' => isset( $_REQUEST['block_rule_exclude_products_categories'] ) && ! empty( $_REQUEST['block_rule_exclude_products_categories'] ) ? $_REQUEST['block_rule_exclude_products_categories'] : '',
                                    'block_rule_show_to'                     => isset( $_REQUEST['block_rule_show_to'] ) && ! empty( $_REQUEST['block_rule_show_to'] ) ? $_REQUEST['block_rule_show_to'] : '',
                                    'block_rule_show_to_user_roles'          => isset( $_REQUEST['block_rule_show_to_user_roles'] ) && ! empty( $_REQUEST['block_rule_show_to_user_roles'] ) ? $_REQUEST['block_rule_show_to_user_roles'] : '',
                                ),
                                admin_url( '/admin.php' )
                            );
                            ?>


							<a class="type" href="<?php echo esc_attr( $html_url ); ?>">
								<div class="icon <?php echo esc_attr( $html_type['slug'] ); ?>"><span class="wapo-icon wapo-icon-<?php echo esc_attr( $html_type['slug'] ); ?>"></span></div>
								<?php echo esc_html( $html_type['name'] ); ?>
							</a>
						<?php endforeach; ?>
					</div>
					<h3><?php
                        //translators: Add-on editor panel.
                        echo esc_html__( 'Add option for the user', 'yith-woocommerce-product-add-ons' );
                        ?>
                    </h3>
					<div class="types">
						<?php
							$available_addon_types = YITH_WAPO()->get_available_addon_types();
						foreach ( $addons_type as $key => $addon_type ) :
                            if ( str_starts_with( $addon_type['slug'], 'html' ) ) {
                                continue;
                            }
							$class = 'disabled';
							$url   = admin_url( 'admin.php?page=yith_wapo_panel' );
							if ( in_array( $addon_type['slug'], $available_addon_types, true ) ) {
								$class = 'enabled';

                                $url = add_query_arg(
                                    array(
                                        'page'           => 'yith_wapo_panel',
                                        'tab'            => 'blocks',
                                        'block_id'       => $block_id,
                                        'addon_id'       => 'new',
                                        'addon_type'     => $addon_type['slug'],
                                        'block_name'     => isset( $_REQUEST['block_name'] ) && ! empty( $_REQUEST['block_name'] ) ? $_REQUEST['block_name'] : '',
                                        'block_priority' => isset( $_REQUEST['block_priority'] ) && ! empty( $_REQUEST['block_priority'] ) ? $_REQUEST['block_priority'] : '',
                                        'block_rule_show_in'                     => isset( $_REQUEST['block_rule_show_in'] ) && ! empty( $_REQUEST['block_rule_show_in'] ) ? $_REQUEST['block_rule_show_in'] : '',
                                        'block_rule_show_in_products'            => isset( $_REQUEST['block_rule_show_in_products'] ) && ! empty( $_REQUEST['block_rule_show_in_products'] ) ? $_REQUEST['block_rule_show_in_products'] : '',
                                        'block_rule_show_in_categories'          => isset( $_REQUEST['block_rule_show_in_categories'] ) && ! empty( $_REQUEST['block_rule_show_in_categories'] ) ? $_REQUEST['block_rule_show_in_categories'] : '',
                                        'block_rule_exclude_products'            => isset( $_REQUEST['block_rule_exclude_products'] ) && ! empty( $_REQUEST['block_rule_exclude_products'] ) ? $_REQUEST['block_rule_exclude_products'] : '',
                                        'block_rule_exclude_products_products'   => isset( $_REQUEST['block_rule_exclude_products_products'] ) && ! empty( $_REQUEST['block_rule_exclude_products_products'] ) ? $_REQUEST['block_rule_exclude_products_products'] : '',
                                        'block_rule_exclude_products_categories' => isset( $_REQUEST['block_rule_exclude_products_categories'] ) && ! empty( $_REQUEST['block_rule_exclude_products_categories'] ) ? $_REQUEST['block_rule_exclude_products_categories'] : '',
                                        'block_rule_show_to'                     => isset( $_REQUEST['block_rule_show_to'] ) && ! empty( $_REQUEST['block_rule_show_to'] ) ? $_REQUEST['block_rule_show_to'] : '',
                                        'block_rule_show_to_user_roles'          => isset( $_REQUEST['block_rule_show_to_user_roles'] ) && ! empty( $_REQUEST['block_rule_show_to_user_roles'] ) ? $_REQUEST['block_rule_show_to_user_roles'] : '',
                                    ),
                                    admin_url( '/admin.php' )
                                );

							}
							?>
							<a class="type <?php echo esc_attr( $class ); ?>" href="<?php echo esc_attr( $url ); ?>" <?php echo 'disabled' === $class ? 'onclick="return false;"' : ''; ?>>
								<img src="<?php echo esc_attr( YITH_WAPO_URL ) . 'assets/img/addons-icons/premium.svg'; ?>" class="premium-badge">
								<div class="icon <?php echo esc_attr( $addon_type['slug'] ); ?>"><span class="wapo-icon wapo-icon-<?php echo esc_attr( $addon_type['slug'] ); ?>"></span></div>
								<span><?php echo esc_html( $addon_type['name'] ); ?></span>
							</a>
						<?php endforeach; ?>
						<div class="clear"></div>
					</div>

				</div>

			<?php endif; ?>

		</div>
	</div>

<?php endif; ?>
