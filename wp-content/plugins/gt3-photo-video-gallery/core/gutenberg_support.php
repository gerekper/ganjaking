<?php

namespace GT3\Gallery\PhotoVideo;

defined('ABSPATH') OR exit;

class Gallery {
	protected $attributes = array(
		'border_col'     => array(
			'type'    => 'string',
			'default' => '#dddddd',
		),
		'border_padding' => array(
			'type'    => 'string',
			'default' => '0',
		),
		'border_size'    => array(
			'type'    => 'string',
			'default' => '1',
		),
		'border_type'    => array(
			'type'    => 'string',
			'default' => 'default',
		),
		'columns'        => array(
			'type'    => 'string',
			'default' => '3',
		),
		'corners_type'   => array(
			'type'    => 'string',
			'default' => 'default',
		),
		'is_margin'      => array(
			'type'    => 'string',
			'default' => 'default',
		),
		'margin'         => array(
			'type'    => 'string',
			'default' => '30',
		),
		'link'           => array(
			'type'    => 'string',
			'default' => 'default',
		),
		'order'          => array(
			'type'    => 'string',
			'default' => 'default',
		),
		'rand_order'     => array(
			'type'    => 'string',
			'default' => 'default',
		),
		'real_columns'   => array(
			'type'    => 'string',
			'default' => '',
		),
		'size'           => array(
			'type'    => 'string',
			'default' => 'default',
		),
		'thumb_type'     => array(
			'type'    => 'string',
			'default' => 'default',
		),
		'ids'            => array(
			'type'    => 'string',
			'default' => '',
		),
		'gt3_gallery'    => array(
			'type'    => 'string',
			'default' => 'yes',
		),
	);

	function __construct(){
		add_action('init', function(){
			if(function_exists('register_block_type')) {
				register_block_type('gt3-photo-video-gallery/gallery', array(
					'attributes'      => $this->attributes,
					'render_callback' => 'gt3pg_gallery_shortcode',
				));
			}
		});
	}
}

new Gallery();
