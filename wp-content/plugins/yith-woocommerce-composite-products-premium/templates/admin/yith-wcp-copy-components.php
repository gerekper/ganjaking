<style>
	html.wp-toolbar { padding: 0 !important; }
	#wpadminbar, #adminmenuback, #adminmenuwrap, #wpfooter { display: none; }
	#wpcontent { padding: 0; margin: 0 20px !important; }

	table, #methods { margin-bottom: 50px !important; }
	table.wp-list-table .column-components { width: 100px; }
	#methods .add { margin-bottom: 10px; }
	#methods small { margin-left: 25px; }
</style>

<?php

$act = isset( $_REQUEST['act'] ) ? $_REQUEST['act'] : '';
$post_id = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : 0;
if ( ! $post_id > 0 ) { die; }

$wcp_data = get_post_meta( $post_id, '_ywcp_component_data_list' );

if ( is_array( $wcp_data ) && isset( $wcp_data[0]) ) :

	$wcp_data = $wcp_data[0];

	if ( ! $act == 'copy' ) : ?>

		<div id="ywcp-copy-template">

			<form method="post">

				<input type="hidden" name="act" value="copy">
				<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">

				<h2>1. <?php _e( 'Select components to copy', 'yith-composite-products-for-woocommerce' ); ?></h2>

				<table class="wp-list-table widefat fixed striped components">
					<thead>
						<tr>
							<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'yith-composite-products-for-woocommerce' ); ?></label><input id="cb-select-all-1" type="checkbox"></td>
							<th scope="col" id="name" class="manage-column column-name"><span><?php _e( 'Name', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" id="desc" class="manage-column column-desc"><span><?php _e( 'Description', 'yith-composite-products-for-woocommerce' ); ?></span></th>
						</tr>
					</thead>

					<tbody id="the-list">

						<?php 
							foreach ( $wcp_data as $key => $wcp_data_item ) :
								if ( ! empty( $wcp_data_item ) ) : ?>

									<tr id="component-<?php echo $key; ?>">
										<th scope="row" class="check-column">
											<input id="target-component-<?php echo $key; ?>" type="checkbox" name="component[]" value="<?php echo $key; ?>">
										</th>
										<td class="name column-name"><strong><?php echo $wcp_data_item['name']; ?></strong></td>
										<td class="components column-components"><span class="na"><?php echo $wcp_data_item['description']; ?></span></td>
									</tr>

								<?php endif;
							endforeach;
						?>

					</tbody>

					<tfoot>
						<tr>
							<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2"><?php _e( 'Select All', 'yith-composite-products-for-woocommerce' ); ?></label><input id="cb-select-all-2" type="checkbox"></td>
							<th scope="col" class="manage-column column-name column-primary"><span><?php _e( 'Name', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" class="manage-column column-description"><span><?php _e( 'Description', 'yith-composite-products-for-woocommerce' ); ?></span></th>
						</tr>
					</tfoot>

				</table>

				<h2>2. <?php _e( 'Select destination products', 'yith-composite-products-for-woocommerce' ); ?></h2>

				<table class="wp-list-table widefat fixed striped posts">
					<thead>
						<tr>
							<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'yith-composite-products-for-woocommerce' ); ?></label><input id="cb-select-all-1" type="checkbox"></td>
							<th scope="col" id="thumb" class="manage-column column-thumb"><span class="wc-image tips"><?php _e( 'Image', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" id="name" class="manage-column column-name column-primary"><span><?php _e( 'Name', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" id="components" class="manage-column column-components"><span><?php _e( 'Components', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" id="type" class="manage-column column-type"><span><?php _e( 'Product Type', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" id="date" class="manage-column column-date"><span><?php _e( 'Date', 'yith-composite-products-for-woocommerce' ); ?></span></th>
						</tr>
					</thead>

					<tbody id="the-list">
						<?php

							$args = array(
        						'posts_per_page'	=> -1,
								'post_type'			=> 'product',
								'post__not_in'		=> array( $post_id ),
								'tax_query'			=> array(
									array(
										'taxonomy' => 'product_type',
										'field'    => 'slug',
										'terms'    => 'yith-composite', 
									),
								),
							);
							$loop = new WP_Query( $args );

							while ( $loop->have_posts() ) :
								$loop->the_post(); 
								global $product;

								$loop_product_id = get_the_id();
								$loop_components = get_post_meta( $loop_product_id, '_ywcp_component_data_list' );

								$loop_components_count = 0;
								if ( is_array( $loop_components ) && isset( $loop_components[0] ) ) {
									$loop_components_count = count( $loop_components[0] );
								}

								?>

								<tr id="product-<?php echo get_the_id(); ?>" class="iedit author-self level-0 post-<?php echo $loop_product_id; ?> type-product has-post-thumbnail hentry product_cat-clothing product_cat-hoodies">
									<th scope="row" class="check-column">
										<input id="target-product-xxx" type="checkbox" name="product[]" value="<?php echo get_the_id(); ?>">
									</th>
									<td class="thumb column-thumb" data-colname="Image">
										<a href="<?php echo get_edit_post_link(); ?>" target="_blank">
											<?php echo woocommerce_get_product_thumbnail(); ?>
										</a>
									</td>
									<td class="name column-name has-row-actions column-primary" data-colname="Name">
										<strong><a class="row-title" href="<?php echo get_edit_post_link(); ?>" target="_blank"><?php echo get_the_title(); ?></a></strong>
										<div class="row-actions"><span class="id">ID: <?php echo get_the_id(); ?></span></div>
									</td>
									<td class="components column-components"><span class="na"><?php echo $loop_components_count; ?></span></td>
									<td class="type column-type"><span class="na">Composite</span></td>
									<td class="date column-date" data-colname="Date">
										<?php _e( 'Published', 'yith-composite-products-for-woocommerce' ); ?><br />
										<?php echo get_the_date('Y/m/d'); ?>
									</td>
								</tr>

								<?php
							endwhile;
							wp_reset_query();

						?>
					</tbody>

					<tfoot>
						<tr>
							<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2"><?php _e( 'Select All', 'yith-composite-products-for-woocommerce' ); ?></label><input id="cb-select-all-2" type="checkbox"></td>
							<th scope="col" class="manage-column column-thumb"><span class="wc-image tips"><?php _e( 'Image', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" class="manage-column column-name column-primary"><span><?php _e( 'Name', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" class="manage-column column-components"><span><?php _e( 'Components', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" class="manage-column column-type"><span><?php _e( 'Components', 'yith-composite-products-for-woocommerce' ); ?></span></th>
							<th scope="col" class="manage-column column-date"><span><?php _e( 'Date', 'yith-composite-products-for-woocommerce' ); ?></span></th>
						</tr>
					</tfoot>

				</table>

				<h2>3. <?php _e( 'Select method', 'yith-composite-products-for-woocommerce' ); ?></h2>

				<div id="methods">
					<div class="add">
						<input type="radio" name="method" value="add" id="add-components" checked="checked">
						<label for="add-components"><?php _e( 'Simply add components', 'yith-composite-products-for-woocommerce' ); ?></label><br />
					</div>
					<div class="replace">
						<input type="radio" name="method" value="replace" id="replace-components">
						<label for="replace-components"><?php _e( 'Replace all components', 'yith-composite-products-for-woocommerce' ); ?></label><br />
						<small><?php _e( 'You will lose all original destination products components', 'yith-composite-products-for-woocommerce' ); ?></small>
					</div>
				</div>

				<button class="button button-primary"><?php _e( 'Paste Components', 'yith-composite-products-for-woocommerce' ); ?></button>

			</form>

		</div>

	<?php else : ?>

		<?php

			$method = isset( $_REQUEST['method'] ) ? $_REQUEST['method'] : 'add';
			$components = isset( $_REQUEST['component'] ) ? $_REQUEST['component'] : false;
			$target_products = isset( $_REQUEST['product'] ) ? $_REQUEST['product'] : false;

			if ( $components && $target_products ) {

				$new_componens_array = array();
				foreach ( $components as $key => $component_id ) {
					$new_componens_array[ $component_id ] = $wcp_data[ $component_id ];
				}

				foreach ( $target_products as $key => $target_product_id ) {
					
					if ( $method == 'add' ) {

						$old_componens_array = get_post_meta( $target_product_id, '_ywcp_component_data_list' );
						$old_componens_array = $old_componens_array[0];
						$new_array = array_merge( $old_componens_array, $new_componens_array );

						update_post_meta( $target_product_id, '_ywcp_component_data_list', $new_array );

					} else if ( $method == 'replace' ) {

						update_post_meta( $target_product_id, '_ywcp_component_data_list', $new_componens_array );

					}

				}

				echo '<h2>' . __( 'Components updated', 'yith-composite-products-for-woocommerce' ) . '</h2>';

			}

		?>

	<?php endif; ?>

<?php endif; ?>


