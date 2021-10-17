<?php
/**
 * Admin Settings for CSV importer
 * @version 0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evocsv_settings{
	function __construct(){
		$this->options = get_option('evcal_options_evocsv');
		echo $this->content();
	}
	function content(){
		global $ajde;
		$ajde->load_ajde_backender();

		// Settings Tabs array
		$tabs = array(
			'evocsv_1'=>__('Import','eventon'), 
		);

		$focus_tab = (isset($_GET['tab']) )? sanitize_text_field( urldecode($_GET['tab'])):'evocsv_1';

		// Update or add options
			if( isset($_POST['evocsv_noncename']) && isset( $_POST ) ){				
				if ( wp_verify_nonce( $_POST['evocsv_noncename'], AJDE_EVCAL_BASENAME ) ){

					foreach($_POST as $pf=>$pv){
						$pv = (is_array($pv))? $pv: (htmlspecialchars ($pv) );
						$evo_options[$pf] = $pv;					
					}
					update_option('evcal_options_'.$focus_tab, $evo_options);
					$_POST['settings-updated']='Successfully updated values.';
				
				//nonce check	
				}else{
					die( __( 'Action failed. Please refresh the page and retry.', 'eventon' ) );
				}	
			}
		?>
		<div class="wrap" id='evocsv_settings'>
			<div id='eventon'><div id="icon-themes" class="icon32"></div></div>
			<h1><?php _e('Settings for Importing Events','eventon');?> </h1>
			<h2 class='nav-tab-wrapper' id='meta_tabs'>
				<?php					
					foreach($tabs as $nt=>$ntv){	
						echo "<a href='?page=evocsv&tab=".$nt."' class='nav-tab ".( ($focus_tab == $nt)? 'nav-tab-active':null)."' evo_meta='evocsv_1'>".$ntv."</a>";
					}			
				?>
			</h2>	
		<div class='metabox-holder'>		
		<?php			
		$updated_code = (isset($_POST['settings-updated']))? '<div class="updated fade"><p>'.$_POST['settings-updated'].'</p></div>':null;
		echo $updated_code;
				
		//TABS	
		switch ($focus_tab):	
		
		// Import step
			case "evocsv_1":
				echo "<div id='evocsv_1' class='postbox'><div class='inside'>";
				$steps = (!isset($_GET['steps']))?'ichi':$_GET['steps'];	
				echo $this->import_content($steps);
				echo "</div></div>";
			break;
		endswitch;
		echo "</div>";
	}

	// import
		function import_content($step){
			global $eventon_csv;

			switch ($step) {
				// reading file and showing results
				case 'ni':
					$this->display_events();
				break;
				case 'ichi':					
					ob_start();
				 	echo "<h2>".__('Get Started, Select a CSV file','eventon')."</h2>";
					echo "<p>".__('Select the properly formated CSV file with events to import.','eventon')."</p>";
					echo "<form action='".admin_url()."admin.php?page=evocsv&steps=ni' method='post' enctype='multipart/form-data'>";

						settings_fields('eventon_csv_field_grp'); 
						wp_nonce_field( $eventon_csv->plugin_path, 'eventon_csv_noncename' );

					echo "<input type='file' name='events_csv_file'/><br/><br/>";
					echo "<input type='submit' name='' class='btn_prime evo_admin_btn' value='Upload CSV file'/>";
					
					$this->print_guidelines();

					echo "</form>";

					echo ob_get_clean();
					
				break;
			}

		}

	// display fetched events list
		function display_events(){
			global $eventon_csv;

			if( !$this->csv_verify_nonce_post( 'eventon_csv_noncename'))
				return false;

			// verified nonce
			if (empty($_FILES['events_csv_file']['tmp_name'])) {
				$this->log['error'][] = 'No file uploaded, Please try again!.';

				$this->print_messages();
				$this->import_content('ichi');
				return;
			}

			// load uploaded file content
			require_once('DataSource.php');
			$time_start = microtime(true);
			$csv = new File_CSV_DataSource;
			$file = $_FILES['events_csv_file']['tmp_name'];
			$this->stripBOM($file);

			// check if file loaded correct
			if (!$csv->load($file)) {
				$this->log['error'][] = 'Failed to load file, Please try again!.';
				$this->print_messages();						
				$this->import_content('ichi');
				return;
			}		

			$csv->symmetrize();
			$COUNT = count($csv->connect());

			echo "<h2>".__('Verify Processed Events & Import','eventon')."</h2>";
			echo "<p>".__('Please look through the events processed from the uploaded CSV file and select the ones you want to import into your website calendar.','eventon'). '<br/>Processed <b>'.$COUNT.'</b> items total.'."</p>";

			// if no items present on processed
			if($COUNT==0)
				echo "<p style='padding:4px 10px; background-color:#F9E5E1'>".__('IMPORTANT! We could not process any events from the CSV file provided by you. Either the CSV file is not properly built or you have no items in the CSV file. Please make sure you have constructed the CSV file according the the guidelines.','eventon')."</p>";

			if($COUNT==0) return false;

			echo "<form class='evocsv_import_form' action='".admin_url()."admin.php?page=evocsv&steps=sun' method='post' enctype='multipart/form-data'>

				<p id='select_row_options'>
					<a class='deselect btn_triad evo_admin_btn'><span></span>Deselect All</a> <a class='select btn_triad evo_admin_btn'><span></span>Select All</a> <input id='evocsv_import_selected' style='display:none; float:right' type='submit' class='btn_prime evo_admin_btn' value='".__('Import Selected Events','eventon')."'/>
					<a id='evocsv_import_selected_items' class='btn_prime evo_admin_btn'><span></span>IMPORT</a>
				</p>

				<div id='evocsv_import_progress' style='display:none'>
					<p class='bar'><span></span></p>
					<p class='text'><em>0</em> out of <i>".$COUNT."</i> processed. <b class='loading'></b><span class='failed' style='display:none'><em></em> Failed</span></p>					
				</div>

				<div id='evocsv_import_results' style='display:none'>
					<p class='results'><b></b>Import complete! <span class='good'><em>1</em> Imported</span> <span class='bad'><em>0</em> Failed</span></p>
					<p><a href='".admin_url()."edit.php?post_type=ajde_events'>View all imported events</a></p>
				</div>

				<p id='evocsv_import_errors' style='display:none'>Error</p>
			

				<div id='evocsv_fetched_events'>";
				settings_fields('eventon_csv_field_grp'); 
				wp_nonce_field( $eventon_csv->plugin_path, 'eventon_csv_noncename' );

			echo "<table id='evocsv_events' class='wp-list-table widefat'>
				<thead><tr>
					<th>".__('Status','eventon')."</th>
					<th>".__('Post Status','eventon')."</th>
					<th>".__('Event Name','eventon')."</th>
					<th>".__('Description','eventon')."</th>
					<th>".__('Start Date & Time','eventon')."</th>
					<th>".__('End Date & Time','eventon')."</th>
					<th>".__('Location','eventon')."</th>
					<th>".__('Organizer','eventon')."</th>
					<th>".__('Image','eventon')."</th>
					</tr>
				</thead><tbody>";

			$count = 1;
			$textarea_fields = array('event_description');

			foreach($csv->connect() as $csv_data){

				$csv_data = apply_filters('evocsv_fetched_event_data', $csv_data);
				$csv_data = $this->validate_base_fields($csv_data);

				echo "<tr class='row' data-status='ss'><td>";
				
				//echo $csv_data['event_name'];
				
				$this->hidden_fields($csv_data, $count);
				$eventName = !empty($csv_data['event_name'])? html_entity_decode($csv_data['event_name']):'Event Name';

				echo "<span class='status ss' title='Selected'></span></td>";
				echo "<td><span>" . (!empty($csv_data['publish_status'])?$csv_data['publish_status']:'draft') ."</span></td>";
				echo "<td><span>".$eventName."</span></td>";
				echo "<td class='event_desc'><span class='".(!empty($csv_data['event_description'])?'check':'bar')."'></span></td>";

				echo "<td>{$csv_data['event_start_date']}<br/>{$csv_data['event_start_time']}</td>";
				echo "<td>{$csv_data['event_end_date']}<br/>{$csv_data['event_end_time']}</td>";
				
					$location_id = (!empty($csv_data['evo_location_id'])? $csv_data['evo_location_id']: false);
					$location_name = (!empty($csv_data['location_name'])? $csv_data['location_name']: false);
					$address = (!empty($csv_data['event_location'])? $csv_data['event_location']:'');

					$loc_title = ($location_name)? $location_name: ($location_id?'ID: '.$location_id:'');
					$location = (!empty($location_id) || !empty($address))? true: false;
				?>
				<td title='<?php echo $loc_title;?>'><span class='<?php echo ($location?'check':'bar'); ?> eventon_csv_icons'></span></td>
				
				<?php
					$organizer_id = (!empty($csv_data['evo_organizer_id'])? $csv_data['evo_organizer_id']:false);
					$organizer_name = (!empty($csv_data['event_organizer'])? $csv_data['event_organizer']:false);

					$org_title = ($organizer_name)? $organizer_name: ($organizer_id?'ID: '.$organizer_id:'');
					$organizer = ($organizer_id || $organizer_name)? true: false;
				?>
				<td title='<?php echo $org_title;?>'><span class='<?php echo ($organizer?'check':'bar');?> eventon_csv_icons'></span></td>
				<?php
					
					$passed_image = (!empty($csv_data['image_id']) || !empty($csv_data['image_url']))? true: false;

				echo "<td><span class='".($passed_image?'check':'bar')."'></span></td>";
				echo "</tr>";

				$count ++;
			}
			echo "</tbody></table></div>";

			echo "</form>";
		}

		// validate base fields such as date, time description
			function validate_base_fields($csv_data){
				// event date validation
					if(!empty($csv_data['event_start_date'])){
						if(preg_match('/^(\d{1,2})\/(\d{1,2})\/((?:\d{2}){1,2})$/', $csv_data['event_start_date']) ){
							$event_start_date = $event_start_date_val =$csv_data['event_start_date'];
						}else{	
							$event_start_date ="<p class='inner_check_no eventon_csv_icons'></p>";	
							$event_start_date_val =null;
						}
					}else{ $event_start_date ="<p class='inner_check_no eventon_csv_icons'></p>"; $event_start_date_val =null;	}
				
				// event start time validation
					if(!empty($csv_data['event_start_time'])){
						if(preg_match('/(1[0-2]|0?[0-9]):[0-5]?[0-9]?:(AM|PM)/', $csv_data['event_start_time']) ){
							$event_start_time = $event_start_time_val =$csv_data['event_start_time'];
						}else{	
							$event_start_time ="<p class='inner_check_no eventon_csv_icons'></p>";	
							$event_start_time_val =null;
						}
					}else{ $event_start_time ="<p class='inner_check_no eventon_csv_icons'></p>"; $event_start_time_val =null;	}
				// end time
					if(!empty($csv_data['event_end_time'])){
						if(preg_match('/(1[0-2]|0?[0-9]):[0-5]?[0-9]?:(AM|PM)/', $csv_data['event_end_time']) ){
							$event_end_time = $event_end_time_val =$csv_data['event_end_time'];
						}else{	
							$event_end_time ="<p class='inner_check_no eventon_csv_icons'></p>";	
							$event_end_time_val =$event_start_time_val;
						}
					}else{ $event_end_time ="<p class='inner_check_no eventon_csv_icons'></p>"; 
						$event_end_time_val =$event_start_time_val;	}								
				// event end date
					if(!empty($csv_data['event_end_date'])){
						if(preg_match('/^(\d{1,2})\/(\d{1,2})\/((?:\d{2}){1,2})$/', $csv_data['event_end_date']) ){
							$event_end_date = $event_end_date_val =$csv_data['event_end_date'];
						}else{	
							$event_end_date ="<p class='inner_check_no eventon_csv_icons'></p>";
							$event_end_date_val = $event_start_date_val;
						}
					}else{ // no end date present
						$event_end_date ="<p class='inner_check_no eventon_csv_icons'></p>";	
						$event_end_date_val = $event_start_date_val;
					}

				// description
					$event_description = (!empty($csv_data['event_description']))? 
						html_entity_decode(convert_chars(addslashes($csv_data['event_description'] ))): null;

				$csv_data['event_start_date'] = $event_start_date_val;
				$csv_data['event_start_time'] = $event_start_time_val;
				$csv_data['event_end_date'] = $event_end_date_val;
				$csv_data['event_end_time'] = $event_end_time_val;
				$csv_data['event_description'] = $event_description;

				return $csv_data;
			}

		// throw input and textfields hidden fields
			function hidden_fields($csv_data, $count){	
				global $eventon_csv;

				$textarea_fields = array('event_description');

				?><input class='input_status' type='hidden' name='events[<?php echo $count;?>][status]' value='ss'/>
				<?php
				$fields = $eventon_csv->admin->get_all_fields();

				if(!is_array($fields)) return;

				foreach($fields as $field){
					if( empty( $csv_data[$field]) ) continue;

					if(in_array($field, $textarea_fields)){
						echo "<textarea class='evocsv_event_data_row' style='display:none' name='events[{$count}][{$field}]'>". (!empty($csv_data[$field])? addslashes($csv_data[$field]):'')."</textarea>";
					}else{
						echo "<input class='evocsv_event_data_row' type='hidden' name='events[{$count}][{$field}]' ". 'value="'. ( addslashes($csv_data[$field]) ).'"/>';
					}
				}
			}

	    /** function to verify wp nonce and the $_POST array submit values	 */
			function csv_verify_nonce_post($post_field){
				global $_POST, $eventon_csv;

				if(isset( $_POST ) && !empty($_POST[$post_field]) && $_POST[$post_field]  ){
					if ( wp_verify_nonce( $_POST[$post_field],  $eventon_csv->plugin_path )){
						return true;
					}else{	
						$this->log['error'][] =__("Could not verify submission. Please try again.",'eventon');
						$this->print_messages();
						return false;	}
				}else{	
					$this->log['error'][] =__("Could not verify submission. Please try again.",'eventon');
					$this->print_messages();
					return false;	
				}
			}

		/** Print the messages for the csv settings	 */
			function print_messages(){
				if (!empty($this->log)) {
					
					if (!empty($this->log['error'])): ?>					
					<div class="error">
						<?php foreach ($this->log['error'] as $error): ?>
							<p class=''><?php echo $error; ?></p>
						<?php endforeach; ?>
					</div>			
					<?php endif; ?>					
					
					<?php if (!empty($this->log['notice'])): ?>
					<div class="updated fade">
						<?php foreach ($this->log['notice'] as $notice): ?>
							<p><?php echo $notice; ?></p>
						<?php endforeach; ?>
					</div>
					<?php endif; 
								
					$this->log = array();
				}
			}
		/** Print guidelines messages	 */
			function print_guidelines(){
				global $eventon, $eventon_csv;
				
				ob_start();
				
				require_once($eventon_csv->plugin_path.'/guide.php');				
				$content = ob_get_clean();
				
				echo $eventon->output_eventon_pop_window( 
					array('content'=>$content, 'title'=>'How to use CSV Importer', 'type'=>'padded')
				);
				?>					
					<h3><?php _e('**CSV file guidelines','eventon')?></h3>
					<p><?php _e('Please read the guidelines below for correct CSV format to import events from a CSV file successfully.','eventon');?> <?php _e('Make sure you are using a .csv file created using microsoft excel or google docs spreadsheet. It is VERY important to follow the guide and correct acceptable data format to properly import events from CSV file.','eventon');?></p>
					<p style='text-align:left'><a id='eventon_csv_guide_trig' class='btn_secondary evo_admin_btn ajde_popup_trig'><?php _e('Guide for CSV file','evocsv');?></a></p>

				<?php
			}	

	// SUPPRORTING
		//process string ids into an array
			function process_ids($ids){
				if(empty($ids))
					return false;

				$uids = str_replace(' ', '', $ids);
				if(strpos($uids, ',')=== false){
					$uids = array($uids);
				}else{
					$uids = explode(',', $uids);
				}
				return $uids;
			}

		// CSV file stripping
			function stripBOM($fname) {
		        $res = fopen($fname, 'rb');
		        if (false !== $res) {
		            $bytes = fread($res, 3);
		            if ($bytes == pack('CCC', 0xef, 0xbb, 0xbf)) {
		                $this->log['notice'][] = 'Getting rid of byte order mark...';
		                fclose($res);

		                $contents = file_get_contents($fname);
		                if (false === $contents) {
		                    trigger_error('Failed to get file contents.', E_USER_WARNING);
		                }
		                $contents = substr($contents, 3);
		                $success = file_put_contents($fname, $contents);
		                if (false === $success) {
		                    trigger_error('Failed to put file contents.', E_USER_WARNING);
		                }
		            } else {
		                fclose($res);
		            }
		        } else {
		            $this->log['error'][] = 'Failed to open file, aborting.';
		        }
		    }
}
new evocsv_settings();