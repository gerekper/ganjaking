<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_config extends WYSIJA_object{
	// the name of our option in the settings
	var $name_option = 'wysija';

	/* Check boxes are cheeky depending on the browser, some browser, won't post it, so it won't appear in the global _POST variable
	 * SO in order to identify unchecked value, we list all of the fields which are checkboxes
	 */
	var $cboxes = array(
		'emails_notified_when_sub',
		'emails_notified_when_unsub',
		'emails_notified_when_bounce',
		'emails_notified_when_dailysummary',
		'bounce_process_auto',
		'ms_bounce_process_auto',
		'sharedata',
		'manage_subscriptions',
		'viewinbrowser',
		'dkim_active',
		'cron_manual',
		'commentform',
		'smtp_rest',
		'ms_smtp_rest',
		'registerform',
		'ms_allow_admin_sending_method',
		'ms_allow_admin_toggle_signup_confirmation',
		'debug_log_cron',
		'debug_log_post_notif',
		'debug_log_query_errors',
		'debug_log_queue_process',
		'debug_log_manual',

	);

	/**
	 * all of the default values in that option
	 */
	var $defaults = array(
		'limit_listing' => 10,
		'role_campaign' => 'switch_themes',
		'role_subscribers' => 'switch_themes',
		'emails_notified_when_unsub' => true,
		'sending_method' => 'gmail',
		'sending_emails_number' => '70',
		'sending_method' => 'site',
		'sending_emails_site_method' => 'phpmail',
		'smtp_port' => '',
		'smtp_auth' => true,
		'bounce_port' => '',
		'confirm_dbleoptin' => 1,
		'bounce_selfsigned' => 0,
		'bounce_email_notexists' => 'unsub',
		'bouncing_emails_each' => 'daily',
		'bounce_inbox_full' => 'not',
		'pluginsImportedEgg' => false,
		'advanced_charset' => 'UTF-8',
		'sendmail_path' => '/usr/sbin/sendmail',
		'sending_emails_each' => 'hourly',
		'bounce_max' => 8,
		'debug_new' => false,
		'analytics' => 0,
		'send_analytics_now' => 0,
		'industry' => 'other',
		'recaptcha' => false,
		'recaptcha_key' => '',
		'recaptcha_secret' => '',
		'manage_subscriptions' => false,
		'editor_fullarticle' => false,
		'allow_no_js' => true,
		'urlstats_base64' => true,
		'viewinbrowser' => true,
		'commentform' => false,
		'registerform' => false,
		'ms_sending_config' => 'one-each',
		'ms_sending_method' => 'site',
		'ms_sending_emails_site_method' => 'phpmail',
		'ms_sending_emails_each' => 'hourly',
		'ms_sending_emails_number' => '100',
		'ms_allow_admin_sending_method' => false,
		'ms_allow_admin_toggle_signup_confirmation' => false,
		'ms_bouncing_emails_each' => 'daily',
		'cron_page_hit_trigger' => 1,
		'beta_mode' => false,
		'cron_manual' => true,
		'email_cyrillic' => false,
		'allow_wpmail' => false,
	);

	var $capabilities = array();
	var $values = array();

	function __construct(){
		// global telling us if we're currently running the installation process install/helper
		global $wysija_installing;
		// get our WordPress option containing all of our settings
		$encoded_option = get_option( $this->name_option );
		// we set a flag to identify whether we need to run the helpers/install.php
		$plugin_needs_installing = $plugin_needs_fixing = false;

		// 1 - "Is our plugin installed?" we make all of the checks to be sure that the plugin has been installed already
		if ( $encoded_option ){
			// our settings option needs to be a base64 encoded string containing a serialized array
			$this->values = unserialize( base64_decode( $encoded_option ) );

			// we make sure that the installation of the plugin has been complete
			if ( ! isset( $this->values['installed'] ) ){
				if ( defined( 'WP_ADMIN' ) && isset( $_GET['page'] ) && substr( $_GET['page'], 0, 7 ) == 'wysija_' && get_option( 'installation_step' ) == 16 ){
					// if we fall in that situation, there has been a problem
					// the step 16 of the installation has to set the "installed" and "installed_time" parameters in our config option
					// how could that happen?
					// let's determine the real version number of this installation so that the proper update sequence are run
					// let's run a hooked action the function cannot be run directly otherwise some missing WP functions will run a crash
					$plugin_needs_fixing = true;
				} else {
					// when we come to that step, we know the plugin has not been installed so we tell it to run the installation helper
					$plugin_needs_installing = true;
				}
			}
		} else {
			// our settings option is not set, that means the plugin is not installed
			$plugin_needs_installing = true;
		}

		// regenerate the DKIM key
		// dkim is not active that means the dkim_keys are not used so we can reinitialize them as 1024 if they are not already 1024
		// (we use to have a 512 DKIM which is not good enough for Gmail's spam filters)
		if ( ! isset( $this->values['dkim_active'] ) && ! empty( $this->values['dkim_pubk'] ) && ! isset( $this->values['dkim_1024'] ) ){
			unset($this->values['dkim_pubk']);
			unset($this->values['dkim_privk']);
		}

		// in multisite some options are global and need to have just one value accross all of the sites.
		// for instance (multisite sending method and multisite bounce handling)
		if ( is_multisite() ) {
			// safety net for accidentaly saved ms values in the child site option
			foreach ( $this->values as $key => $val ){
				// if we have a key prefixed by ms_ in that option then we just unset it.
				// the real ms value is loaded right after and comes from a global get_site_option
				if ( substr( $key, 0, 3 ) === 'ms_' ){
					unset( $this->values[ $key ] );
				}
			}
			$encoded_option = get_site_option( 'ms_' . $this->name_option );
			// let's merge the global multisite options to our child site settings so that they can be fetched through getValue()
			if ( $encoded_option ){
				$this->values = array_merge( $this->values, unserialize( base64_decode( $encoded_option ) ) );
			}

			// in multisite the default sending method is the network one
			$this->defaults['sending_method'] = 'network';
		}

		// install the application because there is no option setup it's safer than the classic activation scheme
		if ( defined( 'WP_ADMIN' ) ){

			if ( $plugin_needs_installing && $wysija_installing !== true ){
				$wysija_installing = true;
				$helper_install = WYSIJA::get( 'install', 'helper', false, 'wysija-newsletters', false );
				add_action( 'admin_menu', array( $helper_install, 'install' ), 97 );
			} else {
				$helper_update = WYSIJA::get( 'update' , 'helper' );
				if ( $plugin_needs_fixing ){
					// plugin needs fixing
					add_action( 'admin_menu', array( $helper_update, 'repair_settings' ), 103 );
				} else {
					// plugin is clean let's look for update request
					// the plugin has already been installed, so we check if there are some update query to be run
					add_action( 'admin_menu', array( $helper_update, 'check' ), 103 );
				}
			}
			// wait until the translation files are loaded and load our strings
			// From the backend we load the translated strings for that function at admin_menu level
			// Ben: there is a reason for that, I just don't remember which
			add_action( 'admin_menu', array( $this, 'add_translated_default' ) );
		} else {
			// wait until the translation files are loaded and load our strings
			add_action( 'init', array( $this, 'add_translated_default' ), 96 );
		}

		// we're already loading our translation files through hooks, this said
		$this->add_translated_default();
	}
	/*
	 * to make sure the translation is not screwed by an empty space or so
	 */
	function cleanTrans( $string ){
		return str_replace(
			array(
				'[ link]',
				'[link ]',
				'[ link ]',
				'[/ link]',
				'[/link ]',
				'[ /link]',
				'[/ link ]',
			),
			array(
				'[link]',
				'[link]',
				'[link]',
				'[/link]',
				'[/link]',
				'[/link]',
				'[/link]',
			),
			trim( $string )
		);
	}

	/**
	 * this is translatable text we use in the plugin which needs to be loaded through a hook so that the translation files are already there
	 */
	function add_translated_default(){
		// definition of extra translated defaults fields
		$this->defaults['confirm_email_title'] = sprintf( __( 'Confirm your subscription to %1$s', WYSIJA ), get_option( 'blogname' ) );
		$this->defaults['confirm_email_body'] = __( "Hello!\n\nHurray! You've subscribed to our site.\nWe need you to activate your subscription to the list(s): [lists_to_confirm] by clicking the link below: \n\n[activation_link]Click here to confirm your subscription.[/activation_link]\n\nThank you,\n\n The team!\n", WYSIJA );
		$this->defaults['subscribed_title'] = __( 'You\'ve subscribed to: %1$s', WYSIJA );
		$this->defaults['subscribed_subtitle'] = __( 'Yup, we\'ve added you to our list. You\'ll hear from us shortly.', WYSIJA );
		$this->defaults['unsubscribed_title'] = __( 'You\'ve unsubscribed!', WYSIJA );
		$this->defaults['unsubscribed_subtitle'] = __( 'Great, you\'ll never hear from us again!', WYSIJA );
		$this->defaults['unsubscribe_linkname'] = __( 'Unsubscribe', WYSIJA );
		$this->defaults['manage_subscriptions_linkname'] = __( 'Edit your subscription', WYSIJA );
		$this->defaults['viewinbrowser_linkname'] = $this->cleanTrans( __( 'Display problems? [link]View this newsletter in your browser.[/link]', WYSIJA ) );
		$this->defaults['registerform_linkname'] = $this->defaults['commentform_linkname'] = __( 'Yes, add me to your mailing list.', WYSIJA );

		$this->capabilities['newsletters'] = array(
			'label' => __( 'Who can create newsletters?', WYSIJA )
		);
		$this->capabilities['subscribers'] = array( // if this role (name) is changed, please change at the filter "wysija_capabilities" as well
			'label' => __( 'Who can manage subscribers?', WYSIJA )
		);
		$this->capabilities['config'] = array(
			'label' => __( 'Who can change MailPoet\'s settings?', WYSIJA )
		);
		$this->capabilities['theme_tab'] = array(
			'label' => __( 'Who can see the themes tab in the visual editor?', WYSIJA )
		);
		$this->capabilities['style_tab'] = array(
			'label' => __( 'Who can see the styles tab in the visual editor?', WYSIJA )
		);

		$this->capabilities = apply_filters( 'wysija_capabilities', $this->capabilities );

	}

	/**
	 * we have a specific save for option since we are saving it in wordpress options table
	 * @param array $data of data to save
	 * @param boolean $saved_through_interfaces telling us whether
	 */
	function save( $data = false, $saved_through_interfaces = false ) {

		if ( $data ){
			// when saving configuration from the settings page we need to make sure that if checkboxes have been unticked we remove the corresponding option
			$bouncing_freq_has_changed = $sending_freq_has_changed = $ms_sending_freq_has_changed = false;
			if ( $saved_through_interfaces ){
				$helper_wp_tools = WYSIJA::get( 'wp_tools', 'helper', false, 'wysija-newsletters', false );
				$editable_roles = $helper_wp_tools->wp_get_roles();
				foreach ( $this->capabilities as $keycap => $capability ){
					foreach ( $editable_roles as $role ){
						$this->cboxes[] = 'rolescap---' . $role['key'] . '---' . $keycap;
					}
				}

				// if the wysija's cron option has just been turned on from an off value
				// then we check the licence from mailpoet.com to share the cron url with us
				if ( isset( $data['cron_manual'] ) && $data['cron_manual'] != $this->getValue( 'cron_manual' ) ){
					$helper_licence = WYSIJA::get( 'licence', 'helper' );
					$helper_licence->check( true );
				}

				// loop through all of the checkboxes values
				foreach ( $this->cboxes as $checkbox ){
					// set the value as false if the value doesn't exist in the array (happens when checkbox is unchecked)
					if ( ! isset( $data[ $checkbox ] ) ){
						$data[ $checkbox ] = $this->values[ $checkbox ] = false;
					} else {
						// otherwise we set it as value 1
						$data[ $checkbox ] = $this->values[ $checkbox ] = 1;
					}

					// identify all of the roles checkboxes to update the WP user roles live when changed
					if ( strpos( $checkbox, 'rolescap---' ) !== false ){

						$role_capability = str_replace( 'rolescap---', '', $checkbox );

						$role_capability_exploded = explode( '---', $role_capability );
						$role = get_role( $role_capability_exploded[0] );
						$capability = 'wysija_' . $role_capability_exploded[1];
						// added for invalid roles ...

						// this is a rolecap let's add or remove the cap to the role
						if ( $role ){
							if ( $this->values[ $checkbox ] ){
								$role->add_cap( $capability );
							} else {
								// remove cap only for roles different of admins
								if ( $role->has_cap( $capability ) && ! in_array( $role_capability_exploded[0], array( 'administrator', 'super_admin' ) ) ){
									$role->remove_cap( $capability );
								}
							}
						}

						// no need to keep these role values in our option, they already are saved in WordPress' roles options
						unset( $this->values[ $checkbox ] );
					}
				}

				$helper_user = WYSIJA::get( 'user', 'helper', false, 'wysija-newsletters', false );

				// validating the from email
				if ( isset( $data['from_email'] ) && ! $helper_user->validEmail( $data['from_email'] ) ){
					if ( ! $data['from_email'] ){
						$data['from_email'] = __( 'empty', WYSIJA );
					}
					$this->error( sprintf( __( 'The <strong>from email</strong> value you have entered (%1$s) is not a valid email address.', WYSIJA ), '' ), true );
					$data['from_email'] = $this->values['from_email'];
				}

				// validating the replyto email
				if ( isset( $data['replyto_email'] ) && ! $helper_user->validEmail( $data['replyto_email'] ) ){
					if ( ! $data['replyto_email'] ){
						$data['replyto_email'] = __( 'empty', WYSIJA );
					}
					$this->error( sprintf( __( 'The <strong>reply to</strong> email value you have entered (%1$s) is not a valid email address.', WYSIJA ), '' ), true );
					$data['replyto_email'] = $this->values['replyto_email'];
				}

                                if ( isset( $data['bounce_rule_action_required_forwardto'] ) && ! $helper_user->validEmail( $data['bounce_rule_action_required_forwardto'] ) ){
					$this->error('Invalid bounce forward email');
					$data['bounce_rule_action_required_forwardto'] = $this->values['bounce_rule_action_required_forwardto'];
				}

                                if ( isset( $data['bounce_rule_blocked_ip_forwardto'] ) && ! $helper_user->validEmail( $data['bounce_rule_blocked_ip_forwardto'] ) ){
					$this->error('Invalid bounce forward email');
					$data['bounce_rule_blocked_ip_forwardto'] = $this->values['bounce_rule_blocked_ip_forwardto'];
				}

                                if ( isset( $data['bounce_rule_nohandle_forwardto'] ) && ! $helper_user->validEmail( $data['bounce_rule_nohandle_forwardto'] ) ){
					$this->error('Invalid bounce forward email');
					$data['bounce_rule_nohandle_forwardto'] = $this->values['bounce_rule_nohandle_forwardto'];
				}

				// in that case the admin changed the frequency of the wysija cron meaning that we need to clear it
				// network's method frequency has changed
				if ( isset( $data['ms_sending_emails_each'] ) && $data['ms_sending_emails_each'] != $this->getValue( 'ms_sending_emails_each' ) ){
					$ms_sending_freq_has_changed = true;
					$data['last_save'] = time();
				}

				// we're on a single site and the sending frequency has been modified
				// we need to refresh the sending scheduled task down below
				if ( isset( $data['sending_emails_each'] ) && $data['sending_emails_each'] != $this->getValue( 'sending_emails_each' ) ){
					$sending_freq_has_changed = true;
					$data['last_save'] = time();
				}

				// we're on a single site and the bounce frequency has been changed
				// we need to refresh the bounce scheduled task down below
				if ( isset( $data['bouncing_emails_each'] ) && $data['bouncing_emails_each'] != $this->getValue( 'bouncing_emails_each' ) ){
					$bouncing_freq_has_changed = true;
					$data['last_save'] = time();
				}

				// if saved with gmail then we set up the smtp settings
				// @deprecated since 2.6.12
				if ( isset( $data['sending_method'] ) ){
					if ( $data['sending_method'] == 'gmail' ) {
						$data['smtp_host'] = 'smtp.gmail.com';
						$data['smtp_port'] = '465';
						$data['smtp_secure'] = 'ssl';
						$data['smtp_auth'] = true;
					}
				}

				// basic validation of the smtp_host field
				if ( isset( $data['smtp_host'] ) ){
					$data['smtp_host'] = trim( $data['smtp_host'] );
				}

				// specific case to identify common action to different rules there some that don't appear in the interface, yet we use them.
				// BEN: this code needs to be reviewed and retested... I know what is the purpose but I don't understand $indexrule and $ruleMain
				foreach ( $data as $key => $value ){
					$fs = 'bounce_rule_';
					if ( strpos( $key, $fs ) !== false ){
						if ( strpos( $key, '_forwardto' ) === false ){
							$indexrule = str_replace( $fs, '', $key );
							$helper_rules = WYSIJA::get( 'rules', 'helper', false, 'wysija-newsletters', false );
							$rules = $helper_rules->getRules();
							foreach ( $rules as $keyy => $vals ){
								if ( isset( $vals['behave'] ) ){
									$ruleMain = $helper_rules->getRules( $vals['behave'] );
									$data[ $fs . $vals['key'] ] = $value;
								}
							}
						}
					}
				}

				// the regenerate box appeared for old versions of MailPoet where we had to switch from a 512 bits DKIM key to a 1024 for better score with Gmail
				// if the dkim_regenerate box has been ticked then we unset the dkim values so that they are regenerated in the next page load
				if ( isset( $data['dkim_regenerate'] ) && $data['dkim_regenerate'] == 'regenerate' ){
					if ( isset( $this->values['dkim_pubk'] ) ) {
						unset($data['dkim_pubk']);
						unset($this->values['dkim_pubk']);
						unset($data['dkim_privk']);
						unset($this->values['dkim_privk']);
						unset($data['dkim_regenerate']);
					}
				}

				// when we switch the double optin value on to off or off to on, we refresh the total user count which is different in both cases
				if ( isset( $data['confirm_dbleoptin'] ) && isset( $this->values['confirm_dbleoptin'] ) && $data['confirm_dbleoptin'] != $this->values['confirm_dbleoptin'] ){
					$helper_user = WYSIJA::get( 'user', 'helper' );
					$helper_user->refreshUsers();
				}
			}


			$is_multisite = is_multisite();
			$is_network_admin = WYSIJA::current_user_can( 'manage_network' );
			$global_MS_settings = array();
			foreach ( $data as $key => $value ){
				// we detect a ms value, so we put it in a separate array to store it somewhere else central
				if ( $is_multisite && $is_network_admin && strpos( $key, 'ms_' ) !== false ){
					$global_MS_settings[ $key ] = $value;
					continue;
				}

				// verify that the confirm email body contains an activation link
				// if it doesn't add it at the end of the email
				if ( $key == 'confirm_email_body' && strpos( $value, '[activation_link]' ) === false ){
					$value .= "\n" . '[activation_link]Click here to confirm your subscription.[/activation_link]';
				}

				// I'm not sure why do we do that, we separate the DKIm wrappers from teh value saved in the option.. why not, there must be a reason
				if ( $key == 'dkim_pubk' ){
					$value = str_replace( array( '-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----', "\n" ), '', $value );
				}

				if( is_string($value) ){
					$value = preg_replace( '#< *script(?:(?!< */ *script *>).)*< */ *script *>#isU', '', $value );
                                        $value = preg_replace("#<([^><]+?)([^a-z_\-]on\w*|xmlns)(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $value);
				}

				// for the manage subscription option you can select which list appear publicy to the user in their subscription page.
				// this piece of code  make sure that they appear or not
				if ( $key == 'manage_subscriptions_lists' ){
					$model_list = WYSIJA::get( 'list', 'model' );
					$model_list->update( array( 'is_public' => 0 ),array( 'is_public' => 1 ) );
					$model_list->reset();
					$model_list->update( array( 'is_public' => 1 ), array( 'list_id' => $value ) );

					unset( $this->values[ $key ] );
				}

				// we have a variable in this class which is defaults
				// we save the option only if its value is different than the default one: no need to overload the db.
				if ( ! isset( $this->defaults[ $key ] ) || ( isset( $this->defaults[ $key ] ) && $value != $this->defaults[ $key ] ) ){
					$this->values[ $key ] = $value;
				} else {
					unset( $this->values[ $key ] );
				}
			}


			// save the confirmation email in the email table
			// IMPORTANT: once we move the confirmation email to the newsletter listing we can get rid of that
			if ( isset( $data['confirm_email_title'] ) && isset( $data['confirm_email_body'] ) ){
				$model_email = WYSIJA::get( 'email', 'model', false, 'wysija-newsletters', false );
				$is_multisite = is_multisite();
				// the from email on a multisite with the network method on is coming from the ms value
				if ( $is_multisite && $data['sending_method'] == 'network' ) {
					$from_email = $data['ms_from_email'];
				} else {
					$from_email = $data['from_email'];
				}
				// updating email
				$model_email->update(
					array(
						'from_name' => $data['from_name'],
						'from_email' => $from_email,
						'replyto_name' => $data['replyto_name'],
						'replyto_email' => $data['replyto_email'],
						'subject' => $data['confirm_email_title'],
						'body' => $data['confirm_email_body'],
					),
					array(
						'email_id' => $this->values['confirm_email_id'],
					)
				);
			}
			unset($this->values['confirm_email_title']);
			unset($this->values['confirm_email_body']);
		}

		// serialize and encode the option's values and save them in WP's options
		update_option( $this->name_option, base64_encode( serialize( $this->values ) ) );

		// when we are on a multisite, part of the options need to be saved into a global option common to all of the sites
		if ( $is_multisite ){
			// the network admin has access to that extra information through the interfaces when does interfaces are generated then $dataMultisite is filled with values
			if ( ! empty( $global_MS_settings ) ){
				if ( $ms_sending_freq_has_changed ){
					// if the sending frequency on the network method has changed, we need to update each single cron task on all of the child sites
					// we reset an array to clear the cron of every single site using the multisite method
					update_site_option( 'ms_wysija_sending_cron', array() );
				}

				// get the data which was saved in the global option before
				$data_saved_ms_before = unserialize( base64_decode( get_site_option( 'ms_' . $this->name_option ) ) );

				// if it's not empty we just merge both sets of values
				if ( ! empty( $data_saved_ms_before ) ){
					$global_MS_settings = array_merge( $data_saved_ms_before, $global_MS_settings );
				}
				// we save the global ms option
				update_site_option( 'ms_' . $this->name_option, base64_encode( serialize( $global_MS_settings ) ) );
			}

			// let's merge the latest MS modified values with the values of the site's config, this is to avoid a bug after saving
			$data_saved_ms_fresh = unserialize( base64_decode( get_site_option( 'ms_' . $this->name_option ) ) );
			if ( ! empty( $data_saved_ms_fresh ) ){
				$this->values = array_merge( $this->values, $data_saved_ms_fresh );
			}
		}

		// the sending frequency has changed on that site's settings let's clear the frequency recorded in WP's and wysija's crons
		if ( $sending_freq_has_changed ){
			// WordPress cron clearing
			wp_clear_scheduled_hook( 'wysija_cron_queue' );
			// MailPoet's cron reset
			WYSIJA::set_cron_schedule( 'queue' );
		}

		// same than above but with the bounce frequency
		if ( $bouncing_freq_has_changed ){
			// WordPress cron clearing
			wp_clear_scheduled_hook( 'wysija_cron_bounce' );

			// MailPoet's cron reset
			WYSIJA::set_cron_schedule( 'bounce' );
		}

		// if it has been saved through the interface we notify the admin
		if ( $saved_through_interfaces ){
			$this->notice( __( 'Your settings are saved.', WYSIJA ) );
		}
	}


	/**
	 * some values in the settings needs to be overridden by ms values this is used in the getValue function
	 * it's a filter because of the premium plugin interacting with it
	 * eg bounce with ms_bounce
	 * @param array $ms_overriden
	 * @return array
	 */
	function ms_override( $ms_overriden ){
		if ( $this->getValue( 'premium_key' ) ){
			$bounce_value = array( 'bounce', 'bouncing' );
			return array_merge( $ms_overriden, $bounce_value );
		}
		return $ms_overriden;
	}

	/**
	 * Return  a setting value from our encoded config WordPress' option
	 * @param string $key
	 * @param type $default
	 * @return mixed
	 */
	function getValue( $key, $default = false ) {

		// special case for multisite
		if ( is_multisite() && $key != 'premium_key' ){

			// if we're getting the from email value we set a default value for the ms FROM
			if ( $key == 'ms_from_email' && ! isset( $this->defaults['ms_from_email'] ) ){
				$helper_toolbox = WYSIJA::get( 'toolbox', 'helper' );
				if ( is_object( $helper_toolbox ) ){
					$this->defaults['ms_from_email'] = 'info@' . $helper_toolbox->_make_domain_name( network_site_url() );
				}
			}

			$values_overridden_by_multisite = array();

			// apply a filter to add key/values to
			add_filter( 'mpoet_ms_override', array( $this, 'ms_override' ), 1 );
			$values_overidden_by_bounce = apply_filters( 'mpoet_ms_override', $values_overridden_by_multisite );

			foreach ( $values_overidden_by_bounce as $key_part_bounce ){
				if ( strpos( $key, $key_part_bounce . '_' ) !== false && strpos( $key, 'ms_' . $key_part_bounce . '_' ) === false ){
					$key = 'ms_' . $key;
					break;
				}
			}
			if ( $key == 'beta_mode' ){
				$key = 'ms_'.$key;
			}
		}

		if ( isset( $this->values[ $key ] ) ) {
			if ( $key == 'pluginsImportableEgg' ){
				$helperImport = WYSIJA::get( 'plugins_import', 'helper', false, 'wysija-newsletters', false );
				foreach ( $this->values[ $key ] as $tablename => $plugInfosExtras ){
					$extra_data = $helperImport->getPluginsInfo( $tablename );
					if ( $extra_data ){
						$this->values[ $key ][ $tablename ] = array_merge( $extra_data, $this->values[ $key ][ $tablename ] );
					}
				}
			}
			return $this->values[ $key ];
		} else {
			// special case for the confirmation email
			if ( in_array( $key, array( 'confirm_email_title', 'confirm_email_body' ) ) ){
				$model_email = WYSIJA::get( 'email', 'model', false, 'wysija-newsletters', false );
				$result_email = $model_email->getOne( $this->getValue( 'confirm_email_id' ) );
				if ( $result_email ){
					$this->values['confirm_email_title'] = $result_email['subject'];
					$this->values['confirm_email_body'] = $result_email['body'];
					return $this->values[ $key ];
				} else {
					if ( $default === false && isset( $this->defaults[ $key ] ) ){
						return $this->defaults[ $key ];
					} elseif ( ! ( $default === false ) ){
						return $default;
					}
				}
			} else {
				if ( $default === false && isset( $this->defaults[ $key ] ) ){
					return $this->defaults[ $key ];
				} elseif ( ! ( $default === false ) ){
					return $default;
				}
			}
		}
		return false;
	}

	/**
	 * TODO should this method really be here? It is used when rendering an email or when sending one
	 * @param type $editor
	 */
	function emailFooterLinks( $editor = false ){
		$unsubscribe = array();
		$unsubscribetxt = $editsubscriptiontxt = '';

		if ( ! isset( $this->values['unsubscribe_linkname'] ) ){
			$unsubscribetxt = __( 'Unsubscribe', WYSIJA );
		} else {
			$unsubscribetxt = $this->getValue( 'unsubscribe_linkname' );
		}

		if ( ! isset( $this->values['manage_subscriptions_linkname'] ) ){
			$editsubscriptiontxt = __( 'Edit your subscription', WYSIJA );
		} else {
			$editsubscriptiontxt = $this->getValue( 'manage_subscriptions_linkname' );
		}


		$unsubscribe[0] = array(
				'link' => '[unsubscribe_link]',
				'label' => $unsubscribetxt,
			);

		if ( $this->getValue( 'manage_subscriptions' ) ){
			$unsubscribe[1] = array(
				'link' => '[subscriptions_link]',
				'label' => $editsubscriptiontxt,
			);
		}

		if ( $editor ){
			$modelU = WYSIJA::get( 'user', 'model', false, 'wysija-newsletters', false );

			$unsubscribe[0]['link'] = $modelU->getConfirmLink( false, 'unsubscribe', false, true ) . '&demo=1';
			if ( $this->getValue( 'manage_subscriptions' ) ){
				$unsubscribe[1]['link'] = $modelU->getConfirmLink( false, 'subscriptions', false, true );
			}
		}

		return $unsubscribe;
	}

	/**
	 * TODO should this method really be here?
	 * It is used when rendering an email in the editor or before sending it
	 * @param boolean $editor if the link is in the editor, then it will be a demo link
	 * @return type
	 */
	function view_in_browser_link( $editor = false ){
		$data = array();

		if(!$this->getValue('viewinbrowser')){
                    return $data;
                }

                if ( isset( $this->values['viewinbrowser_linkname'] ) ){
                        // Grab the value for the view in browser link
                        $link = $this->values['viewinbrowser_linkname'];
                }

                // If we don't have the value from DB load a default
                if ( ! isset( $link ) ||  empty( $link ) || ! $link ){
                        $link = esc_attr__( 'Display problems? [link]View this newsletter in your browser.[/link]', WYSIJA );
                }

		// if we spot a link tag in the text we decompose the text in different parts pre rendering
		if ( strpos( $link, '[link]' ) !== false ){
			$linkpre = explode( '[link]', $link );
			$data['pretext'] = $linkpre[0];
			$linkpost = explode( '[/link]', $linkpre[1] );
			$data['posttext'] = $linkpost[1];
			$data['label'] = $linkpost[0];
			$data['link'] = '[view_in_browser_link]';
		}else{
                    $data['pretext'] = $data['posttext'] = '';
                    $data['label'] = $link;
                    $data['link'] = '[view_in_browser_link]';
                }

		if ( $editor ){
			$params_url = array(
				'wysija-page' => 1,
				'controller' => 'email',
				'action' => 'view',
				'email_id' => 0,
				'user_id' => 0,
			);
			if ( ! empty( $_REQUEST['id'] ) ){
				$params_url['email_id'] = (int)$_REQUEST['id'];
			}
			$data['link'] = WYSIJA::get_permalink( $this->getValue( 'confirm_email_link' ), $params_url );
		}

		return $data;
	}

	// Add a deprecation warning for this Method.
	function viewInBrowserLink( $editor = false ) {
		_doing_it_wrong( 'WYSIJA_model_config->viewInBrowserLink()', __( 'Use `view_in_browser_link` instead.', WYSIJA ), '2.6.10' );
		return $this->view_in_browser_link( $editor );
	}

}
