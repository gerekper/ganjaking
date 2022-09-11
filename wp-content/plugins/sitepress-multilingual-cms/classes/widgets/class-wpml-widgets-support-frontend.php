<?php

use WPML\FP\Fns;
use WPML\FP\Lst;
use WPML\FP\Obj;
use WPML\FP\Str;
use WPML\LIB\WP\Hooks;

/**
 * This code is inspired by WPML Widgets (https://wordpress.org/plugins/wpml-widgets/),
 * created by Jeroen Sormani
 *
 * @author OnTheGo Systems
 */
class WPML_Widgets_Support_Frontend implements IWPML_Action {

	/** @var array $displayFor */
	private $displayFor;

	/**
	 * WPML_Widgets constructor.
	 *
	 * @param string $current_language
	 */
	public function __construct( $current_language ) {
		$this->displayFor = [ null, $current_language, 'all' ];
	}

	public function add_hooks() {
		add_filter( 'widget_block_content', [ $this, 'filterByLanguage' ], - PHP_INT_MAX, 1 );
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public function filterByLanguage( $content ) {
		$render = function () use ( $content ) {
			return wpml_collect( parse_blocks( $content ) )
				->map( Fns::unary( 'render_block' ) )
				->reduce( Str::concat(), '' );
		};

		return Hooks::callWithFilter( $render, 'pre_render_block', [ $this, 'shouldRender' ], 10, 2 );
	}

	/**
	 * Determine if a block should be rendered depending on its language
	 * Returning an empty string will stop the block from being rendered.
	 *
	 * @param string|null $pre_render The pre-rendered content. Default null.
	 * @param array       $block      The block being rendered.
	 *
	 * @return string|null
	 */
	public function shouldRender( $pre_render, $block ) {
		return Lst::includes( Obj::path( [ 'attrs', 'wpml_language' ], $block ), $this->displayFor ) ? $pre_render : '';
	}
}
