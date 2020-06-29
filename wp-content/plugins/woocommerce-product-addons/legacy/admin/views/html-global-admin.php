<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap woocommerce">
	<div class="icon32 icon32-posts-product" id="icon-woocommerce"><br/></div>

    <h2><?php _e( 'Global Add-ons', 'woocommerce-product-addons' ) ?> <a href="<?php echo add_query_arg( 'add', true, admin_url( 'edit.php?post_type=product&page=global_addons' ) ); ?>" class="add-new-h2"><?php _e( 'Add Global Add-on', 'woocommerce-product-addons' ); ?></a></h2><br/>

	<table id="global-addons-table" class="wp-list-table widefat" cellspacing="0">
		<thead>
			<tr>
				<th scope="col"><?php _e( 'Reference', 'woocommerce-product-addons' ); ?></th>
				<th><?php _e( 'Number of Fields', 'woocommerce-product-addons' ); ?></th>
				<th><?php _e( 'Priority', 'woocommerce-product-addons' ); ?></th>
				<th><?php _e( 'Applies to...', 'woocommerce-product-addons' ); ?></th>
				<th><?php _e( 'Actions', 'woocommerce-product-addons' ); ?></th>
			</tr>
		</thead>
		<tbody id="the-list">
			<?php
				$global_addons = Product_Addon_Groups::get_all_global_groups();

				if ( $global_addons ) {
					foreach ( $global_addons as $global_addon ) {
						?>
						<tr>
							<td><?php echo $global_addon['name']; ?></td>
							<td><?php echo sizeof( $global_addon['fields'] ); ?></td>
							<td><?php echo $global_addon['priority']; ?></td>
							<td><?php

								$restrict_to_categories = $global_addon['restrict_to_categories'];
								if ( 0 === count( $restrict_to_categories) ) {
									_e( 'All Products', 'woocommerce-product-addons' );
								} else {
									$objects = array_keys( $restrict_to_categories );
									$term_names = array_values( $restrict_to_categories );
									$term_names = apply_filters( 'woocommerce_product_addons_global_display_term_names', $term_names, $objects );
									echo implode( ', ', $term_names );
								}

							?></td>
							<td>
								<a href="<?php echo add_query_arg( 'edit', $global_addon['id'], admin_url( 'edit.php?post_type=product&page=global_addons' ) ); ?>" class="button"><?php _e( 'Edit', 'woocommerce-product-addons' ); ?></a> <a href="<?php echo wp_nonce_url( add_query_arg( 'delete', $global_addon['id'], admin_url( 'edit.php?post_type=product&page=global_addons' ) ), 'delete_addon' ); ?>" class="button"><?php _e( 'Delete', 'woocommerce-product-addons' ); ?></a>
							</td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan="5"><?php _e( 'No global add-ons exists yet.', 'woocommerce-product-addons' ); ?> <a href="<?php echo add_query_arg( 'add', true, admin_url( 'edit.php?post_type=product&page=global_addons' ) ); ?>"><?php _e( 'Add one?', 'woocommerce-product-addons' ); ?></a></td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
</div>
