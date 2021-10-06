<?php

defined( 'ABSPATH' ) || exit;

global $wpdb, $woocommerce;
$settings        = get_option(
	'sfn_cart_addons',
	array(
		'header_title'   => __( 'Product Add-ons', 'sfn_cart_addons' ),
		'default_addons' => array(),
	)
);
$terms           = get_terms( 'product_cat', array( 'hide_empty' => false ) );
$categories      = array();
$category_addons = get_option( 'sfn_cart_addons_categories', array() );
$product_addons  = get_option( 'sfn_cart_addons_products', array() );

foreach ( $terms as $term ) {
	$used = false;
	foreach ( $category_addons as $c_addon ) {
		if ( $c_addon['category_id'] == $term->term_id ) {
			$used = true;
			break;
		}
	}

	if ( ! $used ) {
		$categories[] = array(
			'id'   => $term->term_id,
			'name' => $term->name,
		);
	}
}
?>
<div class="wrap woocommerce">
	<div id="icon-edit" class="icon32 icon32-posts-product"><br></div>
	<h2><?php esc_html_e( 'Cart Add-Ons', 'sfn_cart_addons' ); ?></h2>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
	<div id="message" class="updated"><p><?php esc_html_e( 'Settings updated', 'sfn_cart_addons' ); ?></p></div>
	<?php endif; ?>

	<form action="admin-post.php" method="post">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="heading"><?php esc_html_e( 'Display Title', 'sfn_cart_addons' ); ?></label>
					</th>
					<td>
						<?php $settings['header_title'] = isset( $settings['header_title'] ) ? $settings['header_title'] : ''; ?>
						<input type="text" name="header_title" id="heading" value="<?php echo esc_attr( $settings['header_title'] ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'The title text displayed above the add-ons, both on the cart page, and using the shortcode.', 'sfn_cart_addons' ); ?>
						</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="number"><?php esc_html_e( 'Maximum number of upsells to show in the cart.', 'sfn_cart_addons' ); ?></label>
					</th>
					<td>
						<?php $settings['upsell_number'] = isset( $settings['upsell_number'] ) ? $settings['upsell_number'] : ''; ?>
						<input type="number" name="upsell_number" id="number" value="<?php echo esc_attr( $settings['upsell_number'] ); ?>" class="small-text" placeholder="6" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="default_products"><?php esc_html_e( 'Default Add-Ons', 'sfn_cart_addons' ); ?></label>
					</th>
					<td>
						<select
							class="sfn-product-search"
							id="default_products"
							name="default_products[]"
							multiple="multiple"
							data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'sfn_cart_addons' ); ?>"
							style="width: 600px"
						>
							<?php
							$product_ids = array_filter( array_map( 'absint', $settings['default_addons'] ) );

							foreach ( $product_ids as $product_id ) :
								$product      = wc_get_product( $product_id );
								$product_name = $product ? wp_strip_all_tags( $product->get_formatted_name() ) : '';
								?>
								<option value="<?php echo esc_attr( $product_id ); ?>" selected="selected"><?php echo esc_html( $product_name ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description">
							<?php esc_html_e( 'These products will be displayed on the cart page if there are no matching products and/or categories in the shopping cart from the settings below.', 'sfn_cart_addons' ); ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>

		<h4><?php esc_html_e( 'Category Matches', 'sfn_cart_addons' ); ?></h4>
		<p class="description">
			<?php esc_html_e( 'If a product in the shopping cart matches a category defined below, the cart upsells will display the matching products to show. Set the priority order to define which category upsells should be shown when items in the shopping cart match multiple categories. Categories with the highest priority will be the upsells that are displayed when there are multiple category matches in the cart.', 'sfn_cart_addons' ); ?>
		</p>

		<table class="wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th width="25" scope="col" id="drag" class="manage-column column-drag"></th>
					<th width="50" scope="col" id="priority" class="manage-column column-usage_count" style=""><?php esc_html_e( 'Priority', 'sfn_cart_addons' ); ?></th>
					<th width="20%" scope="col" id="category" class="manage-column column-type" style=""><?php esc_html_e( 'Category', 'sfn_cart_addons' ); ?></th>
					<th scope="col" id="products" class="manage-column column-products" style=""><?php esc_html_e( 'Product Add-Ons', 'sfn_cart_addons' ); ?></th>
					<th width="10%" scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody id="cat_tbody">
				<?php
				if ( ! empty( $category_addons ) ) :
					$p = 0;
					foreach ( $category_addons as $x => $addons ) :
						$p++;
						$category = get_term( $addons['category_id'], 'product_cat' );
						?>
				<tr scope="row">
					<td class="column-drag"><span class="dashicons dashicons-menu"></span></td>
					<td class="priority-alignment">
						<span class="priority"><?php echo esc_html( $p ); ?></span>
						<input type="hidden" name="category_priorities[]" value="<?php echo esc_attr( $x ); ?>" size="3" />
					</td>
					<td class="post-title column-title">
						<strong><?php echo esc_html( stripslashes( $category->name ) ); ?></strong>
						<input type="hidden" name="category[<?php echo esc_attr( $x ); ?>]" value="<?php echo esc_attr( $addons['category_id'] ); ?>" />
					</td>
					<td>
						<select
							class="sfn-product-search"
							id="cselect_<?php echo esc_attr( $x ); ?>"
							name="category_products[<?php echo esc_attr( $x ); ?>][]"
							multiple="multiple"
							data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'sfn_cart_addons' ); ?>"
							style="width: 95%"
						>
							<?php
							$product_ids = array_filter( array_map( 'absint', $addons['products'] ) );

							foreach ( $product_ids as $product_id ) :
								$product      = wc_get_product( $product_id );
								$product_name = $product ? wp_strip_all_tags( $product->get_formatted_name() ) : '';
								?>
								<option value="<?php echo esc_attr( $product_id ); ?>" selected="selected"><?php echo esc_html( $product_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td align="center" class="column-remove-row">
						<a class="remove" href="#" title="<?php esc_attr_e( 'Remove Row', 'sfn_cart_addons' ); ?>"><span class="dashicons dashicons-no"></span></a>
					</td>
				</tr>
						<?php
					endforeach;
				endif;
				?>
			</tbody>
		</table>
		<br />
		<button type="button" id="add_category" class="button"><?php esc_html_e( '+ Add Category', 'sfn_cart_addons' ); ?></button>

		<h4><?php esc_html_e( 'Product Matches', 'sfn_cart_addons' ); ?></h4>
		<p class="description">
			<?php esc_html_e( 'If a product in the shopping cart matches one of the products defined below, the cart upsells will display the matching products below to show. Set the priority to define which product upsells should be shown when items in the shopping cart are all defined. Products with the highest priority will be the upsells that are displayed when there are multiple products in the cart.', 'sfn_cart_addons' ); ?>
		</p>

		<table class="wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th width="25" scope="col" id="drag" class="manage-column column-drag"></th>
					<th width="50" scope="col" id="priority" class="manage-column column-usage_count" style=""><?php esc_html_e( 'Priority', 'sfn_cart_addons' ); ?></th>
					<th width="20%" scope="col" id="products" class="manage-column column-type" style=""><?php esc_html_e( 'Product', 'sfn_cart_addons' ); ?></th>
					<th scope="col" id="products" class="manage-column column-products" style=""><?php esc_html_e( 'Product Add-Ons', 'sfn_cart_addons' ); ?></th>
					<th width="10%" scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody id="product_tbody">
				<?php
				if ( ! empty( $product_addons ) ) :
					$p = 0;
					foreach ( $product_addons as $x => $addons ) :
						$product = wc_get_product( $addons['product_id'] );

						if ( ! $product ) {
							continue;
						}

						$p++;
						?>
				<tr scope="row">
					<td class="column-drag"><span class="dashicons dashicons-menu"></span></td>
					<td class="priority-alignment">
						<span class="priority"><?php echo esc_html( $p ); ?></span>
						<input type="hidden" name="product_priorities[]" value="<?php echo esc_attr( $x ); ?>" size="3" />
					</td>
					<td class="post-title column-title">
						<strong><?php echo wp_kses_post( $product->get_formatted_name() ); ?></strong>
						<input type="hidden" name="product[<?php echo esc_attr( $x ); ?>]" value="<?php echo esc_attr( $addons['product_id'] ); ?>" />

						<select name="product[<?php echo esc_attr( $x ); ?>]" class="product-select" style="display:none;">
							<option value="<?php echo esc_attr( $addons['product_id'] ); ?>" selected><?php echo esc_html( $addons['product_id'] ); ?></option>
						</select>

						<?php
						$addon_product = wc_get_product( $addons['product_id'] );
						$display       = $addon_product->is_type( 'variable' ) ? 'block' : 'none';
						?>
						<label style="display:<?php echo esc_attr( $display ); ?>;">
							<input type="checkbox" id="product_include_variations_{number}" name="product_include_variations[<?php echo esc_attr( $x ); ?>]" value="1" <?php checked( ! empty( $addons['include_variations'] ), true ); ?> />
							<?php esc_html_e( 'include variations', 'sfn_cart_addons' ); ?>
						</label>
					</td>
					<td>
						<select
							class="sfn-product-search"
							id="pselect_<?php echo esc_attr( $x ); ?>"
							name="product_products[<?php echo esc_attr( $x ); ?>][]"
							multiple="multiple"
							data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'sfn_cart_addons' ); ?>"
							style="width: 95%">
							<?php
							$product_ids = array_filter( array_map( 'absint', $addons['products'] ) );

							foreach ( $product_ids as $product_id ) :
								$product      = wc_get_product( $product_id );
								$product_name = $product ? wp_strip_all_tags( $product->get_formatted_name() ) : '';
								?>
								<option value="<?php echo esc_attr( $product_id ); ?>" selected="selected"><?php echo esc_html( $product_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td align="center" class="column-remove-row">
						<a class="remove" href="#" title="<?php esc_attr_e( 'Remove Row', 'sfn_cart_addons' ); ?>"><span class="dashicons dashicons-no"></span></a>
					</td>
				</tr>
						<?php
					endforeach;
				endif;
				?>
			</tbody>
		</table>
		<br />
		<button type="button" id="add_product" class="button"><?php esc_html_e( '+ Add Product', 'sfn_cart_addons' ); ?></button>

		<p class="submit">
			<?php wp_nonce_field( 'sfn_cart_addons_update_settings', 'sfn_cart_addons_update_settings_nonce' ); ?>
			<input type="hidden" name="action" value="sfn_cart_addons_update_settings" />
			<input type="submit" name="save" value="<?php esc_attr_e( 'Update Settings', 'sfn_cart_addons' ); ?>" class="button-primary" />
		</p>

	</form>
</div>
<table id="category_form_template" style="display: none;">
	<tbody>
	<tr scope="row">
		<td class="column-drag">
			<span class="dashicons dashicons-menu"></span>
		</td>
		<td width="50" class="priority-alignment">
			<span class="priority"></span>
			<input type="hidden" name="category_priorities[]" value="{number}" size="3" />
		</td>
		<td width="20%" class="post-title column-title">
			<select name="category[{number}]" id="category_{number}" class="category-select"></select>
		</td>
		<td>
			<select id="cselect_{number}" name="category_products[{number}][]" class="sfn-product-search-tpl" multiple data-placeholder="<?php esc_attr_e( 'Search for a product &hellip;', 'sfn_cart_addons' ); ?>" style="width: 95%"></select>
		</td>
		<td width="10%" align="center" class="column-remove-row">
			<a class="remove" href="#" title="<?php esc_attr_e( 'Remove Row', 'sfn_cart_addons' ); ?>"><span class="dashicons dashicons-no"></span></a>
		</td>
	</tr>
	</tbody>
</table>

<table id="product_form_template" style="display: none;">
	<tbody>
	<tr scope="row">
		<td class="column-drag">
			<span class="dashicons dashicons-menu"></span>
		</td>
		<td width="50" class="priority-alignment">
			<span class="priority"></span>
			<input type="hidden" name="product_priorities[]" value="{number}" size="3" />
		</td>
		<td width="20%" class="post-title column-title">
			<select id="product_{number}" name="product[{number}]" class="sfn-product-search-tpl product-select" data-placeholder="<?php esc_attr_e( 'Search for a product &hellip;', 'sfn_cart_addons' ); ?>" style="width: 95%"></select>
			<label class="include-variations-label" style="display: none;">
				<input type="checkbox" id="product_include_variations_{number}" name="product_include_variations[{number}]" value="1" />
				<?php esc_html_e( 'include variations', 'sfn_cart_addons' ); ?>
			</label>
		</td>
		<td>
			<select id="pselect_{number}" name="product_products[{number}][]" class="sfn-product-search-tpl" multiple data-placeholder="<?php esc_attr_e( 'Search for a product &hellip;', 'sfn_cart_addons' ); ?>" style="width: 95%"></select>
		</td>
		<td width="10%" align="center" class="column-remove-row">
			<a class="remove" href="#" title="<?php esc_attr_e( 'Remove Row', 'sfn_cart_addons' ); ?>"><span class="dashicons dashicons-no"></span></a>
		</td>
	</tr>
	</tbody>
</table>

<table id="no_addons_template" style="display: none">
	<tbody>
		<tr class="no_addons" scope="row">
			<td colspan="5" align="center"><?php esc_html_e( 'No add-ons defined', 'sfn_cart_addons' ); ?></td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	var store_categories = JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( $categories  ) ); ?>' ) );
</script>
