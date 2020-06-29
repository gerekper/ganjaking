<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?><div class='wpc-condition-group-wrap'>
	<p class='or-text'><strong><?php _e( 'Or', 'woocommerce-advanced-product-labels' ); ?></strong></p>

	<div class='wpc-condition-group clearfix' data-group='<?php echo absint( $condition_group ); ?>'>

		<span class='wpc-condition-group-actions alignright'>
			<a href='javascript:void(0);' class='duplicate'><?php _e( 'Duplicate', 'woocommerce-advanced-product-labels' ); ?></a>&nbsp;|&nbsp;<a href='javascript:void(0);' class='delete'><?php _e( 'Delete', 'woocommerce-advanced-product-labels' ); ?></a>
		</span>
		<p class='match-text'><?php _e( 'A product must match all of the following conditions to show the label:', 'woocommerce-advanced-product-labels' ); ?></p>

		<div class='wpc-conditions-list'><?php

			if ( ! empty( $conditions ) ) :

				foreach ( $conditions as $condition_id => $condition ) :
					$wp_condition = new WAPL_Condition( $condition_id, $condition_group, $condition['condition'], $condition['operator'], $condition['value'] );
					$wp_condition->output_condition_row();
				endforeach;

			else :

				$wp_condition = new WAPL_Condition( null, $condition_group );
				$wp_condition->output_condition_row();

			endif;

		?></div>

		<div class="wpc-condition-template hidden" style="display: none;"><?php
			$wp_condition = new WAPL_Condition( '9999', $condition_group );
			$wp_condition->output_condition_row();
		?></div>
		<a style="margin-top: 0.5em; margin-right: 63px;" class='wpc-condition-add wpc-add button alignright' data-group='<?php echo absint( $condition_group ); ?>' href='javascript:void(0);'><?php _e( 'Add condition', 'woocommerce-advanced-product-labels' ); ?></a>

	</div>
</div>
