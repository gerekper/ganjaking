<?php
defined('WYSIJA') or die('Restricted access');
/**
 * Class Import.
 *
 * Importing subscribers
 */
class WJ_Import extends WYSIJA_object {

	private $_header_row = array();
	private $_csv_data = array();
	private $_match = array();
	private $_ignore_invalid_date = array(); // specific for date fields
	private $_custom_fields = array();
	private $_email_key = '';
	private $_data_to_insert = array();
	private $_number_list = 0;
	private $_data_numbers = array();
	private $_line_delimiter = "\n";
	private $_duplicate_emails_count = array(); // detect the emails that are duplicates in the import file
	private $_csv_array = array(); // containing the csv into an array
	private $_ignored_row_count = 0; // the count of rows we ignore because there was no valid email present
	private $_lines_count = 0; // count the number of valid lines inserted in the DB
	private $_chunks_count = 0; // number of chunks we chopped from the csv
	private $_first_row_is_data = false; // means that there is no header on that csv file
	private $_emails_inserted_in_current_chunk = array(); // array of emails
	private $_csv_file_string = '';
	private $_data_result = array(); // used for importmatch refactor
	private $_regex_new_field = "/^new_field\|([^\|]+)\|(.+)$/i";
        private $_field_separators_to_test = array( ',', ';', "\t" );
	private	$_field_enclosers_to_test = array( '"', '' );

	function __construct() {
		if (!empty($_POST['wysija']['match']))
			$this->_match = $_POST['wysija']['match'];
		if (!empty($_POST['wysija']['ignore_invalid_date']))
			$this->_ignore_invalid_date = $_POST['wysija']['ignore_invalid_date'];
		if (!empty($_REQUEST['wysija']['user_list']['list']))
			$this->_number_list = count($_REQUEST['wysija']['user_list']['list']);
		if (!empty($_POST['firstrowisdata']))
			$this->_first_row_is_data = true;
		$this->_data_numbers = array('invalid' => array(), 'inserted' => 0, 'valid_email_processed' => 0, 'list_added' => 0, 'list_user_ids' => 0, 'list_list_ids' => $this->_number_list, 'emails_queued' => 0);
	}

	/**
	 * loading file data passed in a global variable
	 * @return type
	 */
	private function _get_temp_file_info() {
		if (!empty($_REQUEST['wysija']['dataImport'])){
                    return (array) json_decode(base64_decode($_REQUEST['wysija']['dataImport']),true);
                }
	}

	/**
	 * loading the file based on global parameters
	 * @return string
	 */
	private function _loading_file_content() {
		// try to access the temporary file created in the previous step
		$this->_csv_data = $this->_get_temp_file_info();

                if ( empty($this->_csv_data['csv']) || !is_string( $this->_csv_data['csv'] ) || preg_match('|[^a-z0-9#_.-]|i',$this->_csv_data['csv']) !== 0 ){
                    $this->error('Error with import file name');
                    return false;
                }

		$helper_file = WYSIJA::get('file', 'helper');

                if (!is_string($this->_csv_data['csv']) OR preg_match('|[^a-z0-9#_.-]|i',$this->_csv_data['csv']) !== 0 ){
                    die('Import file error.');
                }
		$result_file = $helper_file->get($this->_csv_data['csv'], 'import');

		if (!$result_file) {
			$upload_dir = wp_upload_dir();
			$this->error(sprintf(__('Cannot access CSV file. Verify access rights to this directory "%1$s"', WYSIJA), $upload_dir['basedir']), true);
			return false;
		}

		// get the temp csv file
		$this->_csv_file_string = file_get_contents($result_file);
	}

	private function _set_custom_fields(){
		$WJ_Field = new WJ_Field();
		$_custom_fields = $WJ_Field->get_all();

		if(!empty($_custom_fields)){
		   foreach($_custom_fields as $key => &$row){
				$this->_custom_fields['cf_'.$row->id] = $row;
			}
		}
	}

	/**
	 * Validate if a column is already matched previously
	 * @param string $column_name
	 * @return boolean
	 */
	private function _is_column_matched($column_name) {
	if (empty($this->_data_to_insert)) {
		return false;
	}
	$column_names = array_values($this->_data_to_insert);
	return in_array(trim($column_name), $column_names);
	}
	/**
	 * match columns together with the csv data based on the data passed
	 */
	private function _match_columns_to_insert() {

		// we're going through all of the selected value in each dropdown when importing
		foreach($this->_match as $csv_column_number => $column_in_user_table){

                        if (!is_string($column_in_user_table) OR preg_match('|[^a-z0-9#_\|.-]|i',$column_in_user_table) !== 0 ){
                            continue;
                        }
                        // Reduce matching twice the same column
			if ($this->_is_column_matched($column_in_user_table))
				continue;

			// Ignore `nomatch` columns
			if ($column_in_user_table == 'nomatch')
				continue;

			// Check if maybe it's a new field
			preg_match($this->_regex_new_field, $column_in_user_table, $maybe_newfield);
			if ( !empty($maybe_newfield) && in_array($maybe_newfield[1], array('date', 'input')) ){
				// TODO need to change to WJ_Field I guess when Marco is done moving the files
				// saving a new custom field
				$custom_field = new WJ_Field();

				$custom_field->set(array(
					'name' => $maybe_newfield[2],
					'type' => $maybe_newfield[1],
					'required' => false,
					'settings' => array(
						'label' => $maybe_newfield[2],
					)
				));

				$custom_field->save();

				// this is the column name in the database so this is where we need to import that field
				$column_in_user_table = $custom_field->user_column_name();
				$this->_match[$csv_column_number] = $column_in_user_table;
			}

			// keep the match of CSV column number to column key in our database
			// not sure why do we trim the column key ...
			$this->_data_to_insert[$csv_column_number] = trim($column_in_user_table);

			// this column is the email column, let's keep track of it for later validation etc..
			if($column_in_user_table == 'email'){
				$this->_email_key = $csv_column_number;
			}
		}

		$this->_set_custom_fields();

		// if the status column is not matched, we make sure that we have an entry for the status column so that we default it to some value on import
		if(!in_array('status',$this->_data_to_insert)){
			$this->_data_to_insert['status'] = 'status';
		}
	}

	/**
	 * build the header of the  import query
	 * @return type
	 */
	private function _get_import_query_header() {
		return 'INSERT INTO [wysija]user (`' . implode('` ,`', $this->_data_to_insert) . '`,`created_at`) VALUES ';
	}

	/**
	 *
	 * @param type $array_csv
	 */
	private function _check_duplicate_emails($array_csv) {
		// look for duplicate emails
		foreach ($array_csv as $keyline => $csv_line) {
			if (isset($csv_line[$this->_email_key])) {
				if (isset($this->_duplicate_emails_count[$csv_line[$this->_email_key]])) {
					$this->_duplicate_emails_count[$csv_line[$this->_email_key]]++;
					//$arra[$keyline]
				} else {
					$this->_duplicate_emails_count[$csv_line[$this->_email_key]] = 1;
				}
			} else {
				//if the record doesn't have the attribute email then we just ignore it
				$this->_ignored_row_count++;
				unset($array_csv[$keyline]);
			}
		}
	}

	/**
	 * save new column/field match to improve usability the next time our admin
	 * import a field with similar headers/columns names
	 * @return boolean
	 */
	private function _smart_column_match_recording() {
		if ($this->_first_row_is_data === false) {
			//save the importing fields to be able to match them the next time
			$import_fields = get_option('wysija_import_fields');

			foreach($this->_match as $csv_column_number => $column_in_user_table){
				if($column_in_user_table != 'nomatch') {

					// make a column name key
					$column_name_key = str_replace(array(' ','-','_'),'',strtolower($this->_header_row[$csv_column_number]));
					// fill in the array of "csv column name" / "user table column" matches
					$import_fields[$column_name_key] = $column_in_user_table;

				}
			}

			WYSIJA::update_option('wysija_import_fields' , $import_fields);
			return true;
		}
		return false;
	}

	/**
	 * import a csv type into wysija's subscribers' table
	 * @return type
	 */
	public function import_subscribers() {

		// import the contacts
		// 1-check that a list is selected and that there is a csv file pasted
		// 2-save the list if necessary
		// 3-save the contacts and record them for each list selected
		$this->_loading_file_content();
		// convert the csv file to an array of lines
		$this->_csv_array = $this->_csv_to_array( $this->_csv_file_string , 0 , $this->_csv_data['fsep'] , $this->_csv_data['fenc']);

		// check what columns of the csv have been matched together with our subscribers' table
		$this->_match_columns_to_insert();

		$this->_header_row = array_map('trim', $this->_csv_array[0]);

		// we process the sql insertion 200 by 200 so that we are safe with the server's memory and cpu
		$csv_chunks = array_chunk($this->_csv_array, 200);
		$this->_csv_array = null;
		$this->_chunks_count = 0;
		$this->_lines_count = 0;

		// setting up a timeout value on the sql side to avoid timeout when importing a lot of data apparently.
		global $wpdb;
		$wpdb->query('set session wait_timeout=600');

		// loop and insert the data chunks by chunks
		foreach ($csv_chunks as $key_chunk => $csv_chunk) {

			$this->_check_duplicate_emails($csv_chunk);

			$result = $this->_import_rows($csv_chunk);

			if ($result !== false)
				$this->_chunks_count++;
			else {
				// there was an error we try 3 more times the same chunk and se how it goes
				$try = 0;
				while ($result === false && $try < 3) {
					$result = $this->_import_rows($csv_chunk);
					if ($result !== false) {
						$this->_chunks_count++;
						break;
					}
					$try++;
				}

				if ($result === false) {
					$this->error(__('There seems to be an error with the list you\'re trying to import.', WYSIJA), true);
					return false;
				}
			}
			// increment the lines count
			$this->_lines_count += $result;
			// free up some memory
			unset($csv_chunks[$key_chunk]);
		}

		// useful the next time we import a file with the same format
		$this->_smart_column_match_recording();

		// refresh the total count of users in wysija
		$helper_user = WYSIJA::get('user','helper');
		$helper_user->refreshUsers();

		// keep only the real duplicate emails unset the unique ones
		// TODO check that this email duplicate function could be a memory sink hole
		// especially that right now we don't use its value
		foreach ($this->_duplicate_emails_count as $email_address => $times_email_in_csv) {
			if ($times_email_in_csv == 1)
				unset($this->_duplicate_emails_count[$email_address]);
		}

		// how come we need to do that sometimes? how a lines count could become negative?
		if ($this->_lines_count < 0)
			$this->_lines_count = 0;

		// all of these numbers were useful at some point when we were showing more information after an import
		$this->_data_numbers['ignored'] = ($this->_data_numbers['valid_email_processed'] - $this->_data_numbers['inserted']);
		$this->_data_numbers['ignored_list'] = ( ($this->_data_numbers['list_user_ids'] * $this->_data_numbers['list_list_ids']) - $this->_data_numbers['list_added'] );


		return $this->_data_numbers;
	}

	public function get_duplicate_emails_count() {
		return $this->_duplicate_emails_count;
	}

	/**
	 * convert a csv string to an array
	 * @param type $csv_file_content
	 * @param type $rows_to_read
	 * @param type $delimiter
	 * @param type $enclosure
	 * @return array
	 */
	private function _csv_to_array($csv_file_content, $rows_to_read = 0, $delimiter = ',', $enclosure = '') {
		$data = array();

                if( !(in_array($delimiter, $this->_field_separators_to_test) && in_array($enclosure, $this->_field_enclosers_to_test)) ){
                    $this->error('Unknown csv separators.');
                    return false;
                }

		// the new way for splitting a string into an array of lines
		$csv_data_array = explode( $this->_line_delimiter, $csv_file_content );

		$i=1;
		foreach($csv_data_array as $csv_line){
			if($rows_to_read!=0 && $i> $rows_to_read) return $data;

			// str_getcsv only exists in php5 and is a faster and cleaner function than our csv_explode
			if (!function_exists('str_getcsv') || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				$data[] = $this->_lines_explode($csv_line, $delimiter, $enclosure);
			} else {
				$data[] = str_getcsv($csv_line, $delimiter, $enclosure);

			}

			$i++;
		}

		return $data;
	}

	/**
	 *  explode lines to columns
	 * @param type $csv_line
	 * @param type $delimiter
	 * @param type $enclose
	 * @param type $preserve
	 * @return type
	 */
	private function _lines_explode($csv_line, $delimiter, $enclose, $preserve = false) {
		$resArr = array();
		$n = 0;
		if (empty($enclose)) {
			$resArr = explode($delimiter, $csv_line);
		} else {
			$expEncArr = explode($enclose, $csv_line);
			foreach ($expEncArr as $EncItem) {
				if ($n++ % 2) {
					array_push($resArr, array_pop($resArr) . ($preserve ? $enclose : '') . $EncItem . ( $preserve ? $enclose : ''));
				} else {
					$expDelArr = explode($delimiter, $EncItem);
					array_push($resArr, array_pop($resArr) . array_shift($expDelArr));
					$resArr = array_merge($resArr, $expDelArr);
				}
			}
		}

		return $resArr;
	}

	/**
	 * function processing a chunk of a csv array to import it in the DB
	 * @global object $wpdb
	 * @param array $csv_chunk
	 * @return boolean|string
	 */
	private function _import_rows($csv_chunk) {

		global $wpdb;

		$this->_emails_inserted_in_current_chunk = array();
		$time = time();
		$lines = array();
		$lines_count = count($csv_chunk);
		$columns_count = count($this->_data_to_insert);

		$query = $this->_get_import_query_header();

		// make sure that each line has the right numbers of columns if it doesn't then we can skip it
		foreach ($csv_chunk as $k => $line) {
			if (!(count($line) >= (count($this->_data_to_insert) - 1))) {
				unset($csv_chunk[$k]);
				$lines_count--;
			}
		}

		$valid_email_processed = 0;
		$j = 1;

		foreach ($csv_chunk as $key_line => $line) {

			// if first row is not data but header then we just skip it only on the first chunk
			if ($this->_first_row_is_data === false && $j == 1 && $this->_chunks_count == 0) {
				$j++;
				continue;
			}

			$i = 1;
			$values = array();

			// TODO maybe we should check the value of the status column so that if we export a wysija's subscribers' list
			// and import it again in another site then we keep the status
			if (isset($this->_data_to_insert['status']))
				$line['status'] = 1;

			foreach ($line as $key_column => &$value_column) {

				// make sure this column is a column we want to insert in our DB
				if (isset($this->_data_to_insert[$key_column])) {
					$column_name = $this->_data_to_insert[$key_column];
					$original_value_column = $value_column;
					$value_column = $this->_validate_value($column_name, $value_column);

					// if it is a a date column, we convert it as a unixtimestamp
					if(isset($this->_custom_fields[$column_name]) && $this->_custom_fields[$column_name]->type == 'date'){
						$value_column = strtotime($value_column);
					}

					// this kind of result invalidates the whole row
					if ($value_column === false) {
						// record the invalid row and continue with the loop
						$this->_data_numbers['invalid'][] = $original_value_column;
						unset($csv_chunk[$key_line]);
						$lines_count--;
						continue 2;
					} else {
						// only if this is the email row we record an entry in the recorded emails and the email processed count
						if ($this->_email_key === $key_column) {
							$this->_emails_inserted_in_current_chunk[] = $value_column;
							$valid_email_processed++;
						}
					}

					// prepare the query
					$values[] = "'" . esc_sql($value_column, $wpdb->dbh) . "'";
				}
			}

			$values[] = $time;
			$lines[] .= "(" . implode(', ', $values) . ")";
		}

		$query .= implode(', ', $lines);

	  	// update values when the user already exists
		$query .= ' ON DUPLICATE KEY UPDATE '.implode(', ', array_map(array($this, 'process_data_to_insert'), $this->_data_to_insert));

		// replace query to import the subscribers
		$model_wysija = new WYSIJA_model();
		$import_query = $model_wysija->query($query);

		$lines_count = $wpdb->rows_affected;
		$this->_data_numbers['inserted'] += $wpdb->rows_affected;
		$this->_data_numbers['valid_email_processed'] += $valid_email_processed;

		if ($import_query === false) {
			$this->error(__('Error when inserting emails.', WYSIJA), true);
			return false;
		}
		$time_now = time();
		$result_query_import_list = $this->_import_new_users_into_lists($time_now);

		$this->_trigger_active_autoresponders($time_now);

		if ($result_query_import_list === false) {
			$this->error(__('Error when inserting list.', WYSIJA), true);
			return false;
		}

		if ($import_query == 0)
			return '0';

		return $lines_count;
	}

	/**
	 * used to validate or cast values before importing
	 * TODO should we add a type for import of custom fields ?
	 * Comment : Marco, feel free to modify entirely
	 * @param type $column_name
	 * @param type $value
	 */
	function _validate_value($column_name, $value) {
		$value = esc_attr(trim($value));

		switch ($column_name) {
			case 'email':
				return is_email($value);
				break;
			case 'status':

				if (in_array(strtolower($value), array('subscribed', 'confirmed', 1, '1', 'true'))) {
					return 1;
				} elseif (in_array(strtolower($value), array('unsubscribed', -1, '-1', 'false'))) {
					return -1;
				} elseif (in_array(strtolower($value), array('unconfirmed', 0, '0'))) {
					return 0;
				}
				else
					return 1;
				break;
			default :
				return $value;
		}
	}

	/**
	 * take care of active autoresponders retro-activity
	 * @param type $time_now
	 * @return boolean
	 */
	private function _trigger_active_autoresponders($time_now) {
		$helper_email = WYSIJA::get('email', 'helper');
		$model_wysija = new WYSIJA_model();

		// list the active auto responders emails
		$active_autoresponders_per_list = $helper_email->get_active_follow_ups(array('email_id', 'params'), true);

		if (!empty($active_autoresponders_per_list)) {
			foreach ($_REQUEST['wysija']['user_list']['list'] as $list_id) {
				// checking if this list has a list of follow ups
				if (isset($active_autoresponders_per_list[$list_id])) {
					// for each follow up of that list we queu an email
					foreach ($active_autoresponders_per_list[$list_id] as $key_queue => $follow_up) {
						// insert query per active followup
						$query_queue = 'INSERT IGNORE INTO [wysija]queue (`email_id` ,`user_id`,`send_at`) ';
						$query_queue .= ' SELECT ' . (int)$follow_up['email_id'] . ' , B.user_id , ' . ($time_now + $follow_up['delay']);
						$query_queue .= ' FROM [wysija]user_list as B';
						$query_queue .= ' WHERE B.list_id=' . (int) $list_id . ' AND sub_date=' . $time_now;

						$model_wysija->query($query_queue);

						$this->_data_numbers['emails_queued'] += $wpdb->rows_affected;
					}
				}
			}
			return true;
		}
		return false;
	}

	/**
	 *
	 * @global type $wpdb
	 * @param type $time_now
	 * @return type
	 */
	private function _import_new_users_into_lists($time_now) {
		global $wpdb;
		$wpdb->rows_affected = 0;
		$model_wysija = new WYSIJA_model();

		$user_ids = $this->_get_imported_user_ids();

		// insert query per list
		$query = 'INSERT IGNORE INTO [wysija]user_list (`list_id` ,`user_id`,`sub_date`) VALUES ';

		foreach ($_REQUEST['wysija']['user_list']['list'] as $keyl => $list_id) {
			if (empty($list_id))
				continue;
			// for each list pre selected go through that process
			foreach ($user_ids as $key => $user_data) {

				// inserting each user id to this list
				$query.='(' . (int)$list_id . ' , ' . (int)$user_data['user_id'] . ' , ' . (int)$time_now . ')';

				// if this is not the last row we put a comma for the next row
				if (count($user_ids) > ($key + 1)) {
					$query.=' , ';
				}
			}

			// if this is not the last row we put a comma for the next row
			if (count($_REQUEST['wysija']['user_list']['list']) > ($keyl + 1)) {
				$query.=',';
			}
		}

		$result_query = $model_wysija->query($query);

		$this->_data_numbers['list_added']+=$wpdb->rows_affected;
		$this->_data_numbers['list_user_ids']+=count($user_ids);
		return $result_query;
	}

	/**
	 * get a list of user_ids freshly imported
	 * @return type
	 */
	private function _get_imported_user_ids() {
		$model_user = WYSIJA::get('user', 'model');
		// select query to get all of the ids of the emails that have just been inserted
		return $model_user->get(array('user_id'), array('email' => $this->_emails_inserted_in_current_chunk));
	}


	/**
	 * this save a default list of column matching to ease the import process, this is done only the first time when the
	 * field is not populated yet
	 */
	private function _save_default_import_field_match(){
		$import_fields = get_option('wysija_import_fields');
		if (!$import_fields) {
			$import_fields = array(
				'fname' => 'firstname',
				'firstname' => 'firstname',
				'prenom' => 'firstname',
				'nom' => 'lastname',
				'name' => 'lastname',
				'lastname' => 'lastname',
				'lname' => 'lastname',
				'ipaddress' => 'ip',
				'ip' => 'ip',
				'addresseip' => 'ip',
			);
			WYSIJA::update_option('wysija_import_fields', $import_fields);
		}
	}


	/**
	 * put the whole file or posted string into one string and clean up the string that may cause trouble during import
	 * @return boolean
	 */
	private function _get_csv_file_cleaned_up(){
		// is it a text import or a file import?
		if($_POST['wysija']['import']['type'] == 'copy'){
			if(!isset($_POST['wysija']['user_list']['csv'])){
				// memory limit has been reached
				$this->error(__('The list you\'ve pasted is too big for the browser. <strong>Upload the file</strong> instead.', WYSIJA), true);
				return false;
			}
			$this->_csv_file_string = trim(stripslashes($_POST['wysija']['user_list']['csv']));

		}else{
			// move_uploaded_file($_importfile, $destination);
			$this->_csv_file_string = trim(file_get_contents($_FILES['importfile']['tmp_name']));
		}

		$this->_csv_file_string = str_replace(array("\r", "\n\n", "\n\t\t\n\t\n\t\n\t\n", "\n\t\t\n\t\n\t\n", "\xEF\xBB\xBF", "\n\t\n", "\n(+1)"), array("\n", "\n", "\n ;", "\n", '', ';', ''), $this->_csv_file_string);


		// this might be gmail recipients(copy-paste from a gmail account) rare paste ...
		 if(!preg_match_all('/<([a-z0-9_\'&\.\-\+])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,10})+>/i' , $this->_csv_file_string , $matches)){
			  //return false;
		 }else{

			if (substr($this->_csv_file_string, -1) != ",")
				$this->_csv_file_string = trim($this->_csv_file_string) . ',';


			if (count($matches[0]) == count($matches)) {
				//this is gmail simple paste
				$this->_csv_file_string = str_replace(array('>,', '<'), array("\n", ','), $this->_csv_file_string);
			}
			$this->_csv_file_string = trim($this->_csv_file_string);
		}
	}


	/**
	 * try to figure out the format of that CSV file, which separators and enclosure strings does it use
	 * @return boolean
	 */
	private function _run_test_on_csv_file(){
		// try different set of enclosure and separator for the csv which can have different look depending on the data carried

		$this->_csv_data['fsep'] = false;
		$this->_csv_data['fenc'] = '';
		$helper_user = WYSIJA::get('user','helper');

		foreach($this->_field_enclosers_to_test as $enclosure){
			foreach($this->_field_separators_to_test as $fsep){

				// testing different combinations of separator and enclosers
				$this->_csv_array = $this->_csv_to_array( $this->_csv_file_string, 10, $fsep, $enclosure );

				// make sure that our CSV has more than one row and that it has the same number of values in the first row and the second row
				if ( ( count( $this->_csv_array ) > 1 && count( $this->_csv_array[0] ) == count( $this->_csv_array[1] ) ) ) {
					if ( count( $this->_csv_array[0] ) > 1 || $helper_user->validEmail( trim( $this->_csv_array[0][0] ) ) || $helper_user->validEmail( trim( $this->_csv_array[1][0] ) ) ) {
						$this->_csv_data['fsep'] = $fsep;
						$this->_csv_data['fenc'] = $enclosure;
						break(2);
					}
				}
			}
		}

		 // if we didn't manage to find a separator in that file then it is not a csv file and we come out
		if(empty($this->_csv_data['fsep'])){
			$this->notice( sprintf(
				"%s <a href='http://support.mailpoet.com/knowledgebase/importing-subscribers-with-a-csv-file/' target='_blank'>%s</a>",
				__("The data you are trying to import doesn't appear to be in the CSV format (Comma Separated Values).", WYSIJA),
				__("Read More", WYSIJA)
			) );
			$this->notice(__('The first line of a CSV file should be the column headers : "email","lastname","firstname".',WYSIJA));
			$this->notice(__('The second line of a CSV file should be a set of values : "joeeg@example.com","Average","Joe".',WYSIJA));

			$this->notice(__('The two first lines of the file you\'ve uploaded are as follow:', WYSIJA));

			$arraylines = explode("\n", $this->_csv_file_string);

			if (empty($arraylines[0]))
				$text = __('Line is empty', WYSIJA);
			else
				$text = $arraylines[0];
			$this->notice('<strong>' . esc_html($text) . '</strong>');

			if (empty($arraylines[1]))
				$text = __('Line is empty', WYSIJA);
			else
				$text = $arraylines[1];
			$this->notice('<strong>' . esc_html($text) . '</strong>');

			return false;
		}

		// test the size of the file
		$temp_csv_array = $this->_csv_to_array($this->_csv_file_string, 0, $this->_csv_data['fsep'], $this->_csv_data['fenc']);

		$this->_data_result['totalrows'] = count($temp_csv_array);
		end($temp_csv_array);
		$this->_data_result['lastrow'] = current($temp_csv_array);
	}


	/**
	 * save the CSV string into a file so that we can use it later in the next step of the process
	 * @return boolean|string
	 */
	private function _save_csv_file(){
		// try to make a wysija dir to save the import file
		$helper_file = WYSIJA::get('file', 'helper');
		$result_dir = $helper_file->makeDir('import');
		if (!$result_dir) {
			return false;
		}

		$file_name = 'import-' . time() . '.csv';
		$handle = fopen($result_dir . $file_name, 'w');
		fwrite($handle, $this->_csv_file_string);
		fclose($handle);

		return $file_name;
	}


	/**
	 * detect which column is an email one
	 */
	private function _test_csv_emails(){
		$found_email = 0;
		$this->_email_key = array();
		$helper_user = WYSIJA::get('user', 'helper');
		foreach ($this->_csv_array as $csv_row) {
			foreach ($csv_row as $key_column => $value_column) {
				if ($helper_user->validEmail(trim($value_column))) {
					$found_email++;

					$this->_email_key[$key_column] = $this->_csv_array[0][$key_column];
				}
			}
		}

		$this->_data_result['errormatch'] = false;
		$check_again = __('Check again what you\'re trying to import.',WYSIJA);
		if (($found_email < 1)) {
			$this->error(__('We couldn\'t find any valid addresses in the first 10 rows.', WYSIJA).' '.$check_again, true);
		}

		if ((count($this->_csv_array) < 2)) {
			$this->error(__('You\'re import file is not valid.', WYSIJA).' '.$check_again, true);
		}
	}


	/**
	 * function used to scan the CSV before trying to match each column with our own fields
	 * @return boolean
	 */
	public function scan_csv_file(){
		$this->_data_result = array();

		// make sure the import match will be as easy as possible with default matches
		if( $this->_save_default_import_field_match() === false) return false;
		// get the csv into a string and check for messy email address etc
		if( $this->_get_csv_file_cleaned_up() === false) return false;
		// find out the format of that CSV file comma semi colon etc ..
		if( $this->_run_test_on_csv_file() === false) return false;

		// save the string into a file for later use
		$file_name = $this->_save_csv_file();
		if ($file_name === false)
			return false;

		// look for the email column
		$this->_test_csv_emails();

		// stock the data we will use in the next page
		$this->_data_result['csv'] = $this->_csv_array;

		$data_import = array(
			'csv' => $file_name,
			'fsep' => $this->_csv_data['fsep'],
			'fenc' => $this->_csv_data['fenc']);
		$this->_data_result['dataImport'] = base64_encode(json_encode($data_import));

		$this->_data_result['keyemail'] = $this->_email_key;

		//test if the first row is data or not
		//test the email column
		foreach ($this->_data_result['keyemail'] as $k)
			$this->_email_key = $k;


		// test whether the first row is a data row or is a descriptive header row
		$helper_user = WYSIJA::get('user','helper');
		if($helper_user->validEmail( $this->_email_key )){
			$this->_data_result['firstrowisdata']=true;
		}else{
			$this->_data_result['totalrows']--;
		}

		return $this->_data_result;
	}

	private function process_data_to_insert($v) {
		return '`'.$v.'` = VALUES(`'.$v.'`)';
  	}

}
