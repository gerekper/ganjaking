<?php
/**
 * Mask Image Group control class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Controls;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || die();

class Group_Control_Mask_Image extends Group_Control_Base {

    /**
     * Fields.
     *
     * Holds all the background control fields.
     *
     * @access protected
     * @static
     *
     * @var array Background control fields.
     */
    protected static $fields;

    /**
     * Get background control type.
     *
     * Retrieve the control type, in this case `ha_text_color`.
     *
     * @since 1.0.0
     * @access public
     * @static
     *
     * @return string Control type.
     */
    public static function get_type() {
        return 'ha-mask-image';
    }

    /**
     * Init fields.
     *
     * Initialize mask image control fields.
     *
     * @since 1.2.2
     * @access public
     *
     * @return array Control fields.
     */
    public function init_fields() {
        $fields = [];

	    $fields['mask_shape'] = [
		    'label' => _x( 'Masking Shape', 'Mask Image', 'happy-addons-pro' ),
		    'title' => _x( 'Masking Shape', 'Mask Image', 'happy-addons-pro' ),
		    'type' => Controls_Manager::CHOOSE,
		    'default' => 'default',
		    'options' => [
			    'default' => [
				    'title' =>_x( 'Default Shapes', 'Mask Image', 'happy-addons-pro' ),
				    'icon' => 'hm hm-happyaddons',
			    ],
			    'custom' => [
				    'title' => _x( 'Custom Shape', 'Mask Image', 'happy-addons-pro' ),
				    'icon' => 'hm hm-image',
			    ],
		    ],
		    'toggle' => false,
		    'style_transfer' => true,
	    ];

	    $fields['mask_shape_default'] = [
		    'label' => _x( 'Default', 'Mask Image', 'happy-addons-pro' ),
		    'label_block' => true,
            'show_label' => false,
		    'type' => Image_Selector::TYPE,
		    'default' => 'shape1',
		    'options' => hapro_masking_shape_list( 'list' ),
		    'selectors' => [
			    '{{SELECTOR}}' => '-webkit-mask-image: url({{VALUE}}); mask-image: url({{VALUE}});',
		    ],
		    'selectors_dictionary' => hapro_masking_shape_list( 'url' ),
		    'condition' => [
			    'mask_shape' => 'default',
		    ],
		    'style_transfer' => true,
	    ];

	    $fields['mask_shape_custom'] = [
		    'label' => _x( 'Custom Shape', 'Mask Image', 'happy-addons-pro' ),
		    'type' => Controls_Manager::MEDIA,
            'show_label' => false,
            'description' => sprintf(
			    __( 'Note: Make sure svg support is enable to upload svg file. Or install %sSVG Support%s plugin to add svg support.', 'happy-addons-pro' ),
			    '<a href="https://wordpress.org/plugins/svg-support/" target="_blank">',
			    '</a>'
		    ),
		    'selectors' => [
			    '{{SELECTOR}}' => '-webkit-mask-image: url({{URL}}); mask-image: url({{URL}});',
		    ],
		    'condition' => [
			    'mask_shape' => 'custom',
		    ],
		    'style_transfer' => true,
	    ];

	    $fields['mask_position'] = [
		    'label' => _x( 'Position', 'Mask Image', 'happy-addons-pro' ),
		    'type' => Controls_Manager::SELECT,
		    'default' => 'center-center',
		    'options' => [
			    'center-center' => _x( 'Center Center', 'Mask Image', 'happy-addons-pro' ),
			    'center-left' => _x( 'Center Left', 'Mask Image', 'happy-addons-pro' ),
			    'center-right' => _x( 'Center Right', 'Mask Image', 'happy-addons-pro' ),
			    'top-center' => _x( 'Top Center', 'Mask Image', 'happy-addons-pro' ),
			    'top-left' => _x( 'Top Left', 'Mask Image', 'happy-addons-pro' ),
			    'top-right' => _x( 'Top Right', 'Mask Image', 'happy-addons-pro' ),
			    'bottom-center' => _x( 'Bottom Center', 'Mask Image', 'happy-addons-pro' ),
			    'bottom-left' => _x( 'Bottom Left', 'Mask Image', 'happy-addons-pro' ),
			    'bottom-right' => _x( 'Bottom Right', 'Mask Image', 'happy-addons-pro' ),
		    ],
            'selectors_dictionary' => [
                'center-center' => 'center center',
                'center-left' => 'center left',
                'center-right' => 'center right',
                'top-center' => 'top center',
                'top-left' => 'top left',
                'top-right' => 'top right',
                'bottom-center' => 'bottom center',
                'bottom-left' => 'bottom left',
                'bottom-right' => 'bottom right',
            ],
		    'selectors' => [
			    '{{SELECTOR}}' => '-webkit-mask-position: {{VALUE}}; mask-position: {{VALUE}};',
		    ],
		    'style_transfer' => true,
	    ];

	    $fields['mask_size'] = [
		    'label' => _x( 'Size', 'Mask Image', 'happy-addons-pro' ),
		    'type' => Controls_Manager::SELECT,
		    'default' => 'contain',
		    'options' => [
			    'auto' => _x( 'Auto', 'Mask Image', 'happy-addons-pro' ),
			    'cover' => _x( 'Cover', 'Mask Image', 'happy-addons-pro' ),
			    'contain' => _x( 'Contain', 'Mask Image', 'happy-addons-pro' ),
			    'initial' => _x( 'Custom', 'Mask Image', 'happy-addons-pro' ),
		    ],
		    'selectors' => [
			    '{{SELECTOR}}' => '-webkit-mask-size: {{VALUE}}; mask-size: {{VALUE}};',
		    ],
		    'style_transfer' => true,
	    ];

	    $fields['mask_custom_size'] = [
		    'label' => _x( 'Custom Size', 'Mask Image', 'happy-addons-pro' ),
		    'type' => Controls_Manager::SLIDER,
		    'responsive' => true,
		    'size_units' => [ 'px', 'em', '%', 'vw' ],
		    'range' => [
			    'px' => [
				    'min' => 0,
				    'max' => 1000,
			    ],
			    'em' => [
				    'min' => 0,
				    'max' => 100,
			    ],
			    '%' => [
				    'min' => 0,
				    'max' => 100,
			    ],
			    'vw' => [
				    'min' => 0,
				    'max' => 100,
			    ],
		    ],
		    'default' => [
			    'size' => 100,
			    'unit' => '%',
		    ],
		    'required' => true,
		    'selectors' => [
			    '{{SELECTOR}}' => '-webkit-mask-size: {{SIZE}}{{UNIT}}; mask-size: {{SIZE}}{{UNIT}};',
		    ],
		    'condition' => [
			    'mask_size' => 'initial',
		    ],
		    'style_transfer' => true,
	    ];

	    $fields['mask_repeat'] = [
		    'label' => _x( 'Repeat', 'Mask Image', 'happy-addons-pro' ),
		    'type' => Controls_Manager::SELECT,
		    'default' => 'no-repeat',
		    'options' => [
			    'repeat' => _x( 'Repeat', 'Mask Image', 'happy-addons-pro' ),
			    'repeat-x' => _x( 'Repeat-x', 'Mask Image', 'happy-addons-pro' ),
			    'repeat-y' => _x( 'Repeat-y', 'Mask Image', 'happy-addons-pro' ),
			    'space' => _x( 'Space', 'Mask Image', 'happy-addons-pro' ),
			    'round' => _x( 'Round', 'Mask Image', 'happy-addons-pro' ),
			    'no-repeat' => _x( 'No-repeat', 'Mask Image', 'happy-addons-pro' ),
			    'repeat-space' => _x( 'Repeat Space', 'Mask Image', 'happy-addons-pro' ),
			    'round-space' => _x( 'Round Space', 'Mask Image', 'happy-addons-pro' ),
			    'no-repeat-round' => _x( 'No-repeat Round', 'Mask Image', 'happy-addons-pro' ),
		    ],
            'selectors_dictionary' => [
                'repeat' => 'repeat',
                'repeat-x' => 'repeat-x',
                'repeat-y' => 'repeat-y',
                'space' => 'space',
                'round' => 'round',
                'no-repeat' => 'no-repeat',
                'repeat-space' => 'repeat space',
                'round-space' => 'round space',
                'no-repeat-round' => 'no-repeat round',
            ],
		    'selectors' => [
			    '{{SELECTOR}}' => '-webkit-mask-repeat: {{VALUE}}; mask-repeat: {{VALUE}};',
		    ],
		    'style_transfer' => true,
	    ];

        return $fields;
    }


    /**
     * Filter fields.
     *
     * Filter which controls to display, using `include`, `exclude`, `condition`
     * and `of_type` arguments.
     *
     * @since 1.2.2
     * @access protected
     *
     * @return array Control fields.
     */
    protected function filter_fields() {
        $fields = parent::filter_fields();

        $args = $this->get_args();

        foreach ( $fields as &$field ) {
            if ( isset( $field['of_type'] ) && ! in_array( $field['of_type'], $args['types'] ) ) {
                unset( $field );
            }
        }

        return $fields;
    }

    /**
     * Get default options.
     *
     * Retrieve the default options of the mask image control. Used to return the
     * default options while initializing the mask image control.
     *
     * @since 1.9.0
     * @access protected
     *
     * @return array Default mask image control options.
     */
    protected function get_default_options() {
        return [
	        'popover' => [
		        'starter_name' => 'ha-mask-image',
		        'starter_title' => _x( 'Image Masking ', 'Mask Image', 'happy-addons-pro' ).'<i style="color: #d5dadf;" class="hm hm-happyaddons"></i>',
		        'settings' => [
			        'render_type' => 'ui',
		        ],
	        ],
        ];
    }
}
