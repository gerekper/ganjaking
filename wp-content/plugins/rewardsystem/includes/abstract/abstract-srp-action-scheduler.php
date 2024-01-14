<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * SRP_Action_Scheduler class.
 */
abstract class SRP_Action_Scheduler {

	/**
	 * ID
	 * 
	 * @var string.
	 * */
	protected $id;

	/**
	 * Progress Bar Count.
	 * */
	protected $progress_bar_count = 'srp_progress_bar_count';

	/**
	 * Action Scheduler Name.
	 */
	protected $action_scheduler_name;

	/**
	 * Chunked Action Scheduler Name.
	 */
	protected $chunked_action_scheduler_name;

	/**
	 * Option Name.
	 */
	protected $option_name;

	/**
	 * Settings option Name.
	 */
	protected $settings_option_name;

	/**
	 * Class initialization.
	 */
	public function __construct() {
		// Scheduler action.
		add_action( $this->get_action_scheduler_name(), array( $this, 'scheduler_action' ) );
		// Chunked action scheduler action.
		add_action( $this->get_chunked_action_scheduler_name(), array( $this, 'chunked_scheduler_action' ) );
		//Custom Dashboard menu.
		add_action( 'admin_menu', array( $this, 'custom_dashboard_page' ) );
		//Remove dashboard navigation menu.
		add_action( 'admin_head', array( $this, 'remove_dashboard_navigation_menu' ) );
	}

	/**
	 * Get id.
	 * 
	 * @return string
	 * */
	public function get_id() {
		return $this->id;
	}

	/*
	 * Schedule action.
	 */

	public function schedule_action( $post_ids, $setting_values ) {

		if ( false === as_next_scheduled_action( $this->get_action_scheduler_name() ) || false == as_next_scheduled_action( $this->get_chunked_action_scheduler_name() ) ) {
			// Delete progress count.
			$this->delete_progress_count();
			// Update options.
			$this->update_action_scheduler_args( $post_ids, $setting_values );
			//Schedule the event to update.
			as_schedule_single_action( time(), $this->get_action_scheduler_name() );
		}
	}

	/*
	 * Update action scheduler arguments.
	 */

	public function update_action_scheduler_args( $post_ids, $setting_values ) {
		update_option( $this->option_name, $post_ids );
		update_option( $this->settings_option_name, $setting_values );
	}

	/*
	 * Scheduler action.
	 */

	public function scheduler_action() {

		$chunked_data = array_filter( array_chunk( $this->get_posts_data(), 10 ) );
		if ( empty( $chunked_data ) ) {
			return;
		}

		foreach ( $chunked_data as $index => $value ) {
			//Schedule the event to update.
			as_schedule_single_action( time() + $index, $this->get_chunked_action_scheduler_name(), array( 'chunked_value' => $value ) );
		}
	}

	/*
	 * Get posts data.
	 */

	public function get_posts_data() {
		return get_option( $this->option_name, array() );
	}

	/*
	 * Get settings data.
	 */

	public function get_settings_data() {
		return get_option( $this->settings_option_name, array() );
	}

	/*
	 * Chunked scheduler action.
	 */

	public function chunked_scheduler_action( $chunked_value ) {
		return array();
	}

	/*
	 * Add Custom Dashboard Page
	 */

	public function custom_dashboard_page() {

		if ( ! isset( $_GET[ 'rs_action_scheduler' ] ) ) {
			return;
		}

		add_dashboard_page(
				$this->get_page_title(), $this->get_menu_title(), 'read', $this->get_menu_slug(), array( $this, 'progress_bar' )
		);
	}

	/*
	 * Get page title
	 */

	public function get_page_title() {
		return __( 'Action Scheduler', 'rewardsystem' );
	}

	/*
	 * Get menu title
	 */

	public function get_menu_title() {
		return __( 'Action Scheduler', 'rewardsystem' );
	}

	/*
	 * Get menu slug
	 */

	public function get_menu_slug() {
		return isset( $_GET[ 'rs_action_scheduler' ] ) ? 'rewardsystem_callback' : '';
	}

	/*
	 * Remove dashboard navigation menu
	 */

	public function remove_dashboard_navigation_menu() {
		remove_submenu_page( 'index.php', $this->get_menu_slug() );
	}

	/**
	 * Set action scheduler name.
	 */
	public function set_action_scheduler_name( $action_scheduler_name ) {
		$this->action_scheduler_name = $action_scheduler_name;
	}

	/**
	 * Get action scheduler name.
	 */
	public function get_action_scheduler_name() {
		return $this->action_scheduler_name;
	}

	/**
	 * Set chunked action scheduler name.
	 */
	public function set_chunked_action_scheduler_name( $chunked_action_scheduler_name ) {
		$this->chunked_action_scheduler_name = $chunked_action_scheduler_name;
	}

	/**
	 * Get chunked action scheduler name.
	 */
	public function get_chunked_action_scheduler_name() {
		return $this->chunked_action_scheduler_name;
	}

	/**
	 * Delete Progress count
	 * */
	public function delete_progress_count() {
		delete_site_option( $this->progress_bar_count );
	}

	/**
	 * Get Progress count
	 * */
	public function get_progress_count() {
		return ( int ) get_site_option( $this->progress_bar_count, 0 );
	}

	/**
	 * Update Progress count
	 * */
	public function update_progress_count( $progress = 0 ) {
		update_site_option( $this->progress_bar_count, $progress );
	}

	/*
	 * Get progress bar label.
	 */

	public function get_progress_bar_label() {
		$label = __( 'Action scheduler is under process...', 'rewardsystem' );
		return $label;
	}

	/*
	 * Display progress bar.
	 */

	public function progress_bar() {

		$action_scheduler_id = isset( $_GET[ 'rs_action_scheduler' ] ) ? wc_clean( wp_unslash( $_GET[ 'rs_action_scheduler' ] ) ) : '';
		if ( ! $action_scheduler_id ) {
			return;
		}

		$action_scheduler = RS_Action_Scheduler_Instances::get_action_scheduler_by_id( $action_scheduler_id );
		if ( ! is_object( $action_scheduler ) ) {
			return;
		}

		include_once SRP_PLUGIN_PATH . '/includes/admin/views/progressbar/html-progress-bar.php';
	}

	/**
	 * Get settings url.
	 */
	public function get_settings_url() {
		return '';
	}

	/**
	 * Get redirect url.
	 */
	public function get_redirect_url() {
		return add_query_arg( array( 'page' => 'rewardsystem_callback' ), SRP_ADMIN_URL );
	}

	/**
	 * Get success message.
	 */
	public function get_success_message() {
		return '';
	}
}
