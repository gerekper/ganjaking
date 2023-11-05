<?php

namespace Smush\Core;

abstract class Controller {
	private $actions = array();

	private $filters = array();

	public function init() {
		foreach ( $this->actions as $action_hook => $actions ) {
			foreach ( $actions as $action_args ) {
				add_action( $action_hook, $action_args['callback'], $action_args['priority'], $action_args['accepted_args'] );
			}
		}

		foreach ( $this->filters as $filter_hook => $filters ) {
			foreach ( $filters as $filter_args ) {
				add_filter( $filter_hook, $filter_args['callback'], $filter_args['priority'], $filter_args['accepted_args'] );
			}
		}
	}

	public function stop() {
		foreach ( $this->actions as $action_hook => $actions ) {
			foreach ( $actions as $action_args ) {
				remove_action( $action_hook, $action_args['callback'], $action_args['priority'] );
			}
		}

		foreach ( $this->filters as $filter_hook => $filters ) {
			foreach ( $filters as $filter_args ) {
				remove_action( $filter_hook, $filter_args['callback'], $filter_args['priority'] );
			}
		}
	}

	public function register_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions[ $hook_name ][] = array(
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	public function register_filter( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters[ $hook_name ][] = array(
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}
}