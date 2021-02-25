<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2020 ThemePunch 
*/

if(!defined('ABSPATH')) exit();
if(!class_exists('RevSliderFunctions')) exit();

class RsScrollvideoLoader extends RevSliderFunctions {
	
	public $upload_dir;
	public $scrollvideo_dir;
	public $scrollvideo_url;
	
	
	public function __construct(){
		$this->upload_dir	= wp_upload_dir();
		$this->scrollvideo_dir = $this->get_val($this->upload_dir, 'basedir').'/revslider/scrollvideo/';
		$this->scrollvideo_url = $this->get_val($this->upload_dir, 'baseurl').'/revslider/scrollvideo/';
		
		if(!file_exists($this->scrollvideo_dir)) @mkdir($this->scrollvideo_dir, 0777, true);
		
	}
	
	public function add_filters(){
		add_filter('revslider_do_ajax', array($this, 'add_scrollvideo_ajax_functions'), 10, 3);
		
		add_filter('revslider_exportSlider_usedMedia', array($this, 'export_adddon_images'), 10, 3);
		add_filter('revslider_exportSlider_export_data', array($this, 'export_remove_unused_path'), 10, 2);
		add_filter('revslider_importSliderFromPost_modify_slider_data', array($this, 'import_update_addon_image_urls'), 10, 3);
		add_filter('revslider_duplicate_slider', array($this, 'duplicate_update_addon_image_urls'), 10, 4);
	}
	
	public function add_scrollvideo_ajax_functions($return = '', $action = '', $data = array()){
		
		switch($action){
			case 'delete_frame':
				$id = intval($this->get_val($data, 'slider', 0));
				$filename = $this->get_val($data, 'file', 0);
				$q = $this->get_val($data, 'q', '');
				$f = $this->get_val($data, 'f', '');
				$success = $this->delete_frame($id, $filename, $q, $f);
				return ($success === false) ? array('error' => __('Could not delete the frame', 'revslider')) : __('Frame successfully cleared', 'revslider');
			break;
			case 'clear_frames':
				$success = false;
				$id = intval($this->get_val($data, 'slider', 0));
				if($id > 0){
					$success = $this->clear_frames($id);
				}
				
				return ($success === false) ? array('error' => __('Could not delete the frames', 'revslider')) : __('Frames successfully cleared', 'revslider');
			break;
			case 'get_frames':
				$id = intval($this->get_val($data, 'slider', 0));
				if($id === 0) return array('error' => __('Could not retrieve the frames', 'revslider'));
				
				// return first, last framenumber and URL to the folder
				$frames = $this->get_frames($id);
				
				return array('data' => $frames);
			case 'save_frame':
				$id = intval($this->get_val($data, 'slider', 0));
				$filename = $this->get_val($data, 'file', 0);
				$bitmap = $this->get_val($data, 'bitmap', '');
				$q = $this->get_val($data, 'q', '');
				$f = $this->get_val($data, 'f', '');
				// create framefolder and add frame into it
				
				$return = $this->save_frame($id, $filename, $bitmap, $q, $f);
				return ($return !== false) ? $return : array('error' => __('Could not upload the frame', 'revslider'));
			break;
			default:
				return $return;
			break;
		}
	}
	
	public function delete_frame($id, $filename, $q, $f){
		$id = intval($id);
		$_q = intval($q);
		$_f = intval($f);
		$_frameid = intval($filename);
		if($id === 0) return array('error' => __('Frame id must be numeric', 'revslider'));
		if($_q === 0) return array('error' => __('Quality must be numeric', 'revslider'));
		if($_f === 0) return array('error' => __('FPS must be numeric', 'revslider'));
		if($_frameid === 0) return array('error' => __('Frames could not be added', 'revslider'));
		while(strlen($_frameid) < 3){
			$_frameid = '0'.$_frameid;
		}
		while(strlen($_q) < 2){
			$_q = '0'.$_q;
		}
		while(strlen($_f) < 2){
			$_f = '0'.$_f;
		}
		$_file = $_frameid.'_f'.$_f.'_q'.$_q.'.jpg';
		
		if(!file_exists($this->scrollvideo_dir.$id)) return array('error' => __('Frame not found', 'revslider'));
		if(!file_exists($this->scrollvideo_dir.$id.'/'.$_file)) return array('error' => __('Frame not found', 'revslider'));
		
		WP_Filesystem();
		global $wp_filesystem;
		
		return @$wp_filesystem->delete($this->scrollvideo_dir.$id.'/'.$_file, true);
	}
	
	public function clear_frames($id){
		$id = intval($id);
		
		if($id === 0) return false;
		
		WP_Filesystem();
		global $wp_filesystem;
		
		return @$wp_filesystem->delete($this->scrollvideo_dir.$id, true);
	}
	
	
	public function get_frames($id){
		if(!file_exists($this->scrollvideo_dir.$id)) @mkdir($this->scrollvideo_dir.$id, 0777, true);
		
		$max = 0;
		//$last = '';
		$counter = 0;
		$q = '';
		$f = '';
		$dirs = scandir($this->scrollvideo_dir.$id);
		if(!empty($dirs)){
			foreach($dirs as $file){
				if($file !== '.' && $file !== '..' && !is_dir($this->scrollvideo_dir.$id.'/'.$file)){
					$_f		= explode('_', $file);
					if(count($_f) !== 3) continue;
					
					$name	= intval($this->get_val($_f, 0));
					$f		= str_replace(array('f'), '', $this->get_val($_f, 1));
					$q		= str_replace(array('q', '.jpg', 'jpg'), '', $this->get_val($_f, 2));
					if($max === 0) $max = $name;
					if($max < $name){
						$max = $name;
						//$last = $file;
					}
					$counter++;
				}
			}
		}
		
		$sequence_ok = ($counter !== $max) ? false : true;
		
		while(strlen($max) < 3){
			$max = '0'.$max;
		}
		return array('folder' => $this->scrollvideo_url.$id, 'last' => $max, 'q' => $q, 'f' => $f, 'sequence_ok' => $sequence_ok);
	}
	
	
	public function save_frame($id, $filename, $bitmap, $q, $f){
		$id = intval($id);
		$_q = intval($q);
		$_f = intval($f);
		$_frameid = intval($filename);
		if($id === 0) return array('error' => __('Frame id must be numeric', 'revslider'));
		if($_q === 0) return array('error' => __('Quality must be numeric', 'revslider'));
		if($_f === 0) return array('error' => __('FPS must be numeric', 'revslider'));
		if($_frameid === 0) return array('error' => __('Frames could not be added', 'revslider'));
		while(strlen($_frameid) < 3){
			$_frameid = '0'.$_frameid;
		}
		while(strlen($_q) < 2){
			$_q = '0'.$_q;
		}
		while(strlen($_f) < 2){
			$_f = '0'.$_f;
		}
		
		$_file = $_frameid.'_f'.$_f.'_q'.$_q.'.jpg';
		if(preg_match('/^data:image\/(\w+);base64,/', $bitmap, $type)){
			$data = substr($bitmap, strpos($bitmap, ',') + 1);
			$type = strtolower($type[1]); // jpg, png, gif

			if(!in_array($type, array('jpg', 'jpeg', 'gif', 'png'))){
				return array('error' => __('Frame has an invalid image type', 'revslider'));
			}
			$data = str_replace(' ', '+', $data);
			$data = base64_decode($data);

			if($data === false){
				return array('error' => __('Frame has an invalid image type', 'revslider'));
			}
		}else{
			return array('error' => __('Frame has an invalid data', 'revslider'));
		}
		
		if(!file_exists($this->scrollvideo_dir.$id)) @mkdir($this->scrollvideo_dir.$id, 0777, true);
		
		$ret = @file_put_contents($this->scrollvideo_dir.$id.'/'.$_file, $data);
		
		return ($ret !== false) ? __('Frame added', 'revslider') : array('error' => __('Frame could not be added', 'revslider'));
	}
	
	public function export_remove_unused_path($data, $export){
		
		$dir	= $this->get_val($data, array('params', 'addOns', 'revslider-scrollvideo-addon', 'sequence', 'dir'), false);
		$_dir	= str_replace($this->scrollvideo_url, $this->scrollvideo_dir, $dir);
		$mpeg	= $this->get_val($data, array('params', 'addOns', 'revslider-scrollvideo-addon', 'mpeg'), false);
		$mpeg	= str_replace($this->get_val($this->upload_dir, 'baseurl'), '', $mpeg);
		$fps	= $this->get_val($data, array('params', 'addOns', 'revslider-scrollvideo-addon', 'sequence', 'fps'), false);
		if(strlen($fps) < 2) $fps = '0'.$fps;
		$qual	= $this->get_val($data, array('params', 'addOns', 'revslider-scrollvideo-addon', 'sequence', 'quality'), false);
		$qual	*= 10;
		if(strlen($qual) < 2) $qual = '0'.$qual;
		
		$frames = false;
		
		if(file_exists($_dir)){
			$dirs = scandir($_dir);
			if(!empty($dirs)){
				foreach($dirs as $file){
					if($file !== '.' && $file !== '..' && !is_dir($_dir.'/'.$file)){
						$_f		= explode('_', $file);
						if(count($_f) !== 3) continue;
						
						$f		= str_replace(array('f'), '', $this->get_val($_f, 1));
						$q		= str_replace(array('q', '.jpg', 'jpg'), '', $this->get_val($_f, 2));
						if($f !== $fps) continue;
						if($q !== $qual) continue;
						
						$frames = true;
						break;
					}
				}
			}
			
			if($frames === false){
				if(!empty($mpeg)){
					$data['params']['addOns']['revslider-scrollvideo-addon']['mpeg'] = $mpeg;
				}
			}else{
				if(!empty($mpeg)){
					$data['params']['addOns']['revslider-scrollvideo-addon']['mpeg'] = '';
					$data['params']['addOns']['revslider-scrollvideo-addon']['videoID'] = '';
				}
			}
			
			$data['params']['addOns']['revslider-scrollvideo-addon']['dir'] = $_dir	= str_replace($this->scrollvideo_url, $this->scrollvideo_dir, $dir);
		}
		
		return $data;
	}
	
	public function export_adddon_images($data, $slides, $sliderParams){
		//sequence.dir
		
		$img	= $this->get_val($sliderParams, array('addOns', 'revslider-scrollvideo-addon', 'image'), false);
		$img	= str_replace($this->get_val($this->upload_dir, 'baseurl'), '', $img);
		$mpeg	= $this->get_val($sliderParams, array('addOns', 'revslider-scrollvideo-addon', 'mpeg'), false);
		$mpeg	= str_replace($this->get_val($this->upload_dir, 'baseurl'), '', $mpeg);
		$dir	= $this->get_val($sliderParams, array('addOns', 'revslider-scrollvideo-addon', 'sequence', 'dir'), false);
		$_dir	= str_replace($this->scrollvideo_url, $this->scrollvideo_dir, $dir);
		
		$fps	= $this->get_val($sliderParams, array('addOns', 'revslider-scrollvideo-addon', 'sequence', 'fps'), false);
		if(strlen($fps) < 2) $fps = '0'.$fps;
		$qual	= $this->get_val($sliderParams, array('addOns', 'revslider-scrollvideo-addon', 'sequence', 'quality'), false);
		$qual	*= 10;
		if(strlen($qual) < 2) $qual = '0'.$qual;
		$qual = (string)$qual;
		$fps = (string)$fps;
		
		//add all frames!

		$frames = false;
		//check folder
		if(!file_exists($_dir)) @mkdir($_dir, 0777, true);
		
		$dirs = (!empty(trim($_dir))) ? scandir($_dir) : array();
		if(!empty($dirs)){
			foreach($dirs as $file){
				if($file !== '.' && $file !== '..' && !is_dir($_dir.'/'.$file)){
					$_f		= explode('_', $file);
					if(count($_f) !== 3) continue;
					
					$f		= str_replace(array('f'), '', $this->get_val($_f, 1));
					$q		= str_replace(array('q', '.jpg', 'jpg'), '', $this->get_val($_f, 2));
					if($f !== $fps) continue;
					if($q !== $qual) continue;
					
					$frames = true;
					
					$path = str_replace($this->get_val($this->upload_dir, 'basedir'), '', $_dir.'/'.$file);
					$data['used_images'][$path] = true;
				}
			}
		}
		if($frames === false){
			if(!empty($mpeg)) $data['used_videos'][$mpeg] = true;
		}
		//$_file = $_frameid.'_f'.$fps.'_q'.$qual.'.jpg';
		
		if(!empty($img)) $data['used_images'][$img] = true;
		
		//if(!empty($dir)) $data['used_images'][$dir] = true;
		
		return $data;
	}
	
	
	public function duplicate_update_addon_image_urls($new_id, $old_id, $slides, $slider){
		WP_Filesystem();
		global $wp_filesystem;
		
		$image_path = $this->upload_dir['basedir'];
		$dirs = @scandir($image_path.'/revslider/scrollvideo/'.$old_id);
		if($dirs !== false){
			foreach($dirs as $file){
				if($file !== '.' && $file !== '..' && !is_dir($image_path.'/revslider/scrollvideo/'.$old_id.'/'.$file)){
					//echo $file;
					$zimage	= $wp_filesystem->exists($image_path.'/revslider/scrollvideo/'.$old_id.'/'.$file);
					if($zimage){
						$_file = $image_path . '/revslider/scrollvideo/'.$new_id.'/'.$file;
						@mkdir(dirname($_file), 0777, true);
						@copy($image_path.'/revslider/scrollvideo/'.$old_id.'/'.$file, $_file);
					}
				}
			}
		}
		
	}
	
	
	public function import_update_addon_image_urls($data, $image_path, $import){
		global $wp_filesystem;
		
		$old_id = intval($this->get_val($data, array('sliderParams', 'id'), false));
		$new_id = $import->slider_id;
		$dirs = @scandir($image_path.'images/revslider/scrollvideo/'.$old_id);
		
		if($dirs !== false){
			foreach($dirs as $file){
				if($file !== '.' && $file !== '..' && !is_dir($image_path.'images/revslider/scrollvideo/'.$old_id.'/'.$file)){
					//echo $file;
					$zimage	= $wp_filesystem->exists($image_path.'images/revslider/scrollvideo/'.$old_id.'/'.$file);
					if($zimage){
						$_file = $this->upload_dir['basedir'] . '/revslider/scrollvideo/'.$new_id.'/'.$file;
						@mkdir(dirname($_file), 0777, true);
						@copy($image_path.'images/revslider/scrollvideo/'.$old_id.'/'.$file, $_file);
					}
				}
			}
		}
		
		$dir = $this->get_val($data, array('params', 'addOns', 'revslider-scrollvideo-addon', 'dir'), false);
		
		if($dir !== false){
			$data['sliderParams']['params']['addOns']['revslider-scrollvideo-addon']['dir'] = $this->upload_dir['baseurl'] . '/revslider/scrollvideo/'.$new_id.'/';
			$data['params']['addOns']['revslider-scrollvideo-addon']['dir'] = $this->upload_dir['baseurl'] . '/revslider/scrollvideo/'.$new_id.'/';
		}
		
		$dir = $this->get_val($data, array('sliderParams', 'params', 'addOns', 'revslider-scrollvideo-addon', 'sequence', 'dir'), false);
		if($dir !== false){
			$data['sliderParams']['params']['addOns']['revslider-scrollvideo-addon']['sequence']['dir'] = $this->upload_dir['baseurl'] . '/revslider/scrollvideo/'.$new_id.'/';
			$data['params']['addOns']['revslider-scrollvideo-addon']['sequence']['dir'] = $this->upload_dir['baseurl'] . '/revslider/scrollvideo/'.$new_id.'/';
		}
		
		return $data;
	}
}

?>