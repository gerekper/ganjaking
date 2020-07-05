<?php

/**
 * WC_Checkout_Field_Editor class.
 */
class WC_Checkout_Field_Editor {
	public $locale_fields = array();

	/**
	 * __construct function.
	 *
	 * @return void
	 */
	public function __construct() {
		// Validation rules are controlled by the locale and can't be changed.
		$this->locale_fields = array(
			'billing_address_1',
			'billing_address_2',
			'billing_state',
			'billing_postcode',
			'billing_city',
			'billing_country',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_state',
			'shipping_postcode',
			'shipping_city',
			'order_comments',
		);

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id' ) );
		add_filter( 'woocommerce_debug_tools', array( $this, 'debug_button' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_data' ), 10, 2 );
		add_filter( 'woocommerce_default_address_fields', array( $this, 'set_default_address_field' ) );

		if ( ! empty( $_GET['dismiss_welcome'] ) ) {
			update_option( 'hide_checkout_field_editors_welcome_notice', 1 );
		}
	}

	/**
	 * Menu function.
	 *
	 * @return void
	 */
	public function menu() {
		$this->screen_id = add_submenu_page( 'woocommerce', __( 'WooCommerce Checkout Field Editor', 'woocommerce-checkout-field-editor' ),  __( 'Checkout Fields', 'woocommerce-checkout-field-editor' ), 'manage_woocommerce', 'checkout_field_editor', array( $this, 'the_editor' ) );

		add_action( 'admin_print_scripts-' . $this->screen_id, array( $this, 'scripts' ) );
	}

	/**
	 * Add_screen_id function.
	 *
	 * @param mixed $ids
	 * @return void
	 */
	public function add_screen_id( $ids ) {
		$ids[] = 'woocommerce_page_checkout_field_editor';
		$ids[] = sanitize_title( __( 'WooCommerce', 'woocommerce-checkout-field-editor' ) ) . '_page_checkout_field_editor';

		return $ids;
	}

	/**
	 * Scripts function.
	 *
	 * @return void
	 */
	public function scripts() {
		wp_enqueue_script( 'wc-checkout-fields', plugins_url( '/dist/js/admin.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-sortable', 'jquery-tiptip', 'woocommerce_admin' ), WC_CHECKOUT_FIELD_EDITOR_VERSION, true );
		wp_enqueue_style( 'wc-checkout-fields', plugins_url( '/dist/css/admin.css', dirname( __FILE__ ) ), array(), WC_CHECKOUT_FIELD_EDITOR_VERSION );

		if ( '' == get_option( 'hide_checkout_field_editors_welcome_notice' ) ) {
			wp_enqueue_style( 'woocommerce-activation', WC()->plugin_url() . '/assets/css/activation.css', array(), WC_CHECKOUT_FIELD_EDITOR_VERSION );
		}
	}

	/**
	 * Welcome function.
	 *
	 * @return void
	 */
	public function welcome() {
		wp_enqueue_style( 'woocommerce-activation', WC()->plugin_url() . '/assets/css/activation.css', array(), WC_CHECKOUT_FIELD_EDITOR_VERSION );
		?>
		<div id="message" class="woocommerce-message wc-connect updated">
			<div class="squeezer">
				<h4><?php esc_html_e( 'Checkout field editor is ready &#8211; Customize your forms below', 'woocommerce-checkout-field-editor' ); ?></h4>
				<p class="submit"><a class="button-primary" href="https://docs.woocommerce.com/document/checkout-field-editor/"><?php esc_html_e( 'Documentation', 'woocommerce-checkout-field-editor' ); ?></a> <a class="skip button-primary" href="<?php echo esc_url( add_query_arg( 'dismiss_welcome', true ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce-checkout-field-editor' ); ?></a></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Debug_button function.
	 *
	 * @param mixed $old
	 * @return void
	 */
	public function debug_button( $old ) {
		$new = array(
			'reset_checkout_fields' => array(
				'name'     => __( 'Checkout Fields', 'woocommerce-checkout-field-editor' ),
				'button'   => __( 'Reset Checkout Fields', 'woocommerce-checkout-field-editor' ),
				'desc'     => __( 'This tool will remove all customizations made to the checkout fields using the checkout field editor.', 'woocommerce-checkout-field-editor' ),
				'callback' => array( $this, 'debug_button_action' ),
			),
		);

		$tools = array_merge( $old, $new );

		return $tools;
	}

	/**
	 * Debug_button_action function.
	 *
	 * @return void
	 */
	public function debug_button_action() {
		delete_option( 'wc_fields_billing' );
		delete_option( 'wc_fields_shipping' );
		delete_option( 'wc_fields_additional' );

		echo '<div class="updated"><p>' . esc_html__( 'Checkout fields successfully reset', 'woocommerce-checkout-field-editor' ) . '</p></div>';
	}

	/**
	 * The_editor function.
	 *
	 * @return void
	 */
	public function the_editor() {
		$tabs = array( 'billing', 'shipping', 'additional' );

		$tab = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'billing';

		if ( ! empty( $_POST ) ) {
			echo $this->save_options( $tab );
		}

		echo '<div class="wrap woocommerce"><div class="icon32 icon32-attributes" id="icon-woocommerce"><br /></div>';
			echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';

			foreach ( $tabs as $key ) {
				$active = ( $key == $tab ) ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="' . esc_url( admin_url( 'admin.php?page=checkout_field_editor&tab=' . $key ) ) . '">' . ucwords( $key ) . ' ' . esc_html__( 'Fields', 'woocommerce-checkout-field-editor' ) . '</a>';
			}

			echo '</h2>';

			if ( get_option( 'hide_checkout_field_editors_welcome_notice' ) == '' ) {
				$this->welcome();
			}

			global $supress_field_modification;

			$supress_field_modification = true;
			$core_fields = array_keys( WC()->countries->get_address_fields( WC()->countries->get_base_country(), $tab . '_' ) );
			$core_fields[] = 'order_comments';
			$supress_field_modification = false;

			$validation_rules = apply_filters( 'woocommerce_custom_checkout_validation', array(
				'required' => __( 'Required', 'woocommerce-checkout-field-editor' ),
				'email'    => __( 'Email', 'woocommerce-checkout-field-editor' ),
				'number'   => __( 'Number', 'woocommerce-checkout-field-editor' ),
				'phone'    => __( 'Phone', 'woocommerce-checkout-field-editor' ),
			) );

			$field_types = apply_filters( 'woocommerce_custom_checkout_fields', array(
				'text'     => __( 'Text', 'woocommerce-checkout-field-editor' ),
				'password' => __( 'Password', 'woocommerce-checkout-field-editor' ),
				'textarea' => __( 'Textarea', 'woocommerce-checkout-field-editor' ),
				'select'   => __( 'Select', 'woocommerce-checkout-field-editor' ),

				// Custom ones.
				'multiselect' => __( 'Multiselect', 'woocommerce-checkout-field-editor' ),
				'radio'       => __( 'Radio', 'woocommerce-checkout-field-editor' ),
				'checkbox'    => __( 'Checkbox', 'woocommerce-checkout-field-editor' ),
				'date'        => __( 'Date Picker', 'woocommerce-checkout-field-editor' ),
				'heading'     => __( 'Heading', 'woocommerce-checkout-field-editor' ),
			) );

			$positions = apply_filters( 'woocommerce_custom_checkout_position', array(
				'form-row-first' => __( 'Left', 'woocommerce-checkout-field-editor' ),
				'form-row-wide'  => __( 'Full-width', 'woocommerce-checkout-field-editor' ),
				'form-row-last'  => __( 'Right', 'woocommerce-checkout-field-editor' ),
			) );

			$display_options = apply_filters( 'woocommerce_custom_checkout_display_options', array(
				'emails'     => __( 'Emails', 'woocommerce-checkout-field-editor' ),
				'view_order' => __( 'Order Detail Pages', 'woocommerce-checkout-field-editor' ),
			) );

			echo '<form method="post" id="mainform" action="">';
				?>
				<table id="wc_checkout_fields" class="widefat">
					<thead>
						<tr>
							<th width="1%" class="sort"></th>
							<th class="check-column"><input type="checkbox" /></th>
							<th><?php esc_html_e( 'Name', 'woocommerce-checkout-field-editor' ); ?></th>
							<th width="1%"><?php esc_html_e( 'Type', 'woocommerce-checkout-field-editor' ); ?></th>
							<th><?php esc_html_e( 'Label', 'woocommerce-checkout-field-editor' ); ?></th>
							<th><?php esc_html_e( 'Placeholder / Option Values', 'woocommerce-checkout-field-editor' ); ?></th>
							<th width="1%"><?php esc_html_e( 'Position', 'woocommerce-checkout-field-editor' ); ?></th>
							<?php if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) { ?>
								<th class="clear"><?php esc_html_e( 'Clear Row', 'woocommerce-checkout-field-editor' ); ?></th>
							<?php } ?>
							<th width="1%"><?php esc_html_e( 'Validation Rules', 'woocommerce-checkout-field-editor' ); ?></th>
							<th width="1%"><?php esc_html_e( 'Display Options', 'woocommerce-checkout-field-editor' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th colspan="4">
								<a class="button button-primary new_row" href="#"><?php esc_html_e( '+ Add field', 'woocommerce-checkout-field-editor' ); ?></a>
								<a class="button enable_row" href=""><?php esc_html_e( 'Enable Checked', 'woocommerce-checkout-field-editor' ); ?></a>
								<a class="button disable_row" href=""><?php esc_html_e( 'Disable/Remove Checked', 'woocommerce-checkout-field-editor' ); ?></a>
							</th>
							<th colspan="5"><p class="description"><?php
								switch ( $tab ) {
									case 'billing':
										echo wp_kses( __( 'The fields above show in the "billing information" section of the checkout page. <strong>Disabling core fields can cause unexpected results with some plugins; we recommend against this if possible.</strong>', 'woocommerce-checkout-field-editor' ), array( 'strong' => array() ) );
									break;
									case 'shipping':
										echo wp_kses( __( 'The fields above show in the "shipping information" section of the checkout page. <strong>Disabling core fields can cause unexpected results with some plugins; we recommend against this if possible.</strong>', 'woocommerce-checkout-field-editor' ), array( 'strong' => array() ) );
									break;
									case 'additional':
										esc_html_e( 'The fields above show beneath the billing and shipping sections on the checkout page.', 'woocommerce-checkout-field-editor' );
									break;
								}
							?></p></th>
						</tr>
						<tr class="new_row" style="display:none;">
							<td width="1%" class="sort ui-sortable-handle"> </td>
							<td class="check-column">
								<input type="checkbox" />
							</td>
							<td>
								<input type="text" class="input-text" name="new_field_name[0]" />
								<input type="hidden" name="field_name[0]" class="field_name" value="" />
								<input type="hidden" name="field_order[0]" class="field_order" value="" />
								<input type="hidden" name="field_enabled[0]" class="field_enabled" value="1" />
							</td>
							<td class="field-type">
								<?php if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) { ?>
									<select name="field_type[0]" class="field_type wc-enhanced-select" style="width:100px;">
										<?php foreach ( $field_types as $key => $type ) {
											echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $type ) . '</option>';
										}
										?>
									</select>
								<?php } else { ?>
									<select name="field_type[0]" class="field_type chosen_select enhanced" style="width:100px">
										<?php foreach ( $field_types as $key => $type ) {
											echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $type ) . '</option>';
										}
										?>
									</select>
								<?php } ?>
							</td>
							<td>
								<input type="text" class="input-text" name="field_label[0]" />
							</td>
							<td class="field-options">
								<input type="text" class="input-text placeholder" name="field_placeholder[0]" />
								<input type="text" class="input-text options" name="field_options[0]" placeholder="<?php esc_attr_e( 'Pipe (|) separate options.', 'woocommerce-checkout-field-editor' ); ?>" />
							</td>
							<td>
								<?php if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) { ?>
									<select name="field_position[0]" class="field_position wc-enhanced-select" style="width:100px;">
										<?php foreach ( $positions as $key => $type ) {
											echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $type ) . '</option>';
										}
										?>
									</select>
								<?php } else { ?>
									<select name="field_position[0]" class="field_position chosen_select enhanced" style="width:100px">
										<?php foreach ( $positions as $key => $type ) {
											echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $type ) . '</option>';
										}
										?>
									</select>
								<?php } ?>
							</td>
							<?php if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) { ?>
								<td class="clear">
									<input type="checkbox" name="field_clear[0]" />
								</td>
							<?php } ?>
							<td>
								<?php if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) { ?>
									<select name="field_validation[0][]" class="wc-enhanced-select" style="width:200px;" multiple="multiple">
										<?php foreach ( $validation_rules as $key => $rule ) {
											echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $rule ) . '</option>';
										}
										?>
									</select>
								<?php } else { ?>
									<select name="field_validation[0][]" class="chosen_select enhanced" multiple="multiple" style="width: 200px;">
										<?php
											foreach ( $validation_rules as $key => $rule ) {
												echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $rule ) . '</option>';
											}
										?>
									</select>
								<?php } ?>
							</td>
							<td>
								<?php if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) { ?>
									<select name="field_display_options[0][]" class="wc-enhanced-select" style="width:150px;" multiple="multiple">
										<?php foreach ( $display_options as $key => $option ) {
											echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
										}
										?>
									</select>
								<?php } else { ?>
									<select name="field_display_options[0][]" class="chosen_select enhanced" multiple="multiple" style="width: 150px;">
										<?php
											foreach ( $display_options as $key => $option ) {
												echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
											}
										?>
									</select>
								<?php } ?>
							</td>
						</tr>
					</tfoot>
					<tbody id="checkout_fields">
						<?php

						$i = 0;

						foreach ( $this->get_fields( $tab ) as $name => $options ) :

						$i++;

						if ( ! isset( $options['placeholder'] ) ) {
							$options['placeholder'] = '';
						}

						if ( ! isset( $options['validate'] ) ) {
							$options['validate'] = array();
						}

						if ( ! isset( $options['display_options'] ) ) {
							$options['display_options'] = array();
						}

						if ( ! isset( $options['enabled'] ) || $options['enabled'] ) {
							$options['enabled'] = '1';
						} else {
							$options['enabled'] = '0';
						}

						if ( ! isset( $options['type'] ) ) {
							$options['type'] = 'text';
						}
						?>
						<tr class="<?php if ( in_array( $name, $core_fields ) ) echo 'core '; if ( ! $options[ 'enabled' ] ) echo 'disabled '; ?>" data-field-name="<?php echo esc_attr( $name ); ?>">
							<td width="1%" class="sort ui-sortable-handle"> </td>
							<td class="check-column">
								<input type="checkbox" />
							</td>
							<td>
								<?php if ( ! in_array( $name, $core_fields ) ) : ?>
									<input type="text" class="input-text" name="new_field_name[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $name ); ?>" />
									<input type="hidden" name="field_name[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $name ); ?>" />
								<?php else : ?>
									<strong class="core-field"><?php echo $name; ?></strong>
									<input type="hidden" name="field_name[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $name ); ?>" />
								<?php endif; ?>

								<input type="hidden" name="field_order[<?php echo esc_attr( $i ); ?>]" class="field_order" value="<?php echo esc_attr( $i ); ?>" />
								<input type="hidden" name="field_enabled[<?php echo esc_attr( $i ); ?>]" class="field_enabled" value="<?php echo esc_attr( $options[ 'enabled' ] ); ?>" />
							</td>
							<td class="field-type">
								<?php if ( in_array( $name, array(
									'billing_address_1',
									'billing_state',
									'billing_city',
									'billing_country',
									'billing_postcode',
									'shipping_address_1',
									'shipping_state',
									'shipping_city',
									'shipping_country',
									'shipping_postcode'
								) ) ) : ?>
									<span class="na tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'This field is address locale dependent and cannot be modified.', 'woocommerce-checkout-field-editor' ) ); ?>">&ndash;</span>
								<?php elseif ( in_array( $name, array( 'order_comments' ) ) ) : ?>
									<span class="na tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'This field cannot be modified.', 'woocommerce-checkout-field-editor' ) ); ?>">&ndash;</span>
								<?php else : ?>
									<?php if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) { ?>
										<select name="field_type[<?php echo esc_attr( $i ); ?>]" class="field_type wc-enhanced-select" style="width:100px">
											<?php foreach ( $field_types as $key => $type ) {
												echo '<option value="' . esc_attr( $key ) . '" ' . selected( $options['type'], $key, false ) . '>' . esc_html( $type ) . '</option>';
											}
											?>
										</select>
									<?php } else { ?>
										<select name="field_type[<?php echo esc_attr( $i ); ?>]" class="field_type chosen_select" style="width:100px">
											<?php foreach ( $field_types as $key => $type ) {
												echo '<option value="' . esc_attr( $key ) . '" ' . selected( $options['type'], $key, false ) . '>' . esc_html( $type ) . '</option>';
											}
											?>
										</select>
									<?php } ?>
								<?php endif; ?>
							</td>
							<td style="width:150px;">
								<?php if ( in_array( $name, array(
									'billing_address_1',
									'billing_state',
									'billing_city',
									'billing_postcode',
									'shipping_address_1',
									'shipping_state',
									'shipping_city',
									'shipping_postcode',
								) ) ) : ?>
									<span class="na tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'This field is address locale dependent and cannot be modified.', 'woocommerce-checkout-field-editor' ) ); ?>">&ndash;</span>
								<?php else : ?>
									<input type="text" class="input-text" name="field_label[<?php echo esc_attr( $i ); ?>]" value="<?php echo isset( $options['label'] ) ? esc_attr( $options['label'] ) : ''; ?>" />
								<?php endif; ?>
							</td>
							<td class="field-options" style="width:150px;">
								<?php if ( in_array( $name, array(
									'billing_address_1',
									'billing_state',
									'billing_city',
									'billing_country',
									'billing_postcode',
									'shipping_address_1',
									'shipping_state',
									'shipping_city',
									'shipping_country',
									'shipping_postcode',
								) ) ) : ?>
									<span class="na tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'This field is address locale dependent and cannot be modified.', 'woocommerce-checkout-field-editor' ) ); ?>">&ndash;</span>
								<?php else : ?>
									<input type="text" class="input-text placeholder" name="field_placeholder[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $options['placeholder'] ); ?>" />
									<input type="text" class="input-text options" name="field_options[<?php echo esc_attr( $i ); ?>]" placeholder="<?php esc_attr_e( 'Pipe (|) separate options.', 'woocommerce-checkout-field-editor' ); ?>" value="<?php if ( ! empty( $options['placeholder'] ) && ! empty( $options['options'] ) ) echo $options['placeholder'] . ' || '; if ( isset( $options['options'] ) ) echo implode( ' | ', $options['options'] ); ?>" />
									<span class="na">&ndash;</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) { ?>
									<select name="field_position[<?php echo esc_attr( $i ); ?>]" class="field_position wc-enhanced-select" style="width:100px">
										<?php foreach ( $positions as $key => $type ) {
											echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $options['class'] ), true, false ) . '>' . esc_html( $type ) . '</option>';
										}
										?>
									</select>
								<?php } else { ?>
									<select name="field_position[<?php echo esc_attr( $i ); ?>]" class="field_position chosen_select" style="width:100px">
										<?php foreach ( $positions as $key => $type ) {
											echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $options['class'] ), true, false ) . '>' . esc_html( $type ) . '</option>';
										}
										?>
									</select>
								<?php } ?>
							</td>
							<?php if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) { ?>
								<td class="clear">
									<input type="checkbox" name="field_clear[<?php echo esc_attr( $i ); ?>]" <?php checked( isset( $options['clear'] ) && $options['clear'], true ); ?> />
								</td>
							<?php } ?>
							<td class="field-validation">
								<?php if ( in_array( $name, $this->locale_fields ) ) : ?>
									&ndash;
								<?php else : ?>
								<div class="options">
									<?php if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) { ?>
										<select name="field_validation[<?php echo esc_attr( $i ); ?>][]" class="wc-enhanced-select" multiple="multiple" style="width: 200px;">
											<?php
												foreach ( $validation_rules as $key => $rule ) {
													echo '<option value="' . esc_attr( $key ) . '" ' . selected( ! empty( $options[ $key ] ) || in_array( $key, $options[ 'validate' ] ), true, false ) . '>' . esc_html( $rule ) . '</option>';
												}
											?>
										</select>
									<?php } else { ?>
										<select name="field_validation[<?php echo esc_attr( $i ); ?>][]" class="chosen_select" multiple="multiple" style="width: 200px;">
											<?php
												foreach ( $validation_rules as $key => $rule ) {
													echo '<option value="' . esc_attr( $key ) . '" ' . selected( ! empty( $options[ $key ] ) || in_array( $key, $options[ 'validate' ] ), true, false ) . '>' . esc_html( $rule ) . '</option>';
												}
											?>
										</select>
									<?php } ?>
								</div>
								<span class="na">&ndash;</span>
								<?php endif; ?>
							</td>
							<td class="field-validation">
								<?php if ( in_array( $name, $core_fields ) ) : ?>
									&ndash;
								<?php else : ?>
								<div class="options">
									<?php if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) { ?>
										<select name="field_display_options[<?php echo esc_attr( $i ); ?>][]" class="wc-enhanced-select" multiple="multiple" style="width: 150px;">
											<?php
												foreach ( $display_options as $key => $option ) {
													echo '<option value="' . esc_attr( $key ) . '" ' . selected( ! empty( $options[ 'display_options' ] ) && in_array( $key, $options[ 'display_options' ] ), true, false ) . '>' . esc_html( $option ) . '</option>';
												}
											?>
										</select>
									<?php } else { ?>
										<select name="field_display_options[<?php echo esc_attr( $i ); ?>][]" class="chosen_select" multiple="multiple" style="width: 150px;">
											<?php
												foreach ( $display_options as $key => $option ) {
													echo '<option value="' . esc_attr( $key ) . '" ' . selected( ! empty( $options[ 'display_options' ] ) && in_array( $key, $options[ 'display_options' ] ), true, false ) . '>' . esc_html( $option ) . '</option>';
												}
											?>
										</select>
									<?php } ?>
								</div>
								<span class="na">&ndash;</span>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php
			echo '<p class="submit"><input type="submit" class="button-primary" value="' . esc_attr__( 'Save Changes', 'woocommerce-checkout-field-editor' ) . '" /></p>';
			echo '</form>';
		echo '</div>';
	}

	/**
	 * Get_fields function.
	 *
	 * @param mixed $key
	 * @return void
	 */
	public static function get_fields( $key ) {
		$fields = array_filter( get_option( 'wc_fields_' . $key, array() ) );

		if ( empty( $fields ) || count( $fields ) == 0 ) {
			if ( 'billing' === $key || 'shipping' === $key ) {
				$fields = WC()->countries->get_address_fields( WC()->countries->get_base_country(), $key . '_' );

			} elseif ( 'additional' === $key ) {
				$fields = array(
					'order_comments' => array(
						'type'        => 'textarea',
						'class'       => array('notes'),
						'label'       => __( 'Order Notes', 'woocommerce-checkout-field-editor' ),
						'placeholder' => _x( 'Notes about your order, e.g. special notes for delivery.', 'placeholder', 'woocommerce-checkout-field-editor' ),
					),
				);
			}
		}

		return $fields;
	}

	/**
	 * List of restricted field names that shouldn't be used.
	 *
	 * @since 1.5.7
	 * @version 1.5.7
	 * @return array
	 */
	public function restricted_field_names() {
		return apply_filters( 'wc_checkout_field_editor_restricted_field_names', array(
			'role',
		) );
	}

	/**
	 * Sanitize field names.
	 *
	 * @since 1.5.7
	 * @version 1.5.7
	 * @param string $field_name
	 * @return string
	 */
	public function sanitize_field_name( $field_name ) {
		if ( in_array( $field_name, $this->restricted_field_names(), true ) ) {
			return 'cf_' . $field_name; // "cf" just stands for custom field.
		}

		return $field_name;
	}

	/**
	 * Save_options function.
	 *
	 * @param mixed $fields
	 * @param mixed $tab
	 * @return void
	 */
	public function save_options( $tab ) {
		$o_fields              = $this->get_fields( $tab );
		$fields                = $o_fields;
		$core_fields           = array_keys( WC()->countries->get_address_fields( WC()->countries->get_base_country(), $tab . '_' ) );
		$core_fields[]         = 'order_comments';
		$field_names           = ! empty( $_POST['field_name'] ) ? $_POST['field_name'] : array();
		$new_field_names       = ! empty( $_POST['new_field_name'] ) ? $_POST['new_field_name'] : array();
		$field_labels          = ! empty( $_POST['field_label'] ) ? $_POST['field_label'] : array();
		$field_order           = ! empty( $_POST['field_order'] ) ? $_POST['field_order'] : array();
		$field_enabled         = ! empty( $_POST['field_enabled'] ) ? $_POST['field_enabled'] : array();
		$field_type            = ! empty( $_POST['field_type'] ) ? $_POST['field_type'] : array();
		$field_placeholder     = ! empty( $_POST['field_placeholder'] ) ? $_POST['field_placeholder'] : array();
		$field_options         = ! empty( $_POST['field_options'] ) ? $_POST['field_options'] : array();
		$field_position        = ! empty( $_POST['field_position'] ) ? $_POST['field_position'] : array();

		// Backwards compatibility.
		$field_options = str_replace( '| |', '||', $field_options );

		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			$field_clear           = ! empty( $_POST['field_clear'] ) ? $_POST['field_clear'] : array();
		}

		$field_validation      = ! empty( $_POST['field_validation'] ) ? $_POST['field_validation'] : array();
		$field_display_options = ! empty( $_POST['field_display_options'] ) ? $_POST['field_display_options'] : array();
		$max                   = max( array_map( 'absint', array_keys( $field_names ) ) );

		for ( $i = 0; $i <= $max; $i++ ) {
			$name     = empty( $field_names[ $i ] ) ? '' : urldecode( sanitize_title( wc_clean( stripslashes( $field_names[ $i ] ) ) ) );
			$new_name = empty( $new_field_names[ $i ] ) ? '' : urldecode( sanitize_title( wc_clean( stripslashes( $new_field_names[ $i ] ) ) ) );

			// Check reserved names.
			if ( $new_name && in_array( $new_name, array(
				'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
				'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
			) ) ) {
				continue;
			}

			// Sanitize field names for restriction.
			$name     = $this->sanitize_field_name( $name );
			$new_name = $this->sanitize_field_name( $new_name );

			if ( $name && $new_name && $new_name !== $name ) {
				if ( isset( $fields[ $name ] ) ) {
					$fields[ $new_name ] = $fields[ $name ];
				} else {
					$fields[ $new_name ] = array();
				}

				unset( $fields[ $name ] );

				$name = $new_name;
			} else {
				$name = $name ? $name : $new_name;
			}

			if ( ! $name ) {
				continue;
			}

			if ( ! isset( $fields[ $name ] ) ) {
				$fields[ $name ] = array();
			}

			$o_type                     = isset( $o_fields[ $name ]['type'] ) ? $o_fields[ $name ]['type'] : 'text';

			$fields[ $name ]['type']    = empty( $field_type[ $i ] ) ? $o_type : wc_clean( $field_type[ $i ] );
			$fields[ $name ]['label']   = empty( $field_labels[ $i ] ) ? '' : wp_kses_post( trim( stripslashes( $field_labels[ $i ] ) ) );

			if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
				$fields[ $name ]['clear']   = empty( $field_clear[ $i ] ) ? false : true;
			}

			$maybe_placeholder = empty( $field_options[ $i ] ) ? array() : array_map( 'wc_clean', array_map( 'stripslashes', explode( '||', $field_options[ $i ] ) ) );
			if ( count( $maybe_placeholder ) > 1 ) {
				$field_placeholder[ $i ] = array_shift( $maybe_placeholder );
				$field_options[ $i ]     = array_shift( $maybe_placeholder );
			}

			$fields[ $name ]['options'] = empty( $field_options[ $i ] ) ? array() : array_map( 'wc_clean', array_map( 'stripslashes', explode( '|', $field_options[ $i ] ) ) );

			// Keys = values
			if ( ! empty( $fields[ $name ]['options'] ) ) {
				$fields[ $name ]['options'] = array_combine( $fields[ $name ]['options'], $fields[ $name ]['options'] );
			}

			$order_text = version_compare( WC_VERSION, '3.0.0', '<' ) ? 'order' : 'priority';

			if ( empty( $field_placeholder[ $i ] ) ) {
				if ( 'select' === $fields[ $name ]['type'] ) {
					$fields[ $name ]['placeholder'] = __( 'Select option', 'woocommerce-checkout-field-editor' );
				} elseif ( 'multiselect' === $fields[ $name ]['type'] ) {
					$fields[ $name ]['placeholder'] = __( 'Select some options', 'woocommerce-checkout-field-editor' );
				} else {
					$fields[ $name ]['placeholder'] = '';
				}
			} else {
				$fields[ $name ]['placeholder'] = wc_clean( stripslashes( $field_placeholder[ $i ] ) );
			}

			$fields[ $name ][ $order_text ] = empty( $field_order[ $i ] ) ? '' : wc_clean( $field_order[ $i ] ) * 10;
			$fields[ $name ]['enabled']     = empty( $field_enabled[ $i ] ) ? false : true;

			// Non-locale.
			if ( ! in_array( $name, $this->locale_fields ) ) {
				$fields[ $name ]['validate']    = empty( $field_validation[ $i ] ) ? array() : $field_validation[ $i ];

				// Require.
				if ( in_array( 'required', $fields[ $name ]['validate'] ) ) {
					$fields[ $name ]['required'] = true;
				} else {
					$fields[ $name ]['required'] = false;
				}
			}

			// Custom.
			if ( ! in_array( $name, array(
				'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
				'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
			) ) ) {
				$fields[ $name ]['custom'] = true;

				$fields[ $name ]['display_options'] = empty( $field_display_options[ $i ] ) ? array() : $field_display_options[ $i ];
			} else {
				$fields[ $name ]['custom'] = false;
			}

			// Position.
			$classes = isset( $o_fields[ $name ]['class'] ) ? $o_fields[ $name ]['class'] : array();
			$classes = array_diff( $classes, array( 'form-row-first', 'form-row-last', 'form-row-wide' ) );

			if ( isset( $field_position[ $i ] ) ) {
				$classes[] = $field_position[ $i ];
			}

			$fields[ $name ]['class'] = $classes;

			// Remove.
			if ( $fields[ $name ]['custom'] && ! $fields[ $name ]['enabled'] ) {
				unset( $fields[ $name ] );
			}
		}

		uasort( $fields, array( $this, 'sort_fields' ) );

		$result = update_option( 'wc_fields_' . $tab, $fields );

		if ( true === (bool) $result ) {
			echo '<div class="updated"><p>' . esc_html__( 'Your changes were saved.', 'woocommerce-checkout-field-editor' ) . '</p></div>';
		} else {
			echo '<div class="error"><p> ' . esc_html__( 'Your changes were not saved due to an error (or you made none!).', 'woocommerce-checkout-field-editor' ) . '</p></div>';
		}
	}

	/**
	 * Sort_fields function.
	 *
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
	 */
	public function sort_fields( $a, $b ) {
		$order_text = version_compare( WC_VERSION, '3.0.0', '<' ) ? 'order' : 'priority';

		if ( ! isset( $a[ $order_text ] ) || $a[ $order_text ] == $b[ $order_text ] ) {
			return 0;
		}

		return ( $a[ $order_text ] < $b[ $order_text ] ) ? -1 : 1;
	}

	/**
	 * Save_data function.
	 *
	 * @param mixed $id
	 * @param mixed $posted
	 * @return void
	 */
	public function save_data( $order_id, $posted ) {
		$types = array( 'billing', 'shipping', 'additional' );

		foreach ( $types as $type ) {
			$fields = $this->get_fields( $type );

			foreach ( $fields as $name => $field ) {
				if ( empty( $posted[ $name ] ) ) {
					continue;
				}

				if ( ! empty( $field['custom'] ) ) {
					$value = wc_clean( $posted[ $name ] );

					if ( $value ) {
						update_post_meta( $order_id, $name, $value );
					}
				}
			}
		}
	}

	/**
	 * Sets any default address field.
	 *
	 * @since 1.5.35
	 * @param array $fields Default fields.
	 * @return $fields Modified fields.
	 */
	public function set_default_address_field( $fields ) {
		remove_filter( 'woocommerce_default_address_fields', array( $this, 'set_default_address_field' ) );
		$tabs = array( 'billing', 'shipping' );

		foreach ( $tabs as $tab ) {
			foreach ( $this->get_fields( $tab ) as $name => $options ) {
				if ( 'billing_address_2' === $name ) {
					$fields['address_2']['placeholder'] = $options['placeholder'];
				}
			}
		}

		add_filter( 'woocommerce_default_address_fields', array( $this, 'set_default_address_field' ) );

		return $fields;
	}
}
