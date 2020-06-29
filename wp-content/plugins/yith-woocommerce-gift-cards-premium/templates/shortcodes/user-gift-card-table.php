<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user = wp_get_current_user();

if ( $user->ID == 0 )
    return;

$user_roles = isset( $role ) ? $role : '';

if ( $user_roles != ''){
    $user_role_array = explode( ',', $user_roles );

    $current_user_roles_array = $user->roles;

    $allow_role = (count(array_intersect($current_user_roles_array, $user_role_array))) ? true : false;

    if ( $allow_role == false )
        return;
}


$gift_card_columns = apply_filters( 'yith_ywgc_my_gift_cards_columns',
	array(
		'code'    => esc_html__( 'Code', 'yith-woocommerce-gift-cards' ),
		'balance' => esc_html__( 'Balance', 'yith-woocommerce-gift-cards' ),
		'status'  => esc_html__( 'Status', 'yith-woocommerce-gift-cards' ),
		'direct_link'  => esc_html__( 'Auto Apply', 'yith-woocommerce-gift-cards' ),
	) );



$gift_cards_args = apply_filters( 'yith_ywgc_woocommerce_my_account_my_orders_query', array(
	'numberposts' => - 1,
	'fields'      => 'ids',
    'meta_query'  => array(
            'relation' => 'AND',
                array(
                    'key'     => '_ywgc_balance_total',
                    'value'   => 0,
                    'compare' => '>'
                ),
        array(
            'relation' => 'OR',
                array(
                    'key'     => YWGC_META_GIFT_CARD_CUSTOMER_USER,
                    'value'   => get_current_user_id(),
                ),
                array(
                    'key'     => '_ywgc_recipient',
                    'value'   => $user->user_email,
                ),
    )
    ),
	'post_type'   => YWGC_CUSTOM_POST_TYPE_NAME,
	'post_status' => 'any',
) );


//  Retrieve the gift cards matching the criteria
$ids = get_posts( $gift_cards_args );


?>
<div class="gift-card-panel-title-container">
    <h2 style="float: left"><?php echo apply_filters( 'yith_ywgc_my_giftcards_table_shortcode_title', esc_html__( 'My Gift Cards', 'yith-woocommerce-gift-cards' ) ); ?></h2>
</div>

<?php if ( $ids ) : ?>


	<table class="shop_table shop_table_responsive my_account_giftcards">
		<thead>
		<tr>
			<?php foreach ( $gift_card_columns as $column_id => $column_name ) : ?>
				<th class="<?php echo esc_attr( $column_id ); ?>"><span
						class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
			<?php endforeach; ?>
		</tr>
		</thead>

		<tbody>
		<?php foreach ( $ids as $gift_card_id ) :

			$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_card_id ) );

			if ( ! $gift_card->exists() ) {
				continue;
			}
			?>
			<tr class="ywgc-gift-card status-<?php echo esc_attr( $gift_card->status ); ?>">
				<?php foreach ( $gift_card_columns as $column_id => $column_name ) : ?>
					<td class="<?php echo esc_attr( $column_id ); ?> "
					    data-title="<?php echo esc_attr( $column_name ); ?>">

						<?php
						$value = '';
						switch ( $column_id ) {
							case 'code' :
								$value = $gift_card->get_code();
								break;
							case 'balance' :
								$value = wc_price( apply_filters( 'yith_ywgc_get_gift_card_price', $gift_card->get_balance() ) );
								break;

							case 'status' :
								$value = ywgc_get_status_label( $gift_card );
								$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');
								if ( $gift_card->expiration ) {
									$value .= '<br>' . sprintf( _x( 'Expires on: %s (%s)', 'gift card expiration date', 'yith-woocommerce-gift-cards' ), date_i18n( $date_format, $gift_card->expiration ),$date_format );
								}
								break;
								
                            case 'direct_link' :

                                $redirect_page = isset( $redirect ) ? $redirect : 'cart';

                                $shop_page_url = apply_filters( 'yith_ywgc_shop_page_url', get_permalink ( wc_get_page_id ( $redirect_page ) ) ? get_permalink ( wc_get_page_id ( $redirect_page ) ) : site_url () );

                                $args = array(
                                    YWGC_ACTION_ADD_DISCOUNT_TO_CART => $gift_card->gift_card_number,
                                    YWGC_ACTION_VERIFY_CODE          => YITH_YWGC ()->hash_gift_card ( $gift_card ),
                                );

                                $direct_link = esc_url ( add_query_arg ( $args, $shop_page_url ) );

                                $link_text = esc_html__( "Apply this gift card", 'yith-woocommerce-gift-cards' );

                                echo '<a href="' . $direct_link . '" target="_self">' . $link_text . '</a>';

                                break;

							default:
								$value = apply_filters( 'yith_ywgc_my_account_column', '', $column_id, $gift_card );
						}

						if ( $value ) {
							echo '<span>' . $value . '</span>';
						}
						?>

					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
    <br>
    <br>
    <br>
    <?php _e( 'No gift cards found.', 'yith-woocommerce-gift-cards' ); ?>
<?php endif; ?>
