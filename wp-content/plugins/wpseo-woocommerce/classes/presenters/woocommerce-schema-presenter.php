<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

use Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter;

/**
 * Presents the schema output inside a script tag.
 */
class WPSEO_WooCommerce_Schema_Presenter extends Abstract_Indexable_Presenter {

	/**
	 * The Schema to output.
	 *
	 * @var array
	 */
	protected $graph;

	/**
	 * The classes to add to the script tag.
	 *
	 * @var string[]
	 */
	protected $classes;

	/**
	 * WPSEO_WooCommerce_Schema_Presenter constructor.
	 *
	 * @param array    $graph   The schema graph.
	 * @param string[] $classes The classes to add to the script tag.
	 */
	public function __construct( $graph, $classes ) {
		$this->graph   = $graph;
		$this->classes = $classes;
	}

	/**
	 * Gets the raw schema value as an associative array.
	 *
	 * @return array The raw schema.
	 */
	public function get() {
		return $this->graph;
	}

	/**
	 * Presents the schema in a script tag.
	 *
	 * @return string The schema in a script tag.
	 */
	public function present() {
		$graph = $this->get();

		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		];

		$classes_string = \implode( ' ', $this->classes );

		$output = \WPSEO_Utils::format_json_encode( $schema );
		return '<script type="application/ld+json" class="' . \esc_attr( $classes_string ) . '">' . $output . '</script>' . PHP_EOL;
	}

	/**
	 * Returns the output as string.
	 *
	 * @return string The output.
	 */
	public function __toString() {
		return $this->present();
	}
}
