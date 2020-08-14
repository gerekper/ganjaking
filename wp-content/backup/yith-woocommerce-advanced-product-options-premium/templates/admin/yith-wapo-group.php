<?php
/**
 * Admin Products Options Group
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */

defined( 'ABSPATH' ) or exit;

global $wpdb, $woocommerce;

$id =  ( isset( $_REQUEST['id'] ) && $_REQUEST['id'] > 0 ? $_REQUEST['id'] : 0 );
$group = new YITH_WAPO_Group( $id );

$vendor_user = YITH_WAPO::get_current_multivendor();

$show_vendor_column = YITH_WAPO::$is_vendor_installed && ( !isset( $vendor_user ) || ( isset( $vendor_user ) && is_object( $vendor_user ) && ! $vendor_user->has_limited_access() ) );

$is_less_than_2_7 = version_compare( WC()->version, '2.7', '<' );
?>

<div id="group" class="wrap wapo-plugin">

	<h1>
		<?php if ( $group->id > 0 ) {
			echo __( 'Group', 'yith-woocommerce-product-add-ons' ) . ': ' . $group->name;
			echo '<a href="edit.php?post_type=product&page=yith_wapo_group_addons&id='.esc_attr( $group->id ).'" class="page-title-action">'.__( 'Manage Add-ons', 'yith-woocommerce-product-add-ons' ).'&raquo;</a>';
		}
		else { echo __( 'New group', 'yith-woocommerce-product-add-ons' ); } ?>

	</h1>

	<form id="group-form" action="edit.php?post_type=product&page=yith_wapo_group" method="post">

		<input type="hidden" name="id" value="<?php echo $group->id; ?>">
		<input type="hidden" name="act" value="<?php echo $group->id > 0 ? 'update' : 'new'; ?>">
		<input type="hidden" name="class" value="YITH_WAPO_Group">
		<input type="hidden" name="types-order" value="">

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="name"><?php echo __( 'Group name', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td><input name="name" type="text" value="<?php echo $group->name; ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="products_id"><?php echo __( 'Products', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td><?php yith_wapo_multi_products_select( 'products_id[]', $group->products_id ); ?></td>
				</tr>
				<?php do_action( 'yith_wapo_excluded_products_template', array( $group ) ); ?>
				<tr>
					<th scope="row"><label for="categories_id"><?php echo __( 'Categories', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td>
						<select name="categories_id[]" class="categories_id-select2" multiple="multiple" placeholder="<?php echo __( 'Applied to...', 'yith-woocommerce-product-add-ons' ); ?>"><?php

							$categories_array = explode( ',', $group->categories_id );
							echo_product_categories_childs_of( 0, 0, $categories_array );

							function echo_product_categories_childs_of( $id = 0, $tabs = 0, $categories_array = array() ) {

								// WPML
								/*
								global $sitepress;
								if ( is_object( $sitepress ) ) {
									$yith_wapo_current_lang = apply_filters( 'wpml_current_language', NULL );
									$yith_wapo_temp_lang = $sitepress->get_default_language();
									if ( $yith_wapo_current_lang != $yith_wapo_temp_lang ) {
										$sitepress->switch_lang( $yith_wapo_temp_lang );
									}
								}
								*/
								
								$categories = get_categories( array( 'taxonomy'=>'product_cat', 'parent'=>$id, 'orderby'=>'name', 'order'=>'ASC' ) );
								foreach ( $categories as $key => $value ) {
									echo '<option value="' . $value->term_id . '" ' . ( in_array( $value->term_id, $categories_array ) ? 'selected="selected"' : '' ) . '>' . str_repeat( '&#8212;', $tabs ) . ' ' . $value->name . '</option>';
									$childs = get_categories( array( 'taxonomy'=>'product_cat', 'parent'=>$value->term_id, 'orderby'=>'name', 'order'=>'ASC' ) );
									if ( count( $childs ) > 0 ) { echo_product_categories_childs_of( $value->term_id, $tabs + 1, $categories_array ); }
								}
							}

						?></select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="priority"><?php echo __( 'Priority Order', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td><input name="priority" type="number" value="<?php echo $group->priority; ?>" class="small-text"></td>
				</tr>
				<?php if ( YITH_WAPO::$is_vendor_installed && $show_vendor_column ) : ?>
					<tr>
						<th scope="row"><label for="vendor_id"><?php echo __( 'Vendor', 'yith-woocommerce-product-add-ons' ); ?></label></th>
						<td>
							<select name="vendor_id">
								<option value="0" <?php selected( $group->visibility, 0 ); ?>><?php echo __( 'None', 'yith-woocommerce-product-add-ons' ); //@since 1.1.0 ?></option>
								<?php YITH_WAPO_Group::printOptionsVendorList( $group->vendor_id ) ?>
							</select>
						</td>
					</tr>
				<?php endif; ?>
				<tr>
					<th scope="row"><label for="visibility"><?php echo __( 'Visibility', 'yith-woocommerce-product-add-ons' ); ?></label></th>
					<td>
						<select name="visibility">
							<option value="0" <?php selected( $group->visibility, 0 ); ?>><?php echo __( 'Hidden', 'yith-woocommerce-product-add-ons' ); ?></option>
							<option value="1" <?php selected( $group->visibility, 1 ); ?>><?php echo __( 'Administrators only', 'yith-woocommerce-product-add-ons' ); ?></option>
							<option value="9" <?php selected( $group->visibility, 9 ); selected( $group->id, 0 ); ?>><?php echo __( 'Public', 'yith-woocommerce-product-add-ons' ); ?></option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" form="group-form" class="button button-primary" value="<?php echo __( 'Save group', 'yith-woocommerce-product-add-ons' );?>">
		</p>

	</form>

</div>