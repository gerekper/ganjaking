<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/includes
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $rtwwdpd_actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $rtwwdpd_actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $rtwwdpd_filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $rtwwdpd_filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->rtwwdpd_actions = array();
		$this->rtwwdpd_filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $rtwwdpd_hook             The name of the WordPress action that is being registered.
	 * @param    object               $rtwwdpd_component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $rtwwdpd_callback         The name of the function definition on the $rtwwdpd_component.
	 * @param    int                  $rtwwdpd_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $rtwwdpd_accepted_args    Optional. The number of arguments that should be passed to the $rtwwdpd_callback. Default is 1.
	 */
	public function rtwwdpd_add_action( $rtwwdpd_hook, $rtwwdpd_component, $rtwwdpd_callback, $rtwwdpd_priority = 10, $rtwwdpd_accepted_args = 1 ) {
		$this->rtwwdpd_actions = $this->rtwwdpd_add( $this->rtwwdpd_actions, $rtwwdpd_hook, $rtwwdpd_component, $rtwwdpd_callback, $rtwwdpd_priority, $rtwwdpd_accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $rtwwdpd_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $rtwwdpd_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $rtwwdpd_callback         The name of the function definition on the $rtwwdpd_component.
	 * @param    int                  $rtwwdpd_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $rtwwdpd_accepted_args    Optional. The number of arguments that should be passed to the $rtwwdpd_callback. Default is 1
	 */
	public function rtwwdpd_add_filter( $rtwwdpd_hook, $rtwwdpd_component, $rtwwdpd_callback, $rtwwdpd_priority = 10, $rtwwdpd_accepted_args = 1 ) {
		$this->rtwwdpd_filters = $this->rtwwdpd_add( $this->rtwwdpd_filters, $rtwwdpd_hook, $rtwwdpd_component, $rtwwdpd_callback, $rtwwdpd_priority, $rtwwdpd_accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $rtwwdpd_hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $rtwwdpd_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $rtwwdpd_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $rtwwdpd_callback         The name of the function definition on the $component.
	 * @param    int                  $rtwwdpd_priority         The priority at which the function should be fired.
	 * @param    int                  $rtwwdpd_accepted_args    The number of arguments that should be passed to the $rtwwdpd_callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function rtwwdpd_add( $rtwwdpd_hooks, $rtwwdpd_hook, $rtwwdpd_component, $rtwwdpd_callback, $rtwwdpd_priority, $rtwwdpd_accepted_args ) {

		$rtwwdpd_hooks[] = array(
			'hook'          => $rtwwdpd_hook,
			'component'     => $rtwwdpd_component,
			'callback'      => $rtwwdpd_callback,
			'priority'      => $rtwwdpd_priority,
			'accepted_args' => $rtwwdpd_accepted_args
		);

		return $rtwwdpd_hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function rtwwdpd_run() {

		foreach ( $this->rtwwdpd_filters as $rtwwdpd_hook ) 
		{
			
			add_filter( $rtwwdpd_hook['hook'], array( $rtwwdpd_hook['component'], $rtwwdpd_hook['callback'] ), $rtwwdpd_hook['priority'], $rtwwdpd_hook['accepted_args'] );
		}

		foreach ( $this->rtwwdpd_actions as $rtwwdpd_hook ) {
			add_action( $rtwwdpd_hook['hook'], array( $rtwwdpd_hook['component'], $rtwwdpd_hook['callback'] ), $rtwwdpd_hook['priority'], $rtwwdpd_hook['accepted_args'] );
		}

	}

}
