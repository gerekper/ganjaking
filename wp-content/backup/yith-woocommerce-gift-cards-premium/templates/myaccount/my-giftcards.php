<?php
/**
 * My gift cards
 *
 * @package yith-woocommerce-gift-cards-premium\templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gift_card_columns = apply_filters( 'yith_ywgc_my_gift_cards_columns',
	array(
		'code'    => esc_html__( 'Code', 'yith-woocommerce-gift-cards' ),
		'balance' => esc_html__( 'Balance', 'yith-woocommerce-gift-cards' ),
		'usage'   => esc_html__( "Usage", 'yith-woocommerce-gift-cards' ),
		'status'  => esc_html__( 'Status', 'yith-woocommerce-gift-cards' ),
		'direct_link'  => esc_html__( 'Auto Apply', 'yith-woocommerce-gift-cards' ),
	) );

$user = wp_get_current_user();

$gift_cards_args = apply_filters( 'yith_ywgc_woocommerce_my_account_my_orders_query', array(
	'numberposts' => - 1,
	'fields'      => 'ids',
    'meta_query'  => array(
        'relation' => 'OR',
        array(
            'key'     => YWGC_META_GIFT_CARD_CUSTOMER_USER,
            'value'   => get_current_user_id(),
        ),
        array(
            'key'     => '_ywgc_recipient',
            'value'   => $user->user_email,
        ),
    ),
	'post_type'   => YWGC_CUSTOM_POST_TYPE_NAME,
	'post_status' => 'any',
) );

//  Retrieve the gift cards matching the criteria
$ids = get_posts( $gift_cards_args );


// Panel to register gift card codes manually

if ( isset( $_POST["ywgc-link-code"] ) ){

    $code =  $_POST["ywgc-link-code"];

    $args = array(
        'gift_card_number' => $code,
    );

    $gift_card = new YITH_YWGC_Gift_Card( $args );

    if ( ! is_object( $gift_card ) || $gift_card->ID == 0 ){
        echo '<div class="yith-add-new-gc-my-account-notice-message" style="font-weight: bolder">' . esc_html__( "The code added is not associated to any existing gift card.", 'yith-woocommerce-gift-cards' ) . '</div>';
    }
    else{
        if ( is_object( $gift_card ) && $gift_card->ID != 0 ){
            $user = wp_get_current_user();
            $gift_card->register_user($user->ID);
            echo '<div class="yith-add-new-gc-my-account-notice-message" style="font-weight: bolder">' . sprintf(esc_html__( "The gift card code %s is now linked to your account.", 'yith-woocommerce-gift-cards' ), $gift_card->get_code()) . '</div>';
        }
    }
}

?>
<form method="post" name="form-link-gift-card-to-user" class="form-link-gift-card-to-user" style="margin-top: 5px; display: none">
    <fieldset>
        <label for="ywgc-link-code"><?php _e ( "Link a gift card to your account ", 'yith-woocommerce-gift-cards' ); ?></label>
        <input placeholder="<?php _e ( "Your gift card code here ...", 'yith-woocommerce-gift-cards' ); ?>" type="text" name="ywgc-link-code" id="ywgc-link-code" value="">
        <button style="margin-top: 10px;" type="submit"><?php _e ( "Add it", 'yith-woocommerce-gift-cards' ); ?></button>
    </fieldset>
</form>


<div class="gift-card-panel-title-container">
    <h2 style="float: left"><?php echo apply_filters( 'yith_ywgc_my_account_my_giftcards', esc_html__( 'My Gift Cards', 'yith-woocommerce-gift-cards' ) ); ?></h2>
    <button class="yith-add-new-gc-my-account-button" style="float: right"><?php echo apply_filters( 'yith_ywgc_my_account_add_new_text', esc_html__( 'Add new', 'yith-woocommerce-gift-cards' ) ); ?></button>
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

							case 'usage' :
								$orders = $gift_card->get_registered_orders();

								if ( $orders ) {
									foreach ( $orders as $order_id ) {
										?>
										<a href="<?php echo wc_get_endpoint_url( 'view-order', $order_id ); ?>"
										   class="ywgc-view-order button">
											<?php printf( esc_html__( "Order %s", 'yith-woocommerce-gift-cards' ), $order_id ); ?>
										</a><br>
										<?php
									}
								} else {
									_e( "The code has not been used yet", 'yith-woocommerce-gift-cards' );
								}
								break;

                            case 'direct_link' :

                                $shop_page_url = apply_filters( 'yith_ywgc_shop_page_url', get_permalink ( wc_get_page_id ( 'shop' ) ) ? get_permalink ( wc_get_page_id ( 'shop' ) ) : site_url () );

                                $args = array(
                                    YWGC_ACTION_ADD_DISCOUNT_TO_CART => $gift_card->gift_card_number,
                                    YWGC_ACTION_VERIFY_CODE          => YITH_YWGC ()->hash_gift_card ( $gift_card ),
                                );

                                $direct_link = esc_url ( add_query_arg ( $args, $shop_page_url ) );

                                $link_text = esc_html__( "Apply this gift card", 'yith-woocommerce-gift-cards' );

                                echo '<a href="' . $direct_link . '" target="_blank">' . $link_text . '</a>';

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
