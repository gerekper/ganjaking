<?php
/**
 * Plugin Name:     Disable "BIG image" Threshold
 * Plugin URI:      https://wordpress.org/plugins/disable-big-image-threshold
 * Description:     Disables the "BIG image" threshold added in WordPress 5.3.
 * Author:          Jonathan Desrosiers
 * Author URI:      https://jonathandesrosiers.com
 * Text Domain:     disable-big-image-threshold
 * Version:         1.0
 *
 * @package         Disable_Big_Image_Threshold
 */

add_filter( 'big_image_size_threshold', '__return_false' );
