<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Message Class
 *
 * @class   YITH_WCMBS_Message
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Message {

    /**
     * sender: admin or user
     *
     * @var string
     * @since 1.0.0
     */
    public $sender;

    /**
     * date of message
     *
     * @var string
     * @since 1.0.0
     */
    public $date;

    /**
     * text of message
     *
     * @var string
     * @since 1.0.0
     */
    public $text;

    /**
     * status of message
     *
     * @var string
     * @since 1.0.0
     */
    public $status;


    /**
     * set data for Message
     *
     * @param string $sender
     * @param string $date
     * @param string $text
     * @param string $status
     *
     * @access public
     * @since  1.0.0
     */
    public function set_data( $sender, $date, $text, $status ) {
        $this->sender = $sender;
        $this->date   = $date;
        $this->text   = $text;
        $this->status = $status;
    }

    /**
     * Return html containing the date
     *
     * @access public
     * @since  1.0.0
     *
     * @return string
     */
    public function get_date_html() {
        $date = date( 'Y-m-d h:i:s', $this->date );

        return $date;
    }

}