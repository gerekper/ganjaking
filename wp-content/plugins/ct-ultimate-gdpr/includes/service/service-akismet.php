<?php

/**
 * Class CT_Ultimate_GDPR_Service_Akismet
 */
class CT_Ultimate_GDPR_Service_Akismet extends CT_Ultimate_GDPR_Service_Abstract implements CT_Ultimate_CCPA_Service_Interface {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_akismet/akismet.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_akismet/akismet.php', '__return_true' );
		// add_action( 'yikes-mailchimp-additional-form-fields', array( $this, 'akismet_mail_components_filter' ), 10, 1 );
		// get comments
		
	}

	/** Add 'gdpr accepted' note to admin mails
	 *
	 * @param $components
	 * @param $form
	 * @param $mailer
	 *
	 * @return mixed
	 */
	public function akismet_mail_components_filter( $components, $form, $mailer ) {

		if ( isset( $components['body'] ) ) {

		    $mailSentText = $this->get_gdpr_accepted();

            if(!empty($mailSentText)){

                $components['body'] .= PHP_EOL . PHP_EOL . ''.$mailSentText.' : ' . date( get_option( 'date_format' ) ) . ' ' . date( get_option( 'time_format' ) );

            }

		}

		return $components;

	}

	/** Used for data access. Returns all the data that user upload using this plugin
	 * @return $this
	 */
	public function collect() {
		$currentUserEmail = $this->user->get_email();
		$currentMemberData = array();
		$page = (int) $page;

		$comments = get_comments(
			array(
				'author_email' => $currentUserEmail
			)
		);
		foreach ( (array) $comments as $comment ) {
			$comment_as_submitted = get_metadata_raw('comment', $comment->comment_ID, 'akismet_as_submitted');
			if ( $comment_as_submitted ) {
				$commentAsSubmitted = maybe_unserialize($comment_as_submitted);
				$commentAsSubmitted[0]['comment_ID'] = $comment->comment_ID;
				$currentMemberData[] = $commentAsSubmitted;
			}
		}
		
		$this->set_collected( $currentMemberData );
		return $this;
	}

	/** 
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Akismet Anti-Spam' );
	}

	/** Determine if the plugin is installed and activated
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'akismet_http_post' ); // just check a core function of the plugin if it exists or not to determine if the plugin is enabled
	}
	/** Get the emails uploaded to the plugin
	 * 
	 */
	public function breach_recipients_filter( $recipients ) { 
		global $wpdb;
		if ( ! $this->is_breach_enabled() ) {
			return $recipients;
		}
		$commentmetas = $wpdb->get_results("SELECT meta_value FROM $wpdb->commentmeta WHERE meta_key = 'akismet_as_submitted' ");
		foreach($commentmetas as $commentmeta){
			$metaValue = unserialize($commentmeta->meta_value);
			$recipients[$metaValue['comment_author_email']] = $metaValue['comment_author_email'];
		}
		return $recipients;

	}
	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true;
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
		// erase_personal_data
		$this->collect();
		$errors = array();
		$commentIds = array();
		/** @var WP_Comment $post */
		foreach ( (array) $this->collected as $commentmeta ) {
			$commentId = $commentmeta[0]['comment_ID'];
			unset($commentmeta[0]['comment_ID']);
			$newCommentMeta = (array) (new ArrayObject($commentmeta));
			$newCommentMeta[0]['comment_author'] = 'Anonymous';
			$newCommentMeta[0]['comment_author_email'] = '';
			$newCommentMeta [0]['comment_author_url'] = '';
			$commentIds[] = $newCommentMeta; // maybe_serialize($newCommentMeta); // $commentId;
			$isUpdated = update_comment_meta( $commentId, 'akismet_as_submitted', $newCommentMeta);
			if(!$isUpdated){
				$errors[] = $commentId;
			}
		}
		if ( ! empty( $errors ) && count($errors)) {
			throw new Exception( sprintf( esc_html__( "Akismet: Could not update comment data for comments: %s ||" . json_encode($commentIds) . '|||' . count($this->collected), 'ct-ultimate-gdpr' ), implode( ', ', $errors ) ) );
		}
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {
		$sectionName = 'ct-ultimate-gdpr-services-akismet_accordion-25';
		// Add Accordion
		add_settings_section(
			$sectionName, // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);
		// Option: Inject Consent Checbox to All Easy Forms
		add_settings_field(
			'services_Akismet_inject_consent_field', // Name of the field
			esc_html__( "Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ), // The label of the field
			array($this, 'render_services_Akismet_inject_consent_field'), // Content of the field
			$this->front_controller->find_controller('services')->get_id(), // Page where it is displayed
			$sectionName
		);
		// Option: Consent Text

        // add_settings_field(
        //     "services_{$this->get_id()}_gdpr_accepted", // ID
        //     sprintf( esc_html__( "[%s] Filter for Email sent at the bottom of contact form 7", 'ct-ultimate-gdpr' ), "GDPR Accepted" ), // Title
        //     array( $this, "render_gdpr_accepted_field" ), // Callback
        //     $this->front_controller->find_controller('services')->get_id(), // Page
        //     $sectionName // Section
        // );

        // add_settings_field(
        //     "services_{$this->get_id()}_hide_from_forgetme_form", // ID
        //     sprintf( esc_html__( "[%s] Hide from Forget Me Form", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
        //     array( $this, "render_hide_from_forgetme_form" ), // Callback
        //     $this->front_controller->find_controller('services')->get_id(), // Page
        //     $sectionName // Section
        // );
		
		/* This field only appear in Breach Tab */
		add_settings_field(
			"breach_services_{$this->get_id()}",
			esc_html( $this->get_name() ), // Title
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2' // Section
		);

    }
	public function render_services_Akismet_inject_consent_field(){
		$settingLink = $this->is_active() ? '<a href="' . get_admin_url() . '/options-general.php?page=akismet-key-config">Settings</a>' : 'Settings (Not Active)';
		printf('<i>Set this on Akismet Plugin ' . $settingLink . '</i>');
	}
	public function render_field_breach_services() {

		$admin      = $this->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value( $field_name, array(), $this->front_controller->find_controller('breach')->get_id() );
		$checked    = in_array( $this->get_id(), $values ) ? 'checked' : '';
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s[]' value='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$this->get_id(),
			$checked
		);
	}
	public function render_field_services_Akismet_consent_text_field() {
        $admin      = $this->get_admin_controller();
        $field_name = "services_Akismet_consent_text_field";
        printf(
            "<textarea 
				class='ct-ultimate-gdpr-accepted' 
				id='%s' 
				name='%s' 
				rows='1' 
				cols='100' 
				placeholder='I consent to the storage of my data according to the Privacy Policy'
			>%s</textarea>",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name, $this->get_gdpr_accepted())
        );
    }
    public function render_gdpr_accepted_field() {
        $admin      = $this->get_admin_controller();
        $field_name = "services_{$this->get_id()}_service_gdpr_accepted";
        printf(
            "<textarea class='ct-ultimate-gdpr-accepted' id='%s' name='%s' rows='1' cols='100'>%s</textarea>",
            $admin->get_field_name( __FUNCTION__ ),
            $admin->get_field_name_prefixed( $field_name ),
            $admin->get_option_value_escaped( $field_name, $this->get_gdpr_accepted())
        );
    }


    public function get_gdpr_accepted() {
        $gdpr_filter   = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( "services_{$this->get_id()}_service_gdpr_accepted", '', CT_Ultimate_GDPR_Controller_Services::ID );
        return $gdpr_filter;
    }

	/**
	 *
	 */
	public function render_field_services_Akismet_consent_field() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_services_Akismet_consent_field_position_first() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 * @return mixed
	 */
	public function front_action() { // Add the GDPR consent box on every easy form
		//GDPRCheckbox on every form
		add_action( 'yikes-mailchimp-additional-form-fields', array( $this, 'insert_gpdr_compliance_checkbox_filter' ), 10, 2 );
	}

	/**
	 * @param $original_fields
	 *
	 * @return mixed
	 */
	public function insert_gpdr_compliance_checkbox_filter( $original_fields ) {
		
		$inject = $this->get_admin_controller()->get_option_value( 'services_Akismet_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$consentText = $this->get_admin_controller()->get_option_value( 'services_Akismet_consent_text_field', false, $this->front_controller->find_controller('services')->get_id() );
		$fields = $original_fields;

		
		// $data = apply_filters( 'ct_ultimate_gdpr_service_Akismet_form_content', $fields, $original_fields, $inject, $position_first );
		$data = array(
			'consent_text' => $consentText ? $consentText : 'I consent to the storage of my data according to the Privacy Policy'
		);
		if ( $inject ) {
			echo ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-akismet-consent-field', false ), false, $data );
		}
	}

    public function enqueue_static(  ) {

        // wp_enqueue_script( 'ct-ultimate-gdpr-service-akismet', ct_ultimate_gdpr_url( 'assets/js/service-akismet.js' ) );
        wp_localize_script( 'ct-ultimate-gdpr-service-akismet', 'ct_ultimate_gdpr_Akismet', array(
            'checkbox' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-akismet-consent-field', false ) ),
        ) );

    }

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'Akismet process data from user\'s comments', 'ct-ultimate-gdpr' );
	}
}