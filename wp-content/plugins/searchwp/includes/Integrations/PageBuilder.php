<?php

/**
 * SearchWP PageBuilder.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Integrations;

/**
 * Class PageBuilder is responsible for customizing SearchWP's Native
 * implementation to work with page builder queries by allowing Native
 * (and forcing Native) to run outside the main query.
 *
 * @since 4.1.5
 */
abstract class PageBuilder {

	/**
	 * Name used for canonical reference to Integration.
	 *
	 * @since 4.1.5
	 * @var   string
	 */
	protected $name = 'pagebuilder';

	/**
	 * Prevent unwanted overruns.
	 *
	 * @since 4.1.8
	 * @var false
	 */
	public $run_once = true;

	/**
	 * The Engine to use.
	 *
	 * @since 4.1.8
	 * @var string
	 */
	public $engine = 'default';

	/**
	 * Modify Native behavior by forcing execution outside the main query.
	 *
	 * @since 4.1.5
	 * @return void
	 */
	public function modify_native_behavior() {
		$applicable = ! ( is_admin() || wp_doing_ajax() );

		if ( ! apply_filters( 'searchwp\integration\pagebuilder\enabled', $applicable, [
			'context' => $this->name
		] ) ) {
			return;
		}

		add_filter( 'searchwp\native\short_circuit', [ $this, 'short_circuit'], 5, 2 );
		add_filter( 'searchwp\native\force',         [ $this, 'force' ], 990, 2 );
		add_filter( 'searchwp\native\strict',        [ $this, 'strict' ], 990, 2 );
		add_action( 'pre_get_posts',                 [ $this, 'pre_get_posts' ], -1 );
	}

	/**
	 * Hook in before \SearchWP\Native so as to trigger it for the page builder module query.
	 *
	 * @since 4.1.8.
	 * @param mixed $query
	 * @return void
	 */
	public function pre_get_posts( $query ) {
		if ( ! $query->is_main_query() || ! isset( $_GET['s'] ) ) {
			return;
		}

		$engine = apply_filters( 'searchwp\integration\pagebuilder\engine', 'default', [
			'context' => $this->name,
			'query'   => $query,
		] );

		// Allow short circuit by emptying engine.
		if ( empty( $engine ) ) {
			return;
		}

		$this->engine = (string) $engine;
		$query->set( 'searchwp', $this->engine );

		add_filter( 'searchwp\native\args', [ $this, 'native_args' ], 990, 2 );

		// Prevent this from happening again.
		if ( $this->run_once ) {
			remove_action( 'pre_get_posts', [ $this, 'pre_get_posts' ], -1 );
		}
	}

	/**
	 * Updates the \SearchWP\Native arguments.
	 *
	 * @since 4.1.8
	 * @param array $args Incoming arguments.
	 * @param \WP_Query $query The \WP_Query.
	 * @return array
	 */
	public function native_args( $args, $query ) {
		$args['s']      = stripslashes( $_GET['s'] );
		$args['engine'] = $this->engine;

		// Prevent this from happening again.
		if ( $this->run_once ) {
			remove_filter( 'searchwp\native\args', [ $this, 'native_args' ], 990, 2 );
		}

		return $args;
	}

	/**
	 * Prevent \SearchWP\Native from short circuiting.
	 *
	 * @since 4.1.8
	 * @param boolean $short_circuit
	 * @param \WP_Query $query
	 * @return boolean
	 */
	public function short_circuit( $short_circuit, $query ) {
		if ( $query->is_main_query() && isset( $_GET['s'] ) ) {
			$short_circuit = false;
		}

		// Prevent this from happening again.
		if ( $this->run_once ) {
			remove_filter( 'searchwp\native\short_circuit', [ $this, 'short_circuit '], 5 );
		}

		return $short_circuit;
	}

	/**
	 * Force \SearchWP\Native to run.
	 *
	 * @since 4.1.8
	 * @param boolean $force
	 * @param array $args
	 * @return boolean
	 */
	public function force( $force, $args ) {
		if ( $args['query']->is_search() ) {
			$force = true;
		}

		// Prevent this from happening again.
		if ( $this->run_once ) {
			remove_filter( 'searchwp\native\force', [ $this, 'force' ], 990 );
		}

		return $force;
	}

	/**
	 * Prevent \SearchWP\Native from being strict to is_main_query()
	 *
	 * @since 4.1.8
	 * @param boolean $strict
	 * @param \WP_Query $query
	 * @return boolean
	 */
	public function strict( $strict, $query ) {
		if ( ! $query->is_search() ) {
			$strict = true;
		}

		// Prevent this from happening again.
		if ( $this->run_once ) {
			remove_filter( 'searchwp\native\strict', [ $this, 'strict' ], 990 );
		}

		return $strict;
	}
}
