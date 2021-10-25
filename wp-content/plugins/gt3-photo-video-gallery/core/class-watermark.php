<?php

namespace GT3\PhotoVideoGallery;

use Imagick;
use WP_Filesystem_Direct;

class Watermark {
	private static $upload_dir = '';
	private static $upload_dir_originals = '';
	private static $mime_types = array(
		'image/jpeg',
		'image/png'
	);

	public static function wp_handle_upload_handler($file){
		if (!static::check_folder()) $file;


		if(in_array($file['type'], static::$mime_types)) {
			add_filter('wp_generate_attachment_metadata', array( __CLASS__, 'process' ), 10, 2);
		}

		return $file;
	}

	public static function delete_attachment_handler($post_id){
		$post = wp_prepare_attachment_for_js($post_id);
		if(!$post) {
			return;
		}
		if(!in_array($post['mime'], static::$mime_types)) {
			return;
		}

		static::restore($post_id);
	}

	public static function process($meta, $uploaded_id = false){
		if (!static::check_folder()) return;

		$image_id = false !== $uploaded_id ? $uploaded_id : $meta;

		$settings  = Settings::instance()->getSettings('basic');
		$settings  = $settings['watermark'];
		$watermark = $settings['image'];

		$image_path = wp_prepare_attachment_for_js($image_id);

		$watermark_path = get_attached_file($watermark['id']);
		if(false === $watermark_path) {
			return $meta;
		}
		set_transient('gt3_watermark_processing', true, '30');
		$watermark_id = $watermark['id'];
		static::restore($image_id);

		// get watermark dimensions

		$position = $settings['alignment'];
		list($dest_x, $dest_y) = array( 0, 0 );

		$sizes           = array_reverse($settings['sizes']);
		$watermark_paths = [];

		$fs = new WP_Filesystem_Direct([]);
		foreach($sizes as $size => $size_en) {
			if($size === 'full') {
				$image_path_size = wp_normalize_path(get_attached_file($image_path['id']));
				$path            = str_replace(static::$upload_dir, '', $image_path_size);
			} else {
				$image_path_size = image_get_intermediate_size($image_path['id'], $size);
				$path            = $image_path_size['path'];
				$image_path_size = static::$upload_dir.'/'.$image_path_size['path'];
				$image_path_size = wp_normalize_path($image_path_size);
			}

			if(false === $image_path_size || !file_exists($image_path_size) || !is_file($image_path_size)) {
				continue;
			}
			$original_image_path = wp_normalize_path(static::$upload_dir_originals.$path);
			wp_mkdir_p(dirname($original_image_path));
			$fs->copy($image_path_size, $original_image_path, true);
			if (!$fs->exists($original_image_path)) continue;

			$image     = new Imagick($image_path_size);
			$image_dim = $image->getImageGeometry();

			$watermark = new Imagick($watermark_path);

			if($watermark->getImageAlphaChannel() > 0) {
				$watermark->evaluateImage(Imagick::EVALUATE_MULTIPLY, ($settings['opacity']/100), Imagick::CHANNEL_ALPHA);
			} else {
				if(property_exists(Imagick::class, 'ALPHACHANNEL_OPAQUE')) {
					$watermark->setImageAlphaChannel(Imagick::ALPHACHANNEL_OPAQUE);
				}
				$watermark->evaluateImage(Imagick::EVALUATE_DIVIDE, 1/($settings['opacity']/100), Imagick::CHANNEL_ALPHA);
			}
			$watermark_dim = $watermark->getImageGeometry();

			$watermark_width  = round($image_dim['width']/10, 0);
			$watermark_coef   = round($watermark_dim['width']/$watermark_dim['height'], 2);
			$watermark_height = round($watermark_width/$watermark_coef, 0);

			// calculate watermark new dimensions
			$watermark->resizeImage($watermark_width, $watermark_height, imagick::FILTER_CATROM, 1);
			$offset = min(round($image_dim['width']/(100/5), 0), round($image_dim['height']/(100/5), 0));

			switch($position) {
				case 'left_top':
					$dest_x = $offset;
					$dest_y = $offset;
					break;
				case 'right_bottom':
					$dest_x = $image_dim['width']-$watermark_width-$offset;
					$dest_y = $image_dim['height']-$watermark_height-$offset;
					break;
				case 'left_bottom':
					$dest_x = $offset;
					$dest_y = $image_dim['height']-$watermark_height-$offset;
					break;
				case 'right_top':
					$dest_x = $image_dim['width']-$watermark_width-$offset;
					$dest_y = $offset;
					break;
			}

//			$image->setImageCompressionQuality($settings['quality']);
//			$image->setImageCompression(imagick::COMPRESSION_JPEG);

			$image->compositeImage($watermark, Imagick::COMPOSITE_DEFAULT, $dest_x, $dest_y, Imagick::CHANNEL_ALL);

			$image->writeImage($image_path_size);
			$watermark_paths[$size] = $original_image_path;
			$image->destroy();
			$watermark->destroy();
		}

		update_post_meta($image_id, '_watermark_original', wp_json_encode($watermark_paths));
		update_post_meta($image_id, '_watermark_id', $watermark_id);
		delete_transient('gt3_watermark_processing');

		if(false !== $uploaded_id) {
			return $meta;
		}

		return array(
			'error' => false,
			'msg'   => __('Watermark has been added.'),
		);
	}

	public static function restore($image_id){
		if (!static::check_folder()) return;

		$originals = get_post_meta($image_id, '_watermark_original', true);
		if(false !== strpos($originals, '\\')) {
			$originals = str_replace('\\', '/', $originals);
		}
		if('' !== $originals) {
			try {
				$originals = json_decode($originals, true);
				if(json_last_error()) {
					$originals = array();
				}
			} catch(\Exception $exception) {
				$originals = array();
			}
		}
		if (!is_array($originals)) {
			$originals = array();
		}
		$fs = new WP_Filesystem_Direct([]);

		foreach($originals as $size => $original) {
			if($size === 'full') {
				$image_path_size = get_attached_file($image_id);
			} else {
				$image_path_size = image_get_intermediate_size($image_id, $size);
				$image_path_size = static::$upload_dir.'/'.$image_path_size['path'];
			}
			$image_path_size = wp_normalize_path($image_path_size);

			if(false === $image_path_size || !file_exists($image_path_size) || !is_file($image_path_size)) {
				continue;
			}

			$fs->move($original, $image_path_size, true);
		}
		delete_post_meta($image_id, '_watermark_original');
		delete_post_meta($image_id, '_watermark_id');
	}

	public static function check_folder(){
		if (!function_exists('WP_Filesystem')) {
			require_once(ABSPATH.'wp-admin/includes/file.php');
		}
		if (!WP_Filesystem() || 'direct' !== get_filesystem_method()) {
			return false;
		};
		if(!strlen(static::$upload_dir)) {
			static::$upload_dir           = _wp_upload_dir();
			static::$upload_dir           = wp_normalize_path(static::$upload_dir['basedir']);
			static::$upload_dir_originals = wp_normalize_path(static::$upload_dir.'/watermark_original/');

			$fs = new WP_Filesystem_Direct([]);
			if(!$fs->exists(static::$upload_dir_originals)) {
				$fs->mkdir(static::$upload_dir_originals);
				$fs->chmod(static::$upload_dir_originals, 0755);
			}
			return true;
		}
		return false;
	}
}
