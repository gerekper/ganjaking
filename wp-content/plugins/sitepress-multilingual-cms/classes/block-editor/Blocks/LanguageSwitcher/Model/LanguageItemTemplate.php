<?php
namespace WPML\BlockEditor\Blocks\LanguageSwitcher\Model;

use WPML\BlockEditor\Blocks\LanguageSwitcher\Model\Label\LabelTemplateInterface;

class LanguageItemTemplate {

	/** @var \DOMNode */
	private $template;

	/** @var \DOMNode */
	private $container;

	/** @var null|LabelTemplateInterface */
	private $labelTemplate;

	/**
	 * @param \DOMNode $template
	 * @param \DOMNode $container
	 * @param null|LabelTemplateInterface $labelTemplate
	 */
	public function __construct(\DOMNode $template, \DOMNode $container, LabelTemplateInterface $labelTemplate = null) {
		$this->template = $template;
		$this->container = $container;
		$this->labelTemplate = $labelTemplate;
	}

	/**
	 * @return \DOMNode
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @return \DOMNode
	 */
	public function getContainer() {
		return $this->container;
	}

	/**
	 * @return null|LabelTemplateInterface
	 */
	public function getLabelTemplate() {
		return $this->labelTemplate;
	}
}