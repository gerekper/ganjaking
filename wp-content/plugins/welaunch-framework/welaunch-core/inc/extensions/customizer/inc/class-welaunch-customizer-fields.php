<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * weLaunch Customizer Fields Class
 *
 * @class   weLaunch_Core
 * @version 4.0.0
 * @package weLaunch Framework
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound
// phpcs:disable Generic.Files.OneClassPerFile

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Customizer_Control_Checkbox', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_checkbox
	 */
	class weLaunch_Customizer_Control_Checkbox extends weLaunch_Customizer_Control {
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-checkbox';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Color_Rgba', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_color_rgba
	 */
	class weLaunch_Customizer_Control_Color_Rgba extends weLaunch_Customizer_Control {
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-color_rgba';
	}
}


if ( ! class_exists( 'weLaunch_Customizer_Control_Color', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_color
	 */
	class weLaunch_Customizer_Control_Color extends weLaunch_Customizer_Control {
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-color';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Media', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_media
	 */
	class weLaunch_Customizer_Control_Media extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-media';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Spinner', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_spinner
	 */
	class weLaunch_Customizer_Control_Spinner extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-spinner';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Palette', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_palette
	 */
	class weLaunch_Customizer_Control_Palette extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-palette';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Button_Set', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_button_set
	 */
	class weLaunch_Customizer_Control_Button_Set extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-button_set';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Image_Select', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_image_select
	 */
	class weLaunch_Customizer_Control_Image_Select extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile

		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-image_select';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Radio', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_radio
	 */
	class weLaunch_Customizer_Control_Radio extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-radio';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Select', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_select
	 */
	class weLaunch_Customizer_Control_Select extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile

		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-select';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Gallery', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_gallery
	 */
	class weLaunch_Customizer_Control_Gallery extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-gallery';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Slider', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_slider
	 */
	class weLaunch_Customizer_Control_Slider extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-slider';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Sortable', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_sortable
	 */
	class weLaunch_Customizer_Control_Sortable extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-sortable';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Switch', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_switch
	 */
	class weLaunch_Customizer_Control_Switch extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-switch';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Text', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_text
	 */
	class weLaunch_Customizer_Control_Text extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile
		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-text';
	}
}

if ( ! class_exists( 'weLaunch_Customizer_Control_Textarea', false ) ) {
	/**
	 * Class weLaunch_Customizer_Control_textarea
	 */
	class weLaunch_Customizer_Control_Textarea extends weLaunch_Customizer_Control {
 // phpcs:ignore Generic.Files.OneClassPerFile

		/**
		 * Set control type.
		 *
		 * @var string
		 */
		public $type = 'welaunch-textarea';
	}
}
