<?php

add_filter(
	'woocommerce_product_export_meta_value',

	/**
	 * Should return date in mysql date format for "_wc_pre_orders_availability_datetime" meta key.
	 *
	 * @since 2.0.0
	 *
	 * @param string $meta_value Meta value.
	 * @param WC_Meta_Data $meta Meta data object.
	 *
	 * @returns mixed
	 */
	function ( $meta_value, $meta ) {
		if ( $meta_value && '_wc_pre_orders_availability_datetime' === $meta->key ) {
			$meta_value = date( 'Y-m-d H:i:s', $meta_value );
		}

		return $meta_value;
	},
	10,
	2
);

add_filter(
	'woocommerce_product_import_process_item_data',

	/**
	 * Should return date in timestamp date format for "_wc_pre_orders_availability_datetime" meta key.
	 *
	 * @since 2.0.0
	 *
	 * @param array $parsed_data Array of csv row data.
	 *
	 * @returns array
	 */
	function ( $data ) {
		if ( empty( $data['meta_data'] ) ) {
			return $data;
		}

		$meta_keys = wp_list_pluck( $data['meta_data'], 'key' );
		$index     = array_search( '_wc_pre_orders_availability_datetime', $meta_keys, true );

		if ( false !== $index && $data['meta_data'][ $index ]['value'] ) {
			$data['meta_data'][ $index ] = array_merge(
				$data['meta_data'][ $index ],
				array( 'value' => strtotime( $data['meta_data'][ $index ]['value'] ) )
			);
		}

		return $data;
	}
);
