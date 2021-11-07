<?php
/**
 * Google product attributes.
 *
 * @see https://developers.facebook.com/docs/commerce-platform/catalog/categories#cat-spec-fields
 *
 * @package WC_Instagram/Data
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

return array(
	'age_group' => array(
		'label'   => __( 'Age group', 'woocommerce-instagram' ),
		'options' => array(
			'adult'    => __( 'Adult', 'woocommerce-instagram' ),
			'all ages' => __( 'All ages', 'woocommerce-instagram' ),
			'infant'   => __( 'Infant', 'woocommerce-instagram' ),
			'kids'     => __( 'Kids', 'woocommerce-instagram' ),
			'newborn'  => __( 'New born', 'woocommerce-instagram' ),
			'teen'     => __( 'Teen', 'woocommerce-instagram' ),
			'toddler'  => __( 'Toddler', 'woocommerce-instagram' ),
		),
	),
	'color'     => array(
		'label' => __( 'Color', 'woocommerce-instagram' ),
	),
	'gender'    => array(
		'label'   => __( 'Gender', 'woocommerce-instagram' ),
		'options' => array(
			'male'   => __( 'Male', 'woocommerce-instagram' ),
			'female' => __( 'Female', 'woocommerce-instagram' ),
			'unisex' => __( 'Unisex', 'woocommerce-instagram' ),
		),
	),
	'material'  => array(
		'label' => __( 'Material', 'woocommerce-instagram' ),
	),
	'pattern'   => array(
		'label' => __( 'Pattern', 'woocommerce-instagram' ),
	),
	'size'      => array(
		'label' => __( 'Size', 'woocommerce-instagram' ),
	),
);
