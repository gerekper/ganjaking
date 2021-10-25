<?php
namespace GT3\PhotoVideoGalleryPro\Elementor\Widgets;
defined('ABSPATH') OR exit;

use GT3_Post_Type_Gallery;
use Elementor\Controls_Manager;
use GT3\PhotoVideoGalleryPro\Elementor\Widgets\Basic;

abstract class Album_Basic extends Basic {
	const POST_TYPE = GT3_Post_Type_Gallery::post_type;
	const TAXONOMY = GT3_Post_Type_Gallery::taxonomy;
}
