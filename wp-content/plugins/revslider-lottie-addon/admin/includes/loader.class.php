<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2020 ThemePunch
*/

if(!defined('ABSPATH')) exit();
if(!class_exists('RevSliderFunctions')) exit();

class RsLottieLoader extends RevSliderFunctions {
	
	public $lib_ver	= '2.0.5';
	public $lib_url	= 'addons/lottie.php';
	public $dl_url	= 'addons/lottie-download.php';
	private $path	= '/revslider/lottie/objects/';
	private $remove_path;
	private $download_path;
	public $upload_dir;
	public $allowed_categories = array('lottiecustoms');
	
	public function __construct(){
		$this->upload_dir	= wp_upload_dir();
		$this->remove_path	= $this->upload_dir['basedir'].'/rstemplottie/';
		$this->download_path = $this->remove_path;
		$this->check_load_library();
	}
	
	public function add_filters(){
		add_filter('revslider_get_full_library', array($this, 'load_lord_icons'), 10, 6);
		add_filter('revslider_get_full_library', array($this, 'load_custom_icons'), 10, 6);
		add_filter('revslider_do_ajax', array($this, 'add_lottie_ajax_functions'), 10, 3);
		add_filter('revslider_delete_custom_library_item', array($this, 'delete_custom_lottie_item'), 10, 3);
		add_filter('revslider_edit_custom_library_item', array($this, 'edit_custom_lottie_item'), 10, 5);
		add_filter('revslider_upload_custom_library_item', array($this, 'upload_custom_lottie_item'), 10, 2);
	}
	
	public function add_lottie_ajax_functions($return = '', $action = '', $data = array()){
		switch($action){
			case 'download_lordicon_file':
				$return = array();
				$handle = $this->get_val($data, 'handle');
				if(!empty($handle)){
					foreach($handle as $k => $h){
						$handle[$k]['url'] = $this->download_lord_icon($h['handle']);
						$return[] = $handle[$k];
					}
				}
				
				return array('data' => $return);
			break;
			case 'download_customlottie_file':
				$return = array();
				$handle = $data['handle'];
				if(!empty($handle)){
					foreach($handle as $k => $h){
						$handle[$k]['url'] = '';//$this->download_lord_icon($h['handle']);
						$return[] = $handle[$k];
					}
				}
				
				return array('data' => $return);
			break;
			default:
				return $return;
			break;
		}
	}
	
	public function sanitize_library_type($name){
		$name = trim($name);
		$name = preg_replace('/[^a-zA-Z0-9 ]/', '', $name);
		
		return (strlen($name) < 3) ? false : $name;
	}
	
	
	public function download_lord_icon($handle){
		$rslb = new RevSliderLoadBalancer();
		
		$object = get_option('rs-lottie-library', array());
		if(empty($object)){
			$this->check_load_library();
			$object = get_option('rs-lottie-library', array());
		}
		
		$found = false;
		if(isset($object['lordicons']) && isset($object['lordicons']['items'])){
			foreach($object['lordicons']['items'] as $k => $v){
				if($v['handle'] === $handle){
					$found = $k;
					break;
				}
			}
		}
		
		if($found === false) return false;
		
		$rattr = array(
			'library_version' => urlencode($this->lib_ver),
			'handle'	=> urlencode($handle),
			'version'	=> urlencode(RS_LOTTIE_REVISION),
		);
		$request = $rslb->call_url($this->dl_url, $rattr, 'library');
		
		if(!is_wp_error($request)){
			$data = $request['body'];
			$check = json_decode($data, true);
			if(!empty($check)){
				if(isset($check['error'])){
					$data = false;
					$this->throw_error($check['error']);
				}
			}elseif(trim($data) == ''){
				$this->throw_error(__('No data received', 'revslider'));
			}
			if($data !== false && $data !== ''){
				$file = $this->upload_dir['basedir'] . $this->path . 'lordicons/' . $handle;
				$_file = $this->upload_dir['baseurl'] . $this->path . 'lordicons/' . $handle;
				
				$done = (file_exists(dirname($file))) ? true : @mkdir(dirname($file), 0777, true);
				if($done === false) $this->throw_error($this->upload_dir['basedir'] . $this->path . 'lordicons/ '.__('could not be created programmatically', 'revslider'));
				$done = file_put_contents($file, $data);
				if($done === false) $this->throw_error($file.' '.__('could not be created programmatically', 'revslider'));
				$object['lordicons']['items'][$found]['img'] = $_file;
				
				update_option('rs-lottie-library', $object, false);
				
				return $_file;
			}
		}
		
		return false;
	}
	
	
	/**
	 **/
	public function load_custom_icons($object, $include, $tmp_slide_uid, $refresh_from_server, $get_static_slide, $admin_functions){
		if(in_array('lottiecustoms', $include) || in_array('all', $include)){
			$favorite = new RevSliderFavorite();
			
			$_object = get_option('rs-lottie-library', array());
			if(empty($_object) || !isset($_object['lottiecustoms'])){
				$this->check_load_library();
				$_object = get_option('rs-lottie-library', array());
			}
			
			if(!empty($_object)){
				foreach($_object as $category => $values){
					if($category === 'hash'){
						unset($_object[$category]);
						continue;
					}
					if($category !== 'lottiecustoms'){
						unset($_object[$category]);
						continue;
					}
					
					if(!isset($object[$category])){
						$object[$category] = array('items' => array());
					}
					if(!isset($object[$category]['items'])){
						$object[$category]['items'] = array();
					}
					
					if(isset($values['items'])){
						foreach($values['items'] as $k => $v){
							$handle = $this->get_val($v, 'handle');
							$object[$category]['items'][$k] = $v;
							$object[$category]['items'][$k]['favorite'] = $favorite->is_favorite($category, $handle);
							$file = $this->get_val($v, 'img', false);
							//check if file exists, if not, change to false
							if($file !== false){ //check if file exists
								if(!file_exists($this->upload_dir['basedir'] . $this->path . 'lottiecustoms/' . $handle)){
									unset($object[$category]['items'][$k]);
									//$object[$category]['items'][$k]['img'] = false; //set for redownload
								}
							}else{
								unset($object[$category]['items'][$k]);
							}
						}
					}
				}
			}
		}
		
		return $object;
	}
	
	
	public function load_lord_icons($object, $include, $tmp_slide_uid, $refresh_from_server, $get_static_slide, $admin_functions){
		
		
		if(in_array('lordicons', $include) || in_array('all', $include)){
			$favorite = new RevSliderFavorite();
			
			$_object = get_option('rs-lottie-library', array());
			
			if(empty($_object) || !isset($_object['lordicons'])){
				$this->check_load_library(true);
				$_object = get_option('rs-lottie-library', array());
			}
			if(!empty($_object)){
				foreach($_object as $category => $values){
					if($category !== 'lordicons'){
						unset($_object[$category]);
						continue;
					}
					
					if(!isset($object[$category])){
						$object[$category] = array('items' => array());
					}
					if(!isset($object[$category]['items'])){
						$object[$category]['items'] = array();
					}
					
					if(isset($values['items'])){
						foreach($values['items'] as $k => $v){
							$handle = $this->get_val($v, 'handle');
							$object[$category]['items'][$k] = $v;
							$object[$category]['items'][$k]['favorite'] = $favorite->is_favorite($category, $handle);
							$file = $this->get_val($v, 'img', false);
							if($file !== false){ //check if file exists
								if(!file_exists($this->upload_dir['basedir'] . $this->path . 'lordicons/'. $handle)){
									$object[$category]['items'][$k]['img'] = false; //set for redownload
								}
							}
							//check if file exists, if not, change to false
						}
					}
				}
			}
		}
		
		return $object;
	}
	
	
	public function upload_custom_lottie_item($success, $data){
		$customs	= json_decode(stripslashes($this->get_val($_POST, 'customs', '')), true);
		$tag		= $this->get_val($customs, 'tag', false);
		$lib_type	= $this->get_val($customs, 'type', '');
		if($lib_type !== 'lottiecustoms') return $success;
		
		$lib_type	= $this->sanitize_library_type($lib_type);
		
		if($tag !== false){
			$obj = new RevSliderObjectLibrary();
			$new = $obj->create_custom_tag($tag, $lib_type);
			if(!is_array($new)){
				if(!is_array($customs)) $customs = array('type' => 'lottiecustoms');
				$customs['tag']	= 'All';
				$customs['id']	= 0;
			}else{
				if(!is_array($customs)) $customs = array('type' => 'lottiecustoms');
				$customs['tag']	= $this->get_val($new, 'name', 'All');
				$customs['id']	= $this->get_val($new, 'id', 0);
			}
		}
		
		$success = $this->import_custom_lottie_file($data, $customs);
		
		return $success;
	}
	
	public function edit_custom_lottie_item($success, $id, $type, $name, $tags){
		$library = get_option('rs-lottie-library', array());
		
		if(!isset($library[$type])) return $success;
		if(!isset($library[$type]['items'])) return $success;
		if(empty($library[$type]['items'])) return $success;
		foreach($library[$type]['items'] as $lk => $lv){
			if(strval($this->get_val($lv, 'id', 0)) === strval($id)){
				$path = $this->get_val($lv, 'img');
				if(!empty($name)){
					$library[$type]['items'][$lk]['title'] = $this->sanitize_library_type($name);
				}
				
				if(!empty($tags)){
					if(is_array($tags)){
						$library[$type]['items'][$lk]['tags'] = array();
						
						foreach($tags as $t){
							$library[$type]['items'][$lk]['tags'][] = strval($t);
						}
					}else{
						$library[$type]['items'][$lk]['tags'] = array(strval($tags));
					}
				}
				update_option('rs-lottie-library', $library);

				return true;
			}
		}
		
		return $success;
	}
	
	public function delete_custom_lottie_item($success, $id, $type){
		$library = get_option('rs-lottie-library', array());
		
		if(!isset($library[$type])) return $success;
		if(!isset($library[$type]['items'])) return $success;
		if(empty($library[$type]['items'])) return $success;
		foreach($library[$type]['items'] as $lk => $lv){
			if(strval($this->get_val($lv, 'id', 0)) === strval($id)){
				$path = $this->get_val($lv, 'img');
				if(!empty($path)){ }
				unset($library[$type]['items'][$lk]);
				
				update_option('rs-lottie-library', $library);
				/*
				require_once(ABSPATH . 'wp-admin/includes/file.php');
				WP_Filesystem();
				global $wp_filesystem;
				//$wp_filesystem->delete($this->remove_path, true);
				*/
				return true;
			}
		}
		
		return $success;
	}
	
	/**
	 * import (unzip) an uploaded custom lottie files
	 */
	private function import_custom_lottie_file($data, $customs){
		require_once(ABSPATH . 'wp-admin/includes/file.php');
	
		$import_file = $this->get_val($_FILES, 'import_file');
		
		$error		 = $this->get_val($import_file, 'error');
		switch($error){
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				$this->throw_error(__('No file sent.', 'revslider'));
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$this->throw_error(__('Exceeded filesize limit.', 'revslider'));
			default:
			break;
		}
		$path		= $this->get_val($import_file, 'tmp_name');
		$name		= $this->get_val($import_file, 'name');
		$type		= $this->get_val($import_file, 'type');
		$library	= get_option('rs-lottie-library', array());
		$tag		= $this->get_val($customs, 'tag', false);
		//$tagID		= $this->get_val($customs, 'tagID', false);
		$tagID		= $this->get_val($customs, 'id', false);
		$lib_type	= $this->get_val($customs, 'type');
		$lib_type	= $this->sanitize_library_type($lib_type);
		
		if(!in_array($lib_type, $this->allowed_categories)) $this->throw_error(__('Category does not exist', 'revslider'));
		if(isset($path['error'])) $this->throw_error($path['error']);
		if(file_exists($path) == false) $this->throw_error(__('Import file not found', 'revslider'));
		
		WP_Filesystem();
		global $wp_filesystem;
		
		
		$import	= array();
		$finfo	= finfo_open(FILEINFO_MIME_TYPE);
		$info	= finfo_file($finfo, $path);
		$zip	= false;
		
		//text/plain
		switch($info){
			case 'application/json':
			case 'text/plain':
				$import[] = $path;
			break;
			case 'application/zip':
				$zip	= true;
				@$wp_filesystem->delete($this->download_path, true);
				$file	= unzip_file($path, $this->download_path);
				
				if(is_wp_error($file)){
					@define('FS_METHOD', 'direct'); //lets try direct.
					
					WP_Filesystem(); //WP_Filesystem() needs to be called again since now we use direct!
					
					$file = unzip_file($path, $this->download_path);
					if(is_wp_error($file)){
						$this->download_path = RS_PLUGIN_PATH.'rstemplottie/';
						$this->remove_path	 = $this->download_path;
						$file				 = unzip_file($path, $this->download_path);
						
						if(is_wp_error($file)){
							$file_basename	= basename($path);
							$this->download_path = str_replace($file_basename, '', $path);
							$file			= unzip_file($path, $this->download_path);
						}
					}
				}
				
				if($file){
					//check all files in download_path and add them to an array list of files
					$files = list_files($this->download_path, 1);
					if(!empty($files)){
						foreach($files as $file){
							if(is_dir($file)) continue;
							$import[] = $file;
						}
					}
				}else{
					$wp_filesystem->delete($this->remove_path, true);
					$msg = $file->get_error_message();
					$this->throw_error($msg);
				}
			break;
		}
		
		if(!empty($import)){
			foreach($import as $k => $v){
				$check = $wp_filesystem->exists($v) ? $wp_filesystem->get_contents($v) : '';
				$check = json_decode($check, true);
				if(empty($check)) unset($import[$k]);
			}
		}
		
		if(empty($import)){
			$wp_filesystem->delete($this->remove_path, true);
			$this->throw_error(__('No valid file sent.', 'revslider'));
		}
		
		$tags = get_option('rs-custom-library-tags', array());
		$found = false;
		if(!empty($tags)){
			foreach($tags as $t => $_v){
				if($t !== $lib_type) continue;
				
				foreach($_v as $k => $v){
					if($tagID !== false){
						if(strval($k) === strval($tagID)){
							$found	= true;
							$tag	= $v;
							$tagID	= $tagID;
							break;
						}
					}else{
						if($this->get_val($v, 'name', -1) === $tag){
							$found	= true;
							$tag	= $v;
							$tagID	= $k;
							break;
						}
					}
				}
			}
		}

		if($found !== true){
			$tag = 'All';
			$tagID = 0;
		}
		
		//push all imports to the correct folder
		//create entries in the database
		//remove files from the temp path
		//move to the upload folder
		$_id = 0;
		if(!isset($library[$lib_type])) $library[$lib_type] = array();
		if(!isset($library[$lib_type]['items'])) $library[$lib_type]['items'] = array();
		
		foreach($library[$lib_type]['items'] as $lk => $lv){
			if($_id < $this->get_val($lv, 'id', 0)) $_id = $this->get_val($lv, 'id', 0);
		}
		
		$found = false;
		foreach($import as $k => $v){
			$handle = ($zip === true) ? basename($v) : $name; //if zip is false, file has still a temporary name
			$new = $this->upload_dir['basedir'] . $this->path . $lib_type . '/' . $handle;
			$url = $this->upload_dir['baseurl'] . $this->path . $lib_type . '/' . $handle;
			$done = (file_exists(dirname($new))) ? true : @mkdir(dirname($new), 0777, true);
			if($done === false) $this->throw_error(dirname($new) . ' '.__('could not be created programmatically', 'revslider'));
			$done = copy($v, $new);
			if($done === false) $this->throw_error($handle . ' '.__('could not be created programmatically', 'revslider'));
			//push to library
			if(!empty($library[$lib_type]['items'])){
				$found = false;
				foreach($library[$lib_type]['items'] as $lk => $lv){
					if($lv['handle'] !== $handle) continue;
					$found	= $lk;
					break;
				}
			}
			if($found === false) $_id += 1;
			$_name = str_replace(array('.json', '-', '_'), array('', ' ', ' '), $handle);
			$_data = array(
				//'file' => $new,
				'id'	 => $_id,
				'handle' => $handle,
				'title'	 => $this->sanitize_library_type($_name),
				'img'	 => $url
			);
			
			if($tagID !== 0 && $tagID !== false){
				$_data['tags'] = array(strval($tagID));
			}
			
			if($found !== false){
				$library[$lib_type]['items'][$found]['tags'] = $this->get_val($_data, 'tags', array());
				$library[$lib_type]['items'][$found]['img'] = $_data['img'];
			}else{
				$library[$lib_type]['items'][] = $_data;
			}
		}
		
		update_option('rs-lottie-library', $library);
		
		$wp_filesystem->delete($this->remove_path, true);
		
		return $library[$lib_type];
	}
	
	public function check_load_library($force = false){
		$rslb		= new RevSliderLoadBalancer();
		$last_check	= get_option('revslider-lottie-library-check');

		if($last_check == false){ //first time called
			$last_check = 1296001;
			update_option('revslider-lottie-library-check', time());
		}
		
		// Get latest object list
		if(time() - $last_check > 1296000 || $force == true){ //30 days
			update_option('revslider-lottie-library-check', time());
			
			$validated = get_option('revslider-valid', 'false');
			$code = ($validated == 'false') ? '' : get_option('revslider-code', '');
			$hash = ($force) ? '' : get_option('revslider-lottie-library-hash', '');
			$rattr = array(
				'library_version' => urlencode($this->lib_ver),
				'hash'		=> urlencode($hash),
				'code'		=> urlencode($code),
				'version'	=> urlencode(RS_LOTTIE_REVISION),
			);
			$request = $rslb->call_url($this->lib_url, $rattr, 'library');
			
			if(!is_wp_error($request)){
				if($response = maybe_unserialize($request['body'])){
					if('actual' != $response){
						$cur = get_option('rs-lottie-library', array());
						$library = json_decode($response, true);

						if(is_array($library)){
							if(isset($library['hash'])) update_option('revslider-lottie-library-hash', $library['hash']);
							if(!empty($library)){
								if(isset($library['lordicons']) && isset($library['lordicons']['items'])){
									if(!empty($cur) && isset($cur['lordicons']) && isset($cur['lordicons']['items'])){
										foreach($cur['lordicons']['items'] as $_k => $_v){
											foreach($library['lordicons']['items'] as $k => $v){
												if($_v['handle'] !== $v['handle']) continue;
												
												$cur['lordicons']['items'][$_k]['img'] = ($_v['ver'] < $v['ver']) ? false : $_v['img'];
												unset($library['lordicons']['items'][$_k]);
												break;
											}
										}
									}else{
										$cur['lordicons'] = $library['lordicons'];
									}
									update_option('rs-lottie-library', $cur, false);
								}
							}
						}
					}
				}
			}
		}
	}
	
}
?>