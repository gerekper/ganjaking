<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * WooSlider Settings Class
 *
 * All functionality pertaining to the settings in WooSlider.
 *
 * @package WordPress
 * @subpackage WooSlider
 * @category Core
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - init_sections()
 * - init_fields()
 * - get_duration_options()
 */
class WooSlider_Settings extends WooSlider_Settings_API {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct () {
	    parent::__construct(); // Required in extended classes.
	    add_action( 'admin_head', array( $this, 'add_contextual_help' ) );
	} // End __construct()

	/**
	 * init_sections function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function init_sections () {

		$sections = array();

		$sections['default-settings'] = array(
					'name' 			=> __( 'General Settings', 'wooslider' ),
					'description'	=> __( 'Settings to apply to all slideshows, unless overridden.', 'wooslider' )
				);

		$sections['control-settings'] = array(
					'name' 			=> __( 'Control Settings', 'wooslider' ),
					'description'	=> __( 'Customise the ways in which slideshows can be controlled.', 'wooslider' )
				);

		$sections['button-settings'] = array(
					'name' 			=> __( 'Button Settings', 'wooslider' ),
					'description'	=> __( 'Customise the texts of the various slideshow buttons.', 'wooslider' )
				);

		$this->sections = $sections;

	} // End init_sections()

	/**
	 * init_fields function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @uses  WooSlider_Utils::get_slider_types()
	 * @return void
	 */
	public function init_fields () {
		global $pagenow;

	    $fields = array();

    	$fields['animation'] = array(
								'name' => __( 'Animation', 'wooslider' ),
								'description' => __( 'The slider animation', 'wooslider' ),
								'type' => 'select',
								'default' => 'fade',
								'section' => 'default-settings',
								'required' => 0,
								'options' => array( 'fade' => __( 'Fade', 'wooslider' ), 'slide' => __( 'Slide', 'wooslider' ) )
								);

    	$fields['direction'] = array(
								'name' => __( 'Slide Direction', 'wooslider' ),
								'description' => __( 'The direction to slide (if using the "Slide" animation)', 'wooslider' ),
								'type' => 'select',
								'default' => 'horizontal',
								'section' => 'default-settings',
								'required' => 0,
								'options' => array( 'horizontal' => __( 'Horizontal', 'wooslider' ), 'vertical' => __( 'Vertical', 'wooslider' ) )
								);

    	$fields['slideshow_speed'] = array(
								'name' => __( 'Slideshow Speed', 'wooslider' ),
								'description' => __( 'Set the delay between each slide animation (in seconds)', 'wooslider' ),
								'type' => 'range',
								'default' => '7.0',
								'section' => 'default-settings',
								'required' => 0,
								'options' => $this->get_duration_options( false )
								);

    	$fields['animation_duration'] = array(
								'name' => __( 'Animation Speed', 'wooslider' ),
								'description' => __( 'Set the duration of each slide animation (in seconds)', 'wooslider' ),
								'type' => 'range',
								'default' => '0.6',
								'section' => 'default-settings',
								'required' => 0,
								'options' => $this->get_duration_options()
								);

    	// Button Settings
    	$fields['prev_text'] = array(
								'name' => __( '"Previous" Link Text', 'wooslider' ),
								'description' => __( 'The text to display on the "Previous" button.', 'wooslider' ),
								'type' => 'text',
								'default' => __( 'Previous', 'wooslider' ),
								'section' => 'button-settings'
								);

    	$fields['next_text'] = array(
								'name' => __( '"Next" Link Text', 'wooslider' ),
								'description' => __( 'The text to display on the "Next" button.', 'wooslider' ),
								'type' => 'text',
								'default' => __( 'Next', 'wooslider' ),
								'section' => 'button-settings'
								);

    	$fields['play_text'] = array(
								'name' => __( '"Play" Button Text', 'wooslider' ),
								'description' => __( 'The text to display on the "Play" button.', 'wooslider' ),
								'type' => 'text',
								'default' => __( 'Play', 'wooslider' ),
								'section' => 'button-settings'
								);

    	$fields['pause_text'] = array(
								'name' => __( '"Pause" Button Text', 'wooslider' ),
								'description' => __( 'The text to display on the "Pause" button.', 'wooslider' ),
								'type' => 'text',
								'default' => __( 'Pause', 'wooslider' ),
								'section' => 'button-settings'
								);

    	// Control Settings
    	$fields['autoslide'] = array(
								'name' => '',
								'description' => __( 'Animate the slideshows automatically', 'wooslider' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'control-settings'
								);

    	$fields['smoothheight'] = array(
								'name' => '',
								'description' => __( 'Adjust the height of the slideshow to the height of the current slide', 'wooslider' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'control-settings'
								);

    	$fields['direction_nav'] = array(
								'name' => '',
								'description' => __( 'Display the "Previous/Next" navigation', 'wooslider' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'control-settings'
								);

    	$fields['control_nav'] = array(
								'name' => '',
								'description' => __( 'Display the slideshow pagination', 'wooslider' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'control-settings'
								);

    	$fields['keyboard_nav'] = array(
								'name' => '',
								'description' => __( 'Enable keyboard navigation', 'wooslider' ),
								'type' => 'checkbox',
								'default' => false,
								'section' => 'control-settings'
								);

    	$fields['mousewheel_nav'] = array(
								'name' => '',
								'description' => __( 'Enable the mousewheel navigation', 'wooslider' ),
								'type' => 'checkbox',
								'default' => false,
								'section' => 'control-settings'
								);

    	$fields['touch'] = array(
								'name' => '',
								'description' => __( 'Enable the touch swipe navigation on touch-screen devices', 'wooslider' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'control-settings'
								);

    	$fields['playpause'] = array(
								'name' => '',
								'description' => __( 'Enable the "Play/Pause" event', 'wooslider' ),
								'type' => 'checkbox',
								'default' => false,
								'section' => 'control-settings'
								);

    	$fields['randomize'] = array(
								'name' => '',
								'description' => __( 'Randomize the order of slides in slideshows', 'wooslider' ),
								'type' => 'checkbox',
								'default' => false,
								'section' => 'control-settings'
								);

    	$fields['animation_loop'] = array(
								'name' => '',
								'description' => __( 'Loop the slideshow animations', 'wooslider' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'control-settings'
								);

    	$fields['pause_on_action'] = array(
								'name' => '',
								'description' => __( 'Pause the slideshow autoplay when using the pagination or "Previous/Next" navigation', 'wooslider' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'control-settings'
								);

    	$fields['pause_on_hover'] = array(
								'name' => '',
								'description' => __( 'Pause the slideshow autoplay when hovering over a slide', 'wooslider' ),
								'type' => 'checkbox',
								'default' => false,
								'section' => 'control-settings'
								);

		$this->fields = $fields;

	} // End init_fields()

	/**
	 * Get options for the duration fields.
	 * @since  1.0.0
	 * @param  $include_milliseconds (default: true) Whether or not to include milliseconds between 0 and 1.
	 * @return array Options between 0.1 and 10 seconds.
	 */
	private function get_duration_options ( $include_milliseconds = true ) {
		$numbers = array( '1.0', '1.5', '2.0', '2.5', '3.0', '3.5', '4.0', '4.5', '5.0', '5.5', '6.0', '6.5', '7.0', '7.5', '8.0', '8.5', '9.0', '9.5', '10.0' );
		$options = array();

		if ( true == (bool)$include_milliseconds ) {
			$milliseconds = array( '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9' );
			foreach ( $milliseconds as $k => $v ) {
				$options[$v] = $v;
			}
		} else {
			$options['0.5'] = '0.5';
		}

		foreach ( $numbers as $k => $v ) {
			$options[$v] = $v;
		}

		return $options;
	} // End get_duration_options()

	/**
	 * Add contextual help to the settings screen.
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function add_contextual_help () {
		$current_screen = get_current_screen();
		$screens = array( 'slide_page_wooslider-settings', 'edit-slide', 'slide', 'edit-slide-page' );

		// Get out if we're not on a screen in the plugin.
		if ( ! isset( $current_screen->id ) || ! in_array( $current_screen->id, $screens ) ) { return; }

		// Settings screen help tabs.
		if ( isset( $current_screen->id ) && ( 'slide_page_wooslider-settings' == $current_screen->id ) ) {
			$current_screen->add_help_tab( array(
			'id'		=> 'overview',
			'title'		=> __( 'Overview', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'This screen contains all the default settings for your slideshows created by WooSlider (animation duration, speeds, display of slideshow controls, etc). Anything set here will apply to all WooSlider slideshows, unless overridden by a slideshow.', 'wooslider' ) . '</p>'
			) );

			$current_screen->add_help_tab( array(
			'id'		=> 'general-settings',
			'title'		=> __( 'General Settings', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'Settings to apply to all slideshows, unless overridden.', 'wooslider' ) . '</p>' .
				'<ol>' .
				'<li><strong>' . __( 'Animation', 'wooslider' ) . '</strong> - ' . __( 'The default animation to use for your slideshows ("slide" or "fade").', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Slide Direction', 'wooslider' ) . '</strong> - ' . __( 'Slide the slideshows either vertically or horizontally (works only with the "slide" animation).', 'wooslider' ) . ' <em>' . __( 'NOTE: When sliding vertically, all slides need to have the same height.', 'wooslider' ) . '</em></li>' .
				'<li><strong>' . __( 'Slideshow Speed', 'wooslider' ) . '</strong> - ' . __( 'The delay between each slide animation (in seconds).', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Animation Speed', 'wooslider' ) . '</strong> - ' . __( 'The duration of each slide animation (in seconds).', 'wooslider' ) . '</li>' .
				'</ol>'
			) );

			$current_screen->add_help_tab( array(
			'id'		=> 'control-settings',
			'title'		=> __( 'Control Settings', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'Customise the ways in which slideshows can be controlled.', 'wooslider' ) . '</p>' .
				'<ol>' .
				'<li><strong>' . __( 'Animate the slideshows automatically', 'wooslider' ) . '</strong> - ' . __( 'Whether or not to automatically animate between the slides (the alternative is to slide only when using the controls).', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Adjust the height of the slideshow to the height of the current slide', 'wooslider' ) . '</strong> - ' . __( 'Alternatively, the slideshow will take the height from it\'s tallest slide.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Display the "Previous/Next" navigation', 'wooslider' ) . '</strong> - ' . __( 'Show/hide the "Previous" and "Next" button controls.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Display the slideshow pagination', 'wooslider' ) . '</strong> - ' . __( 'Show/hide the pagination bar below the slideshow.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Enable keyboard navigation', 'wooslider' ) . '</strong> - ' . __( 'Enable navigation of this slideshow via the "left" and "right" arrow keys on the viewer\'s computer keyboard.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Enable the mousewheel navigation', 'wooslider' ) . '</strong> - ' . __( 'Enable navigation of this slideshow via the viewer\'s computer mousewheel.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Enable the "Play/Pause" event', 'wooslider' ) . '</strong> - ' . __( 'Show/hide the "Play/Pause" button below the slideshow for pausing and resuming the automated slideshow.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Randomize the order of slides in slideshows', 'wooslider' ) . '</strong> - ' . __( 'Display the slides in the slideshow in a random order.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Loop the slideshow animations', 'wooslider' ) . '</strong> - ' . __( 'When arriving at the end of the slideshow, carry on sliding from the first slide, indefinitely.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Pause the slideshow autoplay when using the pagination or "Previous/Next" navigation', 'wooslider' ) . '</strong> - ' . __( 'Pause the slideshow automation when the viewer decides to navigate using the manual controls.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Pause the slideshow autoplay when hovering over a slide', 'wooslider' ) . '</strong> - ' . __( 'Pause the slideshow automation when the viewer hovers over the slideshow.', 'wooslider' ) . '</li>' .
				'</ol>'
			) );

			$current_screen->add_help_tab( array(
			'id'		=> 'button-settings',
			'title'		=> __( 'Button Settings', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'Customise the texts of the various slideshow buttons.', 'wooslider' ) . '</p>' .
				'<ol>' .
				'<li><strong>' . __( '"Previous" Link Text', 'wooslider' ) . '</strong> - ' . __( 'The text for the "Previous" button.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( '"Next" Link Text', 'wooslider' ) . '</strong> - ' . __( 'The text for the "Next" button.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( '"Play" Button Text', 'wooslider' ) . '</strong> - ' . __( 'The text for the "Play" button.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( '"Pause" Button Text', 'wooslider' ) . '</strong> - ' . __( 'The text for the "Pause" button.', 'wooslider' ) . '</li>' .
				'</ol>'
			) );
		}

		// "Edit Slides" help tabs.
		if ( isset( $current_screen->id ) && ( 'edit-slide' == $current_screen->id ) ) {
			$current_screen->add_help_tab( array(
			'id'		=> 'overview',
			'title'		=> __( 'Overview', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'This screen provides access to all of your posts. You can customize the display of this screen to suit your workflow.', 'wooslider' ) . '</p>'
			) );

			$current_screen->add_help_tab( array(
			'id'		=> 'screen-content',
			'title'		=> __( 'Screen Content', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'You can customize the display of this screen’s contents in a number of ways:', 'wooslider' ) . '</p>' .
				'<ol>' .
				'<li>' . __( 'You can hide/display columns based on your needs and decide how many slides to list per screen using the Screen Options tab.', 'wooslider' ) . '</li>' .
				'<li>' . __( 'You can filter the list of slides by status using the text links in the upper left to show All, Published, Draft, or Trashed slides. The default view is to show all slides.', 'wooslider' ) . '</li>' .
				'<li>' . __( 'You can view slides in a simple title list or with an excerpt. Choose the view you prefer by clicking on the icons at the top of the list on the right.', 'wooslider' ) . '</li>' .
				'<li>' . __( 'You can refine the list to show only slides from a specific month by using the dropdown menus above the slides list. Click the Filter button after making your selection. You also can refine the list by clicking on the slide groups in the slides list.', 'wooslider' ) . '</li>' .
				'</ol>'
			) );

			$current_screen->add_help_tab( array(
			'id'		=> 'available-actions',
			'title'		=> __( 'Available Actions', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'Hovering over a row in the posts list will display action links that allow you to manage your post. You can perform the following actions:', 'wooslider' ) . '</p>' .
				'<ol>' .
				'<li><strong>' . __( 'Edit', 'wooslider' ) . '</strong> ' . __( 'takes you to the editing screen for that slide. You can also reach that screen by clicking on the slide title.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Quick Edit', 'wooslider' ) . '</strong> ' . __( 'provides inline access to the metadata of your slide, allowing you to update slide details without leaving this screen.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Trash', 'wooslider' ) . '</strong> ' . __( 'removes your slide from this list and places it in the trash, from which you can permanently delete it.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Preview', 'wooslider' ) . '</strong> ' . __( 'will show you what your draft slide will look like if you publish it. View will take you to your live site to view the slide. Which link is available depends on your slide\'s status.', 'wooslider' ) . '</li>' .
				'</ol>'
			) );

			$current_screen->add_help_tab( array(
			'id'		=> 'bulk-actions',
			'title'		=> __( 'Bulk Actions', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'You can also edit or move multiple slides to the trash at once. Select the slides you want to act on using the checkboxes, then select the action you want to take from the Bulk Actions menu and click Apply.', 'wooslider' ) . '</p>' .
				'<p>' . __( 'When using Bulk Edit, you can change the metadata (slide groups, etc) for all selected slides at once. To remove a slide from the grouping, just click the x next to its name in the Bulk Edit area that appears.', 'wooslider' ) . '</p>'
			) );
		}

		// "Add Slide" help tabs.
		if ( isset( $current_screen->id ) && ( 'slide' == $current_screen->id ) ) {
			$current_screen->add_help_tab( array(
			'id'		=> 'customize-display',
			'title'		=> __( 'Customize This Display', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'The title field and the big Slide Editing Area are fixed in place, but you can reposition all the other boxes using drag and drop, and can minimize or expand them by clicking the title bar of each box. Use the Screen Options tab to unhide more boxes (Excerpt, Featured Image) or to choose a 1- or 2-column layout for this screen.', 'wooslider' ) . '</p>'
			) );

			$current_screen->add_help_tab( array(
			'id'		=> 'title-content-editor',
			'title'		=> __( 'Title and Content Editor', 'wooslider' ),
			'content'	=>
				'<p><strong>' . __( 'Title', 'wooslider' ) . '</strong> - ' . __( 'Enter a title for your slide.', 'wooslider' ) . '</p>' .
				'<p><strong>' . __( 'Content Editor', 'wooslider' ) . '</strong> - ' . __( 'Enter the text for your slide. There are two modes of editing: Visual and HTML. Choose the mode by clicking on the appropriate tab. Visual mode gives you a WYSIWYG editor. Click the last icon in the row to get a second row of controls. The HTML mode allows you to enter raw HTML along with your slide text. You can insert media files by clicking the icons above the content editor and following the directions. You can go to the distraction-free writing screen via the Fullscreen icon in Visual mode (second to last in the top row) or the Fullscreen button in HTML mode (last in the row). Once there, you can make buttons visible by hovering over the top area. Exit Fullscreen back to the regular content editor. This content area is a blank canvas for each slide. Any content added here will display in the slide.	', 'wooslider' ) . '</p>'
			) );

			$current_screen->add_help_tab( array(
			'id'		=> 'publish-box',
			'title'		=> __( 'Publish Box', 'wooslider' ),
			'content'	=>
				'<p><strong>' . __( 'Publish', 'wooslider' ) . '</strong> - ' . __( 'You can set the terms of publishing your slide in the Publish box. For Status, Visibility, and Publish (immediately), click on the Edit link to reveal more options. Visibility includes options for password-protecting a post or making it stay at the front of each slideshow it is in (sticky). Publish (immediately) allows you to set a future or past date and time, so you can schedule a slide to be published in the future or backdate a slide.', 'wooslider' ) . '</p>' .
				'<p><strong>' . __( 'Featured Image', 'wooslider' ) . '</strong> - ' . __( 'This allows you to associate an image with your slide without inserting it. This is used only if the "thumbnails" pagination setting is enabled. Otherwise, the featured image is not used.', 'wooslider' ) . '</p>'
			) );
		}

		// "Edit Slide Groups" help tabs.
		if ( isset( $current_screen->id ) && ( 'edit-slide-page' == $current_screen->id ) ) {
			$current_screen->add_help_tab( array(
			'id'		=> 'overview',
			'title'		=> __( 'Overview', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'You can use slide groups to define groupings of slides to be displayed in various WooSlider slideshows.', 'wooslider' ) . '</p>'
			) );

			$current_screen->add_help_tab( array(
			'id'		=> 'adding-slide-groups',
			'title'		=> __( 'Adding Slide Groups', 'wooslider' ),
			'content'	=>
				'<p>' . __( 'When adding a new slide group on this screen, you’ll fill in the following fields:', 'wooslider' ) . '</p>' .
				'<ol>' .
				'<li><strong>' . __( 'Name', 'wooslider' ) . '</strong> - ' . __( 'The name is how it appears on your site.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Slug', 'wooslider' ) . '</strong> - ' . __( 'The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'wooslider' ) . '</li>' .
				'<li><strong>' . __( 'Description', 'wooslider' ) . '</strong> - ' . __( 'The description is not prominent by default; however, some themes may display it (WooSlider doesn\'t display this anywhere by default).', 'wooslider' ) . '</li>' .
				'</ol>'
			) );
		}

		$current_screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', 'wooslider' ) . '</strong></p>' .
		'<p><a href="http://support.woothemes.com/?ref=' . 'wooslider' . '" target="_blank">' . __( 'Support Desk', 'wooslider' ) . '</a></p>'
		);
	} // End add_contextual_help()
} // End Class
?>