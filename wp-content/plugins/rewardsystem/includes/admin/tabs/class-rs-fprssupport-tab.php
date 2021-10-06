<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSSupportTab' ) ) {

	class RSSupportTab {

		public static function init() {

			add_action( 'woocommerce_rs_settings_tabs_fprssupport' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action( 'woocommerce_update_options_fprssupport' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action( 'woocommerce_admin_field_fp_support_content' , array( __CLASS__ , 'fp_support_content' ) ) ;
		}

		/*
		 * Function label settings to Member Level Tab
		 */

		public static function reward_system_admin_fields() {
			global $woocommerce ;
			return apply_filters( 'woocommerce_fprssupport_tab' , array(
				array(
					'type' => 'rs_modulecheck_start' ,
				) ,
				array(
					'type' => 'title' ,
					'id'   => '_fp_reward_system_support'
				) ,
				array(
					'type' => 'fp_support_content' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_fp_reward_system_support' ) ,
				array(
					'type' => 'rs_modulecheck_end' ,
				) ,
			) ) ;
		}

		/**
		 * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
		 */
		public static function reward_system_register_admin_settings() {

			woocommerce_admin_fields( self::reward_system_admin_fields() ) ;
		}

		/**
		 * Update the Settings on Save Changes may happen in SUMO Reward Points
		 */
		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() ) ;
		}

		/**
		 * Initialize the Default Settings by looping this function
		 */
		public static function reward_system_default_settings() {
			global $woocommerce ;
			foreach ( self::reward_system_admin_fields() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function fp_support_content() {
			?>
			<h3><?php esc_html_e('Welcome Page', 'rewardsystem'); ?></h3>
			<p><?php esc_html_e('For more information on SUMO Reward Points please check the ', 'rewardsystem'); ?><a href="<?php echo esc_url(admin_url( 'admin.php?page=sumo-reward-points-welcome-page' )) ; ?>" ><?php esc_html_e('Welcome page', 'rewardsystem'); ?></a></p>
			<h3><?php esc_html_e('Documentation', 'rewardsystem'); ?></h3>
			<p><?php esc_html_e('Please check the documentation as we have lots of information there. The documentation file can be found inside the documentation folder which you will find when you unzip the downloaded zip file.', 'rewardsystem'); ?></p>
			<h3><?php esc_html_e('Contact Support', 'rewardsystem'); ?></h3>
			<p id="fp_support_content"><?php esc_html_e(' For support, feature request or any help, please ', 'rewardsystem'); ?><a href="http://support.fantasticplugins.com/" target="_blank"><?php esc_html_e('register and open a support ticket on our site', 'rewardsystem'); ?></a></p>
			<?php
		}

	}

	RSSupportTab::init() ;
}
