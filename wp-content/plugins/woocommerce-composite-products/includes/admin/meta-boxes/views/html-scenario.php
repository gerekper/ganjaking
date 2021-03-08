<?php
/**
 * Scenario metabox
 *
 * @version 4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$render_on_toggle = 'closed' === $toggle && ! empty( $scenario_data[ 'scenario_id' ] );

// Add enabled key if not exists.
if ( ! isset( $scenario_data[ 'enabled' ] ) ) {
	// By default active.
	$scenario_data[ 'enabled' ] = 'yes';
}

// Check if scenario is new from ajax call.
$is_ajax = isset( $scenario_data[ 'is_ajax' ] ) && true === $scenario_data[ 'is_ajax' ];

?>
<div class="<?php echo $is_ajax ? 'bto_scenario--added ' : ''; ?>bto_scenario wc-metabox <?php echo $toggle; ?>" rel="<?php echo isset( $scenario_data[ 'position' ] ) ? esc_attr( $scenario_data[ 'position' ] ) : ''; ?>">
	<h3 class="bto_scenario_handle">
		<strong class="scenario_name">
			<?php
			$toggle_class = ( 'yes' === $scenario_data[ 'enabled' ] ) ? 'woocommerce-input-toggle--enabled' : 'woocommerce-input-toggle--disabled';
			?>
			<span class="woocommerce-input-toggle <?php echo $toggle_class; ?>"></span>
			<span class="scenario_name_inner">
				<?php
					if ( ! empty( $scenario_data[ 'title' ] ) ) {
						echo esc_html( $scenario_data[ 'title' ] );
					}
				?>
			</span>
		</strong>
		<?php
			$non_effective_conditions_notice = __( 'Please review this Scenario to make sure it works as intended. When creating Scenarios, remember that it\'s only possible to hide Components and Component Options based on product/variation selections made <strong>in preceeding Components</strong> only.', 'woocommerce-composite-products' );
			echo sprintf( '<div class="woocommerce-help-tip scenario-help-tip scenario-help-tip--non-effective-conditions" %s data-tip="%s"></div>', $is_ajax || empty( $scenario_data[ 'title' ] ) || empty( $scenario_data[ 'has_non_effective_conditions' ] ) ? 'style="display:none"' : '', $non_effective_conditions_notice );
		?>
		<div class="handle">

			<input type="hidden" name="bto_scenario_data[<?php echo $id; ?>][enabled]" class="enabled" value="<?php echo $scenario_data[ 'enabled' ]; ?>"/>
			<input type="hidden" name="bto_scenario_data[<?php echo $id; ?>][position]" class="scenario_position" value="<?php echo isset( $scenario_data[ 'position' ] ) ? esc_attr( $scenario_data[ 'position' ] ) : $id; ?>"/>

			<?php
				if ( ! empty( $scenario_data[ 'scenario_id' ] ) ) {
					?><input type="hidden" name="bto_scenario_data[<?php echo $id; ?>][scenario_id]" class="scenario_id" value="<?php echo $scenario_data[ 'scenario_id' ]; ?>"/><?php
				}
			?>
			<span class="handle-loading"></span>
			<div class="handle-item toggle-item" aria-label="<?php _e( 'Click to toggle', 'woocommerce' ); ?>"></div>
			<div class="handle-item sort-item" aria-label="<?php esc_attr_e( 'Drag and drop to set order', 'woocommerce-composite-products' ); ?>"></div>
			<a href="#" class="remove_row delete"><?php echo __( 'Remove', 'woocommerce' ); ?></a>
		</div>
	</h3><?php

	ob_start();
	include( WC_CP_ABSPATH . 'includes/admin/meta-boxes/views/html-scenario-contents.php' );
	$scenario_content = ob_get_clean();

	?><div class="bto_scenario_data wc-metabox-content" data-scenario_content="<?php echo $render_on_toggle ? htmlspecialchars( $scenario_content ) : ''; ?>"><?php
		echo $render_on_toggle ? '' : $scenario_content;
	?></div>
</div>
