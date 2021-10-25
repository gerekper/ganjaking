<?php
/**
 * all demo import
 * @package  Appside
 * @since 1.0.0
 * @link https://github.com/proteusthemes/one-click-demo-import
 */

if ( !defined('ABSPATH') ){
	exit(); // exit if access directly
}

if (!class_exists('Appside_Theme_Demo_Import_Class')) {

	class Appside_Theme_Demo_Import_Class
	{
		/*
		* $instance
		* @since 1.0.0
		* */
		protected static $instance;

		public function __construct()
		{
			//import demo files
			add_filter( 'pt-ocdi/import_files', array($this,'import_files') );
			//import theme options data
			add_action('pt-ocdi/after_content_import_execution', array($this,'after_content_import_execution'), 3, 99 );
			//import import data setup default menu and home page and blog page
			add_action('pt-ocdi/after_import',array($this,'after_import_setup'));
			add_filter('pt-ocdi/disable_pt_branding','__return_true');
            add_filter( 'pt-ocdi/confirmation_dialog_options', array($this,'confirmation_dialog_options'), 10, 1 );
		}

		/**
		 * getInstance()
		 * */
		public static function getInstance()
		{
			if (null == self::$instance) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Import Demo Files Data
		 * @since 1.0.
		 * */
		public function import_files() {
			return array(
				array(
					'import_file_name'           => 'All Demos Page and Media ',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/all-home-content.xml',
					'local_import_customizer_file'     => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/customize.dat',
					'local_import_widget_file'     => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/widgets.wie',
					'local_import_json'           => array(
						array(
							'file_path'     => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/theme-options.json',
							'option_name'   => 'appside_theme_options',
						),
					),
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/main-demo.jpg'
				),
				array(
					'import_file_name'           => 'Travel App',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/travel-app.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/travel-app/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/travel.jpg'
				),
				array(
					'import_file_name'           => 'Saas App',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/startup.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/sass/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/startup.jpg'
				),
				array(
					'import_file_name'           => 'Ecommerce App',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/ecommerce-app.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/ecommerce-app/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/ecommerce.jpg'
				),
				array(
					'import_file_name'           => 'Startup App',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/ecommerce-app.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/startup/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/sass.jpg'
				),
				array(
					'import_file_name'           => 'Medical App',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/medical-app.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/mediacal-app/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/medical.jpg'
				),
				array(
					'import_file_name'           => 'Learning App',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/learning-app.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/learning-app/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/learning.jpg'
				),
				array(
					'import_file_name'           => 'Restaturant App',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/restaurant.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/restaurant-app/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/restaturant.jpg'
				),
				array(
					'import_file_name'           => 'Social App',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/social-app.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/social-app/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/social.jpg'
				),
                array(
					'import_file_name'           => 'Home Page Two',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/appside-home-two.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/home-two',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/home-two.jpg'
				),
                array(
					'import_file_name'           => 'Home Page Three',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/appside-home-three.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/home-three',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/home-three.jpg'
				),
                array(
					'import_file_name'           => 'Home Page Four',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/appside-home-four.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/home-four',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/home-four.jpg'
				),
                array(
					'import_file_name'           => 'Home Page Five',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/appside-home-five.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/home-five',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/home-five.jpg'
				),
                array(
					'import_file_name'           => 'Home Page Six',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/appside-home-six.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/home-six',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/home-six.jpg'
				),
                array(
					'import_file_name'           => 'Home Dark One',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/appside-home-dark-one.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/home-dark-01/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/home-dark-one.jpg'
				),
                array(
					'import_file_name'           => 'Home Page Dark 02',
					'local_import_file'            => trailingslashit( APPSIDE_MASTER_ROOT_PATH ) . 'demo-data-import/demo-data/appside-home-dark-two.xml',
					'import_notice' => esc_html__( 'Please Give Some Time To Import Theme Demo Data, It May Take 5-10 Minutes, It Will Download All Theme Data From Server So Be Cool!.', 'appside-master' ),
					'preview_url'   => 'https://irtech.biz/wp/appside/home-dark-02/',
                    'import_preview_image_url' => APPSIDE_MASTER_ROOT_URL .'demo-data-import/demo-data/images/home-dark-two.jpg'
				),
				
			);
		}

		/**
		 * Import Theme Options Data
		 * @since 1.0.0
		 * */
		function after_content_import_execution( $selected_import_files, $import_files, $selected_index ) {

			$downloader = new \OCDI\Downloader();

			if( ! empty( $import_files[$selected_index]['import_json'] ) ) {

				foreach( $import_files[$selected_index]['import_json'] as $index => $import ) {
					$file_path = $downloader->download_file( $import['file_url'], 'demo-import-file-'. $index .'-'. date( 'Y-m-d__H-i-s' ) .'.json' );
					$file_raw  = \OCDI\Helpers::data_from_file( $file_path );
					update_option( $import['option_name'], json_decode( $file_raw, true ) );
				}

			} else if( ! empty( $import_files[$selected_index]['local_import_json'] ) ) {

				foreach( $import_files[$selected_index]['local_import_json'] as $index => $import ) {
					$file_path = $import['file_path'];
					$file_raw  = \OCDI\Helpers::data_from_file( $file_path );
					update_option( $import['option_name'], json_decode( $file_raw, true ) );
				}

			}

		}
		/**
		 * after_import_setup
		 * @package Appside
		 * @since 1.0.0
		 * */
		function after_import_setup(){

			//assign menus to their locations
			$main_menu = get_term_by('name', 'Primary Menu','nav_menu');

			set_theme_mod('nav_menu_locations',array(
					'main-menu' => $main_menu->term_id
				)
			);

			//assign front page and posts page ( blog page )
			$front_page_id = get_page_by_title('Home One');
			$blog_page_id = get_page_by_title('Blog Classic');

			update_option('show_on_front','page');
			update_option('page_on_front',$front_page_id->ID);
			update_option('page_for_posts',$blog_page_id->ID);
		}

		/**
		 * @package Appside
         * @since 1.0.3
		 *
		 * */
        public function confirmation_dialog_options ( $options ) {
            return array_merge( $options, array(
                'width'       => 600,
                'dialogClass' => 'wp-dialog',
                'resizable'   => false,
                'height'      => 'auto',
                'modal'       => true,
            ) );
        }

	}//end class
	if (class_exists('Appside_Theme_Demo_Import_Class')){
		Appside_Theme_Demo_Import_Class::getInstance();
	}
}