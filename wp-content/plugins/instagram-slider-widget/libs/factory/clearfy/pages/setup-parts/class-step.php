<?php

namespace WBCR\FactoryClearfy227\Pages;

/**
 * Step
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
abstract class Step {

	protected $id;

	protected $prev_id = false;
	protected $next_id = false;

	/**
	 * @var \WBCR\FactoryClearfy227\Pages\Setup
	 */
	protected $page;

	/**
	 * @var \Wbcr_Factory436_Plugin
	 */
	protected $plugin;

	public function __construct(\WBCR\FactoryClearfy227\Pages\Setup $page)
	{
		$this->page = $page;
		$this->plugin = $page->plugin;
		//$this->form_handler();
	}

	public function get_id()
	{
		if( empty($this->id) ) {
			throw new \Exception('Step ID setting is required for the {' . static::class . '} class!');
		}

		return $this->id;
	}

	public function get_next_id()
	{
		return $this->next_id;
	}

	/**
	 * Requests assets (js and css) for the page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @see   FactoryPages435_AdminPage
	 *
	 */
	public function assets($scripts, $styles)
	{
		// nothing
	}

	protected function continue_step($skip = false)
	{
		$next_id = $this->get_next_id();
		if( !$next_id ) {
			$next_id = $this->get_id();
		}
		wp_safe_redirect($this->page->getActionUrl($next_id));
		die();
	}

	protected function skip_step()
	{
		$this->continue_step(true);
	}

	abstract public function get_title();

	abstract public function html();

}