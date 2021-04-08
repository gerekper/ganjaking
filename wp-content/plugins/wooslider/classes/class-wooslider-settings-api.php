<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooSlider Settings API Class
 *
 * A settings API (wrapping the WordPress Settings API).
 *
 * @package WordPress
 * @subpackage WooSlider
 * @category Settings
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * var $token
 * var $page_slug
 * var $name
 * var $menu_label
 * var $settings
 * var $sections
 * var $fields
 * var $errors
 * var $settings_version
 *
 * public $has_range
 * public $has_imageselector
 *
 * - __construct()
 * - setup_settings()
 * - init_sections()
 * - init_fields()
 * - create_sections()
 * - create_fields()
 * - determine_method()
 * - parse_fields()
 * - register_settings_screen()
 * - settings_screen()
 * - get_settings()
 * - settings_fields()
 * - settings_errors()
 * - settings_description()
 * - form_field_text()
 * - form_field_checkbox()
 * - form_field_textarea()
 * - form_field_select()
 * - form_field_radio()
 * - form_field_multicheck()
 * - form_field_range()
 * - form_field_images()
 * - form_field_info()
 * - validate_fields()
 * - validate_field_text()
 * - validate_field_checkbox()
 * - validate_field_multicheck()
 * - validate_field_range()
 * - validate_field_url()
 * - check_field_timestamp()
 * - check_field_text()
 * - add_error()
 * - parse_errors()
 * - get_array_field_types()
 * - enqueue_scripts()
 * - enqueue_styles()
 */
class WooSlider_Settings_API {
	public $token;
	public $page_slug;
	public $name;
	public $menu_label;
	public $settings;
	public $sections;
	public $fields;
	public $errors;

	public $has_range;
	public $has_imageselector;
	public $has_tabs;
	private $tabs;
	public $settings_version;
	public $priority;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct () {
		$this->token = 'wooslider';
		$this->page_slug = 'wooslider-settings-api';

		$this->sections = array();
		$this->fields = array();
		$this->remaining_fields = array();
		$this->errors = array();

		$this->has_range = false;
		$this->has_imageselector = false;
		$this->has_tabs = false;
		$this->tabs = array();
		$this->settings_version = '';
		$this->priority = 99;
	} // End __construct()

	/**
	 * setup_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function setup_settings () {
		add_action( 'admin_menu', array( $this, 'register_settings_screen' ), intval( $this->priority ) );
		add_action( 'admin_init', array( $this, 'settings_fields' ) );
		add_action( 'wooslider_settings_tabs_general', array( $this, 'default_settings_screen_markup' ) );

		$this->init_sections();
		$this->init_fields();
		$this->get_settings();
		if ( $this->has_tabs == true ) {
			$this->create_tabs();
		}
	} // End setup_settings()

	/**
	 * init_sections function.
	 *
	 * @access public
	 * @return void
	 */
	public function init_sections () {
		// Override this function in your class and assign the array of sections to $this->sections.
		_e( 'Override init_sections() in your class.', 'wooslider' );
	} // End init_sections()

	/**
	 * init_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function init_fields () {
		// Override this function in your class and assign the array of sections to $this->fields.
		_e( 'Override init_fields() in your class.', 'wooslider' );
	} // End init_fields()

	/**
	 * settings_tabs function.
	 *
	 * @access public
	 * @since  2.0.0
	 * @return void
	 */
	public function settings_tabs () {
		if ( ! $this->has_tabs ) { return; }

		if ( count( $this->tabs ) > 0 ) {
			$html = '';

			$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";

			$sections = array(
						'all' => array( 'href' => '#all', 'name' => __( 'All', 'wooslider' ), 'class' => 'current all tab' )
					);

			foreach ( $this->tabs as $k => $v ) {
				$sections[$k] = array( 'href' => '#' . esc_attr( $k ), 'name' => esc_attr( $v['name'] ), 'class' => 'tab' );
			}

			$count = 1;
			foreach ( $sections as $k => $v ) {
				$count++;
				$html .= '<li><a href="' . $v['href'] . '"';
				if ( isset( $v['class'] ) && ( $v['class'] != '' ) ) { $html .= ' class="' . esc_attr( $v['class'] ) . '"'; }
				$html .= '>' . esc_attr( $v['name'] ) . '</a>';
				if ( $count <= count( $sections ) ) { $html .= ' | '; }
				$html .= '</li>' . "\n";
			}

			$html .= '</ul><div class="clear"></div>' . "\n";

			echo $html;
		}
	} // End settings_tabs()

	/**
	 * create_tabs function.
	 *
	 * @access private
	 * @since  2.0.0
	 * @return void
	 */
	private function create_tabs () {
		if ( count( $this->sections ) > 0 ) {
			$tabs = array();
			foreach ( $this->sections as $k => $v ) {
				$tabs[$k] = $v;
			}

			$this->tabs = $tabs;
		}
	} // End create_tabs()

	/**
	 * create_sections function.
	 *
	 * @access public
	 * @return void
	 */
	public function create_sections () {
		if ( count( $this->sections ) > 0 ) {
			foreach ( $this->sections as $k => $v ) {
				add_settings_section( $k, $v['name'], array( $this, 'section_description' ), $this->token );
			}
		}
	} // End create_sections()

	/**
	 * create_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function create_fields () {
		if ( count( $this->sections ) > 0 ) {
			// $this->parse_fields( $this->fields );

			foreach ( $this->fields as $k => $v ) {
				$method = $this->determine_method( $v, 'form' );
				$name = $v['name'];
				if ( $v['type'] == 'info' ) { $name = ''; }
				add_settings_field( $k, $name, $method, $this->token, $v['section'], array( 'key' => $k, 'data' => $v ) );

				// Let the API know that we have a colourpicker field.
				if ( $v['type'] == 'range' && $this->has_range == false ) { $this->has_range = true; }
			}
		}
	} // End create_fields()

	/**
	 * determine_method function.
	 *
	 * @access protected
	 * @param array $data
	 * @return array or string
	 */
	protected function determine_method ( $data, $type = 'form' ) {
		$method = '';

		if ( ! in_array( $type, array( 'form', 'validate', 'check' ) ) ) { return; }

		// Check for custom functions.
		if ( isset( $data[$type] ) ) {
			if ( function_exists( $data[$type] ) ) {
				$method = $data[$type];
			}

			if ( $method == '' && method_exists( $this, $data[$type] ) ) {
				if ( $type == 'form' ) {
					$method = array( $this, $data[$type] );
				} else {
					$method = $data[$type];
				}
			}
		}

		if ( $method == '' && method_exists ( $this, $type . '_field_' . $data['type'] ) ) {
			if ( $type == 'form' ) {
				$method = array( $this, $type . '_field_' . $data['type'] );
			} else {
				$method = $type . '_field_' . $data['type'];
			}
		}

		if ( $method == '' && function_exists ( $this->token . '_' . $type . '_field_' . $data['type'] ) ) {
			$method = $this->token . '_' . $type . '_field_' . $data['type'];
		}

		if ( $method == '' ) {
			if ( $type == 'form' ) {
				$method = array( $this, $type . '_field_text' );
			} else {
				$method = $type . '_field_text';
			}
		}

		return $method;
	} // End determine_method()

	/**
	 * parse_fields function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $fields
	 * @return void
	 */
	public function parse_fields ( $fields ) {
		foreach ( $fields as $k => $v ) {
			if ( isset( $v['section'] ) && ( $v['section'] != '' ) && ( isset( $this->sections[$v['section']] ) ) ) {
				if ( ! isset( $this->sections[$v['section']]['fields'] ) ) {
					$this->sections[$v['section']]['fields'] = array();
				}

				$this->sections[$v['section']]['fields'][$k] = $v;
			} else {
				$this->remaining_fields[$k] = $v;
			}
		}
	} // End parse_fields()

	/**
	 * register_settings_screen function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function register_settings_screen () {
		global $wooslider;

		$hook = add_submenu_page( 'edit.php?post_type=slide', $this->name, $this->menu_label, 'manage_options', $this->page_slug, array( $this, 'settings_screen' ) );

		$this->hook = $hook;

		if ( isset( $_GET['page'] ) && ( $_GET['page'] == $this->page_slug ) ) {
			add_action( 'admin_notices', array( $this, 'settings_errors' ) );
			add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'enqueue_styles' ) );
		}
	} // End register_settings_screen()

	/**
	 * settings_screen function.
	 *
	 * @access public
	 * @return void
	 */
	public function settings_screen () {
		$this->settings_screen_header();
		$current_tab = $this->_get_current_tab();
?>
	<form action="options.php" method="post">
		<?php do_action( 'wooslider_settings_tabs_' . $current_tab ); ?>
	</form>
<?php
		$this->settings_screen_footer();
	} // End settings_screen()

	/**
	 * Display the default settings screen logic.
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function default_settings_screen_markup () {
		$this->settings_tabs();
		settings_fields( $this->token );
		do_settings_sections( $this->token );
		submit_button();
	} // End default_settings_screen_markup()

	/**
	 * The header markup for the settings screens.
	 * @access  protected
	 * @since   2.0.0
	 * @return  void
	 */
	protected function settings_screen_header () {
		global $wooslider;
		$tabs = $this->_get_settings_tabs();
		$current_tab = $this->_get_current_tab();
?>
<div id="wooslider" class="wrap <?php echo esc_attr( $this->token ); ?>">
	<?php screen_icon( 'wooslider' ); ?>
	<h2 class="nav-tab-wrapper">
	<?php
	echo $this->get_settings_tabs_html( $tabs, $current_tab );
	do_action( 'wooslider_settings_tabs' );
	?>
	<?php if ( '' != $this->settings_version ) { echo ' <span class="version">' . $this->settings_version . '</span>'; } ?>
	<span class="powered-by-woo"><?php _e( 'Powered by', 'wooslider' ); ?><a href="http://www.woothemes.com/" title="WooThemes"><img src="<?php echo $wooslider->plugin_url; ?>assets/images/woothemes.png" alt="WooThemes" /></a></span>
	</h2>
<?php
		do_action( 'wooslider_settings_screen_header' );
	} // End settings_screen_header()

	/**
	 * The footer markup for the settings screens.
	 * @access  protected
	 * @since   2.0.0
	 * @return  void
	 */
	protected function settings_screen_footer () {
?>
	</div><!--/#wooslider-->
<?php
	} // End settings_screen_footer()

	/**
	 * Generate an array of admin section tabs.
	 * @access  private
	 * @since   1.0.0
	 * @return  array Tab data with key, and a value of array( 'name', 'callback' )
	 */
	private function _get_settings_tabs () {
		$tabs = array(
				'general' => __( 'General', 'wooslider' )
				);
		return (array)apply_filters( 'wooslider_get_settings_tabs', $tabs );
	} // End _get_settings_tabs()

	/**
	 * Generate HTML markup for the section tabs.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $tabs An array of tabs.
	 * @param   string $current_tab The key of the current tab.
	 * @return  string HTML markup for the settings tabs.
	 */
	public function get_settings_tabs_html ( $tabs = false, $current_tab = false ) {
		if ( ! is_array( $tabs ) ) $tabs = $this->_get_settings_tabs(); // Fail-safe, in case we don't pass tab data.
		if ( false == $current_tab ) $current_tab = $this->_get_current_tab();

		$html = '';
		if ( 0 < count( $tabs ) ) {
			foreach ( $tabs as $k => $v ) {
				$class = 'nav-tab';
				if ( $current_tab == $k ) {
					$class .= ' nav-tab-active';
				}
				$url = add_query_arg( 'tab', $k, add_query_arg( 'page', $this->token, admin_url( 'options-general.php' ) ) );
				$html .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $v ) . '</a>';
			}
		}
		return $html;
	} // End get_settings_tabs_html()

	/**
	 * Get the current selected tab key.
	 * @access  private
	 * @since   1.0.0
	 * @param   array $tabs Available tabs.
	 * @return  string Current tab's key, or a default value.
	 */
	private function _get_current_tab ( $tabs = false ) {
		if ( ! is_array( $tabs ) ) $tabs = $this->_get_settings_tabs(); // Fail-safe, in case we don't pass tab data.
		if ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], array_keys( $tabs ) ) )
			$current_tab = esc_attr( $_GET['tab'] );
		else
			$current_tab = 'general';

		return $current_tab;
	} // End _get_current_tab()

	/**
	 * get_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_settings () {
		if ( ! is_array( $this->settings ) ) {
			$this->settings = get_option( $this->token, array() );
		}

		foreach ( $this->fields as $k => $v ) {
			if ( ! isset( $this->settings[$k] ) && isset( $v['default'] ) ) {
				$this->settings[$k] = $v['default'];
			}
			if ( $v['type'] == 'checkbox' && $this->settings[$k] != true ) {
				$this->settings[$k] = 0;
			}
		}

		return $this->settings;
	} // End get_settings()

	/**
	 * settings_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function settings_fields () {
		register_setting( $this->token, $this->token, array( $this, 'validate_fields' ) );
		$this->create_sections();
		$this->create_fields();
	} // End settings_fields()

	/**
	 * settings_errors function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_errors () {
		echo settings_errors( $this->token . '-errors' );
	} // End settings_errors()

	/**
	 * section_description function.
	 *
	 * @access public
	 * @return void
	 */
	public function section_description ( $section ) {
		if ( isset( $this->sections[$section['id']]['description'] ) ) {
			echo wpautop( esc_html( $this->sections[$section['id']]['description'] ) );
		}
	} // End section_description_main()

	/**
	 * form_field_text function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_text ( $args ) {
		$options = $this->get_settings();

		echo '<input id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" size="40" type="text" value="' . esc_attr( $options[$args['key']] ) . '" />' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<span class="description">' . esc_html( $args['data']['description'] ) . '</span>' . "\n";
		}
	} // End form_field_text()

	/**
	 * form_field_checkbox function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_checkbox ( $args ) {
		$options = $this->get_settings();

		$has_description = false;
		if ( isset( $args['data']['description'] ) ) {
			$has_description = true;
			echo '<label for="' . $this->token . '[' . esc_attr( $args['key'] ) . ']">' . "\n";
		}
		echo '<input id="' . $args['key'] . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" type="checkbox" value="1"' . checked( esc_attr( $options[$args['key']] ), '1', false ) . ' />' . "\n";
		if ( $has_description ) {
			echo esc_html( $args['data']['description'] ) . '</label>' . "\n";
		}
	} // End form_field_text()

	/**
	 * form_field_textarea function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_textarea ( $args ) {
		$options = $this->get_settings();

		echo '<textarea id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" cols="42" rows="5">' . esc_html( $options[$args['key']] ) . '</textarea>' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p><span class="description">' . esc_html( $args['data']['description'] ) . '</span></p>' . "\n";
		}
	} // End form_field_textarea()

	/**
	 * form_field_select function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_select ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			$html .= '<select id="' . esc_attr( $args['key'] ) . '" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . ']">' . "\n";
				foreach ( $args['data']['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $options[$args['key']] ), $k, false ) . '>' . $v . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<p><span class="description">' . esc_html( $args['data']['description'] ) . '</span></p>' . "\n";
			}
		}
	} // End form_field_select()

	/**
	 * form_field_radio function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_radio ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			foreach ( $args['data']['options'] as $k => $v ) {
				$html .= '<input type="radio" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $options[$args['key']] ), $k, false ) . ' /> ' . $v . '<br />' . "\n";
			}
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<span class="description">' . esc_html( $args['data']['description'] ) . '</span>' . "\n";
			}
		}
	} // End form_field_radio()

	/**
	 * form_field_multicheck function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_multicheck ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '<div class="multicheck-container" style="height: 100px; overflow-y: auto;">' . "\n";
			foreach ( $args['data']['options'] as $k => $v ) {
				$checked = '';

				if ( in_array( $k, (array)$options[$args['key']] ) ) { $checked = ' checked="checked"'; }
				$html .= '<input type="checkbox" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . '][]" class="multicheck multicheck-' . esc_attr( $args['key'] ) . '" value="' . esc_attr( $k ) . '"' . $checked . ' /> ' . $v . '<br />' . "\n";
			}
			$html .= '</div>' . "\n";
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<span class="description">' . esc_html( $args['data']['description'] ) . '</span>' . "\n";
			}
		}
	} // End form_field_multicheck()

	/**
	 * form_field_range function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_range ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			$html .= '<select id="' . esc_attr( $args['key'] ) . '" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . ']" class="range-input">' . "\n";
				foreach ( $args['data']['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $options[$args['key']] ), $k, false ) . '>' . $v . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<p><span class="description">' . esc_html( $args['data']['description'] ) . '</span></p>' . "\n";
			}
		}
	} // End form_field_range()

	/**
	 * form_field_images function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_images ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			foreach ( $args['data']['options'] as $k => $v ) {
				$html .= '<input type="radio" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . ']" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $options[$args['key']] ), $k, false ) . ' /> ' . $v . '<br />' . "\n";
			}
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<span class="description">' . esc_html( $args['data']['description'] ) . '</span>' . "\n";
			}
		}
	} // End form_field_images()

	/**
	 * form_field_info function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_info ( $args ) {
		$class = '';
		if ( isset( $args['data']['class'] ) ) {
			$class = ' ' . esc_attr( $args['data']['class'] );
		}
		$html = '<div id="' . $args['key'] . '" class="info-box' . $class . '">' . "\n";
		if ( isset( $args['data']['name'] ) && ( $args['data']['name'] != '' ) ) {
			$html .= '<h3 class="title">' . esc_html( $args['data']['name'] ) . '</h3>' . "\n";
		}
		if ( isset( $args['data']['description'] ) && ( $args['data']['description'] != '' ) ) {
			$html .= '<p>' . esc_html( $args['data']['description'] ) . '</p>' . "\n";
		}
		$html .= '</div>' . "\n";

		echo $html;
	} // End form_field_info()

	/**
	 * validate_fields function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $input
	 * @uses $this->parse_errors()
	 * @return array $options
	 */
	public function validate_fields ( $input ) {
		$options = $this->get_settings();

		foreach ( $this->fields as $k => $v ) {
			// Make sure checkboxes are present even when false.
			if ( $v['type'] == 'checkbox' && ! isset( $input[$k] ) ) { $input[$k] = false; }

			if ( isset( $input[$k] ) ) {
				// Perform checks on required fields.
				if ( isset( $v['required'] ) && ( $v['required'] == true ) ) {
					if ( in_array( $v['type'], $this->get_array_field_types() ) && ( count( (array) $input[$k] ) <= 0 ) ) {
						$this->add_error( $k, $v );
						continue;
					} else {
						if ( $input[$k] == '' ) {
							$this->add_error( $k, $v );
							continue;
						}
					}
				}

				$value = $input[$k];

				// Check if the field is valid.
				$method = $this->determine_method( $v, 'check' );

				if ( function_exists ( $method ) ) {
					$is_valid = $method( $value );
				} else {
					if ( method_exists( $this, $method ) ) {
						$is_valid = $this->$method( $value );
					}
				}

				if ( ! $is_valid ) {
					$this->add_error( $k, $v );
					continue;
				}

				$method = $this->determine_method( $v, 'validate' );

				if ( function_exists ( $method ) ) {
					$options[$k] = $method( $value );
				} else {
					if ( method_exists( $this, $method ) ) {
						$options[$k] = $this->$method( $value );
					}
				}
			}
		}

		// Parse error messages into the Settings API.
		$this->parse_errors();
		return $options;
	} // End validate_fields()

	/**
	 * validate_field_text function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_text ( $input ) {
		return trim( esc_attr( $input ) );
	} // End validate_field_text()

	/**
	 * validate_field_checkbox function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_checkbox ( $input ) {
		if ( ! isset( $input ) ) {
			return 0;
		} else {
			return (bool)$input;
		}
	} // End validate_field_checkbox()

	/**
	 * validate_field_multicheck function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_multicheck ( $input ) {
		$input = (array) $input;

		$input = array_map( 'esc_attr', $input );

		return $input;
	} // End validate_field_multicheck()

	/**
	 * validate_field_range function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_range ( $input ) {
		$input = number_format( floatval( $input ), 1 );

		return $input;
	} // End validate_field_range()

	/**
	 * validate_field_url function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_url ( $input ) {
		return trim( esc_url( $input ) );
	} // End validate_field_url()

	/**
	 * check_field_text function.
	 * @param  string $input String of the value to be validated.
	 * @since  2.0.0
	 * @return boolean Is the value valid?
	 */
	public function check_field_text ( $input ) {
		$is_valid = true;

		return $is_valid;
	} // End check_field_text()

	/**
	 * add_error function.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @param string $key
	 * @param array $data
	 * @return void
	 */
	protected function add_error ( $key, $data ) {
		if ( isset( $data['error_message'] ) ) {
			$message = $data['error_message'];
		} else {
			$message = sprintf( __( '%s is a required field', 'wooslider' ), $data['name'] );
		}
		$this->errors[$key] = $message;
	} // End add_error()

	protected function parse_errors () {
		if ( count ( $this->errors ) > 0 ) {
			foreach ( $this->errors as $k => $v ) {
				add_settings_error( $this->token . '-errors', $k, $v, 'error' );
			}
		} else {
			$message = sprintf( __( '%s updated', 'wooslider' ), $this->name );
			add_settings_error( $this->token . '-errors', $this->token, $message, 'updated' );
		}
	} // End parse_errors()

	/**
	 * get_array_field_types function.
	 *
	 * @description Return an array of field types expecting an array value returned.
	 * @access protected
	 * @since 1.0.0
	 * @return void
	 */
	protected function get_array_field_types () {
		return array( 'multicheck' );
	} // End get_array_field_types()

	/**
	 * enqueue_scripts function.
	 *
	 * @description Load in JavaScripts where necessary.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {
		global $wooslider;
		if ( $this->has_range ) {
			wp_enqueue_script( 'wooslider-settings-ranges', $wooslider->plugin_url . 'assets/js/ranges.js', array( 'jquery-ui-slider' ), '1.0.0' );
		}

		wp_register_script( 'wooslider-settings-imageselectors', $wooslider->plugin_url . 'assets/js/image-selectors.js', array( 'jquery' ), '1.0.0' );

		if ( $this->has_imageselector ) {
			wp_enqueue_script( 'wooslider-settings-imageselectors' );
		}

		if ( $this->has_tabs ) {
			wp_enqueue_script( 'wooslider-settings-tabs-navigation', $wooslider->plugin_url . 'assets/js/tabs-navigation.js', array( 'jquery' ), '1.0.0' );
		}
	} // End enqueue_scripts()

	/**
	 * enqueue_styles function.
	 *
	 * @description Load in CSS styles where necessary.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		global $wooslider;
		wp_enqueue_style( $wooslider->token . '-admin' );

		wp_enqueue_style( 'wooslider-settings-api', $wooslider->plugin_url . 'assets/css/settings.css', '', '1.0.0' );

		$this->enqueue_field_styles();
	} // End enqueue_styles()

	/**
	 * enqueue_field_styles function.
	 *
	 * @description Load in CSS styles where necessary.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_field_styles () {
		global $wooslider;

		if ( $this->has_range ) {
			wp_enqueue_style( 'wooslider-settings-ranges', $wooslider->plugin_url . 'assets/css/ranges.css', '', '1.0.0' );
		}

		wp_register_style( 'wooslider-settings-imageselectors', $wooslider->plugin_url . 'assets/css/image-selectors.css', '', '1.0.0' );

		if ( $this->has_imageselector ) {
			wp_enqueue_style( 'wooslider-settings-imageselectors' );
		}
	} // End enqueue_field_styles()
} // End Class
?>