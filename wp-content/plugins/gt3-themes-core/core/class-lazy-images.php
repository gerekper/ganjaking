<?php

namespace GT3\ThemesCore;

defined('ABSPATH') OR exit;

class Lazy_Images {
	private static $instance = null;

	/** @return Lazy_Images */
	public static function instance(){
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct(){
		if(is_admin()) {
			return;
		}

		add_filter('wp_kses_allowed_html', array( $this, 'allow_lazy_attributes' ));
		add_action('wp_footer', array($this, 'wp_footer'));
	}

	public function setup_filters(){
		add_filter('jetpack_lazy_images_skip_image_with_attributes', '__return_true');
		add_filter('wp_get_attachment_image_attributes', array( $this, 'process_image_attributes' ), 0, 3);
	}

	public function remove_filters(){
		remove_filter('jetpack_lazy_images_skip_image_with_attributes', '__return_true');
		remove_filter('wp_get_attachment_image_attributes', array( $this, 'process_image_attributes' ), 0);
	}

	public function allow_lazy_attributes($allowed_tags){
		if(!isset($allowed_tags['img'])) {
			return $allowed_tags;
		}

		// But, if images are allowed, ensure that our attributes are allowed!
		$img_attributes = array_merge(
			$allowed_tags['img'],
			array(
				'data-lazy-src'    => 1,
				'data-lazy-srcset' => 1,
				'data-lazy-sizes'  => 1,
			)
		);

		$allowed_tags['img'] = $img_attributes;

		return $allowed_tags;
	}

	public function should_skip_image_with_blacklisted_class($classes){
		$blacklisted_classes = array();

		$blacklisted_classes = apply_filters('gt3/core/lazy_images_blacklisted_classes', $blacklisted_classes);

		if(!is_array($blacklisted_classes) || empty($blacklisted_classes)) {
			return false;
		}

		foreach($blacklisted_classes as $class) {
			if(false !== strpos($classes, $class)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array   $attributes
	 * @param WP_Post $attachment
	 *
	 * @return array
	 */
	public function process_image_attributes($attributes, $attachment, $size){
		if(empty($attributes['src'])) {
			return $attributes;
		}

		if(!empty($attributes['class']) && $this->should_skip_image_with_blacklisted_class($attributes['class'])) {
			return $attributes;
		}

		$old_attributes = $attributes;

		foreach(array( 'srcset', 'sizes' ) as $attribute) {
			if(isset($old_attributes[$attribute])) {
				$attributes["data-lazy-$attribute"] = $old_attributes[$attribute];
				unset($attributes[$attribute]);
			}
		}

		$attributes['data-lazy-src'] = esc_url_raw(add_query_arg('is-pending-load', true, $attributes['src']));

		$attributes['srcset'] = $this->get_placeholder_image($attachment->ID, $size);
		$attributes['class']  = sprintf(
			'%s gt3-core-lazy-image',
			empty($old_attributes['class'])
				? ''
				: $old_attributes['class']
		);

		self::remove_filters();

		return apply_filters('gt3pg_pro_lazy_images_new_attributes', $attributes);
	}

	public static function get_placeholder_image($id, $size = 'full'){
		$placeholder = get_post_meta($id, '_gt3-core-lazy-placeholder', true);
		if('' === $placeholder) {
			$placeholder = static::transparentImage($id, $size);
			update_post_meta($id, '_gt3-core-lazy-placeholder', $placeholder);
		}

		return apply_filters(
			'lazyload_images_placeholder_image',
			$placeholder
		);
	}

	protected static function transparentImage($id, $size){
		list ($url, $width, $height) = image_downsize($id, $size);
		$w     = round($width/10);
		$image = imagecreatetruecolor($w, round($w/(round($width/$height, 2)), 2));
		imagesavealpha($image, true);
		imagealphablending($image, false);
		$color = imagecolorallocatealpha($image, 0, 0, 0, 127); //fill transparent back
		imagefill($image, 0, 0, $color);
		ob_start();
		imagepng($image, null, 9);
		$buffer      = base64_encode(ob_get_clean());
		$placeholder = 'data:image/png;base64,'.($buffer);

		return $placeholder;
	}

	public function wp_footer(){
		static $loaded = 0;
		if($loaded++) {
			return;
		}

		wp_enqueue_script(
			'gt3-core-lazy-images',
			Assets::get_dist_url().'js/lazy.js',
			array(),
			filemtime(Assets::get_dist_path().'js/lazy.js'),
			true
		);

		?>
		<style>
			img.gt3-core-lazy-image {
				opacity: 0;
				visibility: visible;
				transition: opacity .4s linear, visibility .4s linear;
			}
			img.gt3-core-lazy-image.gt3-core-lazy-image--loaded {
				opacity: 1;
				visibility: visible;
			}
		</style>


		<?php
	}

}
