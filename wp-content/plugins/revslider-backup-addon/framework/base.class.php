<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsAddOnBackupBase {
	
	const MINIMUM_VERSION = '6.0';
	
	protected function systemsCheck() {
		
		if(!class_exists('RevSliderFront')) {
		
			return 'add_notice_plugin';
		
		}
		else if(!version_compare(RevSliderGlobals::SLIDER_REVISION, RsAddOnBackupBase::MINIMUM_VERSION, '>=')) {
		
			return 'add_notice_version';
		
		}
		else if(get_option('revslider-valid', 'false') == 'false') {
		
			 return 'add_notice_activation';
		
		}
		
		return false;
		
	}
	
	protected function loadClasses() {
		
		$isAdmin = is_admin();
		
		if($isAdmin) {
			
			//handle update process, this uses the typical ThemePunch server process
			require_once(static::$_PluginPath . 'admin/includes/update.class.php');
			$update_admin = new RevAddOnBackupUpdate(static::$_Version);

			add_filter('pre_set_site_transient_update_plugins', array($update_admin, 'set_update_transient'));
			add_filter('plugins_api', array($update_admin, 'set_updates_api_results'), 10, 3);
			
			// hook into ajax
			add_filter('revslider_do_ajax', array($this, 'add_backup_ajax_functions'), 10, 3);
			
			add_action('revslider_slide_updateSlideFromData_post', array($this, 'check_add_new_backup'), 10, 3); //hooks into the saving process of a Slide
			
			// hook no longer exists in the plugin
			// add_action('revslider_slide_updateStaticSlideFromData_post', array($this, 'check_add_new_backup_static'), 10, 3); //hooks into the saving process of a Static Slide

			add_action('revslider_slide_deleteSlide', array($this, 'delete_backup_full')); //hooks into the deletion process of a Slide
			add_action('revslider_slider_deleteAllSlides', array($this, 'delete_backup_full_slider')); //hooks into the deletion process of a Slide
			
			self::create_tables(); //creates tables needed for this plugin to work
			
			// Add-Ons page
			// add_filter('rev_addon_dash_slideouts', array($this, 'addons_page_content'));
			
			// admin CSS/JS
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			
			// admin slider class no longer needed
			//require_once(static::$_PluginPath . 'admin/includes/slider.class.php');
			
			// admin init
			//new RsBackupSliderAdmin(static::$_PluginTitle, static::$_Version);
			
		}
		
		//build js global var for activation
		add_filter( 'revslider_activate_addon', array($this, 'get_var'), 10, 2);
		
	}
	
	/**
	 * Load the textdomain
	 **/
	protected function _loadPluginTextDomain(){
		
		load_plugin_textdomain('rs_' . static::$_PluginTitle, false, static::$_PluginPath . 'languages/');
		
	}
	
		
	// load admin scripts
	public function enqueue_admin_scripts($hook) {

		if($hook === 'toplevel_page_revslider' || $hook === 'slider-revolution_page_rev_addon') {

			if(!isset($_GET['page'])) return;
			
			$page = $_GET['page'];
			if($page !== 'revslider' && $page !== 'rev_addon') return;
			
			$_handle = 'rs-' . static::$_PluginTitle . '-admin';
			$_base   = static::$_PluginUrl . 'admin/assets/';
			
			wp_enqueue_style($_handle, $_base . 'css/revslider-' . static::$_PluginTitle . '-addon-admin.css', array(), static::$_Version);
			wp_enqueue_script($_handle, $_base . 'js/revslider-' . static::$_PluginTitle . '-addon-admin.js', array('jquery', 'revbuilder-admin'), static::$_Version, true);
			wp_localize_script($_handle, 'revslider_backup_addon', $this->get_var() );

		}
		
	}
	
	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public function get_var($var='',$slug='revslider-backup-addon') {
		
		if($slug == 'revslider-backup-addon'){
		
			return array(
				
				'md5' => substr(md5(rand()), 0, 7),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'enabled' => get_option('revslider_backup_enabled'),
				'bricks' => array(
					'backup' => __('Backups', 'revslider-backup-addon'),
					'backup_addon' => __('Backups AddOn', 'revslider-backup-addon'),
					'placeholder' => __('Select', 'revslider-backup-addon'),
					'load_backup' => __('Load Backup', 'revslider-backup-addon'),
					'preview_backup' => __('Preview Backup', 'revslider-backup-addon'),
					'show_backups' => __('Show Backups for this Slide', 'revslider-backup-addon'),
					'select_backup' => __('Select Backup', 'revslider-backup-addon'),
					'no_backups' => __('No backups found for the selected Slide', 'revslider-backup-addon'),
					'restore' => __('Restore Slide Backup from', 'revslider-backup-addon')
					
				)
			);
			
		}
		
		return $var;
	
	}
	
	/**
	 * Change Enable Status of this Add-On
	 *
	 * @since    1.0.0
	 */
	private function change_addon_status($enabled) {
		update_option( "revslider_backup_enabled", $enabled );	
	}
	
	/**
	 * adds ajax functions
	 * @since: 1.0.0
	 */
	public function add_backup_ajax_functions($return = "", $action = "", $data = array()){

		switch($action){
			
			case 'wp_ajax_enable_revslider-backup-addon':
				$this->change_addon_status( 1 );
				return  __('Backups AddOn enabled', 'revslider-backup-addon');
			break;
			
			case 'wp_ajax_disable_revslider-backup-addon':
				$this->change_addon_status( 0 );
				return  __('Backups AddOn disabled', 'revslider-backup-addon');
			break;
			
			case 'fetch_slide_backups':
				$slide_id = $data['slideID'];
				$slide_data = $this->fetch_slide_backups($slide_id, true);
				
				return array('data' => $slide_data);
			break;
			case 'restore_slide_backup':
			
				$backup_id = intval($data['id']);
				$slide_id = $data['slide_id'];
				$session_id = esc_attr($data['session_id']);
				$response = $this->restore_slide_backup($backup_id, $slide_id, $session_id);
				
				if($response !== true) {
					
					$f = new RevSliderFunctions();
					$f->throw_error(__("Backup restoration failed...",'rs_backup'));
					
				}
				
				$urlRedirect = $this->getViewUrl("slide","id=$slide_id");
				$responseText = __("Backup restored, redirecting...",'rs_backup');
				return $responseText;
			break;
			
			/*
				previews do not exist anymore
			*/
			/*
			case 'preview_slide_backup':
				//check if we are static or not
				
				$operations = new RevSliderOperations();
				
				ob_start();
				//first get data
				$backup_id = intval($data['id']);
				$slide_id = $data['slide_id'];
				
				if($backup_id == "empty_output"){
					echo '<div class="message_loading_preview">'.__("Loading Preview...",'rs_backup').'</div>';
					exit();
				}
				
				$my_data = $this->fetch_backup($backup_id);
				
				$sliderID = $my_data['slider_id'];
				
				if(strpos($slide_id, 'static_') !== false){
					$my_data['slideid'] = $slide_id;
					
					add_filter('revslider_enable_static_layers', array('rs_backup_slide', 'disable_static_slide_for_preview'));
					
				}else{
					$my_data['slideid'] = $my_data['slide_id'];
				}
				
				$f = new RevSliderFunctions();
				$my_data['params'] = (array)json_decode($my_data['params']);
				$my_data['layers'] = (array)json_decode($my_data['layers']);
				$my_data['layers'] = $f->class_to_array($my_data['layers']);
				$my_data['settings'] = (array)json_decode($my_data['settings']);
				
				
				//asort($my_data['layers']);
				
				$output = new RevSliderOutput();
				$output->setOneSlideMode($my_data);

				$operations->previewOutput($sliderID,$output);
				$html = ob_get_contents();
				
				ob_clean();
				ob_end_clean();
				
				//add button to apply the Backup
				//$html .= '<div >'.__('', 'rs_backup').'</div>';
				echo $html;
				exit;
				//self::ajaxResponseData(array('markup' => $html));
			break;
			*/
			default:
				return $return;
			// end default
		}

	}
	
	/**
	 * fetch all slide revisions by slide_id
	 * @since: 1.0.0
	 */
	private function fetch_slide_backups($slide_id, $basic = false){
		global $wpdb;
		
		if(strpos($slide_id, 'static_') !== false){
			$slide = new RevSliderSlide();
			$slide_id = $slide->get_static_slide_id(str_replace('static_', '', $slide_id));
			$where = array($slide_id);
			$where[] = 'true';
		}else{
			$where = array($slide_id);
			$where[] = 'false';
		}
		
		if($basic){

			$record = $wpdb->get_results($wpdb->prepare("SELECT `id`, `slide_id`, `slider_id`, `created` FROM ".$wpdb->prefix . 'revslider_backup_slides'." WHERE slide_id = %s AND static = %s ORDER BY `created` ASC", $where),ARRAY_A);
			
			if(!empty($record)){
				
				$f = new RevSliderFunctions();
				foreach($record as $k => $rec){
					$record[$k]['created'] = $f->convert_post_date($rec['created'], true);
				}
			}
		}else{

			$record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slide_id = %s AND static = %s", $where), ARRAY_A);
		}
		
		
		
		return $record;
	}
	
	/**
	 * restore slide backup
	 * @since: 1.0.0
	 */
	private function restore_slide_backup($backup_id, $slide_id, $session_id){
		global $wpdb;
		
		$backup = $this->fetch_backup($backup_id);
		$current = $this->getDataByID($slide_id);
		
		/*
		 * process potential older backups previous to 6.0
		*/
		if(!empty($backup) && isset($backup['settings'])) {
			
			$legacy = false;
			$settings = json_decode($backup['settings'], true);
			
			if(empty($settings)) {
				$legacy = true;
				$settings = array('version', RS_REVISION);
			}
			else if(isset($settings['version']) && version_compare($settings['version'], '6.0.0', '<')) {
				$legacy = true;
				$settings['version'] = RS_REVISION;
			}
			
			if($legacy) {
				
				$slide = new RevSliderSlide();
				$slide->init_by_data($backup);
				
				$update = new RevSliderPluginUpdate();
				$slide = $update->migrate_slide_to_6_0($slide);
				
				$layers = json_decode($backup['layers'], true);
				foreach($layers as $key => $layer) {
					$layers[$key] = $update->migrate_layer_to_6_0($layer, false, $slide);
				}
				
				$backup['params'] = json_encode($slide);
				$backup['layers'] = json_encode($layers);
				$backup['settings'] = json_encode($settings);
			
			}
		
		}
		
		//update the current
		if(!empty($backup) && !empty($current)){
			
			//self::add_new_backup($current, $session_id);
			
			$current['params'] = $backup['params'];
			$current['layers'] = $backup['layers'];
			$current['settings'] = $backup['settings'];
			$update_id = $current['id'];
			unset($current['id']);
			
			if(strpos($slide_id, 'static_') !== false){
				$return = $wpdb->update($wpdb->base_prefix . 'revslider_static_slides', $current, array('id' => $update_id));
			}else{
				$return = $wpdb->update($wpdb->base_prefix . 'revslider_slides', $current, array('id' => $update_id));
			}
			//now change the backup date to current date, to set it to the last version
			$backup['created'] =  date('Y-m-d H:i:s');
			$update_id = $backup['id'];
			unset($backup['id']);
			
			$return1 = $wpdb->update($wpdb->prefix . 'revslider_backup_slides', $backup, array('id' => $update_id));
			
			return true;
		}
		
		return false;
	}
	
	public function getDataByID($slideid){
		
		global $wpdb;
		$return = false;
		
		if(strpos($slideid, 'static_') !== false){
			$sliderID = str_replace('static_', '', $slideid);
			$record = $this->fetch($wpdb->base_prefix . 'revslider_static_slides', $wpdb->prepare("slider_id = %s", array($sliderID)));
			if(!empty($record)){
				$return = $record[0];
			}
			//$return = false;
		}else{
			$record = $this->fetchSingle($wpdb->base_prefix . 'revslider_slides', $wpdb->prepare("id = %d", array($slideid)));
			$return = $record;
		}
		
		return $return;
	}
	
	/**
	 * 
	 * throw error
	 */
	private function throwError($message, $code=-1){
		
		$f = new RevSliderFunctions();
		$f->throw_error($message, $code);
		
	}
	
	/**
	 * fetch backup by backup_id
	 * @since: 1.0.0
	 */
	private function fetch_backup($backup_id){
		global $wpdb;
		
		$record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix . 'revslider_backup_slides'." WHERE id = %s", array($backup_id)), ARRAY_A);
		
		if(!empty($record)) $record = $record[0];
		
		return $record;
		
	}
	
	//------------------------------------------------------------
	// validate for errors
	private function checkForErrors($prefix = ""){
		global $wpdb;
		
		if($wpdb->last_error !== ''){
			$query = $wpdb->last_query;
			$message = $wpdb->last_error;
			
			if($prefix) $message = $prefix.' - <b>'.$message.'</b>';
			if($query) $message .=  '<br>---<br> Query: ' . esc_attr($query);
			
			$this->throwError($message);
		}
	}

	/**
	 * 
	 * get data array from the database
	 * 
	 */
	private function fetch($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){
		global $wpdb;
		
		$query = "select * from $tableName";
		if($where) $query .= " where $where";
		if($orderField) $query .= " order by $orderField";
		if($groupByField) $query .= " group by $groupByField";
		if($sqlAddon) $query .= " ".$sqlAddon;
		
		$response = $wpdb->get_results($query,ARRAY_A);
		
		$this->checkForErrors("fetch");
		
		return($response);
	}
	
	/**
	 * 
	 * fetch only one item. if not found - throw error
	 */
	private function fetchSingle($tableName,$where="",$orderField="",$groupByField="",$sqlAddon=""){
		$response = $this->fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);
		
		if(empty($response))
			$this->throwError("Record not found");
		$record = $response[0];
		return($record);
	}
	
	/**
	 * check if a new backup should be created
	 * @since: 1.0.0
	 */
	public function check_add_new_backup($slide_data, $ajax_data, $slide_class){
		
		global $wpdb;
		
		$record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->base_prefix."revslider_slides WHERE id = %s", array($slide_class->get_id())), ARRAY_A);
		
		if(!empty($record)){
			$this->add_new_backup($record[0], esc_attr($ajax_data['session_id']));
		}
	}
	
	
	/**
	 * check if a new backup should be created
	 * @since: 1.0.0
	 */
	/*
	// hook no longer exists in the plugin
	public function check_add_new_backup_static($slide_data, $ajax_data, $slide_class){
		
		global $wpdb;
		
		$record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->base_prefix."revslider_static_slides WHERE id = %s", array($slide_class->get_id())), ARRAY_A);
		
		if(!empty($record)){
			$this->add_new_backup($record[0], esc_attr($ajax_data['session_id']), 'true');
		}
	}
	*/
	
	/**
	 * add new slide backup
	 * @since: 1.0.0
	 */
	private function add_new_backup($slide, $session_id, $static = 'false'){
		global $wpdb;
		
		$slide['slide_id'] = $slide['id'];
		unset($slide['id']);
		
		$slide['created'] = date('Y-m-d H:i:s');
		$slide['session'] = $session_id;
		$slide['static'] = $static;
		
		//check if session_id exists, if yes then update
		$row = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".$wpdb->prefix . "revslider_backup_slides WHERE session = %s AND slide_id = %s", array($session_id, $slide['slide_id'])), ARRAY_A);
		if(!empty($row) && isset($row[0]) && !empty($row[0])){
			$wpdb->update($wpdb->prefix . "revslider_backup_slides", $slide, array('id' => $row[0]['id']));
		}else{
			$wpdb->insert($wpdb->prefix . "revslider_backup_slides", $slide);
		}
		
		$cur = $this->check_backup_num($slide['slide_id']);
		
		if($cur > 11){
			$early = $this->get_oldest_backup($slide['slide_id']);
			
			if($early !== false){
				$this->delete_backup($early['id']);
			}
		}
	}
	
	/**
	 * get oldest backup of a slide
	 * @since: 1.0.0
	 */
	private function get_oldest_backup($slide_id){
		global $wpdb;
		
		$early = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slide_id = %s ORDER BY `created` ASC LIMIT 0,1", array($slide_id)), ARRAY_A);
		if(!empty($early)){
			return $early[0];
		}else{
			return false;
		}
	}
	
	/**
	 * check for the number of backups for a slide
	 * @since: 1.0.0
	 */
	private function check_backup_num($slide_id){
		global $wpdb;
		
		$cur = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) AS `row` FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slide_id = %s GROUP BY `slide_id`", array($slide_id)), ARRAY_A);
		
		if(!empty($cur)){
			return $cur[0]['row'];
		}else{
			return 0;
		}
	}
	
	/**
	 * delete a backup of a slide
	 * @since: 1.0.0
	 */
	private function delete_backup($id){
		global $wpdb;
		
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix . "revslider_backup_slides WHERE id = %s", array($id)));
		
	}
	
	/**
	 * delete all backup of a slide
	 * @since: 1.0.0
	 */
	public function delete_backup_full($id){
		global $wpdb;
		
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slide_id = %s", array($id)));
		
	}
	
	
	/**
	 * delete all backup of a slide
	 * @since: 1.0.0
	 */
	public function delete_backup_full_slider($id){
		global $wpdb;
		
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix . "revslider_backup_slides WHERE slider_id = %s", array($id)));
		
	}
	
	/**
	 * 
	 * get url to some view.
	 */
	private function getViewUrl($viewName,$urlParams=""){
		$params = "&view=".$viewName;
		if(!empty($urlParams))
			$params .= "&".$urlParams;
		
		$link = admin_url( 'admin.php?page=revslider'.$params);
		return($link);
	}
	
	/**
	 * Create/Update Database Tables
	 */
	public static function create_tables($network = false){
		global $wpdb;
		
		if(function_exists('is_multisite') && is_multisite() && $network){ //do for each existing site
		
			// $old_blog = $wpdb->blogid;
			
            // Get all blog ids and create tables
			$blogids = $wpdb->get_col("SELECT blog_id FROM ".$wpdb->blogs);
			
            foreach($blogids as $blog_id){
				switch_to_blog($blog_id);
				self::_create_tables();
				restore_current_blog();
            }
			
            // switch_to_blog($old_blog); //go back to correct blog
			
		}else{  //no multisite, do normal installation
		
			self::_create_tables();
			
		}
		
	}
	
	/**
	 * Create Tables, edited for multisite
	 * @since 1.5.0
	 */
	public static function _create_tables(){
		
		global $wpdb;
		
		//Create/Update Grids Database
		$grid_ver = get_option("revslider_backup_table_version", '0.99');
		
		if(version_compare($grid_ver, '1', '<')){
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			$table_name = $wpdb->prefix . 'revslider_backup_slides';
			$sql = "CREATE TABLE " .$table_name ." (
			  id int(9) NOT NULL AUTO_INCREMENT,
			  slide_id int(9) NOT NULL,
			  slider_id int(9) NOT NULL,
			  slide_order int not NULL,
			  params LONGTEXT NOT NULL,
			  layers LONGTEXT NOT NULL,
			  settings TEXT NOT NULL,
			  created DATETIME NOT NULL,
			  session VARCHAR(100) NOT NULL,
			  static VARCHAR(20) NOT NULL,
			  UNIQUE KEY id (id)
			);";
			
			dbDelta($sql);
			
			update_option('revslider_backup_table_version', '1');
			
			$grid_ver = '1';
		}
		
	}

}
	
?>