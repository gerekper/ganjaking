<?php
/**
 * Template options in WC Product Panel
 *
 * @var array $extra_costs The product extra costs
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$global_extra_cost_ids = yith_wcbk_get_extra_cost_ids( array( 'suppress_filters' => false ) );
?>

<table id="yith-wcbk-extra-costs__table">
	<thead>
	<tr>
		<th class="extra-cost__name"><?php esc_html_e( 'Cost', 'yith-booking-for-woocommerce' ); ?></th>
		<th class="extra-cost__amount"><?php esc_html_e( 'Amount', 'yith-booking-for-woocommerce' ); ?></th>
		<th class="extra-cost__multiply-by"><?php esc_html_e( 'Multiply by', 'yith-booking-for-woocommerce' ); ?></th>
		<th class="extra-cost__actions"></th>
	</tr>
	</thead>
	<tbody id="yith-wcbk-extra-costs__list">
	<?php

	foreach ( $global_extra_cost_ids as $extra_cost_id ) {
		$extra_cost_args = $extra_costs[ $extra_cost_id ] ?? array( 'id' => $extra_cost_id );
		$extra_cost      = yith_wcbk_product_extra_cost( $extra_cost_args );

		yith_wcbk_get_module_view( 'costs', 'product-tabs/extra-costs/extra-cost.php', compact( 'extra_cost' ) );
	}

	// Custom Extra Costs.
	$idx = 1;
	foreach ( $extra_costs as $extra_cost_identifier => $extra_cost ) {
		$extra_cost = yith_wcbk_product_extra_cost( $extra_cost );
		if ( $extra_cost->is_custom() ) {
			$index = '_' . $idx;

			yith_wcbk_get_module_view( 'costs', 'product-tabs/extra-costs/extra-cost-custom.php', compact( 'extra_cost', 'index' ) );
			$idx ++;
		}
	}
	?>
	</tbody>
</table>
<?php
$index      = '_{{INDEX}}';
$extra_cost = yith_wcbk_product_extra_cost( array( 'id' => 0 ) );
ob_start();
yith_wcbk_get_module_view( 'costs', 'product-tabs/extra-costs/extra-cost-custom.php', compact( 'extra_cost', 'index' ) );
$template = ob_get_clean();
?>

<div class="yith-wcbk-settings-section__content__actions">
	<span
			id="yith-wcbk-extra-costs__new-extra-cost"
			class="yith-wcbk-admin-action-link"
			data-template="<?php echo esc_attr( $template ); ?>"
	>+ <?php esc_html_e( 'Add cost', 'yith-booking-for-woocommerce' ); ?></span>
</div>
