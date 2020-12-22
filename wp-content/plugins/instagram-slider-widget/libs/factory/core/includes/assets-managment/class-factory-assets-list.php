<?php
/**
 * The class contains a base class for all lists of assets.
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 *
 * @package       factory-core
 * @since         1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Assets List
 *
 * @since 1.0.0
 */
class Wbcr_Factory439_AssetsList {

	protected $all = [];
	public $header_place = [];
	public $footer_place = [];
	public $required = [];

	protected $default_place;

	/**
	 * @var Wbcr_Factory439_Plugin
	 */
	protected $plugin;

	/**
	 * @param Wbcr_Factory439_Plugin $plugin
	 * @param bool                   $defaultIsFooter
	 */
	public function __construct( Wbcr_Factory439_Plugin $plugin, $defaultIsFooter = true ) {
		$this->plugin = $plugin;

		if ( $defaultIsFooter ) {
			$this->default_place = &$this->footer_place;
		}
		if ( ! $defaultIsFooter ) {
			$this->default_place = &$this->header_place;
		}
	}

	/**
	 * Remove items from the collection
	 *
	 * @return $this
	 */
	public function deregister() {
		foreach ( func_get_args() as $item ) {

			if ( ! is_string( $item ) ) {
				return $this;
			}

			$key_in_all           = array_search( $item, $this->all );
			$key_in_default_place = array_search( $item, $this->default_place );
			$key_in_header_place  = array_search( $item, $this->header_place );
			$key_inFooterPlace    = array_search( $item, $this->footer_place );

			if ( $key_in_all ) {
				unset( $this->all[ $key_in_all ] );
			}
			if ( $key_in_default_place ) {
				unset( $this->default_place[ $key_in_default_place ] );
			}
			if ( $key_in_header_place ) {
				unset( $this->header_place[ $key_in_header_place ] );
			}
			if ( $key_inFooterPlace ) {
				unset( $this->footer_place[ $key_inFooterPlace ] );
			}
		}

		return $this;
	}

	/**
	 * Checks whether the collection is empty.
	 *
	 * @param string $source   if the 'bootstrap' specified, checks only whether the bootstrap assets were required.
	 *
	 * @return boolean
	 */
	public function isEmpty( $source = 'wordpress' ) {
		if ( 'bootstrap' === $source ) {
			return empty( $this->required[ $source ] );
		}

		return empty( $this->all ) && empty( $this->required );
	}

	public function IsHeaderEmpty() {
		return empty( $this->header_place );
	}

	public function IsFooterEmpty() {
		return empty( $this->footer_place );
	}

	/**
	 * Adds new items to the requried collection.
	 *
	 * @param mixed
	 */
	public function request( $items, $source = 'wordpress' ) {

		if ( is_array( $items ) ) {
			foreach ( $items as $item ) {
				$this->required[ $source ][] = $item;
			}
		} else {
			$this->required[ $source ][] = $items;
		}

		return $this;
	}
}

