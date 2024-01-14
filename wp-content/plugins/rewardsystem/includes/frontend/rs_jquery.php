<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function user_selection_field( $field_id, $field_label, $getuser ) {
	ob_start();
	?>
	<tr valign="top">
		<th class="titledesc" scope="row">
			<label for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( $field_label, 'rewardsystem' ); ?></label>
		</th>
		<td>
			<select multiple="multiple"  class="wc-customer-search"  name="<?php echo esc_attr( $field_id ); ?>[]" id="<?php echo esc_attr( $field_id ); ?>" data-placeholder="<?php esc_html_e( 'Search Users', 'rewardsystem' ); ?>" data-exclude='<?php echo json_encode( rs_exclude_particular_users( $field_id ) ); ?>'>
				<?php
				$json_ids = array();
				if ( '' != $getuser ) {
					$listofuser = $getuser;
					if ( ! is_array( $listofuser ) ) {
						$userids = array_filter( array_map( 'absint', (array) explode( ',', $listofuser ) ) );
					} else {
						$userids = $listofuser;
					}
					foreach ( $userids as $userid ) {
						$user     = get_user_by( 'id', $userid );
						$json_ids = esc_html( $user->display_name ) . '(#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
						?>
						<option value="<?php echo esc_attr( $userid ); ?>" selected="selected"><?php echo esc_html( $json_ids ); ?></option>
						<?php
					}
				}
				?>
			</select>
		</td>
	</tr>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}

function rs_function_to_add_field_for_product_select( $field_id, $field_label, $getproducts ) {
	ob_start();
	?>
	<tr valign="top">
		<th class="titledesc" scope="row">
			<label for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( $field_label, 'rewardsystem' ); ?></label>
		</th>
		<td class="forminp forminp-select">
			<select class="wc-product-search" multiple="multiple" id="<?php echo esc_attr( $field_id ); ?>"  name="<?php echo esc_attr( $field_id ); ?>[]" data-placeholder="<?php esc_html_e( 'Search for a product', 'rewardsystem' ); ?>"  >
				<?php
				$json_ids = array();
				if ( '' != $getproducts ) {
					$product_ids = $getproducts;
					foreach ( $product_ids as $product_id ) {
						$product = wc_get_product( $product_id );
						if ( is_object( $product ) ) {
							$json_ids = esc_html( $product->get_formatted_name() );
							?>
							<option value="<?php echo esc_attr( $product_id ); ?>" selected="selected"><?php echo esc_html( $json_ids ); ?></option>
							<?php
						}
					}
				}
				?>
			</select>
		</td>
	</tr>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
