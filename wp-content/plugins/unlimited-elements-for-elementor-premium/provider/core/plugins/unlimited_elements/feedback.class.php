<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class UnlimitedElementsFeedbackUC{


	/**
	 * Enqueue feedback dialog scripts.
	 *
	 * Registers the feedback dialog scripts and enqueues them.
	 *
	 * @since 0.1.2
	 * @access public
	 */
	public function enqueue_feedback_dialog_scripts() {
		
		if ( ! in_array( get_current_screen()->id, [ 'plugins', 'plugins-network' ], true ) ) {
			return;
		}
		
		add_action( 'admin_footer', [ $this, 'print_deactivate_feedback_dialog' ] );
		
		wp_enqueue_style( 'feedback-css', HelperProviderCoreUC_EL::$urlCore. 'assets/feedback.css' );
		wp_enqueue_script( 'feedback-admin', HelperProviderCoreUC_EL::$urlCore . 'assets/feedback.js', array('jquery') );
		
	}


	/**
	 * Print deactivate feedback dialog.
	 *
	 * Display a dialog box to ask the user why he deactivated Elementor.
	 *
	 * Fired by `admin_footer` filter.
	 *
	 * @since 0.1.2
	 * @access public
	 */
	public function print_deactivate_feedback_dialog() {
		
		$nonce = wp_create_nonce("unlimited_elements_feedback_action");
		
		
		$deactivate_reasons = [
			'imported_all_templates_needed'  => [
				'title'             => __( "I don't need the plugin anymore", "unlimited-elements-for-elementor"),
				'input_placeholder' => "Do you mind telling us why you don't need the plugin anymore",
			],
			'couldnt_find_suitable_template' => [
				'title'             => __( 'I found a better plugin that has the same features', "unlimited-elements-for-elementor"),
				'input_placeholder' => __( 'Do you mind writing us the name of the diffrent plugin you intend to use?', "unlimited-elements-for-elementor"),
			],
			'didnt_like_templates'           => [
				'title'             => __( "It's a temporary deactivation", "unlimited-elements-for-elementor"),
				'input_placeholder' => 'Why are you temporarily deactivating the plugin?',
			],
			'couldnt_get_it_to_work'         => [
				'title'             => __( "I couldn't get the plugin to work", "unlimited-elements-for-elementor"),
				'input_placeholder' => 'Can you describe what problem you encountered?',
			],
			'couldnt_get_it_to_work'         => [
				'title'             => __( "I found to many bugs", "unlimited-elements-for-elementor"),
				'input_placeholder' => 'Please help us improve the plugin by writing us which bug you found',
			],
			
			'other'                          => [
				'title'             => __( 'Other', "unlimited-elements-for-elementor"),
				'input_placeholder' => __( 'Please describe the reason you are deactivating the plugin', "unlimited-elements-for-elementor"),
			],
		];
		
		?>

		<div class="unlimited-elements__modal-holder"></div>
		
		<script id="tmpl-unlimited-elements__plugin-feedback" type="text/x-handlebars-template">
			<section class="unlimited-elements__modal unlimited-elements__modal--plugin-feedback">
				<div class="unlimited-elements__modal-inner">
					<div class="unlimited-elements__modal-inner-bg">
						<header class="unlimited-elements__modal-header">
							<h3>Quick Feedback</h3>
							<button class="unlimited-elements__modal-close"></button>
						</header>
						<section class="unlimited-elements__modal-content">
							<div class="unlimited-elements-notice">
								<h2>If you have a moment, please share why you are deactivating Unlimited Elements for Elementor:</h2>
								<ul>
									<?php foreach ( $deactivate_reasons as $deactivate_reason => $deactivate_options ) { ?>
										<li>
											<input id="elements-deact-<?php echo esc_attr( $deactivate_reason ); ?>" type="radio" name="elements_deactivation_reason" value="<?php echo esc_attr( $deactivate_reason ); ?>"/>
											<label for="elements-deact-<?php echo esc_attr( $deactivate_reason ); ?>"><?php echo esc_html( $deactivate_options['title'] ); ?></label>
											<?php if ( ! empty( $deactivate_options['input_placeholder'] ) ) : ?>
												<div class="elements-deact-text">
													<input type="text" name="elements_deactivation_reason_<?php echo esc_attr( $deactivate_reason ); ?>" placeholder="<?php echo esc_attr( $deactivate_options['input_placeholder'] ); ?>"/>
												</div>
											<?php endif; ?>
										</li>
									<?php } ?>
								</ul>
							</div>
							<div class="unlimited-elements__disaable-buttons">
								<button class="unlimited-elements__disable-submit" data-nonce="<?php echo $nonce?>" data-action="<?php echo site_url() ?>/wp-admin/admin-ajax.php">Submit &amp; Deactivate</button>
								<a href="{{skip}}" class="unlimited-elements__disable-skip">Skip &amp; Deactivate</a>
							</div>
					</div>
				</div>
			</section>
		</script>
		<?php
	}
	
	
	/**
	 * send feedback dialog
	 */
	public function send_feedback_dialog(){

		$nonce = UniteFunctionsUC::getPostGetVariable("nonce", "", UniteFunctionsUC::SANITIZE_NOTHING);
		
		$isValid = wp_verify_nonce($nonce, "unlimited_elements_feedback_action");
		
		if($isValid == false)
			UniteFunctionsUC::throwError("Restricted access");
		
		$answer = UniteFunctionsUC::getPostGetVariable("answer", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		$answer_text = UniteFunctionsUC::getPostGetVariable("answer_text", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		$website = site_url();
		
		$to = GlobalsUnlimitedElements::EMAIL_FEEDBACK; // Send email
  		$subject = "Deactivation Feedback Unlimited Elements";
		
  		//$headers = array();
  		//$headers[] = "From: WP Feedback <wordpress@$website>";
  		$headers = "";
		$message = 'Plugin deactivated in '.$website. "\r\n";
		
		if(!empty($answer))
			$message .= 'Answer: '.$answer. "\r\n";
		
		if (!empty($answer_text))
			$message .= 'Answer text: '.$answer_text. "\r\n";
		
		//if no answer don't send mail
		if(empty($answer_text)){
			echo "done";
			exit();
		}

		//send mail only if there is answer
		wp_mail($to, $subject, strip_tags($message), $headers);
      	
		echo "done";
		exit();
	}
	
	
	/**
	 * init the feedback
	 */
	public function init(){
		
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_feedback_dialog_scripts' ] );
		
		add_action('wp_ajax_unlimited_elements_feedback', [ $this, 'send_feedback_dialog' ]);
		add_action('wp_ajax_nopriv_unlimited_elements_feedback', [ $this, 'send_feedback_dialog' ]);
		
	}
	

}
