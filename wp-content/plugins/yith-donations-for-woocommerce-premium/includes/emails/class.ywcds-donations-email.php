<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Donations_Email' ) ){

    class YITH_WC_Donations_Email extends  WC_Email{


        protected  $donation_list;
        public function  __construct(){

            $this->title            = __( 'YITH Donations','yith-donations-for-woocommerce' );
            $this->description = __('This is the email sent to the customer from the admin with the YITH Donations for WooCommerce plugin', 'yith-donations-for-woocommerce');
            $this->template_html 	= 'emails/donation.php';
            $this->template_plain 	= 'emails/plain/donation.php';
            $this->id = 'ywcds_email';
            $this->customer_email = true;
            parent::__construct();
        }

        /**
         * Trigger email send
         *
         * @since   1.0.0
         * @param   $order_id int the order id
         * @param   $item_list array the list of items to review
         * @param   $days_ago int number of days after order completion
         * @param   $test_email
         * @return  void
         * @author  Alberto Ruggiero
         */
        public function trigger( $order_id, $donation_list,  $test_email = '' ) {

	        if ( $this->is_enabled() ) {
		        $this->email_type            = get_option( 'ywcds_mail_type' );
		        $this->heading               = get_option( 'ywcds_mail_subject' );
		        $this->subject               = get_option( 'ywcds_mail_subject' );
		        $this->donation_list         = $donation_list;
		        $this->find['site-title']    = '{site_title}';
		        $this->replace['site-title'] = $this->get_blogname();


		        if ( $order_id ) {

			        $this->object    = wc_get_order( $order_id );
			        $this->recipient =  $this->object->get_billing_email();

		        } else {

			        $this->object    = 0;
			        $this->recipient = $test_email;

		        }

		        if ( ! $this->get_recipient() ) {
			        return;
		        }

		        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), "" );

	        }
        }

        /**
         * Send the email.
         *
         * @since   1.0.3
         * @param   string $to
         * @param   string $subject
         * @param   string $message
         * @param   string $headers
         * @param   string $attachments
         * @return  bool
         * @author  Alberto Ruggiero
         */
        public function send( $to, $subject, $message, $headers, $attachments ) {

            add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
            add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
            add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

            $message = apply_filters( 'woocommerce_mail_content', $this->style_inline( $message ) );

            $return = wp_mail( $to, $subject, $message, $headers, $attachments );

            remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
            remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
            remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

            return $return;

        }

        /**
         * Get HTML content
         *
         * @since   1.0.0
         * @return  string
         * @author  Alberto Ruggiero
         */
        function get_content_html() {
            ob_start();
            wc_get_template( $this->template_html, array(
                'order' 		=> $this->object,
                'donation_list' =>  $this->donation_list,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false
            ), YWCDS_TEMPLATE_PATH , YWCDS_TEMPLATE_PATH );
            return ob_get_clean();
        }

        /**
         * Get Plain content
         *
         * @since   1.0.0
         * @return  string
         * @author  Alberto Ruggiero
         */
        function get_content_plain() {
            ob_start();
            wc_get_template( $this->template_plain, array(
                'order' 		=> $this->object,
                'donation_list' =>  $this->donation_list,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => true
            ), YWCDS_TEMPLATE_PATH , YWCDS_TEMPLATE_PATH );
            return ob_get_clean();
        }

        /**
         * Admin Panel Options Processing - Saves the options to the DB
         *
         * @since   1.0.0
         * @return  boolean|null
         * @author  Alberto Ruggiero
         */
        function process_admin_options() {
            woocommerce_update_options( $this->form_fields['mail'] );
        }

        /**
         * Setup email settings screen.
         *
         * @since   1.0.0
         * @return  string
         * @author  Alberto Ruggiero
         */
        public function admin_options() {
            ?>
            <table class="form-table">
                <?php woocommerce_admin_fields( $this->form_fields['mail'] ); ?>
            </table>

            <?php if ( current_user_can( 'edit_themes' ) && ( ! empty( $this->template_html ) || ! empty( $this->template_plain ) ) ) { ?>
                <div id="template">
                    <?php
                    $templates = array(
                        'template_html'  => __( 'HTML template', 'woocommerce' ),
                        'template_plain' => __( 'Plain text template', 'woocommerce' )
                    );

                    foreach ( $templates as $template_type => $title ) :
                        $template = $this->get_template( $template_type );

                        if ( empty( $template ) ) {
                            continue;
                        }

                        $local_file    = $this->get_theme_template_file( $template );
                        $core_file     = YWCDS_TEMPLATE_PATH . $template;
                        $template_file = apply_filters( 'woocommerce_locate_core_template', $core_file, $template,  YWCDS_TEMPLATE_PATH );
                        $template_dir  = apply_filters( 'woocommerce_template_directory', 'woocommerce', $template );
                        ?>
                        <div class="template <?php echo $template_type; ?>">

                            <h4><?php echo wp_kses_post( $title ); ?></h4>

                            <?php if ( file_exists( $local_file ) ) { ?>

                                <p>
                                    <a href="#" class="button toggle_editor"></a>

                                    <?php if ( is_writable( $local_file ) ) : ?>
                                        <a href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'move_template', 'saved' ), add_query_arg( 'delete_template', $template_type ) ), 'woocommerce_email_template_nonce', '_wc_email_nonce' ) ); ?>" class="delete_template button"><?php _e( 'Delete template file', 'woocommerce' ); ?></a>
                                    <?php endif; ?>

                                    <?php printf( __( 'This template has been overridden by your theme and can be found in: <code>%s</code>.', 'woocommerce' ), trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ); ?>
                                </p>

                                <div class="editor" style="display:none">
                                    <textarea class="code" cols="25" rows="20" <?php if ( ! is_writable( $local_file ) ) : ?>readonly="readonly" disabled="disabled"<?php else : ?>data-name="<?php echo $template_type . '_code'; ?>"<?php endif; ?>><?php echo file_get_contents( $local_file ); ?></textarea>
                                </div>

                            <?php } elseif ( file_exists( $template_file ) ) { ?>

                                <p>
                                    <a href="#" class="button toggle_editor"></a>

                                    <?php if ( ( is_dir( get_stylesheet_directory() . '/' . $template_dir . '/emails/' ) && is_writable( get_stylesheet_directory() . '/' . $template_dir . '/emails/' ) ) || is_writable( get_stylesheet_directory() ) ) { ?>
                                        <a href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'delete_template', 'saved' ), add_query_arg( 'move_template', $template_type ) ), 'woocommerce_email_template_nonce', '_wc_email_nonce' ) ); ?>" class="button"><?php _e( 'Copy file to theme', 'woocommerce' ); ?></a>
                                    <?php } ?>

                                    <?php printf( __( 'To override and edit this email template copy <code>%s</code> to your theme folder: <code>%s</code>.', 'woocommerce' ), plugin_basename( $template_file ) , trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ); ?>
                                </p>

                                <div class="editor" style="display:none">
                                    <textarea class="code" readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo file_get_contents( $template_file ); ?></textarea>
                                </div>

                            <?php } else { ?>

                                <p><?php _e( 'File was not found.', 'woocommerce' ); ?></p>

                            <?php } ?>

                        </div>
                        <?php
                    endforeach;
                    ?>
                </div>
                <?php
                wc_enqueue_js( "
				jQuery( 'select.email_type' ).change( function() {

					var val = jQuery( this ).val();

					jQuery( '.template_plain, .template_html' ).show();

					if ( val != 'multipart' && val != 'html' ) {
						jQuery('.template_html').hide();
					}

					if ( val != 'multipart' && val != 'plain' ) {
						jQuery('.template_plain').hide();
					}

				}).change();

				var view = '" . esc_js( __( 'View template', 'woocommerce' ) ) . "';
				var hide = '" . esc_js( __( 'Hide template', 'woocommerce' ) ) . "';

				jQuery( 'a.toggle_editor' ).text( view ).toggle( function() {
					jQuery( this ).text( hide ).closest(' .template' ).find( '.editor' ).slideToggle();
					return false;
				}, function() {
					jQuery( this ).text( view ).closest( '.template' ).find( '.editor' ).slideToggle();
					return false;
				} );

				jQuery( 'a.delete_template' ).click( function() {
					if ( window.confirm('" . esc_js( __( 'Are you sure you want to delete this template file?', 'woocommerce' ) ) . "') ) {
						return true;
					}

					return false;
				});

				jQuery( '.editor textarea' ).change( function() {
					var name = jQuery( this ).attr( 'data-name' );

					if ( name ) {
						jQuery( this ).attr( 'name', name );
					}
				});
			" );
            }
        }

        /**
         * Initialise Settings Form Fields
         *
         * @since   1.0.0
         * @return  void
         * @author  Alberto Ruggiero
         */
        function init_form_fields() {
            $this->form_fields = include( YWCDS_DIR . '/plugin-options/mail-options.php' );
        }

        /**
         * Get email content type.
         *
         * @since   1.1.4
         * @return  string
         * @author  Alberto Ruggiero
         */
        public function get_content_type( $default_content_type = '') {
            switch ( get_option( 'ywcds_mail_type' ) ) {
                case 'html' :
                    return 'text/html';
                default :
                    return 'text/plain';
            }
        }

        /**
         * Checks if this email is enabled and will be sent.
         * @since   1.1.4
         * @return  bool
         * @author  Alberto Ruggiero
         */
        public function is_enabled() {
            return ( get_option( 'ywcds_mail_enabled' ) === 'yes' );
        }

    }
}

return new YITH_WC_Donations_Email();