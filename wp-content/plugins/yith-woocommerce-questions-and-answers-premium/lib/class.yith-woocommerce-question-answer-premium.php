<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WooCommerce_Question_Answer_Premium' ) ) {

	/**
	 *
	 * @class   YITH_WooCommerce_Question_Answer_Premium
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_WooCommerce_Question_Answer_Premium extends YITH_WooCommerce_Question_Answer {


		/**
		 * @var int the number of items matching the search criteria to show
		 */
		public $search_items_to_show = 10;
		/**
		 * Let user vote a question
		 *
		 * @var bool
		 */
		public $enable_question_votes = false;

		/**
		 * Let user vote a answers
		 *
		 * @var bool
		 */
		public $enable_answer_votes = false;

		/**
		 * Notify the administrator of new question submitted
		 *
		 * @var bool
		 */
		public $notify_new_question = false;

		/**
		 * Type of mail to sent (plain text or html)
		 *
		 * @var bool
		 */
		public $notify_new_question_type = 'disabled';

		/**
		 * Notify the administrator of new question submitted
		 *
		 * @var bool
		 */
		public $notify_new_answer = false;

		/**
		 * Type of mail to sent (plain text or html)
		 *
		 * @var bool
		 */
		public $notify_new_answer_type = 'disabled';

		/**
		 * Let users to subscribe to question they entered and being notified of new answers
		 *
		 * @var bool
		 */
		public $enable_user_notification = false;

		/**
		 * Who can report an inappropriate content
		 *
		 * @var string
		 */
		public $reporting_abuse_type = "disabled";

		/**
		 * Enable users to report inappropriate content
		 *
		 * @var bool
		 */
		public $enable_abuse_reporting = false;

		/**
		 * Set a threshold for hiding an answer automatically
		 *
		 * @var bool
		 */
		public $abuse_hiding_threshold = 0;

		/**
		 * Set max length of answer content
		 *
		 * @var bool
		 */
		public $answer_excerpt_length = 0;

		/**
		 * Remove name of users from questions and answers
		 */
		public $anonymise_user = false;

        /**
         * Remove date from questions and answers
         */
		public $anonymise_date = false;

		/**
		 * Set a percent value, stating how much of customers who bought a product should be notified about a question
		 *
		 * @var int
		 */
		public $ask_customers_percent = 0;

		/**
		 * Set the behaviour about asking customers to respond to questions
		 *
		 * @var int
		 */
		public $ask_customers_type = "disabled";

		/**
		 * Set if customers should be notified about questions on product the bought
		 */
		public $ask_customers = false;

		/**
		 * @var string shop name
		 */
		public $shop_name = '';

		/**
		 * Set if reCaptcha has to be used on plugin submission forms
		 *
		 * @var bool
		 */
		public $recaptcha_enabled = false;


		public $recaptcha_version = '';

		/**
		 * Set if reCaptcha has to be used on plugin submission forms
		 *
		 * @var bool
		 */
		public $recaptcha_sitekey = '';

		/**
		 * Set if reCaptcha has to be used on plugin submission forms
		 *
		 * @var bool
		 */
		public $recaptcha_secretkey = '';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {

			parent::__construct();

            /**
             * Including the GDRP
             */
            add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

			$this->update_old_entries();

			add_action( 'yith_questions_answers_before_content', array( $this, 'add_vote_section' ) );

			//  Add Ajax calls for upvoting/downvoting questions
			add_action( 'wp_ajax_vote_question', array( $this, 'update_discussion_attribute_callback' ) );
			add_action( 'wp_ajax_nopriv_vote_question', array( $this, 'update_discussion_attribute_callback' ) );

			//  Add Ajax calls for upvoting/downvoting answers
			add_action( 'wp_ajax_vote_answer', array( $this, 'update_discussion_attribute_callback' ) );
			add_action( 'wp_ajax_nopriv_vote_answer', array( $this, 'update_discussion_attribute_callback' ) );

			//  Add Ajax calls for abuse reporting
			add_action( 'wp_ajax_report_answer_abuse', array( $this, 'update_discussion_attribute_callback' ) );
			add_action( 'wp_ajax_nopriv_report_answer_abuse', array( $this, 'update_discussion_attribute_callback' ) );

			//  Add Ajax calls for retrieve full content
			add_action( 'wp_ajax_get_full_content', array( $this, 'get_full_content' ) );
			add_action( 'wp_ajax_nopriv_get_full_content', array( $this, 'get_full_content' ) );

			add_action( "init", array( $this, 'init_custom_actions' ) );

			/**
			 * Custom actions
			 */
			add_action( "qa_action_vote_question", array( $this, 'on_post_vote_to_question' ) );

			/**
			 * Custom columns
			 */
			add_filter( 'yith_questions_answers_custom_column_title', array( $this, 'add_custom_table_columns' ) );
			add_action( 'yith_questions_answers_custom_column_content', array( $this, 'show_custom_table_columns', ), 10, 2 );

			/**
			 * Notify admin on new question submitted
			 */
			add_action( "yith_questions_answers_after_new_question", array( $this, 'notify_admin_on_new_question' ) );

			/**
			 * Notify admin on new answer submitted
			 */
			add_action( "yith_questions_answers_after_new_answer", array( $this, 'notify_admin_on_new_answer' ) );

			/**
			 * Notify admin on new answer submitted
			 */
			add_action( "yith_questions_answers_after_new_answer", array( $this, 'notify_user_on_new_answer' ) );


			/**
			 * Select some customer that buyed the same product to add a response to the question just asked
			 */
			add_action( "yith_questions_answers_after_new_question", array( $this, 'ask_customers_for_answer' ) );

			/**
			 * Set max count of abuse reports permitted for answers
			 */
			add_filter( 'ywqa_get_answers_abuse_limit', array( $this, 'get_answers_abuse_limit' ) );

			/**
			 * Save additional metadata when a discussion is saved
			 */
			add_action( 'ywqa_after_discussion_save', array( $this, 'save_additional_meta' ) );

			/**
			 * Add shortcode for showing questions and answers in a position different than standard tab
			 */
			add_shortcode( "ywqa_questions", array( $this, "show_questions_shortcode" ) );

			add_action( 'init', array( $this, "create_custom_page" ) );

			add_filter( 'wp_get_nav_menu_items', array( $this, "hide_unsubscribe_page" ), 10, 3 );

			add_shortcode( 'ywqa_unsubscribe', array( $this, 'unsubscribe_shortcode' ) );

			add_action( 'ywqa_unsubscribe_from_ask_customer', array(
				$this,
				'unsubscribe_customer_for_product_feedback',
			) );

			add_filter( 'page_row_actions', array(
				$this,
				'add_rows_actions',
			), 10, 2 );
			add_filter( 'post_row_actions', array(
				$this,
				'add_rows_actions',
			), 10, 2 );

			/**
			 * Manage "content appropriated" status
			 */
			add_action( "admin_action_set-appropriate-content", array( $this, 'set_appropriate_content' ) );
			add_action( "admin_action_set-inappropriate-content", array( $this, 'set_inappropriate_content' ) );

			/**
			 * Manage "approved" status
			 */
			add_action( "admin_action_set-approved-content", array( $this, 'set_approved_content' ) );
			add_action( "admin_action_set-unapproved-content", array( $this, 'set_unapproved_content' ) );

			//  Add ajax callback handler for back-end operation
			add_action( 'wp_ajax_get_questions', array(
				$this,
				'get_questions_callback'
			) );
			add_action( 'wp_ajax_nopriv_get_questions', array(
				$this,
				'get_questions_callback',
			) );

			/**
			 * Manage an ajax call for retrieving questions by page
			 */
			add_action( 'wp_ajax_get_answers', array( $this, 'get_answers_callback' ) );
			add_action( 'wp_ajax_nopriv_get_answers', array(
				$this,
				'get_answers_callback',
			) );

			/**
			 * Manage an ajax call for submitting questions
			 */
			add_action( 'wp_ajax_submit_question', array( $this, 'submit_question_callback' ) );
			add_action( 'wp_ajax_nopriv_submit_question', array(
				$this,
				'submit_question_callback',
			) );

			/**
			 * Manage ajax call for action submit_answer on frontend
			 */
			add_action( 'wp_ajax_submit_answer', array( $this, 'submit_answer_callback' ) );
			add_action( 'wp_ajax_nopriv_submit_answer', array(
				$this,
				'submit_answer_callback',
			) );

			/**
			 * Manage ajax call for action submit_answer on backend
			 */
			add_action( 'wp_ajax_admin_respond_to_question', array( $this, 'admin_respond_to_question_callback' ) );

			/**
			 * Manage ajax call for editing an answer on backend
			 */
			add_action( 'wp_ajax_edit_discussion_content', array( $this, 'edit_discussion_content_callback' ) );

			/**
			 * Manage ajax call for changing che answer status
			 */
			add_action( 'wp_ajax_change_answer_status', array( $this, 'change_answer_status_callback' ) );

			//  Load the single question template for a specific question
			add_action( 'wp_ajax_goto_question', array( $this, 'goto_question_callback' ) );
			add_action( 'wp_ajax_nopriv_goto_question', array(
				$this,
				'goto_question_callback',
			) );


			add_action( 'ywqa_inappropriate_content_reporting', array(
				$this,
				'on_inappropriate_content_report',
			), 10, 2 );

			/**
			 * Add custom views for questions and answers table
			 */
			add_filter( 'views_edit-question_answer', array(
				$this,
				'add_custom_views'
			) );

			add_filter( 'posts_where', array(
				$this,
				'filter_discussion'
			), 10, 2 );

			add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );

			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );

			add_filter( 'posts_search', array( $this, 'product_search' ) );

			/**
			 * Add an email action for sending the digital gift card
			 */
			add_filter( 'woocommerce_email_actions', array( $this, 'add_email_actions' ) );

			/**
			 * Locate the plugin email templates
			 */
			add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_email_templates' ), 10, 3 );

			/**
			 * Add the email used to send digital gift card to woocommerce email tab
			 */
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_email_classes' ) );

			add_action( 'woocommerce_email_header', array( $this, 'woocommerce_email_header' ), 5, 2 );

			add_action( 'woocommerce_email_footer', array( $this, 'woocommerce_email_footer' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'test' ) );
		}


        /**
         * Including the GDRP
         */
        public function load_privacy() {

            if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) )
                require_once( YITH_YWQA_LIB_DIR . 'class.yith-woocommerce-question-answer-privacy.php' );

        }

		public function test() {
			$assets_path = WC()->plugin_url() . '/assets/';//str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			wp_enqueue_style( 'select2', $assets_path . 'css/select2.css' );


		}

		/**
		 * Add CSS to plugin's email
		 *
		 * @param mixed           $email_heading
		 * @param string|WC_Email $email
		 */
		public function woocommerce_email_header( $email_heading, $email = '' ) {
			$plugin_email_ids = array(
				'ywqa-email-ask-customers-answer',
				'ywqa-email-notify-answer',
				'ywqa-email-notify-question'
			);


			if ( ( $email instanceof WC_Email ) && in_array( $email->id, $plugin_email_ids ) ) {

				add_filter( 'woocommerce_email_styles', array( $this, 'append_email_style' ), 5 );
			}
		}

		public function woocommerce_email_footer() {
			remove_filter( 'woocommerce_email_styles', array( $this, 'append_email_style' ) );

		}

		public function append_email_style( $style ) {
			ob_start();

			wc_get_template( 'emails/email-styles.php',
				array(),
				'',
				YITH_YWQA_TEMPLATES_DIR );

			$css = ob_get_clean();

			return $style . ' ' . $css;

		}

		/**
		 * Locate the plugin email templates
		 *
		 * @param $core_file
		 * @param $template
		 * @param $template_base
		 *
		 * @return string
		 */
		public function locate_email_templates( $core_file, $template, $template_base ) {

			$custom_template = array(
				'emails/ywqa-ask-customers.php',
				'emails/plain/ywqa-ask-customers.php',
				'emails/ywqa-notify-answer.php',
				'emails/plain/ywqa-notify-answer.php',
				'emails/ywqa-notify-question.php',
				'emails/plain/ywqa-notify-question.php',
			);

			if ( in_array( $template, $custom_template ) ) {
				$core_file = YITH_YWQA_TEMPLATES_DIR . $template;
			}

			return $core_file;
		}

		/**
		 * Add an email action for sending the digital gift card
		 *
		 * @param array $actions list of current actions
		 *
		 * @return array
		 */
		function add_email_actions( $actions ) {
			//  Add trigger action for sending digital gift card
			$actions[] = 'ywqa-email-notify-question';
			$actions[] = 'ywqa-email-notify-answer';
			$actions[] = 'ywqa-email-ask-customers-answer';

			return $actions;
		}

		/**
		 * Add the email used to send digital gift card to woocommerce email tab
		 *
		 * @param string $email_classes current email classes
		 *
		 * @return mixed
		 */
		public function add_woocommerce_email_classes( $email_classes ) {
			// add the email class to the list of email classes that WooCommerce loads
			$email_classes['ywqa-email-ask-customers-answer'] = include( 'emails/class.ywqa-email-ask-customers-answer.php' );
			$email_classes['ywqa-email-notify-answer']        = include( 'emails/class.ywqa-email-notify-answer.php' );
			$email_classes['ywqa-email-notify-question']      = include( 'emails/class.ywqa-email-notify-question.php' );

			return $email_classes;
		}

		/**
		 * Search questions and answers for a specific product
		 *
		 * @param string $where
		 *
		 * @return string
		 */
		public function product_search( $where ) {

			//todo check if the following code is necessary
			global $pagenow, $wpdb, $wp;

			if ( ( 'edit.php' != $pagenow ) || ( ! isset( $_GET["product_id"] ) ) || ( 'question_answer' != $wp->query_vars['post_type'] ) ) {
				return $where;
			}

			$product_id = $_GET["product_id"];
			if ( $product_id ) {


				$question_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s and post_parent = %d",
					YWQA_CUSTOM_POST_TYPE_NAME,
					$product_id ) );

				$answer_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s and post_parent IN(select id from {$wpdb->posts} where  post_type = %s and post_parent = %d)",
					YWQA_CUSTOM_POST_TYPE_NAME,
					YWQA_CUSTOM_POST_TYPE_NAME,
					$product_id ) );

				$question_ids = array_merge( $question_ids, $answer_ids, array( - 1 ) );


				if ( sizeof( $question_ids ) > 0 ) {
					$where = " and ({$wpdb->posts}.ID IN (" . implode( ',', $question_ids ) . "))";
				}
			}


			return $where;
		}

		/**
		 * Add current screen to woocommerce pages
		 *
		 * @param $screen_ids
		 *
		 * @return array
		 */
		public function add_screen_ids( $screen_ids ) {

			$screen_ids[] = get_current_screen()->id;

			return $screen_ids;
		}

		/**
		 * Enable table item filters for custom post types
		 */
		public function restrict_manage_posts() {
			global $typenow, $wp_query;

			if ( 'question_answer' == $typenow ) {
				//  Show product choosen
				$this->product_dropdown();
			}
		}

		/**
		 * Add the product dropdown
		 *
		 * @return void
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function product_dropdown() {
			$product_id      = ! empty( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : '';
			$product         = ! empty( $product_id ) ? wc_get_product( $product_id ) : false;
			$product_display = ! empty( $product ) ? yit_get_prop( $product, 'name' ) . '(#' . $product_id . ')' : '';

			$args = array(
				'id'               => 'product_id',
				'class'            => 'wc-product-search ywqa-filter-product',
				'name'             => 'product_id',
				'data-multiple'    => false,
				'data-allow_clear' => true,
				'data-placeholder' => esc_html__( 'Search for a product&hellip;', 'yith-woocommerce-questions-and-answers' ),
				'data-selected'    => array( $product_id => $product_display ),
				'value'            => $product_id,
				'style'            => 'width: 200px;'
			);

			yit_add_select2_fields( $args );

		}

		/**
		 * Filter visible item based on current view
		 *
		 * @param $where
		 * @param $par2
		 *
		 * @return string
		 */
		public function filter_discussion( $where, $par2 ) {

			if ( ! isset( $par2 ) || ! isset( $par2->query["post_type"] ) || "question_answer" != $par2->query["post_type"] ) {
				return $where;
			}

			global $wpdb;
			if ( isset( $_GET["filter_question"] ) ) {
				if ( "unanswered_q" == $_GET["filter_question"] ) {
					$where .= sprintf( " and post_parent IN (select id from {$wpdb->prefix}posts where post_type = 'product') and ID NOT IN (select distinct(post_parent) from {$wpdb->prefix}posts where post_type = '%s') ", YWQA_CUSTOM_POST_TYPE_NAME );
				} else if ( "questions" == $_GET["filter_question"] ) {
					$where .= sprintf( " and post_parent IN (select id from {$wpdb->prefix}posts where post_type = 'product')", YWQA_CUSTOM_POST_TYPE_NAME );
				} else if ( "answers" == $_GET["filter_question"] ) {
					$where .= sprintf( " and post_parent IN (select id from {$wpdb->prefix}posts where post_type = '%s')", YWQA_CUSTOM_POST_TYPE_NAME );
				}
			}

			return $where;
		}

		/**
		 * Add custom views for questions and answers table
		 */
		public function add_custom_views( $views ) {
			$count = $this->get_unanswered_question_to_show_count();
			if ( $count ) {
				$views["unanswered"] = sprintf( "<a href='edit.php?post_type=question_answer&filter_question=unanswered_q'>%s<span class='count'> (%s)</span></a>",
					esc_html__( "Unanswered questions", 'yith-woocommerce-questions-and-answers' ), $count );
			}

			$count = $this->get_questions_to_show_count();
			if ( $count ) {
				$views["questions"] = sprintf( "<a href='edit.php?post_type=question_answer&filter_question=questions'>%s<span class='count'> (%s)</span></a>",
					esc_html__( "Questions", 'yith-woocommerce-questions-and-answers' ), $count );
			}

			$count = $this->get_answers_to_show_count();
			if ( $count ) {
				$views["answers"] = sprintf( "<a href='edit.php?post_type=question_answer&filter_question=answers'>%s<span class='count'> (%s)</span></a>",
					esc_html__( "Answers", 'yith-woocommerce-questions-and-answers' ), $count );
			}


			return $views;
		}

		/**
		 * Check if a question or answer has received too much inappropriate content report and set it automatically as
		 * inappropriate content
		 *
		 * @param $discussion_id question/answer di
		 * @param $count         total number of requests for the question/answer
		 */
		public function on_inappropriate_content_report( $discussion_id, $count ) {

			if ( ( $this->abuse_hiding_threshold > 0 ) && ( $this->abuse_hiding_threshold <= $count ) ) {
				$discussion = $this->get_discussion( $discussion_id );
				$discussion->set_appropriate_status( false );
			}
		}

		/**
		 * Set current item to appropriate content.
		 */
		public function set_appropriate_content() {

			$this->set_appropriate_content_action( true );
		}

		/**
		 * Set current item to INappropriate content.
		 */
		public function set_inappropriate_content() {

			$this->set_appropriate_content_action( false );
		}

		/**
		 * Set current item to approved content.
		 */
		public function set_approved_content() {

			$this->set_approved_content_action( true );
		}

		/**
		 * Set current item to unapproved content.
		 */
		public function set_unapproved_content() {

			$this->set_approved_content_action( false );
		}

		/**
		 * Check nonce and set the new appropriate content status, then go back to previous page
		 *
		 * @param $post_id        the discussion in use
		 * @param $is_appropriate appropriate content flag
		 */
		public function set_appropriate_content_action( $is_appropriate ) {
			$post_id = $_GET["id"];
			if ( ! wp_verify_nonce( $_GET["_wpnonce"], 'set-appropriate-status-' . $post_id ) ) {
				return;
			}

			$discussion = $this->get_discussion( $post_id );

			$discussion->set_appropriate_status( $is_appropriate );

			wp_redirect( $_SERVER['HTTP_REFERER'] );

		}

		/**
		 * Check nonce and set the new appropriate content status, then go back to previous page
		 *
		 * @param $post_id     the discussion in use
		 * @param $is_approved content is approved or not
		 */
		public function set_approved_content_action( $is_approved ) {
			$post_id = $_GET["id"];
			if ( ! wp_verify_nonce( $_GET["_wpnonce"], 'set-approved-status-' . $post_id ) ) {
				return;
			}

			$discussion = $this->get_discussion( $post_id );
			$discussion->set_approved_status( $is_approved );

			wp_redirect( $_SERVER['HTTP_REFERER'] );

		}

		/**
		 * Add rows actions for questions and answers custom post type
		 *
		 * @param array   $actions
		 * @param WP_Post $post
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_rows_actions( $actions, $post ) {

			if ( YWQA_CUSTOM_POST_TYPE_NAME != $post->post_type ) {
				return $actions;
			}

			unset( $actions["inline hide-if-no-js"] );
			if ( 'inappropriate' === $post->post_status ) {
				$actions["restore-appropriate"] = "<a title='" . esc_attr__( 'Restore as appropriate content', 'yith-woocommerce-questions-and-answers' ) . "' href='" . wp_nonce_url( admin_url( "admin.php?action=set-appropriate-content&post_type=" . YWQA_CUSTOM_POST_TYPE_NAME . "&id=" . $post->ID ), 'set-appropriate-status-' . $post->ID ) . "'>" . esc_html__( 'Set appropriate', 'yith-woocommerce-questions-and-answers' ) . "</a>";
			} elseif ( 'publish' === $post->post_status ) {
				$actions["set-inappropriate"] = "<a title='" . esc_attr__( 'Set as inappropriate content', 'yith-woocommerce-questions-and-answers' ) . "' href='" . wp_nonce_url( admin_url( "admin.php?action=set-inappropriate-content&post_type=" . YWQA_CUSTOM_POST_TYPE_NAME . "&id=" . $post->ID ), 'set-appropriate-status-' . $post->ID ) . "'>" . esc_html__( 'Set inappropriate', 'yith-woocommerce-questions-and-answers' ) . "</a>";
			}

			if ( 'unapproved' === $post->post_status ) {
				$actions["restore-approved"] = "<a title='" . esc_attr__( 'Restore as approved content', 'yith-woocommerce-questions-and-answers' ) . "' href='" . wp_nonce_url( admin_url( "admin.php?action=set-approved-content&post_type=" . YWQA_CUSTOM_POST_TYPE_NAME . "&id=" . $post->ID ), 'set-approved-status-' . $post->ID ) . "'>" . esc_html__( 'Approve', 'yith-woocommerce-questions-and-answers' ) . "</a>";
			} elseif ( 'publish' === $post->post_status ) {
				$actions["set-unapproved"] = "<a title='" . esc_attr__( 'Set as unapproved content', 'yith-woocommerce-questions-and-answers' ) . "' href='" . wp_nonce_url( admin_url( "admin.php?action=set-unapproved-content&post_type=" . YWQA_CUSTOM_POST_TYPE_NAME . "&id=" . $post->ID ), 'set-approved-status-' . $post->ID ) . "'>" . esc_html__( 'Unapprove', 'yith-woocommerce-questions-and-answers' ) . "</a>";
			}

			return $actions;
		}
		/**
		 * Look for custom action request and trigger the related action
		 */
		public function init_custom_actions() {
			if ( isset( $_GET["qa_action"] ) ) {
				do_action( "qa_action_" . $_GET["qa_action"] );
			}

			if ( isset( $_POST["unsubscribe_email"] ) ) {
				do_action( 'ywqa_unsubscribe_from_ask_customer' );
			}

			$this->register_custom_post_statuses();
		}

		public function register_custom_post_statuses() {
			//  Register a status for questions and answers that are reported as inappropriate
			register_post_status( 'inappropriate', array(
				'label'                     => esc_html_x( 'Inappropriate', 'yith-woocommerce-questions-and-answers' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Inappropriate content <span class="count">(%s)</span>',
					'Inappropriate content <span class="count">(%s)</span>',
					'yith-woocommerce-questions-and-answers' ),
			) );

			register_post_status( 'unapproved', array(
				'label'                     => esc_html_x( 'Unapproved', 'yith-woocommerce-questions-and-answers' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Content not approved <span class="count">(%s)</span>',
					'Content not approved <span class="count">(%s)</span>',
					'yith-woocommerce-questions-and-answers' ),
			) );
		}

		/**
		 * Remove the customer from being requested to put an answer to a question.
		 */
		public function unsubscribe_customer_for_product_feedback() {

			if ( ( ! isset( $_GET['email'] ) ) ||
			     ( ! isset( $_GET['id'] ) ) ||
			     ( ! isset( $_GET['id2'] ) )
			) {
				return;
			}

			$email       = urldecode( base64_decode( $_GET['email'] ) );
			$product_id  = intval( urldecode( base64_decode( $_GET['id'] ) ) );
			$customer_id = intval( urldecode( base64_decode( $_GET['id2'] ) ) );
			$nonce_form  = "unsubscribe_" . $_GET['email'];

			//  Check for nonces
			if ( ! wp_verify_nonce( $_POST["_wpnonce"], $nonce_form ) ) {
				wp_die( "<p>" . esc_html__( "An error occurred. The requested resource does not exist or the content is deprecated.", 'yith-woocommerce-questions-and-answers' ) . "</p>" );
			}

			$form_email = $_POST["unsubscribe_email"];
			if ( strcasecmp( $form_email, $email ) == 0 ) {
				//  unsubscribe customer
				update_user_meta( $customer_id, '_ywqa_exclude_product', $product_id );

				$query_args = array(
					'id',
					'id2',
					'email',
				);

				$url = add_query_arg( 'unsubscribe', 'completed', remove_query_arg( $query_args ) );
				wp_redirect( $url );
				exit();
			} else {
				wp_die( "<p>" . esc_html__( "The email address is not valid.", 'yith-woocommerce-questions-and-answers' ) . "</p>" );
			}
		}

		/**
		 * Let users that buyed a product to avoid being asked to response to a question to the same product
		 */
		public function unsubscribe_shortcode( $atts ) {

			if ( ( ( ! isset( $_GET['email'] ) ) ||
			       ( ! isset( $_GET['id'] ) ) ||
			       ( ! isset( $_GET['id2'] ) ) ) &&
			     ( ! isset( $_GET['unsubscribe'] ) )
			) {
				?>
				<script language="JavaScript">
					self.location = "<?php echo home_url(); ?>";


				</script>

				<?php
				return;
			}

			if ( isset( $_GET['unsubscribe'] ) ) {
				?>
				<p>
					<?php esc_html_e( 'Unsubscription completed. You will not receive any more answering requests for the selected products.', 'yith-woocommerce-questions-and-answers' ); ?>
				</p>
				<?php
				return;
			}
			?>

			<p>
				<?php esc_html_e( 'Please, confirm your email address to stop receiving answering requests for the questions about products you have bought:', 'yith-woocommerce-questions-and-answers' ); ?>
			</p>
			<form method="post" action="">
				<p class="form-row form-row-wide">
					<label
						for="unsubscribe_email"><?php esc_html_e( 'Email address', 'yith-woocommerce-questions-and-answers' ); ?>
						<span class="required">*</span>
					</label>
					<input type="email" class="input-text" name="unsubscribe_email" id="unsubscribe_email" />
				</p>

				<p>
					<?php wp_nonce_field( "unsubscribe_" . $_GET['email'] ); ?>
					<input type="submit" class="button" name="unsubscribe"
					       value="<?php esc_html_e( 'Unsubscribe', 'yith-woocommerce-questions-and-answers' ); ?>" />
				</p>
			</form>
			<?php
		}

		public function hide_unsubscribe_page( $items, $menu, $args ) {

			foreach ( $items as $key => $value ) {
				if ( "questions_and_answers_unsubscribe" === basename( $value->url ) ) {
					unset( $items[ $key ] );
				}
			}

			return $items;
		}

		/**
		 * Creates plugin custom pages(for example the page unsubscribe).
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Lorenzo Giuffrida
		 */
		public function create_custom_page() {

			if ( ! function_exists( 'wc_create_page' ) ) {
				return;
			}

			wc_create_page( esc_sql( esc_html_x( 'questions_and_answers_unsubscription', 'Page slug', 'yith-woocommerce-questions-and-answers' ) ),
				'ywqa_unsubscribe_page_id',
				esc_html_x( 'Questions & Answers unsubscription', 'Page title', 'yith-woocommerce-questions-and-answers' ),
				'[ywqa_unsubscribe]',
				! empty( $page['parent'] ) ? wc_get_page_id( $page['parent'] ) : '' );
		}

		/**
		 * Show questions in custom place with shortcode [ywqa_questions]
		 */
		public function show_questions_shortcode( $atts ) {
			global $product;

			if ( ! isset( $product ) ) {
				return '';
			}

			ob_start();
			$this->show_main_template();
			$html = ob_get_contents();
			ob_end_clean();

			return $html;
		}

		/**
		 * Save additional metadata when a discussion is saved
		 */
		public function save_additional_meta( $discussion_id ) {
			update_post_meta( $discussion_id, YWQA_METAKEY_VERSION, YITH_YWQA_VERSION );

			add_post_meta( $discussion_id, YWQA_METAKEY_DISCUSSION_VOTES, array(), true );
			add_post_meta( $discussion_id, YWQA_METAKEY_DISCUSSION_UPVOTES, 0, true );
			add_post_meta( $discussion_id, YWQA_METAKEY_DISCUSSION_DOWNVOTES, 0, true );

			add_post_meta( $discussion_id, YWQA_METAKEY_ANSWER_ABUSE_REPORTS, array(), true );
			add_post_meta( $discussion_id, YWQA_METAKEY_ANSWER_ABUSE_COUNT, 0, true );
		}

		/**
		 * Set max count of abuse reports permitted for answers
		 *
		 * @return bool
		 */
		public function get_answers_abuse_limit() {
			return $this->abuse_hiding_threshold;
		}


		/**
		 * Select some customers and send them a survey about the product that they purchased
		 *
		 * @var YWQA_Question $question
		 */
		public function ask_customers_for_answer( $question ) {

			if ( ! $this->ask_customers ) {
				return;
			}

			$num_elements  = - 1;
			$percent_value = false;

			if ( "custom" === $this->ask_customers_type ) {
				$percent_value = true;
				$num_elements  = $this->ask_customers_percent;
			}

			$customers = $this->get_customers_for_product( $question, $num_elements, $percent_value );

			foreach ( $customers as $customer_detail ) {
				$this->send_email_to_customer_of_same_product( $question, $customer_detail );
			}
		}

		public function send_email_to_customer_of_same_product( $question, $customer_detail ) {
			/* Let the user choose to do not receive this notification anymore for this product or for all products */
			$query_args = array(
				'id'    => urlencode( base64_encode( $question->product_id ) ),
				'id2'   => urlencode( base64_encode( $customer_detail["customer_user_id"] ) ),
				'email' => urlencode( base64_encode( $customer_detail["billing_email"] ) ),
			);

			$unsubscribe_product_url     = esc_url( add_query_arg( $query_args, get_permalink( get_option( 'ywqa_unsubscribe_page_id' ) ) ) );
			$query_args['id']            = urlencode( base64_encode( - 1 ) );
			$unsubscribe_all_product_url = esc_url( add_query_arg( $query_args, get_permalink( get_option( 'ywqa_unsubscribe_page_id' ) ) ) );

			$args = array(
				'question'                    => $question,
				'recipient'                   => $customer_detail["billing_email"],
				'unsubscribe_product_url'     => $unsubscribe_product_url,
				'unsubscribe_all_product_url' => $unsubscribe_all_product_url,
			);

			do_action( 'ywqa-email-ask-customers-answer', $args );
		}

		public function notify_admin_on_new_question( $question ) {
			if ( ! $this->notify_new_question ) {
				return;
			}

			$args = array(
				'question' => $question,
			);

			do_action( 'ywqa-email-notify-question', $args );
			//$this->send_mail( $to, $subject, $email_content );
		}

		/**
		 * Notify to the user that wrote a question,  that an answer was submitted.
		 *
		 * @param YWQA_Answer $answer
		 */
		public function notify_user_on_new_answer( $answer ) {

			//  Nothing to notify is the option is not enabled
			if ( ! $this->enable_user_notification ) {
				return;
			}

            //  Nothing to notify if the user who submitted the question does not ask for a notification
			if ( ! get_post_meta( $answer->parent_id, YWQA_METAKEY_NOTIFY_USER, true ) ) {
				return;
			}

            $answer_manual_approval   = ( "yes" === get_option( "ywqa_answer_manual_approval", "no" ) ) ? 1 : 0;

            //  Nothing to notify is answer is unapproved
            if ( $answer_manual_approval && $answer->date == '' && $answer->answered_backend != 1 ) {
                return;
            }

            /*
             * Use author_id and author_email stored in the parent question and overwrite it if the
             * author_id is related to a valid web site user
             */
			$question_author_id = $answer->get_question()->discussion_author_id;
			$notification_email = $answer->get_question()->discussion_author_email;

			if ( $question_author_id ) {
				if ( $user = get_userdata( $question_author_id ) ) {
					$notification_email = $user->user_email;
				}
			}

            /*
             * Is a recipient email is set, let's start the notification
             */
			if ( $notification_email ) {

                $args = array(
					'answer'          => $answer,
					'recipient_email' => $notification_email,
				);

				do_action( 'ywqa-email-notify-answer', $args );
			}
		}

		/**
		 * Notify admin that an answer was submitted
		 *
		 * @param $answer
		 */
		public function notify_admin_on_new_answer( $answer ) {

			if ( $this->notify_new_answer ) {
				$args = array(
					'answer'          => $answer,
					'recipient_email' => apply_filters( 'yith_ywqa_admin_email_notification', get_option( 'admin_email' ) ),
				);

				do_action( 'ywqa-email-notify-answer', $args );
			}
		}

		public function init_plugin_settings() {

			parent::init_plugin_settings();

			$this->enable_question_votes    = ( "yes" === get_option( "ywqa_enable_question_vote", "no" ) );
			$this->enable_answer_votes      = ( "yes" === get_option( "ywqa_enable_answer_vote", "no" ) );
			$this->enable_user_notification = ( "yes" === get_option( "ywqa_notify_answers_to_user", "no" ) );

			$this->notify_new_question_type = get_option( "ywqa_notify_new_question", "disabled" );
			$this->notify_new_question      = ( "disabled" != $this->notify_new_question_type ) ? 1 : 0;

			$this->notify_new_answer_type = get_option( "ywqa_notify_new_answer", "disabled" );
			$this->notify_new_answer      = ( "disabled" != $this->notify_new_answer_type ) ? 1 : 0;

			$this->reporting_abuse_type   = get_option( "ywqa_enable_answer_abuse_reporting", "disabled" );
			$this->enable_abuse_reporting = ( "disabled" != $this->reporting_abuse_type ) ? 1 : 0;
			$this->abuse_hiding_threshold = get_option( "ywqa_hide_inappropriate_content_threshold", 0 );

			$this->answer_excerpt_length = get_option( "ywqa_enable_answer_excerpt", 0 );
			$this->anonymise_user        = ( "yes" === get_option( "ywqa_anonymise_user", "no" ) );
			$this->anonymise_date        = ( "yes" === get_option( "ywqa_anonymise_date", "no" ) );

			/**
			 * Ask customer to respond to a question
			 */
			$this->ask_customers_percent = get_option( "ywqa_ask_customers_percent", 0 );
			$this->ask_customers_type    = get_option( "ywqa_ask_customers", "disabled" );
			$this->ask_customers         = ( "disabled" != $this->ask_customers_type ) ? 1 : 0;

			/**
			 * Set reCaptcha settings
			 */
			$this->recaptcha_enabled   = ( "yes" === get_option( "ywqa_enable_recaptcha", "no" ) );
			$this->recaptcha_version   = get_option('ywqa_recaptcha_version', "v2");
			$this->recaptcha_sitekey   = get_option( "ywqa_recaptcha_site_key" );
			$this->recaptcha_secretkey = get_option( "ywqa_recaptcha_secret_key" );

			$this->shop_name = get_option( 'ywqa_shop_name', '' );
		}

		/**
		 * Count the number of questions waiting for an answer
		 */
		public function get_unanswered_question_to_show_count() {
			global $wpdb;
			//  Update from version 1.0.0
			$query = $wpdb->prepare( "select count(ID)
				from {$wpdb->prefix}posts
				where post_type = %s and post_status = 'publish' and post_parent IN (select id from {$wpdb->prefix}posts where post_type = 'product')
				 and ID NOT IN (select distinct(post_parent) from {$wpdb->prefix}posts where post_type = %s  and post_status = 'publish')",
				YWQA_CUSTOM_POST_TYPE_NAME,
				YWQA_CUSTOM_POST_TYPE_NAME
			);

			return $wpdb->get_var( $query );
		}

		/**
		 * Count the number of questions for a specific product
		 *
		 * @param int $product_id
		 *
		 * @return null|string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_count_questions_by_products( $product_id = - 1 ) {

			global $wpdb;

			$query = $wpdb->prepare( "select count(ID)
				from {$wpdb->posts}
				where post_type = %s and
				      post_status = 'publish' and
				      post_parent = %d",
				YWQA_CUSTOM_POST_TYPE_NAME,
				$product_id
			);

			return $wpdb->get_var( $query );
		}

		/**
		 * Count the number of items of type "question"
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_questions_to_show_count() {

			global $wpdb;

			//  Update from version 1.0.0
			$query = $wpdb->prepare( "select count(ID)
				from {$wpdb->posts}
				where post_type = %s and
				      post_status = 'publish' and
				      post_parent IN
				        (select id
				        from {$wpdb->posts}
				        where post_type = 'product')",
				YWQA_CUSTOM_POST_TYPE_NAME
			);

			return $wpdb->get_var( $query );
		}

		/**
		 * Count the number of items of type "answer"
		 */
		public function get_answers_to_show_count() {
			global $wpdb;
			//  Update from version 1.0.0
			$query = $wpdb->prepare( "select count(ID)
				from {$wpdb->posts}
				where post_type = %s  and
				      post_parent IN (
				        select id
				        from {$wpdb->posts}
				        where post_type = %s and
				              post_status = 'publish')",
				YWQA_CUSTOM_POST_TYPE_NAME,
				YWQA_CUSTOM_POST_TYPE_NAME
			);

			return $wpdb->get_var( $query );
		}

		/**
		 * Check previous entries and update with missing metakeys
		 */
		public function update_old_entries() {
			global $wpdb;

			if ( false === get_option( 'yith_wqa_update_1_0_0' ) ) {
				//  Update from version 1.0.0
				$query = $wpdb->prepare( "select ID
                    from {$wpdb->prefix}posts
                    where post_type = %s and ID NOT IN (select post_id from {$wpdb->prefix}postmeta where meta_key = %s and meta_value = %s)",
					YWQA_CUSTOM_POST_TYPE_NAME,
					YWQA_METAKEY_VERSION,
					YITH_YWQA_VERSION
				);

				$items = $wpdb->get_results( $query, ARRAY_A );

				foreach ( $items as $item ) {
					$this->save_additional_meta( $item["ID"] );
				}

				update_option( 'yith_wqa_update_1_0_0', 1 );
			}
		}

		/**
		 * Select a group of customers that has bought the same product for which the question is asked
		 *
		 * @param YWQA_Question $question the question for which we search customers
		 * @param int           $limit    the size of the group to be selected
		 * @param bool          $percent  if the size is entered as fixed or percentage
		 *
		 * @return array|null|object
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_customers_for_product( $question, $limit = - 1, $percent = false ) {

			global $wpdb;

			$query = $wpdb->prepare( "
				select p.*, it.*, it_m.*, po_m.meta_value as customer_user_id, po_m2.meta_value as billing_first_name, po_m3.meta_value as billing_last_name, po_m4.meta_value as billing_email
				from {$wpdb->prefix}posts p
				left join {$wpdb->prefix}postmeta as po_m on p.ID = po_m.post_id
				left join {$wpdb->prefix}postmeta as po_m2 on po_m.post_id = po_m2.post_id
				left join {$wpdb->prefix}postmeta as po_m3 on po_m.post_id = po_m3.post_id
				left join {$wpdb->prefix}postmeta as po_m4 on po_m.post_id = po_m4.post_id
				left join {$wpdb->prefix}woocommerce_order_items as it on p.ID = it.order_id
				left join {$wpdb->prefix}woocommerce_order_itemmeta as it_m on it.order_item_id = it_m.order_item_id
				where p.post_type = 'shop_order'
				and p.post_status = 'wc-completed'
				and it.order_item_type = 'line_item'
				and it_m.meta_key = '_product_id'
				and po_m.meta_key = '_customer_user'
				and po_m2.meta_key = '_billing_first_name'
				and po_m3.meta_key = '_billing_last_name'
				and po_m4.meta_key = '_billing_email'
				and po_m.meta_value not IN (select DISTINCT (user_id) from {$wpdb->prefix}usermeta where meta_key = '_ywqa_exclude_product' and ((meta_value = it_m.meta_value) or (meta_value = -1)))
				and po_m.meta_value > 0
				and po_m.meta_value != %d
				and it_m.meta_value = %d",
				$question->discussion_author_id,
				$question->product_id );

			$items = $wpdb->get_results( $query, ARRAY_A );

			//  extract the number of elements asked by param $num_element

			if ( $limit > 0 ) {
				//  If count of elements to select if in percent, calculate the equivalent num of element
				if ( $percent ) {
					$limit = count( $items ) / 100 * $limit;
				}

				//  randomize results
				shuffle( $items );

				foreach ( $items as $key => $value ) {

					if ( count( $items ) <= $limit ) {
						break;
					}

					unset( $items[ $key ] );
				}
			}

			return $items;
		}

		/**
		 * show content for custom columns
		 *
		 * @param string $column_name column name to be shown
		 * @param int    $post_ID     discussion id
		 */
		public function show_custom_table_columns( $column_name, $post_ID ) {
			$discussion = $this->get_discussion( $post_ID );
			if ( null == $discussion ) {
				return;
			}

			switch ( $column_name ) {
				case "qa_author":
					echo $this->get_author_information( $discussion );
					break;

				case "upvotes" :
					$stats = $discussion->get_voting_stats();
					echo $stats["yes"];
					break;

				case "downvotes":
					$stats = $discussion->get_voting_stats();
					echo $stats["not"];
					break;

				case "abuse":
					$count = ( $discussion instanceof YWQA_Answer ) ? $discussion->get_abuse_count() : 0;
					echo $count;
					break;

				case 'actions' :
					if ( strcmp( $discussion->product_id, '0' ) != 0 ) {
						$product = wc_get_product( $discussion->product_id );
					} else {
						$product = '';
					}

					if ( ! $product ) {
						esc_html_e( "Product not found!", 'yith-woocommerce-questions-and-answers' );

						return;
					}

					$product_title     = $product->get_title();
					$product_edit_link = get_permalink( $discussion->product_id );

					echo sprintf( '<span class="for-product">%s</span><a class="view-product" target="_blank" href="%s" title="%s">%s</a>',
						esc_html__( "Product: ", 'yith-woocommerce-questions-and-answers' ),
						$product_edit_link,
						$product_title,
						$product_title );

					if ( $discussion instanceof YWQA_Answer ) {
						$question       = $discussion->get_question();
						$question_title = ywqa_strip_trim_text( $question->content );

						$question_edit_link = get_edit_post_link( $question->ID );

						echo " <br>" . sprintf( '<span class="response-to">%s</span><a class="response-to" href="%s" title="%s">%s</a>',
								esc_html__( "Response to: ", 'yith-woocommerce-questions-and-answers' ),
								$question_edit_link,
								$question_title,
								$question_title );
					}
					break;
			}
		}

		/**
		 * Add custom columns to questions and answers table
		 *
		 * @param $columns
		 */
		public function add_custom_table_columns( $columns ) {

			$columns["qa_author"] = esc_html__( "Author", 'yith-woocommerce-questions-and-answers' );
			$columns["upvotes"]   = esc_html__( "Upvotes", 'yith-woocommerce-questions-and-answers' );
			$columns["downvotes"] = esc_html__( "Downvotes", 'yith-woocommerce-questions-and-answers' );
			$columns["abuse"]     = esc_html__( "Abuse reports", 'yith-woocommerce-questions-and-answers' );

			$columns["actions"] = '';

			return $columns;
		}

		/**
		 * Add frontend styles and scripts
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function enqueue_styles_scripts() {
			global $post;
			if ( ! is_product() ) {
				return;
			}

			$maintenance = isset( $_GET["script_debug_on"] );
			$suffix      = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || $maintenance ? '' : '.min';

			wp_enqueue_style( 'ywqa-frontend', YITH_YWQA_ASSETS_URL . '/css/ywqa-frontend.css' );

			//  register and enqueue ajax calls related script file
			wp_register_script( "ywqa-frontend",
				YITH_YWQA_URL . 'assets/js/' . yit_load_js_file( 'ywqa-frontend.js' ),
				array(
					'jquery',
					'jquery-blockui',
					'wc-single-product',
				),
				YITH_YWQA_VERSION,
				true );

			$nonce = wp_create_nonce( "vote-product-" . $post->ID );

			$localize_params = array(
				'nonce_value'          => $nonce,
				'recaptcha'            => $this->recaptcha_enabled,
				'recaptcha_version'    => $this->recaptcha_version,
				'loader'               => apply_filters( 'yith_questions_and_answers_loader', YITH_YWQA_ASSETS_URL . '/images/loading.gif' ),
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'abuse_response'       => esc_html__( "Thanks", 'yith-woocommerce-questions-and-answers' ),
				'discussion_complete'  => esc_html__( "Your content has been sent correctly.", 'yith-woocommerce-questions-and-answers' ),
				'discussion_error'     => esc_html__( "An error has occurred, your content has not been added correctly.", 'yith-woocommerce-questions-and-answers' ),
				'content_is_empty'     => esc_html__( "Please write your text before submitting it.", 'yith-woocommerce-questions-and-answers' ),
				'guest_name_message'   => esc_html__( "Please enter your name", 'yith-woocommerce-questions-and-answers' ),
				'guest_email_message'  => esc_html__( "Please enter your e-mail", 'yith-woocommerce-questions-and-answers' ),
				'mandatory_guest_data' => $this->mandatory_guest_data,
				'recaptcha_not_valid'  => esc_html__( "Please confirm you are human solving the reCaptcha.", 'yith-woocommerce-questions-and-answers' ),
				'goto_questions_tab'   => isset( $_GET["qa"] ) ? 1 : 0,
			);

			if ( $this->recaptcha_enabled ) {
				$localize_params['recaptcha_sitekey'] = $this->recaptcha_sitekey;
			}

			wp_localize_script( 'ywqa-frontend', 'ywqa', $localize_params );

			wp_enqueue_script( "ywqa-frontend" );


			//  Enqueue reCaptcha script on need(if guest post is disabled, avoid enqueuing the script
			if ( $this->recaptcha_enabled && ( $this->allow_guest_users || get_current_user_id() ) ) {

                if ( $this->recaptcha_version == 'v2'){
                    $recaptcha_api_js_url = '//www.google.com/recaptcha/api.js';
                }
                else{
                    $recaptcha_api_js_url = '//www.google.com/recaptcha/api.js?render=' . $this->recaptcha_sitekey;
                }


                wp_register_script( "ywqa-recaptcha",
                    $recaptcha_api_js_url,
                    array(),
                    YITH_YWQA_VERSION,
                    true );

				wp_enqueue_script( "ywqa-recaptcha" );

			}
		}


		/**
		 * Add a vote to a question
		 */
		public function on_post_vote_to_question() {

			$question_id   = intval( $_GET['discussion_id'] );
			$question_vote = intval( $_GET['question_vote'] );
			$this->vote_discussion( $question_id, $question_vote );
		}

		/**
		 * Add or remove upvote or downvote to a product review
		 *
		 * @param int $discussion_id review id
		 * @param int $vote          1 for upvotes, -1 for downvotes
		 * @param int $add_or_remove 1 for adding a vote to total count of votes of type $rate_value, -1 for removing a vote from total count
		 *
		 * @return void
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		private function update_vote_to_discussion( $discussion_id, $vote, $add_or_remove ) {

			//  if user had a rate for this review, remove it from comment total count  before adding the new one
			if ( 1 == $vote ) {
				$count = get_post_meta( $discussion_id, YWQA_METAKEY_DISCUSSION_UPVOTES, true );
				if ( ! isset( $count ) ) {
					$count = 0;
				}

				$count += $add_or_remove;
				update_post_meta( $discussion_id, YWQA_METAKEY_DISCUSSION_UPVOTES, $count > 0 ? $count : 0 );
			} else if ( - 1 == $vote ) {

				$count = get_post_meta( $discussion_id, YWQA_METAKEY_DISCUSSION_DOWNVOTES, true );
				if ( ! isset( $count ) ) {
					$count = 0;
				}

				$count += $add_or_remove;
				update_post_meta( $discussion_id, YWQA_METAKEY_DISCUSSION_DOWNVOTES, $count > 0 ? $count : 0 );
			}
		}

		/**
		 * Set a vote for a specific a question
		 *
		 * @param int $discussion_id the discussion ot vote
		 * @param int $vote          the value to add
		 */
		public function vote_discussion( $discussion_id, $vote ) {
			global $current_user;

			$meta_votes = get_post_meta( $discussion_id, YWQA_METAKEY_DISCUSSION_VOTES, true );
			if ( $current_user->ID > 0 ) {
				// set rate for current review (it's a array of (user_id, value) where value is 1 for positive and -1 for negative rating
				$previous_user_rate = 0;
				if ( isset( $meta_votes[ $current_user->ID ] ) ) {
					$previous_user_rate = $meta_votes[ $current_user->ID ];
				}

				//  if user had a rate for this review, remove it from review total count  before adding the new one
				$this->update_vote_to_discussion( $discussion_id, $previous_user_rate, - 1 );

				$meta_votes[ $current_user->ID ] = $vote;
			}

			update_post_meta( $discussion_id, YWQA_METAKEY_DISCUSSION_VOTES, $meta_votes );

			//  Add user rate to total count of upvotes or downvotes
			$this->update_vote_to_discussion( $discussion_id, $vote, 1 );
		}

		/**
		 * Retrieve text explaining how much upvotes a question received
		 *
		 * @param int|object $question question id or question instance
		 *
		 * @return string
		 */
		private function get_positive_vote_text( $question ) {
			if ( is_numeric( $question ) ) {
				$question = new YWQA_Question( $question );
			}

			$stats          = $question->get_voting_stats();
			$positive_count = $stats["yes"];

			return sprintf( _n( "%d vote", "%d votes", $positive_count, 'yith-woocommerce-questions-and-answers' ), $positive_count );
		}

		/**
		 * Retrieve text with voting statistics
		 *
		 * @param int|YWQA_Answer $discussion discussion id or discussion instance
		 *
		 * @return string
		 */
		public function get_helpful_answer_text( $discussion ) {
			if ( is_numeric( $discussion ) ) {
				$discussion = new YWQA_Answer( $discussion );
			}

			$stats = $discussion->get_voting_stats();

			$total = $stats["yes"] + $stats["not"];
			if ( $total > 0 ) {
				return sprintf( esc_html__( "%d of %d found it useful. What do you think?", 'yith-woocommerce-questions-and-answers' ), $stats["yes"], $total );
			}

			return esc_html__( "Do you think this answer is useful?", 'yith-woocommerce-questions-and-answers' );
		}

		/**
		 * Show voting section for a specific question
		 *
		 * @param YWQA_Question $question the question
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_vote_section( $question ) {
			if ( "yes" === get_option( "ywqa_enable_question_vote", "no" ) ) :
				?>
				<div class="question-votes">
					<a class="vote-question vote-yes"
					   title=" <?php esc_html_e( "Upvote this question", 'yith-woocommerce-questions-and-answers' ); ?>"
					   href="#" rel="nofollow"
					   data-discussion-id="<?php echo $question->ID; ?>" data-discussion-vote="1"></a>
					<span
						class="question-votes-count"><?php echo $this->get_positive_vote_text( $question->ID ); ?></span>
					<a class="vote-question vote-no"
					   title="<?php esc_html_e( "Downvote this question", 'yith-woocommerce-questions-and-answers' ); ?>"
					   href="#" rel="nofollow" data-discussion-id="<?php echo $question->ID; ?>" data-discussion-vote="-1"></a>
				</div>
				<?php
			endif;

		}

		/**
		 * Set the abuse report statistics for a specific a question
		 *
		 * @param int $discussion_id
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function report_abuse( $discussion_id ) {
			global $current_user;

			$abuse_reports = get_post_meta( $discussion_id, YWQA_METAKEY_ANSWER_ABUSE_REPORTS, true );

			if ( $current_user->ID > 0 ) {

				$count = get_post_meta( $discussion_id, YWQA_METAKEY_ANSWER_ABUSE_COUNT, true );
				if ( ! isset( $count ) ) {
					$count = 0;
				}

				//  if the current user has report an abuse, don't record anymore
				if ( ! isset( $abuse_reports[ $current_user->ID ] ) ) {


					$abuse_reports[ $current_user->ID ] = 1;
					update_post_meta( $discussion_id, YWQA_METAKEY_ANSWER_ABUSE_REPORTS, $abuse_reports );


					$count ++;
					update_post_meta( $discussion_id, YWQA_METAKEY_ANSWER_ABUSE_COUNT, $count > 0 ? $count : 0 );
				}
				/**
				 * Report a new inappropriate content
				 */
				do_action( "ywqa_inappropriate_content_reporting", $discussion_id, $count );
			}
		}

		public function get_full_content() {
			if ( ! isset( $_POST['discussion_id'] ) ) {
				return;
			}

			$discussion_id = intval( $_POST['discussion_id'] );
			$discussion    = new YWQA_Discussion( $discussion_id );

			wp_send_json( array( "code" => 1, "value" => $discussion->content ) );
		}

		/**
		 * Goto a question page from an ajax request
		 */
		public function goto_question_callback() {
			$question_id = filter_var( $_POST['discussion_id'], FILTER_SANITIZE_NUMBER_INT );
			$order       = isset( $_POST["order"] ) ? filter_var( $_POST['order'], FILTER_SANITIZE_STRING ) : "recent";

			$question = new YWQA_Question( $question_id );

			ob_start();
			wc_get_template( 'single-product/ywqa-product-answers.php',
				array(
					'question'  => $question,
					'max_items' => $this->answers_to_show,
					'order'     => $order,
				),
				'',
				YITH_YWQA_TEMPLATES_DIR );

			$content = ob_get_contents();
			ob_end_clean();

			wp_send_json( array(
				"code"  => 1,
				"items" => $content,
			) );
		}

		/**
		 * Manage an ajax call for retrieving questions by page
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public
		function get_questions_callback() {

			$product_id = intval( $_POST['product_id'] );
			$page       = $_POST['page'];
			$order      = $_POST['order'];

			ob_start();
			$this->show_questions( $product_id, $this->questions_to_show, $page, false );
			$questions = ob_get_contents();
			ob_end_clean();

			ob_start();
			$answered_count = $this->get_count_questions_by_products( $product_id );
			$this->show_items_pagination( $product_id, $page, $this->questions_to_show, $answered_count );
			$pagination = ob_get_contents();
			ob_end_clean();

			wp_send_json( array(
				"code"       => 1,
				"items"      => $questions,
				"pagination" => $pagination,
			) );
		}

		/**
		 * Manage an ajax call for retrieving questions by page
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public
		function get_answers_callback() {

			$product_id  = intval( $_POST['product_id'] );
			$page        = $_POST['page'];
			$order       = $_POST['order'];
			$question_id = $_POST['question_id'];

			$question = new YWQA_Question( $question_id );

			ob_start();
			$this->show_answers( $question, $this->answers_to_show, $page, $order );
			$items = ob_get_contents();
			ob_end_clean();

			ob_start();
			$answers_count = $question->get_answers_count();
			$this->show_items_pagination( $product_id, $page, $this->answers_to_show, $answers_count, $order, $question_id );
			$pagination = ob_get_contents();
			ob_end_clean();

			wp_send_json( array(
				"code"       => 1,
				"items"      => $items,
				"pagination" => $pagination,
			) );
		}

		/**
		 * Verify if a recaptcha test is passed
		 */
		public
		function recaptcha_test_passed() {

			if ( ! isset( $_SERVER['REMOTE_ADDR'] ) ) {
				return false;
			}

			if ( ! isset( $_POST["recaptcha"] ) ) {
				return false;
			}

			$remote_ip = $_SERVER['REMOTE_ADDR'];

			$sec_token = $_POST["recaptcha"];

			$recaptcha = new \ReCaptcha\ReCaptcha( $this->recaptcha_secretkey );
			$resp      = $recaptcha->verify( $sec_token, $remote_ip );

			return $resp;
		}

		public function verify_recaptcha() {
			/**
			 * If recaptcha is enabled, check the token
			 */
			if ( $this->recaptcha_enabled ) {

				if ( ! isset( $_POST["recaptcha"] ) ) {

					wp_send_json( array(
						"code"    => - 1,
						"message" => esc_html__( "An error occurred during the reCaptcha validation. Recaptcha token not set.", "ywau" ),
					) );
				}

				$resp = $this->recaptcha_test_passed();

				if ( ! $resp->isSuccess() ) {
					$errors = $resp->getErrorCodes();

					wp_send_json( array(
						"code"    => - 1,
						"message" => esc_html__( "An error occurred during the reCaptcha validation. The token is not valid.", "ywau" ),
					) );
				}
			}

			return true;
		}

		/**
		 * Manage an ajax call for submitting a new question
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function submit_question_callback() {

			/**
			 * If reCaptcha is enabled, check the token
			 */
			if ( $this->recaptcha_enabled && ! $this->verify_recaptcha() ) {
				return;
			};

			$product_id = intval( $_POST['product_id'] );
			$text       = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['text'] ) ) );
			$text       = stripslashes( $text );

			$subscribe = $_POST['subscribe'];

			$current_user_id = get_current_user_id();

			$args = array(
				'content'              => $text,
				'discussion_author_id' => $current_user_id,
				'product_id'           => $product_id,
				'parent_id'            => $product_id,
			);

			if ( ! $current_user_id ) {
				$args['discussion_author_name']  = $this->allow_guest_users && isset( $_POST['name'] ) ? $_POST['name'] : esc_html__( "Anonymous user", 'yith-woocommerce-questions-and-answers' );
				$args['discussion_author_email'] = $this->allow_guest_users && isset( $_POST['email'] ) ? $_POST['email'] : '';
			}

			//  Add new question
			$question = $this->create_question( $args );
			$question->set_approved_status( ! $this->question_manual_approval );

			//  Set a flag if user want to be notified of new answers
			if ( $this->enable_user_notification ) {
				$notify_customer = isset( $_POST["subscribe"] ) && $_POST["subscribe"];
				update_post_meta( $question->ID, YWQA_METAKEY_NOTIFY_USER, $notify_customer );
			}

			ob_start();
			wc_get_template( 'single-product/ywqa-product-answers.php',
				array(
					'question'  => $question,
					'max_items' => $this->answers_to_show,
				),
				'', YITH_YWQA_TEMPLATES_DIR );

			$content = ob_get_contents();
			ob_end_clean();

			wp_send_json( array(
				"code"             => $question->ID > 0,
				"items"            => $content,
				"waiting_approval" => $this->question_manual_approval,
				"message"          => apply_filters ( 'yith_wcqa_question_success_message',esc_html__( "Thanks for your post. It has been sent to site administrator for approval.", 'yith-woocommerce-questions-and-answers' )),
			) );
		}


		/**
		 * Manage an ajax call for changing the answer status
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public
		function change_answer_status_callback() {
			if ( ! isset( $_POST["action_type"] ) ||
			     ! isset( $_POST["discussion_id"] )
			) {
				return;
			}

			$action_type   = $_POST["action_type"];
			$discussion_id = intval( $_POST['discussion_id'] );

			$discussion = $this->get_discussion( $discussion_id );

			switch ( $action_type ) {
				case "set_approved" :

					$discussion->set_approved_status( true );
					break;

				case "set_unapproved" :
					$discussion->set_approved_status( false );
					break;

				case "set_appropriate" :
					$discussion->set_appropriate_status( true );
					break;

				case "set_inappropriate" :
					$discussion->set_appropriate_status( false );
					break;
			}

			$result = '';
			if ( $discussion instanceof YWQA_Answer ) {

				ob_start();
				$this->show_single_answer_backend( $discussion );
				$result = ob_get_contents();
				ob_end_clean();
			}

			wp_send_json( array(
				"code"   => $discussion->ID,
				"result" => $result,
			) );
		}

		/**
		 * Manage an ajax call for editing an answer to a question
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public
		function edit_discussion_content_callback() {
			if ( ! isset( $_POST['discussion_id'] ) ) {
				return;
			}

			if ( ! isset( $_POST['action_type'] ) ) {
				return;
			}

			$action_type   = $_POST["action_type"];
			$discussion_id = intval( $_POST['discussion_id'] );
			$discussion    = $this->get_discussion( $discussion_id );

			switch ( $action_type ) {
				case "edit" :
					if ( ! isset( $_POST['discussion_content'] ) ) {
						return;
					}

					$discussion_content = $_POST['discussion_content'];

					$discussion->content = $discussion_content;
					$res                 = $discussion->save();

					break;

				case "delete":
					$res = $discussion->delete();
					break;
			}

			wp_send_json( array(
				"code" => $res,
			) );
		}

		/**
		 * Manage an ajax call for submitting a new answer to a question
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public
		function admin_respond_to_question_callback() {
			$product_id  = intval( $_POST['product_id'] );
			$question_id = intval( $_POST['question_id'] );
			$anwered_backend = intval( $_POST['answered_backend'] );

            //$text = sanitize_text_field($_POST['answer_content']);
			$text = $_POST['answer_content'];

			$args = array(
				'content'              => $text,
				'discussion_author_id' => get_current_user_id(),
				'product_id'           => $product_id,
				'parent_id'            => $question_id,
				'answered_backend'     => $anwered_backend,
			);

			//  Add new question
			$answer = $this->create_answer( $args );

			$question = new YWQA_Question( $question_id );

			ob_start();
			$this->show_answers_backend( $question );
			$content = ob_get_contents();
			ob_end_clean();

			wp_send_json( array(
				"code"  => $answer->ID > 0,
				"items" => $content,
			) );
		}

		/**
		 * Manage an ajax call for submitting a new answer to a question
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public
		function submit_answer_callback() {

			/**
			 * If recaptcha is enabled, check the token
			 */
			if ( $this->recaptcha_enabled && ! $this->verify_recaptcha() ) {

				return;
			};

			$product_id  = intval( $_POST['product_id'] );
			$question_id = intval( $_POST['question_id'] );

			$text = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['answer_content'] ) ) );
			$text = stripslashes( $text );

			$args = array(
				'content'              => $text,
				'discussion_author_id' => get_current_user_id(),
				'product_id'           => $product_id,
				'parent_id'            => $question_id,
			);

			if ( ! get_current_user_id() ) {
				$args['discussion_author_name']  = $this->allow_guest_users && isset( $_POST['name'] ) ? $_POST['name'] : esc_html__( "Anonymous user", 'yith-woocommerce-questions-and-answers' );
				$args['discussion_author_email'] = $this->allow_guest_users && isset( $_POST['email'] ) ? $_POST['email'] : '';
			}

			//  Add new question
			$answer = $this->create_answer( $args );
			$answer->set_approved_status( ! $this->answer_manual_approval );

			$question = new YWQA_Question( $question_id );

			ob_start();
			wc_get_template( 'single-product/ywqa-product-answers.php',
				array(
					'question'  => $question,
					'max_items' => $this->answers_to_show,
				),
				'', YITH_YWQA_TEMPLATES_DIR );

			$content = ob_get_contents();
			ob_end_clean();

			wp_send_json( array(
				"code"             => $answer->ID > 0,
				"items"            => $content,
				"waiting_approval" => $this->answer_manual_approval,
				"message"          => apply_filters ( 'yith_wcqa_answer_success_message',esc_html__( "Thanks for your post. It has been sent to site administrator for approval.", 'yith-woocommerce-questions-and-answers' )),
			) );

		}

		/**
		 * Manage an ajax call for voting questions
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public
		function update_discussion_attribute_callback() {
			$current_user = wp_get_current_user();

			$discussion_id   = intval( $_POST['discussion_id'] );
			$discussion_vote = intval( $_POST['discussion_vote'] );

			$current_filter = current_filter();

			switch ( $current_filter ) {
				case 'wp_ajax_vote_question':
				case 'wp_ajax_nopriv_vote_question':
					$action = "vote_question";
					break;

				case 'wp_ajax_vote_answer':
				case 'wp_ajax_nopriv_vote_answer':
					$action = "vote_answer";
					break;
				case 'wp_ajax_report_answer_abuse':
				case 'wp_ajax_nopriv_report_answer_abuse':
					$action = "report_answer_abuse";

					break;

				default:
					wp_send_json( array( "code" => 0, "value" => "" ) );
			}

			if ( ! is_user_logged_in() ) {
				$return_path = $_POST['return_path'];

				// Add vars to querystring, building a redirect url to go after logins succesfull completed
				$return_path = add_query_arg( 'qa_action', $action, $return_path );

				$return_path = add_query_arg( 'discussion_id', $discussion_id, $return_path );
				$return_path = add_query_arg( 'discussion_vote', $discussion_vote, $return_path );

                $goto_url = apply_filters( 'yith_ywqa_login_url_no_logged_users',  wp_login_url( $return_path ) );

				wp_send_json( array( "code" => - 1, "value" => $goto_url ) );
			}

			switch ( $action ) {
				case "vote_question":
					$this->vote_discussion( $discussion_id, $discussion_vote );
					wp_send_json( array( "code" => 1, "value" => $this->get_positive_vote_text( $discussion_id ) ) );

					break;

				case "vote_answer":
					$this->vote_discussion( $discussion_id, $discussion_vote );

					wp_send_json( apply_filters( 'yith_ywqa_vote_answer',array( "code" => 1, "value" => $this->get_helpful_answer_text( $discussion_id ) ),$discussion_id,$discussion_vote ) );

					break;

				case "report_answer_abuse":
					$this->report_abuse( $discussion_id );
					wp_send_json( array(
						"code"  => 1,
						"value" => esc_html__( "Thanks!", 'yith-woocommerce-questions-and-answers' )
					) );

					break;
			}
		}

		/**
		 * Show pagination elements for questions
		 *
		 * @param $product_id    the product id for which to show questions
		 * @param $c_page        current page
		 * @param $items_to_show number of items to show
		 * @param $total_items   total number of answers
		 */
		public
		function show_items_pagination(
			$product_id, $c_page, $items_to_show, $total_items, $order = 'recent', $question_id = 0
		) {
			$type   = ( $question_id > 0 ) ? "answer" : "question";
			$p_page = $c_page > 1 ? $c_page - 1 : 0;
			$n_page = ( $c_page * $items_to_show ) < $total_items ? $c_page + 1 : 0;
			$order  = empty( $order ) ? "recent" : $order;

			$my_args = array(
				"c_page" => $p_page,
				"order"  => $order,
				"type"   => $type,
			);

			$prev_url = esc_url( add_query_arg( $my_args, get_permalink( $product_id ) ) );

			$my_args["c_page"] = $n_page;
			$next_url          = esc_url( add_query_arg( $my_args, get_permalink( $product_id ) ) );

			?>
			<li class="question-page previous">
				<?php if ( $p_page ) : ?>
				<a href="<?php echo $prev_url; ?>" class="goto-page" data-ywqa-page="<?php echo $p_page; ?>"
				   data-ywqa-product-id="<?php echo $product_id; ?>"
				   data-ywqa-order="<?php echo $order; ?>" <?php echo( $question_id ? 'data-ywqa-question-id="' . $question_id . '"' : '' ); ?>>
					<?php endif; ?>
					<img src="<?php echo YITH_YWQA_ASSETS_URL . '/images/previous-page.png'; ?>" />
					<?php if ( $p_page ) : ?>
				</a>
			<?php endif; ?>
			</li>

			<?php if ( $p_page > 0 ) : ?>
				<li class="question-page">
					<a href="<?php echo $prev_url; ?>"
					   class="goto-page"
					   data-ywqa-page="<?php echo $p_page; ?>"
					   data-ywqa-product-id="<?php echo $product_id; ?>"
					   data-ywqa-order="<?php echo $order; ?>" <?php echo( $question_id ? 'data-ywqa-question-id="' . $question_id . '"' : '' ); ?>>
						<span class="page-number"><?php echo $p_page; ?></span>
					</a>
				</li>
			<?php endif; ?>

			<li class="question-page selected">
				<span class="page-number selected"><?php echo $c_page; ?></span>
			</li>

			<?php if ( $n_page > 0 ) : ?>
				<li class="question-page">
					<a href="<?php echo $next_url; ?>"
					   class="goto-page page-number"
					   data-ywqa-page="<?php echo $n_page; ?>"
					   data-ywqa-product-id="<?php echo $product_id; ?>"
					   data-ywqa-order="<?php echo $order; ?>" <?php echo( $question_id ? 'data-ywqa-question-id="' . $question_id . '"' : '' ); ?>>
						<span class="page-number"><?php echo $n_page; ?></span>
					</a>
				</li>
			<?php endif; ?>

			<li class="question-page next">
				<?php if ( $n_page > 0 ) : ?>
				<a href="<?php echo $next_url; ?>"
				   class="goto-page"
				   data-ywqa-page="<?php echo $n_page; ?>"
				   data-ywqa-product-id="<?php echo $product_id; ?>"
				   data-ywqa-order="<?php echo $order; ?>" <?php echo( $question_id ? 'data-ywqa-question-id="' . $question_id . '"' : '' ); ?>>
					<?php endif; ?>
					<img src="<?php echo YITH_YWQA_ASSETS_URL . '/images/next-page.png'; ?>" />
					<?php if ( $n_page > 0 ) : ?>
				</a>
			<?php endif; ?>
			</li>
			<?php
		}

		/**
		 * Check if there is a new question or answer from the user
		 *
		 * @return bool it's a new question
		 */
		public
		function is_new_question() {
			if ( ! isset( $_POST["add_new_question"] ) ) {
				return false;
			}

			//  Check if the recaptcha test is valid...
			if ( $this->recaptcha_enabled && ! $this->recaptcha_test_passed() ) {
				//  The validation failed for some reason, the content will not be created
				return;
			}

			parent::is_new_question();
		}

		/**
		 * Check if there is a new answer
		 *
		 * @return bool it's a new answer
		 */
		public
		function is_new_answer() {
			if ( ! isset( $_POST["add_new_answer"] ) ) {
				return false;
			}

			//  Check if the recaptcha test is valid...
			if ( $this->recaptcha_enabled && ! $this->recaptcha_test_passed() ) {
				//  The validation failed for some reason, the content will not be created

				return;
			}

			parent::is_new_answer();
		}

        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.2.3
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWQA_INIT' ) {
            $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
                $new_row_meta_args['is_premium'] = true;
            }

            return $new_row_meta_args;
        }
        /**
         * Regenerate auction prices
         *
         * Action Links
         *
         * @return void
         * @since    1.2.3
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, 'yith_woocommerce_question_answer_panel', true );
            return $links;
        }
	}
}
