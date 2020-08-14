<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

return array(
    'mandrill' => array(
        'ywces_mandrill_section_title' => array(
            'name' => esc_html__( 'Mandrill Settings', 'yith-woocommerce-coupon-email-system' ),
            'type' => 'title',
        ),
        'ywces_mandrill_enable'        => array(
            'name'    => esc_html__( 'Enable Mandrill', 'yith-woocommerce-coupon-email-system' ),
            'type'    => 'checkbox',
            'desc'    => esc_html__( 'Use Mandrill to send emails', 'yith-woocommerce-coupon-email-system' ),
            'id'      => 'ywces_mandrill_enable',
            'default' => 'no',
        ),
        'ywces_mandrill_apikey'        => array(
            'name'    => esc_html__( 'Mandrill API Key', 'yith-woocommerce-coupon-email-system' ),
            'type'    => 'text',
            'id'      => 'ywces_mandrill_apikey',
            'default' => '',
            'css'     => 'width: 400px;',
        ),
        'ywces_mandrill_section_end'   => array(
            'type' => 'sectionend',
        ),
    )
);