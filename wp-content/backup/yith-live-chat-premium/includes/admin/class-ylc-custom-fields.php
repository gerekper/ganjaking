<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class YLC_Custom_Fields {

	/**
	 * @var YIT_Plugin_Panel
	 */
	private $panel;

	/**
	 * Constructor
	 *
	 * @since   1.0.0
	 *
	 * @param $panel YIT_Plugin_Panel
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public function __construct( $panel ) {

		$this->panel = $panel;

		add_action( 'yit_panel_upload-avatar', array( $this, 'output_upload_avatar' ), 10, 3 );
		add_action( 'yit_panel_email-field', array( $this, 'output_email_field' ), 10, 3 );
		add_action( 'yit_panel_page-select', array( $this, 'output_page_select' ), 10, 3 );
		add_action( 'yit_panel_checkbox-list', array( $this, 'output_checkbox_list' ), 10, 1 );
		add_action( 'yit_panel_firebase-rules', array( $this, 'output_firebase_rules' ), 10, 1 );

	}

	/**
	 * Outputs a custom avatar field template in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option            array
	 * @param   $db_value          string
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public function output_upload_avatar( $option, $db_value ) {

		$file = $db_value != '' ? $db_value : YLC_ASSETS_URL . '/images/default-avatar-admin.png';

		$id   = $this->panel->get_id_field( $option['id'] );
		$name = $this->panel->get_name_field( $option['id'] );
		?>
        <div id="<?php echo $id ?>-container" class="yit_options yith-plugin-fw-field-wrapper " <?php echo yith_panel_field_deps_data( $option, $this->panel ) ?>>
            <div class="option">
                <input type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $db_value == '1' ? '' : esc_attr( $db_value ) ?>" class="custom_upload_img_url" />
                <input type="button" value="<?php esc_html_e( 'Upload', 'yith-plugin-fw' ) ?>" id="<?php echo $id ?>-button" class="upload_button button button-secondary yith-plugin-fw-upload-button" />
            </div>
            <div class="clear"></div>
            <span class="description"><?php echo $option['desc'] ?></span>
        </div>
        <div class="custom_upload_img_preview" style="margin-top:10px;">
            <img src="<?php echo $file; ?>" style="width:60px" />
        </div>
        <script type="text/javascript">
            (function ($) {

                $('.plugin-option .custom_upload_img_url').change(function () {
                    var url = $(this).val();
                    var re = new RegExp("(http|ftp|https)://[a-zA-Z0-9@?^=%&amp;:/~+#-_.]*.(gif|jpg|jpeg|png|ico)");

                    var preview = $('.custom_upload_img_preview img');
                    if (url !== '') {
                        if (re.test(url)) {
                            preview.attr('src', url)

                        } else {
                            preview.attr('src', '<?php echo YLC_ASSETS_URL . '/images/default-avatar-admin.png' ?>');
                        }
                    } else {
                        preview.attr('src', '<?php echo YLC_ASSETS_URL . '/images/default-avatar-admin.png' ?>');
                    }

                }).change();

            }(window.jQuery || window.Zepto));
        </script>
		<?php
	}

	/**
	 * Outputs a custom email field template in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option            array
	 * @param   $db_value          string
	 * @param   $custom_attributes string
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public function output_email_field( $option, $db_value, $custom_attributes ) {

		$id   = $this->panel->get_id_field( $option['id'] );
		$name = $this->panel->get_name_field( $option['id'] );
		?>
        <div id="<?php echo $id ?>-container" class="yit_options yith-plugin-fw-field-wrapper " <?php echo yith_panel_field_deps_data( $option, $this->panel ) ?>>
            <div class="option">
                <input type="email" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo esc_attr( $db_value ) ?>" <?php echo $custom_attributes ?> />
            </div>
            <span class="description"><?php echo $option['desc'] ?></span>

            <div class="clear"></div>
        </div>
		<?php
	}

	/**
	 * Outputs a custom email field template in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option            array
	 * @param   $db_value          string
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public function output_page_select( $option, $db_value ) {

		$id   = $this->panel->get_id_field( $option['id'] );
		$name = $this->panel->get_name_field( $option['id'] );

		$selections     = ( ! is_array( $db_value ) ) ? array() : $db_value;
		$excluded_pages = apply_filters( 'ylc_excluded_pages', array() );

		if ( function_exists( 'WC' ) ) {
			$excluded_pages[] = wc_get_page_id( 'shop' );
		}

		$args = array(
			'sort_column' => 'menu_order',
			'post_type'   => 'page',
			'post_status' => 'publish',
			'exclude'     => $excluded_pages
		);

		$pages = get_pages( $args );
		?>

        <div id="<?php echo $id ?>-container" class="yit_options yith-plugin-fw-field-wrapper " <?php echo yith_panel_field_deps_data( $option, $this->panel ) ?>>
            <div class="option">

                <select multiple="multiple" id="<?php echo $id ?>" name="<?php echo $name; ?>[]" style="width:350px" data-placeholder="<?php esc_attr_e( 'Choose pages&hellip;', 'yith-live-chat' ); ?>" title="<?php esc_attr_e( 'Pages', 'yith-live-chat' ) ?>" class="ylc-select">
					<?php
					if ( ! empty( $pages ) ) {
						foreach ( $pages as $page ) {
							echo '<option value="' . esc_attr( $page->ID ) . '" ' . selected( in_array( $page->ID, $selections ), true, false ) . '>' . $page->post_title . '</option>';
						}
					}
					?>
                </select>
                <br />
                <a class="select_all button button-primary" href="#"><?php esc_html_e( 'Select all', 'yith-live-chat' ); ?></a>
                <a class="select_none button button-secondary" href="#"><?php esc_html_e( 'Select none', 'yith-live-chat' ); ?></a>

            </div>
            <span class="description"><?php echo ( $option['desc'] ) ? $option['desc'] : ''; ?></span>

            <div class="clear"></div>
        </div>
		<?php
	}

	/**
	 * Outputs a custom checkbox list template in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option array
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public function output_checkbox_list( $option ) {

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$role_names = $wp_roles->get_names();

		?>
        <div class="ylc-checkbox-list">
            <div class="option">
				<?php foreach ( $role_names as $role_slug => $name ) : ?>

					<?php if ( $role_slug == 'administrator' || $role_slug == 'ylc_chat_op' ) {
						continue;
					} ?>

					<?php $role = get_role( $role_slug ) ?>
                    <label>
                        <input
                            name="ylc_enable[<?php echo $role_slug ?>]"
                            id="ylc_enable_<?php echo $role_slug; ?>"
                            type="checkbox"
                            value="1"
							<?php checked( $role->has_cap( 'answer_chat' ) ); ?>
                        />
						<?php echo $name ?>
                    </label>
				<?php endforeach; ?>
            </div>
            <span class="description"><?php echo $option['desc'] ?></span>

            <div class="clear"></div>
        </div>
		<?php

	}

	/**
	 * Outputs a custom firebase rules template in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option array
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public function output_firebase_rules( $option ) {

		?>
        <div class="yit_options yith-plugin-fw-field-wrapper ">
            <div class="option">
                <textarea class="yith-plugin-fw-textarea" rows="5" cols="50" style="width:100%; height: 280px" disabled><?php echo $option['std'] ?></textarea>
            </div>
            <span class="description"><?php echo $option['desc'] ?></span>

            <div class="clear"></div>
        </div>
		<?php

	}

}

