<?php
/**
 * Manual Referral Link Rules.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="widefat fixed rsdynamicrulecreation_manual" cellspacing="0">
	<thead>
		<tr>

			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Referrer Username' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Buyer Username' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname-link" scope="col"><?php esc_html_e( 'Linking Type' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Linking' , 'rewardsystem' ) ; ?></th>
		</tr>
	</thead>

	<tbody id="here">
		<?php
		$i = ( $per_page * $current_page ) - $per_page ;
		if ( srp_check_is_array( $rules_based_on_per_page ) ) :
			?>
			<tr class="rs-manual-referral-link-search">
				<td colspan="4">
					<input type="text" id='rs_search_user' name="rs_search_user">
					<button type="submit" 
							id="rs_search_user_action"
							name="rs_search_user_action">
								<?php esc_html_e( 'Search Referrer/Buyer' , 'rewardsystem' ) ; ?>
					</button>
				</td>
			</tr>
			<?php
			foreach ( $rules_based_on_per_page as $key => $rewards_dynamic_rule ) :
				$rule_key = ! $searched_user ? $i : $key ;
				if ( ! isset( $rewards_dynamic_rule[ 'referer' ] ) && ! isset( $rewards_dynamic_rule[ 'refferal' ] ) ) {
					continue ;
				}
				if ( '' != $rewards_dynamic_rule[ 'referer' ] && '' != $rewards_dynamic_rule[ 'refferal' ] ) :
					?>
					<tr data-row = "<?php echo esc_attr( $rule_key ) ; ?>">
						<td class="column-columnname">
							<?php if ( ( float ) $woocommerce->version <= ( float ) ( '2.2.0' ) ) { ?>
								<select name="rewards_dynamic_rule_manual[<?php echo esc_attr($rule_key) ; ?>][referer]" 
										class="short rs_manual_linking_referer">
											<?php
											$user = get_user_by( 'id' , absint( $rewards_dynamic_rule[ 'referer' ] ) ) ;
											echo '<option value="' . absint( $user->ID ) . '" ' ;
											selected( 1 , 1 ) ;
											echo '>' . esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')</option>' ;
											?>
								</select>
								<?php
							} else {
								$user_id     = absint( $rewards_dynamic_rule[ 'referer' ] ) ;
								$user        = get_user_by( 'id' , $user_id ) ;
								$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
								if ( ( float ) $woocommerce->version >= ( float ) ( '3.0.0' ) ) {
									?>
									<select multiple="multiple"  
											class="wc-customer-search" 
											name="rewards_dynamic_rule_manual[<?php echo esc_attr($rule_key) ; ?>][referer]" 
											data-placeholder="<?php esc_html_e( 'Search Users' , 'rewardsystem' ) ; ?>">
										<option value="<?php echo esc_html( $user_id ) ; ?>" selected="selected"><?php echo esc_attr( $user_string ) ; ?><option>
									</select>
									<?php 
								} else {
									?>
									<input type="hidden"
										   class="wc-customer-search"
										   name="rewards_dynamic_rule_manual[<?php echo esc_attr($rule_key) ; ?>][referer]"
										   data-placeholder="<?php esc_html_e( 'Search for a customer' , 'rewardsystem' ) ; ?>" 
										   data-selected="<?php echo esc_attr( $user_string ) ; ?>" 
										   value="<?php echo esc_html( $user_id ) ; ?>" data-allow_clear="true" />
										   <?php
								}
							}
							?>
						</td>
						<td class="column-columnname">
							<?php if ( ( float ) $woocommerce->version <= ( float ) ( '2.2.0' ) ) { ?>
								<select name="rewards_dynamic_rule_manual[<?php echo esc_attr($rule_key) ; ?>][refferal]" 
										class="short rs_manual_linking_referral">
											<?php
											$user = get_user_by( 'id' , absint( $rewards_dynamic_rule[ 'refferal' ] ) ) ;
											echo '<option value="' . absint( $user->ID ) . '" ' ;
											selected( 1 , 1 ) ;
											echo '>' . esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')</option>' ;
											?>
								</select>
							<?php } else { ?>
								<?php
								$user_id     = absint( $rewards_dynamic_rule[ 'refferal' ] ) ;
								$user        = get_user_by( 'id' , $user_id ) ;
								$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')' ;
								if ( ( float ) $woocommerce->version >= ( float ) ( '3.0.0' ) ) {
									?>
									<select multiple="multiple"  class="wc-customer-search" 
											name="rewards_dynamic_rule_manual[<?php echo esc_attr($rule_key) ; ?>][refferal]" 
											data-placeholder="<?php esc_html_e( 'Search Users' , 'rewardsystem' ) ; ?>" >
										<option value="<?php echo esc_html( $user_id ) ; ?>" selected="selected"><?php echo esc_attr( $user_string ) ; ?><option>
									</select>
								<?php } else { ?>
									<input type="hidden" class="wc-customer-search"
										   name="rewards_dynamic_rule_manual[<?php echo esc_attr($rule_key) ; ?>][refferal]"
										   data-placeholder="<?php esc_html_e( 'Search for a customer' , 'rewardsystem' ) ; ?>" 
										   data-selected="<?php echo esc_attr( $user_string ) ; ?>" 
										   value="<?php echo esc_html( $user_id ) ; ?>" data-allow_clear="true" />
										   <?php
								}
							}
							?>
						</td>
						<td class="column-columnname-link">    
						<?php
						if ( '' != @$rewards_dynamic_rule[ 'type' ] ) {
							?>
								<span> <b><?php esc_html_e( 'Automatic' , 'rewardsystem' ) ; ?></b></span>
								<?php
						} else {
							?>
								<span> <b><?php esc_html_e( 'Manual' , 'rewardsystem' ) ; ?></b></span>
								<?php
						}
						?>
							<input type="hidden" value="<?php echo esc_html(@$rewards_dynamic_rule[ 'type' ]) ; ?>"
								   name="rewards_dynamic_rule_manual[<?php echo esc_attr($rule_key) ; ?>][type]"/>
						</td>
						<td class="column-columnname num">
							<span class="rs-remove-manual-referral-link-rule button-secondary"><?php esc_html_e( 'Remove Linking' , 'rewardsystem' ) ; ?></span>
							<span class="rs_removed_rule"></span>
						</td>
					</tr>
					<?php
					$i ++;
				endif ;
			endforeach ;
		else :
			?>
			<tr>
				<td colspan="2"><?php esc_html_e( 'No Results Found' , 'rewardsystem' ) ; ?></td>
			</tr>  
			<?php
		endif ;

		if ( $searched_user ) :
			?>
			<tr>
				<td colspan="4">
					<a href="<?php echo esc_url( rs_get_endpoint_url( $query_args , '1' , admin_url( 'admin.php' ) ) ) ; ?>">
						<?php esc_html_e( 'Go Back' , 'rewardsystem' ) ; ?>
					</a>
				</td>
			</tr>
		<?php endif ; ?>
	</tbody>

	<tfoot>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<?php if ( ! $searched_user ) : ?>
				<td class="manage-column column-columnname num" scope="col"> 
					<span class="add button-primary"><?php esc_html_e( 'Add Linking' , 'rewardsystem' ) ; ?></span>
				</td>
				<?php
			else :
				?>
				<td></td>
			<?php endif ; ?>
		</tr>

		<?php if ( $page_count > 1 ) : ?>
			<tr>
				<td colspan="<?php echo esc_attr( '4' ) ; ?>" class="footable-visible">
					<?php
					$pagination = array(
						'page_count'      => $page_count ,
						'permalink'       => admin_url( 'admin.php' ) ,
						'query_args'      => $query_args ,
						'current_page'    => $current_page ,
						'prev_page_count' => ( 0 == ( $current_page - 1 ) ) ? ( $current_page ) : ( $current_page - 1 ) ,
						'next_page_count' => ( ( $current_page + 1 ) <= ( $page_count ) ) ? ( $current_page + 1 ) : ( $current_page )
							) ;

					srp_get_template( 'pagination.php' , $pagination ) ;
					?>
				</td>
			</tr>
		<?php endif ; ?>

		<tr>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Referrer Username' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname" scope="col"><?php esc_html_e( 'Buyer Username' , 'rewardsystem' ) ; ?></th>
			<th class="manage-column column-columnname-link" scope="col"><?php esc_html_e( 'Linking Type' , 'rewardsystem' ) ; ?></th>
			<?php if ( ! $searched_user ) : ?>
				<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Add Linking' , 'rewardsystem' ) ; ?></th>
				<?php
			else :
				?>
				<th class="manage-column column-columnname num" scope="col"><?php esc_html_e( 'Remove Linking' , 'rewardsystem' ) ; ?></th>
				<?php endif ; ?>
		</tr>
	</tfoot>
</table>
<?php
