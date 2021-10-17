<?php
/**
 * 
 * EventON Addons / products class
 * connected from each addons
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Classes
 * @version     2.8.1
 */

if(class_exists('evo_addons')) return;

class evo_addons{

	private $addon_data;
	private $urls;
	private $notice_code;
	private $addon = false;
	private $slug ='';

	// Construct
	function __construct($arr=''){
		if(!empty($arr))	$this->setup_addon( $arr);
	}

	// set up addon instance afterwards
		function setup_addon($A){
			// assign initial values for instance of addon
			$this->addon_data = $A;
			
			if(!isset($A['slug'])) return;

			$this->slug = $A['slug'];
		
			// set up addon instance
			if(isset($A['slug']))	$this->addon = new EVO_Product($A['slug']);

			// when first time addon installed and updated from old version
				if( $this->addon && !$this->addon->get_version() || 
					( $this->addon->get_version() && version_compare($this->addon->get_version() , $this->addon_data['version'], '<' ) && isset($this->addon_data['version']) ) ){
					do_action('evo_addon_version_change', $this->addon_data['version']);
				}
			
			// once version change check is done run main updater
			if(is_admin()){
				global $pagenow;
				if( !empty($pagenow) && in_array($pagenow, array(
					'plugins.php',
					'update-core.php',
					'admin.php',
					'admin-ajax.php',				
					'plugin-install.php',				
				) ) ){
					
					$ADDON = new EVO_Product($A['slug'], true);

					if( ($pagenow == 'admin.php' && isset($_GET['tab']) && $_GET['tab']=='evcal_4' ) || $pagenow!='admin.php'	){

						// Get other plugin information from header
						if(file_exists(AJDE_EVCAL_DIR.'/'.$this->slug.'/'.$this->slug.'.php')){
							if(function_exists('get_plugin_data')){
								$_plug_data = get_plugin_data(AJDE_EVCAL_DIR.'/'.$this->slug.'/'.$this->slug.'.php');

								if( isset($_plug_data['PluginURI']))	$this->addon_data['plugin_uri'] = $_plug_data['PluginURI'];
								if( isset($_plug_data['Author']))	$this->addon_data['author'] = $_plug_data['Author'];
							}
						}
						
						// set up the new addon product for eventon					
						$ADDON->setup(
							array(
								'ID'=> (!empty($this->addon_data['ID'])? $this->addon_data['ID']: ''),
								'author'=> (!empty($this->addon_data['author'])? $this->addon_data['author']: ''),
								'plugin_uri'=> (!empty($this->addon_data['plugin_uri'])? $this->addon_data['plugin_uri']: ''),
								'version'=>$this->addon_data['version'], 
								'slug'=> $this->addon_data['slug'],
								'plugin_slug'=>$this->addon_data['plugin_slug'],
								'name'=>$this->addon_data['name'],
								'guide_file'=> isset($this->addon_data['guide_file'])?
									$this->addon_data['guide_file']:
									(( isset($this->addon_data['plugin_path']) && file_exists($this->addon_data['plugin_path'].'/guide.php') )? 
									$this->addon_data['plugin_url'].'/guide.php':null),
							)
						);	
					}
				}
			}
		}
		
	// Check for eventon compatibility
		function evo_version_check(){
			if( version_compare(EVO()->version, $this->addon_data['evo_version']) == -1 ){
				$this->notice_code = '01';
				add_action('admin_notices', array($this, 'notice'));
				return false;
			}
			return true;
		}
		public function notice(){
			if( empty($this->notice_code) ) return false;
			?>
	        <div class="message error"><p><?php printf(__('EventON %s is disabled! - '), $this->addon_data['name']); echo $this->notice_message($this->notice_code);?></p></div>
	        <?php
		}
		public function notice_message($code){
			$decypher = array(
				'01'=>	$this->addon_data['name'].' need EventON version <b>'.$this->addon_data['evo_version'].'</b> or higher to work correctly, please update EventON.',
				'02'=>	'EventON version is older than what is suggested for this addon. Please update EventON.',
			);
			return $decypher[$code];
		}

	// Deactivate Addon from eventon products
		public function remove_addon(){
			$PROD = new EVO_Product_Lic($this->addon_data['slug']);
			return $PROD->deactivate();
		}
	
	// deprecated
		public function activate(){}
		function updater(){}

}

?>