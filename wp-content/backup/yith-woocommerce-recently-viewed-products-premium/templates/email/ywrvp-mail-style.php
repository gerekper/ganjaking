<?php
/**
 * YITH WooCommerce Recently Viewed Products
 */

if ( !defined( 'YITH_WRVP' ) ) {
    exit; // Exit if accessed directly
}

$hide_style_for_email_templates = isset( $hide_style_for_email_templates ) ? $hide_style_for_email_templates : false;
if ( !$hide_style_for_email_templates ): ?>
#template_body,
#template_header {
width: 640px;
}
<?php endif; ?>
#body_content table td {
padding: 25px;
}
#ywrvp-products-list,
#ywrvp-product-info,
#ywrvp-custom-products-list,
#ywrvp-custom-product-info {
width: 100%;
}
#ywrvp-products-list {
margin: 20px 0;
border: 1px solid #ebebeb;
padding: 10px;
}
#ywrvp-products-list td.ywrvp-product {
padding: 12px 0;
border-bottom: 1px solid #ebebeb;
}
#ywrvp-products-list td.ywrvp-product.last {
border-bottom: none;
}
#ywrvp-product-info .product-image {
padding: 0 5px 0 0;
width: 1px;
}
#ywrvp-product-info .product-info {
padding: 0 5px;
vertical-align: top;
}
#ywrvp-product-info .product-info h3 {
margin-top: 0;
font-size: 17px;
}
#ywrvp-product-info .product-action {
padding: 0 0 0 5px;
text-align: right;
width: 120px;
}
#ywrvp-product-info .product-action div {
text-align: right;
}
#ywrvp-product-info div {
margin-bottom: 5px;
}
#coupon-code {
text-align: center;
padding: 15px 0;
}
#coupon-code span {
background-repeat: no-repeat;
font-size: 20px;
padding: 15px 15px 15px 55px;
background-position: 10px center;
border: 2px solid #ebebeb;
color: #557da1;
border-radius: 10px !important;
}
#ywrvp-custom-products-list td.ywrvp-custom-product {
padding: 10px;
border: 1px solid #ebebeb;
}
#ywrvp-custom-product-info .product-image,
#ywrvp-custom-product-info .product-action {
padding: 10px;
}
#ywrvp-custom-product-info .product-image {
width: 1px;
}
#ywrvp-custom-product-info .product-info {
padding: 0;
vertical-align: top;
}
#ywrvp-custom-product-info .product-info h3{
font-size: 17px;
}
#ywrvp-custom-product-info .product-action div {
background-color: #557da1;
margin-top: 5px;
text-align: center;
}

a.mail-button {
color: #fff;
background-color: #557da1;
text-decoration: none;
padding: 8px;
text-transform: uppercase;
font-size: 10px;
line-height: 27px;
border-radius: 3px !important;
}

#ywrvp-product-info .product-image img,
#ywrvp-custom-product-info .product-image img {
max-width: 100px;
height: auto;
}