<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( ! class_exists ( 'YWQA_Discussion' ) ) {
    /**
     *
     * @class      class.ywqa-discussion.php
     * @package    Yithemes
     * @since      Version 1.0.0
     * @author     Your Inspiration Themes
     *
     */
    class YWQA_Discussion {

        /**
         * @var int Id of the question or answer
         */
        public $ID;

        /**
         * @var string the content of the question
         */
        public $content = '';

        /**
         * @var int the user who submitted the question
         */
        public $discussion_author_id = 0;

        /**
         * @var string the discussion author name
         */
        public $discussion_author_name = '';

        /**
         * @var string the discussion author name
         */
        public $discussion_author_email = '';
        
        /**
         * @var the date when the question was submitted
         */
        public $date;

        /**
         * @var int the product id related to current question
         */
        public $product_id = 0;

        /**
         * @var int id of the parent element(it's a product for the question element, it's a question element for the answer element)
         */
        public $parent_id = 0;

        /**
         * @var string discussion type, can be  "question", "answer"
         */
        public $type = '';

        /**
         * @var string status of the post
         */
        public $status = 'publish';

        /**
         * Create a new item
         *
         * @param null $args
         */
        public function __construct ( $args = null ) {
            if ( is_numeric ( $args ) ) {
                $args = $this->get_array ( $args );
            }

            if ( $args ) {
                foreach ( $args as $key => $value ) {

                    $this->{$key} = $value;
                }
            }
        }

        /**
         * Retrieve the discussion author name
         */
        public function get_author_name () {
            $author_name = esc_html__( "Anonymous user", 'yith-woocommerce-questions-and-answers' );

            if ( $this->discussion_author_id ) {
                $user_info   = get_userdata ( $this->discussion_author_id );
                if ( is_object($user_info) )
                    $author_name = $user_info->display_name;
            }
            elseif ( ! empty( $this->discussion_author_name ) ) {
                $author_name = $this->discussion_author_name;
            }
            

            return $author_name;
        }

        /**
         * Retrieve document content with nofollow links
         *
         */
        public function get_nofollow_content () {
            /**
             * Adds rel nofollow string to all HTML A elements in content.
             */
            $text = stripslashes ( $this->content );
            $text = preg_replace_callback ( '|<a (.+?)>|i', array ( $this, 'nofollow_callback' ), $text );

            return $text;
        }

        public function nofollow_callback ( $matches ) {
            $text = $matches[ 1 ];
            $text = str_replace ( array ( ' rel="nofollow"', " rel='nofollow'" ), '', $text );

            return "<a $text rel=\"nofollow\">";
        }

        /**
         * retrieve discussion attribute from id
         *
         * @param int $post_id the discussion id
         *
         * @return array|null
         */
        private function get_array ( $post_id ) {
            $post = get_post ( $post_id );

            if ( ! isset( $post ) ) {
                return null;
            }

            return array (
                "date"                   => $post->post_date,
                "discussion_author_id"   => $post->{YWQA_METAKEY_DISCUSSION_AUTHOR_ID},
                "discussion_author_name" => $post->{YWQA_METAKEY_DISCUSSION_AUTHOR_NAME},
                "discussion_author_email" => $post->{YWQA_METAKEY_DISCUSSION_AUTHOR_EMAIL},
                "content"                => $post->post_content,
                "product_id"             => $post->{YWQA_METAKEY_PRODUCT_ID},
                "ID"                     => $post->ID,
                "status"                 => $post->post_status,
                "parent_id"              => $post->post_parent,
            );
        }

        /**
         * Delete the current item
         *
         * @return array|bool|WP_Post
         */
        public function delete () {
            return wp_trash_post ( $this->ID );
        }

        /**
         * Save the current question
         */
        public function save () {

	        $this->content = stripslashes($this->content);

            // Create post object
            $args = array (
                'post_date'    => isset( $this->date ) ? $this->date : current_time ( 'mysql', 0 ),
                'post_title'   => ywqa_strip_trim_text ( $this->content ),
                'post_content' => $this->content,
                'post_status'  => $this->status,
                'post_type'    => YWQA_CUSTOM_POST_TYPE_NAME,
                'post_parent'  => $this->parent_id,
            );

            if ( ! isset( $this->ID ) ) {
                // Insert the post into the database
                $this->ID = wp_insert_post ( $args );
            } else {
                $args[ "ID" ] = $this->ID;
                wp_update_post ( $args );
            }

            update_post_meta ( $this->ID, YWQA_METAKEY_PRODUCT_ID, $this->product_id );
            update_post_meta ( $this->ID, YWQA_METAKEY_DISCUSSION_TYPE, $this->type );
            update_post_meta ( $this->ID, YWQA_METAKEY_DISCUSSION_AUTHOR_ID, $this->discussion_author_id );
            update_post_meta ( $this->ID, YWQA_METAKEY_DISCUSSION_AUTHOR_NAME, $this->discussion_author_name );
            update_post_meta ( $this->ID, YWQA_METAKEY_DISCUSSION_AUTHOR_EMAIL, $this->discussion_author_email );

            do_action ( 'ywqa_after_discussion_save', $this->ID );

            return $this->ID;
        }

        /**
         * Giving a discussion id, retrieve upvotes and downvotes count
         *
         * @param   int question
         *
         * @return  array   array of positive and negative vote count
         *
         * @since  1.0
         * @author Lorenzo Giuffrida
         */
        public function get_voting_stats () {
            $yes_votes = 0;
            $no_votes  = 0;

            if ( isset( $this->ID ) ) {
                $votes = get_post_meta ( $this->ID, YWQA_METAKEY_DISCUSSION_VOTES, true );

                //  get total amount of people that click yes to the comment review
                if ( isset( $votes ) && is_array ( $votes ) ) {
                    $votes_grouped = array_count_values ( array_values ( $votes ) );
                    $yes_votes     = isset( $votes_grouped[ '1' ] ) && is_numeric ( $votes_grouped[ '1' ] ) ? $votes_grouped[ '1' ] : 0;
                    $no_votes      = isset( $votes_grouped[ '-1' ] ) && is_numeric ( $votes_grouped[ '-1' ] ) ? $votes_grouped[ '-1' ] : 0;
                }
            }

            return array ( "yes" => $yes_votes, "not" => $no_votes );
        }

        public function get_abuse_count () {
            if ( isset( $this->ID ) ) {
                return get_post_meta ( $this->ID, YWQA_METAKEY_ANSWER_ABUSE_COUNT, true );
            }

            return 0;
        }

        /**
         * Set the status of the post as approved or not approved
         *
         * @param $new_status true for approved content, false for unapproved content
         */
        public function set_approved_status ( $new_status ) {
            $this->status = $new_status ? 'publish' : 'unapproved';
            $my_post      = array (
                'ID'          => $this->ID,
                'post_status' => $this->status,
            );

            // Update the post into the database
            $res = wp_update_post ( $my_post );

            if ( $this->status == 'publish' ){
                $answer= new YWQA_Answer($this->ID);

                $qa_class = YITH_WooCommerce_Question_Answer_Premium::get_instance();

                $qa_class->notify_user_on_new_answer($answer);
            }

        }

        /**
         * Check if the content is marked as unapproved
         *
         * @return bool
         */
        public function is_unapproved () {
            return "unapproved" == $this->status;
        }

        /**
         * Set the status of the post as inappropriate or appropriate
         *
         * @param $new_status true for appropriate content, false for inappropriate content
         */
        public function set_appropriate_status ( $is_appropriate ) {
            $this->status = $is_appropriate ? 'publish' : 'inappropriate';

            $my_post = array (
                'ID'          => $this->ID,
                'post_status' => $this->status,
            );

            // Update the post into the database
            $res = wp_update_post ( $my_post );
        }

        /**
         * Check if the content is marked as inappropriate
         *
         * @return bool
         */
        public function is_inappropriate () {
            return "inappropriate" == $this->status;
        }

        public function get_item_class ( $classes = '' ) {
            if ( $this instanceof YWQA_Answer ) {
                $classes .= " answer ";
            } else if ( $this instanceof YWQA_Question ) {
                $classes .= " question ";
            }

            if ( $this->is_unapproved () ) {
                $classes .= " unapproved";
            }

            if ( $this->is_inappropriate () ) {
                $classes .= " inappropriate";
            }

            return trim ( $classes );
        }
    }
}