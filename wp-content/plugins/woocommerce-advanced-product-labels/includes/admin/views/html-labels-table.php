<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$labels = wapl_get_advanced_product_labels( array( 'post_status' => array( 'draft', 'publish' ) ) );

?><tr valign='top'>
	<th scope='row' class='titledesc'><?php
		_e( 'Labels', 'woocommerce-advanced-product-labels' ); ?><br />
	</th>
	<td class='forminp' id='woocommerce-advanced-product-labels-overview'>

		<table class='wp-list-table wpc-conditions-post-table wpc-sortable-post-table widefat'>
			<thead>
				<tr>
					<th style='width: 17px;' class="column-cb check-column"></th>
					<th style='padding-left: 10px;' class="column-primary"><?php _e( 'Title', 'woocommerce-advanced-product-labels' ); ?></th>
					<th style='padding-left: 10px;'><?php _e( 'Text', 'woocommerce-advanced-product-labels' ); ?></th>
					<th style='padding-left: 10px;'><?php _e( 'Type', 'woocommerce-advanced-product-labels' ); ?></th>
					<th style='width: 70px;'><?php _e( '# Groups', 'woocommerce-advanced-product-labels' ); ?></th>
				</tr>
			</thead>
			<tbody><?php

				$i = 0;
				foreach ( $labels as $label ) :

					$settings = get_post_meta( $label->ID, '_wapl_global_label', true );
					$alt      = ( $i++ ) % 2 == 0 ? 'alternate' : '';
					?><tr class='<?php echo $alt; ?>'>

						<th class='sort check-column'>
							<input type='hidden' name='sort[]' value='<?php echo absint( $label->ID ); ?>' />
						</th>
						<td class="column-primary">
							<strong>
								<a href='<?php echo get_edit_post_link( $label->ID ); ?>' class='row-title' title='<?php _e( 'Edit Label', 'woocommerce-advanced-product-labels' ); ?>'><?php
									echo _draft_or_post_title( $label->ID );
								?></a><?php
								_post_states( $label );
							?></strong>
							<div class='row-actions'>
								<span class='edit'>
									<a href='<?php echo get_edit_post_link( $label->ID ); ?>' title='<?php _e( 'Edit Label', 'woocommerce-advanced-product-labels' ); ?>'><?php
										_e( 'Edit', 'woocommerce-advanced-product-labels' ); ?>
									</a>
									 |
								</span>
								<span class='trash'>
									<a href='<?php echo get_delete_post_link( $label->ID ); ?>' title='<?php _e( 'Delete Label', 'woocommerce-advanced-product-labels' ); ?>'><?php
										_e( 'Delete', 'woocommerce-advanced-product-labels' );
									?></a>
								</span>
							</div>
						</td>

						<td><?php echo wp_kses_post( $settings['text'] ); ?></td>
						<td><?php echo esc_html( wapl_get_label_types( $settings['type'] ) ); ?></td>
						<td><?php echo absint( count( $settings['conditions'] ) ); ?></td>

					</tr><?php

				endforeach;

				if ( empty( $labels ) ) :

					?><tr>
						<td colspan='2' style="display: table-cell;"><?php _e( 'There are no Labels. Yet...', 'woocommerce-advanced-product-labels' ); ?></td>
					</tr><?php

				endif;

			?></tbody>
			<tfoot>
				<tr>
					<th colspan='5' style='padding-left: 10px; display: table-cell;'>
						<a href='<?php echo admin_url( 'post-new.php?post_type=wapl' ); ?>' class='add button'><?php _e( 'Add Product Label', 'woocommerce-advanced-product-labels' ); ?></a>
					</th>
				</tr>
			</tfoot>
		</table>
	</td>
</tr>
