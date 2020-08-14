<?php
/**
 * One-Click Checkout my account section
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// cont
$col = 0;
?>

<div class="clear"></div>

<?php do_action( 'yith_wocc_before_my_account_section' ); ?>

<div class="yith-wocc-account woocommerce">

	<h2><?php echo wp_kses_post( apply_filters( 'yith_wocc_account_section_title', esc_html__( 'One-Click Checkout', 'yith-woocommerce-one-click-checkout' ) ) ); ?></h2>

	<form method="post" class="yith-wocc-account-option">

		<p>
			<label for="yith-wocc-activate">
				<input type="checkbox" id="yith-wocc-activate" name="yith-wocc-activate" value="1" <?php
					if( isset( $value_options['activate'] ) ) checked( $value_options['activate'], '1' ) ?>/>
				<span>&nbsp;</span>
				<?php esc_html_e( 'Select this option if you want to activate the one-click checkout features', 'yith-woocommerce-one-click-checkout' ) ?>
			</label>
		</p>

		<?php if( $stripe_active ) : ?>
			<p>
				<label for="yith-wocc-use-stripe">
					<input type="checkbox" id="yith-wocc-use-stripe" name="yith-wocc-use-stripe" value="1"
						<?php if( isset( $value_options['use-stripe'] ) ) checked( $value_options['use-stripe'], '1' ) ?>/>
					<span>&nbsp;</span>
					<?php esc_html_e( 'Select this option if you want to use Stripe as default payment method. You need to have a default credit card saved.', 'yith-woocommerce-one-click-checkout' ) ?>
				</label>
			</p>
		<?php endif; ?>

		<input type="hidden" name="yith-wocc-save-option" value="1" />
		<button type="submit" class="button"><?php esc_html_e( 'Save', 'yith-woocommerce-one-click-checkout' ) ?></button>
	</form>

	<?php if( $enabled_shipping ) : ?>

	<div class="col12-set yith-wocc-custom-address">

		<h3><?php esc_html_e( 'Your custom shipping address', 'yith-woocommerce-one-click-checkout' ) ?></h3>

		<a href="<?php echo esc_attr( $edit_url ); ?>"  class="add-address-action button" title="<?php esc_html_e( 'Add custom shipping address', 'yit' ); ?>"><?php esc_html_e( 'Add new shipping address', 'yith-woocommerce-one-click-checkout' ) ?></a>

		<?php if( ! empty( $custom_address ) ) : ?>

			<?php foreach( $custom_address as $key => $address ) : ?>

				<?php if( ( $col % 2 ) == 0 ) : ?>
					<div class="clear"></div>
				<?php endif; ?>

				<div class="col-<?php echo ( ( $col % 2 ) == 0 ) ? 1 : 2; ?> address">

					<header class="title">

						<h4><?php echo esc_html__( 'Address', 'yith-woocommerce-one-click-checkout' ) . ' #' . ( $col + 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h4>

						<div class="action">
							<a href="<?php echo esc_url( add_query_arg( 'edit', $key, $edit_url ) ) ?>" class="address-action occ-icon-cog" title="<?php esc_html_e( 'Edit custom shipping address', 'yit' ); ?>"></a>

							<a href="<?php echo esc_url( add_query_arg( 'remove', $key, $remove_url ) ) ?>" class="address-action occ-icon-cancel" title="<?php esc_html_e( 'Remove custom shipping address', 'yit' ); ?>"></a>
						</div>

					</header>

					<address>
						<?php echo wp_kses_post( $address ); ?>
					</address>

				</div>

			<?php $col++; endforeach; ?>

		<?php else : ?>

			<p><?php esc_html_e( 'You haven\'t added any custom shipping address yet.', 'yith-woocommerce-one-click-checkout' ); ?></p>

		<?php endif; ?>

	</div>

	<?php endif; ?>

</div>

<?php do_action( 'yith_wocc_after_my_account_section' ); ?>
