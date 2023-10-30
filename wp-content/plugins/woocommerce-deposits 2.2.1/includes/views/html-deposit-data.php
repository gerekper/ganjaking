<?php
/**
 * Deposits form for product edit screen.
 *
 * @package woocommerce-deposits
 */

global $post;

// Set up variations suffixes.
if ( isset( $loop ) && isset( $variation ) ) {
	$id_suffix   = '_' . $loop;
	$name_suffix = '[' . $loop . ']';
	$class       = 'woocommerce_variation_deposits form-row';
	$product     = $variation;
	$data_type   = 'variation';
} else {
	$id_suffix   = '';
	$name_suffix = '';
	$class       = 'panel woocommerce_options_panel';
	$product     = $post;
	$data_type   = 'product';
}

?>

<div id="deposits<?php echo esc_attr( $id_suffix ); ?>" class="<?php echo esc_attr( $class ); ?>" data-type="<?php echo esc_attr( $data_type ); ?>">
	<div class="options_group">
		<?php

		if ( isset( $loop ) ) {
			$inherit_wc_deposit_enabled = esc_html__( 'Inherit product settings', 'woocommerce-deposits' );
		} else {
			$inherit_wc_deposit_enabled = esc_html__( 'Inherit storewide settings', 'woocommerce-deposits' );
		}

		$inherit_wc_deposit_type          = $inherit_wc_deposit_enabled;
		$inherit_wc_deposit_selected_type = $inherit_wc_deposit_enabled;

		if ( ! isset( $variation ) ) {
			switch ( get_option( 'wc_deposits_default_type', 'percent' ) ) {
				case 'percent':
					$inherit_wc_deposit_type .= ' (' . esc_html__( 'percent', 'woocommerce-deposits' ) . ')';
					break;
				case 'fixed':
					$inherit_wc_deposit_type .= ' (' . esc_html__( 'fixed amount', 'woocommerce-deposits' ) . ')';
					break;
				case 'plan':
					$inherit_wc_deposit_type .= ' (' . esc_html__( 'payment plan', 'woocommerce-deposits' ) . ')';
					break;
				case 'none':
					$inherit_wc_deposit_type .= ' (' . esc_html__( 'none', 'woocommerce-deposits' ) . ')';
					break;
			}
			switch ( get_option( 'wc_deposits_default_enabled', 'no' ) ) {
				case 'optional':
					$inherit_wc_deposit_enabled .= ' (' . esc_html__( 'yes, optional', 'woocommerce-deposits' ) . ')';
					break;
				case 'forced':
					$inherit_wc_deposit_enabled .= ' (' . esc_html__( 'yes, required', 'woocommerce-deposits' ) . ')';
					break;
				case 'no':
					$inherit_wc_deposit_enabled .= ' (' . esc_html__( 'no', 'woocommerce-deposits' ) . ')';
					break;
			}
			switch ( get_option( 'wc_deposits_default_selected_type', 'deposit' ) ) {
				case 'deposit':
					$inherit_wc_deposit_selected_type .= ' (' . esc_html__( 'pay deposit', 'woocommerce-deposits' ) . ')';
					break;
				case 'full':
					$inherit_wc_deposit_selected_type .= ' (' . esc_html__( 'pay in full', 'woocommerce-deposits' ) . ')';
					break;
			}
		}

		woocommerce_wp_select(
			array(
				'id'            => '_wc_deposit_enabled' . $id_suffix,
				'name'          => '_wc_deposit_enabled' . $name_suffix,
				'label'         => __( 'Enable Deposits', 'woocommerce-deposits' ),
				/* translators: Link to storewide settings */
				'description'   => ! isset( $loop ) ? sprintf( __( 'Allow customers to pay a deposit for this product. <br> <a href="%s" target="_blank">Manage storewide settings</a>', 'woocommerce-deposits' ), admin_url( 'admin.php?page=wc-settings&tab=products&section=deposits' ) ) : '',
				'options'       => array(
					''         => $inherit_wc_deposit_enabled,
					'optional' => __( 'Yes - deposits are optional', 'woocommerce-deposits' ),
					'forced'   => __( 'Yes - deposits are required', 'woocommerce-deposits' ),
					'no'       => __( 'No', 'woocommerce-deposits' ),
				),
				'style'         => 'min-width:50%;',
				'desc_tip'      => false,
				'wrapper_class' => '_wc_deposit_enabled_field',
				'class'         => 'select _wc_deposit_enabled',
				'value'         => get_post_meta( $product->ID, '_wc_deposit_enabled', true ),
			)
		);

		woocommerce_wp_select(
			array(
				'id'            => '_wc_deposit_type' . $id_suffix,
				'name'          => '_wc_deposit_type' . $name_suffix,
				'label'         => __( 'Deposit Type', 'woocommerce-deposits' ),
				'description'   => __( 'Choose how customers can pay for this product using a deposit.', 'woocommerce-deposits' ),
				'options'       => array(
					''        => $inherit_wc_deposit_type,
					'percent' => __( 'Percentage', 'woocommerce-deposits' ),
					'fixed'   => __( 'Fixed Amount', 'woocommerce-deposits' ),
					'plan'    => __( 'Payment Plan', 'woocommerce-deposits' ),
				),
				'style'         => 'min-width:50%;',
				'desc_tip'      => true,
				'wrapper_class' => '_wc_deposit_type_field',
				'class'         => 'select _wc_deposit_type',
				'value'         => get_post_meta( $product->ID, '_wc_deposit_type', true ),
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id'            => '_wc_deposit_multiple_cost_by_booking_persons' . $id_suffix,
				'name'          => '_wc_deposit_multiple_cost_by_booking_persons' . $name_suffix,
				'label'         => __( 'Booking Persons', 'woocommerce-deposits' ),
				'description'   => __( 'Multiply fixed deposits by the number of persons booking', 'woocommerce-deposits' ),
				'wrapper_class' => '_wc_deposit_multiple_cost_by_booking_persons_field show_if_booking',
				'class'         => '_wc_deposit_multiple_cost_by_booking_persons',
				'value'         => get_post_meta( $product->ID, '_wc_deposit_multiple_cost_by_booking_persons', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'            => '_wc_deposit_amount' . $id_suffix,
				'name'          => '_wc_deposit_amount' . $name_suffix,
				'label'         => __( 'Deposit Amount', 'woocommerce-deposits' ),
				'placeholder'   => wc_format_localized_price( 0 ),
				'description'   => __( 'The amount of deposit needed. Do not include currency or percent symbols.', 'woocommerce-deposits' ),
				'data_type'     => 'price',
				'desc_tip'      => true,
				'wrapper_class' => '_wc_deposit_amount_field',
				'class'         => '_wc_deposit_amount',
				'value'         => get_post_meta( $product->ID, '_wc_deposit_amount', true ),
			)
		);

		woocommerce_wp_select(
			array(
				'id'            => '_wc_deposit_selected_type' . $id_suffix,
				'name'          => '_wc_deposit_selected_type' . $name_suffix,
				'label'         => __( 'Default Deposit Selected Type', 'woocommerce-deposits' ),
				'description'   => __( 'Choose the default selected type of payment on page load.', 'woocommerce-deposits' ),
				'options'       => array(
					''        => $inherit_wc_deposit_selected_type,
					'deposit' => __( 'Pay Deposit', 'woocommerce-deposits' ),
					'full'    => __( 'Pay in Full', 'woocommerce-deposits' ),
				),
				'style'         => 'min-width:50%;',
				'desc_tip'      => true,
				'wrapper_class' => '_wc_deposit_selected_type_field',
				'class'         => 'select _wc_deposit_selected_type',
				'value'         => get_post_meta( $product->ID, '_wc_deposit_selected_type', true ),
			)
		);

		?>

		<input type="hidden" class="_wc_deposits_default_enabled_field" value="<?php echo esc_attr( get_option( 'wc_deposits_default_enabled', 'no' ) ); ?>" />
		<input type="hidden" class="_wc_deposits_default_type_field" value="<?php echo esc_attr( get_option( 'wc_deposits_default_type', 'percent' ) ); ?>" />
		<input type="hidden" class="_wc_deposits_default_plans_field" value="<?php echo esc_attr( implode( ',', get_option( 'wc_deposits_default_plans', array() ) ) ); ?>" />
		<input type="hidden" class="_wc_deposits_default_amount_field" value="<?php echo esc_attr( get_option( 'wc_deposits_default_amount' ) ); ?>" />
		<input type="hidden" class="_wc_deposits_default_selected_type_field" value="<?php echo esc_attr( get_option( 'wc_deposits_default_selected_type', 'deposit' ) ); ?>" />

		<p class="form-field _wc_deposit_payment_plans_field">
			<label for="_wc_deposit_payment_plans<?php echo esc_attr( $id_suffix ); ?>"><?php esc_html_e( 'Payment Plans', 'woocommerce-deposits' ); ?></label>
			<?php
			$plan_ids              = WC_Deposits_Plans_Manager::get_plan_ids();
			$default_payment_plans = get_option( 'wc_deposits_default_plans', array() );
			if ( ! $plan_ids ) {
				echo esc_html__( 'You have not created any payment plans yet.', 'woocommerce-deposits' );
				echo ' <a href="' . esc_url( admin_url( 'edit.php?post_type=product&page=deposit_payment_plans' ) ) . '" class="button button-small" target="_blank">' . esc_html__( 'Create a Payment Plan', 'woocommerce-deposits' ) . '</a>';
			} else {
				$values = (array) get_post_meta( $product->ID, '_wc_deposit_payment_plans', true );
				?>
				<select id="_wc_deposit_payment_plans<?php echo esc_attr( $id_suffix ); ?>" name="_wc_deposit_payment_plans<?php echo esc_attr( $name_suffix ); ?>[]" class="wc-enhanced-select _wc_deposit_payment_plans" style="min-width: 50%;" multiple="multiple" data-plans-order="<?php echo esc_attr( join( ',', $values ) ); ?>" placeholder="<?php esc_attr_e( 'Choose some plans', 'woocommerce-deposits' ); ?>">
				<?php
				foreach ( $plan_ids as $plan_id => $name ) {
					echo '<option value="' . esc_attr( $plan_id ) . '" ' . selected( in_array( $plan_id, $values, true ), true, false ) . '>' . esc_attr( $name ) . '</option>';
				}
				?>
				</select><?php echo wc_help_tip( __( 'Choose which payment plans customers can use for this product.', 'woocommerce-deposits' ) ); ?>
				<?php
				if ( ! empty( $default_payment_plans ) ) {
					$default_payment_plan_string = '';
					foreach ( $default_payment_plans as $plan_id ) {
						$default_payment_plan_string .= $plan_ids[ $plan_id ] . ',';
					}
					$default_payment_plan_string = rtrim( $default_payment_plan_string, ',' );

					if ( ! isset( $variation ) ) {
						/* translators: default payment plan */
						echo '<span class="description">' . sprintf( esc_html__( '"%s" will be used if no payment plan is selected.', 'woocommerce-deposits' ), '<em>' . esc_html( $default_payment_plan_string ) . '</em>' ) . '</span>';
					} else {
						/* translators: default payment plan */
						echo '<span class="description">' . sprintf( esc_html__( '"%s" will be used if no payment plan is selected.', 'woocommerce-deposits' ), '<em class="variation-default-plans-placeholder"></em>' ) . '</span>';
					}
				}
			}
			?>
		</p>
	</div>
</div>
