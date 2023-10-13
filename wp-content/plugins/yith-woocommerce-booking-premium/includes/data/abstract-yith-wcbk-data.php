<?php
/**
 * Abstract Class YITH_WCBK_Data
 *
 * Handles generic data interaction which is implemented by
 * the different data store classes.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Data' ) ) {
	/**
	 * Class YITH_WCBK_Data
	 *
	 * @since 3.0.0
	 */
	abstract class YITH_WCBK_Data extends WC_Data {

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook_prefix() {
			return 'yith_wcbk_' . $this->object_type . '_get_';
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook() {
			return 'yith_wcbk_' . $this->object_type . '_get';
		}

		/**
		 * Get an object property
		 *
		 * @param string $prop    The property.
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return mixed
		 */
		protected function get_prop( $prop, $context = 'view' ) {
			$value = parent::get_prop( $prop, $context );

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook(), $value, $prop, $this );
			}

			return $value;
		}

		/**
		 * Store options in DB
		 *
		 * @return int
		 */
		public function save() {
			if ( ! $this->data_store ) {
				return $this->get_id();
			}

			do_action( 'yith_wcbk_before_' . $this->object_type . '_object_save', $this, $this->data_store );

			if ( $this->get_id() ) {
				$this->data_store->update( $this );
			} else {
				$this->data_store->create( $this );
			}

			do_action( 'yith_wcbk_after_' . $this->object_type . '_object_save', $this, $this->data_store );

			return $this->get_id();
		}

		/*
		|--------------------------------------------------------------------------
		| Meta Data
		|--------------------------------------------------------------------------
		|
		| Methods for handling meta data.
		*/

		/**
		 * Check if the key is an internal one.
		 *
		 * @param string $prop Key to check.
		 *
		 * @return bool   true if it's an internal key, false otherwise
		 */
		public function is_internal_prop( $prop ) {
			return array_key_exists( $prop, $this->data ) && ( is_callable( array( $this, 'set_' . $prop ) ) || is_callable( array( $this, 'get_' . $prop ) ) );
		}

		/**
		 * Check if the key is an internal one.
		 *
		 * @param string $key Key to check.
		 *
		 * @return bool   true if it's an internal key, false otherwise
		 */
		protected function is_internal_meta_key( $key ) {
			return ! empty( $key ) && $this->data_store && in_array( $key, $this->data_store->get_internal_meta_keys(), true );
		}

		/**
		 * Add meta data from array.
		 *
		 * @param array $data Key/Value pairs.
		 */
		public function update_metas( $data ) {
			if ( ! empty( $data ) && is_array( $data ) ) {
				foreach ( $data as $key => $value ) {
					$this->update_meta_data( $key, $value );
				}
			}
		}
	}
}
