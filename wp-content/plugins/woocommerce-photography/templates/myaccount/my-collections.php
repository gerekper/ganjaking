<?php
/**
 * My Collections.
 *
 * Shows user collections on the account page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<h2><?php echo apply_filters( 'woocommerce_photography_my_account_collections_title', __( 'My Collections', 'woocommerce-photography' ) ); ?></h2>

<form action="" method="post">
	<table class="shop_table shop_table_responsive my-account-collections">
		<thead>
			<tr>
				<th><span class="nobr"><?php _e( 'Cover image', 'woocommerce-photography' ); ?></span></th>
				<th><span class="nobr"><?php _e( 'Title', 'woocommerce-photography' ); ?></span></th>
				<th><span class="nobr"><?php _e( 'Number of photos', 'woocommerce-photography' ); ?></span></th>
				<th><span class="nobr"><?php _e( 'Visibility', 'woocommerce-photography' ); ?></span></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$customers_can_change_visibility = apply_filters( 'woocommerce_photography_customers_can_change_visibility', true );
				$customer_can_change_visibility  = apply_filters( 'woocommerce_photography_customer_' . get_current_user_id() . '_can_change_visibility', true );
				foreach ( $collections as $collection_id ) :
					$collection = get_term( $collection_id, 'images_collections' );

					// Continue with the collection is empty.
					if ( ! $collection ) {
						continue;
					}

					$url                                 = get_term_link( $collection_id, 'images_collections' );
					$visibility                          = WC_Photography_WC_Compat::get_term_meta( $collection_id, 'visibility', true );
					$customers_can_change_visibility_col = apply_filters( 'woocommerce_photography_customers_can_change_visibility_collection_' . $collection_id, true );
					?>
				<tr>
					<td class="collection-thumbnail" data-title="<?php _e( 'Cover image', 'woocommerce-photography' ); ?>" class="cover-img"><?php
						$image        = '';
						$thumbnail_id = WC_Photography_WC_Compat::get_term_meta( $collection_id, 'thumbnail_id', true );

						if ( $thumbnail_id ) {
							$image = wp_get_attachment_image( $thumbnail_id, 'shop_thumbnail' );
						} else {
							$image = wc_placeholder_img( 'shop_thumbnail' );
						}

						echo $image;
					?></td>
					<td class="collection-title" data-title="<?php _e( 'Title', 'woocommerce-photography' ); ?>"><a href="<?php echo $url; ?>"><?php echo esc_html( $collection->name ); ?></a></td>
					<td class="collection-count" data-title="<?php _e( 'Number of photos', 'woocommerce-photography' ); ?>"><?php echo intval( $collection->count ); ?></td>
					<td class="collection-visibility" data-title="<?php _e( 'Visibility', 'woocommerce-photography' ); ?>">
						<?php if ( $customers_can_change_visibility && $customer_can_change_visibility && $customers_can_change_visibility_col ) : ?>
							<select>
								<option value="restricted" <?php selected( $visibility, 'restricted' ); ?>><?php _e( 'Restricted', 'woocommerce-photography' ); ?></option>
								<option value="public" <?php selected( $visibility, 'public' ); ?>><?php _e( 'Public', 'woocommerce-photography' ); ?></option>
							</select>
							<button class="button disabled" type="button" data-collection_id="<?php echo intval( $collection_id ); ?>" disabled="disabled"><?php _e( 'Save', 'woocommerce-photography' ); ?></button>
						<?php else : ?>
							<?php echo wc_photography_i18n_collection_visibility( $collection_id, $visibility ); ?>
						<?php endif ?>
					</td>
					<td class="collection-actions order-actions wc-actions"><a class="button" href="<?php echo $url; ?>"><?php _e( 'View Photographs', 'woocommerce-photography' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</form>
