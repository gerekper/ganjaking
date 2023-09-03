<?php
/**
 * Admin Bundled Product view
 *
 * @package  WooCommerce Product Bundles
 * @version  6.21.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="wc-bundled-item wc-metabox <?php echo esc_attr( $toggle ); ?> <?php echo esc_attr( $stock_status ); ?>" rel="<?php echo esc_attr( $loop ); ?>">
	<h3>
		<span class="bundled-item-product-id"><a class="edit-product" href="<?php echo esc_url( admin_url( 'post.php?action=edit&post=' . esc_attr( $product->get_id() ) ) );?>">#<span class="bundled-product-id"><?php echo esc_html( $product->get_id() ); ?></span></a></span>
		<span class="bundled-item-title-inner"><strong class="item-title"><?php echo esc_html( $product->get_title() ); ?></strong></span>
		<?php
			echo $product->is_virtual() ? '' : sprintf( '<div class="woocommerce-help-tip bundled-item-status bundled-item-status--forced-virtual" data-tip="%s"></div>', esc_attr__( 'This product will be treated as virtual when purchased in this bundle.', 'woocommerce-product-bundles' ) );
			echo ( false !== $item_id && 'in_stock' !== $stock_status ) ? sprintf( '<div class="woocommerce-help-tip bundled-item-status bundled-item-status--%s" data-tip="%s"></div>', esc_attr( $stock_status ), esc_attr( $stock_status_label ) ) : '';
		?>
		<div class="handle">
			<?php
				$sku = $product->get_sku();
				/* translators: Bundled product SKU */
				echo $sku ? ( '<small class="item-sku">' . sprintf( esc_html_x( 'SKU: %s', 'bundled product sku', 'woocommerce-product-bundles' ), esc_html( $sku ) ) . '</small>' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<div class="handle-item toggle-item" aria-label="<?php esc_attr_e( 'Click to toggle', 'woocommerce' ); ?>"></div>
			<div class="handle-item sort-item" aria-label="<?php esc_attr_e( 'Drag and drop to set order', 'woocommerce-product-bundles' ); ?>"></div>
			<a href="#" class="remove_row delete"><?php echo esc_html__( 'Remove', 'woocommerce' ); ?></a>
		</div>
	</h3>
	<div class="item-data wc-metabox-content">
		<input type="hidden" name="bundle_data[<?php echo esc_attr( $loop ); ?>][menu_order]" class="item_menu_order" value="<?php echo esc_attr( $loop ); ?>" /><?php

		if ( false !== $item_id ) {
			?><input type="hidden" name="bundle_data[<?php echo esc_attr( $loop ); ?>][item_id]" class="item_id" value="<?php echo esc_attr( $item_id ); ?>" /><?php
		}

		?><input type="hidden" name="bundle_data[<?php echo esc_attr( $loop ); ?>][product_id]" class="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>" />

		<ul class="subsubsub"><?php

			/*--------------------------------*/
			/*  Tab menu items.               */
			/*--------------------------------*/

			$tab_loop = 0;

			foreach ( $tabs as $tab_values ) {

				$tab_id = $tab_values[ 'id' ];

				?><li><a href="#" data-tab="<?php echo esc_attr( $tab_id ); ?>" class="<?php echo $tab_loop === 0 ? 'current' : ''; ?>"><?php
					echo wp_kses_post( $tab_values[ 'title' ] );
				?></a></li><?php

				$tab_loop++;
			}

		?></ul><?php

		/*--------------------------------*/
		/*  Tab contents.                 */
		/*--------------------------------*/

		$tab_loop = 0;

		foreach ( $tabs as $tab_values ) {

			$tab_id = $tab_values[ 'id' ];

			?><div class="options_group options_group_<?php echo esc_attr( $tab_id ); ?> <?php echo $tab_loop > 0 ? 'options_group_hidden' : ''; ?>"><?php
				/**
				 * 'woocommerce_bundled_product_admin_{$tab_id}_html' action.
				 */
				do_action( 'woocommerce_bundled_product_admin_' . $tab_id . '_html', $loop, $product->get_id(), $item_data, $post_id );
			?></div><?php

			$tab_loop++;
		}

	?></div>
</div>
