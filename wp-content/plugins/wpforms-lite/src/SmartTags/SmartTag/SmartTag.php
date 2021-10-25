<?php

namespace WPForms\SmartTags\SmartTag;

/**
 * Class SmartTag.
 *
 * @since 1.6.7
 */
abstract class SmartTag {

	/**
	 * Full smart tag.
	 * For example: {smart_tag attr="1" attr2="true"}.
	 *
	 * @since 1.6.7
	 *
	 * @var string
	 */
	protected $smart_tag;

	/**
	 * List of attributes.
	 *
	 * @since 1.6.7
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * SmartTag constructor.
	 *
	 * @since 1.6.7
	 *
	 * @param string $smart_tag Full smart tag.
	 */
	public function __construct( $smart_tag ) {

		$this->smart_tag = $smart_tag;
	}

	/**
	 * Get smart tag value.
	 *
	 * @since 1.6.7
	 *
	 * @param array  $form_data Form data.
	 * @param array  $fields    List of fields.
	 * @param string $entry_id  Entry ID.
	 *
	 * @return string
	 */
	abstract public function get_value( $form_data, $fields = [], $entry_id = '' );

	/**
	 * Get list of smart tag attributes.
	 *
	 * @since 1.6.7
	 *
	 * @return array
	 */
	final protected function get_attributes() {

		if ( ! empty( $this->attributes ) ) {
			$this->attributes;
		}

		preg_match_all( '/(\w+)=["\'](.+?)["\']/', $this->smart_tag, $attributes );
		$this->attributes = array_combine( $attributes[1], $attributes[2] );

		return $this->attributes;
	}
}
