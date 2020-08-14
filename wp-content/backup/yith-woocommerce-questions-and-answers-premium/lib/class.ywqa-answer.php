<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YWQA_Answer' ) ) {
	/**
	 *
	 * @class      class.ywqa-answer.php
	 * @package    Yithemes
	 * @since      Version 1.0.0
	 * @author     Your Inspiration Themes
	 *
	 */
	class YWQA_Answer extends YWQA_Discussion {

		/**
		 * Initialize a question object
		 *
		 * @param int|array $args the question id or an array for initializing the object
		 *
		 */
		public function __construct( $args = null ) {
			parent::__construct( $args );

			$this->type = "answer";
		}

		/**
		 * Retrieve the question for this answer
		 *
		 * @return null| YWQA_Question
		 */
		public function get_question() {
			if ( ! isset( $this->parent_id ) ) {

				return null;
			}

			return new YWQA_Question( $this->parent_id );
		}
	}
}