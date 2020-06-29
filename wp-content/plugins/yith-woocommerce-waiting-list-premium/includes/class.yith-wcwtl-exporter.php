<?php
/**
 * Class YITH_WCWTL_Exporter.
 *
 * @package YITH WooCommerce Waiting List
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_WCWTL_Exporter' ) ) {
	/**
	 * Waiting List Export - handles data export.
	 *
	 * @package     YITH WooCommerce Waiting List
	 * @version     1.6.0
	 */
	class YITH_WCWTL_Exporter {

		/**
		 * @var \WC_Product
		 */
		protected $product = false;

		/**
		 * @var array
		 */
		protected $list = array();

		/**
		 * YITH_WCWTL_Exporter constructor.
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @param integer $id
		 */
		public function __construct( $id ) {
			$this->product = wc_get_product( intval( $id ) );
			if ( $this->product ) {
				$this->list = yith_waitlist_get( $this->product );
			}
		}

		/**
		 * Export data
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return mixed
		 */
		public function run() {

			if ( ! $this->product || empty( $this->list ) ) {
				return false;
			}

			$name = 'export-' . str_replace( ' ', '-', trim( $this->product->get_title() ) );
			$list = $this->build_csv_array( $this->list );

			$this->download_csv( $list, $name );

			return true;
		}

		/**
		 * Build csv array
		 *
		 * @since  1.5.3
		 * @author Francesco Licandro
		 * @param array $list
		 * @return array
		 */
		protected function build_csv_array( $list ) {

			$csv_array = array();
			$fields    = array(
				'eMail',
				'Is Customer',
			);
			// first add csv fields
			$csv_array[] = $fields;

			foreach ( $list as $email ) {
				$t   = array();
				$t[] = $email;
				$t[] = get_user_by( 'email', $email ) !== false ? 'yes' : 'no';

				$csv_array[] = $t;
			}

			return $csv_array;
		}

		/**
		 * Transform an array to CSV file
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @param        $array
		 * @param string $filename
		 * @param string $delimiter
		 */
		protected function download_csv( $array, $filename, $delimiter = ',' ) {
			header( "X-Robots-Tag: noindex, nofollow", true );
			header( "Content-Type: application/csv" );
			header( "Content-Disposition: attachment; filename=\"" . $filename . ".csv\";" );
			$f = fopen( 'php://output', 'w' );

			foreach ( $array as $line ) {
				fputcsv( $f, $line, $delimiter );
			}
			exit;
		}

	}
}
