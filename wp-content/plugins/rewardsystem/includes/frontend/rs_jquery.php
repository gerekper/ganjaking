<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

function user_selection_field( $field_id, $field_label, $getuser ) {
	global $woocommerce ;
	?>
	<?php if ( ( float ) $woocommerce->version <= ( float ) ( '2.2.0' ) ) { ?>
		<tr valign="top">
			<th class="titledesc" scope="row">
				<label for="<?php echo esc_attr($field_id) ; ?>"><?php esc_html_e( $field_label , 'rewardsystem' ) ; ?></label>
			</th>
			<td>
				<select name="<?php echo esc_attr($field_id) ; ?>[]" multiple="multiple" id="<?php echo esc_attr($field_id) ; ?>" class="short <?php echo esc_attr($field_id) ; ?> rs-customer-search" data-exclude='<?php echo json_encode( rs_exclude_particular_users($field_id)) ; ?>'>
					<?php
					$json_ids = array() ;
					if ( '' != $getuser ) {
						$listofuser = $getuser ;
						if ( ! is_array( $listofuser ) ) {
							$userids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $listofuser ) ) ) ;
						} else {
							$userids = $listofuser ;
						}
						foreach ( $userids as $userid ) {
							$user = get_user_by( 'id' , $userid ) ;
							?>
							<option value="<?php echo esc_attr($userid) ; ?>" selected="selected"><?php echo esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ; ?></option>
							<?php
						}
					}
					?>
				</select>
			</td>
		</tr>
		<?php
	} else {
		if ( ( float ) $woocommerce->version >= ( float ) ( '3.0.0' ) ) {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="<?php echo esc_attr($field_id) ; ?>"><?php esc_html_e( $field_label , 'rewardsystem' ) ; ?></label>
				</th>
				<td>
					<select multiple="multiple"  class="wc-customer-search"  name="<?php echo esc_attr($field_id) ; ?>[]" id="<?php echo esc_attr($field_id) ; ?>" data-placeholder="<?php esc_html_e( 'Search Users' , 'rewardsystem' ) ; ?>" data-exclude='<?php echo json_encode( rs_exclude_particular_users($field_id) ) ; ?>'>
						<?php
						$json_ids = array() ;
						if (  '' != $getuser ) {
							$listofuser = $getuser ;
							if ( ! is_array( $listofuser ) ) {
								$userids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $listofuser ) ) ) ;
							} else {
								$userids = $listofuser ;
							}
							foreach ( $userids as $userid ) {
								$user     = get_user_by( 'id' , $userid ) ;
								$json_ids = esc_html( $user->display_name ) . '(#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
								?>
								<option value="<?php echo esc_attr($userid) ; ?>" selected="selected"><?php echo esc_html( $json_ids ) ; ?></option>
								<?php
							}
						}
						?>
														
					</select>
				</td>
			</tr>
			</select>
			<?php
		} else {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="<?php echo esc_attr($field_id) ; ?>"><?php esc_html_e( $field_label , 'rewardsystem' ) ; ?></label>
				</th>
				<td>
					<input type="hidden" class="wc-customer-search" name="<?php echo esc_attr($field_id) ; ?>" id="<?php echo esc_attr($field_id) ; ?>" data-multiple="true" data-exclude='<?php echo json_encode( rs_exclude_particular_users($field_id) ) ; ?>' data-placeholder="<?php esc_html_e( 'Search Users' , 'rewardsystem' ) ; ?>" data-selected="
																					 <?php
																						$json_ids = array() ;
																						if ( '' != $getuser) {
																							$listofuser = $getuser ;
																							if ( ! is_array( $listofuser ) ) {
																								$userids = array_filter( array_map( 'absint' , ( array ) explode( ',' , $listofuser ) ) ) ;
																							} else {
																								$userids = $listofuser ;
																							}
																							foreach ( $userids as $userid ) {
																								$user                  = get_user_by( 'id' , $userid ) ;
																								$json_ids[ $user->ID ] = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
																							}echo esc_attr( json_encode( $json_ids ) ) ;
																						}
																						?>
					" value="<?php echo wp_kses_post(implode( ',' , array_keys( $json_ids )) ) ; ?>" data-allow_clear="true" />
				</td>
			</tr>

			<?php
		}
	}
}

function rs_function_to_add_field_for_product_select( $field_id, $field_label, $getproducts ) {
	global $woocommerce ;
	if ( ( float ) $woocommerce->version <= ( float ) ( '2.2.0' ) ) {
		?>
		<tr valign="top">
			<th class="titledesc" scope="row">
				<label for="<?php echo esc_attr($field_id) ; ?>"><?php esc_html_e( $field_label , 'rewardsystem' ) ; ?></label>
			</th>
			<td class="forminp forminp-select">
				<select multiple name="<?php echo esc_attr($field_id) ; ?>[]" id='<?php echo esc_attr($field_id) ; ?>' class="<?php echo esc_attr($field_id) ; ?> rs_ajax_chosen_select_products_redeem">
					<?php
					$selected_products_exclude = array_filter( ( array ) $getproducts ) ;
					if ( ''!=$selected_products_exclude ) {
						if ( ! empty( $selected_products_exclude ) ) {
							$list_of_produts = ( array ) $getproducts ;
							foreach ( $list_of_produts as $rs_free_id ) {
								echo wp_kses_post('<option value="' . $rs_free_id . '" ') ;
								selected( 1 , 1 ) ;
								$title = get_the_title( $rs_free_id );
								echo wp_kses_post(">#$rs_free_id&ndash;$title") ;
							}
						}
					} else {
						?>
						<option value=""></option>
						<?php
					}
					?>
				</select>
			</td>
		</tr>
		<?php
	} else {
		if ( $woocommerce->version >= ( float ) ( '3.0.0' ) ) {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="<?php echo esc_attr($field_id) ; ?>"><?php esc_html_e( $field_label , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<select class="wc-product-search" multiple="multiple" id="<?php echo esc_attr($field_id) ; ?>"  name="<?php echo esc_attr($field_id) ; ?>[]" data-placeholder="<?php esc_html_e( 'Search for a product' , 'rewardsystem' ) ; ?>"  >
						<?php
						$json_ids = array() ;
						if ( ''!=$getproducts ) {
							$product_ids = $getproducts ;
							foreach ( $product_ids as $product_id ) {
								$product = wc_get_product( $product_id ) ;
								if ( is_object( $product ) ) {
									$json_ids = wp_kses_post( $product->get_formatted_name() ) ;
									?>
									 <option value="<?php echo esc_attr($product_id) ; ?>" selected="selected"><?php echo esc_html( $json_ids ) ; ?></option>
									<?php
								}
							}
						}
						?>
								
					</select>
				</td>
			</tr>
			<?php
		} else {
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="<?php echo esc_attr($field_id) ; ?>"><?php esc_html_e( $field_label , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<input type="hidden" class="wc-product-search" id="<?php echo esc_attr($field_id) ; ?>"  name="<?php echo esc_attr($field_id) ; ?>" data-placeholder="<?php esc_html_e( 'Search for a product' , 'rewardsystem' ) ; ?>" data-action="woocommerce_json_search_products_and_variations" data-multiple="true" data-selected="
																									   <?php
																										$json_ids = array() ;
																										if ( ''!=$getproducts ) {
																											$list_of_produts = $getproducts ;
																											$product_ids     = array_filter( array_map( 'absint' , ( array ) explode( ',' , $getproducts ) ) ) ;

																											foreach ( $product_ids as $product_id ) {
																												$product = wc_get_product( $product_id ) ;
																												if ( is_object( $product ) ) {
																													$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() ) ;
																												}
																											} echo esc_attr( json_encode( $json_ids ) ) ;
																										}
																										?>
						   " value="<?php echo wp_kses_post(implode( ',' , array_keys( $json_ids ) )) ; ?>" />
				</td>
			</tr>

			<?php
		}
	}
}
