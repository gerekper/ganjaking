<?php
namespace Essential_Addons_Elementor\Pro\Extensions;

if (!defined('ABSPATH')) {
    exit;
}

use \Elementor\Controls_Manager;
use \Elementor\Core\Responsive\Responsive;
use \Elementor\Repeater;
use \Elementor\Utils;

class EAEL_Parallax_Section
{

    public function __construct()
    {
        add_action('elementor/element/section/section_layout/after_section_end', array($this, 'register_controls'), 10);
        add_action('elementor/frontend/section/after_render', array($this, 'after_render'), 10);

        //Elementor Flexbox Container
        add_action('elementor/element/container/section_layout/after_section_end', array($this, 'register_controls'), 10);
        add_action('elementor/frontend/container/after_render', array($this, 'after_render'), 10);
    }

    public function register_controls($element)
    {
        $element->start_controls_section('eael_parallax_section',
            [
                'label' => __('<i class="eaicon-logo"></i> Parallax', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_LAYOUT,
            ]
        );

        $element->add_control('eael_parallax_switcher',
            [
                'label' => __('Enable Parallax', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $element->add_control('eael_parallax_update',
            [
                'label' => '<div class="elementor-update-preview" style="display: block;"><div class="elementor-update-preview-button-wrapper" style="display:block;"><button class="elementor-update-preview-button elementor-button elementor-button-success" style="background: #d30c5c; margin: 0 auto; display:block;">Apply Changes</button></div><div class="elementor-update-preview-title" style="display:block;text-align:center;margin-top: 10px;">Update changes to page</div></div>',
                'type' => Controls_Manager::RAW_HTML,
                'condition' => [
                    'eael_parallax_switcher' => 'yes',
                ],
            ]
        );

        $element->add_control('eael_parallax_type',
            [
                'label' => __('Type', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'scroll' => __('Scroll', 'essential-addons-elementor'),
                    'scroll-opacity' => __('Scroll with Fade', 'essential-addons-elementor'),
                    'opacity' => __('Fade', 'essential-addons-elementor'),
                    'scale' => __('Zoom', 'essential-addons-elementor'),
                    'scale-opacity' => __('Zoom with Fade', 'essential-addons-elementor'),
                    'automove' => __('In-Motion', 'essential-addons-elementor'),
                    'multi' => __('Multi-Layered', 'essential-addons-elementor'),
                ],
                'label_block' => 'true',
                'condition' => [
                    'eael_parallax_switcher' => 'yes',
                ],
            ]
        );

        $element->add_control('eael_parallax_auto_type',
            [
                'label' => __('Motion Direction', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 11,
                'options' => [
                    11 => __('Left to Right', 'essential-addons-elementor'),
                    'right' => __('Right to Left', 'essential-addons-elementor'),
                    'top' => __('Top to Bottom', 'essential-addons-elementor'),
                    'bottom' => __('Bottom to Top', 'essential-addons-elementor'),
                ],
                'condition' => [
                    'eael_parallax_type' => 'automove',
                    'eael_parallax_switcher' => 'yes',
                ],
            ]
        );

        $element->add_control('eael_parallax_speed',
            [
                'label' => __('Parallax Speed', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 2,
                'step' => 0.1,
                'default' => 1.3,
                'condition' => [
                    'eael_parallax_type!' => ['automove', 'multi'],
                    'eael_parallax_switcher' => 'yes',
                ],
            ]
        );

        $element->add_control('eael_auto_speed',
            [
                'label' => __('Motion Speed', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 4,
                'min' => 0,
                'max' => 150,
                'condition' => [
                    'eael_parallax_type' => 'automove',
                    'eael_parallax_switcher' => 'yes',
                ],
            ]
        );

        $element->add_control('eael_parallax_android_support',
            [
                'label' => esc_html__('Parallax on Android Devices', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'eael_parallax_type!' => ['automove', 'multi'],
                    'eael_parallax_switcher' => 'yes',
                ],
            ]
        );

        $element->add_control('eael_parallax_ios_support',
            [
                'label' => esc_html__('Parallax on iOS Devices', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'eael_parallax_type!' => ['automove', 'multi'],
                    'eael_parallax_switcher' => 'yes',
                ],
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control('eael_parallax_layer_image',
            [
                'label' => __('Choose Image', 'essential-addons-elementor'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'label_block' => true,
                'ai' => [
                    'active' => false,
                ],
            ]
        );

        $repeater->add_control('eael_parallax_layer_mouse',
            [
                'label' => esc_html__('Mouse Hover Interaction', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $repeater->add_control('eael_parallax_layer_rate',
            [
                'label' => esc_html__('Moving Intensity', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => -10,
                'min' => -20,
                'max' => 20,
                'step' => 1,
                'condition' => [
                    'eael_parallax_layer_mouse' => 'yes',
                ],
            ]
        );

        $repeater->add_control('eael_parallax_layer_hor_pos',
            [
                'label' => esc_html__('Horizontal Position', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 50,
                'min' => 0,
                'max' => 100,
            ]
        );

        $repeater->add_control('eael_parallax_layer_ver_pos',
            [
                'label' => esc_html__('Vertical Position', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 50,
                'min' => 0,
                'max' => 100,
            ]
        );

        $repeater->add_control('eael_parallax_layer_back_size',
            [
                'label' => esc_html__('Image Size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'auto',
                'options' => [
                    'auto' => esc_html__('Auto', 'essential-addons-elementor'),
                    'cover' => esc_html__('Cover', 'essential-addons-elementor'),
                    'contain' => esc_html__('Contain', 'essential-addons-elementor'),
                ],
            ]
        );

        $repeater->add_control('eael_parallax_layer_z_index',
            [
                'label' => __('z-index', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
            ]
        );

	    $element->add_control( 'eael_parallax_layers_list',
		    [
			    'label'          => '',
			    'type'           => Controls_Manager::REPEATER,
			    'fields'         => $repeater->get_controls(),
			    'style_transfer' => false,
			    'condition'      => [
				    'eael_parallax_switcher' => 'yes',
				    'eael_parallax_type'     => 'multi',
			    ],
		    ]
	    );

        $element->end_controls_section();
    }

    public function after_render($element)
    {
        $settings = $element->get_settings_for_display();
        $parallax = isset($settings['eael_parallax_type']) ? $settings['eael_parallax_type'] : '';

        if ( ( 'container' === $element->get_type() || 'section' === $element->get_type() ) && isset($parallax)
            && '' !== $parallax && 'yes' === $element->get_settings('eael_parallax_switcher')
        ) {
            $android = (isset($settings['eael_parallax_android_support']) && $settings['eael_parallax_android_support'] == 'yes') ? 0 : 1;
            $ios = (isset($settings['eael_parallax_ios_support']) && $settings['eael_parallax_ios_support'] == 'yes') ? 0 : 1;
            $speed = !empty($settings['eael_parallax_speed']) ? $settings['eael_parallax_speed'] : 0.5;
            $auto_speed = !empty($settings['eael_auto_speed']) ? $settings['eael_auto_speed'] : 3;
            $repeater_list = (isset($settings['eael_parallax_layers_list']) && $settings['eael_parallax_layers_list']) ? $settings['eael_parallax_layers_list'] : array();

            ?>
			<script>
				(function($) {
                    "use strict";

                    var target = $('.elementor-element-<?php echo $element->get_id(); ?>');

                    <?php if ('automove' != $parallax && 'multi' != $parallax): ?>

                    var EaelParallaxElement = {

						init: function() {
							elementorFrontend.hooks.addAction('frontend/element_ready/global', EaelParallaxElement.initWidget);
						},
                        responsiveParallax: function(){
                            var android = <?php echo $android; ?>,
                                ios = <?php echo $ios; ?>;
                            switch( true || 1 ){
                                case android && ios:
                                    return /iPad|iPhone|iPod|Android/;
                                    break;
                                case android && ! ios:
                                    return /Android/;
                                    break;
                                case ! android && ios:
                                    return /iPad|iPhone|iPod/;
                                    break;
                                case ( ! android && ! ios ):
                                    return null;
                            }
                        },
						initWidget: function( $scope ) {

							target.jarallax({
								type: '<?php echo $parallax; ?>',
								speed: <?php echo $speed; ?>,
								keepImg: true,
                                disableParallax: EaelParallaxElement.responsiveParallax(),
							});
						}

					};

					$( window ).on('elementor/frontend/init', EaelParallaxElement.init);

                    <?php elseif ('multi' == $parallax): $counter = 0;?>
		                        target.addClass('eael-prallax-multi');

		                            <?php foreach ($repeater_list as $layer) {
                    $counter = $counter + 1;?>
		                                var backgroundImage = '<?php echo $layer['eael_parallax_layer_image']['url']; ?>',
		                                    mouseParallax   = ' data-parallax="' + <?php echo ($layer['eael_parallax_layer_mouse'] == 'yes') ? 'true' : 'false'; ?> +'" ',
		                                    mouseRate       = ' data-rate="' + <?php echo $layer['eael_parallax_layer_rate']; ?> + '" ';

		                                $('<div id="eael-parallax-layer-<?php echo $element->get_id() . '-' . $counter; ?>"' + mouseParallax + mouseRate +' class="eael-parallax-layer"></div>').prependTo( target ).css({
		                                    'z-index'               : <?php echo !empty($layer['eael_parallax_layer_z_index']) ? $layer['eael_parallax_layer_z_index'] : 0; ?>,
		                                    'background-image'      : 'url(' + backgroundImage + ')',
		                                    'background-size'       : '<?php echo $layer['eael_parallax_layer_back_size']; ?>',
		                                    'background-position-x' : <?php echo !empty($layer['eael_parallax_layer_hor_pos']) ? $layer['eael_parallax_layer_hor_pos'] : 50; ?> + '%',
		                                    'background-position-y' : <?php echo !empty($layer['eael_parallax_layer_ver_pos']) ? $layer['eael_parallax_layer_ver_pos'] : 50; ?> + '%'
		                                });
		                            <?php };?>

		                            if( $(window).outerWidth() > <?php echo esc_js(Responsive::get_breakpoints()['md']); ?> ) {
		                                    $('.elementor-element-<?php echo $element->get_id(); ?>').mousemove( function( e ) {

		                                    $( this ).find('.eael-parallax-layer[data-parallax="true"]').each(function( index,element ){
		                                        $( this ).parallax( $( this ).data('rate'), e );
		                                    });
		                                });
		                            }
		                        <?php else: ?>

                        target.css('background-position','0px 0px');

						<?php if (11 == $settings['eael_parallax_auto_type']): ?>
                            var position = parseInt( target.css('background-position-x') );

                            setInterval( function() {
                                position = position + <?php echo $auto_speed; ?>;
                                target.css("backgroundPosition", position + "px 0");
                            },70 );

                    	<?php elseif ('right' == $settings['eael_parallax_auto_type']): ?>

                    	var position = parseInt( target.css('background-position-x') );

                            setInterval( function() {

                                position = position - <?php echo $auto_speed; ?>;

                                target.css("backgroundPosition", position + "px 0");

                            },70 );

                    	<?php elseif ('top' == $settings['eael_parallax_auto_type']): ?>

                    	var position = parseInt(target.css('background-position-y'));

                            setInterval( function() {

                                position = position + <?php echo $auto_speed; ?>;

                                target.css("backgroundPosition", "0 " + position + "px");

                            },70 );

                    	<?php elseif ('bottom' == $settings['eael_parallax_auto_type']): ?>

                    	var position = parseInt( target.css('background-position-y') );

                            setInterval( function() {

                                position = position - <?php echo $auto_speed; ?>;
                                target.css("backgroundPosition", "0 " + position + "px");

                            },70 );

                    	<?php endif;?>
                    <?php endif;?>
				}( jQuery ) );
			</script>
		<?php }
    }
}