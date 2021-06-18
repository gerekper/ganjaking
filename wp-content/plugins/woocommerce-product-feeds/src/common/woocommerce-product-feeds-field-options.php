<?php

/**
 * Class WoocommerceProductFeedsFieldOptions
 *
 * Returns the valid dropdown options for various fields to allow re-use in different contexts.
 */
class WoocommerceProductFeedsFieldOptions {

	public static function adult_options() {
		return [
			'yes' => _x( 'Yes', 'Option for "adult" field', 'woocommerce_gpf' ),
			'no'  => _x( 'No', 'Option for "adult" field', 'woocommerce_gpf' ),
		];
	}

	public static function age_group_options() {
		return [
			'newborn' => _x( 'Newborn', 'Option for "age group" field', 'woocommerce_gpf' ),
			'infant'  => _x( 'Infant', 'Option for "age group" field', 'woocommerce_gpf' ),
			'toddler' => _x( 'Toddler', 'Option for "age group" field', 'woocommerce_gpf' ),
			'kids'    => _x( 'Kids', 'Option for "age group" field', 'woocommerce_gpf' ),
			'adult'   => _x( 'Adult', 'Option for "age group" field', 'woocommerce_gpf' ),
		];
	}

	public static function availability_options() {
		return [
			'in stock'     => _x( 'In stock', 'Option for "availability" field', 'woocommerce_gpf' ),
			'preorder'     => _x( 'Pre-order', 'Option for "availability" field', 'woocommerce_gpf' ),
			'backorder'    => _x( 'Backorder', 'Option for "availability" field', 'woocommerce_gpf' ),
			'out of stock' => _x( 'Out of stock', 'Option for "availability" field', 'woocommerce_gpf' ),
		];
	}

	public static function condition_options() {
		return [
			'new'         => _x( 'New', 'Option for "condition" field', 'woocommerce_gpf' ),
			'refurbished' => _x( 'Refurbished', 'Option for "condition" field', 'woocommerce_gpf' ),
			'used'        => _x( 'Used', 'Option for "condition" field', 'woocommerce_gpf' ),
		];
	}

	public static function energy_efficiency_class_options() {
		return [
			'A+++' => _x( 'A+++', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'A++'  => _x( 'A++', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'A+'   => _x( 'A+', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'A'    => _x( 'A', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'B'    => _x( 'B', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'C'    => _x( 'C', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'D'    => _x( 'D', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'E'    => _x( 'E', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'F'    => _x( 'F', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
			'G'    => _x( 'G', 'Option for "energy efficiency class" field', 'woocommerce_gpf' ),
		];
	}

	public static function gender_options() {
		return [
			'male'   => _x( 'Male', 'Option for "gender" field', 'woocommerce_gpf' ),
			'female' => _x( 'Female', 'Option for "gender" field', 'woocommerce_gpf' ),
			'unisex' => _x( 'Unisex', 'Option for "gender" field', 'woocommerce_gpf' ),
		];
	}

	public static function google_funded_promotion_eligibility_options() {
		return [
			'all'  => _x( 'All', 'Option for "Google funded promotion eligibility" fiield', 'woocommerce_gpf' ),
			'none' => _x( 'None', 'Option for "Google funded promotion eligibility" fiield', 'woocommerce_gpf' ),
		];
	}

	public static function is_bundle_options() {
		return [
			'on' => _x( 'Yes', 'Option for "is bundle" field', 'woocommerce_gpf' ),
		];
	}

	public static function pickup_method_options() {
		return [
			'buy'           => _x( 'Buy', 'Option for "pickup method" field', 'woocommerce_gpf' ),
			'reserve'       => _x( 'Reserve', 'Option for "pickup method" field', 'woocommerce_gpf' ),
			'ship to store' => _x( 'Ship to store', 'Option for "pickup method" field', 'woocommerce_gpf' ),
			'not supported' => _x( 'Not supported', 'Option for "pickup method" field', 'woocommerce_gpf' ),
		];
	}

	public static function pickup_sla_options() {
		return [
			'same day'   => _x( 'Same day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'next day'   => _x( 'Next day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'1-day'      => _x( '1-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'2-day'      => _x( '2-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'3-day'      => _x( '3-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'4-day'      => _x( '4-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'5-day'      => _x( '5-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'6-day'      => _x( '6-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'7-day'      => _x( '7-day', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
			'multi-week' => _x( 'Multi-week', 'Option for "pickup SLA" field', 'woocommerce_gpf' ),
		];
	}

	public static function size_system_options() {
		return [
			'US'  => _x( 'US', 'Option for "size system" field', 'woocommerce_gpf' ),
			'UK'  => _x( 'UK', 'Option for "size system" field', 'woocommerce_gpf' ),
			'EU'  => _x( 'EU', 'Option for "size system" field', 'woocommerce_gpf' ),
			'AU'  => _x( 'AU', 'Option for "size system" field', 'woocommerce_gpf' ),
			'BR'  => _x( 'BR', 'Option for "size system" field', 'woocommerce_gpf' ),
			'CN'  => _x( 'CN', 'Option for "size system" field', 'woocommerce_gpf' ),
			'FR'  => _x( 'FR', 'Option for "size system" field', 'woocommerce_gpf' ),
			'DE'  => _x( 'DE', 'Option for "size system" field', 'woocommerce_gpf' ),
			'IT'  => _x( 'IT', 'Option for "size system" field', 'woocommerce_gpf' ),
			'JP'  => _x( 'JP', 'Option for "size system" field', 'woocommerce_gpf' ),
			'MEX' => _x( 'MEX', 'Option for "size system" field', 'woocommerce_gpf' ),
		];
	}

	public static function size_type_options() {
		return [
			'regular'      => _x( 'Regular', 'Option for "size type" field', 'woocommerce_gpf' ),
			'petite'       => _x( 'Petite', 'Option for "size type" field', 'woocommerce_gpf' ),
			'plus'         => _x( 'Plus', 'Option for "size type" field', 'woocommerce_gpf' ),
			'big and tall' => _x( 'Big and tall', 'Option for "size type" field', 'woocommerce_gpf' ),
			'maternity'    => _x( 'Maternity', 'Option for "size type" field', 'woocommerce_gpf' ),
		];
	}
}
