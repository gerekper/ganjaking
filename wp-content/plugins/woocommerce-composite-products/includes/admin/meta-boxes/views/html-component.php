<?php
/**
 * Admin Component meta box html
 *
 * @version 8.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bto_group wc-metabox <?php echo esc_attr( $toggle ); ?>" rel="<?php echo isset( $data[ 'position' ] ) ? esc_attr( $data[ 'position' ] ) : esc_attr( $id ); ?>">
	<h3 class="bto_group_handle">
		<strong class="group_name"><?php
			if ( isset( $data[ 'title' ] ) && ! empty( $data[ 'component_id' ] ) ) {
				echo esc_html( $data[ 'title' ] );
			}
		?></strong><span class="group_virtual"><?php
			echo sprintf( '<div class="woocommerce-help-tip component-virtual" data-tip="%s"></div>', esc_attr__( 'Any product purchased in this Component will be treated as virtual.', 'woocommerce-composite-products' ) );
		?></span>
		<div class="handle">

			<input type="hidden" name="bto_data[<?php echo esc_attr( $id ); ?>][position]" class="group_position" value="<?php echo isset( $data[ 'position' ] ) ? esc_attr( $data[ 'position' ] ) : esc_attr( $id ); ?>" />

			<?php
				if ( ! empty( $data[ 'component_id' ] ) ) {
					?><input type="hidden" name="bto_data[<?php echo esc_attr( $id ); ?>][group_id]" class="group_id" value="<?php echo esc_attr( $data[ 'component_id' ] ); ?>" />
					<div class="component-id-item">
						<?php
						/* translators: Component ID. */
						echo '<small>' . sprintf( esc_attr_x( 'ID: %s', 'component id', 'woocommerce-composite-products' ), esc_attr( $data[ 'component_id' ] ) ) . '</small>';
						?>
					</div><?php
				}
			?>
			<div class="handle-item toggle-item" aria-label="<?php esc_attr_e( 'Click to toggle', 'woocommerce' ); ?>"></div>
			<div class="handle-item sort-item" aria-label="<?php esc_attr_e( 'Drag and drop to set order', 'woocommerce-composite-products' ); ?>"></div>
			<a class="remove_row delete" href="#"><?php esc_html_e( 'Remove', 'woocommerce' ); ?></a>
		</div>
	</h3>
	<div class="bto_group_data wc-metabox-content">
		<ul class="subsubsub"><?php

			/*--------------------------------*/
			/*  Tab menu items.               */
			/*--------------------------------*/

			$tab_loop = 0;

			foreach ( $tabs as $tab_id => $tab_values ) {

				?><li><a href="#" data-tab="<?php echo esc_attr( $tab_id ); ?>" class="<?php echo $tab_loop === 0 ? 'current' : ''; ?>"><?php
					echo esc_html( $tab_values[ 'title' ] );
				?></a></li><?php

				$tab_loop++;
			}

		?></ul><?php

		/*--------------------------------*/
		/*  Tab contents.                 */
		/*--------------------------------*/

		$tab_loop = 0;

		foreach ( $tabs as $tab_id => $tab_values ) {

			?><div class="tab_group tab_group_<?php echo esc_attr( $tab_id ); ?> <?php echo $tab_loop > 0 ? 'tab_group_hidden' : ''; ?>"><?php

				/**
				 * Action 'woocommerce_composite_component_admin_{$tab_id}_html':
				 *
				 * @param  string  $component_id
				 * @param  array   $component_data
				 * @param  string  $composite_id
				 *
				 * Action 'woocommerce_composite_component_admin_config_html':
				 *
				 * @hooked WC_CP_Admin::component_config_title()        - 10
				 * @hooked WC_CP_Admin::component_config_description()  - 15
				 * @hooked WC_CP_Admin::component_config_options()      - 20
				 * @hooked WC_CP_Admin::component_config_quantity_min() - 25
				 * @hooked WC_CP_Admin::component_config_quantity_max() - 33
				 * @hooked WC_CP_Admin::component_config_discount()     - 35
				 * @hooked WC_CP_Admin::component_config_optional()     - 40
				 *
				 *
				 * Action 'woocommerce_composite_component_admin_advanced_html':
				 *
				 * @hooked WC_CP_Admin::component_config_default_option()           -   5
				 * @hooked WC_CP_Admin::component_sort_filter_show_orderby()        -  10
				 * @hooked WC_CP_Admin::component_sort_filter_show_filters()        -  15
				 * @hooked WC_CP_Admin::component_layout_hide_product_title()       -  20
				 * @hooked WC_CP_Admin::component_layout_hide_product_description() -  25
				 * @hooked WC_CP_Admin::component_layout_hide_product_thumbnail()   -  30
				 * @hooked WC_CP_Admin::component_id_marker()                       - 100
				 *
				 */
				do_action( 'woocommerce_composite_component_admin_' . $tab_id . '_html', $id, $data, $composite_id );

			?></div><?php

			$tab_loop++;
		}

	?></div>
</div>
