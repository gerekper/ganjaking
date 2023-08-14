<?php

namespace WPDeveloper\BetterDocsPro\REST;

use WP_REST_Request;
use WPDeveloper\BetterDocs\Core\BaseAPI;

class InstantAnswer extends BaseAPI {
    public function register() {
        $this->post( '/ask', [$this, 'ask'] );
        $this->post( '/feedback', [$this, 'feedback'] );
    }

    public function sanitize( $post_data = [] ) {
        if ( ! empty( $post_data ) ) {
            $sanitized_data = [];
            foreach ( $post_data as $key => $data ) {
                if ( $key === 'email' ) {
                    $sanitized_data[$key] = sanitize_email( $data );
                } else {
                    $sanitized_data[$key] = esc_html( stripslashes( $data ) );
                }
            }

            return $sanitized_data;
        }
        return [];
    }

    public function ready_subject( $sanitized_data, $ask_subject ) {
        $ask_subject = ! empty( $ask_subject ) ? $ask_subject : '[ia_subject]';

        $_subject_data = '';
        if ( isset( $sanitized_data['subject'] ) ) {
            $_subject_data = $sanitized_data['subject'];
        }
        $subject = str_replace( '[ia_subject]', $_subject_data, $ask_subject );

        $_email_data = '';
        if ( isset( $sanitized_data['email'] ) ) {
            $_email_data = $sanitized_data['email'];
        }
        $subject = str_replace( '[ia_email]', $_email_data, $subject );

        $_name_data = '';
        if ( isset( $sanitized_data['name'] ) ) {
            $_name_data = $sanitized_data['name'];
        }
        $subject = str_replace( '[ia_name]', $_name_data, $subject );

        return $subject;
    }

    /**
     * This method is responsible for sending emails to site admin from ask form in IA.
     * @param mixed $request
     * @return mixed
     */
    public function ask( WP_REST_Request $request ) {
        $sanitized_data = $this->sanitize( $_POST );
        if ( empty( $sanitized_data ) || ! isset( $sanitized_data['email'] ) ) {
            return;
        }

        $ask_subject = $this->settings->get( 'ask_subject', '[ia_subject]' );
        $to          = $this->settings->get( 'ask_email', get_bloginfo( 'admin_email' ) );

        $subject = html_entity_decode( $this->ready_subject( $sanitized_data, $ask_subject ), ENT_QUOTES, 'UTF-8' );
        if ( isset( $sanitized_data['subject'] ) ) {
            $sanitized_data['subject'] = $subject;
        }

        $files = $request->get_file_params();
        if ( ! empty( $files ) ) {
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }

            $upload_overrides = ['test_form' => false];
            $new_files        = $files['file'];
            $movedFiles       = [];
            foreach ( $new_files['name'] as $key => $value ) {
                if ( $new_files['name'][$key] ) {
                    $file = [
                        'name'     => $new_files['name'][$key],
                        'type'     => $new_files['type'][$key],
                        'tmp_name' => $new_files['tmp_name'][$key],
                        'error'    => $new_files['error'][$key],
                        'size'     => $new_files['size'][$key]
                    ];
                    $movedFile = wp_handle_upload( $file, $upload_overrides );
                    if ( ! isset( $movedFile['error'] ) ) {
                        $movedFiles[] = $movedFile;
                    }
                }
            }
            if ( ! empty( $movedFiles ) ) {
                $sanitized_data['files'] = $movedFiles;
            }
        }
        $body    = $this->mail_body( $sanitized_data );
        $name    = html_entity_decode( $sanitized_data['name'], ENT_QUOTES, 'UTF-8' );
        $from    = $sanitized_data['email'];
        $headers = ['Content-Type: text/html; charset=UTF-8', "From: $name <$from>", 'Reply-To: ' . $from];
        if ( wp_mail( $to, $subject, $body, $headers ) ) {
            return true;
        }
        return false;
    }

    public function mail_body( $data ) {
        if ( empty( $data ) ) {
            return '';
        }

        ob_start();
        betterdocs_pro()->views->get( 'admin/email/ia', ['all_data' => $data] );
        return ob_get_clean();
    }

    /**
     * Save Global Feedback
     * @param WP_REST_Request $request
     * @return bool
     */
    public function feedback( WP_REST_Request $request ) {
        $feedback = get_option( '_betterdocs_feelings', [] );
        $feelings = isset( $request['feelings'] ) ? $request['feelings'] : 'happy';

        $feedback[$feelings] = ( isset( $feedback[$feelings] ) ? intval( $feedback[$feelings] ) : 0 ) + 1;
        if ( update_option( '_betterdocs_feelings', $feedback, 'no' ) ) {
            return true;
        }
        return false;
    }
}
