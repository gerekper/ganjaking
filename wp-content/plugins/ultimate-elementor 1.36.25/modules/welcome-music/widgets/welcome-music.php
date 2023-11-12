<?php
/**
 * UAEL Welcome Music.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\WelcomeMusic\Widgets;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use UltimateElementor\Base\Common_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Welcome_Music.
 */
class Welcome_Music extends Common_Widget {
	/**
	 * Retrieve Welcome Music Widget name.
	 *
	 * @since 1.35.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Welcome_Music' );
	}

	/**
	 * Retrieve Welcome Music Widget title.
	 *
	 * @since 1.35.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Welcome_Music' );
	}

	/**
	 * Retrieve Welcome Music Widget icon.
	 *
	 * @since 1.35.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Welcome_Music' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.35.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Welcome_Music' );
	}

	/**
	 * Retrieve the list of scripts the widget depends on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.35.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'uael-frontend-script',
		);
	}

	/**
	 * Register Welcome Music controls.
	 *
	 * @since 1.35.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'music_section',
			array(
				'label' => __( 'Music', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'music_type',
			array(
				'label'   => __( 'Music Source', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'uael' ),
					'link'    => __( 'Link', 'uael' ),
					'choose'  => __( 'Media Library', 'uael' ),

				),
			)
		);

		$this->add_control(
			'music_default',
			array(
				'label'     => __( 'Select Tune', 'uael' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'jingle_bells',
				'options'   => array(
					'jingle_bells' => __( 'Jingle Bells', 'uael' ),
					'winter_winds' => __( 'Winter Winds', 'uael' ),
					'wintery'      => __( 'Wintery', 'uael' ),
					'winter_loop'  => __( 'Winter Loop', 'uael' ),

				),
				'condition' => array(
					'music_type' => 'default',
				),
			)
		);

		$this->add_control(
			'music_link',
			array(
				'label'         => __( 'Link', 'uael' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com/song.mp3', 'uael' ),
				'description'   => __( 'NOTE: Add a direct link to the actual audio file', 'uael' ),
				'show_external' => false,
				'options'       => false,
				'condition'     => array(
					'music_type' => 'link',
				),
			)
		);

		$this->add_control(
			'music_choose',
			array(
				'label'      => __( 'Choose Music', 'uael' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => array(
					'active' => true,
				),
				'media_type' => 'audio',
				'condition'  => array(
					'music_type' => 'choose',
				),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'autoplay_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				/* translators: %s Google autoplay policy link */
				'raw'             => sprintf( __( 'NOTE: Autoplay will not work in Chrome and other Chromium-based browsers due to Google\'s autoplay %s', 'uael' ), '<a href="https://developer.chrome.com/blog/autoplay/" target="_blank" rel="noopener">policy</a>' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'autoplay' => 'yes',

				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'volume_range',
			array(
				'label'       => __( 'Volume (%)', 'uael' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'NOTE: If this option is set empty then the music will play as per the user\'s device volume', 'uael' ),
				'size_units'  => array( '%' ),
				'range'       => array(
					'%' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 5,
					),
				),
				'default'     => array(
					'unit' => '%',
					'size' => 15,
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_section',
			array(
				'label' => __( 'Play/Pause Button', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'display_button',
			array(
				'label'        => __( 'Display Button', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'uael-welcome-music-btn-display-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'button_position',
			array(
				'label'                => __( 'Position', 'uael' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'bottom_right',
				'options'              => array(
					'top_right'    => __( 'Top Right', 'uael' ),
					'top_left'     => __( 'Top Left', 'uael' ),
					'bottom_left'  => __( 'Bottom Left', 'uael' ),
					'bottom_right' => __( 'Bottom Right', 'uael' ),
				),
				'selectors_dictionary' => array(
					'top_right'    => 'top: 20px; right: 20px;',
					'top_left'     => 'top: 20px; left: 20px;',
					'bottom_left'  => 'bottom: 20px; left: 20px;',
					'bottom_right' => 'bottom: 20px; right: 20px;',
				),
				'selectors'            => array(
					'{{WRAPPER}} .uael-welcome-music-container' => '{{VALUE}}',
				),
				'condition'            => array(
					'display_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #uael-play-pause' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'display_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_size',
			array(
				'label'      => __( 'Size', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 25,
				),
				'selectors'  => array(
					'{{WRAPPER}} #uael-play-pause' => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'display_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'play_icon',
			array(
				'label'     => __( 'Play Button', 'uael' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-play-circle',
					'library' => 'solid',
				),
				'condition' => array(
					'display_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'pause_icon',
			array(
				'label'     => __( 'Pause Button', 'uael' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-pause-circle',
					'library' => 'solid',
				),
				'condition' => array(
					'display_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_zindex',
			array(
				'label'     => __( 'Z-Index', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 5,
				'selectors' => array(
					'{{WRAPPER}} .uael-welcome-music-container' => 'z-index: {{VALUE}};',
				),
				'condition' => array(
					'display_button' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$help_link_1 = UAEL_DOMAIN . 'docs/welcome-music/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin';

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_music_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'welcome_music_help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . $help_link_1 . ' target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Render Welcome Music output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.35.0
	 * @access protected
	 */
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$id            = $this->get_id();
		$is_editor     = Plugin::instance()->editor->is_edit_mode();
		$music_link    = '';
		$autoplay      = 'yes' === $settings['autoplay'];
		$loop          = 'yes' === $settings['loop'];
		$default_songs = array(
			'jingle_bells' => esc_url( 'https://opengameart.org/sites/default/files/JingleBells_0.mp3' ),
			'winter_winds' => esc_url( 'https://opengameart.org/sites/default/files/winter-wind_0.mp3' ),
			'wintery'      => esc_url( 'https://opengameart.org/sites/default/files/Christmas%20Special%204%20%28Guitar%29.wav' ),
			'winter_loop'  => esc_url( 'https://opengameart.org/sites/default/files/wintery%20loop.wav' ),
		);

		switch ( $settings['music_type'] ) {
			case 'default':
				$music_link = $default_songs[ $settings['music_default'] ];
				break;
			case 'link':
				$music_link = isset( $settings['music_link']['url'] ) ? $settings['music_link']['url'] : '';
				break;
			case 'choose':
				$music_link = isset( $settings['music_choose']['url'] ) ? $settings['music_link']['url'] : '';
				break;
			default:
		}

		if ( isset( $settings['volume_range']['size'] ) && ! empty( $settings['volume_range']['size'] ) ) {
			$this->add_render_attribute( 'music-container', 'data-volume', $settings['volume_range']['size'] );
		}

		if ( $is_editor ) {
			?>
			<div class="uael-builder-msg">
				<h5><?php esc_html_e( 'Welcome Music - ID ', 'uael' ); ?><?php echo esc_attr( $id ); ?></h5>
				<p><?php esc_html_e( 'Click here to edit the "Welcome Music" settings. This text will not be visible on frontend.', 'uael' ); ?></p>
			</div>
			<?php
		}
		?>
		<audio class="uael-welcome-track" data-autoplay="<?php echo esc_attr( $autoplay ); ?>" <?php echo $loop ? 'loop' : ''; ?>>
			<source src="<?php echo esc_url( $music_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" type="audio/mpeg" />
		</audio>
		<div class="uael-welcome-music-container" <?php echo wp_kses_post( $this->get_render_attribute_string( 'music-container' ) ); ?>>
			<div id="uael-play-pause" class="uael-play">
				<?php
				Icons_Manager::render_icon(
					$settings['play_icon'],
					array(
						'aria-hidden' => 'true',
						'class'       => 'play',
					)
				);
				?>
				<?php
				Icons_Manager::render_icon(
					$settings['pause_icon'],
					array(
						'aria-hidden' => 'true',
						'class'       => 'pause',
					)
				);
				?>
			</div>
		</div>
		<?php
	}
}
