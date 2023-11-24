<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorForm{

	const LOGS_OPTIONS_KEY = "unlimited_elements_form_logs";
	const LOGS_MAX_COUNT = 10;

	const HOOK_NAMESPACE = "unlimited_elements/form";

	const ACTION_SAVE = "save";
	const ACTION_EMAIL = "email";
	const ACTION_EMAIL2 = "email2";
	const ACTION_WEBHOOK = "webhook";
	const ACTION_WEBHOOK2 = "webhook2";
	const ACTION_REDIRECT = "redirect";
	const ACTION_GOOGLE_SHEETS = "google_sheets";

	const PLACEHOLDER_ADMIN_EMAIL = "admin_email";
	const PLACEHOLDER_EMAIL_FIELD = "email_field";
	const PLACEHOLDER_FORM_FIELDS = "form_fields";
	const PLACEHOLDER_SITE_NAME = "site_name";

	private static $isFormIncluded = false;    //indicator that the form included once

	private $formSettings;
	private $formFields;
	private $formMeta;

	/**
	 * add conditions elementor control
	 */
	public static function getConditionsRepeaterSettings(){

		$settings = new UniteCreatorSettings();

		//--- operator

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrOptions = array("And" => "and", "Or" => "or");

		$settings->addSelect("operator", $arrOptions, __("Operator", "unlimited-elements-for-elementor"), "and", $params);

		//--- field name

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;

		$settings->addTextBox("field_name", "", __("Field Name", "unlimited-elements-for-elementor"), $params);

		//--- condition

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrOptions = array(
			"=" => "= (equal)",
			">" => "> (more)",
			">=" => ">= (more or equal)",
			"<" => "< (less)",
			"<=" => "<= (less or equal)",
			"!=" => "!= (not equal)");

		$arrOptions = array_flip($arrOptions);

		$settings->addSelect("condition", $arrOptions, __("Condition", "unlimited-elements-for-elementor"), "=", $params);

		//--- value

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["label_block"] = true;

		$settings->addTextBox("field_value", "", __("Field Value", "unlimited-elements-for-elementor"), $params);

		return ($settings);
	}

	/**
	 * add form includes
	 */
	public function addFormIncludes(){

		//don't include inside editor

		if(self::$isFormIncluded == true)
			return;

		//include common scripts only once
		if(self::$isFormIncluded == false){
			$urlFormJS = GlobalsUC::$url_assets_libraries . "form/uc_form.js";

			UniteProviderFunctionsUC::addAdminJQueryInclude();
			HelperUC::addScriptAbsoluteUrl_widget($urlFormJS, "uc_form");
		}

		self::$isFormIncluded = true;
	}

	/**
	 * get conditions data
	 * modify the data, add class and attributes
	 */
	public function getVisibilityConditionsParamsData($data, $visibilityParam){

		$name = UniteFunctionsUC::getVal($visibilityParam, "name");

		$arrValue = UniteFunctionsUC::getVal($visibilityParam, "value");

		if(empty($arrValue))
			return ($data);

		$arrValue = UniteFunctionsUC::getVal($arrValue, "{$name}_conditions");

		if(empty($arrValue))
			return ($data);

		$data["ucform_class"] = " ucform-has-conditions";

		return ($data);
	}

	/**
	 * get the list of form logs
	 */
	public static function getFormLogs(){

		$logs = get_option(self::LOGS_OPTIONS_KEY, array());

		return $logs;
	}

	/**
	 * get the form values
	 */
	private function getFieldsData($arrContent, $arrFields){

		$arrOutput = array();

		foreach($arrFields as $arrField){
			// get field input
			$fieldID = UniteFunctionsUC::getVal($arrField, "id");
			$fieldValue = UniteFunctionsUC::getVal($arrField, "value");

			// get saved settings from layout
			$arrFieldSettings = HelperProviderCoreUC_EL::getAddonValuesWithDataFromContent($arrContent, $fieldID);

			// get values that we'll use in the form
			// note - not all the fields will have a name/title
			$name = UniteFunctionsUC::getVal($arrFieldSettings, "field_name");
			$title = UniteFunctionsUC::getVal($arrFieldSettings, "label");
			$required = UniteFunctionsUC::getVal($arrFieldSettings, "required");
			$required = UniteFunctionsUC::strToBool($required);

			$arrFieldOutput = array();
			$arrFieldOutput["title"] = $title;
			$arrFieldOutput["name"] = $name;
			$arrFieldOutput["value"] = $fieldValue;
			$arrFieldOutput["required"] = $required;

			$arrOutput[] = $arrFieldOutput;
		}

		return ($arrOutput);
	}

	/**
	 * submit form
	 */
	public function submitFormFront(){

		$formData = UniteFunctionsUC::getPostGetVariable("formdata", null, UniteFunctionsUC::SANITIZE_NOTHING);
		$formID = UniteFunctionsUC::getPostGetVariable("formId", null, UniteFunctionsUC::SANITIZE_KEY);
		$layoutID = UniteFunctionsUC::getPostGetVariable("postId", null, UniteFunctionsUC::SANITIZE_ID);

		UniteFunctionsUC::validateNotEmpty($formID, "form id");
		UniteFunctionsUC::validateNumeric($layoutID, "post id");

		if(empty($formData))
			UniteFunctionsUC::throwError("No form data found.");

		$arrContent = HelperProviderCoreUC_EL::getElementorContentByPostID($layoutID);

		if(empty($arrContent))
			UniteFunctionsUC::throwError("Elementor content not found.");

		$addonForm = HelperProviderCoreUC_EL::getAddonWithDataFromContent($arrContent, $formID);

		// here can add some validation next...

		$arrFormSettings = $addonForm->getProcessedMainParamsValues();
		$arrFieldsData = $this->getFieldsData($arrContent, $formData);

		$this->doSubmitActions($arrFormSettings, $arrFieldsData);
	}

	/**
	 * do submit actions
	 */
	private function doSubmitActions($formSettings, $formFields){

		$this->formSettings = $formSettings;
		$this->formFields = $formFields;

		$data = array();
		$debugMessages = array();

		$debugData = array(
			"settings" => $formSettings,
			"fields" => $formFields,
			"errors" => array(),
		);

		try{
			$debugMessages[] = "Form has been received.";

			$formErrors = $this->validateFormSettings($formSettings);

			if(empty($formErrors) === false){
				$debugData["errors"] = array_merge($debugData["errors"], $formErrors);

				$formErrors = implode(" ", $formErrors);

				UniteFunctionsUC::throwError("Form settings validation failed ($formErrors).");
			}

			$fieldsErrors = $this->validateFormFields($formFields);

			if(empty($fieldsErrors) === false){
				$debugData["errors"] = array_merge($debugData["errors"], $fieldsErrors);

				$fieldsErrors = implode(" ", $fieldsErrors);

				UniteFunctionsUC::throwError("Form validation failed ($fieldsErrors).");
			}

			$formActions = UniteFunctionsUC::getVal($formSettings, "form_actions");

			foreach($formActions as $action){
				do_action(self::HOOK_NAMESPACE . "/before_{$action}_action", $formFields, $formSettings);

				switch($action){
					case self::ACTION_SAVE:
						$this->createFormEntry();

						$debugMessages[] = "Form entry has been successfully created.";
					break;

					case self::ACTION_EMAIL:
					case self::ACTION_EMAIL2:
						$emailFields = $this->getEmailFields($action);

						$debugData[$action] = $emailFields;

						$this->sendEmail($emailFields);

						$emails = implode(", ", $emailFields["to"]);

						$debugMessages[] = "Email has been successfully sent to $emails.";
					break;

					case self::ACTION_WEBHOOK:
					case self::ACTION_WEBHOOK2:
						$webhookFields = $this->getWebhookFields($action);

						$debugData[$action] = $webhookFields;

						$this->sendWebhook($webhookFields);

						$debugMessages[] = "Webhook has been successfully sent to {$webhookFields["url"]}.";
					break;

					case self::ACTION_REDIRECT:
						$redirectUrl = UniteFunctionsUC::getVal($formSettings, "redirect_url");
						$redirectUrl = esc_url_raw($redirectUrl);
						$validUrl = UniteFunctionsUC::isUrlValid($redirectUrl);

						if($validUrl === true){
							$data["redirect"] = $redirectUrl;

							$debugMessages[] = "Redirecting to $redirectUrl...";
						}
					break;

					case self::ACTION_GOOGLE_SHEETS:
						$spreadsheetFields = $this->getGoogleSheetsFields();

						$debugData[$action] = $spreadsheetFields;

						$this->sendToGoogleSheets($spreadsheetFields);

						$debugMessages[] = "Data has been successfully sent to Google Sheets.";
					break;

					default:
						UniteFunctionsUC::throwError("Form action \"$action\" is not implemented.");
				}

				do_action(self::HOOK_NAMESPACE . "/after_{$action}_action", $formFields, $formSettings);
			}

			$success = true;
			$message = esc_html__("Form has been successfully submitted.", "unlimited-elements-for-elementor");
		}catch(Exception $e){
			$success = false;
			$message = esc_html__("Unable to submit form.", "unlimited-elements-for-elementor");

			$debugMessages[] = $e->getMessage();
		}

		$this->createFormLog($debugMessages);

		$isDebug = UniteFunctionsUC::getVal($formSettings, "debug_mode");
		$isDebug = UniteFunctionsUC::strToBool($isDebug);

		if($isDebug === true){
			$debugMessage = implode(" ", $debugMessages);
			$debugType = UniteFunctionsUC::getVal($formSettings, "debug_type");

			$data["debug"] = "<p><b>DEBUG:</b> $debugMessage</p>";

			if($debugType === "full"){
				$debugData = json_encode($debugData, JSON_PRETTY_PRINT);
				$debugData = esc_html($debugData);

				$data["debug"] .= "<pre>$debugData</pre>";
			}
		}

		HelperUC::ajaxResponse($success, $message, $data);
	}

	/**
	 * validate form settings
	 */
	public function validateFormSettings($formSettings){

		$errors = array();

		$formActions = UniteFunctionsUC::getVal($formSettings, "form_actions");
		$formValidations = $this->getFormSettingsValidations();

		foreach($formValidations as $validation){
			foreach($validation["actions"] as $actionKey => $actionTitle){
				if(in_array($actionKey, $formActions) === false)
					continue;

				foreach($validation["rules"] as $fieldName => $rules){
					$fieldKey = $this->getFieldKey($fieldName, $actionKey);
					$fieldTitle = UniteFunctionsUC::getVal($validation["titles"], $fieldName, $fieldKey);
					$fieldValue = UniteFunctionsUC::getVal($formSettings, $fieldKey);
					$errorTitle = $actionTitle . ":";

					if(empty($fieldTitle) === false)
						$errorTitle .= " " . $fieldTitle;

					foreach($rules as $ruleName => $ruleParams){
						switch($ruleName){
							case "required":
								if($fieldValue === "")
									$errors[] = sprintf(esc_html__("%s field is empty.", "unlimited-elements-for-elementor"), $errorTitle);
							break;

							case "required_if":
								foreach($ruleParams as $depFieldName => $depFieldRequiredValue){
									$depFieldKey = $this->getFieldKey($depFieldName, $actionKey);
									$depFieldValue = UniteFunctionsUC::getVal($formSettings, $depFieldKey);

									if($depFieldValue === $depFieldRequiredValue && $fieldValue === ""){
										$errors[] = sprintf(esc_html__("%s field is empty.", "unlimited-elements-for-elementor"), $errorTitle);
										break;
									}
								}
							break;

							case "email":
								$validEmail = UniteFunctionsUC::isEmailValid($fieldValue);

								if($fieldValue !== "" && $validEmail === false)
									$errors[] = sprintf(esc_html__("%s field has an invalid email address.", "unlimited-elements-for-elementor"), $errorTitle);
							break;

							case "email_recipients":
								$emails = $this->prepareEmailRecipients($fieldValue);

								foreach($emails as $email){
									$validEmail = UniteFunctionsUC::isEmailValid($email);

									if($validEmail === false)
										$errors[] = sprintf(esc_html__("%s field has an invalid email address: %s.", "unlimited-elements-for-elementor"), $errorTitle, $email);
								}
							break;

							case "google_connect":
								$services = new UniteServicesUC();
								$services->includeGoogleAPI();

								try{
									UEGoogleAPIHelper::getFreshAccessToken();
								}catch(Exception $exception){
									$errors[] = sprintf(__("%s Google access token is missing or expired. Please connect to Google in the \"General Settings > Integrations\".", "unlimited-elements-for-elementor"), $errorTitle);
								}
							break;

							case "url":
								$validUrl = UniteFunctionsUC::isUrlValid($fieldValue);

								if($fieldValue !== "" && $validUrl === false)
									$errors[] = sprintf(esc_html__("%s field has an invalid URL.", "unlimited-elements-for-elementor"), $errorTitle);
							break;

							default:
								UniteFunctionsUC::throwError("Validation rule \"$ruleName\" is not implemented.");
						}
					}
				}
			}
		}

		return $errors;
	}

	/**
	 * get form settings validations
	 */
	private function getFormSettingsValidations(){

		$validations = array(

			array(
				"actions" => array(
					self::ACTION_EMAIL => __("Email", "unlimited-elements-for-elementor"),
					self::ACTION_EMAIL2 => __("Email 2", "unlimited-elements-for-elementor"),
				),
				"rules" => array(
					"to" => array(
						"required" => true,
					),
					"custom_to" => array(
						"required_if" => array("to" => "custom"),
						"email_recipients" => true,
					),
					"subject" => array(
						"required" => true,
					),
					"message" => array(
						"required" => true,
					),
					"from" => array(
						"email" => true,
					),
					"reply_to" => array(
						"email" => true,
					),
					"cc" => array(
						"email_recipients" => true,
					),
					"bcc" => array(
						"email_recipients" => true,
					),
				),
				"titles" => array(
					"to" => __("To", "unlimited-elements-for-elementor"),
					"custom_to" => __("Custom To", "unlimited-elements-for-elementor"),
					"subject" => __("Subject", "unlimited-elements-for-elementor"),
					"message" => __("Message", "unlimited-elements-for-elementor"),
					"from" => __("From Address", "unlimited-elements-for-elementor"),
					"reply_to" => __("Reply To", "unlimited-elements-for-elementor"),
					"cc" => __("Cc", "unlimited-elements-for-elementor"),
					"bcc" => __("Bcc", "unlimited-elements-for-elementor"),
				),
			),

			array(
				"actions" => array(
					self::ACTION_WEBHOOK => __("Webhook", "unlimited-elements-for-elementor"),
					self::ACTION_WEBHOOK2 => __("Webhook 2", "unlimited-elements-for-elementor"),
				),
				"rules" => array(
					"url" => array(
						"required" => true,
						"url" => true,
					),
				),
				"titles" => array(
					"url" => __("URL", "unlimited-elements-for-elementor"),
				),
			),

			array(
				"actions" => array(
					self::ACTION_REDIRECT => __("Redirect", "unlimited-elements-for-elementor"),
				),
				"rules" => array(
					"url" => array(
						"required" => true,
						"url" => true,
					),
				),
				"titles" => array(
					"url" => __("URL", "unlimited-elements-for-elementor"),
				),
			),

			array(
				"actions" => array(
					self::ACTION_GOOGLE_SHEETS => __("Google Sheets", "unlimited-elements-for-elementor"),
				),
				"rules" => array(
					"credentials" => array(
						"google_connect" => true,
					),
					"id" => array(
						"required" => true,
					),
				),
				"titles" => array(
					"credentials" => "",
					"id" => __("Spreadsheet ID", "unlimited-elements-for-elementor"),
				),
			),

		);

		return $validations;
	}

	/**
	 * validate form fields
	 */
	private function validateFormFields($formFields){

		$errors = array();

		foreach($formFields as $field){
			if($field["required"] === true && $field["value"] === "")
				$errors[] = sprintf(esc_html__("%s field is empty.", "unlimited-elements-for-elementor"), $this->getFieldTitle($field));
		}

		return $errors;
	}

	/**
	 * create form entry
	 */
	private function createFormEntry(){

		$isFormEntriesEnabled = HelperProviderUC::isFormEntriesEnabled();

		if($isFormEntriesEnabled === false)
			return;

		try{
			UniteFunctionsWPUC::processDBTransaction(function(){

				global $wpdb;

				$entriesTable = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_FORM_ENTRIES_NAME);

				$entriesData = array_merge($this->getFormMeta(), array(
					"form_name" => $this->getFormName(),
				));

				$isEntryCreated = $wpdb->insert($entriesTable, $entriesData);

				if($isEntryCreated === false){
					throw new Exception($wpdb->last_error);
				}

				$entryId = $wpdb->insert_id;

				$entryFieldsTable = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_FORM_ENTRY_FIELDS_NAME);

				foreach($this->formFields as $field){
					$entryFieldsData = array(
						"entry_id" => $entryId,
						"title" => $this->getFieldTitle($field),
						"name" => $field["name"],
						"value" => $field["value"],
					);

					$isFieldCreated = $wpdb->insert($entryFieldsTable, $entryFieldsData);

					if($isFieldCreated === false){
						throw new Exception($wpdb->last_error);
					}
				}
			});
		}catch(Exception $e){
			UniteFunctionsUC::throwError("Unable to create form entry: {$e->getMessage()}");
		}
	}

	/**
	 * create form log
	 */
	private function createFormLog($messages){

		$isFormLogsSavingEnabled = HelperProviderUC::isFormLogsSavingEnabled();

		if($isFormLogsSavingEnabled === false)
			return;

		$logs = self::getFormLogs();

		$logs[] = array(
			"form" => $this->getFormName(),
			"message" => implode(" ", $messages),
			"date" => current_time("mysql"),
		);

		$logs = array_slice($logs, -self::LOGS_MAX_COUNT);

		update_option(self::LOGS_OPTIONS_KEY, $logs);
	}

	/**
	 * send email
	 */
	private function sendEmail($emailFields){

		$isSent = wp_mail(
			$emailFields["to"],
			$emailFields["subject"],
			$emailFields["message"],
			$emailFields["headers"],
			$emailFields["attachments"]
		);

		if($isSent === false){
			$emails = implode(", ", $emailFields["to"]);

			UniteFunctionsUC::throwError("Unable to send email to $emails.");
		}
	}

	/**
	 * get email fields
	 */
	private function getEmailFields($action){

		$from = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("from", $action));
		$from = $this->replacePlaceholders($from, array(self::PLACEHOLDER_ADMIN_EMAIL));

		$fromName = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("from_name", $action));
		$fromName = $this->replacePlaceholders($fromName, array(self::PLACEHOLDER_SITE_NAME));

		$replyTo = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("reply_to", $action));
		$replyTo = $this->replacePlaceholders($replyTo, array(self::PLACEHOLDER_ADMIN_EMAIL));

		$to = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("to", $action));

		if($to === "custom")
			$to = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("custom_to", $action));

		$to = $this->replacePlaceholders($to, array(self::PLACEHOLDER_ADMIN_EMAIL, self::PLACEHOLDER_EMAIL_FIELD));
		$to = $this->prepareEmailRecipients($to);

		$cc = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("cc", $action));
		$cc = $this->replacePlaceholders($cc, array(self::PLACEHOLDER_ADMIN_EMAIL, self::PLACEHOLDER_EMAIL_FIELD));
		$cc = $this->prepareEmailRecipients($cc);

		$bcc = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("bcc", $action));
		$bcc = $this->replacePlaceholders($bcc, array(self::PLACEHOLDER_ADMIN_EMAIL, self::PLACEHOLDER_EMAIL_FIELD));
		$bcc = $this->prepareEmailRecipients($bcc);

		$subject = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("subject", $action));
		$subject = $this->replacePlaceholders($subject, array(self::PLACEHOLDER_SITE_NAME));

		$message = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("message", $action));
		$message = $this->prepareEmailMessageField($message);

		$emailFields = array(
			"from" => $from,
			"from_name" => $fromName,
			"reply_to" => $replyTo,
			"to" => $to,
			"cc" => $cc,
			"bcc" => $bcc,
			"subject" => $subject,
			"message" => $message,
			"headers" => array(),
			"attachments" => array(),
		);

		$emailFields = $this->applyActionFieldsFilter($action, $emailFields);

		$emailFields["headers"] = array_merge($this->prepareEmailHeaders($emailFields), $emailFields["headers"]);

		return $emailFields;
	}

	/**
	 * prepare email recipients
	 */
	private function prepareEmailRecipients($emailAddresses){

		$emailAddresses = strtolower($emailAddresses);
		$emailAddresses = explode(",", $emailAddresses);
		$emailAddresses = array_map("trim", $emailAddresses);
		$emailAddresses = array_filter($emailAddresses);
		$emailAddresses = array_unique($emailAddresses);

		return $emailAddresses;
	}

	/**
	 * prepare email message field
	 */
	private function prepareEmailMessageField($emailMessage){

		$formFieldsReplace = array();

		foreach($this->formFields as $field){
			$formFieldsReplace[] = "{$this->getFieldTitle($field)}: {$field["value"]}";
		}

		$formFieldsReplace = implode("<br />", $formFieldsReplace);

		$emailMessage = preg_replace("/(\r\n|\r|\n)/", "<br />", $emailMessage); // nl2br

		$emailMessage = $this->replacePlaceholders($emailMessage, array(
			self::PLACEHOLDER_ADMIN_EMAIL,
			self::PLACEHOLDER_EMAIL_FIELD,
			self::PLACEHOLDER_SITE_NAME,
			self::PLACEHOLDER_FORM_FIELDS,
		), array(
			self::PLACEHOLDER_FORM_FIELDS => $formFieldsReplace,
		));

		return $emailMessage;
	}

	/**
	 * prepare email headers
	 */
	private function prepareEmailHeaders($emailFields){

		$headers = array();

		if(empty($emailFields["from"]) === false){
			$from = $emailFields["from"];

			if($emailFields["from_name"])
				$from = "{$emailFields["from_name"]} <{$emailFields["from"]}>";

			$headers[] = "From: $from";
		}

		if(empty($emailFields["reply_to"]) === false)
			$headers[] = "Reply-To: {$emailFields["reply_to"]}";

		if(empty($emailFields["cc"]) === false){
			foreach($emailFields["cc"] as $email){
				$headers[] = "Cc: $email";
			}
		}

		if(empty($emailFields["bcc"]) === false){
			foreach($emailFields["bcc"] as $email){
				$headers[] = "Bcc: $email";
			}
		}

		return $headers;
	}

	/**
	 * send webhook
	 */
	private function sendWebhook($webhookFields){

		$response = wp_remote_request($webhookFields["url"], $webhookFields);
		$status = wp_remote_retrieve_response_code($response);

		if($status !== 200)
			UniteFunctionsUC::throwError("Unable to send webhook to {$webhookFields["url"]}.");
	}

	/**
	 * get webhook fields
	 */
	private function getWebhookFields($action){

		$url = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("url", $action));
		$mode = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("mode", $action));
		$body = array();

		if($mode === "advanced"){
			$body["form"] = array(
				"name" => $this->getFormName(),
			);

			$body["fields"] = $this->formFields;
			$body["meta"] = $this->getFormMeta();
		}else{
			foreach($this->formFields as $index => $field){
				if(empty($field["name"]) === false)
					$name = $field["name"];
				elseif(empty($field["title"]) === false)
					$name = $field["title"];
				else
					$name = $this->getFieldTitle($field) . " " . $index;

				$body[$name] = $field["value"];
			}

			$body["form_name"] = $this->getFormName();
		}

		$webhookFields = array(
			"url" => $url,
			"mode" => $mode,
			"body" => $body,
		);

		$webhookFields = $this->applyActionFieldsFilter($action, $webhookFields);

		return $webhookFields;
	}

	/**
	 * send to google sheets
	 */
	private function sendToGoogleSheets($spreadsheetFields){

		$services = new UniteServicesUC();
		$services->includeGoogleAPI();

		$sheetsService = new UEGoogleAPISheetsService();
		$sheetsService->useCredentials();

		$headersRow = array();
		$emptyRow = array();
		$valuesRow = array();

		foreach($spreadsheetFields["headers"] as $value){
			$cell = $sheetsService->prepareCellData($value);
			$cell = $sheetsService->applyBoldFormatting($cell);

			$headersRow[] = $cell;
			$emptyRow[] = $sheetsService->prepareCellData("");
		}

		foreach($spreadsheetFields["values"] as $value){
			$valuesRow[] = $sheetsService->prepareCellData($value);
		}

		$headersRow = $sheetsService->prepareRowData($headersRow);
		$emptyRow = $sheetsService->prepareRowData($emptyRow);
		$valuesRow = $sheetsService->prepareRowData($valuesRow);

		$headersRequest = $sheetsService->getUpdateCellsRequest($spreadsheetFields["sheet_id"], 0, 1, array($headersRow));
		$emptyRowRequest = $sheetsService->getUpdateCellsRequest($spreadsheetFields["sheet_id"], 1, 2, array($emptyRow));
		$insertRowRequest = $sheetsService->getInsertDimensionRequest($spreadsheetFields["sheet_id"], 2, 3);
		$valuesRequest = $sheetsService->getUpdateCellsRequest($spreadsheetFields["sheet_id"], 2, 3, array($valuesRow));

		// Flow:
		// - override the 1st row with headers
		// - override the 2nd row with empty values for separation
		// - insert the 3d row
		// - update the 3rd row with values
		$sheetsService->batchUpdateSpreadsheet($spreadsheetFields["id"], array(
			$headersRequest,
			$emptyRowRequest,
			$insertRowRequest,
			$valuesRequest,
		));
	}

	/**
	 * get google sheets fields
	 */
	private function getGoogleSheetsFields(){

		$spreadsheetId = UniteFunctionsUC::getVal($this->formSettings, "google_sheets_id");
		$sheetId = UniteFunctionsUC::getVal($this->formSettings, "google_sheets_sheet_id", 0);
		$sheetId = intval($sheetId);

		$headers = array();
		$values = array();

		// Add form fields
		foreach($this->formFields as $index => $field){
			if(empty($field["title"]) === false)
				$title = $field["title"];
			elseif(empty($field["name"]) === false)
				$title = $field["name"];
			else
				$title = $this->getFieldTitle($field) . " " . $index;

			$headers[] = $title;
			$values[] = $field["value"];
		}

		// Add empty column between fields and meta
		$headers[] = "";
		$values[] = "";

		// Add form meta
		$formMeta = $this->getFormMeta();

		unset($formMeta["post_id"]);
		unset($formMeta["user_id"]);

		foreach($formMeta as $key => $value){
			$headers[] = $this->getMetaTitle($key);
			$values[] = $value;
		}

		$spreadsheetFields = array(
			"id" => $spreadsheetId,
			"sheet_id" => $sheetId,
			"headers" => $headers,
			"values" => $values,
		);

		return $spreadsheetFields;
	}

	/**
	 * apply action fields filter
	 */
	private function applyActionFieldsFilter($action, $fields){

		$fields = apply_filters(self::HOOK_NAMESPACE . "/{$action}_fields", $fields, $this->formFields, $this->formSettings);

		return $fields;
	}

	/**
	 * get form name
	 */
	private function getFormName(){

		return $this->formSettings["form_name"] ?: __("Unnamed", "unlimited-elements-for-elementor");
	}

	/**
	 * get form meta
	 */
	private function getFormMeta(){

		if($this->formMeta === null)
			$this->formMeta = array(
				"post_id" => get_the_ID(),
				"post_title" => get_the_title(),
				"post_url" => get_permalink(),
				"user_id" => get_current_user_id(),
				"user_ip" => UniteFunctionsUC::getUserIp(),
				"user_agent" => UniteFunctionsUC::getUserAgent(),
				"created_at" => current_time("mysql"),
			);

		return $this->formMeta;
	}

	/**
	 * get field key
	 */
	private function getFieldKey($fieldName, $fieldPrefix){

		$fieldKey = $fieldPrefix . "_" . $fieldName;

		return $fieldKey;
	}

	/**
	 * get field title
	 */
	private function getFieldTitle($field){

		return $field["title"] ?: __("Untitled", "unlimited-elements-for-elementor");
	}

	/**
	 * get meta title
	 */
	private function getMetaTitle($key){

		$titles = array(
			"post_id" => "Page ID",
			"post_title" => "Page Title",
			"post_url" => "Page Link",
			"user_id" => "User ID",
			"user_ip" => "User IP",
			"user_agent" => "User Agent",
			"created_at" => "Creation Date",
		);

		return UniteFunctionsUC::getVal($titles, $key, $key);
	}

	/**
	 * get placeholder replacement
	 */
	private function getPlaceholderReplace($placeholder){

		switch($placeholder){
			case self::PLACEHOLDER_ADMIN_EMAIL:
				return get_bloginfo("admin_email");

			case self::PLACEHOLDER_EMAIL_FIELD:
				foreach($this->formFields as $field){
					$validEmail = UniteFunctionsUC::isEmailValid($field["value"]);

					if($validEmail === true)
						return $field["value"];
				}

				return "";

			case self::PLACEHOLDER_SITE_NAME:
				return get_bloginfo("name");

			default:
				return "";
		}
	}

	/**
	 * replace placeholders
	 */
	private function replacePlaceholders($value, $placeholders, $additionalReplaces = array()){

		foreach($placeholders as $placeholder){
			if(isset($additionalReplaces[$placeholder]) === true)
				$replace = $additionalReplaces[$placeholder];
			else
				$replace = $this->getPlaceholderReplace($placeholder);

			$value = $this->replacePlaceholder($value, $placeholder, $replace);
		}

		return $value;
	}

	/**
	 * replace placeholder
	 */
	private function replacePlaceholder($value, $placeholder, $replace){

		$value = str_replace("{{$placeholder}}", $replace, $value);

		return $value;
	}

}
