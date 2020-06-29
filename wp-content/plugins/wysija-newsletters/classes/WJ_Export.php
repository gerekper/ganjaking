<?php
defined('WYSIJA') or die('Restricted access');
/**
 * Class Export.
 *
 * Exporting subscribers
 */
class WJ_Export extends WYSIJA_object {

	private $_file_header      = '';
	private $_file_handle      = null;
	private $_user_ids_rows    = 0;
	private $_user_ids         = array();
	private $_fields           = array();
	private $_export_batch     = 2000;
	private $_filter_list      = '';
	private $_filter_confirmed = '';
	private $_fields_separator = ';';
	private $_lines_separator  = "\n";
	private $_base_encode      = 'UTF-8';
	/**
	 * Default encoding of exported files
	 * @var string
	 */
	private $_output_encode    = 'UTF-8//TRANSLIT//IGNORE';

	//needed for tung's batch actions
	public $batch_select = null;

	function __construct() {
		if ( ! empty( $_POST['wysija']['export']['fields'] ) ) {
			$this->_fields = $_POST['wysija']['export']['fields'];
		}

		if ( ! empty( $_POST['wysija']['export']['user_ids'] ) ) {
			$this->_user_ids = $this->_get_posted_user_ids();
		}
		if ( ! empty($_POST['wysija']['export']['filter']['list'] ) ) {
			$this->_filter_list = $_POST['wysija']['export']['filter']['list'];
		}

		// how do we separate the values in the files commas or semy colons ?
		if ( ! empty($_POST['wysija']['export']['format'] ) ) {
			$this->_fields_separator = $_POST['wysija']['export']['format'];
		}

		if ( ! empty($_POST['wysija']['export']['filter']['confirmed'] ) ) {
			$this->_filter_confirmed = $_POST['wysija']['export']['filter']['confirmed'];
		}

		$this->set_output_encode();
	}

	/**
	 * Set output encoding based on platform
	 */
	protected function set_output_encode() {
		$this->_output_encode = WYSIJA::is_windows() ? 'Windows-1252' : 'UTF-8//TRANSLIT//IGNORE';
	}

	/**
	 * get the number of rows exported
	 * @return type
	 */
	public function get_user_ids_rows() {
		return $this->_user_ids_rows;
	}

	/**
	 * get an array of user_ids from the global $_POST
	 * @return type
	 */
	private function _get_posted_user_ids() {
            return (array) json_decode( base64_decode( $_POST['wysija']['export']['user_ids'] ), true );
	}

	/**
	 * get the query used to select a bung of ids
	 * @return string
	 */
	private function _get_query_users_ids() {

		// based on filters prepare a query to get a list of user_ids
		if ( ! empty( $this->batch_select ) ) { // batch select and export
			$this->_user_ids_rows = $this->batch_select['count'];
			$qry = $this->batch_select['original_query'];
		} else { // export all list
			// prepare the filters
			$filters = array();
			if ( ! empty( $this->_filter_list ) ) {
				if ( ! is_array( $this->_filter_list ) ) {
					$this->_filter_list = array( $this->_filter_list );
				}
			}
			$filters['lists'] = $this->_filter_list;

			// include also unsubscribed and unconfirmed
			if ( ! empty( $this->_filter_confirmed ) ) {
				$filters['status'] = 'subscribed';
			}

			$model_user = WYSIJA::get( 'user', 'model' );
			$select     = array( 'A.user_id' );
			$qry = $model_user->get_subscribers( $select, $filters, '', $return_query = true );
		}

		return $qry;
	}

	/**
	 * get chunks of subscribers ids and push them step by step to the export file
	 */
	private function _get_chunks_user_ids() {

		$model_user      = WYSIJA::get( 'user', 'model' );
		$this->_user_ids = array();
		$query_user_ids  = $this->_get_query_users_ids();
		$query_count     = str_replace( array( 'DISTINCT(A.user_id)', 'DISTINCT(B.user_id)' ), 'COUNT(DISTINCT(A.user_id))', $query_user_ids );

		if ( empty( $this->_user_ids_rows ) ) {
			$useridsrows_result   = $model_user->getResults( $query_count, ARRAY_N );
			$this->_user_ids_rows = (int) $useridsrows_result[0][0];
		}

		if ( $this->_user_ids_rows <= $this->_export_batch ) {
			$user_ids_db = $model_user->getResults( $query_user_ids, ARRAY_N );

			foreach ( $user_ids_db as $uarr ) {
				$this->_user_ids[] = $uarr[0];
			}

			$this->_push_data_to_export_file();
		} else {
			$pages = ceil( $this->_user_ids_rows / $this->_export_batch ); //pagination
			for ( $i = 0; $i < $pages; $i++ ) {
				$query_batch = $query_user_ids . ' ORDER BY user_id ASC LIMIT ' . ($i * $this->_export_batch) . ',' . $this->_export_batch;
				$user_ids_db = $model_user->getResults( $query_batch, ARRAY_N );
				foreach ( $user_ids_db as $uarr ) {
					$this->_user_ids[] = $uarr[0];
				}
				$this->_push_data_to_export_file();

				unset($user_ids_db); //free memory
			}
		}
	}

	/**
	 * split the user_ids array into chunks, load the fields of all the concerned
	 * users and push the data to the file
	 */
	private function _push_data_to_export_file() {
		$user_ids_chunks = array(); // chunk rows into separated batchs, limit by $this->_export_batch
		$user_ids_chunks = array_chunk( $this->_user_ids, 200 );
		$this->_user_ids = null; // free memory

		$model_user = WYSIJA::get( 'user', 'model' );
                $model_user->refresh_columns();
		foreach ( $user_ids_chunks as $user_id_chunk ) {
			// get the full data for that specific chunk of ids
			$data = $model_user->get( $this->_fields, array( 'user_id' => $user_id_chunk ) );

			if ( in_array( 'created_at', $this->_fields ) ) {
				foreach ( $data as $key => $row ) {
					$data[$key]['created_at'] = date_i18n( get_option( 'date_format' ), $row['created_at'] );
				}
			}

			$rows_count = count( $data );

			// As required in Wysija/plugin#798 removed BOM from file
			// fwrite( $this->_file_handle, "\xEF\xBB\xBF" );

			// append content to the file
			foreach ( $data as $k => $row ) {
				$row = array_map(array($this, '_escape_commas_and_quotes'), $row);
				$row_string     = implode( $this->_fields_separator, $row );
				$encoded_string = iconv( $this->_base_encode, $this->_output_encode, $row_string );
				fwrite( $this->_file_handle, $encoded_string . ( $rows_count !== $k ? $this->_lines_separator : '' ) );
			}
		}
	}

	function _escape_commas_and_quotes($value) {
		$value = str_replace('"', '""', $value);
		return (preg_match('/,/', $value)) ?
			'"' . $value . '"' :
			$value;
	}

	/**
	 * simply prepare the header of the file based on the fields
	 */
	private function _prepare_headers() {
		$model_user = WYSIJA::get( 'user_field', 'model' );
		$database_fields = $model_user->getFields();

		$name_fields = array();
		//prepare the columns that need to be exported
		foreach ( $this->_fields as $key_field ) {
			$name_fields[] = $database_fields[$key_field];
		}

		//create the export file step by step
		$row_string         = implode( $this->_fields_separator, $name_fields ) . $this->_lines_separator;
		$encoded_string     = iconv( $this->_base_encode, $this->_output_encode, $row_string );
		$this->_file_header = $encoded_string;
	}

	/**
	 * export the subscribers
	 * @return type
	 */
	public function export_subscribers() {

		//generate temp file
		$helper_file = WYSIJA::get( 'file', 'helper' );
		$this->_prepare_headers();
		$result_file = $helper_file->temp( $this->_file_header, 'export', '.csv' );

		//open the created file in append mode
		$this->_file_handle = fopen( $result_file['path'], 'a' );

		//get a list of user_ids to export
		if ( ! empty( $this->_user_ids ) && empty( $this->batch_select ) ) {

			$this->_user_ids_rows = count( $this->_user_ids );
			$this->_push_data_to_export_file();
		} else {

			$this->_get_chunks_user_ids();
		}

		fclose( $this->_file_handle );
		return $result_file;
	}

}
