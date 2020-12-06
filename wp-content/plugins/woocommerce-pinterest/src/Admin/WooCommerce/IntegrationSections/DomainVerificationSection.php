<?php


namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Pinterest\Api\ApiState;


/**
 * Class DomainVerificationSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 * This class is a storage for Domain Verification section fields on settings page
 */
class DomainVerificationSection implements IntegrationSectionInterface {


	/**
	 * ApiState instance
	 *
	 * @var ApiState
	 */
	private $apiState;

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * DomainVerificationSection constructor.
	 *
	 * @param ApiState $apiState
	 * @param FileManager $fileManager
	 */
	public function __construct( ApiState $apiState, FileManager $fileManager) {
		$this->apiState    = $apiState;
		$this->fileManager = $fileManager;
	}

	public function getTitle() {
		return __('Domain verification', 'woocommerce-pinterest');
	}

	public function getSlug() {
		return 'domain_verification_section';
	}

	public function getFields() {
		$fields = array();

		if ($this->apiState->isConnected('v3')) {
			$fields['domain_verification'] = array(
				'title' => __('Domain verification', 'woocommerce-pinterest'),
				'type' => 'domain_verification',
			);
		}

		$fields['verification_code'] = array(
			'title' => __('Verification code', 'woocommerce-pinterest'),
			'type' => 'text',
			'description' => $this->renderVerificationCodeFieldDescription()
		);

		return $fields;
	}

	private function renderVerificationCodeFieldDescription() {
		$documentationLink = 'https://docs.woocommerce.com/document/pinterest-for-woocommerce/#section-2';
		return $this->fileManager->renderTemplate('admin/woocommerce/verification-code-field-description.php',
			array('documentationLink' => $documentationLink)
		);
	}
}
