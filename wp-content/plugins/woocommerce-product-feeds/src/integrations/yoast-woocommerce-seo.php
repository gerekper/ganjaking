<?php

class WoocommerceGpfYoastWoocommerceSeo {

	private $list = [];

	public function run() {
		$this->list['disabled:yoast']                                      = '- Yoast WooCommerce SEO data -';
		$this->list['method:WoocommerceGpfYoastWoocommerceSeo::getGtin']   = __( 'Any of GTIN fields', 'woocommerce_gpf' );
		$this->list['method:WoocommerceGpfYoastWoocommerceSeo::getGtin8']  = __( 'GTIN 8 field', 'woocommerce_gpf' );
		$this->list['method:WoocommerceGpfYoastWoocommerceSeo::getGtin12'] = __( 'GTIN 12 field', 'woocommerce_gpf' );
		$this->list['method:WoocommerceGpfYoastWoocommerceSeo::getGtin13'] = __( 'GTIN 13 field', 'woocommerce_gpf' );
		$this->list['method:WoocommerceGpfYoastWoocommerceSeo::getGtin14'] = __( 'GTIN 14 field', 'woocommerce_gpf' );
		$this->list['method:WoocommerceGpfYoastWoocommerceSeo::getIsbn']   = __( 'ISBN field', 'woocommerce_gpf' );
		$this->list['method:WoocommerceGpfYoastWoocommerceSeo::getMpn']    = __( 'MPN field', 'woocommerce_gpf' );

		add_filter( 'woocommerce_gpf_custom_field_list', [ $this, 'register_prepopulate_options' ] );
		add_filter(
			'woocommerce_gpf_prepopulation_description',
			[ $this, 'render_prepopulation_descriptions' ],
			10,
			2
		);
	}

	/**
	 * Adds the items to the prepopulation dropdowns in the admin area.
	 *
	 * @param $list
	 *
	 * @return mixed
	 */
	public function register_prepopulate_options( $list ) {
		return $this->list + $list;
	}

	/**
	 * Outputs the description in the status report based on the chosen key.
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function render_prepopulation_descriptions( $description, $key ) {
		if ( ! empty( $this->list[ $key ] ) ) {
			return sprintf(
				// Translators: %s is the field name.
				__( '%s from Yoast WooCommerce SEO', 'woocommerce_gpf' ),
				$this->list[ $key ]
			);
		}

		return $description;
	}

	/**
	 * @param $product
	 *
	 * @return array
	 *
	 * phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	 */
	public static function getGtin( $product ) {
		$meta = get_post_meta( $product->get_id(), 'wpseo_global_identifier_values', true );
		if ( ! empty( $meta['isbn'] ) ) {
			$meta_value = $meta['isbn'];
		}
		if ( ! empty( $meta['gtin8'] ) ) {
			$meta_value = $meta['gtin8'];
		}
		if ( ! empty( $meta['gtin12'] ) ) {
			$meta_value = $meta['gtin12'];
		}
		if ( ! empty( $meta['gtin13'] ) ) {
			$meta_value = $meta['gtin13'];
		}
		if ( ! empty( $meta['gtin14'] ) ) {
			$meta_value = $meta['gtin14'];
		}
		if ( ! empty( $meta_value ) ) {
			return [ $meta_value ];
		}

		return [];
	}

	public static function getGtin8( $product ) {
		$meta = get_post_meta( $product->get_id(), 'wpseo_global_identifier_values', true );
		if ( ! empty( $meta['gtin8'] ) ) {
			return [ $meta['gtin8'] ];
		}

		return [];
	}

	public static function getGtin12( $product ) {
		$meta = get_post_meta( $product->get_id(), 'wpseo_global_identifier_values', true );
		if ( ! empty( $meta['gtin12'] ) ) {
			return [ $meta['gtin12'] ];
		}

		return [];
	}

	public static function getGtin13( $product ) {
		$meta = get_post_meta( $product->get_id(), 'wpseo_global_identifier_values', true );
		if ( ! empty( $meta['gtin13'] ) ) {
			return [ $meta['gtin13'] ];
		}

		return [];
	}

	public static function getGtin14( $product ) {
		$meta = get_post_meta( $product->get_id(), 'wpseo_global_identifier_values', true );
		if ( ! empty( $meta['gtin14'] ) ) {
			return [ $meta['gtin14'] ];
		}

		return [];
	}

	public static function getIsbn( $product ) {
		$meta = get_post_meta( $product->get_id(), 'wpseo_global_identifier_values', true );
		if ( ! empty( $meta['isbn'] ) ) {
			return [ $meta['isbn'] ];
		}

		return [];
	}

	public static function getMpn( $product ) {
		$meta = get_post_meta( $product->get_id(), 'wpseo_global_identifier_values', true );
		if ( ! empty( $meta['mpn'] ) ) {
			return [ $meta['mpn'] ];
		}

		return [];
	}
}
