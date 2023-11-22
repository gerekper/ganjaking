<?php

namespace DynamicContentForElementor;

trait Image
{
    public static function get_thumbnail_sizes()
    {
        $sizes = get_intermediate_image_sizes();
        $ret = [];
        foreach ($sizes as $s) {
            $ret[$s] = $s;
        }
        return $ret;
    }
    public static function is_resized_image($imagePath)
    {
        $ext = \pathinfo($imagePath, \PATHINFO_EXTENSION);
        $pezzi = \explode('-', \substr($imagePath, 0, -(\strlen($ext) + 1)));
        if (\count($pezzi) > 1) {
            $misures = \array_pop($pezzi);
            $fullsize = \implode('-', $pezzi) . '.' . $ext;
            $pezzi = \explode('x', $misures);
            if (\count($pezzi) == 2) {
                if (\is_numeric($pezzi[0]) && \is_numeric($pezzi[1])) {
                    return $fullsize;
                    // return original value
                }
            }
        }
        return \false;
    }
    public static function get_image_id($image_url)
    {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type LIKE 'attachment' AND guid LIKE %s;", '%' . $wpdb->esc_like($image_url) . '%');
        $attachment = $wpdb->get_col($sql);
        $img_id = \reset($attachment);
        if (!$img_id) {
            if (\strpos($image_url, '-scaled.') !== \false) {
                $image_url = \str_replace('-scaled.', '.', $image_url);
                $img_id = self::get_image_id($image_url);
            }
        }
        return $img_id;
    }
    /**
     * Get size information for all currently-registered image sizes.
     *
     * @return array $sizes Data for all currently-registered image sizes.
     * @copyright MA-Group
     * @license GPL v3
     * @link http://ali2woo.com/
     */
    public static function get_image_sizes()
    {
        global $_wp_additional_image_sizes;
        $sizes = array();
        foreach (get_intermediate_image_sizes() as $_size) {
            if (\in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                $sizes[$_size]['width'] = get_option("{$_size}_size_w");
                $sizes[$_size]['height'] = get_option("{$_size}_size_h");
                $sizes[$_size]['crop'] = (bool) get_option("{$_size}_crop");
            } elseif (isset($_wp_additional_image_sizes[$_size])) {
                $sizes[$_size] = array('width' => $_wp_additional_image_sizes[$_size]['width'], 'height' => $_wp_additional_image_sizes[$_size]['height'], 'crop' => $_wp_additional_image_sizes[$_size]['crop']);
            }
        }
        return $sizes;
    }
    /**
     * Get size information for a specific image size.
     *
     * @param string $size The image size for which to retrieve data.
     * @return false|array $size Size data about an image size or false if the size doesn't exist.
     * @copyright MA-Group
     * @license GPL v3
     * @link http://ali2woo.com/
     */
    public static function get_image_size($size)
    {
        $sizes = self::get_image_sizes();
        if (isset($sizes[$size])) {
            return $sizes[$size];
        }
        return \false;
    }
    /**
     * Get the width of a specific image size.
     *
     * @param string $size The image size for which to retrieve data.
     * @return false|string $size Width of an image size or false if the size doesn't exist.
     * @copyright MA-Group
     * @license GPL v3
     * @link http://ali2woo.com/
     */
    public static function get_image_width($size)
    {
        if (!($size = self::get_image_size($size))) {
            return \false;
        }
        if (isset($size['width'])) {
            return $size['width'];
        }
        return \false;
    }
    /**
     * Get the height of a specific image size.
     *
     * @param string $size The image size for which to retrieve data.
     * @return false|string $size Height of an image size or false if the size doesn't exist.
     * @copyright MA-Group
     * @license GPL v3
     * @link http://ali2woo.com/
     */
    public static function get_image_height($size)
    {
        $size = self::get_image_size($size);
        if (!$size) {
            return \false;
        }
        if (isset($size['height'])) {
            return $size['height'];
        }
        return \false;
    }
    /**
     * Get Image alt
     *
     * @param integer $attachment_ID
     * @return string
     */
    public static function get_image_alt(int $attachment_ID)
    {
        $alt = get_post_meta($attachment_ID, '_wp_attachment_image_alt', \true);
        if (!empty($alt)) {
            return esc_attr($alt);
        }
        return '';
    }
}
