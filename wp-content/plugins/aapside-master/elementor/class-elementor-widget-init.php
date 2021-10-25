<?php
/**
 * All Elementor widget init
 * @package Appside
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // exit if access directly
}


if ( ! class_exists( 'Appside_Elementor_Widget_Init' ) ) {

	class Appside_Elementor_Widget_Init {
		/*
			* $instance
			* @since 1.0.0
			* */
		private static $instance;

		/*
		* construct()
		* @since 1.0.0
		* */
		public function __construct() {
			add_action( 'elementor/elements/categories_registered', array( $this, '_widget_categories' ) );
			//elementor widget registered
			add_action( 'elementor/widgets/widgets_registered', array( $this, '_widget_registered' ) );
			// elementor editor css
			add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'load_assets_for_elementor' ) );
			add_action( 'elementor/controls/controls_registered', array( $this, 'modify_controls' ), 10, 1 );
			//register script after elementor script load
			add_action( 'elementor/frontend/after_register_scripts', array( $this, 'enqueue_scripts' ) );
			//add custom icons to elementor new controls
			add_filter('elementor/icons_manager/native',array($this,'add_custom_icon_to_elementor_icons'));
		}

		/*
	   * getInstance()
	   * @since 1.0.0
	   * */
		public static function getInstance() {
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * _widget_categories()
		 * @since 1.0.0
		 * */
		public function _widget_categories( $elements_manager ) {
			$elements_manager->add_category(
				'appside_widgets',
				[
					'title' => esc_html__( 'Appside Widgets', 'aapside-master' ),
					'icon'  => 'fa fa-plug',
				]
			);
			$elements_manager->add_category(
				'appside_builder_widgets',
				[
					'title' => esc_html__( 'Appside Builder Widgets', 'aapside-master' ),
					'icon'  => 'fa fa-plug',
				]
			);
		}

		/**
		 * _widget_registered()
		 * @since 1.0.0
		 * */
		public function _widget_registered() {

			if ( ! class_exists( 'Elementor\Widget_Base' ) ) {
				return;
			}

			$elementor_widgets = array(
				'appside-preloader-one',
				'video-button-one',
				'advance-heading-one',
				'advance-button-one',
				'back-top-one',
				'animation-circle-one',
				'animation-circle-two',
				'accordion',
				'accordion-two',
				'header-area-one',
				'appside-header-two',
				'appside-button-one',
				'appside-button-two',
				'appside-button-three',
				'appside-button-four',
				'button-group',
				'button-group-two',
				'img-with-video-one',
				'img-with-video-two',
				'img-with-video-three',
				'feature-box-one',
				'feature-box-two',
				'feature-box-three',
				'feature-box-four',
				'feature-box-five',
				'feature-box-six',
				'counterup-one',
				'counterup-two',
				'counterup-three',
				'counterup-four',
				'countdown',
				'tab-one',
				'screenshort-one',
				'screenshort-two',
				'team-member-one',
				'team-member-two',
				'testimonial-one',
				'testimonial-two',
				'testimonial-three',
				'testimonial-four',
				'testimonial-five',
				'testimonial-six',
				'testimonial-seven',
				'testimonial-eight',
                'testimonial-nine',
                'testimonial-ten',
				'price-plan-one',
				'price-plan-two',
				'price-plan-three',
				'blog-grid-one',
				'blog-slider-one',
				'price-plan-area',
				'icon-box-one',
				'icon-box-two',
				'icon-box-three',
				'icon-box-four',
				'icon-box-list',
				'icon-box-five',
				'icon-box-six',
				'icon-box-seven',
				'icon-box-eight',
				'icon-box-nine',
				'icon-box-ten',
				'icon-box-eleven',
				'icon-box-twoelve',
				'icon-box-thirteen',
				'icon-box-fourteen',
				'icon-box-fifteen',
				'icon-box-sixteen',
				'icon-box-seventeen',
				'icon-box-eighteen',
				'icon-list-one',
				'icon-list-two',
				'brand-carousel-one',
				'navbar-style-one',
				'navbar-style-two',
				'section-title-one',
				'heading-title-one',
				'img-box-one',
				'quote-box-one',
				'contact-info-list-one',
				'portfolio-grid-one',
				'portfolio-grid-two',
				'portfolio-grid-three',
				'portfolio-filter-one',
				'contact-single-item-02',
				'work-single-item',
			);

			$elementor_widgets = apply_filters( 'appside_elementor_widget', $elementor_widgets );

			if ( is_array( $elementor_widgets ) && ! empty( $elementor_widgets ) ) {
				foreach ( $elementor_widgets as $widget ) {
					if ( file_exists( APPSIDE_MASTER_ELEMENTOR . '/widgets/class-' . $widget . '-elementor-widget.php' ) ) {
						require_once APPSIDE_MASTER_ELEMENTOR . '/widgets/class-' . $widget . '-elementor-widget.php';
					}
				}
			}

		}

		/**
		 * Adding custom icon to icon control in Elementor
		 */
		public function modify_controls( $controls_registry ) {
			// Get existing icons
			$icons = $controls_registry->get_control( 'icon' )->get_settings( 'options' );

			// Append new icons

			$new_icons = array_merge(
				array(
					'flaticon-graphic-design'           => esc_html__( 'graphic-design', 'aapside-master' ),
					'flaticon-vector'                   => esc_html__( 'vector', 'aapside-master' ),
					'flaticon-paint-palette'            => esc_html__( 'paint-palette', 'aapside-master' ),
					'flaticon-responsive'               => esc_html__( 'responsive', 'aapside-master' ),
					'flaticon-layers'                   => esc_html__( 'layers', 'aapside-master' ),
					'flaticon-layers-1'                 => esc_html__( 'layers 1', 'aapside-master' ),
					'flaticon-layers-2'                 => esc_html__( 'layers 2', 'aapside-master' ),
					'flaticon-picture'                  => esc_html__( 'picture', 'aapside-master' ),
					'flaticon-camera'                   => esc_html__( 'camera', 'aapside-master' ),
					'flaticon-picture-1'                => esc_html__( 'picture-1', 'aapside-master' ),
					'flaticon-picture-2'                => esc_html__( 'picture-2', 'aapside-master' ),
					'flaticon-apple'                    => esc_html__( 'apple', 'aapside-master' ),
					'flaticon-apple-1'                  => esc_html__( 'apple-2', 'aapside-master' ),
					'flaticon-android-logo'             => esc_html__( 'android-logo', 'aapside-master' ),
					'flaticon-android-character-symbol' => esc_html__( 'android-character', 'aapside-master' ),
					'flaticon-android-logo-1'           => esc_html__( 'android', 'aapside-master' ),
					'flaticon-windows'                  => esc_html__( 'windows', 'aapside-master' ),
					'flaticon-windows-8'                => esc_html__( 'windows-8', 'aapside-master' ),
					'flaticon-windows-logo-silhouette'  => esc_html__( 'windows-logo-silhouette', 'aapside-master' ),
					'flaticon-rating'                   => esc_html__( 'rating', 'aapside-master' ),
					'flaticon-review'                   => esc_html__( 'review', 'aapside-master' ),
					'flaticon-review-1'                 => esc_html__( 'review', 'aapside-master' ),
					'flaticon-conversation'             => esc_html__( 'conversation', 'aapside-master' ),
					'flaticon-support'                  => esc_html__( 'support', 'aapside-master' ),
					'flaticon-communication'            => esc_html__( 'communication', 'aapside-master' ),
					'flaticon-customer-service'         => esc_html__( 'customer-service', 'aapside-master' ),
					'flaticon-chat'                     => esc_html__( 'chat', 'aapside-master' ),
					'flaticon-email'                    => esc_html__( 'email', 'aapside-master' ),
					'flaticon-mail'                     => esc_html__( 'mail', 'aapside-master' ),
					'flaticon-message'                  => esc_html__( 'message', 'aapside-master' ),
					'flaticon-email-1'                  => esc_html__( 'email-1', 'aapside-master' ),
					'flaticon-award'                    => esc_html__( 'award', 'aapside-master' ),
					'flaticon-cap'                      => esc_html__( 'cap', 'aapside-master' ),
					'flaticon-medal'                    => esc_html__( 'medal', 'aapside-master' ),
					'flaticon-trophy'                   => esc_html__( 'trophy', 'aapside-master' ),
					'flaticon-trophy-1'                 => esc_html__( 'trophy', 'aapside-master' ),
					'flaticon-badge'                    => esc_html__( 'badge', 'aapside-master' ),
					'flaticon-trophy-2'                 => esc_html__( 'trophy-2', 'aapside-master' ),
					'flaticon-settings'                 => esc_html__( 'settings', 'aapside-master' ),
					'flaticon-settings-1'               => esc_html__( 'settings', 'aapside-master' ),
					'flaticon-tools'                    => esc_html__( 'tools', 'aapside-master' ),
					'flaticon-customer-support'         => esc_html__( 'customer-support', 'aapside-master' ),
					'flaticon-settings-2'               => esc_html__( 'settings-2', 'aapside-master' ),
					'flaticon-shield'                   => esc_html__( 'shield', 'aapside-master' ),
					'flaticon-shield-1'                 => esc_html__( 'shield', 'aapside-master' ),
					'flaticon-checked'                  => esc_html__( 'checked', 'aapside-master' ),
					'flaticon-analytics'                => esc_html__( 'analytics', 'aapside-master' ),
					'flaticon-conversation-1'           => esc_html__( 'conversation', 'aapside-master' ),
					'flaticon-speech-bubble'            => esc_html__( 'speech-bubble', 'aapside-master' ),
					'flaticon-chat-1'                   => esc_html__( 'chat-1', 'aapside-master' ),
					'flaticon-database'                 => esc_html__( 'database', 'aapside-master' ),
					'flaticon-data-protection'          => esc_html__( 'protection', 'aapside-master' ),
					'flaticon-cloud'                    => esc_html__( 'cloud', 'aapside-master' ),
					'xg-icon-phone'                     => esc_html__( 'phone', 'aapside-master' ),
					'xg-icon-pin'                       => esc_html__( 'pin', 'aapside-master' ),
					'xg-icon-mail'                      => esc_html__( 'mail', 'aapside-master' ),
					'xg-icon-phone-1'                   => esc_html__( 'phone one', 'aapside-master' ),
					'xg-icon-download'                  => esc_html__( 'download', 'aapside-master' ),
					'xg-icon-download-1'                => esc_html__( 'download', 'aapside-master' ),
					'xg-icon-happiness'                 => esc_html__( 'happy', 'aapside-master' ),
					'xg-icon-dance'                     => esc_html__( 'dance', 'aapside-master' ),
					'xg-icon-customer-review'           => esc_html__( 'customer review', 'aapside-master' ),
					'xg-icon-review'                    => esc_html__( 'review', 'aapside-master' ),
					'xg-icon-rating'                    => esc_html__( 'rating', 'aapside-master' ),
					'xg-icon-save'                      => esc_html__( 'save', 'aapside-master' ),
					'xg-icon-floppy-disk'               => esc_html__( 'floppy-disk', 'aapside-master' ),
					'xg-icon-floppy-disk-1'             => esc_html__( 'floppy-disk', 'aapside-master' ),
					'xg-icon-lock'                      => esc_html__( 'lock', 'aapside-master' ),
					'xg-icon-lock-1'                    => esc_html__( 'lock', 'aapside-master' ),
					'xg-icon-password'                  => esc_html__( 'password', 'aapside-master' ),
					'xg-icon-password-1'                => esc_html__( 'password', 'aapside-master' ),
					'xg-icon-shield'                    => esc_html__( 'shield', 'aapside-master' ),
					'xg-icon-log-out'                   => esc_html__( 'log out', 'aapside-master' ),
					'xg-icon-settings'                  => esc_html__( 'settings', 'aapside-master' ),
					'xg-icon-settings-1'                => esc_html__( 'settings', 'aapside-master' ),
					'xg-icon-settings-2'                => esc_html__( 'settings', 'aapside-master' ),
					'xg-icon-customer'                  => esc_html__( 'customer', 'aapside-master' ),
					'xg-icon-tools'                     => esc_html__( 'tools', 'aapside-master' ),
					"iricon-user"                       => esc_html__( 'user', 'aapside-master' ),
					"iricon-cloud"                      => esc_html__( 'cloud', 'aapside-master' ),
					"iricon-cloud-computing"            => esc_html__( 'cloud computing', 'aapside-master' ),
					"iricon-share"                      => esc_html__( 'share', 'aapside-master' ),
					"iricon-suitcase"                   => esc_html__( 'suitcase', 'aapside-master' ),
					"iricon-suitcase-1"                 => esc_html__( 'suitcase', 'aapside-master' ),
					"iricon-luggage"                    => esc_html__( 'luggage', 'aapside-master' ),
					"iricon-auto-update"                => esc_html__( 'auto-update', 'aapside-master' ),
					"iricon-suitcase-2"                 => esc_html__( 'suitcase', 'aapside-master' ),
					"iricon-tourist"                    => esc_html__( 'tourist', 'aapside-master' ),
					"iricon-journey"                    => esc_html__( 'journey', 'aapside-master' ),
					"iricon-luggage-1"                  => esc_html__( 'luggage', 'aapside-master' ),
					"iricon-shopping-bag"               => esc_html__( 'shopping bag', 'aapside-master' ),
					"iricon-airplane"                   => esc_html__( 'airplane', 'aapside-master' ),
					"iricon-booking"                    => esc_html__( 'booking', 'aapside-master' ),
					"iricon-plane"                      => esc_html__( 'plane', 'aapside-master' ),
					"iricon-itinerary"                  => esc_html__( 'itinerary', 'aapside-master' ),
					"iricon-purse"                      => esc_html__( 'purse', 'aapside-master' ),
					"iricon-save-money"                 => esc_html__( 'save money', 'aapside-master' ),
					"iricon-money-bag"                  => esc_html__( 'money bag', 'aapside-master' ),
					"iricon-sunbed"                     => esc_html__( 'sunbed', 'aapside-master' ),
					"iricon-beach"                      => esc_html__( 'beach', 'aapside-master' ),
					"iricon-sex-on-the-beach"           => esc_html__( 'on the beach', 'aapside-master' ),
					"iricon-coconuts"                   => esc_html__( 'coconuts', 'aapside-master' ),
					"iricon-bell"                       => esc_html__( 'bell', 'aapside-master' ),
					"iricon-communications"             => esc_html__( 'communications', 'aapside-master' ),
					"iricon-interface"                  => esc_html__( 'interface', 'aapside-master' ),
					"iricon-web"                        => esc_html__( 'web', 'aapside-master' ),
					"iricon-communications-1"           => esc_html__( 'communications', 'aapside-master' ),
					"iricon-signs"                      => esc_html__( 'signs', 'aapside-master' ),
					"iricon-multimedia"                 => esc_html__( 'multimedia', 'aapside-master' ),
					"iricon-technology"                 => esc_html__( 'technology', 'aapside-master' ),
					"iricon-interface-1"                => esc_html__( 'interface', 'aapside-master' ),
					"iricon-technology-1"               => esc_html__( 'technology', 'aapside-master' ),
					"iricon-business"                   => esc_html__( 'business', 'aapside-master' ),
					"iricon-communications-2"           => esc_html__( 'communications', 'aapside-master' ),
					"iricon-home"                       => esc_html__( 'home', 'aapside-master' ),
					"iricon-interface-2"                => esc_html__( 'interface', 'aapside-master' ),
					"iricon-computer"                   => esc_html__( 'computer', 'aapside-master' ),
					"iricon-lock"                       => esc_html__( 'lock', 'aapside-master' ),
					"iricon-paper-plane"                => esc_html__( 'paper plane', 'aapside-master' ),
					"iricon-delivery"                   => esc_html__( 'delivery', 'aapside-master' ),
					"iricon-speed"                      => esc_html__( 'speed', 'aapside-master' ),
					"iricon-positive-vote"              => esc_html__( 'positive vote', 'aapside-master' ),
					"iricon-computer-screen"            => esc_html__( 'computer screen', 'aapside-master' ),
					"iricon-copy"                       => esc_html__( 'copy', 'aapside-master' ),
					"iricon-shopping-cart"              => esc_html__( 'shopping cart', 'aapside-master' ),
					"iricon-shopping-bag-1"             => esc_html__( 'shopping cart', 'aapside-master' ),
					"iricon-cart"                       => esc_html__( 'cart', 'aapside-master' ),
					"iricon-shopping-cart-1"            => esc_html__( 'shopping cart', 'aapside-master' ),
					"iricon-discount-voucher"           => esc_html__( 'discount voucher', 'aapside-master' ),
					"iricon-price-tag"                  => esc_html__( 'price tag', 'aapside-master' ),
					"iricon-price-tag-1"                => esc_html__( 'price tag', 'aapside-master' ),
					"iricon-student"                    => esc_html__( 'student', 'aapside-master' ),
					"iricon-teacher"                    => esc_html__( 'teacher', 'aapside-master' ),
					"iricon-teacher-1"                  => esc_html__( 'teacher', 'aapside-master' ),
					"iricon-couple"                     => esc_html__( 'couple', 'aapside-master' ),
					"iricon-chef"                       => esc_html__( 'chef', 'aapside-master' ),
					"iricon-cooking"                    => esc_html__( 'cooking', 'aapside-master' ),
					"iricon-chef-1"                     => esc_html__( 'chef', 'aapside-master' ),
					"iricon-chef-2"                     => esc_html__( 'chef', 'aapside-master' ),
					"iricon-bicycle"                    => esc_html__( 'bicycle', 'aapside-master' ),
					"iricon-scooters"                   => esc_html__( 'scooters', 'aapside-master' ),
					"iricon-bike"                       => esc_html__( 'bike', 'aapside-master' ),
					"iricon-tray"                       => esc_html__( 'tray', 'aapside-master' ),
					"iricon-meal"                       => esc_html__( 'meal', 'aapside-master' ),
					"iricon-cutlery-1"                  => esc_html__( 'cutlery', 'aapside-master' ),
					"iricon-cutlery"                    => esc_html__( 'cutlery', 'aapside-master' ),
					"iricon-ice-cream"                  => esc_html__( 'ice-cream', 'aapside-master' ),
					"iricon-ice-cream-1"                => esc_html__( 'ice-cream', 'aapside-master' ),
					"iricon-man"                        => esc_html__( 'man', 'aapside-master' ),
					"oxo-icon-right-quotation-mark"     => esc_html__( 'mark', 'aapside-master' ),
					"oxo-icon-quote-left"               => esc_html__( 'quote-left', 'aapside-master' ),
					"oxo-icon-android-logo"             => esc_html__( 'android-logo', 'aapside-master' ),
					"oxo-icon-speedometer"              => esc_html__( 'speedometer', 'aapside-master' ),
					"oxo-icon-left-quote"               => esc_html__( 'left-quote', 'aapside-master' ),
					"oxo-icon-user"                     => esc_html__( 'user', 'aapside-master' ),
					"oxo-icon-bar-chart"                => esc_html__( 'mark', 'aapside-master' ),
					"oxo-icon-speech-bubble"            => esc_html__( 'speech-bubble', 'aapside-master' ),
					"oxo-icon-pencil"                   => esc_html__( 'pencil', 'aapside-master' ),
					"oxo-icon-user-1"                   => esc_html__( 'user-1', 'aapside-master' ),
					"oxo-icon-trash"                    => esc_html__( 'trash', 'aapside-master' ),
					"oxo-icon-clock"                    => esc_html__( 'clock', 'aapside-master' ),
					"oxo-icon-mouse"                    => esc_html__( 'mouse', 'aapside-master' ),
					"oxo-icon-volume"                   => esc_html__( 'volume', 'aapside-master' ),
					"oxo-icon-folder"                   => esc_html__( 'folder', 'aapside-master' ),
					"oxo-icon-sitemap"                  => esc_html__( 'sitemap', 'aapside-master' ),
					"oxo-icon-shuffle"                  => esc_html__( 'shuffle', 'aapside-master' ),
					"oxo-icon-levels"                   => esc_html__( 'levels', 'aapside-master' ),
					"oxo-icon-screen"                   => esc_html__( 'screen', 'aapside-master' ),
					"oxo-icon-switch"                   => esc_html__( 'switch', 'aapside-master' ),
					"oxo-icon-sledge"                   => esc_html__( 'sledge', 'aapside-master' ),
					"oxo-icon-basketball"               => esc_html__( 'basketball', 'aapside-master' ),
                    "oxo-icon-cloud-computing"          => esc_html__( 'cloud-computing', 'aapside-master' ),
					"oxo-icon-next"                     => esc_html__( 'next', 'aapside-master' ),
                    "oxo-icon-speech-bubble-1"          => esc_html__( 'speech-bubble-1', 'aapside-master' ),
					"oxo-icon-upload"                   => esc_html__( 'upload', 'aapside-master' ),
					"oxo-icon-upload-1"                 => esc_html__( 'upload-1', 'aapside-master' ),
					"oxo-icon-power-button"             => esc_html__( 'power-button', 'aapside-master' ),
					"oxo-icon-download"                 => esc_html__( 'download', 'aapside-master' ),
					"oxo-icon-upload-2"                 => esc_html__( 'upload-2', 'aapside-master' ),
					"oxo-icon-switch-1"                 => esc_html__( 'switch-1', 'aapside-master' ),
					"oxo-icon-mail"                     => esc_html__( 'mail', 'aapside-master' ),
					"oxo-icon-mail-1"                   => esc_html__( 'mail-1', 'aapside-master' ),
					"oxo-icon-computer-1"               => esc_html__( 'computer-1', 'aapside-master' ),
					"oxo-icon-computer"                 => esc_html__( 'computer', 'aapside-master' ),
					"oxo-icon-tools-and-utensils"       => esc_html__( 'tools-and-utensils', 'aapside-master' ),
					"oxo-icon-pin"                      => esc_html__( 'pin', 'aapside-master' ),
					"oxo-icon-stars"                    => esc_html__( 'stars', 'aapside-master' ),
					"oxo-icon-monitor"                  => esc_html__( 'monitor', 'aapside-master' ),
					"oxo-icon-notepad"                  => esc_html__( 'notepad', 'aapside-master' ),
					"oxo-icon-clipboards"               => esc_html__( 'clipboards', 'aapside-master' ),
					"oxo-icon-clipboards-1"             => esc_html__( 'clipboards-1', 'aapside-master' ),
					"oxo-icon-padlock"                  => esc_html__( 'padlock', 'aapside-master' ),
					"oxo-icon-android"                  => esc_html__( 'android', 'aapside-master' ),
					"oxo-icon-placeholder"              => esc_html__( 'placeholder', 'aapside-master' ),
					"oxo-icon-clean"                    => esc_html__( 'clean', 'aapside-master' ),
					"oxo-icon-plane"                    => esc_html__( 'plane', 'aapside-master' ),
					"oxo-icon-apple"                    => esc_html__( 'apple', 'aapside-master' ),
					"oxo-icon-padlock-1"                => esc_html__( 'padlock-1', 'aapside-master' ),
					"oxo-icon-followers"                => esc_html__( 'followers', 'aapside-master' ),
					"oxo-icon-loupe"                    => esc_html__( 'loupe', 'aapside-master' ),
					"oxo-icon-collaboration"            => esc_html__( 'collaboration', 'aapside-master' ),
					"oxo-icon-shopping-cart"            => esc_html__( 'shopping-cart', 'aapside-master' ),
					"oxo-icon-email"                    => esc_html__( 'email', 'aapside-master' ),
					"oxo-icon-padlock-2"                => esc_html__( 'padlock-2', 'aapside-master' ),
					"oxo-icon-notification"             => esc_html__( 'notification', 'aapside-master' ),
					"oxo-icon-prescription"             => esc_html__( 'prescription', 'aapside-master' ),
					"oxo-icon-share"                    => esc_html__( 'share', 'aapside-master' ),
					"oxo-icon-email-1"                  => esc_html__( 'email-1', 'aapside-master' ),
					"oxo-icon-paper-plane"              => esc_html__( 'paper-plane', 'aapside-master' ),
					"oxo-icon-smartphone"               => esc_html__( 'smartphone', 'aapside-master' ),
					"oxo-icon-graduation-hat"           => esc_html__( 'graduation-hat', 'aapside-master' ),
					"oxo-icon-heart-beat"               => esc_html__( 'heart-beat', 'aapside-master' ),
					"oxo-icon-call"                     => esc_html__( 'call', 'aapside-master' ),
					"oxo-icon-attach"                   => esc_html__( 'attach', 'aapside-master' ),
					"oxo-icon-cardiology"               => esc_html__( 'cardiology', 'aapside-master' ),
					"oxo-icon-bell-ring"                => esc_html__( 'bell-ring', 'aapside-master' ),
					"oxo-icon-air-freight"              => esc_html__( 'air-freight', 'aapside-master' ),
					"oxo-icon-search"                   => esc_html__( 'search', 'aapside-master' ),
					"oxo-icon-edit"                     => esc_html__( 'edit', 'aapside-master' ),
					"oxo-icon-privacy"                  => esc_html__( 'privacy', 'aapside-master' ),
					"oxo-icon-shopping-cart-1"          => esc_html__( 'shopping-cart-1', 'aapside-master' ),
					"oxo-icon-speedometer-1"            => esc_html__( 'speedometer-1', 'aapside-master' ),
					"oxo-icon-clipboard"                => esc_html__( 'clipboard', 'aapside-master' ),
					"oxo-icon-apple-1"                  => esc_html__( 'apple-1', 'aapside-master' ),
					"oxo-icon-gallery"                  => esc_html__( 'gallery', 'aapside-master' ),
					"oxo-icon-save-money"               => esc_html__( 'save-money', 'aapside-master' ),
					"oxo-icon-share-1"                  => esc_html__( 'share-1', 'aapside-master' ),
					"oxo-icon-star"                     => esc_html__( 'star', 'aapside-master' ),
					"oxo-icon-hotel"                    => esc_html__( 'hotel', 'aapside-master' ),
					"oxo-icon-clock-1"                  => esc_html__( 'clock-1', 'aapside-master' ),
					"oxo-icon-notification-1"           => esc_html__( 'notification-1', 'aapside-master' ),
					"oxo-icon-smartphone-1"             => esc_html__( 'smartphone-1', 'aapside-master' ),
					"oxo-icon-fast-delivery"            => esc_html__( 'fast-delivery', 'aapside-master' ),
					"oxo-icon-mobile"                   => esc_html__( 'mobile', 'aapside-master' ),
					"oxo-icon-contract"                 => esc_html__( 'contract', 'aapside-master' ),
					"oxo-icon-accounting"               => esc_html__( 'accounting', 'aapside-master' ),
					"oxo-icon-notebook"                 => esc_html__( 'notebook', 'aapside-master' ),
					"oxo-icon-verify"                   => esc_html__( 'verify', 'aapside-master' ),
					"oxo-icon-feedback"                 => esc_html__( 'feedback', 'aapside-master' ),
					"oxo-icon-sticky-notes"             => esc_html__( 'sticky-notes', 'aapside-master' ),
					"oxo-icon-favourite"                => esc_html__( 'favourite', 'aapside-master' ),
					"oxo-icon-graduate"                 => esc_html__( 'graduate', 'aapside-master' ),
					"oxo-icon-student"                  => esc_html__( 'student', 'aapside-master' ),
					"oxo-icon-send"                     => esc_html__( 'send', 'aapside-master' ),
					"oxo-icon-greeting"                 => esc_html__( 'student', 'aapside-master' ),
					"oxo-icon-graduated"                => esc_html__( 'graduated', 'aapside-master' ),
					"oxo-icon-open"                     => esc_html__( 'open', 'aapside-master' ),
					"oxo-icon-monitor-1"                => esc_html__( 'monitor-1', 'aapside-master' ),
					"oxo-icon-notification-2"           => esc_html__( 'notification-2', 'aapside-master' )
				),
				$icons
			);

			// Then we set a new list of icons as the options of the icon control
			$controls_registry->get_control( 'icon' )->set_settings( 'options', $new_icons );
		}

		/**
		 * load custom assets for elementor
		 * @since 1.0.0
		 * */
		public function load_assets_for_elementor() {
			wp_enqueue_style( 'flaticon', APPSIDE_MASTER_CSS . '/flaticon.css' );
			wp_enqueue_style( 'xg-icons', APPSIDE_MASTER_CSS . '/xg-icons.css' );
            wp_enqueue_style( 'ir-icon', APPSIDE_MASTER_CSS . '/ir-icon.css' );
            wp_enqueue_style( 'oxo-icon', APPSIDE_MASTER_CSS . '/oxo-icon.css' );
			wp_enqueue_style( 'appside-elementor-editor', APPSIDE_MASTER_ADMIN_ASSETS . '/css/elementor-editor.css' );
		}

		/**
		 * enqueue scripts
		 * @since 2.0.0
		 * */
		public function enqueue_scripts() {
			wp_enqueue_script( 'parallax-scroll', APPSIDE_MASTER_JS . '/jquery.parallax-scroll.js', array(), false, true );
			wp_enqueue_script( 'smoove', APPSIDE_MASTER_JS . '/jquery.smoove.js', null, false, true );
            wp_enqueue_script( 'parallax', APPSIDE_MASTER_JS . '/parallax.js', array(), false, true );
            wp_enqueue_script( 'countdown', APPSIDE_MASTER_JS . '/jquery.countdown.min.js', array(), false, true );
		}

		/**
		 * elementor custom icons
		 * @since 2.0.0
		 * */
		public function add_custom_icon_to_elementor_icons($icons){

			$icons['iricon'] = [
				'name' => 'iricon',
				'label' => esc_html__( 'Ir Icon', 'aapside-master' ),
				'url' => APPSIDE_MASTER_CSS .'/ir-icon.css', // icon css file
				'enqueue' => [APPSIDE_MASTER_CSS .'/ir-icon.css'], // icon css file
				'prefix' => 'iricon-', //prefix ( like fas-fa  )
		        'displayPrefix' => '', //prefix to display icon
		        'labelIcon' => 'iricon-purse', //tab icon of elementor icons library
		        'ver' => '1.0.0',
		        'fetchJson' => APPSIDE_MASTER_JS .'/icons/iricon.js', //json file with icon list example {"icons: ['icon class']}
		        'native' => true,
			];
            $icons['oxo-icon'] = [
                'name' => 'oxo-icon',
                'label' => esc_html__( 'OXO Icon', 'aapside-master' ),
                'url' => APPSIDE_MASTER_CSS .'/oxo-icon.css', // icon css file
                'enqueue' => [APPSIDE_MASTER_CSS .'/oxo-icon.css'], // icon css file
                'prefix' => 'oxo-icon-', //prefix ( like fas-fa  )
                'displayPrefix' => '', //prefix to display icon
                'labelIcon' => 'oxo-icon-star', //tab icon of elementor icons library
                'ver' => '1.0.0',
                'fetchJson' => APPSIDE_MASTER_JS .'/icons/oxoicon.js', //json file with icon list example {"icons: ['icon class']}
                'native' => true,
            ];

			$icons['xg-icon'] = [
				'name' => 'xg-icon',
				'label' => esc_html__( 'XG Icon', 'aapside-master' ),
				'url' => APPSIDE_MASTER_CSS .'/xg-icons.css', // icon css file
				'enqueue' => [APPSIDE_MASTER_CSS .'/xg-icons.css'], // icon css file
				'prefix' => 'xg-icon-', //prefix ( like fas-fa  )
				'displayPrefix' => '', //prefix to display icon
				'labelIcon' => 'xg-icon-happiness', //tab icon of elementor icons library
				'ver' => '1.0.0',
				'fetchJson' => APPSIDE_MASTER_JS .'/icons/xgicon.js', //json file with icon list example {"icons: ['icon class']}
				'native' => true,
			];

			$icons['flaticon'] = [
				'name' => 'flaticon',
				'label' => esc_html__( 'Flaticon', 'aapside-master' ),
				'url' => APPSIDE_MASTER_CSS .'/flaticon.css', // icon css file
				'enqueue' => [APPSIDE_MASTER_CSS .'/flaticon.css'], // icon css file
				'prefix' => 'flaticon-', //prefix ( like fas-fa  )
				'displayPrefix' => '', //prefix to display icon
				'labelIcon' => 'flaticon-graphic-design', //tab icon of elementor icons library
				'ver' => '1.0.0',
				'fetchJson' => APPSIDE_MASTER_JS .'/icons/flaticon.js', //json file with icon list example {"icons: ['icon class']}
				'native' => true,
			];

        	return $icons;
        }
	}

	if ( class_exists( 'Appside_Elementor_Widget_Init' ) ) {
		Appside_Elementor_Widget_Init::getInstance();
	}

}//end if
