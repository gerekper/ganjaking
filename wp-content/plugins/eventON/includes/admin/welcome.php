<?php
/**
 * Welcome Page Class
 *
 * Shows a feature overview for the new version (major).
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin
 * @version     1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class EVO_Welcome_Page {

	/**
	 * Get things started
	 */
	public function __construct() {		
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Hide individual dashboard pages
	 * @return void 
	 */
	public function admin_menus() {
		$welcome_page_title = __( 'Welcome to EventON', 'eventon' );
		$about = add_dashboard_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'evo-about', array( $this, 'about_screen' ) );
		
		add_action( 'admin_print_styles-'. $about, array( $this, 'admin_css' ) );

		// Getting Started Page
		add_dashboard_page(
			__( 'Getting started with EventON Calendar', 'eventon' ),
			__( 'Getting started with EventON Calendar', 'eventon' ),
			'manage_options',
			'evo-getting-started',
			array( $this, 'getting_started_screen' )
		);

		// Changelog Page
		add_dashboard_page(
			__( 'EventON Changelog', 'eventon' ),
			__( 'EventON Changelog', 'eventon' ),
			'manage_options',
			'evo-changelog',
			array( $this, 'changelog_screen' )
		);
	}

	/**
	 * CSS styles for the about page
	 * @return void 
	 */
	public function admin_css() {
		wp_enqueue_style( 'eventon-activation', AJDE_EVCAL_URL.'/assets/css/admin/activation.css' );
	}
	
	/**
	 * Hide individual dashboard pages
	 * @return void 
	 */
	public function admin_head() {
		global $eventon;

		remove_submenu_page( 'index.php', 'evo-about' );		
		remove_submenu_page( 'index.php', 'evo-getting-started' );
		remove_submenu_page( 'index.php', 'evo-changelog' );

		$badge_url = AJDE_EVCAL_URL . '/assets/images/welcome/evo-badge.png';		
		?>
		<style type="text/css" media='screen'>
		/*<![CDATA[*/
		.evo-badge {
			padding-top: 150px;
			height: 52px;
			width: 185px;
			color: #666;
			font-weight: bold;
			font-size: 14px;
			text-align: center;
			text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
			margin: 0 -5px;
			background: url('<?php echo $badge_url; ?>') no-repeat;
		}

		.about-wrap .evo-badge {
			position: absolute;
			top: 0;
			right: 0;
		}

		.evo-welcome-screenshots {
			float: right;
			margin-left: 10px!important;
		}

		.about-wrap .feature-section {
			margin-top: 20px;
		}

		/*]]>*/
			
		</style>
		<?php
	}
	
	// TABS for the welcome screen
		public function tabs(){
			$selected = isset( $_GET['page'] ) ? sanitize_text_field($_GET['page']) : 'evo-about';
			?>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php echo $selected == 'evo-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'evo-about' ), 'index.php' ) ) ); ?>">
					<?php _e( "Welcome", 'eventon' ); ?>
				</a>
				<a class="nav-tab <?php echo $selected == 'evo-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'evo-getting-started' ), 'index.php' ) ) ); ?>">
					<?php _e( 'Getting Started', 'eventon' ); ?>
				</a>
				<a class="nav-tab <?php echo $selected == 'evo-changelog' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'evo-changelog' ), 'index.php' ) ) ); ?>">
					<?php _e( 'Changelog', 'eventon' ); ?>
				</a>				
			</h2>
			<?php
		}
	/**
	 * Render Changelog Screen
	 *
	 * @access public
	 * @since 2.2.22
	 * @return void
	 */
	public function changelog_screen() {
		$display_version = EVO_VERSION;
		?>
		<div class="wrap about-wrap">
			<h1><?php _e( 'EventON Calendar Changelog', 'eventon' ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! EventON Calendar %s is a stylish minimal calendar that will help you stay on top!', 'eventon' ), $display_version ); ?></div>
			<div class="evo-badge"><?php printf( __( 'Version %s', 'eventon' ), $display_version ); ?></div>

			<?php $this->tabs(); ?>

			<div class="changelog">
				<h3><?php _e( 'Full Changelog', 'eventon' );?></h3>

				<div class="feature-section">
					<?php echo $this->parse_readme(); ?>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url('admin.php?page=eventon' ) ); ?>"><?php _e( 'Go to EventON Settings', 'eventon' ); ?></a>
			</div>
		</div>
		<?php
	}
	/**
	 * Parse the Eventon readme.txt file
	 *
	 * @since 2.2.22
	 * @return string $readme HTML formatted readme file
	 */
	public function parse_readme() {
		$file = file_exists( AJDE_EVCAL_PATH . '/__README.txt' ) ? AJDE_EVCAL_PATH . '/__README.txt' : null;

		if ( ! $file ) {
			$readme = '<p>' . __( 'No valid changlog was found.', 'edd' ) . '</p>';
		} else {
			$readme = file_get_contents( $file );
			$readme = nl2br( esc_html( $readme ) );
			$readme = explode( '== Changelog ==', $readme );
			$readme = end( $readme );

			$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
			$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
			$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
			$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
			$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
		}

		return $readme;
	}	


	// Into text/links shown on all about pages.
		private function intro() {
			global $eventon;			
		?>
			<p class="eventon-actions" style='margin:0'>		
				
				<a class="evo_admin_btn btn_prime" href="http://www.myeventon.com/documentation/" target='_blank'><?php _e( 'Documentation', 'eventon' ); ?></a>
				
				<a class="evo_admin_btn btn_prime" href="http://www.myeventon.com/support/" target='_blank'><?php _e( 'Support', 'eventon' ); ?></a>

				<a class="evo_admin_btn btn_prime" href="http://www.myeventon.com/news/" target='_blank'><?php _e( 'News', 'eventon' ); ?></a>
				<a class="evo_admin_btn btn_prime" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'evo-changelog' ), 'index.php' ) ) ); ?>" ><?php _e( 'Changelog', 'eventon' ); ?></a>
				<a href="http://www.twitter.com/myeventon" target='_blank' class="evo_admin_btn btn_prime"><?php _e( 'Follow on Twitter', 'eventon' ); ?></a>				
			</p>
			<?php /*
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php if ( $_GET['page'] == 'evo-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'evo-about' ), 'index.php' ) ) ); ?>">
					<?php _e( "What's New", 'eventon' ); ?>			
				</a>
			</h2>
			<?php */
		}
	
	// Output the about screen.
		public function about_screen() {
			?>		
			<div class='evo_welcome_page'>	
				<div class="wrap about-wrap eventon-welcome-box">
					<div id='eventon_welcome_header'>			
						<p class='logo'>
							<?php echo EVO()->evo_admin->get_svg_el();?>
							<span><?php _e('EventON','eventon');?></span></p>
					</div>

								
					<div class="return-to-dashboard">
						<a class='evo_wel_btn' href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'eventon' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to myeventon Settings', 'eventon' ); ?></a>
					</div>
					<div class='evowel_info1'>
						<p class='h3'><?php
							if(!empty($_GET['evo-updated']))
								$message = __( 'Thank you for updating EventON', 'eventon' );
							else
								$message = __( 'Thank you for purchasing EventON', 'eventon' );
								
							printf( __( '%s', 'eventon' ), $message);
						?></p>		
						<p class='version_num'><?php printf( __( '%s', 'eventon' ), EVO()->version );?></p>
						<p class='h4'><?php 
							if(!empty($_GET['evo-updated']))
								printf( __( 'We hope you will enjoy the new features we have added!','eventon'));
							else
								printf( __( 'We hope you will enjoy eventON - the event calendar plugin for WordPress!','eventon'));
						?></p>						
					</div>


				</div>

				<div class='get_started'>
					<div class="get_started_in">
						<h2>
							<a class='evo_admin_btn btn_prime' href='<?php echo admin_url('index.php?page=evo-getting-started');?>'>Get started</a>
							<a class='evo_admin_btn btn_prime' href='https://docs.myeventon.com' target='_blank'>Docs</a>
							<a class='evo_admin_btn btn_prime' href='https://www.youtube.com/playlist?list=PLj0uAR9EylGrROSEOpT6WuL_ZkRgEIhLq' target='_blank'>Videos</a>
							<a class='evo_admin_btn btn_prime' href='<?php echo admin_url('index.php?page=evo-changelog');?>'>Changelog</a>
						</h2>
					</div>

				</div>

				<div class='evow_credits'>
					<p style='font-size:14px; margin:0; padding-bottom:3px;opacity: 0.8;text-transform: uppercase;'><a href='http://www.ashanjay.com' target='_blank'>A Product of AshanJay Designs LLC</a></p>
				</div>
			</div>
		<?php
		}
	
	/**
	 * Render getting started screen
	 * @since 2.2.22
	 * @return void 
	 */
	public function getting_started_screen(){

		$display_version = EVO_VERSION;
		?>
		
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to EventON %s', 'eventon' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! EventON Calendar %s is a stylish minimal calendar that will help you stay on top!', 'eventon' ), $display_version ); ?></div>
			<div class="evo-badge"><?php printf( __( 'Version %s', 'eventon' ), $display_version ); ?></div>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'Use these tips to get started with EventON Calendar.', 'eventon' ); ?></p>

			<div class="changelog">
				<h3><?php _e( 'Creating Your First Event', 'eventon' );?></h3>

				<div class="feature-section">
					
					
					<h4><?php printf( __( '<a href="%s">Events &rarr; Add New</a>', 'eventon' ), admin_url( 'post-new.php?post_type=ajde_events' ) ); ?></h4>
					<p><?php _e( 'You can access all your events from the Events menu. To create your first event, simply click Add New and then fill out the event details.', 'eventon' ); ?></p>

					<h4><?php _e( 'Event Details', 'eventon' );?></h4>
					<p><?php _e( 'You can enter key event information such as Time & Date under event details. You can optionally select <em>All day event, Hide end time and event repeat values</em> in here. ', 'eventon' );?></p>

					<h4><?php _e( 'Other Event Data', 'eventon' );?></h4>
					<p><?php _e( 'Entering Location and Venue data will show Google maps on Events. Organizers will allow you to set event organizers with their contact information. <em>User Interaction for event click</em> controls how the event will interact upon a user click on the event from the calendar.', 'eventon' );?></p>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Display a Calendar via Shortcode', 'eventon' );?></h3>

				<div class="feature-section">

					

					<h4><?php _e( 'Basic Month Calendar','eventon' );?></h4>
					<p><?php _e( 'The <code>[add_eventon]</code> shortcode will display the basic calendar for the current month. EventON Calendar is responsive and mobile ready.', 'eventon' );?></p>

					<h4><?php _e( 'Show List of Events', 'eventon' );?></h4>
					<p><?php _e( 'You can show a list of months by using the event list shortcode as below:', 'eventon' );?></p>
					<p><code>[add_eventon_list number_of_months="3"]</code></p>

					<h4><?php _e( 'Tiles Calendar Design', 'eventon' );?></h4>
					<p><?php _e( 'You can convert the list calendar to event tiles layout using shortcode below:', 'eventon' );?></p>
					<p><code>[add_eventon tiles="yes"]</code></p>

					<h4><?php _e( 'Additional Calendar Variations', 'eventon' ); ?></h4>
					<p><?php printf( __( 'You can create other calendar variations using the shortcode variables present via <a href="%s">shortcode generator</a>. A general guide to some of the shortcode variables can be found in <a href="%s">here.</a> You can also find our <a href="%s">extensive online documentation library</a> for additional help.', 'eventon' ), 'http://www.myeventon.com/documentation/shortcode-generator/', 'http://www.myeventon.com/documentation/shortcode-guide/', 'http://www.myeventon.com/documentation/' ); ?></p>

					<h4><?php printf( __( '<a href="%s">Configure EventON Settings</a>', 'eventon' ), admin_url( 'admin.php?page=eventon' ) ); ?></h4>
					<p><?php _e( 'You can further customize EventON calendar from EventON Settings. Calendar appearance, language, and various other options can be set to your preferance in EventON Settings.', 'eventon' ); ?></p>
				</div>				
			</div>

			<div class="changelog">
				<h3><?php _e( 'Extend EventON Features', 'eventon' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Library of Growing Addons','eventon' );?></h4>
					<p><?php printf( __( 'Addons for EventON extends the features to next level in your calendar. Some of our popular addons are: <a href="%s">interactive month grid</a>, <a href="%s">front-end event submission</a>, <a href="%s">RSVP to events</a>, Event Tickets and many more ', 'eventon' ), 
					'http://www.myeventon.com/addons/full-cal/',
					'http://www.myeventon.com/addons/action-user/',
					'http://www.myeventon.com/addons/rsvp-events/');?></p>

					<h4><?php _e( 'Visit the Addons Store', 'eventon' );?></h4>
					<p><?php _e( '<a href="http://www.myeventon.com/addons/" target="_blank">The Addon store</a> has a list of all available addons for EventON that you can purchase.', 'eventon' );?></p>

				</div>
			</div>

		</div>

		<?php
	}


	/** Sends user to the welcome page on first activation	 */
		public function welcome() {
			// Bail if no activation redirect transient is set
		    if ( ! get_transient( '_evo_activation_redirect' )  )
				return;

			// Delete the redirect transient
			delete_transient( '_evo_activation_redirect' );

			// Bail if we are waiting to install or update via the interface update/install links
			if ( get_option( '_evo_needs_update' ) == 1  )
				return;

			// Bail if activating from network, or bulk, or within an iFrame
			if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) )
				return;
			
			// plugin is updated
			if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'eventon.php' ) ) )
				return;
				//wp_safe_redirect( admin_url( 'index.php?page=evo-about&evo-updated=true' ) );
			
			wp_safe_redirect( admin_url( 'index.php?page=evo-about' ) );

			// update dynamic styles file for eventon
			EVO()->evo_admin->generate_dynamic_styles_file();			
			
			exit;
		}	
}

new EVO_Welcome_Page();
?>