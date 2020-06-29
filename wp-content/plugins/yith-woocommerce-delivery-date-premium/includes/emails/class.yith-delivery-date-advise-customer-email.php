<?php
if( !defined( 'ABSPATH' ) ) {
    exit;
}


if( !class_exists( 'WC_Email' ) ) {
    require_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
}

if( !class_exists( 'YITH_Delivery_Date_Advise_Customer_Email' ) ) {

    class YITH_Delivery_Date_Advise_Customer_Email extends WC_Email
    {

        public function __construct()
        {
            $this->id = 'yith_advise_user_delivery_email';
            $this->customer_email = true;
            $this->title = __( 'Shipped to carrier', 'yith-woocommerce-delivery-date' );
            $this->description = __( 'This email is sent to user when administrator has processed and sent the order to the carrier', 'yith-woocommerce-delivery-date' );

            $this->subject = get_option( 'ywcdd_mail_subject' );
            $this->heading = get_option( 'ywcdd_mail_subject' );

            $this->template_html = 'emails/email-delivery-date-advise-customers.php';
            $this->template_plain = 'emails/plain/email-delivery-date-advise-customers.php';

            add_action( 'yith_advise_user_delivery_email_notification', array( $this, 'trigger' ), 10, 1 );

            parent::__construct();
        }


        /**
         * @param WC_Order $order
         */
        public function trigger( $order )
        {

            if( empty( $order ) ) {
                return;
            }



            /**@var WP_User $user */
            $user = $order->get_user();
            $order_id =yit_get_prop( $order, 'id',true, 'edit' );

            if( $user ) {

                $user_email = $user->user_email;
                $username = $user->display_name;

            }
            else {

                $user_email = yit_get_prop( $order, '_billing_email' );
                $user_name = yit_get_prop( $order, '_billing_first_name' );
                $user_lastname = yit_get_prop( $order, '_billing_last_name' );
                $username = $user_lastname . ' ' . $user_name;
            }

            if( !empty( $user_email ) ) {

                $this->recipient = $user_email;
                $delivery_date = ywcdd_get_date_by_format( $order->get_meta( 'ywcdd_order_delivery_date' ) );
                $timeslot = $order->get_meta('ywcdd_order_slot_from').'-'.$order->get_meta('ywcdd_order_slot_to');
                $order_id = '#' . $order->get_order_number();
                $order_content = $this->get_order_content( $order );
                $order_url = sprintf( '<a href="%s">#%s</a>', $order->get_view_order_url(), $order->get_order_number() );

                if( version_compare( WC()->version, '3.2.0', '>=' ) ){
                    $this->placeholders['{customer_name}'] = $username ;
                    $this->placeholders['{customer_email}'] = $user_email ;
                    $this->placeholders['{delivery_date}'] = $delivery_date ;
                    $this->placeholders['{order_id}'] = $order_id ;
                    $this->placeholders['{delivery_time}'] = $timeslot;
                    $this->placeholders['{order_content}'] = $order_content ;
                    $this->placeholders['{order_url}'] = $order_url ;
                    $this->placeholders['{site_title}'] = $this->get_blogname() ;
                }else {
	                $this->find['username']      = '{customer_name}';
	                $this->find['user_email']    = '{customer_email}';
	                $this->find['site_title']    = '{site_title}';
	                $this->find['delivery_date'] = '{delivery_date}';
	                $this->find['order_id']      = '{order_id}';
	                $this->find['order_content'] = '{order_content}';
	                $this->find['order_url']     = '{order_url}';

	                $this->replace['username']      = $username;
	                $this->replace['user_email']    = $user_email;
	                $this->replace['site_title']    = $this->get_blogname();
	                $this->replace['delivery_date'] = $delivery_date;
	                $this->replace['order_id']      = $order_id;
	                $this->replace['order_content'] = $order_content;
	                $this->replace['order_url']     = $order_url;
                }

                if( !$this->is_enabled() || !$this->get_recipient() ) {
                    return;
                }


                $result = $this->send( $this->get_recipient(), $this->get_subject(), $this->format_string( $this->get_content() ), $this->get_headers(), $this->get_attachments() );

                if( $result ) {

	                $order->update_meta_data( '_ywcdd_email_sent', 'yes' );
	                $order->save();
                }
            }
        }

        /**
         * get order content
         * @author YITHEMES
         * @since 1.0.4
         * @param $order
         * @return string
         */
       public function get_order_content( $order )
        {

            $type = get_option( 'ywcdd_mail_type' );

            if( 'html' == $type ) {
                $email_order_details = 'emails/email-order-details.php';
                $plain_text = false;
            }
            else {
                $email_order_details = 'emails/plain/email-order-details.php';
                $plain_text = true;
            }

            ob_start();
            wc_get_template( $email_order_details, array( 'order' => $order, 'sent_to_admin' => false, 'plain_text' => $plain_text, 'email' => $this ) );
            return ob_get_clean();


        }

        public function get_content_html()
        {
            return wc_get_template_html( $this->template_html, array(
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => false,
                'email' => $this,
            ),
                YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );
        }

        public function get_content_plain()
        {
            return wc_get_template_html( $this->template_plain, array(
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => true,
                'email' => $this,
            ),
                YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );
        }

        /**
         * get_headers function.
         *
         * @access public
         * @return string
         */
        public function get_headers()
        {

            $headers = "Content-Type: " . $this->get_content_type() . "\r\n";

            return apply_filters( 'woocommerce_email_headers', $headers, $this->id, $this->object );
        }

        /**
         * check if this email is enabled
         * @author YITHEMES
         * @since 1.0.0
         * @return bool
         */
        public function is_enabled()
        {
            $enabled = get_option( 'ywcdd_mail_enabled' );

            return $enabled === 'yes';
        }

        /**
         * Admin Panel Options Processing - Saves the options to the DB
         *
         * @since   1.0.0
         * @return  boolean|null
         * @author  Alberto Ruggiero
         */
        public function process_admin_options()
        {

            woocommerce_update_options( $this->form_fields['email-settings'] );
        }

        /**
         * Setup email settings screen.
         *
         * @since   1.0.0
         * @return  string
         * @author  Alberto Ruggiero
         */
        public function admin_options()
        {
            ?>
            <table class="form-table">
                <?php woocommerce_admin_fields( $this->form_fields['email-settings'] ); ?>
            </table>

            <?php if( current_user_can( 'edit_themes' ) && ( !empty( $this->template_html ) || !empty( $this->template_plain ) ) ) { ?>
            <div id="template">
                <?php
                $templates = array(
                    'template_html' => __( 'HTML template', 'woocommerce' ),
                    'template_plain' => __( 'Plain text template', 'woocommerce' )
                );

                foreach ( $templates as $template_type => $title ) :
                    $template = $this->get_template( $template_type );

                    if( empty( $template ) ) {
                        continue;
                    }


                    $local_file = $this->get_theme_template_file( $template );
                    $core_file = YITH_DELIVERY_DATE_TEMPLATE_PATH . '/' . $template;
                    $template_file = apply_filters( 'woocommerce_locate_core_template', $core_file, $template, YITH_DELIVERY_DATE_TEMPLATE_PATH );
                    $template_dir = apply_filters( 'woocommerce_template_directory', 'woocommerce', $template );
                    ?>
                    <div class="template <?php echo $template_type; ?>">

                        <h4><?php echo wp_kses_post( $title ); ?></h4>

                        <?php if( file_exists( $local_file ) ) { ?>

                            <p>
                                <a href="#" class="button toggle_editor"></a>

                                <?php if( is_writable( $local_file ) ) : ?>
                                    <a href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'move_template', 'saved' ), add_query_arg( 'delete_template', $template_type ) ), 'woocommerce_email_template_nonce', '_wc_email_nonce' ) ); ?>"
                                       class="delete_template button"><?php _e( 'Delete template file', 'woocommerce' ); ?></a>
                                <?php endif; ?>

                                <?php printf( __( 'This template has been overridden by your theme and can be found in: <code>%s</code>.', 'woocommerce' ), trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ); ?>
                            </p>

                            <div class="editor" style="display:none">
                                <textarea class="code" cols="25" rows="20"
                                          <?php if( !is_writable( $local_file ) ) : ?>readonly="readonly"
                                          disabled="disabled"
                                          <?php else : ?>data-name="<?php echo $template_type . '_code'; ?>"<?php endif; ?>><?php echo file_get_contents( $local_file ); ?></textarea>
                            </div>

                        <?php }
                        elseif( file_exists( $template_file ) ) { ?>

                            <p>
                                <a href="#" class="button toggle_editor"></a>

                                <?php if( ( is_dir( get_stylesheet_directory() . '/' . $template_dir . '/emails/' ) && is_writable( get_stylesheet_directory() . '/' . $template_dir . '/emails/' ) ) || is_writable( get_stylesheet_directory() ) ) { ?>
                                    <a href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'delete_template', 'saved' ), add_query_arg( 'move_template', $template_type ) ), 'woocommerce_email_template_nonce', '_wc_email_nonce' ) ); ?>"
                                       class="button"><?php _e( 'Copy file to theme', 'woocommerce' ); ?></a>
                                <?php } ?>

                                <?php printf( __( 'To override and edit this email template, copy <code>%s</code> to your theme folder: <code>%s</code>.', 'woocommerce' ), plugin_basename( $template_file ), trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ); ?>
                            </p>

                            <div class="editor" style="display:none">
                                <textarea class="code" readonly="readonly" disabled="disabled" cols="25"
                                          rows="20"><?php echo file_get_contents( $template_file ); ?></textarea>
                            </div>

                        <?php }
                        else { ?>

                            <p><?php _e( 'File not found.', 'woocommerce' ); ?></p>

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
        public function init_form_fields()
        {
            $this->form_fields = include( YITH_DELIVERY_DATE_DIR . '/plugin-options/email-settings-options.php' );
        }

        public function get_email_type()
        {

            return get_option( 'ywcdd_mail_type' );
        }

        /**
         * get content type
         * @author YITHEMES
         * @since 1.0.0
         * @return string
         */
        public function get_content_type( $default_content_type = '')
        {
            $type = get_option( 'ywcdd_mail_type' );

            switch ( $type ) {
                case 'html' :
                    return 'text/html';
                default :
                    return 'text/plain';
            }
        }
    }
}
return new YITH_Delivery_Date_Advise_Customer_Email();
