<?php
/**
 * State metabox
 *
 * @version 8.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render_on_toggle = 'closed' === $toggle && ! empty( $state_data[ 'state_id' ] );

// Add enabled key if not exists.
if ( ! isset( $state_data[ 'enabled' ] ) ) {
	// By default active.
	$state_data[ 'enabled' ] = 'yes';
}

// Check if state is new from ajax call.
$is_ajax = isset( $state_data[ 'is_ajax' ] ) && true === $state_data[ 'is_ajax' ];

?>
<div class="<?php echo $is_ajax ? 'bto_state--added ' : ''; ?>bto_state wc-metabox <?php echo esc_attr( $toggle ); ?>" rel="<?php echo isset( $state_data[ 'position' ] ) ? esc_attr( $state_data[ 'position' ] ) : ''; ?>">
	<h3 class="bto_state_handle">
		<strong class="state_name">
			<?php
			$toggle_class = ( 'yes' === $state_data[ 'enabled' ] ) ? 'woocommerce-input-toggle--enabled' : 'woocommerce-input-toggle--disabled';
			?>
			<span class="woocommerce-input-toggle <?php echo esc_attr( $toggle_class ); ?>"></span>
			<span class="state_name_inner">
				<?php
					if ( ! empty( $state_data[ 'title' ] ) ) {
						echo esc_html( $state_data[ 'title' ] );
					}
				?>
			</span>
		</strong>
		<div class="handle">

			<input type="hidden" name="bto_state_data[<?php echo esc_attr( $id ); ?>][enabled]" class="enabled" value="<?php echo esc_attr( $state_data[ 'enabled' ] ); ?>"/>
			<input type="hidden" name="bto_state_data[<?php echo esc_attr( $id ); ?>][position]" class="state_position" value="<?php echo isset( $state_data[ 'position' ] ) ? esc_attr( $state_data[ 'position' ] ) : esc_attr( $id ); ?>"/>

			<?php
				if ( ! empty( $state_data[ 'state_id' ] ) ) {
					?><input type="hidden" name="bto_state_data[<?php echo esc_attr( $id ); ?>][scenario_id]" class="scenario_id" value="<?php echo esc_attr( $state_data[ 'state_id' ] ); ?>"/><?php
				}
			?>
			<span class="handle-loading"></span>
			<div class="handle-item toggle-item" aria-label="<?php esc_attr_e( 'Click to toggle', 'woocommerce' ); ?>"></div>
			<div class="handle-item sort-item" aria-label="<?php esc_attr_e( 'Drag and drop to set order', 'woocommerce-composite-products' ); ?>"></div>
			<a href="#" class="remove_row delete"><?php esc_html_e( 'Remove', 'woocommerce' ); ?></a>
		</div>
	</h3><?php

	ob_start();
	include( WC_CP_ABSPATH . 'includes/admin/meta-boxes/views/html-state-contents.php' );
	$state_content = ob_get_clean();

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	?><div class="bto_state_data wc-metabox-content" data-state_content="<?php echo $render_on_toggle ? htmlspecialchars( $state_content ) : ''; ?>"><?php
		echo $render_on_toggle ? '' : $state_content;
		// phpcs:enable
	?></div>
</div>
