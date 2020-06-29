<?php

/**
 * The main admin controller for Conditional Content.
 *
 * Handles adding the meta boxes to the wccc post type and manages the saving of data.
 */
class WC_Conditional_Content_Admin_Controller {

	private static $instance;

	/**
	 * Registers an single instance of the WC_Conditional_Content_Admin_Controller class
	 */
	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Conditional_Content_Admin_Controller();
		}
	}

	/**
	 * Returns a single instance of the WC_Conditional_Content_Admin_Controller class.
	 * @return WC_Conditional_Content_Admin_Controller
	 */
	public static function instance() {
		self::register();
		return self::$instance;
	}

	/**
	 * Registers the settings and rules metabox for the wccc post type.  Called from the metabox callback as defined in
	 * WC_Conditional_Content_Taxonomy.
	 */
	public static function add_metaboxes() {
		$instance = self::instance();
		add_meta_box( 'wccc_settings', 'Output Settings', array($instance, 'settings_metabox'), 'wccc', 'side', 'low' );
		add_meta_box( 'wccc_rules', 'Rules', array($instance, 'rules_metabox'), 'wccc', 'normal', 'high' );
	}

	/**
	 * Creates a new instance of the WC_Conditional_Content_Admin_Controller class
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array($this, 'on_enqueue_scripts'), 100 );

		//Save Data
		add_action( 'save_post', array($this, 'save_data'), 10, 2 );


		//Hook up the ajax actions
		add_action( 'wp_ajax_wccc_change_rule_type', array($this, 'ajax_render_rule_choice') );
		add_action( 'wp_ajax_wccc_json_search', array($this, 'ajax_json_search') );
	}

	/*
	 * Load the required scripts and style sheets on the wccc post type admin screens.
	 */

	public function on_enqueue_scripts( $handle ) {
		global $post_type, $woocommerce;

		if ( ($handle == 'post-new.php' || $handle == 'post.php' || $handle == 'edit.php') && $post_type == 'wccc' ) {
			//Styles
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
			wp_enqueue_style( 'wccc-admin-app', WC_Conditional_Content::plugin_url() . '/assets/admin/css/wccc-admin-app.css' );
			
			//Chosen
			if ( WC_Conditional_Content_Compatibility::is_wc_version_gte_2_6() ) {
				wp_enqueue_style( 'chosen', WC_Conditional_Content::plugin_url() . '/assets/css/chosen.css' );
				
				wp_register_script( 'chosen', WC_Conditional_Content::plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', array('jquery'), WC_VERSION );
				wp_register_script( 'ajax-chosen', WC_Conditional_Content::plugin_url() . '/assets/js/chosen/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'chosen'), WC_VERSION );
				
			}
			
			wp_enqueue_script('ajax-chosen');
			
			wp_enqueue_script( 'wccc-admin-app', WC_Conditional_Content::plugin_url() . '/assets/admin/js/wccc-admin-app.js', array('jquery', 'jquery-ui-datepicker', 'underscore', 'backbone', 'ajax-chosen') );

			$data = array(
			    'ajax_nonce' => wp_create_nonce( 'wcccaction-admin' ),
			    'plugin_url' => WC_Conditional_Content::plugin_url(),
			    'ajax_url' => admin_url( 'admin-ajax.php' ),
			    'ajax_chosen' => wp_create_nonce( 'json-search' ),
			    'search_products_nonce' => wp_create_nonce( 'search-products' ),
			    'text_or' => __( 'or', 'wc_conditional_content' ),
			    'text_apply_when' => __( 'Apply this content when these conditions are matched', 'wc_conditional_content' ),
			    'remove_text' => __( 'test', 'wc_conditional_content' )
			);

			wp_localize_script( 'wccc-admin-app', 'WCCCParams', $data );
		}
	}

	/**
	 * Renders the rules metabox.
	 */
	public function rules_metabox() {
		include 'views/metabox-rules.php';
	}

	/**
	 * Renders the settings metabox.
	 */
	public function settings_metabox() {
		include 'views/metabox-settings.php';
	}

	/**
	 * Saves the data for the wccc post type.
	 * @param int $post_id Post ID
	 * @param WP_Post Post Object
	 * @return null
	 */
	public function save_data( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) )
			return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
		if ( is_int( wp_is_post_revision( $post ) ) )
			return;
		if ( is_int( wp_is_post_autosave( $post ) ) )
			return;
		if ( $post->post_type != 'wccc' )
			return;

		if ( !WC_Conditional_Content::verify_nonce( 'admin' ) ) {
			return;
		}

		if ( isset( $_POST['wccc_settings_location'] ) ) {
			$location = explode( ':', $_POST['wccc_settings_location'] );
			$settings = array('location' => $location[0], 'hook' => $location[1]);

			if ( $settings['hook'] == 'custom' ) {
				$settings['custom_hook'] = $_POST['wccc_settings_location_custom_hook'];
				$settings['custom_priority'] = $_POST['wccc_settings_location_custom_priority'];
			} else {
				$settings['custom_hook'] = '';
				$settings['custom_priority'] = '';
			}

			$settings['type'] = $_POST['wccc_settings_type'];

			update_post_meta( $post_id, '_wccc_settings', $settings );
		}

		if ( isset( $_POST['wccc_rule'] ) ) {
			update_post_meta( $post_id, 'wccc_rule', $_POST['wccc_rule'] );
		} else {
			delete_post_meta( $post_id, 'wccc_rule' );
		}
	}

	/**
	 * Ajax and PHP Rendering Functions for Options.
	 *
	 * Renders the correct Operator and Values controls.
	 */
	public function ajax_render_rule_choice( $options ) {
		// defaults
		$defaults = array(
		    'group_id' => 0,
		    'rule_id' => 0,
		    'rule_type' => null,
		    'condition' => null,
		    'operator' => null,
		);

		$is_ajax = false;
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'wccc_change_rule_type' ) {
			$is_ajax = true;
		}

		if ( $is_ajax ) {

			if ( !check_ajax_referer( 'wcccaction-admin', 'security' ) ) {
				die();
			}

			$options = array_merge( $defaults, $_POST );
		} else {
			$options = array_merge( $defaults, $options );
		}

		$rule_object = woocommerce_conditional_content_get_rule_object( $options['rule_type'] );
		if ( !empty( $rule_object ) ) {
			$values = $rule_object->get_possibile_rule_values();
			$operators = $rule_object->get_possibile_rule_operators();
			$condition_input_type = $rule_object->get_condition_input_type();

			// create operators field
			$operator_args = array(
			    'input' => 'select',
			    'name' => 'wccc_rule[' . $options['group_id'] . '][' . $options['rule_id'] . '][operator]',
			    'choices' => $operators,
			);

			echo '<td class="operator">';
			if ( !empty( $operators ) ) {
				WC_Conditional_Content_Input_Builder::create_input_field( $operator_args, $options['operator'] );
			} else {
				echo '<input type="hidden" name="' . $operator_args['name'] . '" value="==" />';
			}
			echo '</td>';

			// create values field
			$value_args = array(
			    'input' => $condition_input_type,
			    'name' => 'wccc_rule[' . $options['group_id'] . '][' . $options['rule_id'] . '][condition]',
			    'choices' => $values,
			);

			echo '<td class="condition">';
			WC_Conditional_Content_Input_Builder::create_input_field( $value_args, $options['condition'] );
			echo '</td>';
		}

		// ajax?
		if ( $is_ajax ) {
			die();
		}
	}

	/**
	 * Ajax callback to perform searches for Chosen Select Ajax style controls.
	 */
	public function ajax_json_search() {
		check_ajax_referer( 'json-search', 'security' );

		header( 'Content-Type: application/json; charset=utf-8' );

		$method = (string) urldecode( stripslashes( strip_tags( $_GET['method'] ) ) );
		$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
		if ( empty( $term ) )
			die();

		switch ( $method ) {
			case 'roles' :

				$roles = get_editable_roles();
				if ( $roles && $role ) {
					
				}

				break;
			case 'users' :
				break;
		}

		die();
	}

	/**
	 * Called from the metabox_settings.php screen.  Renders the template for a rule group that has already been saved.
	 * @param array $options The group config options to render the template with.
	 */
	public function render_rule_choice_template( $options ) {
		// defaults
		$defaults = array(
		    'group_id' => 0,
		    'rule_id' => 0,
		    'rule_type' => null,
		    'condition' => null,
		    'operator' => null,
		);


		$options = array_merge( $defaults, $options );
		$rule_object = woocommerce_conditional_content_get_rule_object( $options['rule_type'] );

		$values = $rule_object->get_possibile_rule_values();
		$operators = $rule_object->get_possibile_rule_operators();
		$condition_input_type = $rule_object->get_condition_input_type();

		// create operators field
		$operator_args = array(
		    'input' => 'select',
		    'name' => 'wccc_rule[<%= groupId %>][<%= ruleId %>][operator]',
		    'choices' => $operators,
		);

		echo '<td class="operator">';
		if ( !empty( $operators ) ) {
			WC_Conditional_Content_Input_Builder::create_input_field( $operator_args, $options['operator'] );
		} else {
			echo '<input type="hidden" name="' . $operator_args['name'] . '" value="==" />';
		}
		echo '</td>';

		// create values field
		$value_args = array(
		    'input' => $condition_input_type,
		    'name' => 'wccc_rule[<%= groupId %>][<%= ruleId %>][condition]',
		    'choices' => $values,
		);

		echo '<td class="condition">';
		WC_Conditional_Content_Input_Builder::create_input_field( $value_args, $options['condition'] );
		echo '</td>';
	}

}
