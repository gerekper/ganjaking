<?php

namespace GT3\PhotoVideoGallery\Block\Traits;
defined('ABSPATH') OR exit;

trait Default_Attributes_Trait {
	protected $default_attributes = array();

	protected function getDefaultsAttributes(){
		return array(
			// Basic
			'firstLoad'      => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'align'          => array(
				'type'    => 'string',
				'default' => '',
			),
			'blockAlignment' => array(
				'type'    => 'string',
				'default' => '',
			),
			'textAlignment'  => array(
				'type'    => 'string',
				'default' => '',
			),
			'_uid'           => array(
				'type'    => 'string',
				'default' => '',
			),
			'_blockName'     => array(
				'type'    => 'string',
				'default' => '',
			),
			'className'      => array(
				'type'    => 'string',
				'default' => '',
			),
			'blockAnimation' => array(
				'type'    => 'object',
				'default' => array(
					'type'     => '',
					'speed'    => 'normal',
					'delay'    => 0,
					'infinite' => false,
				),
			),
			// Images
			'source'         => array(
				'type'    => 'string',
				'default' => 'module',
			),
			'gallery'        => array(
				'type'    => 'string',
				'default' => '',
			),
			'ids'            => array(
				'type'    => 'string',
				'default' => '',
			),
			'categories'     => array(
				'type'    => 'array',
				'default' => array(),
				'items'   => array(
					'type' => 'mixed'
				),
			),
			//
			'images'         => array(
				'type'    => 'array',
				'default' => array(),
				'items'   => array(
					'type' => 'mixed'
				),
			),
			// General
			'rightClick'     => array(
				'type'    => 'string',
				'default' => 'default',
			),
			'random'         => array(
				'type'    => 'string',
				'default' => 'default',
			),
			// Load More
			'loadMoreEnable' => array(
				'type'    => 'string',
				'default' => 'default',
			),
			'loadMoreFirst'  => array(
				'type'    => 'string',
				'default' => 'default',
			),
			'loadMoreLimit'  => array(
				'type'    => 'string',
				'default' => 'default',
			),
		);
	}

	protected function getDefaults(){
		$defaults = array();
		foreach($this->default_attributes as $key => $value) {
			if(key_exists('default', $value)) {
				$defaults[$key] = $value['default'];
			}
		}

		return $defaults;
	}
}
