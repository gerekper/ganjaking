<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$email_sent      = '';
$clicks          = '';
$recovered_carts = '';
$conversion      = '';
if ( isset( $_GET['post'] ) ) {
	$post_id         = $_GET['post'];
	$email_sent      = intval( apply_filters( 'ywrac_email_template_sent_counter', get_post_meta( $post_id, '_email_sent_counter', true ), $post_id ) );
	$clicks          = intval( apply_filters( 'ywrac_email_template_clicks_counter', get_post_meta( $post_id, '_email_clicks_counter', true ), $post_id ) );
	$recovered_carts = intval( apply_filters( 'ywrac_email_template_cart_recovered', get_post_meta( $post_id, '_cart_recovered', true ), $post_id ) );
	if ( $email_sent != 0 && $email_sent != '' ) {
		$conversion = number_format( 100 * $recovered_carts / $email_sent, 2, '.', '' ) . ' %';
	}
}
return array(
	'label'    => esc_html__( 'Email Report', 'yith-woocommerce-recover-abandoned-cart' ),
	'pages'    => 'ywrac_email', // or array( 'post-type1', 'post-type2')
	'context'  => 'normal', // ('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs'     => array(

		'stats' => array(
			'label'  => esc_html__( 'Report', 'yith-woocommerce-recover-abandoned-cart' ),
			'fields' => apply_filters(
				'ywrac_email_metabox_stat',
				array(
					'ywrac_email_stat'      => array(
						'label' => '',
						'desc'  => sprintf( '<span class="label">%s</span><span class="value">%d</span>', esc_html__( 'Sent Emails', 'yith-woocommerce-recover-abandoned-cart' ), $email_sent ),
						'type'  => 'simple-text',
					),

					'ywrac_click'           => array(
						'label' => '',
						'desc'  => sprintf( '<span class="label">%s</span><span class="value">%d</span>', esc_html__( 'Clicks', 'yith-woocommerce-recover-abandoned-cart' ), $clicks ),
						'type'  => 'simple-text',
					),

					'ywrac_recovered_carts' => array(
						'label' => '',
						'desc'  => sprintf( '<span class="label">%s</span><span class="value">%d</span>', esc_html__( 'Recovered Carts', 'yith-woocommerce-recover-abandoned-cart' ), $recovered_carts ),
						'type'  => 'simple-text',
					),

					'ywrac_conversion'      => array(
						'label' => '',
						'desc'  => sprintf( '<span class="label">%s</span><span class="value">%s</span>', esc_html__( 'Conversion Rate', 'yith-woocommerce-recover-abandoned-cart' ), $conversion ),
						'type'  => 'simple-text',
					),

				)
			),
		),
	),
);
