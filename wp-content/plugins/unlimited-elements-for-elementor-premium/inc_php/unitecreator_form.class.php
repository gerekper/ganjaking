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

	const FOLDER_NAME = "unlimited_elements_form";
	const HOOK_NAMESPACE = "ue_form";
	const VALIDATION_ERROR_CODE = -1;

	const ACTION_SAVE = "save";
	const ACTION_EMAIL = "email";
	const ACTION_EMAIL2 = "email2";
	const ACTION_WEBHOOK = "webhook";
	const ACTION_WEBHOOK2 = "webhook2";
	const ACTION_REDIRECT = "redirect";
	const ACTION_GOOGLE_SHEETS = "google_sheets";
	const ACTION_HOOK = "hook";

	const PLACEHOLDER_ADMIN_EMAIL = "admin_email";
	const PLACEHOLDER_EMAIL_FIELD = "email_field";
	const PLACEHOLDER_FORM_FIELDS = "form_fields";
	const PLACEHOLDER_SITE_NAME = "site_name";

	const TYPE_FILES = "files";

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
	private function getFieldsData($arrContent, $arrFields, $arrFiles){

		$arrOutput = array();

		foreach($arrFields as $arrField){
			// get field input
			$fieldId = UniteFunctionsUC::getVal($arrField, "id");
			$fieldType = UniteFunctionsUC::getVal($arrField, "type");
			$fieldValue = UniteFunctionsUC::getVal($arrField, "value");
			$fieldParams = array();

			// get saved settings from layout
			$fieldSettings = HelperProviderCoreUC_EL::getAddonValuesWithDataFromContent($arrContent, $fieldId);

			if($fieldType === self::TYPE_FILES){
				$fieldValue = UniteFunctionsUC::getVal($arrFiles, $fieldId, array());
				$fieldParams["allowed_types"] = $this->prepareFilesFieldAllowedTypes($fieldSettings);
			}

			// get values that we'll use in the form
			// note: not all the fields will have a name/title
			$name = UniteFunctionsUC::getVal($fieldSettings, "field_name");
			$title = UniteFunctionsUC::getVal($fieldSettings, "label");
			$required = UniteFunctionsUC::getVal($fieldSettings, "required");
			$required = UniteFunctionsUC::strToBool($required);

			$arrFieldOutput = array();
			$arrFieldOutput["title"] = $title;
			$arrFieldOutput["name"] = $name;
			$arrFieldOutput["type"] = $fieldType;
			$arrFieldOutput["value"] = $fieldValue;
			$arrFieldOutput["required"] = $required;
			$arrFieldOutput["params"] = $fieldParams;

			$arrOutput[] = $arrFieldOutput;
		}

		return ($arrOutput);
	}

	/**
	 * submit form
	 */
	public function submitFormFront(){

		$formData = UniteFunctionsUC::getPostGetVariable("formData", null, UniteFunctionsUC::SANITIZE_NOTHING);
		$formFiles = UniteFunctionsUC::getFilesVariable("formFiles");
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
		$arrFieldsData = $this->getFieldsData($arrContent, $formData, $formFiles);

		$this->doSubmitActions($arrFormSettings, $arrFieldsData);
	}

	/**
	 * do submit actions
	 */
	private function doSubmitActions($formSettings, $formFields){

		$this->formSettings = $formSettings;
		$this->formFields = $formFields;

		$data = array();
		$errors = array();
		$debugData = array();
		$debugMessages = array();

		try{
			$debugMessages[] = "Form has been received.";

			// Validate form settings
			$formErrors = $this->validateFormSettings($this->formSettings);

			if(empty($formErrors) === false){
				$errors = array_merge($errors, $formErrors);

				$formErrors = implode(" ", $formErrors);

				UniteFunctionsUC::throwError("Form settings validation failed ($formErrors).");
			}

			// Validate form fields
			$fieldsErrors = $this->validateFormFields($this->formFields);

			if(empty($fieldsErrors) === false){
				$errors = array_merge($errors, $fieldsErrors);

				$validationError = $this->getValidationErrorMessage($fieldsErrors);

				UniteFunctionsUC::throwError($validationError, self::VALIDATION_ERROR_CODE);
			}

			// Upload form files
			$filesErrors = $this->uploadFormFiles();

			if(empty($filesErrors) === false){
				$errors = array_merge($errors, $filesErrors);

				UniteFunctionsUC::throwError("Form upload failed.");
			}

			// Process form actions
			$formActions = UniteFunctionsUC::getVal($this->formSettings, "form_actions");
			$actionsErrors = array();

			foreach($formActions as $action){
				try{
					$this->executeFormAction("before_{$action}_action");

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
							$redirectFields = $this->getRedirectFields();

							$data["redirect"] = $redirectFields["url"];
							$debugData[$action] = $redirectFields["url"];

							$debugMessages[] = "Redirecting to {$redirectFields["url"]}.";
						break;

						case self::ACTION_GOOGLE_SHEETS:
							$spreadsheetFields = $this->getGoogleSheetsFields();

							$debugData[$action] = $spreadsheetFields;

							$this->sendToGoogleSheets($spreadsheetFields);

							$debugMessages[] = "Data has been successfully sent to Google Sheets.";
						break;

						case self::ACTION_HOOK:
							$hookFields = $this->getHookFields();

							$debugData[$action] = $hookFields;

							$this->executeFormAction("custom/{$hookFields["name"]}");

							$debugMessages[] = "Hook has been successfully executed.";
						break;

						default:
							UniteFunctionsUC::throwError("Form action \"$action\" is not implemented.");
					}

					$this->executeFormAction("after_{$action}_action");
				}catch(Exception $exception){
					$actionsErrors[] = "{$this->getActionTitle($action)}: {$exception->getMessage()}";
				}
			}

			if(empty($actionsErrors) === false){
				$errors = array_merge($errors, $actionsErrors);

				$actionsErrors = implode(" ", $actionsErrors);

				UniteFunctionsUC::throwError("Form actions failed ($actionsErrors).");
			}

			$success = true;
			$message = $this->getFormSuccessMessage();
		}catch(Exception $exception){
			$success = false;
			$message = $this->getFormErrorMessage();

			if($exception->getCode() === self::VALIDATION_ERROR_CODE)
				$message = $exception->getMessage();

			$debugMessages[] = $exception->getMessage();
		}

		$this->createFormLog($debugMessages);

		$isDebug = UniteFunctionsUC::getVal($this->formSettings, "debug_mode");
		$isDebug = UniteFunctionsUC::strToBool($isDebug);

		if($isDebug === true){
			$debugMessage = implode(" ", $debugMessages);
			$debugType = UniteFunctionsUC::getVal($this->formSettings, "debug_type");

			$data["debug"] = "<p><b>DEBUG:</b> $debugMessage</p>";

			if($debugType === "full"){
				$debugData["errors"] = $errors;
				$debugData["fields"] = $this->formFields;
				$debugData["settings"] = $this->formSettings;

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
			foreach($validation["actions"] as $actionKey){
				if(in_array($actionKey, $formActions) === false)
					continue;

				$actionTitle = $this->getActionTitle($actionKey);

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
				"actions" => array(self::ACTION_EMAIL, self::ACTION_EMAIL2),
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
				"actions" => array(self::ACTION_WEBHOOK, self::ACTION_WEBHOOK2),
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
				"actions" => array(self::ACTION_REDIRECT),
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
				"actions" => array(self::ACTION_GOOGLE_SHEETS),
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

			array(
				"actions" => array(self::ACTION_HOOK),
				"rules" => array(
					"name" => array(
						"required" => true,
					),
				),
				"titles" => array(
					"name" => __("Name", "unlimited-elements-for-elementor"),
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
			$value = $field["value"];

			if($field["required"] === true)
				if($value === "" || (is_array($value) === true && empty($value) === true))
					$errors[] = $this->formatFieldError($field, $this->getFieldEmptyErrorMessage());

			if($field["type"] === self::TYPE_FILES){
				foreach($value as $file){
					if($file["error"] !== UPLOAD_ERR_OK){
						$errors[] = $this->formatFieldError($field, $this->getFileUploadErrorMessage());

						break;
					}

					$result = wp_check_filetype_and_ext($file["tmp_name"], $file["name"], $field["params"]["allowed_types"]);
					$allowedExtensions = array_keys($field["params"]["allowed_types"]);

					if($result["ext"] === false || $result["type"] === false){
						$errors[] = $this->formatFieldError($field, $this->getFileTypeErrorMessage($allowedExtensions));

						break;
					}
				}
			}
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

				if($isEntryCreated === false)
					UniteFunctionsUC::throwError($wpdb->last_error);

				$entryId = $wpdb->insert_id;

				$entryFieldsTable = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_FORM_ENTRY_FIELDS_NAME);

				foreach($this->formFields as $field){
					$entryFieldsData = array(
						"entry_id" => $entryId,
						"title" => $this->getFieldTitle($field),
						"name" => $field["name"],
						"type" => $field["type"],
						"value" => $field["value"],
					);

					$isFieldCreated = $wpdb->insert($entryFieldsTable, $entryFieldsData);

					if($isFieldCreated === false)
						UniteFunctionsUC::throwError($wpdb->last_error);
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
	 * upload form files
	 */
	private function uploadFormFiles(){

		// Create upload folder
		$folderName = self::FOLDER_NAME . "/"
			. date("Y") . "/"
			. date("m") . "/"
			. date("d") . "/";

		$folderPath = GlobalsUC::$path_images . $folderName;

		$created = wp_mkdir_p($folderPath);

		if($created === false)
			UniteFunctionsUC::throwError("Unable to create upload folder: $folderPath");

		// Process files upload
		$errors = array();

		foreach($this->formFields as &$field){
			if($field["type"] !== self::TYPE_FILES)
				continue;

			$urls = array();

			foreach($field["value"] as $file){
				$fileName = wp_unique_filename($folderPath, $file["name"]);
				$filePath = $folderPath . "/" . $fileName;

				$moved = move_uploaded_file($file["tmp_name"], $filePath);

				if($moved === false){
					$errors[] = "Unable to move uploaded file: $filePath";

					continue;
				}

				$chmoded = chmod($filePath, 0644);

				if($chmoded === false){
					$errors[] = "Unable to change file permissions: $filePath";

					continue;
				}

				$urls[] = GlobalsUC::$url_images . $folderName . $fileName;
			}

			$field["value"] = $this->encodeFilesFieldValue($urls);
		}

		return $errors;
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
			$title = $this->getFieldTitle($field);
			$value = $field["value"];

			if($field["type"] === self::TYPE_FILES)
				$value = $this->getFilesFieldLinksHtml($value);

			$formFieldsReplace[] = "$title: $value";
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

		$headers = array("Content-Type: text/html; charset=utf-8");

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
	 * get redirect fields
	 */
	private function getRedirectFields(){

		$url = UniteFunctionsUC::getVal($this->formSettings, "redirect_url");
		$url = esc_url_raw($url);

		$redirectFields = array(
			"url" => $url,
		);

		return $redirectFields;
	}

	/**
	 * send to google sheets
	 */
	private function sendToGoogleSheets($spreadsheetFields){

		$services = new UniteServicesUC();
		$services->includeGoogleAPI();

		$sheetsService = new UEGoogleAPISheetsService();
		$sheetsService->setAccessToken(UEGoogleAPIHelper::getFreshAccessToken());

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
	 * get hook fields
	 */
	private function getHookFields(){

		$name = UniteFunctionsUC::getVal($this->formSettings, "hook_name");

		$hookFields = array(
			"name" => $name,
		);

		return $hookFields;
	}

	/**
	 * execute form action
	 */
	private function executeFormAction($name){

		do_action(self::HOOK_NAMESPACE . "/$name", $this->formFields, $this->formSettings);
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
	 * get form message
	 */
	private function getFormMessage($key, $fallback){

		$message = UniteFunctionsUC::getVal($this->formSettings, $key);

		if(empty($message) === true)
			$message = $fallback;

		return $message;
	}

	/**
	 * get form success message
	 */
	private function getFormSuccessMessage(){

		$fallback = __("Your submission has been received!", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("success_message", $fallback);
		$message = esc_html($message);

		return $message;
	}

	/**
	 * get form error message
	 */
	private function getFormErrorMessage(){

		$fallback = __("Oops! Something went wrong, please try again later.", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("error_message", $fallback);
		$message = esc_html($message);

		return $message;
	}

	/**
	 * get validation error message
	 */
	private function getValidationErrorMessage($errors){

		$fallback = __("Please correct the following errors:", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("validation_error_message", $fallback);
		$message = esc_html($message);
		$message .= "<br />- " . implode("<br />- ", $errors);

		return $message;
	}

	/**
	 * get field empty error message
	 */
	private function getFieldEmptyErrorMessage(){

		$fallback = __("The field is empty.", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("field_empty_error_message", $fallback);

		return $message;
	}

	/**
	 * get file upload error message
	 */
	private function getFileUploadErrorMessage(){

		$fallback = __("The file upload failed.", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("file_upload_error_message", $fallback);

		return $message;
	}

	/**
	 * get file type error message
	 */
	private function getFileTypeErrorMessage($extensions){

		$fallback = __("The file must be of type: %s.", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("file_type_error_message", $fallback);
		$message = sprintf($message, implode(", ", $extensions));

		return $message;
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
	 * get field error
	 */
	private function formatFieldError($field, $error){

		return sprintf(esc_html("%s: %s"), $this->getFieldTitle($field), $error);
	}

	/**
	 * get action title
	 */
	private function getActionTitle($key){

		$titles = array(
			self::ACTION_EMAIL => __("Email", "unlimited-elements-for-elementor"),
			self::ACTION_EMAIL2 => __("Email 2", "unlimited-elements-for-elementor"),
			self::ACTION_WEBHOOK => __("Webhook", "unlimited-elements-for-elementor"),
			self::ACTION_WEBHOOK2 => __("Webhook 2", "unlimited-elements-for-elementor"),
			self::ACTION_REDIRECT => __("Redirect", "unlimited-elements-for-elementor"),
			self::ACTION_GOOGLE_SHEETS => __("Google Sheets", "unlimited-elements-for-elementor"),
			self::ACTION_HOOK => __("WordPress Hook", "unlimited-elements-for-elementor"),
		);

		return UniteFunctionsUC::getVal($titles, $key, $key);
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

	/**
	 * prepare files field allowed types
	 */
	private function prepareFilesFieldAllowedTypes($fieldSettings){

		$allowedTypes = UniteFunctionsUC::getVal($fieldSettings, "allowed_types", array());
		$customAllowedTypes = UniteFunctionsUC::getVal($fieldSettings, "custom_allowed_types");
		$customAllowedTypes = strtolower($customAllowedTypes);
		$customAllowedTypes = explode(",", $customAllowedTypes);
		$customAllowedTypes = array_map("trim", $customAllowedTypes);
		$customAllowedTypes = array_filter($customAllowedTypes);
		$customAllowedTypes = array_unique($customAllowedTypes);

		$typesMap = array(
			"archives" => array("tar", "zip", "gz", "gzip", "rar", "7z"),
			"audios" => array("mp3", "aac", "wav", "ogg", "flac", "wma"),
			"documents" => array("txt", "csv", "tsv", "pdf", "doc", "docx", "pot", "potx", "pps", "ppsx", "ppt", "pptx", "xls", "xlsx", "odt", "odp", "ods", "key", "pages"),
			"images" => array("jpeg", "jpg", "png", "tif", "tiff", "svg", "webp", "gif", "bmp", "ico", "heic"),
			"videos" => array("wmv", "avi", "flv", "mov", "mpeg", "mp4", "ogv", "webm", "3gp", "3gpp"),
			"custom" => $customAllowedTypes,
		);

		// merge wp mime types with the plugin mimes (in case of missing one)
		// format: extension => mime
		$mimes = array_merge(wp_get_mime_types(), array(
			"svg" => "image/svg+xml",
		));

		$types = array();

		foreach($allowedTypes as $type){
			if(isset($typesMap[$type]) === false)
				UniteFunctionsUC::throwError("File type \"$type\" is not implemented.");

			foreach($typesMap[$type] as $extension){
				$result = wp_check_filetype("temp.$extension", $mimes);

				if($result["ext"] !== false && $result["type"] !== false)
					$types[$result["ext"]] = $result["type"];
			}
		}

		return $types;
	}

	/**
	 * encode files field value
	 */
	private function encodeFilesFieldValue($urls){

		$value = implode(", ", $urls);

		return $value;
	}

	/**
	 * decode files field value
	 */
	public function decodeFilesFieldValue($value){

		$urls = explode(", ", $value);
		$urls = array_filter($urls);

		return $urls;
	}

	/**
	 * get files field links html
	 */
	public function getFilesFieldLinksHtml($value, $separator = ", ", $withDownload = false){

		$urls = $this->decodeFilesFieldValue($value);

		if(empty($urls) === true)
			return "";

		$links = array();

		foreach($urls as $url){
			$href = esc_attr($url);
			$label = esc_html(basename($url));
			$link = "<a href=\"$href\" target=\"_blank\">$label</a>";

			if ($withDownload === true)
				$link .= "<a href=\"$href\" target=\"_blank\" download><i class=\"dashicons dashicons-download\"></i></a>";

			$links[] = $link;
		}

		$links = implode($separator, $links);

		return $links;
	}

}
