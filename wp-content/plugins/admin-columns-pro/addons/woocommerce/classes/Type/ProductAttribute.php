<?php

namespace ACA\WC\Type;

class ProductAttribute {

	const TAXONOMY_PREFIX = 'pa_';

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param string $name 'pa_color', 'pa_material', 'color'
	 */
	public function __construct( $name ) {
		$this->name = $name;
	}

	/**
	 * @return bool
	 */
	public function is_taxonomy() {
		return 0 === strpos( $this->name, self::TAXONOMY_PREFIX ) && taxonomy_exists( $this->get_taxonomy_name() );
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_taxonomy_name() {
		return wc_attribute_taxonomy_name( urldecode( $this->remove_prefix( self::TAXONOMY_PREFIX, $this->name ) ) );
	}

	/**
	 * @param string $prefix
	 * @param string $string
	 *
	 * @return string
	 */
	private function remove_prefix( $prefix, $string ) {
		return (string) preg_replace( "/^{$prefix}/", '', $string );
	}

	/**
	 * @return string
	 */
	public function get_label() {
		return $this->is_taxonomy()
			? wc_attribute_label( $this->get_taxonomy_name() )
			: wc_attribute_label( $this->name );
	}

}